<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Services\BaseFinnotechController;
use App\Models\Service;
use App\Services\Finnotech\FinnotechService;
use App\Services\Finnotech\SmsAuthorizationService;
use Illuminate\Support\Facades\Log;

class TollRoadInquiryController extends BaseFinnotechController
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
        $this->apiEndpoint = 'toll_road_inquiry';
        $this->scope = 'ecity:freeway-toll-inquiry:get';
        $this->requiresSms = false;
        $this->httpMethod = 'GET';

        $this->requiredFields = ['plate_part1', 'plate_letter', 'plate_part2', 'plate_part3'];
        
        $this->validationRules = [
            'plate_part1' => 'required|string|digits:2',
            'plate_letter' => 'required|string',
            'plate_part2' => 'required|string|digits:3',
            'plate_part3' => 'required|string|digits:2',
        ];

        $this->validationMessages = [
            'plate_part1.required' => 'قسمت اول پلاک الزامی است',
            'plate_part1.digits' => 'قسمت اول پلاک باید ۲ رقم باشد',
            'plate_letter.required' => 'حرف پلاک الزامی است',
            'plate_part2.required' => 'قسمت دوم پلاک الزامی است',
            'plate_part2.digits' => 'قسمت دوم پلاک باید ۳ رقم باشد',
            'plate_part3.required' => 'قسمت آخر پلاک الزامی است',
            'plate_part3.digits' => 'قسمت آخر پلاک باید ۲ رقم باشد',
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
            return ['status' => 'failed', 'message' => 'خطا در دریافت اطلاعات عوارض آزادراهی'];
        }

        $result = $responseData['result'];
        $bills = $result['bills'] ?? [];
        $totalAmount = $result['totalAmount'] ?? 0;

        $formattedBills = [];
        foreach ($bills as $bill) {
            $priceRial = $bill['price'] ?? 0;
            $priceToman = intval($priceRial / 10); // Convert rial to toman
            
            $formattedBills[] = [
                'payment_id' => $bill['paymentId'] ?? '',
                'date' => $bill['date'] ?? '',
                'date_persian' => $this->formatDateToPersian($bill['date'] ?? ''),
                'price' => $priceToman, // Store as toman
                'price_formatted' => number_format($priceToman) . ' تومان',
                'gateway' => $bill['gateway'] ?? null,
                'freeway' => $bill['freeway'] ?? '',
                'freeway_name' => $this->getFreewayName($bill['freeway'] ?? ''),
                'status' => 'unpaid', // Assuming unpaid since it's a toll inquiry
                'status_color' => 'red',
                'status_text' => 'پرداخت نشده',
            ];
        }
        
        // Convert total amount from rial to toman
        $totalAmountToman = intval($totalAmount / 10);

        return [
            'status' => 'success',
            'data' => [
                'track_id' => $responseData['trackId'] ?? '',
                'bills' => $formattedBills,
                'total_amount' => $totalAmountToman, // Store as toman
                'total_amount_formatted' => number_format($totalAmountToman) . ' تومان',
                'bill_count' => count($formattedBills),
                'has_bills' => !empty($formattedBills),
                'processed_date' => now()->format('Y/m/d H:i:s'),
                'summary' => $this->generateSummary($formattedBills, $totalAmountToman),
                'statistics' => $this->generateStatistics($formattedBills),
            ]
        ];
    }

    /**
     * Format date to Persian
     */
    private function formatDateToPersian(string $date): string
    {
        if (empty($date)) return '';
        try {
            $carbonDate = \Carbon\Carbon::parse($date);
            return \Morilog\Jalali\Jalalian::fromCarbon($carbonDate)->format('Y/m/d H:i');
        } catch (\Exception $e) {
            return $date;
        }
    }

    /**
     * Get freeway name by code
     */
    private function getFreewayName(string $code): string
    {
        $freewayNames = [
            '21' => 'آزادراه تهران-قم',
            '22' => 'آزادراه تهران-کرج',
            '23' => 'آزادراه کرج-قزوین',
            '24' => 'آزادراه قزوین-رشت',
            '25' => 'آزادراه تهران-ساوه',
            // Add more freeway mappings as needed
        ];

        return $freewayNames[$code] ?? "آزادراه شماره {$code}";
    }

    /**
     * Generate summary text
     */
    private function generateSummary(array $bills, int $totalAmount): string
    {
        $count = count($bills);
        
        if ($count === 0) {
            return 'برای این خودرو هیچ عوارض آزادراهی ثبت نشده است.';
        }

        return "تعداد {$count} عوارض آزادراهی ثبت شده - مجموع: " . number_format($totalAmount) . " تومان";
    }

    /**
     * Generate statistics
     */
    private function generateStatistics(array $bills): array
    {
        $freewayCount = [];
        $monthlyCount = [];

        foreach ($bills as $bill) {
            $freeway = $bill['freeway_name'];
            $freewayCount[$freeway] = ($freewayCount[$freeway] ?? 0) + 1;

            try {
                $month = \Carbon\Carbon::parse($bill['date'])->format('Y-m');
                $monthlyCount[$month] = ($monthlyCount[$month] ?? 0) + 1;
            } catch (\Exception $e) {
                // Ignore date parsing errors
            }
        }

        return [
            'total_count' => count($bills),
            'freeway_breakdown' => $freewayCount,
            'monthly_breakdown' => $monthlyCount,
        ];
    }

    /**
     * Show service result using specific toll road view
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

        return view('front.services.results.toll-road-inquiry', [
            'service' => $service,
            'data' => $result->output_data,
            'inputData' => $result->input_data,
            'resultId' => $resultId,
        ]);
    }
} 