<?php

namespace App\Jobs;

use App\Models\BlogContentPipeline;
use App\Models\AiContent;
use App\Services\AiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;

class EnhanceBlogContentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 1200; // 20 minutes timeout for content generation
    public $tries = 1; // Only try once to prevent duplicate content generation
    public $maxExceptions = 1; // Stop after first exception to prevent duplicates
    public $backoff = [30]; // Single backoff value since we only try once

    protected $pipelineId;
    protected $title;
    protected $existingContent;
    protected $description;
    protected $options;
    protected $modelType;
    protected $userId;

    /**
     * Create a new job instance.
     */
    public function __construct(
        int $pipelineId,
        string $title,
        string $existingContent,
        string $description,
        array $options,
        string $modelType = 'advanced',
        ?int $userId = null
    ) {
        $this->pipelineId = $pipelineId;
        $this->title = $title;
        $this->existingContent = $existingContent;
        $this->description = $description;
        $this->options = $options;
        $this->modelType = $modelType;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            $pipeline = BlogContentPipeline::find($this->pipelineId);
            if (!$pipeline) {
                Log::error('Blog pipeline not found', ['pipeline_id' => $this->pipelineId]);
                return;
            }

            // Update status to processing
            $pipeline->status = BlogContentPipeline::STATUS_PROCESSING;
            $pipeline->save();

            Log::info('Starting enhanced AI content generation for blog pipeline', [
                'pipeline_id' => $this->pipelineId,
                'title' => $this->title,
                'mode' => $this->options['mode'] ?? 'enhance',
                'existing_content_length' => strlen($this->existingContent)
            ]);

            $aiService = app(AiService::class);

            // Generate/enhance content
            $result = $aiService->generateContent(
                $this->title,
                $this->description,
                'Persian',
                $this->modelType,
                true, // auto_process
                0,    // no specific category
                $this->options
            );

            if ($result) {
                // Update pipeline status
                $pipeline->status = BlogContentPipeline::STATUS_PROCESSED;
                $pipeline->enhanced_at = now();
                $pipeline->save();

                // Extract AI content ID from result URL
                preg_match('/ai-contents\/(\d+)\/edit/', $result, $matches);
                $aiContentId = $matches[1] ?? null;

                if ($aiContentId) {
                    // Link AI content to pipeline
                    $aiContent = AiContent::find($aiContentId);
                    if ($aiContent) {
                        $aiContent->model_type = 'BlogContentPipeline';
                        $aiContent->model_id = $pipeline->id;
                        $aiContent->save();
                    }
                }

                // Send success notification to user
                if ($this->userId) {
                    Notification::make()
                        ->success()
                        ->title('تولید محتوا تکمیل شد')
                        ->body('محتوای هوش مصنوعی برای مقاله «' . $this->title . '» با موفقیت تولید شد و آماده بررسی است.')
                ->persistent()
                ->sendToDatabase(\App\Models\User::find($this->userId));
        }
    }
}