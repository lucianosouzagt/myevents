<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\JwtService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JwtAuthenticate
{
    public function __construct(private JwtService $jwt) {}

    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $auth = $request->header('Authorization', '');
        if (!str_starts_with($auth, 'Bearer ')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $token = substr($auth, 7);
        $payload = $this->jwt->verify($token);
        if (!$payload) {
            return response()->json(['error' => 'Invalid token'], 401);
        }
        $user = User::find($payload['sub'] ?? null);
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        if (!empty($roles)) {
            $userRoles = $user->roles()->pluck('slug')->all();
            foreach ($roles as $r) {
                if (!in_array($r, $userRoles, true)) {
                    return response()->json(['error' => 'Forbidden'], 403);
                }
            }
        }
        $request->attributes->set('jwt_user', $user);
        return $next($request);
    }
}

