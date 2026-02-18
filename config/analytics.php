<?php

return [
    'enabled' => env('ANALYTICS_ENABLED', true),
    'honor_dnt' => env('ANALYTICS_HONOR_DNT', true),
    'anonymize_ip' => env('ANALYTICS_ANONYMIZE_IP', true),
    'track_paths' => [
        // you can exclude paths by prefix (e.g., '/admin')
    ],
    'exclude_prefixes' => [
        '/admin', // do not track admin pages
        '/api',   // do not track API
    ],
];

