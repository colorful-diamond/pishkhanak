<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class LocalRequestService
{
    private string $redisPrefix;
    private string $listKey; // For tracking all requests
    private int $redisTtl = 1800; // 30 minutes

    public function __construct()
    {
        // Laravel's Redis facade automatically adds the prefix from config
        // We only need our specific prefix since Laravel adds the global prefix automatically
        $this->redisPrefix = 'local_request:';
        $this->listKey = 'local_requests_list';
    }

    /**
     * Create a new local request and store in Redis only
     */
    public function createRequest(
        string $serviceSlug, 
        int $serviceId, 
        array $requestData, 
        ?int $userId = null, 
        ?string $sessionId = null,
        int $estimatedDuration = 300,
        ?string $ipAddress = null,
        ?string $userAgent = null
    ): array {
        
        $hash = $this->generateUniqueHash();
        $now = Carbon::now();
        
        $localRequest = [
            'hash' => $hash,
            'service_slug' => $serviceSlug,
            'service_id' => $serviceId,
            'user_id' => $userId,
            'session_id' => $sessionId,
            'request_data' => $requestData,
            'status' => 'pending',
            'step' => 'initializing',
            'progress' => 0,
            'current_message' => 'شروع پردازش درخواست...',
            'estimated_duration' => $estimatedDuration,
            'started_at' => $now->toISOString(),
            'completed_at' => null,
            'otp_data' => null,
            'result_data' => null,
            'error_data' => null,
            'ip_address' => $ipAddress ?? request()?->ip(),
            'user_agent' => $userAgent ?? request()?->userAgent(),
            'expires_at' => $now->addMinutes(30)->toISOString(),
            'created_at' => $now->toISOString(),
            'updated_at' => $now->toISOString()
        ];

        // Store in Redis with TTL
        $redisKey = $this->redisPrefix . $hash;
        Redis::setex($redisKey, $this->redisTtl, json_encode($localRequest));

        // Add to list for tracking (with TTL)
        Redis::lpush($this->listKey, $hash);
        Redis::expire($this->listKey, $this->redisTtl);

        // Publish creation event
        $channelName = "local_request_updates:{$hash}";
        Redis::publish($channelName, json_encode($this->formatRequestData($localRequest)));

        Log::info('Local request created in Redis', [
            'hash' => $hash,
            'service_slug' => $serviceSlug,
            'user_id' => $userId
        ]);

        return $localRequest;
    }

    /**
     * Update request progress in Redis
     */
    public function updateProgress(
        string $hash, 
        int $progress, 
        ?string $step = null, 
        ?string $message = null
    ): bool {
        
        $localRequest = $this->getRequest($hash);
        
        if (!$localRequest) {
            Log::warning('Local request not found for progress update', ['hash' => $hash]);
            return false;
        }

        // Update fields
        $localRequest['progress'] = min(100, max(0, $progress));
        if ($step) $localRequest['step'] = $step;
        if ($message) $localRequest['current_message'] = $message;
        $localRequest['updated_at'] = Carbon::now()->toISOString();

        // Save back to Redis
        $this->saveRequest($hash, $localRequest);
        $this->publishUpdate($hash, $localRequest);

        return true;
    }

    /**
     * Mark request as requiring OTP and prepare for pub/sub communication
     */
    public function markAsOtpRequired(string $hash, array $otpData): bool
    {
        $localRequest = $this->getRequest($hash);
        
        if (!$localRequest) {
            Log::warning('Local request not found for OTP marking', ['hash' => $hash]);
            return false;
        }

        $localRequest['status'] = 'otp_required';
        $localRequest['step'] = 'waiting_otp';
        $localRequest['otp_data'] = $otpData;
        $localRequest['progress'] = 70;
        $localRequest['current_message'] = 'در انتظار دریافت کد تایید';
        $localRequest['updated_at'] = Carbon::now()->toISOString();

        $this->saveRequest($hash, $localRequest);
        $this->publishUpdate($hash, $localRequest);

        return true;
    }

    /**
     * Publish OTP to Redis channel for background job to receive
     */
    public function publishOtp(string $hash, string $otp, array $additionalData = []): bool
    {
        try {
            // Store OTP directly in Redis for the job to pick up
            $otpData = array_merge([
                'hash' => $hash,
                'otp' => $otp,
                'timestamp' => Carbon::now()->toISOString(),
                'source' => 'frontend'
            ], $additionalData);

            // Store OTP data directly in the request
            $localRequest = $this->getRequest($hash);
            if ($localRequest) {
                $localRequest['received_otp'] = $otpData;
                $localRequest['step'] = 'otp_submitted';
                $localRequest['progress'] = 75;
                $localRequest['current_message'] = 'کد تایید دریافت شد، در حال پردازش...';
                $localRequest['updated_at'] = Carbon::now()->toISOString();
                
                $this->saveRequest($hash, $localRequest);
                $this->publishUpdate($hash, $localRequest);
            }

            Log::info('OTP stored for background processing', [
                'hash' => $hash,
                'otp_length' => strlen($otp)
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error storing OTP for background processing', [
                'hash' => $hash,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Check if OTP has been submitted by user
     */
    public function hasReceivedOtp(string $hash): ?array
    {
        try {
            $localRequest = $this->getRequest($hash);
            
            if (!$localRequest) {
                return null;
            }

            return $localRequest['received_otp'] ?? null;
        } catch (\Exception $e) {
            Log::error('Error checking for received OTP', [
                'hash' => $hash,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Clear received OTP after processing
     */
    public function clearReceivedOtp(string $hash): bool
    {
        try {
            $localRequest = $this->getRequest($hash);
            
            if ($localRequest && isset($localRequest['received_otp'])) {
                unset($localRequest['received_otp']);
                $localRequest['updated_at'] = Carbon::now()->toISOString();
                
                $this->saveRequest($hash, $localRequest);
                
                Log::info('Cleared received OTP after processing', ['hash' => $hash]);
                return true;
            }
            
            return false;
        } catch (\Exception $e) {
            Log::error('Error clearing received OTP', [
                'hash' => $hash,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Mark request as completed
     */
    public function markAsCompleted(string $hash, array $resultData): bool
    {
        $localRequest = $this->getRequest($hash);
        
        if (!$localRequest) {
            Log::warning('Local request not found for completion', ['hash' => $hash]);
            return false;
        }

        $localRequest['status'] = 'completed';
        $localRequest['step'] = 'completed';
        $localRequest['result_data'] = $resultData;
        $localRequest['progress'] = 100;
        $localRequest['current_message'] = 'پردازش با موفقیت کامل شد';
        $localRequest['completed_at'] = Carbon::now()->toISOString();
        $localRequest['updated_at'] = Carbon::now()->toISOString();

        $this->saveRequest($hash, $localRequest);
        $this->publishUpdate($hash, $localRequest);

        return true;
    }

    /**
     * Mark request as failed
     */
    public function markAsFailed(string $hash, string $errorMessage, array $errorData = []): bool
    {
        $localRequest = $this->getRequest($hash);
        
        if (!$localRequest) {
            Log::warning('Local request not found for failure marking', ['hash' => $hash]);
            return false;
        }

        $localRequest['status'] = 'failed';
        $localRequest['error_data'] = array_merge($errorData, ['message' => $errorMessage]);
        $localRequest['current_message'] = $errorMessage;
        $localRequest['completed_at'] = Carbon::now()->toISOString();
        $localRequest['updated_at'] = Carbon::now()->toISOString();

        $this->saveRequest($hash, $localRequest);
        $this->publishUpdate($hash, $localRequest);

        return true;
    }

    /**
     * Get request status from Redis
     */
    public function getRequestStatus(string $hash): ?array
    {
        $localRequest = $this->getRequest($hash);
        
        if (!$localRequest) {
            return null;
        }

        return $this->formatRequestData($localRequest);
    }

    /**
     * Get raw request data from Redis
     */
    public function getRequest(string $hash): ?array
    {
        try {
            $redisKey = $this->redisPrefix . $hash;
            $data = Redis::get($redisKey);
            
            if ($data) {
                return json_decode($data, true);
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('Error getting request from Redis', [
                'hash' => $hash,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Save request to Redis
     */
    private function saveRequest(string $hash, array $localRequest): void
    {
        $redisKey = $this->redisPrefix . $hash;
        Redis::setex($redisKey, $this->redisTtl, json_encode($localRequest));
    }

    /**
     * Update the last remaining time for a request
     */
    private function updateLastRemainingTime(string $hash, int $remainingTime): bool
    {
        try {
            $localRequest = $this->getRequest($hash);
            if (!$localRequest) {
                return false;
            }
            
            $localRequest['last_remaining_time'] = $remainingTime;
            $this->saveRequest($hash, $localRequest);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to update last remaining time', [
                'hash' => $hash,
                'remaining_time' => $remainingTime,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Publish update to Redis channel
     */
    private function publishUpdate(string $hash, array $localRequest): void
    {
        $channelName = "local_request_updates:{$hash}";
        Redis::publish($channelName, json_encode($this->formatRequestData($localRequest)));
    }

    /**
     * Format request data for API response
     */
    private function formatRequestData(array $localRequest): array
    {
        $now = Carbon::now();
        $startedAt = isset($localRequest['started_at']) ? Carbon::parse($localRequest['started_at']) : $now;
        $estimatedDuration = $localRequest['estimated_duration'] ?? 300;
        
        // Calculate remaining time - subtract elapsed time from estimated duration
        $elapsed = $startedAt->diffInSeconds($now); // Fixed: startedAt->diffInSeconds(now) gives positive elapsed time
        $calculatedRemaining = max(0, $estimatedDuration - $elapsed);
        
        // Get previous remaining time if it exists
        $previousRemaining = $localRequest['last_remaining_time'] ?? $calculatedRemaining;
        
        // Never increase the remaining time - only decrease or stay the same
        $remaining = min($calculatedRemaining, $previousRemaining);
        
        // Update the request data if remaining time changed
        if ($remaining !== $previousRemaining) {
            $this->updateLastRemainingTime($localRequest['hash'], $remaining);
        }

        return [
            'hash' => $localRequest['hash'] ?? 'unknown',
            'service_slug' => $localRequest['service_slug'] ?? 'unknown',
            'status' => $localRequest['status'] ?? 'unknown',
            'step' => $localRequest['step'] ?? 'unknown',
            'progress' => $localRequest['progress'] ?? 0,
            'current_message' => $this->getProgressMessage($localRequest),
            'estimated_remaining_time' => $remaining,
            'otp_data' => $localRequest['otp_data'] ?? null,
            'result_data' => $localRequest['result_data'] ?? null,
            'error_data' => $localRequest['error_data'] ?? null,
            'is_expired' => $this->isExpired($localRequest),
            'requires_otp' => ($localRequest['status'] ?? '') === 'otp_required' || ($localRequest['step'] ?? '') === 'otp_error',
            'is_completed' => ($localRequest['status'] ?? '') === 'completed',
            'is_failed' => ($localRequest['status'] ?? '') === 'failed',
            'updated_at' => $localRequest['updated_at'] ?? null
        ];
    }

    /**
     * Get progress message based on step
     */
    private function getProgressMessage(array $localRequest): string
    {
        if (!empty($localRequest['current_message'])) {
            return $localRequest['current_message'];
        }

        return match($localRequest['step'] ?? 'initializing') {
            'initializing' => 'شروع پردازش درخواست...',
            'authentication' => 'ارسال درخواست به درگاه دولت هوشمند...',
            'sending_otp' => 'ارسال کد تایید...',
            'waiting_otp' => 'در انتظار دریافت کد تایید',
            'otp_error' => 'کد تایید اشتباه است - لطفاً مجدداً تلاش کنید',
            'verifying_otp' => 'تایید کد دریافتی...',
            'processing_result' => 'پردازش نتیجه...',
            'completed' => 'پردازش کامل شد',
            default => 'در حال پردازش...'
        };
    }

    /**
     * Check if request is expired
     */
    private function isExpired(array $localRequest): bool
    {
        if (!isset($localRequest['expires_at'])) {
            return false;
        }

        return Carbon::parse($localRequest['expires_at'])->isPast();
    }

    /**
     * Clean up expired requests from Redis
     */
    public function cleanupExpiredRequests(): int
    {
        try {
            $cleaned = 0;
            $allHashes = Redis::lrange($this->listKey, 0, -1);

            foreach ($allHashes as $hash) {
                $request = $this->getRequest($hash);
                
                if (!$request || $this->isExpired($request)) {
                    // Remove from Redis
                    $redisKey = $this->redisPrefix . $hash;
                    Redis::del($redisKey);
                    
                    // Remove from list
                    Redis::lrem($this->listKey, 1, $hash);
                    
                    $cleaned++;
                }
            }

            if ($cleaned > 0) {
                Log::info('Cleaned up expired local requests from Redis', ['count' => $cleaned]);
            }

            return $cleaned;
        } catch (\Exception $e) {
            Log::error('Error cleaning up expired requests', ['error' => $e->getMessage()]);
            return 0;
        }
    }

    /**
     * Get all active requests for a user
     */
    public function getUserActiveRequests(?int $userId, ?string $sessionId = null): array
    {
        try {
            $allHashes = Redis::lrange($this->listKey, 0, -1);
            $userRequests = [];

            foreach ($allHashes as $hash) {
                $request = $this->getRequest($hash);
                
                if (!$request || $this->isExpired($request)) {
                    continue;
                }

                // Check if request belongs to user
                $belongsToUser = false;
                if ($userId && isset($request['user_id']) && $request['user_id'] == $userId) {
                    $belongsToUser = true;
                } elseif (!$userId && $sessionId && isset($request['session_id']) && $request['session_id'] === $sessionId) {
                    $belongsToUser = true;
                }

                if ($belongsToUser && !in_array($request['status'], ['completed', 'failed'])) {
                    $userRequests[] = $this->formatRequestData($request);
                }
            }

            return $userRequests;
        } catch (\Exception $e) {
            Log::error('Error getting user active requests', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Cancel a request
     */
    public function cancelRequest(string $hash): bool
    {
        $localRequest = $this->getRequest($hash);
        
        if (!$localRequest || $localRequest['status'] === 'completed') {
            return false;
        }

        return $this->markAsFailed($hash, 'درخواست توسط کاربر لغو شد', ['cancelled_by_user' => true]);
    }

    /**
     * Generate a unique hash for the request
     */
    private function generateUniqueHash(): string
    {
        do {
            $hash = 'req_' . Str::upper(Str::random(16));
        } while (Redis::exists($this->redisPrefix . $hash));
        
        return $hash;
    }
} 