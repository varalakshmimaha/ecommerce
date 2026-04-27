<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
use App\Models\AffiliateProfile;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class RegisterController extends Controller
{
    public function create()
    {
        return view('frontend.auth.register');
    }

    public function store(Request $request)
    {
        // Normalize referral code to uppercase before validation
        if ($request->filled('referral_code')) {
            $request->merge(['referral_code' => strtoupper(trim($request->referral_code))]);
        }

        $validated = $request->validate([
            'name'          => ['nullable', 'string', 'max:255'],
            'mobile'        => ['required', 'digits:10', 'unique:users'],
            'email'         => ['nullable', 'string', 'email', 'max:255', 'unique:users'],
            'password'      => ['required', Rules\Password::defaults()],
            'referral_code' => ['nullable', 'string', 'max:20', 'exists:users,referral_code'],
        ], [
            'mobile.digits'        => 'Mobile number must be exactly 10 digits.',
            'referral_code.exists' => 'The referral code is invalid.',
        ]);

        $referrer = null;
        if (!empty($validated['referral_code'])) {
            $referrer = User::where('referral_code', $validated['referral_code'])
                ->whereIn('role', ['affiliate', 'rm', 'manager'])
                ->first();
        }

        $user = DB::transaction(function () use ($request, $validated, $referrer) {
            $isAffiliate = $referrer !== null;

            $newReferralCode = null;
            if ($isAffiliate) {
                $base = strtoupper(substr(preg_replace('/[^a-z]/', '', strtolower($request->name ?? 'usr')), 0, 3));
                $base = str_pad($base, 3, 'X');
                do {
                    $newReferralCode = $base . strtoupper(Str::random(5));
                } while (User::where('referral_code', $newReferralCode)->exists());
            }

            $user = User::create([
                'name'             => $request->name,
                'mobile'           => $request->mobile,
                'email'            => $request->email,
                'password'         => Hash::make($request->password),
                'parent_id'        => $referrer?->id,
                'role'             => $isAffiliate ? 'affiliate' : 'customer',
                'affiliate_status' => $isAffiliate ? 'pending' : 'none',
                'referral_code'    => $newReferralCode,
                'is_verified'      => true,
            ]);

            if ($isAffiliate) {
                AffiliateProfile::create(['user_id' => $user->id]);
            }

            if ($referrer) {
                DB::table('referrals')->insert([
                    'referrer_user_id' => $referrer->id,
                    'referred_user_id' => $user->id,
                    'referral_code'    => $referrer->referral_code,
                    'source'           => $request->filled('ref') ? 'link' : 'manual_code',
                    'ip'               => $request->ip(),
                    'user_agent'       => substr((string) $request->userAgent(), 0, 255),
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);
            }

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        if ($referrer) {
            return redirect()->route('become.affiliate.apply.create')
                ->with('success', 'Account created! Please complete your KYC and bank details to activate your affiliate account.');
        }

        return redirect()->route('user.dashboard')->with('success', 'Registration successful! Welcome to your dashboard.');
    }

    private function generateReferralCode(string $name): string
    {
        $base = strtoupper(Str::of($name)->slug('')->substr(0, 3)->padLeft(3, 'X'));
        do {
            $code = $base . strtoupper(Str::random(5));
        } while (User::where('referral_code', $code)->exists());
        return $code;
    }
}
