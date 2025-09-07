<?php

namespace App\Http\Controllers\Services;

use App\Models\Service;
use App\Rules\IranianMobile;
use App\Rules\IranianNationalCode;

class FacilityInquiryController extends BaseSmsFinnotechController
{
    protected function configureService(): void
    {
        $this->apiEndpoint = '/credit/v2/clients/{clientId}/users/{user}/sms/facilityInquiry';
        $this->scope = 'credit:sms-facility-inquiry:get';
        $this->requiresSms = true;
        $this->httpMethod = 'GET';

        $this->requiredFields = [
            'mobile',
            'national_code'
        ];

        $this->validationRules = [
            'mobile' => ['required', 'string', new IranianMobile()],
            'national_code' => ['required', 'string', new IranianNationalCode()],
        ];

        $this->validationMessages = [
            'mobile.required' => 'شماره موبایل الزامی است.',
            'national_code.required' => 'کد ملی الزامی است.',
        ];
    }

    /**
     * Process facility inquiry service
     */
    public function process(array $serviceData, Service $service): array
    {
        $nationalCode = $serviceData['national_code'];
        $mobile = $serviceData['mobile'];

        try {
            // Step 1: Send SMS OTP
            $otpResult = $this->smsAuthService->sendSms($nationalCode, $mobile, $this->scope);
            
            if (isset($otpResult['error'])) {
                throw new \Exception($otpResult['error']['message'] ?? 'خطا در ارسال پیامک');
            }

            return [
                'status' => 'sms_sent',
                'message' => 'کد تایید به شماره موبایل شما ارسال شد.',
                'mobile' => $mobile,
                'national_code' => $nationalCode,
                'temp_data' => [
                    'trackId' => $otpResult['trackId'] ?? null,
                    'scope' => $this->scope,
                    'endpoint' => $this->apiEndpoint
                ]
            ];

        } catch (\Exception $e) {
            \Log::error('Facility Inquiry SMS Error: ' . $e->getMessage());
            
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
                'error_details' => 'خطا در ارسال درخواست استعلام تسهیلات'
            ];
        }
    }

    /**
     * Process SMS OTP verification and get facility data
     */
    protected function processSmsVerification(array $verificationData, array $tempData): array
    {
        $nationalCode = $verificationData['national_code'];
        $otpCode = $verificationData['otp'];
        
        try {
            // Step 2: Verify OTP and get facility data
            $result = $this->smsAuthService->verifyOtpAndCallService(
                $nationalCode,
                $otpCode,
                $this->scope,
                $tempData['endpoint'] ?? $this->apiEndpoint,
                ['user' => $nationalCode]
            );

            if (isset($result['error'])) {
                throw new \Exception($result['error']['message'] ?? 'خطا در دریافت اطلاعات تسهیلات');
            }

            // Format the facility data
            return $this->formatFacilityData($result['result'], $nationalCode);

        } catch (\Exception $e) {
            \Log::error('Facility Inquiry Verification Error: ' . $e->getMessage());
            
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
                'error_details' => 'خطا در دریافت اطلاعات تسهیلات'
            ];
        }
    }

    /**
     * Format facility data for display
     */
    private function formatFacilityData(array $data, string $nationalCode): array
    {
        if (isset($data['result']) && $data['result'] == 120) {
            return [
                'status' => 'success',
                'message' => 'استعلام تسهیلات با موفقیت انجام شد',
                'national_code' => $nationalCode,
                'facility_status' => 'no_facilities',
                'facility_message' => $data['message'] ?? 'هیچ تسهیلات و تعهدی یافت نشد',
                'processed_at' => now()->format('Y/m/d H:i:s')
            ];
        }

        $facilityList = $data['facilityList'] ?? [];
        $formattedFacilities = [];

        foreach ($facilityList as $facility) {
            $formattedFacilities[] = [
                'bank_code' => $facility['FacilityBankCode'] ?? '',
                'bank_branch' => $facility['FacilityBranch'] ?? '',
                'request_number' => $facility['FacilityRequestNo'] ?? '',
                'facility_type' => $this->getFacilityTypeName($facility['FacilityType'] ?? ''),
                'original_amount' => number_format($facility['FacilityAmountOrginal'] ?? 0),
                'debt_amount' => number_format($facility['FacilityDebtorTotalAmount'] ?? 0),
                'benefit_amount' => number_format($facility['FacilityBenefitAmount'] ?? 0),
                'status' => $facility['FacilityStatus'] ?? '',
                'start_date' => $this->formatJalaliDate($facility['FacilitySetDate'] ?? ''),
                'end_date' => $this->formatJalaliDate($facility['FacilityEndDate'] ?? ''),
                'past_expired_amount' => number_format($facility['FacilityPastExpiredAmount'] ?? 0),
                'deferred_amount' => number_format($facility['FacilityDeferredAmount'] ?? 0),
                'suspicious_amount' => number_format($facility['FacilitySuspiciousAmount'] ?? 0),
            ];
        }

        return [
            'status' => 'success',
            'message' => 'استعلام تسهیلات با موفقیت انجام شد',
            'national_code' => $nationalCode,
            'user_name' => $data['name'] ?? '',
            'legal_id' => $data['legalId'] ?? null,
            'facility_status' => 'has_facilities',
            'total_facility_amount' => number_format($data['facilityTotalAmount'] ?? 0),
            'total_debt_amount' => number_format($data['facilityDebtTotalAmount'] ?? 0),
            'total_past_expired' => number_format($data['facilityPastExpiredTotalAmount'] ?? 0),
            'total_deferred' => number_format($data['facilityDeferredTotalAmount'] ?? 0),
            'total_suspicious' => number_format($data['facilitySuspiciousTotalAmount'] ?? 0),
            'dishonored' => $data['dishonored'] ?? '',
            'facilities' => $formattedFacilities,
            'processed_at' => now()->format('Y/m/d H:i:s')
        ];
    }

    /**
     * Get facility type name by code
     */
    private function getFacilityTypeName(string $type): string
    {
        $types = [
            '10' => 'قرض الحسنه',
            '11' => 'مشارکت مدنی',
            '12' => 'مشارکت حقوقی',
            '13' => 'سرمایه گذاری مستقیم',
            '14' => 'مضاربه',
            '15' => 'معاملات سلف',
            '16' => 'فروش اقساطی مواد اولیه، لوازم یدکی و ابزار کار',
            '17' => 'فروش اقساطی وسایل تولید ماشین‌آلات و تاسیسات',
            '18' => 'فروش اقساطی مسکن',
            '19' => 'اجاره به شرط تملیک',
            '20' => 'جعاله',
            '21' => 'مزارعه',
            '22' => 'مساقات',
            '23' => 'خرید دین',
            '24' => 'معاملات قدیم',
            '25' => 'استصناع',
            '26' => 'مرابحه',
        ];

        return $types[$type] ?? "نوع $type";
    }

    /**
     * Format Jalali date
     */
    private function formatJalaliDate(string $date): string
    {
        if (empty($date) || $date === '0') {
            return '-';
        }
        
        if (strlen($date) === 8) {
            return substr($date, 0, 4) . '/' . substr($date, 4, 2) . '/' . substr($date, 6, 2);
        }
        
        return $date;
    }
} 