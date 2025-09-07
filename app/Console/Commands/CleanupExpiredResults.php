<?php

namespace App\Console\Commands;

use App\Models\ServiceResult;
use Illuminate\Console\Command;

class CleanupExpiredResults extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:cleanup-results {--days=30 : Number of days to keep results}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired service results';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $cutoffDate = now()->subDays($days);

        $this->info("Cleaning up service results older than {$days} days...");

        // Count results to be deleted
        $count = ServiceResult::where('processed_at', '<', $cutoffDate)->count();

        if ($count === 0) {
            $this->info('No expired results found.');
            return;
        }

        // Delete expired results
        $deleted = ServiceResult::where('processed_at', '<', $cutoffDate)->delete();

        $this->info("Successfully deleted {$deleted} expired results.");

        // Show statistics
        $totalResults = ServiceResult::count();
        $recentResults = ServiceResult::where('processed_at', '>=', $cutoffDate)->count();

        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Results', $totalResults],
                ['Recent Results (last ' . $days . ' days)', $recentResults],
                ['Deleted Results', $deleted],
            ]
        );
    }
} 