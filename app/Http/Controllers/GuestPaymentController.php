<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\PaymentGateway;
use App\Models\GatewayTransaction;
use App\Models\User;
use App\Models\Otp;
use App\Services\PaymentService;
use App\Services\PaymentGatewayManager;
use App\Services\ServicePaymentService;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Exception;

class GuestPaymentController extends Controller
{
    protected PaymentService $paymentService;
    protected PaymentGatewayManager $gatewayManager;
    protected ServicePaymentService $servicePaymentService;
    protected SmsService $smsService;

    public function __construct(
        PaymentService $paymentService,
        PaymentGatewayManager $gatewayManager,
        ServicePaymentService $servicePaymentService,
        SmsService $smsService
    ) {
        $this->paymentService = $paymentService;
        $this->gatewayManager = $gatewayManager;
        $this->servicePaymentService = $servicePaymentService;
        $this->smsService = $smsService;
    }

    /**
     * Show guest wallet charge page (preview page)
     */
    public function showChargePage(Request $request, Service $service)
    {
        // Validate that service requires payment
        if (!$service->is_paid || $service->price <= 0) {
            return redirect()->back()->with('error', 'این سرویس نیاز به پرداخت ندارد.');
        }

        // Get service data from session
        $serviceData = Session::get('guest_service_data', []);
        $mobile = Session::get('guest_mobile');
        $serviceRequestId = Session::get('guest_service_request_id');
        $serviceRequestHash = Session::get('guest_service_request_hash');

        // Validate that we have service data (user came from service submission)
        if (empty($serviceData) || !$serviceRequestId || !$serviceRequestHash) {
            return redirect()->route('services.show', $service->slug)
                ->with('error', 'لطفاً ابتدا فرم سرویس را تکمیل کنید.');
        }

        // Get available payment gateways
        $gateways = PaymentGateway::active()
            ->forCurrency('IRT')
            ->get()
            ->filter(function ($gateway) use ($service) {
                return $gateway->supportsAmount($service->price);
            });

        if ($gateways->isEmpty()) {
            return redirect()->back()->with('error', 'هیچ درگاه پرداخت مناسبی یافت نشد.');
        }

        Log::info('Guest charge page displayed', [
            'service_id' => $service->id,
            'service_request_id' => $serviceRequestId,
            'mobile' => $mobile,
            'available_gateways' => $gateways->count()
        ]);

        return view('payments.guest-charge', compact('service', 'serviceData', 'mobile', 'gateways', 'serviceRequestHash'));
    }



