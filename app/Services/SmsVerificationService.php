<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SmsVerificationService
{
    protected $cachePrefix = 'sms_verify:';
    protected $defaultTtl = 60; // 1 minute (higher rate for SMS verification)

    /**
     * Create a new SMS verification request
     */
    public function createVerificationRequest(
        string $serviceSlug,
        int $serviceId,
        array $serviceData,
        string $scope,
        string $mobile,
        string $nationalId,
        ?string $trackId = null,
        ?int $userId = null,
        ?string $sessionId = null
    ): array {
        $hash = $this->generateHash();
        
        $verificationData = [
            'hash' => $hash,
            'service_slug' => $serviceSlug,
            'service_id' => $serviceId,
            'service_data' => $serviceData,
            'scope' => $scope,
            'mobile' => $mobile,
            'national_id' => $nationalId,
            'track_id' => $trackId,
            'user_id' => $userId,
            'session_id' => $sessionId,
            'status' => 'pending', // pending, verified, expired, failed
            'created_at' => now()->timestamp,
            'expires_at' => now()->addSeconds(60)->timestamp, // 1 minute expiry
            'attempts' => 0,
            'max_attempts' => 3
        ];

        Cache::put($this->cachePrefix . $hash, $verificationData, $this->defaultTtl);
        
        Log::info('SMS verification request created', [
            'hash' => $hash,
            'service_slug' => $serviceSlug,
            'mobile' => $mobile,
            'user_id' => $userId
        ]);

        return $verificationData;
    }

    /**
     * Get SMS verification request by hash
     */
    public function getVerificationRequest(string $hash): ?array
    {
        return Cache::get($this->cachePrefix . $hash);
    }

    /**
     * Update SMS verification request
     */
    public function updateVerificationRequest(string $hash, array $updates): bool
    {
        $request = $this->getVerificationRequest($hash);
        
        if (!$request) {
            return false;
        }

        $request = array_merge($request, $updates);
        $ttl = max(60, $request['expires_at'] - now()->timestamp); // Minimum 1 minute TTL
        
        Cache::put($this->cachePrefix . $hash, $request, $ttl);
        
        return true;
    }

    /**
     * Mark verification as successful
     */
    public function markAsVerified(string $hash, ?string $accessToken = null): bool
    {
        return $this->updateVerificationRequest($hash, [
            'status' => 'verified',
            'verified_at' => now()->timestamp,
            'access_token' => $accessToken
        ]);
    }

    /**
     * Increment failed attempts
     */
    public function incrementAttempts(string $hash): bool
    {
        $request = $this->getVerificationRequest($hash);
        
        if (!$request) {
            return false;
        }

        $attempts = ($request['attempts'] ?? 0) + 1;
        $updates = ['attempts' => $attempts];
        
        if ($attempts >= ($request['max_attempts'] ?? 3)) {
            $updates['status'] = 'failed';
        }

        return $this->updateVerificationRequest($hash, $updates);
    }

    /**
     * Check if verification request is valid
     */
    public function isValidRequest(string $hash): bool
    {
        $request = $this->getVerificationRequest($hash);
        
        if (!$request) {
            return false;
        }

        // Check if expired
        if (now()->timestamp > $request['expires_at']) {
            $this->updateVerificationRequest($hash, ['status' => 'expired']);
            return false;
        }

        // Check if already failed
        if (in_array($request['status'], ['failed', 'expired'])) {
            return false;
        }

        return true;
    }

    /**
     * Check if request can accept more attempts
     */
    public function canAttempt(string $hash): bool
    {
        $request = $this->getVerificationRequest($hash);
        
        if (!$request || !$this->isValidRequest($hash)) {
            return false;
        }

        return ($request['attempts'] ?? 0) < ($request['max_attempts'] ?? 3);
    }

    /**
     * Delete verification request (cleanup)
     */
    public function deleteVerificationRequest(string $hash): bool
    {
        return Cache::forget($this->cachePrefix . $hash);
    }

    /**
     * Generate unique hash for SMS verification
     */
    protected function generateHash(): string
    {
        do {
            $hash = 'sms_' . strtoupper(Str::random(16));
        } while (Cache::has($this->cachePrefix . $hash));

        return $hash;
    }

    /**
     * Get remaining time for verification in seconds
     */
    public function getRemainingTime(string $hash): int
    {
        $request = $this->getVerificationRequest($hash);
        
        if (!$request) {
            return 0;
        }

        return max(0, $request['expires_at'] - now()->timestamp);
    }

    /**
     * Resend SMS verification (if supported by the service)
     */
    public function canResend(string $hash): bool
    {
        $request = $this->getVerificationRequest($hash);
        
        if (!$request || !$this->isValidRequest($hash)) {
            return false;
        }

        // Allow resend if no successful verification and within time limit
        return $request['status'] === 'pending' && $this->getRemainingTime($hash) > 0;
    }
} 