<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Services\BaseFinnotechController;
use App\Services\Finnotech\FinnotechService;
use App\Services\Finnotech\SmsAuthorizationService;

class MilitaryServiceStatusController extends BaseFinnotechController
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
        $this->apiEndpoint = 'military-service-status';
        $this->scope = 'government:military:get';
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
            return ['status' => 'failed', 'message' => 'خطا در دریافت وضعیت نظام وظیفه'];
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
                'military_status' => [
                    'status_code' => $result['statusCode'] ?? '',
                    'status_title' => $result['statusTitle'] ?? '',
                    'status_description' => $this->getStatusDescription($result['statusCode'] ?? ''),
                    'status_date' => $result['statusDate'] ?? '',
                    'is_exempt' => $this->isExempt($result['statusCode'] ?? ''),
                    'is_completed' => $this->isCompleted($result['statusCode'] ?? ''),
                ],
                'service_details' => [
                    'unit_name' => $result['unitName'] ?? '',
                    'service_location' => $result['serviceLocation'] ?? '',
                    'start_date' => $result['startDate'] ?? '',
                    'end_date' => $result['endDate'] ?? '',
                    'service_duration' => $result['serviceDuration'] ?? '',
                    'rank' => $result['rank'] ?? '',
                ],
                'exemption_info' => [
                    'exemption_type' => $result['exemptionType'] ?? '',
                    'exemption_reason' => $result['exemptionReason'] ?? '',
                    'exemption_date' => $result['exemptionDate'] ?? '',
                    'exemption_document' => $result['exemptionDocument'] ?? '',
                ],
                'additional_info' => [
                    'has_military_card' => $result['hasMilitaryCard'] ?? false,
                    'military_card_number' => $result['militaryCardNumber'] ?? '',
                    'deferment_count' => $result['defermentCount'] ?? 0,
                    'last_deferment_date' => $result['lastDefermentDate'] ?? '',
                ],
                'next_actions' => $this->getNextActions($result),
                'documents_needed' => $this->getDocumentsNeeded($result),
            ]
        ];
    }

    /**
     * Get status description in Persian
     */
    private function getStatusDescription(string $statusCode): string
    {
        $descriptions = [
            'COMPLETED' => 'خدمت سربازی به پایان رسیده است',
            'EXEMPT_EDUCATION' => 'معافیت تحصیلی',
            'EXEMPT_MEDICAL' => 'معافیت پزشکی',
            'EXEMPT_PHYSICAL' => 'معافیت جسمانی',
            'EXEMPT_GUARDIAN' => 'معافیت نان‌آوری',
            'DEFERRED' => 'دارای معافیت موقت',
            'IN_SERVICE' => 'در حال انجام خدمت',
            'AWAITING_CALL' => 'در انتظار احضار',
            'DRAFT_DODGER' => 'فراری از خدمت',
            'NOT_APPLICABLE' => 'مشمول خدمت نمی‌باشد',
        ];

        return $descriptions[$statusCode] ?? 'وضعیت نامشخص';
    }

    /**
     * Check if person is exempt from military service
     */
    private function isExempt(string $statusCode): bool
    {
        return in_array($statusCode, [
            'EXEMPT_EDUCATION',
            'EXEMPT_MEDICAL', 
            'EXEMPT_PHYSICAL',
            'EXEMPT_GUARDIAN',
            'NOT_APPLICABLE'
        ]);
    }

    /**
     * Check if military service is completed
     */
    private function isCompleted(string $statusCode): bool
    {
        return $statusCode === 'COMPLETED';
    }

    /**
     * Get next actions based on status
     */
    private function getNextActions(array $result): array
    {
        $statusCode = $result['statusCode'] ?? '';
        $actions = [];

        switch ($statusCode) {
            case 'AWAITING_CALL':
                $actions[] = 'آماده‌سازی مدارک لازم برای خدمت';
                $actions[] = 'پیگیری تاریخ احضار';
                break;
            case 'DEFERRED':
                $actions[] = 'تمدید معافیت در صورت نیاز';
                $actions[] = 'پیگیری شرایط معافیت';
                break;
            case 'DRAFT_DODGER':
                $actions[] = 'مراجعه فوری به دفتر نظام وظیفه';
                $actions[] = 'ارائه توضیحات و مدارک';
                break;
            case 'IN_SERVICE':
                $actions[] = 'ادامه خدمت طبق برنامه';
                break;
            default:
                $actions[] = 'هیچ اقدام خاصی نیاز نیست';
        }

        return $actions;
    }

    /**
     * Get required documents based on status
     */
    private function getDocumentsNeeded(array $result): array
    {
        $statusCode = $result['statusCode'] ?? '';
        $documents = ['کارت ملی', 'شناسنامه'];

        if (in_array($statusCode, ['AWAITING_CALL', 'IN_SERVICE'])) {
            $documents[] = 'کارت پایان دوره';
            $documents[] = 'گواهی سلامت';
        }

        if ($statusCode === 'DEFERRED') {
            $documents[] = 'مدارک معافیت';
            $documents[] = 'گواهی تحصیل/کار';
        }

        return $documents;
    }
} 