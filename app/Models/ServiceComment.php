<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class ServiceComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'user_id',
        'parent_id',
        'content',
        'author_name',
        'author_email',
        'author_phone',
        'rating',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'helpful_count',
        'unhelpful_count',
        'ip_address',
        'user_agent',
        'is_admin_reply',
        'is_featured',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'is_admin_reply' => 'boolean',
        'is_featured' => 'boolean',
        'rating' => 'integer',
        'helpful_count' => 'integer',
        'unhelpful_count' => 'integer',
    ];

    protected $appends = ['author_display_name', 'formatted_date', 'is_parent'];

    /**
     * Get the service this comment belongs to
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the user who wrote this comment
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent comment if this is a reply
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ServiceComment::class, 'parent_id');
    }

    /**
     * Get replies to this comment
     */
    public function replies(): HasMany
    {
        return $this->hasMany(ServiceComment::class, 'parent_id')
            ->with('replies') // Recursive loading for nested replies
            ->orderBy('created_at', 'asc');
    }

    /**
     * Get approved replies only
     */
    public function approvedReplies(): HasMany
    {
        return $this->hasMany(ServiceComment::class, 'parent_id')
            ->where('status', 'approved')
            ->with('approvedReplies') // Recursive loading
            ->orderBy('created_at', 'asc');
    }

    /**
     * Get the user who approved this comment
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get votes for this comment
     */
    public function votes(): HasMany
    {
        return $this->hasMany(CommentVote::class, 'comment_id');
    }

    /**
     * Scope for approved comments
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for pending comments
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for parent comments (not replies)
     */
    public function scopeParentComments(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope for featured comments
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for comments with ratings
     */
    public function scopeWithRating(Builder $query): Builder
    {
        return $query->whereNotNull('rating');
    }

    /**
     * Get display name for author
     */
    public function getAuthorDisplayNameAttribute(): string
    {
        if ($this->user) {
            return $this->user->name;
        }
        
        return $this->author_name ?? 'کاربر مهمان';
    }

    /**
     * Get formatted date
     */
    public function getFormattedDateAttribute(): string
    {
        if ($this->created_at->diffInDays(now()) < 7) {
            return $this->created_at->diffForHumans();
        }
        
        // Use Persian date formatting
        return verta($this->created_at)->format('j F Y');
    }

    /**
     * Check if this is a parent comment
     */
    public function getIsParentAttribute(): bool
    {
        return is_null($this->parent_id);
    }

    /**
     * Approve the comment
     */
    public function approve(User $approvedBy = null): void
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $approvedBy?->id,
            'approved_at' => now(),
        ]);
    }

    /**
     * Reject the comment
     */
    public function reject(string $reason = null, User $rejectedBy = null): void
    {
        $this->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'approved_by' => $rejectedBy?->id,
            'approved_at' => now(),
        ]);
    }

    /**
     * Mark as spam
     */
    public function markAsSpam(): void
    {
        $this->update(['status' => 'spam']);
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(): void
    {
        $this->update(['is_featured' => !$this->is_featured]);
    }

    /**
     * Increment helpful count
     */
    public function markAsHelpful(): void
    {
        $this->increment('helpful_count');
    }

    /**
     * Increment unhelpful count
     */
    public function markAsUnhelpful(): void
    {
        $this->increment('unhelpful_count');
    }

    /**
     * Check if user has voted on this comment
     */
    public function hasUserVoted($userId = null, $ipAddress = null): bool
    {
        if (!$userId && !$ipAddress) {
            return false;
        }

        $query = $this->votes();
        
        if ($userId) {
            $query->where('user_id', $userId);
        } elseif ($ipAddress) {
            $query->where('ip_address', $ipAddress);
        }

        return $query->exists();
    }

    /**
     * Get user's vote type on this comment
     */
    public function getUserVoteType($userId = null, $ipAddress = null): ?string
    {
        if (!$userId && !$ipAddress) {
            return null;
        }

        $query = $this->votes();
        
        if ($userId) {
            $query->where('user_id', $userId);
        } elseif ($ipAddress) {
            $query->where('ip_address', $ipAddress);
        }

        return $query->first()?->vote_type;
    }
}