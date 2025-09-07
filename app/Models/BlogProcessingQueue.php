<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogProcessingQueue extends Model
{
    use HasFactory;

    protected $table = 'blog_processing_queue';

    protected $fillable = [
        'pipeline_id',
        'priority',
        'status',
        'processing_config',
        'job_id',
        'batch_id',
        'queued_at',
        'started_at',
        'completed_at',
        'retry_count',
        'last_error',
    ];

    protected $casts = [
        'processing_config' => 'array',
        'queued_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Priority constants
    const PRIORITY_LOW = 'low';
    const PRIORITY_NORMAL = 'normal';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';

    // Relationships
    public function pipeline(): BelongsTo
    {
        return $this->belongsTo(BlogContentPipeline::class, 'pipeline_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', self::STATUS_PROCESSING);
    }

    public function scopeByPriority($query)
    {
        return $query->orderByRaw("FIELD(priority, 'urgent', 'high', 'normal', 'low')")
                    ->orderBy('queued_at');
    }

    public function scopeReadyForProcessing($query)
    {
        return $query->where('status', self::STATUS_PENDING)
                    ->where('retry_count', '<', 3);
    }

    // Helper methods
    public function markAsProcessing(): void
    {
        $this->update([
            'status' => self::STATUS_PROCESSING,
            'started_at' => now(),
        ]);
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'completed_at' => now(),
        ]);
    }

    public function markAsFailed(string $error): void
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'last_error' => $error,
            'retry_count' => $this->retry_count + 1,
        ]);
    }

    public function canRetry(): bool
    {
        return $this->retry_count < 3 && $this->status === self::STATUS_FAILED;
    }

    public function retry(): void
    {
        if ($this->canRetry()) {
            $this->update([
                'status' => self::STATUS_PENDING,
                'job_id' => null,
            ]);
        }
    }
}