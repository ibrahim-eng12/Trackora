<?php

namespace IbrahimEng12\Trackora\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class Visitor extends Model
{
    protected $fillable = [
        'ip_address',
        'user_agent',
        'browser',
        'platform',
        'device_type',
        'country',
        'city',
        'page_visited',
        'referrer',
        'session_id',
        'is_unique',
        'user_id',
    ];

    protected $casts = [
        'is_unique' => 'boolean',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTable(config('trackora.table_name', 'trackora_visits'));
    }

    /**
     * Get the user that owns the visitor record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model', \App\Models\User::class));
    }

    /**
     * Track a visitor from the given request.
     */
    public static function track(Request $request, ?string $page = null): self
    {
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();
        $sessionId = session()->getId();

        $isUnique = !self::where('ip_address', $ipAddress)
            ->whereDate('created_at', today())
            ->exists();

        $browserInfo = self::parseBrowser($userAgent);
        $locationInfo = config('trackora.geolocation.enabled', true)
            ? self::getLocation($ipAddress)
            : ['country' => null, 'city' => null];

        return self::create([
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'browser' => $browserInfo['browser'],
            'platform' => $browserInfo['platform'],
            'device_type' => $browserInfo['device_type'],
            'country' => $locationInfo['country'],
            'city' => $locationInfo['city'],
            'page_visited' => $page ?? $request->path(),
            'referrer' => $request->header('referer'),
            'session_id' => $sessionId,
            'is_unique' => $isUnique,
            'user_id' => auth()->id(),
        ]);
    }

    /**
     * Get location information for an IP address.
     */
    protected static function getLocation(string $ip): array
    {
        $country = null;
        $city = null;

        if (in_array($ip, ['127.0.0.1', '::1']) || self::isPrivateIp($ip)) {
            return compact('country', 'city');
        }

        try {
            $response = @file_get_contents("http://ip-api.com/json/{$ip}?fields=country,city");
            if ($response) {
                $data = json_decode($response, true);
                $country = $data['country'] ?? null;
                $city = $data['city'] ?? null;
            }
        } catch (\Exception $e) {
            // Silently fail - geolocation is not critical
        }

        return compact('country', 'city');
    }

    /**
     * Check if an IP address is private.
     */
    protected static function isPrivateIp(string $ip): bool
    {
        return !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    }

    /**
     * Parse browser information from user agent string.
     */
    protected static function parseBrowser(?string $userAgent): array
    {
        $browser = 'Unknown';
        $platform = 'Unknown';
        $deviceType = 'Desktop';

        if (!$userAgent) {
            return compact('browser', 'platform', 'device_type');
        }

        // Detect browser
        if (preg_match('/MSIE|Trident/i', $userAgent)) {
            $browser = 'Internet Explorer';
        } elseif (preg_match('/Firefox/i', $userAgent)) {
            $browser = 'Firefox';
        } elseif (preg_match('/Edg/i', $userAgent)) {
            $browser = 'Edge';
        } elseif (preg_match('/Chrome/i', $userAgent)) {
            $browser = 'Chrome';
        } elseif (preg_match('/Safari/i', $userAgent)) {
            $browser = 'Safari';
        } elseif (preg_match('/Opera|OPR/i', $userAgent)) {
            $browser = 'Opera';
        }

        // Detect platform
        if (preg_match('/Windows/i', $userAgent)) {
            $platform = 'Windows';
        } elseif (preg_match('/Macintosh|Mac OS/i', $userAgent)) {
            $platform = 'macOS';
        } elseif (preg_match('/Linux/i', $userAgent)) {
            $platform = 'Linux';
        } elseif (preg_match('/Android/i', $userAgent)) {
            $platform = 'Android';
        } elseif (preg_match('/iPhone|iPad|iPod/i', $userAgent)) {
            $platform = 'iOS';
        }

        // Detect device type
        if (preg_match('/Mobile|Android|iPhone|iPod/i', $userAgent)) {
            $deviceType = 'Mobile';
        } elseif (preg_match('/iPad|Tablet/i', $userAgent)) {
            $deviceType = 'Tablet';
        }

        return [
            'browser' => $browser,
            'platform' => $platform,
            'device_type' => $deviceType,
        ];
    }

