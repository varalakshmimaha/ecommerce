<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AffiliateProfile;
use App\Models\Commission;
use App\Models\Order;
use App\Models\Referral;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AffiliateController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'all');
        $valid = ['pending', 'approved', 'rejected', 'all'];
        if (!in_array($tab, $valid, true)) $tab = 'all';

        $query = User::query();

        if ($tab === 'pending') {
            $query->where('affiliate_status', 'pending');
        } elseif ($tab === 'approved') {
            $query->where('role', 'affiliate')->where('affiliate_status', 'approved');
        } elseif ($tab === 'rejected') {
            $query->where('affiliate_status', 'rejected');
        } else {
            $query->where(function ($q) {
                $q->where('role', 'affiliate')
                  ->orWhereIn('affiliate_status', ['pending', 'approved', 'rejected']);
            });
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%$s%")
                  ->orWhere('mobile', 'like', "%$s%")
                  ->orWhere('email', 'like', "%$s%")
                  ->orWhere('referral_code', 'like', "%$s%");
            });
        }

        $affiliates = $query->with(['parent:id,name,role', 'affiliateProfile'])
            ->orderByDesc('created_at')
            ->paginate(25)->withQueryString();

        // Per-affiliate commission totals + referral counts
        $commissionTotals = [];
        $referralCounts = [];
        $affIds = $affiliates->pluck('id')->all();
        if (!empty($affIds)) {
            $rows = Commission::whereIn('beneficiary_user_id', $affIds)
                ->selectRaw('beneficiary_user_id, status, SUM(amount) as total')
                ->groupBy('beneficiary_user_id', 'status')
                ->get();
            foreach ($rows as $r) {
                $commissionTotals[$r->beneficiary_user_id][$r->status] = (float) $r->total;
            }
            $referralCounts = User::whereIn('parent_id', $affIds)
                ->selectRaw('parent_id, COUNT(*) as c')
                ->groupBy('parent_id')
                ->pluck('c', 'parent_id')
                ->all();
        }

        $counts = [
            'pending' => User::where('affiliate_status', 'pending')->count(),
            'approved' => User::where('role', 'affiliate')->where('affiliate_status', 'approved')->count(),
            'rejected' => User::where('affiliate_status', 'rejected')->count(),
            'all' => User::where(function ($q) {
                $q->where('role', 'affiliate')->orWhereIn('affiliate_status', ['pending', 'approved', 'rejected']);
            })->count(),
        ];

        $parents = User::whereIn('role', ['rm', 'manager'])->orderBy('role')->orderBy('name')->get(['id', 'name', 'role']);

        return view('admin.hierarchy.affiliates', compact('affiliates', 'tab', 'counts', 'parents', 'commissionTotals', 'referralCounts'));
    }

    public function create()
    {
        $managers = User::where('role', 'manager')->orderBy('name')->get(['id', 'name']);
        $rms = User::where('role', 'rm')->orderBy('name')->get(['id', 'name', 'parent_id']);
        return view('admin.hierarchy.affiliate-create', compact('managers', 'rms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'mobile'         => 'required|string|max:15|unique:users,mobile',
            'email'          => 'required|email|unique:users,email',
            'password'       => 'required|string|min:6',
            'manager_id'     => 'required|exists:users,id',
            'rm_id'          => 'required|exists:users,id',
            'address'        => 'required|string|max:500',
            'city'           => 'required|string|max:100',
            'state'          => 'required|string|max:100',
            'pincode'        => 'required|string|max:10',
            'account_holder' => 'required|string|max:255',
            'bank_name'      => 'required|string|max:255',
            'account_number' => 'required|string|max:30',
            'ifsc'           => 'required|string|max:20',
            'upi_id'         => 'nullable|string|max:100',
            'pan_number'     => 'required|string|max:20',
            'aadhaar_number' => 'required|digits:12',
            'kyc_doc'        => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $rm = User::findOrFail($validated['rm_id']);
        if ($rm->role !== 'rm') {
            return back()->with('error', 'Selected RM is not a valid Relationship Manager.');
        }

        $user = User::create([
            'name'             => $validated['name'],
            'mobile'           => $validated['mobile'],
            'email'            => $validated['email'],
            'password'         => Hash::make($validated['password']),
            'role'             => 'affiliate',
            'parent_id'        => $rm->id,
            'affiliate_status' => 'approved',
            'approved_at'      => now(),
            'referral_code'    => $this->generateReferralCode((object)['name' => $validated['name']]),
            'is_verified'      => true,
        ]);

        $kycPath = $request->file('kyc_doc')->store('kyc', 'public');

        AffiliateProfile::create([
            'user_id'        => $user->id,
            'address'        => $validated['address'],
            'city'           => $validated['city'],
            'state'          => $validated['state'],
            'pincode'        => $validated['pincode'],
            'account_holder' => $validated['account_holder'],
            'bank_name'      => $validated['bank_name'],
            'account_number' => $validated['account_number'],
            'ifsc'           => $validated['ifsc'],
            'upi_id'         => $validated['upi_id'] ?? null,
            'pan_number'     => strtoupper($validated['pan_number']),
            'aadhaar_number' => $validated['aadhaar_number'],
            'kyc_doc_path'   => $kycPath,
            'kyc_verified'   => false,
        ]);

        return redirect()->route('admin.affiliates.index')->with('success', "Affiliate created. Referral code: {$user->referral_code}");
    }

    public function show(User $user)
    {
        $auth = auth()->user();
        abort_unless(
            $auth->is_admin ||
            $auth->id === $user->id ||
            ($auth->role === 'rm' && $user->parent_id === $auth->id) ||
            ($auth->role === 'manager' && \App\Models\User::where('id', $user->parent_id)->where('parent_id', $auth->id)->exists()),
            403
        );
        $user->load(['parent:id,name,role,parent_id', 'affiliateProfile']);

        $rm      = $user->parent;
        $manager = $rm ? User::find($rm->parent_id) : null;

        $totals = [
            'lifetime' => (float) Commission::forUser($user->id)->whereNotIn('status', ['reversed'])->sum('amount'),
            'wallet'   => WalletTransaction::balanceFor($user->id),
        ];

        $walletTransactions = WalletTransaction::with('creator:id,name')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        $referredUsers      = User::where('parent_id', $user->id)->orderByDesc('created_at')->limit(50)->get(['id','name','mobile','role','created_at']);
        $referredUsersCount = User::where('parent_id', $user->id)->count();

        // Order history — sourced directly from commissions so it always matches earnings
        $orderHistory       = collect();
        $orderCommissionMap = [];

        $commissionOrderIds = Commission::where('beneficiary_user_id', $user->id)
            ->whereNotNull('order_id')
            ->pluck('order_id')
            ->unique()
            ->values()
            ->all();

        if (!empty($commissionOrderIds)) {
            $allHierarchyIds = array_values(array_filter([$user->id, $rm?->id, $manager?->id]));

            $orderHistory = Order::whereIn('id', $commissionOrderIds)
                ->orderByDesc('created_at')
                ->get(['id', 'order_number', 'user_id', 'name', 'mobile', 'total_amount', 'order_status', 'created_at']);

            $commRows = Commission::whereIn('order_id', $commissionOrderIds)
                ->whereIn('beneficiary_user_id', $allHierarchyIds)
                ->get(['order_id', 'beneficiary_user_id', 'beneficiary_role', 'amount', 'status']);

            foreach ($commRows as $c) {
                $orderCommissionMap[$c->order_id][$c->beneficiary_role] = [
                    'amount'  => (float) $c->amount,
                    'status'  => $c->status,
                    'user_id' => $c->beneficiary_user_id,
                ];
            }
        }

        $monthlyEarnings = \App\Models\Commission::where('beneficiary_user_id', $user->id)
            ->whereNotIn('status', ['reversed'])
            ->selectRaw("
                YEAR(created_at) AS year, MONTH(created_at) AS month,
                SUM(amount) AS total_amount,
                COUNT(DISTINCT order_id) AS orders_count,
                CASE
                    WHEN SUM(CASE WHEN status = 'paid'    THEN 1 ELSE 0 END) = COUNT(*) THEN 'paid'
                    WHEN SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) > 0        THEN 'pending'
                    ELSE 'approved'
                END AS month_status
            ")
            ->groupByRaw('YEAR(created_at), MONTH(created_at)')
            ->orderByRaw('YEAR(created_at) DESC, MONTH(created_at) DESC')
            ->get();

        $showRefTab = true;

        $monthlyCommissionDetails = \App\Models\Commission::where('beneficiary_user_id', $user->id)
            ->whereNotNull('order_id')
            ->whereNotIn('status', ['reversed'])
            ->with('order:id,order_number,created_at')
            ->get(['id', 'order_id', 'base_amount', 'percentage', 'amount', 'status', 'created_at'])
            ->groupBy(fn($c) => $c->created_at->year . '-' . $c->created_at->month);

        return view('admin.hierarchy.affiliate-show', compact(
            'user', 'totals', 'walletTransactions',
            'referredUsers', 'referredUsersCount',
            'rm', 'manager',
            'orderHistory', 'orderCommissionMap',
            'monthlyEarnings', 'monthlyCommissionDetails', 'showRefTab'
        ));
    }

    public function walletTransaction(Request $request, User $user)
    {
        abort_unless($user->role === 'affiliate', 404);

        $validated = $request->validate([
            'type'   => 'required|in:credit,debit,request',
            'amount' => 'required|numeric|min:0.01',
            'remark' => 'nullable|string|max:500',
        ]);

        if ($validated['type'] === 'request') {
            WithdrawalRequest::create([
                'user_id'      => $user->id,
                'amount'       => $validated['amount'],
                'notes'        => $validated['remark'] ?? null,
                'request_type' => 'credit',
                'status'       => 'pending',
            ]);
            return redirect()->back()->with('success', 'Credit request for ₹' . number_format($validated['amount'], 2) . ' submitted. Approve it from Withdrawal Requests.');
        }

        WalletTransaction::create([
            'user_id'    => $user->id,
            'type'       => $validated['type'],
            'amount'     => $validated['amount'],
            'remark'     => $validated['remark'] ?? null,
            'created_by' => auth()->id(),
            'status'     => 'approved',
        ]);

        $msg = '₹' . number_format($validated['amount'], 2) . ' ' . ($validated['type'] === 'credit' ? 'added to' : 'removed from') . ' wallet.';
        return redirect()->back()->with('success', $msg);
    }

    public function toggleSection(Request $request, User $user)
    {
        abort_unless(auth()->user()->is_admin, 403);
        abort_unless($user->role === 'affiliate', 404);
        $section = $request->input('section');
        $allowed = ['show_earnings_referrals'];
        if (!in_array($section, $allowed, true)) {
            return response()->json(['success' => false, 'message' => 'Invalid section.'], 422);
        }
        $perms = $user->permissions ?? [];
        $perms[$section] = (bool) $request->input('value', true);
        $user->permissions = $perms;
        $user->save();
        return response()->json(['success' => true, 'section' => $section, 'value' => $perms[$section]]);
    }

    public function approveWallet(WalletTransaction $transaction)
    {
        abort_unless($transaction->user->role === 'affiliate', 404);
        $transaction->update(['status' => 'approved']);
        return redirect()->back()->with('success', 'Wallet transaction approved.');
    }

    public function rejectWallet(WalletTransaction $transaction)
    {
        abort_unless($transaction->user->role === 'affiliate', 404);
        $transaction->update(['status' => 'rejected']);
        return redirect()->back()->with('success', 'Wallet transaction rejected.');
    }

    public function edit(User $user)
    {
        $user->load('affiliateProfile');
        $parents = User::whereIn('role', ['rm', 'manager'])->orderBy('role')->orderBy('name')->get(['id', 'name', 'role']);
        return view('admin.hierarchy.affiliate-edit', compact('user', 'parents'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'mobile'         => 'required|string|max:15|unique:users,mobile,' . $user->id,
            'email'          => 'nullable|email|unique:users,email,' . $user->id,
            'password'       => 'nullable|string|min:6',
            'parent_id'      => 'nullable|exists:users,id',
            'referral_code'  => 'nullable|string|max:20|unique:users,referral_code,' . $user->id,
            'address'        => 'required|string|max:500',
            'city'           => 'required|string|max:100',
            'state'          => 'required|string|max:100',
            'pincode'        => 'required|string|max:10',
            'account_holder' => 'required|string|max:255',
            'bank_name'      => 'required|string|max:255',
            'account_number' => 'required|string|max:30',
            'ifsc'           => 'required|string|max:20',
            'upi_id'         => 'nullable|string|max:100',
            'pan_number'     => 'required|string|max:20',
            'aadhaar_number' => 'required|digits:12',
            'kyc_doc'        => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'kyc_verified'   => 'nullable',
        ]);

        if ($validated['parent_id'] ?? null) {
            $parent = User::findOrFail($validated['parent_id']);
            if (!in_array($parent->role, ['rm', 'manager'], true)) {
                return back()->with('error', 'Parent must be an RM or Manager.');
            }
            if ($parent->id === $user->id) {
                return back()->with('error', 'A user cannot be their own parent.');
            }
        }

        $userData = [
            'name'          => $validated['name'],
            'mobile'        => $validated['mobile'],
            'email'         => $validated['email'] ?? null,
            'parent_id'     => $validated['parent_id'] ?? null,
            'referral_code' => $validated['referral_code'] ?? $user->referral_code,
        ];
        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }
        $user->update($userData);

        $profileData = [
            'address'        => $validated['address'],
            'city'           => $validated['city'],
            'state'          => $validated['state'],
            'pincode'        => $validated['pincode'],
            'account_holder' => $validated['account_holder'],
            'bank_name'      => $validated['bank_name'],
            'account_number' => $validated['account_number'],
            'ifsc'           => strtoupper($validated['ifsc']),
            'upi_id'         => $validated['upi_id'] ?? null,
            'pan_number'     => strtoupper($validated['pan_number']),
            'aadhaar_number' => $validated['aadhaar_number'],
            'kyc_verified'   => $request->boolean('kyc_verified'),
        ];
        if ($request->hasFile('kyc_doc')) {
            $profileData['kyc_doc_path'] = $request->file('kyc_doc')->store('kyc', 'public');
        }
        AffiliateProfile::updateOrCreate(['user_id' => $user->id], $profileData);

        return redirect()->route('admin.affiliates.show', $user)->with('success', 'Affiliate updated.');
    }

    public function destroy(User $user)
    {
        $user->update([
            'role' => 'customer',
            'parent_id' => null,
            'affiliate_status' => 'none',
            'referral_code' => null,
        ]);
        return redirect()->route('admin.affiliates.index')->with('success', 'Affiliate removed (demoted to customer). Commissions and referral history preserved.');
    }

    public function approve(Request $request, User $user)
    {
        if ($user->role === 'affiliate' && $user->affiliate_status === 'approved') {
            return redirect()->back()->with('error', 'User is already an approved affiliate.');
        }

        if (empty($user->referral_code)) {
            $user->referral_code = $this->generateReferralCode($user);
        }
        $user->role = 'affiliate';
        $user->affiliate_status = 'approved';
        $user->approved_at = now();
        $user->save();

        return redirect()->back()->with('success', "Affiliate approved. Referral code: {$user->referral_code}");
    }

    public function reject(Request $request, User $user)
    {
        $validated = $request->validate([
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        $user->affiliate_status = 'rejected';
        $user->save();

        if ($user->affiliateProfile) {
            $user->affiliateProfile->update([
                'rejection_reason' => $validated['rejection_reason'] ?? 'Your application did not meet our requirements.',
            ]);
        }

        return redirect()->back()->with('success', 'Application rejected.');
    }

    public function verifyKyc(User $user)
    {
        $profile = $user->affiliateProfile;
        if (!$profile) {
            return redirect()->back()->with('error', 'No KYC profile found for this user.');
        }
        $profile->update(['kyc_verified' => !$profile->kyc_verified]);
        return redirect()->back()->with('success', $profile->kyc_verified ? 'KYC marked as verified.' : 'KYC verification removed.');
    }

    public function assignParent(Request $request, User $user)
    {
        $validated = $request->validate([
            'parent_id' => 'nullable|exists:users,id',
        ]);

        if ($validated['parent_id'] ?? null) {
            $parent = User::findOrFail($validated['parent_id']);
            if (!in_array($parent->role, ['rm', 'manager'], true)) {
                return redirect()->back()->with('error', 'Parent must be an RM or Manager.');
            }
            if ($parent->id === $user->id) {
                return redirect()->back()->with('error', 'A user cannot be their own parent.');
            }
        }

        $user->parent_id = $validated['parent_id'] ?? null;
        $user->save();

        return redirect()->back()->with('success', 'Parent assignment updated.');
    }

    private function generateReferralCode($user): string
    {
        $name = is_object($user) ? ($user->name ?? 'AFF') : 'AFF';
        $base = strtoupper(Str::of($name)->slug('')->substr(0, 3)->padLeft(3, 'X'));
        do {
            $code = $base . strtoupper(Str::random(5));
        } while (User::where('referral_code', $code)->exists());
        return $code;
    }
}
