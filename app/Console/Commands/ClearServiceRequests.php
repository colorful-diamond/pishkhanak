<?php

namespace App\Console\Commands;

use App\Models\ServiceRequest;
use Illuminate\Console\Command;

class ClearServiceRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'service-requests:clear {--force : Force the operation without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all service requests data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = ServiceRequest::count();
        
        if ($count === 0) {
            $this->info('No service requests found to delete.');
            return 0;
        }

        if (!$this->option('force')) {
            if (!$this->confirm("This will delete {$count} service requests. Are you sure?")) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        ServiceRequest::truncate();
        
        $this->info("Successfully deleted {$count} service requests.");
        
        return 0;
    }
} 