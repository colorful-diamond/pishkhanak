<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VarnishService
{
    protected string $varnishHost;
    protected int $varnishPort;

    public function __construct()
    {
        $this->varnishHost = config('varnish.host', '127.0.0.1');
        $this->varnishPort = config('varnish.port', 6081);
    }

    /**
     * Purge a specific URL from Varnish cache
     */
    public function purgeUrl(string $url): bool
    {
        try {
            $response = Http::withHeaders([
                'Host' => parse_url($url, PHP_URL_HOST) ?? config('app.url'),
            ])->send('PURGE', $this->buildVarnishUrl($url));

            if ($response->successful()) {
                Log::info('Varnish cache purged for URL', ['url' => $url]);
                return true;
            }

            Log::warning('Failed to purge Varnish cache', [
                'url' => $url,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('Error purging Varnish cache', [
                'url' => $url,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Purge URLs by pattern (regex)
     */
    public function purgeByPattern(string $pattern): bool
    {
        try {
            $response = Http::withHeaders([
                'Host' => config('app.url'),
                'X-Purge-Method' => 'regex',
            ])->send('PURGE', $this->buildVarnishUrl($pattern));

            if ($response->successful()) {
                Log::info('Varnish cache purged by pattern', ['pattern' => $pattern]);
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Error purging Varnish cache by pattern', [
                'pattern' => $pattern,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Purge by cache tags
     */
    public function purgeByTags(array|string $tags): bool
    {
        if (is_array($tags)) {
            $tags = implode('|', $tags);
        }

        try {
            $response = Http::withHeaders([
                'Host' => config('app.url'),
                'X-Cache-Tags' => $tags,
            ])->send('PURGE', $this->buildVarnishUrl('/'));

            if ($response->successful()) {
                Log::info('Varnish cache purged by tags', ['tags' => $tags]);
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Error purging Varnish cache by tags', [
                'tags' => $tags,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Purge entire cache (use with caution!)
     */
    public function purgeAll(): bool
    {
        return $this->purgeByPattern('.*');
    }

    /**
     * Purge cache for a specific model
     */
    public function purgeModel(string $modelType, $modelId): bool
    {
        $tag = strtolower($modelType) . ':' . $modelId;
        return $this->purgeByTags($tag);
    }

    /**
     * Purge cache for a service
     */
    public function purgeService(string $serviceSlug): bool
    {
        $tags = [
            'service:' . $serviceSlug,
            'path:services/' . $serviceSlug,
        ];
        
        return $this->purgeByTags($tags);
    }

    /**
     * Purge cache for a category
     */
    public function purgeCategory(string $categorySlug): bool
    {
        $tags = [
            'category:' . $categorySlug,
            'path:category/' . $categorySlug,
        ];
        
        return $this->purgeByTags($tags);
    }

    /**
     * Purge homepage cache
     */
    public function purgeHomepage(): bool
    {
        return $this->purgeUrl('/');
    }

    /**
     * Purge blog pages
     */
    public function purgeBlog(): bool
    {
        return $this->purgeByPattern('^/blog/.*');
    }

    /**
     * Warm up cache by pre-fetching URLs
     */
    public function warmUp(array $urls): void
    {
        foreach ($urls as $url) {
            try {
                Http::get($url);
                Log::info('Cache warmed up for URL', ['url' => $url]);
            } catch (\Exception $e) {
                Log::warning('Failed to warm up cache', [
                    'url' => $url,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Build Varnish URL
     */
    protected function buildVarnishUrl(string $path): string
    {
        $path = ltrim($path, '/');
        return "http://{$this->varnishHost}:{$this->varnishPort}/{$path}";
    }

    /**
     * Check if Varnish is healthy
     */
    public function isHealthy(): bool
    {
        try {
            $response = Http::timeout(2)->get($this->buildVarnishUrl('varnish-health'));
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get Varnish statistics (requires varnishstat)
     */
    public function getStats(): array
    {
        try {
            $output = shell_exec('varnishstat -1 -j 2>/dev/null');
            
            if ($output) {
                $stats = json_decode($output, true);
                
                return [
                    'hit_rate' => $this->calculateHitRate($stats),
                    'cache_hits' => $stats['MAIN.cache_hit']['value'] ?? 0,
                    'cache_misses' => $stats['MAIN.cache_miss']['value'] ?? 0,
                    'backend_connections' => $stats['MAIN.backend_conn']['value'] ?? 0,
                    'client_requests' => $stats['MAIN.client_req']['value'] ?? 0,
                    'cache_objects' => $stats['MAIN.n_object']['value'] ?? 0,
                ];
            }
        } catch (\Exception $e) {
            Log::error('Failed to get Varnish stats', ['error' => $e->getMessage()]);
        }

        return [];
    }

    /**
     * Calculate cache hit rate
     */
    protected function calculateHitRate(array $stats): float
    {
        $hits = $stats['MAIN.cache_hit']['value'] ?? 0;
        $misses = $stats['MAIN.cache_miss']['value'] ?? 0;
        
        $total = $hits + $misses;
        
        if ($total === 0) {
            return 0.0;
        }
        
        return round(($hits / $total) * 100, 2);
    }
}