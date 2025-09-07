<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Services\BaseFinnotechController;
use App\Models\Service;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Services\Finnotech\FinnotechService;
use App\Services\Finnotech\SmsAuthorizationService;

class ChequeColorInquiryController extends BaseFinnotechController
{
    /**
     * Constructor
     */
    public function __construct(FinnotechService $finnotechService, SmsAuthorizationService $smsAuthService)
    {
        parent::__construct($finnotechService, $smsAuthService);
        $this->configureService();
    }

    private string $nationalId;
    private string $trackId;

    /**
     * Configure service-specific settings
     */
    protected function configureService(): void
    {
        Log::info('🔧 ChequeColorInquiryController configureService called');
        
        $this->apiEndpoint = 'cheque-color-inquiry';
        $this->scope = 'credit:cheque-color-inquiry:get';
        $this->requiresSms = false;
        $this->httpMethod = 'GET';
        
        $this->requiredFields = ['national_code'];
        
        $this->validationRules = [
            'national_code' => 'required|string|digits:10',
        ];
        
        $this->validationMessages = [
            'national_code.required' => 'کد ملی الزامی است',
            'national_code.digits' => 'کد ملی باید 10 رقم باشد',
        ];
        
        Log::info('🔧 ChequeColorInquiryController configuration completed', [
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
            'idCode' => $serviceData['national_code'] ?? '',  // API expects idCode for national ID
        ];
    }

    /**
     * Process service data using Client-Credential flow
     */
    public function process(array $serviceData, Service $service): array
    {
        Log::info('🚀 ChequeColorInquiryController process method called', [
            'serviceData' => $serviceData,
            'serviceId' => $service->id
        ]);
        
        try {
            $nationalId = $serviceData['national_code'];
            $trackId = $this->generateTrackId();
            
            // Get client credential token
            $accessToken = $this->finnotechService->getToken();
            if (!$accessToken) {
                Log::error('Failed to get client credential token for cheque color inquiry');
                return [
                    'success' => false,
                    'message' => 'خطا در دریافت توکن احراز هویت'
                ];
            }
            $clientId = config('finnotech.client_id');
            
            // Make API call
            $url = config('finnotech.base_url') . "/credit/v2/clients/{$clientId}/chequeColorInquiry";
            
            Log::info('Making cheque color inquiry API call', [
                'url' => $url,
                'national_id' => $nationalId,
                'track_id' => $trackId
            ]);
            
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$accessToken}",
                'Content-Type' => 'application/json',
            ])->get($url, [
                'idCode' => $nationalId,
                'trackId' => $trackId
            ]);
            
            if (!$response->successful()) {
                Log::error('Cheque color inquiry API call failed', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'national_id' => $nationalId
                ]);
                
