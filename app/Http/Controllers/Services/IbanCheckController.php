<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\BaseServiceController;
use App\Http\Controllers\Services\ServicePaymentTrait;
use App\Contracts\ServicePreviewInterface;
use App\Models\Service;
use App\Models\ServiceResult;
use App\Services\PreviewCacheService;
use App\Helpers\IranianBankHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Hekmatinasser\Verta\Verta;

class IbanCheckController extends Controller implements BaseServiceController, ServicePreviewInterface
{
    use ServicePaymentTrait;

    /**
     * Handle IBAN validation/inquiry
     *
     * @param Request $request
     * @param Service $service
     * @return \Illuminate\Http\Response
     */
    public function handle(Request $request, Service $service)
    {
        // Validate IBAN input
        $validator = Validator::make($request->all(), [
            'iban' => 'required|string|regex:/^IR\d{24}$/',
        ], [
            'iban.required' => 'شماره شبا الزامی است.',
            'iban.regex' => 'شماره شبا باید با IR شروع شده و 26 کاراکتر باشد.',
        ]);
        
        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $serviceData = [
            'iban' => $request->input('iban'),
        ];
        
        // Handle service with payment check
        return $this->handleServiceWithPayment($request, $service, $serviceData);
    }

    /**
     * Process IBAN validation/inquiry and return result data
     *
     * @param array $serviceData
     * @param Service $service
     * @return array
     */
    public function process(array $serviceData, Service $service): array
    {
        try {
            $iban = $serviceData['iban'] ?? '';
            
            // Validate IBAN format
            if (!IranianBankHelper::validateIranianIban($iban)) {
                return [
                    'success' => false,
                    'message' => 'شماره شبا معتبر نیست.'
                ];
            }
            
            Log::info('🔄 Starting IBAN validation/inquiry', [
                'iban_masked' => substr($iban, 0, 6) . '***' . substr($iban, -4),
                'service_slug' => $service->slug,
            ]);
            
            // Try Finnotech first, then fallback to Jibit
            $result = $this->tryFinnotechIbanFirst($iban);
            if ($result !== null) {
                return $result;
            }

            // Fallback to Jibit
            return $this->tryJibitIbanFallback($iban);
            
        } catch (\Exception $e) {
            Log::error('❌ IbanCheckController process failed', [
                'error' => $e->getMessage(),
                'service_data' => $serviceData,
            ]);

            return [
                'success' => false,
                'message' => 'خطا در پردازش سرویس: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Try Finnotech as primary provider for IBAN inquiry
     */
    private function tryFinnotechIbanFirst(string $iban): ?array
    {
        try {
            Log::info('🔄 Trying Finnotech as primary provider for IBAN validation', [
                'iban_masked' => substr($iban, 0, 6) . '***' . substr($iban, -4),
            ]);

            // Call Finnotech service
            $finnotechService = app(\App\Services\Finnotech\FinnotechService::class);
            $endpoint = "/oak/v2/clients/pishkhanak/ibanInquiry";
            
            $response = $finnotechService->makeApiRequest($endpoint, [
                'iban' => $iban,
            ]);

            if ($response && isset($response->result)) {
                Log::info('✅ Finnotech IBAN validation success', [
                    'response' => $response,
                ]);

                return [
                    'success' => true,
                    'data' => [
                        'provider' => 'finnotech',
                        'iban' => $response->result->IBAN ?? $iban,
                        'is_valid' => true,
                        'account_number' => $response->result->deposit ?? '',
                        'bank_name' => $response->result->bankName ?? '',
                        'account_status' => $response->result->depositStatus ?? '',
                        'account_description' => $response->result->depositDescription ?? '',
                        'account_comment' => $response->result->depositComment ?? '',
                        'account_owners' => $response->result->depositOwners ?? [],
                        'validation_date' => Verta::now()->format('Y/n/j H:i:s'),
                        'formatted_iban' => $this->formatIban($iban),
                        'raw_response' => $response,
                    ],
                    'message' => 'بررسی شبا با موفقیت انجام شد (فینوتک)'
                ];
            }

            Log::warning('⚠️ Finnotech IBAN validation returned empty/invalid response', [
                'response' => $response,
            ]);
            return null;

        } catch (\Exception $e) {
            Log::warning('⚠️ Finnotech IBAN validation failed, will try Jibit fallback', [
                'error' => $e->getMessage(),
                'iban_masked' => substr($iban, 0, 6) . '***' . substr($iban, -4),
            ]);
            return null;
        }
    }

    /**
     * Try Jibit as fallback provider for IBAN validation
     */
    private function tryJibitIbanFallback(string $iban): array
    {
        try {
            Log::info('🔄 Using Jibit as fallback provider for IBAN validation', [
                'iban_masked' => substr($iban, 0, 6) . '***' . substr($iban, -4),
            ]);

            // Call Jibit service
            $jibitService = app(\App\Services\JibitService::class);
            $apiResult = $jibitService->getSheba($iban);
            
            if ($apiResult && isset($apiResult->accountNumber)) {
                Log::info('✅ Jibit IBAN validation fallback success', [
                    'response' => $apiResult,
                ]);

                // Get bank info from IBAN
                $bankInfo = IranianBankHelper::getBankFromIban($iban);
                
                return [
                    'success' => true,
                    'data' => [
                        'provider' => 'jibit',
                        'iban' => $iban,
                        'is_valid' => true,
                        'account_number' => $apiResult->accountNumber,
                        'bank_name' => $bankInfo['bank_persian_name'] ?? 'نامشخص',
                        'bank_code' => $bankInfo['iban_code'] ?? '',
                        'validation_date' => Verta::now()->format('Y/n/j H:i:s'),
                        'formatted_iban' => $this->formatIban($iban),
                        'raw_response' => $apiResult,
                    ],
                    'message' => 'بررسی شبا با موفقیت انجام شد (جیبیت)'
                ];
            }

            throw new \Exception('هر دو سرویس فینوتک و جیبیت در بررسی شبا ناموفق بودند.');

        } catch (\Exception $e) {
            Log::error('❌ Both Finnotech and Jibit failed for IBAN validation', [
                'error' => $e->getMessage(),
                'iban_masked' => substr($iban, 0, 6) . '***' . substr($iban, -4),
            ]);

            return [
                'success' => false,
                'message' => 'خطا در بررسی شبا: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Format IBAN for display
     */
    private function formatIban(string $iban): string
    {
        // Format as IR## #### #### #### #### #### ##
        $clean = str_replace(' ', '', $iban);
        if (strlen($clean) !== 26) return $iban;
        
        return substr($clean, 0, 4) . ' ' . 
               substr($clean, 4, 4) . ' ' . 
               substr($clean, 8, 4) . ' ' . 
               substr($clean, 12, 4) . ' ' . 
               substr($clean, 16, 4) . ' ' . 
               substr($clean, 20, 6);
    }

    /**
     * Get preview data for IBAN validation service
     *
     * @param array $serviceData
     * @param Service $service
     * @return array
     */
    public function getPreviewData(array $serviceData, Service $service): array
    {
        try {
            $iban = $serviceData['iban'] ?? '';
            
            Log::info('🚀 IbanCheckController: Starting IBAN validation preview', [
                'service_id' => $service->id,
                'service_slug' => $service->slug,
                'iban_masked' => $iban ? substr($iban, 0, 6) . '***' . substr($iban, -4) : 'N/A',
            ]);
            
            // Check cache first (using generic cache method)
            $cacheKey = "iban_preview:" . md5($iban);
            $cachedData = cache($cacheKey);
            if ($cachedData) {
                Log::info('📋 IbanCheckController: Using cached preview data');
                return $cachedData;
            }
            
            // Basic IBAN validation for preview
            if (!IranianBankHelper::validateIranianIban($iban)) {
                return [
                    'iban' => $iban,
                    'is_valid' => false,
                    'error_message' => 'شماره شبا نامعتبر است.',
                    'validation_date' => Verta::now()->format('Y/n/j H:i:s'),
                ];
            }
            
            // Get bank info from IBAN structure
            $bankInfo = IranianBankHelper::getBankFromIban($iban);
            
            $previewData = [
                'iban' => $iban,
                'formatted_iban' => $this->formatIban($iban),
                'is_valid' => true,
                'bank_name' => $bankInfo['bank_persian_name'] ?? 'بانک نامشخص',
                'estimated_bank_code' => $bankInfo['iban_code'] ?? '',
                'validation_date' => Verta::now()->format('Y/n/j H:i:s'),
                'preview_note' => 'این اطلاعات بر اساس ساختار شبا تخمین زده شده است.'
            ];
            
            // Cache the preview data
            cache([$cacheKey => $previewData], now()->addMinutes(30));
            
            return $previewData;
            
        } catch (\Exception $e) {
            Log::error('💥 IbanCheckController: Error generating preview', [
                'error' => $e->getMessage(),
                'iban' => $iban ?? 'N/A',
            ]);
            
            return [
                'iban' => $iban ?? '',
                'is_valid' => false,
                'error_message' => 'خطا در تولید پیش‌نمایش: ' . $e->getMessage(),
                'validation_date' => Verta::now()->format('Y/n/j H:i:s'),
            ];
        }
    }

    /**
     * Show the service result (interface implementation)
     */
    public function show(string $resultId, Service $service)
    {
        return redirect()->route('services.result', ['id' => $resultId]);
    }

    /**
     * Show progress page (interface implementation)
     */
    public function showProgress(Request $request, Service $service, string $hash)
    {
        return redirect()->route('services.show', $service->slug);
    }

    /**
     * Handle OTP submission (interface implementation)
     */
    public function handleOtpSubmission(Request $request, Service $service)
    {
        return redirect()->route('services.show', $service->slug);
    }

    /**
     * Show SMS verification page (interface implementation)
     */
    public function showSmsVerification(Request $request, Service $service, string $hash)
    {
        return redirect()->route('services.show', $service->slug);
    }

    /**
     * Handle SMS OTP verification (interface implementation)
     */
    public function handleSmsOtpVerification(Request $request, Service $service, string $hash)
    {
        return redirect()->route('services.show', $service->slug);
    }

    /**
     * Show SMS result page (interface implementation)
     */
    public function showSmsResult(Request $request, Service $service, string $id)
    {
        return redirect()->route('services.result', ['id' => $id]);
    }

    /**
     * Check if the service supports preview (interface implementation)
     */
    public function supportsPreview(): bool
    {
        return true;
    }

    /**
     * Get preview template (interface implementation)
     */
    public function getPreviewTemplate(): string
    {
        return 'front.services.custom.iban-check.preview';
    }
} 