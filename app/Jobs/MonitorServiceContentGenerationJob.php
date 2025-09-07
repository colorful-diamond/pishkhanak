<?php

namespace App\Jobs;

use App\Models\AiContent;
use App\Jobs\GenerateSummaryJob;
use App\Jobs\GenerateImageJob;
use App\Services\ImageGenerationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;

class MonitorServiceContentGenerationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $aiContentId;
    protected $eventHash;
    protected $expectedSections;

    public $tries = 10;
    public $backoff = [30, 60, 120, 180, 240]; // Retry with increasing delays

    /**
     * Create a new job instance.
     */
    public function __construct($aiContentId, $eventHash, $expectedSections)
    {
        $this->aiContentId = $aiContentId;
        $this->eventHash = $eventHash;
        $this->expectedSections = $expectedSections;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $aiContent = AiContent::find($this->aiContentId);
        
        if (!$aiContent) {
            Log::error('AI content not found for monitoring', ['id' => $this->aiContentId]);
            return;
        }

        // Check if all sections are generated
        $currentSections = $aiContent->ai_sections ?? [];
        $sectionsCount = is_array($currentSections) ? count($currentSections) : 0;
        
        Log::info('Monitoring service content generation', [
            'ai_content_id' => $this->aiContentId,
            'current_sections' => $sectionsCount,
            'expected_sections' => $this->expectedSections,
            'attempts' => $this->attempts()
        ]);

        if ($sectionsCount >= $this->expectedSections) {
            // All sections generated, proceed to summary
            Log::info('All sections generated, proceeding to summary', [
                'ai_content_id' => $this->aiContentId
            ]);
            
            // Update status
            $aiContent->update([
                'sections_generated_at' => now(),
                'current_generation_step' => 'summary'
            ]);
            
            // Dispatch summary generation with auto-process flag (EXACT SAME AS BLOG)
            // This will automatically chain: Summary -> Meta -> FAQ
            GenerateSummaryJob::dispatch($this->aiContentId, $this->eventHash, true)
                ->onQueue('ai-content')
                ->delay(now()->addSeconds(1)); // 1 second delay same as blog
            
            // Also dispatch image generation in parallel
            $this->dispatchImageGeneration($aiContent);
            
            return; // Job completed successfully
        }
        
        // Check if we've exceeded max attempts
        if ($this->attempts() >= 8) {
            Log::warning('Max monitoring attempts reached, proceeding anyway', [
                'ai_content_id' => $this->aiContentId,
                'sections_generated' => $sectionsCount,
                'expected' => $this->expectedSections
            ]);
            
            // Proceed to next steps even if not all sections are complete (SAME AS BLOG)
            // This will automatically chain: Summary -> Meta -> FAQ
            GenerateSummaryJob::dispatch($this->aiContentId, $this->eventHash, true)
                ->onQueue('ai-content')
                ->delay(now()->addSeconds(1));
                
            $this->dispatchImageGeneration($aiContent);
            
            return;
        }
        
        // Not all sections ready, retry this job
        $this->release(30); // Retry after 30 seconds
    }
    
    /**
     * Dispatch image generation jobs - EXACT COPY from blog pipeline
     */
    protected function dispatchImageGeneration($aiContent)
    {
        try {
            // Get image generation service and settings (EXACT SAME AS BLOG PIPELINE)
            $imageService = app(ImageGenerationService::class);
            $imageGenerationSettings = $imageService->getImageSettings();
            
            // Get headings from AI content
            $headings = $aiContent->ai_headings ?? [];
            $language = $aiContent->language ?? 'Persian';
            
            Log::info('Starting image generation for service content', [
                'ai_content_id' => $this->aiContentId,
                'headings_count' => count($headings),
                'settings' => $imageGenerationSettings
            ]);
            
            // Generate prompts using the EXACT SAME method as blog pipeline
            $prompts = $imageService->generateImagePrompts($headings, $language, $imageGenerationSettings);
            
            // Create jobs for parallel image generation (EXACT SAME AS BLOG)
            $jobs = [];
            $totalImages = count($headings);
            
            foreach ($prompts as $index => $promptData) {
                // Each job handles one image independently (EXACT SAME PARAMETERS)
                $jobs[] = new GenerateImageJob(
                    $this->aiContentId,
                    $index,
                    $promptData['prompt'],
                    $promptData['title'],
                    $imageGenerationSettings,
                    $this->eventHash, // Use the event hash as session hash
                    $totalImages // Add total images count for proper progress calculation
                );
            }
            
            // Push jobs to queue with small delays to prevent overload (EXACT SAME DELAY LOGIC)
            foreach ($jobs as $index => $job) {
                // Add a small delay between jobs to prevent API rate limiting
                $delay = $index * 2; // 2 seconds between each job (SAME AS BLOG)
                Queue::later(
                    now()->addSeconds($delay),
                    $job
                );
            }
            
            Log::info('Image generation jobs dispatched in parallel (blog pipeline style)', [
                'ai_content_id' => $this->aiContentId,
                'jobs_count' => count($jobs),
                'parallel_execution' => true,
                'delay_between_jobs' => '2 seconds',
                'total_images' => $totalImages
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to dispatch image generation', [
                'error' => $e->getMessage(),
                'ai_content_id' => $this->aiContentId,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}