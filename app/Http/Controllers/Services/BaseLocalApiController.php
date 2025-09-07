<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Exception;

abstract class BaseLocalApiController extends Controller implements BaseServiceController
{
    protected string $serviceSlug;
    protected array $requiredFields;
    protected array $validationRules;
    protected array $validationMessages;
    protected bool $requiresOtp;
    protected string $localApiUrl = 'http://127.0.0.1:9999';
    protected int $timeout = 30;

    public function __construct()
    {
        $this->configureService();
    }

    /**
     * Configure service-specific settings
     * Must be implemented by child classes
     */
    abstract protected function configureService(): void;

    /**
     * Handle the service request
     */
    public function handle(Request $request, Service $service)
    {
        try {
            // Check if this is an OTP verification request
            if ($request->has('otp') && $request->has('hash')) {
                return $this->handleOtpVerification($request, $service);
            }

            // Validate input
            $validator = Validator::make($request->all(), $this->validationRules, $this->validationMessages);
            
            if ($validator->fails()) {
                return back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $serviceData = $request->only($this->requiredFields);
            
            // Log service request
            Log::info('Local API service request initiated', [
                'service_slug' => $this->serviceSlug,
                'service_id' => $service->id,
                'user_id' => Auth::id(),
                'ip' => $request->ip()
            ]);

            // Call local API
            $result = $this->callLocalApi($serviceData);

            return $this->handleApiResponse($result, $service, $serviceData, $request);

        } catch (Exception $e) {
            Log::error('Error in local API service', [
                'service_slug' => $this->serviceSlug,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withErrors(['error' => 'خطا در پردازش درخواست. لطفاً مجدداً تلاش کنید.'])
                ->withInput();
        }
    }

    /**
     * Process service and return result data
     */
    public function process(array $serviceData, Service $service): array
    {
        try {
            $result = $this->callLocalApi($serviceData);
            
            if ($result['status'] === 'success') {
                return [
                    'success' => true,
                    'data' => $result
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $result['message'] ?? 'خطا در پردازش درخواست'
                ];
            }
        } catch (Exception $e) {
            Log::error('Error processing local API service', [
                'service_slug' => $this->serviceSlug,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'خطا در پردازش درخواست'
            ];
        }
    }

    /**
     * Show service result
     */
    public function show(string $resultId, Service $service)
    {
        $result = ServiceResult::where('result_hash', $resultId)
            ->where('service_id', $service->id)
            ->firstOrFail();

        return view('front.services.result', compact('result', 'service'));
    }

    /**
     * Handle OTP verification for services that require it
     */
    protected function handleOtpVerification(Request $request, Service $service)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|string|size:5',
            'hash' => 'required|string',
            'mobile' => 'required|string',
            'national_code' => 'required|string'
        ], [
            'otp.required' => 'کد تایید الزامی است.',
            'otp.size' => 'کد تایید باید 5 رقم باشد.',
            'hash.required' => 'کد احراز هویت یافت نشد.',
            'mobile.required' => 'شماره موبایل الزامی است.',
            'national_code.required' => 'کد ملی الزامی است.'
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $otpData = [
            'otp' => $request->input('otp'),
            'hash' => $request->input('hash'),
            'mobile' => $request->input('mobile'),
            'national_code' => $request->input('national_code')
        ];

        try {
            $result = $this->callLocalApi($otpData);
            return $this->handleApiResponse($result, $service, $otpData, $request);
        } catch (Exception $e) {
            Log::error('Error in OTP verification', [
                'service_slug' => $this->serviceSlug,
                'error' => $e->getMessage()
            ]);

            return back()
                ->withErrors(['otp' => 'خطا در تایید کد. لطفاً مجدداً تلاش کنید.'])
                ->withInput();
        }
    }

    /**
     * Call the local API server
     */
    protected function callLocalApi(array $data): array
    {
        try {
            // Transform data to match Node.js service expectations (snake_case to camelCase)
            $transformedData = $this->transformDataForNodeJs($data);
            
            $response = Http::timeout($this->timeout)
                ->post("{$this->localApiUrl}/api/services/{$this->serviceSlug}", $transformedData);

            if (!$response->successful()) {
                throw new Exception('Local API returned error: ' . $response->status());
            }

            return $response->json();

        } catch (Exception $e) {
            Log::error('Local API call failed', [
                'service_slug' => $this->serviceSlug,
                'url' => "{$this->localApiUrl}/api/services/{$this->serviceSlug}",
                'data' => $data,
                'error' => $e->getMessage()
            ]);

            throw new Exception('ارتباط با سرویس محلی برقرار نشد.');
        }
    }

    /**
     * Transform Laravel data format to Node.js service format
     */
    protected function transformDataForNodeJs(array $data): array
    {
        $transformed = [];
        
        foreach ($data as $key => $value) {
            // Convert snake_case to camelCase
            $camelCaseKey = $this->snakeToCamelCase($key);
            $transformed[$camelCaseKey] = $value;
        }
        
        return $transformed;
    }

    /**
     * Convert snake_case string to camelCase
     */
    protected function snakeToCamelCase(string $string): string
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $string))));
    }

    /**
     * Handle API response and redirect accordingly
     */
    protected function handleApiResponse(array $result, Service $service, array $serviceData, Request $request)
    {
        switch ($result['status']) {
            case 'success':
                return $this->handleSuccessResponse($result, $service, $serviceData);

            case 'error':
                return $this->handleErrorResponse($result, $request);

            default:
                return back()
                    ->withErrors(['error' => 'پاسخ نامعتبر از سرویس'])
                    ->withInput();
        }
    }

    /**
     * Handle success response
     */
    protected function handleSuccessResponse(array $result, Service $service, array $serviceData)
    {
        // Log the result for debugging
        Log::info('Handling success response', [
            'service_slug' => $this->serviceSlug,
            'result_code' => $result['code'] ?? 'no_code',
            'result_status' => $result['status'] ?? 'no_status',
            'result_data' => $result
        ]);

        // Check if this is an SMS verification step
        if (isset($result['code']) && $result['code'] === 'SMS_SENT') {
            Log::info('SMS_SENT detected, redirecting to OTP verification', [
                'service' => $service->slug,
                'hash' => $result['hash'] ?? 'no_hash'
            ]);

            // Store session data for OTP verification
            Session::put('local_api_otp_data', [
                'service_id' => $service->id,
                'service_slug' => $this->serviceSlug,
                'hash' => $result['hash'],
                'expiry' => $result['expiry'],
                'mobile' => $serviceData['mobile'] ?? null,
                'national_code' => $serviceData['national_code'] ?? null,
            ]);

            return redirect()->route('services.progress.otp', [
                'service' => $service->slug,
                'hash' => $result['hash']
            ]);
        }

        // Check if this is a credit score SMS notification result
        if (isset($result['code']) && $result['code'] === 'CREDIT_SCORE_SMS_SENT') {
            Log::info('CREDIT_SCORE_SMS_SENT detected, redirecting to SMS result page', [
                'service' => $service->slug,
                'user_id' => Auth::id()
            ]);

            // Store the result
            $serviceResult = ServiceResult::create([
                'service_id' => $service->id,
                'user_id' => Auth::id(),
                'input_data' => $serviceData,
                'output_data' => $result,
                'status' => 'success',
                'processed_at' => now(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // Redirect to SMS notification result page
            return redirect()->route('services.progress.sms-result', [
                'service' => $service->slug,
                'id' => $serviceResult->result_hash
            ]);
        }

        // Handle completed service (fallback)
        Log::warning('Unexpected success response - falling back to general result page', [
            'service_slug' => $this->serviceSlug,
            'result_code' => $result['code'] ?? 'no_code',
            'result_status' => $result['status'] ?? 'no_status',
            'expected_codes' => ['SMS_SENT', 'CREDIT_SCORE_SMS_SENT'],
            'result_data' => $result
        ]);

        $serviceResult = ServiceResult::create([
            'service_id' => $service->id,
            'user_id' => Auth::id(),
            'input_data' => $serviceData,
            'output_data' => $result,
            'status' => 'success',
            'processed_at' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('services.result', ['id' => $serviceResult->result_hash]);
    }

    /**
     * Handle error response
     */
    protected function handleErrorResponse(array $result, Request $request)
    {
        $errorCode = $result['code'] ?? 'UNKNOWN_ERROR';
        $errorMessage = $result['message'] ?? 'خطای نامشخص';

        // Map error codes to user-friendly messages
        $errorMessages = [
            'INVALID_NATIONAL_CODE' => 'کد ملی وارد شده اشتباه است.',
            'SERVICE_UNAVAILABLE' => 'سرویس در حال حاضر در دسترس نیست. لطفاً چند دقیقه دیگر مجدداً تلاش کنید.',
            'CAPTCHA_MAX_RETRIES' => 'خطا در حل کپچا. لطفاً مجدداً تلاش کنید.',
            'INVALID_OTP' => 'کد تایید وارد شده اشتباه است.',
            'OTP_EXPIRED' => 'کد تایید منقضی شده است. لطفاً درخواست جدید ارسال کنید.',
            'OTP_SERVICE_UNAVAILABLE' => 'سرویس در حال حاضر در دسترس نیست. لطفاً چند دقیقه دیگر مجدداً تلاش کنید.',
        ];

        $userFriendlyMessage = $errorMessages[$errorCode] ?? $errorMessage;

        // For OTP-specific errors, redirect back to OTP page
        if (in_array($errorCode, ['INVALID_OTP', 'OTP_EXPIRED', 'OTP_SERVICE_UNAVAILABLE'])) {
            return back()
                ->withErrors(['otp' => $userFriendlyMessage])
                ->withInput();
        }

        return back()
            ->withErrors(['error' => $userFriendlyMessage])
            ->withInput();
    }

    /**
     * Show OTP verification page
     */
    public function showOtpVerification(Request $request, Service $service, string $hash)
    {
        $otpData = Session::get('local_api_otp_data');

        if (!$otpData || $otpData['hash'] !== $hash) {
            return redirect()->route('services.show', $service->slug)
                ->withErrors(['error' => 'جلسه منقضی شده است. لطفاً مجدداً تلاش کنید.']);
        }

        return view('front.services.local-api.otp-verification', [
            'service' => $service,
            'hash' => $hash,
            'mobile' => $otpData['mobile'],
            'national_code' => $otpData['national_code'],
            'expiry' => $otpData['expiry']
        ]);
    }

    /**
     * Show SMS notification result page
     */
    public function showSmsResult(Request $request, Service $service, string $resultId)
    {
        $result = ServiceResult::where('result_hash', $resultId)
            ->where('service_id', $service->id)
            ->firstOrFail();

        return view('front.services.local-api.sms-result', [
            'service' => $service,
            'result' => $result,
            'resultData' => $result->output_data
        ]);
    }
} 