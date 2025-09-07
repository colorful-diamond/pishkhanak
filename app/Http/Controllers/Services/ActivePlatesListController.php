<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Services\BaseFinnotechController;
use App\Services\Finnotech\FinnotechService;
use App\Services\Finnotech\SmsAuthorizationService;

class ActivePlatesListController extends BaseFinnotechController
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
        $this->apiEndpoint = 'active-plates-list';
        $this->scope = 'vehicle:active-plate-numbers:get';
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
            'nationalId' => $serviceData['national_code'] ?? '',  // API expects nationalId
        ];
    }

    /**
     * Format response data for display
     */
    protected function formatResponseData(array $responseData): array
    {
        if (!isset($responseData['result'])) {
            return ['status' => 'failed', 'message' => 'خطا در دریافت لیست پلاک‌های فعال'];
        }

        $result = $responseData['result'];
        $plates = $result['plates'] ?? [];
        
        $processedPlates = [];
        foreach ($plates as $plate) {
            $processedPlates[] = [
                'plate_number' => $plate['plateNumber'] ?? '',
                'formatted_plate' => $this->formatPlateNumber($plate['plateNumber'] ?? ''),
                'vehicle_type' => $plate['vehicleType'] ?? 'خودرو',
                'brand' => $plate['brand'] ?? '',
                'model' => $plate['model'] ?? '',
                'color' => $plate['color'] ?? '',
                'production_year' => $plate['productionYear'] ?? '',
                'registration_date' => $plate['registrationDate'] ?? '',
                'status' => $plate['status'] ?? 'فعال',
                'insurance_status' => $plate['insuranceStatus'] ?? 'نامشخص',
                'technical_visit_status' => $plate['technicalStatus'] ?? 'نامشخص',
                'ownership_type' => $plate['ownershipType'] ?? 'مالک',
                'region_name' => $plate['regionName'] ?? '',
            ];
        }

        return [
            'status' => 'success',
            'data' => [
                'national_code' => request('national_code'),
                'total_plates' => count($plates),
                'plates' => $processedPlates,
                'summary' => [
                    'cars' => collect($processedPlates)->where('vehicle_type', 'خودرو')->count(),
                    'motorcycles' => collect($processedPlates)->where('vehicle_type', 'موتورسیکلت')->count(),
                    'active_insurance' => collect($processedPlates)->where('insurance_status', 'فعال')->count(),
                    'expired_insurance' => collect($processedPlates)->where('insurance_status', 'منقضی')->count(),
                ],
                'has_plates' => count($plates) > 0,
            ]
        ];
    }

    /**
     * Format plate number for display
     */
    private function formatPlateNumber(string $plateNumber): string
    {
        if (strlen($plateNumber) !== 8) {
            return $plateNumber;
        }

        $region = substr($plateNumber, 0, 2);
        $letter = substr($plateNumber, 2, 1);
        $numbers = substr($plateNumber, 3, 3);
        $series = substr($plateNumber, 6, 2);

        return "{$region} {$letter} {$numbers} {$series}";
    }
} 