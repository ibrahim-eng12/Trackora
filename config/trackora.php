<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Route Prefix
    |--------------------------------------------------------------------------
    |
    | The prefix for all Trackora dashboard routes.
    |
    */
    'route_prefix' => 'trackora', // You can change the route for the dashboard of trackora HERE!
    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    |
    | The middleware applied to the Trackora dashboard routes.
    |
    */
    'middleware' => ['web', 'auth'], // here you can add Role middleware like role:admin to allow only admin auth access
    /*
    |--------------------------------------------------------------------------
    | Allowed Users
    |--------------------------------------------------------------------------
    |
    | Users allowed to access the Trackora dashboard. Leave empty to allow
    | all authenticated users. Can be user IDs or email addresses.
    |
    */
    'allowed_users' => [], // here you can add IDs like 1 OR 1, 2  to allow only these users dashboard access
    /*
    |--------------------------------------------------------------------------
    | Tracking Enabled
    |--------------------------------------------------------------------------
    |
    | Enable or disable visitor tracking globally.
    |
    */
    'enabled' => env('TRACKORA_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Track Authenticated Users
    |--------------------------------------------------------------------------
    |
    | Whether to track visits from authenticated users.
    |
    */
    'track_authenticated' => true,

    /*
    |--------------------------------------------------------------------------
    | Track Bots
    |--------------------------------------------------------------------------
    |
    | Whether to track visits from known bots/crawlers.
    |
    */
    'track_bots' => false,

    /*
    |--------------------------------------------------------------------------
    | Excluded Paths
    |--------------------------------------------------------------------------
    |
    | Paths that should not be tracked. Supports wildcards (*).
    |
    */
    'excluded_paths' => [
        'admin/*',
        'api/*',
        'livewire/*',
        '_debugbar/*',
        'telescope/*',
        'horizon/*',
        'trackora/*',
    ],

    /*
    |--------------------------------------------------------------------------
    | Excluded IPs
    |--------------------------------------------------------------------------
    |
    | IP addresses that should not be tracked.
    |
    */
    'excluded_ips' => [],

    /*
    |--------------------------------------------------------------------------
    | Geolocation
    |--------------------------------------------------------------------------
    |
    | Enable geolocation lookup for visitor IP addresses.
    | Uses the free ip-api.com service by default.
    |
    */
    'geolocation' => [
        'enabled' => true,
        'provider' => 'ip-api', // Currently only ip-api is supported
    ],

    /*
    |--------------------------------------------------------------------------
    | Data Retention
    |--------------------------------------------------------------------------
    |
    | Number of days to keep visitor records. Set to null to keep forever.
    |
    */
    'retention_days' => 90,

    /*
    |--------------------------------------------------------------------------
    | Table Name
    |--------------------------------------------------------------------------
    |
    | The database table name for storing visitor records.
    |
    */
    'table_name' => 'trackora_visits',

    /*
    |--------------------------------------------------------------------------
    | Dashboard Settings
    |--------------------------------------------------------------------------
    |
    | Settings for the Trackora dashboard.
    |
    */
    'dashboard' => [
        'per_page' => 25,
        'default_period' => 30, // Days
        'chart_colors' => [
            'primary' => '#3b82f6',
            'secondary' => '#10b981',
        ],
    ],
];
