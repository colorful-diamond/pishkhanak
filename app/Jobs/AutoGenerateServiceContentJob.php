<?php

namespace App\Jobs;

use App\Models\Service;
use App\Models\AiContent;
use App\Jobs\GenerateHeadingsJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class AutoGenerateServiceContentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $service;

    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    public function handle()
    {
        try {
            Log::info("Starting content generation for service: {$this->service->title} (ID: {$this->service->id})");

            // Create AI content entry
            $serviceTitle = $this->service->title;
            $shortDescription = $this->service->short_description ?? "خدمات {$serviceTitle} با بهترین کیفیت و قیمت مناسب";

            $aiContent = AiContent::create([
                'title' => $serviceTitle,
                'slug' => Str::slug($serviceTitle) . '-' . time(),
                'short_description' => $shortDescription,
                'language' => 'Persian',
                'model_type' => 'advanced',
                'status' => 'pending',
                'temperature' => 0.7,
                'max_tokens' => 4000,
                'parameters' => [
                    'grounding' => true, // Enable online search
                    'sections_count' => 8,
                    'subheadings_per_section' => 4, // Updated from 3 to 4
                    'service_id' => $this->service->id,
                    'parent_service_id' => $this->service->parent_id,
                    'category' => $this->service->category ? $this->service->category->name : null,
                ]
            ]);

            Log::info("Created AI content entry with ID: {$aiContent->id}");

            // Update service to reference the AI content
            $this->service->update([
                'content' => $aiContent->id
            ]);

            Log::info("Updated service {$this->service->id} to reference AI content {$aiContent->id}");

            // Dispatch headings generation job
            GenerateHeadingsJob::dispatch($aiContent->id);

            Log::info("Dispatched GenerateHeadingsJob for AI content: {$aiContent->id}");

        } catch (\Exception $e) {
            Log::error("Failed to generate content for service {$this->service->id}: " . $e->getMessage(), [
                'service_id' => $this->service->id,
                'service_title' => $this->service->title,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }
}