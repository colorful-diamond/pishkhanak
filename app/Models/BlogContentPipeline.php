<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class BlogContentPipeline extends Model
{
    use HasFactory;

    protected $table = 'blog_content_pipeline';

    protected $fillable = [
        'original_id',
        'title',
        'original_content',
        'original_summary',
        'original_faq',
        'original_final',
        'original_slug',
        'is_old',
        'original_updated_at',
        'status',
        'ai_title',
        'ai_content',
        'ai_summary',
        'ai_headings',
        'ai_sections',
        'ai_meta',
        'ai_faq',
        'ai_schema',
        'ai_images',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'slug',
        'processing_attempts',
        'processing_errors',
        'error_message',
        'processing_started_at',
        'processing_completed_at',
        'enhanced_at',
        'processing_time_seconds',
        'ai_model_used',
        'scheduled_publish_date',
        'published_at',
        'published_post_id',
        'category_id',
        'suggested_categories',
        'quality_score',
        'quality_metrics',
        'requires_review',
        'review_notes',
        'imported_by',
        'reviewed_by',
    ];

    protected $casts = [
        'is_old' => 'boolean',
        'original_updated_at' => 'datetime',
        'ai_headings' => 'array',
        'ai_sections' => 'array',
        'ai_meta' => 'array',
        'ai_faq' => 'array',
        'ai_schema' => 'array',
        'ai_images' => 'array',
        'suggested_categories' => 'array',
        'quality_metrics' => 'array',
        'processing_errors' => 'array',
        'meta_keywords' => 'array',
        'requires_review' => 'boolean',
        'processing_started_at' => 'datetime',
        'processing_completed_at' => 'datetime',
        'scheduled_publish_date' => 'datetime',
        'published_at' => 'datetime',
        'quality_score' => 'decimal:2',
    ];

    // Status constants
    const STATUS_IMPORTED = 'imported';
    const STATUS_QUEUED = 'queued';
    const STATUS_PROCESSING = 'processing';
    const STATUS_PROCESSED = 'processed';
    const STATUS_REVIEWED = 'reviewed';
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_PUBLISHED = 'published';
    const STATUS_FAILED = 'failed';
    const STATUS_SKIPPED = 'skipped';

    // Relationships
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function publishedPost(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'published_post_id');
    }

    public function importer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'imported_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function processingQueue(): HasOne
    {
        return $this->hasOne(BlogProcessingQueue::class, 'pipeline_id');
    }

    public function publicationQueue(): HasOne
    {
        return $this->hasOne(BlogPublicationQueue::class, 'pipeline_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(BlogProcessingLog::class, 'pipeline_id');
    }

    public function processingStatuses(): HasMany
    {
        return $this->hasMany(AiProcessingStatus::class, 'pipeline_id');
    }

    public function latestProcessingStatus(): HasOne
    {
        return $this->hasOne(AiProcessingStatus::class, 'pipeline_id')->latestOfMany();
    }

    // Scopes
    public function scopeImported($query)
    {
        return $query->where('status', self::STATUS_IMPORTED);
    }

    public function scopeQueued($query)
    {
        return $query->where('status', self::STATUS_QUEUED);
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', self::STATUS_PROCESSING);
    }

    public function scopeProcessed($query)
    {
        return $query->where('status', self::STATUS_PROCESSED);
    }

    public function scopeReadyForPublication($query)
    {
        return $query->where('status', self::STATUS_PROCESSED)
                    ->where('quality_score', '>=', 0.7)
                    ->where('requires_review', false);
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', self::STATUS_SCHEDULED);
    }

    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    public function scopeRequiringReview($query)
    {
        return $query->where('requires_review', true);
    }

    public function scopeHighQuality($query, $minScore = 0.7)
    {
        return $query->where('quality_score', '>=', $minScore);
    }

    // Helper Methods
    public function markAsProcessing(): void
    {
        $this->update([
            'status' => self::STATUS_PROCESSING,
            'processing_started_at' => now(),
            'processing_attempts' => $this->processing_attempts + 1,
        ]);
    }

    public function markAsProcessed(): void
    {
        $processingTime = $this->processing_started_at 
            ? now()->diffInSeconds($this->processing_started_at)
            : null;

        $this->update([
            'status' => self::STATUS_PROCESSED,
            'processing_completed_at' => now(),
            'processing_time_seconds' => $processingTime,
        ]);
    }

    public function markAsFailed(string $error): void
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'processing_errors' => [
                'error' => $error,
                'timestamp' => now()->toIso8601String(),
                'attempt' => $this->processing_attempts ?? 0,
            ],
        ]);
    }

    public function markAsScheduled(\DateTime $publishDate): void
    {
        $this->update([
            'status' => self::STATUS_SCHEDULED,
            'scheduled_publish_date' => $publishDate,
        ]);
    }

    public function markAsPublished(int $postId): void
    {
        $this->update([
            'status' => self::STATUS_PUBLISHED,
            'published_at' => now(),
            'published_post_id' => $postId,
        ]);
    }

    public function calculateQualityScore(): float
    {
        $score = 0;
        $weights = [
            'content_length' => 0.2,
            'has_meta' => 0.15,
            'has_faq' => 0.15,
            'has_summary' => 0.1,
            'has_schema' => 0.1,
            'has_images' => 0.1,
            'title_quality' => 0.2,
        ];

        // Content length score
        $contentLength = strlen($this->ai_content ?? $this->original_content);
        if ($contentLength >= 1000 && $contentLength <= 5000) {
            $score += $weights['content_length'];
        } elseif ($contentLength > 500) {
            $score += $weights['content_length'] * 0.5;
        }

        // Meta data score
        if ($this->meta_title && $this->meta_description) {
            $score += $weights['has_meta'];
        }

        // FAQ score
        if (!empty($this->ai_faq)) {
            $score += $weights['has_faq'];
        }

        // Summary score
        if ($this->ai_summary) {
            $score += $weights['has_summary'];
        }

        // Schema score
        if (!empty($this->ai_schema)) {
            $score += $weights['has_schema'];
        }

        // Images score
        if (!empty($this->ai_images)) {
            $score += $weights['has_images'];
        }

        // Title quality score
        $title = $this->ai_title ?? $this->title;
        if (strlen($title) >= 30 && strlen($title) <= 100) {
            $score += $weights['title_quality'];
        } elseif (strlen($title) >= 20) {
            $score += $weights['title_quality'] * 0.5;
        }

        $this->quality_score = round($score, 2);
        $this->quality_metrics = [
            'content_length' => $contentLength,
            'has_meta' => (bool)($this->meta_title && $this->meta_description),
            'has_faq' => !empty($this->ai_faq),
            'has_summary' => (bool)$this->ai_summary,
            'has_schema' => !empty($this->ai_schema),
            'has_images' => !empty($this->ai_images),
            'title_length' => strlen($title),
        ];
        $this->save();

        return $this->quality_score;
    }

    public function generateSlug(): string
    {
        $title = $this->ai_title ?? $this->title;
        $baseSlug = Str::slug($title);
        
        // Check for uniqueness
        $slug = $baseSlug;
        $counter = 1;
        
        while (Post::where('slug', $slug)->exists() || 
               self::where('slug', $slug)->where('id', '!=', $this->id)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        
        $this->slug = $slug;
        $this->save();
        
        return $slug;
    }

    public function logAction(string $action, string $status, array $details = [], ?string $error = null): void
    {
        BlogProcessingLog::create([
            'pipeline_id' => $this->id,
            'action' => $action,
            'status' => $status,
            'details' => $details,
            'error_message' => $error,
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function getContentForProcessing(): string
    {
        // Prioritize original content, then summaries, then FAQ
        if ($this->original_content && strlen($this->original_content) > 100) {
            return $this->original_content;
        }
        
        if ($this->original_summary && strlen($this->original_summary) > 50) {
            return $this->original_summary;
        }
        
        if ($this->original_final && strlen($this->original_final) > 50) {
            return $this->original_final;
        }
        
        if ($this->original_faq) {
            return $this->original_faq;
        }
        
        return $this->title;
    }

    public function prepareForPublication(): array
    {
        return [
            'title' => $this->ai_title ?? $this->title,
            'slug' => $this->slug ?? $this->generateSlug(),
            'content' => $this->ai_content ?? $this->original_content,
            'summary' => $this->ai_summary ?? $this->original_summary,
            'description' => $this->meta_description,
            'category_id' => $this->category_id,
            'status' => 'published',
            'published_at' => now(),
            'author_id' => $this->reviewed_by ?? $this->imported_by ?? 1,
            'ai_headings' => $this->ai_headings,
            'ai_sections' => $this->ai_sections,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->meta_keywords,
            'schema' => $this->ai_schema,
            'faqs' => $this->ai_faq,
        ];
    }
}