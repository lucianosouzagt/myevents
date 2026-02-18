<?php

namespace App\Services;

class JwtService
{
    private string $algo = 'HS256';

    private function b64url(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function b64urlDecode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }

    public function sign(array $payload, int $ttlSeconds = 3600): string
    {
        $header = ['typ' => 'JWT', 'alg' => $this->algo];
        $now = time();
        $payload = array_merge($payload, ['iat' => $now, 'exp' => $now + $ttlSeconds]);
        $secret = config('app.key');
        $h = $this->b64url(json_encode($header));
        $p = $this->b64url(json_encode($payload));
        $sig = $this->b64url(hash_hmac('sha256', $h.'.'.$p, $secret, true));
        return $h.'.'.$p.'.'.$sig;
    }

    public function verify(string $token): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) return null;
        [$h, $p, $s] = $parts;
        $secret = config('app.key');
        $expected = $this->b64url(hash_hmac('sha256', $h.'.'.$p, $secret, true));
        if (!hash_equals($expected, $s)) return null;
        $payload = json_decode($this->b64urlDecode($p), true);
        if (!is_array($payload)) return null;
        if (($payload['exp'] ?? 0) < time()) return null;
        return $payload;
    }
}

