<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminTimeout
{
    public function handle(Request $request, Closure $next): Response
    {
        $lifetime = 15 * 60; // 15 minutes
        $now = time();
        $last = (int) $request->session()->get('admin_last_activity_at', $now);
        if (Auth::guard('admin')->check() && ($now - $last) > $lifetime) {
            Auth::guard('admin')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('admin.login.form')->with('error', 'Sessão admin expirada.');
        }
        $request->session()->put('admin_last_activity_at', $now);
        return $next($request);
    }
}

