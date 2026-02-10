<?php

namespace IbrahimEng12\Trackora\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use IbrahimEng12\Trackora\Models\Visitor;

class TrackoraController extends Controller
{
    /**
     * Display the Trackora dashboard.
     */
    public function index(Request $request)
    {
        $period = $request->get('period', config('trackora.dashboard.default_period', 30));
        $perPage = config('trackora.dashboard.per_page', 25);

        // Main Statistics
        $totalVisits = Visitor::count();
        $uniqueVisitors = Visitor::where('is_unique', true)->count();
        $todayVisits = Visitor::whereDate('created_at', today())->count();
        $todayUniqueVisitors = Visitor::whereDate('created_at', today())
            ->where('is_unique', true)
            ->count();

        // Daily stats for chart
        $dailyStats = Visitor::selectRaw('DATE(created_at) as date, COUNT(*) as total, SUM(is_unique) as unique_count')
            ->where('created_at', '>=', now()->subDays($period))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top pages
        $topPages = Visitor::selectRaw('page_visited, COUNT(*) as visits')
            ->whereNotNull('page_visited')
            ->where('created_at', '>=', now()->subDays($period))
            ->groupBy('page_visited')
            ->orderByDesc('visits')
            ->limit(10)
            ->get();

        // Browser stats
        $browserStats = Visitor::selectRaw('browser, COUNT(*) as count')
            ->whereNotNull('browser')
            ->where('created_at', '>=', now()->subDays($period))
            ->groupBy('browser')
            ->orderByDesc('count')
            ->get();

        // Platform stats
        $platformStats = Visitor::selectRaw('platform, COUNT(*) as count')
            ->whereNotNull('platform')
            ->where('created_at', '>=', now()->subDays($period))
            ->groupBy('platform')
            ->orderByDesc('count')
            ->get();

        // Device type stats
        $deviceStats = Visitor::selectRaw('device_type, COUNT(*) as count')
            ->whereNotNull('device_type')
            ->where('created_at', '>=', now()->subDays($period))
            ->groupBy('device_type')
            ->orderByDesc('count')
            ->get();

        // Recent visitors
        $recentVisitors = Visitor::latest()
            ->paginate($perPage);

        // Referrer stats
        $referrerStats = Visitor::selectRaw('referrer, COUNT(*) as count')
            ->whereNotNull('referrer')
            ->where('referrer', '!=', '')
            ->where('created_at', '>=', now()->subDays($period))
            ->groupBy('referrer')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // Country stats
        $countryStats = Visitor::selectRaw('country, COUNT(*) as count')
            ->whereNotNull('country')
            ->where('country', '!=', '')
            ->where('created_at', '>=', now()->subDays($period))
            ->groupBy('country')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // City stats
        $cityStats = Visitor::selectRaw('city, country, COUNT(*) as count')
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->where('created_at', '>=', now()->subDays($period))
            ->groupBy('city', 'country')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        return view('trackora::dashboard', compact(
            'totalVisits',
            'uniqueVisitors',
            'todayVisits',
            'todayUniqueVisitors',
            'dailyStats',
            'topPages',
            'browserStats',
            'platformStats',
            'deviceStats',
            'recentVisitors',
            'referrerStats',
            'countryStats',
            'cityStats',
            'period'
        ));
    }

    /**
     * Export visitor data.
     */
    public function export(Request $request)
    {
        $period = $request->get('period', 30);
        $format = $request->get('format', 'csv');

        $visitors = Visitor::where('created_at', '>=', now()->subDays($period))
            ->orderByDesc('created_at')
            ->get();

        if ($format === 'json') {
            return response()->json($visitors);
        }

        // CSV export
        $filename = 'trackora_export_' . now()->format('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($visitors) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'ID', 'IP Address', 'Page', 'Browser', 'Platform',
                'Device', 'Country', 'City', 'Referrer', 'Unique', 'Created At'
            ]);

            foreach ($visitors as $visitor) {
                fputcsv($file, [
                    $visitor->id,
                    $visitor->ip_address,
                    $visitor->page_visited,
                    $visitor->browser,
                    $visitor->platform,
                    $visitor->device_type,
                    $visitor->country,
                    $visitor->city,
                    $visitor->referrer,
                    $visitor->is_unique ? 'Yes' : 'No',
                    $visitor->created_at->toDateTimeString(),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Clear all visitor records.
     */
    public function clear(Request $request)
    {
        Visitor::truncate();

        return redirect()
            ->route('trackora.dashboard')
            ->with('success', 'All visitor records have been cleared.');
    }

    /**
     * Purge old records based on retention settings.
     */
    public function purge(Request $request)
    {
        $deleted = Visitor::purgeOldRecords();

        return redirect()
            ->route('trackora.dashboard')
            ->with('success', "Purged {$deleted} old visitor records.");
    }

    /**
     * API endpoint for visitor statistics.
     */
    public function stats(Request $request)
    {
        $period = $request->get('period', 30);

        return response()->json([
            'total_visits' => Visitor::count(),
            'unique_visitors' => Visitor::where('is_unique', true)->count(),
            'today_visits' => Visitor::whereDate('created_at', today())->count(),
            'today_unique' => Visitor::whereDate('created_at', today())
                ->where('is_unique', true)
                ->count(),
            'daily_stats' => Visitor::getDailyStats($period),
            'top_pages' => Visitor::getTopPages(10),
            'top_browsers' => Visitor::getTopBrowsers(5),
            'top_platforms' => Visitor::getTopPlatforms(5),
            'device_stats' => Visitor::getDeviceTypeStats(),
            'top_countries' => Visitor::getTopCountries(10),
        ]);
    }
}
