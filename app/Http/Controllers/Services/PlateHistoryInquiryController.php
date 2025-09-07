<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Services\BaseFinnotechController;
use App\Models\Service;
use App\Rules\IranianNationalCode;
use App\Services\Finnotech\FinnotechService;
use App\Services\Finnotech\SmsAuthorizationService;
use Illuminate\Support\Facades\Log;

class PlateHistoryInquiryController extends BaseFinnotechController
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
        $this->apiEndpoint = 'plate_history_inquiry';
        $this->scope = 'vehicle:plate-number-history:get';
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
            return ['status' => 'failed', 'message' => 'خطا در دریافت تاریخچه پلاک'];
        }

        $result = $responseData['result'];
        $plateHistory = $result['plateHistory'] ?? [];

        $formattedHistory = [];
        foreach ($plateHistory as $history) {
            $formattedHistory[] = [
                'vehicle_system' => $history['vehicleSystem'] ?? '',
                'vehicle_type' => $history['vehicleType'] ?? '',
                'install_date' => $history['installDate'] ?? '',
                'detach_date' => $history['detachDate'] ?? '',
                'vehicle_model' => $history['vehicleModel'] ?? '',
                'duration' => $this->calculateDuration($history['installDate'] ?? '', $history['detachDate'] ?? ''),
                'is_current' => empty($history['detachDate']),
                'formatted_install_date' => $this->formatPersianDate($history['installDate'] ?? ''),
                'formatted_detach_date' => $this->formatPersianDate($history['detachDate'] ?? ''),
            ];
        }

        // Sort by install date, most recent first
        usort($formattedHistory, function($a, $b) {
            return strcmp($b['install_date'], $a['install_date']);
        });

        return [
            'status' => 'success',
            'data' => [
                'track_id' => $responseData['trackId'] ?? '',
                'response_code' => $responseData['responseCode'] ?? '',
                'plate_info' => [
                    'plate_status' => $result['plateStatus'] ?? '',
                    'trace_plate' => $result['tracePlate'] ?? '',
                ],
                'history' => $formattedHistory,
                'history_count' => count($formattedHistory),
                'current_vehicle' => $this->getCurrentVehicle($formattedHistory),
                'statistics' => $this->generateStatistics($formattedHistory),
                'processed_date' => now()->format('Y/m/d H:i:s'),
                'summary' => $this->generateSummary($formattedHistory, $result['plateStatus'] ?? ''),
            ]
        ];
    }



    /**
     * Calculate duration between two dates
     */
    private function calculateDuration(string $startDate, string $endDate): string
    {
        if (empty($startDate)) return '';
        
        try {
            // Convert Persian dates to timestamps
            $start = $this->parsePersianDate($startDate);
            $end = empty($endDate) ? time() : $this->parsePersianDate($endDate);
            
            $diff = $end - $start;
            $years = floor($diff / (365 * 24 * 3600));
            $months = floor(($diff % (365 * 24 * 3600)) / (30 * 24 * 3600));
            
            if ($years > 0) {
                return $months > 0 ? "{$years} سال و {$months} ماه" : "{$years} سال";
            } elseif ($months > 0) {
                return "{$months} ماه";
            } else {
                $days = floor($diff / (24 * 3600));
                return "{$days} روز";
            }
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * Parse Persian date to timestamp
     */
    private function parsePersianDate(string $date): int
    {
        // Convert Persian date format "1378/02/05" to timestamp
        $parts = explode('/', $date);
        if (count($parts) === 3) {
            return mktime(0, 0, 0, (int)$parts[1], (int)$parts[2], (int)$parts[0]);
        }
        return 0;
    }

    /**
     * Format Persian date
     */
    private function formatPersianDate(string $date): string
    {
        if (empty($date)) return 'در حال حاضر';
        return $date;
    }

    /**
     * Get current vehicle
     */
    private function getCurrentVehicle(array $history): ?array
    {
        foreach ($history as $item) {
            if ($item['is_current']) {
                return $item;
            }
        }
        return null;
    }

    /**
     * Generate statistics
     */
    private function generateStatistics(array $history): array
    {
        $vehicleSystems = array_count_values(array_column($history, 'vehicle_system'));
        $totalVehicles = count($history);
        $currentVehicle = $this->getCurrentVehicle($history);
        
        return [
            'total_vehicles' => $totalVehicles,
            'has_current_vehicle' => !is_null($currentVehicle),
            'most_used_system' => $totalVehicles > 0 ? array_key_first($vehicleSystems) : '',
            'systems_used' => array_keys($vehicleSystems),
            'average_ownership_duration' => $this->calculateAverageOwnership($history),
        ];
    }

    /**
     * Calculate average ownership duration
     */
    private function calculateAverageOwnership(array $history): string
    {
        $completedOwnerships = array_filter($history, fn($h) => !$h['is_current']);
        
        if (empty($completedOwnerships)) {
            return 'نامشخص';
        }

        $totalDays = 0;
        $count = 0;
        
        foreach ($completedOwnerships as $ownership) {
            if (!empty($ownership['install_date']) && !empty($ownership['detach_date'])) {
                $start = $this->parsePersianDate($ownership['install_date']);
                $end = $this->parsePersianDate($ownership['detach_date']);
                $totalDays += ($end - $start) / (24 * 3600);
                $count++;
            }
        }
        
        if ($count === 0) return 'نامشخص';
        
        $avgDays = $totalDays / $count;
        $avgYears = floor($avgDays / 365);
        $avgMonths = floor(($avgDays % 365) / 30);
        
        if ($avgYears > 0) {
            return $avgMonths > 0 ? "حدود {$avgYears} سال و {$avgMonths} ماه" : "حدود {$avgYears} سال";
        } elseif ($avgMonths > 0) {
            return "حدود {$avgMonths} ماه";
        } else {
            return "کمتر از یک ماه";
        }
    }

    /**
     * Generate summary text
     */
    private function generateSummary(array $history, string $plateStatus): string
    {
        $count = count($history);
        $currentVehicle = $this->getCurrentVehicle($history);
        
        if ($count === 0) {
            return "برای این پلاک هیچ تاریخچه‌ای یافت نشد.";
        }
        
        $summary = "این پلاک روی {$count} خودرو نصب شده است. ";
        
        if ($currentVehicle) {
            $summary .= "در حال حاضر روی {$currentVehicle['vehicle_system']} {$currentVehicle['vehicle_type']} نصب است.";
        } else {
            $summary .= "در حال حاضر روی هیچ خودرویی نصب نیست.";
        }
        
        return $summary;
    }

    /**
     * Show service result using specific plate history view
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

        return view('front.services.results.plate-history-inquiry', [
            'service' => $service,
            'data' => $result->output_data,
            'inputData' => $result->input_data,
            'resultId' => $resultId,
        ]);
    }
} 