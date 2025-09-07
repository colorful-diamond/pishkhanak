<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiProcessingStatus extends Model
{
    protected $fillable = [
        'pipeline_id',
        'job_id',
        'batch_id',
        'status',
        'current_step',
        'total_steps',
        'completed_steps',
        'steps',
        'step_timings',
        'progress_percentage',
        'current_message',
        'error_message',
        'error_details',
        'retry_count',
        'started_at',
        'completed_at',
        'duration_seconds',
        'metadata',
    ];

    protected $casts = [
        'steps' => 'array',
        'step_timings' => 'array',
        'error_details' => 'array',
        'metadata' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Status constants
    const STATUS_QUEUED = 'queued';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';

    // Step definitions
    const STEPS = [
        'initialization' => 'PERSIAN_TEXT_818cc3bd',
        'content_analysis' => 'PERSIAN_TEXT_000db5dc',
        'web_search' => 'PERSIAN_TEXT_078f6104',
        'content_generation' => 'PERSIAN_TEXT_0cb468e8',
        'title_optimization' => 'PERSIAN_TEXT_43ef1fa5',
        'summary_generation' => 'PERSIAN_TEXT_44bf365e',
        'meta_generation' => 'PERSIAN_TEXT_cbf77746',
        'faq_generation' => 'PERSIAN_TEXT_6fb4bd31',
        'schema_generation' => 'PERSIAN_TEXT_27f69a12',
        'image_generation' => 'PERSIAN_TEXT_00b4f311',
        'quality_check' => 'PERSIAN_TEXT_e33f5deb',
        'finalization' => 'PERSIAN_TEXT_3dfed555',
    ];

    public function pipeline(): BelongsTo
    {
        return $this->belongsTo(BlogContentPipeline::class, 'pipeline_id');
    }

    /**
     * Initialize processing with steps
     */
    public static function initializeForPipeline(int $pipelineId, string $jobId = null, string $type = 'blog'): self
    {
        // Use different steps for services
        $stepDefinitions = $type === 'service' ? [
            'initialization' => 'PERSIAN_TEXT_818cc3bd',
            'service_analysis' => 'PERSIAN_TEXT_09df886a',
            'headings_generation' => 'PERSIAN_TEXT_eba4eb9b',
            'content_generation' => 'PERSIAN_TEXT_d687f599',
            'summary_generation' => 'PERSIAN_TEXT_44bf365e',
            'meta_generation' => 'PERSIAN_TEXT_cbf77746',
            'faq_generation' => 'PERSIAN_TEXT_6fb4bd31',
            'image_generation' => 'PERSIAN_TEXT_00b4f311',
            'quality_check' => 'PERSIAN_TEXT_e33f5deb',
            'finalization' => 'PERSIAN_TEXT_3dfed555',
        ] : self::STEPS;
        
        $steps = [];
        foreach ($stepDefinitions as $key => $label) {
            $steps[$key] = [
                'label' => $label,
                'status' => 'pending',
                'started_at' => null,
                'completed_at' => null,
                'duration' => null,
                'message' => null,
            ];
        }

        return self::create([
            'pipeline_id' => $pipelineId,
            'job_id' => $jobId,
            'status' => self::STATUS_QUEUED,
            'total_steps' => count($steps),
            'completed_steps' => 0,
            'steps' => $steps,
            'step_timings' => [],
            'progress_percentage' => 0,
            'current_message' => 'PERSIAN_TEXT_bf5db967',
            'metadata' => ['type' => $type],
        ]);
    }

    /**
     * Start processing
     */
    public function startProcessing(): void
    {
        $this->update([
            'status' => self::STATUS_PROCESSING,
            'started_at' => now(),
            'current_message' => 'PERSIAN_TEXT_c3cdc02c',
        ]);
    }

    /**
     * Update current step
     */
    public function updateStep(string $stepKey, string $status = 'processing', string $message = null): void
    {
        $steps = $this->steps;
        
        if (!isset($steps[$stepKey])) {
            return;
        }

        // Update step status
        $steps[$stepKey]['status'] = $status;
        $steps[$stepKey]['message'] = $message;

        if ($status === 'processing') {
            $steps[$stepKey]['started_at'] = now()->toIso8601String();
            $this->current_step = $stepKey;
            $this->current_message = $steps[$stepKey]['label'] . ($message ? ': ' . $message : '');
        } elseif ($status === 'completed') {
            $steps[$stepKey]['completed_at'] = now()->toIso8601String();
            if ($steps[$stepKey]['started_at']) {
                $steps[$stepKey]['duration'] = now()->diffInSeconds($steps[$stepKey]['started_at']);
            }
            $this->completed_steps++;
        } elseif ($status === 'failed') {
            $steps[$stepKey]['failed_at'] = now()->toIso8601String();
        }

        // Calculate progress
        $completedCount = collect($steps)->where('status', 'completed')->count();
        $this->progress_percentage = round(($completedCount / $this->total_steps) * 100);

        $this->steps = $steps;
        $this->save();
    }

    /**
     * Mark step as completed
     */
    public function completeStep(string $stepKey, string $message = null): void
    {
        $this->updateStep($stepKey, 'completed', $message);
    }

    /**
     * Mark step as failed
     */
    public function failStep(string $stepKey, string $error): void
    {
        $this->updateStep($stepKey, 'failed', $error);
    }

    /**
     * Complete processing
     */
    public function completeProcessing(array $metadata = []): void
    {
        $duration = $this->started_at ? (int) now()->diffInSeconds($this->started_at) : null;
        
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'completed_at' => now(),
            'duration_seconds' => $duration,
            'progress_percentage' => 100,
            'current_message' => 'PERSIAN_TEXT_aa3636be',
            'metadata' => array_merge($this->metadata ?? [], $metadata),
        ]);
    }

    /**
     * Fail processing
     */
    public function failProcessing(string $error, array $details = []): void
    {
        $duration = $this->started_at ? (int) now()->diffInSeconds($this->started_at) : null;
        
        $this->update([
            'status' => self::STATUS_FAILED,
            'completed_at' => now(),
            'duration_seconds' => $duration,
            'error_message' => $error,
            'error_details' => $details,
            'current_message' => 'PERSIAN_TEXT_2178a646' . $error,
        ]);
    }

    /**
     * Cancel processing
     */
    public function cancelProcessing(): void
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
            'completed_at' => now(),
            'current_message' => 'PERSIAN_TEXT_163f95e2',
        ]);
    }

    /**
     * Get step status
     */
    public function getStepStatus(string $stepKey): ?string
    {
        return $this->steps[$stepKey]['status'] ?? null;
    }

    /**
     * Get formatted duration
     */
    public function getFormattedDuration(): string
    {
        if (!$this->duration_seconds) {
            return '-';
        }

        $minutes = floor($this->duration_seconds / 60);
        $seconds = $this->duration_seconds % 60;

        if ($minutes > 0) {
            return "PERSIAN_TEXT_05ff7c3e";
        }

        return "PERSIAN_TEXT_9333ff68";
    }

    /**
     * Check if processing is in progress
     */
    public function isProcessing(): bool
    {
        return $this->status === self::STATUS_PROCESSING;
    }

    /**
     * Check if processing is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if processing failed
     */
    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }
}