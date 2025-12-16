<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('frontend.auth.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'mobile' => ['required', 'string'],
            'password' => ['required'],
        ]);

        $user = \App\Models\User::where('mobile', $credentials['mobile'])->first();

        if (!$user || !\Illuminate\Support\Facades\Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors([
                'mobile' => 'The provided credentials do not match our records.',
            ])->onlyInput('mobile');
        }

        Auth::login($user);

        $request->session()->regenerate();

        // Redirect based on user role
        if ($user->is_admin) {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->intended(route('user.dashboard'));
    }

    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
