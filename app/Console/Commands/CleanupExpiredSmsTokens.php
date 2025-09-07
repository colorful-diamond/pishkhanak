<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Finnotech\SmsAuthorizationService;
use Illuminate\Support\Facades\Log;

class CleanupExpiredSmsTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'finnotech:cleanup-expired-sms-tokens 
                           {--dry-run : Show what would be deleted without actually deleting}
                           {--force : Force cleanup without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired SMS authorization tokens from Redis';

    /**
     * @var SmsAuthorizationService
     */
    private $smsAuthService;

    /**
     * Create a new command instance.
     */
    public function __construct(SmsAuthorizationService $smsAuthService)
    {
        parent::__construct();
        $this->smsAuthService = $smsAuthService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $force = $this->option('force');

        $this->info('🧹 Finnotech SMS Token Cleanup');
        $this->info('================================');

        if ($isDryRun) {
            $this->warn('🔍 DRY RUN MODE - No tokens will be deleted');
        }

        // Get count of tokens before cleanup
        $beforeCount = $this->getTokenCount();
        $this->info("📊 Current SMS tokens in Redis: {$beforeCount}");

        if ($beforeCount === 0) {
            $this->info('✅ No SMS tokens found in Redis - nothing to clean up');
            return Command::SUCCESS;
        }

        // Confirm deletion unless force flag is used
        if (!$isDryRun && !$force) {
            if (!$this->confirm('Are you sure you want to clean up expired SMS tokens?')) {
                $this->info('❌ Cleanup cancelled');
                return Command::FAILURE;
            }
        }

        $this->info('🔄 Starting cleanup process...');

        try {
            if ($isDryRun) {
                $removedCount = $this->simulateCleanup();
                $this->info("🔍 DRY RUN: Would remove {$removedCount} expired tokens");
            } else {
                $removedCount = $this->smsAuthService->cleanupExpiredTokens();
                $this->info("🗑️  Removed {$removedCount} expired tokens");
            }

            $afterCount = $this->getTokenCount();
            $this->info("📊 SMS tokens remaining: {$afterCount}");
            
            if ($removedCount > 0) {
                $this->info("✅ Cleanup completed successfully!");
                
                // Log the cleanup activity
                Log::info('SMS token cleanup completed via command', [
                    'removed_count' => $removedCount,
                    'tokens_before' => $beforeCount,
                    'tokens_after' => $afterCount,
                    'dry_run' => $isDryRun,
                    'executed_by' => 'console_command'
                ]);
            } else {
                $this->info("ℹ️  No expired tokens found to clean up");
            }

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("❌ Error during cleanup: {$e->getMessage()}");
            
            Log::error('SMS token cleanup command failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return Command::FAILURE;
        }
    }

    /**
     * Get current count of SMS tokens in Redis.
     *
     * @return int
     */
    private function getTokenCount(): int
    {
        try {
            $pattern = 'finnotech:sms_auth:*';
            $keys = \Illuminate\Support\Facades\Redis::keys($pattern);
            return count($keys);
        } catch (\Exception $e) {
            $this->warn("Warning: Could not count tokens: {$e->getMessage()}");
            return 0;
        }
    }

    /**
     * Simulate cleanup for dry run mode.
     *
     * @return int Number of tokens that would be removed
     */
    private function simulateCleanup(): int
    {
        $pattern = 'finnotech:sms_auth:*';
        $keys = \Illuminate\Support\Facades\Redis::keys($pattern);
        $expiredCount = 0;

        foreach ($keys as $key) {
            try {
                $tokenJson = \Illuminate\Support\Facades\Redis::get($key);
                if (!$tokenJson) {
                    $expiredCount++;
                    continue;
                }

                $tokenData = json_decode($tokenJson, true);
                if (!$tokenData || !isset($tokenData['expires_at'])) {
                    $expiredCount++;
                    continue;
                }

                $expiresAt = \Carbon\Carbon::parse($tokenData['expires_at']);
                if ($expiresAt->isPast()) {
                    $expiredCount++;
                    $this->line("  🔍 Would delete: {$key} (expired {$expiresAt->diffForHumans()})");
                }
            } catch (\Exception $e) {
                $this->warn("  ⚠️  Error checking {$key}: {$e->getMessage()}");
                $expiredCount++;
            }
        }

        return $expiredCount;
    }
} 