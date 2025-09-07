<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TokenService;
use App\Models\Token;
use Illuminate\Support\Facades\Log;

class TestFinnotechTokenRefresh extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'finnotech:test-token-refresh {--token-name= : Specific token name to test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Finnotech token refresh functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tokenService = new TokenService();
        $tokenName = $this->option('token-name');

        $this->info('Testing Finnotech token refresh system...');
        $this->newLine();

        if ($tokenName) {
            $this->testSpecificToken($tokenService, $tokenName);
        } else {
            $this->testAllTokens($tokenService);
        }

        $this->info('Token refresh test completed!');
    }

    /**
     * Test a specific token
     */
    private function testSpecificToken(TokenService $tokenService, string $tokenName): void
    {
        $this->info("Testing token: {$tokenName}");
        
        // Get current token status
        $token = Token::getByName($tokenName);
        if (!$token) {
            $this->error("Token {$tokenName} not found!");
            return;
        }

        $this->info("Current token status:");
        $this->info("- Active: " . ($token->is_active ? 'Yes' : 'No'));
        $this->info("- Access token expired: " . ($token->isAccessTokenExpired() ? 'Yes' : 'No'));
        $this->info("- Refresh token expired: " . ($token->isRefreshTokenExpired() ? 'Yes' : 'No'));
        $this->info("- Needs refresh: " . ($token->needsRefresh() ? 'Yes' : 'No'));
        $this->info("- Expires at: " . ($token->expires_at?->format('Y-m-d H:i:s') ?? 'N/A'));
        $this->newLine();

        // Try to refresh the token
        $this->info("Attempting to refresh token...");
        $success = $tokenService->refreshTokenByName($tokenName);
        
        if ($success) {
            $this->info("✅ Token refresh successful!");
            
            // Get updated token status
            $token->refresh();
            $this->info("Updated token status:");
            $this->info("- Expires at: " . ($token->expires_at?->format('Y-m-d H:i:s') ?? 'N/A'));
            $this->info("- Needs refresh: " . ($token->needsRefresh() ? 'Yes' : 'No'));
        } else {
            $this->error("❌ Token refresh failed!");
        }
    }

    /**
     * Test all Finnotech tokens
     */
    private function testAllTokens(TokenService $tokenService): void
    {
        $this->info("Testing all Finnotech tokens...");
        $this->newLine();

        // Test main Finnotech token
        $this->info("1. Testing main Finnotech token...");
        $this->testSpecificToken($tokenService, Token::NAME_FINNOTECH);
        $this->newLine();

        // Test category tokens
        $categoryTokens = [
            Token::NAME_FINNOTECH_INQUIRY,
            Token::NAME_FINNOTECH_CREDIT,
            Token::NAME_FINNOTECH_KYC,
            Token::NAME_FINNOTECH_TOKEN,
            Token::NAME_FINNOTECH_PROMISSORY,
            Token::NAME_FINNOTECH_VEHICLE,
            Token::NAME_FINNOTECH_INSURANCE,
            Token::NAME_FINNOTECH_SMS,
        ];

        foreach ($categoryTokens as $index => $tokenName) {
            $this->info(($index + 2) . ". Testing {$tokenName}...");
            $this->testSpecificToken($tokenService, $tokenName);
            $this->newLine();
        }

        // Show overall health status
        $this->info("Overall token health status:");
        $healthStatus = $tokenService->getTokenHealthStatus();
        
        foreach ($healthStatus as $tokenName => $status) {
            if (str_contains($tokenName, 'fino')) {
                $statusIcon = $status['needs_refresh'] ? '⚠️' : '✅';
                $this->info("{$statusIcon} {$tokenName}: " . ($status['needs_refresh'] ? 'Needs refresh' : 'OK'));
            }
        }
    }
} 