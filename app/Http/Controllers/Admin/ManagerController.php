<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AffiliateProfile;
use App\Models\Commission;
use App\Models\Order;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ManagerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'manager');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%$s%")->orWhere('mobile', 'like', "%$s%")->orWhere('email', 'like', "%$s%");
            });
        }

        $managers = $query->withCount([
            'children as rms_count' => fn ($q) => $q->where('role', 'rm'),
        ])->orderByDesc('created_at')->paginate(25)->withQueryString();

        $managerIds = $managers->pluck('id')->all();

        // Per-manager commission totals + count (grouped by status)
        $commissionTotals = [];
        $commissionCounts = [];
        if (!empty($managerIds)) {
            $rows = Commission::whereIn('beneficiary_user_id', $managerIds)
                ->selectRaw('beneficiary_user_id, status, SUM(amount) as total, COUNT(*) as cnt')
                ->groupBy('beneficiary_user_id', 'status')
                ->get();
            foreach ($rows as $r) {
                $commissionTotals[$r->beneficiary_user_id][$r->status] = (float) $r->total;
                $commissionCounts[$r->beneficiary_user_id] = ($commissionCounts[$r->beneficiary_user_id] ?? 0) + (int) $r->cnt;
            }
        }

        // Per-manager affiliates count + RM & Affiliate earnings grouped by manager
        $affiliatesCounts = [];
        $rmEarningsByManager = [];
        $affEarningsByManager = [];
        if (!empty($managerIds)) {
            $rmRows = User::whereIn('parent_id', $managerIds)
                ->where('role', 'rm')
                ->get(['id', 'parent_id']);
            $rmToManager = $rmRows->pluck('parent_id', 'id'); // rmId => managerId

            if ($rmRows->isNotEmpty()) {
                $rmIds = $rmRows->pluck('id')->all();

                // Affiliate counts per manager
                $affGrouped = User::whereIn('parent_id', $rmIds)
                    ->where('role', 'affiliate')
                    ->selectRaw('parent_id, COUNT(*) as c')
                    ->groupBy('parent_id')
                    ->pluck('c', 'parent_id');
                foreach ($affGrouped as $rmId => $cnt) {
                    $mgrId = $rmToManager[$rmId] ?? null;
                    if ($mgrId) $affiliatesCounts[$mgrId] = ($affiliatesCounts[$mgrId] ?? 0) + (int) $cnt;
                }

                // RM lifetime earnings per manager
                $rmCommRows = Commission::whereIn('beneficiary_user_id', $rmIds)
                    ->whereNotIn('status', ['reversed'])
                    ->selectRaw('beneficiary_user_id, SUM(amount) as total')
                    ->groupBy('beneficiary_user_id')
                    ->pluck('total', 'beneficiary_user_id');
                foreach ($rmCommRows as $rmId => $total) {
                    $mgrId = $rmToManager[$rmId] ?? null;
                    if ($mgrId) $rmEarningsByManager[$mgrId] = ($rmEarningsByManager[$mgrId] ?? 0) + (float) $total;
                }

                // Affiliate lifetime earnings per manager (via their RMs)
                $affRows = User::whereIn('parent_id', $rmIds)
                    ->where('role', 'affiliate')
                    ->get(['id', 'parent_id']);
                if ($affRows->isNotEmpty()) {
                    $affToRm = $affRows->pluck('parent_id', 'id'); // affId => rmId
                    $affCommRows = Commission::whereIn('beneficiary_user_id', $affRows->pluck('id')->all())
                        ->whereNotIn('status', ['reversed'])
                        ->selectRaw('beneficiary_user_id, SUM(amount) as total')
                        ->groupBy('beneficiary_user_id')
                        ->pluck('total', 'beneficiary_user_id');
                    foreach ($affCommRows as $affId => $total) {
                        $rmId = $affToRm[$affId] ?? null;
                        $mgrId = $rmToManager[$rmId] ?? null;
                        if ($mgrId) $affEarningsByManager[$mgrId] = ($affEarningsByManager[$mgrId] ?? 0) + (float) $total;
                    }
                }
            }
        }

        return view('admin.hierarchy.managers.index', compact('managers', 'commissionTotals', 'commissionCounts', 'affiliatesCounts', 'rmEarningsByManager', 'affEarningsByManager'));
    }

    public function create()
    {
        return view('admin.hierarchy.managers.form', ['manager' => new User()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'mobile'         => 'required|string|max:15|unique:users,mobile',
            'email'          => 'required|email|unique:users,email',
            'password'       => 'required|string|min:6',
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

        $user = User::create([
            'name'             => $validated['name'],
            'mobile'           => $validated['mobile'],
            'email'            => $validated['email'],
            'password'         => Hash::make($validated['password']),
            'role'             => 'manager',
            'affiliate_status' => 'none',
            'is_verified'      => true,
            'referral_code'    => $this->generateReferralCode($validated['name']),
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

        return redirect()->route('admin.managers.index')->with('success', 'Manager created.');
    }

    public function show(User $manager)
    {
        abort_unless($manager->role === 'manager', 404);
        abort_unless(auth()->user()->is_admin || auth()->id() === $manager->id, 403);

        $rms = User::where('parent_id', $manager->id)
            ->where('role', 'rm')
            ->withCount(['children as affiliates_count' => fn ($q) => $q->where('role', 'affiliate')])
            ->orderByDesc('created_at')
            ->get();

        $rmIds = $rms->pluck('id')->all();

        // Affiliates with names so we can display them in the order history
        $affiliates = User::whereIn('parent_id', $rmIds)
            ->where('role', 'affiliate')
            ->get(['id', 'name', 'parent_id']);
        $affiliateIds      = $affiliates->pluck('id')->all();
        $affiliateNameMap  = $affiliates->pluck('name', 'id');   // affId  => name
        $affiliateRmMap    = $affiliates->pluck('parent_id', 'id'); // affId => rmId
        $rmNameMap         = $rms->pluck('name', 'id');           // rmId   => name

        // Per-RM own commission totals
        $rmCommissionTotals = [];
        if (!empty($rmIds)) {
            $rmRows = Commission::whereIn('beneficiary_user_id', $rmIds)
                ->where('beneficiary_role', 'rm')
                ->selectRaw('beneficiary_user_id, status, SUM(amount) as total')
                ->groupBy('beneficiary_user_id', 'status')
                ->get();
            foreach ($rmRows as $r) {
                $rmCommissionTotals[$r->beneficiary_user_id][$r->status] = (float) $r->total;
            }
        }

        // Manager totals
        $managerTotals = [
            'lifetime' => (float) Commission::forUser($manager->id)->whereNotIn('status', ['reversed'])->sum('amount'),
            'wallet'   => WalletTransaction::balanceFor($manager->id),
        ];

        // Wallet transactions history
        $walletTransactions = WalletTransaction::with('creator:id,name')
            ->where('user_id', $manager->id)
            ->orderByDesc('created_at')
            ->get();

        // Order history with per-level commission breakdown
        $orderHistory       = collect();
        $orderCommissionMap = [];
        $allHierarchyIds    = array_merge([$manager->id], $rmIds, $affiliateIds);

        if (!empty($allHierarchyIds)) {
            $relevantOrderIds = Commission::whereIn('beneficiary_user_id', $allHierarchyIds)
                ->whereNotNull('order_id')
                ->pluck('order_id')
                ->unique()->filter()->values()->all();

            if (!empty($relevantOrderIds)) {
                $orderHistory = Order::whereIn('id', $relevantOrderIds)
                    ->orderByDesc('created_at')
                    ->limit(200)
                    ->get(['id', 'order_number', 'user_id', 'name', 'mobile', 'total_amount', 'order_status', 'created_at']);

                $commRows = Commission::whereIn('order_id', $relevantOrderIds)
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
        }

        $summary = [
            'rms_count'        => $rms->count(),
            'affiliates_count' => count($affiliateIds),
            'orders_count'     => $orderHistory->count(),
        ];

        return view('admin.hierarchy.managers.show', compact(
            'manager',
            'rms',
            'rmCommissionTotals',
            'managerTotals',
            'walletTransactions',
            'orderHistory',
            'orderCommissionMap',
            'affiliateNameMap',
            'affiliateRmMap',
            'rmNameMap',
            'summary'
        ));
    }

    public function toggleSection(Request $request, User $manager)
    {
        abort_unless(auth()->user()->is_admin, 403);
        abort_unless($manager->role === 'manager', 404);

        $section = $request->input('section');
        $allowed = ['show_referrals'];
        if (!in_array($section, $allowed, true)) {
            return response()->json(['success' => false, 'message' => 'Invalid section.'], 422);
        }

        $perms = $manager->permissions ?? [];
        $perms[$section] = (bool) $request->input('value', true);
        $manager->permissions = $perms;
        $manager->save();

        return response()->json(['success' => true, 'section' => $section, 'value' => $perms[$section]]);
    }

    public function walletTransaction(Request $request, User $manager)
    {
        abort_unless($manager->role === 'manager', 404);

        $validated = $request->validate([
            'type'   => 'required|in:credit,debit,request',
            'amount' => 'required|numeric|min:0.01',
            'remark' => 'nullable|string|max:500',
        ]);

        if ($validated['type'] === 'request') {
            WithdrawalRequest::create([
                'user_id'      => $manager->id,
                'amount'       => $validated['amount'],
                'notes'        => $validated['remark'] ?? null,
                'request_type' => 'credit',
                'status'       => 'pending',
            ]);
            return redirect()->back()->with('success', 'Credit request for ₹' . number_format($validated['amount'], 2) . ' submitted. Approve it from Withdrawal Requests.');
        }

        WalletTransaction::create([
            'user_id'    => $manager->id,
            'type'       => $validated['type'],
            'amount'     => $validated['amount'],
            'remark'     => $validated['remark'] ?? null,
            'created_by' => auth()->id(),
            'status'     => 'approved',
        ]);

        $msg = '₹' . number_format($validated['amount'], 2) . ' ' . ($validated['type'] === 'credit' ? 'added to' : 'removed from') . ' wallet.';
        return redirect()->back()->with('success', $msg);
    }

    public function approveWallet(WalletTransaction $transaction)
    {
        abort_unless($transaction->user->role === 'manager', 404);
        $transaction->update(['status' => 'approved']);
        return redirect()->back()->with('success', 'Wallet transaction approved.');
    }

    public function rejectWallet(WalletTransaction $transaction)
    {
        abort_unless($transaction->user->role === 'manager', 404);
        $transaction->update(['status' => 'rejected']);
        return redirect()->back()->with('success', 'Wallet transaction rejected.');
    }

    public function edit(User $manager)
    {
        abort_unless($manager->role === 'manager', 404);
        return view('admin.hierarchy.managers.form', compact('manager'));
    }

    public function update(Request $request, User $manager)
    {
        abort_unless($manager->role === 'manager', 404);

        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'mobile'         => 'required|string|max:15|unique:users,mobile,' . $manager->id,
            'email'          => 'required|email|unique:users,email,' . $manager->id,
            'password'       => 'nullable|string|min:6',
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
        ]);

        $data = [
            'name'   => $validated['name'],
            'mobile' => $validated['mobile'],
            'email'  => $validated['email'],
        ];
        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }
        $manager->update($data);

        $profileData = [
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
        ];
        if ($request->hasFile('kyc_doc')) {
            $profileData['kyc_doc_path'] = $request->file('kyc_doc')->store('kyc', 'public');
        }
        AffiliateProfile::updateOrCreate(['user_id' => $manager->id], $profileData);

        return redirect()->route('admin.managers.index')->with('success', 'Manager updated.');
    }

    public function destroy(User $manager)
    {
        abort_unless($manager->role === 'manager', 404);
        // Demote instead of hard-delete — their RMs / commissions stay linked.
        $manager->update(['role' => 'customer', 'parent_id' => null]);
        return redirect()->route('admin.managers.index')->with('success', 'Manager removed (demoted to customer).');
    }

    private function generateReferralCode(string $name): string
    {
        $base = strtoupper(Str::of($name)->slug('')->substr(0, 3)->padLeft(3, 'X'));
        do {
            $code = $base . strtoupper(Str::random(5));
        } while (User::where('referral_code', $code)->exists());
        return $code;
    }
}
