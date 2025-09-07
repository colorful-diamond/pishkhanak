<?php

namespace App\Console\Commands;

use App\Models\Redirect;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class RedirectCacheWarm extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redirects:cache-warm {--popular : Only warm popular redirects} {--urls=* : Specific URLs to warm}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Warm up the redirect cache to improve performance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting redirect cache warming...');

        $startTime = microtime(true);

        // Always warm the main active redirects cache
        $this->info('Warming main redirects cache...');
        $activeRedirects = Redirect::getAllActiveRedirects();
        $this->info("Cached {$activeRedirects->count()} active redirects.");

        if ($this->option('popular')) {
            // Warm popular redirects only
            $this->info('Warming popular redirects...');
            Redirect::preloadPopularRedirects();
            $this->info('Popular redirects cache warmed.');
        } elseif ($this->option('urls')) {
            // Warm specific URLs
            $urls = $this->option('urls');
            $this->info('Warming specific URLs...');
            
            foreach ($urls as $url) {
                $redirect = Redirect::findForUrl($url);
                $status = $redirect ? 'found redirect' : 'no redirect';
                $this->line("  {$url} - {$status}");
            }
        } else {
            // Warm all active redirects
            $this->info('Warming all redirect URLs...');
            
            $warmed = 0;
            foreach ($activeRedirects as $redirect) {
                $cleanUrl = Redirect::cleanUrl($redirect->from_url);
                $urlCacheKey = 'redirect.url.' . md5($cleanUrl);
                Cache::put($urlCacheKey, $redirect, 7200); // 2 hours
                $warmed++;
            }
            
            $this->info("Warmed {$warmed} redirect URLs.");
        }

        $endTime = microtime(true);
        $duration = round(($endTime - $startTime) * 1000, 2);

        $this->info("Cache warming completed in {$duration}ms.");

        // Show cache stats
        $stats = Redirect::getCacheStats();
        $this->table(['Metric', 'Value'], [
            ['Active redirects cached', $stats['active_redirects_cached'] ? 'Yes' : 'No'],
            ['URL cache keys', $stats['cache_keys_count']],
        ]);

        return Command::SUCCESS;
    }
}