<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateServiceContent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-service-content';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate content for services and save as HTML files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating content for services...');

        // Load service data from JSON file
        $filePath = storage_path('app/service_data_for_content_generation.json');
        $data = json_decode(File::get($filePath), true);

        $servicesToUpdate = $data['services_to_update'];

        // Process each service
        foreach ($servicesToUpdate as $service) {
            if (isset($service['done']) && $service['done']) {
                continue; // Skip already processed services
            }

            // Generate content for the service
            $content = $this->generateContentForService($service);

            // Save content to HTML file
            $htmlFilePath = storage_path("app/services/{$service['id']}.html");
            File::put($htmlFilePath, $content);

            $this->info("Content generated for service ID: {$service['id']}");
        }

        $this->info('Content generation completed.');
    }

    /**
     * Generate content for a given service.
     *
     * @param array $service
     * @return string
     */
    private function generateContentForService(array $service): string
    {
        // Placeholder for content generation logic
        // This should include SEO optimization, related service links, and FAQs
        return "<h1>{$service['title']}</h1><p>Generated content for service ID: {$service['id']}</p>";
    }
} 