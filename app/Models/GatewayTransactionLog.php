<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GatewayTransactionLog extends Model
{
    use HasFactory;

    protected $table = 'gateway_transaction_logs';

    protected $fillable = [
        'gateway_transaction_id',
        'action',
        'source',
        'message',
        'data',
        'request_data',
        'response_data',
        'ip_address',
        'user_agent',
        'method',
        'url',
        'headers',
        'error_code',
        'error_message',
        'stack_trace',
        'response_time_ms',
        'memory_usage_mb',
    ];

    protected $casts = [
        'data' => 'array',
        'request_data' => 'array',
        'response_data' => 'array',
        'headers' => 'array',
        'response_time_ms' => 'integer',
        'memory_usage_mb' => 'integer',
    ];

    // Constants for actions
    const ACTION_CREATED = 'created';
    const ACTION_PROCESSING = 'processing';
    const ACTION_GATEWAY_CALLED = 'gateway_called';
    const ACTION_GATEWAY_RESPONSE = 'gateway_response';
    const ACTION_WEBHOOK_RECEIVED = 'webhook_received';
    const ACTION_COMPLETED = 'completed';
    const ACTION_FAILED = 'failed';
    const ACTION_CANCELLED = 'cancelled';
    const ACTION_REFUNDED = 'refunded';
    const ACTION_EXPIRED = 'expired';

    // Constants for sources
    const SOURCE_WEB = 'web';
    const SOURCE_API = 'api';
    const SOURCE_WEBHOOK = 'webhook';
    const SOURCE_CRON = 'cron';
    const SOURCE_ADMIN = 'admin';
    const SOURCE_SYSTEM = 'system';

    // Relationships
    public function gatewayTransaction(): BelongsTo
    {
        return $this->belongsTo(GatewayTransaction::class);
    }

    // Scopes
    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    public function scopeBySource($query, string $source)
    {
        return $query->where('source', $source);
    }

    public function scopeWithErrors($query)
    {
        return $query->whereNotNull('error_code');
    }

    public function scopeRecentFirst($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    // Helper methods
    public function hasError(): bool
    {
        return !empty($this->error_code) || !empty($this->error_message);
    }

    public function getFormattedResponseTime(): string
    {
        if ($this->response_time_ms) {
            return $this->response_time_ms . 'ms';
        }
        return '-';
    }

    public function getFormattedMemoryUsage(): string
    {
        if ($this->memory_usage_mb) {
            return $this->memory_usage_mb . 'MB';
        }
        return '-';
    }

    public function getActionLabel(): string
    {
        return match($this->action) {
            self::ACTION_CREATED => 'ایجاد شده',
            self::ACTION_PROCESSING => 'در حال پردازش',
            self::ACTION_GATEWAY_CALLED => 'فراخوانی درگاه',
            self::ACTION_GATEWAY_RESPONSE => 'پاسخ درگاه',
            self::ACTION_WEBHOOK_RECEIVED => 'دریافت وب‌هوک',
            self::ACTION_COMPLETED => 'تکمیل شده',
            self::ACTION_FAILED => 'ناموفق',
            self::ACTION_CANCELLED => 'لغو شده',
            self::ACTION_REFUNDED => 'بازگردانده شده',
            self::ACTION_EXPIRED => 'منقضی شده',
            default => $this->action
        };
    }

    public function getSourceLabel(): string
    {
        return match($this->source) {
            self::SOURCE_WEB => 'وب',
            self::SOURCE_API => 'API',
            self::SOURCE_WEBHOOK => 'وب‌هوک',
            self::SOURCE_CRON => 'زمان‌بند',
            self::SOURCE_ADMIN => 'مدیر',
            self::SOURCE_SYSTEM => 'سیستم',
            default => $this->source
        };
    }

    public function getActionBadgeClass(): string
    {
        if ($this->hasError()) {
            return 'danger';
        }

        return match($this->action) {
            self::ACTION_COMPLETED => 'success',
            self::ACTION_PROCESSING, self::ACTION_GATEWAY_CALLED => 'warning',
            self::ACTION_FAILED, self::ACTION_CANCELLED, self::ACTION_EXPIRED => 'danger',
            self::ACTION_REFUNDED => 'info',
            default => 'secondary'
        };
    }
} 