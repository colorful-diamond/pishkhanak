<?php

namespace App\Services;

use App\Models\Currency;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CurrencyService
{
    /**
     * Get base currency
     */
    public function getBaseCurrency(): Currency
    {
        return Cache::remember('base_currency', 3600, function () {
            return Currency::getBaseCurrency();
        });
    }

    /**
     * Get all active currencies
     */
    public function getActiveCurrencies(): Collection
    {
        return Cache::remember('active_currencies', 3600, function () {
            return Currency::getActiveCurrencies();
        });
    }

    /**
     * Get currency by code
     */
    public function getCurrency(string $code): ?Currency
    {
        return Cache::remember("currency_{$code}", 3600, function () use ($code) {
            return Currency::where('code', $code)->active()->first();
        });
    }

    /**
     * Convert amount between currencies
     */
    public function convert(int $amount, string $fromCurrency, string $toCurrency): int
    {
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }

        $fromCurrencyModel = $this->getCurrency($fromCurrency);
        $toCurrencyModel = $this->getCurrency($toCurrency);

        if (!$fromCurrencyModel || !$toCurrencyModel) {
            throw new \InvalidArgumentException('Invalid currency codes provided');
        }

        // Convert to base currency first
        $baseAmount = $fromCurrencyModel->convertToBase($amount);
        
        // Then convert to target currency
        return $toCurrencyModel->convertFromBase($baseAmount);
    }

    /**
     * Format amount with currency symbol
     */
    public function formatAmount(int $amount, string $currencyCode): string
    {
        $currency = $this->getCurrency($currencyCode);
        
        if (!$currency) {
            return (string) $amount;
        }

        return $currency->formatAmount($amount);
    }

    /**
     * Get exchange rate between two currencies
     */
    public function getExchangeRate(string $fromCurrency, string $toCurrency): float
    {
        if ($fromCurrency === $toCurrency) {
            return 1.0;
        }

        $fromCurrencyModel = $this->getCurrency($fromCurrency);
        $toCurrencyModel = $this->getCurrency($toCurrency);

        if (!$fromCurrencyModel || !$toCurrencyModel) {
            throw new \InvalidArgumentException('Invalid currency codes provided');
        }

        // Calculate rate through base currency
        return $toCurrencyModel->exchange_rate / $fromCurrencyModel->exchange_rate;
    }

    /**
     * Update exchange rates from external API
     */
    public function updateExchangeRates(): array
    {
        try {
            $baseCurrency = $this->getBaseCurrency();
            $currencies = $this->getActiveCurrencies()->where('code', '!=', $baseCurrency->code);
            
            $updated = [];
            $errors = [];

            foreach ($currencies as $currency) {
                try {
                    $newRate = $this->fetchExchangeRate($baseCurrency->code, $currency->code);
                    
                    if ($newRate && $newRate > 0) {
                        $currency->update(['exchange_rate' => $newRate]);
                        $updated[] = $currency->code;
                        
                        Log::info('Exchange rate updated', [
                            'currency' => $currency->code,
                            'old_rate' => $currency->exchange_rate,
                            'new_rate' => $newRate,
                        ]);
                    }
                } catch (\Exception $e) {
                    $errors[] = [
                        'currency' => $currency->code,
                        'error' => $e->getMessage(),
                    ];
                    
                    Log::warning('Failed to update exchange rate', [
                        'currency' => $currency->code,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // Clear currency cache after updates
            $this->clearCurrencyCache();

            return [
                'success' => true,
                'updated' => $updated,
                'errors' => $errors,
                'total_updated' => count($updated),
                'total_errors' => count($errors),
            ];

        } catch (\Exception $e) {
            Log::error('Exchange rate update failed', ['error' => $e->getMessage()]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'updated' => [],
                'errors' => [],
            ];
        }
    }

    /**
     * Fetch exchange rate from external API
     */
    protected function fetchExchangeRate(string $baseCurrency, string $targetCurrency): ?float
    {
        // Example using a free exchange rate API
        // You should replace this with your preferred exchange rate provider
        try {
            $response = Http::timeout(10)->get("https://api.exchangerate-api.com/v4/latest/{$baseCurrency}");
            
            if ($response->successful()) {
                $data = $response->json();
                return $data['rates'][$targetCurrency] ?? null;
            }
        } catch (\Exception $e) {
            // Try alternative API
            try {
                $response = Http::timeout(10)->get("https://api.fixer.io/latest", [
                    'base' => $baseCurrency,
                    'symbols' => $targetCurrency,
                ]);
                
                if ($response->successful()) {
                    $data = $response->json();
                    return $data['rates'][$targetCurrency] ?? null;
                }
            } catch (\Exception $e2) {
                Log::warning('All exchange rate APIs failed', [
                    'base' => $baseCurrency,
                    'target' => $targetCurrency,
                    'error1' => $e->getMessage(),
                    'error2' => $e2->getMessage(),
                ]);
            }
        }

        return null;
    }

    /**
     * Get currency conversion rates for display
     */
    public function getConversionRates(string $baseCurrency = null): array
    {
        $base = $baseCurrency ? $this->getCurrency($baseCurrency) : $this->getBaseCurrency();
        $currencies = $this->getActiveCurrencies()->where('code', '!=', $base->code);
        
        $rates = [];
        
        foreach ($currencies as $currency) {
            $rates[$currency->code] = [
                'name' => $currency->name,
                'symbol' => $currency->symbol,
                'rate' => $this->getExchangeRate($base->code, $currency->code),
                'last_updated' => $currency->updated_at,
            ];
        }
        
        return $rates;
    }

    /**
     * Validate currency code
     */
    public function isValidCurrency(string $code): bool
    {
        return $this->getCurrency($code) !== null;
    }

    /**
     * Get smallest unit for currency (e.g., cents for USD, tomans for IRT)
     */
    public function getSmallestUnit(string $currencyCode): int
    {
        $currency = $this->getCurrency($currencyCode);
        return $currency ? $currency->getSmallestUnit() : 1;
    }

    /**
     * Convert user-friendly amount to smallest unit
     */
    public function toSmallestUnit(float $amount, string $currencyCode): int
    {
        $multiplier = $this->getSmallestUnit($currencyCode);
        return intval($amount * $multiplier);
    }

    /**
     * Convert smallest unit to user-friendly amount
     */
    public function fromSmallestUnit(int $amount, string $currencyCode): float
    {
        $divisor = $this->getSmallestUnit($currencyCode);
        return $amount / $divisor;
    }

    /**
     * Get popular currency pairs
     */
    public function getPopularPairs(): array
    {
        return [
            'USD/IRT',
            'EUR/IRT', 
            'USD/EUR',
        ];
    }

    /**
     * Clear currency cache
     */
    public function clearCurrencyCache(): void
    {
        Cache::forget('base_currency');
        Cache::forget('active_currencies');
        
        // Clear individual currency caches
        $currencies = Currency::all();
        foreach ($currencies as $currency) {
            Cache::forget("currency_{$currency->code}");
        }
    }

    /**
     * Create new currency
     */
    public function createCurrency(array $data): Currency
    {
        // Validate currency data
        $this->validateCurrencyData($data);
        
        // If this is set as base currency, remove base flag from others
        if (!empty($data['is_base_currency'])) {
            Currency::where('is_base_currency', true)->update(['is_base_currency' => false]);
        }

        $currency = Currency::create($data);
        $this->clearCurrencyCache();
        
        return $currency;
    }

    /**
     * Update currency
     */
    public function updateCurrency(Currency $currency, array $data): Currency
    {
        $this->validateCurrencyData($data, $currency->id);
        
        // If this is set as base currency, remove base flag from others
        if (!empty($data['is_base_currency']) && !$currency->is_base_currency) {
            Currency::where('is_base_currency', true)->update(['is_base_currency' => false]);
        }

        $currency->update($data);
        $this->clearCurrencyCache();
        
        return $currency;
    }

    /**
     * Validate currency data
     */
    protected function validateCurrencyData(array $data, int $excludeId = null): void
    {
        // Check for duplicate currency codes
        if (!empty($data['code'])) {
            $query = Currency::where('code', $data['code']);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
            
            if ($query->exists()) {
                throw new \InvalidArgumentException("Currency code {$data['code']} already exists");
            }
        }

        // Validate exchange rate
        if (isset($data['exchange_rate']) && $data['exchange_rate'] <= 0) {
            throw new \InvalidArgumentException('Exchange rate must be positive');
        }

        // Validate decimal places
        if (isset($data['decimal_places']) && ($data['decimal_places'] < 0 || $data['decimal_places'] > 8)) {
            throw new \InvalidArgumentException('Decimal places must be between 0 and 8');
        }
    }

    /**
     * Get currency statistics
     */
    public function getCurrencyStatistics(): array
    {
        $currencies = $this->getActiveCurrencies();
        $baseCurrency = $this->getBaseCurrency();
        
        return [
            'total_currencies' => $currencies->count(),
            'base_currency' => $baseCurrency->code,
            'currencies_with_decimals' => $currencies->where('decimal_places', '>', 0)->count(),
            'currencies_without_decimals' => $currencies->where('decimal_places', 0)->count(),
            'last_rate_update' => $currencies->max('updated_at'),
            'currency_list' => $currencies->pluck('name', 'code')->toArray(),
        ];
    }
} 