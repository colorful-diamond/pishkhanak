<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Services\Finnotech\SmsAuthorizationService;
use App\Exceptions\FinnotechException;

class FinnotechSmsAuthController extends Controller
{
    /**
     * @var SmsAuthorizationService
     */
    private $smsAuthService;

    public function __construct(SmsAuthorizationService $smsAuthService)
    {
        $this->smsAuthService = $smsAuthService;
    }

    /**
     * Start SMS authorization process - Show initial form or redirect to Finnotech
     */
    public function startSmsAuth(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'scope' => 'required|string',
            'national_id' => 'required|string|size:10',
            'mobile' => 'required|string|regex:/^09[0-9]{9}$/',
            'return_url' => 'nullable|url'
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'اطلاعات وارد شده نامعتبر است',
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $scope = $request->scope;
        $nationalId = $request->national_id;
        $mobile = $request->mobile;
        $returnUrl = $request->return_url;

        try {
            // Check if user already has valid token
            $tokenData = $this->smsAuthService->getToken($scope, $nationalId, $mobile);
            
            if ($tokenData) {
                // User already has valid token
                Log::info('User already has valid SMS token', [
                    'scope' => $scope,
                    'national_id' => $nationalId,
                    'mobile' => $mobile
                ]);

                if ($returnUrl) {
                    return redirect($returnUrl);
                }

                if ($request->expectsJson()) {
                    return response()->json([
                        'status' => 'success',
                        'message' => 'شما قبلاً احراز هویت شده‌اید',
                        'data' => ['has_valid_token' => true]
                    ]);
                }

                return redirect()->route('home')->with('success', 'شما قبلاً احراز هویت شده‌اید');
            }

            // Generate authorization URL for Finnotech SMS auth
            $authUrl = $this->smsAuthService->generateAuthorizationUrl($scope, $mobile, $nationalId);
            
            // Store session data for callback
            session([
                'finnotech_sms_auth' => [
                    'scope' => $scope,
                    'national_id' => $nationalId,
                    'mobile' => $mobile,
                    'return_url' => $returnUrl,
                    'started_at' => now()->toIso8601String()
                ]
            ]);

            Log::info('Starting SMS authorization process', [
                'scope' => $scope,
                'national_id' => $nationalId,
                'mobile' => $mobile,
                'auth_url' => $authUrl
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'sms_auth_redirect',
                    'message' => 'هدایت به صفحه احراز هویت پیامکی',
                    'data' => [
                        'authorization_url' => $authUrl,
                        'redirect_required' => true
                    ]
                ]);
            }

            // Redirect to Finnotech SMS authorization
            return redirect($authUrl);

        } catch (FinnotechException $e) {
            Log::error('SMS authorization start failed', [
                'scope' => $scope,
                'national_id' => $nationalId,
                'mobile' => $mobile,
                'error' => $e->getMessage()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'خطا در شروع احراز هویت پیامکی: ' . $e->getMessage()
                ], 400);
            }

