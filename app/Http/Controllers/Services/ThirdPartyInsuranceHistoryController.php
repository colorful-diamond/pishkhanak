<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Services\BaseFinnotechController;
use App\Contracts\ServicePreviewInterface;
use App\Services\Finnotech\FinnotechService;
use App\Services\Finnotech\SmsAuthorizationService;
use App\Models\Service;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Exception;

class ThirdPartyInsuranceHistoryController extends BaseFinnotechController implements ServicePreviewInterface
{
    public function __construct(FinnotechService $finnotechService, SmsAuthorizationService $smsAuthService)
    {
        parent::__construct($finnotechService, $smsAuthService);
        $this->configureService();
    }
    /**
     * Prepare API parameters from form data
     */
    protected function prepareApiParameters(array $serviceData): array
    {
        // Convert plate parts to 9-digit format expected by API
        $plateNumber = $this->convertPlatePartsToApiFormat($serviceData);
        
        $apiParams = [
            'nationalCode' => $serviceData['national_code'] ?? '',
            'plateNumber' => $plateNumber,
        ];
        
        // Debug logging to check what we're sending to API
        Log::info('Third Party Insurance API Parameters', [
            'service_data' => $serviceData,
            'api_params' => $apiParams,
            'plate_number_length' => strlen($plateNumber),
            'national_code_length' => strlen($serviceData['national_code'] ?? '')
        ]);
        
        return $apiParams;
    }

    /**
     * Convert plate parts to API format (9 digits)
     * Format: part1(2) + letter(2) + part2(3) + part3(2) = 9 digits
     * Example: 12الف345 67 -> 120134567 (12 + 01 + 345 + 67)
     */
    private function convertPlatePartsToApiFormat(array $serviceData): string
    {
        // If we have separate plate parts (from our form)
        if (isset($serviceData['plate_part1'], $serviceData['plate_letter'], $serviceData['plate_part2'], $serviceData['plate_part3'])) {
            // Ensure all parts are properly formatted
            $part1 = str_pad($serviceData['plate_part1'], 2, '0', STR_PAD_LEFT);
            $letter = str_pad($this->convertLetterToNumber($serviceData['plate_letter']), 2, '0', STR_PAD_LEFT);
            $part2 = str_pad($serviceData['plate_part2'], 3, '0', STR_PAD_LEFT);
            $part3 = str_pad($serviceData['plate_part3'], 2, '0', STR_PAD_LEFT);
            
            $plateNumber = $part1 . $letter . $part2 . $part3;
            
            // Validate that result is exactly 9 digits
            if (strlen($plateNumber) !== 9 || !ctype_digit($plateNumber)) {
                Log::warning('Invalid plate number format generated', [
                    'parts' => compact('part1', 'letter', 'part2', 'part3'),
                    'result' => $plateNumber,
                    'length' => strlen($plateNumber)
                ]);
            }
            
            return $plateNumber;
        }
        
        // If we already have a complete plate number
        return $serviceData['plate_number'] ?? '';
    }

    /**
     * Convert Persian letter to number for API
     */
    private function convertLetterToNumber(string $letter): string
    {
        $letterMap = [
            'الف' => '01', 'ب' => '02', 'پ' => '03', 'ت' => '04', 'ث' => '05',
            'ج' => '06', 'چ' => '07', 'ح' => '08', 'خ' => '09', 'د' => '10',
            'ذ' => '11', 'ر' => '12', 'ز' => '13', 'ژ' => '14', 'س' => '15',
            'ش' => '16', 'ص' => '17', 'ض' => '18', 'ط' => '19', 'ظ' => '20',
            'ع' => '21', 'غ' => '22', 'ف' => '23', 'ق' => '24', 'ک' => '25',
            'گ' => '26', 'ل' => '27', 'م' => '28', 'ن' => '29', 'و' => '30',
            'ه' => '31', 'ی' => '32', 'معلولین' => '33', 'تشریفات' => '34', 'D' => '34', 'S' => '34'
        ];

        return $letterMap[$letter] ?? '00';
    }

