<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
        $validated = $this->validateInput($request);

        User::create([
            'name' => $validated['name'],
            'mobile' => $validated['mobile'],
            'email' => $validated['email'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => 'rm',
            'parent_id' => $validated['parent_id'] ?? null,
            'affiliate_status' => 'none',
            'is_verified' => true,
        ]);

        return redirect()->route('admin.rms.index')->with('success', 'RM created.');
    }

    public function show(User $rm)
    {
        abort_unless($rm->role === 'rm', 404);

        $rm->load('parent:id,name,role');

        // Affiliates directly under this RM
        $affiliates = User::where('parent_id', $rm->id)
            ->where('role', 'affiliate')
            ->orderByDesc('created_at')
            ->get(['id', 'name', 'mobile', 'email', 'referral_code', 'affiliate_status', 'created_at']);

        $affiliateIds = $affiliates->pluck('id')->all();

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

        // RM's own commission totals (3% of each downline order)
        $rmTotals = [
            'pending'  => (float) Commission::forUser($rm->id)->pending()->sum('amount'),
            'approved' => (float) Commission::forUser($rm->id)->approved()->sum('amount'),
            'paid'     => (float) Commission::forUser($rm->id)->paid()->sum('amount'),
        ];

        // Recent commissions for this RM
        $rmCommissions = Commission::with('order:id,order_number,total_amount,order_status,created_at')
            ->where('beneficiary_user_id', $rm->id)
            ->orderByDesc('created_at')
            ->limit(25)
            ->get();

        // Orders placed by affiliates under this RM
        $referralOrders = !empty($affiliateIds)
            ? Order::whereIn('user_id', $affiliateIds)->orderByDesc('created_at')->limit(25)->get(['id', 'order_number', 'user_id', 'name', 'total_amount', 'order_status', 'created_at'])
            : collect();

        $summary = [
            'affiliates_count' => $affiliates->count(),
            'orders_count'     => !empty($affiliateIds) ? Order::whereIn('user_id', $affiliateIds)->count() : 0,
        ];

        return view('admin.hierarchy.rms.show', compact(
            'rm',
            'affiliates',
            'affiliateCommissionTotals',
            'affiliateOrderCounts',
            'affiliateReferralCounts',
            'rmTotals',
            'rmCommissions',
            'referralOrders',
            'summary'
        ));
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
            'name' => $validated['name'],
            'mobile' => $validated['mobile'],
            'email' => $validated['email'] ?? null,
            'parent_id' => $validated['parent_id'] ?? null,
        ];
        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $rm->update($data);

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
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:15|unique:users,mobile' . $uniq,
            'email' => 'nullable|email|unique:users,email' . $uniq,
            'parent_id' => 'nullable|exists:users,id',
        ];
        $rules['password'] = $userId ? 'nullable|string|min:6' : 'required|string|min:6';

        $validated = $request->validate($rules);

        if ($validated['parent_id'] ?? null) {
            $parent = User::findOrFail($validated['parent_id']);
            abort_unless($parent->role === 'manager', 422, 'Parent must be a Manager.');
        }
        return $validated;
    }
}
