<?php

namespace App\Services;

use App\Models\GatewayTransaction;
use App\Models\PaymentGateway;
use App\Models\Currency;
use App\Models\User;

/**
 * Service for creating different types of SEP payments
 */
class SepPaymentTypes
{
    protected PaymentGateway $gateway;

    public function __construct()
    {
        $this->gateway = PaymentGateway::where('slug', 'sep')->firstOrFail();
    }

    /**
     * Create a standard purchase transaction
     */
    public function createPurchase(
        int $amount,
        string $description,
        ?User $user = null,
        array $metadata = []
    ): GatewayTransaction {
        $currency = Currency::where('code', 'IRT')->firstOrFail();

        return GatewayTransaction::create([
            'user_id' => $user?->id,
            'payment_gateway_id' => $this->gateway->id,
            'currency_id' => $currency->id,
            'amount' => $amount,
            'tax_amount' => 0,
            'gateway_fee' => 0,
            'total_amount' => $amount,
            'type' => 'payment',
            'status' => 'pending',
            'description' => $description,
            'reference_id' => $this->generateReferenceId('PURCHASE'),
            'metadata' => array_merge([
                'transaction_type' => 'purchase',
                'payment_method' => 'standard',
            ], $metadata),
            'user_ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Create a purchase with wage/fee included
     */
    public function createPurchaseWithWage(
        int $amount,
        int $wage,
        string $description,
        ?User $user = null,
        array $metadata = []
    ): GatewayTransaction {
        $currency = Currency::where('code', 'IRT')->firstOrFail();

        return GatewayTransaction::create([
            'user_id' => $user?->id,
            'payment_gateway_id' => $this->gateway->id,
            'currency_id' => $currency->id,
            'amount' => $amount,
            'tax_amount' => 0,
            'gateway_fee' => $wage,
            'total_amount' => $amount + $wage,
            'type' => 'payment',
            'status' => 'pending',
            'description' => $description,
            'reference_id' => $this->generateReferenceId('WAGE'),
            'metadata' => array_merge([
                'transaction_type' => 'purchase_with_wage',
                'payment_method' => 'standard',
                'wage_amount' => $wage,
            ], $metadata),
            'user_ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Create a purchase with restricted card numbers (hashed)
     */
    public function createRestrictedCardPurchase(
        int $amount,
        array $allowedCardNumbers,
        string $description,
        ?User $user = null,
        array $metadata = []
    ): GatewayTransaction {
        // Validate card numbers
        foreach ($allowedCardNumbers as $cardNumber) {
            if (!preg_match('/^\d{16}$/', $cardNumber)) {
                throw new \InvalidArgumentException('Card numbers must be 16 digits');
            }
        }

        // Hash card numbers using MD5 as specified in SEP documentation
        $hashedCards = array_map(function($cardNumber) {
            return md5($cardNumber);
        }, $allowedCardNumbers);

        $currency = Currency::where('code', 'IRT')->firstOrFail();

        return GatewayTransaction::create([
            'user_id' => $user?->id,
            'payment_gateway_id' => $this->gateway->id,
            'currency_id' => $currency->id,
            'amount' => $amount,
            'tax_amount' => 0,
            'gateway_fee' => 0,
            'total_amount' => $amount,
            'type' => 'payment',
            'status' => 'pending',
            'description' => $description,
            'reference_id' => $this->generateReferenceId('RESTRICTED'),
            'metadata' => array_merge([
                'transaction_type' => 'restricted_card_purchase',
                'payment_method' => 'restricted_cards',
                'hashed_card_numbers' => $hashedCards,
                'allowed_cards_count' => count($hashedCards),
            ], $metadata),
            'user_ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Create a purchase with mobile number integration
     */
    public function createMobilePurchase(
        int $amount,
        string $mobileNumber,
        string $description,
        ?User $user = null,
        array $metadata = []
    ): GatewayTransaction {
        // Validate mobile number
        if (!preg_match('/^09\d{9}$/', $mobileNumber)) {
            throw new \InvalidArgumentException('Mobile number must be in format 09xxxxxxxxx');
        }

        $currency = Currency::where('code', 'IRT')->firstOrFail();

        return GatewayTransaction::create([
            'user_id' => $user?->id,
            'payment_gateway_id' => $this->gateway->id,
            'currency_id' => $currency->id,
            'amount' => $amount,
            'tax_amount' => 0,
            'gateway_fee' => 0,
            'total_amount' => $amount,
            'type' => 'payment',
            'status' => 'pending',
            'description' => $description,
            'reference_id' => $this->generateReferenceId('MOBILE'),
            'metadata' => array_merge([
                'transaction_type' => 'mobile_purchase',
                'payment_method' => 'mobile_integration',
                'mobile_number' => $mobileNumber,
            ], $metadata),
            'user_ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Create a purchase with custom token expiry
     */
    public function createCustomExpiryPurchase(
        int $amount,
        int $tokenExpiryMinutes,
        string $description,
        ?User $user = null,
        array $metadata = []
    ): GatewayTransaction {
        // Validate token expiry (20-3600 minutes as per SEP documentation)
        if ($tokenExpiryMinutes < 20 || $tokenExpiryMinutes > 3600) {
            throw new \InvalidArgumentException('Token expiry must be between 20 and 3600 minutes');
        }

        $currency = Currency::where('code', 'IRT')->firstOrFail();

        return GatewayTransaction::create([
            'user_id' => $user?->id,
            'payment_gateway_id' => $this->gateway->id,
            'currency_id' => $currency->id,
            'amount' => $amount,
            'tax_amount' => 0,
            'gateway_fee' => 0,
            'total_amount' => $amount,
            'type' => 'payment',
            'status' => 'pending',
            'description' => $description,
            'reference_id' => $this->generateReferenceId('CUSTOM'),
            'metadata' => array_merge([
                'transaction_type' => 'custom_expiry_purchase',
                'payment_method' => 'standard',
                'token_expiry_minutes' => $tokenExpiryMinutes,
            ], $metadata),
            'user_ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Create a wallet charge transaction
     */
    public function createWalletCharge(
        int $amount,
        ?User $user = null,
        array $metadata = []
    ): GatewayTransaction {
        if (!$user) {
            throw new \InvalidArgumentException('User is required for wallet charge transactions');
        }

        $currency = Currency::where('code', 'IRT')->firstOrFail();

        return GatewayTransaction::create([
            'user_id' => $user->id,
            'payment_gateway_id' => $this->gateway->id,
            'currency_id' => $currency->id,
            'amount' => $amount,
            'tax_amount' => 0,
            'gateway_fee' => 0,
            'total_amount' => $amount,
            'type' => 'wallet_charge',
            'status' => 'pending',
            'description' => 'شارژ کیف پول',
            'reference_id' => $this->generateReferenceId('WALLET'),
            'metadata' => array_merge([
                'transaction_type' => 'wallet_charge',
                'payment_method' => 'standard',
            ], $metadata),
            'user_ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Create a service payment transaction
     */
    public function createServicePayment(
        int $amount,
        string $serviceId,
        string $serviceName,
        ?User $user = null,
        array $serviceData = [],
        array $metadata = []
    ): GatewayTransaction {
        $currency = Currency::where('code', 'IRT')->firstOrFail();

        return GatewayTransaction::create([
            'user_id' => $user?->id,
            'payment_gateway_id' => $this->gateway->id,
            'currency_id' => $currency->id,
            'amount' => $amount,
            'tax_amount' => 0,
            'gateway_fee' => 0,
            'total_amount' => $amount,
            'type' => 'service_payment',
            'status' => 'pending',
            'description' => "پرداخت سرویس: {$serviceName}",
            'reference_id' => $this->generateReferenceId('SERVICE'),
            'metadata' => array_merge([
                'transaction_type' => 'service_payment',
                'payment_method' => 'standard',
                'service_id' => $serviceId,
                'service_name' => $serviceName,
                'service_title' => $serviceName, // Also store as service_title for consistency
                'service_data' => $serviceData,
                'type' => 'service_payment',
            ], $metadata),
            'user_ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Add guest payment hash to transaction metadata
     */
    public function addGuestPaymentHash(GatewayTransaction $transaction, string $guestHash): void
    {
        $metadata = $transaction->metadata ?? [];
        $metadata['guest_payment_hash'] = $guestHash;
        $transaction->update(['metadata' => $metadata]);
    }

    /**
     * Add custom reference number to transaction
     */
    public function setCustomReferenceId(GatewayTransaction $transaction, string $referenceId): void
    {
        // Validate reference ID format (SEP allows up to 50 characters)
        if (strlen($referenceId) > 50) {
            throw new \InvalidArgumentException('Reference ID cannot exceed 50 characters');
        }

        $transaction->update(['reference_id' => $referenceId]);
    }

    /**
     * Generate a unique reference ID
     */
    protected function generateReferenceId(string $prefix): string
    {
        $timestamp = now()->format('YmdHis');
        $random = substr(uniqid(), -6);
        return "{$prefix}_{$timestamp}_{$random}";
    }

    /**
     * Get gateway instance
     */
    public function getGateway(): PaymentGateway
    {
        return $this->gateway;
    }

    /**
     * Validate amount according to SEP limits
     */
    public function validateAmount(int $amount): bool
    {
        return $amount >= 1000 && $amount <= 500000000;
    }

    /**
     * Calculate total amount with potential fees
     */
    public function calculateTotalAmount(int $amount, int $wage = 0): int
    {
        return $amount + $wage;
    }

    /**
     * Get supported payment methods
     */
    public function getSupportedPaymentMethods(): array
    {
        return [
            'standard' => 'پرداخت استاندارد',
            'mobile_integration' => 'پرداخت با شماره موبایل',
            'restricted_cards' => 'پرداخت با کارت‌های محدود',
            'custom_expiry' => 'پرداخت با انقضای سفارشی',
        ];
    }

    /**
     * Get transaction type descriptions
     */
    public function getTransactionTypes(): array
    {
        return [
            'purchase' => 'خرید استاندارد',
            'purchase_with_wage' => 'خرید با کارمزد',
            'restricted_card_purchase' => 'خرید با کارت محدود',
            'mobile_purchase' => 'خرید با موبایل',
            'custom_expiry_purchase' => 'خرید با انقضای سفارشی',
            'wallet_charge' => 'شارژ کیف پول',
            'service_payment' => 'پرداخت سرویس',
        ];
    }
} 