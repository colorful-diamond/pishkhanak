<?php

namespace App\Console\Commands;

use App\Models\Token;
use App\Services\TokenService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RefreshAllTokensCommand extends Command
{
    protected $signature = 'tokens:refresh-all 
                            {--force : Force refresh even if not needed}
                            {--dry-run : Show what would be done without making changes}';

    protected $description = 'Refresh ALL individual tokens (not just by provider)';

    public function handle(TokenService $tokenService): int
    {
        // Set memory limit for this command
        $memoryLimit = env('PHP_MEMORY_LIMIT', '4G');
        ini_set('memory_limit', $memoryLimit);
        
        $this->info('🔄 Refreshing ALL tokens individually...');
        $this->info("💾 Memory limit set to: {$memoryLimit}");
        
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');
        
        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be made');
        }
        
        // Get all active tokens
        $tokens = Token::where('is_active', true)->get(['id', 'name', 'provider', 'expires_at', 'refresh_expires_at']);
        
        $this->info("📊 Found {$tokens->count()} active tokens");
        
        $needsRefresh = [];
        $processed = 0;
        $refreshed = 0;
        $skipped = 0;
        $failed = 0;
        
        foreach ($tokens as $token) {
            $processed++;
            
            // Check if token needs refresh
            $tokenNeedsRefresh = $token->expires_at && $token->expires_at->subHours(2)->isPast();
            
            if (!$force && !$tokenNeedsRefresh) {
                $this->line("⏭️  Skipping {$token->name} - not needed yet (expires: {$token->expires_at?->format('Y-m-d H:i')})");
                $skipped++;
                continue;
            }
            
            $this->line("🔄 Processing {$token->name} ({$token->provider})...");
            
            if ($dryRun) {
                $this->info("   Would refresh: {$token->name}");
                continue;
            }
            
            try {
                // Refresh token by name
                $success = $tokenService->refreshTokenByName($token->name);
                
                if ($success) {
                    $this->info("   ✅ Success: {$token->name}");
                    $refreshed++;
                } else {
                    $this->error("   ❌ Failed: {$token->name}");
                    $failed++;
                }
                
            } catch (\Exception $e) {
                $this->error("   💥 Exception for {$token->name}: {$e->getMessage()}");
                $failed++;
                
                Log::error("Token refresh failed for {$token->name}", [
                    'error' => $e->getMessage(),
                    'provider' => $token->provider
                ]);
            }
            
            // Small delay to prevent overwhelming APIs
            usleep(500000); // 0.5 seconds
        }
        
        // Summary
        $this->newLine();
        $this->info('📈 Summary:');
        $this->info("   Processed: {$processed}");
        $this->info("   Refreshed: {$refreshed}");
        $this->info("   Skipped: {$skipped}");
        $this->info("   Failed: {$failed}");
        
        if ($failed > 0) {
            $this->warn("⚠️  Some tokens failed to refresh. Check logs for details.");
            return 1;
        }
        
        if ($refreshed === 0 && !$dryRun) {
            $this->warn("ℹ️  No tokens needed refreshing.");
        } else {
            $this->info('✅ Token refresh completed successfully!');
        }
        
        return 0;
    }
}