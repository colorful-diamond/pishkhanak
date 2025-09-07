<?php

namespace App\Http\Controllers;

use App\Services\PaymentService;
use App\Services\PaymentGatewayManager;
use App\Models\GatewayTransaction;
use App\Models\GatewayTransactionLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class WebhookController extends Controller
{
    protected PaymentService $paymentService;
    protected PaymentGatewayManager $gatewayManager;

    public function __construct(PaymentService $paymentService, PaymentGatewayManager $gatewayManager)
    {
        $this->paymentService = $paymentService;
        $this->gatewayManager = $gatewayManager;
    }

    /**
     * Handle webhook from payment gateway
     */
    public function handleWebhook(Request $request, string $gatewaySlug): JsonResponse
    {
        $startTime = microtime(true);
        $requestId = uniqid('webhook_', true);
        
        try {
            // Rate limiting for webhooks
            $cacheKey = 'webhook_rate_limit_' . $request->ip();
            $attempts = Cache::get($cacheKey, 0);
            
            if ($attempts >= 100) { // Max 100 requests per minute
                Log::warning('Webhook rate limit exceeded', [
                    'ip' => $request->ip(),
                    'gateway' => $gatewaySlug,
                    'request_id' => $requestId,
                ]);
                
                return response()->json(['error' => 'Rate limit exceeded'], 429);
            }
            
            Cache::put($cacheKey, $attempts + 1, 60); // 1 minute

            // Log incoming webhook
            Log::info('Webhook received', [
                'gateway' => $gatewaySlug,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'headers' => $this->sanitizeHeaders($request->headers->all()),
                'payload_size' => strlen($request->getContent()),
                'request_id' => $requestId,
            ]);

            // Validate gateway exists and is active
            $gateway = $this->gatewayManager->gatewayBySlug($gatewaySlug);
            if (!$gateway) {
                Log::error('Webhook for unknown gateway', [
                    'gateway' => $gatewaySlug,
                    'request_id' => $requestId,
                ]);
                
                return response()->json(['error' => 'Gateway not found'], 404);
            }

            // Get webhook data
            $webhookData = $this->getWebhookData($request);
            
            // Extract transaction identifier from webhook
            $transactionIdentifier = $this->extractTransactionIdentifier($webhookData, $gatewaySlug);
            
            if (!$transactionIdentifier) {
                Log::error('Cannot extract transaction identifier from webhook', [
                    'gateway' => $gatewaySlug,
                    'data' => $webhookData,
                    'request_id' => $requestId,
                ]);
                
                return response()->json(['error' => 'Invalid webhook data'], 400);
            }

            // Find transaction
            $transaction = $this->findTransaction($transactionIdentifier, $gatewaySlug);
            
            if (!$transaction) {
                Log::error('Transaction not found for webhook', [
                    'gateway' => $gatewaySlug,
                    'identifier' => $transactionIdentifier,
                    'request_id' => $requestId,
                ]);
                
                return response()->json(['error' => 'Transaction not found'], 404);
            }

            // Log webhook reception for transaction
            $responseTime = round((microtime(true) - $startTime) * 1000);
            
            $transaction->addLog(
                GatewayTransactionLog::ACTION_WEBHOOK_RECEIVED,
                GatewayTransactionLog::SOURCE_WEBHOOK,
                [
                    'message' => 'Webhook received from ' . $gatewaySlug,
                    'request_data' => $webhookData,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'method' => $request->method(),
                    'url' => $request->fullUrl(),
                    'headers' => $this->sanitizeHeaders($request->headers->all()),
                    'response_time_ms' => $responseTime,
                    'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
                    'data' => [
                        'request_id' => $requestId,
                        'gateway_slug' => $gatewaySlug,
                        'transaction_identifier' => $transactionIdentifier,
                    ],
                ]
            );

            // Process webhook with gateway-specific logic
            $result = $this->processWebhookByGateway($transaction, $webhookData, $gatewaySlug);

            // Update transaction status based on webhook result
            if ($result['success']) {
                if ($result['status'] === 'completed') {
                    $transaction->markAsCompleted();
                    $transaction->update([
                        'gateway_reference' => $result['reference_id'] ?? null,
                        'gateway_response' => array_merge($transaction->gateway_response ?? [], $result),
                    ]);
                } elseif ($result['status'] === 'failed') {
                    $transaction->markAsFailed($result['reason'] ?? 'خطا در تأیید پرداخت');
                }

                Log::info('Webhook processed successfully', [
                    'transaction_id' => $transaction->id,
                    'gateway' => $gatewaySlug,
                    'status' => $result['status'],
                    'request_id' => $requestId,
                ]);

                return response()->json(['success' => true, 'status' => $result['status']]);
            }

            Log::error('Webhook processing failed', [
                'transaction_id' => $transaction->id,
                'gateway' => $gatewaySlug,
                'error' => $result['error'] ?? 'خطای نامشخص',
                'request_id' => $requestId,
            ]);

            return response()->json(['error' => $result['error'] ?? 'Processing failed'], 400);

        } catch (\Exception $e) {
            $responseTime = round((microtime(true) - $startTime) * 1000);
            
            Log::error('Webhook processing exception', [
                'gateway' => $gatewaySlug,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_id' => $requestId,
                'response_time_ms' => $responseTime,
            ]);

            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Get webhook data from request
     */
    protected function getWebhookData(Request $request): array
    {
        $contentType = $request->header('Content-Type', '');
        
        if (str_contains($contentType, 'application/json')) {
            return $request->json()->all();
        }
        
        return $request->all();
    }

    /**
     * Extract transaction identifier from webhook data
     */
    protected function extractTransactionIdentifier(array $webhookData, string $gatewaySlug): ?string
    {
        // Gateway-specific transaction identifier extraction
        return match($gatewaySlug) {
            'asanpardakht' => $webhookData['transaction_id'] ?? $webhookData['refNum'] ?? null,
            'zarinpal' => $webhookData['authority'] ?? null,
            'mellat' => $webhookData['RefId'] ?? $webhookData['SaleOrderId'] ?? null,
            'parsian' => $webhookData['token'] ?? $webhookData['OrderId'] ?? null,
            default => $webhookData['transaction_id'] ?? $webhookData['order_id'] ?? $webhookData['ref_id'] ?? null,
        };
    }

    /**
     * Find transaction by identifier
     */
    protected function findTransaction(string $identifier, string $gatewaySlug): ?GatewayTransaction
    {
        // Try to find by UUID first
        $transaction = GatewayTransaction::where('uuid', $identifier)->first();
        
        if ($transaction) {
            return $transaction;
        }

        // Try to find by gateway transaction ID
        $transaction = GatewayTransaction::where('gateway_transaction_id', $identifier)->first();
        
        if ($transaction) {
            return $transaction;
        }

        // Try to find by gateway reference
        return GatewayTransaction::where('gateway_reference', $identifier)->first();
    }

    /**
     * Process webhook with gateway-specific logic
     */
    protected function processWebhookByGateway(GatewayTransaction $transaction, array $webhookData, string $gatewaySlug): array
    {
        try {
            $gatewayInstance = $this->gatewayManager->gatewayBySlug($gatewaySlug);
            
            // Use gateway's verification method
            $result = $gatewayInstance->verifyPayment($transaction, $webhookData);
            
            if ($result['success'] && ($result['verified'] ?? false)) {
                return [
                    'success' => true,
                    'status' => 'completed',
                    'reference_id' => $result['reference_id'] ?? null,
                    'amount' => $result['amount'] ?? null,
                ];
            }
            
            return [
                'success' => true,
                'status' => 'failed',
                'reason' => $result['message'] ?? 'خطا در تأیید پرداخت',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Sanitize headers for logging (remove sensitive data)
     */
    protected function sanitizeHeaders(array $headers): array
    {
        $sensitiveHeaders = ['authorization', 'x-api-key', 'x-auth-token', 'cookie'];
        
        foreach ($headers as $key => $value) {
            if (in_array(strtolower($key), $sensitiveHeaders)) {
                $headers[$key] = '[REDACTED]';
            }
        }
        
        return $headers;
    }

    /**
     * Health check endpoint for gateway monitoring
     */
    public function healthCheck(): JsonResponse
    {
        try {
            // Check database connection
            $pendingCount = GatewayTransaction::whereIn('status', ['pending', 'processing'])->count();
            
            // Check cache
            $cacheWorking = Cache::put('health_check', time(), 10) && Cache::get('health_check');
            
            return response()->json([
                'status' => 'healthy',
                'timestamp' => now()->toISOString(),
                'pending_transactions' => $pendingCount,
                'cache_working' => $cacheWorking,
                'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString(),
            ], 503);
        }
    }
} 