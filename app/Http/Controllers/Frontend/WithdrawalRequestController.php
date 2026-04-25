<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\WalletTransaction;
use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;

class WithdrawalRequestController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();

        if (!in_array($user->role, ['affiliate', 'rm', 'manager'], true)) {
            return redirect()->back()->with('error', 'Only affiliates, RMs, and managers can request withdrawals.');
        }

        $walletBalance = WalletTransaction::balanceFor($user->id);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'notes'  => 'nullable|string|max:500',
        ]);

        if ((float)$validated['amount'] > $walletBalance + 0.001) {
            return redirect()->back()->with('error', 'Requested amount exceeds your wallet balance of ₹' . number_format($walletBalance, 2) . '.');
        }

        $pending = WithdrawalRequest::where('user_id', $user->id)->where('status', 'pending')->exists();
        if ($pending) {
            return redirect()->back()->with('error', 'You already have a pending withdrawal request. Please wait for it to be processed.');
        }

        WithdrawalRequest::create([
            'user_id' => $user->id,
            'amount'  => $validated['amount'],
            'notes'   => $validated['notes'] ?? null,
            'status'  => 'pending',
        ]);

        return redirect()->back()->with('success', 'Withdrawal request of ₹' . number_format($validated['amount'], 2) . ' submitted. Admin will process it shortly.');
    }
}