                return [
                    'success' => false,
                    'message' => 'خطا در دریافت اطلاعات رنگ چکاز سرور'
                ];
            }
            
            $responseData = $response->json();
            
            if (!isset($responseData['status']) || $responseData['status'] !== 'DONE') {
                $errorMessage = $responseData['error']['message'] ?? 'خطا در دریافت اطلاعات رنگ چک';
                
                Log::warning('Cheque color inquiry API returned error status', [
                    'response' => $responseData,
                    'national_id' => $nationalId
                ]);
                
                return [
                    'success' => false,
                    'message' => $errorMessage
                ];
            }
            
            // Process successful response
            $result = $responseData['result'] ?? [];
            
            // Store additional data for formatting
            $this->nationalId = $nationalId;
            $this->trackId = $trackId;
            
            $formattedData = $this->formatResponseData($result);
            
            // 💰 DEDUCT MONEY ONLY AFTER SUCCESSFUL API RESPONSE
            /** @var \App\Models\User $user */
            $user = \Illuminate\Support\Facades\Auth::user();
            $user->withdraw($service->price, [
                'description' => "پرداخت سرویس: {$service->title}",
                'service_id' => $service->id,
                'type' => 'service_payment',
                'track_id' => $trackId,
                'api_endpoint' => $this->apiEndpoint
            ]);
            
            Log::info('💰 Payment deducted after successful cheque color inquiry', [
                'user_id' => $user->id,
                'amount' => $service->price,
                'track_id' => $trackId
            ]);
            
            Log::info('🎯 Cheque color inquiry completed successfully', [
                'national_id' => $nationalId,
                'track_id' => $trackId,
                'cheque_color' => $result['chequeColor'] ?? 'unknown'
            ]);

            return [
                'success' => true,
                'data' => $formattedData
            ];

        } catch (\Exception $e) {
            Log::error('❌ Cheque color inquiry processing failed', [
                'national_id' => $serviceData['national_code'] ?? 'unknown',
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
     * Format response data for display
     */
    protected function formatResponseData(array $responseData): array
    {
        $chequeColor = $responseData['chequeColor'] ?? '0';
        $colorInfo = $this->getChequeColorInfo($chequeColor);
        
        return [
            'status' => 'success',
            'user_info' => [
                'national_id' => $this->nationalId,
                'track_id' => $this->trackId,
            ],
            'cheque_color' => [
                'code' => $chequeColor,
                'name' => $colorInfo['name'],
                'description' => $colorInfo['description'],
                'color_class' => $colorInfo['color_class'],
                'risk_level' => $colorInfo['risk_level'],
                'risk_level_name' => $colorInfo['risk_level_name'],
                'icon' => $colorInfo['icon'],
                'recommendation' => $colorInfo['recommendation'],
            ]
        ];
    }

    /**
     * Get cheque color information
     */
    private function getChequeColorInfo(string $colorCode): array
    {
        $colorInfo = [
            '1' => [
                'name' => 'سفید',
                'description' => 'وضعیت سفید به این معناست که صادرکننده چک فاقد هرگونه سابقه چک برگشتی بوده یا در صورت وجود سابقه، تمامی موارد رفع سوء اثر شده است.',
                'color_class' => 'bg-gray-100 text-gray-800 border-gray-200',
                'risk_level' => 'بدون ریسک',
                'risk_level_name' => 'عالی',
                'icon' => '✅',
                'recommendation' => 'شما دارای سابقه مالی پاک هستید.'
            ],
            '2' => [
                'name' => 'زرد',
                'description' => 'وضعیت زرد به معنای داشتن یک فقره چک برگشتی یا حداکثر مبلغ 50 میلیون ریال تعهد برگشتی است.',
                'color_class' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                'risk_level' => 'ریسک پایین',
                'risk_level_name' => 'قابل قبول',
                'icon' => '⚠️',
                'recommendation' => 'توصیه می‌شود سابقه خود را پاک نمایید.'
            ],
            '3' => [
                'name' => 'نارنجی',
                'description' => 'وضعیت نارنجی نشان می دهد که صادرکننده چک دارای دو الی چهار فقره چک برگشتی یا حداکثر مبلغ 200 میلیون ریال تعهد برگشتی است.',
                'color_class' => 'bg-orange-100 text-orange-800 border-orange-200',
                'risk_level' => 'ریسک متوسط',
                'risk_level_name' => 'نیاز به توجه',
                'icon' => '🔶',
                'recommendation' => 'لازم است اقدامات لازم برای رفع سوء اثر انجام دهید.'
            ],
            '4' => [
                'name' => 'قهوه‌ای',
                'description' => 'وضعیت قهوه ای از این حکایت دارد که صادرکننده چک دارای پنج تا ده فقره چک برگشتی یا حداکثر مبلغ 500 میلیون ریال تعهد برگشتی است.',
                'color_class' => 'bg-amber-100 text-amber-800 border-amber-200',
                'risk_level' => 'ریسک بالا',
                'risk_level_name' => 'خطرناک',
                'icon' => '🔴',
                'recommendation' => 'فوری باید نسبت به رفع سوء اثر اقدام نمایید.'
            ],
            '5' => [
                'name' => 'قرمز',
                'description' => 'وضعیت قرمز نیز حاکی از این است که صادرکننده چک دارای بیش از ده فقره چک برگشتی یا بیش از مبلغ 500 میلیون ریال تعهد برگشتی است.',
                'color_class' => 'bg-red-100 text-red-800 border-red-200',
                'risk_level' => 'ریسک بسیار بالا',
                'risk_level_name' => 'بحرانی',
                'icon' => '🚨',
                'recommendation' => 'وضعیت بحرانی - فوری به مشاور مالی مراجعه نمایید.'
            ],
        ];
        
        return $colorInfo[$colorCode] ?? [
            'name' => 'نامشخص',
            'description' => 'اطلاعات رنگ چک نامشخص است.',
            'color_class' => 'bg-gray-100 text-gray-800 border-gray-200',
            'risk_level' => 'نامشخص',
            'risk_level_name' => 'نامشخص',
            'icon' => '❓',
            'recommendation' => 'لطفاً مجدداً استعلام نمایید.'
        ];
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
        return view('front.services.results.cheque-color-inquiry', [
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
        return 'cheque_color_' . uniqid() . '_' . time();
    }
} 