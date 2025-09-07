<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Services\BaseSmsFinnotechController;
use App\Models\Service;
use Illuminate\Support\Facades\Log;

class GuarantyInquiryController extends BaseSmsFinnotechController
{
    /**
     * Configure service-specific settings
     */
    protected function configureService(): void
    {
        Log::info('🔧 GuarantyInquiryController configureService called');
        
        $this->apiEndpoint = 'guaranty-inquiry';
        $this->scope = 'credit:sms-guaranty-inquiry:get';
        $this->requiresSms = true;
        $this->httpMethod = 'GET';
        
        $this->requiredFields = ['national_code'];
        $this->validationRules = [
            'national_code' => 'required|string|min:10|max:11',
        ];
        $this->validationMessages = [
            'national_code.required' => 'کد ملی یا شناسه ملی الزامی است',
            'national_code.min' => 'کد ملی باید 10 رقم و شناسه ملی باید 11 رقم باشد',
            'national_code.max' => 'کد ملی باید 10 رقم و شناسه ملی باید 11 رقم باشد',
        ];
        
        Log::info('🔧 GuarantyInquiryController configuration completed', [
            'requiresSms' => $this->requiresSms,
            'apiEndpoint' => $this->apiEndpoint,
            'scope' => $this->scope
        ]);
    }

