<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_hash',
        'service_id',
        'user_id',
        'input_data',
        'status',
        'payment_transaction_id',
        'wallet_transaction_id',
        'error_message',
        'processed_at',
    ];

    protected $casts = [
        'input_data' => 'array',
        'processed_at' => 'datetime',
    ];

    /**
     * Boot the model and generate request hash
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($serviceRequest) {
            if (empty($serviceRequest->request_hash)) {
                $serviceRequest->request_hash = self::generateRequestHash();
            }
        });
    }

    /**
     * Generate a unique request hash
     */
    public static function generateRequestHash(): string
    {
        do {
            $hash = 'req_' . strtoupper(substr(md5(uniqid() . microtime() . random_bytes(16)), 0, 16));
        } while (static::where('request_hash', $hash)->exists());

        return $hash;
    }

    /**
     * Find service request by hash
     */
    public static function findByHash(string $hash): ?self
    {
        return static::where('request_hash', $hash)->first();
    }

    /**
     * Get the service that owns the request
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the user that owns the request
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the payment transaction associated with this request
     */
    public function paymentTransaction(): BelongsTo
    {
        return $this->belongsTo(GatewayTransaction::class, 'payment_transaction_id');
    }

    /**
     * Get the wallet transaction associated with this request
     */
    public function walletTransaction(): BelongsTo
    {
        return $this->belongsTo(\Bavix\Wallet\Models\Transaction::class, 'wallet_transaction_id');
    }

    /**
     * Scope for pending requests
     */
    public function scopePending($query)
    {
        return $query->whereNull('processed_at');
    }

    /**
     * Scope for processed requests
     */
    public function scopeProcessed($query)
    {
        return $query->whereNotNull('processed_at');
    }

    /**
     * Check if the request is pending
     */
    public function isPending(): bool
    {
        return is_null($this->processed_at);
    }

    /**
     * Check if the request is processed
     */
    public function isProcessed(): bool
    {
        return !is_null($this->processed_at);
    }

    /**
     * Mark the request as processed
     */
    public function markAsProcessed(): void
    {
        $this->update(['processed_at' => now()]);
    }

    /**
     * Get the status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'free' => 'رایگان',
            'guest' => 'مهمان',
            'insufficient_balance' => 'موجودی ناکافی',
            'wallet' => 'کیف پول',
            default => $this->status,
        };
    }
} 