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

class CardAccountController extends Controller implements BaseServiceController, ServicePreviewInterface
{
    use ServicePaymentTrait;

    /**
     * Handle card to account conversion
     *
     * @param Request $request
     * @param Service $service
     * @return \Illuminate\Http\Response
     */
    public function handle(Request $request, Service $service)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'card_number' => 'required|string|regex:/^\d{16}$/',
        ], [
            'card_number.required' => 'شماره کارت الزامی است.',
            'card_number.regex' => 'شماره کارت باید 16 رقم باشد.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $cardNumber = $request->input('card_number');
        $serviceData = ['card_number' => $cardNumber];
        
        // Handle service with payment check
        return $this->handleServiceWithPayment($request, $service, $serviceData);
    }

    /**
     * Prepare API parameters from service data
     */
    protected function prepareApiParameters(array $serviceData): array
    {
        return [
            'card' => $serviceData['card_number'] ?? '',
        ];
    }

    /**
     * Process card to account conversion and return result data
     *
     * @param array $serviceData
     * @param Service $service
     * @return array
     */
    public function process(array $serviceData, Service $service): array
    {
        try {
            $cardNumber = $serviceData['card_number'] ?? '';
            
            // Validate card number
            if (!preg_match('/^\d{16}$/', $cardNumber)) {
                return [
                    'success' => false,
                    'message' => 'شماره کارت باید 16 رقم باشد.'
                ];
            }
            
            // Use Jibit API for card to account conversion
            $jibitService = app(\App\Services\JibitService::class);
            
            // Get card to account conversion
            $apiResult = $jibitService->getCardToAccount($cardNumber);
            
            if ($apiResult && isset($apiResult->cardInfo)) {
                $cardInfo = $apiResult->cardInfo;
                $result = [
                    'account_number' => $cardInfo->depositNumber ?? '',
                    'bank_name' => $this->getBankNameFromCode($cardInfo->bank ?? ''),
                    'account_type' => $cardInfo->type ?? 'جاری',
                    'owner_name' => $cardInfo->ownerName ?? '',
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
                    'message' => 'خطا در دریافت اطلاعات از سرویس خارجی.'
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
     * Get preview data for card to account service
     * Uses only card inquiry API (not card-to-account conversion)
     *
     * @param array $serviceData
     * @param Service $service
     * @return array
     */
    public function getPreviewData(array $serviceData, Service $service): array
    {
        try {
            $cardNumber = $serviceData['card_number'] ?? '';
            
            Log::info('🚀 CardAccountController: Starting getPreviewData (card inquiry only)', [
                'service_id' => $service->id,
                'service_slug' => $service->slug,
                'card_number_masked' => $cardNumber ? substr($cardNumber, 0, 6) . '******' . substr($cardNumber, -4) : 'N/A',
            ]);
            
            if (!preg_match('/^\d{16}$/', $cardNumber)) {
                Log::error('❌ CardAccountController: Invalid card number format', [
                    'card_number_masked' => $cardNumber ? substr($cardNumber, 0, 6) . '******' . substr($cardNumber, -4) : 'N/A',
                    'card_length' => strlen($cardNumber),
                    'is_numeric' => is_numeric($cardNumber)
                ]);
                return [
                    'success' => false,
                    'message' => 'شماره کارت معتبر نیست.'
                ];
            }

            // Check cache first
            $cachedData = PreviewCacheService::getCardInquiry($cardNumber);
            if ($cachedData) {
                Log::info('🎯 CardAccountController: Using cached data', [
                    'card_number_masked' => substr($cardNumber, 0, 6) . '******' . substr($cardNumber, -4),
                    'cached_at' => $cachedData['cached_at'] ?? 'unknown',
                    'has_owner_name' => isset($cachedData['owner_name']),
                    'has_bank_name' => isset($cachedData['bank_name']),
                    'has_bank_logo' => isset($cachedData['bank_logo'])
                ]);
                
                // Remove cache metadata and sensitive data before returning
                $previewData = $cachedData;
                unset($previewData['cached_at'], $previewData['cache_type'], $previewData['card_hash']);
                unset($previewData['iban'], $previewData['account_number']); // Hide sensitive data
                
                return [
                    'success' => true,
                    'preview_data' => $previewData,
                    'from_cache' => true
                ];
            }

            // Use Jibit API to get card information for preview (card inquiry only)
            $jibitService = app(\App\Services\JibitService::class);
            
            Log::info('🔗 CardAccountController: Calling Jibit API for card inquiry only', [
                'card_number_masked' => substr($cardNumber, 0, 6) . '******' . substr($cardNumber, -4),
                'jibit_service_class' => get_class($jibitService)
            ]);
            
            try {
                // Use card inquiry API only (not card-to-account conversion)
                $apiResult = $jibitService->getCard($cardNumber);
                
                Log::info('📡 CardAccountController: Jibit card inquiry API response received', [
                    'card_number_masked' => substr($cardNumber, 0, 6) . '******' . substr($cardNumber, -4),
                    'has_result' => !empty($apiResult),
                    'result_type' => gettype($apiResult),
                    'has_cardInfo' => isset($apiResult->cardInfo),
                ]);
                
                if ($apiResult && isset($apiResult->cardInfo)) {
                    $ownerName = $apiResult->cardInfo->ownerName ?? '';
                    $bankCode = $apiResult->cardInfo->bank ?? '';
                    $bankName = $this->getBankNameFromCode($bankCode);
                    
                    Log::info('✅ CardAccountController: Card inquiry API data extracted', [
                        'card_number_masked' => substr($cardNumber, 0, 6) . '******' . substr($cardNumber, -4),
                        'owner_name' => $ownerName,
                        'bank_code' => $bankCode,
                        'bank_name_from_code' => $bankName,
                    ]);
                    
                    // If bank name is unknown, try to get it from card prefix
                    if ($bankName === 'نامشخص' || empty($bankName)) {
                        $bankName = $this->getBankNameFromCard($cardNumber);
                        Log::info('🔍 CardAccountController: Bank name resolved from card prefix', [
                            'card_prefix' => substr($cardNumber, 0, 6),
                            'resolved_bank_name' => $bankName
                        ]);
                    }
                    
                    // Get bank logo using IranianBankHelper
                    $bankLogo = IranianBankHelper::getBankLogoPath($bankName);
                    
                    $previewData = [
                        'owner_name' => $ownerName,
                        'bank_name' => $bankName,
                        'bank_logo' => $bankLogo,
                        'engagement_message' => $ownerName ? "کارت متعلق به {$ownerName} است - شماره حساب را دریافت کنید!" : "اطلاعات کارت شما آماده است - شماره حساب را دریافت کنید!"
                    ];
                    
                    Log::info('🎉 CardAccountController: Preview data created successfully from card inquiry API', [
                        'card_number_masked' => substr($cardNumber, 0, 6) . '******' . substr($cardNumber, -4),
                        'preview_data' => $previewData
                    ]);
                    
                    // Cache the successful result
                    PreviewCacheService::setCardInquiry($cardNumber, $previewData);
                    
                    return [
                        'success' => true,
                        'preview_data' => $previewData,
                        'from_cache' => false
                    ];
                } else {
                    Log::warning('⚠️ CardAccountController: Card inquiry API response was not successful or missing cardInfo', [
                        'card_number_masked' => substr($cardNumber, 0, 6) . '******' . substr($cardNumber, -4),
                        'has_result' => !empty($apiResult),
                        'has_cardInfo' => isset($apiResult->cardInfo),
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('💥 CardAccountController: Error calling Jibit card inquiry API', [
                    'error' => $e->getMessage(),
                    'card_number_masked' => substr($cardNumber, 0, 6) . '******' . substr($cardNumber, -4),
                    'trace' => $e->getTraceAsString()
                ]);
            }
            
            // Fallback to basic bank info if API fails
            $bankName = $this->getBankNameFromCard($cardNumber);
            $bankLogo = IranianBankHelper::getBankLogoPath($bankName);
            
            Log::info('🔄 CardAccountController: Using fallback bank detection', [
                'card_number_masked' => substr($cardNumber, 0, 6) . '******' . substr($cardNumber, -4),
                'detected_bank' => $bankName,
                'bank_logo' => $bankLogo
            ]);
            
            $fallbackPreviewData = [
                'bank_name' => $bankName,
                'bank_logo' => $bankLogo,
                'engagement_message' => "کارت {$bankName} - شماره حساب را دریافت کنید!"
            ];
            
            Log::info('✅ CardAccountController: Fallback preview data created', [
                'card_number_masked' => substr($cardNumber, 0, 6) . '******' . substr($cardNumber, -4),
                'fallback_preview_data' => $fallbackPreviewData
            ]);
            
            // Cache the fallback result (shorter TTL since it's less reliable)
            PreviewCacheService::setCardInquiry($cardNumber, $fallbackPreviewData);
            
            return [
                'success' => true,
                'preview_data' => $fallbackPreviewData,
                'from_cache' => false
            ];
        } catch (\Exception $e) {
            Log::error('💥 CardAccountController: Failed to get card preview data', [
                'error' => $e->getMessage(),
                'card_number_masked' => isset($cardNumber) ? substr($cardNumber, 0, 6) . '******' . substr($cardNumber, -4) : 'N/A',
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return basic preview without API data
            return [
                'success' => true,
                'preview_data' => [
                    'engagement_message' => 'کارت شما آماده است - شماره حساب را دریافت کنید!'
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
            'MELLI' => 'ملی',
            'SEPAH' => 'سپه',
            'TOSEE_SADERAT' => 'توسعه صادرات',
            'SANAT_VA_MADAN' => 'صنعت و معدن',
            'KESHAVARZI' => 'کشاورزی',
            'MASKAN' => 'مسکن',
            'POST_BANK' => 'پست بانک',
            'GHARZOLHASANEH' => 'قوامین',
            'AYANDEH' => 'آینده',
            'SHAHR' => 'شهر',
            'ASIA' => 'آسیا',
            'GARDESHGARI' => 'گردشگری',
            'EGHTESAD_NOVIN' => 'اقتصاد نوین',
            'IRAN_ZAMIN' => 'ایران زمین',
            'MARKAZI' => 'مرکزی',
            'TOSEE_TAVON' => 'توسعه تعاون',
            'KARAFARIN' => 'کارآفرین',
            'PASARGAD' => 'پاسارگاد',
            'PARSIAN' => 'پارسیان',
            'SAMAN' => 'سامان',
            'SINA' => 'سینا',
            'TOSEE' => 'توسعه',
        ];

        return $bankCodes[$bankCode] ?? 'نامشخص';
    }

    /**
     * Get bank name from card number
     *
     * @param string $cardNumber
     * @return string
     */
    private function getBankNameFromCard(string $cardNumber): string
    {
        $bankCodes = [
            '603799' => 'ملی',
            '589210' => 'سپه',
            '627648' => 'توسعه صادرات',
            '627961' => 'صنعت و معدن',
            '603770' => 'کشاورزی',
            '628023' => 'مسکن',
            '627760' => 'پست بانک',
            '639599' => 'قوامین',
            '636214' => 'آینده',
            '502806' => 'شهر',
            '504172' => 'آسیا',
            '505416' => 'گردشگری',
            '636949' => 'اقتصاد نوین',
            '505785' => 'ایران زمین',
            '636795' => 'مرکزی',
            '628157' => 'توسعه تعاون',
            '504706' => 'کارآفرین',
            '502229' => 'پاسارگاد',
            '622106' => 'پارسیان',
            '639194' => 'پارسیان',
            '621986' => 'سامان',
            '639607' => 'سینا',
            '636214' => 'آینده',
            '502908' => 'توسعه',
            '621299' => 'آینده',
        ];

        $bankCode = substr($cardNumber, 0, 6);
        
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

        // Filter out sensitive fields from input data
        $inputData = $result->input_data;
        unset($inputData['card_number_clean']);

        return view('front.services.result', [
            'service' => $service,
            'result' => $formattedResult,
            'inputData' => $inputData,
            'resultId' => $resultId,
        ]);
    }

    /**
     * Show progress page (default implementation for interface compatibility)
     * Card to account service doesn't use background processing
     */
    public function showProgress(Request $request, Service $service, string $hash)
    {
        return redirect()->route('services.show', $service->slug)
            ->withErrors(['error' => 'این سرویس از صفحه پیشرفت پشتیبانی نمی‌کند.']);
    }

    /**
     * Handle OTP submission (default implementation for interface compatibility)
     * Card to account service doesn't require OTP verification
     */
    public function handleOtpSubmission(Request $request, Service $service)
    {
        return redirect()->route('services.show', $service->slug)
            ->withErrors(['error' => 'این سرویس از احراز هویت با کد یکبار مصرف پشتیبانی نمی‌کند.']);
    }

    /**
     * Show SMS verification page (default implementation for interface compatibility)
     * Card to account service doesn't require SMS verification
     */
    public function showSmsVerification(Request $request, Service $service, string $hash)
    {
        return redirect()->route('services.show', $service->slug)
            ->withErrors(['error' => 'این سرویس از احراز هویت پیامکی پشتیبانی نمی‌کند.']);
    }

    /**
     * Handle SMS OTP verification (default implementation for interface compatibility)
     * Card to account service doesn't require SMS verification
     */
    public function handleSmsOtpVerification(Request $request, Service $service, string $hash)
    {
        return redirect()->route('services.show', $service->slug)
            ->withErrors(['error' => 'این سرویس از احراز هویت پیامکی پشتیبانی نمی‌کند.']);
    }

    /**
     * Show SMS result page (default implementation for interface compatibility)
     * Card to account service uses standard result display
     */
    public function showSmsResult(Request $request, Service $service, string $id)
    {
        // Redirect to standard result page
        return $this->show($id, $service);
    }
} 