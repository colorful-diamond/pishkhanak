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
use Illuminate\Support\Facades\Cookie;
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
        Log::info('🔄 PAYMENT CALLBACK: Starting guest payment callback processing', [
            'gateway' => $gateway,
            'transaction_uuid' => $transaction,
            'request_method' => $request->method(),
            'request_data' => $request->all(),
            'user_agent' => $request->header('User-Agent'),
            'ip_address' => $request->ip(),
            'timestamp' => now(),
        ]);
        
        // 🔧 DUPLICATE PROCESSING PROTECTION: Check if transaction is already being processed
        if ($transaction) {
            $processingKey = "payment_processing_{$transaction}";
            if (cache()->has($processingKey)) {
                Log::warning('🚫 DUPLICATE GUEST CALLBACK PREVENTED: Transaction already being processed', [
                    'transaction_uuid' => $transaction,
                    'gateway' => $gateway,
                    'processing_key' => $processingKey,
                ]);
                return redirect()->route('app.page.home')->with('error', 'پرداخت در حال پردازش است. لطفاً منتظر بمانید.');
            }
            
            // Set processing lock for 5 minutes
            cache()->put($processingKey, true, 300);
        }

        try {
            // Find the payment gateway
            $paymentGateway = PaymentGateway::where('slug', $gateway)->where('is_active', true)->first();
            
            Log::info('🔍 PAYMENT CALLBACK: Gateway lookup result', [
                'gateway_slug' => $gateway,
                'gateway_found' => $paymentGateway ? true : false,
                'gateway_id' => $paymentGateway->id ?? null,
                'gateway_name' => $paymentGateway->name ?? null,
                'gateway_is_active' => $paymentGateway->is_active ?? null,
            ]);
            
            if (!$paymentGateway) {
                Log::error('❌ PAYMENT CALLBACK: Invalid or inactive payment gateway', [
                    'requested_gateway' => $gateway,
                    'available_gateways' => PaymentGateway::where('is_active', true)->pluck('slug')->toArray(),
                ]);
                throw new Exception('درگاه پرداخت نامعتبر یا غیرفعال است');
            }

            // Find the transaction
            $gatewayTransaction = null;
            
            Log::info('🔍 PAYMENT CALLBACK: Starting transaction lookup', [
                'lookup_methods' => [
                    'by_uuid' => $transaction ? true : false,
                    'by_invoice' => $request->has('invoice'),
                    'by_order_id' => $request->has('order_id'),
                ],
                'transaction_uuid' => $transaction,
                'invoice' => $request->invoice ?? null,
                'order_id' => $request->order_id ?? null,
            ]);
            
            if ($transaction) {
                $gatewayTransaction = GatewayTransaction::where('uuid', $transaction)->first();
                Log::info('📄 PAYMENT CALLBACK: Transaction lookup by UUID', [
                    'uuid' => $transaction,
                    'found' => $gatewayTransaction ? true : false,
                    'transaction_id' => $gatewayTransaction->id ?? null,
                ]);
            } elseif ($request->has('invoice') || $request->has('order_id')) {
                $referenceId = $request->invoice ?? $request->order_id;
                $gatewayTransaction = GatewayTransaction::where('reference_id', $referenceId)->first();
                Log::info('📄 PAYMENT CALLBACK: Transaction lookup by reference', [
                    'reference_id' => $referenceId,
                    'lookup_field' => $request->has('invoice') ? 'invoice' : 'order_id',
                    'found' => $gatewayTransaction ? true : false,
                    'transaction_id' => $gatewayTransaction->id ?? null,
                ]);
            }

            if (!$gatewayTransaction) {
                Log::error('❌ PAYMENT CALLBACK: Transaction not found', [
                    'search_criteria' => [
                        'uuid' => $transaction,
                        'invoice' => $request->invoice ?? null,
                        'order_id' => $request->order_id ?? null,
                    ],
                    'recent_transactions' => GatewayTransaction::latest()->limit(5)->pluck('uuid', 'id')->toArray(),
                ]);
                throw new Exception('تراکنش یافت نشد');
            }

            // Check if this is a guest payment
            $metadata = $gatewayTransaction->metadata ?? [];
            
            // Determine if this is a guest payment by checking:
            // 1. type === 'guest_wallet_charge' OR
            // 2. type === 'wallet_charge_for_service' AND user_id is null
            $isGuestPayment = (
                (isset($metadata['type']) && $metadata['type'] === 'guest_wallet_charge') ||
                (isset($metadata['type']) && $metadata['type'] === 'wallet_charge_for_service' && empty($gatewayTransaction->user_id))
            );
            
            Log::info('📋 PAYMENT CALLBACK: Transaction metadata analysis', [
                'transaction_id' => $gatewayTransaction->id,
                'transaction_uuid' => $gatewayTransaction->uuid,
                'transaction_status' => $gatewayTransaction->status,
                'transaction_amount' => $gatewayTransaction->total_amount,
                'transaction_user_id' => $gatewayTransaction->user_id,
                'metadata' => $metadata,
                'metadata_type' => $metadata['type'] ?? 'unknown',
                'is_guest_payment' => $isGuestPayment,
                'guest_check_reasons' => [
                    'type_guest_wallet_charge' => isset($metadata['type']) && $metadata['type'] === 'guest_wallet_charge',
                    'type_wallet_charge_for_service_with_null_user' => isset($metadata['type']) && $metadata['type'] === 'wallet_charge_for_service' && empty($gatewayTransaction->user_id)
                ]
            ]);
            
            if (!$isGuestPayment) {
                Log::info('↗️ PAYMENT CALLBACK: Redirecting to regular payment handler', [
                    'transaction_type' => $metadata['type'] ?? 'unknown',
                    'transaction_user_id' => $gatewayTransaction->user_id,
                    'reason' => 'not_guest_payment',
                ]);
                // Not a guest payment, redirect to regular callback handler
                return app(PaymentController::class)->handleCallback($request, $gateway, $transaction);
            }

            // Get gateway instance and verify payment
            Log::info('🔐 PAYMENT CALLBACK: Starting payment verification', [
                'gateway_id' => $paymentGateway->id,
                'transaction_id' => $gatewayTransaction->id,
                'verification_data' => $request->all(),
            ]);
            
            $gatewayInstance = $this->gatewayManager->gatewayById($paymentGateway->id);
            $verificationResult = $gatewayInstance->verifyPayment($gatewayTransaction, $request->all());

            Log::info('✅ PAYMENT CALLBACK: Payment verification completed', [
                'transaction_id' => $gatewayTransaction->id,
                'verification_success' => $verificationResult['success'] ?? false,
                'verification_data' => $verificationResult ?? null,
                'verification_message' => $verificationResult['message'] ?? null,
                'payment_verified' => $verificationResult['verified'] ?? false,
            ]);

            // Update transaction status
            $this->updateTransactionStatus($gatewayTransaction, $verificationResult);

            if ($verificationResult['success'] && ($verificationResult['verified'] ?? false)) {
                Log::info('🎉 PAYMENT CALLBACK: Payment successful, proceeding to success handler', [
                    'transaction_id' => $gatewayTransaction->id,
                    'amount' => $gatewayTransaction->total_amount,
                    'service_id' => $metadata['service_id'] ?? null,
                ]);
                // Payment successful - redirect to phone verification page
                $result = $this->handleSuccessfulPayment($gatewayTransaction, $metadata);
                
                // 🔧 CLEANUP: Remove processing lock after successful completion
                if ($transaction) {
                    cache()->forget("payment_processing_{$transaction}");
                }
                
                return $result;
            } else {
                Log::warning('❌ PAYMENT CALLBACK: Payment verification failed', [
                    'transaction_id' => $gatewayTransaction->id,
                    'verification_result' => $verificationResult,
                    'gateway_response' => $verificationResult ?? null,
                ]);
                // Payment failed
                $result = $this->handleFailedPayment($gatewayTransaction, $verificationResult);
                
                // 🔧 CLEANUP: Remove processing lock after failure
                if ($transaction) {
                    cache()->forget("payment_processing_{$transaction}");
                }
                
                return $result;
            }

        } catch (Exception $e) {
            Log::error('💥 PAYMENT CALLBACK: Exception occurred during callback processing', [
                'error' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'gateway' => $gateway,
                'transaction' => $transaction,
                'request_data' => $request->all(),
                'stack_trace' => $e->getTraceAsString(),
            ]);
            
            // 🔧 CLEANUP: Remove processing lock on exception
            if ($transaction) {
                cache()->forget("payment_processing_{$transaction}");
            }

            return redirect()->route('app.page.home')->with('error', 'خطا در پردازش پرداخت: ' . $e->getMessage());
        }
    }

    /**
     * Handle successful guest payment
     */
    protected function handleSuccessfulPayment(GatewayTransaction $transaction, array $metadata)
    {
        Log::info('🎉 SUCCESSFUL PAYMENT: Processing successful guest payment', [
            'transaction_id' => $transaction->id,
            'transaction_uuid' => $transaction->uuid,
            'transaction_amount' => $transaction->total_amount,
            'transaction_status' => $transaction->status,
            'metadata' => $metadata,
        ]);

        $serviceId = $metadata['service_id'] ?? null;
        $sessionToken = $metadata['guest_session_token'] ?? null;
        $requestHash = $metadata['service_request_hash'] ?? null;

        Log::info('📋 SUCCESSFUL PAYMENT: Extracting metadata', [
            'service_id' => $serviceId,
            'session_token_present' => $sessionToken ? true : false,
            'request_hash' => $requestHash,
            'guest_session' => $metadata['guest_session'] ?? null,
        ]);

        if (!$serviceId || !$sessionToken) {
            Log::error('❌ SUCCESSFUL PAYMENT: Missing critical metadata', [
                'transaction_id' => $transaction->id,
                'missing_fields' => [
                    'service_id' => !$serviceId,
                    'session_token' => !$sessionToken,
                ],
                'available_metadata' => $metadata,
            ]);
            return redirect()->route('app.page.home')->with('error', 'اطلاعات پرداخت ناقص است');
        }

        // Store payment success information in session (enhanced with redundancy like PaymentController)
        $sessionData = [
            'guest_payment_success' => true,
            'guest_payment_transaction_id' => $transaction->id,
            'guest_payment_transaction_uuid' => $transaction->uuid,
            'guest_payment_service_id' => $serviceId,
            'guest_payment_amount' => $transaction->total_amount,
            'guest_session_token' => $sessionToken,
        ];

        // Store request hash for fallback lookup
        if ($requestHash) {
            $sessionData['pending_service_request_hash'] = $requestHash;
        }

        // Store service continuation data if available (like PaymentController)
        if ($serviceId && $requestHash) {
            $sessionData['pending_service_continuation'] = [
                'service_id' => $serviceId,
                'service_request_hash' => $requestHash,
                'session_key' => $metadata['service_session_key'] ?? 'guest_service_data',
                'continue_service' => $metadata['continue_service'] ?? null,
            ];
        }

        Log::info('💾 SUCCESSFUL PAYMENT: Storing session data', [
            'transaction_id' => $transaction->id,
            'session_data' => $sessionData,
            'session_id' => session()->getId(),
        ]);

        foreach ($sessionData as $key => $value) {
            Session::put($key, $value);
        }

        // Also store in cookie for better persistence (like PaymentController)
        Cookie::queue('guest_pending_transaction', $transaction->uuid, 60); // 60 minutes
        Cookie::queue('guest_payment_success', 'true', 60);

        // Verify session data was stored correctly
        $storedData = [
            'guest_payment_success' => Session::get('guest_payment_success'),
            'guest_payment_transaction_id' => Session::get('guest_payment_transaction_id'),
            'guest_payment_service_id' => Session::get('guest_payment_service_id'),
            'guest_payment_amount' => Session::get('guest_payment_amount'),
            'guest_session_token' => Session::get('guest_session_token'),
            'pending_service_request_hash' => Session::get('pending_service_request_hash'),
        ];

        Log::info('✅ SUCCESSFUL PAYMENT: Session data verification', [
            'transaction_id' => $transaction->id,
            'stored_session_data' => $storedData,
            'verification_success' => $storedData['guest_payment_success'] === true,
        ]);

        Log::info('🎯 SUCCESSFUL PAYMENT: Guest payment processing completed successfully', [
            'transaction_id' => $transaction->id,
            'service_id' => $serviceId,
            'amount' => $transaction->total_amount,
            'next_step' => 'login_page',
            'redirect_route' => 'app.auth.login',
        ]);

        // Redirect directly to login page (same as normal PaymentController)
        return redirect()->route('app.auth.login')
            ->with('payment_success', true)
            ->with('show_wallet_info', true);
    }

    /**
     * Handle failed guest payment (enhanced with PaymentController patterns)
     */
    protected function handleFailedPayment(GatewayTransaction $transaction, array $verificationResult)
    {
        $metadata = $transaction->metadata ?? [];
        $serviceId = $metadata['service_id'] ?? null;
        $requestHash = $metadata['service_request_hash'] ?? null;

        Log::warning('❌ FAILED PAYMENT: Processing failed guest payment', [
            'transaction_id' => $transaction->id,
            'transaction_uuid' => $transaction->uuid,
            'transaction_amount' => $transaction->total_amount,
            'transaction_status' => $transaction->status,
            'service_id' => $serviceId,
            'request_hash' => $requestHash,
            'verification_result' => $verificationResult,
            'metadata' => $metadata,
            'failure_reason' => $verificationResult['message'] ?? 'Unknown failure',
            'gateway_response' => $verificationResult ?? null,
        ]);

        // Determine user-friendly error message (enhanced method)
        $errorMessage = $this->determineGuestErrorMessage($verificationResult, $transaction);

        // Check if this was a service-related payment (like PaymentController)
        if (isset($metadata['continue_service']) && !empty($metadata['continue_service'])) {
            return $this->redirectToServicePreviewOnFailure($metadata, null, $errorMessage);
        }

        // Handle service-specific failures
        if ($serviceId) {
            return $this->redirectGuestToServiceOnFailure($metadata, $errorMessage);
        }

        // Final fallback
        return redirect()->route('app.page.home')->with('error', $errorMessage);
    }

    /**
     * Redirect to service preview page on payment failure (like PaymentController)
     */
    protected function redirectToServicePreviewOnFailure(array $metadata, ?User $user, string $errorMessage)
    {
        try {
            $serviceSlug = $metadata['continue_service'];
            $sessionKey = $metadata['service_session_key'] ?? 'guest_service_data';
            $requestHash = $metadata['service_request_hash'] ?? null;
            
            Log::info('Attempting service preview redirect for guest', [
                'service_slug' => $serviceSlug,
                'session_key' => $sessionKey,
                'request_hash' => $requestHash,
            ]);
            
            // Find the service to get its model for route parameter
            $service = \App\Models\Service::where('slug', $serviceSlug)->first();
            
            if (!$service) {
                Log::error('Service not found for failed guest payment redirect', [
                    'service_slug' => $serviceSlug
                ]);
                return redirect()->route('app.page.home')->with('error', $errorMessage);
            }
            
            // Restore the service request data in session if needed
            if ($sessionKey && $requestHash) {
                Log::info('Restoring session data for failed guest payment', [
                    'session_key' => $sessionKey,
                    'request_hash' => $requestHash
                ]);
                
                // Try to restore service data from ServiceRequest
                $serviceRequest = \App\Models\ServiceRequest::findByHash($requestHash);
                if ($serviceRequest && $serviceRequest->service_id == $service->id) {
                    Session::put($sessionKey, $serviceRequest->input_data);
                    Session::put('guest_service_request_id', $serviceRequest->id);
                    Session::put('guest_service_request_hash', $requestHash);
                    
                    Log::info('Service data restored from ServiceRequest for guest', [
                        'service_request_id' => $serviceRequest->id,
                        'service_data_keys' => array_keys($serviceRequest->input_data)
                    ]);
                }
            }
            
            // Use guest preview route
            $routeParams = ['service' => $service];
            if ($requestHash) {
                $routeParams['hash'] = $requestHash;
            }
            
            Log::info('Redirecting guest to service preview', [
                'route_params' => $routeParams,
                'service_id' => $service->id,
                'service_slug' => $service->slug
            ]);
            
            return redirect()->route('services.preview.guest', $routeParams)
                ->with('error', $errorMessage)
                ->with('payment_failed', true)
                ->with('show_payment_error', true)
                ->with('show_payment_retry', true);
                
        } catch (\Exception $e) {
            Log::error('Error redirecting guest to service preview on payment failure', [
                'error' => $e->getMessage(),
                'metadata' => $metadata
            ]);
            
            return redirect()->route('app.page.home')->with('error', $errorMessage);
        }
    }

    /**
     * Redirect guest to service page on payment failure (enhanced)
     */
    protected function redirectGuestToServiceOnFailure(array $metadata, string $errorMessage)
    {
        try {
            $serviceId = $metadata['service_id'];
            $requestHash = $metadata['service_request_hash'] ?? null;
            
            $service = \App\Models\Service::find($serviceId);
            
            if (!$service) {
                Log::error('Service not found for guest payment failure redirect', [
                    'service_id' => $serviceId
                ]);
                return redirect()->route('app.page.home')->with('error', $errorMessage);
            }
            
            // If we have a request hash, try to redirect to preview page
            if ($requestHash) {
                $serviceRequest = \App\Models\ServiceRequest::findByHash($requestHash);
                if ($serviceRequest && $serviceRequest->service_id == $service->id) {
                    // Restore service data to session
                    Session::put('guest_service_data', $serviceRequest->input_data);
                    Session::put('guest_service_request_id', $serviceRequest->id);
                    Session::put('guest_service_request_hash', $requestHash);
                    
                    return redirect()->route('services.preview.guest', [
                        'service' => $service,
                        'hash' => $requestHash
                    ])->with('error', $errorMessage)
                      ->with('payment_failed', true)
                      ->with('show_payment_retry', true);
                }
            }
            
            // Fallback to service page
            return redirect()->route('services.show', $service->slug)
                ->with('error', $errorMessage)
                ->with('payment_failed', true);
                
        } catch (\Exception $e) {
            Log::error('Error redirecting guest to service on payment failure', [
                'error' => $e->getMessage(),
                'metadata' => $metadata
            ]);
            
            return redirect()->route('app.page.home')->with('error', $errorMessage);
        }
    }

    /**
     * Determine user-friendly error message for guest payments (enhanced with PaymentController patterns)
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
        
        // Handle SEP cancellation (same as PaymentController)
        if ($state === 'CanceledByUser' || $status == 1) {
            return 'پرداخت توسط شما لغو شد. در صورت تمایل می‌توانید مجدداً اقدام نمایید.';
        }
        
        // Handle specific error codes (matching PaymentController exactly)
        if ($respCode === '-1' || $respCode === -1 || stripos($respMsg ?? '', 'لغو') !== false || stripos($respMsg ?? '', 'کاربر') !== false) {
            return 'پرداخت توسط شما لغو شد. در صورت تمایل می‌توانید مجدداً اقدام نمایید.';
        }
        
        if ($respCode === '-2' || $respCode === -2 || stripos($respMsg ?? '', 'انصراف') !== false) {
            return 'از پرداخت انصراف دادید. برای ادامه استفاده از سرویس، لطفاً پرداخت را تکمیل نمایید.';
        }
        
        if (stripos($respMsg ?? '', 'timeout') !== false || stripos($respMsg ?? '', 'زمان') !== false) {
            return 'زمان پرداخت به پایان رسید. لطفاً مجدداً تلاش کنید.';
        }
        
        if (stripos($respMsg ?? '', 'موجودی') !== false || stripos($respMsg ?? '', 'کافی') !== false) {
            return 'موجودی حساب شما کافی نیست. لطفاً از کارت دیگری استفاده کنید.';
        }
        
        if (stripos($respMsg ?? '', 'رمز') !== false || stripos($respMsg ?? '', 'pin') !== false) {
            return 'رمز کارت اشتباه وارد شده است. لطفاً مجدداً تلاش کنید.';
        }
        
        if (stripos($respMsg ?? '', 'cvv') !== false || stripos($respMsg ?? '', 'cvv2') !== false) {
            return 'کد CVV2 اشتباه وارد شده است. لطفاً مجدداً تلاش کنید.';
        }
        
        // Use the original message if provided
        if ($respMsg) {
            return $respMsg;
        }
        
        // Default error message (same as PaymentController)
        return $verificationResult['message'] ?? 'پرداخت ناموفق بود. لطفاً مجدداً تلاش کنید.';
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
        Log::info('🚀 GUEST PAYMENT: Starting guest payment processing after login', [
            'user_id' => $user->id,
            'user_mobile' => $user->mobile,
            'user_name' => $user->name,
            'current_balance' => $user->wallet->balance ?? 0,
            'session_data_present' => [
                'guest_payment_success' => Session::has('guest_payment_success'),
                'guest_payment_transaction_id' => Session::has('guest_payment_transaction_id'),
                'guest_payment_service_id' => Session::has('guest_payment_service_id'),
                'guest_payment_amount' => Session::has('guest_payment_amount'),
                'guest_session_token' => Session::has('guest_session_token'),
                'pending_service_request_hash' => Session::has('pending_service_request_hash'),
            ]
        ]);

        try {
            DB::beginTransaction();
            
            $transactionId = Session::get('guest_payment_transaction_id');
            Log::info('🔍 GUEST PAYMENT: Checking session transaction ID', [
                'user_id' => $user->id,
                'transaction_id' => $transactionId,
                'session_has_id' => Session::has('guest_payment_transaction_id'),
            ]);
            
            if (!$transactionId) {
                Log::error('❌ GUEST PAYMENT: No transaction ID found in session', [
                    'user_id' => $user->id,
                    'all_session_keys' => array_keys(Session::all()),
                ]);
                throw new Exception('شناسه تراکنش یافت نشد');
            }

            $transaction = GatewayTransaction::find($transactionId);
            Log::info('🔍 GUEST PAYMENT: Transaction lookup result', [
                'user_id' => $user->id,
                'transaction_id' => $transactionId,
                'transaction_found' => !!$transaction,
                'transaction_uuid' => $transaction?->uuid,
                'transaction_status' => $transaction?->status,
                'transaction_amount' => $transaction?->total_amount,
                'transaction_user_id' => $transaction?->user_id,
            ]);

            if (!$transaction || $transaction->status !== 'completed') {
                Log::error('❌ GUEST PAYMENT: Invalid or incomplete transaction', [
                    'user_id' => $user->id,
                    'transaction_id' => $transactionId,
                    'transaction_exists' => !!$transaction,
                    'transaction_status' => $transaction?->status ?? 'N/A',
                    'expected_status' => 'completed',
                ]);
                throw new Exception('تراکنش معتبر نیست یا تکمیل نشده است');
            }

            $metadata = $transaction->metadata ?? [];
            $serviceId = $metadata['service_id'] ?? null;
            $serviceData = $metadata['service_data'] ?? [];

            Log::info('🔍 GUEST PAYMENT: Analyzing transaction metadata for wallet charging', [
                'user_id' => $user->id,
                'transaction_uuid' => $transaction->uuid,
                'service_id' => $serviceId,
                'has_service_data' => !empty($serviceData),
                'service_request_hash' => $metadata['service_request_hash'] ?? 'N/A',
                'guest_session' => $metadata['guest_session'] ?? 'N/A',
            ]);

            if (!$serviceId) {
                Log::error('❌ GUEST PAYMENT: Missing service ID in transaction metadata', [
                    'user_id' => $user->id,
                    'transaction_uuid' => $transaction->uuid,
                    'metadata_keys' => array_keys($metadata),
                ]);
                throw new Exception('اطلاعات سرویس یافت نشد');
            }

            $service = Service::find($serviceId);
            if (!$service) {
                Log::error('❌ GUEST PAYMENT: Service not found in database', [
                    'user_id' => $user->id,
                    'transaction_uuid' => $transaction->uuid,
                    'service_id' => $serviceId,
                ]);
                throw new Exception('سرویس یافت نشد');
            }

            Log::info('✅ GUEST PAYMENT: Service found and validated', [
                'user_id' => $user->id,
                'transaction_uuid' => $transaction->uuid,
                'service_id' => $service->id,
                'service_title' => $service->title,
                'service_price' => $service->price,
            ]);

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
            $balanceBefore = $user->wallet->balance;
            // 🔧 DUPLICATE CHARGE PREVENTION: Check if wallet was already charged for this transaction
            $existingDeposit = $user->walletTransactions()
                ->where('meta->gateway_transaction_id', $transaction->id)
                ->where('type', 'deposit')
                ->first();

            if ($existingDeposit) {
                Log::info('🚫 GUEST PAYMENT: Wallet already charged for this transaction, skipping deposit', [
                    'user_id' => $user->id,
                    'transaction_id' => $transaction->id,
                    'transaction_uuid' => $transaction->uuid,
                    'existing_deposit_id' => $existingDeposit->id,
                    'existing_deposit_amount' => $existingDeposit->amount,
                    'existing_deposit_description' => $existingDeposit->description,
                    'reason' => 'duplicate_charge_prevention'
                ]);
            } else {
                Log::info('💰 GUEST PAYMENT: Starting wallet deposit process', [
                    'user_id' => $user->id,
                    'transaction_uuid' => $transaction->uuid,
                    'deposit_amount' => $transaction->total_amount,
                    'balance_before' => $balanceBefore,
                    'service_title' => $service->title,
                    'was_guest_payment' => $wasGuestPayment,
                ]);

                $user->deposit($transaction->total_amount, [
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
            }

            $user->refresh();
            $balanceAfter = $user->wallet->balance;
            Log::info('✅ GUEST PAYMENT: Wallet deposit completed successfully', [
                'user_id' => $user->id,
                'transaction_uuid' => $transaction->uuid,
                'deposit_amount' => $transaction->total_amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'balance_increase' => $balanceAfter - $balanceBefore,
                'deposit_successful' => ($balanceAfter > $balanceBefore),
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