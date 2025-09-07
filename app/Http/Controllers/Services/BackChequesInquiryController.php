<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Services\BaseSmsFinnotechController;
use App\Models\Service;
use Illuminate\Support\Facades\Log;

class BackChequesInquiryController extends BaseSmsFinnotechController
{
    /**
     * Configure service-specific settings
     */
    protected function configureService(): void
    {
        Log::info('🔧 BackChequesInquiryController configureService called');
        
        $this->apiEndpoint = 'back-cheques-inquiry';
        $this->scope = 'credit:sms-back-cheques:get';
        $this->requiresSms = true;
        $this->httpMethod = 'GET';
        
        $this->requiredFields = ['national_code'];
        $this->validationRules = [
            'national_code' => 'required|string|digits:10',
        ];
        $this->validationMessages = [
            'national_code.required' => 'کد ملی الزامی است',
            'national_code.digits' => 'کد ملی باید 10 رقم باشد',
        ];
        
        Log::info('🔧 BackChequesInquiryController configuration completed', [
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
        Log::info('🚀 BackChequesInquiryController process method called', [
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
                $errorMessage = $response['error']['message'] ?? 'خطا در دریافت اطلاعات چک های برگشتی';
                
                Log::warning('Back cheques API returned error status', [
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
            
            Log::info('💰 Payment deducted after successful back cheques inquiry', [
                'user_id' => $user->id,
                'amount' => $service->price,
                'track_id' => $response['trackId'] ?? 'unknown'
            ]);
            
            Log::info('🎯 Back cheques inquiry completed successfully', [
                'national_id' => $nationalId,
                'track_id' => $response['trackId'] ?? 'unknown',
                'cheques_count' => count($result['chequeList'] ?? []),
                'has_cheques' => isset($result['chequeList']) && count($result['chequeList']) > 0
            ]);

            return [
                'success' => true,
                'data' => $formattedData
            ];

        } catch (\Exception $e) {
            Log::error('❌ Back cheques inquiry processing failed', [
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
            'user_info' => [
                'national_id' => $responseData['nid'] ?? '',
                'legal_id' => $responseData['legalId'] ?? null,
                'name' => $responseData['name'] ?? 'نامشخص',
            ],
            'cheques' => [],
            'summary' => [
                'total_cheques' => 0,
                'total_amount' => 0,
                'banks_count' => 0,
            ]
        ];

        if (isset($responseData['chequeList']) && is_array($responseData['chequeList'])) {
            $bankCodes = [];
            $totalAmount = 0;
            
            $data['cheques'] = array_map(function ($cheque) use (&$bankCodes, &$totalAmount) {
                $amount = intval(floatval($cheque['amount'] ?? 0));
                $totalAmount += $amount;
                
                $bankCode = $cheque['bankCode'] ?? '';
                if (!in_array($bankCode, $bankCodes)) {
                    $bankCodes[] = $bankCode;
                }
                
                // Get bank info including logo
                $bankInfo = $this->getBankInfoFromCode($bankCode);
                
                return [
                    'account_number' => $cheque['accountNumber'] ?? '',
                    'amount' => $amount,
                    'formatted_amount' => $this->formatCurrency($amount),
                    'back_date' => $this->formatPersianDate($cheque['backDate'] ?? ''),
                    'raw_back_date' => $cheque['backDate'] ?? '',
                    'bank_code' => $bankCode,
                    'bank_name' => $bankInfo['name'],
                    'bank_logo' => $bankInfo['logo'],
                    'bank_color' => $bankInfo['color'],
                    'branch_code' => $cheque['branchCode'] ?? '',
                    'branch_description' => $cheque['branchDescription'] ?? '',
                    'date' => $this->formatPersianDate($cheque['date'] ?? ''),
                    'raw_date' => $cheque['date'] ?? '',
                    'cheque_id' => $cheque['id'] ?? '',
                    'cheque_number' => $cheque['number'] ?? '',
                ];
            }, $responseData['chequeList']);
            
            $data['summary'] = [
                'total_cheques' => count($responseData['chequeList']),
                'total_amount' => $totalAmount,
                'formatted_total_amount' => $this->formatCurrency($totalAmount),
                'banks_count' => count($bankCodes),
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
        return view('front.services.results.back-cheques-inquiry', [
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
        return 'back_cheques_' . uniqid() . '_' . time();
    }
} 