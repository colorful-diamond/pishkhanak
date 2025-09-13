<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Services\BaseFinnotechController;
use App\Models\Service;
use App\Services\Finnotech\FinnotechService;
use App\Services\Finnotech\SmsAuthorizationService;

class TrafficViolationImageController extends BaseFinnotechController
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
        $this->apiEndpoint = 'traffic-violation-image';
        $this->scope = 'traffic:violation-image:get';
        $this->requiresSms = false;
        $this->httpMethod = 'GET';
        
        $this->requiredFields = ['mobile', 'national_code', 'violation_serial'];
        
        $this->validationRules = [
            'mobile' => 'required|string|regex:/^09[0-9]{9}$/',
            'national_code' => 'required|string|digits:10',
            'violation_serial' => 'required|string|min:8|max:20|regex:/^[a-zA-Z0-9]+$/',
        ];
        
        $this->validationMessages = [
            'mobile.required' => 'شماره موبایل الزامی است',
            'mobile.regex' => 'شماره موبایل نامعتبر است',
            'national_code.required' => 'کد ملی الزامی است',
            'national_code.digits' => 'کد ملی باید 10 رقم باشد',
            'violation_serial.required' => 'سریال خلافی الزامی است',
            'violation_serial.min' => 'سریال خلافی باید حداقل 8 کاراکتر باشد',
            'violation_serial.max' => 'سریال خلافی نباید بیش از 20 کاراکتر باشد',
            'violation_serial.regex' => 'سریال خلافی تنها باید شامل حروف انگلیسی و اعداد باشد',
        ];
    }

    /**
     * Prepare API parameters from service data
     */
    protected function prepareApiParameters(array $serviceData): array
    {
        return [
            'mobile' => $serviceData['mobile'] ?? '',
            'nationalCode' => $serviceData['national_code'] ?? '',
            'violationSerial' => $serviceData['violation_serial'] ?? '',
        ];
    }

    /**
     * Format response data for display
     */
    protected function formatResponseData(array $responseData): array
    {
        if (!isset($responseData['result'])) {
            return ['status' => 'failed', 'message' => 'خطا در دریافت تصویر خلافی'];
        }

        $result = $responseData['result'];
        
        // Convert Rial to Toman
        $amountRial = (int)($result['amount'] ?? 0);
        $amountToman = floor($amountRial / 10);
        
        return [
            'status' => 'success',
            'data' => [
                'track_id' => $responseData['trackId'] ?? '',
                'violation_serial' => request('violation_serial'),
                'image_url' => $result['imageUrl'] ?? null,
                'image_base64' => $result['imageData'] ?? null,
                'violation_info' => [
                    'code' => $result['violationCode'] ?? '',
                    'description' => $result['description'] ?? '',
                    'date' => $result['date'] ?? '',
                    'date_persian' => $this->formatDateToPersian($result['date'] ?? ''),
                    'location' => $result['location'] ?? '',
                    'amount_rial' => $amountRial,
                    'amount_toman' => $amountToman,
                    'formatted_amount' => number_format($amountToman) . ' تومان',
                ],
                'has_image' => !empty($result['imageUrl']) || !empty($result['imageData']),
                'download_available' => !empty($result['imageUrl']),
                'processed_date' => now()->format('Y/m/d H:i:s'),
                'summary' => $this->generateSummary($result),
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
            // Assume input is in format 'Y-m-d H:i:s' or similar
            $carbonDate = \Carbon\Carbon::parse($date);
            return \Morilog\Jalali\Jalalian::fromCarbon($carbonDate)->format('Y/m/d H:i');
        } catch (\Exception $e) {
            return $date;
        }
    }

    /**
     * Generate summary for violation image
     */
    private function generateSummary(array $result): string
    {
        $hasImage = !empty($result['imageUrl']) || !empty($result['imageData']);
        $violationCode = $result['violationCode'] ?? '';
        
        if ($hasImage) {
            if (!empty($violationCode)) {
                return "تصویر خلافی با کد {$violationCode} با موفقیت دریافت شد.";
            }
            return "تصویر خلافی با موفقیت دریافت شد.";
        } else {
            return "متأسفانه تصویری برای این خلافی یافت نشد.";
        }
    }

    /**
     * Show service result using specific traffic violation image view
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

        return view('front.services.results.traffic-violation-image', [
            'service' => $service,
            'data' => $result->output_data,
            'inputData' => $result->input_data,
            'resultId' => $resultId,
        ]);
    }
} 