<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'rate',
        'is_active',
        'is_default',
        'applicable_currencies',
        'min_amount',
        'max_amount',
        'description',
        'sort_order',
    ];

    protected $casts = [
        'rate' => 'decimal:4',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'applicable_currencies' => 'array',
        'min_amount' => 'integer',
        'max_amount' => 'integer',
        'sort_order' => 'integer',
    ];

    // Constants
    const TYPE_PERCENTAGE = 'percentage';
    const TYPE_FIXED = 'fixed';

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
        return $query->where(function ($q) use ($currencyCode) {
            $q->whereNull('applicable_currencies')
              ->orWhereJsonContains('applicable_currencies', $currencyCode);
        });
    }

    public function scopeForAmount($query, int $amount)
    {
        return $query->where('min_amount', '<=', $amount)
                    ->where(function ($q) use ($amount) {
                        $q->whereNull('max_amount')
                          ->orWhere('max_amount', '>=', $amount);
                    });
    }

    // Methods
    public static function getActiveRules()
    {
        return static::active()->orderBy('sort_order')->get();
    }

    public static function getDefaultRule(): ?self
    {
        return static::active()->default()->first();
    }

    public static function getApplicableRules(int $amount, string $currencyCode)
    {
        return static::active()
                    ->forAmount($amount)
                    ->forCurrency($currencyCode)
                    ->orderBy('sort_order')
                    ->get();
    }

    public function isApplicableFor(int $amount, string $currencyCode): bool
    {
        // Check amount range
        if ($amount < $this->min_amount) {
            return false;
        }

        if ($this->max_amount && $amount > $this->max_amount) {
            return false;
        }

        // Check currency
        if (!empty($this->applicable_currencies)) {
            return in_array($currencyCode, $this->applicable_currencies);
        }

        return true;
    }

    public function calculateTax(int $amount): int
    {
        if ($this->type === self::TYPE_PERCENTAGE) {
            return intval($amount * $this->rate / 100);
        }

        if ($this->type === self::TYPE_FIXED) {
            return intval($this->rate);
        }

        return 0;
    }

    public function getFormattedRate(): string
    {
        if ($this->type === self::TYPE_PERCENTAGE) {
            return $this->rate . '%';
        }

        return number_format($this->rate);
    }
} 