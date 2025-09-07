<?php

namespace App\Http\Middleware;

use App\Models\Redirect;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RedirectMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip redirects for admin panel, API routes, and other system routes
        if ($this->shouldSkipRedirect($request)) {
            return $next($request);
        }

        // Get the current URL path
        $currentPath = $request->getPathInfo();

        // Try to find a redirect for this URL (now with enhanced caching)
        $startTime = microtime(true);
        $redirect = Redirect::findForUrl($currentPath);
        $lookupTime = (microtime(true) - $startTime) * 1000; // in milliseconds

        if ($redirect) {
            // Record the hit asynchronously (don't slow down the redirect)
            dispatch(function () use ($redirect) {
                $redirect->recordHit();
            })->afterResponse();

            // Log the redirect for debugging (include performance metrics)
            Log::info('Redirect executed', [
                'from' => $currentPath,
                'to' => $redirect->to_url,
                'status' => $redirect->status_code,
                'lookup_time_ms' => round($lookupTime, 2),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            // Perform the redirect
            return redirect($redirect->to_url, $redirect->status_code);
        }

        return $next($request);
    }

    /**
     * Determine if we should skip redirect checking for this request
     */
    private function shouldSkipRedirect(Request $request): bool
    {
        $path = $request->getPathInfo();

        // Skip admin panel routes
        if (str_starts_with($path, '/access')) {
            return true;
        }

        // Skip API routes
        if (str_starts_with($path, '/api')) {
            return true;
        }

        // Skip auth routes
        if (str_starts_with($path, '/auth')) {
            return true;
        }

        // Skip webhooks
        if (str_starts_with($path, '/webhook')) {
            return true;
        }

        // Skip asset files
        if (str_contains($path, '.') && preg_match('/\.(css|js|png|jpg|jpeg|gif|svg|ico|woff|woff2|ttf|eot)$/i', $path)) {
            return true;
        }

        // Skip POST, PUT, DELETE requests (only redirect GET requests)
        if (!$request->isMethod('GET')) {
            return true;
        }

        return false;
    }
}