    /**
     * Configure service-specific settings
     */
    protected function configureService(): void
    {
        $this->apiEndpoint = 'third_party_insurance_history';
        $this->scope = 'kyc:thirdParty-insurance-inquiry:get';
        $this->requiresSms = false;
        $this->httpMethod = 'GET';
        
        $this->requiredFields = ['national_code', 'plate_part1', 'plate_letter', 'plate_part2', 'plate_part3'];
        
        $this->validationRules = [
            'national_code' => 'required|string|digits:10',
            'plate_part1' => 'required|string|digits:2',
            'plate_letter' => 'required|string',
            'plate_part2' => 'required|string|digits:3', 
            'plate_part3' => 'required|string|digits:2',
        ];
        
        $this->validationMessages = [
            'national_code.required' => 'کد ملی الزامی است',
            'national_code.digits' => 'کد ملی باید 10 رقم باشد',
            'plate_part1.required' => 'قسمت اول پلاک الزامی است',
            'plate_part1.digits' => 'قسمت اول پلاک باید 2 رقم باشد',
            'plate_letter.required' => 'حرف پلاک الزامی است',
            'plate_part2.required' => 'قسمت دوم پلاک الزامی است',
            'plate_part2.digits' => 'قسمت دوم پلاک باید 3 رقم باشد',
            'plate_part3.required' => 'قسمت آخر پلاک الزامی است',
            'plate_part3.digits' => 'قسمت آخر پلاک باید 2 رقم باشد',
        ];
    }

    /**
     * Format response data for display
     */
    protected function formatResponseData(array $responseData): array
    {
        if (!isset($responseData['result'])) {
            return ['status' => 'failed', 'message' => 'خطا در دریافت سوابق بیمه شخص ثالث'];
        }

        $result = $responseData['result'];
        
        return [
            'status' => 'success',
            'data' => [
                // Basic Info
                'track_id' => $responseData['trackId'] ?? '',
                'response_code' => $responseData['responseCode'] ?? '',
                
                // Vehicle Information
                'vehicle_info' => $this->getVehicleInfo($result),
                
                // Current Insurance Policy
                'current_policy' => $this->getCurrentPolicy($result),
                
                // Coverage Details
                'coverage_details' => $this->getCoverageDetails($result),
                
                // Discount Information
                'discount_info' => $this->getDiscountInfoFromAPI($result),
                
                // Claims Statistics
                'claims_stats' => $this->getClaimsStats($result),
                
                // Company Information
                'insurance_company' => $this->getCompanyInfo($result),
                
                // Raw data for debugging (can be removed in production)
                'raw_result' => $result,
            ]
        ];
    }

    /**
     * Get vehicle information from API result
     */
    private function getVehicleInfo(array $result): array
    {
        return [
            'plate_parts' => [
                'part1' => $result['plk1'] ?? '',
                'letter' => $this->convertNumberToLetter($result['plk2'] ?? ''),
                'part2' => $result['plk3'] ?? '',
                'serial' => $result['plkSrl'] ?? '',
            ],
            'formatted_plate' => $this->formatPlateFromParts($result),
            'vehicle_system' => $result['mapVehicleSystemName'] ?? $result['systemField'] ?? '',
            'vehicle_type' => $result['mapTypNam'] ?? $result['typeField'] ?? '',
            'vehicle_usage' => $result['mapUsageName'] ?? $result['usageField'] ?? '',
            'model_year' => $result['modelField'] ?? '',
            'main_color' => $result['mainColorField'] ?? '',
            'second_color' => $result['secondColorField'] ?? '',
            'capacity' => $result['capacityField'] ?? '',
            'engine_number' => $result['engineNumberField'] ?? $result['mtrNum'] ?? '',
            'chassis_number' => $result['chassisNumberField'] ?? $result['shsNum'] ?? '',
            'vin_number' => $result['vinNumberField'] ?? $result['vin'] ?? '',
            'cylinder_count' => $result['cylinderNumberField'] ?? $result['cylCnt'] ?? '',
            'axel_count' => $result['axelNumberField'] ?? '',
            'wheel_count' => $result['wheelNumberField'] ?? '',
            'install_date' => $result['installDateField'] ?? '',
        ];
    }

