<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AutoResponseContext extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'keywords',
        'example_queries',
        'is_active',
        'priority',
        'confidence_threshold',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'priority' => 'integer',
        'confidence_threshold' => 'float',
    ];

    /**
     * Get the responses for this context
     */
    public function responses(): HasMany
    {
        return $this->hasMany(AutoResponse::class, 'context_id');
    }

    /**
     * Get the active responses for this context
     */
    public function activeResponses(): HasMany
    {
        return $this->responses()->where('is_active', true);
    }

    /**
     * Get the logs for this context
     */
    public function logs(): HasMany
    {
        return $this->hasMany(AutoResponseLog::class, 'context_id');
    }

    /**
     * Get keywords as array
     */
    public function getKeywordsArrayAttribute(): array
    {
        if (empty($this->keywords)) {
            return [];
        }
        
        return array_map('trim', explode(',', $this->keywords));
    }

    /**
     * Get example queries as array
     */
    public function getExampleQueriesArrayAttribute(): array
    {
        if (empty($this->example_queries)) {
            return [];
        }
        
        return array_map('trim', explode("\n", $this->example_queries));
    }

    /**
     * Scope for active contexts
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordering by priority
     */
    public function scopeOrderByPriority($query)
    {
        return $query->orderBy('priority', 'desc');
    }

    /**
     * Get the best response for a given language
     */
    public function getBestResponseForLanguage(string $language = 'fa'): ?AutoResponse
    {
        return $this->activeResponses()
            ->where('language', $language)
            ->orderBy('satisfaction_score', 'desc')
            ->orderBy('usage_count', 'desc')
            ->first();
    }

    /**
     * Calculate effectiveness score based on logs
     */
    public function getEffectivenessScoreAttribute(): ?float
    {
        $totalLogs = $this->logs()->count();
        
        if ($totalLogs === 0) {
            return null;
        }

        $helpfulLogs = $this->logs()->where('was_helpful', true)->count();
        
        return round(($helpfulLogs / $totalLogs) * 100, 2);
    }
}
