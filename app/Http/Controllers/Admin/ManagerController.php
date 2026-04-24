<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

        // Per-manager affiliates count (affiliates under their RMs)
        $affiliatesCounts = [];
        if (!empty($managerIds)) {
            $rmRows = User::whereIn('parent_id', $managerIds)
                ->where('role', 'rm')
                ->get(['id', 'parent_id']);
            $rmToManager = $rmRows->pluck('parent_id', 'id'); // rmId => managerId
            if ($rmRows->isNotEmpty()) {
                $affGrouped = User::whereIn('parent_id', $rmRows->pluck('id')->all())
                    ->where('role', 'affiliate')
                    ->selectRaw('parent_id, COUNT(*) as c')
                    ->groupBy('parent_id')
                    ->pluck('c', 'parent_id');
                foreach ($affGrouped as $rmId => $cnt) {
                    $mgrId = $rmToManager[$rmId] ?? null;
                    if ($mgrId) {
                        $affiliatesCounts[$mgrId] = ($affiliatesCounts[$mgrId] ?? 0) + (int) $cnt;
                    }
                }
            }
        }

        return view('admin.hierarchy.managers.index', compact('managers', 'commissionTotals', 'commissionCounts', 'affiliatesCounts'));
    }

    public function create()
    {
        return view('admin.hierarchy.managers.form', ['manager' => new User()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:15|unique:users,mobile',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        User::create([
            'name' => $validated['name'],
            'mobile' => $validated['mobile'],
            'email' => $validated['email'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => 'manager',
            'affiliate_status' => 'none',
            'is_verified' => true,
        ]);

        return redirect()->route('admin.managers.index')->with('success', 'Manager created.');
    }

    public function show(User $manager)
    {
        abort_unless($manager->role === 'manager', 404);

        // Direct RMs under this manager, each with their affiliates + per-RM stats
        $rms = User::where('parent_id', $manager->id)
            ->where('role', 'rm')
            ->withCount([
                'children as affiliates_count' => fn ($q) => $q->where('role', 'affiliate'),
            ])
            ->with(['children' => function ($q) {
                $q->where('role', 'affiliate')
                  ->orderByDesc('created_at')
                  ->select(['id', 'name', 'mobile', 'email', 'role', 'parent_id', 'referral_code', 'affiliate_status', 'created_at']);
            }])
            ->orderByDesc('created_at')
            ->get();

        // Collect all affiliate ids under this manager (via any of the RMs)
        $affiliateIds = $rms->flatMap(fn ($rm) => $rm->children->pluck('id'))->all();

        // Per-affiliate commission totals (for the affiliate themselves)
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

        // Per-affiliate order counts (orders placed by each affiliate's own customers / themselves)
        $affiliateOrderCounts = [];
        if (!empty($affiliateIds)) {
            $affiliateOrderCounts = Order::whereIn('user_id', $affiliateIds)
                ->selectRaw('user_id, COUNT(*) as c')
                ->groupBy('user_id')
                ->pluck('c', 'user_id')
                ->all();
        }

        // Per-affiliate referrals (people they referred)
        $affiliateReferralCounts = [];
        if (!empty($affiliateIds)) {
            $affiliateReferralCounts = User::whereIn('parent_id', $affiliateIds)
                ->selectRaw('parent_id, COUNT(*) as c')
                ->groupBy('parent_id')
                ->pluck('c', 'parent_id')
                ->all();
        }

        // Per-RM own commission totals (RM earns 3% directly on each downline order)
        $rmCommissionTotals = [];
        $rmIds = $rms->pluck('id')->all();
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

        // Individual RM commission rows (list of actual earnings)
        $rmCommissions = !empty($rmIds)
            ? Commission::with([
                    'order:id,order_number,total_amount,order_status,created_at',
                    'beneficiary:id,name',
                ])
                ->whereIn('beneficiary_user_id', $rmIds)
                ->where('beneficiary_role', 'rm')
                ->orderByDesc('created_at')
                ->limit(50)
                ->get()
            : collect();

        // Manager's own commission totals
        $managerTotals = [
            'pending'  => (float) Commission::forUser($manager->id)->pending()->sum('amount'),
            'approved' => (float) Commission::forUser($manager->id)->approved()->sum('amount'),
            'paid'     => (float) Commission::forUser($manager->id)->paid()->sum('amount'),
        ];

        // Recent commissions earned by the manager
        $managerCommissions = Commission::with('order:id,order_number,total_amount,order_status,created_at')
            ->where('beneficiary_user_id', $manager->id)
            ->orderByDesc('created_at')
            ->limit(25)
            ->get();

        // Orders placed by the affiliates in this manager's tree
        $referralOrders = collect();
        if (!empty($affiliateIds)) {
            $referralOrders = Order::whereIn('user_id', $affiliateIds)
                ->orderByDesc('created_at')
                ->limit(25)
                ->get(['id', 'order_number', 'user_id', 'name', 'total_amount', 'order_status', 'created_at']);
        }

        $summary = [
            'rms_count'        => $rms->count(),
            'affiliates_count' => count($affiliateIds),
            'orders_count'     => !empty($affiliateIds) ? Order::whereIn('user_id', $affiliateIds)->count() : 0,
        ];

        return view('admin.hierarchy.managers.show', compact(
            'manager',
            'rms',
            'affiliateCommissionTotals',
            'affiliateOrderCounts',
            'affiliateReferralCounts',
            'rmCommissionTotals',
            'rmCommissions',
            'managerTotals',
            'managerCommissions',
            'referralOrders',
            'summary'
        ));
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
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:15|unique:users,mobile,' . $manager->id,
            'email' => 'nullable|email|unique:users,email,' . $manager->id,
            'password' => 'nullable|string|min:6',
        ]);

        $data = [
            'name' => $validated['name'],
            'mobile' => $validated['mobile'],
            'email' => $validated['email'] ?? null,
        ];
        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $manager->update($data);

        return redirect()->route('admin.managers.index')->with('success', 'Manager updated.');
    }

    public function destroy(User $manager)
    {
        abort_unless($manager->role === 'manager', 404);
        // Demote instead of hard-delete — their RMs / commissions stay linked.
        $manager->update(['role' => 'customer', 'parent_id' => null]);
        return redirect()->route('admin.managers.index')->with('success', 'Manager removed (demoted to customer).');
    }
}
