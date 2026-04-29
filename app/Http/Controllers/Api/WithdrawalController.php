<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WalletTransaction;
use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WithdrawalController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 15);

        $items = WithdrawalRequest::where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->paginate(min(max($perPage, 1), 50));

        return response()->json(['success' => true, 'data' => $items]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if (!in_array($user->role, ['affiliate', 'rm', 'manager'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Withdrawals are only available to approved affiliates.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'notes'  => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if (WithdrawalRequest::where('user_id', $user->id)->where('status', 'pending')->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'You already have a pending withdrawal request.',
            ], 422);
        }

        $balance = WalletTransaction::balanceFor($user->id);
        $amount  = (float) $request->amount;

        if ($amount > $balance + 0.001) {
            return response()->json([
                'success' => false,
                'message' => 'Amount exceeds your available wallet balance of ₹' . number_format($balance, 2) . '.',
            ], 422);
        }

        $req = WithdrawalRequest::create([
            'user_id'      => $user->id,
            'amount'       => $amount,
            'notes'        => $request->notes,
            'request_type' => 'withdrawal',
            'status'       => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Withdrawal request submitted. Awaiting admin approval.',
            'data'    => $req,
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $req = WithdrawalRequest::where('user_id', $request->user()->id)->find($id);
        if (!$req) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $req]);
    }

    public function destroy(Request $request, $id)
    {
        $req = WithdrawalRequest::where('user_id', $request->user()->id)->find($id);
        if (!$req) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }
        if ($req->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending requests can be cancelled.',
            ], 422);
        }
        $req->delete();
        return response()->json(['success' => true, 'message' => 'Request cancelled']);
    }
}