    /**
     * Process guest wallet charge
     */
    public function processCharge(Request $request)
    {
        $request->validate([
            'service_request_hash' => 'required|string|exists:service_requests,request_hash',
            'gateway_id' => 'required|exists:payment_gateways,id',
            'guest_session_token' => 'required|string'
        ]);

        try {
            DB::beginTransaction();

            $serviceRequest = \App\Models\ServiceRequest::where('request_hash', $request->service_request_hash)->firstOrFail();
            $service = $serviceRequest->service;
            $serviceData = $serviceRequest->input_data;
            $gateway = PaymentGateway::findOrFail($request->gateway_id);
            
            // Validate gateway and amount
            if (!$gateway->is_active) {
                throw new Exception('درگاه پرداخت انتخاب شده در دسترس نیست');
            }

            if (!$gateway->supportsAmount($service->price)) {
                throw new Exception('مبلغ وارد شده توسط درگاه انتخاب شده پشتیبانی نمی‌شود');
            }

            // Store guest information in session (without phone for now)
            Session::put('guest_session_token', $request->guest_session_token);
            Session::put('guest_service_id', $service->id);
            Session::put('guest_service_data', $serviceData);

            // Create payment data
            $paymentData = [
                'user_id' => null, // Guest payment - no user
                'amount' => $service->price,
                'currency' => 'IRT',
                'description' => "شارژ کیف‌پول مهمان برای سرویس: {$service->title}",
                'gateway_id' => $gateway->id,
                'metadata' => [
                    'type' => 'guest_wallet_charge',
                    'service_id' => $service->id,
                    'service_title' => $service->title,
                    'service_name' => $service->title, // For backward compatibility
                    'service_data' => $serviceData,
                    'guest_session_token' => $request->guest_session_token,
                    'service_request_hash' => $serviceRequest->request_hash,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ],
            ];

            // Create payment transaction
            $paymentResult = $this->paymentService->createPayment($paymentData);

            if (!$paymentResult['success']) {
                throw new Exception($paymentResult['message'] ?? 'خطا در ایجاد پرداخت');
            }

            $transaction = $paymentResult['transaction'];
            
            // PaymentService already created the payment with gateway
            $gatewayResult = $paymentResult['gateway_result'] ?? $paymentResult;

            if (!$gatewayResult['success']) {
                throw new Exception($gatewayResult['message'] ?? 'خطا در ایجاد پرداخت در درگاه');
            }

            DB::commit();

            Log::info('Guest payment created successfully', [
                'transaction_id' => $transaction->id,
                'service_id' => $service->id,
                'amount' => $service->price,
            ]);

            // Handle payment gateway redirect properly
            return $this->handlePaymentGatewayRedirect($gatewayResult, $transaction);

        } catch (ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Guest payment creation failed', [
                'error' => $e->getMessage(),
                'service_id' => $request->service_id,
                'phone' => $request->phone,
            ]);

            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Handle payment gateway redirect based on gateway requirements
     */
    protected function handlePaymentGatewayRedirect(array $gatewayResult, GatewayTransaction $transaction)
    {
        // Check if gateway requires form submission (like Asan Pardakht)
        if (isset($gatewayResult['redirect_method']) && $gatewayResult['redirect_method'] === 'form_submit') {
            return response()->view('payments.gateway-redirect', [
                'transaction' => $transaction,
                'gateway_data' => $gatewayResult,
                'payment_form' => $gatewayResult['payment_form'],
                'is_guest' => true,
            ]);
        }

        // Standard URL redirect
        if (isset($gatewayResult['payment_url'])) {
            return redirect($gatewayResult['payment_url']);
        }

        // Check if gateway result has nested data structure
        if (isset($gatewayResult['data'])) {
            $data = $gatewayResult['data'];
            
            // Check if nested data has form submission
            if (isset($data['redirect_method']) && $data['redirect_method'] === 'form_submit') {
                return response()->view('payments.gateway-redirect', [
                    'transaction' => $transaction,
                    'gateway_data' => $data,
                    'payment_form' => $data['payment_form'],
                    'is_guest' => true,
                ]);
            }

            // Standard URL redirect from nested data
            if (isset($data['payment_url'])) {
                return redirect($data['payment_url']);
            }
        }

        // If no redirect method is available, return error
        throw new Exception('خطا در هدایت به درگاه پرداخت');
    }

    /**
     * Handle guest payment callback
     */
    public function handleCallback(Request $request, string $gateway, string $transaction = null)
    {
        try {
            // Find the payment gateway
            $paymentGateway = PaymentGateway::where('slug', $gateway)->where('is_active', true)->first();
            
            if (!$paymentGateway) {
                throw new Exception('درگاه پرداخت نامعتبر یا غیرفعال است');
            }

            // Find the transaction
            $gatewayTransaction = null;
            
            if ($transaction) {
                $gatewayTransaction = GatewayTransaction::where('uuid', $transaction)->first();
            } elseif ($request->has('invoice') || $request->has('order_id')) {
                $referenceId = $request->invoice ?? $request->order_id;
                $gatewayTransaction = GatewayTransaction::where('reference_id', $referenceId)->first();
            }

            if (!$gatewayTransaction) {
                throw new Exception('تراکنش یافت نشد');
            }

            // Check if this is a guest payment
            $metadata = $gatewayTransaction->metadata ?? [];
            if (!isset($metadata['type']) || $metadata['type'] !== 'guest_wallet_charge') {
                // Not a guest payment, redirect to regular callback handler
                return app(PaymentController::class)->handleCallback($request, $gateway, $transaction);
            }

            // Get gateway instance and verify payment
            $gatewayInstance = $this->gatewayManager->gatewayById($paymentGateway->id);
            $verificationResult = $gatewayInstance->verifyPayment($gatewayTransaction, $request->all());

            // Update transaction status
            $this->updateTransactionStatus($gatewayTransaction, $verificationResult);

            if ($verificationResult['success'] && ($verificationResult['data']['verified'] ?? false)) {
                // Payment successful - redirect to phone verification page
                return $this->handleSuccessfulPayment($gatewayTransaction, $metadata);
            } else {
                // Payment failed
                return $this->handleFailedPayment($gatewayTransaction, $verificationResult);
            }

        } catch (Exception $e) {
            Log::error('Guest payment callback failed', [
                'error' => $e->getMessage(),
                'gateway' => $gateway,
                'transaction' => $transaction,
                'request_data' => $request->all(),
            ]);

            return redirect()->route('app.page.home')->with('error', 'خطا در پردازش پرداخت: ' . $e->getMessage());
        }
    }

