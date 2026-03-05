<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            // If logged in but not admin, redirect to user dashboard
            if (Auth::check()) {
                return redirect()->route('user.dashboard')->with('error', 'You do not have admin access.');
            }
            return redirect()->route('login');
        }

        return $next($request);
    }
}