    /**
     * Get current policy information
     */
    private function getCurrentPolicy(array $result): array
    {
        return [
            'policy_number' => $result['prntPlcyCmpDocNo'] ?? '',
            'third_policy_code' => $result['thirdPolicyCode'] ?? '',
            'issue_date' => $result['issueDate'] ?? '',
            'start_date' => $result['startDate'] ?? '',
            'end_date' => $result['endDate'] ?? '',
            'days_remaining' => $this->calculateDaysRemaining($result['endDate'] ?? ''),
            'is_active' => $this->isPolicyActive($result['endDate'] ?? ''),
            'status_type_code' => $result['statusTypeCode'] ?? 1,
        ];
    }

    /**
     * Get coverage details
     */
    private function getCoverageDetails(array $result): array
    {
        return [
            'person_coverage' => [
                'amount' => $result['personCvrCptl'] ?? 0,
                'formatted' => number_format($result['personCvrCptl'] ?? 0) . ' ریال',
                'toman' => ($result['personCvrCptl'] ?? 0) / 10,
                'formatted_toman' => number_format(($result['personCvrCptl'] ?? 0) / 10) . ' تومان',
            ],
            'life_coverage' => [
                'amount' => $result['lifeCvrCptl'] ?? 0,
                'formatted' => number_format($result['lifeCvrCptl'] ?? 0) . ' ریال',
                'toman' => ($result['lifeCvrCptl'] ?? 0) / 10,
                'formatted_toman' => number_format(($result['lifeCvrCptl'] ?? 0) / 10) . ' تومان',
            ],
            'financial_coverage' => [
                'amount' => $result['financialCvrCptl'] ?? 0,
                'formatted' => number_format($result['financialCvrCptl'] ?? 0) . ' ریال',
                'toman' => ($result['financialCvrCptl'] ?? 0) / 10,
                'formatted_toman' => number_format(($result['financialCvrCptl'] ?? 0) / 10) . ' تومان',
            ],
        ];
    }

    /**
     * Get discount information from API
     */
    private function getDiscountInfoFromAPI(array $result): array
    {
        return [
            'person_discount' => [
                'years_without_claim' => $result['disPrsnYrNum'] ?? 0,
                'percentage' => $result['disPrsnYrPrcnt'] ?? 0,
                'discount_percentage' => $result['discountPersonPercent'] ?? 0,
            ],
            'financial_discount' => [
                'years_without_claim' => $result['disFnYrNum'] ?? 0,
                'percentage' => $result['disFnYrPrcnt'] ?? 0,
            ],
            'life_discount' => [
                'years_without_claim' => $result['disLfYrNum'] ?? 0,
                'percentage' => $result['disLfYrPrcnt'] ?? 0,
            ],
            'third_party_discount' => [
                'percentage' => $result['discountThirdPercent'] ?? 0,
            ],
        ];
    }

    /**
     * Get claims statistics
     */
    private function getClaimsStats(array $result): array
    {
        return [
            'total_loss_count' => $result['cuntLossAmont'] ?? 0,
            'policy_health_loss' => $result['policyHealthLossCount'] ?? 0,
            'policy_financial_loss' => $result['policyFinancialLossCount'] ?? 0,
            'policy_person_loss' => $result['policyPersonLossCount'] ?? 0,
        ];
    }

    /**
     * Get insurance company information
     */
    private function getCompanyInfo(array $result): array
    {
        return [
            'company_name' => $result['companyName'] ?? '',
            'company_code' => $result['companyCode'] ?? '',
            'last_company_document' => $result['lastCompanyDocumentNumber'] ?? '',
            'endorse_text' => $result['endorseText'] ?? '',
            'endorse_date' => $result['endorseDate'] ?? '',
            'print_endorse_document' => $result['printEndorsCompanyDocumentNumber'] ?? '',
        ];
    }

