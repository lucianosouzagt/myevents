<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = [];
        $urls[] = [
            'loc' => url('/'),
            'priority' => '1.0',
            'changefreq' => 'daily',
        ];

        $events = Event::query()->where('is_public', true)->orderByDesc('updated_at')->limit(500)->get(['id', 'updated_at']);
        foreach ($events as $e) {
            $urls[] = [
                'loc' => route('events.show', ['id' => $e->id]),
                'lastmod' => optional($e->updated_at)->toAtomString(),
                'priority' => '0.7',
                'changefreq' => 'weekly',
            ];
        }

        $content = view('sitemap.xml', compact('urls'))->render();
        return new Response($content, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}

