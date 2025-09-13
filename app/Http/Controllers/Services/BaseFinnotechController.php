<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceResult;
use App\Services\Finnotech\FinnotechService;
use App\Services\Finnotech\SmsAuthorizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\Http; // Added for HTTP client

abstract class BaseFinnotechController extends Controller implements BaseServiceController
{
    protected FinnotechService $finnotechService;
    protected SmsAuthorizationService $smsAuthService;
    protected string $apiEndpoint;
    protected string $scope;
    protected array $requiredFields;
    protected array $validationRules;
    protected array $validationMessages;
    protected bool $requiresSms;
    protected string $httpMethod = 'GET';

    public function __construct(FinnotechService $finnotechService, SmsAuthorizationService $smsAuthService)
    {
        $this->finnotechService = $finnotechService;
        $this->smsAuthService = $smsAuthService;
        // configureService() is called in child constructors
    }

    /**
     * Log comprehensive debugging information for loan-inquiry troubleshooting
     */
    protected function logLoanInquiryDebugInfo($context, $serviceData = null, $request = null, $authData = null)
    {
        if ($this->apiEndpoint !== 'loan-inquiry') {
            return;
        }

        $debugInfo = [
            'context' => $context,
            'api_endpoint' => $this->apiEndpoint,
            'required_fields' => $this->requiredFields,
            'validation_rules' => $this->validationRules,
            'timestamp' => now()->toDateTimeString(),
        ];

        if ($request) {
            $debugInfo['request_data'] = [
                'all' => $request->all(),
                'only_required' => $request->only($this->requiredFields ?? []),
                'has_mobile' => $request->has('mobile'),
                'has_national_code' => $request->has('national_code'),
                'mobile_value' => $request->input('mobile'),
                'national_code_value' => $request->input('national_code'),
            ];
        }

        if ($serviceData) {
            $debugInfo['service_data'] = [
                'full_data' => $serviceData,
                'keys' => array_keys($serviceData),
                'has_mobile' => isset($serviceData['mobile']),
                'has_national_code' => isset($serviceData['national_code']),
                'mobile_value' => $serviceData['mobile'] ?? 'MISSING',
                'national_code_value' => $serviceData['national_code'] ?? 'MISSING',
            ];
        }

        if ($authData) {
            $debugInfo['auth_data'] = [
                'full_data' => $authData,
                'service_data' => $authData['service_data'] ?? 'MISSING',
                'mobile' => $authData['mobile'] ?? 'MISSING',
                'national_code' => $authData['national_code'] ?? 'MISSING',
            ];
        }

        Log::info("Loan Inquiry Debug - {$context}", $debugInfo);
    }

    /**
     * Configure service-specific settings
     * Must be implemented by each service controller
     */
    abstract protected function configureService(): void;

