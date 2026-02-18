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
        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages(['email' => 'Credenciais inválidas.']);
        }
        $request->session()->regenerate();
        $user = Auth::user();
        if (!$user || !method_exists($user, 'hasRole') || !$user->hasRole('admin')) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            throw ValidationException::withMessages(['email' => 'Acesso restrito a administradores.']);
        }
        return redirect()->intended('/admin');
    }
}
