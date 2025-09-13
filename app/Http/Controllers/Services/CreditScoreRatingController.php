<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Services\BaseSmsFinnotechController;
use App\Contracts\ServicePreviewInterface;
use App\Models\Service;
use Illuminate\Support\Facades\Log;

class CreditScoreRatingController extends BaseSmsFinnotechController implements ServicePreviewInterface
{
    /**
     * Configure service-specific settings
     */
    protected function configureService(): void
    {
        Log::info('🔧 CreditScoreRatingController configureService called');
        
        $this->apiEndpoint = 'credit-score-rating';
        $this->scope = 'credit:sms-rating:get';
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
        
        Log::info('🔧 CreditScoreRatingController configuration completed', [
            'requiresSms' => $this->requiresSms,
            'apiEndpoint' => $this->apiEndpoint,
            'scope' => $this->scope
        ]);
    }

    /**
     * Format response data for display
     */
    protected function formatResponseData(array $responseData): array
    {
        Log::info('🔄 CreditScoreRatingController formatResponseData called', [
            'response_keys' => array_keys($responseData)
        ]);

        $formatted = [
            'service_name' => 'رتبه‌بندی اعتباری',
            'status' => 'success',
            'data' => $responseData
        ];

        try {
            // Extract Iranian banking credit information if available
            if (isset($responseData['data'])) {
                $data = $responseData['data'];
                
                // Iranian credit scoring system (0-900 scale)
                $formatted['credit_info'] = [
                    'score' => $data['credit_score'] ?? $data['score'] ?? 0,
                    'max_score' => 900, // Iranian system uses 0-900 scale
                    'rating' => $data['rating'] ?? 'نامشخص',
                    'rating_grade' => $data['grade'] ?? $this->getRatingGrade($data['credit_score'] ?? $data['score'] ?? 0),
                    'percentage' => $data['percentage'] ?? round(($data['credit_score'] ?? $data['score'] ?? 0) / 900 * 100),
                    'status' => $data['status'] ?? 'قابل بررسی'
                ];
                
                // Banking status information
                $formatted['banking_status'] = [
                    'blacklist_status' => $data['blacklist_status'] ?? 'قابل بررسی',
                    'returned_cheques' => $data['returned_cheques'] ?? 'نامشخص',
                    'facility_status' => $data['facility_status'] ?? 'قابل بررسی',
                    'guarantee_status' => $data['guarantee_status'] ?? 'قابل بررسی'
                ];
                
                // Credit factors with Iranian banking system terminology
                $formatted['credit_factors'] = [
                    'payment_history' => [
                        'status' => $data['payment_history'] ?? 'قابل بررسی',
                        'description' => 'تاریخچه پرداخت اقساط و تسهیلات'
                    ],
                    'facility_utilization' => [
                        'status' => $data['facility_utilization'] ?? 'قابل بررسی',
                        'description' => 'نحوه استفاده از تسهیلات بانکی'
                    ],
                    'credit_length' => [
                        'status' => $data['credit_length'] ?? 'قابل بررسی',
                        'description' => 'سابقه کار با سیستم بانکی'
                    ],
                    'guarantor_status' => [
                        'status' => $data['guarantor_status'] ?? 'قابل بررسی',
                        'description' => 'وضعیت تعهدات ضمانتی'
                    ]
                ];
            }
        } catch (\Exception $e) {
            Log::error('Error formatting credit score rating response', [
                'error' => $e->getMessage(),
                'response' => $responseData
            ]);
        }

        Log::info('✅ CreditScoreRatingController response formatting completed', [
            'formatted_keys' => array_keys($formatted)
        ]);

        return $formatted;
    }

    /**
     * Get service display name
     */
    public function getServiceDisplayName(): string
    {
        return 'رتبه‌بندی اعتباری';
    }

    /**
     * Get service description
     */
    public function getServiceDescription(): string
    {
        return 'استعلام امتیاز اعتباری و رتبه بانکی از مرکز اطلاعات اعتباری ایران - بررسی تاریخچه پرداخت، محکومیت‌های مالی و وضعیت دریافت تسهیلات';
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
            // Return sample preview data based on Iranian banking credit system
            $previewData = [
                'credit_info' => [
                    'credit_score' => 785,
                    'max_score' => 900,
                    'rating' => 'عالی',
                    'rating_grade' => 'A',
                    'percentage' => 87,
                    'status' => 'قابل دریافت تسهیلات'
                ],
                'banking_status' => [
                    'blacklist_status' => 'عدم وجود در لیست سیاه',
                    'returned_cheques' => 0,
                    'facility_status' => 'بدون تسهیلات معوق',
                    'guarantee_status' => 'بدون ضمانت معوق'
                ],
                'credit_factors' => [
                    'payment_history' => [
                        'score' => 95, 
                        'status' => 'عالی', 
                        'description' => 'تاریخچه پرداخت منظم و بدون تاخیر'
                    ],
                    'facility_utilization' => [
                        'score' => 78, 
                        'status' => 'خوب', 
                        'description' => 'استفاده متعادل از تسهیلات بانکی'
                    ],
                    'credit_length' => [
                        'score' => 82, 
                        'status' => 'خوب', 
                        'description' => 'سابقه مطلوب در سیستم بانکی'
                    ],
                    'guarantor_status' => [
                        'score' => 90, 
                        'status' => 'عالی', 
                        'description' => 'عدم وجود تعهدات ضمانتی معوق'
                    ]
                ],
                'available_facilities' => [
                    'personal_loan' => 'قابل دریافت تا 500 میلیون ریال',
                    'credit_card' => 'قابل دریافت با حد اعتباری بالا',
                    'car_loan' => 'قابل دریافت با شرایط مناسب',
                    'mortgage' => 'قابل دریافت با بررسی کارشناسی'
                ],
                'recommendations' => [
                    'حفظ رکورد پرداخت به موقع اقساط',
                    'عدم افزایش بیش از حد تعهدات ضمانتی',
                    'مراجعه به شعب بانک‌ها برای دریافت تسهیلات'
                ],
                'warning_notes' => [
                    'این اطلاعات بر اساس آخرین داده‌های مرکز اطلاعات اعتباری است',
                    'رتبه اعتباری ممکن است بر اساس تراکنش‌های جدید تغییر کند',
                    'تصمیم نهایی درخصوص تسهیلات با بانک‌های مربوطه است'
                ]
            ];
            
            return [
                'success' => true,
                'preview_data' => $previewData,
                'from_cache' => false
            ];
            
        } catch (\Exception $e) {
            Log::error('Error generating credit score rating preview data', [
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
        return 'front.services.custom.credit-score-rating.preview';
    }

    /**
     * Get credit rating grade based on Iranian banking system
     */
    private function getRatingGrade(int $score): string
    {
        if ($score >= 800) return 'A+';
        if ($score >= 700) return 'A';
        if ($score >= 600) return 'B';
        if ($score >= 500) return 'C';
        if ($score >= 400) return 'D';
        return 'F';
    }
}