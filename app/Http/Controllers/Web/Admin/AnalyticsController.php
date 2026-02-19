<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageView;
use App\Models\User;
use App\Models\VisitSession;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeAdmin();

        $from = $request->date('from') ?: now()->subDays(30)->startOfDay();
        $to = $request->date('to') ?: now()->endOfDay();
        [$from, $to] = [CarbonImmutable::parse($from)->startOfDay(), CarbonImmutable::parse($to)->endOfDay()];

        $cacheKey = sprintf('admin.analytics.%s.%s', $from->toDateString(), $to->toDateString());

        $data = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($from, $to) {
            $sessions = VisitSession::query()
                ->whereBetween('started_at', [$from, $to])
                ->get(['id', 'pageviews_count', 'started_at']);

            $totalVisits = $sessions->count();
            $bounces = $sessions->where('pageviews_count', 1)->count();
            $bounceRate = $totalVisits > 0 ? round(($bounces / $totalVisits) * 100, 2) : 0.0;

            $topPages = PageView::query()
                ->whereBetween('viewed_at', [$from, $to])
                ->selectRaw('path, count(*) as views')
                ->groupBy('path')
                ->orderByDesc('views')
                ->limit(10)
                ->get();

            $devices = PageView::query()
                ->whereBetween('viewed_at', [$from, $to])
                ->selectRaw('device_type, count(*) as views')
                ->groupBy('device_type')
                ->pluck('views', 'device_type');

            $sources = PageView::query()
                ->whereBetween('viewed_at', [$from, $to])
                ->selectRaw("coalesce(nullif(utm_source, ''), CASE WHEN referrer IS NULL OR referrer = '' THEN 'direct' ELSE 'referral' END) as source, count(*) as views")
                ->groupBy('source')
                ->pluck('views', 'source');

            $dailyGrowth = User::query()
                ->whereBetween('created_at', [$from, $to])
                ->selectRaw("date_trunc('day', created_at) as day, count(*) as total")
                ->groupBy('day')
                ->orderBy('day')
                ->get();

            $totalUsers = User::count();

            $activeUserIds = PageView::query()
                ->whereBetween('viewed_at', [$from, $to])
                ->join('visit_sessions', 'page_views.visit_session_id', '=', 'visit_sessions.id')
                ->whereNotNull('visit_sessions.user_id')
                ->distinct()
                ->pluck('visit_sessions.user_id');

            $activeUsers = $activeUserIds->count();
            $inactiveUsers = max($totalUsers - $activeUsers, 0);

            $uniqueVisitors = VisitSession::query()
                ->whereBetween('started_at', [$from, $to])
                ->distinct('session_id')
                ->count('session_id');

            $conversions = $totalUsers > 0 && $uniqueVisitors > 0
                ? round(($totalUsers / max($uniqueVisitors, 1)) * 100, 2)
                : 0.0;

            $timeSeries = PageView::query()
                ->whereBetween('viewed_at', [$from, $to])
                ->selectRaw("date_trunc('day', viewed_at) as day, count(*) as views")
                ->groupBy('day')
                ->orderBy('day')
                ->get();

            return compact(
                'totalUsers',
                'dailyGrowth',
                'conversions',
                'activeUsers',
                'inactiveUsers',
                'totalVisits',
                'bounceRate',
                'topPages',
                'devices',
                'sources',
                'timeSeries'
            );
        });

        return view('admin.analytics.dashboard', [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'data' => $data,
        ]);
    }

    public function exportCsv(Request $request)
    {
        $this->authorizeAdmin();
        $from = $request->date('from') ?: now()->subDays(30)->startOfDay();
        $to = $request->date('to') ?: now()->endOfDay();

        $rows = PageView::query()
            ->whereBetween('viewed_at', [$from, $to])
            ->select(['viewed_at', 'path', 'route_name', 'utm_source', 'utm_medium', 'utm_campaign', 'device_type'])
            ->orderBy('viewed_at')
            ->get()
            ->map(function ($pv) {
                return [
                    $pv->viewed_at?->toDateTimeString(),
                    $pv->path,
                    $pv->route_name,
                    $pv->utm_source,
                    $pv->utm_medium,
                    $pv->utm_campaign,
                    $pv->device_type,
                ];
            })
            ->toArray();

        $headers = ['DateTime', 'Path', 'Route', 'UTM Source', 'UTM Medium', 'UTM Campaign', 'Device'];

        $callback = function () use ($headers, $rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $headers);
            foreach ($rows as $r) {
                fputcsv($out, $r);
            }
            fclose($out);
        };

        $filename = 'analytics-' . now()->format('Ymd_His') . '.csv';
        return Response::streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    protected function authorizeAdmin(): void
    {
        abort_unless(Auth::guard('admin')->check(), 403);
    }
}
