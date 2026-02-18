<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class InactivityTimeout
{
    public function handle(Request $request, Closure $next): Response
    {
        $lifetime = 30 * 60; // 30 minutes
        $now = time();
        $last = (int) $request->session()->get('last_activity_at', $now);

        if (Auth::check() && ($now - $last) > $lifetime) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('error', 'Sessão expirada por inatividade.');
        }

        $request->session()->put('last_activity_at', $now);
        return $next($request);
    }
}