    /**
     * Process service data using SMS authorization flow
     */
    public function process(array $serviceData, Service $service): array
    {
        Log::info('🚀 GuarantyInquiryController process method called', [
            'serviceData' => $serviceData,
            'serviceId' => $service->id
        ]);
        
        try {
            $nationalId = $serviceData['national_code'];
            $mobile = $serviceData['mobile'] ?? '';
            
            // Make API call using SmsAuthorizationService
            $response = $this->smsAuthService->makeAuthorizedApiCall(
                $this->scope,
                $nationalId,
                $mobile,
                [], // no POST params
                ['trackId' => $this->generateTrackId()] // query params
            );
            
            if (!$response || !isset($response['status']) || $response['status'] !== 'DONE') {
                $errorMessage = $response['error']['message'] ?? 'خطا در دریافت اطلاعات ضمانت ها';
                
                Log::warning('Guaranty API returned error status', [
                    'response' => $response,
                    'national_id' => $nationalId
                ]);
                
                return [
                    'success' => false,
                    'message' => $errorMessage
                ];
            }
            
            // Process successful response
            $result = $response['result'] ?? [];
            $formattedData = $this->formatResponseData($result);
            
            // 💰 DEDUCT MONEY ONLY AFTER SUCCESSFUL API RESPONSE
            /** @var \App\Models\User $user */
            $user = \Illuminate\Support\Facades\Auth::user();
            $user->withdraw($service->price, [
                'description' => "پرداخت سرویس: {$service->title}",
                'service_id' => $service->id,
                'type' => 'service_payment',
                'track_id' => $response['trackId'] ?? 'unknown',
                'api_endpoint' => $this->apiEndpoint
            ]);
            
            Log::info('💰 Payment deducted after successful guaranty inquiry', [
                'user_id' => $user->id,
                'amount' => $service->price,
                'track_id' => $response['trackId'] ?? 'unknown'
            ]);
            
            Log::info('🎯 Guaranty inquiry completed successfully', [
                'national_id' => $nationalId,
                'track_id' => $response['trackId'] ?? 'unknown',
                'guarantees_count' => count($result['debtorList'] ?? []),
                'has_guarantees' => isset($result['debtorList']) && count($result['debtorList']) > 0
            ]);

            return [
                'success' => true,
                'data' => $formattedData
            ];

        } catch (\Exception $e) {
            Log::error('❌ Guaranty inquiry processing failed', [
                'national_id' => $serviceData['national_code'] ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'خطا در پردازش درخواست. لطفاً مجدداً تلاش کنید.'
            ];
        }
    }

    /**
     * Format response data for display
     */
    protected function formatResponseData(array $responseData): array
    {
        $data = [
            'status' => 'success',
            'guarantor_info' => [
                'first_name' => $responseData['guarantyFirstName'] ?? '',
                'last_name' => $responseData['guarantyLastName'] ?? '',
                'full_name' => trim(($responseData['guarantyFirstName'] ?? '') . ' ' . ($responseData['guarantyLastName'] ?? '')),
                'national_code' => $responseData['guarantyNationalCode'] ?? '',
                'legal_id' => $responseData['guarantyLegalId'] ?? null,
                'inquiry_result_id' => $responseData['inquiryResultId'] ?? '',
            ],
            'guarantees' => [],
            'summary' => [
                'total_guarantees' => 0,
                'total_debt_amount' => 0,
                'total_original_amount' => 0,
                'banks_count' => 0,
                'active_guarantees' => 0,
            ]
        ];

        if (isset($responseData['debtorList']) && is_array($responseData['debtorList'])) {
            $bankCodes = [];
            $totalDebtAmount = 0;
            $totalOriginalAmount = 0;
            $activeGuarantees = 0;
            
            $data['guarantees'] = array_map(function ($debtor) use (&$bankCodes, &$totalDebtAmount, &$totalOriginalAmount, &$activeGuarantees) {
                $totalAmount = intval(floatval($debtor['totalAmount'] ?? 0));
                $originalAmount = intval(floatval($debtor['orginalAmount'] ?? 0));
                $benefitAmount = intval(floatval($debtor['benefitAmount'] ?? 0));
                $bankCode = $debtor['bankCode'] ?? '';
                
                $totalDebtAmount += $totalAmount;
                $totalOriginalAmount += $originalAmount;
                
                if (!in_array($bankCode, $bankCodes) && !empty($bankCode)) {
                    $bankCodes[] = $bankCode;
                }
                
                // Check if guarantee is active (has remaining debt)
                if ($totalAmount > 0) {
                    $activeGuarantees++;
                }
                
                // Get bank info including logo
                $bankInfo = $this->getBankInfoFromCode($bankCode);
                
                return [
                    'debtor_first_name' => $debtor['debtorFirstName'] ?? '',
                    'debtor_last_name' => $debtor['debtorLastName'] ?? '',
                    'debtor_full_name' => trim(($debtor['debtorFirstName'] ?? '') . ' ' . ($debtor['debtorLastName'] ?? '')),
                    'total_amount' => $totalAmount,
                    'formatted_total_amount' => $this->formatCurrency($totalAmount),
                    'benefit_amount' => $benefitAmount,
                    'formatted_benefit_amount' => $this->formatCurrency($benefitAmount),
                    'obligation_amount' => intval(floatval($debtor['obligationAmount'] ?? 0)),
                    'formatted_obligation_amount' => $this->formatCurrency(intval(floatval($debtor['obligationAmount'] ?? 0))),
                    'suspicious_amount' => intval(floatval($debtor['suspiciousAmount'] ?? 0)),
                    'deferred_amount' => intval(floatval($debtor['deferredAmount'] ?? 0)),
                    'original_amount' => $originalAmount,
                    'formatted_original_amount' => $this->formatCurrency($originalAmount),
                    'past_expired_amount' => intval(floatval($debtor['pastExpiredAmount'] ?? 0)),
                    'bank_code' => $bankCode,
                    'bank_name' => $bankInfo['name'],
                    'bank_logo' => $bankInfo['logo'],
                    'bank_color' => $bankInfo['color'],
                    'set_date' => $this->formatPersianDate($debtor['setDate'] ?? ''),
                    'raw_set_date' => $debtor['setDate'] ?? '',
                    'end_date' => $this->formatPersianDate($debtor['endDate'] ?? ''),
                    'raw_end_date' => $debtor['endDate'] ?? '',
                    'guaranty_percent' => $debtor['guarantyPercent'] ?? '0',
                    'request_number' => $debtor['requestNumber'] ?? '',
                    'request_type' => $debtor['requestType'] ?? '',
                    'request_type_name' => $this->getRequestTypeName($debtor['requestType'] ?? ''),
                    'branch_code' => $debtor['branchCode'] ?? '',
                    'branch_description' => $debtor['branchDescription'] ?? '',
                    'guaranty_id_number' => $debtor['guarantyIdNumber'] ?? '',
                    'defunct_amount' => $debtor['defunctAmount'] ? intval(floatval($debtor['defunctAmount'])) : 0,
                    'commitment_balance_amount' => intval(floatval($debtor['commitmentBalanceAmount'] ?? 0)),
                    'late_penalty_amount' => intval(floatval($debtor['latePenaltyAmount'] ?? 0)),
                    'is_active' => $totalAmount > 0,
                    'status' => $totalAmount > 0 ? 'فعال' : 'تسویه شده',
                    'status_color' => $totalAmount > 0 ? 'text-red-600 bg-red-100' : 'text-emerald-600 bg-emerald-100',
                ];
            }, $responseData['debtorList']);
            
            $data['summary'] = [
                'total_guarantees' => count($responseData['debtorList']),
                'total_debt_amount' => $totalDebtAmount,
                'formatted_total_debt_amount' => $this->formatCurrency($totalDebtAmount),
                'total_original_amount' => $totalOriginalAmount,
                'formatted_total_original_amount' => $this->formatCurrency($totalOriginalAmount),
                'banks_count' => count($bankCodes),
                'active_guarantees' => $activeGuarantees,
                'settled_guarantees' => count($responseData['debtorList']) - $activeGuarantees,
            ];
        }

        return $data;
    }

    /**
     * Show the result page
     */
    public function show(string $resultId, Service $service)
    {
        $result = $this->getServiceResult($resultId, $service);

        if (!$result) {
            abort(404, 'نتیجه سرویس یافت نشد');
        }

        if ($result->isExpired()) {
            return view('front.services.results.expired');
        }

        // Use the output_data directly since it's already formatted correctly
        return view('front.services.results.guaranty-inquiry', [
            'service' => $service,
            'data' => $result->output_data,
            'inputData' => $result->input_data,
            'resultId' => $resultId,
        ]);
    }

    /**
     * Get bank information from bank code
     */
    private function getBankInfoFromCode(string $bankCode): array
    {
        $bankNames = [
            '12' => 'بانک ملت',
            '13' => 'بانک صادرات ایران',
            '14' => 'بانک مسکن',
            '15' => 'بانک توسعه تعاون',
            '16' => 'بانک کشاورزی',
            '17' => 'بانک پارسیان',
            '18' => 'بانک تجارت',
            '19' => 'بانک صنعت و معدن',
            '20' => 'بانک توسعه صادرات ایران',
            '21' => 'پست بانک ایران',
            '22' => 'بانک توسعه صادرات',
            '51' => 'موسسه اعتباری کوثر',
            '54' => 'بانک پارسیان',
            '55' => 'بانک اقتصاد نوین',
            '56' => 'بانک سامان',
            '57' => 'بانک پاسارگاد',
            '58' => 'بانک سرمایه',
            '59' => 'بانک سینا',
            '60' => 'بانک مهر اقتصاد',
            '61' => 'بانک انصار',
            '62' => 'بانک مهر ایران',
            '63' => 'بانک آینده',
            '64' => 'بانک شهر',
            '65' => 'بانک دی',
            '66' => 'بانک رفاه کارگران',
            '69' => 'بانک ایران زمین',
        ];
        
        $bankName = $bankNames[$bankCode] ?? "بانک کد {$bankCode}";
        
        // Try to find bank in database
        $bank = \App\Models\Bank::where('name', 'LIKE', '%' . trim(str_replace(['بانک ', 'ایران'], '', $bankName)) . '%')
                                ->where('is_active', true)
                                ->first();
        
        if (!$bank) {
            // Alternative matching by common patterns
            $searchTerms = [
                '12' => ['ملت'],
                '13' => ['صادرات'],
                '14' => ['مسکن'],
                '17' => ['پارسیان'],
                '18' => ['تجارت'],
                '21' => ['پست'],
                '54' => ['پارسیان'],
                '55' => ['اقتصاد نوین'],
                '56' => ['سامان'],
                '57' => ['پاسارگاد'],
                '66' => ['رفاه'],
            ];
            
            if (isset($searchTerms[$bankCode])) {
                foreach ($searchTerms[$bankCode] as $term) {
                    $bank = \App\Models\Bank::where('name', 'LIKE', '%' . $term . '%')
                                          ->where('is_active', true)
                                          ->first();
                    if ($bank) break;
                }
            }
        }
        
        return [
            'name' => $bankName,
            'logo' => $bank && $bank->logo ? asset($bank->logo) : null,
            'color' => $bank ? $bank->color : null,
        ];
    }

    /**
     * Get request type name
     */
    private function getRequestTypeName(string $requestType): string
    {
        $requestTypes = [
            '1' => 'وام کوتاه مدت',
            '2' => 'وام میان مدت',
            '3' => 'وام بلند مدت',
            '4' => 'تسهیلات',
            '5' => 'اعتبار اسنادی',
            '6' => 'ضمانت نامه',
        ];
        
        return $requestTypes[$requestType] ?? "نوع درخواست {$requestType}";
    }

    /**
     * Format currency amount
     */
    private function formatCurrency(int $amount): string
    {
        return number_format($amount) . ' تومان';
    }

    /**
     * Format Persian date from YYYYMMDD to YYYY/MM/DD
     */
    private function formatPersianDate(string $date): string
    {
        if (empty($date) || strlen($date) !== 8) {
            return $date;
        }
        
        $year = substr($date, 0, 4);
        $month = substr($date, 4, 2);
        $day = substr($date, 6, 2);
        
        return "{$year}/{$month}/{$day}";
    }

    /**
     * Generate track ID
     */
    private function generateTrackId(): string
    {
        return 'guaranty_inquiry_' . uniqid() . '_' . time();
    }
} 