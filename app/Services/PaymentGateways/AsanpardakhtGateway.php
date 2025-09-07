<?php

namespace App\Services\PaymentGateways;

use App\Models\GatewayTransaction;
use App\Models\GatewayTransactionLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class AsanpardakhtGateway extends AbstractPaymentGateway
{
    /**
     * Get production API URL
     */
    protected function getApiUrl(): string
    {
        return 'https://ipgrest.asanpardakht.ir/v1/';
    }

    /**
     * Get sandbox API URL
     */
    protected function getSandboxApiUrl(): string
    {
        return 'https://ipgrest.asanpardakht.ir/v1/'; // Same as production for Asan Pardakht
    }

    /**
     * Get payment gateway URL
     */
    protected function getGatewayUrl(): string
    {
        return 'https://asan.shaparak.ir';
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
                    'message' => 'AsanPardakht payment token request initiated',
                    'data' => [
                        'amount' => $transaction->total_amount,
                        'currency' => $transaction->currency->code,
                        'local_invoice_id' => $transaction->reference_id,
                    ]
                ]
            );

            // Request payment token
            $tokenResponse = $this->requestPaymentToken($transaction);
            
            if (!$tokenResponse['success']) {
                throw new Exception($tokenResponse['message'] ?? 'خطا در ایجاد توکن پرداخت');
            }

            $token = $tokenResponse['token'];

            // Generate payment form HTML
            $paymentFormHtml = $this->generatePaymentForm($token);

            // Log successful token creation
            $this->logTransaction(
                $transaction,
                GatewayTransactionLog::ACTION_GATEWAY_RESPONSE,
                [
                    'message' => 'AsanPardakht payment token created successfully',
                    'data' => [
                        'token' => $token,
                        'reference_id' => $transaction->reference_id,
                    ]
                ]
            );

            return $this->successResponse([
                'payment_url' => $this->getGatewayUrl(),
                'payment_form' => $paymentFormHtml,
                'token' => $token,
                'reference_id' => $transaction->reference_id,
                'message' => 'Payment token created successfully',
                'redirect_method' => 'form_submit', // Indicates this gateway uses form submission
            ]);

        } catch (Exception $e) {
            // Log error
            $this->logTransaction(
                $transaction,
                GatewayTransactionLog::ACTION_FAILED,
                [
                    'message' => 'AsanPardakht payment creation failed',
                    'error' => $e->getMessage(),
                ]
            );

            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Request payment token from Asan Pardakht
     */
    protected function requestPaymentToken(GatewayTransaction $transaction): array
    {
        $callbackUrl = route('payment.callback', ['gateway' => 'asanpardakht', 'transaction' => $transaction->uuid]);
        
        $requestData = [
            'merchantConfigurationId' => $this->getConfig('merchant_id'),
            'serviceTypeId' => 1, // Standard purchase
            'localInvoiceId' => $transaction->reference_id,
            'amountInRials' => $transaction->total_amount,
            'localDate' => date('Ymd His'),
            'callbackURL' => $callbackUrl,
            'additionalData' => 'Wallet charge via ' . config('app.name'),
            'paymentId' => 0,
            'settlementPortions' => [],
        ];

        $response = Http::withHeaders([
            'usr' => $this->getConfig('username'),
            'pwd' => $this->getConfig('password'),
            'Content-Type' => 'application/json',
        ])->post($this->getCurrentApiUrl() . 'Token/', $requestData);

        if ($response->successful()) {
            $token = $response->body(); // Token is returned as plain text
            
            if (empty($token)) {
                return $this->errorResponse('تراکنش یافت نشد یا ناموفق بود');
            }

            return $this->successResponse(['token' => $token]);
        }

        $errorMessage = $response->body() ?: 'خطای نامشخص از درگاه پرداخت';
        Log::error('AsanPardakht Token Request Failed', [
            'status' => $response->status(),
            'response' => $response->body(),
            'request' => $requestData,
        ]);

        return $this->errorResponse($errorMessage);
    }

    /**
     * Generate payment form HTML for auto-submission
     */
    protected function generatePaymentForm(string $token): string
    {
        $gatewayUrl = $this->getGatewayUrl();
        
        return "
        <form id='asanpardakht-payment-form' method='POST' action='{$gatewayUrl}' style='display: none;'>
            <input type='hidden' name='RefId' value='{$token}' />
        </form>
        <script>
            document.getElementById('asanpardakht-payment-form').submit();
        </script>";
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
                    'message' => 'AsanPardakht payment verification initiated',
                    'request_data' => $callbackData,
                ]
            );

            // Step 1: Get transaction result
            $transactionResult = $this->getTransactionResult($transaction);
            
            if (!$transactionResult['success']) {
                throw new Exception($transactionResult['message'] ?? 'خطا در دریافت نتیجه تراکنش');
            }

            $resultData = $transactionResult;
            
            // Validate the transaction data
            if (!$this->validateTransactionResult($transaction, $resultData)) {
                throw new Exception('Transaction validation failed');
            }

            $payGateTranId = $resultData['payGateTranID'];

            // Step 2: Verify transaction
            $verifyResult = $this->verifyTransaction($payGateTranId);
            
            if (!$verifyResult['success']) {
                throw new Exception($verifyResult['message'] ?? 'خطا در تأیید پرداخت آسان پرداخت');
            }

            // Step 3: Settle transaction
            $settleResult = $this->settleTransaction($payGateTranId);
            
            if (!$settleResult['success']) {
                                 // Log settlement failure but don't fail the transaction
                 // AsanPardakht will auto-settle after 12 hours
                 $this->logTransaction(
                     $transaction,
                     GatewayTransactionLog::ACTION_FAILED,
                     [
                         'message' => 'خطا در تسویه آسان پرداخت - تسویه خودکار انجام خواهد شد',
                         'pay_gate_tran_id' => $payGateTranId,
                     ]
                 );
            }

                         // Log successful verification
             $this->logTransaction(
                 $transaction,
                 GatewayTransactionLog::ACTION_COMPLETED,
                 [
                     'message' => 'AsanPardakht payment verified and settled successfully',
                     'data' => [
                         'pay_gate_tran_id' => $payGateTranId,
                         'amount' => $resultData['amount'],
                         'rrn' => $resultData['rrn'],
                         'card_number' => $resultData['cardNumber'],
                     ]
                 ]
             );

             return $this->successResponse([
                 'verified' => true,
                 'reference_id' => $payGateTranId,
                 'rrn' => $resultData['rrn'],
                 'card_number' => $resultData['cardNumber'],
                 'amount' => $resultData['amount'],
                 'message' => 'Payment verified and settled successfully',
             ]);

         } catch (Exception $e) {
             // Log verification error
             $this->logTransaction(
                 $transaction,
                 GatewayTransactionLog::ACTION_FAILED,
                 [
                     'message' => 'AsanPardakht payment verification failed',
                     'error' => $e->getMessage(),
                 ]
             );

            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get transaction result from Asan Pardakht
     */
    protected function getTransactionResult(GatewayTransaction $transaction): array
    {
        $queryParams = http_build_query([
            'merchantConfigurationId' => $this->getConfig('merchant_id'),
            'localInvoiceId' => $transaction->reference_id,
        ]);

        $response = Http::withHeaders([
            'usr' => $this->getConfig('username'),
            'pwd' => $this->getConfig('password'),
        ])->get($this->getCurrentApiUrl() . 'TranResult?' . $queryParams);

        if ($response->successful()) {
            $data = $response->json();
            
            if (empty($data)) {
                return $this->errorResponse('تراکنش یافت نشد یا ناموفق بود');
            }

            return $this->successResponse($data);
        }

        $errorMessage = $response->body() ?: 'خطای نامشخص از درگاه پرداخت';
        Log::error('AsanPardakht Transaction Result Failed', [
            'status' => $response->status(),
            'response' => $response->body(),
            'transaction_id' => $transaction->id,
        ]);

        return $this->errorResponse($errorMessage);
    }

    /**
     * Verify transaction with Asan Pardakht
     */
    protected function verifyTransaction(string $payGateTranId): array
    {
        $requestData = [
            'merchantConfigurationId' => $this->getConfig('merchant_id'),
            'payGateTranId' => $payGateTranId,
        ];

        $response = Http::withHeaders([
            'usr' => $this->getConfig('username'),
            'pwd' => $this->getConfig('password'),
            'Content-Type' => 'application/json',
        ])->post($this->getCurrentApiUrl() . 'Verify/', $requestData);

        if ($response->successful()) {
            return $this->successResponse(['verified' => true]);
        }

        $errorMessage = $response->body() ?: 'خطای نامشخص از درگاه پرداخت';
        Log::error('AsanPardakht Verification Failed', [
            'status' => $response->status(),
            'response' => $response->body(),
            'pay_gate_tran_id' => $payGateTranId,
        ]);

        return $this->errorResponse($errorMessage);
    }

    /**
     * Settle transaction with Asan Pardakht
     */
    protected function settleTransaction(string $payGateTranId): array
    {
        $requestData = [
            'merchantConfigurationId' => $this->getConfig('merchant_id'),
            'payGateTranId' => $payGateTranId,
        ];

        $response = Http::withHeaders([
            'usr' => $this->getConfig('username'),
            'pwd' => $this->getConfig('password'),
            'Content-Type' => 'application/json',
        ])->post($this->getCurrentApiUrl() . 'Settlement/', $requestData);

        if ($response->successful()) {
            return $this->successResponse(['settled' => true]);
        }

        $errorMessage = $response->body() ?: 'خطای نامشخص از درگاه پرداخت';
        Log::error('AsanPardakht Settlement Failed', [
            'status' => $response->status(),
            'response' => $response->body(),
            'pay_gate_tran_id' => $payGateTranId,
        ]);

        return $this->errorResponse($errorMessage);
    }

    /**
     * Validate transaction result against stored data
     */
    protected function validateTransactionResult(GatewayTransaction $transaction, array $resultData): bool
    {
        // Validate amount
        if ((int)$resultData['amount'] !== $transaction->total_amount) {
            Log::error('AsanPardakht amount mismatch', [
                'expected' => $transaction->total_amount,
                'received' => $resultData['amount'],
                'transaction_id' => $transaction->id,
            ]);
            return false;
        }

        // Validate local invoice ID
        if ($resultData['salesOrderID'] !== $transaction->reference_id) {
            Log::error('AsanPardakht reference ID mismatch', [
                'expected' => $transaction->reference_id,
                'received' => $resultData['salesOrderID'],
                'transaction_id' => $transaction->id,
            ]);
            return false;
        }

        return true;
    }

    /**
     * Process refund
     */
    public function refund(GatewayTransaction $transaction, int $amount = null): array
    {
        try {
            $refundAmount = $amount ?? $transaction->total_amount;
            
                         // Log refund attempt
             $this->logTransaction(
                 $transaction,
                 GatewayTransactionLog::ACTION_PROCESSING,
                 [
                     'message' => 'AsanPardakht refund initiated',
                     'data' => [
                         'refund_amount' => $refundAmount,
                         'original_amount' => $transaction->total_amount,
                     ]
                 ]
             );

            // Get pay gate transaction ID from transaction logs
            $payGateTranId = $this->getPayGateTranId($transaction);
            
            if (!$payGateTranId) {
                throw new Exception('Payment gateway transaction ID not found');
            }

            // Try to cancel first (if verified but not settled)
            $cancelResult = $this->cancelTransaction($payGateTranId);
            
            if ($cancelResult['success']) {
                $this->logTransaction(
                    $transaction,
                    GatewayTransactionLog::ACTION_REFUNDED,
                    [
                        'message' => 'AsanPardakht transaction cancelled successfully',
                        'data' => ['pay_gate_tran_id' => $payGateTranId]
                    ]
                );

                return $this->successResponse([
                    'refunded' => true,
                    'refund_amount' => $refundAmount,
                    'reference_id' => $payGateTranId,
                    'message' => 'Transaction cancelled successfully',
                ]);
            }

            // If cancel fails, try reverse (if not verified)
            $reverseResult = $this->reverseTransaction($payGateTranId);
            
            if ($reverseResult['success']) {
                $this->logTransaction(
                    $transaction,
                    GatewayTransactionLog::ACTION_REFUNDED,
                    [
                        'message' => 'AsanPardakht transaction reversed successfully',
                        'data' => ['pay_gate_tran_id' => $payGateTranId]
                    ]
                );

                return $this->successResponse([
                    'refunded' => true,
                    'refund_amount' => $refundAmount,
                    'reference_id' => $payGateTranId,
                    'message' => 'Transaction reversed successfully',
                ]);
            }

            throw new Exception('Both cancel and reverse operations failed');

                 } catch (Exception $e) {
             $this->logTransaction(
                 $transaction,
                 GatewayTransactionLog::ACTION_FAILED,
                 [
                     'message' => 'AsanPardakht refund failed',
                     'error' => $e->getMessage(),
                 ]
             );

             return $this->errorResponse($e->getMessage());
         }
    }

    /**
     * Cancel a verified transaction
     */
    protected function cancelTransaction(string $payGateTranId): array
    {
        $requestData = [
            'merchantConfigurationId' => $this->getConfig('merchant_id'),
            'payGateTranId' => $payGateTranId,
        ];

        $response = Http::withHeaders([
            'usr' => $this->getConfig('username'),
            'pwd' => $this->getConfig('password'),
            'Content-Type' => 'application/json',
        ])->post($this->getCurrentApiUrl() . 'Cancel/', $requestData);

        if ($response->successful()) {
            return $this->successResponse(['cancelled' => true]);
        }

        return $this->errorResponse($response->body() ?: 'خطای نامشخص از درگاه پرداخت');
    }

    /**
     * Reverse an unverified transaction
     */
    protected function reverseTransaction(string $payGateTranId): array
    {
        $requestData = [
            'merchantConfigurationId' => $this->getConfig('merchant_id'),
            'payGateTranId' => $payGateTranId,
        ];

        $response = Http::withHeaders([
            'usr' => $this->getConfig('username'),
            'pwd' => $this->getConfig('password'),
            'Content-Type' => 'application/json',
        ])->post($this->getCurrentApiUrl() . 'Reverse/', $requestData);

        if ($response->successful()) {
            return $this->successResponse(['reversed' => true]);
        }

        return $this->errorResponse($response->body() ?: 'خطای نامشخص از درگاه پرداخت');
    }

    /**
     * Get pay gate transaction ID from transaction logs
     */
    protected function getPayGateTranId(GatewayTransaction $transaction): ?string
    {
                 $verificationLog = $transaction->logs()
             ->where('action', GatewayTransactionLog::ACTION_COMPLETED)
             ->where('response_data->data->pay_gate_tran_id', '!=', null)
             ->first();

        if ($verificationLog && isset($verificationLog->response_data['data']['pay_gate_tran_id'])) {
            return $verificationLog->response_data['data']['pay_gate_tran_id'];
        }

        return null;
    }

    /**
     * Check payment status
     */
    public function getPaymentStatus(GatewayTransaction $transaction): array
    {
        return $this->getTransactionResult($transaction);
    }

    /**
     * Get gateway configuration requirements
     */
    public function getConfigRequirements(): array
    {
        return [
            'merchant_id' => [
                'type' => 'string',
                'label' => 'Merchant Configuration ID',
                'description' => 'Your unique merchant configuration ID from Asan Pardakht',
                'required' => true,
            ],
            'username' => [
                'type' => 'string',
                'label' => 'API Username',
                'description' => 'Your API username from Asan Pardakht',
                'required' => true,
            ],
            'password' => [
                'type' => 'password',
                'label' => 'API Password',
                'description' => 'Your API password from Asan Pardakht',
                'required' => true,
            ],
            'sandbox' => [
                'type' => 'boolean',
                'label' => 'Sandbox Mode',
                'description' => 'Enable sandbox mode for testing',
                'required' => false,
                'default' => false,
            ],
        ];
    }

    /**
     * Validate gateway configuration
     */
    public function validateConfig(array $config): bool
    {
        $required = ['merchant_id', 'username', 'password'];
        
        foreach ($required as $field) {
            if (empty($config[$field])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get supported currencies
     */
    public function getSupportedCurrencies(): array
    {
        return ['IRT']; // Iranian Toman
    }

    /**
     * Get minimum/maximum amounts
     */
    public function getAmountLimits(): array
    {
        return [
            'min_amount' => 1000, // 1000 IRT minimum
            'max_amount' => 500000000, // 500M IRT maximum
        ];
    }

    /**
     * Validate webhook signature
     */
    public function validateWebhookSignature(array $payload, string $signature): bool
    {
        // Asan Pardakht doesn't use webhook signatures
        // Validation is done through the TranResult API call
        return true;
    }

    /**
     * Get Asan Pardakht specific callback URL
     */
    public function getCallbackUrl(): string
    {
        return route('payment.callback', ['gateway' => 'asanpardakht']);
    }
} 