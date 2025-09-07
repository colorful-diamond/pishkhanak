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
            // Extract credit score information if available
            if (isset($responseData['data'])) {
                $data = $responseData['data'];
                
                $formatted['credit_score'] = [
                    'score' => $data['score'] ?? 0,
                    'max_score' => $data['max_score'] ?? 850,
                    'rating' => $data['rating'] ?? 'نامشخص',
                    'factors' => $data['factors'] ?? []
                ];
                
                $formatted['summary'] = [
                    'payment_history' => $data['payment_history'] ?? 'نامشخص',
                    'credit_utilization' => $data['credit_utilization'] ?? 'نامشخص',
                    'credit_length' => $data['credit_length'] ?? 'نامشخص',
                    'credit_mix' => $data['credit_mix'] ?? 'نامشخص',
                    'new_credit' => $data['new_credit'] ?? 'نامشخص'
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
        return 'مشاهده امتیاز و رتبه اعتباری شما در سیستم بانکی';
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
            // Return sample preview data for credit score rating
            $previewData = [
                'credit_score' => [
                    'score' => 750,
                    'max_score' => 850,
                    'rating' => 'عالی',
                    'percentage' => 88
                ],
                'factors' => [
                    'payment_history' => ['score' => 95, 'impact' => 'بالا'],
                    'credit_utilization' => ['score' => 25, 'impact' => 'متوسط'],
                    'credit_length' => ['score' => 80, 'impact' => 'متوسط'],
                    'credit_mix' => ['score' => 60, 'impact' => 'کم'],
                    'new_credit' => ['score' => 85, 'impact' => 'کم']
                ],
                'recommendations' => [
                    'تنوع بیشتر در انواع اعتبار',
                    'کاهش درخواست‌های جدید اعتبار'
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
}