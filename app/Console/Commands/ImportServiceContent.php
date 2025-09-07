<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Service;
use Illuminate\Support\Facades\File;

class ImportServiceContent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-service-content {service_id} {file_path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import content for a service from a file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $serviceId = $this->argument('service_id');
        $filePath = $this->argument('file_path');

        $service = Service::find($serviceId);

        if (!$service) {
            $this->error("Service with ID {$serviceId} not found.");
            return 1;
        }

        if (!File::exists($filePath)) {
            $this->error("File not found at path: {$filePath}");
            return 1;
        }

        $content = File::get($filePath);
        $service->content = $content;
        $service->save();

        $this->info("Content for service '{$service->title}' (ID: {$serviceId}) has been imported successfully from {$filePath}.");

        return 0;
    }
} 