    /**
     * Handle successful guest payment
     */
    protected function handleSuccessfulPayment(GatewayTransaction $transaction, array $metadata)
    {
        $serviceId = $metadata['service_id'] ?? null;
        $sessionToken = $metadata['guest_session_token'] ?? null;

        if (!$serviceId || !$sessionToken) {
            Log::error('Missing guest payment metadata', [
                'transaction_id' => $transaction->id,
                'metadata' => $metadata
            ]);
            return redirect()->route('app.page.home')->with('error', 'اطلاعات پرداخت ناقص است');
        }

        // Store payment success information in session
        Session::put('guest_payment_success', true);
        Session::put('guest_payment_transaction_id', $transaction->id);
        Session::put('guest_payment_service_id', $serviceId);
        Session::put('guest_payment_amount', $transaction->amount);
        Session::put('guest_session_token', $sessionToken);
        
        // Store request hash for fallback lookup
        $requestHash = $metadata['service_request_hash'] ?? null;
        if ($requestHash) {
            Session::put('pending_service_request_hash', $requestHash);
        }

        Log::info('Guest payment successful', [
            'transaction_id' => $transaction->id,
            'service_id' => $serviceId,
            'amount' => $transaction->amount,
        ]);

        // Redirect to phone verification page
        return redirect()->route('guest.payment.verify.phone')
            ->with('payment_success', true)
            ->with('success', 'پرداخت با موفقیت انجام شد. لطفاً شماره موبایل خود را وارد کنید.');
    }

