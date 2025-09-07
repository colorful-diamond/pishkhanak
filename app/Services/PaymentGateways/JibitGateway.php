<?php

namespace App\Services\PaymentGateways;

use App\Models\GatewayTransaction;
use App\Models\GatewayTransactionLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Exception;

class JibitGateway extends AbstractPaymentGateway
{
    /**
     * Cache key for PPG access token
     */
    private const PPG_TOKEN_CACHE_KEY = 'jibit_ppg_access_token';
    
    /**
     * Cache key for PPG refresh token
     */
    private const PPG_REFRESH_TOKEN_CACHE_KEY = 'jibit_ppg_refresh_token';
    
    /**
     * Cache key for token generation count (to respect twice per day limit)
     */
    private const PPG_TOKEN_GENERATION_COUNT_KEY = 'jibit_ppg_token_generation_count';
    
    /**
     * Token cache TTL (23 hours - token expires in 24 hours)
     */
    private const TOKEN_CACHE_TTL = 82800; // 23 hours in seconds
    
    /**
     * Refresh token cache TTL (47 hours - refresh token expires in 48 hours)
     */
    private const REFRESH_TOKEN_CACHE_TTL = 169200; // 47 hours in seconds

    /**
     * Get production API URL
     */
    protected function getApiUrl(): string
    {
        return 'https://napi.jibit.ir/ppg/v3';
    }

    /**
     * Get sandbox API URL
     */
    protected function getSandboxApiUrl(): string
    {
        return 'https://napi.jibit.ir/ppg/v3'; // Jibit uses same URL for sandbox
    }

