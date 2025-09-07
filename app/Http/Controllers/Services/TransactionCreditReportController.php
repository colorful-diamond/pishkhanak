<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Services\BaseFinnotechController;
use App\Models\Service;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use App\Services\Finnotech\FinnotechService;
use App\Services\Finnotech\SmsAuthorizationService;

class TransactionCreditReportController extends BaseFinnotechController
{
    /**
     * Constructor
     */
    public function __construct(FinnotechService $finnotechService, SmsAuthorizationService $smsAuthService)
    {
        parent::__construct($finnotechService, $smsAuthService);
        $this->configureService();
    }

    private string $nationalCode;
    private string $mobile;
    private string $trackId;

    /**
     * Configure service-specific settings
     */
    protected function configureService(): void
    {
        Log::info('🔧 TransactionCreditReportController configureService called');
        
        $this->apiEndpoint = 'transaction-credit-report';
        $this->scope = 'kyc:transaction-credit-inquiry-request:get';
        $this->requiresSms = true;
        $this->httpMethod = 'GET';
        
        $this->requiredFields = ['national_code', 'mobile'];
        $this->validationRules = [
            'national_code' => 'required|string|size:10',
            'mobile' => 'required|string|regex:/^09[0-9]{9}$/',
        ];
        $this->validationMessages = [
            'national_code.required' => 'کد ملی الزامی است',
            'national_code.size' => 'کد ملی باید 10 رقم باشد',
            'mobile.required' => 'شماره موبایل الزامی است',
            'mobile.regex' => 'شماره موبایل نامعتبر است',
        ];
        
        Log::info('🔧 TransactionCreditReportController configuration completed', [
            'requiresSms' => $this->requiresSms,
            'apiEndpoint' => $this->apiEndpoint,
            'scope' => $this->scope
        ]);
    }

    /**
     * Prepare API parameters from service data
     */
    protected function prepareApiParameters(array $serviceData): array
    {
        return [
            'mobile' => $serviceData['mobile'] ?? '',
            'nationalCode' => $serviceData['national_code'] ?? '',
        ];
    }

