<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function balance(Request $request)
    {
        $userId  = $request->user()->id;
        $balance = WalletTransaction::balanceFor($userId);

        $pendingCredit = (float) WalletTransaction::where('user_id', $userId)
            ->where('type', 'credit')->where('status', 'pending')->sum('amount');
        $pendingDebit = (float) WalletTransaction::where('user_id', $userId)
            ->where('type', 'debit')->where('status', 'pending')->sum('amount');

        return response()->json([
            'success' => true,
            'data'    => [
                'balance'        => round($balance, 2),
                'pending_credit' => round($pendingCredit, 2),
                'pending_debit'  => round($pendingDebit, 2),
            ],
        ]);
    }

    public function transactions(Request $request)
    {
        $userId = $request->user()->id;

        $query = WalletTransaction::where('user_id', $userId);

        if ($type = $request->query('type')) {
            $query->where('type', $type);
        }
        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $perPage = (int) $request->query('per_page', 20);
        $items   = $query->orderByDesc('created_at')->paginate(min(max($perPage, 1), 50));

        return response()->json(['success' => true, 'data' => $items]);
    }
}
