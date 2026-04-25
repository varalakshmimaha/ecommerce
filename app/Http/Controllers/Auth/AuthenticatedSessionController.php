<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
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

        $hasAdminAccess = $user->is_admin || in_array($user->role, ['manager', 'rm'], true);

        if (!$hasAdminAccess) {
            return back()->withErrors([
                'mobile' => 'You do not have access to the admin panel.',
            ])->onlyInput('mobile');
        }

        Auth::login($user);

        $request->session()->regenerate();

        // Redirect manager/rm to their own profile page
        if (!$user->is_admin) {
            if ($user->role === 'manager') {
                return redirect()->route('admin.managers.show', $user);
            }
            if ($user->role === 'rm') {
                return redirect()->route('admin.rms.show', $user);
            }
        }

        return redirect()->intended(route('admin.dashboard'));
    }

    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}

