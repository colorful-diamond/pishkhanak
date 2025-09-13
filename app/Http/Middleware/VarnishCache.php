<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VarnishCache
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ?string $ttl = null): Response
    {
        $response = $next($request);

        // Skip caching for authenticated users
        if (Auth::check()) {
            $response->headers->set('Cache-Control', 'private, no-cache, no-store, must-revalidate');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
            return $response;
        }

        // Skip caching for POST/PUT/PATCH/DELETE requests
        if (!in_array($request->method(), ['GET', 'HEAD'])) {
            return $response;
        }

        // Skip if response has errors
        if ($response->getStatusCode() >= 400) {
            return $response;
        }

        // Set cache headers based on route
        $this->setCacheHeaders($response, $request, $ttl);

        return $response;
    }

    /**
     * Set appropriate cache headers based on the request
     */
    protected function setCacheHeaders(Response $response, Request $request, ?string $ttl = null): void
    {
        $path = $request->path();
        $cacheTime = 0;

        // Determine cache time based on route patterns
        if ($ttl) {
            // Use provided TTL
            $cacheTime = $this->parseTtl($ttl);
        } elseif ($path === '/') {
            // Homepage - 5 minutes
            $cacheTime = 300;
        } elseif (str_starts_with($path, 'blog')) {
            // Blog pages - 15 minutes
            $cacheTime = 900;
        } elseif (str_starts_with($path, 'services') || str_starts_with($path, 'category')) {
            // Service pages - 1 hour
            $cacheTime = 3600;
        } elseif (str_starts_with($path, 'about') || str_starts_with($path, 'contact')) {
            // Static pages - 1 day
            $cacheTime = 86400;
        } elseif ($this->isStaticAsset($path)) {
            // Static assets - 30 days
            $cacheTime = 2592000;
        }

        if ($cacheTime > 0) {
            $response->headers->set('Cache-Control', "public, max-age={$cacheTime}, s-maxage={$cacheTime}");
            $response->headers->set('X-Cache-TTL', (string)$cacheTime);
            
            // Add cache tags for targeted purging
            $this->addCacheTags($response, $request);
            
            // Add Vary headers for proper caching
            $this->addVaryHeaders($response, $request);
        } else {
            // No cache
            $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
            $response->headers->set('Pragma', 'no-cache');
        }
    }

    /**
     * Parse TTL string (e.g., '5m', '1h', '1d')
     */
    protected function parseTtl(string $ttl): int
    {
        $unit = substr($ttl, -1);
        $value = (int) substr($ttl, 0, -1);

        return match($unit) {
            's' => $value,
            'm' => $value * 60,
            'h' => $value * 3600,
            'd' => $value * 86400,
            default => (int) $ttl,
        };
    }

    /**
     * Check if path is a static asset
     */
    protected function isStaticAsset(string $path): bool
    {
        $extensions = ['css', 'js', 'jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'woff', 'woff2', 'ttf', 'eot', 'ico'];
        $pathInfo = pathinfo($path);
        
        return isset($pathInfo['extension']) && in_array($pathInfo['extension'], $extensions);
    }

    /**
     * Add cache tags for targeted purging
     */
    protected function addCacheTags(Response $response, Request $request): void
    {
        $tags = [];
        
        // Add route-based tags
        if ($request->route()) {
            $tags[] = 'route:' . $request->route()->getName();
        }
        
        // Add path-based tags
        $tags[] = 'path:' . $request->path();
        
        // Add model-based tags if applicable
        if ($request->route('service')) {
            $tags[] = 'service:' . $request->route('service');
        }
        
        if ($request->route('category')) {
            $tags[] = 'category:' . $request->route('category');
        }
        
        if (!empty($tags)) {
            $response->headers->set('X-Cache-Tags', implode(' ', $tags));
        }
    }

    /**
     * Add Vary headers for proper caching
     */
    protected function addVaryHeaders(Response $response, Request $request): void
    {
        $vary = ['Accept-Encoding'];
        
        // Vary by cookie if user might be logged in
        if ($request->hasCookie('laravel_session')) {
            $vary[] = 'Cookie';
        }
        
        // Vary by user agent for mobile detection
        if ($this->shouldVaryByUserAgent()) {
            $vary[] = 'User-Agent';
        }
        
        // Vary by language
        $vary[] = 'Accept-Language';
        
        $response->headers->set('Vary', implode(', ', $vary));
    }

    /**
     * Determine if we should vary cache by user agent
     */
    protected function shouldVaryByUserAgent(): bool
    {
        // Enable if you have different mobile/desktop versions
        return config('app.vary_by_user_agent', false);
    }
}