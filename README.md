<p align="center">
  <img src="logo.png" alt="Trackora" width="400">
</p>

# Trackora

A powerful Laravel package to track and analyze website visitors with detailed statistics, browser detection, geolocation, and a beautiful dashboard with light/dark mode support.

## Features

- Track website visitors automatically with middleware
- Browser and platform detection
- Device type detection (Desktop, Mobile, Tablet)
- Geolocation support (country, city)
- Unique visitor tracking per day
- Beautiful responsive dashboard with statistics
- Light/Dark mode toggle with persistent preference
- Export data to CSV or JSON
- Configurable data retention
- Bot/crawler filtering
- API endpoint for statistics

## Requirements

- PHP 8.1+
- Laravel 10.0+

## Installation

Install the package via Composer:

```bash
composer require ibrahim-eng12/trackora
```

Publish the configuration, migration, and asset files:

```bash
php artisan vendor:publish --tag=trackora-config
php artisan vendor:publish --tag=trackora-migrations
php artisan vendor:publish --tag=trackora-assets
```

Run the migrations:

```bash
php artisan migrate
```

## Configuration

After publishing, the configuration file will be available at `config/trackora.php`:

```php
return [
    // URL prefix for dashboard (e.g., /trackora)
    'route_prefix' => 'trackora',

    // Middleware for dashboard routes
    'middleware' => ['web', 'auth'],

    // Users allowed to access dashboard (empty = all authenticated users)
    'allowed_users' => [],

    // Enable/disable tracking globally
    'enabled' => env('TRACKORA_ENABLED', true),

    // Track authenticated users
    'track_authenticated' => true,

    // Track bots/crawlers
    'track_bots' => false,

    // Paths to exclude from tracking (supports wildcards)
    'excluded_paths' => [
        'admin/*',
        'api/*',
        'livewire/*',
        '_debugbar/*',
    ],

    // IPs to exclude from tracking
    'excluded_ips' => [],

    // Geolocation settings
    'geolocation' => [
        'enabled' => true,
        'provider' => 'ip-api',
    ],

    // Data retention (days, null = forever)
    'retention_days' => 90,

    // Database table name
    'table_name' => 'trackora_visits',

    // Dashboard settings
    'dashboard' => [
        'per_page' => 25,
        'default_period' => 30,
        'chart_colors' => [
            'primary' => '#3b82f6',
            'secondary' => '#10b981',
        ],
    ],
];
```

## Usage

### Automatic Tracking with Middleware

Add the tracking middleware to your routes or middleware groups:

**Option 1: Global middleware (in `bootstrap/app.php` for Laravel 11+):**

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
        \IbrahimEng12\Trackora\Http\Middleware\TrackVisitor::class,
    ]);
})
```

**Option 2: Route middleware (in `app/Http/Kernel.php` for Laravel 10):**

```php
protected $middlewareGroups = [
    'web' => [
        // ... other middleware
        \IbrahimEng12\Trackora\Http\Middleware\TrackVisitor::class,
    ],
];
```

**Option 3: Per-route middleware:**

```php
Route::middleware(['trackora.track'])->group(function () {
    // Your routes here
});
```

### Manual Tracking

You can also track visitors manually:

```php
use IbrahimEng12\Trackora\Models\Visitor;

// Track current request
Visitor::track($request);

// Track with custom page
Visitor::track($request, 'custom-page-name');
```

### Accessing the Dashboard

Visit `/trackora` (or your configured route prefix) to access the dashboard.

### Statistics Methods

The `Visitor` model provides several static methods for statistics:

```php
use IbrahimEng12\Trackora\Models\Visitor;

// Counts
Visitor::getTotalCount();           // Total visits
Visitor::getUniqueCount();          // Unique visitors
Visitor::getTodayCount();           // Today's visits
Visitor::getTodayUniqueCount();     // Today's unique visitors
Visitor::getCountByDate('2024-01-15');
Visitor::getCountBetweenDates('2024-01-01', '2024-01-31');

// Analytics
Visitor::getTopPages(10);           // Top visited pages
Visitor::getTopBrowsers(10);        // Browser statistics
Visitor::getTopPlatforms(10);       // Platform statistics
Visitor::getDeviceTypeStats();      // Device type breakdown
Visitor::getDailyStats(30);         // Daily stats for N days
Visitor::getTopCountries(10);       // Country statistics
Visitor::getTopCities(10);          // City statistics
Visitor::getTopReferrers(10);       // Referrer statistics

// Maintenance
Visitor::purgeOldRecords();         // Purge based on retention_days config
```

### API Endpoint

The package provides a JSON API endpoint at `/trackora/stats`:

```bash
GET /trackora/stats?period=30
```

Response:

```json
{
    "total_visits": 1250,
    "unique_visitors": 850,
    "today_visits": 45,
    "today_unique": 32,
    "daily_stats": [...],
    "top_pages": [...],
    "top_browsers": [...],
    "top_platforms": [...],
    "device_stats": [...],
    "top_countries": [...]
}
```

### Customizing Views

Publish the views to customize the dashboard:

```bash
php artisan vendor:publish --tag=trackora-views
```

Views will be published to `resources/views/vendor/trackora/`.

### Publishing Assets

If you need to customize the dashboard assets (logo, etc.):

```bash
php artisan vendor:publish --tag=trackora-assets
```

Assets will be published to `public/vendor/trackora/`.

## Restricting Dashboard Access

### By User ID or Email

Add specific user IDs or emails to the `allowed_users` config:

```php
'allowed_users' => [
    1,                          // User ID
    'admin@example.com',        // Email
],
```

### By Custom Logic

Create your own middleware that extends `AuthorizeTrackoraDashboard`:

```php
namespace App\Http\Middleware;

use IbrahimEng12\Trackora\Http\Middleware\AuthorizeTrackoraDashboard;

class CustomTrackoraAuthorization extends AuthorizeTrackoraDashboard
{
    protected function isAuthorized($request): bool
    {
        return $request->user()?->isAdmin();
    }
}
```

## Scheduled Cleanup

To automatically purge old records, add this to your scheduler:

```php
// In app/Console/Kernel.php or routes/console.php
Schedule::call(function () {
    \IbrahimEng12\Trackora\Models\Visitor::purgeOldRecords();
})->daily();
```

## License

MIT License. See [LICENSE](LICENSE) for details.
