<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Services\BaseSmsFinnotechController;
use App\Contracts\ServicePreviewInterface;
use App\Models\Service;
use App\Services\Finnotech\Finance;
use App\Services\Finnotech\Token;
use Illuminate\Support\Facades\Log;

class LoanInquiryController extends BaseSmsFinnotechController implements ServicePreviewInterface
{
    /**
     * @var Finance
     */
    private $financeService;

    /**
     * Configure service-specific settings
     */
    protected function configureService(): void
    {
        Log::info('🔧 LoanInquiryController configureService called');
        
        $this->apiEndpoint = 'loan-inquiry';
        $this->scope = 'credit:sms-facility-inquiry:get';
        $this->requiresSms = true;
        $this->httpMethod = 'GET';
        
        $this->requiredFields = ['national_code', 'mobile'];
        $this->validationRules = [
            'national_code' => 'required|string|digits:10',
            'mobile' => 'required|string|regex:/^09[0-9]{9}$/',
        ];
        $this->validationMessages = [
            'national_code.required' => 'کد ملی الزامی است',
            'national_code.digits' => 'کد ملی باید 10 رقم باشد',
            'mobile.required' => 'شماره موبایل الزامی است',
            'mobile.regex' => 'شماره موبایل نامعتبر است (باید با 09 شروع شود و 11 رقم باشد)',
        ];

        // Initialize Finance service
        $token = new Token(
            config('services.finnotech.client_id'),
            config('services.finnotech.client_secret'),
            config('services.finnotech.sandbox', false)
        );
        $this->financeService = new Finance($token);
        
        Log::info('🔧 LoanInquiryController configuration completed', [
            'requiresSms' => $this->requiresSms,
            'apiEndpoint' => $this->apiEndpoint
        ]);
    }

    /**
     * Set the current SMS token for the Finance service
     */
    protected function setCurrentSmsToken(string $token): void
    {
        $this->financeService->getToken()->setCurrentSmsToken($token);
    }

    /**
     * Process the loan inquiry service with SMS token
     */
    public function process(array $serviceData, Service $service): array
    {
        Log::info('🚀 LoanInquiryController process method called', [
            'serviceData' => $serviceData,
            'serviceId' => $service->id
        ]);
        
        try {
            $nationalId = $serviceData['national_code'];
            $mobile = $serviceData['mobile'];
            
            // Get SMS token from SmsAuthorizationService
            $smsToken = $this->smsAuthService->getToken(
                $this->scope,
                $nationalId,
                $mobile
            );
            
            if (!$smsToken || !isset($smsToken['access_token'])) {
                Log::error('No SMS token available for loan inquiry', [
                    'national_id' => $nationalId,
                    'mobile' => $mobile,
                    'scope' => $this->scope
                ]);
                
                return [
                    'success' => false,
                    'message' => 'توکن احراز هویت یافت نشد. لطفاً مجدداً تلاش کنید.'
                ];
            }
            
            $accessToken = $smsToken['access_token'];
            $trackId = $this->generateTrackId();
            
            // Build API endpoint according to Finnotech documentation
            $clientId = config('services.finnotech.client_id');
            $baseUrl = config('services.finnotech.sandbox') ? 
                'https://sandboxapi.finnotech.ir' : 
                'https://api.finnotech.ir';
                
            $endpoint = "/credit/v2/clients/{$clientId}/users/{$nationalId}/sms/facilityInquiry";
            $fullUrl = $baseUrl . $endpoint;
            
            Log::info('🔗 Making Finnotech loan inquiry API call', [
                'endpoint' => $endpoint,
                'national_id' => $nationalId,
                'track_id' => $trackId,
                'has_token' => !empty($accessToken)
            ]);
            
            // Make API call using SMS token
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json'
            ])->timeout(30)->get($fullUrl, [
                'trackId' => $trackId
            ]);
            
            if (!$response->successful()) {
                Log::error('Finnotech API call failed', [
                    'status_code' => $response->status(),
                    'response_body' => $response->body(),
                    'endpoint' => $endpoint
                ]);
                
                return [
                    'success' => false,
                    'message' => 'خطا در ارتباط با سرویس دهنده. لطفاً مجدداً تلاش کنید.'
                ];
            }
            
            $apiResponse = $response->json();
            
            Log::info('✅ Finnotech API response received', [
                'status' => $apiResponse['status'] ?? 'unknown',
                'track_id' => $trackId,
                'has_result' => isset($apiResponse['result'])
            ]);
            
            // Check API response status
            if (!isset($apiResponse['status']) || $apiResponse['status'] !== 'DONE') {
                $errorMessage = $apiResponse['error']['message'] ?? 'خطا در دریافت اطلاعات تسهیلات';
                
                Log::warning('Finnotech API returned error status', [
                    'response' => $apiResponse,
                    'track_id' => $trackId
                ]);
                
                return [
                    'success' => false,
                    'message' => $errorMessage
                ];
            }
            
