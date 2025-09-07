<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Services\BaseFinnotechController;
use App\Services\Finnotech\FinnotechService;
use App\Services\Finnotech\SmsAuthorizationService;

class PassportStatusInquiryController extends BaseFinnotechController
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
        $this->apiEndpoint = 'passport-status-inquiry';
        $this->scope = 'kyc:passport-inquiry:post';
        $this->requiresSms = true; // KYC services require SMS verification
        $this->httpMethod = 'POST';
        
        $this->requiredFields = ['national_code', 'mobile'];
        
        $this->validationRules = [
            'national_code' => 'required|string|digits:10',
            'mobile' => 'required|string|regex:/^09[0-9]{9}$/',
        ];
        
        $this->validationMessages = [
            'national_code.required' => 'کد ملی الزامی است',
            'national_code.digits' => 'کد ملی باید 10 رقم باشد',
            'mobile.required' => 'شماره موبایل الزامی است',
            'mobile.regex' => 'شماره موبایل نامعتبر است',
        ];
    }

    /**
     * Prepare API parameters from service data
     */
    protected function prepareApiParameters(array $serviceData): array
    {
        return [
            'nationalCode' => $serviceData['national_code'] ?? '',
            'mobile' => $serviceData['mobile'] ?? '',
        ];
    }

    /**
     * Format response data for display
     */
    protected function formatResponseData(array $responseData): array
    {
        if (!isset($responseData['result'])) {
            return ['status' => 'failed', 'message' => 'خطا در دریافت اطلاعات گذرنامه'];
        }

        $result = $responseData['result'];
        
        return [
            'status' => 'success',
            'data' => [
                'national_code' => request('national_code'),
                'mobile' => request('mobile'),
                'passport_info' => [
                    'has_passport' => $result['hasPassport'] ?? false,
                    'passport_request' => $result['passportRequest'] ?? false,
                    'request_description' => $result['requestDescription'] ?? '',
                    'request_date' => $result['requestDate'] ?? '',
                    'passport_serial' => $result['passportSerial'] ?? '',
                    'passport_number' => $result['passportNumber'] ?? '',
                    'issue_date' => $result['issueDate'] ?? '',
                    'validity_date' => $result['validityDate'] ?? '',
                    'passport_status' => $result['passportStatus'] ?? '',
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
     * Get passport status description in Persian
     */
    private function getPassportStatusDescription(string $status): string
    {
        $statusDescriptions = [
            'معتبر' => 'گذرنامه معتبر و قابل استفاده است',
            'نامعتبر' => 'گذرنامه منقضی شده یا باطل شده است',
            'در حال بررسی' => 'درخواست گذرنامه در حال بررسی است',
            'صادر شده' => 'گذرنامه صادر شده و آماده تحویل است',
            'تحویل داده شده' => 'گذرنامه تحویل داده شده است',
        ];

        return $statusDescriptions[$status] ?? $status;
    }

    /**
     * Check if passport is valid and active
     */
    private function isPassportValid(array $passportInfo): bool
    {
        if (!($passportInfo['has_passport'] ?? false)) {
            return false;
        }

        $status = $passportInfo['passport_status'] ?? '';
        return in_array($status, ['معتبر', 'صادر شده', 'تحویل داده شده']);
    }

    /**
     * Check if passport is expired
     */
    private function isPassportExpired(array $passportInfo): bool
    {
        $validityDate = $passportInfo['validity_date'] ?? '';
        if (empty($validityDate)) {
            return false;
        }

        try {
            $validityDateCarbon = \Carbon\Carbon::createFromFormat('Y/m/d', $validityDate);
            return $validityDateCarbon->isPast();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get days until passport expiry
     */
    private function getDaysUntilExpiry(array $passportInfo): ?int
    {
        $validityDate = $passportInfo['validity_date'] ?? '';
        if (empty($validityDate)) {
            return null;
        }

        try {
            $validityDateCarbon = \Carbon\Carbon::createFromFormat('Y/m/d', $validityDate);
            return $validityDateCarbon->diffInDays(now(), false);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Enhanced format response data with additional analysis
     */
    protected function formatResponseDataEnhanced(array $responseData): array
    {
        $basicFormatted = $this->formatResponseData($responseData);
        
        if ($basicFormatted['status'] === 'success') {
            $passportInfo = $basicFormatted['data']['passport_info'];
            
            // Add analysis
            $basicFormatted['data']['analysis'] = [
                'is_valid' => $this->isPassportValid($passportInfo),
                'is_expired' => $this->isPassportExpired($passportInfo),
                'days_until_expiry' => $this->getDaysUntilExpiry($passportInfo),
                'status_description' => $this->getPassportStatusDescription($passportInfo['passport_status'] ?? ''),
                'recommendations' => $this->getRecommendations($passportInfo),
            ];
        }
        
        return $basicFormatted;
    }

    /**
     * Get recommendations based on passport status
     */
    private function getRecommendations(array $passportInfo): array
    {
        $recommendations = [];
        
        if (!($passportInfo['has_passport'] ?? false)) {
            $recommendations[] = 'شما گذرنامه ندارید. برای دریافت گذرنامه به اداره گذرنامه مراجعه کنید.';
        } else {
            $daysUntilExpiry = $this->getDaysUntilExpiry($passportInfo);
            
            if ($this->isPassportExpired($passportInfo)) {
                $recommendations[] = 'گذرنامه شما منقضی شده است. برای تمدید یا صدور گذرنامه جدید اقدام کنید.';
            } elseif ($daysUntilExpiry !== null && $daysUntilExpiry <= 180) {
                $recommendations[] = 'گذرنامه شما کمتر از 6 ماه تا انقضا فاصله دارد. توصیه می‌شود برای تمدید اقدام کنید.';
            } elseif ($this->isPassportValid($passportInfo)) {
                $recommendations[] = 'گذرنامه شما معتبر است و می‌توانید از آن برای سفر استفاده کنید.';
            }
        }
        
        return $recommendations;
    }
}