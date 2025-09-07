<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Services\BaseFinnotechController;
use App\Models\Service;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Services\Finnotech\FinnotechService;
use App\Services\Finnotech\SmsAuthorizationService;

class GuaranteeCollateralsController extends BaseFinnotechController
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
        Log::info('🔧 GuaranteeCollateralsController configureService called');
        
        $this->apiEndpoint = 'guarantee-collaterals';
        $this->scope = 'credit:guarantee-collaterals:get';
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
        
        Log::info('🔧 GuaranteeCollateralsController configuration completed', [
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
        Log::info('🚀 GuaranteeCollateralsController process method called', [
            'serviceData' => $serviceData,
            'serviceId' => $service->id
        ]);
        
        try {
            $guaranteeId = $serviceData['guarantee_id'];
            $trackId = $this->generateTrackId();
            
            // Get client credential token
            $accessToken = $this->finnotechService->getToken();
            if (!$accessToken) {
                Log::error('Failed to get client credential token for guarantee collaterals inquiry');
                return [
                    'success' => false,
                    'message' => 'خطا در دریافت توکن احراز هویت'
                ];
            }
            
            $clientId = config('finnotech.client_id');
            
            // Make API call
            $url = config('finnotech.base_url') . "/credit/v2/clients/{$clientId}/guaranteeCollaterals";
            
            Log::info('Making guarantee collaterals API call', [
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
                Log::error('Guarantee collaterals API call failed', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'guarantee_id' => $guaranteeId
                ]);
                
                return [
                    'success' => false,
                    'message' => 'خطا در دریافت اطلاعات وثایق ضمانت‌نامه از سرور'
                ];
            }
            
            $responseData = $response->json();
            
            if (!isset($responseData['status']) || $responseData['status'] !== 'DONE') {
                $errorMessage = $responseData['error']['message'] ?? 'خطا در دریافت اطلاعات وثایق ضمانت‌نامه';
                
                Log::warning('Guarantee collaterals API returned error status', [
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
            
            Log::info('💰 Payment deducted after successful guarantee collaterals inquiry', [
                'user_id' => $user->id,
                'amount' => $service->price,
                'track_id' => $trackId
            ]);
            
            Log::info('🎯 Guarantee collaterals inquiry completed successfully', [
                'guarantee_id' => $guaranteeId,
                'track_id' => $trackId,
                'collaterals_count' => count($result['collaterals'] ?? [])
            ]);

            return [
                'success' => true,
                'data' => $formattedData
            ];

        } catch (\Exception $e) {
            Log::error('❌ Guarantee collaterals inquiry processing failed', [
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
        $collaterals = $responseData['collaterals'] ?? [];
        $formattedCollaterals = [];
        
        foreach ($collaterals as $collateral) {
            $formattedCollaterals[] = [
                'collateral_id' => $collateral['collateralId'] ?? null,
                'collateral_type_code' => $collateral['collateralTypeCode'] ?? null,
                'central_bank_collateral_type_code' => $collateral['centralBankCollateralTypeCode'] ?? null,
                'collateral_type_description' => $collateral['collateralTypeDescription'] ?? null,
                'evaluated_amount' => $collateral['evaluatedAmount'] ?? null,
                'evaluated_amount_formatted' => $this->formatCurrency($collateral['evaluatedAmount'] ?? null),
                'debt_amount' => $collateral['debtAmount'] ?? null,
                'debt_amount_formatted' => $this->formatCurrency($collateral['debtAmount'] ?? null),
                'interest_rate' => $collateral['interestRate'] ?? null,
                'interest_rate_formatted' => $this->formatPercentage($collateral['interestRate'] ?? null),
                'receive_date' => $collateral['receiveDate'] ?? null,
                'receive_date_formatted' => $this->formatPersianDate($collateral['receiveDate'] ?? null),
                'issue_date' => $collateral['issueDate'] ?? null,
                'issue_date_formatted' => $this->formatPersianDate($collateral['issueDate'] ?? null),
                'assign_date' => $collateral['assignDate'] ?? null,
                'assign_date_formatted' => $this->formatPersianDate($collateral['assignDate'] ?? null),
            ];
        }
        
        return [
            'status' => 'success',
            'input_info' => [
                'guarantee_id' => $this->guaranteeId,
                'track_id' => $this->trackId,
            ],
            'guarantee_info' => [
                'guarantee_id' => $responseData['guaranteeId'] ?? $this->guaranteeId,
                'ret_code_description' => $responseData['retCodeDescription'] ?? null,
                'alert_code' => $responseData['alertCode'] ?? null,
                'message_out' => $responseData['messageOut'] ?? null,
            ],
            'collaterals' => $formattedCollaterals,
            'summary' => [
                'total_collaterals' => count($formattedCollaterals),
                'total_evaluated_amount' => $this->calculateTotalAmount($collaterals, 'evaluatedAmount'),
                'total_debt_amount' => $this->calculateTotalAmount($collaterals, 'debtAmount'),
            ]
        ];
    }

    /**
     * Calculate total amount for summary
     */
    private function calculateTotalAmount(array $collaterals, string $field): array
    {
        $total = 0;
        foreach ($collaterals as $collateral) {
            if (isset($collateral[$field]) && is_numeric($collateral[$field])) {
                $total += (int) $collateral[$field];
            }
        }
        
        return [
            'raw' => $total,
            'formatted' => $this->formatCurrency((string) $total)
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
     * Format percentage
     */
    private function formatPercentage(?string $rate): ?string
    {
        if (empty($rate) || trim($rate) === '') {
            return null;
        }
        
        $numericRate = (float) $rate;
        return number_format($numericRate, 2) . '%';
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
        return view('front.services.results.guarantee-collaterals', [
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
        return 'guarantee_collaterals_' . uniqid() . '_' . time();
    }
} 