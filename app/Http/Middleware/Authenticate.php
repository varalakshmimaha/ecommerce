<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        // If accessing admin routes, redirect to admin login
        if ($request->is('admin/*')) {
            return route('login');
        }

        // Otherwise redirect to frontend user login
        return route('user.login');
    }
}

