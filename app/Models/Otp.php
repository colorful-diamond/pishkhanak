<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class Otp extends Model
{
    use HasFactory;

    protected $fillable = [
        'mobile',
        'code',
        'type',
        'expires_at',
        'ip_address',
        'user_agent',
        'attempts',
        'last_attempt_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
        'last_attempt_at' => 'datetime',
        'is_used' => 'boolean',
    ];

    /**
     * OTP types
     */
    const TYPE_LOGIN = 'login';
    const TYPE_REGISTER = 'register';
    const TYPE_PASSWORD_RESET = 'password_reset';

    /**
     * Configuration constants
     */
    const EXPIRY_MINUTES = 3; // OTP expires in 3 minutes
    const MAX_ATTEMPTS = 5; // Maximum verification attempts
    const RATE_LIMIT_MINUTES = 2; // Wait time between SMS requests
    const MAX_DAILY_SMS = 10; // Maximum SMS per mobile per day
    const CODE_LENGTH = 5; // OTP code length

    /**
     * Generate a new OTP for the given mobile number
     */
    public static function generate(string $mobile, string $type = self::TYPE_LOGIN, ?string $ipAddress = null, ?string $userAgent = null): self
    {
        // Invalidate any existing active OTPs for this mobile and type
        self::where('mobile', $mobile)
            ->where('type', $type)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->update(['is_used' => true]);

        // Generate random code
        $code = str_pad(random_int(0, pow(10, self::CODE_LENGTH) - 1), self::CODE_LENGTH, '0', STR_PAD_LEFT);

        return self::create([
            'mobile' => $mobile,
            'code' => $code,
            'type' => $type,
            'expires_at' => now()->addMinutes(self::EXPIRY_MINUTES),
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);
    }

    /**
     * Verify OTP code
     */
    public function verify(string $code): bool
    {
        // Increment attempts
        $this->increment('attempts');
        $this->update(['last_attempt_at' => now()]);

        // Check if too many attempts
        if ($this->attempts > self::MAX_ATTEMPTS) {
            return false;
        }

        // Check if expired
        if ($this->isExpired()) {
            return false;
        }

        // Check if already used
        if ($this->is_used) {
            return false;
        }

        // Verify code
        if ($this->code === $code) {
            $this->update([
                'verified_at' => now(),
                'is_used' => true,
            ]);
            return true;
        }

        return false;
    }

    /**
     * Check if OTP is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if OTP is valid (not expired, not used, not exceeded attempts)
     */
    public function isValid(): bool
    {
        return !$this->isExpired() && 
               !$this->is_used && 
               $this->attempts < self::MAX_ATTEMPTS;
    }

    /**
     * Find valid OTP for mobile and type
     */
    public static function findValid(string $mobile, string $type = self::TYPE_LOGIN): ?self
    {
        return self::where('mobile', $mobile)
            ->where('type', $type)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->where('attempts', '<', self::MAX_ATTEMPTS)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    /**
     * Check if mobile can request new OTP (rate limiting)
     */
    public static function canRequestOtp(string $mobile, string $type = self::TYPE_LOGIN): bool
    {
        // Check recent requests
        $recentOtp = self::where('mobile', $mobile)
            ->where('type', $type)
            ->where('created_at', '>', now()->subMinutes(self::RATE_LIMIT_MINUTES))
            ->first();

        if ($recentOtp) {
            return false;
        }

        // Check daily limit
        $dailyCount = self::where('mobile', $mobile)
            ->where('created_at', '>', now()->startOfDay())
            ->count();

        return $dailyCount < self::MAX_DAILY_SMS;
    }

    /**
     * Get remaining wait time for rate limiting
     */
    public static function getRemainingWaitTime(string $mobile, string $type = self::TYPE_LOGIN): int
    {
        $recentOtp = self::where('mobile', $mobile)
            ->where('type', $type)
            ->where('created_at', '>', now()->subMinutes(self::RATE_LIMIT_MINUTES))
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$recentOtp) {
            return 0;
        }

        $elapsed = now()->diffInSeconds($recentOtp->created_at);
        $waitTime = (self::RATE_LIMIT_MINUTES * 60) - $elapsed;

        return max(0, $waitTime);
    }

    /**
     * Clean up expired OTPs
     */
    public static function cleanup(): int
    {
        return self::where('expires_at', '<', now()->subHours(24))
            ->delete();
    }

    /**
     * Get daily SMS count for mobile
     */
    public static function getDailySmsCount(string $mobile): int
    {
        return self::where('mobile', $mobile)
            ->where('created_at', '>', now()->startOfDay())
            ->count();
    }

    /**
     * Format mobile number
     */
    public static function formatMobile(string $mobile): string
    {
        // Remove all non-numeric characters
        $mobile = preg_replace('/\D/', '', $mobile);
        
        // Add Iran country code if not present
        if (strlen($mobile) === 10 && substr($mobile, 0, 1) === '9') {
            $mobile = '0' . $mobile;
        }
        
        return $mobile;
    }

    /**
     * Validate Iranian mobile number
     */
    public static function isValidIranianMobile(string $mobile): bool
    {
        // Remove all non-numeric characters
        $cleanMobile = preg_replace('/\D/', '', $mobile);
        
        // Iranian mobile number should be 11 digits starting with 09
        if (strlen($cleanMobile) === 11 && substr($cleanMobile, 0, 2) === '09') {
            return true;
        }
        
        // Also accept 10 digits starting with 9 (without leading 0)
        if (strlen($cleanMobile) === 10 && substr($cleanMobile, 0, 1) === '9') {
            return true;
        }
        
        return false;
    }

    /**
     * Scope for active OTPs
     */
    public function scopeActive($query)
    {
        return $query->where('is_used', false)
                    ->where('expires_at', '>', now());
    }

    /**
     * Scope for expired OTPs
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }
} 