<?php

namespace App\Http\Controllers\Frontend\Affiliate;

use App\Http\Controllers\Controller;
use App\Models\AffiliateProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ApplicationController extends Controller
{
    public function create(Request $request)
    {
        $user = $request->user();

        if ($user->affiliate_status === 'pending') {
            return redirect()->route('become.affiliate')->with('info', 'Your application is already under review.');
        }
        if ($user->affiliate_status === 'approved' && $user->role === 'affiliate') {
            return redirect()->route('affiliate.dashboard');
        }

        $profile = $user->affiliateProfile ?? new AffiliateProfile();

        return view('frontend.affiliate.apply', compact('user', 'profile'));
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if ($user->affiliate_status === 'pending') {
            return redirect()->route('become.affiliate')->with('info', 'Your application is already under review.');
        }

        $validated = $request->validate([
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'pincode' => ['required', 'string', 'max:10'],
            'account_holder' => ['required', 'string', 'max:255'],
            'bank_name' => ['required', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'max:30'],
            'ifsc' => ['required', 'string', 'size:11', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/'],
            'upi_id' => ['nullable', 'string', 'max:100'],
            'pan_number' => ['required', 'string', 'size:10', 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]$/'],
            'aadhaar_number' => ['required', 'digits:12'],
            'kyc_doc' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'terms' => ['accepted'],
        ], [
            'ifsc.regex' => 'Enter a valid IFSC code (e.g. HDFC0001234).',
            'pan_number.regex' => 'Enter a valid PAN number (e.g. ABCDE1234F).',
            'aadhaar_number.digits' => 'Enter a valid 12-digit Aadhaar number.',
            'terms.accepted' => 'You must agree to the affiliate terms.',
        ]);

        $profile = AffiliateProfile::firstOrNew(['user_id' => $user->id]);

        if ($request->hasFile('kyc_doc')) {
            if ($profile->kyc_doc_path && Storage::disk('public')->exists($profile->kyc_doc_path)) {
                Storage::disk('public')->delete($profile->kyc_doc_path);
            }
            $profile->kyc_doc_path = $request->file('kyc_doc')->store('affiliate-kyc', 'public');
        }

        $profile->fill([
            'address' => $validated['address'],
            'city' => $validated['city'],
            'state' => $validated['state'],
            'pincode' => $validated['pincode'],
            'account_holder' => $validated['account_holder'],
            'bank_name' => $validated['bank_name'],
            'account_number' => $validated['account_number'],
            'ifsc' => strtoupper($validated['ifsc']),
            'upi_id' => $validated['upi_id'] ?? null,
            'pan_number' => strtoupper($validated['pan_number']),
            'aadhaar_number' => $validated['aadhaar_number'],
            'kyc_verified' => false,
            'rejection_reason' => null,
        ]);
        $profile->user_id = $user->id;
        $profile->save();

        if (!empty($validated['email']) && empty($user->email)) {
            $user->email = $validated['email'];
        }
        $user->affiliate_status = 'pending';
        $user->save();

        return redirect()->route('become.affiliate')->with('success', 'Application submitted. Our team will review it shortly.');
    }
}