            return redirect()->back()->with('error', 'خطا در شروع احراز هویت پیامکی');
        }
    }

    /**
     * Handle Finnotech SMS auth callback
     */
    public function handleSmsAuthCallback(Request $request)
    {
        // Get session data
        $authData = session('finnotech_sms_auth');
        
        if (!$authData) {
            Log::warning('SMS auth callback without session data', [
                'request_data' => $request->all()
            ]);

            return redirect()->route('home')->with('error', 'جلسه احراز هویت نامعتبر است');
        }

        $validator = Validator::make($request->all(), [
            'code' => 'required|string',
            'state' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            Log::error('Invalid SMS auth callback parameters', [
                'errors' => $validator->errors(),
                'request_data' => $request->all()
            ]);

            return redirect()->route('home')->with('error', 'پارامترهای بازگشت نامعتبر است');
        }

        $code = $request->code;
        $scope = $authData['scope'];
        $nationalId = $authData['national_id'];
        $mobile = $authData['mobile'];
        $returnUrl = $authData['return_url'];

        try {
            // Check if user has valid token (the authorization code callback isn't used in SMS flow)
            $tokenData = $this->smsAuthService->getToken($scope, $nationalId, $mobile);
            
            if ($tokenData) {
                // Clear session data
                session()->forget('finnotech_sms_auth');

                Log::info('SMS authorization completed successfully', [
                    'scope' => $scope,
                    'national_id' => $nationalId,
                    'mobile' => $mobile
                ]);

                // Redirect back to original URL or home
                $redirectUrl = $returnUrl ?: route('home');
                return redirect($redirectUrl)->with('success', 'احراز هویت پیامکی با موفقیت انجام شد');
            } else {
                // If no token, redirect to OTP verification
                return redirect()->route('finnotech.sms-auth.otp.show', [
                    'scope' => $scope,
                    'national_id' => $nationalId,
                    'mobile' => $mobile
                ]);
            }

        } catch (FinnotechException $e) {
            Log::error('SMS auth callback failed', [
                'scope' => $scope,
                'national_id' => $nationalId,
                'mobile' => $mobile,
                'code' => $code,
                'error' => $e->getMessage()
            ]);

            session()->forget('finnotech_sms_auth');
            return redirect()->route('home')->with('error', 'خطا در تکمیل احراز هویت پیامکی');
        }
    }

    /**
     * Show OTP verification page (if needed by the flow)
     */
    public function showOtpVerification(Request $request)
    {
        $authData = session('finnotech_sms_auth');
        
        if (!$authData) {
            return redirect()->route('home')->with('error', 'جلسه احراز هویت نامعتبر است');
        }

        return view('finnotech.sms-auth.otp-verification', [
            'scope' => $authData['scope'],
            'national_id' => $authData['national_id'],
            'mobile' => $authData['mobile'],
            'masked_mobile' => $this->maskMobile($authData['mobile'])
        ]);
    }

    /**
     * Verify OTP (if your flow requires manual OTP input)
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|string|size:5',
            'track_id' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'کد تأیید نامعتبر است',
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $authData = session('finnotech_sms_auth');
        
        if (!$authData) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'جلسه احراز هویت نامعتبر است'
                ], 400);
            }
            return redirect()->route('home')->with('error', 'جلسه احراز هویت نامعتبر است');
        }

        $otp = $request->otp;
        $scope = $authData['scope'];
        $nationalId = $authData['national_id'];
        $mobile = $authData['mobile'];
        $returnUrl = $authData['return_url'];

        try {
            // Here you would verify the OTP with Finnotech
            // This depends on your specific SMS auth implementation
            // For now, this is a placeholder

            Log::info('OTP verification completed', [
                'scope' => $scope,
                'national_id' => $nationalId,
                'mobile' => $mobile,
                'otp' => $otp
            ]);

            // Clear session data
            session()->forget('finnotech_sms_auth');

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'احراز هویت با موفقیت انجام شد',
                    'data' => [
                        'redirect_url' => $returnUrl ?: route('home')
                    ]
                ]);
            }

            $redirectUrl = $returnUrl ?: route('home');
            return redirect($redirectUrl)->with('success', 'احراز هویت پیامکی با موفقیت انجام شد');

        } catch (\Exception $e) {
            Log::error('OTP verification failed', [
                'scope' => $scope,
                'national_id' => $nationalId,
                'mobile' => $mobile,
                'otp' => $otp,
                'error' => $e->getMessage()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'کد تأیید نادرست است'
                ], 400);
            }

            return redirect()->back()->with('error', 'کد تأیید نادرست است')->withInput();
        }
    }

    /**
     * Check token status for a user
     */
    public function checkTokenStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'scope' => 'required|string',
            'national_id' => 'required|string|size:10',
            'mobile' => 'required|string|regex:/^09[0-9]{9}$/'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'پارامترهای نامعتبر',
                'errors' => $validator->errors()
            ], 422);
        }

        $scope = $request->scope;
        $nationalId = $request->national_id;
        $mobile = $request->mobile;

        try {
            $tokenData = $this->smsAuthService->getToken($scope, $nationalId, $mobile);
            
            return response()->json([
                'status' => 'success',
                'data' => [
                    'has_token' => !is_null($tokenData),
                    'expires_at' => $tokenData['expires_at'] ?? null,
                    'created_at' => $tokenData['created_at'] ?? null,
                    'scope' => $scope
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Token status check failed', [
                'scope' => $scope,
                'national_id' => $nationalId,
                'mobile' => $mobile,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'خطا در بررسی وضعیت توکن'
            ], 500);
        }
    }



    /**
     * Revoke SMS authorization token for security purposes
     */
    public function revokeToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'scope' => 'required|string',
            'national_id' => 'required|string|size:10',
            'mobile' => 'required|string|regex:/^09[0-9]{9}$/',
            'reason' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'پارامترهای نامعتبر',
                'errors' => $validator->errors()
            ], 422);
        }

        $scope = $request->scope;
        $nationalId = $request->national_id;
        $mobile = $request->mobile;
        $reason = $request->reason ?: 'manual_revocation';

        try {
            $revoked = $this->smsAuthService->revokeToken($scope, $nationalId, $mobile, $reason);
            
            return response()->json([
                'status' => 'success',
                'data' => [
                    'revoked' => $revoked,
                    'scope' => $scope,
                    'reason' => $reason
                ],
                'message' => $revoked ? 'توکن با موفقیت لغو شد' : 'توکنی برای لغو یافت نشد'
            ]);

        } catch (\Exception $e) {
            Log::error('Token revocation failed', [
                'scope' => $scope,
                'national_id' => $nationalId,
                'mobile' => $mobile,
                'reason' => $reason,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'خطا در لغو توکن'
            ], 500);
        }
    }

    /**
     * Mask mobile number for display
     */
    private function maskMobile(string $mobile): string
    {
        return substr($mobile, 0, 4) . '***' . substr($mobile, -2);
    }
} 