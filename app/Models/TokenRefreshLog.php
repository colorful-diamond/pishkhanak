<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TokenRefreshLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider',
        'token_name',
        'status',
        'trigger_type',
        'message',
        'metadata',
        'started_at',
        'completed_at',
        'duration_ms',
        'error_code',
        'error_details',
    ];

    protected $casts = [
        'metadata' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Status constants
    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED = 'failed';
    const STATUS_SKIPPED = 'skipped';

    // Trigger type constants
    const TRIGGER_AUTOMATIC = 'automatic';
    const TRIGGER_MANUAL = 'manual';
    const TRIGGER_FORCED = 'forced';

    /**
     * Create a new refresh log entry
     */
    public static function create(array $attributes = []): self
    {
        $attributes['started_at'] = $attributes['started_at'] ?? now();
        return parent::create($attributes);
    }

    /**
     * Mark the log as completed
     */
    public function markCompleted(string $status, ?string $message = null, array $metadata = []): void
    {
        $this->update([
            'status' => $status,
            'message' => $message,
            'metadata' => array_merge($this->metadata ?? [], $metadata),
            'completed_at' => now(),
            'duration_ms' => $this->started_at ? now()->diffInMilliseconds($this->started_at) : null,
        ]);
    }

    /**
     * Mark the log as failed with error details
     */
    public function markFailed(string $message, ?string $errorCode = null, ?string $errorDetails = null, array $metadata = []): void
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'message' => $message,
            'error_code' => $errorCode,
            'error_details' => $errorDetails,
            'metadata' => array_merge($this->metadata ?? [], $metadata),
            'completed_at' => now(),
            'duration_ms' => $this->started_at ? now()->diffInMilliseconds($this->started_at) : null,
        ]);
    }

    /**
     * Mark the log as successful
     */
    public function markSuccessful(string $message, array $metadata = []): void
    {
        $this->markCompleted(self::STATUS_SUCCESS, $message, $metadata);
    }

    /**
     * Mark the log as skipped
     */
    public function markSkipped(string $message, array $metadata = []): void
    {
        $this->markCompleted(self::STATUS_SKIPPED, $message, $metadata);
    }

    // Scopes
    public function scopeSuccessful($query)
    {
        return $query->where('status', self::STATUS_SUCCESS);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    public function scopeAutomatic($query)
    {
        return $query->where('trigger_type', self::TRIGGER_AUTOMATIC);
    }

    public function scopeManual($query)
    {
        return $query->where('trigger_type', self::TRIGGER_MANUAL);
    }

    public function scopeForProvider($query, string $provider)
    {
        return $query->where('provider', $provider);
    }

    public function scopeRecent($query, int $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }

    // Accessors
    public function getDurationAttribute(): ?string
    {
        if (!$this->duration_ms) {
            return null;
        }

        if ($this->duration_ms < 1000) {
            return $this->duration_ms . 'ms';
        }

        return round($this->duration_ms / 1000, 2) . 's';
    }

    public function getIsSuccessfulAttribute(): bool
    {
        return $this->status === self::STATUS_SUCCESS;
    }

    public function getIsFailedAttribute(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function getIsSkippedAttribute(): bool
    {
        return $this->status === self::STATUS_SKIPPED;
    }

    /**
     * Get the latest log for a provider
     */
    public static function getLatestForProvider(string $provider): ?self
    {
        return self::where('provider', $provider)
            ->latest('created_at')
            ->first();
    }

    /**
     * Get success rate for a provider in the last X hours
     */
    public static function getSuccessRateForProvider(string $provider, int $hours = 24): float
    {
        $total = self::forProvider($provider)->recent($hours)->count();
        
        if ($total === 0) {
            return 0;
        }

        $successful = self::forProvider($provider)->recent($hours)->successful()->count();
        
        return round(($successful / $total) * 100, 2);
    }

    /**
     * Get statistics for the dashboard
     */
    public static function getStatistics(int $hours = 24): array
    {
        $recentLogs = self::recent($hours);
        
        return [
            'total_attempts' => $recentLogs->count(),
            'successful_attempts' => $recentLogs->successful()->count(),
            'failed_attempts' => $recentLogs->failed()->count(),
            'skipped_attempts' => $recentLogs->where('status', self::STATUS_SKIPPED)->count(),
            'automatic_attempts' => $recentLogs->automatic()->count(),
            'manual_attempts' => $recentLogs->manual()->count(),
            'success_rate' => $recentLogs->count() > 0 ? 
                round(($recentLogs->successful()->count() / $recentLogs->count()) * 100, 2) : 0,
            'avg_duration_ms' => $recentLogs->whereNotNull('duration_ms')->avg('duration_ms'),
        ];
    }
} 