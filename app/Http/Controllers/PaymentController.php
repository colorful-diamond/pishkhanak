<?php

namespace App\Http\Controllers;

use App\Models\GatewayTransaction;
use App\Models\PaymentGateway;
use App\Models\User;
use App\Services\PaymentGatewayManager;
use App\Services\PaymentService;
use App\Services\ServicePaymentService;
use App\Jobs\SendTelegramNotificationJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Exception;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;

class PaymentController extends Controller
{
    protected PaymentGatewayManager $gatewayManager;
    protected PaymentService $paymentService;

    public function __construct(PaymentGatewayManager $gatewayManager, PaymentService $paymentService)
    {
        $this->gatewayManager = $gatewayManager;
        $this->paymentService = $paymentService;
    }

    /**
     * Initialize payment with any gateway
     */
    public function initializePayment(Request $request)
    {
        $request->validate([
            'gateway_id' => 'required|exists:payment_gateways,id',
            'amount' => 'required|numeric|min:1000',
            'currency' => 'nullable|string|in:IRT,USD,EUR',
            'description' => 'nullable|string|max:500',
            'callback_url' => 'nullable|url',
            'metadata' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $gateway = PaymentGateway::findOrFail($request->gateway_id);
            
            // Check if gateway is active
            if (!$gateway->is_active) {
                throw new Exception('درگاه پرداخت انتخاب شده در دسترس نیست');
            }

            // Check if gateway supports the amount
            if (!$gateway->supportsAmount($request->amount)) {
                throw new Exception('مبلغ وارد شده توسط درگاه انتخاب شده پشتیبانی نمی‌شود');
            }

            // Check if gateway supports the currency
            $currency = $request->currency ?? 'IRT';
            if (!$gateway->supportsCurrency($currency)) {
                throw new Exception('واحد پول وارد شده توسط درگاه انتخاب شده پشتیبانی نمی‌شود');
            }

            // Create payment transaction
            $metadata = $request->metadata ?? [];
            $paymentType = 'wallet_charge';
            
            // Determine payment type from metadata
            if (isset($metadata['type'])) {
                $paymentType = $metadata['type'];
            }
            
            $paymentData = [
                'user_id' => $user ? $user->id : null,
                'gateway_id' => $gateway->id,
                'amount' => $request->amount,
                'currency' => $currency,
                'description' => $request->description ?? 'پرداخت از طریق ' . $gateway->name,
                'callback_url' => $request->callback_url,
                'metadata' => $metadata,
                'type' => $paymentType,
            ];

            $paymentResult = $this->paymentService->createPayment($paymentData);

            if (!$paymentResult['success']) {
                throw new Exception($paymentResult['message'] ?? 'خطا در ایجاد پرداخت');
            }

            $transaction = $paymentResult['transaction'];
            
            // PaymentService already created the payment with gateway, 
            // so we just need to get the result from it
            $gatewayResult = $paymentResult['gateway_result'] ?? $paymentResult;

            if (!$gatewayResult['success']) {
                throw new Exception($gatewayResult['message'] ?? 'خطا در ایجاد پرداخت در درگاه');
            }

            DB::commit();

            // Handle different redirect methods
            return $this->handlePaymentRedirect($gatewayResult, $transaction);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Payment initialization failed', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
                'user_id' => Auth::id(),
            ]);

            return back()->with('error', 'خطا در ایجاد پرداخت: ' . $e->getMessage());
        }
    }

    /**
     * Handle payment redirect based on gateway requirements
     */
    protected function handlePaymentRedirect(array $gatewayResult, GatewayTransaction $transaction)
    {
        // Handle gateway results that don't wrap response in 'data' key
        $data = isset($gatewayResult['data']) ? $gatewayResult['data'] : $gatewayResult;
        
        // Check if gateway requires form submission (like Asan Pardakht)
        if (isset($data['redirect_method']) && $data['redirect_method'] === 'form_submit') {
            return response()->view('payments.gateway-redirect', [
                'transaction' => $transaction,
                'gateway_data' => $data,
                'payment_form' => $data['payment_form'],
            ]);
        }

        // Standard URL redirect
        if (isset($data['payment_url'])) {
            return redirect($data['payment_url']);
        }

        // If no redirect method is available, return error
        return back()->with('error', 'خطا در هدایت به درگاه پرداخت');
    }

    /**
     * Handle payment callback from any gateway
     */
    public function handleCallback(Request $request, string $gateway, string $transaction = null)
    {
        try {
            Log::info('Payment callback received', [
                'gateway' => $gateway,
                'transaction' => $transaction,
                'request_data' => $request->all(),
            ]);

            // Parse payload if present
            $payload = null;
            if ($request->has('payload')) {
                try {
                    $payload = json_decode($request->payload, true);
                    Log::info('Parsed payload data', ['payload' => $payload]);
                } catch (\Exception $e) {
                    Log::warning('Failed to parse payload', ['payload' => $request->payload]);
                }
            }

            // Find the gateway by slug
            $paymentGateway = PaymentGateway::where('slug', $gateway)->where('is_active', true)->first();
            
            if (!$paymentGateway) {
                throw new Exception('درگاه پرداخت نامعتبر یا غیرفعال است');
            }

            // Find the transaction
            $gatewayTransaction = null;
            
            // First, try to find by UUID from payload
            if ($payload && isset($payload['transaction_uuid'])) {
                $gatewayTransaction = GatewayTransaction::where('uuid', $payload['transaction_uuid'])->first();
                Log::info('Found transaction by payload UUID', ['uuid' => $payload['transaction_uuid']]);
            }
            
            // If not found, try URL parameter
            if (!$gatewayTransaction && $transaction) {
                // Transaction UUID provided in URL
                $gatewayTransaction = GatewayTransaction::where('uuid', $transaction)->first();
            }
            
            // If still not found, try callback data
            if (!$gatewayTransaction && ($request->has('invoice') || $request->has('order_id') || $request->has('invoiceid'))) {
                // Try to find by reference ID from callback data
                $referenceId = $request->invoice ?? $request->order_id ?? $request->invoiceid;
                $gatewayTransaction = GatewayTransaction::where('reference_id', $referenceId)
                    ->orWhere('uuid', $referenceId)
                    ->first();
            }

            if (!$gatewayTransaction) {
                throw new Exception('تراکنش یافت نشد');
            }

            // Get gateway instance
            $gatewayInstance = $this->gatewayManager->gatewayById($paymentGateway->id);
            
            // Verify payment
            $verificationResult = $gatewayInstance->verifyPayment($gatewayTransaction, $request->all());

            // Update transaction status with callback data
            $this->updateTransactionStatus($gatewayTransaction, $verificationResult, $request->all());

            // Redirect to appropriate page
            return $this->handleCallbackRedirect($gatewayTransaction, $verificationResult, $payload);

        } catch (Exception $e) {
            Log::error('Payment callback failed', [
                'error' => $e->getMessage(),
                'gateway' => $gateway,
                'transaction' => $transaction,
                'request_data' => $request->all(),
            ]);

            return $this->redirectToFailurePage($e->getMessage());
        }
    }

    /**
     * Update transaction status based on verification result
     */
    protected function updateTransactionStatus(GatewayTransaction $transaction, array $verificationResult, array $callbackData = [])
    {
        DB::beginTransaction();

        try {
            Log::info('Updating transaction status', [
                'transaction_id' => $transaction->id,
                'verification_success' => $verificationResult['success'] ?? false,
                'verified' => $verificationResult['verified'] ?? false,
                'amount' => $transaction->amount,
                'total_amount' => $transaction->total_amount,
                'type' => $transaction->type,
                'user_id' => $transaction->user_id,
            ]);
            
            if ($verificationResult['success'] && ($verificationResult['verified'] ?? false)) {
                // Payment successful
                $transaction->update([
                    'status' => 'completed',
                    'gateway_reference_id' => $verificationResult['reference_id'] ?? $verificationResult['rrn'] ?? null,
                    'gateway_data' => array_merge($transaction->gateway_data ?? [], [
                        'verification_result' => $verificationResult,
                        'verified_at' => now(),
                        'callback_data' => $callbackData,
                    ]),
                ]);

                Log::info('Transaction marked as completed', [
                    'transaction_id' => $transaction->id,
                    'type' => $transaction->type,
                    'will_process_wallet_charge' => $transaction->type === 'wallet_charge',
                ]);

                // If this is a wallet charge, update user balance
                if ($transaction->type === 'wallet_charge') {
                    $this->processWalletCharge($transaction);
                }

                // Dispatch Telegram notification for successful payment
                try {
                    SendTelegramNotificationJob::dispatch($transaction)->onQueue('notifications');
                    Log::info('Telegram notification job dispatched', [
                        'transaction_id' => $transaction->id
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to dispatch Telegram notification job', [
                        'transaction_id' => $transaction->id,
                        'error' => $e->getMessage()
                    ]);
                }

            } else {
                // Payment failed - store callback data for error determination
                $transaction->update([
                    'status' => 'failed',
                    'failure_reason' => $verificationResult['message'] ?? 'خطا در تأیید پرداخت',
                    'gateway_data' => array_merge($transaction->gateway_data ?? [], [
                        'verification_result' => $verificationResult,
                        'failed_at' => now(),
                        'callback_data' => $callbackData,
                        'respcode' => $callbackData['respcode'] ?? null,
                        'respmsg' => $callbackData['respmsg'] ?? null,
                    ]),
                ]);
            }

            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to update transaction status', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Process wallet charge for successful payment
     */
    protected function processWalletCharge(GatewayTransaction $transaction)
    {
        $user = $transaction->user;
        $metadata = $transaction->metadata ?? [];
        
        // Check if this is a non-authenticated user payment that requires login
        if (!$user && isset($metadata['requires_login']) && $metadata['requires_login']) {
            // Store transaction ID in session for processing after login
            Session::put('pending_wallet_charge_transaction_id', $transaction->id);
            
            Log::info('Non-authenticated wallet charge pending login', [
                'transaction_id' => $transaction->id,
                'amount' => $transaction->amount,
                'metadata' => $metadata
            ]);
            
            // Don't process the wallet charge yet - wait for user to login
            return;
        }
        
        // Check if this is a service payment
        if (isset($metadata['type']) && in_array($metadata['type'], ['service_payment', 'wallet_charge_for_service'])) {
            $this->processServicePayment($transaction);
            return;
        }
        
        // Regular wallet charge - user must exist at this point
        if (!$user) {
            Log::error('No user found for wallet charge transaction', [
                'transaction_id' => $transaction->id,
                'metadata' => $metadata
            ]);
            throw new \Exception('کاربر یافت نشد برای شارژ کیف پول');
        }
        
        // Load the gateway relationship if not loaded
        if (!$transaction->relationLoaded('paymentGateway')) {
            $transaction->load('paymentGateway');
        }
        
        // Get gateway name safely
        $gatewayName = $transaction->paymentGateway ? $transaction->paymentGateway->name : 'درگاه پرداخت';
        
        Log::info('Starting wallet deposit', [
            'user_id' => $user->id,
            'transaction_id' => $transaction->id,
            'amount' => $transaction->amount,
            'gateway_id' => $transaction->payment_gateway_id,
            'gateway_name' => $gatewayName,
            'balance_before' => $user->balance,
            'balance_float_before' => $user->balanceFloat,
        ]);
        
        try {
            $user->deposit($transaction->amount, [
                'description' => 'شارژ کیف‌پول از طریق ' . $gatewayName,
                'gateway_transaction_id' => $transaction->id,
                'gateway_reference_id' => $transaction->gateway_reference_id,
                'type' => 'wallet_charge',
                'payment_source' => 'gateway_payment',
                'payment_method' => 'gateway',
                'gateway_name' => $gatewayName,
                'processed_at' => now()->toISOString(),
                'source_tracking' => [
                    'source_type' => isset($metadata['service_id']) ? 'service' : 'manual_wallet_charge',
                    'source_id' => $metadata['service_id'] ?? null,
                    'source_title' => $metadata['service_title'] ?? null,
                    'source_category' => $metadata['service_category'] ?? null,
                    'payment_flow' => 'gateway_to_wallet',
                    'user_type' => $transaction->user_id ? 'authenticated' : 'guest',
                    'transaction_context' => isset($metadata['service_id']) ? 'wallet_charge_for_service' : 'manual_wallet_charge',
                    'gateway_transaction_id' => $transaction->id,
                    'gateway_name' => $gatewayName,
                    'original_metadata' => $metadata
                ]
            ]);

            // Force refresh the user's wallet balance
            $user->wallet->refreshBalance();
            
            Log::info('Wallet deposit completed', [
                'user_id' => $user->id,
                'transaction_id' => $transaction->id,
                'balance_after' => $user->balance,
                'balance_float_after' => $user->balanceFloat,
                'wallet_balance' => $user->wallet->balance,
            ]);

            // Check and process any pending services after wallet charge
            $this->processPendingServices($user);

            Log::info('Wallet charged successfully', [
                'user_id' => $user->id,
                'amount' => $transaction->amount,
                'transaction_id' => $transaction->id,
                'final_balance' => $user->balance,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Wallet deposit failed', [
                'user_id' => $user->id,
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Process pending services after successful wallet charge
     */
    protected function processPendingServices(User $user)
    {
        try {
            $result = \App\Http\Controllers\Services\ServicePaymentTrait::processPendingService($user);
            
            if ($result && $result['success']) {
                Log::info('Pending service processed after wallet charge', [
                    'user_id' => $user->id,
                    'result' => $result['message']
                ]);
            } elseif ($result && !$result['success']) {
                Log::warning('Pending service processing failed after wallet charge', [
                    'user_id' => $user->id,
                    'error' => $result['message']
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error processing pending services after wallet charge', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Process service payment after successful payment
     */
    protected function processServicePayment(GatewayTransaction $transaction)
    {
        $metadata = $transaction->metadata ?? [];
        $user = $transaction->user;
        
        // Handle wallet charge for service continuation
        if (isset($metadata['type']) && $metadata['type'] === 'wallet_charge_for_service') {
            // First, add the payment amount to user's wallet
            $user->deposit($transaction->amount, [
                'description' => 'شارژ کیف‌پول برای ادامه سرویس',
                'gateway_transaction_id' => $transaction->id,
                'gateway_reference_id' => $transaction->gateway_reference_id,
            ]);

            // Process service continuation if metadata is available
            if (isset($metadata['continue_service'])) {
                $this->processPendingServiceContinuation($user, $metadata);
                return;
            }
        }
        
        // Regular service payment processing
        $servicePaymentService = app(ServicePaymentService::class);
        $result = $servicePaymentService->processPaymentCallback($transaction);
        
        if ($result['success']) {
            Log::info('Service payment processed successfully', [
                'transaction_id' => $transaction->id,
                'service_id' => $metadata['service_id'] ?? null,
            ]);
        } else {
            Log::error('Service payment processing failed', [
                'transaction_id' => $transaction->id,
                'error' => $result['message'],
            ]);
        }
    }

    /**
     * Process service continuation after wallet charge
     */
    protected function processPendingServiceContinuation($user, $metadata)
    {
        try {
            // Look for pending service data in session or database
            $serviceSlug = $metadata['continue_service'] ?? null;
            $serviceRequestHash = $metadata['service_request_hash'] ?? null;
            $sessionKey = $metadata['service_session_key'] ?? null;

            if ($serviceSlug && $sessionKey) {
                // Retrieve service data from session
                $serviceData = Session::get($sessionKey, []);
                
                if (!empty($serviceData)) {
                    $service = \App\Models\Service::where('slug', $serviceSlug)->first();
                    
                    if ($service && $user->balance >= $service->price) {
                        // Process the service now that wallet has sufficient balance
                        $servicePaymentService = app(\App\Services\ServicePaymentService::class);
                        
                        // Create a simulated request for the service processing
                        $simulatedRequest = new Request();
                        $simulatedRequest->replace($serviceData);
                        $simulatedRequest->setMethod('POST');
                        
                        $result = $servicePaymentService->handleServiceSubmission(
                            $simulatedRequest, 
                            $service, 
                            $serviceData
                        );

                        if ($result['success']) {
                            // Clear the session data
                            Session::forget($sessionKey);
                            
                            Log::info('Service continued successfully after wallet charge', [
                                'user_id' => $user->id,
                                'service_slug' => $serviceSlug,
                                'redirect' => $result['redirect']
                            ]);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Error processing service continuation after wallet charge', [
                'user_id' => $user->id,
                'service_slug' => $metadata['continue_service'] ?? null,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle callback redirect based on transaction status
     */
    protected function handleCallbackRedirect(GatewayTransaction $transaction, array $verificationResult, ?array $payload = null)
    {
        $user = $transaction->user;
        $metadata = $transaction->metadata ?? [];
        
        // Log verification result for debugging
        Log::info('Payment verification result in redirect handler', [
            'transaction_id' => $transaction->id,
            'verification_success' => $verificationResult['success'] ?? false,
            'verified' => $verificationResult['verified'] ?? false,
            'gateway' => $transaction->paymentGateway->slug ?? 'unknown',
            'user_id' => $user ? $user->id : null,
            'transaction_type' => $transaction->type,
            'metadata' => $metadata,
        ]);
        
        if ($verificationResult['success'] && ($verificationResult['verified'] ?? false)) {
            // Successful payment
            
            // Check if this is a non-authenticated user payment
            if (!$user && isset($metadata['requires_login']) && $metadata['requires_login']) {
                // Store transaction UUID in multiple places for redundancy
                Session::put('pending_wallet_charge_transaction_uuid', $transaction->uuid);
                Session::put('pending_wallet_charge_transaction_id', $transaction->id);
                Session::put('pending_wallet_charge_success', true);
                Session::put('pending_wallet_charge_amount', $transaction->amount);
                
                // Also store in a cookie for better persistence
                Cookie::queue('pending_transaction', $transaction->uuid, 60); // 60 minutes
                
                // If there's a service to continue after login, store that info
                if (isset($metadata['continue_service'])) {
                    Session::put('pending_service_continuation', [
                        'service' => $metadata['continue_service'],
                        'request_hash' => $metadata['service_request_hash'] ?? null,
                        'session_key' => $metadata['service_session_key'] ?? null,
                    ]);
                }
                
                // If payload has service info, also store from there
                if ($payload && isset($payload['continue_service'])) {
                    Session::put('pending_service_continuation', [
                        'service' => $payload['continue_service'],
                        'request_hash' => $payload['service_request_hash'] ?? null,
                    ]);
                }
                
                return redirect()->route('app.auth.login')
                    ->with('success', 'پرداخت با موفقیت انجام شد. لطفاً وارد حساب کاربری خود شوید.')
                    ->with('show_wallet_info', true);
            }
            
            if (isset($metadata['type']) && in_array($metadata['type'], ['service_payment', 'wallet_charge_for_service'])) {
                // Service payment - redirect to service result
                $servicePaymentService = app(ServicePaymentService::class);
                $result = $servicePaymentService->processPaymentCallback($transaction);
                
                if ($result['success'] && isset($result['redirect'])) {
                    return redirect($result['redirect']);
                } else {
                    return redirect()->route('app.user.wallet')->with('error', $result['message'] ?? 'خطا در پردازش سرویس');
                }
            } else {
                // Regular wallet charge
                $successMessage = match ($transaction->type) {
                    'wallet_charge' => 'کیف‌پول شما با موفقیت شارژ شد.',
                    default => 'پرداخت با موفقیت انجام شد.',
                };

                return redirect()->route('app.user.wallet')->with('success', $successMessage);
            }
        } else {
            // Failed payment - determine the error message
            $errorMessage = $this->determineErrorMessage($verificationResult, $transaction);
            
            Log::warning('Payment failed, attempting redirect to service preview', [
                'transaction_id' => $transaction->id,
                'user_id' => $user ? $user->id : null,
                'metadata' => $metadata,
                'error_message' => $errorMessage
            ]);
            
            // Check if this was a service-related payment
            if (isset($metadata['continue_service']) && !empty($metadata['continue_service'])) {
                return $this->redirectToServicePreviewOnFailure($metadata, $user, $errorMessage);
            }
            
            // Check for guest payment with service context
            if (isset($metadata['type']) && $metadata['type'] === 'guest_wallet_charge' && 
                isset($metadata['service_id'])) {
                return $this->redirectGuestToServiceOnFailure($metadata, $errorMessage);
            }
            
            // For non-authenticated users without service context, redirect to home
            if (!$user) {
                return redirect()->route('app.page.home')->with('error', $errorMessage);
            }
            
            // For authenticated users, redirect to wallet
            return redirect()->route('app.user.wallet')->with('error', $errorMessage);
        }
    }

    /**
     * Redirect user to service preview page on payment failure
     */
    protected function redirectToServicePreviewOnFailure(array $metadata, ?User $user, string $errorMessage)
    {
        try {
            $serviceSlug = $metadata['continue_service'];
            $sessionKey = $metadata['service_session_key'] ?? null;
            $requestHash = $metadata['service_request_hash'] ?? null;
            
            Log::info('Attempting service preview redirect', [
                'service_slug' => $serviceSlug,
                'session_key' => $sessionKey,
                'request_hash' => $requestHash,
                'user_id' => $user ? $user->id : null
            ]);
            
            // Find the service to get its model for route parameter
            $service = \App\Models\Service::where('slug', $serviceSlug)->first();
            
            if (!$service) {
                Log::error('Service not found for failed payment redirect', [
                    'service_slug' => $serviceSlug
                ]);
                
                // Fallback redirect
                return $user ? 
                    redirect()->route('app.user.wallet')->with('error', $errorMessage) :
                    redirect()->route('app.page.home')->with('error', $errorMessage);
            }
            
            // Restore the service request data in session if needed
            if ($sessionKey && $requestHash) {
                Log::info('Restoring session data for failed payment', [
                    'session_key' => $sessionKey,
                    'request_hash' => $requestHash
                ]);
                
                // Try to restore service data from ServiceRequest if available
                $serviceRequest = \App\Models\ServiceRequest::findByHash($requestHash);
                if ($serviceRequest && $serviceRequest->service_id == $service->id) {
                    Session::put($sessionKey, $serviceRequest->input_data);
                    Session::put('guest_service_request_id', $serviceRequest->id);
                    Session::put('pending_service_request_id', $serviceRequest->id);
                    
                    Log::info('Service data restored from ServiceRequest', [
                        'service_request_id' => $serviceRequest->id,
                        'service_data_keys' => array_keys($serviceRequest->input_data)
                    ]);
                }
            }
            
            // Determine which preview route to use based on user authentication
            $routeName = $user ? 'services.preview.user' : 'services.preview.guest';
            
            $routeParams = ['service' => $service];
            if ($requestHash) {
                $routeParams['hash'] = $requestHash;
            }
            
            Log::info('Redirecting to service preview', [
                'route_name' => $routeName,
                'route_params' => $routeParams,
                'service_id' => $service->id,
                'service_slug' => $service->slug
            ]);
            
            return redirect()->route($routeName, $routeParams)
                ->with('error', $errorMessage)
                ->with('payment_failed', true)
                ->with('show_payment_error', true);
                
        } catch (\Exception $e) {
            Log::error('Error redirecting to service preview on payment failure', [
                'error' => $e->getMessage(),
                'metadata' => $metadata,
                'user_id' => $user ? $user->id : null
            ]);
            
            // Fallback redirect
            return $user ? 
                redirect()->route('app.user.wallet')->with('error', $errorMessage) :
                redirect()->route('app.page.home')->with('error', $errorMessage);
        }
    }

    /**
     * Redirect guest to service page on payment failure
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
                    
                    return redirect()->route('services.preview.guest', [
                        'service' => $service,
                        'hash' => $requestHash
                    ])->with('error', $errorMessage)
                      ->with('payment_failed', true);
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
     * Determine user-friendly error message based on verification result
     */
    protected function determineErrorMessage(array $verificationResult, GatewayTransaction $transaction): string
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
            return 'پرداخت توسط شما لغو شد. در صورت تمایل می‌توانید مجدداً اقدام نمایید.';
        }
        
        // Handle specific error codes
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
        
        // Default error message
        return $verificationResult['message'] ?? 'پرداخت ناموفق بود. لطفاً مجدداً تلاش کنید.';
    }

    /**
     * Redirect to failure page with error message
     */
    protected function redirectToFailurePage(string $errorMessage)
    {
        return redirect()->route('app.user.wallet')->with('error', 'خطا در پردازش پرداخت: ' . $errorMessage);
    }

    /**
     * Check payment status
     */
    public function checkStatus(Request $request, string $transactionId)
    {
        try {
            $transaction = GatewayTransaction::where('uuid', $transactionId)->firstOrFail();
            
            // Check if user has access to this transaction
            if (Auth::id() !== $transaction->user_id) {
                abort(403, 'Access denied');
            }

            $gatewayInstance = $this->gatewayManager->gatewayById($transaction->gateway_id);
            $statusResult = $gatewayInstance->getPaymentStatus($transaction);

            return response()->json([
                'success' => true,
                'status' => $transaction->status,
                'gateway_status' => $statusResult,
                'transaction' => [
                    'id' => $transaction->uuid,
                    'amount' => $transaction->amount,
                    'currency' => $transaction->currency->code,
                    'status' => $transaction->status,
                    'created_at' => $transaction->created_at,
                    'updated_at' => $transaction->updated_at,
                ],
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Get available payment gateways for user
     */
    public function getAvailableGateways(Request $request)
    {
        $amount = $request->query('amount', 0);
        $currency = $request->query('currency', 'IRT');

        $gateways = $this->gatewayManager->gatewaysForAmount($amount, $currency);

        return response()->json([
            'success' => true,
            'gateways' => $gateways->map(function ($gateway) use ($amount) {
                return [
                    'id' => $gateway->id,
                    'name' => $gateway->name,
                    'slug' => $gateway->slug,
                    'logo_url' => $gateway->logo_url,
                    'fee' => $gateway->calculateFee($amount),
                    'total_amount' => $amount, // No fees added
                    'is_default' => $gateway->is_default,
                ];
            }),
        ]);
    }

    /**
     * Refund a transaction
     */
    public function processRefund(Request $request, string $transactionId)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        try {
            $transaction = GatewayTransaction::where('uuid', $transactionId)->first();
            
            if (!$transaction) {
                return response()->json(['success' => false, 'message' => 'تراکنش یافت نشد'], 404);
            }

            // Check if user owns this transaction
            if ($transaction->user_id !== Auth::id()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            // Check if transaction can be refunded
            if (!$transaction->canBeRefunded()) {
                return response()->json(['success' => false, 'message' => 'Transaction cannot be refunded'], 400);
            }

            // Get gateway instance
            $gateway = $this->gatewayManager->gatewayById($transaction->payment_gateway_id);
            
            // Process refund
            $refundResult = $gateway->refund($transaction, $request->reason);

            if ($refundResult['success']) {
                $transaction->update([
                    'status' => GatewayTransaction::STATUS_REFUNDED,
                    'gateway_response' => array_merge($transaction->gateway_response ?? [], [
                        'refund_data' => $refundResult['data'],
                        'refund_date' => now(),
                        'refund_reason' => $request->reason,
                    ]),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Refund processed successfully',
                    'transaction' => $transaction->fresh(),
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $refundResult['message'] ?? 'Refund failed',
            ], 400);

        } catch (Exception $e) {
            Log::error('Refund failed', [
                'error' => $e->getMessage(),
                'transaction_id' => $transactionId,
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'خطا در بازگشت وجه: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show payment form
     */
    public function showPaymentForm(Request $request)
    {
        $gateways = PaymentGateway::where('is_active', true)->get();
        
        return view('payments.form', [
            'gateways' => $gateways,
            'amount' => $request->input('amount'),
            'description' => $request->input('description'),
        ]);
    }

    /**
     * Create payment (alternative to initializePayment for form-based requests)
     */
    public function createPayment(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'currency' => 'nullable|string|size:3|in:IRT,USD,EUR',
            'description' => 'nullable|string|max:255',
            'gateway_id' => 'required|exists:payment_gateways,id',
        ]);

        try {
            $paymentData = [
                'gateway_id' => $request->gateway_id,
                'amount' => $request->amount,
                'currency' => $request->currency ?? 'IRT',
                'description' => $request->description,
                'callback_url' => route('payment.callback', ['gateway' => PaymentGateway::find($request->gateway_id)->slug]),
                'metadata' => [],
            ];

            return $this->initializePayment(new Request($paymentData));

        } catch (Exception $e) {
            return back()
                ->withErrors(['payment' => 'خطا در ایجاد پرداخت: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Show payment success page
     */
    public function showSuccess(string $transactionId)
    {
        $transaction = GatewayTransaction::where('uuid', $transactionId)->first();

        if (!$transaction || $transaction->user_id !== Auth::id()) {
            abort(404, 'تراکنش یافت نشد');
        }

        return view('payments.success', compact('transaction'));
    }

    /**
     * Show payment failed page
     */
    public function showFailed(string $transactionId)
    {
        $transaction = GatewayTransaction::where('uuid', $transactionId)->first();

        if (!$transaction || $transaction->user_id !== Auth::id()) {
            abort(404, 'تراکنش یافت نشد');
        }

        return view('payments.failed', compact('transaction'));
    }

    /**
     * Show payment status/details page
     */
    public function showStatus(string $transactionId)
    {
        $transaction = GatewayTransaction::where('uuid', $transactionId)
            ->with(['paymentGateway', 'currency'])
            ->first();

        if (!$transaction || $transaction->user_id !== Auth::id()) {
            abort(404, 'تراکنش یافت نشد');
        }

        return view('payments.status', compact('transaction'));
    }



    /**
     * Download transaction receipt
     */
    public function downloadReceipt(string $transactionId)
    {
        $transaction = GatewayTransaction::where('uuid', $transactionId)->first();

        if (!$transaction || $transaction->user_id !== Auth::id()) {
            abort(404, 'تراکنش یافت نشد');
        }

        if ($transaction->status !== GatewayTransaction::STATUS_COMPLETED) {
            return back()->with('error', 'فقط می‌توانید رسید تراکنش‌های موفق را دانلود کنید.');
        }

        return view('payments.receipt', compact('transaction'));
    }
} 