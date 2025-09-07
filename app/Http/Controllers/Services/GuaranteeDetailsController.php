<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Services\BaseFinnotechController;
use App\Models\Service;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Services\Finnotech\FinnotechService;
use App\Services\Finnotech\SmsAuthorizationService;

class GuaranteeDetailsController extends BaseFinnotechController
{
    /**
     * Constructor
     */
    public function __construct(FinnotechService $finnotechService, SmsAuthorizationService $smsAuthService)
    {
        parent::__construct($finnotechService, $smsAuthService);
        $this->configureService();
    }

    private string $guaranteeId;
    private string $trackId;

    /**
     * Prepare API parameters from service data
     */
    protected function prepareApiParameters(array $serviceData): array
    {
        return [
            'guaranteeId' => $serviceData['guarantee_id'] ?? '',
            'trackId' => $this->generateTrackId(),
        ];
    }

    /**
     * Configure service-specific settings
     */
    protected function configureService(): void
    {
        Log::info('🔧 GuaranteeDetailsController configureService called');
        
        $this->apiEndpoint = 'guarantee-details';
        $this->scope = 'credit:guarantee-details:get';
        $this->requiresSms = false;
        $this->httpMethod = 'GET';
        
        $this->requiredFields = ['guarantee_id'];
        $this->validationRules = [
            'guarantee_id' => 'required|string|size:13',
        ];
        $this->validationMessages = [
            'guarantee_id.required' => 'کد ضمانت‌نامه الزامی است',
            'guarantee_id.size' => 'کد ضمانت‌نامه باید 13 رقم باشد',
        ];
        
        Log::info('🔧 GuaranteeDetailsController configuration completed', [
            'requiresSms' => $this->requiresSms,
            'apiEndpoint' => $this->apiEndpoint,
            'scope' => $this->scope
        ]);
    }

