<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogPublicationQueue extends Model
{
    use HasFactory;

    protected $table = 'blog_publication_queue';

    protected $fillable = [
        'pipeline_id',
        'publish_date',
        'publish_time',
        'publish_order',
        'status',
        'published_at',
        'publish_error',
    ];

    protected $casts = [
        'publish_date' => 'date',
        'publish_time' => 'datetime:H:i:s',
        'published_at' => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_PUBLISHED = 'published';
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

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('publish_date', $date);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('publish_date', today());
    }

    public function scopeTomorrow($query)
    {
        return $query->whereDate('publish_date', today()->addDay());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('publish_date', '>=', today())
                    ->where('status', self::STATUS_PENDING)
                    ->orderBy('publish_date')
                    ->orderBy('publish_order');
    }

    public function scopeReadyToPublish($query)
    {
        return $query->where('status', self::STATUS_PENDING)
                    ->where('publish_date', '<=', today())
                    ->orderBy('publish_order');
    }

    // Helper methods
    public function markAsPublished(): void
    {
        $this->update([
            'status' => self::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);
    }

    public function markAsFailed(string $error): void
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'publish_error' => $error,
        ]);
    }

    public function cancel(): void
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
        ]);
    }

    public function reschedule($newDate): void
    {
        $this->update([
            'publish_date' => $newDate,
            'status' => self::STATUS_PENDING,
            'publish_error' => null,
        ]);
    }
}