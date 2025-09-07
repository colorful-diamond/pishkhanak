<?php

namespace App\Jobs;

use App\Services\LocalRequestService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class ProcessLocalRequestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 600; // 10 minutes timeout 
    public int $tries = 1; // No retries - handle failures gracefully
    public int $maxExceptions = 1; // Allow 1 exception before failing

    private string $requestHash;
    private LocalRequestService $localRequestService;

    /**
     * Create a new job instance.
     */
    public function __construct(string $requestHash)
    {
        $this->requestHash = $requestHash;
        $this->localRequestService = app(LocalRequestService::class);
    }

    /**
     * Execute the job with continuous processing including OTP handling.
     */
    public function handle(): void
    {
        try {
            $localRequest = $this->localRequestService->getRequest($this->requestHash);

            if (!$localRequest) {
                Log::error('Local request not found for processing', ['hash' => $this->requestHash]);
                return;
            }

            Log::info('Processing local request job started', [
                'hash' => $this->requestHash,
                'service_slug' => $localRequest['service_slug']
            ]);

            // Update status to processing
            $this->localRequestService->updateProgress(
                $this->requestHash, 
                10, 
                'authentication', 
                'شروع پردازش درخواست...'
            );

            // Call the local API server - bot now handles complete flow internally
            $result = $this->callLocalApiServer($localRequest);

            Log::info('Bot result received', [
                'hash' => $this->requestHash,
                'result_status' => $result['status'] ?? 'unknown',
                'result_code' => $result['code'] ?? 'no_code',
                'has_data' => isset($result['data']),
                'message' => $result['message'] ?? 'no_message'
            ]);

            // Bot handles the complete flow and returns final result
            $this->processApiResult($result);

        } catch (Exception $e) {
            Log::error('Error in ProcessLocalRequestJob', [
                'hash' => $this->requestHash,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->localRequestService->markAsFailed(
                $this->requestHash,
                'خطا در پردازش درخواست: ' . $this->errorMessageHandler($e->getMessage()),
                ['exception' => $e->getMessage()]
            );
        }
    }

    // Note: Bot now handles complete SMS + OTP + verification flow internally
    // No need for separate handleSmsOtpFlow, pollForOtp, or continueWithOtpVerification methods

    /**
     * Call the local API server
     */
    private function callLocalApiServer(array $localRequest, ?array $customData = null): array
    {
        $url = config('services.local_api.url', 'http://127.0.0.1:9999');
        $endpoint = "{$url}/api/services/{$localRequest['service_slug']}";

        // Use custom data if provided (for OTP verification), otherwise use original request data
        $requestData = $customData ?? array_merge($localRequest['request_data'], [
            'requestHash' => $this->requestHash
        ]);

        Log::info('Calling local API server', [
            'endpoint' => $endpoint,
            'hash' => $this->requestHash,
            'has_otp' => isset($requestData['otp']),
            'data_keys' => array_keys($requestData)
        ]);

        // Update progress before calling API
        if (!isset($requestData['otp'])) {
            $this->localRequestService->updateProgress(
                $this->requestHash, 
                30, 
                'authentication', 
                'ارسال درخواست به درگاه دولت هوشمند...'
            );
        }

        // Make the HTTP request with timeout  
        $response = Http::timeout(600) // 7 minutes timeout (longer for OTP flow)
            ->post($endpoint, $requestData);

        if (!$response->successful()) {
            throw new Exception("Local API returned status {$response->status()}: {$response->body()}");
        }

        $result = $response->json();

        Log::info('Local API server response received', [
            'hash' => $this->requestHash,
            'status' => $result['status'] ?? 'unknown',
            'code' => $result['code'] ?? 'no_code',
            'has_data' => isset($result['data'])
        ]);

        return $result;
    }

    /**
     * Process API result and mark request as completed or failed
     */
    private function processApiResult(array $result): void
    {
        // Check if this is a transparent return (same request detection)
        if (isset($result['_transparent_return']) && $result['_transparent_return'] === true) {
            Log::info('Transparent return detected - updating progress instead of completing', [
                'hash' => $this->requestHash,
                'status' => $result['status'] ?? 'unknown',
                'progress' => $result['progress'] ?? 0
            ]);
            
            // Update the request progress with current status
            $this->handleTransparentReturn($result);
            return;
        }
        
        if ($result['status'] === 'success') {
            // Process payment and create service result
            $this->processSuccessfulResult($result);

        } else {
            // Handle different error types
            $code = $result['code'] ?? 'UNKNOWN_ERROR';
            $this->handleErrorResult($result, $code);
        }
    }

    /**
     * Handle transparent return (same request detection) by updating progress
     */
    private function handleTransparentReturn(array $result): void
    {
        // Update the request with current progress and status
        $progress = $result['progress'] ?? 0;
        $step = $result['step'] ?? 'processing';
        $message = $result['message'] ?? 'در حال پردازش...';
        $status = $result['status'] ?? 'pending';
        
        // Update progress in Redis
        $this->localRequestService->updateProgress(
            $this->requestHash,
            $progress,
            $step,
            $message
        );
        
        // Handle special cases
        if (isset($result['requires_otp']) && $result['requires_otp'] && isset($result['otp_data'])) {
            // Mark request as requiring OTP
            $this->localRequestService->markAsOtpRequired($this->requestHash, $result['otp_data']);
        }
        
        if (isset($result['is_completed']) && $result['is_completed']) {
            // Mark request as completed with result data
            $resultData = $result['result_data'] ?? $result;
            $this->localRequestService->markAsCompleted($this->requestHash, $resultData);
        }
        
        if (isset($result['is_failed']) && $result['is_failed']) {
            // Mark request as failed with error data
            $errorData = $result['error_data'] ?? $result;
            $this->localRequestService->markAsFailed($this->requestHash, $errorData);
        }
        
        Log::info('Transparent return processed successfully', [
            'hash' => $this->requestHash,
            'status' => $status,
            'progress' => $progress,
            'step' => $step
        ]);
    }

    /**
     * Process successful result with payment and service result creation
     */
    private function processSuccessfulResult(array $result): void
    {
        Log::info('Starting processSuccessfulResult', [
            'hash' => $this->requestHash,
            'result_keys' => array_keys($result)
        ]);

        try {
            $localRequest = $this->localRequestService->getRequest($this->requestHash);
            
            if (!$localRequest) {
                throw new Exception('Local request not found for payment processing');
            }

            // Update progress to final processing
            $this->localRequestService->updateProgress(
                $this->requestHash, 
                90, 
                'completed', 
                'پردازش پرداخت...'
            );

            // Get service and user information
            $service = \App\Models\Service::find($localRequest['service_id']);
            $user = $localRequest['user_id'] ? \App\Models\User::find($localRequest['user_id']) : null;

            if (!$service) {
                throw new Exception('Service not found for payment processing');
            }

            // Create service result first
            $serviceResult = \App\Models\ServiceResult::create([
                'service_id' => $service->id,
                'user_id' => $user?->id,
                'input_data' => $localRequest['request_data'],
                'output_data' => $result,
                'status' => 'success',
                'processed_at' => now(),
                'ip_address' => $localRequest['ip_address'] ?? null,
                'user_agent' => $localRequest['user_agent'] ?? null,
            ]);

            Log::info('Service result created', [
                'hash' => $this->requestHash,
                'result_hash' => $serviceResult->result_hash,
                'service_id' => $service->id,
                'user_id' => $user?->id
            ]);

            // Process payment if user is authenticated
            if ($user && $service->price > 0) {
                try {
                    // Check if user has sufficient balance
                    if ($user->balance < $service->price) {
                        throw new Exception('Insufficient balance for service payment');
                    }

                    // Deduct payment using Bavix wallet system
                    $user->withdraw($service->price, [
                        'description' => "پرداخت سرویس: {$service->title}",
                        'service_id' => $service->id,
                        'service_title' => $service->title,
                        'type' => 'service_payment',
                        'payment_source' => $service->slug,
                        'payment_method' => 'wallet',
                        'processed_at' => now()->toISOString(),
                        'result_hash' => $serviceResult->result_hash,
                        'source_tracking' => [
                            'source_type' => 'service',
                            'source_id' => $service->id,
                            'source_title' => $service->title,
                            'service_slug' => $service->slug,
                            'payment_flow' => 'wallet_to_service',
                            'user_type' => 'authenticated',
                            'transaction_context' => 'background_service_payment',
                            'request_hash' => $this->requestHash
                        ]
                    ]);

                    Log::info('Payment processed successfully', [
                        'hash' => $this->requestHash,
                        'user_id' => $user->id,
                        'service_id' => $service->id,
                        'amount' => $service->price,
                        'result_hash' => $serviceResult->result_hash
                    ]);

                } catch (Exception $e) {
                    Log::error('Payment processing failed', [
                        'hash' => $this->requestHash,
                        'user_id' => $user->id,
                        'service_id' => $service->id,
                        'amount' => $service->price,
                        'error' => $e->getMessage()
                    ]);

                    // Mark service result as payment failed but keep the result
                    $serviceResult->update([
                        'status' => 'payment_failed',
                        'output_data' => array_merge($result, [
                            'payment_error' => $e->getMessage()
                        ])
                    ]);

                    // Continue with completion but note payment failure
                    $result['payment_status'] = 'failed';
                    $result['payment_error'] = $e->getMessage();
                }
            }

            // Update progress to completed
            $this->localRequestService->updateProgress(
                $this->requestHash, 
                100, 
                'completed', 
                'پردازش با موفقیت کامل شد'
            );

            // Determine result URL based on service type and result code
            $resultUrl = $this->determineResultUrl($service, $serviceResult, $result);

            // Mark as completed with result data
            $completionData = [
                'message' => $result['message'] ?? 'عملیات با موفقیت انجام شد',
                'data' => $result['data'] ?? [],
                'code' => $result['code'] ?? 'success',
                'service_result_hash' => $serviceResult->result_hash,
                'result_url' => $resultUrl,
                'completion_type' => $this->getCompletionType($result),
                'payment_status' => $result['payment_status'] ?? 'success'
            ];

            Log::info('About to mark request as completed', [
                'hash' => $this->requestHash,
                'service_result_hash' => $serviceResult->result_hash,
                'result_url' => $resultUrl,
                'completion_data_keys' => array_keys($completionData)
            ]);

            $this->localRequestService->markAsCompleted($this->requestHash, $completionData);

            Log::info('Request completed successfully with payment processing', [
                'hash' => $this->requestHash,
                'code' => $result['code'] ?? 'success',
                'result_hash' => $serviceResult->result_hash,
                'payment_processed' => $user && $service->price > 0,
                'result_url' => $resultUrl
            ]);

        } catch (Exception $e) {
            Log::error('Error in successful result processing', [
                'hash' => $this->requestHash,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Mark as failed
            $this->localRequestService->markAsFailed(
                $this->requestHash,
                'خطا در پردازش نتیجه: ' . $e->getMessage(),
                ['error_type' => 'result_processing_error', 'exception' => $e->getMessage()]
            );
        }
    }

    /**
     * Determine the appropriate result URL based on service and result type
     */
    private function determineResultUrl($service, $serviceResult, array $result): string
    {
        // Check if this is an SMS-type result
        if (isset($result['code']) && $result['code'] === 'CREDIT_SCORE_SMS_SENT') {
            return route('services.progress.sms-result', [
                'service' => $service->slug,
                'id' => $serviceResult->result_hash
            ]);
        }

        // Default service result URL
        return route('services.result', ['id' => $serviceResult->result_hash]);
    }

    /**
     * Get completion type based on result
     */
    private function getCompletionType(array $result): string
    {
        if (isset($result['code'])) {
            switch ($result['code']) {
                case 'CREDIT_SCORE_SMS_SENT':
                    return 'sms_sent';
                case 'CREDIT_SCORE_COMPLETED':
                    return 'transaction_result';
                default:
                    return 'standard';
            }
        }

        return isset($result['data']['transactionId']) ? 'transaction_result' : 'standard';
    }

    /**
     * Handle error results with retry logic for specific error types
     */
    private function handleErrorResult(array $result, string $code): void
    {
        $errorMessage = $result['message'] ?? 'خطا در پردازش درخواست';

        $retryableCodes = [
            'CAPTCHA_SOLVING_FAILED',
            'MAX_RETRIES_REACHED',
            'TIMEOUT',
            'NETWORK_ERROR',
            'SERVICE_UNAVAILABLE'
        ];

        // OTP timeout is a special case - mark as completed with timeout message instead of failed
        if ($code === 'OTP_TIMEOUT') {
            $this->localRequestService->markAsCompleted(
                $this->requestHash,
                [
                    'status' => 'timeout',
                    'code' => 'OTP_TIMEOUT',
                    'message' => 'زمان انتظار کد تایید به پایان رسید. لطفاً دوباره تلاش کنید.',
                    'data' => [
                        'timeout_reason' => 'User did not submit OTP within time limit',
                        'can_retry' => true
                    ]
                ]
            );

            Log::info('Request completed with OTP timeout', [
                'hash' => $this->requestHash,
                'code' => $code,
                'message' => $errorMessage
            ]);
            return;
        }

        if (in_array($code, $retryableCodes) && $this->attempts() < $this->tries) {
            // Retry the job for retryable errors
            Log::info('Retrying job due to retryable error', [
                'hash' => $this->requestHash,
                'code' => $code,
                'attempt' => $this->attempts()
            ]);

            $this->localRequestService->updateProgress(
                $this->requestHash, 
                20, 
                'initializing', 
                'تلاش مجدد پردازش درخواست...'
            );

            throw new Exception("Retryable error: {$errorMessage}");
        }

        // Mark as failed for non-retryable errors or max retries reached
        $this->localRequestService->markAsFailed(
            $this->requestHash,
            $errorMessage,
            [
                'code' => $code,
                'full_response' => $result,
                'attempts' => $this->attempts()
            ]
        );

        Log::error('Request failed', [
            'hash' => $this->requestHash,
            'code' => $code,
            'message' => $errorMessage,
            'attempts' => $this->attempts()
        ]);
    }

    /**
     * Convert technical error messages to user-friendly Persian messages
     */
    private function errorMessageHandler(string $error): string
    {
        $errorMappings = [
            'timeout' => 'درخواست بیش از حد معمول طول کشید',
            'connection' => 'سرویس در حال به روز رسانی است',
            'captcha' => 'خطا در تشخیص کپچا',
            'network' => 'خطا در شبکه', 
            'service unavailable' => 'سرویس موقتاً در دسترس نیست',
            'invalid response' => 'پاسخ نامعتبر از سرور',
        ];

        foreach ($errorMappings as $key => $message) {
            if (stripos($error, $key) !== false) {
                return $message;
            }
        }

        return 'سامانه دولت هوشمند در حال حاضر در دسترس نیست، لطفا بعد از مدتی تلاش کنید.';
    }

    /**
     * Handle job failure
     */
    public function failed(Exception $exception): void
    {
        Log::error('ProcessLocalRequestJob failed permanently', [
            'hash' => $this->requestHash,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts()
        ]);

        // No need to send control messages with polling approach

        $this->localRequestService->markAsFailed(
            $this->requestHash,
            'خطا بعد از ۳ تلاش ناموفق: ' . $this->errorMessageHandler($exception->getMessage()),
            [
                'exception' => $exception->getMessage(),
                'final_attempt' => true,
                'attempts' => $this->attempts()
            ]
        );
    }
} 