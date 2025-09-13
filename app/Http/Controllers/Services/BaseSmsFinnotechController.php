<?php

namespace App\Http\Controllers\Services;

use App\Models\Service;
use App\Services\Finnotech\SmsAuthorizationService;
use App\Services\SmsVerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Exception;

abstract class BaseSmsFinnotechController extends BaseFinnotechController
{
    protected SmsVerificationService $smsVerificationService;

    public function __construct(
        \App\Services\Finnotech\FinnotechService $finnotechService, 
        SmsAuthorizationService $smsAuthService,
        SmsVerificationService $smsVerificationService
    ) {
        parent::__construct($finnotechService, $smsAuthService);
        $this->smsVerificationService = $smsVerificationService;
        $this->configureService();
    }

    /**
     * Handle service request - initiate SMS verification if needed
     */
    public function handle(Request $request, Service $service)
    {
        try {
            // Validate initial form data
            $validator = Validator::make($request->all(), $this->validationRules, $this->validationMessages);
            
            if ($validator->fails()) {
                return back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // If user is not authenticated, redirect to preview page
            if (!Auth::check()) {
                // Store service request for processing after payment (no transaction needed)
                $serviceData = $request->only($this->requiredFields);
                $serviceRequest = \App\Models\ServiceRequest::create([
                    'service_id' => $service->id,
                    'user_id' => null,
                    'input_data' => $serviceData,
                    'status' => 'guest',
                ]);

                // Store guest service data in session for preview page
                \Illuminate\Support\Facades\Session::put('guest_service_request_id', $serviceRequest->id);
                \Illuminate\Support\Facades\Session::put('guest_service_request_hash', $serviceRequest->request_hash);
                \Illuminate\Support\Facades\Session::put('guest_service_data', $serviceData);
                \Illuminate\Support\Facades\Session::put('guest_request_ip', $request->ip());
                \Illuminate\Support\Facades\Session::put('guest_request_user_agent', $request->userAgent());
                
                // Store phone number if provided
                if ($request->has('mobile')) {
                    \Illuminate\Support\Facades\Session::put('guest_mobile', $request->mobile);
                }

                Log::info('Guest user accessing SMS service, redirecting to preview page', [
                    'service_id' => $service->id,
                    'service_slug' => $service->slug,
                    'service_request_id' => $serviceRequest->id,
                    'service_data_keys' => array_keys($serviceData),
                    'ip' => $request->ip()
                ]);

                // Redirect to service preview page with hash
                return redirect()->route('services.preview.guest', [
                    'service' => $service->id, 
                    'hash' => $serviceRequest->request_hash
                ])->with('info', 'برای استفاده از این سرویس، لطفاً کیف پول خود را شارژ کنید.');
            }

            /** @var \App\Models\User $user */
            $user = Auth::user();
            $serviceData = $request->only($this->requiredFields);
            
            if ($user->balance < $service->price) {
                // Create service request for insufficient balance redirect
                $serviceRequest = \App\Models\ServiceRequest::create([
                    'service_id' => $service->id,
                    'user_id' => $user->id,
                    'input_data' => $serviceData,
                    'status' => 'insufficient_balance',
                ]);

                // Store service data in session for later processing after wallet charge
                \Illuminate\Support\Facades\Session::put('pending_service_id', $service->id);
                \Illuminate\Support\Facades\Session::put('pending_service_data', $serviceData);
                \Illuminate\Support\Facades\Session::put('pending_service_request_id', $serviceRequest->id);
                \Illuminate\Support\Facades\Session::put('pending_service_redirect', url()->current());
                
                $shortfall = $service->price - $user->balance;
                
                Log::info('User redirected to preview page due to insufficient balance in SMS service', [
                    'user_id' => $user->id,
                    'service_id' => $service->id,
                    'service_request_id' => $serviceRequest->id,
                    'current_balance' => $user->balance,
                    'required_amount' => $service->price,
                    'shortfall' => $shortfall
                ]);

                // Redirect to service preview page where user can charge wallet
                return redirect()->route('services.preview.user', [
                    'service' => $service->id, 
                    'hash' => $serviceRequest->request_hash
                ])->with('error', "موجودی کیف پول شما کافی نیست. برای استفاده از این سرویس، حداقل {$shortfall} تومان بیشتر نیاز دارید.");
            }
            $nationalId = $serviceData['national_code'] ?? '';
            $mobile = $serviceData['mobile'] ?? $user->mobile ?? '';

            if (empty($nationalId) || empty($mobile)) {
                return back()
                    ->withErrors(['service_error' => 'کد ملی و شماره موبایل برای این سرویس الزامی است'])
                    ->withInput();
            }

            // Try to process service directly first (check for existing tokens)
            try {
                $result = $this->processServiceWithToken($serviceData, $service, '');
                
                if ($result['success']) {
                    
                    // Create ServiceRequest record for tracking
                    $serviceRequest = \App\Models\ServiceRequest::create([
                        'service_id' => $service->id,
                        'user_id' => $user->id,
                        'input_data' => $serviceData,
                        'status' => 'processed',
                        'processed_at' => now(),
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ]);
                    
                    // Deduct the service price from the user's wallet using Bavix Wallet
                    // This ensures the user's balance is reduced by the service price (in tooman, as per project rules)
                    // The withdraw method will throw an exception if the balance is insufficient, but we've already checked above
                    // We also attach a description and metadata for traceability
                    $walletTransaction = $user->withdraw($service->price, [
                        'description' => "پرداخت سرویس: {$service->title}",
                        'service_id' => $service->id,
                        'service_request_id' => $serviceRequest->id,
                        'type' => 'service_payment'
                    ]);

                    // Update service request with wallet transaction ID
                    $serviceRequest->update([
                        'wallet_transaction_id' => $walletTransaction->id
                    ]);

                    $serviceResult = \App\Models\ServiceResult::create([
                        'service_id' => $service->id,
                        'user_id' => $user->id,
                        'service_request_id' => $serviceRequest->id,
                        'input_data' => $serviceData,
                        'output_data' => $result['data'],
                        'status' => 'success',
                        'processed_at' => now(),
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ]);

                    return redirect()->route('services.result', ['id' => $serviceResult->result_hash])
                        ->with('success', 'عملیات با موفقیت انجام شد.');
                }
            } catch (\Exception $e) {
                // Token doesn't exist or expired, need SMS verification
            }

            // Need SMS verification - create verification request
            $smsResult = $this->smsAuthService->requestSmsAuthorization($this->scope, $mobile, $nationalId);
            
            if (!$smsResult['success']) {
                return back()
                    ->withErrors(['service_error' => $smsResult['message'] ?? 'خطا در ارسال کد تایید'])
                    ->withInput();
            }

            // Create SMS verification request with unique hash
            $verificationRequest = $this->smsVerificationService->createVerificationRequest(
                serviceSlug: $service->slug,
                serviceId: $service->id,
                serviceData: $serviceData,
                scope: $this->scope,
                mobile: $mobile,
                nationalId: $nationalId,
                trackId: $smsResult['track_id'] ?? null,
                userId: $user->id,
                sessionId: Session::getId()
            );

            // Redirect to SMS verification page with unique URL
            return redirect()->route('services.sms-verification', [
                'service' => $service->slug,
                'hash' => $verificationRequest['hash']
            ])->with('info', 'کد تایید به شماره موبایل شما ارسال شد.');

        } catch (Exception $e) {
            Log::error('SMS service handler exception', [
                'service_slug' => $service->slug,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withErrors(['service_error' => 'خطا در پردازش درخواست'])
                ->withInput();
        }
    }

    /**
     * Show SMS verification page with unique URL
     */
    public function showSmsVerification(Request $request, Service $service, string $hash)
    {
        $verificationRequest = $this->smsVerificationService->getVerificationRequest($hash);
        
        if (!$verificationRequest) {
            return redirect()->route('services.show', $service->slug)
                ->withErrors(['error' => 'درخواست تایید یافت نشد یا منقضی شده است.']);
        }

        if (!$this->smsVerificationService->isValidRequest($hash)) {
            return redirect()->route('services.show', $service->slug)
                ->withErrors(['error' => 'درخواست تایید منقضی شده است. لطفاً مجدداً تلاش کنید.']);
        }

        // Check authorization - ensure user owns this verification request
        if (Auth::id() !== $verificationRequest['user_id']) {
            abort(403, 'شما مجاز به دسترسی به این صفحه نیستید.');
        }

        return view('services.sms-verification', [
            'service' => $service,
            'mobile' => $verificationRequest['mobile'],
            'national_id' => $verificationRequest['national_id'],
            'scope' => $verificationRequest['scope'],
            'track_id' => $verificationRequest['track_id'],
            'hash' => $hash,
            'remainingTime' => $this->smsVerificationService->getRemainingTime($hash),
            'attempts' => $verificationRequest['attempts'] ?? 0,
            'maxAttempts' => $verificationRequest['max_attempts'] ?? 3,
            'canResend' => $this->smsVerificationService->canResend($hash),
            'message' => 'کد تایید به شماره موبایل شما ارسال شد'
        ]);
    }

         /**
      * Handle SMS verification submission (OTP verification)
      */
     public function handleSmsOtpVerification(Request $request, Service $service, string $hash)
    {
        try {
            // Validate OTP input
            $validator = Validator::make($request->all(), [
                'otp_code' => 'required|string|min:4|max:10'
            ], [
                'otp_code.required' => 'کد تایید الزامی است',
                'otp_code.min' => 'کد تایید باید حداقل 4 رقم باشد'
            ]);

            if ($validator->fails()) {
                $this->smsVerificationService->incrementAttempts($hash);
                return back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $verificationRequest = $this->smsVerificationService->getVerificationRequest($hash);
            
            if (!$verificationRequest) {
                return redirect()->route('services.show', $service->slug)
                    ->withErrors(['error' => 'درخواست تایید یافت نشد یا منقضی شده است.']);
            }

            if (!$this->smsVerificationService->isValidRequest($hash)) {
                return redirect()->route('services.show', $service->slug)
                    ->withErrors(['error' => 'درخواست تایید منقضی شده است. لطفاً مجدداً تلاش کنید.']);
            }

            if (!$this->smsVerificationService->canAttempt($hash)) {
                return redirect()->route('services.show', $service->slug)
                    ->withErrors(['error' => 'تعداد تلاش‌های مجاز به پایان رسیده است. لطفاً مجدداً درخواست دهید.']);
            }

            // Check authorization
            if (Auth::id() !== $verificationRequest['user_id']) {
                abort(403, 'شما مجاز به دسترسی به این صفحه نیستید.');
            }

            $otpCode = $request->input('otp_code');
            
                         // Verify SMS code using SMS Authorization Service
             $verifyResult = $this->smsAuthService->verifyOtp(
                 $verificationRequest['scope'],
                 $verificationRequest['mobile'],
                 $verificationRequest['national_id'],
                 $otpCode
             );

            if (!$verifyResult['success']) {
                $this->smsVerificationService->incrementAttempts($hash);
                
                return back()
                    ->withErrors(['otp_code' => $verifyResult['message'] ?? 'کد تایید نامعتبر است'])
                    ->withInput();
            }

            // Mark verification as successful
            $this->smsVerificationService->markAsVerified($hash, $verifyResult['access_token'] ?? null);

            // Process the service with verified token
            $result = $this->processServiceWithToken(
                $verificationRequest['service_data'], 
                $service, 
                $verifyResult['access_token'] ?? ''
            );

            if (!$result['success']) {
                return back()
                    ->withErrors(['otp_code' => $result['message'] ?? 'خطا در پردازش سرویس'])
                    ->withInput();
            }

            // Create ServiceRequest record for tracking
            $serviceRequest = \App\Models\ServiceRequest::create([
                'service_id' => $service->id,
                'user_id' => $verificationRequest['user_id'],
                'input_data' => $verificationRequest['service_data'],
                'status' => 'processed',
                'processed_at' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            
            // Payment and result storage AFTER successful service processing
            // Note: Payment deduction is now handled by individual service controllers
            $serviceResult = \App\Models\ServiceResult::create([
                'service_id' => $service->id,
                'service_request_id' => $serviceRequest->id,
                'user_id' => Auth::id(),
                'input_data' => $verificationRequest['service_data'],
                'output_data' => $result['data'],
                'status' => 'success',
                'processed_at' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Clean up verification request
            $this->smsVerificationService->deleteVerificationRequest($hash);

            return redirect()->route('services.result', ['id' => $serviceResult->result_hash])
                ->with('success', 'عملیات با موفقیت انجام شد.');

        } catch (Exception $e) {
            Log::error('SMS verification exception', [
                'hash' => $hash,
                'service_slug' => $service->slug,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->smsVerificationService->incrementAttempts($hash);
            
            return back()
                ->withErrors(['otp_code' => 'خطا در تایید کد. لطفاً مجدداً تلاش کنید.'])
                ->withInput();
        }
    }
} 