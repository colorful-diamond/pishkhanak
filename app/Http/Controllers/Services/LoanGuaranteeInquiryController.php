<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Services\BaseFinnotechController;
use App\Services\Finnotech\FinnotechService;
use App\Services\Finnotech\SmsAuthorizationService;

class LoanGuaranteeInquiryController extends BaseFinnotechController
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
        $this->apiEndpoint = 'loan-guarantee-inquiry';
        $this->scope = 'credit:guarantee:get';
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
            return ['status' => 'failed', 'message' => 'خطا در دریافت اطلاعات ضمانت وام'];
        }

        $result = $responseData['result'];
        
        return [
            'status' => 'success',
            'data' => [
                'national_code' => request('national_code'),
                'active_guarantees' => $this->processActiveGuarantees($result['activeGuarantees'] ?? []),
                'guarantee_history' => $this->processGuaranteeHistory($result['guaranteeHistory'] ?? []),
                'summary' => $this->getGuaranteeSummary($result),
                'risk_assessment' => $this->getRiskAssessment($result),
                'alerts' => $this->getAlerts($result),
                'recommendations' => $this->getRecommendations($result),
            ]
        ];
    }

    /**
     * Process active guarantees
     */
    private function processActiveGuarantees(array $activeGuarantees): array
    {
        $processed = [];
        
        foreach ($activeGuarantees as $guarantee) {
            $processed[] = [
                'guarantee_id' => $guarantee['guaranteeId'] ?? '',
                'loan_number' => $guarantee['loanNumber'] ?? '',
                'borrower_name' => $guarantee['borrowerName'] ?? '',
                'borrower_national_id' => $guarantee['borrowerNationalId'] ?? '',
                'bank_name' => $guarantee['bankName'] ?? '',
                'guarantee_amount' => $guarantee['guaranteeAmount'] ?? 0,
                'formatted_guarantee_amount' => number_format($guarantee['guaranteeAmount'] ?? 0) . ' ریال',
                'loan_amount' => $guarantee['loanAmount'] ?? 0,
                'formatted_loan_amount' => number_format($guarantee['loanAmount'] ?? 0) . ' ریال',
                'guarantee_type' => $guarantee['guaranteeType'] ?? '',
                'guarantee_type_persian' => $this->translateGuaranteeType($guarantee['guaranteeType'] ?? ''),
                'start_date' => $guarantee['startDate'] ?? '',
                'end_date' => $guarantee['endDate'] ?? '',
                'loan_status' => $guarantee['loanStatus'] ?? '',
                'status_persian' => $this->translateLoanStatus($guarantee['loanStatus'] ?? ''),
                'risk_level' => $guarantee['riskLevel'] ?? '',
                'risk_level_persian' => $this->translateRiskLevel($guarantee['riskLevel'] ?? ''),
                'monthly_payment' => $guarantee['monthlyPayment'] ?? 0,
                'formatted_monthly_payment' => number_format($guarantee['monthlyPayment'] ?? 0) . ' ریال',
                'overdue_amount' => $guarantee['overdueAmount'] ?? 0,
                'formatted_overdue_amount' => number_format($guarantee['overdueAmount'] ?? 0) . ' ریال',
                'is_at_risk' => ($guarantee['overdueAmount'] ?? 0) > 0 || $guarantee['riskLevel'] === 'HIGH',
                'remaining_balance' => $guarantee['remainingBalance'] ?? 0,
                'formatted_remaining_balance' => number_format($guarantee['remainingBalance'] ?? 0) . ' ریال',
            ];
        }

        return $processed;
    }

    /**
     * Process guarantee history
     */
    private function processGuaranteeHistory(array $guaranteeHistory): array
    {
        $processed = [];
        
        foreach ($guaranteeHistory as $guarantee) {
            $processed[] = [
                'guarantee_id' => $guarantee['guaranteeId'] ?? '',
                'borrower_name' => $guarantee['borrowerName'] ?? '',
                'bank_name' => $guarantee['bankName'] ?? '',
                'guarantee_amount' => $guarantee['guaranteeAmount'] ?? 0,
                'formatted_guarantee_amount' => number_format($guarantee['guaranteeAmount'] ?? 0) . ' ریال',
                'start_date' => $guarantee['startDate'] ?? '',
                'end_date' => $guarantee['endDate'] ?? '',
                'final_status' => $guarantee['finalStatus'] ?? '',
                'status_persian' => $this->translateFinalStatus($guarantee['finalStatus'] ?? ''),
                'was_claimed' => $guarantee['wasClaimed'] ?? false,
                'claimed_amount' => $guarantee['claimedAmount'] ?? 0,
                'formatted_claimed_amount' => number_format($guarantee['claimedAmount'] ?? 0) . ' ریال',
                'settlement_date' => $guarantee['settlementDate'] ?? '',
            ];
        }

        return array_reverse($processed); // Show newest first
    }

    /**
     * Get guarantee summary
     */
    private function getGuaranteeSummary(array $result): array
    {
        $activeGuarantees = $result['activeGuarantees'] ?? [];
        $totalGuaranteeAmount = array_sum(array_column($activeGuarantees, 'guaranteeAmount'));
        $totalOverdueAmount = array_sum(array_column($activeGuarantees, 'overdueAmount'));
        $highRiskCount = count(array_filter($activeGuarantees, fn($g) => $g['riskLevel'] === 'HIGH'));

        return [
            'total_active_guarantees' => count($activeGuarantees),
            'total_guarantee_amount' => $totalGuaranteeAmount,
            'formatted_total_guarantee_amount' => number_format($totalGuaranteeAmount) . ' ریال',
            'total_overdue_amount' => $totalOverdueAmount,
            'formatted_total_overdue_amount' => number_format($totalOverdueAmount) . ' ریال',
            'high_risk_guarantees' => $highRiskCount,
            'has_risk_exposure' => $totalOverdueAmount > 0 || $highRiskCount > 0,
            'guarantee_utilization' => $result['guaranteeUtilization'] ?? 0,
            'max_guarantee_capacity' => $result['maxGuaranteeCapacity'] ?? 0,
            'formatted_max_capacity' => number_format($result['maxGuaranteeCapacity'] ?? 0) . ' ریال',
        ];
    }

    /**
     * Get risk assessment
     */
    private function getRiskAssessment(array $result): array
    {
        $riskAssessment = $result['riskAssessment'] ?? [];
        
        return [
            'overall_risk_score' => $riskAssessment['overallRiskScore'] ?? 0,
            'risk_category' => $riskAssessment['riskCategory'] ?? '',
            'risk_category_persian' => $this->translateRiskCategory($riskAssessment['riskCategory'] ?? ''),
            'potential_loss' => $riskAssessment['potentialLoss'] ?? 0,
            'formatted_potential_loss' => number_format($riskAssessment['potentialLoss'] ?? 0) . ' ریال',
            'probability_of_default' => $riskAssessment['probabilityOfDefault'] ?? 0,
            'credit_exposure' => $riskAssessment['creditExposure'] ?? 0,
            'formatted_credit_exposure' => number_format($riskAssessment['creditExposure'] ?? 0) . ' ریال',
            'diversification_score' => $riskAssessment['diversificationScore'] ?? 0,
            'recommendations' => $riskAssessment['recommendations'] ?? [],
        ];
    }

    /**
     * Translate guarantee type
     */
    private function translateGuaranteeType(string $type): string
    {
        $types = [
            'PERSONAL' => 'ضمانت شخصی',
            'COLLATERAL' => 'ضمانت عینی',
            'BANK' => 'ضمانت بانکی',
            'INSURANCE' => 'ضمانت بیمه‌ای',
            'MIXED' => 'ضمانت ترکیبی',
        ];

        return $types[$type] ?? $type;
    }

    /**
     * Translate loan status
     */
    private function translateLoanStatus(string $status): string
    {
        $statuses = [
            'CURRENT' => 'جاری',
            'LATE' => 'دیرکرد',
            'OVERDUE' => 'معوق',
            'DEFAULT' => 'نکول',
            'CLOSED' => 'بسته شده',
        ];

        return $statuses[$status] ?? $status;
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
     * Translate final status
     */
    private function translateFinalStatus(string $status): string
    {
        $statuses = [
            'COMPLETED' => 'تکمیل شده',
            'CLAIMED' => 'مطالبه شده',
            'SETTLED' => 'تسویه شده',
            'DEFAULTED' => 'نکول',
            'CANCELLED' => 'لغو شده',
        ];

        return $statuses[$status] ?? $status;
    }

    /**
     * Translate risk category
     */
    private function translateRiskCategory(string $category): string
    {
        $categories = [
            'MINIMAL' => 'حداقل',
            'LOW' => 'پایین',
            'MODERATE' => 'متوسط',
            'HIGH' => 'بالا',
            'EXTREME' => 'فوق‌العاده',
        ];

        return $categories[$category] ?? $category;
    }

    /**
     * Get alerts and warnings
     */
    private function getAlerts(array $result): array
    {
        $alerts = [];
        $activeGuarantees = $result['activeGuarantees'] ?? [];
        $riskAssessment = $result['riskAssessment'] ?? [];

        // Check for overdue guarantees
        $overdueGuarantees = array_filter($activeGuarantees, fn($g) => ($g['overdueAmount'] ?? 0) > 0);
        if (!empty($overdueGuarantees)) {
            $totalOverdue = array_sum(array_column($overdueGuarantees, 'overdueAmount'));
            $alerts[] = [
                'type' => 'danger',
                'title' => 'ضمانت‌های معوق',
                'message' => count($overdueGuarantees) . ' ضمانت با مجموع ' . number_format($totalOverdue) . ' ریال معوق دارید'
            ];
        }

        // Check for high-risk guarantees
        $highRiskGuarantees = array_filter($activeGuarantees, fn($g) => $g['riskLevel'] === 'HIGH');
        if (!empty($highRiskGuarantees)) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'ضمانت‌های پرخطر',
                'message' => count($highRiskGuarantees) . ' ضمانت در وضعیت پرخطر قرار دارد'
            ];
        }

        // Check overall risk
        if (($riskAssessment['overallRiskScore'] ?? 0) > 70) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'ریسک بالا',
                'message' => 'امتیاز ریسک کلی شما بالا است'
            ];
        }

        return $alerts;
    }

    /**
     * Get recommendations
     */
    private function getRecommendations(array $result): array
    {
        $recommendations = [];
        $activeGuarantees = $result['activeGuarantees'] ?? [];
        $riskAssessment = $result['riskAssessment'] ?? [];

        $totalOverdue = array_sum(array_column($activeGuarantees, 'overdueAmount'));
        if ($totalOverdue > 0) {
            $recommendations[] = 'فوراً با متقاضیان وام‌های معوق تماس بگیرید';
            $recommendations[] = 'برای کاهش ریسک اقدامات قانونی را بررسی کنید';
        }

        $highRiskCount = count(array_filter($activeGuarantees, fn($g) => $g['riskLevel'] === 'HIGH'));
        if ($highRiskCount > 0) {
            $recommendations[] = 'ضمانت‌های پرخطر را نظارت بیشتری کنید';
            $recommendations[] = 'درخواست ضمانت اضافی یا تسویه زودهنگام را بررسی کنید';
        }

        if (($riskAssessment['diversificationScore'] ?? 0) < 50) {
            $recommendations[] = 'برای کاهش ریسک ضمانت‌ها را متنوع‌سازی کنید';
        }

        if (empty($recommendations)) {
            $recommendations[] = 'وضعیت ضمانت‌های شما مناسب است';
            $recommendations[] = 'به نظارت منظم بر وضعیت وام‌ها ادامه دهید';
        }

        return $recommendations;
    }
} 