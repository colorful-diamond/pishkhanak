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

class IbanAccountController extends Controller implements BaseServiceController, ServicePreviewInterface
{
    use ServicePaymentTrait;

    /**
     * Handle IBAN to account conversion
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
            'iban.regex' => 'شماره شبا باید با IR شروع شده و 24 رقم باشد.',
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
     * Process IBAN to account conversion and return result data
     *
     * @param array $serviceData
     * @param Service $service
     * @return array
     */
    public function process(array $serviceData, Service $service): array
    {
        return $this->processIbanToAccount($serviceData, $service);
    }

    /**
     * Process account to IBAN conversion
     *
     * @param array $serviceData
     * @param Service $service
     * @return array
     */
    private function processAccountToIban(array $serviceData, Service $service): array
    {
        try {
            $accountNumber = $serviceData['account_number'] ?? '';
            $bankCode = $serviceData['bank_code'] ?? '';
            
            // Use Jibit API for account to IBAN conversion
            $jibitService = app(\App\Services\JibitService::class);
            
            $apiResult = $jibitService->getAccountToIban($accountNumber, $bankCode);
            
            if ($apiResult && isset($apiResult->iban)) {
                $result = [
                    'iban' => $apiResult->iban,
                    'account_number' => $accountNumber,
                    'bank_code' => $bankCode,
                    'bank_name' => $this->getBankNameFromCode($bankCode),
                    'conversion_date' => Verta::now()->format('Y/n/j H:i:s'),
                    'api_response' => $apiResult,
                ];
                
                return [
                    'success' => true,
                    'data' => $result
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'خطا در تبدیل حساب به شبا.'
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'خطا در پردازش سرویس: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Process IBAN to account conversion
     *
     * @param array $serviceData
     * @param Service $service
     * @return array
     */
    private function processIbanToAccount(array $serviceData, Service $service): array
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
            
            Log::info('🔄 Starting IBAN to account conversion', [
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
            Log::error('❌ IbanAccountController processIbanToAccount failed', [
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
     * Try Finnotech as primary provider for IBAN to account
     */
    private function tryFinnotechIbanFirst(string $iban): ?array
    {
        try {
            Log::info('🔄 Trying Finnotech as primary provider for IBAN to account', [
                'iban_masked' => substr($iban, 0, 6) . '***' . substr($iban, -4),
            ]);

            // Call Finnotech service
            $finnotechService = app(\App\Services\Finnotech\FinnotechService::class);
            $endpoint = "/oak/v2/clients/pishkhanak/ibanInquiry";
            
            $response = $finnotechService->makeApiRequest($endpoint, [
                'iban' => $iban,
            ]);

            if ($response && isset($response->result)) {
                Log::info('✅ Finnotech IBAN inquiry success', [
                    'response' => $response,
                ]);

                return [
                    'success' => true,
                    'data' => [
                        'provider' => 'finnotech',
                        'account_number' => $response->result->deposit ?? '',
                        'iban' => $response->result->IBAN ?? $iban,
                        'bank_name' => $response->result->bankName ?? '',
                        'account_status' => $response->result->depositStatus ?? '',
                        'account_description' => $response->result->depositDescription ?? '',
                        'account_comment' => $response->result->depositComment ?? '',
                        'account_owners' => $response->result->depositOwners ?? [],
                        'raw_response' => $response,
                    ],
                    'message' => 'تبدیل شبا به حساب با موفقیت انجام شد (فینوتک)'
                ];
            }

            Log::warning('⚠️ Finnotech IBAN inquiry returned empty/invalid response', [
                'response' => $response,
            ]);
            return null;

        } catch (\Exception $e) {
            Log::warning('⚠️ Finnotech IBAN inquiry failed, will try Jibit fallback', [
                'error' => $e->getMessage(),
                'iban_masked' => substr($iban, 0, 6) . '***' . substr($iban, -4),
            ]);
            return null;
        }
    }

    /**
     * Try Jibit as fallback provider for IBAN to account
     */
    private function tryJibitIbanFallback(string $iban): array
    {
        try {
            Log::info('🔄 Using Jibit as fallback provider for IBAN to account', [
                'iban_masked' => substr($iban, 0, 6) . '***' . substr($iban, -4),
            ]);

            // Call Jibit service
            $jibitService = app(\App\Services\JibitService::class);
            $apiResult = $jibitService->getSheba($iban);
            
            if ($apiResult && isset($apiResult->accountNumber)) {
                Log::info('✅ Jibit IBAN fallback success', [
                    'response' => $apiResult,
                ]);

                // Get bank info from IBAN
                $bankInfo = IranianBankHelper::getBankFromIban($iban);
                
                return [
                    'success' => true,
                    'data' => [
                        'provider' => 'jibit',
                        'account_number' => $apiResult->accountNumber,
                        'iban' => $iban,
                        'bank_name' => $bankInfo['bank_persian_name'] ?? 'نامشخص',
                        'bank_code' => $bankInfo['iban_code'] ?? '',
                        'conversion_date' => Verta::now()->format('Y/n/j H:i:s'),
                        'raw_response' => $apiResult,
                    ],
                    'message' => 'تبدیل شبا به حساب با موفقیت انجام شد (جیبیت)'
                ];
            }

            throw new \Exception('هر دو سرویس فینوتک و جیبیت در تبدیل شبا به حساب ناموفق بودند.');

        } catch (\Exception $e) {
            Log::error('❌ Both Finnotech and Jibit failed for IBAN to account', [
                'error' => $e->getMessage(),
                'iban_masked' => substr($iban, 0, 6) . '***' . substr($iban, -4),
            ]);

            return [
                'success' => false,
                'message' => 'خطا در تبدیل شبا به حساب: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get preview data for IBAN to account conversion service
     *
     * @param array $serviceData
     * @param Service $service
     * @return array
     */
    public function getPreviewData(array $serviceData, Service $service): array
    {
        return $this->getIbanToAccountPreview($serviceData, $service);
    }

    /**
     * Get preview data for account to IBAN conversion
     *
     * @param array $serviceData
     * @param Service $service
     * @return array
     */
    private function getAccountToIbanPreview(array $serviceData, Service $service): array
    {
        try {
            $accountNumber = $serviceData['account_number'] ?? '';
            $bankCode = $serviceData['bank_code'] ?? '';
            
            Log::info('🚀 IbanAccountController: Starting account-to-IBAN preview', [
                'service_id' => $service->id,
                'service_slug' => $service->slug,
                'account_number_masked' => $accountNumber ? substr($accountNumber, 0, 4) . '***' . substr($accountNumber, -4) : 'N/A',
                'bank_code' => $bankCode,
            ]);
            
            // Check cache first
            $cachedData = PreviewCacheService::getAccountInquiry($bankCode, $accountNumber);
            if ($cachedData) {
                Log::info('🎯 IbanAccountController: Using cached account-to-IBAN data', [
                    'account_number_masked' => substr($accountNumber, 0, 4) . '***' . substr($accountNumber, -4),
                    'bank_code' => $bankCode,
                    'cached_at' => $cachedData['cached_at'] ?? 'unknown',
                ]);
                
                // Remove cache metadata and sensitive data before returning
                $previewData = $cachedData;
                unset($previewData['cached_at'], $previewData['cache_type'], $previewData['account_hash']);
                unset($previewData['iban'], $previewData['account_number']); // Hide sensitive data
                
                return [
                    'success' => true,
                    'preview_data' => $previewData,
                    'from_cache' => true
                ];
            }

            // Use Jibit API to get account to IBAN conversion for preview
            $jibitService = app(\App\Services\JibitService::class);
            
            Log::info('🔗 IbanAccountController: Calling Jibit API for account-to-IBAN preview', [
                'account_number_masked' => substr($accountNumber, 0, 4) . '***' . substr($accountNumber, -4),
                'bank_code' => $bankCode,
            ]);
            
            try {
                // Call account to IBAN API for preview
                $apiResult = $jibitService->getAccountToIban($accountNumber, $bankCode);
                
                if ($apiResult && isset($apiResult->iban)) {
                    // Get bank info from the resulting IBAN
                    $bankInfo = IranianBankHelper::getBankFromIban($apiResult->iban);
                    $bankName = $bankInfo['bank_persian_name'] ?? $this->getBankNameFromCode($bankCode);
                    $bankLogo = IranianBankHelper::getBankLogoPath($bankInfo['bank_name'] ?? $bankName);
                    
                    $previewData = [
                        'bank_name' => $bankName,
                        'bank_logo' => $bankLogo,
                        'engagement_message' => "حساب {$bankName} - شماره شبا را دریافت کنید!"
                    ];
                    
                    Log::info('🎉 IbanAccountController: Account-to-IBAN preview data created from API', [
                        'account_number_masked' => substr($accountNumber, 0, 4) . '***' . substr($accountNumber, -4),
                        'bank_code' => $bankCode,
                        'bank_name' => $bankName,
                    ]);
                    
                    // Cache the result
                    PreviewCacheService::setAccountInquiry($bankCode, $accountNumber, $previewData);
                    
                    return [
                        'success' => true,
                        'preview_data' => $previewData,
                        'from_cache' => false
                    ];
                }
            } catch (\Exception $e) {
                Log::error('💥 IbanAccountController: Error calling account-to-IBAN API', [
                    'error' => $e->getMessage(),
                    'account_number_masked' => substr($accountNumber, 0, 4) . '***' . substr($accountNumber, -4),
                    'bank_code' => $bankCode,
                ]);
            }
            
            // Fallback to basic bank info
            $bankName = $this->getBankNameFromCode($bankCode);
            $bankLogo = IranianBankHelper::getBankLogoPath($bankName);
            
            $fallbackPreviewData = [
                'bank_name' => $bankName,
                'bank_logo' => $bankLogo,
                'engagement_message' => "حساب {$bankName} - شماره شبا را دریافت کنید!"
            ];
            
            // Cache the fallback result
            PreviewCacheService::setAccountInquiry($bankCode, $accountNumber, $fallbackPreviewData);
            
            return [
                'success' => true,
                'preview_data' => $fallbackPreviewData,
                'from_cache' => false
            ];
        } catch (\Exception $e) {
            Log::error('💥 IbanAccountController: Failed to get account-to-IBAN preview', [
                'error' => $e->getMessage(),
                'account_number_masked' => isset($accountNumber) ? substr($accountNumber, 0, 4) . '***' . substr($accountNumber, -4) : 'N/A',
                'bank_code' => $bankCode ?? 'N/A',
            ]);
            
            return [
                'success' => true,
                'preview_data' => [
                    'engagement_message' => 'حساب شما آماده است - شماره شبا را دریافت کنید!'
                ]
            ];
        }
    }

    /**
     * Get preview data for IBAN to account conversion
     *
     * @param array $serviceData
     * @param Service $service
     * @return array
     */
    private function getIbanToAccountPreview(array $serviceData, Service $service): array
    {
        try {
            $iban = $serviceData['iban'] ?? '';
            
            Log::info('🚀 IbanAccountController: Starting IBAN-to-account preview', [
                'service_id' => $service->id,
                'service_slug' => $service->slug,
                'iban_masked' => $iban ? substr($iban, 0, 6) . '***' . substr($iban, -4) : 'N/A',
            ]);
            
            // Validate IBAN format
            if (!IranianBankHelper::validateIranianIban($iban)) {
                return [
                    'success' => false,
                    'message' => 'شماره شبا معتبر نیست.'
                ];
            }
            
            // Get bank info from IBAN (no API call needed)
            $bankInfo = IranianBankHelper::getBankFromIban($iban);
            
            if ($bankInfo) {
                $bankName = $bankInfo['bank_persian_name'];
                $bankLogo = IranianBankHelper::getBankLogoPath($bankInfo['bank_name'] ?? $bankName);
                
                $previewData = [
                    'bank_name' => $bankName,
                    'bank_logo' => $bankLogo,
                    'engagement_message' => "شبای {$bankName} - شماره حساب را دریافت کنید!"
                ];
                
                Log::info('🎉 IbanAccountController: IBAN-to-account preview data created from IBAN mapping', [
                    'iban_masked' => substr($iban, 0, 6) . '***' . substr($iban, -4),
                    'bank_name' => $bankName,
                    'bank_code' => $bankInfo['iban_code'],
                ]);
                
                return [
                    'success' => true,
                    'preview_data' => $previewData,
                    'from_cache' => false
                ];
            } else {
                Log::warning('⚠️ IbanAccountController: Unknown bank from IBAN', [
                    'iban_masked' => substr($iban, 0, 6) . '***' . substr($iban, -4),
                ]);
                
                return [
                    'success' => true,
                    'preview_data' => [
                        'engagement_message' => 'شبای شما آماده است - شماره حساب را دریافت کنید!'
                    ]
                ];
            }
        } catch (\Exception $e) {
            Log::error('💥 IbanAccountController: Failed to get IBAN-to-account preview', [
                'error' => $e->getMessage(),
                'iban_masked' => isset($iban) ? substr($iban, 0, 6) . '***' . substr($iban, -4) : 'N/A',
            ]);
            
            return [
                'success' => true,
                'preview_data' => [
                    'engagement_message' => 'شبای شما آماده است - شماره حساب را دریافت کنید!'
                ]
            ];
        }
    }

    /**
     * Get bank name from bank code
     *
     * @param string $bankCode
     * @return string
     */
    private function getBankNameFromCode(string $bankCode): string
    {
        $bankCodes = [
            'MELLI' => 'بانک ملی ایران',
            'SEPAH' => 'بانک سپه',
            'TOSEE_SADERAT' => 'بانک توسعه صادرات',
            'SANAT_VA_MADAN' => 'بانک صنعت و معدن',
            'KESHAVARZI' => 'بانک کشاورزی',
            'MASKAN' => 'بانک مسکن',
            'POST_BANK' => 'پست بانک ایران',
            'MELLAT' => 'بانک ملت',
            'TEJARAT' => 'بانک تجارت',
            'SADERAT' => 'بانک صادرات ایران',
            'REFAH' => 'بانک رفاه کارگران',
            'PARSIAN' => 'بانک پارسیان',
            'PASARGAD' => 'بانک پاسارگاد',
            'SAMAN' => 'بانک سامان',
            'EGHTESAD_NOVIN' => 'بانک اقتصاد نوین',
            'AYANDEH' => 'بانک آینده',
            'SINA' => 'بانک سینا',
            'SHAHR' => 'بانک شهر',
            'KARAFARIN' => 'بانک کارآفرین',
            'DAY' => 'بانک دی',
            'IRAN_ZAMIN' => 'بانک ایران زمین',
            'MIDDLE_EAST' => 'بانک خاورمیانه',
            'GHAVAMIN' => 'بانک قوامین',
            'TOSEE_TAAVON' => 'بانک توسعه تعاون',
            'MEHR_IRAN' => 'بانک قرض الحسنه مهر ایران',
            'HEKMAT' => 'بانک حکمت ایرانیان',
            'GARDESHGARI' => 'بانک گردشگری',
            'SARMAYEH' => 'بانک سرمایه',
            'RESALAT' => 'بانک رسالت',
            'ANSAR' => 'بانک انصار',
        ];

        return $bankCodes[$bankCode] ?? 'نامشخص';
    }

    /**
     * Check if the service supports preview
     *
     * @return bool
     */
    public function supportsPreview(): bool
    {
        return true;
    }

    /**
     * Get the preview template for the service
     *
     * @return string
     */
    public function getPreviewTemplate(): string
    {
        return 'services.preview';
    }

    /**
     * Show the result of the service
     *
     * @param string $resultId
     * @param Service $service
     * @return \Illuminate\View\View
     */
    public function show(string $resultId, Service $service)
    {
        $result = ServiceResult::where('result_hash', $resultId)
            ->where('service_id', $service->id)
            ->firstOrFail();

        // Check authorization: only the owner can view their results
        if (!\Illuminate\Support\Facades\Auth::check() || $result->user_id !== \Illuminate\Support\Facades\Auth::id()) {
            abort(401, 'شما مجاز به مشاهده این نتیجه نیستید.');
        }

        // Check if result is expired
        if ($result->isExpired()) {
            abort(410, 'این نتیجه منقضی شده است.');
        }

        $formattedResult = $result->getFormattedResult();

        return view('front.services.result', [
            'service' => $service,
            'result' => $formattedResult,
            'inputData' => $result->input_data,
            'resultId' => $resultId,
        ]);
    }

    /**
     * Show progress page (default implementation for interface compatibility)
     * IBAN to account service doesn't use background processing
     */
    public function showProgress(Request $request, Service $service, string $hash)
    {
        return redirect()->route('services.show', $service->slug)
            ->withErrors(['error' => 'این سرویس از صفحه پیشرفت پشتیبانی نمی‌کند.']);
    }

    /**
     * Handle OTP submission (default implementation for interface compatibility)
     * IBAN to account service doesn't require OTP verification
     */
    public function handleOtpSubmission(Request $request, Service $service)
    {
        return redirect()->route('services.show', $service->slug)
            ->withErrors(['error' => 'این سرویس از احراز هویت با کد یکبار مصرف پشتیبانی نمی‌کند.']);
    }

    /**
     * Show SMS verification page (default implementation for interface compatibility)
     * IBAN to account service doesn't require SMS verification
     */
    public function showSmsVerification(Request $request, Service $service, string $hash)
    {
        return redirect()->route('services.show', $service->slug)
            ->withErrors(['error' => 'این سرویس از احراز هویت پیامکی پشتیبانی نمی‌کند.']);
    }

    /**
     * Handle SMS OTP verification (default implementation for interface compatibility)
     * IBAN to account service doesn't require SMS verification
     */
    public function handleSmsOtpVerification(Request $request, Service $service, string $hash)
    {
        return redirect()->route('services.show', $service->slug)
            ->withErrors(['error' => 'این سرویس از احراز هویت پیامکی پشتیبانی نمی‌کند.']);
    }

    /**
     * Show SMS result page (default implementation for interface compatibility)
     * IBAN to account service uses standard result display
     */
    public function showSmsResult(Request $request, Service $service, string $id)
    {
        // Redirect to standard result page
        return $this->show($id, $service);
    }
} 