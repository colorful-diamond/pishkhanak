<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Services\BaseFinnotechController;
use App\Services\Finnotech\FinnotechService;
use App\Services\Finnotech\SmsAuthorizationService;

class DriverRiskInquiryController extends BaseFinnotechController
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
        $this->apiEndpoint = 'driver-risk-inquiry';
        $this->scope = 'traffic:driver-risk:get';
        $this->requiresSms = false;
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
            return ['status' => 'failed', 'message' => 'خطا در ارزیابی ریسک راننده'];
        }

        $result = $responseData['result'];
        
        return [
            'status' => 'success',
            'data' => [
                'national_code' => request('national_code'),
                'risk_assessment' => $this->getRiskAssessment($result),
                'driving_history' => $this->getDrivingHistory($result),
                'violation_analysis' => $this->getViolationAnalysis($result),
                'insurance_impact' => $this->getInsuranceImpact($result),
                'recommendations' => $this->getRecommendations($result),
                'risk_factors' => $this->getRiskFactors($result),
                'comparison' => $this->getComparison($result),
            ]
        ];
    }

    /**
     * Get overall risk assessment
     */
    private function getRiskAssessment(array $result): array
    {
        $riskScore = $result['riskScore'] ?? 0; // 0-100 scale
        $riskLevel = $this->calculateRiskLevel($riskScore);
        
        return [
            'risk_score' => $riskScore,
            'risk_level' => $riskLevel,
            'risk_category' => $this->getRiskCategory($riskScore),
            'accident_probability' => $result['accidentProbability'] ?? 0,
            'insurance_group' => $result['insuranceGroup'] ?? 'متوسط',
            'last_updated' => $result['lastUpdated'] ?? date('Y-m-d'),
            'data_confidence' => $result['dataConfidence'] ?? 85,
        ];
    }

    /**
     * Get driving history summary
     */
    private function getDrivingHistory(array $result): array
    {
        $history = $result['drivingHistory'] ?? [];
        
        return [
            'license_age' => $history['licenseAge'] ?? 0,
            'total_violations' => $history['totalViolations'] ?? 0,
            'serious_violations' => $history['seriousViolations'] ?? 0,
            'accidents_count' => $history['accidentsCount'] ?? 0,
            'at_fault_accidents' => $history['atFaultAccidents'] ?? 0,
            'clean_years' => $history['cleanYears'] ?? 0,
            'last_violation_date' => $history['lastViolationDate'] ?? '',
            'license_suspensions' => $history['licenseSuspensions'] ?? 0,
            'dui_history' => $history['duiHistory'] ?? false,
        ];
    }

    /**
     * Get violation analysis
     */
    private function getViolationAnalysis(array $result): array
    {
        $violations = $result['violationBreakdown'] ?? [];
        
        return [
            'speed_violations' => $violations['speed'] ?? 0,
            'traffic_light_violations' => $violations['trafficLight'] ?? 0,
            'parking_violations' => $violations['parking'] ?? 0,
            'document_violations' => $violations['document'] ?? 0,
            'dangerous_driving' => $violations['dangerousDriving'] ?? 0,
            'most_common_violation' => $this->getMostCommonViolation($violations),
            'violation_trend' => $this->getViolationTrend($result['violationTrend'] ?? []),
            'severity_distribution' => [
                'minor' => $violations['minor'] ?? 0,
                'moderate' => $violations['moderate'] ?? 0,
                'severe' => $violations['severe'] ?? 0,
            ],
        ];
    }

    /**
     * Get insurance impact information
     */
    private function getInsuranceImpact(array $result): array
    {
        $impact = $result['insuranceImpact'] ?? [];
        
        return [
            'premium_multiplier' => $impact['premiumMultiplier'] ?? 1.0,
            'premium_increase_percentage' => round(($impact['premiumMultiplier'] ?? 1.0 - 1) * 100, 1),
            'insurability_status' => $impact['insurabilityStatus'] ?? 'قابل بیمه',
            'special_conditions' => $impact['specialConditions'] ?? [],
            'discount_eligibility' => $impact['discountEligibility'] ?? false,
            'high_risk_surcharge' => $impact['highRiskSurcharge'] ?? 0,
            'recommended_coverage' => $impact['recommendedCoverage'] ?? 'استاندارد',
        ];
    }

    /**
     * Calculate risk level
     */
    private function calculateRiskLevel(int $riskScore): array
    {
        if ($riskScore <= 20) {
            return ['level' => 'very_low', 'text' => 'بسیار پایین', 'color' => 'green'];
        } elseif ($riskScore <= 40) {
            return ['level' => 'low', 'text' => 'پایین', 'color' => 'lightgreen'];
        } elseif ($riskScore <= 60) {
            return ['level' => 'medium', 'text' => 'متوسط', 'color' => 'yellow'];
        } elseif ($riskScore <= 80) {
            return ['level' => 'high', 'text' => 'بالا', 'color' => 'orange'];
        } else {
            return ['level' => 'very_high', 'text' => 'بسیار بالا', 'color' => 'red'];
        }
    }

    /**
     * Get risk category
     */
    private function getRiskCategory(int $riskScore): string
    {
        if ($riskScore <= 30) return 'راننده ایمن';
        if ($riskScore <= 50) return 'راننده متوسط';
        if ($riskScore <= 70) return 'راننده پرخطر';
        return 'راننده بسیار پرخطر';
    }

    /**
     * Get most common violation type
     */
    private function getMostCommonViolation(array $violations): array
    {
        if (empty($violations)) {
            return ['type' => 'none', 'text' => 'بدون تخلف', 'count' => 0];
        }

        $typeMap = [
            'speed' => 'تخلفات سرعت',
            'trafficLight' => 'عبور از چراغ قرمز',
            'parking' => 'تخلفات پارک',
            'document' => 'تخلفات مدارک',
            'dangerousDriving' => 'رانندگی خطرناک',
        ];

        $maxType = array_keys($violations, max($violations))[0];
        
        return [
            'type' => $maxType,
            'text' => $typeMap[$maxType] ?? $maxType,
            'count' => $violations[$maxType] ?? 0
        ];
    }

    /**
     * Get violation trend
     */
    private function getViolationTrend(array $trend): array
    {
        if (empty($trend)) {
            return ['direction' => 'stable', 'text' => 'ثابت', 'percentage' => 0];
        }

        $recent = array_slice($trend, -2, 2);
        if (count($recent) < 2) {
            return ['direction' => 'insufficient_data', 'text' => 'داده ناکافی', 'percentage' => 0];
        }

        $change = $recent[1] - $recent[0];
        $percentage = $recent[0] > 0 ? round(($change / $recent[0]) * 100, 1) : 0;

        if ($change > 0) {
            return ['direction' => 'increasing', 'text' => 'افزایشی', 'percentage' => $percentage];
        } elseif ($change < 0) {
            return ['direction' => 'decreasing', 'text' => 'کاهشی', 'percentage' => abs($percentage)];
        } else {
            return ['direction' => 'stable', 'text' => 'ثابت', 'percentage' => 0];
        }
    }

    /**
     * Get risk factors
     */
    private function getRiskFactors(array $result): array
    {
        $factors = [];
        $history = $result['drivingHistory'] ?? [];
        $violations = $result['violationBreakdown'] ?? [];

        if (($history['seriousViolations'] ?? 0) > 0) {
            $factors[] = [
                'factor' => 'serious_violations',
                'text' => 'تخلفات جدی',
                'impact' => 'بالا',
                'color' => 'red'
            ];
        }

        if (($history['accidentsCount'] ?? 0) > 1) {
            $factors[] = [
                'factor' => 'multiple_accidents',
                'text' => 'تصادفات متعدد',
                'impact' => 'بالا',
                'color' => 'red'
            ];
        }

        if (($violations['speed'] ?? 0) > 3) {
            $factors[] = [
                'factor' => 'speed_violations',
                'text' => 'تخلفات سرعت مکرر',
                'impact' => 'متوسط',
                'color' => 'orange'
            ];
        }

        if (($history['cleanYears'] ?? 0) > 3) {
            $factors[] = [
                'factor' => 'clean_record',
                'text' => 'سابقه پاک',
                'impact' => 'مثبت',
                'color' => 'green'
            ];
        }

        return $factors;
    }

    /**
     * Get recommendations
     */
    private function getRecommendations(array $result): array
    {
        $recommendations = [];
        $riskScore = $result['riskScore'] ?? 0;
        $violations = $result['violationBreakdown'] ?? [];

        if ($riskScore > 70) {
            $recommendations[] = 'شرکت در دوره‌های آموزش رانندگی دفاعی';
            $recommendations[] = 'کاهش سرعت و افزایش فاصله ایمنی';
            $recommendations[] = 'اجتناب از رانندگی در شرایط جوی نامناسب';
        } elseif ($riskScore > 40) {
            $recommendations[] = 'مراجعه به مشاوره رانندگی';
            $recommendations[] = 'نصب سیستم‌های ایمنی اضافی در خودرو';
        } else {
            $recommendations[] = 'ادامه رانندگی ایمن فعلی';
            $recommendations[] = 'به‌روزرسانی دانش قوانین راهنمایی و رانندگی';
        }

        if (($violations['speed'] ?? 0) > 2) {
            $recommendations[] = 'استفاده از کروز کنترل برای کنترل سرعت';
        }

        return $recommendations;
    }

    /**
     * Get comparison with average drivers
     */
    private function getComparison(array $result): array
    {
        $comparison = $result['comparison'] ?? [];
        
        return [
            'percentile' => $comparison['percentile'] ?? 50,
            'better_than_percentage' => $comparison['betterThanPercentage'] ?? 50,
            'similar_age_group_average' => $comparison['similarAgeGroupAverage'] ?? 50,
            'national_average' => $comparison['nationalAverage'] ?? 50,
            'ranking' => $this->getRanking($comparison['percentile'] ?? 50),
        ];
    }

    /**
     * Get ranking description
     */
    private function getRanking(int $percentile): string
    {
        if ($percentile >= 90) return 'در 10% برترین رانندگان';
        if ($percentile >= 75) return 'در 25% برترین رانندگان';
        if ($percentile >= 50) return 'بهتر از میانگین';
        if ($percentile >= 25) return 'کمتر از میانگین';
        return 'در 25% پایین‌ترین رانندگان';
    }
} 