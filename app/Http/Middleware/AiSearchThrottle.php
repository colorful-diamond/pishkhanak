<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AiSearchThrottle
{
    // Rate limits
    private const AUTHENTICATED_HOURLY_LIMIT = 100;
    private const AUTHENTICATED_DAILY_LIMIT = 500;
    private const ANONYMOUS_HOURLY_LIMIT = 30;
    private const ANONYMOUS_DAILY_LIMIT = 100;
    
    // Cost controls
    private const DAILY_TOKEN_LIMIT = 50000;
    private const DAILY_REQUEST_LIMIT = 1000;
    private const EXPENSIVE_OPERATIONS_DAILY_LIMIT = 50; // voice, image
    
    // Time windows
    private const HOUR_IN_SECONDS = 3600;
    private const DAY_IN_SECONDS = 86400;
    
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $type = 'text'): Response
    {
        $userId = Auth::id();
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();
        
        try {
            // Check rate limits
            if (!$this->checkRateLimit($userId, $ipAddress, $type)) {
                return $this->createRateLimitResponse($request, $userId, $ipAddress, $type);
            }
            
            // Check cost controls
            if (!$this->checkCostLimits($type)) {
                return $this->createCostLimitResponse();
            }
            
            // Check for suspicious activity
            if ($this->detectSuspiciousActivity($userId, $ipAddress)) {
                return $this->createSuspiciousActivityResponse();
            }
            
            // Proceed with request
            $response = $next($request);
            
            // Increment counters after successful request
            $this->incrementCounters($userId, $ipAddress, $type);
            
            // Log request for monitoring
            $this->logRequest($userId, $ipAddress, $type, $userAgent);
            
            return $response;
            
        } catch (\Exception $e) {
            Log::error('AI Search Throttle error: ' . $e->getMessage(), [
                'user_id' => $userId,
                'ip_address' => $ipAddress,
                'type' => $type,
                'request' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'خطا در بررسی محدودیت‌ها. لطفاً بعداً تلاش کنید.',
                'code' => 'THROTTLE_ERROR'
            ], 500);
        }
    }
    
    /**
     * Check rate limits for user/IP
     */
    private function checkRateLimit(?int $userId, string $ipAddress, string $type): bool
    {
        $now = now();
        $hourKey = $now->format('Y-m-d-H');
        $dayKey = $now->format('Y-m-d');
        
        if ($userId) {
            return $this->checkAuthenticatedUserLimits($userId, $hourKey, $dayKey, $type);
        } else {
            return $this->checkAnonymousUserLimits($ipAddress, $hourKey, $dayKey, $type);
        }
    }
    
    /**
     * Check rate limits for authenticated users
     */
    private function checkAuthenticatedUserLimits(int $userId, string $hourKey, string $dayKey, string $type): bool
    {
        // Hourly limit
        $hourlyKey = "ai_search:user:{$userId}:hour:{$hourKey}";
        $hourlyCount = Cache::get($hourlyKey, 0);
        
        if ($hourlyCount >= self::AUTHENTICATED_HOURLY_LIMIT) {
            return false;
        }
        
        // Daily limit
        $dailyKey = "ai_search:user:{$userId}:day:{$dayKey}";
        $dailyCount = Cache::get($dailyKey, 0);
        
        if ($dailyCount >= self::AUTHENTICATED_DAILY_LIMIT) {
            return false;
        }
        
        // Check expensive operation limits (voice, image)
        if (in_array($type, ['voice', 'image'])) {
            $expensiveKey = "ai_search:user:{$userId}:expensive:day:{$dayKey}";
            $expensiveCount = Cache::get($expensiveKey, 0);
            
            if ($expensiveCount >= self::EXPENSIVE_OPERATIONS_DAILY_LIMIT) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Check rate limits for anonymous users
     */
    private function checkAnonymousUserLimits(string $ipAddress, string $hourKey, string $dayKey, string $type): bool
    {
        // Hourly limit
        $hourlyKey = "ai_search:ip:{$ipAddress}:hour:{$hourKey}";
        $hourlyCount = Cache::get($hourlyKey, 0);
        
        if ($hourlyCount >= self::ANONYMOUS_HOURLY_LIMIT) {
            return false;
        }
        
        // Daily limit
        $dailyKey = "ai_search:ip:{$ipAddress}:day:{$dayKey}";
        $dailyCount = Cache::get($dailyKey, 0);
        
        if ($dailyCount >= self::ANONYMOUS_DAILY_LIMIT) {
            return false;
        }
        
        // Anonymous users have more restrictive limits for expensive operations
        if (in_array($type, ['voice', 'image'])) {
            $expensiveKey = "ai_search:ip:{$ipAddress}:expensive:day:{$dayKey}";
            $expensiveCount = Cache::get($expensiveKey, 0);
            
            if ($expensiveCount >= 10) { // Only 10 expensive operations per day for anonymous users
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Check global cost limits
     */
    private function checkCostLimits(string $type): bool
    {
        $dayKey = now()->format('Y-m-d');
        
        // Check global daily token usage
        $tokenUsageKey = "ai_search:global:tokens:day:{$dayKey}";
        $tokenUsage = Cache::get($tokenUsageKey, 0);
        
        if ($tokenUsage >= self::DAILY_TOKEN_LIMIT) {
            return false;
        }
        
        // Check global daily request count
        $requestCountKey = "ai_search:global:requests:day:{$dayKey}";
        $requestCount = Cache::get($requestCountKey, 0);
        
        if ($requestCount >= self::DAILY_REQUEST_LIMIT) {
            return false;
        }
        
        // Check expensive operations globally
        if (in_array($type, ['voice', 'image'])) {
            $expensiveGlobalKey = "ai_search:global:expensive:day:{$dayKey}";
            $expensiveGlobalCount = Cache::get($expensiveGlobalKey, 0);
            
            if ($expensiveGlobalCount >= 200) { // Global limit for expensive operations
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Detect suspicious activity
     */
    private function detectSuspiciousActivity(?int $userId, string $ipAddress): bool
    {
        $now = now();
        $minuteKey = $now->format('Y-m-d-H-i');
        
        // Check requests per minute (burst detection)
        if ($userId) {
            $burstKey = "ai_search:user:{$userId}:burst:{$minuteKey}";
        } else {
            $burstKey = "ai_search:ip:{$ipAddress}:burst:{$minuteKey}";
        }
        
        $burstCount = Cache::get($burstKey, 0);
        
        // More than 10 requests per minute is suspicious
        if ($burstCount >= 10) {
            return true;
        }
        
        // Check for repeated identical requests
        $lastRequestKey = $userId ? "ai_search:user:{$userId}:last_request" : "ai_search:ip:{$ipAddress}:last_request";
        $lastRequestTime = Cache::get($lastRequestKey);
        
        // If last request was less than 2 seconds ago, it might be spam
        if ($lastRequestTime && (time() - $lastRequestTime) < 2) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Increment counters after successful request
     */
    private function incrementCounters(?int $userId, string $ipAddress, string $type): void
    {
        $now = now();
        $hourKey = $now->format('Y-m-d-H');
        $dayKey = $now->format('Y-m-d');
        $minuteKey = $now->format('Y-m-d-H-i');
        
        if ($userId) {
            // User counters
            $this->incrementCounter("ai_search:user:{$userId}:hour:{$hourKey}", self::HOUR_IN_SECONDS);
            $this->incrementCounter("ai_search:user:{$userId}:day:{$dayKey}", self::DAY_IN_SECONDS);
            $this->incrementCounter("ai_search:user:{$userId}:burst:{$minuteKey}", 60);
            
            // Update last request time
            Cache::put("ai_search:user:{$userId}:last_request", time(), 10);
            
            // Expensive operations counter
            if (in_array($type, ['voice', 'image'])) {
                $this->incrementCounter("ai_search:user:{$userId}:expensive:day:{$dayKey}", self::DAY_IN_SECONDS);
            }
        } else {
            // IP counters
            $this->incrementCounter("ai_search:ip:{$ipAddress}:hour:{$hourKey}", self::HOUR_IN_SECONDS);
            $this->incrementCounter("ai_search:ip:{$ipAddress}:day:{$dayKey}", self::DAY_IN_SECONDS);
            $this->incrementCounter("ai_search:ip:{$ipAddress}:burst:{$minuteKey}", 60);
            
            // Update last request time
            Cache::put("ai_search:ip:{$ipAddress}:last_request", time(), 10);
            
            // Expensive operations counter
            if (in_array($type, ['voice', 'image'])) {
                $this->incrementCounter("ai_search:ip:{$ipAddress}:expensive:day:{$dayKey}", self::DAY_IN_SECONDS);
            }
        }
        
        // Global counters
        $this->incrementCounter("ai_search:global:requests:day:{$dayKey}", self::DAY_IN_SECONDS);
        
        if (in_array($type, ['voice', 'image'])) {
            $this->incrementCounter("ai_search:global:expensive:day:{$dayKey}", self::DAY_IN_SECONDS);
        }
    }
    
    /**
     * Helper method to increment cache counter
     */
    private function incrementCounter(string $key, int $ttl): void
    {
        $count = Cache::get($key, 0);
        Cache::put($key, $count + 1, $ttl);
    }
    
    /**
     * Log request for monitoring
     */
    private function logRequest(?int $userId, string $ipAddress, string $type, ?string $userAgent): void
    {
        Log::info('AI Search Request', [
            'user_id' => $userId,
            'ip_address' => $ipAddress,
            'type' => $type,
            'user_agent' => $userAgent,
            'timestamp' => now()->toISOString()
        ]);
    }
    
    /**
     * Create rate limit response
     */
    private function createRateLimitResponse(Request $request, ?int $userId, string $ipAddress, string $type): Response
    {
        $retryAfter = $this->getRetryAfter($userId, $ipAddress, $type);
        
        return response()->json([
            'success' => false,
            'message' => 'تعداد درخواست‌های شما بیش از حد مجاز است. لطفاً کمی صبر کنید.',
            'code' => 'RATE_LIMITED',
            'retry_after' => $retryAfter,
            'type' => $type,
            'authenticated' => !is_null($userId)
        ], 429)->header('Retry-After', $retryAfter);
    }
    
    /**
     * Create cost limit response
     */
    private function createCostLimitResponse(): Response
    {
        return response()->json([
            'success' => false,
            'message' => 'محدودیت روزانه سیستم هوش مصنوعی به پایان رسیده است. لطفاً فردا دوباره تلاش کنید.',
            'code' => 'COST_LIMITED',
            'retry_after' => $this->getSecondsUntilTomorrow()
        ], 429);
    }
    
    /**
     * Create suspicious activity response
     */
    private function createSuspiciousActivityResponse(): Response
    {
        return response()->json([
            'success' => false,
            'message' => 'فعالیت مشکوک شناسایی شد. لطفاً چند دقیقه صبر کنید.',
            'code' => 'SUSPICIOUS_ACTIVITY',
            'retry_after' => 300 // 5 minutes
        ], 429);
    }
    
    /**
     * Get retry after seconds
     */
    private function getRetryAfter(?int $userId, string $ipAddress, string $type): int
    {
        $now = now();
        $nextHour = $now->copy()->addHour()->startOfHour();
        
        // For expensive operations, suggest waiting until next day
        if (in_array($type, ['voice', 'image'])) {
            $tomorrow = $now->copy()->addDay()->startOfDay();
            return $tomorrow->diffInSeconds($now);
        }
        
        // For regular operations, suggest waiting until next hour
        return $nextHour->diffInSeconds($now);
    }
    
    /**
     * Get seconds until tomorrow
     */
    private function getSecondsUntilTomorrow(): int
    {
        $now = now();
        $tomorrow = $now->copy()->addDay()->startOfDay();
        return $tomorrow->diffInSeconds($now);
    }
    
    /**
     * Get current usage statistics for monitoring
     */
    public static function getUsageStats(?int $userId = null, ?string $ipAddress = null): array
    {
        $now = now();
        $hourKey = $now->format('Y-m-d-H');
        $dayKey = $now->format('Y-m-d');
        
        $stats = [
            'global' => [
                'daily_requests' => Cache::get("ai_search:global:requests:day:{$dayKey}", 0),
                'daily_tokens' => Cache::get("ai_search:global:tokens:day:{$dayKey}", 0),
                'daily_expensive' => Cache::get("ai_search:global:expensive:day:{$dayKey}", 0),
            ],
            'limits' => [
                'daily_request_limit' => self::DAILY_REQUEST_LIMIT,
                'daily_token_limit' => self::DAILY_TOKEN_LIMIT,
                'daily_expensive_limit' => 200,
            ]
        ];
        
        if ($userId) {
            $stats['user'] = [
                'hourly_requests' => Cache::get("ai_search:user:{$userId}:hour:{$hourKey}", 0),
                'daily_requests' => Cache::get("ai_search:user:{$userId}:day:{$dayKey}", 0),
                'daily_expensive' => Cache::get("ai_search:user:{$userId}:expensive:day:{$dayKey}", 0),
                'limits' => [
                    'hourly_limit' => self::AUTHENTICATED_HOURLY_LIMIT,
                    'daily_limit' => self::AUTHENTICATED_DAILY_LIMIT,
                    'daily_expensive_limit' => self::EXPENSIVE_OPERATIONS_DAILY_LIMIT,
                ]
            ];
        } elseif ($ipAddress) {
            $stats['ip'] = [
                'hourly_requests' => Cache::get("ai_search:ip:{$ipAddress}:hour:{$hourKey}", 0),
                'daily_requests' => Cache::get("ai_search:ip:{$ipAddress}:day:{$dayKey}", 0),
                'daily_expensive' => Cache::get("ai_search:ip:{$ipAddress}:expensive:day:{$dayKey}", 0),
                'limits' => [
                    'hourly_limit' => self::ANONYMOUS_HOURLY_LIMIT,
                    'daily_limit' => self::ANONYMOUS_DAILY_LIMIT,
                    'daily_expensive_limit' => 10,
                ]
            ];
        }
        
        return $stats;
    }
} 