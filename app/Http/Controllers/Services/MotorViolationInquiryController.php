<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Services\BaseFinnotechController;
use App\Models\Service;
use App\Rules\IranianMobile;
use App\Rules\IranianNationalCode;
use App\Services\Finnotech\FinnotechService;
use App\Services\Finnotech\SmsAuthorizationService;
use Illuminate\Support\Facades\Log;

class MotorViolationInquiryController extends BaseFinnotechController
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
        $this->apiEndpoint = 'motor_violation_inquiry';
        $this->scope = 'billing:riding-offense-inquiry:get';
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
        // Convert plate parts to 8-digit format for motor plates
        $plateNumber = $this->convertPlatePartsToMotorFormat($serviceData);
        
        $params = [
            'plateNumber' => $plateNumber,
            'nationalID' => $serviceData['national_code'],
            'mobile' => $serviceData['mobile'],
        ];

        return $this->addTrackId($params);
    }

    /**
     * Convert plate parts to 8-digit motor format
     */
    private function convertPlatePartsToMotorFormat(array $serviceData): string
    {
        // For motor plates, we typically use 8 digits (no letter conversion needed)
        $part1 = str_pad($serviceData['plate_part1'], 2, '0', STR_PAD_LEFT);
        $part2 = str_pad($serviceData['plate_part2'], 3, '0', STR_PAD_LEFT);
        $part3 = str_pad($serviceData['plate_part3'], 2, '0', STR_PAD_LEFT);
        // Take first character of letter as number for motor plates
        $letterCode = $this->convertLetterToSimpleNumber($serviceData['plate_letter']);
        
        return $part1 . $letterCode . $part2 . $part3;
    }

    /**
     * Convert Persian letter to simple number for motor plates
     */
    private function convertLetterToSimpleNumber(string $letter): string
    {
        // Simplified mapping for motor plates
        $letterMap = [
            'الف' => '1', 'ب' => '2', 'پ' => '3', 'ت' => '4', 'ث' => '5',
            'ج' => '6', 'چ' => '7', 'ح' => '8', 'خ' => '9', 'د' => '0',
        ];

        return $letterMap[$letter] ?? '1';
    }

    /**
     * Format API response data
     */
    protected function formatResponseData(array $responseData): array
    {
        if (!isset($responseData['result'])) {
            return ['status' => 'failed', 'message' => 'خطا در دریافت اطلاعات خلافی موتور'];
        }

        $result = $responseData['result'];
        
        // Convert amount from rial to toman
        $amountRial = $result['Amount'] ?? 0;
        $amountToman = intval($amountRial / 10);

        return [
            'status' => 'success',
            'data' => [
                'track_id' => $responseData['trackId'] ?? '',
                'plate_number' => $result['PlateNumber'] ?? '',
                'violation_info' => [
                    'amount' => $amountToman, // Store as toman
                    'amount_formatted' => number_format($amountToman) . ' تومان',
                    'bill_id' => $result['BillID'] ?? '',
                    'payment_id' => $result['PaymentID'] ?? '',
                    'complaint_code' => $result['ComplaintCode'] ?? '0',
                    'complaint_status' => $result['ComplaintStatus'] ?? 'شکايت ندارد',
                ],
                'payment_info' => [
                    'can_pay' => !empty($result['BillID']) && !empty($result['PaymentID']),
                    'bill_id' => $result['BillID'] ?? '',
                    'payment_id' => $result['PaymentID'] ?? '',
                    'payment_status' => $this->getPaymentStatus($result),
                    'payment_status_color' => $this->getPaymentStatusColor($result),
                ],
                'complaint_info' => [
                    'has_complaint' => ($result['ComplaintCode'] ?? '0') !== '0',
                    'complaint_code' => $result['ComplaintCode'] ?? '0',
                    'complaint_status' => $result['ComplaintStatus'] ?? 'شکايت ندارد',
                    'complaint_description' => $this->getComplaintDescription($result['ComplaintCode'] ?? '0'),
                ],
                'processed_date' => now()->format('Y/m/d H:i:s'),
                'summary' => $this->generateSummary($result, $amountToman),
                'recommendations' => $this->generateRecommendations($result, $amountToman),
            ]
        ];
    }

    /**
     * Get payment status
     */
    private function getPaymentStatus(array $result): string
    {
        $amount = $result['Amount'] ?? 0;
        $billId = $result['BillID'] ?? '';
        $paymentId = $result['PaymentID'] ?? '';
        
        if ($amount == 0) {
            return 'بدون خلافی';
        } elseif (empty($billId) || empty($paymentId)) {
            return 'غیرقابل پرداخت';
        } else {
            return 'قابل پرداخت';
        }
    }

    /**
     * Get payment status color
     */
    private function getPaymentStatusColor(array $result): string
    {
        $status = $this->getPaymentStatus($result);
        
        return match ($status) {
            'بدون خلافی' => 'green',
            'قابل پرداخت' => 'red',
            'غیرقابل پرداخت' => 'gray',
            default => 'gray'
        };
    }

    /**
     * Get complaint description
     */
    private function getComplaintDescription(string $complaintCode): string
    {
        if ($complaintCode === '0' || empty($complaintCode)) {
            return 'هیچ شکایتی ثبت نشده است';
        }
        
        // You can add more complaint code mappings here based on API documentation
        $complaintMap = [
            '1' => 'شکایت ثبت شده',
            '2' => 'شکایت در حال بررسی',
            '3' => 'شکایت رد شده',
            '4' => 'شکایت پذیرفته شده',
        ];
        
        return $complaintMap[$complaintCode] ?? "کد شکایت: {$complaintCode}";
    }

    /**
     * Generate summary text
     */
    private function generateSummary(array $result, int $amountToman): string
    {
        $plateNumber = $result['PlateNumber'] ?? '';
        $complaintCode = $result['ComplaintCode'] ?? '0';
        
        if ($amountToman == 0) {
            return "موتور با پلاک {$plateNumber} هیچ خلافی ندارد.";
        } else {
            $summary = "موتور با پلاک {$plateNumber} دارای " . number_format($amountToman) . " تومان خلافی است.";
            
            if ($complaintCode !== '0') {
                $summary .= " شکایت ثبت شده است.";
            }
            
            return $summary;
        }
    }

    /**
     * Generate recommendations
     */
    private function generateRecommendations(array $result, int $amountToman): array
    {
        $recommendations = [];
        $billId = $result['BillID'] ?? '';
        $paymentId = $result['PaymentID'] ?? '';
        $complaintCode = $result['ComplaintCode'] ?? '0';
        
        if ($amountToman == 0) {
            $recommendations[] = 'عالی! موتور شما هیچ خلافی ندارد';
            $recommendations[] = 'به رانندگی ایمن خود ادامه دهید';
        } else {
            if (!empty($billId) && !empty($paymentId)) {
                $recommendations[] = 'برای پرداخت خلافی از شناسه قبض و پرداخت استفاده کنید';
                $recommendations[] = 'پرداخت خلافی را به تأخیر نیندازید';
            } else {
                $recommendations[] = 'خلافی قابل پرداخت آنلاین نیست - به مراجع مربوطه مراجعه کنید';
            }
            
            if ($complaintCode !== '0') {
                $recommendations[] = 'شکایت شما در حال بررسی است';
                $recommendations[] = 'منتظر نتیجه بررسی شکایت باشید';
            } else {
                $recommendations[] = 'در صورت عدم اعتراف به خلافی، می‌توانید شکایت کنید';
            }
        }
        
        // General safety recommendations
        $recommendations[] = 'همیشه کلاه ایمنی استفاده کنید';
        $recommendations[] = 'قوانین راهنمایی و رانندگی را رعایت کنید';
        
        return $recommendations;
    }

    /**
     * Show service result using specific motor violation inquiry view
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

        return view('front.services.results.motor-violation-inquiry', [
            'service' => $service,
            'data' => $result->output_data,
            'inputData' => $result->input_data,
            'resultId' => $resultId,
        ]);
    }
} 