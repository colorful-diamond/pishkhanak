<?php

namespace App\Services;

use App\Contracts\PaymentGatewayInterface;
use App\Models\PaymentGateway;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class PaymentGatewayManager
{
    /**
     * Get gateway instance by slug
     */
    public function gateway(string $slug): PaymentGatewayInterface
    {
        $gateway = PaymentGateway::where('slug', $slug)->where('is_active', true)->firstOrFail();
        
        return $this->createGatewayInstance($gateway);
    }

    /**
     * Get gateway instance by ID
     */
    public function gatewayById(int $id): PaymentGatewayInterface
    {
        $gateway = PaymentGateway::where('id', $id)->where('is_active', true)->firstOrFail();
        
        return $this->createGatewayInstance($gateway);
    }

    /**
     * Get gateway instance by slug (alias for gateway method)
     */
    public function gatewayBySlug(string $slug): ?PaymentGatewayInterface
    {
        $gateway = PaymentGateway::where('slug', $slug)->where('is_active', true)->first();
        
        if (!$gateway) {
            return null;
        }
        
        return $this->createGatewayInstance($gateway);
    }

    /**
     * Get default gateway
     */
    public function defaultGateway(): PaymentGatewayInterface
    {
        $gateway = PaymentGateway::active()->default()->firstOrFail();
        
        return $this->createGatewayInstance($gateway);
    }

    /**
     * Get all active gateways
     */
    public function activeGateways(): Collection
    {
        return PaymentGateway::active()->orderBy('sort_order')->get();
    }

    /**
     * Get gateways that support a specific currency
     */
    public function gatewaysForCurrency(string $currencyCode): Collection
    {
        return PaymentGateway::active()
            ->forCurrency($currencyCode)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Get gateways that support a specific amount
     */
    public function gatewaysForAmount(int $amount, string $currencyCode = null): Collection
    {
        $query = PaymentGateway::active();
        
        if ($currencyCode) {
            $query->forCurrency($currencyCode);
        }
        
        $allGateways = $query->get();
        
        $filteredGateways = $allGateways->filter(function ($gateway) use ($amount) {
            return $gateway->supportsAmount($amount);
        });
        
        return $filteredGateways;
    }

    /**
     * Find the best gateway for a transaction
     */
    public function findBestGateway(int $amount, string $currencyCode): ?PaymentGateway
    {
        $gateways = $this->gatewaysForAmount($amount, $currencyCode);
        
        if ($gateways->isEmpty()) {
            return null;
        }

        // Priority: default gateway > lowest fee > first by sort order
        $defaultGateway = $gateways->firstWhere('is_default', true);
        if ($defaultGateway) {
            return $defaultGateway;
        }

        // Find gateway with lowest total fee
        return $gateways->sortBy(function ($gateway) use ($amount) {
            return $gateway->calculateFee($amount);
        })->first();
    }

    /**
     * Create gateway instance from model
     */
    protected function createGatewayInstance(PaymentGateway $gateway): PaymentGatewayInterface
    {
        $driverClass = $gateway->driver;
        
        if (!class_exists($driverClass)) {
            throw new \Exception("Gateway driver class {$driverClass} not found");
        }

        if (!is_subclass_of($driverClass, PaymentGatewayInterface::class)) {
            throw new \Exception("Gateway driver {$driverClass} must implement PaymentGatewayInterface");
        }

        return new $driverClass($gateway);
    }

    /**
     * Validate gateway configuration
     */
    public function validateGatewayConfig(PaymentGateway $gateway): bool
    {
        try {
            $instance = $this->createGatewayInstance($gateway);
            return $instance->validateConfig($gateway->config);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get available gateway drivers
     */
    public function getAvailableDrivers(): array
    {
        return [
            'App\\Services\\PaymentGateways\\AsanpardakhtGateway' => [
                'name' => 'آسان پرداخت',
                'slug' => 'asanpardakht',
                'currencies' => ['IRT'],
                'description' => 'درگاه پرداخت آسان پرداخت',
            ],
            // Add more gateways here in the future
        ];
    }

    /**
     * Register a new gateway driver
     */
    public function registerDriver(string $driverClass, array $config): bool
    {
        if (!class_exists($driverClass)) {
            return false;
        }

        if (!is_subclass_of($driverClass, PaymentGatewayInterface::class)) {
            return false;
        }

        // You could store this in a config file or database
        // For now, it's handled through the database seeder
        return true;
    }

    /**
     * Test gateway connection
     */
    public function testGateway(PaymentGateway $gateway): array
    {
        try {
            $instance = $this->createGatewayInstance($gateway);
            
            // Perform basic validation checks
            $configValid = $instance->validateConfig($gateway->config);
            $currencies = $instance->getSupportedCurrencies();
            $limits = $instance->getAmountLimits();
            
            return [
                'success' => true,
                'config_valid' => $configValid,
                'supported_currencies' => $currencies,
                'amount_limits' => $limits,
                'callback_url' => $instance->getCallbackUrl(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get gateway statistics
     */
    public function getGatewayStats(PaymentGateway $gateway): array
    {
        $transactions = $gateway->transactions();
        
        return [
            'total_transactions' => $transactions->count(),
            'successful_transactions' => $transactions->successful()->count(),
            'failed_transactions' => $transactions->failed()->count(),
            'total_amount' => $transactions->successful()->sum('total_amount'),
            'average_amount' => $transactions->successful()->avg('total_amount'),
            'success_rate' => $transactions->count() > 0 
                ? round(($transactions->successful()->count() / $transactions->count()) * 100, 2)
                : 0,
        ];
    }

    /**
     * Calculate total fees for all gateways
     */
    public function calculateTotalFees(int $amount): array
    {
        $gateways = $this->activeGateways();
        $fees = [];
        
        foreach ($gateways as $gateway) {
            if ($gateway->supportsAmount($amount)) {
                $fees[$gateway->slug] = [
                    'name' => $gateway->name,
                    'fee' => $gateway->calculateFee($amount),
                    'total' => $amount + $gateway->calculateFee($amount),
                ];
            }
        }
        
        return $fees;
    }
} 