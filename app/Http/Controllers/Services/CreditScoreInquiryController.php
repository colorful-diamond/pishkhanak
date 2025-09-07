<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Services\BaseFinnotechController;
use App\Services\Finnotech\FinnotechService;
use App\Services\Finnotech\SmsAuthorizationService;

class CreditScoreInquiryController extends BaseFinnotechController
{
    /**
     * Constructor
     */
    public function __construct(FinnotechService $finnotechService, SmsAuthorizationService $smsAuthService)
    {
        parent::__construct($finnotechService, $smsAuthService);
        $this->configureService();
    }

    /**
     * Configure service-specific settings
     */
    protected function configureService(): void
    {
        $this->apiEndpoint = 'credit-score-inquiry';
        $this->scope = 'credit:score:get';
        $this->requiresSms = true; // Credit services require SMS verification
        $this->httpMethod = 'GET';
        
        $this->requiredFields = ['mobile', 'national_code'];
        
        $this->validationRules = [
            'mobile' => 'required|string|regex:/^09[0-9]{9}$/',
            'national_code' => 'required|string|digits:10',
        ];
        
        $this->validationMessages = [
            'mobile.required' => 'شماره موبایل الزامی است',
            'mobile.regex' => 'شماره موبایل نامعتبر است',
            'national_code.required' => 'کد ملی الزامی است',
            'national_code.digits' => 'کد ملی باید 10 رقم باشد',
        ];
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
     * Format response data for display
     */
    protected function formatResponseData(array $responseData): array
    {
        if (!isset($responseData['result'])) {
            return ['status' => 'failed', 'message' => 'خطا در دریافت امتیاز اعتباری'];
        }

        $result = $responseData['result'];
        
        return [
            'status' => 'success',
            'data' => [
                'national_code' => request('national_code'),
                'credit_score' => [
                    'score' => $result['score'] ?? 0,
                    'max_score' => $result['maxScore'] ?? 850,
                    'score_range' => $this->getScoreRange($result['score'] ?? 0),
                    'score_status' => $this->getScoreStatus($result['score'] ?? 0),
                    'percentile' => $result['percentile'] ?? 0,
                ],
                'score_breakdown' => [
                    'payment_history' => $result['paymentHistory'] ?? 0,
                    'credit_utilization' => $result['creditUtilization'] ?? 0,
                    'credit_history_length' => $result['creditHistoryLength'] ?? 0,
                    'credit_mix' => $result['creditMix'] ?? 0,
                    'new_credit' => $result['newCredit'] ?? 0,
                ],
                'credit_info' => [
                    'total_accounts' => $result['totalAccounts'] ?? 0,
                    'active_accounts' => $result['activeAccounts'] ?? 0,
                    'closed_accounts' => $result['closedAccounts'] ?? 0,
                    'total_credit_limit' => $result['totalCreditLimit'] ?? 0,
                    'total_balance' => $result['totalBalance'] ?? 0,
                    'credit_utilization_ratio' => $result['creditUtilizationRatio'] ?? 0,
                ],
                'negative_factors' => [
                    'late_payments' => $result['latePayments'] ?? 0,
                    'defaults' => $result['defaults'] ?? 0,
                    'bankruptcies' => $result['bankruptcies'] ?? 0,
                    'collections' => $result['collections'] ?? 0,
                ],
                'recommendations' => $this->getRecommendations($result),
                'formatted_amounts' => [
                    'total_credit_limit' => number_format($result['totalCreditLimit'] ?? 0) . ' ریال',
                    'total_balance' => number_format($result['totalBalance'] ?? 0) . ' ریال',
                ],
                'last_updated' => $result['lastUpdated'] ?? date('Y-m-d'),
            ]
        ];
    }

    /**
     * Get score range description
     */
    private function getScoreRange(int $score): string
    {
        if ($score >= 750) return 'عالی (750-850)';
        if ($score >= 650) return 'خوب (650-749)';
        if ($score >= 550) return 'متوسط (550-649)';
        if ($score >= 450) return 'ضعیف (450-549)';
        return 'بسیار ضعیف (300-449)';
    }

    /**
     * Get score status
     */
    private function getScoreStatus(int $score): string
    {
        if ($score >= 750) return 'عالی';
        if ($score >= 650) return 'خوب';
        if ($score >= 550) return 'متوسط';
        if ($score >= 450) return 'ضعیف';
        return 'بسیار ضعیف';
    }

    /**
     * Get recommendations based on credit score
     */
    private function getRecommendations(array $result): array
    {
        $score = $result['score'] ?? 0;
        $recommendations = [];

        if ($score < 650) {
            $recommendations[] = 'پرداخت به موقع قسط‌ها و تعهدات';
            $recommendations[] = 'کاهش میزان استفاده از کارت‌های اعتباری';
            $recommendations[] = 'تسویه بدهی‌های معوق';
        }

        if (($result['creditUtilizationRatio'] ?? 0) > 30) {
            $recommendations[] = 'کاهش میزان استفاده از اعتبار موجود';
        }

        if (($result['latePayments'] ?? 0) > 0) {
            $recommendations[] = 'ایجاد یادآوری برای پرداخت‌های به موقع';
        }

        if (empty($recommendations)) {
            $recommendations[] = 'وضعیت اعتباری شما مناسب است، آن را حفظ کنید';
        }

        return $recommendations;
    }
} 