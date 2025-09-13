<?php

namespace App\Services;

use App\Models\Service;
use App\Models\ServiceResult;
use App\Models\ServiceRequest;
use App\Models\User;
use App\Models\GatewayTransaction;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class ServicePaymentService
{
    protected PaymentService $paymentService;
    protected PaymentGatewayManager $gatewayManager;

    public function __construct(
        PaymentService $paymentService,
        PaymentGatewayManager $gatewayManager
    ) {
        $this->paymentService = $paymentService;
        $this->gatewayManager = $gatewayManager;
    }

    /**
     * Handle service submission with payment flow
     *
     * @param Request $request
     * @param Service $service
     * @param array $serviceData
     * @return array
     */
    public function handleServiceSubmission(Request $request, Service $service, array $serviceData): array
    {
        try {
            $user = \Illuminate\Support\Facades\Auth::user();
            $controller = \App\Http\Controllers\Services\ServiceControllerFactory::getController($service);
            
            // FIRST: Check if this is a Background Processing service (LocalApiController)
            // BUT only for authenticated users - guests must go through preview/payment flow
            if ($controller instanceof \App\Http\Controllers\Services\LocalApiController) {
                if ($user) {
                    // Authenticated user - proceed with background processing
                    Log::info('🚀 Background processing service detected for authenticated user', [
                        'service_slug' => $service->slug,
                        'user_id' => $user->id,
                        'controller_class' => get_class($controller)
                    ]);
                    
                    // Check if user has sufficient balance
                    if ($user->balance < $service->price) {
                        Log::info('Insufficient balance for background service', [
                            'user_id' => $user->id,
                            'balance' => $user->balance,
                            'required' => $service->price
                        ]);
                        
                        // Use the proper insufficient balance handler to redirect to preview page
                        return $this->handleInsufficientBalance(request(), $service, [], $user);
                    }
                    
                    // For background services, delegate directly to the LocalApiController
                    $response = $controller->handle($request, $service);
                    
                    // Handle different response types from LocalApiController
                    if ($response instanceof \Illuminate\Http\RedirectResponse) {
                        $targetUrl = $response->getTargetUrl();
                        
                        return [
                            'success' => true,
                            'message' => 'درخواست در حال پردازش در پس‌زمینه است.',
                            'redirect' => $targetUrl
                        ];
                    } else {
                        // Unexpected response type from LocalApiController
                        Log::warning('Unexpected response type from LocalApiController', [
                            'service_slug' => $service->slug,
                            'response_type' => get_class($response)
                        ]);
                        
                        return [
                            'success' => false,
                            'message' => 'خطا در شروع پردازش پس‌زمینه'
                        ];
                    }
                } else {
                    // Guest user - force them through preview/payment flow
                    Log::info('🏃 Guest user accessing background service - routing to preview flow', [
                        'service_slug' => $service->slug,
                        'controller_class' => get_class($controller)
                    ]);
                    
                    // Continue to regular flow (will hit guest handling later)
                }
            }
            
            // SECOND: Check if this is an SMS-based service (handle separately)
            if ($controller instanceof \App\Http\Controllers\Services\BaseSmsFinnotechController) {
                Log::info('🔑 SMS-based service detected, using direct SMS flow', [
                    'service_slug' => $service->slug,
                    'controller_class' => get_class($controller)
                ]);
                
                // For SMS services, delegate directly to the SMS controller
                $response = $controller->handle($request, $service);
                
                // Handle different response types from SMS controller
                if ($response instanceof \Illuminate\Http\RedirectResponse) {
                    $targetUrl = $response->getTargetUrl();
                    
                    // Check if redirect is to results (service completed)
                    if (strpos($targetUrl, '/services/result/') !== false) {
                        return [
                            'success' => true,
                            'message' => 'سرویس با موفقیت انجام شد.',
                            'redirect' => $targetUrl
                        ];
                    } else {
                        // Other redirects (errors, login, etc.)
                        return [
                            'success' => false,
                            'message' => 'انتقال به صفحه مورد نظر',
                            'redirect' => $targetUrl
                        ];
                    }
                } elseif ($response instanceof \Illuminate\Http\Response || 
                          $response instanceof \Illuminate\View\View ||
                          (is_object($response) && method_exists($response, 'render'))) {
                    
                    // This is a view response (like OTP page) - convert to array for consistency
                    return [
                        'success' => true,
                        'message' => 'نمایش صفحه احراز هویت',
                        'view_response' => $response
                    ];
                } else {
                    // Unexpected response type
                    Log::warning('Unexpected response type from SMS controller', [
                        'service_slug' => $service->slug,
                        'response_type' => get_class($response)
                    ]);
                    
                    return [
                        'success' => false,
                        'message' => 'خطا در پردازش سرویس SMS'
                    ];
                }
            }

            // ALL non-SMS services require payment - no free services
            // Check if user is authenticated
            if (!Auth::check()) {
                // Guest user - redirect to payment
                return $this->handleGuestPayment($request, $service, $serviceData);
            }

            // Authenticated user - check wallet balance
            $user = Auth::user();
            
            if ($user->balance >= $service->price) {
                // Sufficient balance - deduct and process (no transaction wrapper)
                return $this->processPaidServiceWithWallet($request, $service, $serviceData, $user);
            } else {
                // Insufficient balance - redirect to wallet charge
                return $this->handleInsufficientBalance($request, $service, $serviceData, $user);
            }

        } catch (\Exception $e) {
            Log::error('Service payment error: ' . $e->getMessage(), [
                'service_id' => $service->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'خطا در پردازش سرویس: ' . $e->getMessage(),
                'redirect' => null
            ];
        }
    }

    /**
     * Process free service
     *
     * @param Request $request
     * @param Service $service
     * @param array $serviceData
     * @return array
     */
    protected function processFreeService(Request $request, Service $service, array $serviceData): array
    {
        // Store service data for processing after payment
        $serviceRequest = $this->storeServiceRequest($service, $serviceData, 'free');

        // Process the service immediately
        $result = $this->processService($service, $serviceData);

        if ($result['success']) {
            // Store the result
            $serviceResult = ServiceResult::create([
                'service_id' => $service->id,
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'input_data' => $serviceData,
                'output_data' => $result['data'],
                'status' => 'success',
                'processed_at' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Mark service request as processed
            $serviceRequest->update([
                'processed_at' => now(),
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'سرویس با موفقیت پردازش شد.',
                'redirect' => route('services.result', ['id' => $serviceResult->result_hash])
            ];
        } else {
            DB::rollBack();
            return [
                'success' => false,
                'message' => $result['message'],
                'redirect' => null
            ];
        }
    }

    /**
     * Handle guest user payment
     *
     * @param Request $request
     * @param Service $service
     * @param array $serviceData
     * @return array
     */
    protected function handleGuestPayment(Request $request, Service $service, array $serviceData): array
    {
        // Store service request for processing after payment (no transaction needed)
        $serviceRequest = $this->storeServiceRequest($service, $serviceData, 'guest');

        // Store guest service data in session for preview page
        Session::put('guest_service_request_id', $serviceRequest->id);
        Session::put('guest_service_request_hash', $serviceRequest->request_hash);
        Session::put('guest_service_data', $serviceData);
        Session::put('guest_request_ip', $request->ip());
        Session::put('guest_request_user_agent', $request->userAgent());
        
        // Store phone number if provided
        if ($request->has('phone')) {
            Session::put('guest_mobile', $request->mobile ?? $request->phone);
        }

        Log::info('Guest service request stored, redirecting to preview page', [
            'service_id' => $service->id,
            'service_request_id' => $serviceRequest->id,
            'service_data_keys' => array_keys($serviceData),
            'ip' => $request->ip()
        ]);

        // Redirect to service preview page with hash
        return [
            'success' => true,
            'message' => 'درخواست شما آماده پردازش است. لطفاً کیف پول خود را شارژ کنید.',
            'redirect' => route('services.preview.guest', ['service' => $service->id, 'hash' => $serviceRequest->request_hash]),
            'show_preview' => true
        ];
    }

    /**
     * Process paid service with wallet balance using direct deduction (no confirmation needed)
     *
     * @param Request $request
     * @param Service $service
     * @param array $serviceData
     * @param User $user
     * @return array
     */
    protected function processPaidServiceWithWallet(Request $request, Service $service, array $serviceData, User $user): array
    {
        try {
            DB::beginTransaction();

            // Get service controller
            $controller = \App\Http\Controllers\Services\ServiceControllerFactory::getController($service);
            
            if (!$controller) {
                return [
                    'success' => false,
                    'message' => 'سرویس از سمت تامین کننده دولتی دچار اختلال است، به زودی مشکل بر طرف می شود.لطفا ساعاتی دیگر تلاش کنید'
                ];
            }

            // SMS-based services should be handled separately
            if ($controller instanceof \App\Http\Controllers\Services\BaseSmsFinnotechController) {
                Log::error('SMS-based service reached payment service - this should not happen', [
                    'service_slug' => $service->slug,
                    'controller_class' => get_class($controller)
                ]);
                
                return [
                    'success' => false,
                    'message' => 'سرویس SMS باید از طریق مسیر جداگانه پردازش شود'
                ];
            }

            Log::info('🚀 DEDUCT ON RESULT: Starting service processing (no upfront deduction)', [
                'service_slug' => $service->slug,
                'service_price' => $service->price,
                'user_balance_before' => $user->balance,
                'user_id' => $user->id,
                'controller_class' => get_class($controller),
                'approach' => 'deduct_only_on_successful_result'
            ]);

            // Process the service
            $result = $controller->process($serviceData, $service);

            if ($result['success']) {
                // 🎯 DEDUCT ON RESULT: Service succeeded - NOW deduct the money and store result
                $walletTransaction = $user->withdraw($service->price, [
                    'description' => "پرداخت سرویس: {$service->title}",
                    'service_id' => $service->id,
                    'service_title' => $service->title,
                    'service_slug' => $service->slug,
                    'service_category_id' => $service->category_id,
                    'service_price' => $service->price,
                    'service_cost' => $service->cost,
                    'type' => 'service_payment',
                    'payment_source' => 'wallet_on_result',
                    'payment_method' => 'wallet',
                    'request_ip' => $request->ip(),
                    'request_user_agent' => $request->userAgent(),
                    'processed_at' => now()->toISOString(),
                    'source_tracking' => [
                        'source_type' => 'service',
                        'source_id' => $service->id,
                        'source_title' => $service->title,
                        'source_category' => $service->category?->name ?? 'بدون دسته‌بندی',
                        'payment_flow' => 'deduct_on_result',
                        'user_type' => 'authenticated',
                        'transaction_context' => 'service_payment_on_success'
                    ]
                ], true); // true = confirmed transaction

                Log::info('✅ DEDUCT ON RESULT: Money deducted after successful service processing', [
                    'user_id' => $user->id,
                    'service_id' => $service->id,
                    'wallet_transaction_id' => $walletTransaction->id,
                    'amount_deducted' => $service->price,
                    'user_balance_after' => $user->fresh()->balance,
                    'timing' => 'after_service_success'
                ]);

                // Store the result
                $serviceResult = ServiceResult::create([
                    'service_id' => $service->id,
                    'user_id' => $user->id,
                    'input_data' => $serviceData,
                    'output_data' => $result['data'] ?? [],
                    'status' => 'success',
                    'processed_at' => now(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'wallet_transaction_id' => $walletTransaction->id,
                ]);

                DB::commit();

                Log::info('🎉 DEDUCT ON RESULT: Service completed successfully - no refunds needed!', [
                    'user_id' => $user->id,
                    'service_id' => $service->id,
                    'result_hash' => $serviceResult->result_hash,
                    'wallet_transaction_id' => $walletTransaction->id,
                    'approach' => 'deduct_on_result_eliminates_refunds'
                ]);

                $redirectUrl = route('services.result', ['id' => $serviceResult->result_hash]);
                
                Log::info('🔄 DEDUCT ON RESULT: Redirecting to result page', [
                    'user_id' => $user->id,
                    'service_id' => $service->id,
                    'service_slug' => $service->slug,
                    'result_hash' => $serviceResult->result_hash,
                    'redirect_url' => $redirectUrl,
                    'route_name' => 'services.result',
                    'route_parameters' => ['id' => $serviceResult->result_hash]
                ]);

                return [
                    'success' => true,
                    'message' => 'سرویس با موفقیت پردازش شد.',
                    'redirect' => $redirectUrl
                ];
            } else {
                // 🚫 DEDUCT ON RESULT: Service failed - NO deduction, NO refund needed!
                DB::commit();

                Log::info('🚫 DEDUCT ON RESULT: Service failed - redirecting to service page with error', [
                    'user_id' => $user->id,
                    'service_id' => $service->id,
                    'service_slug' => $service->slug,
                    'failure_reason' => $result['message'] ?? 'خطا در پردازش سرویس',
                    'user_balance_unchanged' => $user->balance,
                    'approach' => 'redirect_to_service_page_for_better_ux'
                ]);

                return [
                    'success' => false,
                    'message' => $result['message'] ?? 'خطا در پردازش سرویس',
                    'redirect' => route('services.show', $service->slug)
                ];
            }

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('💥 DEDUCT ON RESULT: Service processing failed with exception - redirecting to service page', [
                'user_id' => $user->id,
                'service_id' => $service->id,
                'service_slug' => $service->slug,
                'error' => $e->getMessage(),
                'user_balance_unchanged' => $user->balance,
                'approach' => 'redirect_to_service_page_on_exception_for_better_ux'
            ]);

            return [
                'success' => false,
                'message' => 'خطا در پردازش سرویس: ' . $e->getMessage(),
                'redirect' => route('services.show', $service->slug)
            ];
        }
    }

    /**
     * Handle insufficient wallet balance
     *
     * @param Request $request
     * @param Service $service
     * @param array $serviceData
     * @param User $user
     * @return array
     */
    protected function handleInsufficientBalance(Request $request, Service $service, array $serviceData, User $user): array
    {
        // Store service request for processing after payment (no transaction needed)
        $serviceRequest = $this->storeServiceRequest($service, $serviceData, 'insufficient_balance', $user->id);

        // Store service data in session for later processing after wallet charge
        Session::put('pending_service_id', $service->id);
        Session::put('pending_service_data', $serviceData);
        Session::put('pending_service_request_id', $serviceRequest->id);
        Session::put('pending_service_redirect', url()->current());
        
        $shortfall = $service->price - $user->balance;
        
        Log::info('User redirected to wallet due to insufficient balance', [
            'user_id' => $user->id,
            'service_id' => $service->id,
            'service_request_id' => $serviceRequest->id,
            'current_balance' => $user->balance,
            'required_amount' => $service->price,
            'shortfall' => $shortfall
        ]);

        // Redirect to service preview page where user can select amount to charge
        return [
            'success' => true,
            'message' => "موجودی کیف پول شما کافی نیست. برای استفاده از این سرویس، حداقل {$shortfall} تومان بیشتر نیاز دارید.",
            'redirect' => route('services.preview.user', ['service' => $service->id, 'hash' => $serviceRequest->request_hash]),
            'show_wallet_charge' => true,
            'suggested_amount' => $service->price,
            'pending_service' => $service->title
        ];
    }

    /**
     * Process payment callback for service payments
     *
     * @param GatewayTransaction $transaction
     * @param bool $shouldChargeWallet Whether to charge the wallet (default: true)
     * @return array
     */
    public function processPaymentCallback(GatewayTransaction $transaction, bool $shouldChargeWallet = true): array
    {
        try {
            DB::beginTransaction();

            $metadata = $transaction->metadata ?? [];
            $paymentType = $metadata['type'] ?? '';

            if ($paymentType === 'service_payment') {
                return $this->processServicePaymentCallback($transaction, $shouldChargeWallet);
            } elseif ($paymentType === 'wallet_charge_for_service') {
                return $this->processWalletChargeForServiceCallback($transaction, $shouldChargeWallet);
            }

            DB::rollBack();
            return [
                'success' => false,
                'message' => 'نوع پرداخت نامعتبر است.'
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Service payment callback error: ' . $e->getMessage(), [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'خطا در پردازش بازگشت پرداخت: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Process service payment callback (guest users)
     *
     * @param GatewayTransaction $transaction
     * @return array
     */
    protected function processServicePaymentCallback(GatewayTransaction $transaction, bool $shouldChargeWallet = true): array
    {
        $metadata = $transaction->metadata ?? [];
        $serviceId = $metadata['service_id'] ?? null;
        $serviceRequestId = $metadata['service_request_id'] ?? null;
        $serviceData = $metadata['service_data'] ?? [];

        if (!$serviceId || !$serviceRequestId) {
            return [
                'success' => false,
                'message' => 'اطلاعات سرویس نامعتبر است.'
            ];
        }

        $service = Service::find($serviceId);
        if (!$service) {
            return [
                'success' => false,
                'message' => 'سرویس یافت نشد.'
            ];
        }

        // Find the service request
        $serviceRequest = ServiceRequest::find($serviceRequestId);
        if (!$serviceRequest) {
            return [
                'success' => false,
                'message' => 'درخواست سرویس یافت نشد.'
            ];
        }

        // Process the service
        $result = $this->processService($service, $serviceData);

        if ($result['success']) {
            // Store the result
            $serviceResult = ServiceResult::create([
                'service_id' => $service->id,
                'user_id' => $transaction->user_id,
                'input_data' => $serviceData,
                'output_data' => $result['data'],
                'status' => 'success',
                'processed_at' => now(),
                'ip_address' => $transaction->ip_address,
                'user_agent' => $transaction->user_agent,
            ]);

            // Mark service request as processed
            $serviceRequest->update([
                'payment_transaction_id' => $transaction->id,
                'processed_at' => now(),
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'سرویس با موفقیت پردازش شد.',
                'redirect' => route('services.result', ['id' => $serviceResult->result_hash])
            ];
        } else {
            DB::rollBack();
            
            Log::info('🚫 SERVICE CALLBACK: Service failed - redirecting to service page', [
                'service_id' => $service->id,
                'service_slug' => $service->slug,
                'failure_reason' => $result['message'],
                'approach' => 'redirect_to_service_page_for_better_ux'
            ]);
            
            return [
                'success' => false,
                'message' => $result['message'],
                'redirect' => route('services.show', $service->slug)
            ];
        }
    }

    /**
     * Process wallet charge for service callback
     *
     * @param GatewayTransaction $transaction
     * @return array
     */
    protected function processWalletChargeForServiceCallback(GatewayTransaction $transaction, bool $shouldChargeWallet = true): array
    {
        Log::info('🔄 GUEST PAYMENT: Starting wallet charge for service callback processing', [
            'transaction_id' => $transaction->id,
            'transaction_uuid' => $transaction->uuid,
            'transaction_status' => $transaction->status,
            'transaction_amount' => $transaction->total_amount,
            'transaction_user_id' => $transaction->user_id,
            'payment_gateway' => $transaction->paymentGateway?->name ?? 'Unknown',
        ]);

        $metadata = $transaction->metadata ?? [];
        $serviceId = $metadata['service_id'] ?? null;
        $serviceRequestId = $metadata['service_request_id'] ?? null;
        $serviceRequestHash = $metadata['service_request_hash'] ?? null;
        $serviceData = $metadata['service_data'] ?? [];

        Log::info('🔍 GUEST PAYMENT: Analyzing transaction metadata', [
            'transaction_uuid' => $transaction->uuid,
            'service_id' => $serviceId,
            'service_request_id' => $serviceRequestId,
            'service_request_hash' => $serviceRequestHash,
            'has_service_data' => !empty($serviceData),
            'metadata_keys' => array_keys($metadata),
            'payment_type' => $metadata['type'] ?? 'unknown',
            'guest_session' => $metadata['guest_session'] ?? null,
        ]);

        if (!$serviceId) {
            Log::error('❌ GUEST PAYMENT: Missing service ID in metadata', [
                'transaction_uuid' => $transaction->uuid,
                'metadata_keys' => array_keys($metadata),
            ]);
            return [
                'success' => false,
                'message' => 'اطلاعات سرویس نامعتبر است.'
            ];
        }

        $service = Service::find($serviceId);
        if (!$service) {
            Log::error('❌ GUEST PAYMENT: Service not found', [
                'transaction_uuid' => $transaction->uuid,
                'service_id' => $serviceId,
            ]);
            return [
                'success' => false,
                'message' => 'سرویس یافت نشد.'
            ];
        }

        Log::info('✅ GUEST PAYMENT: Service found', [
            'transaction_uuid' => $transaction->uuid,
            'service_id' => $service->id,
            'service_title' => $service->title,
            'service_slug' => $service->slug,
            'service_price' => $service->price,
        ]);

        // Find the service request - try ID first, then hash for guest payments
        $serviceRequest = null;
        if ($serviceRequestId) {
            Log::info('🔍 GUEST PAYMENT: Looking for service request by ID', [
                'transaction_uuid' => $transaction->uuid,
                'service_request_id' => $serviceRequestId,
            ]);
            $serviceRequest = ServiceRequest::find($serviceRequestId);
        } elseif ($serviceRequestHash) {
            Log::info('🔍 GUEST PAYMENT: Looking for service request by hash (guest payment)', [
                'transaction_uuid' => $transaction->uuid,
                'service_request_hash' => $serviceRequestHash,
            ]);
            $serviceRequest = ServiceRequest::where('request_hash', $serviceRequestHash)->first();
        }
        
        if (!$serviceRequest) {
            Log::error('❌ GUEST PAYMENT: Service request not found', [
                'transaction_uuid' => $transaction->uuid,
                'service_request_id' => $serviceRequestId,
                'service_request_hash' => $serviceRequestHash,
                'search_method' => $serviceRequestId ? 'by_id' : ($serviceRequestHash ? 'by_hash' : 'none'),
            ]);
            return [
                'success' => false,
                'message' => 'درخواست سرویس یافت نشد.'
            ];
        }

        Log::info('✅ GUEST PAYMENT: Service request found', [
            'transaction_uuid' => $transaction->uuid,
            'service_request_id' => $serviceRequest->id,
            'service_request_hash' => $serviceRequest->request_hash ?? 'N/A',
            'service_request_status' => $serviceRequest->status ?? 'N/A',
            'found_by' => $serviceRequestId ? 'id' : 'hash',
        ]);

        $user = $transaction->user;
        if (!$user) {
            Log::info('🎯 GUEST PAYMENT: Processing guest payment - setting up session data', [
                'transaction_uuid' => $transaction->uuid,
                'transaction_id' => $transaction->id,
                'service_id' => $serviceId,
                'service_title' => $service->title,
                'amount' => $transaction->total_amount,
                'guest_session' => $metadata['guest_session'] ?? 'N/A',
            ]);

            // This is a guest payment, set up session data for later processing after login
            Session::put('guest_payment_success', true);
            Session::put('guest_payment_transaction_id', $transaction->id);
            Session::put('guest_payment_service_id', $serviceId);
            Session::put('guest_payment_amount', $transaction->total_amount);
            Session::put('guest_session_token', $metadata['guest_session'] ?? null);
            
            // Store service request hash if available
            $requestHash = $metadata['service_request_hash'] ?? null;
            if ($requestHash) {
                Session::put('pending_service_request_hash', $requestHash);
            }
            
            Log::info('✅ GUEST PAYMENT: Session data successfully prepared for after-login processing', [
                'transaction_id' => $transaction->id,
                'transaction_uuid' => $transaction->uuid,
                'service_id' => $serviceId,
                'service_title' => $service->title,
                'amount' => $transaction->total_amount,
                'service_request_hash' => $requestHash,
                'session_keys_set' => [
                    'guest_payment_success',
                    'guest_payment_transaction_id', 
                    'guest_payment_service_id',
                    'guest_payment_amount',
                    'guest_session_token',
                    $requestHash ? 'pending_service_request_hash' : null
                ],
                'next_step' => 'user_login_required'
            ]);
            
            return [
                'success' => true,
                'message' => 'پرداخت با موفقیت انجام شد. لطفاً وارد حساب کاربری خود شوید تا کیف‌پول شما شارژ شود.',
                'redirect' => route('app.page.home'),
                'guest_payment' => true,
                'requires_login' => true
            ];
        }

        // Add amount to user's wallet (only if shouldChargeWallet is true)
        if ($shouldChargeWallet) {
            $user->deposit($transaction->amount, [
                'description' => "شارژ کیف‌پول از طریق درگاه پرداخت",
                'gateway_transaction_id' => $transaction->id,
                'gateway_reference_id' => $transaction->gateway_reference_id,
                'service_id' => $service->id,
                'service_title' => $service->title,
                'service_slug' => $service->slug,
                'service_category_id' => $service->category_id,
                'service_price' => $service->price,
                'type' => 'wallet_charge_for_service',
                'payment_source' => 'gateway_payment',
                'payment_method' => 'gateway',
                'gateway_name' => $transaction->paymentGateway?->name ?? 'نامشخص',
                'processed_at' => now()->toISOString(),
                'source_tracking' => [
                    'source_type' => 'service',
                    'source_id' => $service->id,
                    'source_title' => $service->title,
                    'source_category' => $service->category?->name ?? 'بدون دسته‌بندی',
                    'payment_flow' => 'gateway_to_wallet',
                    'user_type' => $transaction->user_id ? 'authenticated' : 'guest',
                    'transaction_context' => 'wallet_charge_for_service_continuation',
                    'gateway_transaction_id' => $transaction->id,
                    'gateway_name' => $transaction->paymentGateway?->name ?? 'نامشخص'
                ]
            ]);
        }

        // Deduct service cost from wallet
        $user->withdraw($service->price, [
            'description' => "پرداخت سرویس: {$service->title}",
            'service_id' => $service->id,
            'service_title' => $service->title,
            'service_slug' => $service->slug,
            'service_category_id' => $service->category_id,
            'service_price' => $service->price,
            'service_cost' => $service->cost,
            'type' => 'service_payment',
            'payment_source' => 'wallet_after_gateway_charge',
            'payment_method' => 'wallet',
            'related_gateway_transaction_id' => $transaction->id,
            'processed_at' => now()->toISOString(),
            'source_tracking' => [
                'source_type' => 'service',
                'source_id' => $service->id,
                'source_title' => $service->title,
                'source_category' => $service->category?->name ?? 'بدون دسته‌بندی',
                'payment_flow' => 'wallet_payment_after_gateway_charge',
                'user_type' => $transaction->user_id ? 'authenticated' : 'guest',
                'transaction_context' => 'service_payment_after_wallet_charge',
                'related_gateway_transaction_id' => $transaction->id
            ]
        ]);

        // Process the service
        $result = $this->processService($service, $serviceData);

        if ($result['success']) {
            // Store the result
            $serviceResult = ServiceResult::create([
                'service_id' => $service->id,
                'user_id' => $transaction->user_id,
                'input_data' => $serviceData,
                'output_data' => $result['data'],
                'status' => 'success',
                'processed_at' => now(),
                'ip_address' => $transaction->ip_address,
                'user_agent' => $transaction->user_agent,
            ]);

            // Mark service request as processed
            $serviceRequest->update([
                'payment_transaction_id' => $transaction->id,
                'processed_at' => now(),
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'سرویس با موفقیت پردازش شد.',
                'redirect' => route('services.result', ['id' => $serviceResult->result_hash])
            ];
        } else {
            // Refund the user if service fails
            $user->deposit($service->price, [
                'description' => "بازگشت وجه - خطا در پردازش سرویس: {$service->title}",
                'service_id' => $service->id,
                'type' => 'service_refund'
            ]);

            DB::commit();

            Log::info('🚫 WALLET CALLBACK: Service failed - redirecting to service page', [
                'service_id' => $service->id,
                'service_slug' => $service->slug,
                'failure_reason' => $result['message'],
                'approach' => 'redirect_to_service_page_for_better_ux'
            ]);

            return [
                'success' => false,
                'message' => $result['message'],
                'redirect' => route('services.show', $service->slug)
            ];
        }
    }

    /**
     * Store service request for processing after payment
     *
     * @param Service $service
     * @param array $serviceData
     * @param string $status
     * @param int|null $userId
     * @return ServiceRequest
     */
    protected function storeServiceRequest(Service $service, array $serviceData, string $status, ?int $userId = null): ServiceRequest
    {
        return ServiceRequest::create([
            'service_id' => $service->id,
            'user_id' => $userId,
            'input_data' => $serviceData,
            'status' => $status,
        ]);
    }

    /**
     * Process the actual service using the appropriate service controller
     *
     * @param Service $service
     * @param array $serviceData
     * @return array
     */
    public function processService(Service $service, array $serviceData): array
    {
        try {
            // Get the appropriate service controller
            $serviceController = \App\Http\Controllers\Services\ServiceControllerFactory::getController($service);
            
            if (!$serviceController) {
                return [
                    'success' => false,
                    'message' => 'سرویس از سمت تامین کننده دولتی دچار اختلال است، به زودی مشکل بر طرف می شود.<br>لطفا ساعاتی دیگر تلاش کنید'
                ];
            }

            // Call the service controller's process method directly
            $result = $serviceController->process($serviceData, $service);
            
            return $result;

        } catch (\Exception $e) {
            Log::error('Service processing error: ' . $e->getMessage(), [
                'service_id' => $service->id,
                'service_data' => $serviceData,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'خطا در پردازش سرویس: ' . $e->getMessage()
            ];
        }
    }
} 