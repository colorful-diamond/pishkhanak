<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Services\BaseFinnotechController;
use App\Services\Finnotech\FinnotechService;
use App\Services\Finnotech\SmsAuthorizationService;

class CarInformationAndInsuranceDiscountsController extends BaseFinnotechController
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
        $this->apiEndpoint = 'car-information-and-insurance-discounts';
        $this->scope = 'vehicle:car-info-insurance-discounts:get';
        $this->requiresSms = false; // Vehicle services don't require SMS
        $this->httpMethod = 'GET';
        
        $this->requiredFields = ['plate_number'];
        
        $this->validationRules = [
            'plate_number' => 'required|string|min:7|max:10',
        ];
        
        $this->validationMessages = [
            'plate_number.required' => 'شماره پلاک الزامی است',
            'plate_number.min' => 'شماره پلاک باید حداقل 7 کاراکتر باشد',
            'plate_number.max' => 'شماره پلاک نباید بیش از 10 کاراکتر باشد',
        ];
    }

    /**
     * Prepare API parameters from service data
     */
    protected function prepareApiParameters(array $serviceData): array
    {
        return [
            'plateNumber' => $serviceData['plate_number'] ?? '',
        ];
    }

    /**
     * Format response data for display
     */
    protected function formatResponseData(array $responseData): array
    {
        if (!isset($responseData['result'])) {
            return ['status' => 'failed', 'message' => 'خطا در دریافت اطلاعات خودرو و تخفیفات بیمه'];
        }

        $result = $responseData['result'];
        
        return [
            'status' => 'success',
            'data' => [
                'plate_number' => request('plate_number'),
                'formatted_plate' => $this->formatPlateNumber(request('plate_number')),
                'vehicle_info' => [
                    'vehicle_type' => $result['vehicleType'] ?? '',
                    'vehicle_model' => $result['vehicleModel'] ?? '',
                    'vehicle_tip' => $result['vehicleTip'] ?? '',
                    'production_year' => $result['productionYear'] ?? '',
                    'engine_capacity' => $result['engineCapacity'] ?? '',
                    'fuel_type' => $result['fuelType'] ?? '',
                    'color' => $result['color'] ?? '',
                    'chassis_number' => $result['chassisNumber'] ?? '',
                    'engine_number' => $result['engineNumber'] ?? '',
                ],
                'insurance_info' => [
                    'is_active' => $result['insuranceStatus'] === 'active',
                    'insurance_company' => $result['insuranceCompany'] ?? '',
                    'policy_number' => $result['policyNumber'] ?? '',
                    'start_date' => $result['insuranceStartDate'] ?? '',
                    'end_date' => $result['insuranceEndDate'] ?? '',
                    'coverage_type' => $result['coverageType'] ?? '',
                    'premium_amount' => $result['premiumAmount'] ?? 0,
                    'status' => $result['insuranceStatus'] ?? '',
                    'status_description' => $this->getInsuranceStatusDescription($result['insuranceStatus'] ?? ''),
                ],
                'discounts' => [
                    'no_claim_discount' => [
                        'percentage' => $result['noClaimDiscountPercentage'] ?? 0,
                        'years' => $result['noClaimYears'] ?? 0,
                        'amount' => $result['noClaimDiscountAmount'] ?? 0,
                        'description' => $this->getNoClaimDescription($result['noClaimYears'] ?? 0),
                    ],
                    'loyalty_discount' => [
                        'percentage' => $result['loyaltyDiscountPercentage'] ?? 0,
                        'years_with_company' => $result['yearsWithCompany'] ?? 0,
                        'amount' => $result['loyaltyDiscountAmount'] ?? 0,
                    ],
                    'safety_features_discount' => [
                        'percentage' => $result['safetyFeaturesDiscountPercentage'] ?? 0,
                        'features' => $result['safetyFeatures'] ?? [],
                        'amount' => $result['safetyFeaturesDiscountAmount'] ?? 0,
                    ],
                    'young_driver_discount' => [
                        'percentage' => $result['youngDriverDiscountPercentage'] ?? 0,
                        'eligible' => $result['isYoungDriverEligible'] ?? false,
                        'amount' => $result['youngDriverDiscountAmount'] ?? 0,
                    ],
                    'total_discount' => [
                        'percentage' => $result['totalDiscountPercentage'] ?? 0,
                        'amount' => $result['totalDiscountAmount'] ?? 0,
                    ],
                ],
                'claims_history' => [
                    'total_claims' => $result['totalClaims'] ?? 0,
                    'recent_claims' => $result['recentClaims'] ?? [],
                    'claim_free_years' => $result['claimFreeYears'] ?? 0,
                    'last_claim_date' => $result['lastClaimDate'] ?? '',
                    'claim_impact_on_premium' => $result['claimImpactOnPremium'] ?? 0,
                ],
                'premium_calculation' => [
                    'base_premium' => $result['basePremium'] ?? 0,
                    'total_discounts' => $result['totalDiscountAmount'] ?? 0,
                    'total_surcharges' => $result['totalSurcharges'] ?? 0,
                    'final_premium' => $result['finalPremium'] ?? 0,
                    'next_year_estimate' => $result['nextYearPremiumEstimate'] ?? 0,
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
     * Format plate number for display
     */
    private function formatPlateNumber(string $plateNumber): string
    {
        // Remove any non-alphanumeric characters
        $clean = preg_replace('/[^A-Za-z0-9]/', '', $plateNumber);
        
        // Format based on Iranian plate patterns
        if (strlen($clean) === 8) {
            // New format: XX123Y45
            return substr($clean, 0, 2) . '-' . substr($clean, 2, 3) . '-' . substr($clean, 5, 1) . '-' . substr($clean, 6, 2);
        } elseif (strlen($clean) === 7) {
            // Old format: 12Y345
            return substr($clean, 0, 2) . '-' . substr($clean, 2, 1) . '-' . substr($clean, 3, 3);
        }
        
        return $plateNumber;
    }

    /**
     * Get insurance status description in Persian
     */
    private function getInsuranceStatusDescription(string $status): string
    {
        $statusDescriptions = [
            'active' => 'فعال - بیمه معتبر',
            'expired' => 'منقضی - نیاز به تمدید',
            'pending' => 'در انتظار تایید',
            'cancelled' => 'لغو شده',
            'suspended' => 'تعلیق شده',
        ];

        return $statusDescriptions[$status] ?? $status;
    }

    /**
     * Get no-claim discount description
     */
    private function getNoClaimDescription(int $years): string
    {
        if ($years === 0) {
            return 'عدم سابقه عدم خسارت';
        } elseif ($years === 1) {
            return '1 سال بدون خسارت - تخفیف مقدماتی';
        } elseif ($years <= 3) {
            return "{$years} سال بدون خسارت - تخفیف متوسط";
        } elseif ($years <= 5) {
            return "{$years} سال بدون خسارت - تخفیف خوب";
        } else {
            return "{$years} سال بدون خسارت - حداکثر تخفیف";
        }
    }

    /**
     * Calculate insurance score based on various factors
     */
    private function calculateInsuranceScore(array $data): int
    {
        $score = 50; // Base score
        
        // No-claim bonus
        $claimFreeYears = $data['claims_history']['claim_free_years'] ?? 0;
        $score += min($claimFreeYears * 5, 25); // Max 25 points for claim-free years
        
        // Vehicle age penalty
        $productionYear = $data['vehicle_info']['production_year'] ?? date('Y');
        $vehicleAge = date('Y') - $productionYear;
        if ($vehicleAge > 10) {
            $score -= min(($vehicleAge - 10) * 2, 20); // Max 20 points penalty
        }
        
        // Safety features bonus
        $safetyFeatures = $data['discounts']['safety_features_discount']['features'] ?? [];
        $score += min(count($safetyFeatures) * 3, 15); // Max 15 points for safety features
        
        // Loyalty bonus
        $yearsWithCompany = $data['discounts']['loyalty_discount']['years_with_company'] ?? 0;
        $score += min($yearsWithCompany * 2, 10); // Max 10 points for loyalty
        
        return max(min($score, 100), 0); // Keep score between 0-100
    }

    /**
     * Get recommendations based on vehicle and insurance data
     */
    private function getRecommendations(array $data): array
    {
        $recommendations = [];
        
        $insuranceInfo = $data['insurance_info'];
        $discounts = $data['discounts'];
        $claimsHistory = $data['claims_history'];
        
        // Insurance status recommendations
        if (!$insuranceInfo['is_active']) {
            $recommendations[] = 'بیمه خودروی شما منقضی شده است. برای تمدید بیمه اقدام کنید.';
        } else {
            $endDate = $insuranceInfo['end_date'];
            if ($endDate) {
                try {
                    $endDateCarbon = \Carbon\Carbon::createFromFormat('Y/m/d', $endDate);
                    $daysToExpiry = $endDateCarbon->diffInDays(now(), false);
                    
                    if ($daysToExpiry <= 30 && $daysToExpiry >= 0) {
                        $recommendations[] = "بیمه خودروی شما {$daysToExpiry} روز دیگر منقضی می‌شود. برای تمدید اقدام کنید.";
                    }
                } catch (\Exception $e) {
                    // Ignore date parsing errors
                }
            }
        }
        
        // Discount recommendations
        $noClaimYears = $discounts['no_claim_discount']['years'] ?? 0;
        if ($noClaimYears < 5) {
            $recommendations[] = 'با ادامه رانندگی بدون خسارت، از تخفیفات بیشتری برخوردار خواهید شد.';
        }
        
        if (empty($discounts['safety_features_discount']['features'])) {
            $recommendations[] = 'نصب تجهیزات ایمنی مانند دوربین، GPS و سیستم‌های امنیتی می‌تواند تخفیف بیمه شما را افزایش دهد.';
        }
        
        // Claims history recommendations
        $totalClaims = $claimsHistory['total_claims'] ?? 0;
        if ($totalClaims > 2) {
            $recommendations[] = 'تعداد خسارات شما بالا است. رانندگی محتاطانه‌تر می‌تواند به کاهش هزینه بیمه کمک کند.';
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
            // Add analysis
            $basicFormatted['data']['analysis'] = [
                'insurance_score' => $this->calculateInsuranceScore($basicFormatted['data']),
                'discount_potential' => $this->calculateDiscountPotential($basicFormatted['data']),
                'recommendations' => $this->getRecommendations($basicFormatted['data']),
                'summary' => $this->getAnalysisSummary($basicFormatted['data']),
            ];
        }
        
        return $basicFormatted;
    }

    /**
     * Calculate discount potential
     */
    private function calculateDiscountPotential(array $data): array
    {
        $currentDiscount = $data['discounts']['total_discount']['percentage'] ?? 0;
        $maxPossibleDiscount = 50; // Typical maximum discount
        
        $potential = $maxPossibleDiscount - $currentDiscount;
        
        return [
            'current_discount' => $currentDiscount,
            'potential_additional' => max($potential, 0),
            'max_possible' => $maxPossibleDiscount,
        ];
    }

    /**
     * Get analysis summary
     */
    private function getAnalysisSummary(array $data): string
    {
        $score = $this->calculateInsuranceScore($data);
        $totalDiscount = $data['discounts']['total_discount']['percentage'] ?? 0;
        $isActive = $data['insurance_info']['is_active'];
        
        if (!$isActive) {
            return 'بیمه منقضی - نیاز به تمدید فوری';
        }
        
        if ($score >= 80) {
            return "وضعیت عالی - امتیاز {$score} - تخفیف {$totalDiscount}%";
        } elseif ($score >= 60) {
            return "وضعیت خوب - امتیاز {$score} - تخفیف {$totalDiscount}%";
        } elseif ($score >= 40) {
            return "وضعیت متوسط - امتیاز {$score} - تخفیف {$totalDiscount}%";
        } else {
            return "نیاز به بهبود - امتیاز {$score} - تخفیف {$totalDiscount}%";
        }
    }
}