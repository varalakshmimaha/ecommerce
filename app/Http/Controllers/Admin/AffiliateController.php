<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AffiliateProfile;
use App\Models\Commission;
use App\Models\Order;
use App\Models\Referral;
use App\Models\User;
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
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:15|unique:users,mobile',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'required|string|min:6',
            'manager_id' => 'nullable|exists:users,id',
            'rm_id' => 'nullable|exists:users,id',
        ]);

        $parentId = null;
        if (!empty($validated['rm_id'])) {
            $rm = User::findOrFail($validated['rm_id']);
            if ($rm->role !== 'rm') {
                return back()->with('error', 'Selected RM is not a valid Relationship Manager.');
            }
            $parentId = $rm->id;
        } elseif (!empty($validated['manager_id'])) {
            $manager = User::findOrFail($validated['manager_id']);
            if ($manager->role !== 'manager') {
                return back()->with('error', 'Selected Manager is not valid.');
            }
            $parentId = $manager->id;
        }

        $user = User::create([
            'name' => $validated['name'],
            'mobile' => $validated['mobile'],
            'email' => $validated['email'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => 'affiliate',
            'parent_id' => $parentId,
            'affiliate_status' => 'approved',
            'approved_at' => now(),
            'referral_code' => $this->generateReferralCode((object)['name' => $validated['name']]),
            'is_verified' => true,
        ]);

        return redirect()->route('admin.affiliates.index')->with('success', "Affiliate created. Referral code: {$user->referral_code}");
    }

    public function show(User $user)
    {
        $user->load(['parent:id,name,role', 'affiliateProfile']);

        $commissions = Commission::with('order:id,order_number,total_amount,order_status,created_at')
            ->where('beneficiary_user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(25)->get();

        $totals = [
            'pending' => (float) Commission::forUser($user->id)->pending()->sum('amount'),
            'approved' => (float) Commission::forUser($user->id)->approved()->sum('amount'),
            'paid' => (float) Commission::forUser($user->id)->paid()->sum('amount'),
        ];

        $referredUsers = User::where('parent_id', $user->id)->orderByDesc('created_at')->limit(20)->get(['id','name','mobile','role','created_at']);
        $referredUsersCount = User::where('parent_id', $user->id)->count();

        $referredOrders = Order::whereIn('user_id', User::where('parent_id', $user->id)->pluck('id'))
            ->orderByDesc('created_at')->limit(20)->get(['id','order_number','name','total_amount','order_status','created_at']);

        return view('admin.hierarchy.affiliate-show', compact('user','commissions','totals','referredUsers','referredUsersCount','referredOrders'));
    }

    public function edit(User $user)
    {
        $parents = User::whereIn('role', ['rm', 'manager'])->orderBy('role')->orderBy('name')->get(['id', 'name', 'role']);
        return view('admin.hierarchy.affiliate-edit', compact('user', 'parents'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:15|unique:users,mobile,' . $user->id,
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'parent_id' => 'nullable|exists:users,id',
            'referral_code' => 'nullable|string|max:20|unique:users,referral_code,' . $user->id,
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

        $data = [
            'name' => $validated['name'],
            'mobile' => $validated['mobile'],
            'email' => $validated['email'] ?? null,
            'parent_id' => $validated['parent_id'] ?? null,
            'referral_code' => $validated['referral_code'] ?? $user->referral_code,
        ];
        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }
        $user->update($data);

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
