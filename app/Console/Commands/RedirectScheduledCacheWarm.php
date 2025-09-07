<?php

namespace App\Console\Commands;

use App\Models\Redirect;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RedirectScheduledCacheWarm extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redirects:scheduled-cache-warm';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scheduled cache warming for redirects (runs automatically)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $startTime = microtime(true);
            
            // Warm main cache
            $activeRedirects = Redirect::getAllActiveRedirects();
            
            // Warm popular redirects
            Redirect::preloadPopularRedirects();
            
            $endTime = microtime(true);
            $duration = round(($endTime - $startTime) * 1000, 2);
            
            Log::info('Scheduled redirect cache warming completed', [
                'active_redirects_count' => $activeRedirects->count(),
                'duration_ms' => $duration
            ]);
            
            $this->info("Cache warmed: {$activeRedirects->count()} redirects in {$duration}ms");
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            Log::error('Scheduled redirect cache warming failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->error("Cache warming failed: " . $e->getMessage());
            
            return Command::FAILURE;
        }
    }
}