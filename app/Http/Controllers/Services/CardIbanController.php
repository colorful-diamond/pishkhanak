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

class CardIbanController extends Controller implements BaseServiceController, ServicePreviewInterface
{
    use ServicePaymentTrait;
    
    /**
     * Handle card to IBAN conversion
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
            'version' => '2',  // API requires version 2
        ];
    }

    /**
     * Process card to IBAN conversion and return result data
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
            
            // Use Jibit API for real card to IBAN conversion
            $jibitService = app(\App\Services\JibitService::class);
            
            // Get card to IBAN conversion
            $ibanResult = $jibitService->getCardToIban($cardNumber);
            
            if ($ibanResult && isset($ibanResult->cardInfo)) {
                $cardInfo = $ibanResult->cardInfo;
                // Get IBAN - try different possible field locations
                $iban = $ibanResult->iban ?? $cardInfo->iban ?? $ibanResult->depositNumber ?? '';
                
                $result = [
                    'iban' => $iban, // This is the actual IBAN from card to IBAN conversion
                    'bank_name' => $this->getBankNameFromCode($cardInfo->bank ?? ''),
                    'account_type' => $cardInfo->type ?? 'جاری',
                    'owner_name' => $cardInfo->ownerName ?? '',
                    'conversion_date' => Verta::now()->format('Y/n/j H:i:s'),
                    'api_response' => $ibanResult,
                ];
                
                return [
                    'success' => true,
                    'data' => $result
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'امکان تبدیل این کارت به شبا وجود ندارد.'
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
     * Generate a mock IBAN for demonstration
     *
     * @param string $cardNumber
     * @return string
     */
    private function generateMockIban(string $cardNumber): string
    {
        // Extract bank code from card number (first 6 digits)
        $bankCode = substr($cardNumber, 0, 6);
        
        // Generate a mock account number
        $accountNumber = str_pad(rand(1, 999999999), 9, '0', STR_PAD_LEFT);
        
        // Generate IBAN: IR + 2 check digits + 4 bank code + 10 account number
        $iban = 'IR' . str_pad(rand(10, 99), 2, '0', STR_PAD_LEFT) . $bankCode . $accountNumber;
        
        return $iban;
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
     * Get preview data for card to IBAN service
     * Uses only card inquiry API (not card-to-IBAN conversion)
     *
     * @param array $serviceData
     * @param Service $service
     * @return array
     */
    public function getPreviewData(array $serviceData, Service $service): array
    {
        try {
            $cardNumber = $serviceData['card_number'] ?? '';
            
            if (!preg_match('/^\d{16}$/', $cardNumber)) {
                return [
                    'success' => false,
                    'message' => 'شماره کارت معتبر نیست.'
                ];
            }

            // Check cache first
            $cachedData = PreviewCacheService::getCardInquiry($cardNumber);
            if ($cachedData) {
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
            
            try {
                // Use card inquiry API only (not card-to-IBAN conversion)
                $apiResult = $jibitService->getCard($cardNumber);
                
                if ($apiResult && isset($apiResult->cardInfo)) {
                    $ownerName = $apiResult->cardInfo->ownerName ?? '';
                    $bankCode = $apiResult->cardInfo->bank ?? '';
                    $bankName = $this->getBankNameFromCode($bankCode);
                    
                    // If bank name is unknown, try to get it from card prefix
                    if ($bankName === 'نامشخص' || empty($bankName)) {
                        $bankName = $this->getBankNameFromCard($cardNumber);
                    }
                    
                    // Get bank logo using IranianBankHelper
                    $bankLogo = IranianBankHelper::getBankLogoPath($bankName);
                    
                    $previewData = [
                        'owner_name' => $ownerName,
                        'bank_name' => $bankName,
                        'bank_logo' => $bankLogo,
                        'engagement_message' => $ownerName ? "کارت متعلق به {$ownerName} است - شماره شبا را دریافت کنید!" : "اطلاعات کارت شما آماده است - شماره شبا را دریافت کنید!"
                    ];
                    
                    // Cache the successful result
                    PreviewCacheService::setCardInquiry($cardNumber, $previewData);
                    
                    return [
                        'success' => true,
                        'preview_data' => $previewData,
                        'from_cache' => false
                    ];
                }
            } catch (\Exception $e) {
                Log::error('Error calling Jibit card inquiry API', [
                    'error' => $e->getMessage(),
                ]);
            }
            
            // Fallback to basic bank info if API fails
            $bankName = $this->getBankNameFromCard($cardNumber);
            $bankLogo = IranianBankHelper::getBankLogoPath($bankName);
            
            $fallbackPreviewData = [
                'bank_name' => $bankName,
                'bank_logo' => $bankLogo,
                'engagement_message' => "کارت {$bankName} - شماره شبا را دریافت کنید!"
            ];
            
            // Cache the fallback result (shorter TTL since it's less reliable)
            PreviewCacheService::setCardInquiry($cardNumber, $fallbackPreviewData);
            
            return [
                'success' => true,
                'preview_data' => $fallbackPreviewData,
                'from_cache' => false
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get card preview data', [
                'error' => $e->getMessage(),
            ]);
            
            // Return basic preview without API data
            return [
                'success' => true,
                'preview_data' => [
                    'engagement_message' => 'کارت شما آماده است - شماره شبا را دریافت کنید!'
                ]
            ];
        }
    }



    /**
     * Check if this service supports preview
     *
     * @return bool
     */
    public function supportsPreview(): bool
    {
        return true;
    }

    /**
     * Get the preview template name for this service
     *
     * @return string
     */
    public function getPreviewTemplate(): string
    {
        return 'services.preview';
    }

    /**
     * Show the service result
     *
     * @param string $resultId
     * @param Service $service
     * @return \Illuminate\Http\Response
     */
    public function show(string $resultId, Service $service)
    {
        // Find the result by hash
        $result = ServiceResult::where('result_hash', $resultId)
            ->where('service_id', $service->id)
            ->where('status', 'success')
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
     * Card to IBAN service doesn't use background processing
     */
    public function showProgress(Request $request, Service $service, string $hash)
    {
        return redirect()->route('services.show', $service->slug)
            ->withErrors(['error' => 'این سرویس از صفحه پیشرفت پشتیبانی نمی‌کند.']);
    }

    /**
     * Handle OTP submission (default implementation for interface compatibility)
     * Card to IBAN service doesn't require OTP verification
     */
    public function handleOtpSubmission(Request $request, Service $service)
    {
        return redirect()->route('services.show', $service->slug)
            ->withErrors(['error' => 'این سرویس از احراز هویت با کد یکبار مصرف پشتیبانی نمی‌کند.']);
    }

    /**
     * Show SMS verification page (default implementation for interface compatibility)
     * Card to IBAN service doesn't require SMS verification
     */
    public function showSmsVerification(Request $request, Service $service, string $hash)
    {
        return redirect()->route('services.show', $service->slug)
            ->withErrors(['error' => 'این سرویس از احراز هویت پیامکی پشتیبانی نمی‌کند.']);
    }

    /**
     * Handle SMS OTP verification (default implementation for interface compatibility)
     * Card to IBAN service doesn't require SMS verification
     */
    public function handleSmsOtpVerification(Request $request, Service $service, string $hash)
    {
        return redirect()->route('services.show', $service->slug)
            ->withErrors(['error' => 'این سرویس از احراز هویت پیامکی پشتیبانی نمی‌کند.']);
    }

    /**
     * Show SMS result page (default implementation for interface compatibility)
     * Card to IBAN service uses standard result display
     */
    public function showSmsResult(Request $request, Service $service, string $id)
    {
        // Redirect to standard result page
        return $this->show($id, $service);
    }
} 