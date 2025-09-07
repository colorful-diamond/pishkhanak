<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FooterManagerService;

class ClearFooterCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'footer:clear-cache {--location= : Clear cache for specific location}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear footer and links cache';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $location = $this->option('location');

        if ($location) {
            $this->info("Clearing cache for location: {$location}");
            
            \App\Models\FooterSection::clearCache($location);
            \App\Models\SiteLink::clearCache($location);
            
            $this->info("Cache cleared for location: {$location}");
        } else {
            $this->info('Clearing all footer and links cache...');
            
            FooterManagerService::clearAllCache();
            
            $this->info('All footer and links cache cleared successfully!');
        }

        return Command::SUCCESS;
    }
} 