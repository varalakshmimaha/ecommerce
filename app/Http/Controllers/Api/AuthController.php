<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|string|unique:users,mobile',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'required|string|min:6',
            'name' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Email verification disabled - user is auto-verified
        $user = User::create([
            'mobile' => $request->mobile,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'name' => $request->name,
            'is_verified' => true, // Auto-verify on registration
            'otp' => null,
            'otp_expires_at' => null,
        ]);

        // Auto-login user after registration
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful. You are now logged in.',
            'token' => $token,
            'user' => $user,
        ], 201);
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|string',
            'otp' => 'required|string|size:4',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('mobile', $request->mobile)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        if ($user->otp !== $request->otp) {
            return response()->json(['error' => 'Invalid OTP'], 400);
        }

        if ($user->otp_expires_at < now()) {
            return response()->json(['error' => 'OTP expired'], 400);
        }

        $user->update([
            'is_verified' => true,
            'otp' => null,
            'otp_expires_at' => null,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'OTP verified successfully',
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('mobile', $request->mobile)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        // Email verification disabled - skip verification check
        // if (!$user->is_verified) {
        //     return response()->json(['error' => 'Please verify your account first'], 401);
        // }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile'  => 'required|string',
            'purpose' => 'nullable|in:register,login,reset',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::firstOrCreate(
            ['mobile' => $request->mobile],
            [
                'password'    => Hash::make(Str::random(12)),
                'is_verified' => false,
            ]
        );

        $otp = (string) random_int(1000, 9999);
        $user->update([
            'otp'            => $otp,
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        // TODO: dispatch SMS gateway. For dev, the OTP is returned only when APP_DEBUG is on.
        return response()->json([
            'success' => true,
            'message' => 'OTP sent to your mobile.',
            'data'    => [
                'mobile'     => $user->mobile,
                'expires_in' => 600,
                'debug_otp'  => config('app.debug') ? $otp : null,
            ],
        ]);
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|string|exists:users,mobile',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('mobile', $request->mobile)->first();
        $otp  = (string) random_int(1000, 9999);
        $user->update([
            'otp'            => $otp,
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'OTP sent. Use it to reset your password.',
            'data'    => [
                'mobile'     => $user->mobile,
                'expires_in' => 600,
                'debug_otp'  => config('app.debug') ? $otp : null,
            ],
        ]);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile'       => 'required|string|exists:users,mobile',
            'otp'          => 'required|string|size:4',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('mobile', $request->mobile)->first();

        if ($user->otp !== $request->otp) {
            return response()->json(['success' => false, 'message' => 'Invalid OTP'], 400);
        }

        if (!$user->otp_expires_at || $user->otp_expires_at < now()) {
            return response()->json(['success' => false, 'message' => 'OTP expired'], 400);
        }

        $user->update([
            'password'       => Hash::make($request->new_password),
            'otp'            => null,
            'otp_expires_at' => null,
            'is_verified'    => true,
        ]);

        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully.',
            'data'    => ['token' => $token, 'user' => $user],
        ]);
    }
}

