<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TelegramAdminSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'session_token',
        'ip_hash',
        'user_agent_hash',
        'expires_at',
        'last_activity_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'last_activity_at' => 'datetime'
    ];

    protected $hidden = [
        'session_token'
    ];

    /**
     * Get the admin that owns this session
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(TelegramAdmin::class, 'admin_id');
    }

    /**
     * Check if session is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if session is active
     */
    public function isActive(): bool
    {
        return !$this->isExpired();
    }

    /**
     * Extend session expiration
     */
    public function extend(int $seconds = 3600): void
    {
        $this->update([
            'expires_at' => now()->addSeconds($seconds),
            'last_activity_at' => now()
        ]);
    }

    /**
     * Update last activity timestamp
     */
    public function touch(): void
    {
        $this->update(['last_activity_at' => now()]);
    }

    /**
     * Get session duration in minutes
     */
    public function getDurationAttribute(): int
    {
        return $this->created_at->diffInMinutes($this->expires_at);
    }

    /**
     * Get time until expiration in minutes
     */
    public function getTimeToExpirationAttribute(): int
    {
        return now()->diffInMinutes($this->expires_at, false);
    }

    /**
     * Scope for active sessions only
     */
    public function scopeActive($query)
    {
        return $query->where('expires_at', '>', now());
    }

    /**
     * Scope for expired sessions only
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }

    /**
     * Scope for sessions by admin
     */
    public function scopeForAdmin($query, int $adminId)
    {
        return $query->where('admin_id', $adminId);
    }

    /**
     * Scope for recent activity
     */
    public function scopeRecentlyActive($query, int $minutes = 30)
    {
        return $query->where('last_activity_at', '>=', now()->subMinutes($minutes));
    }
}