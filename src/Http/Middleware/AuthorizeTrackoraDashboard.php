<?php

namespace IbrahimEng12\Trackora\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthorizeTrackoraDashboard
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$this->isAuthorized($request)) {
            abort(403, 'Unauthorized access to Trackora dashboard.');
        }

        return $next($request);
    }

    /**
     * Check if the current user is authorized to access the dashboard.
     */
    protected function isAuthorized(Request $request): bool
    {
        $user = $request->user();

        if (!$user) {
            return false;
        }

        $allowedUsers = config('trackora.allowed_users', []);

        // If no specific users are configured, allow all authenticated users
        if (empty($allowedUsers)) {
            return true;
        }

        // Check if user's ID or email is in the allowed list
        return in_array($user->id, $allowedUsers) || in_array($user->email, $allowedUsers);
    }
}
