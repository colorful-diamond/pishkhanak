<?php

namespace App\Services;

use App\Models\Otp;
use App\Services\Finnotech\SMS;
use App\Services\Finnotech\FinnotechService;
use Illuminate\Support\Facades\Log;
use Exception;

class SmsService
{
    private SMS $smsService;
    private FinnotechService $finnotechService;
    
    public function __construct()
    {
        // Create FinnotechService and SMS service
        $this->finnotechService = new FinnotechService();
        $this->smsService = new SMS($this->finnotechService);
    }

    /**
     * Send OTP SMS to mobile number
     */
    public function sendOtp(string $mobile, string $code, string $type = Otp::TYPE_LOGIN): bool
    {
        try {
            // Format mobile number (validation already done by controller)
            $formattedMobile = Otp::formatMobile($mobile);

            // Get message template based on type
            $message = $this->getOtpMessage($code, $type);

            // Send SMS via Finnotech
            $result = $this->sendSms($formattedMobile, $message);
            Log::info(json_encode($result));

            if ($result === true) {
                Log::info('OTP SMS sent successfully', [
                    'mobile' => $mobile,
                    'type' => $type,
                    'code_length' => strlen($code)
                ]);
                return true;
            }

            Log::warning('OTP SMS failed', [
                'mobile' => $mobile,
                'type' => $type,
                'result' => $result
            ]);
            return false;
        } catch (Exception $e) {
            Log::error('Failed to send OTP SMS', [
                'mobile' => $mobile,
                'type' => $type,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send SMS via Finnotech API
     */
    public function sendSms(string $mobile, string $message, ?string $sender = null): bool
    {
        try {
            // Use SMS service to send SMS (note the parameter order: message, mobile)
            $result = $this->smsService->sendSMS($message, $mobile);

            if ($result) {
                Log::info('SMS sent successfully via Finnotech', [
                    'mobile' => $mobile,
                    'response' => $result
                ]);

                return true;
            }

            Log::error('Finnotech SMS API failed', [
                'mobile' => $mobile,
                'result' => $result
            ]);

            return false;
        } catch (Exception $e) {
            Log::error('SMS sending exception', [
                'mobile' => $mobile,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Send OTP and create OTP record
     */
    public function sendOtpWithRecord(string $mobile, string $type = Otp::TYPE_LOGIN, ?string $ipAddress = null, ?string $userAgent = null): array
    {
        try {
            // Format mobile number (validation already done by controller)
            $formattedMobile = Otp::formatMobile($mobile);

            // Check rate limiting
            if (!Otp::canRequestOtp($formattedMobile, $type)) {
                $waitTime = Otp::getRemainingWaitTime($formattedMobile, $type);
                
                return [
                    'success' => false,
                    'message' => "لطفاً {$waitTime} ثانیه صبر کنید",
                    'code' => 'RATE_LIMITED',
                    'wait_time' => $waitTime
                ];
            }

            // Generate OTP
            $otp = Otp::generate($formattedMobile, $type, $ipAddress, $userAgent);

            // Send SMS
            $smsSent = $this->sendOtp($formattedMobile, $otp->code, $type);

            if ($smsSent) {
                return [
                    'success' => true,
                    'message' => 'کد تأیید به شماره شما ارسال شد',
                    'expires_in' => Otp::EXPIRY_MINUTES * 60, // seconds
                    'code' => 'OTP_SENT'
                ];
            } else {
                // Delete the OTP record if SMS failed
                $otp->delete();
                
                return [
                    'success' => false,
                    'message' => 'خطا در ارسال پیامک. لطفاً مجدداً تلاش کنید',
                    'code' => 'SMS_FAILED'
                ];
            }
        } catch (Exception $e) {
            Log::error('Send OTP with record failed', [
                'mobile' => $mobile,
                'type' => $type,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'خطای سیستمی رخ داده است',
                'code' => 'SYSTEM_ERROR'
            ];
        }
    }

    /**
     * Verify OTP code
     */
    public function verifyOtp(string $mobile, string $code, string $type = Otp::TYPE_LOGIN): array
    {
        try {
            // Format mobile number
            $formattedMobile = Otp::formatMobile($mobile);

            // Find valid OTP
            $otp = Otp::findValid($formattedMobile, $type);

            if (!$otp) {
                return [
                    'success' => false,
                    'message' => 'کد تأیید نامعتبر یا منقضی شده است',
                    'code' => 'INVALID_OTP'
                ];
            }

            // Verify code
            if ($otp->verify($code)) {
                return [
                    'success' => true,
                    'message' => 'کد تأیید صحیح است',
                    'code' => 'OTP_VERIFIED'
                ];
            } else {
                $remainingAttempts = Otp::MAX_ATTEMPTS - $otp->attempts;
                
                if ($remainingAttempts <= 0) {
                    return [
                        'success' => false,
                        'message' => 'تعداد تلاش‌های مجاز تمام شده است. لطفاً کد جدید درخواست کنید',
                        'code' => 'MAX_ATTEMPTS_EXCEEDED'
                    ];
                }

                return [
                    'success' => false,
                    'message' => "کد تأیید اشتباه است. {$remainingAttempts} تلاش باقی مانده",
                    'code' => 'WRONG_CODE',
                    'remaining_attempts' => $remainingAttempts
                ];
            }
        } catch (Exception $e) {
            Log::error('OTP verification failed', [
                'mobile' => $mobile,
                'type' => $type,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'خطای سیستمی رخ داده است',
                'code' => 'SYSTEM_ERROR'
            ];
        }
    }

    /**
     * Get OTP message template
     */
    private function getOtpMessage(string $code, string $type): string
    {
        $appName = config('app.name', 'پیشخوانک');

        switch ($type) {
            case Otp::TYPE_LOGIN:
                return "کد ورود شما به {$appName}: {$code}\nاین کد در 3 دقیقه منقضی می‌شود.";
            
            case Otp::TYPE_REGISTER:
                return "کد تأیید ثبت نام در {$appName}: {$code}\nاین کد در 3 دقیقه منقضی می‌شود.";
            
            case Otp::TYPE_PASSWORD_RESET:
                return "کد بازیابی رمز عبور {$appName}: {$code}\nاین کد در 3 دقیقه منقضی می‌شود.";
            
            default:
                return "کد تأیید {$appName}: {$code}\nاین کد در 3 دقیقه منقضی می‌شود.";
        }
    }

    /**
     * Generate unique track ID for API requests
     */
    private function generateTrackId(): string
    {
        return $this->finnotechService->generateTrackId();
    }

    /**
     * Check SMS service status
     */
    public function checkStatus(): array
    {
        try {
            // Try to get token to verify service availability
            $accessToken = $this->finnotechService->getToken();
            
            if (!$accessToken) {
                return [
                    'status' => 'error',
                    'message' => 'No valid token available'
                ];
            }

            return [
                'status' => 'ok',
                'message' => 'SMS service is available'
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Get SMS sending statistics
     */
    public function getStatistics(): array
    {
        $today = now()->startOfDay();
        
        return [
            'total_today' => Otp::where('created_at', '>=', $today)->count(),
            'login_today' => Otp::where('created_at', '>=', $today)->where('type', Otp::TYPE_LOGIN)->count(),
            'register_today' => Otp::where('created_at', '>=', $today)->where('type', Otp::TYPE_REGISTER)->count(),
            'password_reset_today' => Otp::where('created_at', '>=', $today)->where('type', Otp::TYPE_PASSWORD_RESET)->count(),
            'verified_today' => Otp::where('created_at', '>=', $today)->whereNotNull('verified_at')->count(),
        ];
    }

    /**
     * Clean up expired OTPs
     */
    public function cleanup(): int
    {
        return Otp::cleanup();
    }
} 