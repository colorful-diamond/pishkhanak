<?php

namespace App\Console\Commands;

use App\Models\Redirect;
use Illuminate\Console\Command;

class RedirectCacheClear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redirects:cache-clear {--url= : Clear cache for specific URL only}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear redirect cache';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($url = $this->option('url')) {
            // Clear cache for specific URL
            $this->info("Clearing cache for URL: {$url}");
            Redirect::clearUrlCache($url);
            $this->info('URL cache cleared.');
        } else {
            // Clear all redirect cache
            $this->info('Clearing all redirect cache...');
            Redirect::clearAllCache();
            $this->info('All redirect cache cleared.');
        }

        return Command::SUCCESS;
    }
}