    /**
     * Convert number to Persian letter
     */
    private function convertNumberToLetter(string $number): string
    {
        $numberMap = [
            '01' => 'الف', '02' => 'ب', '03' => 'پ', '04' => 'ت', '05' => 'ث',
            '06' => 'ج', '07' => 'چ', '08' => 'ح', '09' => 'خ', '10' => 'د',
            '11' => 'ذ', '12' => 'ر', '13' => 'ز', '14' => 'ژ', '15' => 'س',
            '16' => 'ش', '17' => 'ص', '18' => 'ض', '19' => 'ط', '20' => 'ظ',
            '21' => 'ع', '22' => 'غ', '23' => 'ف', '24' => 'ق', '25' => 'ک',
            '26' => 'گ', '27' => 'ل', '28' => 'م', '29' => 'ن', '30' => 'و',
            '31' => 'ه', '32' => 'ی', '33' => 'معلولین', '34' => 'تشریفات'
        ];

        // Handle single digit input by padding with zero
        if (strlen($number) === 1) {
            $number = '0' . $number;
        }

        return $numberMap[$number] ?? $number;
    }

    /**
     * Format plate from API parts
     */
    private function formatPlateFromParts(array $result): string
    {
        $part1 = str_pad($result['plk1'] ?? '', 2, '0', STR_PAD_LEFT);
        $letter = $this->convertNumberToLetter($result['plk2'] ?? '');
        $part2 = str_pad($result['plk3'] ?? '', 3, '0', STR_PAD_LEFT);
        $serial = str_pad($result['plkSrl'] ?? '', 2, '0', STR_PAD_LEFT);

        return "{$part1} {$letter} {$part2} {$serial}";
    }

