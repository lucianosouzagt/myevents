<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\JwtService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class JwtAuthController extends Controller
{
    public function login(Request $request, JwtService $jwt)
    {
        $data = $request->validate([
            'email' => ['required','email'],
            'password' => ['required'],
        ]);
        $user = User::where('email', $data['email'])->first();
        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json(['error' => 'Credenciais inválidas'], 401);
        }
        $roles = $user->roles()->pluck('slug')->all();
        $token = $jwt->sign([
            'sub' => $user->id,
            'roles' => $roles,
        ], 3600);
        return response()->json(['token' => $token]);
    }
}

