<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Services\BaseFinnotechController;
use App\Services\Finnotech\FinnotechService;
use App\Services\Finnotech\SmsAuthorizationService;

class LifeStatusInquiryController extends BaseFinnotechController
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
        $this->apiEndpoint = 'life-status-inquiry';
        $this->scope = 'government:life-status:get';
        $this->requiresSms = true; // Government KYC services require SMS
        $this->httpMethod = 'GET';
        
        $this->requiredFields = ['national_code', 'birth_date'];
        
        $this->validationRules = [
            'national_code' => 'required|string|digits:10',
            'birth_date' => 'required|string|date_format:Y/m/d',
        ];
        
        $this->validationMessages = [
            'national_code.required' => 'کد ملی الزامی است',
            'national_code.digits' => 'کد ملی باید 10 رقم باشد',
            'birth_date.required' => 'تاریخ تولد الزامی است',
            'birth_date.date_format' => 'تاریخ تولد باید به فرمت yyyy/mm/dd باشد',
        ];
    }

    /**
     * Prepare API parameters from service data
     */
    protected function prepareApiParameters(array $serviceData): array
    {
        return [
            'nationalCode' => $serviceData['national_code'] ?? '',
            'birthDate' => $serviceData['birth_date'] ?? '',
        ];
    }

    /**
     * Format response data for display
     */
    protected function formatResponseData(array $responseData): array
    {
        if (!isset($responseData['result'])) {
            return ['status' => 'failed', 'message' => 'خطا در دریافت وضعیت حیات'];
        }

        $result = $responseData['result'];
        
        return [
            'status' => 'success',
            'data' => [
                'national_code' => request('national_code'),
                'life_status' => [
                    'is_alive' => $result['isAlive'] ?? true,
                    'status_text' => $result['isAlive'] ? 'در قید حیات' : 'فوت شده',
                    'verification_date' => $result['verificationDate'] ?? date('Y-m-d'),
                    'data_source' => $result['dataSource'] ?? 'سازمان ثبت احوال',
                ],
                'personal_info' => [
                    'full_name' => $result['fullName'] ?? '',
                    'father_name' => $result['fatherName'] ?? '',
                    'birth_date' => $result['birthDate'] ?? '',
                    'birth_place' => $result['birthPlace'] ?? '',
                    'serial_number' => $result['serialNumber'] ?? '',
                ],
                'death_info' => $result['isAlive'] ? null : [
                    'death_date' => $result['deathDate'] ?? '',
                    'death_place' => $result['deathPlace'] ?? '',
                    'death_certificate_number' => $result['deathCertificateNumber'] ?? '',
                    'burial_place' => $result['burialPlace'] ?? '',
                ],
                'verification_details' => [
                    'last_update' => $result['lastUpdate'] ?? '',
                    'reliability_score' => $result['reliabilityScore'] ?? 100,
                    'verification_method' => $result['verificationMethod'] ?? 'رسمی',
                    'additional_notes' => $result['additionalNotes'] ?? '',
                ],
                'usage_info' => [
                    'can_use_for_legal' => $result['canUseForLegal'] ?? true,
                    'can_use_for_financial' => $result['canUseForFinancial'] ?? true,
                    'valid_until' => $result['validUntil'] ?? date('Y-m-d', strtotime('+30 days')),
                    'reference_number' => $result['referenceNumber'] ?? $this->generateReferenceNumber(),
                ],
            ]
        ];
    }

    /**
     * Generate a reference number for the inquiry
     */
    private function generateReferenceNumber(): string
    {
        return 'LS-' . date('Ymd') . '-' . strtoupper(substr(md5(time() . rand()), 0, 6));
    }
} 