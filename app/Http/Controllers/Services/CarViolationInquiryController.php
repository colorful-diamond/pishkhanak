<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Services\BaseFinnotechController;
use App\Contracts\ServicePreviewInterface;
use App\Models\Service;
use App\Services\Finnotech\FinnotechService;
use App\Services\Finnotech\SmsAuthorizationService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class CarViolationInquiryController extends BaseFinnotechController implements ServicePreviewInterface
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
        $this->apiEndpoint = 'car_violation_inquiry';
        $this->scope = 'billing:driving-offense-inquiry:get';
        $this->requiresSms = false;
        $this->httpMethod = 'GET';

        $this->requiredFields = ['plate_part1', 'plate_letter', 'plate_part2', 'plate_part3', 'mobile', 'national_code'];
        
        $this->validationRules = [
            'plate_part1' => 'required|string|digits:2',
            'plate_letter' => 'required|string',
            'plate_part2' => 'required|string|digits:3',
            'plate_part3' => 'required|string|digits:2',
            'mobile' => 'required|string|regex:/^09[0-9]{9}$/',
            'national_code' => 'required|string|digits:10',
        ];

        $this->validationMessages = [
            'plate_part1.required' => 'قسمت اول پلاک الزامی است',
            'plate_part1.digits' => 'قسمت اول پلاک باید ۲ رقم باشد',
            'plate_letter.required' => 'حرف پلاک الزامی است',
            'plate_part2.required' => 'قسمت دوم پلاک الزامی است',
            'plate_part2.digits' => 'قسمت دوم پلاک باید ۳ رقم باشد',
            'plate_part3.required' => 'قسمت آخر پلاک الزامی است',
            'plate_part3.digits' => 'قسمت آخر پلاک باید ۲ رقم باشد',
            'mobile.required' => 'شماره موبایل الزامی است',
            'mobile.regex' => 'شماره موبایل باید ۱۱ رقم و با ۰۹ شروع شود',
            'national_code.required' => 'کد ملی الزامی است',
            'national_code.digits' => 'کد ملی باید ۱۰ رقم باشد',
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
            'version' => '2',
            'mobile' => $serviceData['mobile'],
            'nationalID' => $serviceData['national_code'],
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
            return ['status' => 'failed', 'message' => 'خطا در دریافت اطلاعات خلافی'];
        }

        $result = $responseData['result'];
        $bills = $result['Bills'] ?? [];
        $totalAmount = $result['TotalAmount'] ?? 0;
        
        // Convert total amount from rial to toman
        $totalAmountToman = intval($totalAmount / 10);

        $formattedBills = [];
        foreach ($bills as $bill) {
            $priceRial = $bill['price'] ?? 0;
            $priceToman = intval($priceRial / 10); // Convert rial to toman
            
            $formattedBills[] = [
                'id' => $bill['id'] ?? '',
                'type' => $bill['type'] ?? '',
                'description' => $bill['description'] ?? '',
                'code' => $bill['code'] ?? '',
                'price' => $priceToman, // Store as toman
                'price_formatted' => number_format($priceToman) . ' تومان',
                'city' => $bill['city'] ?? '',
                'location' => $bill['location'] ?? '',
                'date' => $bill['date'] ?? '',
                'date_persian' => $this->formatDateToPersian($bill['date'] ?? ''),
                'serial' => $bill['serial'] ?? '',
                'license' => $bill['license'] ?? '',
                'bill_id' => $bill['billId'] ?? '',
                'payment_id' => $bill['paymentId'] ?? '',
                'normalized_date' => $bill['normalizedDate'] ?? '',
                'is_payable' => $bill['isPayable'] ?? false,
                'has_image' => $bill['hasImage'] ?? false,
                'policeman_code' => $bill['policemanCode'] ?? '',
                'severity' => $this->getViolationSeverity($priceToman), // Use toman price for severity
                'status_color' => ($bill['isPayable'] ?? false) ? 'red' : 'orange',
                'status_text' => ($bill['isPayable'] ?? false) ? 'قابل پرداخت' : 'غیرقابل پرداخت',
            ];
        }

        return [
            'status' => 'success',
            'data' => [
                'track_id' => $responseData['trackId'] ?? '',
                'violations' => $formattedBills,
                'total_amount' => $totalAmountToman, // Store as toman
                'total_amount_formatted' => number_format($totalAmountToman) . ' تومان',
                'violation_count' => count($formattedBills),
                'has_violations' => !empty($formattedBills),
                'processed_date' => now()->format('Y/m/d H:i:s'),
                'summary' => $this->generateSummary($formattedBills, $totalAmountToman),
                'statistics' => $this->generateStatistics($formattedBills),
            ]
        ];
    }

    /**
     * Generate summary text
     */
    private function generateSummary(array $violations, int $totalAmount): string
    {
        $count = count($violations);
        
        if ($count === 0) {
            return 'برای این خودرو هیچ خلافی ثبت نشده است.';
        }

        $payableCount = count(array_filter($violations, fn($v) => $v['is_payable']));
        $payableAmount = array_sum(array_map(fn($v) => $v['is_payable'] ? $v['price'] : 0, $violations));

        return "تعداد {$count} خلافی ثبت شده - {$payableCount} مورد قابل پرداخت (مبلغ: " . number_format($payableAmount) . " تومان)";
    }

    /**
     * Generate statistics
     */
    private function generateStatistics(array $violations): array
    {
        $severityCount = ['minor' => 0, 'moderate' => 0, 'severe' => 0];
        $payableCount = 0;
        $withImageCount = 0;

        foreach ($violations as $violation) {
            $severityCount[$violation['severity']]++;
            if ($violation['is_payable']) $payableCount++;
            if ($violation['has_image']) $withImageCount++;
        }

        return [
            'total_count' => count($violations),
            'payable_count' => $payableCount,
            'with_image_count' => $withImageCount,
            'severity_breakdown' => $severityCount,
        ];
    }

    /**
     * Format date to Persian
     */
    private function formatDateToPersian(string $date): string
    {
        if (empty($date)) return '';
        return str_replace(['/', '-'], ['/', ''], $date);
    }

    /**
     * Get violation severity based on price (in toman)
     */
    private function getViolationSeverity(int $price): string
    {
        // Thresholds are now in toman (previously were in rial)
        if ($price >= 100000) return 'severe';        // 100,000 toman = 1,000,000 rial
        elseif ($price >= 50000) return 'moderate';   // 50,000 toman = 500,000 rial
        else return 'minor';                          // Less than 50,000 toman
    }

    /**
     * Show service result using specific car violations view
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

        return view('front.services.results.car-violation-inquiry', [
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
            // Call third party API to get vehicle information
            $carApiResult = $this->callVehicleApi($serviceData);
            
            $previewData = [];
            
            if ($carApiResult) {
                $previewData = [
                    'violation_status' => 'success',
                    'carData' => $carApiResult,
                    'vehicle_brand' => $carApiResult['data']['brand'] ?? 'نامشخص',
                    'vehicle_model' => $carApiResult['data']['model'] ?? '',
                    'construction_year' => $carApiResult['data']['year'] ?? 'نامشخص',
                    'from_cache' => $carApiResult['from_cache'] ?? false,
                    'cached_at' => $carApiResult['cached_at'] ?? null,
                ];
            } else {
                $previewData = [
                    'violation_status' => 'failed',
                    'carData' => null,
                    'vehicle_brand' => 'نامشخص',
                    'vehicle_model' => '',
                    'construction_year' => 'نامشخص',
                    'from_cache' => false,
                ];
            }
            
            return [
                'success' => true,
                'preview_data' => $previewData,
                'from_cache' => $previewData['from_cache'] ?? false
            ];
            
        } catch (\Exception $e) {
            Log::error('Error generating car violation preview data', [
                'error' => $e->getMessage(),
                'service_data' => $serviceData
            ]);
            
            return [
                'success' => false,
                'preview_data' => [
                    'violation_status' => 'failed',
                    'carData' => null,
                    'vehicle_brand' => 'نامشخص',
                    'vehicle_model' => '',
                    'construction_year' => 'نامشخص',
                ],
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

    /**
     * Call third party vehicle API to get car information
     */
    private function callVehicleApi(array $serviceData): ?array
    {
        try {
            $plateFormatted = $this->convertPlateToVehicleApiFormat($serviceData);
            $nationalCode = $serviceData['national_code'] ?? '';
            
            // Generate cache key
            $cacheKey = $this->generateVehicleCacheKey($serviceData);
            
            // Check cache first (7 days = 10,080 minutes)
            $cachedResult = Cache::get($cacheKey);
            if ($cachedResult) {
                Log::info('Vehicle API cache hit', [
                    'plate' => $plateFormatted,
                    'cache_key' => $cacheKey
                ]);
                
                return array_merge($cachedResult, [
                    'from_cache' => true,
                    'cached_at' => Cache::get($cacheKey . '_timestamp')
                ]);
            }
            
            Log::info('Vehicle API call initiated', [
                'plate' => $plateFormatted,
                'national_code_length' => strlen($nationalCode),
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
                
                Log::info('Vehicle API response received', [
                    'status' => $response->status(),
                    'has_data' => isset($data['result']),
                    'message_code' => $data['messageCode'] ?? null
                ]);
                
                // Only cache successful responses with valid data
                if (isset($data['messageCode']) && $data['messageCode'] == 200 && isset($data['result']['sanjabResponse'])) {
                    $sanjabData = $data['result']['sanjabResponse'];
                    
                    $formattedResult = [
                        'status' => 'success',
                        'data' => [
                            'brand' => $sanjabData['brandTitle'] ?? 'نامشخص',
                            'model' => $sanjabData['tipTitle'] ?? '',
                            'year' => $sanjabData['modelYear'] ?? 'نامشخص',
                            'color' => $sanjabData['colorTitle'] ?? 'نامشخص',
                            'usage' => $sanjabData['usageTitle'] ?? 'نامشخص',
                            'fuel_type' => $sanjabData['fuelTitle'] ?? 'نامشخص',
                        ],
                        'raw_response' => $data
                    ];
                    
                    // Cache for 7 days (10,080 minutes)
                    try {
                        Cache::put($cacheKey, $formattedResult, 10080);
                        Cache::put($cacheKey . '_timestamp', now()->toISOString(), 10080);
                        
                        Log::info('Vehicle API response cached successfully', [
                            'cache_key' => $cacheKey,
                            'cached_until' => now()->addMinutes(10080)->toISOString()
                        ]);
                    } catch (\Exception $cacheError) {
                        Log::warning('Failed to cache vehicle API response', [
                            'error' => $cacheError->getMessage(),
                            'cache_key' => $cacheKey
                        ]);
                        // Continue without caching - the API call was still successful
                    }
                    
                    return array_merge($formattedResult, [
                        'from_cache' => false,
                        'cached_at' => now()->toISOString()
                    ]);
                } else {
                    Log::warning('Vehicle API returned unsuccessful response', [
                        'message_code' => $data['messageCode'] ?? 'unknown',
                        'response_data' => $data
                    ]);
                    
                    return null;
                }
            } else {
                Log::error('Vehicle API request failed', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                
                return null;
            }
            
        } catch (\Exception $e) {
            Log::error('Vehicle API call exception', [
                'error' => $e->getMessage(),
                'service_data' => $serviceData
            ]);
            
            return null;
        }
    }

    /**
     * Convert plate parts to vehicle API format
     */
    private function convertPlateToVehicleApiFormat(array $serviceData): string
    {
        $part1 = $serviceData['plate_part1'] ?? '';
        $letter = $serviceData['plate_letter'] ?? '';
        $part2 = $serviceData['plate_part2'] ?? '';
        $part3 = $serviceData['plate_part3'] ?? '';
        
        // Format: A-ir36-784-ط-89
        return "A-ir{$part3}-{$part2}-{$letter}-{$part1}";
    }

    /**
     * Generate cache key for vehicle API (same as insurance API since it's the same data)
     */
    private function generateVehicleCacheKey(array $serviceData): string
    {
        $plateFormatted = $this->convertPlateToVehicleApiFormat($serviceData);
        $nationalCode = $serviceData['national_code'] ?? '';
        
        // Use same cache key format as insurance service to share cached data
        return 'insurance_api:' . md5($plateFormatted . ':' . $nationalCode);
    }
} 