<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Services\BaseFinnotechController;
use App\Services\Finnotech\FinnotechService;
use App\Services\Finnotech\SmsAuthorizationService;

class ExpatsInquiriesController extends BaseFinnotechController
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
        $this->apiEndpoint = 'expats-inquiries';
        $this->scope = 'government:expats:get';
        $this->requiresSms = true; // Government services require SMS verification
        $this->httpMethod = 'GET';
        
        $this->requiredFields = ['mobile', 'national_code'];
        
        $this->validationRules = [
            'mobile' => 'required|string|regex:/^09[0-9]{9}$/',
            'national_code' => 'required|string|digits:10',
            'passport_number' => 'nullable|string|min:8|max:15',
        ];
        
        $this->validationMessages = [
            'mobile.required' => 'شماره موبایل الزامی است',
            'mobile.regex' => 'شماره موبایل نامعتبر است',
            'national_code.required' => 'کد ملی الزامی است',
            'national_code.digits' => 'کد ملی باید 10 رقم باشد',
            'passport_number.min' => 'شماره گذرنامه باید حداقل 8 کاراکتر باشد',
        ];
    }

    /**
     * Prepare API parameters from service data
     */
    protected function prepareApiParameters(array $serviceData): array
    {
        $params = [
            'mobile' => $serviceData['mobile'] ?? '',
            'nationalCode' => $serviceData['national_code'] ?? '',
        ];

        // Add optional passport number if provided
        if (!empty($serviceData['passport_number'])) {
            $params['passportNumber'] = $serviceData['passport_number'];
        }

        return $params;
    }

    /**
     * Format response data for display
     */
    protected function formatResponseData(array $responseData): array
    {
        if (!isset($responseData['result'])) {
            return ['status' => 'failed', 'message' => 'خطا در دریافت اطلاعات اتباع خارجی'];
        }

        $result = $responseData['result'];
        
        return [
            'status' => 'success',
            'data' => [
                'national_code' => request('national_code'),
                'personal_info' => $this->getPersonalInfo($result),
                'residency_info' => $this->getResidencyInfo($result),
                'travel_history' => $this->getTravelHistory($result),
                'legal_status' => $this->getLegalStatus($result),
                'services_eligibility' => $this->getServicesEligibility($result),
                'alerts' => $this->getAlerts($result),
                'recommendations' => $this->getRecommendations($result),
            ]
        ];
    }

    /**
     * Get personal information
     */
    private function getPersonalInfo(array $result): array
    {
        $personal = $result['personalInfo'] ?? [];
        
        return [
            'full_name' => $personal['fullName'] ?? '',
            'father_name' => $personal['fatherName'] ?? '',
            'birth_date' => $personal['birthDate'] ?? '',
            'birth_place' => $personal['birthPlace'] ?? '',
            'nationality' => $personal['nationality'] ?? '',
            'nationality_persian' => $this->translateNationality($personal['nationality'] ?? ''),
            'gender' => $personal['gender'] ?? '',
            'gender_persian' => $personal['gender'] === 'M' ? 'مرد' : 'زن',
            'passport_number' => $personal['passportNumber'] ?? '',
            'passport_issue_date' => $personal['passportIssueDate'] ?? '',
            'passport_expiry_date' => $personal['passportExpiryDate'] ?? '',
            'is_passport_valid' => $this->isPassportValid($personal['passportExpiryDate'] ?? ''),
        ];
    }

    /**
     * Get residency information
     */
    private function getResidencyInfo(array $result): array
    {
        $residency = $result['residencyInfo'] ?? [];
        
        return [
            'residence_permit_number' => $residency['permitNumber'] ?? '',
            'permit_type' => $residency['permitType'] ?? '',
            'permit_type_persian' => $this->translatePermitType($residency['permitType'] ?? ''),
            'issue_date' => $residency['issueDate'] ?? '',
            'expiry_date' => $residency['expiryDate'] ?? '',
            'is_valid' => $this->isPermitValid($residency['expiryDate'] ?? ''),
            'days_to_expiry' => $this->getDaysToExpiry($residency['expiryDate'] ?? ''),
            'issuing_office' => $residency['issuingOffice'] ?? '',
            'current_address' => $residency['currentAddress'] ?? '',
            'sponsor_info' => [
                'sponsor_name' => $residency['sponsorName'] ?? '',
                'sponsor_type' => $residency['sponsorType'] ?? '',
                'sponsor_id' => $residency['sponsorId'] ?? '',
            ],
            'work_permit' => [
                'has_work_permit' => $residency['hasWorkPermit'] ?? false,
                'employer_name' => $residency['employerName'] ?? '',
                'job_title' => $residency['jobTitle'] ?? '',
                'work_permit_expiry' => $residency['workPermitExpiry'] ?? '',
            ],
        ];
    }

    /**
     * Get travel history
     */
    private function getTravelHistory(array $result): array
    {
        $travels = $result['travelHistory'] ?? [];
        $processedTravels = [];
        
        foreach ($travels as $travel) {
            $processedTravels[] = [
                'entry_date' => $travel['entryDate'] ?? '',
                'exit_date' => $travel['exitDate'] ?? null,
                'border_point' => $travel['borderPoint'] ?? '',
                'travel_type' => $travel['travelType'] ?? '',
                'travel_type_persian' => $this->translateTravelType($travel['travelType'] ?? ''),
                'destination_country' => $travel['destinationCountry'] ?? '',
                'purpose' => $travel['purpose'] ?? '',
                'purpose_persian' => $this->translateTravelPurpose($travel['purpose'] ?? ''),
                'is_current_stay' => empty($travel['exitDate']),
                'stay_duration' => $this->calculateStayDuration($travel),
            ];
        }

        return [
            'total_entries' => count($travels),
            'current_stay_duration' => $this->getCurrentStayDuration($travels),
            'travel_records' => array_reverse($processedTravels), // Show newest first
            'frequent_destinations' => $this->getFrequentDestinations($travels),
        ];
    }

    /**
     * Get legal status
     */
    private function getLegalStatus(array $result): array
    {
        $legal = $result['legalStatus'] ?? [];
        
        return [
            'status' => $legal['status'] ?? '',
            'status_persian' => $this->translateLegalStatus($legal['status'] ?? ''),
            'is_legal' => $legal['isLegal'] ?? false,
            'has_violations' => $legal['hasViolations'] ?? false,
            'violations' => $legal['violations'] ?? [],
            'restrictions' => $legal['restrictions'] ?? [],
            'legal_notes' => $legal['legalNotes'] ?? '',
            'last_status_update' => $legal['lastStatusUpdate'] ?? '',
        ];
    }

    /**
     * Get services eligibility
     */
    private function getServicesEligibility(array $result): array
    {
        $residency = $result['residencyInfo'] ?? [];
        $legal = $result['legalStatus'] ?? [];
        
        return [
            'can_open_bank_account' => ($legal['isLegal'] ?? false) && $this->isPermitValid($residency['expiryDate'] ?? ''),
            'can_get_driver_license' => ($legal['isLegal'] ?? false) && ($residency['permitType'] ?? '') !== 'TOURIST',
            'can_register_business' => ($residency['permitType'] ?? '') === 'BUSINESS' && ($legal['isLegal'] ?? false),
            'can_buy_property' => in_array($residency['permitType'] ?? '', ['PERMANENT', 'INVESTMENT']),
            'can_work' => ($residency['hasWorkPermit'] ?? false) && ($legal['isLegal'] ?? false),
            'can_study' => ($residency['permitType'] ?? '') === 'STUDENT' || ($residency['permitType'] ?? '') === 'PERMANENT',
            'eligible_for_renewal' => $this->isEligibleForRenewal($result),
            'required_documents' => $this->getRequiredDocuments($residency['permitType'] ?? ''),
        ];
    }

    /**
     * Translate nationality
     */
    private function translateNationality(string $nationality): string
    {
        $nationalities = [
            'AFG' => 'افغانستانی',
            'IRQ' => 'عراقی',
            'PAK' => 'پاکستانی',
            'TUR' => 'ترکیه‌ای',
            'AZE' => 'آذربایجانی',
            'CHN' => 'چینی',
            'IND' => 'هندی',
            'SYR' => 'سوری',
        ];

        return $nationalities[$nationality] ?? $nationality;
    }

    /**
     * Translate permit type
     */
    private function translatePermitType(string $type): string
    {
        $types = [
            'TOURIST' => 'گردشگری',
            'BUSINESS' => 'تجاری',
            'STUDENT' => 'تحصیلی',
            'WORK' => 'کاری',
            'TRANSIT' => 'ترانزیت',
            'PERMANENT' => 'اقامت دائم',
            'TEMPORARY' => 'اقامت موقت',
            'INVESTMENT' => 'سرمایه‌گذاری',
        ];

        return $types[$type] ?? $type;
    }

    /**
     * Check if passport is valid
     */
    private function isPassportValid(string $expiryDate): bool
    {
        if (empty($expiryDate)) return false;
        return strtotime($expiryDate) > time();
    }

    /**
     * Check if permit is valid
     */
    private function isPermitValid(string $expiryDate): bool
    {
        if (empty($expiryDate)) return false;
        return strtotime($expiryDate) > time();
    }

    /**
     * Get days to expiry
     */
    private function getDaysToExpiry(string $expiryDate): int
    {
        if (empty($expiryDate)) return 0;
        return max(0, floor((strtotime($expiryDate) - time()) / 86400));
    }

    /**
     * Translate travel type
     */
    private function translateTravelType(string $type): string
    {
        $types = [
            'ENTRY' => 'ورود',
            'EXIT' => 'خروج',
            'TRANSIT' => 'ترانزیت',
        ];

        return $types[$type] ?? $type;
    }

    /**
     * Translate travel purpose
     */
    private function translateTravelPurpose(string $purpose): string
    {
        $purposes = [
            'TOURISM' => 'گردشگری',
            'BUSINESS' => 'تجاری',
            'STUDY' => 'تحصیل',
            'WORK' => 'کار',
            'MEDICAL' => 'درمان',
            'FAMILY' => 'خانوادگی',
        ];

        return $purposes[$purpose] ?? $purpose;
    }

    /**
     * Calculate stay duration
     */
    private function calculateStayDuration(array $travel): string
    {
        $entryDate = $travel['entryDate'] ?? '';
        $exitDate = $travel['exitDate'] ?? date('Y-m-d');
        
        if (empty($entryDate)) return '0 روز';
        
        $days = floor((strtotime($exitDate) - strtotime($entryDate)) / 86400);
        
        if ($days < 30) return $days . ' روز';
        if ($days < 365) return floor($days / 30) . ' ماه';
        return floor($days / 365) . ' سال';
    }

    /**
     * Get current stay duration
     */
    private function getCurrentStayDuration(array $travels): string
    {
        foreach ($travels as $travel) {
            if (empty($travel['exitDate'])) {
                return $this->calculateStayDuration($travel);
            }
        }
        return '0 روز';
    }

    /**
     * Get frequent destinations
     */
    private function getFrequentDestinations(array $travels): array
    {
        $destinations = [];
        foreach ($travels as $travel) {
            $country = $travel['destinationCountry'] ?? '';
            if (!empty($country)) {
                $destinations[$country] = ($destinations[$country] ?? 0) + 1;
            }
        }
        
        arsort($destinations);
        return array_slice($destinations, 0, 3, true);
    }

    /**
     * Translate legal status
     */
    private function translateLegalStatus(string $status): string
    {
        $statuses = [
            'LEGAL' => 'قانونی',
            'ILLEGAL' => 'غیرقانونی',
            'EXPIRED' => 'منقضی',
            'UNDER_REVIEW' => 'در حال بررسی',
            'RESTRICTED' => 'محدود',
        ];

        return $statuses[$status] ?? $status;
    }

    /**
     * Check if eligible for renewal
     */
    private function isEligibleForRenewal(array $result): bool
    {
        $legal = $result['legalStatus'] ?? [];
        $residency = $result['residencyInfo'] ?? [];
        
        return ($legal['isLegal'] ?? false) && 
               !($legal['hasViolations'] ?? false) && 
               $this->getDaysToExpiry($residency['expiryDate'] ?? '') <= 60;
    }

    /**
     * Get required documents for renewal
     */
    private function getRequiredDocuments(string $permitType): array
    {
        $commonDocs = ['گذرنامه معتبر', 'عکس شناسنامه', 'گواهی عدم سوءپیشینه'];
        
        switch ($permitType) {
            case 'WORK':
                return array_merge($commonDocs, ['قرارداد کار', 'گواهی بیمه']);
            case 'STUDENT':
                return array_merge($commonDocs, ['پذیرش دانشگاه', 'گواهی تحصیلی']);
            case 'BUSINESS':
                return array_merge($commonDocs, ['مجوز کسب‌وکار', 'گواهی سپرده']);
            default:
                return $commonDocs;
        }
    }

    /**
     * Get alerts and warnings
     */
    private function getAlerts(array $result): array
    {
        $alerts = [];
        $residency = $result['residencyInfo'] ?? [];
        $legal = $result['legalStatus'] ?? [];
        $personal = $result['personalInfo'] ?? [];

        // Permit expiry alerts
        $daysToExpiry = $this->getDaysToExpiry($residency['expiryDate'] ?? '');
        if ($daysToExpiry < 0) {
            $alerts[] = [
                'type' => 'danger',
                'title' => 'اقامت منقضی',
                'message' => 'مجوز اقامت شما منقضی شده است'
            ];
        } elseif ($daysToExpiry <= 30) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'نزدیک به انقضا',
                'message' => "مجوز اقامت تا {$daysToExpiry} روز دیگر منقضی می‌شود"
            ];
        }

        // Passport expiry alerts
        if (!$this->isPassportValid($personal['passportExpiryDate'] ?? '')) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'گذرنامه منقضی',
                'message' => 'گذرنامه شما منقضی شده یا نزدیک به انقضا است'
            ];
        }

        // Legal status alerts
        if ($legal['hasViolations'] ?? false) {
            $alerts[] = [
                'type' => 'danger',
                'title' => 'تخلفات قانونی',
                'message' => 'شما دارای تخلفات قانونی هستید'
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
        $residency = $result['residencyInfo'] ?? [];
        $legal = $result['legalStatus'] ?? [];

        $daysToExpiry = $this->getDaysToExpiry($residency['expiryDate'] ?? '');
        
        if ($daysToExpiry < 0) {
            $recommendations[] = 'فوراً برای تمدید اقامت اقدام کنید';
            $recommendations[] = 'با اداره اتباع و مهاجرین تماس بگیرید';
        } elseif ($daysToExpiry <= 60) {
            $recommendations[] = 'برای تمدید اقامت آماده شوید';
            $recommendations[] = 'مدارک مورد نیاز را تهیه کنید';
        }

        if ($legal['hasViolations'] ?? false) {
            $recommendations[] = 'تخلفات قانونی خود را رفع کنید';
            $recommendations[] = 'با مشاور حقوقی مشورت کنید';
        }

        if (empty($recommendations)) {
            $recommendations[] = 'وضعیت قانونی شما مناسب است';
            $recommendations[] = 'به رعایت قوانین کشور ادامه دهید';
        }

        return $recommendations;
    }
} 