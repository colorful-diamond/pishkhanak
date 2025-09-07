<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\BaseServiceController;
use App\Http\Controllers\Services\ServicePaymentTrait;
use App\Contracts\ServicePreviewInterface;
use App\Models\Service;
use App\Models\ServiceResult;
use App\Models\Bank;
use App\Services\PreviewCacheService;
use App\Helpers\IranianBankHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Hekmatinasser\Verta\Verta;

class AccountIbanController extends Controller implements BaseServiceController, ServicePreviewInterface
{
    use ServicePaymentTrait;

    /**
     * Handle account to IBAN conversion
     *
     * @param Request $request
     * @param Service $service
     * @return \Illuminate\Http\Response
     */
    public function handle(Request $request, Service $service)
    {
        // CRITICAL: Confirm handle method is being called
        Log::error('🚨 AccountIbanController: HANDLE METHOD CALLED!', [
            'service_slug' => $service->slug,
            'request_method' => $request->method(),
            'timestamp' => now()->toIso8601String(),
        ]);
        
        // Debug ALL request data first
        Log::error('🔍 AccountIbanController: Full request debug', [
            'all_request_data' => $request->all(),
            'account_number_raw' => $request->input('account_number'),
            'bank_id_raw' => $request->input('bank_id'),
            'request_method' => $request->method(),
            'content_type' => $request->header('Content-Type'),
        ]);

        // Clean account number first
        $cleanAccountNumber = preg_replace('/\D/', '', $request->input('account_number', ''));
        
        // Validate account number and bank selection
        $validator = Validator::make([
            'account_number' => $cleanAccountNumber,
            'bank_id' => $request->input('bank_id'),
        ], [
            'account_number' => 'required|string|min:8|max:20',
            'bank_id' => 'required|integer|exists:banks,id',
        ], [
            'account_number.required' => 'شماره حساب الزامی است.',
            'account_number.min' => 'شماره حساب باید حداقل 8 رقم باشد.',
            'account_number.max' => 'شماره حساب نباید بیش از 20 رقم باشد.',
            'bank_id.required' => 'انتخاب بانک الزامی است.',
            'bank_id.exists' => 'بانک انتخابی معتبر نیست.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        // Follow the same pattern as card services - pass simple data only
        $serviceData = [
            'account_number' => $cleanAccountNumber,
            'bank_id' => $request->input('bank_id'),
        ];
        
        // Handle service with payment check
        return $this->handleServiceWithPayment($request, $service, $serviceData);
    }

    /**
     * Prepare API parameters from service data
     */
    protected function prepareApiParameters(array $serviceData): array
    {
        Log::info('🔧 Preparing Jibit API parameters', [
            'service_data' => $serviceData,
            'bank_code' => $serviceData['bank_code'] ?? 'NOT_SET',
            'account_number' => $serviceData['account_number'] ?? 'NOT_SET',
        ]);

        return [
            'bank' => (string) ($serviceData['bank_code'] ?? ''),
            'number' => (string) ($serviceData['account_number'] ?? ''),
            'iban' => true,
        ];
    }

    /**
     * Process account to IBAN conversion and return result data
     *
     * @param array $serviceData
     * @param Service $service
     * @return array
     */
    public function process(array $serviceData, Service $service): array
    {
        // CRITICAL DEBUG: What data is actually being passed to process?
        Log::error('🚨 AccountIbanController: PROCESS METHOD CALLED WITH DATA', [
            'raw_service_data' => $serviceData,
            'service_data_keys' => array_keys($serviceData),
            'has_account_number' => isset($serviceData['account_number']),
            'has_bank_id' => isset($serviceData['bank_id']),
            'has_bank_code' => isset($serviceData['bank_code']),
            'has_bank_name' => isset($serviceData['bank_name']),
            'service_id' => $service->id,
            'service_slug' => $service->slug,
        ]);
                 
        try {
            $accountNumber = $serviceData['account_number'] ?? '';
            $bankId = $serviceData['bank_id'] ?? '';
            
            // Do bank lookup in process() method, not in handle()
            $bank = Bank::find($bankId);

            if (!$bank) {
                throw new \Exception('بانک انتخاب شده یافت نشد.');
            }

            Log::info('✅ Bank found successfully', [
                'bank_id' => $bank->id,
                'bank_name' => $bank->name,
                'bank_en_name' => $bank->en_name,
                'bank_bank_id' => $bank->bank_id,
            ]);

            // Try Finnotech first, then fallback to Jibit
            $result = $this->tryFinnotechFirst($accountNumber, $bank);
            if ($result !== null) {
                return $result;
            }

            // Fallback to Jibit
            return $this->tryJibitFallback($accountNumber, $bank);

        } catch (\Exception $e) {
            Log::error('❌ AccountIbanController process failed', [
                'error' => $e->getMessage(),
                'service_data' => $serviceData,
            ]);

            throw $e;
        }
    }

    /**
     * Try Finnotech as primary provider
     */
    private function tryFinnotechFirst(string $accountNumber, Bank $bank): ?array
    {
        try {
            Log::info('🔄 Trying Finnotech as primary provider', [
                'account_number' => $accountNumber,
                'bank_name' => $bank->name,
            ]);

            // Get Finnotech bank code
            $finnotechBankCode = $this->getFinnotechBankCode($bank);
            
            Log::info('🏦 Finnotech bank code generated', [
                'bank_name' => $bank->name,
                'finnotech_code' => $finnotechBankCode,
            ]);

            // Call Finnotech service
            $finnotechService = app(\App\Services\Finnotech\FinnotechService::class);
            $endpoint = "/facility/v2/clients/pishkhanak/depositToIban";
            
            $response = $finnotechService->makeApiRequest($endpoint, [
                'deposit' => $accountNumber,
                'bankCode' => $finnotechBankCode,
            ]);

            if ($response && isset($response->result)) {
                Log::info('✅ Finnotech success', [
                    'response' => $response,
                ]);

                return [
                    'success' => true,
                    'data' => [
                        'provider' => 'finnotech',
                        'account_number' => $response->result->deposit ?? $accountNumber,
                        'iban' => $response->result->iban ?? '',
                        'bank_name' => $response->result->bankName ?? $bank->name,
                        'account_status' => $response->result->accountStatus ?? '',
                        'account_owners' => $response->result->depositOwners ?? '',
                        'raw_response' => $response,
                    ],
                    'message' => 'تبدیل حساب به شبا با موفقیت انجام شد (فینوتک)'
                ];
            }

            Log::warning('⚠️ Finnotech returned empty/invalid response', [
                'response' => $response,
            ]);
            return null;

        } catch (\Exception $e) {
            Log::warning('⚠️ Finnotech failed, will try Jibit fallback', [
                'error' => $e->getMessage(),
                'account_number' => $accountNumber,
                'bank_name' => $bank->name,
            ]);
            return null;
        }
    }

    /**
     * Try Jibit as fallback provider
     */
    private function tryJibitFallback(string $accountNumber, Bank $bank): array
    {
        try {
            Log::info('🔄 Using Jibit as fallback provider', [
                'account_number' => $accountNumber,
                'bank_name' => $bank->name,
            ]);

            // Get Jibit bank slug
            $jibitSlug = $this->getJibitBankSlug($bank);
            
            Log::info('🏦 Jibit bank slug generated', [
                'bank_name' => $bank->name,
                'jibit_slug' => $jibitSlug,
            ]);

            // Call Jibit service
            $jibitService = app(\App\Services\JibitService::class);
            $response = $jibitService->getAccountToIban($jibitSlug, $accountNumber);

            if ($response && isset($response->iban)) {
                Log::info('✅ Jibit fallback success', [
                    'response' => $response,
                ]);

                return [
                    'success' => true,
                    'data' => [
                        'provider' => 'jibit',
                        'account_number' => $accountNumber,
                        'iban' => $response->iban ?? '',
                        'bank_name' => $bank->name,
                        'raw_response' => $response,
                    ],
                    'message' => 'تبدیل حساب به شبا با موفقیت انجام شد (جیبیت)'
                ];
            }

            throw new \Exception('هر دو سرویس فینوتک و جیبیت در دریافت شبا ناموفق بودند.');

        } catch (\Exception $e) {
            Log::error('❌ Both Finnotech and Jibit failed', [
                'error' => $e->getMessage(),
                'account_number' => $accountNumber,
                'bank_name' => $bank->name,
            ]);

            throw new \Exception('خطا در تبدیل حساب به شبا: ' . $e->getMessage());
        }
    }

    /**
     * Get Finnotech bank code from Bank model
     * Maps bank names to Finnotech 3-digit bank codes
     */
    private function getFinnotechBankCode(Bank $bank): string
    {
        // Finnotech bank code mapping (3-digit codes)
        $finnotechBankMapping = [
            // Core Banks
            'بانک مرکزی' => '001',
            'بانک مرکزی جمهوری اسلامی ایران' => '001',
            'مرکزی' => '001',
            
            'بانک صنعت و معدن' => '011',
            'صنعت و معدن' => '011',
            
            'بانک ملت' => '012',
            'ملت' => '012',
            
            'بانک رفاه کارگران' => '013',
            'رفاه کارگران' => '013',
            'رفاه' => '013',
            
            'بانک مسکن' => '014',
            'مسکن' => '014',
            
            'بانک سپه' => '015',
            'سپه' => '015',
            
            'بانک کشاورزی' => '016',
            'کشاورزی' => '016',
            
            'بانک ملی ایران' => '017',
            'ملی ایران' => '017',
            'ملی' => '017',
            
            'بانک تجارت' => '018',
            'تجارت' => '018',
            
            'بانک صادرات ایران' => '019',
            'صادرات ایران' => '019',
            'صادرات' => '019',
            
            'بانک توسعه صادرات ایران' => '020',
            'توسعه صادرات ایران' => '020',
            'توسعه صادرات' => '020',
            
            'پست بانک ایران' => '021',
            'پست بانک' => '021',
            'پست' => '021',
            
            'بانک توسعه تعاون' => '022',
            'توسعه تعاون' => '022',
            
            // Private Banks  
            'بانک کارآفرین' => '055',
            'کارآفرین' => '055',
            
            'بانک پارسیان' => '054',
            'پارسیان' => '054',
            
            'بانک اقتصاد نوین' => '055',
            'اقتصاد نوین' => '055',
            
            'بانک سامان' => '056',
            'سامان' => '056',
            
            'بانک پاسارگاد' => '057',
            'پاسارگاد' => '057',
            
            'بانک سرمایه' => '058',
            'سرمایه' => '058',
            
            'بانک سینا' => '059',
            'سینا' => '059',
            
            'بانک قرض‌الحسنه مهر ایران' => '060',
            'قرض‌الحسنه مهر ایران' => '060',
            'مهر ایران' => '060',
            
            'بانک شهر' => '061',
            'شهر' => '061',
            
            'بانک آینده' => '062',
            'آینده' => '062',
            
            'بانک گردشگری' => '063',
            'گردشگری' => '063',
            
            'بانک دی' => '064',
            'دی' => '064',
            
            'بانک ایران زمین' => '065',
            'ایران زمین' => '065',
            
            'بانک قرض‌الحسنه رسالت' => '066',
            'قرض‌الحسنه رسالت' => '066',
            'رسالت' => '066',
            
            // Credit Institutions
            'موسسه اعتباری ملل' => '067',
            'اعتباری ملل' => '067',
            'ملل' => '067',
            
            'بانک خاورمیانه' => '068',
            'خاورمیانه' => '068',
            
            'موسسه اعتباری نور' => '069',
            'اعتباری نور' => '069',
            'نور' => '069',
            
            'بانک دوملیته ایران ونزوئلا' => '070',
            'دوملیته ایران ونزوئلا' => '070',
            'ایران ونزوئلا' => '070',
        ];

        Log::info('🏦 Finnotech Bank Code Mapping Debug', [
            'bank_id' => $bank->id,
            'bank_name' => $bank->name,
            'bank_en_name' => $bank->en_name,
            'bank_bank_id' => $bank->bank_id,
        ]);

        // Try multiple matching strategies
        $searchNames = [
            $bank->name,
            $bank->en_name,
            $bank->bank_id,
            trim($bank->name),
            trim($bank->en_name),
            trim($bank->bank_id),
        ];

        foreach ($searchNames as $searchName) {
            if (!empty($searchName) && isset($finnotechBankMapping[$searchName])) {
                $code = $finnotechBankMapping[$searchName];
                Log::info('✅ Finnotech bank code found', [
                    'search_name' => $searchName,
                    'matched_code' => $code,
                ]);
                return $code;
            }
        }

        // Partial matching for Persian names
        foreach ($finnotechBankMapping as $mapName => $code) {
            if (strpos($bank->name, $mapName) !== false || strpos($mapName, $bank->name) !== false) {
                Log::info('✅ Finnotech bank code found via partial match', [
                    'bank_name' => $bank->name,
                    'map_name' => $mapName,
                    'matched_code' => $code,
                ]);
                return $code;
            }
        }

        // If no mapping found, log the error
        Log::error('❌ No Finnotech bank code mapping found for bank', [
            'bank_id' => $bank->id,
            'bank_name' => $bank->name,
            'bank_en_name' => $bank->en_name,
            'bank_bank_id' => $bank->bank_id,
            'available_mappings' => array_keys($finnotechBankMapping),
        ]);

        throw new \Exception("کد بانک فینوتک برای {$bank->name} پیدا نشد.");
    }

    /**
     * Get preview data for account to IBAN conversion
     *
     * @param array $serviceData
     * @param Service $service
     * @return array
     */
    public function getPreviewData(array $serviceData, Service $service): array
    {
        try {
            $accountNumber = $serviceData['account_number'] ?? '';
            $bankCode = $serviceData['bank_code'] ?? '';
            $bankName = $serviceData['bank_name'] ?? '';
            
            Log::info('🚀 AccountIbanController: Starting account-to-IBAN preview', [
                'service_id' => $service->id,
                'service_slug' => $service->slug,
                'account_number_masked' => $accountNumber ? substr($accountNumber, 0, 4) . '***' . substr($accountNumber, -4) : 'N/A',
                'bank_code' => $bankCode,
                'bank_name' => $bankName,
            ]);
            
            // Check cache first
            $cachedData = PreviewCacheService::getAccountInquiry($bankCode, $accountNumber);
            if ($cachedData) {
                Log::info('🎯 AccountIbanController: Using cached account-to-IBAN data', [
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

            // Get bank info for preview
            $bank = Bank::find($serviceData['bank_id'] ?? null);
            if ($bank) {
                $bankLogo = Str::startsWith($bank->logo, 'http') ? $bank->logo : asset('assets/images/banks/' . $bank->logo);
                
                $previewData = [
                    'bank_name' => $bank->name,
                    'bank_logo' => $bankLogo,
                    'engagement_message' => "حساب {$bank->name} - شماره شبا را دریافت کنید!"
                ];
                
                Log::info('🎉 AccountIbanController: Account-to-IBAN preview data created', [
                    'account_number_masked' => substr($accountNumber, 0, 4) . '***' . substr($accountNumber, -4),
                    'bank_code' => $bankCode,
                    'bank_name' => $bank->name,
                    'bank_logo_path' => $bankLogo,
                ]);
                
                // Cache the result
                PreviewCacheService::setAccountInquiry($bankCode, $accountNumber, $previewData);
                
                return [
                    'success' => true,
                    'preview_data' => $previewData,
                    'from_cache' => false
                ];
            }
            
            // Fallback preview
            $fallbackPreviewData = [
                'bank_name' => $bankName ?: 'بانک انتخابی',
                'engagement_message' => "حساب شما - شماره شبا را دریافت کنید!"
            ];
            
            return [
                'success' => true,
                'preview_data' => $fallbackPreviewData,
                'from_cache' => false
            ];
            
        } catch (\Exception $e) {
            Log::error('💥 AccountIbanController: Failed to get account-to-IBAN preview', [
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
     * Format IBAN for display
     *
     * @param string $iban
     * @return string
     */
    private function formatIban(string $iban): string
    {
        // Remove any spaces and convert to uppercase
        $iban = strtoupper(str_replace(' ', '', $iban));
        
        // Add spaces every 4 characters for readability
        return chunk_split($iban, 4, ' ');
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
     */
    public function showProgress(Request $request, Service $service, string $hash)
    {
        return redirect()->route('services.show', $service->slug)
            ->withErrors(['error' => 'این سرویس از صفحه پیشرفت پشتیبانی نمی‌کند.']);
    }

    /**
     * Handle OTP submission (default implementation for interface compatibility)
     */
    public function handleOtpSubmission(Request $request, Service $service)
    {
        return redirect()->route('services.show', $service->slug)
            ->withErrors(['error' => 'این سرویس از احراز هویت با کد یکبار مصرف پشتیبانی نمی‌کند.']);
    }

    /**
     * Show SMS verification page (default implementation for interface compatibility)
     */
    public function showSmsVerification(Request $request, Service $service, string $hash)
    {
        return redirect()->route('services.show', $service->slug)
            ->withErrors(['error' => 'این سرویس از احراز هویت پیامکی پشتیبانی نمی‌کند.']);
    }

    /**
     * Handle SMS OTP verification (default implementation for interface compatibility)
     */
    public function handleSmsOtpVerification(Request $request, Service $service, string $hash)
    {
        return redirect()->route('services.show', $service->slug)
            ->withErrors(['error' => 'این سرویس از احراز هویت پیامکی پشتیبانی نمی‌کند.']);
    }

    /**
     * Show SMS result page (default implementation for interface compatibility)
     */
    public function showSmsResult(Request $request, Service $service, string $id)
    {
        // Redirect to standard result page
        return $this->show($id, $service);
    }

    /**
     * Get Jibit bank slug from Bank model
     * Maps bank names to official Jibit bank slugs
     */
    private function getJibitBankSlug(Bank $bank): string
    {
        // Official Jibit bank slug mapping
        $jibitSlugMapping = [
            // Core Banks
            'بانک مرکزی' => 'MARKAZI',
            'بانک مرکزی جمهوری اسلامی ایران' => 'MARKAZI',
            'مرکزی' => 'MARKAZI',
            'MARKAZI' => 'MARKAZI',
            
            'بانک صنعت و معدن' => 'SANAT_VA_MADAN',
            'صنعت و معدن' => 'SANAT_VA_MADAN',
            'SANAT_VA_MADAN' => 'SANAT_VA_MADAN',
            
            'بانک ملت' => 'MELLAT',
            'ملت' => 'MELLAT',
            'MELLAT' => 'MELLAT',
            
            'بانک رفاه کارگران' => 'REFAH',
            'رفاه کارگران' => 'REFAH',
            'رفاه' => 'REFAH',
            'REFAH' => 'REFAH',
            
            'بانک مسکن' => 'MASKAN',
            'مسکن' => 'MASKAN',
            'MASKAN' => 'MASKAN',
            
            'بانک سپه' => 'SEPAH',
            'سپه' => 'SEPAH',
            'SEPAH' => 'SEPAH',
            
            'بانک کشاورزی' => 'KESHAVARZI',
            'کشاورزی' => 'KESHAVARZI',
            'KESHAVARZI' => 'KESHAVARZI',
            
            'بانک ملی ایران' => 'MELLI',
            'ملی ایران' => 'MELLI',
            'ملی' => 'MELLI',
            'MELLI' => 'MELLI',
            
            'بانک تجارت' => 'TEJARAT',
            'تجارت' => 'TEJARAT',
            'TEJARAT' => 'TEJARAT',
            
            'بانک صادرات ایران' => 'SADERAT',
            'صادرات ایران' => 'SADERAT',
            'صادرات' => 'SADERAT',
            'SADERAT' => 'SADERAT',
            
            'بانک توسعه صادرات ایران' => 'TOSEAH_SADERAT',
            'توسعه صادرات ایران' => 'TOSEAH_SADERAT',
            'توسعه صادرات' => 'TOSEAH_SADERAT',
            'TOSEAH_SADERAT' => 'TOSEAH_SADERAT',
            
            'پست بانک ایران' => 'POST',
            'پست بانک' => 'POST',
            'پست' => 'POST',
            'POST' => 'POST',
            
            'بانک توسعه تعاون' => 'TOSEAH_TAAVON',
            'توسعه تعاون' => 'TOSEAH_TAAVON',
            'TOSEAH_TAAVON' => 'TOSEAH_TAAVON',
            
            // Private Banks
            'بانک کارآفرین' => 'KARAFARIN',
            'کارآفرین' => 'KARAFARIN',
            'KARAFARIN' => 'KARAFARIN',
            
            'بانک پارسیان' => 'PARSIAN',
            'پارسیان' => 'PARSIAN',
            'PARSIAN' => 'PARSIAN',
            
            'بانک اقتصاد نوین' => 'EGHTESAD_NOVIN',
            'اقتصاد نوین' => 'EGHTESAD_NOVIN',
            'EGHTESAD_NOVIN' => 'EGHTESAD_NOVIN',
            
            'بانک سامان' => 'SAMAN',
            'سامان' => 'SAMAN',
            'SAMAN' => 'SAMAN',
            
            'بانک پاسارگاد' => 'PASARGAD',
            'پاسارگاد' => 'PASARGAD',
            'PASARGAD' => 'PASARGAD',
            
            'بانک سرمایه' => 'SARMAYEH',
            'سرمایه' => 'SARMAYEH',
            'SARMAYEH' => 'SARMAYEH',
            
            'بانک سینا' => 'SINA',
            'سینا' => 'SINA',
            'SINA' => 'SINA',
            
            'بانک قرض‌الحسنه مهر ایران' => 'MEHR_IRAN',
            'قرض‌الحسنه مهر ایران' => 'MEHR_IRAN',
            'مهر ایران' => 'MEHR_IRAN',
            'MEHR_IRAN' => 'MEHR_IRAN',
            
            'بانک شهر' => 'SHAHR',
            'شهر' => 'SHAHR',
            'SHAHR' => 'SHAHR',
            
            'بانک آینده' => 'AYANDEH',
            'آینده' => 'AYANDEH',
            'AYANDEH' => 'AYANDEH',
            
            'بانک گردشگری' => 'GARDESHGARI',
            'گردشگری' => 'GARDESHGARI',
            'GARDESHGARI' => 'GARDESHGARI',
            
            'بانک دی' => 'DAY',
            'دی' => 'DAY',
            'DAY' => 'DAY',
            
            'بانک ایران زمین' => 'IRANZAMIN',
            'ایران زمین' => 'IRANZAMIN',
            'IRANZAMIN' => 'IRANZAMIN',
            
            'بانک قرض‌الحسنه رسالت' => 'RESALAT',
            'قرض‌الحسنه رسالت' => 'RESALAT',
            'رسالت' => 'RESALAT',
            'RESALAT' => 'RESALAT',
            
            // Credit Institutions
            'موسسه اعتباری ملل' => 'MELAL',
            'اعتباری ملل' => 'MELAL',
            'ملل' => 'MELAL',
            'MELAL' => 'MELAL',
            
            'بانک خاورمیانه' => 'KHAVARMIANEH',
            'خاورمیانه' => 'KHAVARMIANEH',
            'KHAVARMIANEH' => 'KHAVARMIANEH',
            
            'موسسه اعتباری نور' => 'NOOR',
            'اعتباری نور' => 'NOOR',
            'نور' => 'NOOR',
            'NOOR' => 'NOOR',
            
            'بانک دوملیته ایران ونزوئلا' => 'IRAN_VENEZUELA',
            'دوملیته ایران ونزوئلا' => 'IRAN_VENEZUELA',
            'ایران ونزوئلا' => 'IRAN_VENEZUELA',
            'IRAN_VENEZUELA' => 'IRAN_VENEZUELA',
        ];

        Log::info('🏦 Bank Slug Mapping Debug', [
            'bank_id' => $bank->id,
            'bank_name' => $bank->name,
            'bank_en_name' => $bank->en_name,
            'bank_bank_id' => $bank->bank_id,
        ]);

        // Try multiple matching strategies
        $searchNames = [
            $bank->name,
            $bank->en_name,
            $bank->bank_id,
            trim($bank->name),
            trim($bank->en_name),
            trim($bank->bank_id),
        ];

        foreach ($searchNames as $searchName) {
            if (!empty($searchName) && isset($jibitSlugMapping[$searchName])) {
                $slug = $jibitSlugMapping[$searchName];
                Log::info('✅ Bank slug found', [
                    'search_name' => $searchName,
                    'matched_slug' => $slug,
                ]);
                return $slug;
            }
        }

        // Partial matching for Persian names
        foreach ($jibitSlugMapping as $mapName => $slug) {
            if (strpos($bank->name, $mapName) !== false || strpos($mapName, $bank->name) !== false) {
                Log::info('✅ Bank slug found via partial match', [
                    'bank_name' => $bank->name,
                    'map_name' => $mapName,
                    'matched_slug' => $slug,
                ]);
                return $slug;
            }
        }

        // If no mapping found, log the error and return empty
        Log::error('❌ No Jibit slug mapping found for bank', [
            'bank_id' => $bank->id,
            'bank_name' => $bank->name,
            'bank_en_name' => $bank->en_name,
            'bank_bank_id' => $bank->bank_id,
            'available_mappings' => array_keys($jibitSlugMapping),
        ]);

        throw new \Exception("کد بانک برای {$bank->name} در سیستم جیبیت پیدا نشد.");
    }
} 