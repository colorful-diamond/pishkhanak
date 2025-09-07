<?php

namespace App\Console\Commands;

use App\Models\Token;
use App\Services\TokenService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GenerateApiTokensCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'tokens:generate
                            {provider? : The provider to generate token for (jibit, finnotech, all)}
                            {--force : Force regenerate existing tokens}
                            {--show-details : Show detailed token information}';

    /**
     * The console command description.
     */
    protected $description = 'Generate API tokens for specified providers by making API calls';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $provider = $this->argument('provider');
        $force = $this->option('force');
        $showDetails = $this->option('show-details');

        $this->info('🚀 Starting API Token Generation...');
        $this->newLine();

        // If no provider specified, ask user
        if (!$provider) {
            $provider = $this->choice(
                'Which provider would you like to generate tokens for?',
                ['jibit', 'finnotech', 'all'],
                'all'
            );
        }

        $providers = $provider === 'all' ? ['jibit', 'finnotech'] : [$provider];
        $results = [];

        foreach ($providers as $providerName) {
            $this->info("📡 Processing {$providerName} provider...");
            
            try {
                $result = $this->generateTokenForProvider($providerName, $force, $showDetails);
                $results[$providerName] = $result;
                
                if ($result['success']) {
                    $this->components->success("✅ {$providerName} token generated successfully");
                    if ($showDetails && isset($result['token'])) {
                        $this->displayTokenDetails($result['token']);
                    }
                } else {
                    $this->components->error("❌ Failed to generate {$providerName} token: {$result['message']}");
                }
            } catch (\Exception $e) {
                $this->components->error("💥 Error generating {$providerName} token: " . $e->getMessage());
                $results[$providerName] = ['success' => false, 'message' => $e->getMessage()];
            }
            
            $this->newLine();
        }

        // Summary
        $this->displaySummary($results);
        
        return Command::SUCCESS;
    }

    /**
     * Generate token for a specific provider
     */
    private function generateTokenForProvider(string $provider, bool $force, bool $showDetails): array
    {
        // Check if token already exists
        $existingToken = Token::where('provider', $provider)->where('is_active', true)->first();
        
        if ($existingToken && !$force) {
            return [
                'success' => false,
                'message' => 'Token already exists. Use --force to regenerate.'
            ];
        }

        // If force, deactivate existing tokens
        if ($force) {
            Token::where('provider', $provider)->update(['is_active' => false]);
        }

        // Use TokenService to generate tokens
        $tokenService = app(TokenService::class);
        
        try {
            $success = match ($provider) {
                'jibit' => $tokenService->generateJibitToken(),
                'finnotech' => $tokenService->generateFinnotechToken(),
                default => false
            };

            if ($success) {
                $token = Token::where('provider', $provider)->where('is_active', true)->first();
                return [
                    'success' => true,
                    'token' => $token,
                    'message' => 'Token generated successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to generate token via TokenService'
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Exception: ' . $e->getMessage()
            ];
        }
    }



    /**
     * Display token details
     */
    private function displayTokenDetails(Token $token): void
    {
        $this->table(['Field', 'Value'], [
            ['Provider', $token->provider],
            ['Name', $token->name],
            ['Access Token', '••••••••••••' . substr($token->access_token, -8)],
            ['Refresh Token', $token->refresh_token ? '••••••••••••' . substr($token->refresh_token, -8) : 'N/A'],
            ['Expires At', $token->expires_at ? $token->expires_at->format('Y-m-d H:i:s') . ' (' . $token->expires_at->diffForHumans() . ')' : 'Never'],
            ['Refresh Expires At', $token->refresh_expires_at ? $token->refresh_expires_at->format('Y-m-d H:i:s') . ' (' . $token->refresh_expires_at->diffForHumans() . ')' : 'Never'],
            ['Active', $token->is_active ? 'Yes' : 'No'],
            ['Created At', $token->created_at->format('Y-m-d H:i:s')],
        ]);
    }

    /**
     * Display generation summary
     */
    private function displaySummary(array $results): void
    {
        $this->info('📊 Token Generation Summary:');
        $this->newLine();

        $successful = 0;
        $failed = 0;

        foreach ($results as $provider => $result) {
            $status = $result['success'] ? '✅ Success' : '❌ Failed';
            $message = $result['message'] ?? '';
            
            $this->line("  {$provider}: {$status}" . ($message ? " - {$message}" : ''));
            
            if ($result['success']) {
                $successful++;
            } else {
                $failed++;
            }
        }

        $this->newLine();
        $this->info("✅ Successful: {$successful}");
        if ($failed > 0) {
            $this->error("❌ Failed: {$failed}");
        }

        if ($successful > 0) {
            $this->newLine();
            $this->comment('💡 You can now use the generated tokens in your application.');
            $this->comment('💡 Use "php artisan tokens:health" to check token status.');
            $this->comment('💡 Use "php artisan tokens:refresh" to refresh tokens when needed.');
        }
    }
} 