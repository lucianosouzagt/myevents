<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthenticate
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('admin')->check()) {
            $intended = $request->fullUrl();
            $request->session()->put('url.intended', $intended);
            return redirect()->route('admin.login.form', ['redirect' => $intended]);
        }
        return $next($request);
    }
}

