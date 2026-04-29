<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AffiliateProfile;
use App\Models\Commission;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AffiliateController extends Controller
{
    public function status(Request $request)
    {
        $user    = $request->user();
        $profile = $user->affiliateProfile;

        return response()->json([
            'success' => true,
            'data'    => [
                'role'             => $user->role,
                'affiliate_status' => $user->affiliate_status,
                'referral_code'    => $user->referral_code,
                'has_kyc'          => (bool) ($profile && $profile->pan_number),
                'kyc_verified'     => (bool) ($profile->kyc_verified ?? false),
                'rejection_reason' => $profile->rejection_reason ?? null,
                'profile'          => $profile,
            ],
        ]);
    }

    public function dashboard(Request $request)
    {
        $user = $request->user();

        if (!in_array($user->role, ['affiliate', 'rm', 'manager'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Affiliate dashboard is only available to approved affiliates.',
            ], 403);
        }

        $totals = [
            'pending'  => (float) Commission::forUser($user->id)->pending()->sum('amount'),
            'approved' => (float) Commission::forUser($user->id)->approved()->sum('amount'),
            'paid'     => (float) Commission::forUser($user->id)->paid()->sum('amount'),
        ];
        $totals['lifetime'] = round($totals['pending'] + $totals['approved'] + $totals['paid'], 2);

        $recentCommissions = Commission::with(['order:id,order_number,total_amount,created_at'])
            ->forUser($user->id)
            ->orderByDesc('created_at')->limit(10)->get();

        return response()->json([
            'success' => true,
            'data'    => [
                'role'                 => $user->role,
                'referral_code'        => $user->referral_code,
                'referral_url'         => $user->referral_code
                    ? url('/user/register?ref=' . $user->referral_code)
                    : null,
                'totals'               => $totals,
                'recent_commissions'   => $recentCommissions,
                'wallet_balance'       => round(WalletTransaction::balanceFor($user->id), 2),
                'has_pending_request'  => WithdrawalRequest::where('user_id', $user->id)
                    ->where('status', 'pending')->exists(),
                'referrals_count'      => User::where('parent_id', $user->id)->count(),
            ],
        ]);
    }

    public function applyKyc(Request $request)
    {
        $user = $request->user();

        if ($user->affiliate_status === 'pending') {
            $existing = $user->affiliateProfile;
            if ($existing && $existing->pan_number) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your application is already under review.',
                ], 422);
            }
        }
        if ($user->affiliate_status === 'approved' && $user->role === 'affiliate') {
            return response()->json([
                'success' => false,
                'message' => 'Already an approved affiliate.',
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'email'          => 'nullable|email|max:255',
            'address'        => 'required|string|max:500',
            'city'           => 'required|string|max:100',
            'state'          => 'required|string|max:100',
            'pincode'        => 'required|string|max:10',
            'account_holder' => 'required|string|max:255',
            'bank_name'      => 'required|string|max:255',
            'account_number' => 'required|string|max:30',
            'ifsc'           => ['required', 'string', 'size:11', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/'],
            'upi_id'         => 'nullable|string|max:100',
            'pan_number'     => ['required', 'string', 'size:10', 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]$/'],
            'aadhaar_number' => 'required|digits:12',
            'kyc_doc'        => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data    = $validator->validated();
        $profile = AffiliateProfile::firstOrNew(['user_id' => $user->id]);

        if ($request->hasFile('kyc_doc')) {
            if ($profile->kyc_doc_path && Storage::disk('public')->exists($profile->kyc_doc_path)) {
                Storage::disk('public')->delete($profile->kyc_doc_path);
            }
            $profile->kyc_doc_path = $request->file('kyc_doc')->store('affiliate-kyc', 'public');
        }

        $profile->fill([
            'address'          => $data['address'],
            'city'             => $data['city'],
            'state'            => $data['state'],
            'pincode'          => $data['pincode'],
            'account_holder'   => $data['account_holder'],
            'bank_name'        => $data['bank_name'],
            'account_number'   => $data['account_number'],
            'ifsc'             => strtoupper($data['ifsc']),
            'upi_id'           => $data['upi_id'] ?? null,
            'pan_number'       => strtoupper($data['pan_number']),
            'aadhaar_number'   => $data['aadhaar_number'],
            'kyc_verified'     => false,
            'rejection_reason' => null,
        ]);
        $profile->user_id = $user->id;
        $profile->save();

        if (!empty($data['email']) && empty($user->email)) {
            $user->email = $data['email'];
        }
        $user->affiliate_status = 'pending';
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'KYC submitted. Our team will review and approve your account shortly.',
            'data'    => ['profile' => $profile, 'affiliate_status' => $user->affiliate_status],
        ], 201);
    }
}