    /**
     * Process service data using Client-Credential flow
     */
    public function process(array $serviceData, Service $service): array
    {
        Log::info('🚀 GuaranteeDetailsController process method called', [
            'serviceData' => $serviceData,
            'serviceId' => $service->id
        ]);
        
        try {
            $guaranteeId = $serviceData['guarantee_id'];
            $trackId = $this->generateTrackId();
            
            // Get client credential token
            $accessToken = $this->finnotechService->getToken();
            if (!$accessToken) {
                Log::error('Failed to get client credential token for guarantee details inquiry');
                return [
                    'success' => false,
                    'message' => 'خطا در دریافت توکن احراز هویت'
                ];
            }
            
            $clientId = config('finnotech.client_id');
            
            // Make API call
            $url = config('finnotech.base_url') . "/credit/v2/clients/{$clientId}/guaranteeDetails";
            
            Log::info('Making guarantee details API call', [
                'url' => $url,
                'guarantee_id' => $guaranteeId,
                'track_id' => $trackId
            ]);
            
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$accessToken}",
                'Content-Type' => 'application/json',
            ])->get($url, [
                'guaranteeId' => $guaranteeId,
                'trackId' => $trackId
            ]);
            
            if (!$response->successful()) {
                Log::error('Guarantee details API call failed', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'guarantee_id' => $guaranteeId
                ]);
                
                return [
                    'success' => false,
                    'message' => 'خطا در دریافت اطلاعات ضمانت‌نامه از سرور'
                ];
            }
            
            $responseData = $response->json();
            
            if (!isset($responseData['status']) || $responseData['status'] !== 'DONE') {
                $errorMessage = $responseData['error']['message'] ?? 'خطا در دریافت اطلاعات ضمانت‌نامه';
                
                Log::warning('Guarantee details API returned error status', [
                    'response' => $responseData,
                    'guarantee_id' => $guaranteeId
                ]);
                
                return [
                    'success' => false,
                    'message' => $errorMessage
                ];
            }
            
            // Process successful response
            $result = $responseData['result'] ?? [];
            
            // Store additional data for formatting
            $this->guaranteeId = $guaranteeId;
            $this->trackId = $trackId;
            
            $formattedData = $this->formatResponseData($result);
            
            // 💰 DEDUCT MONEY ONLY AFTER SUCCESSFUL API RESPONSE
            /** @var \App\Models\User $user */
            $user = \Illuminate\Support\Facades\Auth::user();
            $user->withdraw($service->price, [
                'description' => "پرداخت سرویس: {$service->title}",
                'service_id' => $service->id,
                'type' => 'service_payment',
                'track_id' => $trackId,
                'api_endpoint' => $this->apiEndpoint
            ]);
            
            Log::info('💰 Payment deducted after successful guarantee details inquiry', [
                'user_id' => $user->id,
                'amount' => $service->price,
                'track_id' => $trackId
            ]);
            
            Log::info('🎯 Guarantee details inquiry completed successfully', [
                'guarantee_id' => $guaranteeId,
                'track_id' => $trackId
            ]);

            return [
                'success' => true,
                'data' => $formattedData
            ];

        } catch (\Exception $e) {
            Log::error('❌ Guarantee details inquiry processing failed', [
                'guarantee_id' => $serviceData['guarantee_id'] ?? 'unknown',
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
        return [
            'status' => 'success',
            'input_info' => [
                'guarantee_id' => $this->guaranteeId,
                'track_id' => $this->trackId,
            ],
            'guarantee_details' => [
                'guarantee_id' => $responseData['guaranteeId'] ?? $this->guaranteeId,
                'sepam_id' => $responseData['sepamId'] ?? null,
                'branch_code' => $responseData['branchCode'] ?? null,
                'central_bank_type_code' => $responseData['centralBankTypeCode'] ?? null,
                'guarantee_sub_type_code' => $responseData['guaranteeSubTypeCode'] ?? null,
                'guarantee_sub_type_desc' => $responseData['guaranteeSubTypeDesc'] ?? null,
                'cif' => $responseData['cif'] ?? null,
                'debt_amount' => $responseData['debtAmount'] ?? null,
                'debt_amount_formatted' => $this->formatCurrency($responseData['debtAmount'] ?? null),
                'issue_date' => $responseData['issueDate'] ?? null,
                'issue_date_formatted' => $this->formatPersianDate($responseData['issueDate'] ?? null),
                'maturity_date' => $responseData['maturityDate'] ?? null,
                'maturity_date_formatted' => $this->formatPersianDate($responseData['maturityDate'] ?? null),
                'renew_date' => $responseData['renewDate'] ?? null,
                'renew_date_formatted' => $this->formatPersianDate($responseData['renewDate'] ?? null),
                'company_pre_debt_amount' => $responseData['companyPreDebtAmount'] ?? null,
                'company_pre_debt_amount_formatted' => $this->formatCurrency($responseData['companyPreDebtAmount'] ?? null),
                'issue_charge_amount' => $responseData['issueChargeAmount'] ?? null,
                'issue_charge_amount_formatted' => $this->formatCurrency($responseData['issueChargeAmount'] ?? null),
                'last_renew_charge_amount' => $responseData['lastRenewChargeAmount'] ?? null,
                'last_renew_charge_amount_formatted' => $this->formatCurrency($responseData['lastRenewChargeAmount'] ?? null),
                'total_charge' => $responseData['totalCharge'] ?? null,
                'total_charge_formatted' => $this->formatCurrency($responseData['totalCharge'] ?? null),
                'guarantee_status_code' => $responseData['guaranteeStatusCode'] ?? null,
                'guarantee_status_description' => $responseData['guaranteeStatusDescription'] ?? null,
                'economic_desc' => $responseData['economicDesc'] ?? null,
                'economic_subsection_desc' => $responseData['economicSubsectionDesc'] ?? null,
                'ret_code_description' => $responseData['retCodeDescription'] ?? null,
            ]
        ];
    }

    /**
     * Format currency amount
     */
    private function formatCurrency(?string $amount): ?string
    {
        if (empty($amount) || trim($amount) === '') {
            return null;
        }
        
        $numericAmount = (int) $amount;
        return number_format($numericAmount) . ' ریال';
    }

    /**
     * Format Persian date
     */
    private function formatPersianDate(?string $date): ?string
    {
        if (empty($date) || trim($date) === '') {
            return null;
        }
        
        // Format from YYYYMMDD to YYYY/MM/DD
        if (strlen($date) === 8) {
            return substr($date, 0, 4) . '/' . substr($date, 4, 2) . '/' . substr($date, 6, 2);
        }
        
        return $date;
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
        return view('front.services.results.guarantee-details', [
            'service' => $service,
            'data' => $result->output_data,
            'inputData' => $result->input_data,
            'resultId' => $resultId,
        ]);
    }

    /**
     * Generate track ID
     */
    private function generateTrackId(): string
    {
        return 'guarantee_details_' . uniqid() . '_' . time();
    }
} 