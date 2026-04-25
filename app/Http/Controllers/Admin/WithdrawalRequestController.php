<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WalletTransaction;
use App\Models\WithdrawalRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WithdrawalRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = WithdrawalRequest::with('user:id,name,mobile,role');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%$s%")->orWhere('mobile', 'like', "%$s%"));
        }

        $requests = $query->orderByDesc('created_at')->paginate(25)->withQueryString();

        $pending = WithdrawalRequest::where('status', 'pending')->get()->loadMissing('user:id,name,mobile,role');

        // KPI data
        $lifetimeTotal  = (float) WithdrawalRequest::where('status', 'approved')->sum('amount');
        $totalWallet    = (float) DB::table('wallet_transactions')
            ->selectRaw('SUM(CASE WHEN type="credit" THEN amount ELSE -amount END) as bal')
            ->value('bal');
        $totalMembers   = User::whereIn('role', ['affiliate', 'rm', 'manager'])->count();

        return view('admin.hierarchy.withdrawal-requests.index', compact(
            'requests', 'pending', 'lifetimeTotal', 'totalWallet', 'totalMembers'
        ));
    }

    public function show(WithdrawalRequest $withdrawalRequest)
    {
        $withdrawalRequest->load('user', 'reviewer');
        return view('admin.hierarchy.withdrawal-requests.show', compact('withdrawalRequest'));
    }

    public function edit(WithdrawalRequest $withdrawalRequest)
    {
        $withdrawalRequest->load('user', 'reviewer');
        return view('admin.hierarchy.withdrawal-requests.edit', compact('withdrawalRequest'));
    }

    public function approve(WithdrawalRequest $withdrawalRequest)
    {
        if ($withdrawalRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending requests can be approved.');
        }

        $isCredit = ($withdrawalRequest->request_type ?? 'withdrawal') === 'credit';

        if (!$isCredit) {
            $walletBalance = WalletTransaction::balanceFor($withdrawalRequest->user_id);
            if ($withdrawalRequest->amount > $walletBalance + 0.001) {
                return redirect()->back()->with('error', 'Insufficient wallet balance for this request.');
            }
        }

        DB::transaction(function () use ($withdrawalRequest, $isCredit) {
            WalletTransaction::create([
                'user_id'    => $withdrawalRequest->user_id,
                'type'       => $isCredit ? 'credit' : 'debit',
                'amount'     => $withdrawalRequest->amount,
                'remark'     => $isCredit ? 'Credit request approved by admin' : 'Withdrawal approved by admin',
                'created_by' => auth()->id(),
                'status'     => 'approved',
            ]);

            $withdrawalRequest->update([
                'status'      => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);
        });

        $msg = $isCredit
            ? '₹' . number_format($withdrawalRequest->amount, 2) . ' credited to wallet.'
            : '₹' . number_format($withdrawalRequest->amount, 2) . ' withdrawal approved and wallet debited.';

        return redirect()->back()->with('success', $msg);
    }

    public function reject(Request $request, WithdrawalRequest $withdrawalRequest)
    {
        if ($withdrawalRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending requests can be rejected.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        $withdrawalRequest->update([
            'status'           => 'rejected',
            'rejection_reason' => $validated['rejection_reason'] ?? 'Request rejected by admin.',
            'reviewed_by'      => auth()->id(),
            'reviewed_at'      => now(),
        ]);

        return redirect()->back()->with('success', 'Withdrawal request rejected.');
    }
}
