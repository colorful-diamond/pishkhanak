<?php

namespace App\Console\Commands;

use App\Services\LocalRequestService;
use Illuminate\Console\Command;

class CleanupExpiredLocalRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'local-requests:cleanup {--dry-run : Show what would be cleaned without actually doing it}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired local requests from database and Redis';

    /**
     * Execute the console command.
     */
    public function handle(LocalRequestService $localRequestService): int
    {
        $this->info('🧹 Starting cleanup of expired local requests...');

        if ($this->option('dry-run')) {
            $this->warn('🔍 Running in dry-run mode - no changes will be made');
        }

        try {
            $cleaned = $localRequestService->cleanupExpiredRequests();
            
            if ($cleaned > 0) {
                $this->info("✅ Successfully cleaned up {$cleaned} expired requests");
            } else {
                $this->info('✨ No expired requests found - nothing to clean');
            }

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Error during cleanup: ' . $e->getMessage());
            return 1;
        }
    }
} 