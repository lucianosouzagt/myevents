<?php

namespace App\Http\Middleware;

use App\Models\AdminUser;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminTwoFactor
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('admin')->user();
        if ($user instanceof AdminUser && $user->two_factor_enabled) {
            if ($request->routeIs('admin.2fa.form') || $request->routeIs('admin.2fa.verify')) {
                return $next($request);
            }
            if ($user->two_factor_code) {
                return redirect()->route('admin.2fa.form');
            }
        }
        return $next($request);
    }
}

