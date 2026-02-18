<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AdminLoginController extends Controller
{
    public function show()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required','email'],
            'password' => ['required'],
        ]);
        $remember = (bool) $request->boolean('remember');
        if (!Auth::guard('admin')->attempt($credentials, $remember)) {
            throw ValidationException::withMessages(['email' => 'Credenciais inválidas.']);
        }
        $request->session()->regenerate();
        $user = Auth::guard('admin')->user();
        if (!$user) {
            Auth::guard('admin')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            throw ValidationException::withMessages(['email' => 'Falha ao iniciar sessão administrativa.']);
        }
        return redirect()->intended('/admin');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login.form');
    }
}
