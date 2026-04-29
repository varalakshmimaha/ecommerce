<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\User;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $referrals = User::where('parent_id', $userId)
            ->select(['id', 'name', 'mobile', 'role', 'affiliate_status', 'referral_code', 'created_at'])
            ->withCount(['orders', 'children as sub_referrals_count'])
            ->orderByDesc('created_at')
            ->paginate((int) $request->query('per_page', 20));

        return response()->json(['success' => true, 'data' => $referrals]);
    }

    public function show(Request $request, $id)
    {
        $userId = $request->user()->id;

        $referral = User::where('parent_id', $userId)
            ->select(['id', 'name', 'mobile', 'role', 'affiliate_status', 'referral_code', 'created_at'])
            ->find($id);

        if (!$referral) {
            return response()->json(['success' => false, 'message' => 'Referral not found'], 404);
        }

        $orderCount = $referral->orders()->count();

        $commissionEarned = (float) Commission::forUser($userId)
            ->whereHas('order', function ($q) use ($referral) {
                $q->where('user_id', $referral->id);
            })->sum('amount');

        $subReferrals = User::where('parent_id', $referral->id)
            ->select(['id', 'name', 'mobile', 'role', 'affiliate_status', 'created_at'])
            ->withCount('orders')
            ->orderByDesc('created_at')->limit(50)->get();

        return response()->json([
            'success' => true,
            'data'    => [
                'referral'          => $referral,
                'order_count'       => $orderCount,
                'commission_earned' => round($commissionEarned, 2),
                'sub_referrals'     => $subReferrals,
            ],
        ]);
    }

    /**
     * Team listing for managers (sees RMs) and RMs (sees affiliates).
     */
    public function team(Request $request)
    {
        $user = $request->user();

        $childRole = match ($user->role) {
            'manager' => 'rm',
            'rm'      => 'affiliate',
            default   => null,
        };

        if (!$childRole) {
            return response()->json([
                'success' => false,
                'message' => 'Team management is available only to Managers and RMs.',
            ], 403);
        }

        $members = User::where('parent_id', $user->id)
            ->where('role', $childRole)
            ->select(['id', 'name', 'mobile', 'role', 'affiliate_status', 'referral_code', 'created_at'])
            ->withCount(['children as referrals_count'])
            ->orderByDesc('created_at')
            ->paginate((int) $request->query('per_page', 20));

        return response()->json([
            'success' => true,
            'data'    => $members,
            'meta'    => ['child_role' => $childRole],
        ]);
    }
}
