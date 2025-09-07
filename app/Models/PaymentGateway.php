<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentGateway extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'driver',
        'description',
        'is_active',
        'is_default',
        'config',
        'supported_currencies',
        'fee_percentage',
        'fee_fixed',
        'min_amount',
        'max_amount',
        'logo_url',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'config' => 'array',
        'supported_currencies' => 'array',
        'fee_percentage' => 'decimal:2',
        'fee_fixed' => 'integer',
        'min_amount' => 'integer',
        'max_amount' => 'integer',
        'sort_order' => 'integer',
    ];

    // Relationships
    public function gatewayTransactions(): HasMany
    {
        return $this->hasMany(GatewayTransaction::class);
    }

    public function paymentMethods(): HasMany
    {
        return $this->hasMany(PaymentMethod::class);
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

    public function scopeForCurrency($query, string $currencyCode)
    {
        return $query->whereJsonContains('supported_currencies', $currencyCode);
    }

    // Methods
    public static function getDefault(): ?self
    {
        return static::active()->default()->first();
    }

    public static function getActiveGateways()
    {
        return static::active()->orderBy('sort_order')->get();
    }

    public function supportsAmount(int $amount): bool
    {
        if ($amount < $this->min_amount) {
            return false;
        }

        if ($this->max_amount && $amount > $this->max_amount) {
            return false;
        }

        return true;
    }

    public function supportsCurrency(string $currencyCode): bool
    {
        if (empty($this->supported_currencies)) {
            return true; // Supports all currencies if none specified
        }

        return in_array($currencyCode, $this->supported_currencies);
    }

    public function calculateFee(int $amount): int
    {
        $percentageFee = intval($amount * $this->fee_percentage / 100);
        return $percentageFee + $this->fee_fixed;
    }

    public function getGatewayInstance()
    {
        if (!class_exists($this->driver)) {
            throw new \Exception("Gateway driver {$this->driver} not found");
        }

        return new $this->driver($this);
    }

    public function getConfigValue(string $key, $default = null)
    {
        return data_get($this->config, $key, $default);
    }

    public function isSandbox(): bool
    {
        return $this->getConfigValue('sandbox', false);
    }
} 