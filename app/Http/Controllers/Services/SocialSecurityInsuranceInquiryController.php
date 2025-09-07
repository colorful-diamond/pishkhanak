<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Services\BaseFinnotechController;
use App\Services\Finnotech\FinnotechService;
use App\Services\Finnotech\SmsAuthorizationService;

class SocialSecurityInsuranceInquiryController extends BaseFinnotechController
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
        $this->apiEndpoint = 'social-security-insurance-inquiry';
        $this->scope = 'kyc:social-security-insurance:get';
        $this->requiresSms = true; // Government services require SMS verification
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
            return ['status' => 'failed', 'message' => 'خطا در دریافت اطلاعات بیمه تامین اجتماعی'];
        }

        $result = $responseData['result'];
        
        return [
            'status' => 'success',
            'data' => [
                'national_code' => request('national_code'),
                'personal_info' => [
                    'full_name' => $result['fullName'] ?? '',
                    'father_name' => $result['fatherName'] ?? '',
                    'birth_date' => $result['birthDate'] ?? '',
                    'birth_place' => $result['birthPlace'] ?? '',
                ],
                'insurance_info' => [
                    'is_insured' => $result['isInsured'] ?? false,
                    'insurance_number' => $result['insuranceNumber'] ?? '',
                    'membership_date' => $result['membershipDate'] ?? '',
                    'last_contribution_date' => $result['lastContributionDate'] ?? '',
                    'total_contribution_months' => $result['totalContributionMonths'] ?? 0,
                    'current_status' => $result['currentStatus'] ?? '',
                    'status_description' => $this->getInsuranceStatusDescription($result['currentStatus'] ?? ''),
                ],
                'contribution_history' => [
                    'total_years' => $this->calculateYearsFromMonths($result['totalContributionMonths'] ?? 0),
                    'total_months' => $result['totalContributionMonths'] ?? 0,
                    'contribution_gaps' => $result['contributionGaps'] ?? [],
                    'last_employer' => $result['lastEmployer'] ?? '',
                    'employment_status' => $result['employmentStatus'] ?? '',
                ],
                'pension_info' => [
                    'is_eligible_for_pension' => $this->isPensionEligible($result),
                    'pension_calculation' => $result['pensionCalculation'] ?? null,
                    'retirement_date' => $result['retirementDate'] ?? '',
                    'pension_amount' => $result['pensionAmount'] ?? 0,
                ],
                'request_info' => [
                    'track_id' => $responseData['trackId'] ?? '',
                    'response_code' => $responseData['responseCode'] ?? '',
                    'status' => $responseData['status'] ?? '',
                    'processed_at' => now()->format('Y/m/d H:i:s'),
                ],
            ],
            'raw_response' => $responseData
        ];
    }

    /**
     * Get insurance status description in Persian
     */
    private function getInsuranceStatusDescription(string $status): string
    {
        $statusDescriptions = [
            'active' => 'فعال - در حال پرداخت حق بیمه',
            'inactive' => 'غیرفعال - عدم پرداخت حق بیمه',
            'retired' => 'بازنشسته - دریافت کننده مستمری',
            'suspended' => 'معلق - تعلیق پرداخت حق بیمه',
            'deceased' => 'فوت شده',
            'transferred' => 'انتقال یافته به صندوق دیگر',
        ];

        return $statusDescriptions[$status] ?? $status;
    }

    /**
     * Calculate years from months
     */
    private function calculateYearsFromMonths(int $months): int
    {
        return intval($months / 12);
    }

    /**
     * Check if eligible for pension
     */
    private function isPensionEligible(array $result): bool
    {
        $totalMonths = $result['totalContributionMonths'] ?? 0;
        $age = $this->calculateAge($result['birthDate'] ?? '');
        
        // Basic eligibility: 30 years (360 months) of contribution or age 60+ with 15 years (180 months)
        if ($totalMonths >= 360) {
            return true;
        }
        
        if ($age >= 60 && $totalMonths >= 180) {
            return true;
        }
        
        return false;
    }

    /**
     * Calculate age from birth date
     */
    private function calculateAge(string $birthDate): int
    {
        if (empty($birthDate)) {
            return 0;
        }

        try {
            $birth = \Carbon\Carbon::createFromFormat('Y/m/d', $birthDate);
            return $birth->diffInYears(now());
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get recommendations based on insurance status
     */
    private function getRecommendations(array $insuranceInfo): array
    {
        $recommendations = [];
        
        if (!($insuranceInfo['is_insured'] ?? false)) {
            $recommendations[] = 'شما در سیستم تامین اجتماعی بیمه نشده‌اید. برای بیمه شدن با کارفرما یا اداره تامین اجتماعی تماس بگیرید.';
        } else {
            $status = $insuranceInfo['current_status'] ?? '';
            $totalMonths = $insuranceInfo['total_contribution_months'] ?? 0;
            
            if ($status === 'inactive') {
                $recommendations[] = 'وضعیت بیمه شما غیرفعال است. برای ادامه پرداخت حق بیمه اقدام کنید.';
            } elseif ($status === 'active') {
                $recommendations[] = 'وضعیت بیمه شما فعال است. ادامه پرداخت حق بیمه را فراموش نکنید.';
            }
            
            if ($totalMonths < 360) {
                $remainingMonths = 360 - $totalMonths;
                $remainingYears = intval($remainingMonths / 12);
                $remainingMonths = $remainingMonths % 12;
                
                $recommendations[] = "برای کسب حق بازنشستگی کامل، {$remainingYears} سال و {$remainingMonths} ماه دیگر باید حق بیمه پرداخت کنید.";
            }
        }
        
        return $recommendations;
    }

    /**
     * Enhanced format response data with additional analysis
     */
    protected function formatResponseDataEnhanced(array $responseData): array
    {
        $basicFormatted = $this->formatResponseData($responseData);
        
        if ($basicFormatted['status'] === 'success') {
            $insuranceInfo = $basicFormatted['data']['insurance_info'];
            
            // Add analysis
            $basicFormatted['data']['analysis'] = [
                'is_pension_eligible' => $this->isPensionEligible($responseData['result'] ?? []),
                'years_of_contribution' => $this->calculateYearsFromMonths($insuranceInfo['total_contribution_months'] ?? 0),
                'recommendations' => $this->getRecommendations($insuranceInfo),
                'status_summary' => $this->getStatusSummary($insuranceInfo),
            ];
        }
        
        return $basicFormatted;
    }

    /**
     * Get status summary
     */
    private function getStatusSummary(array $insuranceInfo): string
    {
        if (!($insuranceInfo['is_insured'] ?? false)) {
            return 'غیر بیمه';
        }
        
        $status = $insuranceInfo['current_status'] ?? '';
        $totalMonths = $insuranceInfo['total_contribution_months'] ?? 0;
        $years = intval($totalMonths / 12);
        $months = $totalMonths % 12;
        
        return "وضعیت: {$insuranceInfo['status_description']} - سابقه: {$years} سال و {$months} ماه";
    }
}