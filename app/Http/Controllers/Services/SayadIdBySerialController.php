<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Services\BaseFinnotechController;
use App\Models\Service;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Services\Finnotech\FinnotechService;
use App\Services\Finnotech\SmsAuthorizationService;

class SayadIdBySerialController extends BaseFinnotechController
{
    /**
     * Constructor
     */
    public function __construct(FinnotechService $finnotechService, SmsAuthorizationService $smsAuthService)
    {
        parent::__construct($finnotechService, $smsAuthService);
        $this->configureService();
    }

    private string $serialNo;
    private string $seriesNo;
    private string $trackId;

    /**
     * Configure service-specific settings
     */
    protected function configureService(): void
    {
        Log::info('🔧 SayadIdBySerialController configureService called');
        
        $this->apiEndpoint = 'sayad-id-by-serial';
        $this->scope = 'credit:sayad-id-by-serial:get';
        $this->requiresSms = false;
        $this->httpMethod = 'GET';
        
        $this->requiredFields = ['serial_no', 'series_no'];
        $this->validationRules = [
            'serial_no' => 'required|string|min:1|max:20',
            'series_no' => 'required|string|min:1|max:20',
        ];
        $this->validationMessages = [
            'serial_no.required' => 'شماره سریال چک الزامی است',
            'series_no.required' => 'شماره سری چک الزامی است',
            'serial_no.min' => 'شماره سریال نامعتبر است',
            'series_no.min' => 'شماره سری نامعتبر است',
        ];
        
        Log::info('🔧 SayadIdBySerialController configuration completed', [
            'requiresSms' => $this->requiresSms,
            'apiEndpoint' => $this->apiEndpoint,
            'scope' => $this->scope
        ]);
    }

    /**
     * Prepare API parameters from service data
     */
    protected function prepareApiParameters(array $serviceData): array
    {
        return [
            'serialNo' => $serviceData['serial_no'] ?? '',
            'seriesNo' => $serviceData['series_no'] ?? '',
        ];
    }

    /**
     * Process service data using Client-Credential flow
     */
    public function process(array $serviceData, Service $service): array
    {
        Log::info('🚀 SayadIdBySerialController process method called', [
            'serviceData' => $serviceData,
            'serviceId' => $service->id
        ]);
        
        try {
            $serialNo = $serviceData['serial_no'];
            $seriesNo = $serviceData['series_no'];
            $trackId = $this->generateTrackId();
            
            // Get client credential token
            $accessToken = $this->finnotechService->getToken();
            if (!$accessToken) {
                Log::error('Failed to get client credential token for sayad ID inquiry');
                return [
                    'success' => false,
                    'message' => 'خطا در دریافت توکن احراز هویت'
                ];
            }
            
            $clientId = config('finnotech.client_id');
            
            // Make API call
            $url = config('finnotech.base_url') . "/credit/v2/clients/{$clientId}/sayadIdBySerial";
            
            Log::info('Making sayad ID by serial API call', [
                'url' => $url,
                'serial_no' => $serialNo,
                'series_no' => $seriesNo,
                'track_id' => $trackId
            ]);
            
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$accessToken}",
                'Content-Type' => 'application/json',
            ])->get($url, [
                'serialNo' => $serialNo,
                'seriesNo' => $seriesNo,
                'trackId' => $trackId
            ]);
            
            if (!$response->successful()) {
                Log::error('Sayad ID by serial API call failed', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'serial_no' => $serialNo,
                    'series_no' => $seriesNo
                ]);
                
                return [
                    'success' => false,
                    'message' => 'خطا در دریافت اطلاعات چک صیادی از سرور'
                ];
            }
            
            $responseData = $response->json();
            
            if (!isset($responseData['status']) || $responseData['status'] !== 'DONE') {
                $errorMessage = $responseData['error']['message'] ?? 'خطا در دریافت اطلاعات چک صیادی';
                
                Log::warning('Sayad ID by serial API returned error status', [
                    'response' => $responseData,
                    'serial_no' => $serialNo,
                    'series_no' => $seriesNo
                ]);
                
                return [
                    'success' => false,
                    'message' => $errorMessage
                ];
            }
            
            // Process successful response
            $result = $responseData['result'] ?? [];
            
            // Store additional data for formatting
            $this->serialNo = $serialNo;
            $this->seriesNo = $seriesNo;
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
            
            Log::info('💰 Payment deducted after successful sayad ID inquiry', [
                'user_id' => $user->id,
                'amount' => $service->price,
                'track_id' => $trackId
            ]);
            
            Log::info('🎯 Sayad ID by serial inquiry completed successfully', [
                'serial_no' => $serialNo,
                'series_no' => $seriesNo,
                'track_id' => $trackId,
                'sayad_id' => $result['sayadId'] ?? 'unknown'
            ]);

            return [
                'success' => true,
                'data' => $formattedData
            ];

        } catch (\Exception $e) {
            Log::error('❌ Sayad ID by serial inquiry processing failed', [
                'serial_no' => $serviceData['serial_no'] ?? 'unknown',
                'series_no' => $serviceData['series_no'] ?? 'unknown',
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
                'serial_no' => $this->serialNo,
                'series_no' => $this->seriesNo,
                'track_id' => $this->trackId,
            ],
            'cheque_details' => [
                'iban' => $responseData['iban'] ?? null,
                'issued_date' => $responseData['issuedDate'] ?? null,
                'expiration_date' => $responseData['expirationDate'] ?? null,
                'serial_no' => $responseData['serialNo'] ?? $this->serialNo,
                'series_no' => $responseData['seriesNo'] ?? $this->seriesNo,
                'sayad_id' => $responseData['sayadId'] ?? null,
                'media_type' => $responseData['mediaType'] ?? null,
                'media_type_name' => $this->getMediaTypeName($responseData['mediaType'] ?? null),
                'branch_code' => $responseData['branchCode'] ?? null,
            ]
        ];
    }

    /**
     * Get media type name in Persian
     */
    private function getMediaTypeName(?string $mediaType): string
    {
        $mediaTypes = [
            'BANS' => 'چک عادی',
            'CHD' => 'چک الکترونیک (چکاد)',
            'CHS' => 'چک موردی',
            'CHT' => 'چک بانکی (چک های تضمین شده بین بانکی)',
        ];
        
        return $mediaTypes[$mediaType] ?? 'نامشخص';
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
        return view('front.services.results.sayad-id-by-serial', [
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
        return 'sayad_serial_' . uniqid() . '_' . time();
    }
} 