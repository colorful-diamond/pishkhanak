<?php

namespace App\Jobs;

use App\Services\ImageGenerationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120;
    public $tries = 3;
    public $maxExceptions = 3;

    protected $aiContentId;
    protected $sectionIndex;
    protected $prompt;
    protected $settings;
    protected $title;

    /**
     * Create a new job instance.
     */
    public function __construct(int $aiContentId, int $sectionIndex, string $prompt, string $title, array $settings = [])
    {
        $this->aiContentId = $aiContentId;
        $this->sectionIndex = $sectionIndex;
        $this->prompt = $prompt;
        $this->title = $title;
        $this->settings = $settings;
    }

    /**
     * Execute the job.
     */
    public function handle(ImageGenerationService $imageService)
    {
        try {
            Log::info('Starting image generation job', [
                'ai_content_id' => $this->aiContentId,
                'section_index' => $this->sectionIndex,
                'prompt' => $this->prompt
            ]);

            // Generate images using the service
            $images = $imageService->generateImages($this->prompt, $this->settings);

            if (empty($images)) {
                throw new \Exception('No images generated');
            }

            // Store the generated images data
            $this->storeImageData($images);

            Log::info('Image generation job completed successfully', [
                'ai_content_id' => $this->aiContentId,
                'section_index' => $this->sectionIndex,
                'images_count' => count($images)
            ]);

        } catch (\Exception $e) {
            Log::error('Image generation job failed', [
                'ai_content_id' => $this->aiContentId,
                'section_index' => $this->sectionIndex,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    /**
     * Store the generated images data
     */
    protected function storeImageData(array $images)
    {
        $aiContent = \App\Models\AiContent::find($this->aiContentId);
        
        if (!$aiContent) {
            throw new \Exception('AI Content not found');
        }

        // Get existing thumbnails or initialize empty array
        $thumbnails = $aiContent->ai_thumbnails ?? [];
        
        // Add the generated images for this section
        $thumbnails[$this->sectionIndex] = [
            'title' => $this->title,
            'prompt' => $this->prompt,
            'images' => $images,
            'selected_image' => null,
            'final_image' => null,
            'status' => 'pending_selection', // pending_selection, selected, processed
            'generated_at' => now()->toISOString()
        ];

        // Update the AI content
        $aiContent->update([
            'ai_thumbnails' => $thumbnails
        ]);

        // Broadcast the update to frontend
        event(new \App\Events\ImageGenerationCompleted($this->aiContentId, $this->sectionIndex, $images));
    }

    /**
     * Handle job failure.
     */
    public function failed(\Throwable $exception)
    {
        Log::error('Image generation job failed permanently', [
            'ai_content_id' => $this->aiContentId,
            'section_index' => $this->sectionIndex,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);

        // Update the status to failed
        $aiContent = \App\Models\AiContent::find($this->aiContentId);
        if ($aiContent) {
            $thumbnails = $aiContent->ai_thumbnails ?? [];
            $thumbnails[$this->sectionIndex] = [
                'title' => $this->title,
                'prompt' => $this->prompt,
                'images' => [],
                'selected_image' => null,
                'final_image' => null,
                'status' => 'failed',
                'error' => $exception->getMessage(),
                'generated_at' => now()->toISOString()
            ];

            $aiContent->update([
                'ai_thumbnails' => $thumbnails
            ]);
        }

        // Broadcast the failure to frontend
        event(new \App\Events\ImageGenerationFailed($this->aiContentId, $this->sectionIndex, $exception->getMessage()));
    }
} 