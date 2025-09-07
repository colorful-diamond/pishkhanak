<?php

namespace App\Services\PaymentGateways;

use App\Models\GatewayTransaction;
use App\Models\GatewayTransactionLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Exception;

class SepehrGateway extends AbstractPaymentGateway
{
    /**
     * Cache key for access token (if needed)
     */
    private const TOKEN_CACHE_KEY = 'sepehr_access_token';
    
    /**
     * Token cache TTL (29 minutes - tokens expire in 30 minutes)
     */
    private const TOKEN_CACHE_TTL = 1740; // 29 minutes in seconds

    /**
     * Get production API URL
     */
    protected function getApiUrl(): string
    {
        return 'https://sepehr.shaparak.ir:8081';
    }

    /**
     * Get sandbox API URL
     */
    protected function getSandboxApiUrl(): string
    {
        return 'https://sepehr.shaparak.ir:8081'; // Sepehr uses same URL for sandbox
    }

    /**
     * Get payment gateway URL
     */
    protected function getGatewayUrl(): string
    {
        return 'https://sepehr.shaparak.ir:8080';
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
                    'message' => 'Sepehr payment creation initiated',
                    'data' => [
                        'amount' => $transaction->total_amount,
                        'currency' => $transaction->currency->code,
                        'reference_id' => $transaction->reference_id,
                    ]
                ]
            );

            // Get token from Sepehr API
            $tokenResponse = $this->getToken($transaction);

            if (!$tokenResponse['success']) {
                throw new Exception($tokenResponse['message'] ?? 'خطا در دریافت توکن پرداخت');
            }

            if (!isset($tokenResponse['data']['Accesstoken'])) {
                throw new Exception('Access token not found in response');
            }

            $accessToken = $tokenResponse['data']['Accesstoken'];

            // Store access token in transaction metadata
            $transaction->update([
                'metadata' => array_merge($transaction->metadata ?? [], [
                    'access_token' => $accessToken,
                    'terminal_id' => $this->getConfig('terminal_id'),
                ])
            ]);

            // Determine payment page URL based on transaction type
            $paymentUrl = $this->buildPaymentPageUrl($transaction, $accessToken);

            // Log successful token generation
            $this->logTransaction(
                $transaction,
                GatewayTransactionLog::ACTION_CREATED,
                [
                    'message' => 'Sepehr payment token created successfully',
                    'data' => [
                        'access_token' => substr($accessToken, 0, 10) . '...',
                        'payment_url' => $paymentUrl,
                        'terminal_id' => $this->getConfig('terminal_id'),
                    ]
                ]
            );

            // Generate payment form HTML
            $paymentFormHtml = $this->generatePaymentForm($paymentUrl, $this->getConfig('terminal_id'), $accessToken, $transaction);

            return $this->successResponse([
                'access_token' => $accessToken,
                'payment_url' => $paymentUrl,
                'payment_form' => $paymentFormHtml,
                'terminal_id' => $this->getConfig('terminal_id'),
                'amount' => $transaction->total_amount,
                'reference_id' => $transaction->reference_id,
                'status' => 'TOKEN_CREATED',
                'message' => 'Payment token created successfully',
                'redirect_method' => 'form_submit', // Indicate this gateway uses form submission
                'gateway_name' => 'سپهر', // Add gateway name for the view
            ]);

        } catch (Exception $e) {
            // Log error
            $this->logTransaction(
                $transaction,
                GatewayTransactionLog::ACTION_FAILED,
                [
                    'message' => 'Sepehr payment creation failed',
                    'error' => $e->getMessage(),
                ]
            );

            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get token from Sepehr API
     */
    protected function getToken(GatewayTransaction $transaction): array
    {
        $callbackUrl = route('payment.callback', ['gateway' => 'sepehr', 'transaction' => $transaction->uuid]);

        $requestData = [
            'terminalID' => (int) $this->getConfig('terminal_id'),
            'Amount' => $transaction->amount * 10, // Convert from Toman to Rial (multiply by 10)
            'callbackURL' => $callbackUrl,
            'invoiceID' => $transaction->reference_id ?? $transaction->uuid,
            'Payload' => $this->buildPayload($transaction),
        ];

        // Add optional fields
        if (!empty($transaction->user?->email)) {
            $requestData['email'] = $transaction->user->email;
        }

        // Use GET method for callback if specified
        $getMethod = $this->getConfig('get_method', false);
        if ($getMethod) {
            $requestData['getMethod'] = '1';
        }

        // Remove any fields with null values
        $requestData = array_filter($requestData, function($v) { return $v !== null; });

        $apiUrl = $this->getCurrentApiUrl() . '/V1/PeymentApi/GetToken';

        $response = $this->makeRequest('POST', $apiUrl, $requestData, [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ]);

        // Check if we have a valid response structure
        if ($response['success'] && isset($response['data']) && is_array($response['data'])) {
            // Check for successful token generation
            if (isset($response['data']['Accesstoken']) && isset($response['data']['Status']) && $response['data']['Status'] === 0) {
                $tokenResponse = $this->successResponse([
                    'data' => [
                        'Accesstoken' => $response['data']['Accesstoken'],
                        'Status' => $response['data']['Status']
                    ]
                ]);
                
                return $tokenResponse;
            }
        }

        // Handle error response properly
        $statusCode = -1;
        if (isset($response['data']) && is_array($response['data']) && isset($response['data']['Status'])) {
            $statusCode = $response['data']['Status'];
        }
        
        $errorMessage = $this->getTokenErrorMessage($statusCode);

        return $this->errorResponse($errorMessage);
    }

    /**
     * Build payload for different transaction types
     */
    protected function buildPayload(GatewayTransaction $transaction): string
    {
        $metadata = $transaction->metadata ?? [];
        $payload = [];

        // Always include transaction UUID for tracking
        $payload['transaction_uuid'] = $transaction->uuid;
        
        // For non-authenticated users, add special flag
        if (!$transaction->user_id) {
            $payload['guest_transaction'] = true;
            $payload['requires_login'] = $metadata['requires_login'] ?? false;
        }

        // Add guest payment hash if available
        if (isset($metadata['guest_payment_hash'])) {
            $payload['guest_payment_hash'] = $metadata['guest_payment_hash'];
        }

        // Add service continuation data if available
        if (isset($metadata['continue_service'])) {
            $payload['continue_service'] = $metadata['continue_service'];
            $payload['service_request_hash'] = $metadata['service_request_hash'] ?? null;
        }

        // Add transaction type specific data
        if (isset($metadata['transaction_type'])) {
            switch ($metadata['transaction_type']) {
                case 'bill_payment':
                    if (isset($metadata['bill_id']) && isset($metadata['pay_id'])) {
                        $payload['BillID'] = $metadata['bill_id'];
                        $payload['PayID'] = $metadata['pay_id'];
                    }
                    break;

                case 'batch_bill_payment':
                    if (isset($metadata['bills']) && is_array($metadata['bills'])) {
                        $payload['BList'] = $metadata['bills'];
                    }
                    break;

                case 'mobile_topup':
                    if (isset($metadata['mobile_data'])) {
                        $payload = array_merge($payload, $metadata['mobile_data']);
                    }
                    break;

                case 'fund_splitting':
                    if (isset($metadata['split_accounts'])) {
                        $payload['SList'] = $metadata['split_accounts'];
                        $payload['Id'] = $metadata['split_id'] ?? '0001';
                    }
                    break;
            }
        }

        // Add user identification for identified purchases
        if (isset($metadata['national_code'])) {
            $payload['national_code'] = $metadata['national_code'];
        }

        // Add mobile number for mobile-based payments
        if (isset($metadata['mobile_number'])) {
            $payload['mobile_number'] = $metadata['mobile_number'];
        }

        return json_encode($payload, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Build payment page URL with form data
     */
    protected function buildPaymentPageUrl(GatewayTransaction $transaction, string $accessToken): string
    {
        $metadata = $transaction->metadata ?? [];
        $transactionType = $metadata['transaction_type'] ?? 'purchase';

        // Determine the correct payment page endpoint
        $endpoint = match ($transactionType) {
            'bill_payment' => '/Bill',
            'batch_bill_payment' => '/BatchBill',
            'mobile_topup' => '/Charge',
            'purchase_with_mobile' => '/Mpay',
            'bill_payment_with_mobile' => '/MBill',
            'batch_bill_payment_with_mobile' => '/MBatchBill',
            'mobile_topup_with_mobile' => '/MCharge',
            default => '/Pay',
        };

        return $this->getGatewayUrl() . $endpoint;
    }

    /**
     * Generate payment form HTML for redirecting to Sepehr
     */
    protected function generatePaymentForm(string $paymentUrl, string $terminalId, string $accessToken, GatewayTransaction $transaction): string
    {
        $formId = 'sepehr-payment-form';
        $nationalCode = $transaction->metadata['national_code'] ?? null;
        $getMethod = $this->getConfig('get_method', false);
        
        $html = '<form id="' . $formId . '" method="post" action="' . $paymentUrl . '" style="display: none;">';
        $html .= '<input type="hidden" name="TerminalID" value="' . $terminalId . '" />';
        $html .= '<input type="hidden" name="token" value="' . $accessToken . '" />';
        
        if ($nationalCode) {
            $html .= '<input type="hidden" name="nationalCode" value="' . $nationalCode . '" />';
        }
        
        if ($getMethod) {
            $html .= '<input type="hidden" name="getMethod" value="1" />';
        }
        
        $html .= '<noscript>';
        $html .= '<p>در حال انتقال به درگاه پرداخت سپهر...</p>';
        $html .= '<input type="submit" value="ادامه" />';
        $html .= '</noscript>';
        $html .= '</form>';
        
        // Add auto-submit script
        $html .= '<script type="text/javascript">';
        $html .= 'document.getElementById("' . $formId . '").submit();';
        $html .= '</script>';
        
        return $html;
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
                    'message' => 'Sepehr payment verification initiated',
                    'request_data' => $callbackData,
                ]
            );

            // Check if payment was successful
            $respCode = (int) ($callbackData['respcode'] ?? -1);
            
            if ($respCode !== 0) {
                $errorMessage = $this->getCallbackErrorMessage($respCode);
                throw new Exception($errorMessage);
            }

            // Verify required callback data
            $requiredFields = ['digitalreceipt', 'amount', 'invoiceid', 'terminalid', 'rrn'];
            foreach ($requiredFields as $field) {
                if (empty($callbackData[$field])) {
                    throw new Exception("Missing required field: {$field}");
                }
            }

            // Verify amount (convert gateway's Rial to Toman for comparison)
            $gatewayAmountInToman = (int) $callbackData['amount'] / 10;
            if ($gatewayAmountInToman !== $transaction->total_amount) {
                Log::error('Sepehr amount mismatch', [
                    'gateway_amount_rial' => $callbackData['amount'],
                    'gateway_amount_toman' => $gatewayAmountInToman,
                    'transaction_amount_toman' => $transaction->total_amount,
                ]);
                throw new Exception('Amount mismatch between callback and transaction');
            }

            // Verify terminal ID
            if ((int) $callbackData['terminalid'] !== (int) $this->getConfig('terminal_id')) {
                throw new Exception('Terminal ID mismatch');
            }

            // Verify invoice ID
            if ($callbackData['invoiceid'] !== ($transaction->reference_id ?? $transaction->uuid)) {
                throw new Exception('Invoice ID mismatch');
            }

            // For purchase transactions, we must call Advice API to finalize the payment
            $metadata = $transaction->metadata ?? [];
            $transactionType = $metadata['transaction_type'] ?? 'purchase';
            
            if (in_array($transactionType, ['purchase', 'purchase_with_mobile', 'identified_purchase'])) {
                $adviceResult = $this->callAdviceApi($transaction, $callbackData['digitalreceipt']);
                
                if (!$adviceResult['success']) {
                    throw new Exception($adviceResult['message'] ?? 'خطا در تایید نهایی پرداخت');
                }

                // Verify advice amount matches (convert from Rial to Toman)
                $adviceAmountInToman = ($adviceResult['amount'] ?? 0) / 10;
                if ($adviceAmountInToman !== $transaction->total_amount) {
                    Log::error('Sepehr advice amount mismatch', [
                        'advice_amount_rial' => $adviceResult['amount'] ?? 'N/A',
                        'advice_amount_toman' => $adviceAmountInToman,
                        'transaction_amount_toman' => $transaction->total_amount,
                    ]);
                    throw new Exception('Amount mismatch in advice response');
                }
            }

            // Store callback data in transaction metadata
            $transaction->update([
                'gateway_transaction_id' => $callbackData['digitalreceipt'],
                'gateway_reference' => $callbackData['rrn'],
                'gateway_response' => $callbackData,
                'metadata' => array_merge($transaction->metadata ?? [], [
                    'callback_data' => $callbackData,
                    'advice_completed' => in_array($transactionType, ['purchase', 'purchase_with_mobile', 'identified_purchase']),
                ])
            ]);

            // Log successful verification
            $this->logTransaction(
                $transaction,
                GatewayTransactionLog::ACTION_COMPLETED,
                [
                    'message' => 'Sepehr payment verified successfully',
                    'data' => [
                        'digital_receipt' => $callbackData['digitalreceipt'],
                        'rrn' => $callbackData['rrn'],
                        'amount' => $callbackData['amount'],
                        'card_number' => $callbackData['cardnumber'] ?? null,
                        'issuer_bank' => $callbackData['issuerbank'] ?? null,
                        'date_paid' => $callbackData['datePaid'] ?? null,
                    ]
                ]
            );

            return $this->successResponse([
                'verified' => true,
                'digital_receipt' => $callbackData['digitalreceipt'],
                'rrn' => $callbackData['rrn'],
                'amount' => $callbackData['amount'],
                'card_number' => $callbackData['cardnumber'] ?? null,
                'issuer_bank' => $callbackData['issuerbank'] ?? null,
                'date_paid' => $callbackData['datePaid'] ?? null,
                'message' => 'Payment verified successfully',
            ]);

        } catch (Exception $e) {
            // Log error
            $this->logTransaction(
                $transaction,
                GatewayTransactionLog::ACTION_FAILED,
                [
                    'message' => 'Sepehr payment verification failed',
                    'error' => $e->getMessage(),
                    'callback_data' => $callbackData,
                ]
            );

            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Call Advice API to finalize the transaction
     */
    protected function callAdviceApi(GatewayTransaction $transaction, string $digitalReceipt): array
    {
        $requestData = [
            'digitalreceipt' => $digitalReceipt,
            'Tid' => (int) $this->getConfig('terminal_id'),
        ];

        $apiUrl = $this->getCurrentApiUrl() . '/V1/PeymentApi/Advice';
        $response = $this->makeRequest('POST', $apiUrl, $requestData, [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ]);

        if ($response['success'] && isset($response['data']['Status'])) {
            $status = $response['data']['Status'];
            $returnId = $response['data']['ReturnId'] ?? null;
            $message = $response['data']['Message'] ?? '';

            if ($status === 'Ok' || $status === 'Duplicate') {
                // Parse amount from ReturnId (it contains the verified amount)
                $amount = is_numeric($returnId) ? (int) $returnId : null;
                
                return $this->successResponse([
                    'status' => $status,
                    'amount' => $amount,
                    'message' => $message,
                ]);
            } else {
                // NOk status - advice failed
                return $this->errorResponse("Advice failed: {$message} (Code: {$returnId})");
            }
        }

        return $this->errorResponse('خطا در ارتباط با سرویس تایید پرداخت');
    }

    /**
     * Process refund using Rollback API
     */
    public function refund(GatewayTransaction $transaction, int $amount = null): array
    {
        try {
            $digitalReceipt = $transaction->gateway_transaction_id;
            
            if (!$digitalReceipt) {
                throw new Exception('Digital receipt not found for this transaction');
            }

            $refundAmount = $amount ?? $transaction->total_amount;

            // Check if rollback service is enabled
            if (!$this->getConfig('rollback_enabled', false)) {
                throw new Exception('Rollback service is not enabled for this merchant');
            }

            $requestData = [
                'digitalreceipt' => $digitalReceipt,
                'Tid' => (int) $this->getConfig('terminal_id'),
            ];

            $apiUrl = $this->getCurrentApiUrl() . '/V1/PeymentApi/Rollback';
            $response = $this->makeRequest('POST', $apiUrl, $requestData, [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]);

            if ($response['success'] && isset($response['data']['Status'])) {
                $status = $response['data']['Status'];
                $returnId = $response['data']['ReturnId'] ?? null;
                $message = $response['data']['Message'] ?? '';

                if ($status === 'Ok') {
                    // Parse amount from ReturnId
                    $refundedAmount = is_numeric($returnId) ? (int) $returnId : $refundAmount;
                    
                    // Log successful refund
                    $this->logTransaction(
                        $transaction,
                        GatewayTransactionLog::ACTION_REFUNDED,
                        [
                            'message' => 'Sepehr payment refunded successfully',
                            'data' => [
                                'digital_receipt' => $digitalReceipt,
                                'refunded_amount' => $refundedAmount,
                                'status' => $status,
                                'message' => $message,
                            ]
                        ]
                    );

                    return $this->successResponse([
                        'refunded' => true,
                        'refund_id' => $digitalReceipt,
                        'amount' => $refundedAmount,
                        'status' => $status,
                        'message' => $message ?: 'Payment refunded successfully',
                    ]);
                } else {
                    throw new Exception("Rollback failed: {$message} (Code: {$returnId})");
                }
            }

            throw new Exception('خطا در ارتباط با سرویس بازپرداخت');

        } catch (Exception $e) {
            // Log error
            $this->logTransaction(
                $transaction,
                GatewayTransactionLog::ACTION_FAILED,
                [
                    'message' => 'Sepehr payment refund failed',
                    'error' => $e->getMessage(),
                ]
            );

            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get payment status (not directly supported by Sepehr, use callback data)
     */
    public function getPaymentStatus(GatewayTransaction $transaction): array
    {
        try {
            $metadata = $transaction->metadata ?? [];
            
            if (isset($metadata['callback_data'])) {
                $callbackData = $metadata['callback_data'];
                $respCode = (int) ($callbackData['respcode'] ?? -1);
                
                $status = match ($respCode) {
                    0 => 'SUCCESSFUL',
                    -1 => 'CANCELLED',
                    -2 => 'TIMEOUT',
                    default => 'FAILED',
                };

                return $this->successResponse([
                    'status' => $status,
                    'digital_receipt' => $callbackData['digitalreceipt'] ?? null,
                    'rrn' => $callbackData['rrn'] ?? null,
                    'amount' => $callbackData['amount'] ?? null,
                    'response_code' => $respCode,
                    'message' => $this->getCallbackErrorMessage($respCode),
                ]);
            }

            // If no callback data available, check transaction status
            return $this->successResponse([
                'status' => 'PENDING',
                'message' => 'Payment is pending or no callback received yet',
            ]);

        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get token error message
     */
    protected function getTokenErrorMessage(int $statusCode): string
    {
        $messages = [
            -1 => 'تراکنش یافت نشد',
            -2 => 'خطا در ارتباط با سرور - IP نامعتبر یا فایروال',
            -3 => 'خطای عمومی / تراکنش قبلاً برگشت خورده',
            -4 => 'عملیات مورد نظر برای این تراکنش قابل انجام نیست',
            -5 => 'آدرس IP نامعتبر - IP در لیست مجاز نیست',
            -6 => 'سرویس بازپرداخت برای این پذیرنده فعال نیست',
        ];

        return $messages[$statusCode] ?? "خطای نامشخص (کد: {$statusCode})";
    }

    /**
     * Get callback error message
     */
    protected function getCallbackErrorMessage(int $respCode): string
    {
        $messages = [
            0 => 'پرداخت موفق',
            -1 => 'پرداخت لغو شد توسط کاربر',
            -2 => 'پرداخت به دلیل زمان‌بری منقضی شد',
        ];

        return $messages[$respCode] ?? "پرداخت ناموفق (کد: {$respCode})";
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
        return ['IRT']; // Sepehr only supports Iranian Toman/Rial
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
        return route('payment.callback', ['gateway' => 'sepehr']);
    }

    /**
     * Get gateway information
     */
    public function getGatewayInfo(): array
    {
        return [
            'name' => 'Sepehr Electronic Payment Gateway',
            'version' => '3.0.6',
            'supports_refund' => true,
            'supports_partial_refund' => false, // Sepehr doesn't support partial refunds
            'supports_webhook' => false, // Sepehr uses callback, not webhooks
            'payment_methods' => ['CARD'],
            'features' => [
                'standard_purchase',
                'bill_payment',
                'batch_bill_payment',
                'mobile_topup',
                'identified_purchase',
                'fund_splitting',
                'mobile_number_integration',
            ],
        ];
    }

    /**
     * Validate webhook signature (not used by Sepehr)
     */
    public function validateWebhookSignature(array $payload, string $signature): bool
    {
        // Sepehr doesn't use webhook signatures
        return true;
    }
} 