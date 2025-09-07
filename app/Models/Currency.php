<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'symbol',
        'exchange_rate',
        'is_base_currency',
        'is_active',
        'decimal_places',
        'position',
    ];

    protected $casts = [
        'exchange_rate' => 'decimal:8',
        'is_base_currency' => 'boolean',
        'is_active' => 'boolean',
        'decimal_places' => 'integer',
    ];

    // Relationships
    public function gatewayTransactions(): HasMany
    {
        return $this->hasMany(GatewayTransaction::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBaseCurrency($query)
    {
        return $query->where('is_base_currency', true);
    }

    // Methods
    public static function getBaseCurrency(): ?self
    {
        return static::baseCurrency()->first();
    }

    public static function getActiveCurrencies()
    {
        return static::active()->orderBy('code')->get();
    }

    public function formatAmount(int $amount): string
    {
        $value = $this->decimal_places > 0 
            ? number_format($amount / pow(10, $this->decimal_places), $this->decimal_places)
            : number_format($amount);
        
        return $this->position === 'before' 
            ? $this->symbol . $value 
            : $value . ' ' . $this->symbol;
    }

    public function convertFromBase(int $baseAmount): int
    {
        return intval($baseAmount * $this->exchange_rate);
    }

    public function convertToBase(int $amount): int
    {
        return intval($amount / $this->exchange_rate);
    }

    public function getSmallestUnit(): int
    {
        return intval(pow(10, $this->decimal_places));
    }
} 