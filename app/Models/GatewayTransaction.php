<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class GatewayTransaction extends Model
{
    use HasFactory;

    protected $table = 'gateway_transactions';

    protected $fillable = [
        'uuid',
        'user_id',
        'payment_gateway_id',
        'currency_id',
        'amount',
        'tax_amount',
        'gateway_fee',
        'total_amount',
        'gateway_transaction_id',
        'gateway_reference',
        'gateway_response',
        'type',
        'status',
        'description',
        'metadata',
        'user_ip',
        'user_agent',
        'user_country',
        'user_device',
        'processed_at',
        'completed_at',
        'failed_at',
        'expired_at',
    ];

    protected $casts = [
        'amount' => 'integer',
        'tax_amount' => 'integer',
        'gateway_fee' => 'integer',
        'total_amount' => 'integer',
        'gateway_response' => 'array',
        'metadata' => 'array',
        'processed_at' => 'datetime',
        'completed_at' => 'datetime',
        'failed_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    // Constants
    const TYPE_PAYMENT = 'payment';
    const TYPE_REFUND = 'refund';
    const TYPE_PARTIAL_REFUND = 'partial_refund';

    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REFUNDED = 'refunded';
    const STATUS_PARTIALLY_REFUNDED = 'partially_refunded';
    const STATUS_EXPIRED = 'expired';

    // Boot method to auto-generate UUID
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function paymentGateway(): BelongsTo
    {
        return $this->belongsTo(PaymentGateway::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(GatewayTransactionLog::class);
    }

    // Scopes
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeFailed($query)
    {
        return $query->whereIn('status', [self::STATUS_FAILED, self::STATUS_CANCELLED, self::STATUS_EXPIRED]);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_PROCESSING]);
    }

    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    // Status check methods
    public function isPending(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_PROCESSING]);
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isFailed(): bool
    {
        return in_array($this->status, [self::STATUS_FAILED, self::STATUS_CANCELLED, self::STATUS_EXPIRED]);
    }

    public function canBeRefunded(): bool
    {
        return $this->status === self::STATUS_COMPLETED && $this->type === self::TYPE_PAYMENT;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_PROCESSING]);
    }

    // Status update methods
    public function markAsProcessing(): void
    {
        $this->update([
            'status' => self::STATUS_PROCESSING,
            'processed_at' => now(),
        ]);
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'completed_at' => now(),
        ]);
    }

    public function markAsFailed(string $reason = null): void
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'failed_at' => now(),
            'metadata' => array_merge($this->metadata ?? [], ['failure_reason' => $reason]),
        ]);
    }

    public function markAsCancelled(): void
    {
        $this->update(['status' => self::STATUS_CANCELLED]);
    }

    public function markAsExpired(): void
    {
        $this->update([
            'status' => self::STATUS_EXPIRED,
            'expired_at' => now(),
        ]);
    }

    // Helper methods
    public function getFormattedAmount(): string
    {
        return $this->currency->formatAmount($this->amount);
    }

    public function getFormattedTotalAmount(): string
    {
        return $this->currency->formatAmount($this->total_amount);
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            self::STATUS_COMPLETED => 'success',
            self::STATUS_PENDING, self::STATUS_PROCESSING => 'warning',
            self::STATUS_FAILED, self::STATUS_CANCELLED, self::STATUS_EXPIRED => 'danger',
            self::STATUS_REFUNDED, self::STATUS_PARTIALLY_REFUNDED => 'info',
            default => 'secondary'
        };
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'در انتظار',
            self::STATUS_PROCESSING => 'در حال پردازش',
            self::STATUS_COMPLETED => 'تکمیل شده',
            self::STATUS_FAILED => 'ناموفق',
            self::STATUS_CANCELLED => 'لغو شده',
            self::STATUS_REFUNDED => 'بازگردانده شده',
            self::STATUS_PARTIALLY_REFUNDED => 'بخشی بازگردانده شده',
            self::STATUS_EXPIRED => 'منقضی شده',
            default => $this->status
        };
    }

    public function addLog(string $action, string $source, array $data = []): GatewayTransactionLog
    {
        return $this->logs()->create([
            'action' => $action,
            'source' => $source,
            'message' => $data['message'] ?? null,
            'data' => $data['data'] ?? null,
            'request_data' => $data['request_data'] ?? null,
            'response_data' => $data['response_data'] ?? null,
            'ip_address' => $data['ip_address'] ?? request()?->ip(),
            'user_agent' => $data['user_agent'] ?? request()?->userAgent(),
            'method' => $data['method'] ?? request()?->method(),
            'url' => $data['url'] ?? request()?->fullUrl(),
            'headers' => $data['headers'] ?? null,
            'error_code' => $data['error_code'] ?? null,
            'error_message' => $data['error_message'] ?? null,
            'stack_trace' => $data['stack_trace'] ?? null,
            'response_time_ms' => $data['response_time_ms'] ?? null,
            'memory_usage_mb' => $data['memory_usage_mb'] ?? null,
        ]);
    }
} 