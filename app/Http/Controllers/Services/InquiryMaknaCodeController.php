<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Services\BaseFinnotechController;
use App\Services\Finnotech\FinnotechService;
use App\Services\Finnotech\SmsAuthorizationService;

class InquiryMaknaCodeController extends BaseFinnotechController
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
        $this->apiEndpoint = 'inquiry-makna-code';
        $this->scope = 'banking:makna:get';
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
    }

    /**
     * Prepare API parameters from service data
     */
    protected function prepareApiParameters(array $serviceData): array
    {
        return [
            'nationalCode' => $serviceData['national_code'] ?? '',
        ];
    }

    /**
     * Format response data for display
     */
    protected function formatResponseData(array $responseData): array
    {
        if (!isset($responseData['result'])) {
            return ['status' => 'failed', 'message' => 'خطا در دریافت کد مکنا'];
        }

        $result = $responseData['result'];
        
        return [
            'status' => 'success',
            'data' => [
                'national_code' => request('national_code'),
                'makna_info' => $this->getMaknaInfo($result),
                'credit_status' => $this->getCreditStatus($result),
                'financial_summary' => $this->getFinancialSummary($result),
                'risk_assessment' => $this->getRiskAssessment($result),
                'recommendations' => $this->getRecommendations($result),
                'usage_info' => $this->getUsageInfo($result),
            ]
        ];
    }

    /**
     * Get MAKNA information
     */
    private function getMaknaInfo(array $result): array
    {
        $maknaInfo = $result['maknaInfo'] ?? [];
        
        return [
            'makna_code' => $maknaInfo['maknaCode'] ?? '',
            'status' => $maknaInfo['status'] ?? '',
            'status_persian' => $this->translateMaknaStatus($maknaInfo['status'] ?? ''),
            'issue_date' => $maknaInfo['issueDate'] ?? '',
            'last_update' => $maknaInfo['lastUpdate'] ?? '',
            'validity_period' => $maknaInfo['validityPeriod'] ?? '',
            'issuing_authority' => $maknaInfo['issuingAuthority'] ?? 'مرکز تشخیص و گزارش تقلبات بانکی',
            'reference_number' => $maknaInfo['referenceNumber'] ?? '',
            'is_active' => $maknaInfo['isActive'] ?? false,
        ];
    }

    /**
     * Get credit status information
     */
    private function getCreditStatus(array $result): array
    {
        $creditStatus = $result['creditStatus'] ?? [];
        
        return [
            'credit_rating' => $creditStatus['creditRating'] ?? '',
            'credit_rating_persian' => $this->translateCreditRating($creditStatus['creditRating'] ?? ''),
            'score' => $creditStatus['score'] ?? 0,
            'max_score' => $creditStatus['maxScore'] ?? 850,
            'score_category' => $this->getScoreCategory($creditStatus['score'] ?? 0),
            'last_assessment_date' => $creditStatus['lastAssessmentDate'] ?? '',
            'factors_affecting_score' => $creditStatus['factorsAffectingScore'] ?? [],
            'positive_factors' => $creditStatus['positiveFactors'] ?? [],
            'negative_factors' => $creditStatus['negativeFactors'] ?? [],
        ];
    }

    /**
     * Get financial summary
     */
    private function getFinancialSummary(array $result): array
    {
        $financial = $result['financialSummary'] ?? [];
        
        return [
            'total_debt' => $financial['totalDebt'] ?? 0,
            'formatted_total_debt' => number_format($financial['totalDebt'] ?? 0) . ' ریال',
            'active_loans' => $financial['activeLoans'] ?? 0,
            'overdue_payments' => $financial['overduePayments'] ?? 0,
            'formatted_overdue_payments' => number_format($financial['overduePayments'] ?? 0) . ' ریال',
            'bounced_checks' => $financial['bouncedChecks'] ?? 0,
            'credit_utilization' => $financial['creditUtilization'] ?? 0,
            'payment_history_score' => $financial['paymentHistoryScore'] ?? 0,
            'account_age_months' => $financial['accountAgeMonths'] ?? 0,
            'banking_relationship_years' => round(($financial['accountAgeMonths'] ?? 0) / 12, 1),
        ];
    }

    /**
     * Get risk assessment
     */
    private function getRiskAssessment(array $result): array
    {
        $risk = $result['riskAssessment'] ?? [];
        
        return [
            'risk_level' => $risk['riskLevel'] ?? '',
            'risk_level_persian' => $this->translateRiskLevel($risk['riskLevel'] ?? ''),
            'probability_of_default' => $risk['probabilityOfDefault'] ?? 0,
            'recommended_credit_limit' => $risk['recommendedCreditLimit'] ?? 0,
            'formatted_recommended_credit_limit' => number_format($risk['recommendedCreditLimit'] ?? 0) . ' ریال',
            'risk_factors' => $risk['riskFactors'] ?? [],
            'mitigation_suggestions' => $risk['mitigationSuggestions'] ?? [],
            'monitoring_frequency' => $risk['monitoringFrequency'] ?? '',
        ];
    }

    /**
     * Get usage information
     */
    private function getUsageInfo(array $result): array
    {
        return [
            'can_use_for_banking' => true,
            'can_use_for_credit' => ($result['creditStatus']['score'] ?? 0) > 300,
            'can_use_for_loans' => ($result['riskAssessment']['riskLevel'] ?? '') !== 'HIGH',
            'validity_period' => '6 ماه',
            'renewal_required' => $this->isRenewalRequired($result),
            'next_review_date' => $this->getNextReviewDate($result),
            'usage_restrictions' => $this->getUsageRestrictions($result),
        ];
    }

    /**
     * Translate MAKNA status
     */
    private function translateMaknaStatus(string $status): string
    {
        $statuses = [
            'ACTIVE' => 'فعال',
            'SUSPENDED' => 'تعلیق',
            'EXPIRED' => 'منقضی',
            'UNDER_REVIEW' => 'در حال بررسی',
            'RESTRICTED' => 'محدود',
        ];

        return $statuses[$status] ?? $status;
    }

    /**
     * Translate credit rating
     */
    private function translateCreditRating(string $rating): string
    {
        $ratings = [
            'EXCELLENT' => 'عالی',
            'GOOD' => 'خوب',
            'FAIR' => 'متوسط',
            'POOR' => 'ضعیف',
            'VERY_POOR' => 'بسیار ضعیف',
        ];

        return $ratings[$rating] ?? $rating;
    }

    /**
     * Get score category
     */
    private function getScoreCategory(int $score): array
    {
        if ($score >= 750) {
            return ['category' => 'excellent', 'text' => 'عالی', 'color' => 'green'];
        } elseif ($score >= 650) {
            return ['category' => 'good', 'text' => 'خوب', 'color' => 'lightgreen'];
        } elseif ($score >= 550) {
            return ['category' => 'fair', 'text' => 'متوسط', 'color' => 'yellow'];
        } elseif ($score >= 400) {
            return ['category' => 'poor', 'text' => 'ضعیف', 'color' => 'orange'];
        } else {
            return ['category' => 'very_poor', 'text' => 'بسیار ضعیف', 'color' => 'red'];
        }
    }

    /**
     * Translate risk level
     */
    private function translateRiskLevel(string $level): string
    {
        $levels = [
            'LOW' => 'پایین',
            'MEDIUM' => 'متوسط',
            'HIGH' => 'بالا',
            'CRITICAL' => 'بحرانی',
        ];

        return $levels[$level] ?? $level;
    }

    /**
     * Check if renewal is required
     */
    private function isRenewalRequired(array $result): bool
    {
        $issueDate = $result['maknaInfo']['issueDate'] ?? '';
        if (empty($issueDate)) return false;
        
        $sixMonthsAgo = strtotime('-6 months');
        return strtotime($issueDate) < $sixMonthsAgo;
    }

    /**
     * Get next review date
     */
    private function getNextReviewDate(array $result): string
    {
        $lastUpdate = $result['maknaInfo']['lastUpdate'] ?? '';
        if (empty($lastUpdate)) return '';
        
        return date('Y-m-d', strtotime($lastUpdate . ' +3 months'));
    }

    /**
     * Get usage restrictions
     */
    private function getUsageRestrictions(array $result): array
    {
        $restrictions = [];
        $creditScore = $result['creditStatus']['score'] ?? 0;
        $riskLevel = $result['riskAssessment']['riskLevel'] ?? '';
        $status = $result['maknaInfo']['status'] ?? '';

        if ($status === 'SUSPENDED') {
            $restrictions[] = 'کد مکنا تعلیق شده - امکان استفاده محدود';
        }

        if ($creditScore < 400) {
            $restrictions[] = 'امتیاز اعتباری پایین - محدودیت در دریافت تسهیلات';
        }

        if ($riskLevel === 'HIGH') {
            $restrictions[] = 'سطح ریسک بالا - نیاز به بررسی بیشتر';
        }

        $overduePayments = $result['financialSummary']['overduePayments'] ?? 0;
        if ($overduePayments > 0) {
            $restrictions[] = 'پرداخت‌های معوق - محدودیت در خدمات جدید';
        }

        return $restrictions;
    }

    /**
     * Get recommendations
     */
    private function getRecommendations(array $result): array
    {
        $recommendations = [];
        $creditScore = $result['creditStatus']['score'] ?? 0;
        $riskLevel = $result['riskAssessment']['riskLevel'] ?? '';
        $overduePayments = $result['financialSummary']['overduePayments'] ?? 0;

        if ($creditScore < 600) {
            $recommendations[] = 'برای بهبود امتیاز اعتباری اقدام کنید';
            $recommendations[] = 'پرداخت‌های خود را به موقع انجام دهید';
        }

        if ($overduePayments > 0) {
            $recommendations[] = 'فوراً پرداخت‌های معوق را تسویه کنید';
        }

        if ($riskLevel === 'HIGH') {
            $recommendations[] = 'با مشاور مالی برای کاهش ریسک مشورت کنید';
            $recommendations[] = 'از اخذ تسهیلات جدید خودداری کنید';
        }

        if ($this->isRenewalRequired($result)) {
            $recommendations[] = 'کد مکنا نیاز به تجدید دارد';
        }

        if (empty($recommendations)) {
            $recommendations[] = 'وضعیت مالی شما مناسب است';
            $recommendations[] = 'به حفظ رکورد مثبت پرداخت ادامه دهید';
        }

        return $recommendations;
    }
} 