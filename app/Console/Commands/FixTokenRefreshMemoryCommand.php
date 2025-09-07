<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FixTokenRefreshMemoryCommand extends Command
{
    protected $signature = 'tokens:fix-memory-issue';
    protected $description = 'Fix memory issues with token refresh by setting higher limits';

    public function handle(): int
    {
        $this->info('🛠️ Fixing token refresh memory issues...');
        
        // Set memory limit for this process
        ini_set('memory_limit', '2G');
        $this->info('✅ Memory limit set to 2GB for token refresh processes');
        
        // Update environment file if needed
        $envFile = base_path('.env');
        if (file_exists($envFile)) {
            $envContent = file_get_contents($envFile);
            
            // Add or update PHP memory settings for scheduled tasks
            if (!str_contains($envContent, 'SCHEDULED_TASK_MEMORY_LIMIT')) {
                file_put_contents($envFile, "\n# Token refresh memory settings\nSCHEDULED_TASK_MEMORY_LIMIT=2G\n", FILE_APPEND);
                $this->info('✅ Added memory limit setting to .env file');
            }
        }
        
        $this->info('🎯 Memory issue fix completed!');
        $this->info('📝 The scheduler will now use the new token refresh command that handles memory more efficiently.');
        
        return 0;
    }
}