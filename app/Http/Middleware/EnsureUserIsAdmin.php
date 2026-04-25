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
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        $user = Auth::user();
        $hasAccess = $user->is_admin || in_array($user->role, ['manager', 'rm'], true);

        if (!$hasAccess) {
            return redirect()->route('user.dashboard')->with('error', 'You do not have admin access.');
        }

        return $next($request);
    }
}