    /**
     * Check if user agent is a known bot.
     */
    public static function isBot(?string $userAgent): bool
    {
        if (!$userAgent) {
            return false;
        }

        $bots = [
            'googlebot', 'bingbot', 'slurp', 'duckduckbot', 'baiduspider',
            'yandexbot', 'sogou', 'exabot', 'facebot', 'ia_archiver',
            'mj12bot', 'ahrefsbot', 'semrushbot', 'dotbot', 'rogerbot',
            'screaming frog', 'bot', 'spider', 'crawl',
        ];

        $userAgentLower = strtolower($userAgent);
        foreach ($bots as $bot) {
            if (str_contains($userAgentLower, $bot)) {
                return true;
            }
        }

        return false;
    }

    // ==========================================
    // Statistics Methods
    // ==========================================

    /**
     * Get total visitor count.
     */
    public static function getTotalCount(): int
    {
        return self::count();
    }

    /**
     * Get unique visitor count.
     */
    public static function getUniqueCount(): int
    {
        return self::where('is_unique', true)->count();
    }

    /**
     * Get today's visitor count.
     */
    public static function getTodayCount(): int
    {
        return self::whereDate('created_at', today())->count();
    }

    /**
     * Get today's unique visitor count.
     */
    public static function getTodayUniqueCount(): int
    {
        return self::whereDate('created_at', today())
            ->where('is_unique', true)
            ->count();
    }

    /**
     * Get visitor count for a specific date.
     */
    public static function getCountByDate(string $date): int
    {
        return self::whereDate('created_at', $date)->count();
    }

    /**
     * Get visitor count between two dates.
     */
    public static function getCountBetweenDates(string $startDate, string $endDate): int
    {
        return self::whereBetween('created_at', [$startDate, $endDate])->count();
    }

    /**
     * Get top visited pages.
     */
    public static function getTopPages(int $limit = 10): Collection
    {
        return self::selectRaw('page_visited, COUNT(*) as visits')
            ->groupBy('page_visited')
            ->orderByDesc('visits')
            ->limit($limit)
            ->get();
    }

    /**
     * Get top browsers.
     */
    public static function getTopBrowsers(int $limit = 10): Collection
    {
        return self::selectRaw('browser, COUNT(*) as count')
            ->whereNotNull('browser')
            ->groupBy('browser')
            ->orderByDesc('count')
            ->limit($limit)
            ->get();
    }

    /**
     * Get top platforms.
     */
    public static function getTopPlatforms(int $limit = 10): Collection
    {
        return self::selectRaw('platform, COUNT(*) as count')
            ->whereNotNull('platform')
            ->groupBy('platform')
            ->orderByDesc('count')
            ->limit($limit)
            ->get();
    }

    /**
     * Get device type statistics.
     */
    public static function getDeviceTypeStats(): Collection
    {
        return self::selectRaw('device_type, COUNT(*) as count')
            ->whereNotNull('device_type')
            ->groupBy('device_type')
            ->orderByDesc('count')
            ->get();
    }

    /**
     * Get daily statistics for the past N days.
     */
    public static function getDailyStats(int $days = 30): Collection
    {
        return self::selectRaw('DATE(created_at) as date, COUNT(*) as total, SUM(is_unique) as unique_visitors')
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    /**
     * Get top countries.
     */
    public static function getTopCountries(int $limit = 10): Collection
    {
        return self::selectRaw('country, COUNT(*) as count')
            ->whereNotNull('country')
            ->where('country', '!=', '')
            ->groupBy('country')
            ->orderByDesc('count')
            ->limit($limit)
            ->get();
    }

    /**
     * Get top cities.
     */
    public static function getTopCities(int $limit = 10): Collection
    {
        return self::selectRaw('city, country, COUNT(*) as count')
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->groupBy('city', 'country')
            ->orderByDesc('count')
            ->limit($limit)
            ->get();
    }

    /**
     * Get top referrers.
     */
    public static function getTopReferrers(int $limit = 10): Collection
    {
        return self::selectRaw('referrer, COUNT(*) as count')
            ->whereNotNull('referrer')
            ->where('referrer', '!=', '')
            ->groupBy('referrer')
            ->orderByDesc('count')
            ->limit($limit)
            ->get();
    }

    /**
     * Purge old visitor records based on retention settings.
     */
    public static function purgeOldRecords(): int
    {
        $retentionDays = config('trackora.retention_days');

        if ($retentionDays === null) {
            return 0;
        }

        return self::where('created_at', '<', now()->subDays($retentionDays))->delete();
    }
}
