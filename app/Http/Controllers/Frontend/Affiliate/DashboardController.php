<?php

namespace App\Http\Controllers\Frontend\Affiliate;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        abort_unless(in_array($user->role, ['affiliate', 'rm', 'manager'], true), 403);

        $commissions = Commission::with(['order:id,order_number,total_amount,order_status,created_at', 'beneficiary:id,name'])
            ->where('beneficiary_user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(20);

        $totals = [
            'pending' => (float) Commission::forUser($user->id)->pending()->sum('amount'),
            'approved' => (float) Commission::forUser($user->id)->approved()->sum('amount'),
            'paid' => (float) Commission::forUser($user->id)->paid()->sum('amount'),
        ];
        $totals['lifetime'] = $totals['pending'] + $totals['approved'] + $totals['paid'];

        $referralUrl = $user->referral_code
            ? url('/user/register?ref=' . $user->referral_code)
            : null;

        $referralsCount = \App\Models\User::where('parent_id', $user->id)->count();

        return view('frontend.affiliate.dashboard', compact('commissions', 'totals', 'referralUrl', 'user', 'referralsCount'));
    }
}
