<?php

namespace App\Console\Commands;

use App\Models\Redirect;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class RedirectCacheStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redirects:cache-stats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show redirect cache statistics';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Redirect Cache Statistics');
        $this->line('==========================');

        // Get basic stats
        $stats = Redirect::getCacheStats();
        
        // Get redirect counts
        $totalRedirects = Redirect::count();
        $activeRedirects = Redirect::where('is_active', true)->count();
        $inactiveRedirects = Redirect::where('is_active', false)->count();
        
        // Get popular redirects
        $popularRedirects = Redirect::where('is_active', true)
            ->where('hit_count', '>', 0)
            ->orderBy('hit_count', 'desc')
            ->limit(5)
            ->get(['from_url', 'to_url', 'hit_count']);

        // Display stats
        $this->table(['Metric', 'Value'], [
            ['Total redirects', $totalRedirects],
            ['Active redirects', $activeRedirects],
            ['Inactive redirects', $inactiveRedirects],
            ['Active redirects cached', $stats['active_redirects_cached'] ? 'Yes' : 'No'],
            ['URL cache keys', $stats['cache_keys_count']],
        ]);

        if ($popularRedirects->count() > 0) {
            $this->line('');
            $this->info('Top 5 Most Popular Redirects:');
            $this->table(['From URL', 'To URL', 'Hits'], 
                $popularRedirects->map(function ($redirect) {
                    return [
                        $redirect->from_url,
                        $redirect->to_url,
                        $redirect->hit_count
                    ];
                })->toArray()
            );
        }

        // Check cache driver
        $cacheDriver = config('cache.default');
        $this->line('');
        $this->info("Cache Driver: {$cacheDriver}");
        
        if ($cacheDriver === 'redis') {
            $this->info('✓ Redis provides optimal performance for redirect caching');
        } else {
            $this->warn('⚠ Consider using Redis for better redirect cache performance');
        }

        return Command::SUCCESS;
    }
}