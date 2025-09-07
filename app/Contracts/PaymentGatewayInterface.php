<?php

namespace App\Contracts;

use App\Models\GatewayTransaction;

interface PaymentGatewayInterface
{
    /**
     * Initialize payment and get payment URL
     */
    public function createPayment(GatewayTransaction $transaction): array;

    /**
     * Verify payment from gateway callback
     */
    public function verifyPayment(GatewayTransaction $transaction, array $callbackData): array;

    /**
     * Process refund
     */
    public function refund(GatewayTransaction $transaction, int $amount = null): array;

    /**
     * Check payment status
     */
    public function getPaymentStatus(GatewayTransaction $transaction): array;

    /**
     * Get gateway configuration requirements
     */
    public function getConfigRequirements(): array;

    /**
     * Validate gateway configuration
     */
    public function validateConfig(array $config): bool;

    /**
     * Get supported currencies
     */
    public function getSupportedCurrencies(): array;

    /**
     * Get minimum/maximum amounts
     */
    public function getAmountLimits(): array;

    /**
     * Generate gateway-specific callback URL
     */
    public function getCallbackUrl(): string;

    /**
     * Prepare webhook signature validation
     */
    public function validateWebhookSignature(array $payload, string $signature): bool;
} 