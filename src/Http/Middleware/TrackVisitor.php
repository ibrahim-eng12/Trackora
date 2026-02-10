<?php

namespace IbrahimEng12\Trackora\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use IbrahimEng12\Trackora\Models\Visitor;
use Symfony\Component\HttpFoundation\Response;

class TrackVisitor
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->shouldTrack($request)) {
            Visitor::track($request);
        }

        return $next($request);
    }

    /**
     * Determine if the request should be tracked.
     */
    protected function shouldTrack(Request $request): bool
    {
        // Check if tracking is enabled
        if (!config('trackora.enabled', true)) {
            return false;
        }

        // Only track GET requests
        if ($request->isMethod('GET') === false) {
            return false;
        }

        // Skip AJAX requests
        if ($request->ajax()) {
            return false;
        }

        // Check excluded paths
        foreach (config('trackora.excluded_paths', []) as $pattern) {
            if ($request->is($pattern)) {
                return false;
            }
        }

        // Check excluded IPs
        if (in_array($request->ip(), config('trackora.excluded_ips', []))) {
            return false;
        }

        // Check if we should track authenticated users
        if (!config('trackora.track_authenticated', true) && auth()->check()) {
            return false;
        }

        // Check if we should track bots
        if (!config('trackora.track_bots', false) && Visitor::isBot($request->userAgent())) {
            return false;
        }

        return true;
    }
}
