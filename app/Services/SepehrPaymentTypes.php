<?php

namespace App\Services;

use App\Models\GatewayTransaction;
use App\Models\PaymentGateway;
use App\Models\Currency;
use App\Models\User;

/**
 * Service for creating different types of Sepehr payments
 */
class SepehrPaymentTypes
{
    protected PaymentGateway $gateway;

    public function __construct()
    {
        $this->gateway = PaymentGateway::where('slug', 'sepehr')->firstOrFail();
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
            ], $metadata),
            'user_ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Create an identified purchase transaction (with national ID)
     */
    public function createIdentifiedPurchase(
        int $amount,
        string $description,
        string $nationalCode,
        ?User $user = null,
        array $metadata = []
    ): GatewayTransaction {
        // Validate national code
        if (!preg_match('/^\d{10}$/', $nationalCode)) {
            throw new \InvalidArgumentException('National code must be 10 digits');
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
            'reference_id' => $this->generateReferenceId('IDENTIFIED'),
            'metadata' => array_merge([
                'transaction_type' => 'identified_purchase',
                'national_code' => $nationalCode,
            ], $metadata),
            'user_ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Create a bill payment transaction
     */
    public function createBillPayment(
        string $billId,
        string $payId,
        int $amount,
        ?User $user = null,
        array $metadata = []
    ): GatewayTransaction {
        // Validate bill ID (13 or 18 digits)
        if (!preg_match('/^\d{13}$|^\d{18}$/', $billId)) {
            throw new \InvalidArgumentException('Bill ID must be 13 or 18 digits');
        }

        // Validate pay ID
        if (!preg_match('/^\d+$/', $payId)) {
            throw new \InvalidArgumentException('Pay ID must contain only digits');
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
            'description' => "پرداخت قبض {$billId}",
            'reference_id' => $this->generateReferenceId('BILL'),
            'metadata' => array_merge([
                'transaction_type' => 'bill_payment',
                'bill_id' => $billId,
                'pay_id' => $payId,
            ], $metadata),
            'user_ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Create a batch bill payment transaction
     */
    public function createBatchBillPayment(
        array $bills,
        int $totalAmount,
        ?User $user = null,
        array $metadata = []
    ): GatewayTransaction {
        // Validate bills array (max 10 bills)
        if (count($bills) > 10) {
            throw new \InvalidArgumentException('Maximum 10 bills allowed in batch payment');
        }

        foreach ($bills as $bill) {
            if (!isset($bill['BillID']) || !isset($bill['PayID'])) {
                throw new \InvalidArgumentException('Each bill must have BillID and PayID');
            }

            if (!preg_match('/^\d{13}$|^\d{18}$/', $bill['BillID'])) {
                throw new \InvalidArgumentException('Bill ID must be 13 or 18 digits');
            }

            if (!preg_match('/^\d+$/', $bill['PayID'])) {
                throw new \InvalidArgumentException('Pay ID must contain only digits');
            }
        }

        $currency = Currency::where('code', 'IRT')->firstOrFail();
        $billCount = count($bills);

        return GatewayTransaction::create([
            'user_id' => $user?->id,
            'payment_gateway_id' => $this->gateway->id,
            'currency_id' => $currency->id,
            'amount' => $totalAmount,
            'tax_amount' => 0,
            'gateway_fee' => 0,
            'total_amount' => $totalAmount,
            'type' => 'payment',
            'status' => 'pending',
            'description' => "پرداخت گروهی {$billCount} قبض",
            'reference_id' => $this->generateReferenceId('BATCH_BILL'),
            'metadata' => array_merge([
                'transaction_type' => 'batch_bill_payment',
                'bills' => $bills,
                'bill_count' => $billCount,
            ], $metadata),
            'user_ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Create a mobile top-up transaction
     */
    public function createMobileTopup(
        string $mobileNumber,
        int $amount,
        int $operator = 0,
        int $chargeType = 0,
        int $requestType = 0,
        int $dataplanId = 0,
        string $additionalData = '',
        ?User $user = null,
        array $metadata = []
    ): GatewayTransaction {
        // Validate mobile number
        if (!preg_match('/^09\d{9}$/', $mobileNumber)) {
            throw new \InvalidArgumentException('Mobile number must be in format 09xxxxxxxxx');
        }

        // Validate operator (0=MTN, 1=MCI, 2=Rightel)
        if (!in_array($operator, [0, 1, 2])) {
            throw new \InvalidArgumentException('Operator must be 0 (MTN), 1 (MCI), or 2 (Rightel)');
        }

        // Validate charge type (0=Normal, 1=MTN Wow, 2=Rightel Wow)
        if (!in_array($chargeType, [0, 1, 2])) {
            throw new \InvalidArgumentException('Charge type must be 0 (Normal), 1 (MTN Wow), or 2 (Rightel Wow)');
        }

        // Validate request type (0=Top-up, 1=Voucher, 2=Data plan)
        if (!in_array($requestType, [0, 1, 2])) {
            throw new \InvalidArgumentException('Request type must be 0 (Top-up), 1 (Voucher), or 2 (Data plan)');
        }

        $currency = Currency::where('code', 'IRT')->firstOrFail();
        $operatorNames = ['ایرانسل', 'همراه اول', 'رایتل'];

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
            'description' => "شارژ {$operatorNames[$operator]} - {$mobileNumber}",
            'reference_id' => $this->generateReferenceId('TOPUP'),
            'metadata' => array_merge([
                'transaction_type' => 'mobile_topup',
                'mobile_data' => [
                    'mobile' => $mobileNumber,
                    'operator' => $operator,
                    'chargeType' => $chargeType,
                    'requestType' => $requestType,
                    'dataplanId' => $dataplanId,
                    'AdditionalData' => $additionalData,
                ],
                'operator_name' => $operatorNames[$operator],
            ], $metadata),
            'user_ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Create a fund splitting transaction
     */
    public function createFundSplitting(
        int $totalAmount,
        array $splitAccounts,
        string $splitId = '0001',
        string $description = 'پرداخت تسهیم',
        ?User $user = null,
        array $metadata = []
    ): GatewayTransaction {
        // Validate split accounts (max 9 accounts)
        if (count($splitAccounts) > 9) {
            throw new \InvalidArgumentException('Maximum 9 accounts allowed for fund splitting');
        }

        $totalSplitAmount = 0;
        foreach ($splitAccounts as $account) {
            if (!isset($account['Account']) && !isset($account['Iban'])) {
                throw new \InvalidArgumentException('Each split account must have Account or Iban');
            }

            if (!isset($account['Amount'])) {
                throw new \InvalidArgumentException('Each split account must have Amount');
            }

            $totalSplitAmount += (int) $account['Amount'];
        }

        // Verify total split amount matches transaction amount
        if ($totalSplitAmount !== $totalAmount) {
            throw new \InvalidArgumentException('Total split amount must equal transaction amount');
        }

        $currency = Currency::where('code', 'IRT')->firstOrFail();

        return GatewayTransaction::create([
            'user_id' => $user?->id,
            'payment_gateway_id' => $this->gateway->id,
            'currency_id' => $currency->id,
            'amount' => $totalAmount,
            'tax_amount' => 0,
            'gateway_fee' => 0,
            'total_amount' => $totalAmount,
            'type' => 'payment',
            'status' => 'pending',
            'description' => $description,
            'reference_id' => $this->generateReferenceId('SPLIT'),
            'metadata' => array_merge([
                'transaction_type' => 'fund_splitting',
                'split_accounts' => $splitAccounts,
                'split_id' => $splitId,
                'account_count' => count($splitAccounts),
            ], $metadata),
            'user_ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Create a purchase with mobile number integration
     */
    public function createPurchaseWithMobile(
        int $amount,
        string $description,
        string $mobileNumber,
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
            'reference_id' => $this->generateReferenceId('MPURCHASE'),
            'metadata' => array_merge([
                'transaction_type' => 'purchase_with_mobile',
                'mobile_number' => $mobileNumber,
            ], $metadata),
            'user_ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Add guest payment hash to any transaction
     */
    public function addGuestPaymentHash(GatewayTransaction $transaction, string $guestPaymentHash): GatewayTransaction
    {
        $metadata = $transaction->metadata ?? [];
        $metadata['guest_payment_hash'] = $guestPaymentHash;

        $transaction->update(['metadata' => $metadata]);

        return $transaction;
    }

    /**
     * Generate reference ID with prefix
     */
    protected function generateReferenceId(string $prefix): string
    {
        $timestamp = now()->format('YmdHis');
        $random = mt_rand(1000, 9999);
        return "{$prefix}_{$timestamp}_{$random}";
    }

    /**
     * Get available operators for mobile top-up
     */
    public static function getOperators(): array
    {
        return [
            0 => 'ایرانسل (MTN)',
            1 => 'همراه اول (MCI)',
            2 => 'رایتل (Rightel)',
        ];
    }

    /**
     * Get available charge types
     */
    public static function getChargeTypes(): array
    {
        return [
            0 => 'عادی',
            1 => 'ایرانسل وو',
            2 => 'رایتل وو',
        ];
    }

    /**
     * Get available request types
     */
    public static function getRequestTypes(): array
    {
        return [
            0 => 'شارژ مستقیم',
            1 => 'رمز شارژ',
            2 => 'بسته اینترنتی',
        ];
    }

    /**
     * Validate bill ID format
     */
    public static function validateBillId(string $billId): bool
    {
        return preg_match('/^\d{13}$|^\d{18}$/', $billId) === 1;
    }

    /**
     * Validate mobile number format
     */
    public static function validateMobileNumber(string $mobile): bool
    {
        return preg_match('/^09\d{9}$/', $mobile) === 1;
    }

    /**
     * Validate national code format
     */
    public static function validateNationalCode(string $nationalCode): bool
    {
        return preg_match('/^\d{10}$/', $nationalCode) === 1;
    }
} 