    /**
     * Process service data using Client-Credential flow (Step 1: Request OTP)
     */
    public function process(array $serviceData, Service $service): array
    {
        Log::info('🚀 TransactionCreditReportController process method called (Step 1: Request)', [
            'serviceData' => $serviceData,
            'serviceId' => $service->id
        ]);
        
        try {
            $nationalCode = $serviceData['national_code'];
            $mobile = $serviceData['mobile'];
            $trackId = $this->generateTrackId();
            
            // Get client credential token
            $accessToken = $this->finnotechService->getToken();
            if (!$accessToken) {
                Log::error('Failed to get client credential token for transaction credit report request');
                return [
                    'success' => false,
                    'message' => 'خطا در دریافت توکن احراز هویت'
                ];
            }
            
            $clientId = config('finnotech.client_id');
            
            // Step 1: Request OTP
            $url = config('finnotech.base_url') . "/kyc/v2/clients/{$clientId}/transactionCreditInquiryRequest";
            
            Log::info('Making transaction credit inquiry request API call', [
                'url' => $url,
                'national_code' => $nationalCode,
                'mobile' => $mobile,
                'track_id' => $trackId
            ]);
            
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$accessToken}",
                'Content-Type' => 'application/json',
            ])->get($url, [
                'nationalCode' => $nationalCode,
                'mobile' => $mobile,
                'trackId' => $trackId
            ]);
            
            if (!$response->successful()) {
                Log::error('Transaction credit inquiry request API call failed', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'national_code' => $nationalCode,
                    'mobile' => $mobile
                ]);
                
                return [
                    'success' => false,
                    'message' => 'خطا در ارسال درخواست به سرور'
                ];
            }
            
            $responseData = $response->json();
            
            if (!isset($responseData['status']) || $responseData['status'] !== 'DONE') {
                $errorMessage = $responseData['error']['message'] ?? 'خطا در ارسال درخواست';
                
                Log::warning('Transaction credit inquiry request API returned error status', [
                    'response' => $responseData,
                    'national_code' => $nationalCode,
                    'mobile' => $mobile
                ]);
                
                return [
                    'success' => false,
                    'message' => $errorMessage
                ];
            }
            
            // Store session data for OTP verification
            $this->nationalCode = $nationalCode;
            $this->mobile = $mobile;
            $this->trackId = $trackId;
            
            Session::put('transaction_credit_request', [
                'national_code' => $nationalCode,
                'mobile' => $mobile,
                'track_id' => $trackId,
                'service_id' => $service->id,
                'access_token' => $accessToken,
                'step' => 'otp_sent'
            ]);
            
            Log::info('🎯 Transaction credit inquiry OTP request completed successfully', [
                'national_code' => $nationalCode,
                'mobile' => $mobile,
                'track_id' => $trackId
            ]);

            return [
                'success' => true,
                'requires_otp' => true,
                'message' => 'کد تایید به شماره موبایل شما ارسال شد',
                'data' => [
                    'status' => 'otp_sent',
                    'mobile' => $mobile,
                    'national_code' => $nationalCode,
                    'track_id' => $trackId
                ]
            ];

        } catch (\Exception $e) {
            Log::error('❌ Transaction credit inquiry request processing failed', [
                'national_code' => $serviceData['national_code'] ?? 'unknown',
                'mobile' => $serviceData['mobile'] ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'خطا در پردازش درخواست. لطفاً مجدداً تلاش کنید.'
            ];
        }
    }

    /**
     * Handle OTP verification (Step 2: Verify OTP)
     */
    public function verifyOtp(string $otp, Service $service): array
    {
        Log::info('🚀 TransactionCreditReportController verifyOtp called (Step 2: Verify)', [
            'serviceId' => $service->id
        ]);
        
        try {
            $sessionData = Session::get('transaction_credit_request');
            if (!$sessionData) {
                return [
                    'success' => false,
                    'message' => 'اطلاعات جلسه یافت نشد. لطفاً مجدداً تلاش کنید.'
                ];
            }
            
            $nationalCode = $sessionData['national_code'];
            $accessToken = $sessionData['access_token'];
            $requestTrackId = $sessionData['track_id'];
            $verifyTrackId = $this->generateTrackId();
            
            $clientId = config('finnotech.client_id');
            
            // Step 2: Verify OTP
            $url = config('finnotech.base_url') . "/kyc/v2/clients/{$clientId}/transactionCreditInquiryVerify";
            
            Log::info('Making transaction credit inquiry verify API call', [
                'url' => $url,
                'national_code' => $nationalCode,
                'track_id' => $verifyTrackId
            ]);
            
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$accessToken}",
                'Content-Type' => 'application/json',
            ])->post($url . "?trackId={$verifyTrackId}", [
                'otp' => $otp,
                'nationalCode' => $nationalCode
            ]);
            
            if (!$response->successful()) {
                Log::error('Transaction credit inquiry verify API call failed', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'national_code' => $nationalCode
                ]);
                
                return [
                    'success' => false,
                    'message' => 'خطا در تایید کد. لطفاً مجدداً تلاش کنید.'
                ];
            }
            
            $responseData = $response->json();
            
            if (!isset($responseData['status']) || $responseData['status'] !== 'DONE') {
                $errorMessage = $responseData['error']['message'] ?? 'کد تایید نامعتبر است';
                
                Log::warning('Transaction credit inquiry verify API returned error status', [
                    'response' => $responseData,
                    'national_code' => $nationalCode
                ]);
                
                return [
                    'success' => false,
                    'message' => $errorMessage
                ];
            }
            
            $inquiryTrackId = $responseData['result']['inquiryTrackId'] ?? null;
            if (!$inquiryTrackId) {
                return [
                    'success' => false,
                    'message' => 'خطا در دریافت شناسه پیگیری'
                ];
            }
            
            // Update session with inquiry track ID
            $sessionData['inquiry_track_id'] = $inquiryTrackId;
            $sessionData['step'] = 'verified';
            Session::put('transaction_credit_request', $sessionData);
            
            // Now get the final report (Step 3)
            return $this->getFinalReport($service, $sessionData);
            
        } catch (\Exception $e) {
            Log::error('❌ Transaction credit inquiry verify processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'خطا در تایید کد. لطفاً مجدداً تلاش کنید.'
            ];
        }
    }

    /**
     * Get final report (Step 3: Get Report)
     */
    private function getFinalReport(Service $service, array $sessionData): array
    {
        Log::info('🚀 TransactionCreditReportController getFinalReport called (Step 3: Report)', [
            'serviceId' => $service->id
        ]);
        
        try {
            $inquiryTrackId = $sessionData['inquiry_track_id'];
            $accessToken = $sessionData['access_token'];
            $reportTrackId = $this->generateTrackId();
            
            $clientId = config('finnotech.client_id');
            
            // Step 3: Get Report
            $url = config('finnotech.base_url') . "/kyc/v2/clients/{$clientId}/transactionCreditInquiryReport";
            
            Log::info('Making transaction credit inquiry report API call', [
                'url' => $url,
                'inquiry_track_id' => $inquiryTrackId,
                'track_id' => $reportTrackId
            ]);
            
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$accessToken}",
                'Content-Type' => 'application/json',
            ])->get($url, [
                'inquiryTrackId' => $inquiryTrackId,
                'trackId' => $reportTrackId
            ]);
            
            if (!$response->successful()) {
                Log::error('Transaction credit inquiry report API call failed', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'inquiry_track_id' => $inquiryTrackId
                ]);
                
                return [
                    'success' => false,
                    'message' => 'خطا در دریافت گزارش از سرور'
                ];
            }
            
            $responseData = $response->json();
            
            if (!isset($responseData['status']) || $responseData['status'] !== 'DONE') {
                $errorMessage = $responseData['error']['message'] ?? 'خطا در دریافت گزارش';
                
                Log::warning('Transaction credit inquiry report API returned error status', [
                    'response' => $responseData,
                    'inquiry_track_id' => $inquiryTrackId
                ]);
                
                return [
                    'success' => false,
                    'message' => $errorMessage
                ];
            }
            
            // Process successful response
            $result = $responseData['result'] ?? [];
            
            // Add session data to result for formatting
            $result['_session_data'] = $sessionData;
            
            $formattedData = $this->formatResponseData($result);
            
            // 💰 DEDUCT MONEY ONLY AFTER SUCCESSFUL API RESPONSE
            /** @var \App\Models\User $user */
            $user = \Illuminate\Support\Facades\Auth::user();
            $user->withdraw($service->price, [
                'description' => "پرداخت سرویس: {$service->title}",
                'service_id' => $service->id,
                'type' => 'service_payment',
                'track_id' => $reportTrackId,
                'api_endpoint' => $this->apiEndpoint
            ]);
            
            Log::info('💰 Payment deducted after successful transaction credit report', [
                'user_id' => $user->id,
                'amount' => $service->price,
                'track_id' => $reportTrackId
            ]);
            
            // Clear session data
            Session::forget('transaction_credit_request');
            
            Log::info('🎯 Transaction credit inquiry completed successfully', [
                'national_code' => $sessionData['national_code'],
                'inquiry_track_id' => $inquiryTrackId,
                'track_id' => $reportTrackId
            ]);

            return [
                'success' => true,
                'data' => $formattedData
            ];
            
        } catch (\Exception $e) {
            Log::error('❌ Transaction credit inquiry report processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'خطا در دریافت گزارش. لطفاً مجدداً تلاش کنید.'
            ];
        }
    }

    /**
     * Format response data for display
     */
    protected function formatResponseData(array $responseData): array
    {
        $message = $responseData['message'] ?? 'گزارش دریافت شد';
        $sessionData = $responseData['_session_data'] ?? [];
        
        // Parse the message to extract key information
        $analysis = $this->analyzeReportMessage($message);
        
        return [
            'status' => 'success',
            'user_info' => [
                'national_code' => $sessionData['national_code'] ?? null,
                'mobile' => $sessionData['mobile'] ?? null,
                'inquiry_track_id' => $sessionData['inquiry_track_id'] ?? null,
            ],
            'credit_report' => [
                'message' => $message,
                'analysis' => $analysis,
                'status_code' => $analysis['status_code'],
                'status_description' => $analysis['status_description'],
                'recommendations' => $analysis['recommendations'],
            ]
        ];
    }

    /**
     * Analyze report message to extract key information
     */
    private function analyzeReportMessage(string $message): array
    {
        $analysis = [
            'status_code' => 'unknown',
            'status_description' => 'نامشخص',
            'is_verified' => false,
            'has_negative_record' => false,
            'is_banned' => false,
            'is_bankrupt' => false,
            'has_financial_conviction' => false,
            'recommendations' => []
        ];
        
        // Check various status conditions
        if (str_contains($message, 'احراز شده است')) {
            $analysis['is_verified'] = true;
            $analysis['status_code'] = 'verified';
        }
        
        if (str_contains($message, 'سابقه منفی یافت نشد')) {
            $analysis['has_negative_record'] = false;
            $analysis['status_code'] = 'clean';
            $analysis['status_description'] = 'سابقه پاک';
            $analysis['recommendations'][] = 'شما دارای سابقه مالی پاک هستید.';
        }
        
        if (str_contains($message, 'ممنوع المعامله')) {
            $analysis['is_banned'] = true;
            $analysis['status_code'] = 'banned';
            $analysis['status_description'] = 'ممنوع المعامله';
            $analysis['recommendations'][] = 'فوری به مراجع قانونی مراجعه نمایید.';
        }
        
        if (str_contains($message, 'اعسار')) {
            $analysis['is_bankrupt'] = true;
            $analysis['status_code'] = 'bankrupt';
            $analysis['status_description'] = 'معسر';
            $analysis['recommendations'][] = 'مشاوره حقوقی دریافت نمایید.';
        }
        
        if (str_contains($message, 'محکومیت مالی')) {
            $analysis['has_financial_conviction'] = true;
            $analysis['status_code'] = 'financial_conviction';
            $analysis['status_description'] = 'محکومیت مالی';
            $analysis['recommendations'][] = 'اقدام به رفع محکومیت مالی نمایید.';
        }
        
        if (str_contains($message, 'احراز نشده')) {
            $analysis['is_verified'] = false;
            $analysis['status_code'] = 'not_verified';
            $analysis['status_description'] = 'احراز نشده';
            $analysis['recommendations'][] = 'لطفاً مدارک هویتی خود را بررسی نمایید.';
        }
        
        return $analysis;
    }

    /**
     * Show the result page
     */
    public function show(string $resultId, Service $service)
    {
        $result = $this->getServiceResult($resultId, $service);

        if (!$result) {
            abort(404, 'نتیجه سرویس یافت نشد');
        }

        if ($result->isExpired()) {
            return view('front.services.results.expired');
        }

        // Use the output_data directly since it's already formatted correctly
        return view('front.services.results.transaction-credit-report', [
            'service' => $service,
            'data' => $result->output_data,
            'inputData' => $result->input_data,
            'resultId' => $resultId,
        ]);
    }

    /**
     * Generate track ID
     */
    private function generateTrackId(): string
    {
        return 'transaction_credit_' . uniqid() . '_' . time();
    }
} 