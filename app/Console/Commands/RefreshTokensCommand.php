<?php

namespace App\Console\Commands;

use App\Services\TokenService;
use Illuminate\Console\Command;

class RefreshTokensCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tokens:refresh 
                            {--provider= : Specific provider to refresh (jibit or finnotech)}
                            {--force : Force refresh even if not needed}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh API tokens for Jibit and Finnotech services';

    /**
     * Execute the console command.
     */
    public function handle(TokenService $tokenService): int
    {
        $provider = $this->option('provider');
        $force = $this->option('force');

        $this->info('Starting token refresh process...');

        if ($provider) {
            // Refresh specific provider
            $this->info("Refreshing tokens for provider: {$provider}");
            
            $success = $tokenService->refreshToken($provider);
            
            if ($success) {
                $this->info("✅ Successfully refreshed tokens for {$provider}");
            } else {
                $this->error("❌ Failed to refresh tokens for {$provider}");
                return 1;
            }
        } else {
            // Refresh all tokens
            if ($force) {
                $this->info('Force refreshing all tokens...');
                $results = [
                    'jibit' => $tokenService->refreshJibitToken(),
                    'finnotech' => $tokenService->refreshFinnotechToken(),
                ];
            } else {
                $this->info('Refreshing tokens that need refresh...');
                $results = $tokenService->refreshAllTokensNeedingRefresh();
            }

            foreach ($results as $providerName => $success) {
                if ($success) {
                    $this->info("✅ Successfully refreshed tokens for {$providerName}");
                } else {
                    $this->error("❌ Failed to refresh tokens for {$providerName}");
                }
            }

            $successCount = array_sum($results);
            $totalCount = count($results);

            if ($successCount === $totalCount) {
                $this->info("🎉 All tokens refreshed successfully ({$successCount}/{$totalCount})");
            } else {
                $this->warn("⚠️  Some tokens failed to refresh ({$successCount}/{$totalCount})");
            }
        }

        // Show token health status
        $this->info("\n📊 Token Health Status:");
        $healthStatus = $tokenService->getTokenHealthStatus();
        
        $headers = ['Provider', 'Active', 'Access Expired', 'Refresh Expired', 'Needs Refresh', 'Last Used'];
        $rows = [];

        foreach ($healthStatus as $providerName => $status) {
            $rows[] = [
                $providerName,
                $status['active'] ? '✅' : '❌',
                $status['access_token_expired'] ? '❌' : '✅',
                $status['refresh_token_expired'] ? '❌' : '✅',
                $status['needs_refresh'] ? '⚠️' : '✅',
                $status['last_used_at'] ? \Carbon\Carbon::parse($status['last_used_at'])->diffForHumans() : 'Never'
            ];
        }

        $this->table($headers, $rows);

        // Cleanup expired tokens
        $deactivatedCount = $tokenService->deactivateExpiredTokens();
        if ($deactivatedCount > 0) {
            $this->info("🧹 Deactivated {$deactivatedCount} expired tokens");
        }

        $this->info('✨ Token refresh process completed!');
        
        return 0;
    }
} 