    /**
     * Handle failed guest payment
     */
    protected function handleFailedPayment(GatewayTransaction $transaction, array $verificationResult)
    {
        $metadata = $transaction->metadata ?? [];
        $serviceId = $metadata['service_id'] ?? null;
        $requestHash = $metadata['service_request_hash'] ?? null;

        Log::warning('Guest payment failed', [
            'transaction_id' => $transaction->id,
            'service_id' => $serviceId,
            'request_hash' => $requestHash,
            'error' => $verificationResult['message'] ?? 'پرداخت ناموفق',
            'metadata' => $metadata
        ]);

        // Determine user-friendly error message
        $errorMessage = $this->determineGuestErrorMessage($verificationResult, $transaction);

        if ($serviceId) {
            $service = \App\Models\Service::find($serviceId);
            if ($service) {
                // Try to redirect to preview page if we have a request hash
                if ($requestHash) {
                    try {
                        $serviceRequest = \App\Models\ServiceRequest::findByHash($requestHash);
                        if ($serviceRequest && $serviceRequest->service_id == $service->id) {
                            // Restore service data to session for retry
                            Session::put('guest_service_data', $serviceRequest->input_data);
                            Session::put('guest_service_request_id', $serviceRequest->id);
                            Session::put('guest_service_request_hash', $requestHash);
                            
                            Log::info('Redirecting guest to preview page after payment failure', [
                                'service_id' => $service->id,
                                'request_hash' => $requestHash,
                                'service_request_id' => $serviceRequest->id
                            ]);

                            return redirect()->route('services.preview.guest', [
                                'service' => $service,
                                'hash' => $requestHash
                            ])->with('error', $errorMessage)
                              ->with('payment_failed', true)
                              ->with('show_payment_retry', true);
                        }
                    } catch (\Exception $e) {
                        Log::error('Error finding service request for failed guest payment', [
                            'request_hash' => $requestHash,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
                
                // Fallback to service page
                Log::info('Redirecting guest to service page after payment failure', [
                    'service_slug' => $service->slug
                ]);
                
                return redirect()->route('services.show', $service->slug)
                    ->with('error', $errorMessage)
                    ->with('payment_failed', true);
            }
        }

        // Final fallback
        return redirect()->route('app.page.home')->with('error', $errorMessage);
    }

    /**
     * Determine user-friendly error message for guest payments
     */
    protected function determineGuestErrorMessage(array $verificationResult, GatewayTransaction $transaction): string
    {
        // Check if we have specific error codes from the gateway
        $gatewayData = $transaction->gateway_data ?? [];
        $respCode = $gatewayData['respcode'] ?? null;
        $respMsg = $gatewayData['respmsg'] ?? null;
        
        // Also check in callback_data
        if (!$respCode && isset($gatewayData['callback_data'])) {
            $respCode = $gatewayData['callback_data']['respcode'] ?? null;
            $respMsg = $gatewayData['callback_data']['respmsg'] ?? null;
        }
        
        // Check for SEP gateway specific states
        $state = $gatewayData['callback_data']['State'] ?? null;
        $status = $gatewayData['callback_data']['Status'] ?? null;
        
        // Handle SEP cancellation
        if ($state === 'CanceledByUser' || $status == 1) {
            return 'پرداخت توسط شما لغو شد. برای استفاده از سرویس، لطفاً پرداخت را تکمیل نمایید.';
        }
        
        // Handle specific error codes
        if ($respCode === '-1' || $respCode === -1 || stripos($respMsg ?? '', 'لغو') !== false || stripos($respMsg ?? '', 'کاربر') !== false) {
            return 'پرداخت توسط شما لغو شد. برای استفاده از سرویس، لطفاً پرداخت را تکمیل نمایید.';
        }
        
        if ($respCode === '-2' || $respCode === -2 || stripos($respMsg ?? '', 'انصراف') !== false) {
            return 'از پرداخت انصراف دادید. برای ادامه، می‌توانید مجدداً پرداخت کنید.';
        }
        
        if (stripos($respMsg ?? '', 'timeout') !== false || stripos($respMsg ?? '', 'زمان') !== false) {
            return 'زمان پرداخت به پایان رسید. لطفاً دوباره تلاش کنید.';
        }
        
        if (stripos($respMsg ?? '', 'موجودی') !== false || stripos($respMsg ?? '', 'کافی') !== false) {
            return 'موجودی حساب کافی نیست. لطفاً از کارت دیگری استفاده کنید یا حساب را شارژ نمایید.';
        }
        
        if (stripos($respMsg ?? '', 'رمز') !== false || stripos($respMsg ?? '', 'pin') !== false) {
            return 'رمز کارت اشتباه است. لطفاً رمز صحیح را وارد کرده و دوباره تلاش کنید.';
        }
        
        if (stripos($respMsg ?? '', 'cvv') !== false || stripos($respMsg ?? '', 'cvv2') !== false) {
            return 'کد CVV2 اشتباه است. لطفاً کد صحیح پشت کارت را وارد کنید.';
        }
        
        // Use the original message if provided
        if ($respMsg) {
            return $respMsg;
        }
        
        // Default error message for guests
        return 'پرداخت ناموفق بود. لطفاً مجدداً تلاش کنید یا از کارت دیگری استفاده نمایید.';
    }

    /**
     * Show phone verification page after successful payment
     */
    public function showPhoneVerification(Request $request)
    {
        // Check if there's a successful payment in session
        if (!Session::has('guest_payment_success')) {
            return redirect()->route('app.page.home')->with('error', 'اطلاعات پرداخت یافت نشد.');
        }

        $serviceId = Session::get('guest_payment_service_id');
        $amount = Session::get('guest_payment_amount');
        
        // Get service information for display
        $service = null;
        if ($serviceId) {
            $service = Service::find($serviceId);
        }

        return view('payments.guest-verify-phone', compact('service', 'amount'));
    }

    /**
     * Send OTP to guest phone number after payment
     */
    public function sendPhoneVerification(Request $request)
    {
        $request->validate([
            'mobile' => 'required|string|regex:/^09[0-9]{9}$/'
        ]);

        try {
            // Check if there's a successful payment in session
            if (!Session::has('guest_payment_success')) {
                return response()->json([
                    'success' => false,
                    'message' => 'اطلاعات پرداخت یافت نشد.'
                ], 400);
            }

            $phoneNumber = Otp::formatMobile($request->mobile);
            
            // Validate phone number
            if (!Otp::isValidIranianMobile($phoneNumber)) {
                return response()->json([
                    'success' => false,
                    'message' => 'شماره موبایل نامعتبر است.'
                ], 400);
            }

            // Store mobile number in session for verification
            Session::put('guest_payment_phone', $phoneNumber);

            // Send OTP using existing OTP service
            $otpResult = Otp::generate($phoneNumber, 'guest_verification');
            
            if ($otpResult['success']) {
                // Send SMS
                $smsResult = $this->smsService->sendOtp($phoneNumber, $otpResult['code']);
                
                if ($smsResult['success']) {
                    Log::info('Guest verification OTP sent', [
                        'phone' => $phoneNumber,
                        'transaction_id' => Session::get('guest_payment_transaction_id'),
                    ]);
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'کد تایید به شماره موبایل شما ارسال شد.',
                        'expires_at' => $otpResult['expires_at']
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'خطا در ارسال پیامک. لطفاً مجدداً تلاش کنید.'
                    ], 500);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $otpResult['message'] ?? 'خطا در تولید کد تایید.'
                ], 500);
            }
            
        } catch (Exception $e) {
            Log::error('Guest OTP send failed', [
                'error' => $e->getMessage(),
                'phone' => Session::get('guest_payment_phone'),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'خطای غیرمنتظره. لطفاً مجدداً تلاش کنید.'
            ], 500);
        }
    }

    /**
     * Verify guest phone number with OTP
     */
    public function verifyPhoneOtp(Request $request)
    {
        $request->validate([
            'otp_code' => 'required|string|size:6'
        ]);

        try {
            // Check if there's a successful payment in session
            if (!Session::has('guest_payment_success') || !Session::has('guest_payment_phone')) {
                return response()->json([
                    'success' => false,
                    'message' => 'اطلاعات پرداخت یافت نشد.'
                ], 400);
            }

            $phoneNumber = Session::get('guest_payment_phone');
            $otpCode = $request->otp_code;
            
            // Verify OTP using existing OTP service
            $verificationResult = Otp::verify($phoneNumber, $otpCode, 'guest_verification');
            
            if ($verificationResult['success']) {
                // Mark phone as verified in session
                Session::put('guest_phone_verified', true);
                Session::put('guest_phone_verified_at', now());
                
                Log::info('Guest phone verification successful', [
                    'phone' => $phoneNumber,
                    'transaction_id' => Session::get('guest_payment_transaction_id'),
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'شماره موبایل با موفقیت تایید شد.',
                    'redirect' => route('app.auth.login')
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $verificationResult['message'] ?? 'کد تایید نامعتبر است.'
                ], 422);
            }
            
        } catch (Exception $e) {
            Log::error('Guest OTP verification failed', [
                'error' => $e->getMessage(),
                'phone' => Session::get('guest_payment_phone'),
                'otp' => $request->otp_code,
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'خطای غیرمنتظره. لطفاً مجدداً تلاش کنید.'
            ], 500);
        }
    }

    /**
     * Process guest payment after user login/registration
     */
    public function processGuestPaymentAfterLogin(User $user): array
    {
        try {
            DB::beginTransaction();
            
            $transactionId = Session::get('guest_payment_transaction_id');
            if (!$transactionId) {
                throw new Exception('شناسه تراکنش یافت نشد');
            }

            $transaction = GatewayTransaction::find($transactionId);
            if (!$transaction || $transaction->status !== 'completed') {
                throw new Exception('تراکنش معتبر نیست یا تکمیل نشده است');
            }

            $metadata = $transaction->metadata ?? [];
            $serviceId = $metadata['service_id'] ?? null;
            $serviceData = $metadata['service_data'] ?? [];

            if (!$serviceId) {
                throw new Exception('اطلاعات سرویس یافت نشد');
            }

            $service = Service::find($serviceId);
            if (!$service) {
                throw new Exception('سرویس یافت نشد');
            }

            // ✅ UPDATE TRANSACTION TO LINK TO USER (Convert guest payment to user payment)
            $wasGuestPayment = !$transaction->user_id;
            
            if (!$transaction->user_id) {
                $transaction->update(['user_id' => $user->id]);
                
                Log::info('Guest transaction linked to user after login', [
                    'transaction_id' => $transaction->id,
                    'transaction_uuid' => $transaction->uuid,
                    'user_id' => $user->id,
                    'service_id' => $serviceId,
                    'amount' => $transaction->amount,
                    'converted_from_guest' => true
                ]);
            } elseif ($transaction->user_id !== $user->id) {
                // Security check: ensure transaction belongs to this user
                Log::warning('Guest transaction user mismatch during processing', [
                    'transaction_id' => $transaction->id,
                    'transaction_user_id' => $transaction->user_id,
                    'current_user_id' => $user->id,
                    'service_id' => $serviceId
                ]);
                throw new Exception('تراکنش متعلق به کاربر دیگری است');
            }

            // Add payment amount to user's wallet
            $user->deposit($transaction->amount, [
                'description' => "شارژ کیف‌پول مهمان برای سرویس: {$service->title}",
                'gateway_transaction_id' => $transaction->id,
                'gateway_reference_id' => $transaction->gateway_reference_id,
                'service_id' => $service->id,
                'service_title' => $service->title,
                'type' => 'guest_wallet_charge',
                'payment_source' => 'guest_payment',
                'payment_method' => 'gateway',
                'guest_conversion' => true,
                'converted_from_guest' => $wasGuestPayment,
                'processed_at' => now()->toISOString(),
            ]);

            // Now process the service with the charged wallet
            if ($user->balance >= $service->price) {
                $servicePaymentService = app(ServicePaymentService::class);
                
                // Create a request object with the service data
                $request = new \Illuminate\Http\Request();
                $request->replace($serviceData);
                $request->setUserResolver(function () use ($user) {
                    return $user;
                });

                $serviceResult = $servicePaymentService->handleServiceSubmission($request, $service, $serviceData);

                if ($serviceResult['success']) {
                    DB::commit();

                    // Clear guest payment session data
                    Session::forget([
                        'guest_payment_success',
                        'guest_payment_transaction_id',
                        'guest_payment_service_id',
                        'guest_payment_amount',
                        'guest_session_token',
                        'pending_service_request_hash'
                    ]);

                    Log::info('Guest payment processed successfully after login', [
                        'user_id' => $user->id,
                        'transaction_id' => $transaction->id,
                        'service_id' => $service->id,
                        'redirect' => $serviceResult['redirect'] ?? null
                    ]);

                    return [
                        'success' => true,
                        'message' => 'درخواست شما با موفقیت پردازش شد.',
                        'redirect' => $serviceResult['redirect'] ?? route('app.user.wallet'),
                        'service_processed' => true
                    ];
                } else {
                    // Refund if service processing fails
                    $user->deposit($service->price, [
                        'description' => "بازگشت وجه - خطا در پردازش سرویس: {$service->title}",
                        'service_id' => $service->id,
                        'type' => 'service_refund'
                    ]);

                    DB::commit();

                    return [
                        'success' => false,
                        'message' => $serviceResult['message'] ?? 'خطا در پردازش سرویس',
                        'service_processed' => false
                    ];
                }
            } else {
                // Insufficient balance even after charge (shouldn't happen, but handle gracefully)
                DB::commit();
                
                Log::warning('Insufficient balance after guest payment charge', [
                    'user_id' => $user->id,
                    'user_balance' => $user->balance,
                    'service_price' => $service->price,
                    'transaction_amount' => $transaction->amount
                ]);

                return [
                    'success' => true,
                    'message' => 'کیف‌پول شما شارژ شد اما موجودی برای پردازش سرویس کافی نیست.',
                    'redirect' => route('services.preview.user', [
                        'service' => $service,
                        'hash' => $metadata['service_request_hash'] ?? null
                    ]),
                    'service_processed' => false
                ];
            }

        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Guest payment processing after login failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'transaction_id' => Session::get('guest_payment_transaction_id'),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'service_processed' => false
            ];
        }
    }

    /**
     * Update transaction status based on verification result
     */
    protected function updateTransactionStatus(GatewayTransaction $transaction, array $verificationResult)
    {
        if ($verificationResult['success'] && ($verificationResult['data']['verified'] ?? false)) {
            $transaction->update([
                'status' => 'completed',
                'gateway_reference' => $verificationResult['data']['reference_id'] ?? null,
                'gateway_response' => array_merge($transaction->gateway_response ?? [], $verificationResult),
                'completed_at' => now(),
            ]);

            $transaction->addLog('completed', 'webhook', [
                'message' => 'Guest payment verified and completed successfully',
                'response_data' => $verificationResult,
            ]);
        } else {
            $transaction->update([
                'status' => 'failed',
                'gateway_response' => array_merge($transaction->gateway_response ?? [], $verificationResult),
                'failed_at' => now(),
            ]);

            $transaction->addLog('failed', 'webhook', [
                'message' => 'Guest payment verification failed',
                'error' => $verificationResult['message'] ?? 'Payment verification failed',
                'response_data' => $verificationResult,
            ]);
        }
    }
} 