    /**
     * Calculate days remaining until expiry
     */
    private function calculateDaysRemaining(string $endDate): int
    {
        if (empty($endDate)) return 0;
        
        // Convert Persian date to timestamp if needed
        try {
            $timestamp = strtotime($endDate);
            if ($timestamp === false) {
                // Try to parse Persian date format (YYYY/MM/DD)
                $parts = explode('/', $endDate);
                if (count($parts) === 3) {
                    $timestamp = mktime(0, 0, 0, (int)$parts[1], (int)$parts[2], (int)$parts[0]);
                }
            }
            
            return $timestamp ? floor(($timestamp - time()) / 86400) : 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Check if policy is active
     */
    private function isPolicyActive(string $endDate): bool
    {
        return $this->calculateDaysRemaining($endDate) > 0;
    }

    /**
     * Show service result using specific third party insurance view
     */
    public function show(string $resultId, \App\Models\Service $service)
    {
        $result = \App\Models\ServiceResult::where('result_hash', $resultId)
            ->where('service_id', $service->id)
            ->where('status', 'success')
            ->firstOrFail();

        // Check authorization
        if (!\Illuminate\Support\Facades\Auth::check() || $result->user_id !== \Illuminate\Support\Facades\Auth::id()) {
            abort(401, 'شما مجاز به مشاهده این نتیجه نیستید.');
        }

        // Check if result is expired
        if ($result->isExpired()) {
            abort(410, 'این نتیجه منقضی شده است.');
        }

        // Use the output_data directly since it's already formatted correctly by formatResponseData
        return view('front.services.results.third-party-insurance-history', [
            'service' => $service,
            'data' => ['status' => 'success', 'data' => $result->output_data],
            'inputData' => $result->input_data,
            'resultId' => $resultId,
        ]);
    }

    /**
     * Convert plate parts to third party insurance API format
     * Example: ['plate_part1' => '36', 'plate_letter' => 'ط', 'plate_part2' => '784', 'plate_part3' => '89'] 
     * becomes "A-ir36-784-ط-89"
     */
    private function convertPlateToInsuranceApiFormat(array $serviceData): string
    {
        $platePart1 = $serviceData['plate_part1'] ?? '';
        $plateLetter = $serviceData['plate_letter'] ?? '';
        $platePart2 = $serviceData['plate_part2'] ?? '';
        $platePart3 = $serviceData['plate_part3'] ?? '';
        
        return "A-ir{$platePart3}-{$platePart2}-{$plateLetter}-{$platePart1}";
    }

    /**
     * Generate cache key for third party insurance API response
     */
    private function generateInsuranceCacheKey(array $serviceData): string
    {
        $plateFormatted = $this->convertPlateToInsuranceApiFormat($serviceData);
        $nationalCode = $serviceData['national_code'] ?? '';
        
        // Create unique cache key based on plate and national code
        return 'insurance_api:' . md5($plateFormatted . ':' . $nationalCode);
    }

    /**
     * Call third party insurance API to get car insurance information (with 7-day caching)
     */
    private function callInsuranceApi(array $serviceData): ?array
    {
        try {
            $plateFormatted = $this->convertPlateToInsuranceApiFormat($serviceData);
            $nationalCode = $serviceData['national_code'] ?? '';
            $cacheKey = $this->generateInsuranceCacheKey($serviceData);
            
            // Check if we have cached data (7 days = 7 * 24 * 60 = 10080 minutes)
            $cachedData = Cache::get($cacheKey);
            
            if ($cachedData !== null) {
                Log::info('Third party insurance API response retrieved from cache', [
                    'plate' => $plateFormatted,
                    'national_code' => $nationalCode,
                    'cache_key' => $cacheKey,
                    'has_sanjab_response' => isset($cachedData['sanjabResponse'])
                ]);
                
                // Add cache indicator to the response
                $cachedData['from_cache'] = true;
                $cachedData['cached_at'] = Cache::get($cacheKey . '_timestamp');
                
                return $cachedData;
            }
            
            // No cached data, make API call
            Log::info('Third party insurance API cache miss, calling API', [
                'plate' => $plateFormatted,
                'national_code' => $nationalCode,
                'cache_key' => $cacheKey
            ]);
            
            $response = Http::timeout(10) // Add timeout for reliability
                ->asForm()
                ->withHeaders([
                    'accept' => 'application/json, text/plain, */*',
                    'accept-language' => 'fa',
                    'device' => 'web',
                    'deviceid' => '6',
                    'origin' => 'https://www.azki.com',
                    'referer' => 'https://www.azki.com/car-insurance/third-party-insurance',
                    'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36',
                ])
                ->post('https://www.azki.com/api/vehicleorder/sanjab/inquiry', [
                    'plate' => $plateFormatted,
                    'nationalCode' => $nationalCode,
                    'type' => '1',
                    'reasonId' => '1'
                ]);
                
            if ($response->successful()) {
                $data = $response->json();
                
                // Only cache successful responses with valid sanjabResponse
                if (isset($data['sanjabResponse']) && isset($data['messageCode']) && $data['messageCode'] == 200) {
                    // Cache for 7 days (7 * 24 * 60 = 10080 minutes)
                    Cache::put($cacheKey, $data, 10080);
                    Cache::put($cacheKey . '_timestamp', now()->toISOString(), 10080); // Store cache timestamp
                    
                    Log::info('Third party insurance API response received and cached', [
                        'status' => $response->status(),
                        'message_code' => $data['messageCode'] ?? null,
                        'has_sanjab_response' => isset($data['sanjabResponse']),
                        'cache_key' => $cacheKey,
                        'cached_until' => now()->addMinutes(10080)->toISOString()
                    ]);
                } else {
                    Log::warning('Third party insurance API response not cached - invalid or error response', [
                        'status' => $response->status(),
                        'message_code' => $data['messageCode'] ?? null,
                        'has_sanjab_response' => isset($data['sanjabResponse'])
                    ]);
                }
                
                // Add cache indicator to the response
                $data['from_cache'] = false;
                
                return $data;
            } else {
                Log::error('Third party insurance API call failed', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'plate' => $plateFormatted
                ]);
                return null;
            }
            
        } catch (Exception $e) {
            Log::error('Third party insurance API call exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'plate' => $plateFormatted ?? 'unknown'
            ]);
            return null;
        }
    }

