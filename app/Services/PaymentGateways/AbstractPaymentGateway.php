<?php

namespace App\Services\PaymentGateways;

use App\Contracts\PaymentGatewayInterface;
use App\Models\PaymentGateway;
use App\Models\GatewayTransaction;
use App\Models\GatewayTransactionLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

abstract class AbstractPaymentGateway implements PaymentGatewayInterface
{
    protected PaymentGateway $gateway;
    protected array $config;

    public function __construct(PaymentGateway $gateway)
    {
        $this->gateway = $gateway;
        $this->config = $gateway->config;
    }

    /**
     * Get gateway configuration value
     */
    protected function getConfig(string $key, $default = null)
    {
        return data_get($this->config, $key, $default);
    }

    /**
     * Check if gateway is in sandbox mode
     */
    protected function isSandbox(): bool
    {
        return $this->getConfig('sandbox', false);
    }

    /**
     * Get base API URL
     */
    abstract protected function getApiUrl(): string;

    /**
     * Get sandbox API URL
     */
    abstract protected function getSandboxApiUrl(): string;

    /**
     * Get current API URL based on environment
     */
    protected function getCurrentApiUrl(): string
    {
        return $this->isSandbox() ? $this->getSandboxApiUrl() : $this->getApiUrl();
    }

    /**
     * Make HTTP request with logging
     */
    protected function makeRequest(string $method, string $url, array $data = [], array $headers = []): array
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage(true);

        try {
            // Check if Content-Type is application/json to send as JSON
            $isJsonRequest = false;
            foreach ($headers as $key => $value) {
                if (strtolower($key) === 'content-type' && strpos(strtolower($value), 'application/json') !== false) {
                    $isJsonRequest = true;
                    break;
                }
            }

            if ($isJsonRequest) {
                $response = Http::withHeaders($headers)->send($method, $url, ['json' => $data]);
            } else {
                $response = Http::withHeaders($headers)->$method($url, $data);
            }

            $responseTime = intval((microtime(true) - $startTime) * 1000);
            $memoryUsage = intval((memory_get_usage(true) - $startMemory) / 1024 / 1024);

            // DEBUG: Check response content type and body
            $contentType = $response->header('Content-Type');
            $responseBody = $response->body();
            
            // Try to parse JSON response
            $jsonData = null;
            try {
                $jsonData = $response->json();
            } catch (\Exception $e) {
                Log::warning('Failed to parse JSON response', [
                    'gateway' => $this->gateway->slug,
                    'error' => $e->getMessage(),
                ]);
            }

            $result = [
                'success' => $response->successful(),
                'status_code' => $response->status(),
                'data' => $jsonData,
                'raw_response' => $responseBody,
                'response_time_ms' => $responseTime,
                'memory_usage_mb' => $memoryUsage,
            ];

            Log::info('Payment Gateway Request', [
                'gateway' => $this->gateway->slug,
                'method' => $method,
                'url' => $url,
                'response_time_ms' => $responseTime,
                'status_code' => $response->status(),
                'success' => $response->successful(),
            ]);

            return $result;

        } catch (\Exception $e) {
            $responseTime = intval((microtime(true) - $startTime) * 1000);
            $memoryUsage = intval((memory_get_usage(true) - $startMemory) / 1024 / 1024);

            Log::error('Payment Gateway Request Failed', [
                'gateway' => $this->gateway->slug,
                'method' => $method,
                'url' => $url,
                'error' => $e->getMessage(),
                'response_time_ms' => $responseTime,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'response_time_ms' => $responseTime,
                'memory_usage_mb' => $memoryUsage,
            ];
        }
    }

    /**
     * Log transaction activity
     */
    protected function logTransaction(
        GatewayTransaction $transaction, 
        string $action, 
        array $data = [], 
        string $source = GatewayTransactionLog::SOURCE_SYSTEM
    ): GatewayTransactionLog {
        return $transaction->addLog($action, $source, $data);
    }

    /**
     * Generate unique reference ID
     */
    protected function generateReference(): string
    {
        return uniqid($this->gateway->slug . '_', true);
    }

    /**
     * Format amount for gateway (some gateways need specific formatting)
     */
    protected function formatAmount(int $amount): string
    {
        return (string) $amount;
    }

    /**
     * Standard success response
     */
    protected function successResponse(array $data = []): array
    {
        return array_merge([
            'success' => true,
            'gateway' => $this->gateway->slug,
        ], $data);
    }

    /**
     * Standard error response
     */
    protected function errorResponse(string $message, array $data = []): array
    {
        return array_merge([
            'success' => false,
            'gateway' => $this->gateway->slug,
            'message' => $message,
        ], $data);
    }

    /**
     * Default implementation for webhook signature validation
     */
    public function validateWebhookSignature(array $payload, string $signature): bool
    {
        // Override in specific gateway implementations
        return true;
    }

    /**
     * Default callback URL implementation
     */
    public function getCallbackUrl(): string
    {
        return route('payment.callback', ['gateway' => $this->gateway->slug]);
    }

    /**
     * Default configuration validation
     */
    public function validateConfig(array $config): bool
    {
        $requirements = $this->getConfigRequirements();
        
        foreach ($requirements as $field) {
            if (empty($config[$field])) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Get gateway name
     */
    public function getName(): string
    {
        return $this->gateway->name;
    }

    /**
     * Get gateway slug
     */
    public function getSlug(): string
    {
        return $this->gateway->slug;
    }
} 