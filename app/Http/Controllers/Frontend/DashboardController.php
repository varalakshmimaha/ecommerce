<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\Order;
use App\Models\Query;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load(['orders' => function ($query) {
            $query->orderBy('created_at', 'desc')->with('items');
        }]);

        // Upline chain (who referred me, walking up parent_id)
        $upline = [];
        $node = $user->parent_id ? User::find($user->parent_id) : null;
        $guard = 0;
        while ($node && $guard++ < 10) {
            $upline[] = $node;
            $node = $node->parent_id ? User::find($node->parent_id) : null;
        }

        // Direct referrals (my immediate referrals) with stats + their own sub-referrals
        $referrals = User::where('parent_id', $user->id)
            ->withCount([
                'children as sub_count',
                'orders as orders_count',
            ])
            ->with(['children' => function ($q) {
                $q->orderByDesc('created_at')
                  ->withCount('orders as orders_count')
                  ->select(['id', 'name', 'mobile', 'role', 'parent_id', 'referral_code', 'created_at']);
            }])
            ->orderByDesc('created_at')
            ->get(['id', 'name', 'mobile', 'role', 'referral_code', 'created_at', 'parent_id']);

        $referralIds = $referrals->pluck('id')->all();

        // Per-referral: total order subtotal they've generated (base for my commission)
        // + total commission I earned from each referral's orders
        $baseByReferral = [];
        $commByReferral = [];
        if (!empty($referralIds)) {
            // "Base" = sum of subtotals of orders placed by each referral (I earn % of this)
            $baseByReferral = \App\Models\Order::whereIn('user_id', $referralIds)
                ->selectRaw('user_id, SUM(subtotal) as total')
                ->groupBy('user_id')
                ->pluck('total', 'user_id')
                ->all();

            // "Commission" = sum of commissions I earned from orders placed by my referrals
            $commByReferral = Commission::where('beneficiary_user_id', $user->id)
                ->whereIn('order_id', function ($q) use ($referralIds) {
                    $q->select('id')->from('orders')->whereIn('user_id', $referralIds);
                })
                ->join('orders', 'commissions.order_id', '=', 'orders.id')
                ->selectRaw('orders.user_id as referral_user_id, SUM(commissions.amount) as total')
                ->groupBy('orders.user_id')
                ->pluck('total', 'referral_user_id')
                ->all();
        }

        $referralLink = $user->referral_code ? url('/user/register?ref=' . $user->referral_code) : null;

        // Commission totals + recent commissions for the current user
        $commissionTotals = [
            'pending'  => (float) Commission::forUser($user->id)->pending()->sum('amount'),
            'approved' => (float) Commission::forUser($user->id)->approved()->sum('amount'),
            'paid'     => (float) Commission::forUser($user->id)->paid()->sum('amount'),
        ];
        $commissionTotals['lifetime'] = $commissionTotals['pending'] + $commissionTotals['approved'] + $commissionTotals['paid'];

        $recentCommissions = Commission::with('order:id,order_number,total_amount,order_status,created_at')
            ->where('beneficiary_user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(15)
            ->get();

        $walletBalance      = WalletTransaction::balanceFor($user->id);
        $totalAdded         = (float) WalletTransaction::where('user_id', $user->id)->where('type', 'credit')->where('status', 'approved')->sum('amount');
        $totalRemoved       = (float) WalletTransaction::where('user_id', $user->id)->where('type', 'debit')->where('status', 'approved')->sum('amount');
        $hasPendingRequest  = WithdrawalRequest::where('user_id', $user->id)->where('status', 'pending')->where('request_type', 'withdrawal')->exists();
        $withdrawalRequests = WithdrawalRequest::where('user_id', $user->id)->where('request_type', 'withdrawal')->orderByDesc('created_at')->limit(20)->get();
        $totalRequested     = (float) WithdrawalRequest::where('user_id', $user->id)->where('request_type', 'withdrawal')->sum('amount');
        $walletTransactions = WalletTransaction::with('creator:id,name')->where('user_id', $user->id)->where('status', 'approved')->orderByDesc('created_at')->limit(15)->get();
        $referralsCount     = $referrals->count();

        return view('frontend.dashboard', compact(
            'user', 'upline', 'referrals', 'referralLink', 'commissionTotals',
            'recentCommissions', 'baseByReferral', 'commByReferral',
            'walletBalance', 'totalAdded', 'totalRemoved',
            'hasPendingRequest', 'withdrawalRequests', 'totalRequested',
            'walletTransactions', 'referralsCount'
        ));
    }

    public function profile()
    {
        $user = Auth::user();
        return view('frontend.dashboard.profile', compact('user'));
    }

    public function referralShow(User $referral)
    {
        $me = Auth::user();
        // Security: only allow viewing direct referrals
        abort_unless($referral->parent_id === $me->id, 403, 'You can only view your own direct referrals.');

        // Their affiliates (people they referred)
        $subReferrals = User::where('parent_id', $referral->id)
            ->withCount('orders as orders_count')
            ->orderByDesc('created_at')
            ->get(['id', 'name', 'mobile', 'role', 'referral_code', 'created_at', 'parent_id']);

        // Their own orders + my commission from them
        $ordersCount = \App\Models\Order::where('user_id', $referral->id)->count();
        $baseTotal   = (float) \App\Models\Order::where('user_id', $referral->id)->sum('subtotal');
        $commTotal   = (float) Commission::where('beneficiary_user_id', $me->id)
            ->whereIn('order_id', function ($q) use ($referral) {
                $q->select('id')->from('orders')->where('user_id', $referral->id);
            })->sum('amount');

        $myRate = ['affiliate' => 5, 'rm' => 3, 'manager' => 2][$me->role] ?? 0;

        return view('frontend.referral-show', compact('referral', 'subReferrals', 'ordersCount', 'baseTotal', 'commTotal', 'myRate', 'me'));
    }

    public function queries()
    {
        $queries = Query::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
        return view('frontend.dashboard.queries', compact('queries'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return back()->with('success', 'Profile updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('success', 'Password changed successfully!');
    }

    public function createQuery(Request $request)
    {
        $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'order_number' => ['nullable', 'string'],
            'message' => ['required', 'string'],
        ]);

        Query::create([
            'user_id' => Auth::id(),
            'subject' => $request->subject,
            'order_number' => $request->order_number,
            'message' => $request->message,
            'status' => 'open',
        ]);

        return back()->with('success', 'Query submitted successfully!');
    }

    public function downloadInvoice($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->with('items', 'addresses', 'user')
            ->firstOrFail();

        //dd($order->addresses);

        return view('frontend.invoice', compact('order'));
    }
}
