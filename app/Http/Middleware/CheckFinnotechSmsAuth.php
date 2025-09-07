<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\Finnotech\SmsAuthorizationService;
use Illuminate\Support\Facades\Log;

class CheckFinnotechSmsAuth
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
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $scope): Response
    {
        // Get user identification from request
        $nationalId = $request->input('national_code') ?? $request->input('national_id');
        $mobile = $request->input('mobile');

        // Validate required parameters
        if (!$nationalId || !$mobile) {
            return response()->json([
                'status' => 'error',
                'message' => 'کد ملی و شماره موبایل الزامی است',
                'required_fields' => ['national_code', 'mobile']
            ], 422);
        }

        // Validate mobile format
        if (!preg_match('/^09[0-9]{9}$/', $mobile)) {
            return response()->json([
                'status' => 'error',
                'message' => 'شماره موبایل نامعتبر است'
            ], 422);
        }

        // Validate national ID format
        if (!preg_match('/^[0-9]{10}$/', $nationalId)) {
            return response()->json([
                'status' => 'error',
                'message' => 'کد ملی نامعتبر است'
            ], 422);
        }

        try {
            // Check if user has valid token for this scope
            $tokenData = $this->smsAuthService->getToken($scope, $nationalId, $mobile);
            
            if (!$tokenData) {
                // No valid token found - user needs SMS authorization
                Log::info('SMS authorization required - no valid token', [
                    'scope' => $scope,
                    'national_id' => $nationalId,
                    'mobile' => $mobile,
                    'reason' => 'no_token'
                ]);

                return $this->requireSmsAuthorization($scope, $nationalId, $mobile, $request);
            }

            // Double-check token expiry (extra safety)
            if (isset($tokenData['expires_at'])) {
                $expiresAt = \Carbon\Carbon::parse($tokenData['expires_at']);
                if ($expiresAt->isPast()) {
                    Log::info('SMS authorization required - token expired', [
                        'scope' => $scope,
                        'national_id' => $nationalId,
                        'mobile' => $mobile,
                        'expires_at' => $tokenData['expires_at'],
                        'current_time' => now()->toIso8601String(),
                        'reason' => 'token_expired'
                    ]);

                    // Clean up expired token
                    $this->smsAuthService->cleanupExpiredToken($scope, $nationalId, $mobile);

                    return $this->requireSmsAuthorization($scope, $nationalId, $mobile, $request);
                }
            }

            // Token exists and is valid - add token info to request
            $request->merge([
                'finnotech_token' => $tokenData['access_token'],
                'token_expires_at' => $tokenData['expires_at'],
                'token_scope' => $scope,
                'token_created_at' => $tokenData['created_at']
            ]);

            Log::info('Valid SMS token found and verified', [
                'scope' => $scope,
                'national_id' => $nationalId,
                'mobile' => $mobile,
                'expires_at' => $tokenData['expires_at'],
                'time_until_expiry' => \Carbon\Carbon::parse($tokenData['expires_at'])->diffForHumans()
            ]);

            return $next($request);

        } catch (\Exception $e) {
            Log::error('SMS auth middleware error', [
                'scope' => $scope,
                'national_id' => $nationalId,
                'mobile' => $mobile,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'خطا در بررسی احراز هویت پیامکی'
            ], 500);
        }
    }

    /**
     * Handle case where SMS authorization is required
     */
    private function requireSmsAuthorization(string $scope, string $nationalId, string $mobile, Request $request): Response
    {
        try {
            // Generate authorization URL
            $authUrl = $this->smsAuthService->generateAuthorizationUrl($scope, $mobile, $nationalId);
            
            // Check if this is an AJAX request
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'sms_auth_required',
                    'message' => 'احراز هویت پیامکی مورد نیاز است',
                    'data' => [
                        'authorization_url' => $authUrl,
                        'scope' => $scope,
                        'national_id' => $nationalId,
                        'mobile' => $mobile,
                        'next_step' => 'redirect_to_sms_auth'
                    ]
                ], 200);
            }

            // For regular requests, redirect to SMS auth page
            return redirect()->route('finnotech.sms-auth.start', [
                'scope' => $scope,
                'national_id' => $nationalId,
                'mobile' => $mobile,
                'return_url' => $request->fullUrl()
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to generate SMS authorization', [
                'scope' => $scope,
                'national_id' => $nationalId,
                'mobile' => $mobile,
                'error' => $e->getMessage()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'خطا در ایجاد احراز هویت پیامکی'
                ], 500);
            }

            return redirect()->back()->with('error', 'خطا در ایجاد احراز هویت پیامکی');
        }
    }
} 