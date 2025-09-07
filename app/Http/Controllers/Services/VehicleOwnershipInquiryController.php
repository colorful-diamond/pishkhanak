<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Services\BaseFinnotechController;
use App\Services\Finnotech\FinnotechService;
use App\Services\Finnotech\SmsAuthorizationService;

class VehicleOwnershipInquiryController extends BaseFinnotechController
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
        $this->apiEndpoint = 'vehicle-ownership-inquiry';
        $this->scope = 'traffic:vehicle-ownership:get';
        $this->requiresSms = false;
        $this->httpMethod = 'GET';
        
        $this->requiredFields = ['national_code', 'plate_number'];
        
        $this->validationRules = [
            'national_code' => 'required|string|digits:10',
            'plate_number' => 'required|string|size:9',  // API expects 9-digit plate number
        ];
        
        $this->validationMessages = [
            'national_code.required' => 'کد ملی الزامی است',
            'national_code.digits' => 'کد ملی باید 10 رقم باشد',
            'plate_number.required' => 'شماره پلاک الزامی است',
            'plate_number.size' => 'شماره پلاک باید 9 رقم باشد',
        ];
    }

    /**
     * Prepare API parameters from service data
     */
    protected function prepareApiParameters(array $serviceData): array
    {
        return [
            'nationalId' => $serviceData['national_code'] ?? '',  // API expects nationalId
            'plateNumber' => $serviceData['plate_number'] ?? '',
        ];
    }

    /**
     * Format response data for display
     */
    protected function formatResponseData(array $responseData): array
    {
        if (!isset($responseData['result'])) {
            return ['status' => 'failed', 'message' => 'خطا در دریافت اطلاعات مالکیت'];
        }

        $result = $responseData['result'];
        
        return [
            'status' => 'success',
            'data' => [
                'plate_number' => request('plate_number'),
                'vehicle_info' => [
                    'brand' => $result['brand'] ?? '',
                    'model' => $result['model'] ?? '',
                    'color' => $result['color'] ?? '',
                    'fuel_type' => $result['fuelType'] ?? '',
                    'engine_number' => $result['engineNumber'] ?? '',
                    'chassis_number' => $result['chassisNumber'] ?? '',
                    'production_year' => $result['productionYear'] ?? '',
                ],
                'ownership_info' => [
                    'owner_name' => $result['ownerName'] ?? '',
                    'ownership_date' => $result['ownershipDate'] ?? '',
                    'ownership_type' => $result['ownershipType'] ?? '',
                    'registration_date' => $result['registrationDate'] ?? '',
                    'registration_location' => $result['registrationLocation'] ?? '',
                ],
                'status_info' => [
                    'vehicle_status' => $result['vehicleStatus'] ?? '',
                    'insurance_status' => $result['insuranceStatus'] ?? '',
                    'technical_visit_status' => $result['technicalVisitStatus'] ?? '',
                    'last_technical_visit' => $result['lastTechnicalVisit'] ?? '',
                    'next_technical_visit' => $result['nextTechnicalVisit'] ?? '',
                ],
                'plate_info' => [
                    'formatted_plate' => $this->formatPlateNumber(request('plate_number')),
                    'plate_type' => $result['plateType'] ?? 'عادی',
                    'region_code' => $result['regionCode'] ?? '',
                    'region_name' => $result['regionName'] ?? '',
                ]
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