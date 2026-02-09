<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class MapService
{
    /**
     * Tries to extract latitude and longitude from a Google Maps URL.
     * Returns ['lat' => float, 'lng' => float] or null.
     */
    public function getCoordinatesFromUrl(?string $url): ?array
    {
        if (empty($url)) {
            return null;
        }

        // Cache key based on URL hash to avoid repeated HTTP requests for shortened URLs
        return Cache::remember('gmaps_coords_' . md5($url), 86400, function () use ($url) {
            return $this->extractCoordinates($url);
        });
    }

    protected function extractCoordinates(string $url): ?array
    {
        // 1. Check if it's a valid Google Maps URL (basic check)
        if (!Str::contains($url, ['google.com', 'goo.gl', 'maps.app.goo.gl'])) {
            return null;
        }

        // 2. Resolve Shortened URLs (maps.app.goo.gl or goo.gl)
        if (Str::contains($url, ['goo.gl', 'maps.app.goo.gl'])) {
            try {
                $response = Http::withoutVerifying()->head($url);
                if ($response->redirect()) {
                    $url = $response->header('Location');
                } else {
                    // Fallback to GET if HEAD fails or doesn't redirect (some services require GET)
                     $response = Http::withoutVerifying()->get($url);
                     // Sometimes the final URL is in the browser URL bar after JS redirect, 
                     // but usually Http client follows redirects automatically.
                     // Laravel Http client follows redirects by default.
                     $url = $response->effectiveUri() ? (string) $response->effectiveUri() : $url;
                }
            } catch (\Exception $e) {
                // If resolving fails, we can't extract coordinates
                return null;
            }
        }

        // 3. Priority Extraction: Extract from !3d and !4d (Marker Coordinates)
        // These are more accurate than the viewport coordinates (@)
        // Example: .../data=...!3d-23.5505199!4d-46.6333094
        // Regex now supports integers and floats
        preg_match('/!3d(-?\d+(?:\.\d+)?)!4d(-?\d+(?:\.\d+)?)/', $url, $markerMatches);

        if (isset($markerMatches[1]) && isset($markerMatches[2])) {
            return [
                'lat' => (float) $markerMatches[1],
                'lng' => (float) $markerMatches[2],
            ];
        }

        // 4. Fallback Extraction: Viewport coordinates (@)
        // Example: https://www.google.com/maps/place/.../@-23.5505199,-46.6333094,17z/...
        preg_match('/@(-?\d+(?:\.\d+)?),(-?\d+(?:\.\d+)?)/', $url, $viewportMatches);

        if (isset($viewportMatches[1]) && isset($viewportMatches[2])) {
            return [
                'lat' => (float) $viewportMatches[1],
                'lng' => (float) $viewportMatches[2],
            ];
        }

        // 5. Pattern for search queries ?q=lat,lng or &ll=lat,lng
        // Example: https://maps.google.com/?q=-23.5505,-46.6333
        preg_match('/[?&](?:q|ll)=(-?\d+(?:\.\d+)?),(-?\d+(?:\.\d+)?)/', $url, $queryMatches);

        if (isset($queryMatches[1]) && isset($queryMatches[2])) {
            return [
                'lat' => (float) $queryMatches[1],
                'lng' => (float) $queryMatches[2],
            ];
        }

        return null;
    }
}
