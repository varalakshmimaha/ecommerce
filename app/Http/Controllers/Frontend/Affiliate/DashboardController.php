<?php

namespace App\Http\Controllers\Frontend\Affiliate;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\WalletTransaction;
use App\Models\WithdrawalRequest;
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

        $walletBalance      = WalletTransaction::balanceFor($user->id);
        $hasPendingRequest  = WithdrawalRequest::where('user_id', $user->id)->where('status', 'pending')->exists();
        $withdrawalRequests = WithdrawalRequest::where('user_id', $user->id)->orderByDesc('created_at')->limit(10)->get();
        $walletTransactions = WalletTransaction::where('user_id', $user->id)->orderByDesc('created_at')->limit(20)->get();

        return view('frontend.affiliate.dashboard', compact(
            'commissions', 'totals', 'referralUrl', 'user', 'referralsCount',
            'walletBalance', 'hasPendingRequest', 'withdrawalRequests', 'walletTransactions'
        ));
    }

    public function walletRequest(Request $request)
    {
        $user = $request->user();
        abort_unless(in_array($user->role, ['affiliate', 'rm', 'manager'], true), 403);

        $validated = $request->validate([
            'direction' => 'required|in:credit,debit,withdrawal',
            'amount'    => 'required|numeric|min:1',
            'remark'    => 'nullable|string|max:500',
        ]);

        $amount = (float) $validated['amount'];

        if ($validated['direction'] === 'withdrawal') {
            // Withdrawal → pending request, needs admin approval
            WithdrawalRequest::create([
                'user_id'      => $user->id,
                'amount'       => $amount,
                'notes'        => $validated['remark'] ?? null,
                'request_type' => 'withdrawal',
                'status'       => 'pending',
            ]);

            return redirect()->route('user.dashboard', ['tab' => 'wallet'])
                ->with('success', 'Withdrawal request of ₹' . number_format($amount, 2) . ' submitted. Awaiting admin approval.');
        }

        // Add (Credit) or Remove (Debit) → immediate approved wallet transaction
        WalletTransaction::create([
            'user_id'    => $user->id,
            'type'       => $validated['direction'], // 'credit' or 'debit'
            'amount'     => $amount,
            'remark'     => $validated['remark'] ?? null,
            'created_by' => $user->id,
            'status'     => 'approved',
        ]);

        $msg = $validated['direction'] === 'credit'
            ? '₹' . number_format($amount, 2) . ' added to your wallet.'
            : '₹' . number_format($amount, 2) . ' removed from your wallet.';

        return redirect()->route('user.dashboard', ['tab' => 'wallet'])->with('success', $msg);
    }
}
