<?php

namespace App\Jobs;

use App\Models\BlogContentPipeline;
use App\Models\AiProcessingStatus;
use App\Models\BlogProcessingLog;
use App\Services\AiService;
use App\Services\BlogImageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessBlogContentWithMonitoringJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $pipelineId;
    protected array $options;
    protected ?AiProcessingStatus $status = null;

    /**
     * تولید تصویر شاخص برای مقاله
     */
    protected function generateThumbnail(BlogContentPipeline $pipeline): void
    {
        try {
            $imageService = app(BlogImageService::class);
            
            // Generate programmatic thumbnail with Persian text
            $thumbnailPath = $imageService->generateBlogImage(
                $pipeline,
                'programmatic',
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
            $this->updateStep('image_generation', 'failed', 'خطا در تولید تصویر شاخص');
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
        
        // Update only essential fields to avoid varchar limit errors
        try {
            $pipeline->update([
                'status' => BlogContentPipeline::STATUS_FAILED,
                'processing_errors' => [
                    'error' => substr($e->getMessage(), 0, 1000), // Limit error message length
                    'timestamp' => now()->toIso8601String(),
                ],
            ]);
        } catch (\Exception $updateError) {
            // If update fails, at least log it
            Log::error('Failed to update pipeline status', [
                'pipeline_id' => $pipeline->id,
                'error' => $updateError->getMessage()
            ]);
        }
        
        // Update processing status
        if ($this->status) {
            // Mark current step as failed if any
            if ($this->status->current_step) {
                $this->status->failStep($this->status->current_step, $e->getMessage());
            }
            
            $this->status->failProcessing($e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
        }
        
        // Log failure
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
        Log::error('ProcessBlogContentWithMonitoringJob completely failed', [
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
        
        // Mark status as failed
        if ($this->status) {
            $this->status->failProcessing('پردازش محتوا با شکست مواجه شد: ' . $exception->getMessage());
        }
    }
}