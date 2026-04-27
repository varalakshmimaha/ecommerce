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

class RmController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'rm');

        if ($request->filled('manager_id')) {
            $query->where('parent_id', $request->manager_id);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%$s%")->orWhere('mobile', 'like', "%$s%")->orWhere('email', 'like', "%$s%");
            });
        }

        $rms = $query->with('parent:id,name')
            ->withCount([
                'children as affiliates_count' => fn ($q) => $q->where('role', 'affiliate'),
            ])
            ->orderByDesc('created_at')->paginate(25)->withQueryString();

        $managers = User::where('role', 'manager')->orderBy('name')->get(['id', 'name']);

        // Per-RM commission totals
        $commissionTotals = [];
        $rmIds = $rms->pluck('id')->all();
        if (!empty($rmIds)) {
            $rows = Commission::whereIn('beneficiary_user_id', $rmIds)
                ->selectRaw('beneficiary_user_id, status, SUM(amount) as total')
                ->groupBy('beneficiary_user_id', 'status')
                ->get();
            foreach ($rows as $r) {
                $commissionTotals[$r->beneficiary_user_id][$r->status] = (float) $r->total;
            }
        }

        return view('admin.hierarchy.rms.index', compact('rms', 'managers', 'commissionTotals'));
    }

    public function create()
    {
        $managers = User::where('role', 'manager')->orderBy('name')->get(['id', 'name']);
        return view('admin.hierarchy.rms.form', ['rm' => new User(), 'managers' => $managers]);
    }

    public function store(Request $request)
    {
        $creator = auth()->user();
        if ($creator->role === 'manager' && !$creator->is_admin) {
            $request->merge(['parent_id' => $creator->id]);
        }

        $validated = $this->validateInput($request);

        $user = User::create([
            'name'             => $validated['name'],
            'mobile'           => $validated['mobile'],
            'email'            => $validated['email'],
            'password'         => Hash::make($validated['password']),
            'role'             => 'rm',
            'parent_id'        => $validated['parent_id'] ?? null,
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

        return redirect()->route('admin.rms.index')->with('success', 'RM created.');
    }

    public function show(User $rm)
    {
        abort_unless($rm->role === 'rm', 404);
        $auth = auth()->user();
        abort_unless(
            $auth->is_admin ||
            $auth->id === $rm->id ||
            ($auth->role === 'manager' && $rm->parent_id === $auth->id),
            403
        );

        $rm->load('parent:id,name,role');

        // Affiliates directly under this RM
        $affiliates = User::where('parent_id', $rm->id)
            ->where('role', 'affiliate')
            ->orderByDesc('created_at')
            ->get(['id', 'name', 'mobile', 'email', 'referral_code', 'affiliate_status', 'created_at']);

        $affiliateIds    = $affiliates->pluck('id')->all();
        $affiliateNameMap = $affiliates->pluck('name', 'id'); // affId => name

        // Per-affiliate commission totals
        $affiliateCommissionTotals = [];
        if (!empty($affiliateIds)) {
            $rows = Commission::whereIn('beneficiary_user_id', $affiliateIds)
                ->selectRaw('beneficiary_user_id, status, SUM(amount) as total')
                ->groupBy('beneficiary_user_id', 'status')
                ->get();
            foreach ($rows as $r) {
                $affiliateCommissionTotals[$r->beneficiary_user_id][$r->status] = (float) $r->total;
            }
        }

        // Per-affiliate order counts
        $affiliateOrderCounts = !empty($affiliateIds)
            ? Order::whereIn('user_id', $affiliateIds)->selectRaw('user_id, COUNT(*) as c')->groupBy('user_id')->pluck('c', 'user_id')->all()
            : [];

        // Per-affiliate referral counts
        $affiliateReferralCounts = !empty($affiliateIds)
            ? User::whereIn('parent_id', $affiliateIds)->selectRaw('parent_id, COUNT(*) as c')->groupBy('parent_id')->pluck('c', 'parent_id')->all()
            : [];

        // RM's own totals
        $rmTotals = [
            'lifetime' => (float) Commission::forUser($rm->id)->whereNotIn('status', ['reversed'])->sum('amount'),
            'wallet'   => WalletTransaction::balanceFor($rm->id),
        ];

        // Wallet transactions
        $walletTransactions = WalletTransaction::with('creator:id,name')
            ->where('user_id', $rm->id)
            ->orderByDesc('created_at')
            ->get();

        // Order history with per-level commission breakdown
        $allHierarchyIds = array_merge([$rm->id], $affiliateIds);
        $orderHistory    = collect();
        $orderCommissionMap = [];

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
            'affiliates_count' => $affiliates->count(),
            'orders_count'     => $orderHistory->count(),
        ];

        return view('admin.hierarchy.rms.show', compact(
            'rm',
            'affiliates',
            'affiliateCommissionTotals',
            'affiliateOrderCounts',
            'affiliateReferralCounts',
            'affiliateNameMap',
            'rmTotals',
            'walletTransactions',
            'orderHistory',
            'orderCommissionMap',
            'summary'
        ));
    }

    public function walletTransaction(Request $request, User $rm)
    {
        abort_unless($rm->role === 'rm', 404);

        $validated = $request->validate([
            'type'   => 'required|in:credit,debit,request',
            'amount' => 'required|numeric|min:0.01',
            'remark' => 'nullable|string|max:500',
        ]);

        if ($validated['type'] === 'request') {
            WithdrawalRequest::create([
                'user_id'      => $rm->id,
                'amount'       => $validated['amount'],
                'notes'        => $validated['remark'] ?? null,
                'request_type' => 'credit',
                'status'       => 'pending',
            ]);
            return redirect()->back()->with('success', 'Credit request for ₹' . number_format($validated['amount'], 2) . ' submitted. Approve it from Withdrawal Requests.');
        }

        WalletTransaction::create([
            'user_id'    => $rm->id,
            'type'       => $validated['type'],
            'amount'     => $validated['amount'],
            'remark'     => $validated['remark'] ?? null,
            'created_by' => auth()->id(),
            'status'     => 'approved',
        ]);

        $msg = '₹' . number_format($validated['amount'], 2) . ' ' . ($validated['type'] === 'credit' ? 'added to' : 'removed from') . ' wallet.';
        return redirect()->back()->with('success', $msg);
    }

    public function toggleSection(Request $request, User $rm)
    {
        abort_unless(auth()->user()->is_admin, 403);
        abort_unless($rm->role === 'rm', 404);
        $section = $request->input('section');
        $allowed = ['show_referrals'];
        if (!in_array($section, $allowed, true)) {
            return response()->json(['success' => false, 'message' => 'Invalid section.'], 422);
        }
        $perms = $rm->permissions ?? [];
        $perms[$section] = (bool) $request->input('value', true);
        $rm->permissions = $perms;
        $rm->save();
        return response()->json(['success' => true, 'section' => $section, 'value' => $perms[$section]]);
    }

    public function approveWallet(WalletTransaction $transaction)
    {
        abort_unless($transaction->user->role === 'rm', 404);
        $transaction->update(['status' => 'approved']);
        return redirect()->back()->with('success', 'Wallet transaction approved.');
    }

    public function rejectWallet(WalletTransaction $transaction)
    {
        abort_unless($transaction->user->role === 'rm', 404);
        $transaction->update(['status' => 'rejected']);
        return redirect()->back()->with('success', 'Wallet transaction rejected.');
    }

    public function edit(User $rm)
    {
        abort_unless($rm->role === 'rm', 404);
        $managers = User::where('role', 'manager')->orderBy('name')->get(['id', 'name']);
        return view('admin.hierarchy.rms.form', compact('rm', 'managers'));
    }

    public function update(Request $request, User $rm)
    {
        abort_unless($rm->role === 'rm', 404);
        $validated = $this->validateInput($request, $rm->id);

        $data = [
            'name'      => $validated['name'],
            'mobile'    => $validated['mobile'],
            'email'     => $validated['email'],
            'parent_id' => $validated['parent_id'] ?? null,
        ];
        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }
        $rm->update($data);

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
        AffiliateProfile::updateOrCreate(['user_id' => $rm->id], $profileData);

        return redirect()->route('admin.rms.index')->with('success', 'RM updated.');
    }

    public function destroy(User $rm)

    {
        abort_unless($rm->role === 'rm', 404);
        $rm->update(['role' => 'customer', 'parent_id' => null]);
        return redirect()->route('admin.rms.index')->with('success', 'RM removed (demoted to customer).');
    }

    private function validateInput(Request $request, ?int $userId = null): array
    {
        $uniq = $userId ? ',' . $userId : '';
        $rules = [
            'name'           => 'required|string|max:255',
            'mobile'         => 'required|string|max:15|unique:users,mobile' . $uniq,
            'email'          => 'required|email|unique:users,email' . $uniq,
            'parent_id'      => 'required|exists:users,id',
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
            'kyc_doc'        => $userId ? 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048' : 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];
        $rules['password'] = $userId ? 'nullable|string|min:6' : 'required|string|min:6';

        $validated = $request->validate($rules);

        if ($validated['parent_id'] ?? null) {
            $parent = User::findOrFail($validated['parent_id']);
            abort_unless($parent->role === 'manager', 422, 'Parent must be a Manager.');
        }
        return $validated;
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