    /**
     * Get payment gateway URL
     */
    protected function getGatewayUrl(): string
    {
        return 'https://napi.jibit.ir/ppg/v3/payment';
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
                    'message' => 'Jibit payment creation initiated',
                    'data' => [
                        'amount' => $transaction->total_amount,
                        'currency' => $transaction->currency->code,
                        'reference_id' => $transaction->reference_id,
                    ]
                ]
            );

            // Create payment request
            $paymentResponse = $this->createPaymentRequest($transaction);
            
            if (!$paymentResponse['success']) {
                throw new Exception($paymentResponse['message'] ?? 'خطا در ایجاد پرداخت');
            }

            $paymentData = $paymentResponse['data'];

            // Store payment ID in transaction metadata
            $transaction->update([
                'metadata' => array_merge($transaction->metadata ?? [], [
                    'payment_id' => $paymentData['paymentId'],
                    'psp_switching_url' => $paymentData['pspSwitchingUrl'],
                ])
            ]);

            // Log successful payment creation
            $this->logTransaction(
                $transaction,
                GatewayTransactionLog::ACTION_CREATED,
                [
                    'message' => 'Jibit payment created successfully',
                    'data' => [
                        'payment_id' => $paymentData['paymentId'],
                        'psp_switching_url' => $paymentData['pspSwitchingUrl'],
                        'amount' => $paymentData['amount'],
                        'reference_id' => $paymentData['referenceId'],
                    ]
                ]
            );

            return $this->successResponse([
                'payment_id' => $paymentData['paymentId'],
                'payment_url' => $paymentData['pspSwitchingUrl'],
                'amount' => $paymentData['amount'],
                'reference_id' => $paymentData['referenceId'],
                'status' => $paymentData['status'] ?? 'CREATED',
                'message' => 'Payment created successfully',
            ]);

        } catch (Exception $e) {
            // Log error
            $this->logTransaction(
                $transaction,
                GatewayTransactionLog::ACTION_FAILED,
                [
                    'message' => 'Jibit payment creation failed',
                    'error' => $e->getMessage(),
                ]
            );

            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Create payment request to Jibit PPG
     */
    protected function createPaymentRequest(GatewayTransaction $transaction): array
    {
        $callbackUrl = route('payment.callback', ['gateway' => 'jibit', 'transaction' => $transaction->uuid]);

        $requestData = [
            'amount' => $transaction->total_amount * 10,
            'currency' => 'IRR', // Jibit expects IRR
            'callbackUrl' => $callbackUrl,
            'clientReferenceNumber' => $transaction->reference_id ?? $transaction->uuid,
            'description' => $transaction->description ?? 'Payment via ' . config('app.name'),
            'payerId' => $transaction->user_id ?? 'guest',
            'payerName' => $transaction->user?->name ?? 'Guest User',
        ];

        if (!empty($transaction->user?->mobile)) {
            $requestData['payerMobileNumber'] = $transaction->user->mobile;
        }
        if (!empty($transaction->user?->email)) {
            $requestData['payerEmail'] = $transaction->user->email;
        }
        if (!empty($transaction->user?->national_code)) {
            $requestData['payerNationalCode'] = $transaction->user->national_code;
        }

        // Remove any fields with null values (just in case)
        $requestData = array_filter($requestData, function($v) { return $v !== null; });

        $apiUrl = $this->getCurrentApiUrl() . '/purchases';
        $response = $this->makeRequest('POST', $apiUrl, $requestData, [
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
            'Content-Type' => 'application/json',
        ]);

        if ($response['success'] && isset($response['data']['paymentId'])) {
            return $this->successResponse($response['data']);
        }

        $errorMessage = $response['data']['message'] ?? $response['error'] ?? 'خطای نامشخص از درگاه پرداخت';
        Log::error('Jibit Payment Creation Failed', [
            'status_code' => $response['status_code'] ?? 'unknown',
            'response' => $response['data'] ?? $response,
            'request' => $requestData,
        ]);

        return $this->errorResponse($errorMessage);
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
                    'message' => 'Jibit payment verification initiated',
                    'request_data' => $callbackData,
                ]
            );

            // Get payment status
            $paymentStatus = $this->getPaymentStatus($transaction);
            
            if (!$paymentStatus['success']) {
                throw new Exception($paymentStatus['message'] ?? 'خطا در دریافت وضعیت پرداخت');
            }

            $statusData = $paymentStatus;
            
            // Check if payment is successful
            if ($statusData['status'] !== 'SUCCESSFUL') {
                $errorMessage = $this->getStatusErrorMessage($statusData['status']);
                throw new Exception($errorMessage);
            }

            // Verify amount
            if ($statusData['amount'] != $transaction->total_amount) {
                throw new Exception('Amount mismatch');
            }

            // Verify reference ID
            if ($statusData['referenceId'] !== $transaction->reference_id) {
                throw new Exception('Reference ID mismatch');
            }

            // Log successful verification
            $this->logTransaction(
                $transaction,
                GatewayTransactionLog::ACTION_COMPLETED,
                [
                    'message' => 'Jibit payment verified successfully',
                    'data' => [
                        'payment_id' => $statusData['paymentId'],
                        'amount' => $statusData['amount'],
                        'status' => $statusData['status'],
                    ]
                ]
            );

            return $this->successResponse([
                'verified' => true,
                'payment_id' => $statusData['paymentId'],
                'amount' => $statusData['amount'],
                'status' => $statusData['status'],
                'message' => 'Payment verified successfully',
            ]);

        } catch (Exception $e) {
            // Log error
            $this->logTransaction(
                $transaction,
                GatewayTransactionLog::ACTION_FAILED,
                [
                    'message' => 'Jibit payment verification failed',
                    'error' => $e->getMessage(),
                ]
            );

            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get payment status from Jibit
     */
    public function getPaymentStatus(GatewayTransaction $transaction): array
    {
        $paymentId = $this->getPaymentId($transaction);
        
        if (!$paymentId) {
            return $this->errorResponse('Payment ID not found');
        }

        $response = $this->makeRequest('GET', $this->getCurrentApiUrl() . "/payments/{$paymentId}", [], [
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
            'Content-Type' => 'application/json',
        ]);

        if ($response['success'] && isset($response['data']['status'])) {
            return $this->successResponse($response['data']);
        }

        $errorMessage = $response['data']['message'] ?? $response['error'] ?? 'خطا در دریافت وضعیت پرداخت';
        return $this->errorResponse($errorMessage);
    }

    /**
     * Refund payment
     */
    public function refund(GatewayTransaction $transaction, int $amount = null): array
    {
        try {
            $paymentId = $this->getPaymentId($transaction);
            
            if (!$paymentId) {
                throw new Exception('Payment ID not found');
            }

            $refundAmount = $amount ?? $transaction->total_amount;

            $requestData = [
                'amount' => $refundAmount,
                'reason' => 'Refund requested by merchant',
            ];

            $response = $this->makeRequest('POST', $this->getCurrentApiUrl() . "/payments/{$paymentId}/refund", $requestData, [
                'Authorization' => 'Bearer ' . $this->getAccessToken(),
                'Content-Type' => 'application/json',
            ]);

            if ($response['success'] && isset($response['data']['refundId'])) {
                // Log successful refund
                $this->logTransaction(
                    $transaction,
                    GatewayTransactionLog::ACTION_REFUNDED,
                    [
                        'message' => 'Jibit payment refunded successfully',
                        'data' => [
                            'refund_id' => $response['data']['refundId'],
                            'amount' => $refundAmount,
                        ]
                    ]
                );

                return $this->successResponse([
                    'refunded' => true,
                    'refund_id' => $response['data']['refundId'],
                    'amount' => $refundAmount,
                    'message' => 'Payment refunded successfully',
                ]);
            }

            $errorMessage = $response['data']['message'] ?? $response['error'] ?? 'خطا در بازپرداخت';
            return $this->errorResponse($errorMessage);

        } catch (Exception $e) {
            // Log error
            $this->logTransaction(
                $transaction,
                GatewayTransactionLog::ACTION_FAILED,
                [
                    'message' => 'Jibit payment refund failed',
                    'error' => $e->getMessage(),
                ]
            );

            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get payment ID from transaction
     */
    protected function getPaymentId(GatewayTransaction $transaction): ?string
    {
        // Try to get from transaction metadata
        $metadata = $transaction->metadata ?? [];
        return $metadata['payment_id'] ?? null;
    }

    /**
     * Get access token for PPG API requests
     * This method handles JWT token generation and caching for PPG API
     */
    protected function getAccessToken(): string
    {
        // Try to get from cache first
        $cachedToken = Cache::get(self::PPG_TOKEN_CACHE_KEY);
        if ($cachedToken) {
            return $cachedToken;
        }

        // Try to refresh token first
        $token = $this->refreshPpgToken();
        if ($token) {
            return $token;
        }

        // If refresh fails, try to generate new token (respecting twice per day limit)
        $token = $this->generatePpgToken();
        if ($token) {
            return $token;
        }

        throw new Exception('Could not generate or refresh Jibit PPG access token. Please check your PPG API credentials.');
    }

    /**
     * Generate PPG access token using API key and secret (only for first time or when refresh fails)
     */
    protected function generatePpgToken(): ?string
    {
        try {
            // Check if we've already generated tokens twice today
            $today = now()->format('Y-m-d');
            $generationCount = Cache::get(self::PPG_TOKEN_GENERATION_COUNT_KEY . ':' . $today, 0);
            
            if ($generationCount >= 2) {
                Log::error('Jibit PPG token generation limit reached (twice per day)');
                throw new Exception('Token generation limit reached. Please try again tomorrow or contact support.');
            }

            $apiKey = $this->getConfig('api_key');
            $apiSecret = $this->getConfig('api_secret');

            if (empty($apiKey) || empty($apiSecret)) {
                Log::error('Jibit PPG API credentials not configured', [
                    'api_key_exists' => !empty($apiKey),
                    'api_secret_exists' => !empty($apiSecret),
                ]);
                return null;
            }

            $url = $this->getCurrentApiUrl() . '/token';
            $payload = [
                'apiKey' => $apiKey,
                'secretKey' => $apiSecret,
            ];

            // DEBUG: Log what we're sending
            Log::info('Jibit PPG Token Generation Debug', [
                'url' => $url,
                'payload' => $payload,
                'api_key_length' => strlen($apiKey),
                'api_secret_length' => strlen($apiSecret),
                'sandbox_mode' => $this->isSandbox(),
            ]);

            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post($url, $payload);

            // DEBUG: Log the response
            Log::info('Jibit PPG Token Generation Response', [
                'status' => $response->status(),
                'headers' => $response->headers(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['accessToken']) && isset($data['refreshToken'])) {
                    // Increment generation count
                    Cache::put(self::PPG_TOKEN_GENERATION_COUNT_KEY . ':' . $today, $generationCount + 1, now()->endOfDay());
                    
                    // Cache both tokens
                    Cache::put(self::PPG_TOKEN_CACHE_KEY, $data['accessToken'], self::TOKEN_CACHE_TTL);
                    Cache::put(self::PPG_REFRESH_TOKEN_CACHE_KEY, $data['refreshToken'], self::REFRESH_TOKEN_CACHE_TTL);
                    
                    Log::info('Jibit PPG access token generated successfully');
                    return $data['accessToken'];
                }
            }

            $errorMsg = 'Failed to generate Jibit PPG access token. Status: ' . $response->status() . ' Body: ' . $response->body();
            Log::error($errorMsg);
            throw new Exception($errorMsg);

        } catch (Exception $e) {
            Log::error('Error generating Jibit PPG access token: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Refresh PPG access token using refresh token
     */
    protected function refreshPpgToken(): ?string
    {
        try {
            $refreshToken = Cache::get(self::PPG_REFRESH_TOKEN_CACHE_KEY);
            
            if (empty($refreshToken)) {
                Log::info('No refresh token available, will generate new token');
                return null;
            }

            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post($this->getCurrentApiUrl() . '/token/refresh', [
                    'refreshToken' => $refreshToken,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['accessToken']) && isset($data['refreshToken'])) {
                    // Cache both new tokens
                    Cache::put(self::PPG_TOKEN_CACHE_KEY, $data['accessToken'], self::TOKEN_CACHE_TTL);
                    Cache::put(self::PPG_REFRESH_TOKEN_CACHE_KEY, $data['refreshToken'], self::REFRESH_TOKEN_CACHE_TTL);
                    
                    Log::info('Jibit PPG access token refreshed successfully');
                    return $data['accessToken'];
                }
            }

            // If refresh fails, clear the refresh token and return null
            Cache::forget(self::PPG_REFRESH_TOKEN_CACHE_KEY);
            Log::warning('Failed to refresh Jibit PPG token, will generate new one. Status: ' . $response->status() . ' Body: ' . $response->body());
            return null;

        } catch (Exception $e) {
            Log::error('Error refreshing Jibit PPG access token: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get status error message
     */
    protected function getStatusErrorMessage(string $status): string
    {
        $messages = [
            'FAILED' => 'پرداخت ناموفق بود',
            'CANCELLED' => 'پرداخت لغو شد',
            'EXPIRED' => 'پرداخت منقضی شد',
            'PENDING' => 'پرداخت در انتظار است',
            'REJECTED' => 'پرداخت رد شد',
            'IN_PROGRESS' => 'پرداخت در حال انجام است',
            'READY_TO_VERIFY' => 'پرداخت آماده تایید است',
            'UNKNOWN' => 'وضعیت پرداخت نامشخص',
        ];

        return $messages[$status] ?? 'وضعیت پرداخت نامشخص';
    }

    /**
     * Get configuration requirements
     */
    public function getConfigRequirements(): array
    {
        return [
            'api_key',
            'api_secret',
            'sandbox',
        ];
    }

    /**
     * Get supported currencies
     */
    public function getSupportedCurrencies(): array
    {
        return ['IRT', 'USD', 'EUR'];
    }

    /**
     * Get amount limits
     */
    public function getAmountLimits(): array
    {
        return [
            'min' => 1000, // 1,000 IRT
            'max' => 500000000, // 500,000,000 IRT
        ];
    }

    /**
     * Get callback URL
     */
    public function getCallbackUrl(): string
    {
        return route('payment.callback', ['gateway' => 'jibit']);
    }

    /**
     * Get gateway information
     */
    public function getGatewayInfo(): array
    {
        return [
            'name' => 'Jibit Payment Gateway',
            'version' => '3.0',
            'supports_refund' => true,
            'supports_partial_refund' => true,
            'supports_webhook' => true,
            'payment_methods' => ['CARD', 'WALLET', 'BANK_TRANSFER'],
        ];
    }
} 