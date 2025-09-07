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
use Exception;
use Carbon\Carbon;

class RefreshAllTokensJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 900; // 15 minutes
    public int $backoff = 120; // 2 minutes
    
    private bool $forceRefresh;

    /**
     * Create a new job instance.
     */
    public function __construct(bool $forceRefresh = false)
    {
        $this->forceRefresh = $forceRefresh;
        $this->onQueue('tokens');
    }

    /**
     * Execute the job.
     */
    public function handle(TokenService $tokenService): void
    {
        $startTime = now();
        
        Log::info('Starting refresh for ALL tokens individually', [
            'force_refresh' => $this->forceRefresh,
            'started_at' => $startTime->toISOString()
        ]);

        $results = [];
        $totalProcessed = 0;
        $totalRefreshed = 0;
        $totalSkipped = 0;
        $totalFailed = 0;

        try {
            // Get all active tokens (not just by provider)
            $tokens = Token::where('is_active', true)->get();
            
            Log::info('Found tokens to process', [
                'total_tokens' => $tokens->count(),
                'token_names' => $tokens->pluck('name')->toArray()
            ]);

            foreach ($tokens as $token) {
                $tokenResult = $this->refreshSingleToken($tokenService, $token);
                $results[$token->name] = $tokenResult;
                $totalProcessed++;

                if ($tokenResult['success'] && !$tokenResult['skipped']) {
                    $totalRefreshed++;
                } elseif ($tokenResult['skipped']) {
                    $totalSkipped++;
                } else {
                    $totalFailed++;
                }

                // Small delay between refreshes to prevent overwhelming the APIs
                sleep(2);
            }

            $endTime = now();
            $duration = $startTime->diffInSeconds($endTime);

            Log::info('Completed refreshing all tokens', [
                'total_processed' => $totalProcessed,
                'total_refreshed' => $totalRefreshed,
                'total_skipped' => $totalSkipped,
                'total_failed' => $totalFailed,
                'duration_seconds' => $duration,
                'results' => $results
            ]);

        } catch (Exception $e) {
            Log::error('RefreshAllTokensJob failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'processed' => $totalProcessed
            ]);
            throw $e;
        }
    }

    /**
     * Refresh a single token
     */
    private function refreshSingleToken(TokenService $tokenService, Token $token): array
    {
        $startTime = now();
        
        Log::info("Processing token: {$token->name}", [
            'provider' => $token->provider,
            'expires_at' => $token->expires_at?->toISOString(),
            'needs_refresh' => $token->needsRefresh()
        ]);

        // Create refresh log
        $log = TokenRefreshLog::create([
            'provider' => $token->provider,
            'token_name' => $token->name,
            'trigger_type' => TokenRefreshLog::TRIGGER_AUTOMATIC,
            'started_at' => $startTime,
        ]);

        try {
            // Check if refresh is needed (unless forced)
            if (!$this->forceRefresh && !$token->needsRefresh()) {
                $log->markSkipped("Token doesn't need refresh yet", [
                    'expires_at' => $token->expires_at?->toISOString(),
                    'refresh_expires_at' => $token->refresh_expires_at?->toISOString(),
                ]);

                Log::info("Skipped {$token->name} - not needed yet");

                return [
                    'success' => true,
                    'skipped' => true,
                    'message' => "Token doesn't need refresh yet",
                    'log_id' => $log->id,
                ];
            }

            // Store old token info
            $oldMetadata = [
                'old_expires_at' => $token->expires_at?->toISOString(),
                'old_refresh_expires_at' => $token->refresh_expires_at?->toISOString(),
                'force_refresh' => $this->forceRefresh,
            ];

            // Perform refresh based on token type
            $success = $this->performTokenRefresh($tokenService, $token);

            if ($success) {
                // Refresh token model from database
                $token->refresh();
                
                $newMetadata = array_merge($oldMetadata, [
                    'new_expires_at' => $token->expires_at?->toISOString(),
                    'new_refresh_expires_at' => $token->refresh_expires_at?->toISOString(),
                    'refreshed_at' => now()->toISOString(),
                ]);

                $log->markSuccessful("Token refreshed successfully", $newMetadata);
                
                Log::info("Successfully refreshed {$token->name}", $newMetadata);

                return [
                    'success' => true,
                    'skipped' => false,
                    'message' => 'Token refreshed successfully',
                    'log_id' => $log->id,
                    'metadata' => $newMetadata,
                ];
            } else {
                throw new Exception("Token refresh failed for {$token->name}");
            }

        } catch (Exception $e) {
            $errorMessage = "Failed to refresh token {$token->name}: " . $e->getMessage();
            
            $log->markFailed($errorMessage, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'token_name' => $token->name,
                'provider' => $token->provider,
            ]);

            Log::error($errorMessage, [
                'token_name' => $token->name,
                'provider' => $token->provider,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'skipped' => false,
                'message' => $errorMessage,
                'log_id' => $log->id,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Perform the actual token refresh based on token type
     */
    private function performTokenRefresh(TokenService $tokenService, Token $token): bool
    {
        try {
            if ($token->provider === Token::PROVIDER_JIBIT) {
                return $tokenService->refreshToken(Token::PROVIDER_JIBIT);
            } elseif ($token->provider === Token::PROVIDER_FINNOTECH) {
                // For Finnotech tokens, try to refresh by specific token name first
                return $this->refreshFinnotechToken($tokenService, $token);
            }

            return false;

        } catch (Exception $e) {
            Log::error("performTokenRefresh failed for {$token->name}", [
                'error' => $e->getMessage(),
                'provider' => $token->provider
            ]);
            return false;
        }
    }

    /**
     * Refresh Finnotech token by name
     */
    private function refreshFinnotechToken(TokenService $tokenService, Token $token): bool
    {
        try {
            // First try refreshing by specific token name if the service supports it
            if (method_exists($tokenService, 'refreshTokenByName')) {
                return $tokenService->refreshTokenByName($token->name);
            }

            // Fallback: try refresh by provider (this will refresh the main token)
            $success = $tokenService->refreshToken(Token::PROVIDER_FINNOTECH);
            
            if ($success) {
                // If main token was refreshed successfully, we might need to copy 
                // the new token data to all categorized tokens
                $this->propagateFinnotechTokens($token);
                return true;
            }

            return false;

        } catch (Exception $e) {
            Log::error("refreshFinnotechToken failed", [
                'token_name' => $token->name,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Propagate main Finnotech token to categorized tokens
     */
    private function propagateFinnotechTokens(Token $categoryToken): void
    {
        try {
            // Find the main Finnotech token (if it exists)
            $mainToken = Token::where('provider', Token::PROVIDER_FINNOTECH)
                ->where('name', Token::NAME_FINNOTECH_TOKEN)
                ->first();

            if (!$mainToken) {
                Log::warning('No main Finnotech token found for propagation');
                return;
            }

            // Update the category token with new token data
            $categoryToken->update([
                'access_token' => $mainToken->access_token,
                'refresh_token' => $mainToken->refresh_token,
                'expires_at' => $mainToken->expires_at,
                'refresh_expires_at' => $mainToken->refresh_expires_at,
            ]);

            // Clear cache for this token
            $categoryToken->clearCache();

            Log::info("Propagated token data from main to {$categoryToken->name}");

        } catch (Exception $e) {
            Log::error('Failed to propagate Finnotech token', [
                'category_token' => $categoryToken->name,
                'error' => $e->getMessage()
            ]);
        }
    }
}