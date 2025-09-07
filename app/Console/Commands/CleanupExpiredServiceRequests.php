<?php

namespace App\Console\Commands;

use App\Models\ServiceRequest;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanupExpiredServiceRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:cleanup-requests {--days=7 : Number of days to keep unprocessed requests}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired/unprocessed service requests';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $cutoffDate = now()->subDays($days);

        $this->info("Cleaning up unprocessed service requests older than {$days} days...");

        // Count unprocessed requests to be deleted
        $count = ServiceRequest::whereNull('processed_at')
            ->where('created_at', '<', $cutoffDate)
            ->count();

        if ($count === 0) {
            $this->info('No expired service requests found.');
            return;
        }

        // Delete expired unprocessed requests
        $deleted = ServiceRequest::whereNull('processed_at')
            ->where('created_at', '<', $cutoffDate)
            ->delete();

        $this->info("Successfully deleted {$deleted} expired service requests.");

        // Show statistics
        $totalRequests = ServiceRequest::count();
        $pendingRequests = ServiceRequest::whereNull('processed_at')->count();
        $processedRequests = ServiceRequest::whereNotNull('processed_at')->count();

        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Requests', $totalRequests],
                ['Pending Requests', $pendingRequests],
                ['Processed Requests', $processedRequests],
                ['Deleted Requests', $deleted],
            ]
        );

        Log::info('Service request cleanup completed', [
            'deleted_count' => $deleted,
            'cutoff_date' => $cutoffDate
        ]);
    }
} 