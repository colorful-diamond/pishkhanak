<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'payment_gateway_id',
        'type',
        'name',
        'last_four',
        'card_type',
        'expiry_month',
        'expiry_year',
        'gateway_token',
        'gateway_data',
        'is_default',
        'is_active',
        'verified_at',
        'last_used_at',
    ];

    protected $casts = [
        'gateway_data' => 'array',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'verified_at' => 'datetime',
        'last_used_at' => 'datetime',
    ];

    // Constants
    const TYPE_CARD = 'card';
    const TYPE_BANK_ACCOUNT = 'bank_account';
    const TYPE_WALLET = 'wallet';

    const CARD_TYPE_VISA = 'visa';
    const CARD_TYPE_MASTERCARD = 'mastercard';
    const CARD_TYPE_AMEX = 'amex';

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function paymentGateway(): BelongsTo
    {
        return $this->belongsTo(PaymentGateway::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeVerified($query)
    {
        return $query->whereNotNull('verified_at');
    }

    // Methods
    public function isVerified(): bool
    {
        return !is_null($this->verified_at);
    }

    public function isExpired(): bool
    {
        if ($this->type !== self::TYPE_CARD || !$this->expiry_month || !$this->expiry_year) {
            return false;
        }

        $expiryDate = \Carbon\Carbon::createFromDate($this->expiry_year, $this->expiry_month, 1)->endOfMonth();
        return $expiryDate->isPast();
    }

    public function markAsDefault(): void
    {
        // Remove default flag from other payment methods for this user
        static::where('user_id', $this->user_id)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        $this->update(['is_default' => true]);
    }

    public function markAsUsed(): void
    {
        $this->update(['last_used_at' => now()]);
    }

    public function markAsVerified(): void
    {
        $this->update(['verified_at' => now()]);
    }

    public function getDisplayName(): string
    {
        if ($this->type === self::TYPE_CARD) {
            return $this->name . ' •••• ' . $this->last_four;
        }

        return $this->name;
    }

    public function getFormattedExpiry(): ?string
    {
        if ($this->type !== self::TYPE_CARD || !$this->expiry_month || !$this->expiry_year) {
            return null;
        }

        return sprintf('%02d/%s', $this->expiry_month, substr($this->expiry_year, -2));
    }

    public function getCardTypeIcon(): string
    {
        return match($this->card_type) {
            self::CARD_TYPE_VISA => 'visa',
            self::CARD_TYPE_MASTERCARD => 'mastercard',
            self::CARD_TYPE_AMEX => 'amex',
            default => 'credit-card'
        };
    }

    public function getTypeLabel(): string
    {
        return match($this->type) {
            self::TYPE_CARD => 'کارت',
            self::TYPE_BANK_ACCOUNT => 'حساب بانکی',
            self::TYPE_WALLET => 'کیف پول',
            default => $this->type
        };
    }

    public function canBeUsed(): bool
    {
        return $this->is_active && !$this->isExpired();
    }

    public function getStatusBadgeClass(): string
    {
        if (!$this->is_active) {
            return 'secondary';
        }

        if ($this->isExpired()) {
            return 'danger';
        }

        if (!$this->isVerified()) {
            return 'warning';
        }

        return 'success';
    }

    public function getStatusLabel(): string
    {
        if (!$this->is_active) {
            return 'غیرفعال';
        }

        if ($this->isExpired()) {
            return 'منقضی شده';
        }

        if (!$this->isVerified()) {
            return 'تأیید نشده';
        }

        return 'فعال';
    }
} 