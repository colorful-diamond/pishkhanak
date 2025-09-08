<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    /**
     * Handle an incoming request and add security headers to response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Prevent clickjacking attacks
        $response->headers->set('X-Frame-Options', 'DENY');
        
        // Prevent MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        
        // Enable XSS protection in older browsers
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        
        // Control referrer information
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        // Permissions Policy (formerly Feature Policy)
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
        
        // Content Security Policy - adjust as needed for your application
        $csp = $this->getContentSecurityPolicy($request);
        $response->headers->set('Content-Security-Policy', $csp);
        
        // Strict Transport Security (only for HTTPS)
        if ($request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }
        
        // Remove server header information
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');
        
        return $response;
    }
    
    /**
     * Generate Content Security Policy based on environment
     *
     * @param Request $request
     * @return string
     */
    private function getContentSecurityPolicy(Request $request): string
    {
        $isProduction = config('app.env') === 'production';
        
        // Base CSP rules
        $policies = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://unpkg.com https://cdnjs.cloudflare.com",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net",
            "font-src 'self' data: https://fonts.gstatic.com",
            "img-src 'self' data: blob: https:",
            "connect-src 'self' wss: https://reverb.pishkhanak.com https://api.openrouter.ai",
            "frame-src 'none'",
            "object-src 'none'",
            "base-uri 'self'",
        ];
        
        // Add report-uri in production
        if ($isProduction) {
            $policies[] = "upgrade-insecure-requests";
        } else {
            // More permissive in development
            $policies[1] = "script-src 'self' 'unsafe-inline' 'unsafe-eval' *";
            $policies[2] = "style-src 'self' 'unsafe-inline' *";
        }
        
        return implode('; ', $policies);
    }
}