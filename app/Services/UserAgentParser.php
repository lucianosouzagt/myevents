<?php

namespace App\Services;

class UserAgentParser
{
    public static function deviceType(?string $ua): ?string
    {
        if (!$ua) {
            return null;
        }
        $ua = strtolower($ua);
        if (str_contains($ua, 'mobile') || str_contains($ua, 'iphone') || str_contains($ua, 'android')) {
            return 'mobile';
        }
        if (str_contains($ua, 'ipad') || str_contains($ua, 'tablet')) {
            return 'tablet';
        }
        return 'desktop';
    }

    public static function browser(?string $ua): ?string
    {
        if (!$ua) return null;
        $ua = strtolower($ua);
        return match (true) {
            str_contains($ua, 'edg') => 'Edge',
            str_contains($ua, 'chrome') && !str_contains($ua, 'edg') && !str_contains($ua, 'opr') => 'Chrome',
            str_contains($ua, 'safari') && !str_contains($ua, 'chrome') => 'Safari',
            str_contains($ua, 'firefox') => 'Firefox',
            str_contains($ua, 'opr') || str_contains($ua, 'opera') => 'Opera',
            default => 'Other',
        };
    }

    public static function os(?string $ua): ?string
    {
        if (!$ua) return null;
        $ua = strtolower($ua);
        return match (true) {
            str_contains($ua, 'windows') => 'Windows',
            str_contains($ua, 'mac os x') || str_contains($ua, 'macintosh') => 'macOS',
            str_contains($ua, 'android') => 'Android',
            str_contains($ua, 'iphone') || str_contains($ua, 'ipad') || str_contains($ua, 'ios') => 'iOS',
            str_contains($ua, 'linux') => 'Linux',
            default => 'Other',
        };
    }
}

