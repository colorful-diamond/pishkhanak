<?php

namespace App\Jobs;

use App\Models\Token;
use App\Models\TokenRefreshLog;
use App\Services\TokenService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\TokenRefreshFailedNotification;
use Exception;
use Carbon\Carbon;

class AutomaticTokenRefreshJob implements ShouldQueue
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
    public $timeout = 600; // 10 minutes

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 60;

    /**
     * Provider to refresh (optional - if specified, only refresh this provider)
     *
     * @var string|null
     */
    private ?string $provider;

    /**
     * Force refresh even if not needed
     *
     * @var bool
     */
    private bool $forceRefresh;

    /**
     * Create a new job instance.
     */
    public function __construct(?string $provider = null, bool $forceRefresh = false)
    {
        $this->provider = $provider;
        $this->forceRefresh = $forceRefresh;
        
        // Set queue for this job
        $this->onQueue('tokens');
    }

    /**
     * Execute the job.
     */
    public function handle(TokenService $tokenService): void
    {
        Log::info('Starting automatic API tokens refresh job', [
            'provider' => $this->provider,
            'force_refresh' => $this->forceRefresh,
            'job_id' => $this->job?->getJobId(),
        ]);

        $startTime = now();
        $results = [];
        $totalRefreshed = 0;
        $totalFailed = 0;

        try {
            if ($this->provider) {
                // Refresh specific provider
                $results[$this->provider] = $this->refreshProvider($tokenService, $this->provider);
            } else {
                // Refresh all providers
                $providers = [Token::PROVIDER_JIBIT, Token::PROVIDER_FINNOTECH];
                
                foreach ($providers as $provider) {
                    $results[$provider] = $this->refreshProvider($tokenService, $provider);
                }
            }

            // Calculate totals
            foreach ($results as $result) {
                if ($result['success']) {
                    $totalRefreshed++;
                } else {
                    $totalFailed++;
                }
            }

            // Deactivate expired tokens
            $deactivatedCount = $tokenService->deactivateExpiredTokens();

            // Log final results
            $duration = now()->diffInMilliseconds($startTime);
            
            Log::info('Automatic API tokens refresh job completed', [
                'results' => $results,
                'total_refreshed' => $totalRefreshed,
                'total_failed' => $totalFailed,
                'deactivated_tokens' => $deactivatedCount,
                'duration_ms' => $duration,
                'provider' => $this->provider,
                'force_refresh' => $this->forceRefresh,
            ]);

            // Send notification if there were failures
            if ($totalFailed > 0) {
                $this->sendFailureNotification($results);
            }

        } catch (Exception $e) {
            Log::error('Automatic API tokens refresh job failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'provider' => $this->provider,
                'force_refresh' => $this->forceRefresh,
            ]);

            // Log failure for all providers if general failure
            if (!$this->provider) {
                foreach ([Token::PROVIDER_JIBIT, Token::PROVIDER_FINNOTECH] as $provider) {
                    $this->logRefreshAttempt($provider, false, $e->getMessage(), 'JOB_EXCEPTION', $e->getTraceAsString());
                }
            } else {
                $this->logRefreshAttempt($this->provider, false, $e->getMessage(), 'JOB_EXCEPTION', $e->getTraceAsString());
            }

            throw $e;
        }
    }

    /**
     * Refresh tokens for a specific provider
     */
    private function refreshProvider(TokenService $tokenService, string $provider): array
    {
        $log = null;
        $startTime = now();
        
        try {
            // Get token info
            $token = Token::getByProvider($provider);
            $tokenName = $token ? $token->name : ($provider === Token::PROVIDER_JIBIT ? Token::NAME_JIBIT : Token::NAME_FINNOTECH);
            
            // Create refresh log
            $log = TokenRefreshLog::create([
                'provider' => $provider,
                'token_name' => $tokenName,
                'trigger_type' => TokenRefreshLog::TRIGGER_AUTOMATIC,
                'started_at' => $startTime,
            ]);

            // Check if refresh is needed (unless forced)
            if (!$this->forceRefresh && $token && !$token->needsRefresh()) {
                $log->markSkipped("Token doesn't need refresh yet", [
                    'expires_at' => $token->expires_at?->toISOString(),
                    'refresh_expires_at' => $token->refresh_expires_at?->toISOString(),
                ]);

                Log::info("Skipping token refresh for {$provider} - not needed yet", [
                    'expires_at' => $token->expires_at?->toISOString(),
                ]);

                return [
                    'success' => true,
                    'skipped' => true,
                    'message' => "Token doesn't need refresh yet",
                    'log_id' => $log->id,
                ];
            }

            // Store old token info for comparison
            $oldMetadata = [
                'old_expires_at' => $token?->expires_at?->toISOString(),
                'old_refresh_expires_at' => $token?->refresh_expires_at?->toISOString(),
                'old_last_used_at' => $token?->last_used_at?->toISOString(),
                'force_refresh' => $this->forceRefresh,
            ];

            // Perform the refresh
            $success = $tokenService->refreshToken($provider);

            if ($success) {
                // Get updated token info
                $updatedToken = Token::getByProvider($provider);
                $newMetadata = array_merge($oldMetadata, [
                    'new_expires_at' => $updatedToken?->expires_at?->toISOString(),
                    'new_refresh_expires_at' => $updatedToken?->refresh_expires_at?->toISOString(),
                    'refreshed_at' => now()->toISOString(),
                ]);

                $log->markSuccessful("Token refreshed successfully", $newMetadata);

                Log::info("Successfully refreshed token for {$provider}", $newMetadata);

                return [
                    'success' => true,
                    'skipped' => false,
                    'message' => 'Token refreshed successfully',
                    'log_id' => $log->id,
                    'metadata' => $newMetadata,
                ];
            } else {
                $log->markFailed("Token refresh failed", 'REFRESH_FAILED', 'TokenService returned false', $oldMetadata);

                Log::error("Failed to refresh token for {$provider}");

                return [
                    'success' => false,
                    'skipped' => false,
                    'message' => 'Token refresh failed',
                    'error' => 'TokenService returned false',
                    'log_id' => $log->id,
                ];
            }

        } catch (Exception $e) {
            if ($log) {
                $log->markFailed($e->getMessage(), 'EXCEPTION', $e->getTraceAsString(), [
                    'provider' => $provider,
                    'force_refresh' => $this->forceRefresh,
                ]);
            }

            Log::error("Exception while refreshing token for {$provider}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'skipped' => false,
                'message' => $e->getMessage(),
                'error' => $e->getMessage(),
                'log_id' => $log?->id,
            ];
        }
    }

    /**
     * Log a refresh attempt (fallback method)
     */
    private function logRefreshAttempt(string $provider, bool $success, string $message, ?string $errorCode = null, ?string $errorDetails = null): void
    {
        try {
            $token = Token::getByProvider($provider);
            $tokenName = $token ? $token->name : ($provider === Token::PROVIDER_JIBIT ? Token::NAME_JIBIT : Token::NAME_FINNOTECH);
            
            $log = TokenRefreshLog::create([
                'provider' => $provider,
                'token_name' => $tokenName,
                'trigger_type' => TokenRefreshLog::TRIGGER_AUTOMATIC,
            ]);

            if ($success) {
                $log->markSuccessful($message);
            } else {
                $log->markFailed($message, $errorCode, $errorDetails);
            }
        } catch (Exception $e) {
            Log::error('Failed to log refresh attempt: ' . $e->getMessage());
        }
    }

    /**
     * Send failure notification to administrators
     */
    private function sendFailureNotification(array $results): void
    {
        try {
            $failedProviders = [];
            foreach ($results as $provider => $result) {
                if (!$result['success']) {
                    $failedProviders[] = [
                        'provider' => $provider,
                        'error' => $result['error'] ?? $result['message'],
                    ];
                }
            }

            if (!empty($failedProviders)) {
                // Here you can implement notification logic
                // For example, send to admin email or Slack
                Log::warning('Token refresh failures detected', [
                    'failed_providers' => $failedProviders,
                    'timestamp' => now()->toISOString(),
                ]);

                // You can uncomment this if you implement the notification class
                // Notification::route('mail', config('app.admin_email'))
                //     ->notify(new TokenRefreshFailedNotification($failedProviders));
            }
        } catch (Exception $e) {
            Log::error('Failed to send failure notification: ' . $e->getMessage());
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(Exception $exception): void
    {
        Log::error('Automatic token refresh job failed permanently', [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
            'provider' => $this->provider,
            'force_refresh' => $this->forceRefresh,
            'attempts' => $this->attempts(),
        ]);

        // Log failure for all relevant providers
        $providers = $this->provider ? [$this->provider] : [Token::PROVIDER_JIBIT, Token::PROVIDER_FINNOTECH];
        
        foreach ($providers as $provider) {
            $this->logRefreshAttempt(
                $provider, 
                false, 
                "Job failed permanently: " . $exception->getMessage(), 
                'JOB_FAILED_PERMANENTLY',
                $exception->getTraceAsString()
            );
        }
    }

    /**
     * Get the tags that should be assigned to the job.
     */
    public function tags(): array
    {
        return [
            'tokens',
            'automatic-refresh',
            $this->provider ?? 'all-providers',
        ];
    }
} 