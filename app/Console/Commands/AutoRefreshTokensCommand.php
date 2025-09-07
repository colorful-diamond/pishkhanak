<?php

namespace App\Console\Commands;

use App\Jobs\AutomaticTokenRefreshJob;
use App\Models\Token;
use Illuminate\Console\Command;

class AutoRefreshTokensCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tokens:auto-refresh 
                            {--provider= : Specific provider to refresh (jibit or finnotech)}
                            {--force : Force refresh even if not needed}
                            {--sync : Run synchronously instead of dispatching to queue}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Trigger automatic token refresh job with advanced logging and monitoring';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $provider = $this->option('provider');
        $force = $this->option('force');
        $sync = $this->option('sync');

        // Validate provider if specified
        if ($provider && !in_array($provider, [Token::PROVIDER_JIBIT, Token::PROVIDER_FINNOTECH])) {
            $this->error("Invalid provider: {$provider}. Must be 'jibit' or 'finnotech'.");
            return 1;
        }

        $this->info('🚀 Triggering automatic token refresh...');
        
        if ($provider) {
            $this->info("📍 Provider: {$provider}");
        } else {
            $this->info("📍 Provider: All providers");
        }
        
        if ($force) {
            $this->warn("⚠️  Force refresh enabled - will refresh even if not needed");
        }

        try {
            $job = new AutomaticTokenRefreshJob($provider, $force);

            if ($sync) {
                $this->info("⏳ Running synchronously...");
                $tokenService = app(\App\Services\TokenService::class);
                $job->handle($tokenService);
                $this->info("✅ Automatic token refresh completed successfully!");
            } else {
                $this->info("📤 Dispatching job to queue...");
                dispatch($job);
                $this->info("✅ Automatic token refresh job dispatched to queue!");
                $this->info("💡 Use 'php artisan queue:work' to process the job");
                $this->info("🔍 Monitor logs and access panel for detailed results");
            }

            // Show next scheduled run
            $this->info("");
            $this->info("📅 Next scheduled automatic refresh: Every 12 hours");
            $this->info("🔗 View detailed logs in the access panel under 'Token Refresh Logs'");

            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Failed to trigger automatic token refresh: " . $e->getMessage());
            return 1;
        }
    }
} 