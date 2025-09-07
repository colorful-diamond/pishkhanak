<?php

namespace App\Jobs;

use App\Services\TokenService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Exception;

class RefreshApiTokensJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The maximum number of seconds the job can run.
     *
     * @var int
     */
    public $timeout = 300; // 5 minutes

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        // Set queue for this job
        $this->onQueue('tokens');
    }

    /**
     * Execute the job.
     */
    public function handle(TokenService $tokenService): void
    {
        Log::info('Starting API tokens refresh job');

        try {
            // Refresh all tokens that need refresh
            $results = $tokenService->refreshAllTokensNeedingRefresh();
            
            // Deactivate expired tokens
            $deactivatedCount = $tokenService->deactivateExpiredTokens();
            
            // Get token health status
            $healthStatus = $tokenService->getTokenHealthStatus();
            
            Log::info('API tokens refresh job completed', [
                'refresh_results' => $results,
                'deactivated_tokens' => $deactivatedCount,
                'health_status' => $healthStatus
            ]);

            // Log summary
            $successCount = array_sum($results);
            $totalProviders = count($results);
            
            if ($successCount === $totalProviders) {
                Log::info("All tokens refreshed successfully ({$successCount}/{$totalProviders})");
            } elseif ($successCount > 0) {
                Log::warning("Some tokens failed to refresh ({$successCount}/{$totalProviders})");
            } else {
                Log::error("Failed to refresh any tokens (0/{$totalProviders})");
            }

            if ($deactivatedCount > 0) {
                Log::info("Deactivated {$deactivatedCount} expired tokens");
            }

        } catch (Exception $e) {
            Log::error('API tokens refresh job failed: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e; // Re-throw to mark job as failed
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(Exception $exception): void
    {
        Log::error('API tokens refresh job failed permanently', [
            'exception' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
        
        // You could add notification logic here to alert administrators
        // For example, send an email or Slack notification
    }

    /**
     * Calculate the number of seconds to wait before retrying the job.
     *
     * @return int
     */
    public function backoff(): int
    {
        return 60; // Wait 60 seconds between retries
    }
} 