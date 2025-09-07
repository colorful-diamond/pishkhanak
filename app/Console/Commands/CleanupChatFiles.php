<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FileUploadHandler;

class CleanupChatFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai-chat:cleanup-files {--hours=24 : Hours old files to delete}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old AI chat uploaded files';

    /**
     * Execute the console command.
     */
    public function handle(FileUploadHandler $fileHandler)
    {
        $hours = (int) $this->option('hours');
        
        $this->info("Cleaning up files older than {$hours} hours...");
        
        $deletedCount = $fileHandler->cleanupOldFiles($hours);
        
        $this->info("Deleted {$deletedCount} old files.");
        
        return Command::SUCCESS;
    }
} 