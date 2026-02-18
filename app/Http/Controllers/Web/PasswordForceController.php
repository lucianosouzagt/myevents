<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PasswordForceController extends Controller
{
    public function form()
    {
        return view('auth.force-password');
    }

    public function update(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'max:64', 'regex:/[A-Z]/', 'regex:/[a-z]/', 'regex:/[0-9]/', 'regex:/[^A-Za-z0-9]/', 'confirmed'],
        ]);
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $recent = DB::table('password_histories')->where('user_id', $user->id)->orderByDesc('id')->limit(5)->pluck('password_hash')->all();
        foreach ($recent as $old) {
            if (Hash::check($request->password, $old)) {
                return back()->withErrors(['password' => 'A nova senha não pode ser igual às 5 últimas.']);
            }
        }
        $hash = Hash::make($request->password);
        DB::transaction(function () use ($user, $hash) {
            DB::table('users')->where('id', $user->id)->update([
                'password' => $hash,
                'must_change_password' => false,
                'updated_at' => now(),
            ]);
            DB::table('password_histories')->insert([
                'user_id' => $user->id,
                'password_hash' => $hash,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        });
        $fresh = \App\Models\User::find($user->id);
        if ($fresh) {
            Auth::login($fresh);
        }
        $request->session()->regenerate();
        return redirect()->intended('/')->with('success', 'Senha alterada com sucesso.');
    }
}
