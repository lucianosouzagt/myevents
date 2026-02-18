<?php

namespace App\Http\Middleware;

use App\Models\PageView;
use App\Models\VisitSession;
use App\Services\UserAgentParser;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class TrackPageView
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (!Config::get('analytics.enabled')) {
            return $response;
        }
        if (Config::get('analytics.honor_dnt') && $request->headers->get('DNT') === '1') {
            return $response;
        }

        $path = $request->getPathInfo();
        foreach (Config::get('analytics.exclude_prefixes', []) as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return $response;
            }
        }

        try {
            $sessionId = $request->session()->getId();
            $ua = $request->userAgent();
            $deviceType = UserAgentParser::deviceType($ua);
            $browser = UserAgentParser::browser($ua);
            $os = UserAgentParser::os($ua);
            $userId = optional($request->user())->id;
            $ref = $request->headers->get('referer') ?? $request->query('ref');
            $utmSource = $request->query('utm_source');
            $utmMedium = $request->query('utm_medium');
            $utmCampaign = $request->query('utm_campaign');
            $routeName = optional($request->route())->getName();

            $ipHash = null;
            if (Config::get('analytics.anonymize_ip')) {
                $ipHash = hash('sha256', (string) $request->ip());
            }

            $visit = VisitSession::query()
                ->firstOrCreate(
                    ['session_id' => $sessionId],
                    [
                        'user_id' => $userId,
                        'device_type' => $deviceType,
                        'browser' => $browser,
                        'os' => $os,
                        'started_at' => now(),
                    ]
                );

            $view = new PageView([
                'path' => $path,
                'route_name' => $routeName,
                'referrer' => $ref,
                'utm_source' => $utmSource,
                'utm_medium' => $utmMedium,
                'utm_campaign' => $utmCampaign,
                'device_type' => $deviceType,
                'ip_hash' => $ipHash,
                'viewed_at' => now(),
            ]);
            $visit->pageViews()->save($view);

            $visit->increment('pageviews_count');
            $visit->touch();
        } catch (\Throwable $e) {
            // fail silent to avoid impacting the user request
        }

        return $response;
    }
}

