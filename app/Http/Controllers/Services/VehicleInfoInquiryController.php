<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Services\BaseFinnotechController;
use App\Contracts\ServicePreviewInterface;
use App\Models\Service;
use App\Rules\IranianNationalCode;
use App\Services\Finnotech\FinnotechService;
use App\Services\Finnotech\SmsAuthorizationService;
use Illuminate\Support\Facades\Log;

class VehicleInfoInquiryController extends BaseFinnotechController implements ServicePreviewInterface
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
        $this->apiEndpoint = 'vehicle_info_inquiry';
        $this->scope = 'vehicle:vehicle-info-inquiry:get';
        $this->requiresSms = false;
        $this->httpMethod = 'GET';

        $this->requiredFields = ['plate_part1', 'plate_letter', 'plate_part2', 'plate_part3', 'national_code'];
        
        $this->validationRules = [
            'plate_part1' => 'required|string|digits:2',
            'plate_letter' => 'required|string',
            'plate_part2' => 'required|string|digits:3',
            'plate_part3' => 'required|string|digits:2',
            'national_code' => ['required', 'string', new IranianNationalCode()],
        ];

        $this->validationMessages = [
            'plate_part1.required' => 'قسمت اول پلاک الزامی است',
            'plate_part1.digits' => 'قسمت اول پلاک باید ۲ رقم باشد',
            'plate_letter.required' => 'حرف پلاک الزامی است',
            'plate_part2.required' => 'قسمت دوم پلاک الزامی است',
            'plate_part2.digits' => 'قسمت دوم پلاک باید ۳ رقم باشد',
            'plate_part3.required' => 'قسمت آخر پلاک الزامی است',
            'plate_part3.digits' => 'قسمت آخر پلاک باید ۲ رقم باشد',
            'national_code.required' => 'کد ملی الزامی است',
        ];
    }

    /**
     * Prepare API parameters from service data
     */
    protected function prepareApiParameters(array $serviceData): array
    {
        // Convert plate parts to 9-digit format expected by API
        $plateNumber = $this->convertPlatePartsToApiFormat($serviceData);
        
        $params = [
            'nationalId' => $serviceData['national_code'],
            'plateNumber' => $plateNumber,
        ];

        return $this->addTrackId($params);
    }

    /**
     * Convert plate parts to API format (9 digits)
     */
    private function convertPlatePartsToApiFormat(array $serviceData): string
    {
        $part1 = str_pad($serviceData['plate_part1'], 2, '0', STR_PAD_LEFT);
        $letter = str_pad($this->convertLetterToNumber($serviceData['plate_letter']), 2, '0', STR_PAD_LEFT);
        $part2 = str_pad($serviceData['plate_part2'], 3, '0', STR_PAD_LEFT);
        $part3 = str_pad($serviceData['plate_part3'], 2, '0', STR_PAD_LEFT);
        
        return $part1 . $letter . $part2 . $part3;
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
            'ه' => '31', 'ی' => '32', 'معلولین' => '33', 'تشریفات' => '34'
        ];

        return $letterMap[$letter] ?? '01';
    }

    /**
     * Format API response data
     */
    protected function formatResponseData(array $responseData): array
    {
        if (!isset($responseData['result'])) {
            return ['status' => 'failed', 'message' => 'خطا در دریافت اطلاعات خودرو'];
        }

        $result = $responseData['result'];

        return [
            'status' => 'success',
            'data' => [
                'track_id' => $responseData['trackId'] ?? '',
                'response_code' => $responseData['responseCode'] ?? '',
                'vehicle_specs' => [
                    'axel_no' => $result['axelNo'] ?? '',
                    'wheel_no' => $result['wheelNo'] ?? '',
                    'capacity' => $result['capacity'] ?? '',
                    'cylinder_no' => $result['cylinderNo'] ?? '',
                    'main_color' => $result['mainColor'] ?? '',
                    'second_color' => $result['secondColor'] ?? '',
                ],
                'usage_info' => [
                    'usage_code_by_insurance_company' => $result['usageCodeByInsuranceCompany'] ?? '',
                    'usage_code_by_central_insurance' => $result['usageCodeByCentralInsurance'] ?? '',
                    'usage_name_by_insurance_company' => $result['usageNameByInsuranceCompany'] ?? '',
                    'usage_name_by_naja' => $result['usageNameByNaja'] ?? '',
                    'sub_usage' => $result['subUsage'] ?? '',
                ],
                'vehicle_details' => [
                    'model_by_naja' => $result['modelByNaja'] ?? '',
                    'system_by_insurance_company' => $result['systemByInsuranceCompany'] ?? '',
                    'system_name_by_naja' => $result['systemNameByNaja'] ?? '',
                    'tip_by_central_insurance' => $result['tipByCentralInsurance'] ?? '',
                    'tip_by_naja' => $result['tipByNaja'] ?? '',
                    'tip_code_by_company' => $result['tipCodeByCompany'] ?? '',
                    'vehicle_system_code' => $result['vehicleSystemCode'] ?? '',
                    'vehicle_group_code' => $result['vehicleGroupCode'] ?? '',
                    'vehicle_type_name_by_insurance_company' => $result['vehicleTypeNameByInsuranceCompany'] ?? '',
                    'vehicle_type_code_by_insurance_company' => $result['vehicleTypeCodeByInsuranceCompany'] ?? '',
                ],
                'insurance_info' => [
                    'insurance_unique_code' => $result['insuranceUniqueCode'] ?? '',
                    'insurance_print_number' => $result['insurancePrintNumber'] ?? '',
                    'insurance_company_title' => $result['insuranceCompanyTitle'] ?? '',
                    'insurance_company_code' => $result['insuranceCompanyCode'] ?? '',
                    'begin_date' => $result['beginDate'] ?? '',
                    'end_date' => $result['endDate'] ?? '',
                    'insurance_duration' => $this->calculateInsuranceDuration($result['beginDate'] ?? '', $result['endDate'] ?? ''),
                    'is_active' => $this->isInsuranceActive($result['endDate'] ?? ''),
                ],
                'discount_info' => [
                    'discount_life_year_number' => $result['discountLifeYearNumber'] ?? '0',
                    'discount_person_year_number' => $result['discountPersonYearNumber'] ?? '0',
                    'discount_financial_year_number' => $result['discountFinancialYearNumber'] ?? '0',
                    'discount_life_year_percent' => $result['discountLifeYearPercent'] ?? '0',
                    'discount_person_year_percent' => $result['discountPersonYearPercent'] ?? '0',
                    'discount_financial_year_percent' => $result['discountFinancialYearPercent'] ?? '0',
                ],
                'identification' => [
                    'chassis_number' => $result['chassisNumber'] ?? '',
                    'engine_number' => $result['engineNumber'] ?? '',
                    'vin' => $result['vin'] ?? '',
                    'plate_install_date' => $result['plateInstallDate'] ?? '',
                ],
                'processed_date' => now()->format('Y/m/d H:i:s'),
                'summary' => $this->generateSummary($result),
                'recommendations' => $this->generateRecommendations($result),
            ]
        ];
    }

    /**
     * Calculate insurance duration
     */
    private function calculateInsuranceDuration(string $beginDate, string $endDate): string
    {
        if (empty($beginDate) || empty($endDate)) {
            return 'نامشخص';
        }

        try {
            $start = $this->parsePersianDate($beginDate);
            $end = $this->parsePersianDate($endDate);
            
            $diff = $end - $start;
            $days = floor($diff / (24 * 3600));
            $months = floor($days / 30);
            $years = floor($months / 12);
            
            if ($years > 0) {
                $remainingMonths = $months % 12;
                return $remainingMonths > 0 ? "{$years} سال و {$remainingMonths} ماه" : "{$years} سال";
            } elseif ($months > 0) {
                return "{$months} ماه";
            } else {
                return "{$days} روز";
            }
        } catch (\Exception $e) {
            return 'نامشخص';
        }
    }

    /**
     * Check if insurance is active
     */
    private function isInsuranceActive(string $endDate): bool
    {
        if (empty($endDate)) return false;
        
        try {
            $end = $this->parsePersianDate($endDate);
            return $end > time();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Parse Persian date to timestamp
     */
    private function parsePersianDate(string $date): int
    {
        // Convert Persian date format "1401/12/21" to timestamp
        $parts = explode('/', $date);
        if (count($parts) === 3) {
            return mktime(0, 0, 0, (int)$parts[1], (int)$parts[2], (int)$parts[0]);
        }
        return 0;
    }

    /**
     * Generate summary text
     */
    private function generateSummary(array $result): string
    {
        $system = $result['systemNameByNaja'] ?? $result['systemByInsuranceCompany'] ?? '';
        $tip = $result['tipByNaja'] ?? $result['tipByCentralInsurance'] ?? '';
        $model = $result['modelByNaja'] ?? '';
        $color = $result['mainColor'] ?? '';
        
        $summary = '';
        
        if (!empty($system)) {
            $summary .= $system;
            if (!empty($tip)) $summary .= ' ' . $tip;
            if (!empty($model)) $summary .= ' مدل ' . $model;
            if (!empty($color)) $summary .= ' به رنگ ' . $color;
        } else {
            $summary = 'اطلاعات خودرو دریافت شد';
        }
        
        return $summary;
    }

    /**
     * Generate recommendations
     */
    private function generateRecommendations(array $result): array
    {
        $recommendations = [];
        
        // Insurance recommendations
        $isInsuranceActive = $this->isInsuranceActive($result['endDate'] ?? '');
        if (!$isInsuranceActive) {
            $recommendations[] = 'بیمه نامه خودرو منقضی شده است - فوراً اقدام به تمدید کنید';
        } else {
            $endDate = $result['endDate'] ?? '';
            if (!empty($endDate)) {
                try {
                    $end = $this->parsePersianDate($endDate);
                    $daysLeft = floor(($end - time()) / (24 * 3600));
                    if ($daysLeft <= 30) {
                        $recommendations[] = "بیمه نامه تا {$daysLeft} روز دیگر منقضی می‌شود - برای تمدید آماده شوید";
                    }
                } catch (\Exception $e) {
                    // Handle date parsing error
                }
            }
        }
        
        // Discount recommendations
        $lifeDiscount = (int)($result['discountLifeYearNumber'] ?? 0);
        $personDiscount = (int)($result['discountPersonYearNumber'] ?? 0);
        $financialDiscount = (int)($result['discountFinancialYearNumber'] ?? 0);
        
        if ($lifeDiscount > 0 || $personDiscount > 0 || $financialDiscount > 0) {
            $recommendations[] = 'شما دارای تخفیف بیمه هستید - در تمدید بیمه از این تخفیفات استفاده کنید';
        }
        
        // General recommendations
        $recommendations[] = 'اطلاعات خودرو را برای استفاده در امور مختلف نگهداری کنید';
        $recommendations[] = 'در صورت تغییر مشخصات خودرو، اطلاعات را به‌روزرسانی کنید';
        
        return $recommendations;
    }

    /**
     * Show service result using specific vehicle info inquiry view
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

        return view('front.services.results.vehicle-info-inquiry', [
            'service' => $service,
            'data' => $result->output_data,
            'inputData' => $result->input_data,
            'resultId' => $resultId,
        ]);
    }

    /**
     * Get preview data for the service
     */
    public function getPreviewData(array $serviceData, Service $service): array
    {
        try {
            // Sample preview data for vehicle info inquiry
            $plateInfo = [
                'plate_part1' => $serviceData['plate_part1'] ?? '',
                'plate_letter' => $serviceData['plate_letter'] ?? '',
                'plate_part2' => $serviceData['plate_part2'] ?? '',
                'plate_part3' => $serviceData['plate_part3'] ?? '',
            ];
            
            $previewData = [
                'vehicle_manufacturer' => 'سایپا',
                'vehicle_model' => 'پراید',
                'model_year' => '۱۳۹۸',
                'color' => 'سفید',
                'engine_capacity' => '۱۰۰۰ سی‌سی',
                'fuel_type' => 'بنزینی',
                'chassis_number' => 'NAS***********',
                'engine_number' => 'XU7***********',
                'engagement_message' => 'برای مشاهده اطلاعات کامل خودروی پلاک ' . 
                                      ($plateInfo['plate_part1'] ?? '') . ' ' . 
                                      ($plateInfo['plate_letter'] ?? '') . ' ' . 
                                      ($plateInfo['plate_part2'] ?? '') . ' ' . 
                                      ($plateInfo['plate_part3'] ?? '') . 
                                      ' کیف پول خود را شارژ کنید!'
            ];
            
            return [
                'success' => true,
                'preview_data' => $previewData,
                'from_cache' => false
            ];
            
        } catch (\Exception $e) {
            Log::error('Error generating vehicle info preview data', [
                'error' => $e->getMessage(),
                'service_data' => $serviceData
            ]);
            
            return [
                'success' => false,
                'preview_data' => [],
                'message' => 'خطا در تولید پیش‌نمایش اطلاعات'
            ];
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
        return 'services.preview';
    }
} 