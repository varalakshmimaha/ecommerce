<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Mail\NewUserCredentials;


class ForgotPasswordController extends Controller
{
    public function create()
    {
        return view('frontend.auth.forgot-password');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'We can not find a user with that email address.']);
        }

        $token = Password::getRepository()->create($user);

        $user->sendPasswordResetNotification($token);

        return back()->with('status', 'We have e-mailed your password reset link!');
    }
}
