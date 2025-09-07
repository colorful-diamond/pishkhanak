<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Finnotech\SmsAuthorizationService;
use Illuminate\Support\Facades\Log;

class CleanupExpiredSmsTokensCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'finnotech:cleanup-sms-tokens 
                           {--dry-run : Show what would be deleted without actually deleting}
                           {--force : Force cleanup without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired Finnotech SMS authorization tokens from Redis';

    /**
     * The SMS authorization service instance.
     *
     * @var SmsAuthorizationService
     */
    private $smsAuthService;

    /**
     * Create a new command instance.
     *
     * @param SmsAuthorizationService $smsAuthService
     */
    public function __construct(SmsAuthorizationService $smsAuthService)
    {
        parent::__construct();
        $this->smsAuthService = $smsAuthService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->info('Starting Finnotech SMS token cleanup...');

        try {
            // Get statistics before cleanup
            $beforeStats = $this->getTokenStatistics();
            
            $this->line('Current token statistics:');
            $this->table(
                ['Metric', 'Count'],
                [
                    ['Total Tokens', $beforeStats['total']],
                    ['Valid Tokens', $beforeStats['valid']],
                    ['Expired Tokens', $beforeStats['expired']],
                ]
            );

            if ($beforeStats['expired'] === 0) {
                $this->info('No expired tokens found. Nothing to clean up.');
                return self::SUCCESS;
            }

            // Check if dry run
            if ($this->option('dry-run')) {
                $this->warn('DRY RUN MODE: Would remove ' . $beforeStats['expired'] . ' expired tokens');
                return self::SUCCESS;
            }

            // Ask for confirmation unless force flag is used
            if (!$this->option('force') && !$this->confirm('Do you want to proceed with cleaning up ' . $beforeStats['expired'] . ' expired tokens?')) {
                $this->info('Cleanup cancelled.');
                return self::SUCCESS;
            }

            // Perform cleanup
            $this->info('Cleaning up expired tokens...');
            $removedCount = $this->smsAuthService->cleanupExpiredTokens();

            // Get statistics after cleanup
            $afterStats = $this->getTokenStatistics();

            $this->line('');
            $this->info('Cleanup completed successfully!');
            $this->table(
                ['Metric', 'Before', 'After', 'Removed'],
                [
                    ['Total Tokens', $beforeStats['total'], $afterStats['total'], $beforeStats['total'] - $afterStats['total']],
                    ['Valid Tokens', $beforeStats['valid'], $afterStats['valid'], $beforeStats['valid'] - $afterStats['valid']],
                    ['Expired Tokens', $beforeStats['expired'], $afterStats['expired'], $beforeStats['expired'] - $afterStats['expired']],
                ]
            );

            $this->info("Successfully removed {$removedCount} expired tokens.");

            // Log the cleanup operation
            Log::info('Finnotech SMS token cleanup completed', [
                'removed_count' => $removedCount,
                'before_stats' => $beforeStats,
                'after_stats' => $afterStats,
                'command_options' => [
                    'dry_run' => $this->option('dry-run'),
                    'force' => $this->option('force')
                ]
            ]);

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error during token cleanup: ' . $e->getMessage());
            
            Log::error('Finnotech SMS token cleanup failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return self::FAILURE;
        }
    }

    /**
     * Get token statistics from Redis.
     *
     * @return array
     */
    private function getTokenStatistics(): array
    {
        try {
            $pattern = 'finnotech:sms_auth:*';
            $keys = \Illuminate\Support\Facades\Redis::keys($pattern);
            
            $stats = [
                'total' => count($keys),
                'valid' => 0,
                'expired' => 0,
                'invalid' => 0
            ];

            foreach ($keys as $key) {
                try {
                    $tokenJson = \Illuminate\Support\Facades\Redis::get($key);
                    if (!$tokenJson) {
                        $stats['invalid']++;
                        continue;
                    }

                    $tokenData = json_decode($tokenJson, true);
                    if (!$tokenData || !isset($tokenData['expires_at'])) {
                        $stats['invalid']++;
                        continue;
                    }

                    $expiresAt = \Carbon\Carbon::parse($tokenData['expires_at']);
                    if ($expiresAt->isPast()) {
                        $stats['expired']++;
                    } else {
                        $stats['valid']++;
                    }
                } catch (\Exception $e) {
                    $stats['invalid']++;
                }
            }

            return $stats;
        } catch (\Exception $e) {
            return [
                'total' => 0,
                'valid' => 0,
                'expired' => 0,
                'invalid' => 0
            ];
        }
    }
} 