<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AutoResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'context_id',
        'title',
        'response_text',
        'attachments',
        'links',
        'is_active',
        'mark_as_resolved',
        'language',
        'usage_count',
        'satisfaction_score',
    ];

    protected $casts = [
        'attachments' => 'array',
        'links' => 'array',
        'is_active' => 'boolean',
        'mark_as_resolved' => 'boolean',
        'usage_count' => 'integer',
        'satisfaction_score' => 'float',
    ];

    /**
     * Get the context this response belongs to
     */
    public function context(): BelongsTo
    {
        return $this->belongsTo(AutoResponseContext::class, 'context_id');
    }

    /**
     * Get the tickets that used this response
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'auto_response_id');
    }

    /**
     * Get the logs for this response
     */
    public function logs(): HasMany
    {
        return $this->hasMany(AutoResponseLog::class, 'response_id');
    }

    /**
     * Scope for active responses
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for language
     */
    public function scopeForLanguage($query, string $language)
    {
        return $query->where('language', $language);
    }

    /**
     * Increment usage count
     */
    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    /**
     * Update satisfaction score based on feedback
     */
    public function updateSatisfactionScore(): void
    {
        $totalFeedback = $this->logs()->whereNotNull('was_helpful')->count();
        
        if ($totalFeedback > 0) {
            $positiveFeedback = $this->logs()->where('was_helpful', true)->count();
            $this->satisfaction_score = round(($positiveFeedback / $totalFeedback) * 5, 2);
            $this->save();
        }
    }

    /**
     * Get formatted response with variables replaced
     */
    public function getFormattedResponse(array $variables = []): string
    {
        $response = $this->response_text;

        // Replace variables like {{user_name}}, {{ticket_number}}, etc.
        foreach ($variables as $key => $value) {
            $response = str_replace('{{' . $key . '}}', $value, $response);
        }

        return $response;
    }

    /**
     * Check if response has attachments
     */
    public function hasAttachments(): bool
    {
        return !empty($this->attachments) && is_array($this->attachments) && count($this->attachments) > 0;
    }

    /**
     * Check if response has links
     */
    public function hasLinks(): bool
    {
        return !empty($this->links) && is_array($this->links) && count($this->links) > 0;
    }

    /**
     * Get effectiveness percentage
     */
    public function getEffectivenessPercentageAttribute(): ?float
    {
        $totalLogs = $this->logs()->whereNotNull('was_helpful')->count();
        
        if ($totalLogs === 0) {
            return null;
        }

        $helpfulLogs = $this->logs()->where('was_helpful', true)->count();
        
        return round(($helpfulLogs / $totalLogs) * 100, 2);
    }
}