    /**
     * Convert third party insurance API sanjabResponse to carData array format
     */
    private function formatInsuranceResponseAsCarData(?array $insuranceResponse): ?array
    {
        if (!$insuranceResponse || !isset($insuranceResponse['sanjabResponse'])) {
            return null;
        }
        
        $sanjab = $insuranceResponse['sanjabResponse'];
        
        // Format the car data as requested
        $carData = [
            'id' => $sanjab['id'] ?? '',
            'start_date' => $sanjab['startDate'] ?? '',
            'end_date' => $sanjab['endDate'] ?? '',
            'construction_year' => $sanjab['constructionYear'] ?? '',
            'cylinder' => $sanjab['cylinder'] ?? '',
            'color_title' => $sanjab['colorTitle'] ?? '',
            'is_imported' => $sanjab['isImported'] ?? false,
            'number_damage' => $sanjab['numberDamage'] ?? 0,
            'changed_owner' => $sanjab['changedOwner'] ?? false,
            'unique_code' => $sanjab['uniqueCode'] ?? '',
            'plate_installation_date' => $sanjab['plateInstallationJalaliDate'] ?? '',
            'status_code' => $sanjab['statusCode'] ?? 0,
            'end_date_gregorian' => $sanjab['endDateGregorian'] ?? '',
            'city_id' => $sanjab['cityId'] ?? '',
            'province_id' => $sanjab['provinceId'] ?? '',
            'duration_title' => $sanjab['durationIDTitle'] ?? '',
            'old_company_title' => $sanjab['oldCompanyIDTitle'] ?? '',
            'vehicle_brand_title' => $sanjab['vehicleBrandIDTitle'] ?? '',
            'vehicle_model_title' => $sanjab['vehicleModelIDTitle'] ?? '',
            'vehicle_type_title' => $sanjab['vehicleTypeIDTitle'] ?? '',
            'driver_discount_title' => $sanjab['driverDiscountIDTitle'] ?? '',
            'third_discount_title' => $sanjab['thirdDiscountIDTitle'] ?? '',
            'vehicle_usage_title' => $sanjab['vehicleUsageIDTitle'] ?? '',
            'fuel_type_title' => $sanjab['fuelTypeIDTitle'] ?? '',
            'vehicle_construction_year_title' => $sanjab['vehicleConstructionYearTitle'] ?? '',
            'third_financial_damage_title' => $sanjab['thirdFinancialDamageIDTitle'] ?? '',
            'third_life_damage_title' => $sanjab['thirdLifeDamageIDTitle'] ?? '',
            'driver_life_damage_title' => $sanjab['driverLifeDamageIDTitle'] ?? '',
        ];
        
        return $carData;
    }

