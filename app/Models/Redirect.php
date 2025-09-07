<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;

class Redirect extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_url',
        'to_url',
        'status_code',
        'is_active',
        'is_exact_match',
        'description',
        'hit_count',
        'last_hit_at',
        'created_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_exact_match' => 'boolean',
        'last_hit_at' => 'datetime',
        'hit_count' => 'integer',
        'status_code' => 'integer'
    ];

    /**
     * Get the user who created this redirect
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Find redirect for a given URL with enhanced caching
     */
    public static function findForUrl(string $url): ?Redirect
    {
        $cleanUrl = self::cleanUrl($url);
        
        // First check per-URL cache (most efficient)
        $urlCacheKey = 'redirect.url.' . md5($cleanUrl);
        $cached = Cache::get($urlCacheKey);
        
        if ($cached !== null) {
            // Return null if explicitly cached as "no redirect"
            if ($cached === 'NO_REDIRECT') {
                return null;
            }
            
            // Return cached redirect if it exists and is still active
            if ($cached instanceof Redirect && $cached->is_active) {
                return $cached;
            }
        }

        // Get all active redirects (cached)
        $redirects = self::getAllActiveRedirects();
        $foundRedirect = null;

        // Find matching redirect
        foreach ($redirects as $redirect) {
            $cleanFromUrl = self::cleanUrl($redirect->from_url);

            if ($redirect->is_exact_match) {
                if ($cleanUrl === $cleanFromUrl) {
                    $foundRedirect = $redirect;
                    break;
                }
            } else {
                // Wildcard matching - basic pattern matching
                if (fnmatch($cleanFromUrl, $cleanUrl)) {
                    $foundRedirect = $redirect;
                    break;
                }
            }
        }

        // Cache the result (both positive and negative results)
        $cacheValue = $foundRedirect ?? 'NO_REDIRECT';
        $cacheDuration = $foundRedirect ? 7200 : 1800; // 2 hours for redirects, 30 min for no-redirects
        
        Cache::put($urlCacheKey, $cacheValue, $cacheDuration);

        return $foundRedirect;
    }

    /**
     * Get all active redirects with caching
     */
    public static function getAllActiveRedirects()
    {
        $cacheKey = 'redirects.active.all';
        
        return Cache::remember($cacheKey, 3600, function () {
            return static::where('is_active', true)
                ->orderBy('is_exact_match', 'desc') // Exact matches first
                ->orderBy('created_at', 'asc') // Older redirects first (priority)
                ->get();
        });
    }

    /**
     * Warm up the cache with popular URLs
     */
    public static function warmCache(array $popularUrls = []): void
    {
        // Pre-cache all active redirects
        self::getAllActiveRedirects();
        
        // Pre-cache popular URLs if provided
        foreach ($popularUrls as $url) {
            self::findForUrl($url);
        }
    }

    /**
     * Clean URL for comparison (remove protocol, trailing slashes, etc.)
     */
    public static function cleanUrl(string $url): string
    {
        // Remove protocol
        $url = preg_replace('/^https?:\/\//', '', $url);
        
        // Remove domain if present
        $url = preg_replace('/^[^\/]+/', '', $url);
        
        // Ensure starts with /
        if (!str_starts_with($url, '/')) {
            $url = '/' . $url;
        }
        
        // Remove trailing slash except for root
        if ($url !== '/' && str_ends_with($url, '/')) {
            $url = rtrim($url, '/');
        }
        
        return $url;
    }

    /**
     * Record a hit for this redirect (optimized for performance)
     */
    public function recordHit(): void
    {
        // Use increment without triggering model events for better performance
        $this->increment('hit_count');
        $this->timestamps = false; // Temporarily disable timestamps
        $this->update(['last_hit_at' => now()]);
        $this->timestamps = true; // Re-enable timestamps
        
        // Note: We don't clear cache here as hit recording shouldn't affect redirect functionality
        // Cache will naturally expire and refresh
    }

    /**
     * Get status code text
     */
    public function getStatusCodeText(): string
    {
        return match($this->status_code) {
            301 => 'Moved Permanently (301)',
            302 => 'Found (302)',
            303 => 'See Other (303)',
            307 => 'Temporary Redirect (307)',
            308 => 'Permanent Redirect (308)',
            default => "HTTP {$this->status_code}"
        };
    }

    /**
     * Get match type text
     */
    public function getMatchTypeText(): string
    {
        return $this->is_exact_match ? 'دقیق' : 'الگویی';
    }

    /**
     * Clear all redirect-related cache
     */
    public static function clearAllCache(): void
    {
        // Clear main active redirects cache
        Cache::forget('redirects.active.all');
        
        // Clear all per-URL caches (this is expensive but thorough)
        // In production, you might want to use cache tags instead
        $pattern = 'redirect.url.*';
        
        // For Redis cache driver
        if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
            $redis = Cache::getStore()->connection();
            $keys = $redis->keys('*redirect.url.*');
            if (!empty($keys)) {
                $redis->del($keys);
            }
        } else {
            // For other cache drivers, we'll rely on cache expiration
            // You could implement a more sophisticated solution here
        }
    }

    /**
     * Clear cache for a specific URL
     */
    public static function clearUrlCache(string $url): void
    {
        $cleanUrl = self::cleanUrl($url);
        $urlCacheKey = 'redirect.url.' . md5($cleanUrl);
        Cache::forget($urlCacheKey);
    }

    /**
     * Get cache statistics
     */
    public static function getCacheStats(): array
    {
        $stats = [
            'active_redirects_cached' => Cache::has('redirects.active.all'),
            'cache_keys_count' => 0,
            'cache_hit_rate' => 'N/A'
        ];

        // For Redis, we can get more detailed stats
        if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
            $redis = Cache::getStore()->connection();
            $keys = $redis->keys('*redirect.url.*');
            $stats['cache_keys_count'] = count($keys);
        }

        return $stats;
    }

    /**
     * Preload cache with most commonly accessed redirects
     */
    public static function preloadPopularRedirects(): void
    {
        // Get most hit redirects and warm their cache
        $popularRedirects = static::where('is_active', true)
            ->where('hit_count', '>', 0)
            ->orderBy('hit_count', 'desc')
            ->limit(50) // Top 50 most popular
            ->get();

        foreach ($popularRedirects as $redirect) {
            $cleanUrl = self::cleanUrl($redirect->from_url);
            $urlCacheKey = 'redirect.url.' . md5($cleanUrl);
            Cache::put($urlCacheKey, $redirect, 7200); // 2 hours
        }
    }

    /**
     * Enhanced cache management with model events
     */
    protected static function booted()
    {
        // When a redirect is created, updated, or deleted, clear relevant caches
        static::saved(function ($redirect) {
            // Clear main cache
            Cache::forget('redirects.active.all');
            
            // Clear specific URL cache
            self::clearUrlCache($redirect->from_url);
            
            // If the from_url was changed, clear the old URL cache too
            if ($redirect->isDirty('from_url')) {
                $originalUrl = $redirect->getOriginal('from_url');
                if ($originalUrl) {
                    self::clearUrlCache($originalUrl);
                }
            }
        });

        static::deleted(function ($redirect) {
            // Clear main cache
            Cache::forget('redirects.active.all');
            
            // Clear specific URL cache
            self::clearUrlCache($redirect->from_url);
        });
    }
}