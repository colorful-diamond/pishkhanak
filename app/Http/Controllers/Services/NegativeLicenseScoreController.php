<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Services\BaseFinnotechController;
use App\Models\Service;
use App\Rules\IranianMobile;
use App\Rules\IranianNationalCode;
use App\Services\Finnotech\FinnotechService;
use App\Services\Finnotech\SmsAuthorizationService;
use Illuminate\Support\Facades\Log;

class NegativeLicenseScoreController extends BaseFinnotechController
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
        $this->apiEndpoint = 'negative_license_score';
        $this->scope = 'billing:cc-negative-score:get';
        $this->requiresSms = false;
        $this->httpMethod = 'GET';

        $this->requiredFields = ['license_number', 'national_code', 'mobile'];
        
        $this->validationRules = [
            'license_number' => 'required|string|digits:10',
            'national_code' => ['required', 'string', new IranianNationalCode()],
            'mobile' => ['required', 'string', new IranianMobile()],
        ];

        $this->validationMessages = [
            'license_number.required' => 'شماره گواهینامه الزامی است',
            'license_number.digits' => 'شماره گواهینامه باید ۱۰ رقم باشد',
            'national_code.required' => 'کد ملی الزامی است',
            'mobile.required' => 'شماره موبایل الزامی است',
        ];
    }

    /**
     * Prepare API parameters from service data
     */
    protected function prepareApiParameters(array $serviceData): array
    {
        $params = [
            'licenseNumber' => $serviceData['license_number'],
            'nationalID' => $serviceData['national_code'],
            'mobile' => $serviceData['mobile'],
        ];

        return $this->addTrackId($params);
    }

    /**
     * Format API response data
     */
    protected function formatResponseData(array $responseData): array
    {
        if (!isset($responseData['result'])) {
            return ['status' => 'failed', 'message' => 'خطا در دریافت اطلاعات نمره منفی'];
        }

        $result = $responseData['result'];

        return [
            'status' => 'success',
            'data' => [
                'track_id' => $responseData['trackId'] ?? '',
                'license_number' => $result['LicenseNumber'] ?? '',
                'negative_score' => $result['NegativeScore'] ?? '0',
                'offense_count' => $result['OffenseCount'] ?? null,
                'rule' => $result['Rule'] ?? '',
                'formatted_score' => $this->formatScore($result['NegativeScore'] ?? '0'),
                'score_status' => $this->getScoreStatus($result['NegativeScore'] ?? '0'),
                'processed_date' => now()->format('Y/m/d H:i:s'),
                'summary' => $this->generateSummary($result),
            ]
        ];
    }

    /**
     * Format score for display
     */
    private function formatScore(string $score): string
    {
        $numericScore = (int)$score;
        return $numericScore . ' امتیاز منفی';
    }

    /**
     * Get score status based on negative score
     */
    private function getScoreStatus(string $score): array
    {
        $numericScore = (int)$score;
        
        if ($numericScore == 0) {
            return [
                'text' => 'وضعیت مطلوب',
                'color' => 'green',
                'description' => 'گواهینامه شما بدون امتیاز منفی است'
            ];
        } elseif ($numericScore <= 10) {
            return [
                'text' => 'وضعیت قابل قبول',
                'color' => 'yellow',
                'description' => 'امتیاز منفی در حد مجاز است'
            ];
        } elseif ($numericScore <= 20) {
            return [
                'text' => 'وضعیت هشدار',
                'color' => 'orange',
                'description' => 'نزدیک به حد تعلیق گواهینامه'
            ];
        } else {
            return [
                'text' => 'وضعیت خطرناک',
                'color' => 'red',
                'description' => 'امکان تعلیق گواهینامه وجود دارد'
            ];
        }
    }

    /**
     * Generate summary text
     */
    private function generateSummary(array $result): string
    {
        $score = (int)($result['NegativeScore'] ?? '0');
        $licenseNumber = $result['LicenseNumber'] ?? '';
        
        if ($score == 0) {
            return "گواهینامه شماره {$licenseNumber} فاقد امتیاز منفی است.";
        }
        
        return "گواهینامه شماره {$licenseNumber} دارای {$score} امتیاز منفی است.";
    }

    /**
     * Show service result using specific negative score view
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

        return view('front.services.results.negative-license-score', [
            'service' => $service,
            'data' => $result->output_data,
            'inputData' => $result->input_data,
            'resultId' => $resultId,
        ]);
    }
} 