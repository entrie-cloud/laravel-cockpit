<?php

return [

    // The default connection name to use for Cockpit API calls.
    'default' => env('COCKPIT_CONNECTION', 'main'),

    // The URL path prefix to trigger cache clearing for Cockpit cache.
    'cache_clear_path' => env('COCKPIT_CACHE_CLEAR_PATH', '/cockpit-clear'),

    // The query parameter name used to bypass caching when accessing Cockpit API routes.
    'cache_ignore_query' => env('COCKPIT_CACHE_IGNORE_QUERY', 'cockpit-ignore'),

    // List of available connections to Cockpit instances.
    'connections' => [

        // The main connection configuration for interacting with Cockpit.
        'main' => [
            // The base URL of the Cockpit API.
            'api_url' => env('COCKPIT_API_URL'),

            // The API key for authenticating requests to the Cockpit API.
            'api_key' => env('COCKPIT_API_KEY'),

            // The URL to access Cockpit's storage, such as uploads or assets.
            'storage_url' => env('COCKPIT_STORAGE_URL'),

            // Lifetime of the cache in seconds. If set to `true`, caching is enabled indefinitely.
            'cache_lifetime' => env('COCKPIT_CACHE_LIFETIME', true),

            // Secret key for ignoring cache when querying Cockpit via `cache_ignore_query`.
            'cache_ignore_secret' => env('COCKPIT_CACHE_IGNORE_SECRET'),

            // Secret key for clearing Cockpit cache via `cache_clear_path`.
            'cache_clear_secret' => env('COCKPIT_CACHE_CLEAR_SECRET'),
        ],

    ],

];
