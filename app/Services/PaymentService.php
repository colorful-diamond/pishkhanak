<?php

namespace App\Services;

use App\Models\Currency;
use App\Models\PaymentGateway;
use App\Models\GatewayTransaction;
use App\Models\GatewayTransactionLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    protected PaymentGatewayManager $gatewayManager;
    protected TaxCalculationService $taxService;
    protected CurrencyService $currencyService;

    public function __construct(
        PaymentGatewayManager $gatewayManager,
        TaxCalculationService $taxService,
        CurrencyService $currencyService
    ) {
        $this->gatewayManager = $gatewayManager;
        $this->taxService = $taxService;
        $this->currencyService = $currencyService;
    }

    /**
     * Create a new payment transaction
     */
    public function createPayment(array $paymentData): array
    {
        try {
            DB::beginTransaction();

            // Validate payment data
            $validatedData = $this->validatePaymentData($paymentData);
            
            // 🔧 DUPLICATE PROTECTION: Check for existing pending transactions
            $existingTransaction = null;
            
            // Check for service request duplicates
            if (isset($validatedData['metadata']['service_request_hash'])) {
                $existingTransaction = GatewayTransaction::where('status', 'pending')
                    ->whereJsonContains('metadata->service_request_hash', $validatedData['metadata']['service_request_hash'])
                    ->where('user_id', $validatedData['user_id'] ?? null)
                    ->where('created_at', '>', now()->subMinutes(30)) // Only check transactions from last 30 minutes
                    ->first();
            }
            // Check for wallet charge duplicates (same user, same amount, same type)
            elseif (isset($validatedData['metadata']['type']) && $validatedData['metadata']['type'] === 'wallet_charge') {
                $existingTransaction = GatewayTransaction::where('status', 'pending')
                    ->where('user_id', $validatedData['user_id'] ?? null)
                    ->where('amount', $validatedData['amount'])
                    ->where('type', 'wallet_charge')
                    ->where('created_at', '>', now()->subMinutes(10)) // Shorter window for wallet charges
                    ->first();
            }
            
            if ($existingTransaction) {
                Log::warning('🚫 DUPLICATE PAYMENT PREVENTED: Existing pending transaction found', [
                    'existing_transaction_id' => $existingTransaction->id,
                    'service_request_hash' => $validatedData['metadata']['service_request_hash'] ?? null,
                    'user_id' => $validatedData['user_id'] ?? null,
                    'amount' => $validatedData['amount'],
                    'type' => $validatedData['metadata']['type'] ?? null,
                    'existing_created_at' => $existingTransaction->created_at,
                    'time_diff_minutes' => now()->diffInMinutes($existingTransaction->created_at),
                ]);
                
                DB::rollBack();
                return [
                    'success' => false,
                    'message' => 'پرداخت قبلی در حال پردازش است. لطفاً منتظر بمانید.',
                    'existing_transaction_id' => $existingTransaction->id,
                ];
            }

            // Get user and currency
            $user = null;
            if (isset($validatedData['user_id']) && $validatedData['user_id']) {
                $user = User::findOrFail($validatedData['user_id']);
            }
            $currency = Currency::where('code', $validatedData['currency'])->firstOrFail();

            // Find suitable gateway
            $gateway = $this->findSuitableGateway(
                $validatedData['amount'],
                $currency->code,
                $validatedData['gateway_id'] ?? null
            );

            // Calculate tax
            $taxCalculation = $this->taxService->calculateTax(
                $validatedData['amount'],
                $currency->code
            );

            // Calculate gateway fee
            $gatewayFee = $gateway->calculateFee($validatedData['amount']);

            // Create transaction record
            $transaction = $this->createTransactionRecord([
                'user_id' => $user ? $user->id : null,
                'payment_gateway_id' => $gateway->id,
                'currency_id' => $currency->id,
                'amount' => $validatedData['amount'],
                'tax_amount' => $taxCalculation['total_tax'],
                'gateway_fee' => $gatewayFee,
                'total_amount' => $validatedData['amount'], // No tax or fees added
                'type' => $validatedData['type'] ?? GatewayTransaction::TYPE_PAYMENT,
                'status' => GatewayTransaction::STATUS_PENDING,
                'description' => $validatedData['description'] ?? null,
                'metadata' => $validatedData['metadata'] ?? [],
                'user_ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'user_country' => $this->getUserCountry(),
                'user_device' => $this->getUserDevice(),
            ]);

            // Log transaction creation
            $transaction->addLog(GatewayTransactionLog::ACTION_CREATED, GatewayTransactionLog::SOURCE_WEB, [
                'message' => 'Transaction created successfully',
                'data' => [
                    'amount_breakdown' => [
                        'original_amount' => $validatedData['amount'],
                        'tax_amount' => $taxCalculation['total_tax'],
                        'gateway_fee' => $gatewayFee,
                        'total_amount' => $transaction->total_amount,
                    ],
                    'tax_breakdown' => $taxCalculation['tax_breakdown'],
                    'gateway' => $gateway->name,
                ]
            ]);

            // Initialize payment with gateway
            $gatewayInstance = $this->gatewayManager->gatewayById($gateway->id);
            $paymentResult = $gatewayInstance->createPayment($transaction);


            if (!$paymentResult['success']) {
                $transaction->markAsFailed('خطا در ایجاد پرداخت در درگاه: ' . ($paymentResult['message'] ?? 'خطای نامشخص'));
                
                DB::rollBack();
                return [
                    'success' => false,
                    'message' => 'Failed to initialize payment',
                    'error' => $paymentResult['message'] ?? 'Gateway error',
                ];
            }

            // Update transaction with gateway response
            $transaction->update([
                'gateway_transaction_id' => $paymentResult['reference_id'] ?? null,
                'gateway_response' => $paymentResult,
                'status' => GatewayTransaction::STATUS_PROCESSING,
                'processed_at' => now(),
            ]);

            DB::commit();

            Log::info('پرداخت با موفقیت ایجاد شد', [
                'transaction_id' => $transaction->id,
                'transaction_uuid' => $transaction->uuid,
                'user_id' => $user ? $user->id : null,
                'amount' => $transaction->total_amount,
                'gateway' => $gateway->slug,
            ]);

            return [
                'success' => true,
                'transaction' => $transaction,
                'payment_url' => $paymentResult['payment_url'] ?? null,
                'gateway_result' => $paymentResult,
                'message' => 'پرداخت با موفقیت ایجاد شد',
                'amount_breakdown' => [
                    'original_amount' => $this->currencyService->formatAmount($validatedData['amount'], $currency->code),
                    'tax_amount' => $this->currencyService->formatAmount($taxCalculation['total_tax'], $currency->code),
                    'gateway_fee' => $this->currencyService->formatAmount($gatewayFee, $currency->code),
                    'total_amount' => $this->currencyService->formatAmount($transaction->total_amount, $currency->code),
                ],
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Payment creation failed', [
                'error' => $e->getMessage(),
                'data' => $paymentData,
                'user_id' => $paymentData['user_id'] ?? null,
            ]);

            return [
                'success' => false,
                'message' => 'خطا در ایجاد پرداخت',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify payment from gateway callback
     */
    public function verifyPayment(string $transactionUuid, array $callbackData, string $gatewaySlug = null): array
    {
        try {
            DB::beginTransaction();

            // Find transaction
            $transaction = GatewayTransaction::where('uuid', $transactionUuid)->firstOrFail();

            // Verify gateway slug matches
            if ($gatewaySlug && $transaction->paymentGateway->slug !== $gatewaySlug) {
                throw new \InvalidArgumentException('Gateway mismatch');
            }

            // Check if transaction is in a verifiable state
            if (!$transaction->isPending()) {
                return [
                    'success' => false,
                    'message' => 'Transaction is not in a verifiable state',
                    'status' => $transaction->status,
                ];
            }

            // Get gateway instance and verify payment
            $gatewayInstance = $this->gatewayManager->gatewayById($transaction->payment_gateway_id);
            $verificationResult = $gatewayInstance->verifyPayment($transaction, $callbackData);

            if ($verificationResult['success'] && ($verificationResult['verified'] ?? false)) {
                // Payment verified successfully
                $transaction->update([
                    'gateway_reference' => $verificationResult['reference_id'] ?? null,
                    'gateway_response' => array_merge($transaction->gateway_response ?? [], $verificationResult),
                    'status' => GatewayTransaction::STATUS_COMPLETED,
                    'completed_at' => now(),
                ]);

                $transaction->addLog(GatewayTransactionLog::ACTION_COMPLETED, GatewayTransactionLog::SOURCE_WEBHOOK, [
                    'message' => 'Payment verified and completed successfully',
                    'response_data' => $verificationResult,
                ]);

                DB::commit();

                Log::info('Payment verified successfully', [
                    'transaction_id' => $transaction->id,
                    'transaction_uuid' => $transaction->uuid,
                    'gateway_reference' => $verificationResult['reference_id'] ?? null,
                ]);

                return [
                    'success' => true,
                    'transaction' => $transaction,
                    'message' => 'Payment verified successfully',
                    'reference_id' => $verificationResult['reference_id'] ?? null,
                ];

            } else {
                // Payment verification failed
                $transaction->markAsFailed('خطا در اطلاعات پرداخت: ' . ($verificationResult['message'] ?? 'خطای نامشخص'));

                $transaction->addLog(GatewayTransactionLog::ACTION_FAILED, GatewayTransactionLog::SOURCE_WEBHOOK, [
                    'message' => 'Payment verification failed',
                    'response_data' => $verificationResult,
                    'error_message' => $verificationResult['message'] ?? 'خطا در تأیید',
                ]);

                DB::rollBack();

                return [
                    'success' => false,
                    'transaction' => $transaction,
                    'message' => 'خطا در اطلاعات پرداخت',
                    'error' => $verificationResult['message'] ?? 'خطا در تأیید',
                ];
            }

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Payment verification error', [
                'transaction_uuid' => $transactionUuid,
                'error' => $e->getMessage(),
                'callback_data' => $callbackData,
            ]);

            return [
                'success' => false,
                'message' => 'خطا در اطلاعات پرداخت',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Process refund
     */
    public function refundPayment(string $transactionUuid, int $amount = null, string $reason = null): array
    {
        try {
            DB::beginTransaction();

            $transaction = GatewayTransaction::where('uuid', $transactionUuid)->firstOrFail();

            // Check if transaction can be refunded
            if (!$transaction->canBeRefunded()) {
                return [
                    'success' => false,
                    'message' => 'Transaction cannot be refunded',
                    'status' => $transaction->status,
                ];
            }

            $refundAmount = $amount ?? $transaction->total_amount;

            // Validate refund amount
            if ($refundAmount > $transaction->total_amount) {
                return [
                    'success' => false,
                    'message' => 'Refund amount cannot exceed transaction amount',
                ];
            }

            // Process refund with gateway
            $gatewayInstance = $this->gatewayManager->gatewayById($transaction->payment_gateway_id);
            $refundResult = $gatewayInstance->refund($transaction, $refundAmount);

            if ($refundResult['success']) {
                // Create refund transaction
                $refundTransaction = $this->createTransactionRecord([
                    'user_id' => $transaction->user_id,
                    'payment_gateway_id' => $transaction->payment_gateway_id,
                    'currency_id' => $transaction->currency_id,
                    'amount' => -$refundAmount,
                    'tax_amount' => 0,
                    'gateway_fee' => 0,
                    'total_amount' => -$refundAmount,
                    'type' => $refundAmount === $transaction->total_amount ? GatewayTransaction::TYPE_REFUND : GatewayTransaction::TYPE_PARTIAL_REFUND,
                    'status' => GatewayTransaction::STATUS_COMPLETED,
                    'description' => $reason ?? 'Refund processed',
                    'metadata' => [
                        'original_transaction_id' => $transaction->id,
                        'refund_reason' => $reason,
                    ],
                    'gateway_transaction_id' => $refundResult['reference_id'] ?? null,
                    'gateway_reference' => $refundResult['reference_id'] ?? null,
                    'gateway_response' => $refundResult,
                    'completed_at' => now(),
                ]);

                // Update original transaction status
                $newStatus = $refundAmount === $transaction->total_amount 
                    ? GatewayTransaction::STATUS_REFUNDED 
                    : GatewayTransaction::STATUS_PARTIALLY_REFUNDED;
                
                $transaction->update(['status' => $newStatus]);

                DB::commit();

                Log::info('Refund processed successfully', [
                    'original_transaction_id' => $transaction->id,
                    'refund_transaction_id' => $refundTransaction->id,
                    'refund_amount' => $refundAmount,
                ]);

                return [
                    'success' => true,
                    'refund_transaction' => $refundTransaction,
                    'original_transaction' => $transaction,
                    'message' => 'Refund processed successfully',
                ];
            }

            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Refund processing failed',
                'error' => $refundResult['message'] ?? 'Gateway error',
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Refund processing error', [
                'transaction_uuid' => $transactionUuid,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'خطا در پرداخت برگشت',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get transaction by UUID
     */
    public function getTransaction(string $uuid): ?GatewayTransaction
    {
        return GatewayTransaction::where('uuid', $uuid)->with(['user', 'paymentGateway', 'currency', 'logs'])->first();
    }

    /**
     * Get user transactions with pagination
     */
    public function getUserTransactions(int $userId, int $perPage = 15): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return GatewayTransaction::where('user_id', $userId)
            ->with(['paymentGateway', 'currency'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Validate payment data
     */
    protected function validatePaymentData(array $data): array
    {
        $rules = [
            'user_id' => 'nullable|exists:users,id',
            'amount' => 'required|integer|min:1',
            'currency' => 'required|string|size:3',
            'description' => 'nullable|string|max:255',
            'gateway_id' => 'nullable|exists:payment_gateways,id',
            'type' => 'nullable|in:payment,refund,wallet_charge,wallet_charge_for_service',
            'metadata' => 'nullable|array',
        ];

        $validator = validator($data, $rules);
        
        if ($validator->fails()) {
            throw new \InvalidArgumentException('اطلاعات پرداخت نامعتبر است: ' . $validator->errors()->first());
        }

        return $validator->validated();
    }

    /**
     * Find suitable payment gateway
     */
    protected function findSuitableGateway(int $amount, string $currency, int $gatewayId = null): PaymentGateway
    {
        if ($gatewayId) {
            $gateway = PaymentGateway::findOrFail($gatewayId);
            if (!$gateway->is_active || !$gateway->supportsAmount($amount) || !$gateway->supportsCurrency($currency)) {
                throw new \InvalidArgumentException('Selected gateway is not suitable for this payment');
            }
            return $gateway;
        }

        $gateway = $this->gatewayManager->findBestGateway($amount, $currency);
        
        if (!$gateway) {
            throw new \InvalidArgumentException('هیچ درگاه پرداخت مناسبی برای این پرداخت یافت نشد');
        }

        return $gateway;
    }

    /**
     * Create transaction record
     */
    protected function createTransactionRecord(array $data): GatewayTransaction
    {
        return GatewayTransaction::create($data);
    }

    /**
     * Get user country from IP
     */
    protected function getUserCountry(): ?string
    {
        // This is a placeholder - you can implement IP geolocation
        return null;
    }

    /**
     * Get user device type
     */
    protected function getUserDevice(): ?string
    {
        $userAgent = request()->userAgent();
        
        if (str_contains($userAgent, 'Mobile')) {
            return 'mobile';
        } elseif (str_contains($userAgent, 'Tablet')) {
            return 'tablet';
        }
        
        return 'desktop';
    }

    /**
     * Get payment statistics
     */
    public function getPaymentStatistics(array $filters = []): array
    {
        $query = GatewayTransaction::query();

        // Apply filters
        if (!empty($filters['start_date'])) {
            $query->where('created_at', '>=', $filters['start_date']);
        }
        
        if (!empty($filters['end_date'])) {
            $query->where('created_at', '<=', $filters['end_date']);
        }

        if (!empty($filters['gateway_id'])) {
            $query->where('payment_gateway_id', $filters['gateway_id']);
        }

        if (!empty($filters['currency_id'])) {
            $query->where('currency_id', $filters['currency_id']);
        }

        $transactions = $query->get();

        return [
            'total_transactions' => $transactions->count(),
            'successful_transactions' => $transactions->where('status', GatewayTransaction::STATUS_COMPLETED)->count(),
            'failed_transactions' => $transactions->whereIn('status', [
                GatewayTransaction::STATUS_FAILED, 
                GatewayTransaction::STATUS_CANCELLED, 
                GatewayTransaction::STATUS_EXPIRED
            ])->count(),
            'pending_transactions' => $transactions->whereIn('status', [
                GatewayTransaction::STATUS_PENDING, 
                GatewayTransaction::STATUS_PROCESSING
            ])->count(),
            'total_amount' => $transactions->where('status', GatewayTransaction::STATUS_COMPLETED)->sum('total_amount'),
            'total_tax_collected' => $transactions->where('status', GatewayTransaction::STATUS_COMPLETED)->sum('tax_amount'),
            'total_gateway_fees' => $transactions->where('status', GatewayTransaction::STATUS_COMPLETED)->sum('gateway_fee'),
            'average_transaction_amount' => $transactions->where('status', GatewayTransaction::STATUS_COMPLETED)->avg('total_amount'),
            'success_rate' => $transactions->count() > 0 
                ? round(($transactions->where('status', GatewayTransaction::STATUS_COMPLETED)->count() / $transactions->count()) * 100, 2)
                : 0,
        ];
    }
} 