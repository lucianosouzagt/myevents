<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AdminTwoFactorController extends Controller
{
    public function form()
    {
        $user = Auth::guard('admin')->user();
        if (!$user) {
            return redirect()->route('admin.login.form');
        }
        // Gerar código se necessário
        if ($user->two_factor_enabled && !$user->two_factor_code) {
            $code = random_int(100000, 999999);
            \App\Models\AdminUser::query()
                ->whereKey($user->getAuthIdentifier())
                ->update([
                    'two_factor_code' => (string) $code,
                    'two_factor_expires_at' => now()->addMinutes(10),
                    'updated_at' => now(),
                ]);
            // Enviar por e-mail (canal padrão)
            Mail::raw("Seu código de verificação: {$code}", function ($m) use ($user) {
                $m->to($user->email)->subject('Código de verificação (Admin)');
            });
        }
        return view('admin.auth.2fa');
    }

    public function verify(Request $request)
    {
        $data = $request->validate([
            'code' => ['required','digits:6'],
        ]);
        $user = Auth::guard('admin')->user();
        if (!$user) {
            return redirect()->route('admin.login.form');
        }
        if (!$user->two_factor_enabled) {
            return redirect()->route('admin.home');
        }
        if (
            $user->two_factor_code === $data['code']
            && $user->two_factor_expires_at
            && now()->lessThanOrEqualTo($user->two_factor_expires_at)
        ) {
            \App\Models\AdminUser::query()
                ->whereKey($user->getAuthIdentifier())
                ->update([
                    'two_factor_code' => null,
                    'two_factor_expires_at' => null,
                    'updated_at' => now(),
                ]);
            return redirect()->route('admin.home');
        }
        return back()->withErrors(['code' => 'Código inválido ou expirado.']);
    }
}
