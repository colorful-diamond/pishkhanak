<?php

namespace App\Console\Commands;

use App\Models\Token;
use App\Services\TokenService;
use Illuminate\Console\Command;

class TokenHealthCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'tokens:health
                            {--detailed : Show detailed information for each token}
                            {--json : Output in JSON format}';

    /**
     * The console command description.
     */
    protected $description = 'Check the health status of all API tokens';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $detailed = $this->option('detailed');
        $jsonOutput = $this->option('json');

        if ($jsonOutput) {
            return $this->outputJson();
        }

        $this->info('🏥 API Token Health Check');
        $this->newLine();

        $tokenService = app(TokenService::class);
        $healthStatus = $tokenService->getTokenHealthStatus();

        if (empty($healthStatus)) {
            $this->warn('⚠️  No token health data available');
            return Command::SUCCESS;
        }

        // Summary table
        $this->displaySummaryTable($healthStatus);

        if ($detailed) {
            $this->newLine();
            $this->displayDetailedView($healthStatus);
        }

        $this->newLine();
        $this->displayRecommendations($healthStatus);

        return Command::SUCCESS;
    }

    /**
     * Output health status in JSON format
     */
    private function outputJson(): int
    {
        $tokenService = app(TokenService::class);
        $healthStatus = $tokenService->getTokenHealthStatus();

        $this->line(json_encode($healthStatus, JSON_PRETTY_PRINT));
        return Command::SUCCESS;
    }

    /**
     * Display summary table
     */
    private function displaySummaryTable(array $healthStatus): void
    {
        $rows = [];

        foreach ($healthStatus as $provider => $status) {
            $providerName = match ($provider) {
                Token::PROVIDER_JIBIT => '🟦 Jibit',
                Token::PROVIDER_FINNOTECH => '🟩 Finnotech',
                default => $provider,
            };

            $exists = $status['exists'] ? '✅ Yes' : '❌ No';
            $active = $status['active'] ? '✅ Active' : '❌ Inactive';
            
            $tokenStatus = 'Unknown';
            if (!$status['exists']) {
                $tokenStatus = '❌ Missing';
            } elseif (!$status['active']) {
                $tokenStatus = '⚪ Inactive';
            } elseif ($status['access_token_expired']) {
                $tokenStatus = '🔴 Expired';
            } elseif ($status['needs_refresh']) {
                $tokenStatus = '🟡 Needs Refresh';
            } else {
                $tokenStatus = '🟢 Healthy';
            }

            $expiresAt = 'Never';
            if ($status['expires_at']) {
                $expiresAt = \Carbon\Carbon::parse($status['expires_at'])->format('Y-m-d H:i') . 
                           ' (' . \Carbon\Carbon::parse($status['expires_at'])->diffForHumans() . ')';
            }

            $rows[] = [
                $providerName,
                $exists,
                $active,
                $tokenStatus,
                $expiresAt,
            ];
        }

        $this->table([
            'Provider',
            'Token Exists',
            'Active',
            'Status',
            'Expires At',
        ], $rows);
    }

    /**
     * Display detailed view
     */
    private function displayDetailedView(array $healthStatus): void
    {
        $this->info('📋 Detailed Token Information:');

        foreach ($healthStatus as $provider => $status) {
            $this->newLine();
            $this->comment("🔍 {$provider} Provider Details:");

            $details = [
                ['Field', 'Value'],
                ['Exists', $status['exists'] ? 'Yes' : 'No'],
                ['Active', $status['active'] ? 'Yes' : 'No'],
                ['Access Token Expired', $status['access_token_expired'] ? 'Yes' : 'No'],
                ['Refresh Token Expired', $status['refresh_token_expired'] ? 'Yes' : 'No'],
                ['Needs Refresh', $status['needs_refresh'] ? 'Yes' : 'No'],
                ['Access Expires At', $status['expires_at'] ? 
                    \Carbon\Carbon::parse($status['expires_at'])->format('Y-m-d H:i:s') . 
                    ' (' . \Carbon\Carbon::parse($status['expires_at'])->diffForHumans() . ')' 
                    : 'Never'],
                ['Refresh Expires At', $status['refresh_expires_at'] ? 
                    \Carbon\Carbon::parse($status['refresh_expires_at'])->format('Y-m-d H:i:s') . 
                    ' (' . \Carbon\Carbon::parse($status['refresh_expires_at'])->diffForHumans() . ')' 
                    : 'Never'],
                ['Last Used At', $status['last_used_at'] ? 
                    \Carbon\Carbon::parse($status['last_used_at'])->format('Y-m-d H:i:s') . 
                    ' (' . \Carbon\Carbon::parse($status['last_used_at'])->diffForHumans() . ')' 
                    : 'Never'],
            ];

            $this->table($details[0], array_slice($details, 1));
        }
    }

    /**
     * Display recommendations
     */
    private function displayRecommendations(array $healthStatus): void
    {
        $recommendations = [];
        $criticalIssues = 0;
        $warnings = 0;

        foreach ($healthStatus as $provider => $status) {
            if (!$status['exists']) {
                $recommendations[] = "❌ Generate {$provider} token: php artisan tokens:generate {$provider}";
                $criticalIssues++;
            } elseif (!$status['active']) {
                $recommendations[] = "⚠️  Activate {$provider} token in the admin panel";
                $warnings++;
            } elseif ($status['access_token_expired']) {
                $recommendations[] = "🔄 Refresh {$provider} token: php artisan tokens:refresh {$provider}";
                $criticalIssues++;
            } elseif ($status['needs_refresh']) {
                $recommendations[] = "🟡 Consider refreshing {$provider} token soon";
                $warnings++;
            }
        }

        if (empty($recommendations)) {
            $this->components->success('🎉 All tokens are healthy! No action required.');
            return;
        }

        $this->info('💡 Recommendations:');
        foreach ($recommendations as $recommendation) {
            $this->line("  {$recommendation}");
        }

        $this->newLine();

        if ($criticalIssues > 0) {
            $this->components->error("⚠️  {$criticalIssues} critical issue(s) found that require immediate attention!");
        }

        if ($warnings > 0) {
            $this->components->warn("⚠️  {$warnings} warning(s) found that should be addressed soon.");
        }

        $this->newLine();
        $this->comment('💡 Helpful Commands:');
        $this->line('  • Generate all tokens: php artisan tokens:generate all --force');
        $this->line('  • Refresh all tokens: php artisan tokens:refresh');
        $this->line('  • Check detailed health: php artisan tokens:health --detailed');
        $this->line('  • Get JSON output: php artisan tokens:health --json');
    }
} 