<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Services\BaseFinnotechController;
use App\Services\Finnotech\FinnotechService;
use App\Services\Finnotech\SmsAuthorizationService;

class CheckColorInquiryController extends BaseFinnotechController
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
        $this->apiEndpoint = 'check-color-inquiry';
        $this->scope = 'banking:check-color:get';
        $this->requiresSms = false;
        $this->httpMethod = 'GET';
        
        $this->requiredFields = ['mobile', 'national_code', 'check_number'];
        
        $this->validationRules = [
            'mobile' => 'required|string|regex:/^09[0-9]{9}$/',
            'national_code' => 'required|string|digits:10',
            'check_number' => 'required|string|min:8|max:20',
            'bank_code' => 'nullable|string|size:3',
        ];
        
        $this->validationMessages = [
            'mobile.required' => 'شماره موبایل الزامی است',
            'mobile.regex' => 'شماره موبایل نامعتبر است',
            'national_code.required' => 'کد ملی الزامی است',
            'national_code.digits' => 'کد ملی باید 10 رقم باشد',
            'check_number.required' => 'شماره چک الزامی است',
            'check_number.min' => 'شماره چک باید حداقل 8 رقم باشد',
            'bank_code.size' => 'کد بانک باید 3 رقم باشد',
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
            'checkNumber' => $serviceData['check_number'] ?? '',
        ];

        // Add optional bank code if provided
        if (!empty($serviceData['bank_code'])) {
            $params['bankCode'] = $serviceData['bank_code'];
        }

        return $params;
    }

    /**
     * Format response data for display
     */
    protected function formatResponseData(array $responseData): array
    {
        if (!isset($responseData['result'])) {
            return ['status' => 'failed', 'message' => 'خطا در استعلام وضعیت چک'];
        }

        $result = $responseData['result'];
        
        return [
            'status' => 'success',
            'data' => [
                'check_info' => [
                    'check_number' => request('check_number'),
                    'bank_name' => $result['bankName'] ?? '',
                    'branch_name' => $result['branchName'] ?? '',
                    'branch_code' => $result['branchCode'] ?? '',
                ],
                'check_status' => [
                    'color' => $result['color'] ?? '',
                    'color_persian' => $this->translateCheckColor($result['color'] ?? ''),
                    'status' => $result['status'] ?? '',
                    'status_description' => $this->getStatusDescription($result['status'] ?? ''),
                    'is_valid' => $this->isValidCheck($result['color'] ?? ''),
                    'can_cash' => $this->canCash($result['color'] ?? ''),
                ],
                'check_details' => [
                    'amount' => $result['amount'] ?? 0,
                    'formatted_amount' => number_format($result['amount'] ?? 0) . ' ریال',
                    'issue_date' => $result['issueDate'] ?? '',
                    'due_date' => $result['dueDate'] ?? '',
                    'account_number' => $result['accountNumber'] ?? '',
                    'account_owner' => $result['accountOwner'] ?? '',
                ],
                'bounce_info' => $result['color'] === 'RED' ? [
                    'bounce_date' => $result['bounceDate'] ?? '',
                    'bounce_reason' => $result['bounceReason'] ?? '',
                    'bounce_code' => $result['bounceCode'] ?? '',
                    'penalty_amount' => $result['penaltyAmount'] ?? 0,
                    'formatted_penalty' => number_format($result['penaltyAmount'] ?? 0) . ' ریال',
                ] : null,
                'recommendations' => $this->getRecommendations($result),
                'warnings' => $this->getWarnings($result),
            ]
        ];
    }

    /**
     * Translate check color to Persian
     */
    private function translateCheckColor(string $color): string
    {
        $colors = [
            'WHITE' => 'سفید (معتبر)',
            'GRAY' => 'خاکستری (مشروط)',
            'RED' => 'قرمز (برگشتی)',
            'BLACK' => 'سیاه (جعلی)',
        ];

        return $colors[$color] ?? $color;
    }

    /**
     * Get status description
     */
    private function getStatusDescription(string $status): string
    {
        $descriptions = [
            'VALID' => 'چک معتبر و قابل نقد است',
            'CONDITIONAL' => 'چک نیاز به بررسی بیشتر دارد',
            'BOUNCED' => 'چک برگشت خورده است',
            'FORGED' => 'چک جعلی شناسایی شده است',
            'CANCELLED' => 'چک لغو شده است',
            'EXPIRED' => 'چک منقضی شده است',
        ];

        return $descriptions[$status] ?? 'وضعیت نامشخص';
    }

    /**
     * Check if check is valid
     */
    private function isValidCheck(string $color): bool
    {
        return in_array($color, ['WHITE', 'GRAY']);
    }

    /**
     * Check if check can be cashed
     */
    private function canCash(string $color): bool
    {
        return $color === 'WHITE';
    }

    /**
     * Get recommendations based on check status
     */
    private function getRecommendations(array $result): array
    {
        $color = $result['color'] ?? '';
        $recommendations = [];

        switch ($color) {
            case 'WHITE':
                $recommendations[] = 'چک معتبر است و می‌توانید آن را نقد کنید';
                $recommendations[] = 'قبل از نقد کردن موجودی حساب را بررسی کنید';
                break;
            case 'GRAY':
                $recommendations[] = 'چک نیاز به بررسی بیشتر دارد';
                $recommendations[] = 'با بانک تماس بگیرید';
                break;
            case 'RED':
                $recommendations[] = 'چک برگشت خورده، از نقد کردن خودداری کنید';
                $recommendations[] = 'با صادرکننده چک تماس بگیرید';
                break;
            case 'BLACK':
                $recommendations[] = 'چک جعلی است، فوراً با پلیس تماس بگیرید';
                $recommendations[] = 'چک را تحویل مراجع قانونی دهید';
                break;
        }

        return $recommendations;
    }

    /**
     * Get warnings based on check status
     */
    private function getWarnings(array $result): array
    {
        $color = $result['color'] ?? '';
        $warnings = [];

        if ($color === 'RED') {
            $warnings[] = 'خطر: چک برگشتی';
            $warnings[] = 'ممکن است جریمه داشته باشد';
        }

        if ($color === 'BLACK') {
            $warnings[] = 'خطر: چک جعلی';
            $warnings[] = 'فوراً به مراجع قانونی اطلاع دهید';
        }

        if (!empty($result['dueDate']) && strtotime($result['dueDate']) < time()) {
            $warnings[] = 'توجه: سررسید چک گذشته است';
        }

        return $warnings;
    }
} 