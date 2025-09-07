<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogProcessingLog extends Model
{
    use HasFactory;

    protected $table = 'blog_processing_logs';

    protected $fillable = [
        'pipeline_id',
        'action',
        'status',
        'details',
        'error_message',
        'user_agent',
        'ip_address',
        'user_id',
    ];

    protected $casts = [
        'details' => 'array',
    ];

    // Action constants
    const ACTION_IMPORT = 'import';
    const ACTION_AI_PROCESS = 'ai_process';
    const ACTION_REVIEW = 'review';
    const ACTION_PUBLISH = 'publish';
    const ACTION_QUALITY_CHECK = 'quality_check';
    const ACTION_SCHEDULE = 'schedule';
    const ACTION_RETRY = 'retry';

    // Status constants
    const STATUS_STARTED = 'started';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';

    // Relationships
    public function pipeline(): BelongsTo
    {
        return $this->belongsTo(BlogContentPipeline::class, 'pipeline_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeForAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Helper methods
    public static function logImport(int $pipelineId, string $status, array $details = [], ?string $error = null): self
    {
        return self::create([
            'pipeline_id' => $pipelineId,
            'action' => self::ACTION_IMPORT,
            'status' => $status,
            'details' => $details,
            'error_message' => $error,
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public static function logAiProcess(int $pipelineId, string $status, array $details = [], ?string $error = null): self
    {
        return self::create([
            'pipeline_id' => $pipelineId,
            'action' => self::ACTION_AI_PROCESS,
            'status' => $status,
            'details' => $details,
            'error_message' => $error,
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public static function logPublish(int $pipelineId, string $status, array $details = [], ?string $error = null): self
    {
        return self::create([
            'pipeline_id' => $pipelineId,
            'action' => self::ACTION_PUBLISH,
            'status' => $status,
            'details' => $details,
            'error_message' => $error,
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}