<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Services\BaseFinnotechController;
use App\Models\Service;
use App\Rules\IranianMobile;
use App\Rules\IranianNationalCode;
use App\Services\Finnotech\FinnotechService;
use App\Services\Finnotech\SmsAuthorizationService;
use Illuminate\Support\Facades\Log;

class TrafficVehicleInquiryController extends BaseFinnotechController
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
        $this->apiEndpoint = 'traffic_vehicle_inquiry';
        $this->scope = 'vehicle:traffic-toll-inquiry:get';
        $this->requiresSms = false;
        $this->httpMethod = 'GET';

        $this->requiredFields = ['plate_part1', 'plate_letter', 'plate_part2', 'plate_part3', 'mobile', 'national_code'];
        
        $this->validationRules = [
            'plate_part1' => 'required|string|digits:2',
            'plate_letter' => 'required|string',
            'plate_part2' => 'required|string|digits:3',
            'plate_part3' => 'required|string|digits:2',
            'mobile' => ['required', 'string', new IranianMobile()],
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
            'mobile.required' => 'شماره موبایل الزامی است',
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
            'plateNumber' => $plateNumber,
            'mobile' => $serviceData['mobile'],
            'nationalId' => $serviceData['national_code'],
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
            return ['status' => 'failed', 'message' => 'خطا در دریافت اطلاعات طرح ترافیک'];
        }

        $result = $responseData['result'];
        $bills = $result['bills'] ?? [];

        $formattedBills = [];
        $totalAmount = 0;

        foreach ($bills as $bill) {
            $amountRial = $bill['amount'] ?? 0;
            $amountToman = intval($amountRial / 10); // Convert rial to toman
            $feeRial = $bill['fee'] ?? 0;
            $feeToman = intval($feeRial / 10); // Convert rial to toman
            $totalAmountRial = $bill['totalAmount'] ?? 0;
            $totalAmountBillToman = intval($totalAmountRial / 10); // Convert rial to toman
            
            $totalAmount += $amountToman; // Use toman amount for total calculation
            
            $formattedBills[] = [
                'amount' => $amountToman, // Store as toman
                'amount_formatted' => number_format($amountToman) . ' تومان',
                'fee' => $feeToman, // Store as toman
                'fee_formatted' => number_format($feeToman) . ' تومان',
                'date' => $bill['date'] ?? '',
                'date_formatted' => $this->formatDateTime($bill['date'] ?? ''),
                'payment_status' => $bill['paymentStatus'] ?? '',
                'payment_status_persian' => $this->getPaymentStatusPersian($bill['paymentStatus'] ?? ''),
                'payment_status_color' => $this->getPaymentStatusColor($bill['paymentStatus'] ?? ''),
                'total_amount' => $totalAmountBillToman, // Store as toman
                'total_amount_formatted' => number_format($totalAmountBillToman) . ' تومان',
                'unique_id' => $bill['uniqueId'] ?? '',
            ];
        }

        return [
            'status' => 'success',
            'data' => [
                'track_id' => $responseData['trackId'] ?? '',
                'response_code' => $responseData['responseCode'] ?? '',
                'plate_number' => $result['plateNumber'] ?? '',
                'bills' => $formattedBills,
                'bill_count' => count($formattedBills),
                'total_amount' => $totalAmount,
                'total_amount_formatted' => number_format($totalAmount) . ' تومان',
                'has_unpaid_bills' => $this->hasUnpaidBills($formattedBills),
                'statistics' => $this->generateStatistics($formattedBills),
                'processed_date' => now()->format('Y/m/d H:i:s'),
                'summary' => $this->generateSummary($formattedBills, $totalAmount),
            ]
        ];
    }

    /**
     * Format date time
     */
    private function formatDateTime(string $dateTime): string
    {
        if (empty($dateTime)) return '';
        
        try {
            $timestamp = strtotime($dateTime);
            if ($timestamp) {
                return date('Y/m/d H:i', $timestamp);
            }
        } catch (\Exception $e) {
            // Handle formatting error
        }
        
        return $dateTime;
    }

    /**
     * Get payment status in Persian
     */
    private function getPaymentStatusPersian(string $status): string
    {
        return match (strtolower($status)) {
            'paid' => 'پرداخت شده',
            'unpaid' => 'پرداخت نشده',
            'pending' => 'در انتظار پرداخت',
            'failed' => 'پرداخت ناموفق',
            default => 'نامشخص'
        };
    }

    /**
     * Get payment status color for UI
     */
    private function getPaymentStatusColor(string $status): string
    {
        return match (strtolower($status)) {
            'paid' => 'green',
            'unpaid' => 'red',
            'pending' => 'orange',
            'failed' => 'gray',
            default => 'gray'
        };
    }

    /**
     * Check if there are unpaid bills
     */
    private function hasUnpaidBills(array $bills): bool
    {
        foreach ($bills as $bill) {
            if (strtolower($bill['payment_status']) === 'unpaid') {
                return true;
            }
        }
        return false;
    }

    /**
     * Generate statistics
     */
    private function generateStatistics(array $bills): array
    {
        $paidCount = 0;
        $unpaidCount = 0;
        $paidAmount = 0;
        $unpaidAmount = 0;

        foreach ($bills as $bill) {
            if (strtolower($bill['payment_status']) === 'paid') {
                $paidCount++;
                $paidAmount += $bill['amount'];
            } else {
                $unpaidCount++;
                $unpaidAmount += $bill['amount'];
            }
        }

        return [
            'total_bills' => count($bills),
            'paid_bills' => $paidCount,
            'unpaid_bills' => $unpaidCount,
            'paid_amount' => $paidAmount,
            'unpaid_amount' => $unpaidAmount,
            'paid_amount_formatted' => number_format($paidAmount) . ' تومان',
            'unpaid_amount_formatted' => number_format($unpaidAmount) . ' تومان',
        ];
    }

    /**
     * Generate summary text
     */
    private function generateSummary(array $bills, int $totalAmount): string
    {
        $count = count($bills);
        
        if ($count === 0) {
            return 'برای این خودرو هیچ عوارض طرح ترافیکی ثبت نشده است.';
        }

        $unpaidBills = array_filter($bills, fn($b) => strtolower($b['payment_status']) === 'unpaid');
        $unpaidCount = count($unpaidBills);
        $unpaidAmount = array_sum(array_column($unpaidBills, 'amount'));

        if ($unpaidCount === 0) {
            return "تعداد {$count} عوارض طرح ترافیک ثبت شده که همگی پرداخت شده‌اند.";
        } else {
            return "تعداد {$count} عوارض طرح ترافیک ثبت شده - {$unpaidCount} مورد پرداخت نشده (مبلغ: " . number_format($unpaidAmount) . " تومان)";
        }
    }

    /**
     * Show service result using specific traffic vehicle inquiry view
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

        return view('front.services.results.traffic-vehicle-inquiry', [
            'service' => $service,
            'data' => $result->output_data,
            'inputData' => $result->input_data,
            'resultId' => $resultId,
        ]);
    }
} 