            // Process successful response
            $result = $apiResponse['result'] ?? [];
            $formattedData = $this->formatResponseData($result);
            
            // 💰 DEDUCT MONEY ONLY AFTER SUCCESSFUL API RESPONSE
            /** @var \App\Models\User $user */
            $user = \Illuminate\Support\Facades\Auth::user();
            $user->withdraw($service->price, [
                'description' => "پرداخت سرویس: {$service->title}",
                'service_id' => $service->id,
                'type' => 'service_payment',
                'track_id' => $trackId,
                'api_endpoint' => $endpoint
            ]);
            
            Log::info('💰 Payment deducted after successful loan inquiry', [
                'user_id' => $user->id,
                'amount' => $service->price,
                'track_id' => $trackId
            ]);
            
            Log::info('🎯 Loan inquiry completed successfully', [
                'national_id' => $nationalId,
                'track_id' => $trackId,
                'facility_count' => count($result['facilityList'] ?? []),
                'has_facilities' => isset($result['facilityList']) && count($result['facilityList']) > 0
            ]);

            // Note: SMS token revocation is handled automatically in SmsAuthorizationService.makeAuthorizedApiCall()

            return [
                'success' => true,
                'data' => $formattedData
            ];

        } catch (\Exception $e) {
            Log::error('❌ Loan inquiry processing failed', [
                'national_id' => $serviceData['national_code'] ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'خطا در پردازش سرویس: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Format response data for display
     */
    protected function formatResponseData(array $responseData): array
    {
        $rawResponseData = $responseData;
        if(isset($rawResponseData['result'])){
            $responseData = $rawResponseData['result'];
        }
        
        if (!isset($responseData['facilityList']) || empty($responseData['facilityList'])) {
            return [
                'status' => 'no_facilities',
                'message' => $responseData['message'] ?? 'هیچ تسهیلاتی یافت نشد',
                'national_code' => $responseData['user'] ?? null,
                'customer_name' => $responseData['name'] ?? null,
                'legal_id' => $responseData['legalId'] ?? null,
            ];
        }

        $facilityList = $responseData['facilityList'];
        
        // Format summary amounts
        $facilityTotalAmount = intval($responseData['facilityTotalAmount'] ?? 0);
        $facilityDebtTotalAmount = intval($responseData['facilityDebtTotalAmount'] ?? 0);
        $facilityPastExpiredTotalAmount = intval($responseData['facilityPastExpiredTotalAmount'] ?? 0);
        $facilityDeferredTotalAmount = intval($responseData['facilityDeferredTotalAmount'] ?? 0);
        $facilitySuspiciousTotalAmount = intval($responseData['facilitySuspiciousTotalAmount'] ?? 0);
        
        return [
            'status' => 'success',
            'national_code' => $responseData['user'] ?? null,
            'customer_name' => $responseData['name'] ?? null,
            'legal_id' => $responseData['legalId'] ?? null,
            'facility_summary' => [
                'total_amount' => $facilityTotalAmount,
                'debt_total_amount' => $facilityDebtTotalAmount,
                'past_expired_total_amount' => $facilityPastExpiredTotalAmount,
                'deferred_total_amount' => $facilityDeferredTotalAmount,
                'suspicious_total_amount' => $facilitySuspiciousTotalAmount,
                'dishonored' => $responseData['dishonored'] ?? '',
                'formatted_total_amount' => $this->formatCurrency($facilityTotalAmount),
                'formatted_debt_total_amount' => $this->formatCurrency($facilityDebtTotalAmount),
                'formatted_past_expired_total_amount' => $this->formatCurrency($facilityPastExpiredTotalAmount),
                'formatted_deferred_total_amount' => $this->formatCurrency($facilityDeferredTotalAmount),
                'formatted_suspicious_total_amount' => $this->formatCurrency($facilitySuspiciousTotalAmount),
            ],
            'facility_list' => array_map(function ($facility) {
                $originalAmount = intval(floatval($facility['FacilityAmountOrginal'] ?? 0));
                $debtAmount = intval($facility['FacilityDebtorTotalAmount'] ?? 0);
                $pastExpiredAmount = intval($facility['FacilityPastExpiredAmount'] ?? 0);
                $deferredAmount = intval($facility['FacilityDeferredAmount'] ?? 0);
                $suspiciousAmount = intval($facility['FacilitySuspiciousAmount'] ?? 0);
                $benefitAmount = intval(floatval($facility['FacilityBenefitAmount'] ?? 0));
                
                // Get bank info including logo
                $bankInfo = $this->getBankInfoFromCode($facility['FacilityBankCode'] ?? '');
                
                return [
                    'bank_code' => $facility['FacilityBankCode'] ?? '',
                    'bank_name' => $bankInfo['name'],
                    'bank_logo' => $bankInfo['logo'],
                    'bank_color' => $bankInfo['color'],
                    'branch_code' => $facility['FacilityBranchCode'] ?? '',
                    'branch_name' => $facility['FacilityBranch'] ?? '',
                    'request_number' => $facility['FacilityRequestNo'] ?? '',
                    'facility_type' => $facility['FacilityType'] ?? '',
                    'facility_type_name' => $this->getFacilityTypeName($facility['FacilityType'] ?? ''),
                    'facility_status' => $facility['FacilityStatus'] ?? '',
                    'is_active' => ($facility['FacilityStatus'] ?? '') === 'جاری',
                    'original_amount' => $originalAmount,
                    'benefit_amount' => $benefitAmount,
                    'debt_total_amount' => $debtAmount,
                    'past_expired_amount' => $pastExpiredAmount,
                    'deferred_amount' => $deferredAmount,
                    'suspicious_amount' => $suspiciousAmount,
                    'formatted_original_amount' => $this->formatCurrency($originalAmount),
                    'formatted_benefit_amount' => $this->formatCurrency($benefitAmount),
                    'formatted_debt_total_amount' => $this->formatCurrency($debtAmount),
                    'formatted_past_expired_amount' => $this->formatCurrency($pastExpiredAmount),
                    'formatted_deferred_amount' => $this->formatCurrency($deferredAmount),
                    'formatted_suspicious_amount' => $this->formatCurrency($suspiciousAmount),
                    'set_date' => $this->formatPersianDate($facility['FacilitySetDate'] ?? ''),
                    'end_date' => $this->formatPersianDate($facility['FacilityEndDate'] ?? ''),
                    'group' => $facility['FacilityGroup'] ?? '',
                    'has_past_due' => $pastExpiredAmount > 0,
                    'has_deferred' => $deferredAmount > 0,
                    'has_suspicious' => $suspiciousAmount > 0,
                ];
            }, $facilityList),
            'facility_count' => count($facilityList)
        ];
    }

    /**
     * Format result data for display
     */
    protected function formatResultForDisplay(array $resultData): array
    {
        // If data is already formatted by formatResponseData, return as-is
        if (isset($resultData['status']) && in_array($resultData['status'], ['success', 'no_facilities'])) {
            return $resultData;
        }
        
        // If we have raw API response, format it
        if (isset($resultData['result'])) {
            return $this->formatResponseData($resultData);
        }
        
        // If we have direct API data, format it
        return $this->formatResponseData($resultData);
    }

    /**
     * Get bank information from bank code including logo
     */
    private function getBankInfoFromCode(string $bankCode): array
    {
        $bankNames = [
            '11' => 'بانک صنعت و معدن',
            '12' => 'بانک ملت',
            '13' => 'بانک رفاه کارگران', 
            '14' => 'بانک مسکن',
            '15' => 'بانک توسعه تعاون',
            '16' => 'بانک اقتصاد نوین',
            '17' => 'بانک ملی ایران',
            '18' => 'بانک پاسارگاد',
            '19' => 'بانک صنعت ایران',
            '20' => 'بانک توسعه صادرات',
            '21' => 'بانک پست بانک ایران',
            '22' => 'بانک تجارت',
            '23' => 'بانک کشاورزی',
            '24' => 'بانک صادرات ایران',
            '25' => 'بانک مرکزی ایران',
            // Add more bank codes as needed
        ];

        $bankName = $bankNames[$bankCode] ?? "بانک کد {$bankCode}";
        
        // Try to find bank in database by name match
        $bank = \App\Models\Bank::where('name', 'LIKE', '%' . trim(str_replace(['بانک ', 'ایران'], '', $bankName)) . '%')
                                ->where('is_active', true)
                                ->first();
        
        if (!$bank) {
            // Alternative matching by common patterns
            $searchTerms = [
                '11' => ['صنعت', 'معدن'],
                '12' => ['ملت'],
                '13' => ['رفاه'],
                '14' => ['مسکن'],
                '15' => ['تعاون'],
                '16' => ['اقتصاد', 'نوین'],
                '17' => ['ملی'],
                '18' => ['پاسارگاد'],
                '19' => ['صنعت'],
                '20' => ['صادرات'],
                '21' => ['پست'],
                '22' => ['تجارت'],
                '23' => ['کشاورزی'],
                '24' => ['صادرات'],
                '25' => ['مرکزی'],
            ];
            
            if (isset($searchTerms[$bankCode])) {
                foreach ($searchTerms[$bankCode] as $term) {
                    $bank = \App\Models\Bank::where('name', 'LIKE', '%' . $term . '%')
                                          ->where('is_active', true)
                                          ->first();
                    if ($bank) break;
                }
            }
        }
        
        return [
            'name' => $bankName,
            'logo' => $bank && $bank->logo ? asset($bank->logo) : null,
            'color' => $bank ? $bank->color : null,
        ];
    }

    /**
     * Get bank name from bank code (backward compatibility)
     */
    private function getBankNameFromCode(string $bankCode): string
    {
        return $this->getBankInfoFromCode($bankCode)['name'];
    }

    /**
     * Format currency amount
     */
    private function formatCurrency(int $amount): string
    {
        if ($amount == 0) {
            return '0 تومان';
        }
        
        // Convert to readable format with thousand separators
        $formatted = number_format($amount);
        return $formatted . ' تومان';
    }

    /**
     * Format Persian date from YYYYMMDD format
     */
    private function formatPersianDate(string $date): string
    {
        if (empty($date) || strlen($date) != 8) {
            return $date;
        }
        
        $year = substr($date, 0, 4);
        $month = substr($date, 4, 2);
        $day = substr($date, 6, 2);
        
        return $year . '/' . $month . '/' . $day;
    }

    /**
     * Get facility type name from type code
     */
    private function getFacilityTypeName(string $typeCode): string
    {
        $facilityTypes = [
            '10' => 'قرض الحسنه',
            '11' => 'مشارکت مدنی', 
            '12' => 'مشارکت حقوقی',
            '13' => 'سرمایه گذاری مستقیم',
            '14' => 'مضاربه',
            '15' => 'معاملات سلف',
            '16' => 'فروش اقساطی مواد اولیه، لوازم یدکی و ابزار کار',
            '17' => 'فروش اقساطی وسایل تولید ماشین‌آلات و تاسیسات',
            '18' => 'فروش اقساطی مسکن',
            '19' => 'اجاره به شرط تملیک',
            '20' => 'جعاله',
            '21' => 'مزارعه',
            '22' => 'مساقات',
            '23' => 'خرید دین',
            '24' => 'معاملات قدیم',
            '25' => 'استصناع',
            '26' => 'مرابحه',
        ];

        return $facilityTypes[$typeCode] ?? "نوع {$typeCode}";
    }

    /**
     * Generate a unique tracking ID
     */
    private function generateTrackId(): string
    {
        return 'loan_' . time() . '_' . rand(1000, 9999);
    }

    /**
     * Show service result using specific loan inquiry view
     */
    public function show(string $resultId, Service $service)
    {
        $result = \App\Models\ServiceResult::where('result_hash', $resultId)
            ->where('service_id', $service->id)
            ->where('status', 'success')
            ->firstOrFail();

        // Check authorization
        if (!\Illuminate\Support\Facades\Auth::check() || $result->user_id !== \Illuminate\Support\Facades\Auth::id()) {
            abort(401, 'شما مجاز به مشاهده این نتیجه نیستید.');
        }

        // Check if result is expired
        if ($result->isExpired()) {
            abort(410, 'این نتیجه منقضی شده است.');
        }

        // Use the output_data directly since it's already formatted correctly
        return view('front.services.results.loan-inquiry', [
            'service' => $service,
            'data' => ['status' => 'success', 'data' => $result->output_data],
            'inputData' => $result->input_data,
            'resultId' => $resultId,
        ]);
    }

    /**
     * Check if this service supports preview functionality
     */
    public function supportsPreview(): bool
    {
        return true;
    }

    /**
     * Get preview data for this service
     */
    public function getPreviewData(array $serviceData, Service $service): array
    {
        try {
            // Return sample preview data for loan inquiry
            $previewData = [
                'loans_count' => 2,
                'total_amount' => '150,000,000',
                'status' => 'سالم',
                'loans' => [
                    [
                        'type' => 'وام قرض‌الحسنه',
                        'amount' => '100,000,000',
                        'date' => '1402/08/15',
                        'status' => 'فعال',
                        'bank' => 'ملت'
                    ],
                    [
                        'type' => 'تسهیلات مسکن',
                        'amount' => '50,000,000',
                        'date' => '1403/02/10',
                        'status' => 'در حال پرداخت',
                        'bank' => 'سپه',
                        'progress' => 65
                    ]
                ]
            ];
            
            return [
                'success' => true,
                'preview_data' => $previewData,
                'from_cache' => false
            ];
            
        } catch (\Exception $e) {
            Log::error('Error generating loan inquiry preview data', [
                'error' => $e->getMessage(),
                'service_data' => $serviceData
            ]);
            
            return [
                'success' => false,
                'error' => 'خطا در تولید داده‌های پیش‌نمایش'
            ];
        }
    }

    /**
     * Get preview template name
     */
    public function getPreviewTemplate(): string
    {
        return 'front.services.custom.loan-inquiry.preview';
    }
} 