    /**
     * Handle the service submission
     */
    public function handle(Request $request, Service $service)
    {
        try {
            // Enhanced debugging for loan-inquiry at the very start
            if ($this->apiEndpoint === 'loan-inquiry') {
                Log::info('🔥🔥🔥 LOAN INQUIRY HANDLE METHOD STARTED 🔥🔥🔥', [
                    'request_all' => $request->all(),
                    'request_method' => $request->method(),
                    'service_id' => $service->id,
                    'service_slug' => $service->slug,
                    'api_endpoint' => $this->apiEndpoint,
                    'required_fields' => $this->requiredFields,
                    'requiresSms' => $this->requiresSms,
                    'user_authenticated' => Auth::check(),
                    'user_id' => Auth::id(),
                    'method' => 'handle-start'
                ]);
            }

            // Validate input
            $validator = Validator::make($request->all(), $this->validationRules, $this->validationMessages);
            
            if ($validator->fails()) {
                // Enhanced debugging for loan-inquiry validation failure
                if ($this->apiEndpoint === 'loan-inquiry') {
                    Log::error('Loan inquiry validation failed', [
                        'request_all' => $request->all(),
                        'validation_rules' => $this->validationRules,
                        'validation_errors' => $validator->errors()->toArray(),
                        'method' => 'handle-validation-failed'
                    ]);
                }
                return back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $serviceData = $request->only($this->requiredFields);
            
            // Enhanced debugging for loan-inquiry after getting service data
            if ($this->apiEndpoint === 'loan-inquiry') {
                Log::info('Loan inquiry service data prepared from request', [
                    'required_fields' => $this->requiredFields,
                    'service_data' => $serviceData,
                    'service_data_keys' => array_keys($serviceData),
                    'has_mobile' => isset($serviceData['mobile']),
                    'has_national_code' => isset($serviceData['national_code']),
                    'mobile_value' => $serviceData['mobile'] ?? 'MISSING',
                    'national_code_value' => $serviceData['national_code'] ?? 'MISSING',
                    'method' => 'handle-service-data-prepared'
                ]);
            }

            // Check if SMS verification is required
            if ($this->requiresSms) {
                // If user is not authenticated, redirect to preview page
                if (!Auth::check()) {
                    // Store service request for processing after payment (no transaction needed)
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

                $user = Auth::user();
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
                    
                    Log::info('User redirected to preview page due to insufficient balance in Finnotech service', [
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

                // Handle SMS verification flow
                return $this->handleSmsVerification($request, $service, $serviceData);
            }

            // Process the service directly
            $result = $this->process($serviceData, $service);

            if (!$result['success']) {
                return back()
                    ->withErrors(['service_error' => $result['message']])
                    ->withInput();
            }

            // Store result
            $serviceResult = ServiceResult::create([
                'service_id' => $service->id,
                'user_id' => Auth::id(),
                'input_data' => $serviceData,
                'output_data' => $result['data'],
                'status' => 'success',
                'processed_at' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return redirect()->route('services.result', ['id' => $serviceResult->result_hash])
                ->with('success', 'عملیات با موفقیت انجام شد.');

        } catch (Exception $e) {
            Log::error('Service processing error', [
                'service_id' => $service->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withErrors(['service_error' => 'خطای سیستمی رخ داده است. لطفاً مجدداً تلاش کنید.'])
                ->withInput();
        }
    }

    /**
     * Process the service and return result data
     */
    public function process(array $serviceData, Service $service): array
    {
        try {
            // Prepare API parameters
            $apiParams = $this->prepareApiParameters($serviceData);
            
            // Resolve the full endpoint from config
            $serviceKey = $this->getServiceKeyFromEndpoint();
            $fullEndpoint = config("finnotech.endpoints.{$serviceKey}");
            
            if (!$fullEndpoint) {
                Log::error('Endpoint not found in config', [
                    'service_key' => $serviceKey,
                    'api_endpoint' => $this->apiEndpoint
                ]);
                
                return [
                    'success' => false,
                    'message' => 'پیکربندی سرویس یافت نشد'
                ];
            }
            
            // Replace placeholders in endpoint
            $clientId = config('finnotech.client_id');
            $fullEndpoint = str_replace('{clientId}', $clientId, $fullEndpoint);
            
            // Replace {user} placeholder if exists and we have national_code
            if (strpos($fullEndpoint, '{user}') !== false) {
                $nationalId = $serviceData['national_code'] ?? '';
                if ($nationalId) {
                    $fullEndpoint = str_replace('{user}', $nationalId, $fullEndpoint);
                } else {
                    return [
                        'success' => false,
                        'message' => 'کد ملی برای این سرویس الزامی است'
                    ];
                }
            }
            
            // Make API call
            $response = $this->finnotechService->makeApiRequest(
                $fullEndpoint,
                $apiParams,
                $this->httpMethod,
                true
            );

            if (!$response) {
                return [
                    'success' => false,
                    'message' => 'در حال حاضر سرویس درحال به روزرسانی است لطفا دقایقی دیگر دوباره تلاش کنید'
                ];
            }

            // Process and format response data
            $responseArray = json_decode(json_encode($response), true);
            $formattedData = $this->formatResponseData($responseArray);

            return [
                'success' => true,
                'data' => $formattedData,
                'raw_response' => $responseArray
            ];

        } catch (Exception $e) {
            Log::error('API call failed', [
                'endpoint' => $this->apiEndpoint,
                'scope' => $this->scope,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Show the service result
     */
    public function show(string $resultId, Service $service)
    {
        $result = ServiceResult::where('result_hash', $resultId)
            ->where('service_id', $service->id)
            ->where('status', 'success')
            ->firstOrFail();

        // Check authorization
        if (!Auth::check() || $result->user_id !== Auth::id()) {
            abort(401, 'شما مجاز به مشاهده این نتیجه نیستید.');
        }

        // Check if result is expired
        if ($result->isExpired()) {
            abort(410, 'این نتیجه منقضی شده است.');
        }

        $formattedResult = $this->formatResultForDisplay($result->output_data);

        return view('front.services.result', [
            'service' => $service,
            'result' => $formattedResult,
            'inputData' => $result->input_data,
            'resultId' => $resultId,
            'resultType' => $this->getResultType(),
        ]);
    }

    /**
     * Handle SMS verification flow using new SMS Authorization Service
     */
    protected function handleSmsVerification(Request $request, Service $service, array $serviceData)
    {
        $user = Auth::user();

        // Debug logging for loan-inquiry
        if ($this->apiEndpoint === 'loan-inquiry') {
            Log::info('Loan inquiry SMS verification started', [
                'service_data' => $serviceData,
                'user_id' => $user->id,
                'required_fields' => $this->requiredFields,
                'has_national_code' => isset($serviceData['national_code']),
                'national_code_value' => $serviceData['national_code'] ?? 'NOT SET',
                'request_all_data' => $request->all(),
                'request_input_national_code' => $request->input('national_code'),
                'request_input_mobile' => $request->input('mobile'),
                'service_data_keys' => array_keys($serviceData),
                'method' => 'handleSmsVerification'
            ]);
        }

        // Extract national ID and mobile from service data
        $nationalId = $serviceData['national_code'] ?? '';
        $mobile = $serviceData['mobile'] ?? $user->mobile ?? '';

        if (empty($nationalId) || empty($mobile)) {
            return back()
                ->withErrors(['service_error' => 'کد ملی و شماره موبایل برای این سرویس الزامی است'])
                ->withInput();
        }

        // Try to process the service directly (this will check for existing tokens)
        try {
            $result = $this->processServiceWithToken($serviceData, $service, '');

            if ($result['success']) {
                // Token exists and service processed successfully
                
                // Deduct from wallet
                $user->withdraw($service->price, [
                    'description' => "پرداخت سرویس: {$service->title}",
                    'service_id' => $service->id,
                    'type' => 'service_payment'
                ]);

                // Store result
                $serviceResult = ServiceResult::create([
                    'service_id' => $service->id,
                    'user_id' => $user->id,
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

            // Check if authorization is required
            if (isset($result['authorization_required']) && $result['authorization_required']) {
                // Redirect to authorization URL
                return redirect($result['authorization_url'])
                    ->with('info', 'لطفاً برای ادامه، احراز هویت پیامکی را تکمیل کنید.');
            }

            // Other error
            return back()
                ->withErrors(['service_error' => $result['message']])
                ->withInput();

        } catch (\App\Exceptions\FinnotechException $e) {
            // Handle FinnotechException - likely need authorization
            if (strpos($e->getMessage(), 'No valid SMS auth token') !== false) {
                try {
                    $authUrl = $this->smsAuthService->generateAuthorizationUrl(
                        $this->scope,
                        $mobile,
                        $nationalId
                    );
                    
                    return redirect($authUrl)
                        ->with('info', 'لطفاً برای ادامه، احراز هویت پیامکی را تکمیل کنید.');
                } catch (\Exception $urlException) {
                    Log::error('Failed to generate authorization URL', [
                        'scope' => $this->scope,
                        'mobile' => $mobile,
                        'national_id' => $nationalId,
                        'error' => $urlException->getMessage()
                    ]);
                    
                    return back()
                        ->withErrors(['service_error' => 'خطا در راه‌اندازی احراز هویت پیامکی'])
                        ->withInput();
                }
            }

            return back()
                ->withErrors(['service_error' => $e->getMessage()])
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Unexpected error in SMS verification', [
                'service_id' => $service->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withErrors(['service_error' => 'خطا در پردازش سرویس'])
                ->withInput();
        }
    }

    /**
     * Initiate SMS authentication with Finnotech
     */
    protected function initiateSmsAuthentication(Request $request, Service $service, array $serviceData, $user)
    {
        try {
            // Get authentication type from config
            $authTypes = config('finnotech.auth_types');
            $serviceKey = $this->getServiceKeyFromEndpoint();
            
            // Determine if this is SMS authorization or authorization code
            $isSmsAuth = in_array($serviceKey, $authTypes['sms_authorization'] ?? []);
            $isAuthCode = in_array($serviceKey, $authTypes['authorization_code'] ?? []);

            if ($isSmsAuth) {
                // For SMS authorization services, send authorization request
                $mobile = $serviceData['mobile'] ?? $user->mobile;
                $nationalId = $serviceData['national_code'] ?? null;

                // Enhanced debugging for loan-inquiry
                if ($this->apiEndpoint === 'loan-inquiry') {
                    Log::info('initiateSmsAuthentication - SMS Auth validation', [
                        'service_data' => $serviceData,
                        'user_mobile' => $user->mobile,
                        'extracted_mobile' => $mobile,
                        'extracted_nationalId' => $nationalId,
                        'mobile_check' => !empty($mobile),
                        'nationalId_check' => !empty($nationalId),
                        'both_present' => (!empty($mobile) && !empty($nationalId)),
                        'service_data_has_national_code' => isset($serviceData['national_code']),
                        'method' => 'initiateSmsAuthentication'
                    ]);
                }

                if (!$mobile || !$nationalId) {
                    // Log the specific reason for failure
                    if ($this->apiEndpoint === 'loan-inquiry') {
                        Log::error('Loan inquiry validation failed in initiateSmsAuthentication', [
                            'mobile_missing' => empty($mobile),
                            'nationalId_missing' => empty($nationalId),
                            'mobile_value' => $mobile,
                            'nationalId_value' => $nationalId,
                            'service_data' => $serviceData,
                            'user_mobile' => $user->mobile
                        ]);
                    }
                    
                    return back()
                        ->withErrors(['service_error' => 'شماره موبایل و کد ملی برای این سرویس الزامی است'])
                        ->withInput();
                }

                $authResult = $this->sendFinnotechSmsAuth($mobile, $nationalId, $this->scope);
                
                if ($authResult['success']) {
                    // Store session data for verification step
                    session([
                        'finnotech_auth' => [
                            'track_id' => $authResult['track_id'],
                            'mobile' => $mobile,
                            'national_code' => $nationalId,
                            'service_id' => $service->id,
                            'service_data' => $serviceData,
                            'scope' => $this->scope,
                            'initiated_at' => now()
                        ]
                    ]);

                    return back()->with([
                        'sms_sent' => true,
                        'track_id' => $authResult['track_id'],
                        'message' => 'کد تایید به شماره موبایل شما ارسال شد'
                    ])->withInput();
                } else {
                    return back()
                        ->withErrors(['service_error' => $authResult['message'] ?? 'خطا در ارسال کد تایید'])
                        ->withInput();
                }
            } elseif ($isAuthCode) {
                // For authorization code services, redirect to Finnotech authorization
                $authUrl = $this->buildFinnotechAuthUrl($this->scope, $serviceData);
                return redirect($authUrl);
            } else {
                // Fallback: treat as regular service (shouldn't happen with correct config)
                return $this->process($serviceData, $service);
            }

        } catch (Exception $e) {
            Log::error('SMS authentication initiation failed', [
                'service_id' => $service->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withErrors(['service_error' => 'خطا در آغاز فرآیند احراز هویت. لطفاً مجدداً تلاش کنید.'])
                ->withInput();
        }
    }

    /**
     * Verify SMS and process the service
     */
    protected function verifySmsAndProcessService(Request $request, Service $service, array $serviceData, $user)
    {
        try {
            $authData = session('finnotech_auth');
            
            if (!$authData || $authData['service_id'] != $service->id) {
                return back()
                    ->withErrors(['service_error' => 'جلسه احراز هویت منقضی شده است. لطفاً مجدداً تلاش کنید.'])
                    ->withInput();
            }

            // Check if session is expired (10 minutes)
            if (now()->diffInMinutes($authData['initiated_at']) > 10) {
                session()->forget('finnotech_auth');
                return back()
                    ->withErrors(['service_error' => 'کد تایید منقضی شده است. لطفاً مجدداً تلاش کنید.'])
                    ->withInput();
            }

            $otpCode = $request->input('otp_code');
            
            // Verify SMS with Finnotech
            $verifyResult = $this->verifyFinnotechSms(
                $authData['track_id'],
                $authData['mobile'],
                $authData['national_code'],
                $otpCode
            );

            if (!$verifyResult['success']) {
                return back()
                    ->withErrors(['service_error' => $verifyResult['message'] ?? 'کد تایید نامعتبر است'])
                    ->withInput();
            }

            // Get access token
            $tokenResult = $this->getFinnotechSmsToken($verifyResult['code']);
            
            if (!$tokenResult['success']) {
                return back()
                    ->withErrors(['service_error' => 'خطا در دریافت مجوز دسترسی'])
                    ->withInput();
            }

            // Enhanced debugging for loan-inquiry
            if ($this->apiEndpoint === 'loan-inquiry') {
                Log::info('verifySmsAndProcessService - BEFORE calling processServiceWithToken', [
                    'auth_data_service_data' => $authData['service_data'],
                    'auth_data_mobile' => $authData['mobile'] ?? 'MISSING',
                    'auth_data_national_code' => $authData['national_code'] ?? 'MISSING',
                    'service_data_keys' => array_keys($authData['service_data']),
                    'has_national_code_in_service_data' => isset($authData['service_data']['national_code']),
                    'token_obtained' => $tokenResult['success'],
                    'otp_code' => $request->input('otp_code'),
                    'track_id' => $request->input('track_id'),
                    'method' => 'verifySmsAndProcessService-before-processServiceWithToken'
                ]);
            }

            // Now process the service with the SMS token
            $result = $this->processServiceWithToken($authData['service_data'], $service, $tokenResult['access_token']);

            if (!$result['success']) {
                // Check if authorization is required (new SMS auth system)
                if (isset($result['authorization_required']) && $result['authorization_required']) {
                    // Clear old session data
                    session()->forget('finnotech_auth');
                    
                    // Redirect to authorization URL
                    return redirect($result['authorization_url'])
                        ->with('info', 'لطفاً برای ادامه، احراز هویت پیامکی را تکمیل کنید.');
                }
                
                return back()
                    ->withErrors(['service_error' => $result['message']])
                    ->withInput();
            }

            // Deduct from wallet
            $user->withdraw($service->price, [
                'description' => "پرداخت سرویس: {$service->title}",
                'service_id' => $service->id,
                'type' => 'service_payment'
            ]);

            // Store result
            $serviceResult = ServiceResult::create([
                'service_id' => $service->id,
                'user_id' => $user->id,
                'input_data' => $authData['service_data'],
                'output_data' => $result['data'],
                'status' => 'success',
                'processed_at' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Clear session data
            session()->forget('finnotech_auth');

            return redirect()->route('services.result', ['id' => $serviceResult->result_hash])
                ->with('success', 'عملیات با موفقیت انجام شد.');

        } catch (Exception $e) {
            Log::error('SMS verification and service processing failed', [
                'service_id' => $service->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            session()->forget('finnotech_auth');
            
            return back()
                ->withErrors(['service_error' => 'خطا در تایید کد و پردازش سرویس. لطفاً مجدداً تلاش کنید.'])
                ->withInput();
        }
    }

    /**
     * Send SMS authentication request to Finnotech
     */
    protected function sendFinnotechSmsAuth(string $mobile, string $nationalId, string $scope): array
    {
        try {
            $clientId = config('finnotech.client_id');
            $redirectUri = url('/api/finnotech/callback');
            $state = uniqid('fnt_', true);

            $authUrl = config('finnotech.base_url') . '/dev/v2/oauth2/authorize?' . http_build_query([
                'client_id' => $clientId,
                'response_type' => 'code',
                'redirect_uri' => $redirectUri,
                'scope' => $scope,
                'mobile' => $mobile,
                'state' => $state,
                'auth_type' => 'SMS'
            ]);

            // Make GET request to trigger SMS
            $response = Http::timeout(30)->get($authUrl);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['result']['smsSent']) && $data['result']['smsSent']) {
                    return [
                        'success' => true,
                        'track_id' => $data['result']['trackId'],
                        'state' => $state
                    ];
                }
            }

            Log::error('Finnotech SMS auth failed', [
                'response_status' => $response->status(),
                'response_body' => $response->body()
            ]);

            return [
                'success' => false,
                'message' => 'خطا در ارسال کد تایید از طرف سرویس دهنده'
            ];

        } catch (Exception $e) {
            Log::error('Finnotech SMS auth exception', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'خطا در برقراری ارتباط با سرویس دهنده'
            ];
        }
    }

    /**
     * Verify SMS code with Finnotech
     */
    protected function verifyFinnotechSms(string $trackId, string $mobile, string $nationalId, string $otpCode): array
    {
        try {
            $response = Http::timeout(30)->post(config('finnotech.base_url') . '/dev/v2/oauth2/verify/sms', [
                'trackId' => $trackId,
                'mobile' => $mobile,
                'nid' => $nationalId,
                'otp' => $otpCode
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['result']['code'])) {
                    return [
                        'success' => true,
                        'code' => $data['result']['code']
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'کد تایید نامعتبر است'
            ];

        } catch (Exception $e) {
            Log::error('Finnotech SMS verification exception', [
                'track_id' => $trackId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'خطا در تایید کد'
            ];
        }
    }

    /**
     * Get access token using authorization code
     */
    protected function getFinnotechSmsToken(string $code): array
    {
        try {
            $clientId = config('finnotech.client_id');
            $clientSecret = config('finnotech.client_secret');
            $redirectUri = url('/api/finnotech/callback');

            $credentials = base64_encode("{$clientId}:{$clientSecret}");

            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => "Basic {$credentials}",
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ])
                ->asForm()
                ->post(config('finnotech.base_url') . '/dev/v2/oauth2/token', [
                    'grant_type' => 'authorization_code',
                    'code' => $code,
                    'auth_type' => 'SMS',
                    'redirect_uri' => $redirectUri
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['access_token'])) {
                    return [
                        'success' => true,
                        'access_token' => $data['access_token'],
                        'refresh_token' => $data['refresh_token'] ?? null,
                        'expires_in' => $data['expires_in'] ?? 3600
                    ];
                }
            }

            Log::error('Finnotech token exchange failed', [
                'response_status' => $response->status(),
                'response_body' => $response->body()
            ]);

            return [
                'success' => false,
                'message' => 'خطا در دریافت مجوز دسترسی'
            ];

        } catch (Exception $e) {
            Log::error('Finnotech token exchange exception', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'خطا در برقراری ارتباط برای دریافت مجوز'
            ];
        }
    }

    /**
     * Process service with SMS token
     */
    protected function processServiceWithToken(array $serviceData, Service $service, string $accessToken): array
    {
        try {
            
            // Resolve the full endpoint from config FIRST
            $serviceKey = $this->getServiceKeyFromEndpoint();
            $fullEndpoint = config("finnotech.endpoints.{$serviceKey}");
            
            if (!$fullEndpoint) {
                Log::error('Endpoint not found in config for SMS service', [
                    'service_key' => $serviceKey,
                    'api_endpoint' => $this->apiEndpoint
                ]);
                
                return [
                    'success' => false,
                    'message' => 'پیکربندی سرویس یافت نشد'
                ];
            }
            
            // Replace placeholders in endpoint BEFORE preparing API parameters
            $clientId = config('finnotech.client_id');
            $fullEndpoint = str_replace('{clientId}', $clientId, $fullEndpoint);
            
            // Replace {user} placeholder if exists using ORIGINAL service data
            if (strpos($fullEndpoint, '{user}') !== false) {
                // Look for national_code in original service data (before prepareApiParameters transformation)
                $nationalId = $serviceData['national_code'] ?? '';
  
                if (empty($nationalId)) {
                    // Enhanced error logging for loan-inquiry
                    if ($this->apiEndpoint === 'loan-inquiry') {
                        Log::error('Loan inquiry - National Code missing for {user} replacement', [
                            'service_data' => $serviceData,
                            'endpoint' => $fullEndpoint,
                            'service_key' => $serviceKey,
                            'api_endpoint' => $this->apiEndpoint,
                            'checked_key' => 'national_code',
                            'national_code_value' => $serviceData['national_code'] ?? 'KEY_NOT_EXISTS',
                            'all_keys_in_service_data' => array_keys($serviceData),
                            'method' => 'processServiceWithToken'
                        ]);
                    } else {
                        Log::error('National ID missing for SMS service', [
                            'service_data' => $serviceData,
                            'endpoint' => $fullEndpoint,
                            'service_key' => $serviceKey,
                            'api_endpoint' => $this->apiEndpoint
                        ]);
                    }
                    
                    return [
                        'success' => false,
                        'message' => 'کد ملی برای این سرویس الزامی است'
                    ];
                }
                $fullEndpoint = str_replace('{user}', $nationalId, $fullEndpoint);
            }

            // NOW prepare API parameters (this may transform field names)
            $apiParams = $this->prepareApiParameters($serviceData);

            // Use SMS Authorization Service for direct API call
            $nationalId = $serviceData['national_code'] ?? '';
            $mobile = $serviceData['mobile'] ?? '';
            
            if (empty($nationalId) || empty($mobile)) {
                Log::error('Missing required data for SMS authorization', [
                    'national_id' => $nationalId,
                    'mobile' => $mobile,
                    'service_data' => $serviceData
                ]);
                return [
                    'success' => false,
                    'message' => 'کد ملی و شماره موبایل برای این سرویس الزامی است'
                ];
            }

            // Extract query parameters from the API params
            $queryParams = [];
            $postParams = [];
            
            if ($this->httpMethod === 'GET') {
                $queryParams = $apiParams;
            } else {
                $postParams = $apiParams;
            }

            // Make the authorized API call using SMS Authorization Service
            try {
                $response = $this->smsAuthService->makeAuthorizedApiCall(
                    $this->scope,
                    $nationalId,
                    $mobile,
                    $postParams,
                    $queryParams
                );
                // Process and format response data
                $formattedData = $this->formatResponseData($response);

                return [
                    'success' => true,
                    'data' => $formattedData,
                    'raw_response' => $response
                ];
            } catch (\App\Exceptions\FinnotechException $e) {
                // If the error is about missing token, redirect to authorization
                if (strpos($e->getMessage(), 'No valid SMS auth token') !== false) {
                    Log::info('SMS auth token missing, redirecting to authorization', [
                        'scope' => $this->scope,
                        'national_id' => $nationalId,
                        'mobile' => $mobile
                    ]);
                    
                    // Generate authorization URL
                    $serviceDataWithSlug = array_merge($serviceData, [
                        'service_slug' => $service->slug
                    ]);
                    
                    $authUrl = $this->smsAuthService->generateAuthorizationUrl(
                        $this->scope,
                        $mobile,
                        $nationalId,
                        $serviceDataWithSlug,
                        $service->id
                    );
                    
                    return [
                        'success' => false,
                        'authorization_required' => true,
                        'authorization_url' => $authUrl,
                        'message' => 'نیاز به احراز هویت پیامکی دارید'
                    ];
                }
                
                Log::error('SMS authorized API call failed', [
                    'scope' => $this->scope,
                    'national_id' => $nationalId,
                    'mobile' => $mobile,
                    'error' => $e->getMessage()
                ]);
                
                return [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }

        } catch (Exception $e) {
            Log::error('Service processing with token failed', [
                'endpoint' => $this->apiEndpoint,
                'service_data' => $serviceData,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'خطا در پردازش درخواست سرویس'
            ];
        }
    }

    /**
     * Get service key from endpoint for config lookup
     */
    protected function getServiceKeyFromEndpoint(): string
    {
        // Convert endpoint to service key (e.g., 'loan-inquiry' -> 'loan_inquiry')
        return str_replace('-', '_', $this->apiEndpoint);
    }

    /**
     * Build Finnotech authorization URL for authorization code flow
     */
    protected function buildFinnotechAuthUrl(string $scope, array $serviceData): string
    {
        $clientId = config('finnotech.client_id');
        $redirectUri = url('/api/finnotech/callback');
        $state = uniqid('fnt_', true);

        session(['finnotech_auth_state' => $state]);

        return config('finnotech.base_url') . '/dev/v2/oauth2/authorize?' . http_build_query([
            'client_id' => $clientId,
            'response_type' => 'code',
            'redirect_uri' => $redirectUri,
            'scope' => $scope,
            'state' => $state
        ]);
    }

    /**
     * Prepare API parameters from service data
     * Can be overridden by specific services
     */
    protected function prepareApiParameters(array $serviceData): array
    {
        return $serviceData;
    }

    /**
     * Format API response data
     * Must be implemented by each service controller
     */
    abstract protected function formatResponseData(array $responseData): array;

    /**
     * Format result data for display
     * Can be overridden by specific services
     */
    protected function formatResultForDisplay(array $resultData): array
    {
        return $resultData;
    }

    /**
     * Get result type for display
     * Can be overridden by specific services
     */
    protected function getResultType(): string
    {
        return 'finnotech';
    }

    /**
     * Show progress page (default implementation for interface compatibility)
     */
    public function showProgress(Request $request, Service $service, string $hash)
    {
        return redirect()->route('services.show', $service->slug)
            ->withErrors(['error' => 'این سرویس از صفحه پیشرفت پشتیبانی نمی‌کند.']);
    }

    /**
     * Handle OTP submission (default implementation for interface compatibility)
     */
    public function handleOtpSubmission(Request $request, Service $service)
    {
        return redirect()->route('services.show', $service->slug)
            ->withErrors(['error' => 'این سرویس از تایید OTP پشتیبانی نمی‌کند.']);
    }

    /**
     * Show SMS verification page (default implementation for interface compatibility)
     */
    public function showSmsVerification(Request $request, Service $service, string $hash)
    {
        return redirect()->route('services.show', $service->slug)
            ->withErrors(['error' => 'این سرویس از احراز هویت پیامکی پشتیبانی نمی‌کند.']);
    }

    /**
     * Handle SMS verification submission (default implementation for interface compatibility)
     */
    public function handleSmsOtpVerification(Request $request, Service $service, string $hash)
    {
        return redirect()->route('services.show', $service->slug)
            ->withErrors(['error' => 'این سرویس از تایید پیامک پشتیبانی نمی‌کند.']);
    }

    /**
     * Show SMS result page (default implementation for interface compatibility)
     */
    public function showSmsResult(Request $request, Service $service, string $id)
    {
        // Fallback to regular result page
        return redirect()->route('services.result', ['id' => $id]);
    }

    /**
     * Add track ID to API parameters
     */
    protected function addTrackId(array $params): array
    {
        if (!isset($params['trackId'])) {
            $params['trackId'] = 'pishkhanak_' . uniqid() . '_' . time();
        }
        
        return $params;
    }

    /**
     * Clean national ID (remove dashes and spaces)
     */
    protected function cleanNationalId(string $nationalId): string
    {
        return preg_replace('/[^0-9]/', '', $nationalId);
    }

    /**
     * Clean mobile number (format to 11 digits)
     */
    protected function cleanMobile(string $mobile): string
    {
        $mobile = preg_replace('/[^0-9]/', '', $mobile);
        
        // Convert to 11-digit format starting with 09
        if (strlen($mobile) === 10 && substr($mobile, 0, 1) === '9') {
            $mobile = '0' . $mobile;
        }
        
        return $mobile;
    }

    /**
     * Encode plate number to 9-digit format
     */
    protected function encodePlateNumber(array $plateData): string
    {
        // Implementation for plate number encoding
        // Based on Finnotech documentation table
        $region = str_pad($plateData['region'] ?? '00', 2, '0', STR_PAD_LEFT);
        $numbers = str_pad($plateData['numbers'] ?? '000', 3, '0', STR_PAD_LEFT);
        $letter = $this->getLetterCode($plateData['letter'] ?? 'الف');
        $series = str_pad($plateData['series'] ?? '00', 2, '0', STR_PAD_LEFT);
        
        return $region . $letter . $numbers . $series;
    }

    /**
     * Get letter code for plate encoding
     */
    private function getLetterCode(string $letter): string
    {
        $letterMap = [
            'الف' => '01', 'ب' => '02', 'پ' => '03', 'ت' => '04', 'ث' => '05',
            'ج' => '06', 'چ' => '07', 'ح' => '08', 'خ' => '09', 'د' => '10',
            'ذ' => '11', 'ر' => '12', 'ز' => '13', 'ژ' => '14', 'س' => '15',
            'ش' => '16', 'ص' => '17', 'ض' => '18', 'ط' => '19', 'ظ' => '20',
            'ع' => '21', 'غ' => '22', 'ف' => '23', 'ق' => '24', 'ک' => '25',
            'گ' => '26', 'ل' => '27', 'م' => '28', 'ن' => '29', 'و' => '30',
            'ه' => '31', 'ی' => '32', 'معلولین' => '33', 'تشریفات' => '34'
        ];

        return $letterMap[$letter] ?? '01';
    }

    /**
     * Get service result by hash
     *
     * @param string $resultHash
     * @param Service $service
     * @return \App\Models\ServiceResult|null
     */
    protected function getServiceResult(string $resultHash, Service $service): ?\App\Models\ServiceResult
    {
        return \App\Models\ServiceResult::where('result_hash', $resultHash)
            ->where('service_id', $service->id)
            ->first();
    }
} 