    /**
     * Get preview data for the service
     */
    public function getPreviewData(array $serviceData, Service $service): array
    {
        try {
            $plateInfo = [
                'plate_part1' => $serviceData['plate_part1'] ?? '',
                'plate_letter' => $serviceData['plate_letter'] ?? '',
                'plate_part2' => $serviceData['plate_part2'] ?? '',
                'plate_part3' => $serviceData['plate_part3'] ?? '',
            ];
            
            // Call third party insurance API to get real car data
            $insuranceResponse = $this->callInsuranceApi($serviceData);
            $carData = $this->formatInsuranceResponseAsCarData($insuranceResponse);
            
            if ($carData) {
                // Successfully got data from third party insurance API
                $previewData = [
                    'carData' => $carData,
                    'insurance_status' => 'success',
                    'vehicle_brand' => $carData['vehicle_brand_title'] ?? 'نامشخص',
                    'vehicle_model' => $carData['vehicle_model_title'] ?? 'نامشخص',
                    'vehicle_type' => $carData['vehicle_type_title'] ?? 'نامشخص',
                    'construction_year' => $carData['construction_year'] ?? 'نامشخص',
                    'color_title' => $carData['color_title'] ?? 'نامشخص',
                    'old_company' => $carData['old_company_title'] ?? 'نامشخص',
                    'insurance_start_date' => $carData['start_date'] ?? 'نامشخص',
                    'insurance_end_date' => $carData['end_date'] ?? 'نامشخص',
                    'driver_discount' => $carData['driver_discount_title'] ?? 'نامشخص',
                    'third_discount' => $carData['third_discount_title'] ?? 'نامشخص',
                    'number_damage' => $carData['number_damage'] ?? 0,
                    'changed_owner' => $carData['changed_owner'] ? 'دارد' : 'ندارد',
                    'fuel_type' => $carData['fuel_type_title'] ?? 'نامشخص',
                    'vehicle_usage' => $carData['vehicle_usage_title'] ?? 'نامشخص',
                    'cylinder' => $carData['cylinder'] ?? 'نامشخص',
                    'plate_installation_date' => $carData['plate_installation_date'] ?? 'نامشخص',
                    'from_cache' => $insuranceResponse['from_cache'] ?? false,
                    'cached_at' => $insuranceResponse['cached_at'] ?? null,
                    'engagement_message' => 'اطلاعات خودروی پلاک ' . 
                                          ($plateInfo['plate_part1'] ?? '') . ' ' . 
                                          ($plateInfo['plate_letter'] ?? '') . ' ' . 
                                          ($plateInfo['plate_part2'] ?? '') . ' ' . 
                                          ($plateInfo['plate_part3'] ?? '') . 
                                          ' از سامانه بیمه شخص ثالث دریافت شد. برای دریافت سوابق کامل کیف پول خود را شارژ کنید!'
                ];
                
                $dataSource = $insuranceResponse['from_cache'] ?? false ? 'insurance_cache' : 'insurance_api';
                $cachedAt = $insuranceResponse['cached_at'] ?? null;
                
                Log::info('Third party insurance API data retrieved successfully for preview', [
                    'plate' => $this->convertPlateToInsuranceApiFormat($serviceData),
                    'vehicle_info' => [
                        'brand' => $carData['vehicle_brand_title'] ?? '',
                        'model' => $carData['vehicle_model_title'] ?? '',
                        'year' => $carData['construction_year'] ?? ''
                    ],
                    'from_cache' => $insuranceResponse['from_cache'] ?? false,
                    'cached_at' => $cachedAt
                ]);
                
                return [
                    'success' => true,
                    'preview_data' => $previewData,
                    'from_cache' => $insuranceResponse['from_cache'] ?? false,
                    'cached_at' => $cachedAt,
                    'data_source' => $dataSource
                ];
                
            } else {
                // Fallback to sample data if third party insurance API fails
                Log::warning('Third party insurance API call failed, using fallback sample data');
                
                $previewData = [
                    'insurance_status' => 'failed',
                    'vehicle_status' => 'در حال بررسی...',
                    'insurance_company' => 'در حال استعلام...',
                    'policy_status' => 'در حال بررسی...',
                    'expiry_date' => 'نامشخص',
                    'days_remaining' => 'در حال محاسبه...',
                    'coverage_amount' => 'در حال استعلام...',
                    'discount_years' => 'در حال بررسی...',
                    'engagement_message' => 'امکان دریافت اطلاعات از سامانه بیمه شخص ثالث وجود ندارد. برای مشاهده جزئیات کامل بیمه شخص ثالث خودروی پلاک ' . 
                                          ($plateInfo['plate_part1'] ?? '') . ' ' . 
                                          ($plateInfo['plate_letter'] ?? '') . ' ' . 
                                          ($plateInfo['plate_part2'] ?? '') . ' ' . 
                                          ($plateInfo['plate_part3'] ?? '') . 
                                          ' کیف پول خود را شارژ کنید!'
                ];
                
                return [
                    'success' => true,
                    'preview_data' => $previewData,
                    'from_cache' => false,
                    'data_source' => 'fallback_sample'
                ];
            }
            
        } catch (Exception $e) {
            Log::error('Error generating third party insurance preview data', [
                'error' => $e->getMessage(),
                'service_data' => $serviceData,
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'preview_data' => [],
                'message' => 'خطا در تولید پیش‌نمایش اطلاعات'
            ];
        }
    }

    /**
     * Clear third party insurance cache for specific plate and national code (for debugging/admin use)
     */
    private function clearInsuranceCache(array $serviceData): bool
    {
        try {
            $cacheKey = $this->generateInsuranceCacheKey($serviceData);
            $timestampKey = $cacheKey . '_timestamp';
            
            $cleared = Cache::forget($cacheKey);
            Cache::forget($timestampKey);
            
            Log::info('Third party insurance cache cleared', [
                'cache_key' => $cacheKey,
                'cleared' => $cleared,
                'plate' => $this->convertPlateToInsuranceApiFormat($serviceData)
            ]);
            
            return $cleared;
        } catch (Exception $e) {
            Log::error('Failed to clear third party insurance cache', [
                'error' => $e->getMessage(),
                'plate' => $this->convertPlateToInsuranceApiFormat($serviceData)
            ]);
            return false;
        }
    }

    /**
     * Check if this service supports preview
     */
    public function supportsPreview(): bool
    {
        return true;
    }

    /**
     * Get the preview template name for this service
     */
    public function getPreviewTemplate(): string
    {
        return 'front.services.custom.third-party-insurance-history.preview';
    }
} 