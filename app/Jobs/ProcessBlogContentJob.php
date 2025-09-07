<?php

namespace App\Jobs;

use App\Models\BlogContentPipeline;
use App\Models\BlogProcessingLog;
use App\Services\AiService;
use App\Services\BlogImageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessBlogContentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $pipelineId;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 1; // Only try once to prevent duplicate content generation

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     */
    public $maxExceptions = 1; // Stop after first exception to prevent duplicates

    /**
     * The number of seconds the job can run before timing out.
     */
    public $timeout = 1200; // 20 minutes

    /**
     * Create a new job instance.
     */
    public function __construct(int $pipelineId)
    {
        $this->pipelineId = $pipelineId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $pipeline = BlogContentPipeline::find($this->pipelineId);
        
        if (!$pipeline) {
            Log::error('BlogContentPipeline not found', ['id' => $this->pipelineId]);
            return;
        }
        
        try {
            // Mark as processing
            $pipeline->update([
                'status' => BlogContentPipeline::STATUS_PROCESSING,
                'processing_started_at' => now(),
                'processing_attempts' => $pipeline->processing_attempts + 1,
            ]);
            
            // Log start
            BlogProcessingLog::logAiProcess(
                $pipeline->id,
                BlogProcessingLog::STATUS_STARTED,
                ['job_id' => $this->job?->getJobId()]
            );
            
            // Get AI service
            $aiService = app(AiService::class);
            
            // Create comprehensive prompt
            $prompt = $this->createPrompt($pipeline);
            
            // Generate content using Gemini with grounding
            $result = $aiService->generateBlogContent(
                title: $pipeline->title,
                prompt: $prompt,
                originalContent: $pipeline->original_content,
                useWebSearch: true, // Always use grounding for best results
                generateImages: false
            );
            
            // Update pipeline with generated content
            $pipeline->update([
                'ai_title' => $result['title'] ?? $pipeline->title,
                'ai_content' => $result['content'] ?? '',
                'ai_summary' => $result['summary'] ?? '',
                'ai_headings' => $result['headings'] ?? [],
                'ai_sections' => $result['sections'] ?? [],
                'ai_meta' => $result['meta'] ?? [],
                'ai_faq' => $result['faq'] ?? [],
                'ai_schema' => $result['schema'] ?? [],
                'meta_title' => $result['meta_title'] ?? '',
                'meta_description' => $result['meta_description'] ?? '',
                'meta_keywords' => $result['meta_keywords'] ?? [],
                'status' => BlogContentPipeline::STATUS_PROCESSED,
                'processing_completed_at' => now(),
                'processing_time_seconds' => now()->diffInSeconds($pipeline->processing_started_at),
                'ai_model_used' => 'gemini-2.5-pro-grounding',
            ]);
            
            // Generate thumbnail image
            $this->generateThumbnail($pipeline);
            
            // Calculate quality score
            $qualityScore = $pipeline->calculateQualityScore();
            $pipeline->update(['quality_score' => $qualityScore]);
            
            // Check if review is needed
            if ($qualityScore < 0.7) {
                $pipeline->update(['requires_review' => true]);
            }
            
            // Log completion
            BlogProcessingLog::logAiProcess(
                $pipeline->id,
                BlogProcessingLog::STATUS_COMPLETED,
                [
                    'quality_score' => $qualityScore,
                    'processing_time' => $pipeline->processing_time_seconds,
                    'model' => 'gemini-2.5-pro-grounding',
                ]
            );
            
            Log::info('Blog content processed successfully', [
                'pipeline_id' => $pipeline->id,
                'title' => $pipeline->ai_title,
                'quality_score'PERSIAN_TEXT_039f54e9'programmatic',
                [
                    'subtitle' => $pipeline->ai_headings[0] ?? null,
                ]
            );
            
            if ($thumbnailPath) {
                $pipeline->update([
                    'ai_images' => ['thumbnail' => $thumbnailPath]
                ]);
            }
            
        } catch (\Exception $e) {
            Log::warning('Thumbnail generation failed', [
                'pipeline_id' => $pipeline->id,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Handle job failure
     */
    protected function handleFailure(BlogContentPipeline $pipeline, \Exception $e): void
    {
        Log::error('Blog content processing failed', [
            'pipeline_id' => $pipeline->id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        $pipeline->update([
            'status' => BlogContentPipeline::STATUS_FAILED,
            'processing_errors' => [
                'error' => $e->getMessage(),
                'timestamp' => now()->toIso8601String(),
            ],
        ]);
        
        BlogProcessingLog::logAiProcess(
            $pipeline->id,
            BlogProcessingLog::STATUS_FAILED,
            [
                'error' => $e->getMessage(),
                'attempts' => $pipeline->processing_attempts,
            ]
        );
        
        // Rethrow to trigger retry mechanism
        throw $e;
    }
    
    /**
     * The job failed to process.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('ProcessBlogContentJob completely failed', [
            'pipeline_id' => $this->pipelineId,
            'error' => $exception->getMessage()
        ]);
        
        // Update status to failed after all retries
        $pipeline = BlogContentPipeline::find($this->pipelineId);
        if ($pipeline) {
            $pipeline->update([
                'status' => BlogContentPipeline::STATUS_FAILED,
                'requires_review' => true,
            ]);
        }
    }
}