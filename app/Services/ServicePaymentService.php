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
     * Process paid service with wallet balance using confirmation-based transactions
     *
     * @param Request $request
     * @param Service $service
     * @param array $serviceData
     * @param User $user
     * @return array
     */
    protected function processPaidServiceWithWallet(Request $request, Service $service, array $serviceData, User $user): array
    {
        // Use the new confirmation-based payment service to eliminate deduction-refund cycles
        $confirmationService = app(ConfirmationBasedPaymentService::class);
        
        return $confirmationService->processServiceWithConfirmation($request, $service, $serviceData, $user);
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
     * @return array
     */
    public function processPaymentCallback(GatewayTransaction $transaction): array
    {
        try {
            DB::beginTransaction();

            $metadata = $transaction->metadata ?? [];
            $paymentType = $metadata['type'] ?? '';

            if ($paymentType === 'service_payment') {
                return $this->processServicePaymentCallback($transaction);
            } elseif ($paymentType === 'wallet_charge_for_service') {
                return $this->processWalletChargeForServiceCallback($transaction);
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
    protected function processServicePaymentCallback(GatewayTransaction $transaction): array
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
            return [
                'success' => false,
                'message' => $result['message']
            ];
        }
    }

    /**
     * Process wallet charge for service callback
     *
     * @param GatewayTransaction $transaction
     * @return array
     */
    protected function processWalletChargeForServiceCallback(GatewayTransaction $transaction): array
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

        $user = $transaction->user;
        if (!$user) {
            // This is a guest payment, we'll handle it differently
            // The user will be created/claimed after phone verification
            return [
                'success' => false,
                'message' => 'پرداخت مهمان - نیاز به تایید شماره موبایل.'
            ];
        }

        // Add amount to user's wallet
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

            return [
                'success' => false,
                'message' => $result['message']
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