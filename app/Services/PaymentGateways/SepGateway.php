<?php

namespace App\Services\PaymentGateways;

use App\Models\GatewayTransaction;
use App\Models\GatewayTransactionLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Exception;

class SepGateway extends AbstractPaymentGateway
{
    /**
     * Cache key for tokens (if needed)
     */
    private const TOKEN_CACHE_KEY = 'sep_token_cache';
    
    /**
     * Token cache TTL (19 minutes - tokens expire in 20 minutes by default)
     */
    private const TOKEN_CACHE_TTL = 1140; // 19 minutes in seconds

    /**
     * Get production API URL
     */
    protected function getApiUrl(): string
    {
        return 'https://sep.shaparak.ir';
    }

    /**
     * Get sandbox API URL
     */
    protected function getSandboxApiUrl(): string
    {
        return 'https://sep.shaparak.ir'; // SEP uses same URL for sandbox
    }

    /**
     * Get payment gateway URL
     */
    protected function getGatewayUrl(): string
    {
        return 'https://sep.shaparak.ir/OnlinePG/OnlinePG';
    }

    /**
     * Create payment and get payment URL
     */
    public function createPayment(GatewayTransaction $transaction): array
    {
        try {

            // Log payment creation attempt
            $this->logTransaction(
                $transaction,
                GatewayTransactionLog::ACTION_GATEWAY_CALLED,
                [
                    'message' => 'SEP payment creation initiated',
                    'data' => [
                        'amount' => $transaction->total_amount,
                        'currency' => $transaction->currency->code,
                        'reference_id' => $transaction->reference_id,
                    ]
                ]
            );

            // Get token from SEP API
            $tokenResponse = $this->getToken($transaction);

            if (!$tokenResponse['success']) {
                throw new Exception($tokenResponse['message'] ?? 'Failed to get payment token from SEP');
            }

            $token = $tokenResponse['token'];
            
            // Store token in transaction metadata for later use
            $transaction->update([
                'metadata' => array_merge($transaction->metadata ?? [], [
                    'sep_token' => $token,
                    'token_created_at' => now()->toISOString(),
                ]),
            ]);

            // Generate payment URL - SEP uses GET redirect with token parameter
            $paymentUrl = 'https://sep.shaparak.ir/OnlinePG/SendToken?token=' . urlencode($token);

            // Log successful token creation
            $this->logTransaction(
                $transaction,
                GatewayTransactionLog::ACTION_CREATED,
                [
                    'message' => 'SEP payment token created successfully',
                    'data' => [
                        'token' => substr($token, 0, 10) . '...',
                        'payment_url' => $paymentUrl,
                        'terminal_id' => $this->getConfig('terminal_id'),
                    ]
                ]
            );

            return $this->successResponse([
                'token' => $token,
                'payment_url' => $paymentUrl,
                'terminal_id' => $this->getConfig('terminal_id'),
                'amount' => $transaction->total_amount,
                'reference_id' => $transaction->reference_id,
                'status' => 'TOKEN_CREATED',
                'message' => 'Payment token created successfully',
                'redirect_method' => 'direct_redirect', // SEP uses direct URL redirect
                'gateway_name' => 'سامان', // Add gateway name for the view
            ]);

        } catch (Exception $e) {
            // Log error
            $this->logTransaction(
                $transaction,
                GatewayTransactionLog::ACTION_FAILED,
                [
                    'message' => 'SEP payment creation failed',
                    'error' => $e->getMessage(),
                ]
            );

            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get token from SEP API
     */
    protected function getToken(GatewayTransaction $transaction): array
    {
        $callbackUrl = route('payment.callback', ['gateway' => 'sep', 'transaction' => $transaction->uuid]);

        $requestData = [
            'action' => 'token',
            'TerminalId' => (string) $this->getConfig('terminal_id'),
            'Amount' => $transaction->total_amount * 10, // Convert from Toman to Rial (multiply by 10)
            'ResNum' => $transaction->reference_id ?? $transaction->uuid,
            'RedirectUrl' => $callbackUrl,
            'SettlementIbanInfo' => [
                [
                    'IBAN' => 'IR850560611828005171403801',
                    'Amount' => $transaction->total_amount * 10,
                ]
            ]
        ];

        // Add optional fields
        if (!empty($transaction->user?->mobile)) {
            $requestData['CellNumber'] = $transaction->user->mobile;
        }

        // Add token expiry if configured
        $tokenExpiry = $this->getConfig('token_expiry_minutes', 20);
        if ($tokenExpiry && $tokenExpiry !== 20) {
            $requestData['TokenExpiryInMin'] = max(20, min(3600, $tokenExpiry));
        }

        // Add wage/fee if configured (convert from Toman to Rial)
        if ($transaction->gateway_fee > 0) {
            $requestData['Wage'] = $transaction->gateway_fee * 10;
        }

        // Remove any fields with null values
        $requestData = array_filter($requestData, function($v) { return $v !== null; });

        $apiUrl = $this->getCurrentApiUrl() . '/onlinepg/onlinepg';
        
        try {
            $response = Http::timeout($this->getConfig('timeout', 30))
                ->post($apiUrl, $requestData);

            if (!$response->successful()) {
                throw new Exception("SEP API returned HTTP {$response->status()}: " . $response->body());
            }

            $responseData = $response->json();

            if (!is_array($responseData)) {
                throw new Exception('Invalid JSON response from SEP API');
            }

            // Check if status indicates success
            $status = $responseData['status'] ?? null;
            if ($status === 1) {
                // Success - token received
                return $this->successResponse([
                    'token' => $responseData['token'],
                ]);
            } else {
                // Error - get error message
                $errorCode = $responseData['errorCode'] ?? 'unknown';
                $errorDesc = $responseData['errorDesc'] ?? 'Unknown error occurred';
                
                throw new Exception("SEP Token Error (Code: {$errorCode}): {$errorDesc}");
            }

        } catch (Exception $e) {
            Log::error('SEP Token request failed', [
                'error' => $e->getMessage(),
                'terminal_id' => $this->getConfig('terminal_id'),
            ]);

            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Verify payment from gateway callback
     */
    public function verifyPayment(GatewayTransaction $transaction, array $callbackData): array
    {
        try {
            // Log verification attempt
            $this->logTransaction(
                $transaction,
                GatewayTransactionLog::ACTION_WEBHOOK_RECEIVED,
                [
                    'message' => 'SEP payment verification initiated',
                    'request_data' => $callbackData,
                ]
            );

            // Check if this is a cancellation from callback data
            $state = $callbackData['State'] ?? null;
            $status = $callbackData['Status'] ?? null;
            
            // Log callback data for debugging
            Log::info('SEP callback data analysis', [
                'state' => $state,
                'status' => $status,
                'has_ref_num' => isset($callbackData['RefNum']),
                'has_token' => isset($callbackData['Token']),
                'amount' => $callbackData['Amount'] ?? null,
                'affective_amount' => $callbackData['AffectiveAmount'] ?? null,
            ]);
            
            // Handle cancellation cases directly from callback data
            if ($state === 'CanceledByUser' || $status == 1) {
                $errorMessage = $this->getReceiptStatusMessage(1, $state);
                
                // Log cancellation
                $this->logTransaction(
                    $transaction,
                    GatewayTransactionLog::ACTION_FAILED,
                    [
                        'message' => 'SEP payment cancelled by user',
                        'callback_data' => $callbackData,
                        'state' => $state,
                        'status' => $status,
                    ]
                );

                return $this->errorResponse($errorMessage);
            }

            // Handle successful payment directly from callback data
            if ($state === 'OK' && $status == 2) {
                // Payment is successful - use callback data directly
                $receiptData = $callbackData;
                
                // Validate amounts (gateway returns Rial, transaction is in Toman)
                $gatewayAmountRial = $receiptData['AffectiveAmount'];
                $expectedAmountRial = $transaction->total_amount * 10; // Convert transaction amount to Rial
                
                if ($gatewayAmountRial != $expectedAmountRial) {
                    Log::error('SEP amount mismatch', [
                        'gateway_amount_rial' => $gatewayAmountRial,
                        'transaction_amount_toman' => $transaction->total_amount,
                        'expected_amount_rial' => $expectedAmountRial,
                        'ref_num' => $receiptData['RefNum'],
                    ]);
                    throw new Exception('Amount mismatch between gateway and transaction');
                }

                // IMPORTANT: Call settlement/advice API to finalize the transaction
                $settleResult = $this->settleTransaction($receiptData['RefNum']);
                
                if (!$settleResult['success']) {
                    // Log settlement failure but continue with verification 
                    // as payment was already successful
                    Log::warning('SEP settlement failed but payment was successful', [
                        'ref_num' => $receiptData['RefNum'],
                        'settlement_error' => $settleResult['message'] ?? 'Unknown error',
                    ]);
                }

                // Store payment information
                $transaction->update([
                    'gateway_transaction_id' => $receiptData['RefNum'],
                    'gateway_reference' => $receiptData['Rrn'],
                    'gateway_response' => [
                        'receipt' => $receiptData,
                        'verification' => $receiptData, // Use same data for verification
                        'settlement' => $settleResult, // Store settlement result
                    ],
                    'processed_at' => now(),
                ]);

                // Log successful verification
                $this->logTransaction(
                    $transaction,
                    GatewayTransactionLog::ACTION_COMPLETED,
                    [
                        'message' => 'SEP payment verified and settled successfully',
                        'data' => [
                            'ref_num' => $receiptData['RefNum'],
                            'rrn' => $receiptData['Rrn'],
                            'amount_rial' => $gatewayAmountRial,
                            'amount_toman' => $gatewayAmountRial / 10,
                            'card_number' => $receiptData['HashedCardNumber'] ?? null,
                            'settlement_success' => $settleResult['success'],
                        ]
                    ]
                );

                return $this->successResponse([
                    'verified' => true,
                    'settled' => $settleResult['success'],
                    'digital_receipt' => $receiptData['RefNum'],
                    'rrn' => $receiptData['Rrn'],
                    'amount' => $gatewayAmountRial / 10, // Convert from Rial to Toman
                    'card_number' => $receiptData['HashedCardNumber'] ?? null,
                    'transaction_date' => now(),
                    'trace_number' => $receiptData['TraceNo'] ?? null,
                    'status' => 'verified',
                    'message' => 'Payment verified and settled successfully',
                ]);
            }

            // For other cases, try to get transaction receipt from API
            $receiptResponse = $this->getTransactionReceipt($transaction, $callbackData);
            
            if (!$receiptResponse['success']) {
                throw new Exception($receiptResponse['message'] ?? 'Failed to get transaction receipt');
            }

            $receiptData = $receiptResponse;
            
            // Check if payment was successful
            if ($receiptData['State'] !== 'OK' || $receiptData['Status'] !== 2) {
                $errorMessage = $this->getReceiptStatusMessage($receiptData['Status'], $receiptData['State']);
                throw new Exception($errorMessage);
            }

            // Step 2: Verify the transaction (only for non-successful cases that need API verification)
            $verifyResponse = $this->verifyTransaction($receiptData['RefNum']);
            
            if (!$verifyResponse['success']) {
                throw new Exception($verifyResponse['message'] ?? 'Transaction verification failed');
            }

            $verifyData = $verifyResponse;
            
            // Validate amounts (gateway returns Rial, transaction is in Toman)
            $gatewayAmountRial = $verifyData['AffectiveAmount'];
            $expectedAmountRial = $transaction->total_amount * 10; // Convert transaction amount to Rial
            
            if ($gatewayAmountRial !== $expectedAmountRial) {
                Log::error('SEP amount mismatch', [
                    'gateway_amount_rial' => $gatewayAmountRial,
                    'transaction_amount_toman' => $transaction->total_amount,
                    'expected_amount_rial' => $expectedAmountRial,
                    'ref_num' => $receiptData['RefNum'],
                ]);
                throw new Exception('Amount mismatch between gateway and transaction');
            }

            // IMPORTANT: Call settlement/advice API to finalize the transaction
            $settleResult = $this->settleTransaction($receiptData['RefNum']);
            
            if (!$settleResult['success']) {
                // Log settlement failure but continue with verification 
                // as payment was already successful
                Log::warning('SEP settlement failed but payment was successful', [
                    'ref_num' => $receiptData['RefNum'],
                    'settlement_error' => $settleResult['message'] ?? 'Unknown error',
                ]);
            }

            // Store payment information
            $transaction->update([
                'gateway_transaction_id' => $receiptData['RefNum'],
                'gateway_reference' => $receiptData['Rrn'],
                'gateway_response' => [
                    'receipt' => $receiptData,
                    'verification' => $verifyData,
                    'settlement' => $settleResult, // Store settlement result
                ],
                'processed_at' => now(),
            ]);

            // Log successful verification
            $this->logTransaction(
                $transaction,
                GatewayTransactionLog::ACTION_COMPLETED,
                [
                    'message' => 'SEP payment verified and settled successfully via API',
                    'data' => [
                        'ref_num' => $receiptData['RefNum'],
                        'rrn' => $receiptData['Rrn'],
                        'amount_rial' => $gatewayAmountRial,
                        'amount_toman' => $gatewayAmountRial / 10,
                        'card_number' => $receiptData['HashedCardNumber'] ?? null,
                        'settlement_success' => $settleResult['success'],
                    ]
                ]
            );

            return $this->successResponse([
                'verified' => true,
                'settled' => $settleResult['success'],
                'digital_receipt' => $receiptData['RefNum'],
                'rrn' => $receiptData['Rrn'],
                'amount' => $gatewayAmountRial / 10, // Convert from Rial to Toman
                'card_number' => $receiptData['HashedCardNumber'] ?? null,
                'transaction_date' => $verifyData['StraceDate'] ?? null,
                'trace_number' => $receiptData['TraceNo'] ?? null,
                'status' => 'verified',
                'message' => 'Payment verified and settled successfully',
            ]);

        } catch (Exception $e) {
            // Log verification failure
            $this->logTransaction(
                $transaction,
                GatewayTransactionLog::ACTION_FAILED,
                [
                    'message' => 'SEP payment verification failed',
                    'error' => $e->getMessage(),
                    'callback_data' => $callbackData,
                ]
            );

            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Settle/approve transaction with SEP (similar to advice in other gateways)
     * This finalizes the transaction on the gateway side
     */
    protected function settleTransaction(string $refNum): array
    {
        try {
            // Use the verify API as settlement - calling it confirms the transaction
            $apiUrl = $this->getCurrentApiUrl() . '/verifyTxnRandomSessionkey/ipg/VerifyTransaction';
            
            $requestData = [
                'RefNum' => $refNum,
                'TerminalNumber' => (int) $this->getConfig('terminal_id'),
            ];

            Log::info('SEP Settlement attempt', [
                'ref_num' => $refNum,
                'terminal_id' => $this->getConfig('terminal_id'),
                'api_url' => $apiUrl,
            ]);

            $response = Http::timeout($this->getConfig('timeout', 30))
                ->post($apiUrl, $requestData);

            if (!$response->successful()) {
                throw new Exception("SEP Settlement API returned HTTP {$response->status()}: " . $response->body());
            }

            $responseData = $response->json();

            if ($responseData['Success'] !== true || $responseData['ResultCode'] !== 0) {
                $resultCode = $responseData['ResultCode'] ?? 'unknown';
                $resultDesc = $responseData['ResultDescription'] ?? 'Settlement failed';
                
                // Don't throw exception for settlement failure as payment was already successful
                Log::warning('SEP Settlement failed', [
                    'ref_num' => $refNum,
                    'result_code' => $resultCode,
                    'result_description' => $resultDesc,
                ]);
                
                return $this->errorResponse("Settlement Warning (Code: {$resultCode}): {$resultDesc}");
            }

            Log::info('SEP Settlement successful', [
                'ref_num' => $refNum,
                'response_data' => $responseData,
            ]);

            return $this->successResponse([
                'settled' => true,
                'transaction_detail' => $responseData['TransactionDetail'] ?? null,
                'message' => 'Transaction settled successfully',
            ]);

        } catch (Exception $e) {
            Log::error('SEP Settlement API failed', [
                'error' => $e->getMessage(),
                'ref_num' => $refNum,
                'terminal_id' => $this->getConfig('terminal_id'),
            ]);

            // Don't fail the whole verification for settlement issues
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get transaction receipt from SEP
     */
    protected function getTransactionReceipt(GatewayTransaction $transaction, array $callbackData): array
    {
        $apiUrl = $this->getCurrentApiUrl() . '/verifyTxnRandomSessionkey/api/v2/ipg/payment/receipt';
        
        // Extract token or RefNum from callback
        $token = $callbackData['Token'] ?? null;
        $refNum = $callbackData['RefNum'] ?? null;
        
        if (!$token && !$refNum) {
            throw new Exception('Missing required Token or RefNum in callback data');
        }

        $requestData = [
            'TerminalNumber' => (int) $this->getConfig('terminal_id'),
        ];

        if ($token) {
            $requestData['Token'] = $token;
        } else {
            $requestData['RefNum'] = $refNum;
        }

        try {
            $response = Http::timeout($this->getConfig('timeout', 30))
                ->post($apiUrl, $requestData);

            if (!$response->successful()) {
                throw new Exception("SEP Receipt API returned HTTP {$response->status()}: " . $response->body());
            }

            $responseData = $response->json();

            // Check if response is null or empty
            if (empty($responseData)) {
                throw new Exception("SEP Receipt API returned empty response");
            }

            if ($responseData['HasError'] === true) {
                $errorCode = $responseData['ErrorCode'] ?? 'unknown';
                $errorMessage = $responseData['ErrorMessage'] ?? 'Unknown error';
                throw new Exception("SEP Receipt Error (Code: {$errorCode}): {$errorMessage}");
            }

            // Check if Data field exists and is not null
            if (!isset($responseData['Data']) || $responseData['Data'] === null) {
                throw new Exception("SEP Receipt API returned null data - transaction may not exist or be accessible");
            }

            return $this->successResponse($responseData['Data']);

        } catch (Exception $e) {
            Log::error('SEP Receipt API failed', [
                'error' => $e->getMessage(),
                'api_url' => $apiUrl,
                'request_data' => $requestData,
                'response_status' => $response->status() ?? null,
                'response_body' => $response->body() ?? null,
            ]);

            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Verify transaction with SEP
     */
    protected function verifyTransaction(string $refNum): array
    {
        $apiUrl = $this->getCurrentApiUrl() . '/verifyTxnRandomSessionkey/ipg/VerifyTransaction';
        
        $requestData = [
            'RefNum' => $refNum,
            'TerminalNumber' => (int) $this->getConfig('terminal_id'),
        ];

        try {
            $response = Http::timeout($this->getConfig('timeout', 30))
                ->post($apiUrl, $requestData);

            if (!$response->successful()) {
                throw new Exception("SEP Verify API returned HTTP {$response->status()}: " . $response->body());
            }

            $responseData = $response->json();

            if ($responseData['Success'] !== true || $responseData['ResultCode'] !== 0) {
                $resultCode = $responseData['ResultCode'] ?? 'unknown';
                $resultDesc = $responseData['ResultDescription'] ?? 'Verification failed';
                throw new Exception("SEP Verify Error (Code: {$resultCode}): {$resultDesc}");
            }

            return $this->successResponse($responseData['TransactionDetail']);

        } catch (Exception $e) {
            Log::error('SEP Verify API failed', [
                'error' => $e->getMessage(),
                'api_url' => $apiUrl,
                'request_data' => $requestData,
            ]);

            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Process refund (reverse transaction)
     */
    public function refund(GatewayTransaction $transaction, int $amount = null): array
    {
        try {
            // Check if refunds are enabled
            if (!$this->getConfig('refund_enabled', false)) {
                throw new Exception('Refunds are not enabled for this gateway');
            }

            // SEP only supports full refunds
            if ($amount !== null && $amount !== $transaction->total_amount) {
                throw new Exception('SEP only supports full refunds, not partial refunds');
            }

            // Get RefNum from transaction
            $refNum = $transaction->gateway_transaction_id;
            if (!$refNum) {
                throw new Exception('No digital receipt found for this transaction');
            }

            $apiUrl = $this->getCurrentApiUrl() . '/verifyTxnRandomSessionkey/ipg/ReverseTransaction';
            
            $requestData = [
                'RefNum' => $refNum,
                'TerminalNumber' => (int) $this->getConfig('terminal_id'),
            ];

            $response = Http::timeout($this->getConfig('timeout', 30))
                ->post($apiUrl, $requestData);

            if (!$response->successful()) {
                throw new Exception("SEP Reverse API returned HTTP {$response->status()}: " . $response->body());
            }

            $responseData = $response->json();

            if ($responseData['Success'] !== true || $responseData['ResultCode'] !== 0) {
                $resultCode = $responseData['ResultCode'] ?? 'unknown';
                $resultDesc = $responseData['ResultDescription'] ?? 'Reverse failed';
                throw new Exception("SEP Reverse Error (Code: {$resultCode}): {$resultDesc}");
            }

            // Log successful refund
            $this->logTransaction(
                $transaction,
                GatewayTransactionLog::ACTION_REFUNDED,
                [
                    'message' => 'SEP refund processed successfully',
                    'data' => [
                        'ref_num' => $refNum,
                        'amount' => $transaction->total_amount,
                        'reverse_response' => $responseData,
                    ]
                ]
            );

            return $this->successResponse([
                'refund_amount' => $transaction->total_amount,
                'refund_reference' => $refNum,
                'status' => 'refunded',
                'message' => 'Refund processed successfully',
            ]);

        } catch (Exception $e) {
            // Log refund failure
            $this->logTransaction(
                $transaction,
                GatewayTransactionLog::ACTION_FAILED,
                [
                    'message' => 'SEP refund failed',
                    'error' => $e->getMessage(),
                ]
            );

            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get payment status from transaction data
     */
    public function getPaymentStatus(GatewayTransaction $transaction): array
    {
        try {
            $status = $transaction->status;
            $gatewayResponse = $transaction->gateway_response ?? [];

            $statusMap = [
                'pending' => 'در انتظار پرداخت',
                'processing' => 'در حال پردازش',
                'completed' => 'پرداخت موفق',
                'failed' => 'پرداخت ناموفق',
                'cancelled' => 'لغو شده',
                'refunded' => 'برگشت داده شده',
                'expired' => 'منقضی شده',
            ];

            return $this->successResponse([
                'status' => $status,
                'status_text' => $statusMap[$status] ?? $status,
                'amount' => $transaction->total_amount,
                'gateway_reference' => $transaction->gateway_reference,
                'digital_receipt' => $transaction->gateway_transaction_id,
                'created_at' => $transaction->created_at,
                'completed_at' => $transaction->completed_at,
            ]);

        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get receipt status message
     */
    protected function getReceiptStatusMessage(int $status, string $state): string
    {
        $messages = [
            0 => 'تراکنش در حال پردازش است',
            1 => 'تراکنش توسط کاربر لغو شد',
            2 => 'تراکنش موفق انجام شد',
            3 => 'تراکنش ناموفق بود',
        ];

        return $messages[$status] ?? "وضعیت نامشخص: {$state}";
    }

    /**
     * Get configuration requirements
     */
    public function getConfigRequirements(): array
    {
        return [
            'terminal_id',
            'sandbox',
        ];
    }

    /**
     * Get supported currencies
     */
    public function getSupportedCurrencies(): array
    {
        return ['IRT']; // SEP only supports Iranian Toman/Rial
    }

    /**
     * Get amount limits
     */
    public function getAmountLimits(): array
    {
        return [
            'min' => 1000, // 1,000 Rials minimum
            'max' => 500000000, // 500,000,000 Rials maximum
        ];
    }

    /**
     * Get callback URL
     */
    public function getCallbackUrl(): string
    {
        return route('payment.callback', ['gateway' => 'sep']);
    }

    /**
     * Get gateway information
     */
    public function getGatewayInfo(): array
    {
        return [
            'name' => 'Saman Electronic Payment Gateway',
            'version' => '4.1',
            'supports_refund' => true,
            'supports_partial_refund' => false, // SEP doesn't support partial refunds
            'supports_webhook' => false, // SEP uses callback, not webhooks
            'payment_methods' => ['CARD'],
            'features' => [
                'standard_purchase',
                'token_payment',
                'callback_verification',
                'receipt_retrieval',
                'transaction_verification',
                'transaction_reversal',
                'hashed_card_restriction',
                'mobile_number_integration',
            ],
        ];
    }

    /**
     * Validate webhook signature (not used by SEP)
     */
    public function validateWebhookSignature(array $payload, string $signature): bool
    {
        // SEP doesn't use webhook signatures
        return true;
    }
} 