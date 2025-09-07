<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Services\BaseFinnotechController;
use App\Models\Service;
use App\Rules\IranianMobile;
use App\Rules\IranianNationalCode;
use App\Services\Finnotech\FinnotechService;
use App\Services\Finnotech\SmsAuthorizationService;
use Illuminate\Support\Facades\Log;

class DrivingLicenseStatusController extends BaseFinnotechController
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
        $this->apiEndpoint = 'driving_license_status';
        $this->scope = 'kyc:cc-license-check-inquiry:post';
        $this->requiresSms = false;
        $this->httpMethod = 'POST';

        $this->requiredFields = ['national_code', 'mobile'];
        
        $this->validationRules = [
            'national_code' => ['required', 'string', new IranianNationalCode()],
            'mobile' => ['required', 'string', new IranianMobile()],
        ];

        $this->validationMessages = [
            'national_code.required' => 'کد ملی الزامی است',
            'mobile.required' => 'شماره موبایل الزامی است',
        ];
    }

    /**
     * Prepare API parameters from service data
     */
    protected function prepareApiParameters(array $serviceData): array
    {
        $params = [
            'nationalID' => $serviceData['national_code'],
            'mobile' => $serviceData['mobile'],
        ];

        return $this->addTrackId($params);
    }

    /**
     * Format API response data
     */
    protected function formatResponseData(array $responseData): array
    {
        if (!isset($responseData['result'])) {
            return ['status' => 'failed', 'message' => 'خطا در دریافت اطلاعات گواهینامه'];
        }

        $result = $responseData['result'];
        $licenses = $result['licenses'] ?? [];
        $licenseStatus = $result['licenseStatus'] ?? [];

        $formattedLicenses = [];
        foreach ($licenses as $license) {
            $formattedLicenses[] = [
                'bar_code' => $license['barCode'] ?? '',
                'issue_date' => $license['issueDate'] ?? '',
                'issue_date_persian' => $this->formatDateToPersian($license['issueDate'] ?? ''),
                'firstname' => $license['firstname'] ?? '',
                'lastname' => $license['lastname'] ?? '',
                'full_name' => ($license['firstname'] ?? '') . ' ' . ($license['lastname'] ?? ''),
                'national_code' => $license['nationalCode'] ?? '',
                'license_number' => $license['licenseNumber'] ?? '',
                'license_status' => $license['licenseStatus'] ?? '',
                'license_type' => $license['licenseType'] ?? '',
                'validity_period' => $license['licenseValidityPeriod'] ?? '',
                'formatted_validity' => $this->formatValidityPeriod($license['licenseValidityPeriod'] ?? ''),
                'type_color' => $this->getLicenseTypeColor($license['licenseType'] ?? ''),
                'status_color' => $this->getStatusColor($license['licenseStatus'] ?? ''),
            ];
        }

        return [
            'status' => 'success',
            'data' => [
                'track_id' => $responseData['trackId'] ?? '',
                'response_code' => $responseData['responseCode'] ?? '',
                'licenses' => $formattedLicenses,
                'license_count' => count($formattedLicenses),
                'has_licenses' => !empty($formattedLicenses),
                'license_status' => [
                    'code' => $licenseStatus['code'] ?? '',
                    'description' => $licenseStatus['description'] ?? '',
                ],
                'processed_date' => now()->format('Y/m/d H:i:s'),
                'summary' => $this->generateSummary($formattedLicenses, $licenseStatus),
                'statistics' => $this->generateStatistics($formattedLicenses),
            ]
        ];
    }

    /**
     * Format date to Persian
     */
    private function formatDateToPersian(string $date): string
    {
        if (empty($date)) return '';
        // Convert MM/dd/yyyy to Persian format
        try {
            $carbonDate = \Carbon\Carbon::createFromFormat('m/d/Y H:i:s', $date);
            return \Morilog\Jalali\Jalalian::fromCarbon($carbonDate)->format('Y/m/d');
        } catch (\Exception $e) {
            return $date;
        }
    }

    /**
     * Format validity period
     */
    private function formatValidityPeriod(string $period): string
    {
        if (empty($period)) return '';
        return $period . ' سال';
    }

    /**
     * Get license type color
     */
    private function getLicenseTypeColor(string $type): string
    {
        $colors = [
            'پايه سوم' => 'blue',
            'پايه دوم' => 'green',
            'پايه اول' => 'purple',
            'موتورسيكلت' => 'orange',
            'default' => 'gray'
        ];

        return $colors[$type] ?? $colors['default'];
    }

    /**
     * Get status color
     */
    private function getStatusColor(string $status): string
    {
        if (str_contains($status, 'اسکن شده')) {
            return 'green';
        } elseif (str_contains($status, 'معتبر')) {
            return 'blue';
        } elseif (str_contains($status, 'منقضی')) {
            return 'red';
        } else {
            return 'yellow';
        }
    }

    /**
     * Generate summary text
     */
    private function generateSummary(array $licenses, array $licenseStatus): string
    {
        $count = count($licenses);
        
        if ($count === 0) {
            return 'هیچ گواهینامه‌ای برای این کد ملی یافت نشد.';
        }

        $types = array_unique(array_column($licenses, 'license_type'));
        $typeStr = implode('، ', $types);
        
        return "تعداد {$count} گواهینامه یافت شد - انواع: {$typeStr}";
    }

    /**
     * Generate statistics
     */
    private function generateStatistics(array $licenses): array
    {
        $typeCount = [];
        $validCount = 0;

        foreach ($licenses as $license) {
            $type = $license['license_type'];
            $typeCount[$type] = ($typeCount[$type] ?? 0) + 1;
            
            if (str_contains($license['license_status'], 'اسکن شده') || 
                str_contains($license['license_status'], 'معتبر')) {
                $validCount++;
            }
        }

        return [
            'total_count' => count($licenses),
            'valid_count' => $validCount,
            'type_breakdown' => $typeCount,
        ];
    }

    /**
     * Show service result using specific driving license view
     */
    public function show(string $resultId, Service $service)
    {
        $result = \App\Models\ServiceResult::where('result_hash', $resultId)
            ->where('service_id', $service->id)
            ->where('status', 'success')
            ->firstOrFail();

        // Check authorization
        if (!\Illuminate\Support\Facades\Auth::check() || $result->user_id !== \Illuminate\Support\Facades\Auth::id()) {
            abort(401, 'شما مجاز به مشاهده این نتیجه نیستید.');
        }

        if ($result->isExpired()) {
            abort(410, 'این نتیجه منقضی شده است.');
        }

        return view('front.services.results.driving-license-status', [
            'service' => $service,
            'data' => $result->output_data,
            'inputData' => $result->input_data,
            'resultId' => $resultId,
        ]);
    }
} 