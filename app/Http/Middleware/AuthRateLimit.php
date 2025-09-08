<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class AuthRateLimit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $action = 'default'): Response
    {
        $key = $this->resolveRequestSignature($request, $action);
        $maxAttempts = $this->getMaxAttempts($action);
        $decayMinutes = $this->getDecayMinutes($action);

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            
            return response()->json([
                'message' => 'Too many attempts. Please try again in ' . ceil($seconds / 60) . ' minutes.',
                'retry_after' => $seconds
            ], 429);
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        $response = $next($request);

        // Clear rate limit on successful authentication
        if ($action === 'login' && $response->isSuccessful()) {
            RateLimiter::clear($key);
        }

        return $response;
    }

    /**
     * Resolve request signature for rate limiting
     */
    protected function resolveRequestSignature(Request $request, string $action): string
    {
        // Use combination of IP and action for general limits
        $signature = $action . '|' . $request->ip();
        
        // For login/register, also include email/phone if provided
        if (in_array($action, ['login', 'register', 'otp'])) {
            $identifier = $request->input('email') ?? $request->input('phone') ?? $request->input('mobile');
            if ($identifier) {
                $signature .= '|' . sha1($identifier);
            }
        }

        return $signature;
    }

    /**
     * Get max attempts based on action
     */
    protected function getMaxAttempts(string $action): int
    {
        return match ($action) {
            'login' => 5,           // 5 login attempts
            'register' => 3,        // 3 registration attempts
            'otp' => 3,            // 3 OTP requests
            'verify-otp' => 5,     // 5 OTP verification attempts
            'password-reset' => 3,  // 3 password reset requests
            default => 10,         // Default limit
        };
    }

    /**
     * Get decay minutes based on action
     */
    protected function getDecayMinutes(string $action): int
    {
        return match ($action) {
            'login' => 15,          // 15 minutes for login
            'register' => 60,       // 1 hour for registration
            'otp' => 5,            // 5 minutes for OTP requests
            'verify-otp' => 10,    // 10 minutes for OTP verification
            'password-reset' => 60, // 1 hour for password reset
            default => 60,         // Default 1 hour
        };
    }
}