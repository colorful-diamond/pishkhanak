<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Service;
use Illuminate\Support\Facades\File;

class ExportServiceDataForContentGeneration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:export-service-data-for-content-generation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export service data to a JSON file for content generation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Exporting service data...');

        $servicesToUpdate = Service::with('parent:id,slug')
            ->where('id', '>', 200)
            ->whereNotNull('parent_id')
            ->get(['id', 'title', 'slug', 'price', 'parent_id'])
            ->map(function ($service) {
                return [
                    'id' => $service->id,
                    'title' => $service->title,
                    'slug' => $service->slug,
                    'price' => $service->price,
                    'parent_slug' => $service->parent ? $service->parent->slug : null,
                ];
            })
            ->toArray();

        $allServices = Service::with('parent:id,slug')
            ->get(['id', 'title', 'slug', 'parent_id'])
            ->map(function ($service) {
                return [
                    'id' => $service->id,
                    'title' => $service->title,
                    'slug' => $service->slug,
                    'parent_slug' => $service->parent ? $service->parent->slug : null,
                ];
            })
            ->toArray();

        $creditScoreRatingService = Service::where('slug', 'credit-score-rating')->first();
        $styleGuideContent = $creditScoreRatingService ? $creditScoreRatingService->content : '';

        $data = [
            'services_to_update' => $servicesToUpdate,
            'all_services' => $allServices,
            'style_guide_content' => $styleGuideContent,
        ];

        $filePath = storage_path('app/service_data_for_content_generation.json');
        File::put($filePath, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        $this->info('Service data exported successfully to ' . $filePath);
    }
} 