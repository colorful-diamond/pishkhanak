<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Telegram Bot Rate Limiting Middleware
 * 
 * Protects Telegram webhook endpoints from abuse and DDoS attacks
 * with intelligent rate limiting based on IP, user ID, and command type.
 */
class TelegramRateLimit
{
    /**
     * Rate limiting configurations
     */
    private const LIMITS = [
        'webhook' => [
            'max_attempts' => 30,      // Reduced from 100 for security
            'decay_minutes' => 1,      // Per minute
            'block_duration' => 60,    // Block for 1 hour after limit
        ],
        'admin_commands' => [
            'max_attempts' => 10,      // Admin commands more restricted
            'decay_minutes' => 1,
            'block_duration' => 300,   // Block for 5 minutes
        ],
        'user_commands' => [
            'max_attempts' => 20,      // Regular user commands
            'decay_minutes' => 1,
            'block_duration' => 60,    // Block for 1 minute
        ],
        'persian_input' => [
            'max_attempts' => 15,      // Persian text processing (more CPU intensive)
            'decay_minutes' => 1,
            'block_duration' => 120,   // Block for 2 minutes
        ],
    ];

    /**
     * Handle an incoming request with rate limiting
     */
    public function handle(Request $request, Closure $next, string $type = 'webhook'): Response
    {
        $limits = self::LIMITS[$type] ?? self::LIMITS['webhook'];
        
        // Create rate limiting key
        $key = $this->createRateLimitKey($request, $type);
        
        // Check if rate limit is exceeded
        if (RateLimiter::tooManyAttempts($key, $limits['max_attempts'])) {
            $this->logRateLimitExceeded($request, $type, $key);
            
            $retryAfter = RateLimiter::availableIn($key);
            
            return response()->json([
                'ok' => false,
                'error' => 'Rate limit exceeded',
                'retry_after' => $retryAfter
            ], 429)->header('Retry-After', $retryAfter);
        }

        // Execute the request
        $response = $next($request);

        // Increment rate limit counter only for successful requests
        if ($response->getStatusCode() < 400) {
            RateLimiter::hit($key, $limits['decay_minutes'] * 60);
        }

        // Log rate limit status for monitoring
        $this->logRateLimitStatus($request, $type, $key);

        return $response;
    }

    /**
     * Create appropriate rate limiting key based on request type
     */
    private function createRateLimitKey(Request $request, string $type): string
    {
        $baseKey = "telegram_rate_limit:{$type}";
        
        switch ($type) {
            case 'webhook':
                // Rate limit by IP for webhook requests
                return $baseKey . ':ip:' . $request->ip();
                
            case 'admin_commands':
                // Rate limit admin commands by user ID
                $userId = $this->extractUserId($request);
                return $baseKey . ':user:' . ($userId ?: $request->ip());
                
            case 'user_commands':
                // Rate limit user commands by user ID with IP fallback
                $userId = $this->extractUserId($request);
                return $baseKey . ':user:' . ($userId ?: $request->ip());
                
            case 'persian_input':
                // Rate limit Persian text processing by user
                $userId = $this->extractUserId($request);
                return $baseKey . ':user:' . ($userId ?: $request->ip());
                
            default:
                return $baseKey . ':ip:' . $request->ip();
        }
    }

    /**
     * Extract Telegram user ID from webhook request
     */
    private function extractUserId(Request $request): ?string
    {
        $update = $request->json()->all();
        
        // Try to get user ID from various update types
        if (isset($update['message']['from']['id'])) {
            return (string) $update['message']['from']['id'];
        }
        
        if (isset($update['callback_query']['from']['id'])) {
            return (string) $update['callback_query']['from']['id'];
        }
        
        if (isset($update['inline_query']['from']['id'])) {
            return (string) $update['inline_query']['from']['id'];
        }
        
        return null;
    }

    /**
     * Check if request contains admin commands
     */
    private function isAdminCommand(Request $request): bool
    {
        $update = $request->json()->all();
        
        if (!isset($update['message']['text'])) {
            return false;
        }
        
        $text = $update['message']['text'];
        
        // List of admin-only commands
        $adminCommands = [
            '/admin', '/config', '/stats', '/ban', '/unban',
            '/reset', '/audit', '/logs', '/users', '/tickets_admin'
        ];
        
        foreach ($adminCommands as $command) {
            if (str_starts_with($text, $command)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Check if request contains Persian text (for CPU-intensive processing)
     */
    private function hasPersianText(Request $request): bool
    {
        $update = $request->json()->all();
        
        if (!isset($update['message']['text'])) {
            return false;
        }
        
        $text = $update['message']['text'];
        
        // Check for Persian characters (basic detection)
        return preg_match('/[\x{0600}-\x{06FF}]/u', $text) === 1;
    }

    /**
     * Log rate limit exceeded events
     */
    private function logRateLimitExceeded(Request $request, string $type, string $key): void
    {
        Log::warning('Telegram rate limit exceeded', [
            'type' => $type,
            'key' => $key,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_id' => $this->extractUserId($request),
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Log rate limit status for monitoring
     */
    private function logRateLimitStatus(Request $request, string $type, string $key): void
    {
        $attempts = RateLimiter::attempts($key);
        $maxAttempts = self::LIMITS[$type]['max_attempts'];
        
        // Log when approaching rate limit (80% of max)
        if ($attempts >= ($maxAttempts * 0.8)) {
            Log::info('Telegram rate limit warning', [
                'type' => $type,
                'key' => $key,
                'attempts' => $attempts,
                'max_attempts' => $maxAttempts,
                'remaining' => $maxAttempts - $attempts,
                'ip' => $request->ip(),
                'user_id' => $this->extractUserId($request),
                'timestamp' => now()->toISOString(),
            ]);
        }
    }

    /**
     * Clear rate limit for a specific key (admin function)
     */
    public static function clearRateLimit(string $key): void
    {
        RateLimiter::clear($key);
        
        Log::info('Rate limit cleared', [
            'key' => $key,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Get rate limit status for monitoring
     */
    public static function getRateLimitStatus(string $key, string $type = 'webhook'): array
    {
        $limits = self::LIMITS[$type] ?? self::LIMITS['webhook'];
        $attempts = RateLimiter::attempts($key);
        $availableIn = RateLimiter::availableIn($key);
        
        return [
            'key' => $key,
            'type' => $type,
            'attempts' => $attempts,
            'max_attempts' => $limits['max_attempts'],
            'remaining' => max(0, $limits['max_attempts'] - $attempts),
            'available_in' => $availableIn,
            'blocked' => $attempts >= $limits['max_attempts'],
        ];
    }
}