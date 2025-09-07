<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutoResponseLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'context_id',
        'response_id',
        'user_query',
        'ai_analysis',
        'confidence_score',
        'was_helpful',
        'user_feedback',
        'escalated_to_support',
        'responded_at',
    ];

    protected $casts = [
        'ai_analysis' => 'array',
        'confidence_score' => 'float',
        'was_helpful' => 'boolean',
        'escalated_to_support' => 'boolean',
        'responded_at' => 'datetime',
    ];

    /**
     * Get the ticket associated with this log
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Get the context associated with this log
     */
    public function context(): BelongsTo
    {
        return $this->belongsTo(AutoResponseContext::class, 'context_id');
    }

    /**
     * Get the response associated with this log
     */
    public function response(): BelongsTo
    {
        return $this->belongsTo(AutoResponse::class, 'response_id');
    }

    /**
     * Scope for helpful responses
     */
    public function scopeHelpful($query)
    {
        return $query->where('was_helpful', true);
    }

    /**
     * Scope for escalated tickets
     */
    public function scopeEscalated($query)
    {
        return $query->where('escalated_to_support', true);
    }

    /**
     * Scope for responses with high confidence
     */
    public function scopeHighConfidence($query, float $threshold = 0.8)
    {
        return $query->where('confidence_score', '>=', $threshold);
    }

    /**
     * Mark as helpful
     */
    public function markAsHelpful(string $feedback = null): void
    {
        $this->update([
            'was_helpful' => true,
            'user_feedback' => $feedback,
        ]);

        // Update response satisfaction score
        if ($this->response) {
            $this->response->updateSatisfactionScore();
        }
    }

    /**
     * Mark as not helpful
     */
    public function markAsNotHelpful(string $feedback = null): void
    {
        $this->update([
            'was_helpful' => false,
            'user_feedback' => $feedback,
        ]);

        // Update response satisfaction score
        if ($this->response) {
            $this->response->updateSatisfactionScore();
        }
    }

    /**
     * Escalate to support
     */
    public function escalateToSupport(): void
    {
        $this->update(['escalated_to_support' => true]);
        
        // Update ticket status if needed
        if ($this->ticket) {
            $this->ticket->update(['status' => 'open']);
        }
    }

    /**
     * Get AI analysis summary
     */
    public function getAiAnalysisSummaryAttribute(): string
    {
        if (empty($this->ai_analysis)) {
            return 'No AI analysis available';
        }

        $summary = [];
        
        if (isset($this->ai_analysis['matched_keywords'])) {
            $summary[] = 'Matched keywords: ' . implode(', ', $this->ai_analysis['matched_keywords']);
        }
        
        if (isset($this->ai_analysis['sentiment'])) {
            $summary[] = 'Sentiment: ' . $this->ai_analysis['sentiment'];
        }
        
        if (isset($this->ai_analysis['intent'])) {
            $summary[] = 'Intent: ' . $this->ai_analysis['intent'];
        }

        return implode(' | ', $summary);
    }
}
