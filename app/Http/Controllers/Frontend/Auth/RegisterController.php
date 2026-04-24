<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisterController extends Controller
{
    public function create()
    {
        return view('frontend.auth.register');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'mobile' => ['required', 'digits:10', 'unique:users'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', Rules\Password::defaults()],
            'referral_code' => ['nullable', 'string', 'max:20', 'exists:users,referral_code'],
        ], [
            'mobile.digits' => 'Mobile number must be exactly 10 digits.',
            'referral_code.exists' => 'The referral code is invalid.',
        ]);

        $referrer = null;
        if (!empty($validated['referral_code'])) {
            $referrer = User::where('referral_code', $validated['referral_code'])
                ->whereIn('role', ['affiliate', 'rm', 'manager'])
                ->first();
        }

        $user = DB::transaction(function () use ($request, $referrer) {
            $user = User::create([
                'name' => $request->name,
                'mobile' => $request->mobile,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'parent_id' => $referrer?->id,
            ]);

            if ($referrer) {
                DB::table('referrals')->insert([
                    'referrer_user_id' => $referrer->id,
                    'referred_user_id' => $user->id,
                    'referral_code' => $referrer->referral_code,
                    'source' => $request->filled('ref') ? 'link' : 'manual_code',
                    'ip' => $request->ip(),
                    'user_agent' => substr((string) $request->userAgent(), 0, 255),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('user.dashboard')->with('success', 'Registration successful! Welcome to your dashboard.');
    }
}
