<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TelegramSecurityEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_type',
        'admin_id',
        'telegram_user_id',
        'ip_hash',
        'details',
        'severity'
    ];

    protected $casts = [
        'details' => 'array'
    ];

    /**
     * Get the admin associated with this security event
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(TelegramAdmin::class, 'admin_id');
    }

    /**
     * Get event type display name in Persian
     */
    public function getEventTypeDisplayAttribute(): string
    {
        $eventTypes = [
            'auth_success' => 'ورود موفق',
            'auth_failure' => 'ورود ناموفق',
            'auth_attempt_invalid_user' => 'تلاش ورود کاربر نامعتبر',
            'auth_attempt_inactive_user' => 'تلاش ورود کاربر غیرفعال',
            'auth_attempt_locked_user' => 'تلاش ورود کاربر قفل شده',
            'permission_denied' => 'عدم دسترسی',
            'unauthorized_access' => 'دسترسی غیرمجاز',
            'rate_limit_hit' => 'محدودیت نرخ',
            'wallet_operation' => 'عملیات کیف پول',
            'sensitive_config_change' => 'تغییر تنظیمات حساس',
            'admin_role_change' => 'تغییر نقش مدیر',
            'token_operation' => 'عملیات توکن',
            'system_access' => 'دسترسی سیستم',
            'suspicious_activity' => 'فعالیت مشکوک',
        ];

        return $eventTypes[$this->event_type] ?? $this->event_type;
    }

    /**
     * Get severity display name in Persian
     */
    public function getSeverityDisplayAttribute(): string
    {
        $severities = [
            'info' => 'اطلاعات',
            'warning' => 'هشدار',
            'error' => 'خطا',
            'critical' => 'بحرانی'
        ];

        return $severities[$this->severity] ?? $this->severity;
    }

    /**
     * Get severity color for UI
     */
    public function getSeverityColorAttribute(): string
    {
        $colors = [
            'info' => 'blue',
            'warning' => 'yellow',
            'error' => 'orange',
            'critical' => 'red'
        ];

        return $colors[$this->severity] ?? 'gray';
    }

    /**
     * Get severity emoji
     */
    public function getSeverityEmojiAttribute(): string
    {
        $emojis = [
            'info' => 'ℹ️',
            'warning' => '⚠️',
            'error' => '🚨',
            'critical' => '🔥'
        ];

        return $emojis[$this->severity] ?? '📝';
    }

    /**
     * Check if event is critical
     */
    public function isCritical(): bool
    {
        return $this->severity === 'critical';
    }

    /**
     * Check if event is error level or above
     */
    public function isError(): bool
    {
        return in_array($this->severity, ['error', 'critical']);
    }

    /**
     * Check if event is warning level or above
     */
    public function isWarning(): bool
    {
        return in_array($this->severity, ['warning', 'error', 'critical']);
    }

    /**
     * Get formatted event description
     */
    public function getDescriptionAttribute(): string
    {
        $description = $this->event_type_display;
        
        if ($this->admin) {
            $description .= " توسط {$this->admin->display_name}";
        } elseif ($this->telegram_user_id) {
            $description .= " توسط کاربر {$this->telegram_user_id}";
        }

        return $description;
    }

    /**
     * Scope for critical events only
     */
    public function scopeCritical($query)
    {
        return $query->where('severity', 'critical');
    }

    /**
     * Scope for error level and above
     */
    public function scopeErrors($query)
    {
        return $query->whereIn('severity', ['error', 'critical']);
    }

    /**
     * Scope for warning level and above
     */
    public function scopeWarnings($query)
    {
        return $query->whereIn('severity', ['warning', 'error', 'critical']);
    }

    /**
     * Scope for specific event types
     */
    public function scopeOfType($query, string $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    /**
     * Scope for events by admin
     */
    public function scopeForAdmin($query, int $adminId)
    {
        return $query->where('admin_id', $adminId);
    }

    /**
     * Scope for events by telegram user
     */
    public function scopeForTelegramUser($query, string $telegramUserId)
    {
        return $query->where('telegram_user_id', $telegramUserId);
    }

    /**
     * Scope for recent events
     */
    public function scopeRecent($query, int $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }

    /**
     * Scope for events in date range
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}