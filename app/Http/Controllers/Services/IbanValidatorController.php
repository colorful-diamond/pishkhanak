<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\BaseServiceController;
use App\Models\Service;
use App\Models\ServiceResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Hekmatinasser\Verta\Verta;

class IbanValidatorController extends Controller implements BaseServiceController
{
    /**
     * Handle IBAN validation
     *
     * @param Request $request
     * @param Service $service
     * @return \Illuminate\Http\Response
     */
    public function handle(Request $request, Service $service)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'iban' => 'required|string|regex:/^IR\d{24}$/',
        ], [
            'iban.required' => 'شماره شبا الزامی است.',
            'iban.regex' => 'شماره شبا باید با IR شروع شود و 26 کاراکتر باشد.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $iban = $request->input('iban');
        
        // Process the service
        $result = $this->process(['iban' => $iban], $service);
        
        if ($result['success']) {
            // Store the result in database
            $serviceResult = ServiceResult::create([
                'service_id' => $service->id,
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'input_data' => ['iban' => $iban],
                'output_data' => $result['data'],
                'status' => 'success',
                'processed_at' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Redirect to result page
            return redirect()->route('services.result', [
                'id' => $serviceResult->result_hash
            ])->with([
                'success' => true,
                'message' => $result['data']['is_valid'] ? 'شماره شبا معتبر است.' : 'شماره شبا نامعتبر است.',
            ]);
        } else {
            return back()
                ->withErrors(['service_error' => $result['message']])
                ->withInput();
        }
    }

    /**
     * Process IBAN validation and return result data
     *
     * @param array $serviceData
     * @param Service $service
     * @return array
     */
    public function process(array $serviceData, Service $service): array
    {
        try {
            $iban = $serviceData['iban'] ?? '';
            
            // Validate IBAN
            if (!preg_match('/^IR\d{24}$/', $iban)) {
                return [
                    'success' => false,
                    'message' => 'شماره شبا باید با IR شروع شود و 26 کاراکتر باشد.'
                ];
            }
            
            // Use Jibit API for real IBAN validation
            $jibitService = app(\App\Services\JibitService::class);
            $apiResult = $jibitService->getSheba($iban);
            
            if ($apiResult && isset($apiResult->ibanInfo)) {
                $validationResult = [
                    'iban' => $iban,
                    'is_valid' => true,
                    'bank_name' => $this->getBankNameFromCode($apiResult->ibanInfo->bank ?? ''),
                    'account_number' => $apiResult->ibanInfo->depositNumber ?? '',
                    'account_status' => $apiResult->ibanInfo->status ?? 'فعال',
                    'owners' => $apiResult->ibanInfo->owners ?? [],
                    'validation_date' => Verta::now()->format('Y/n/j H:i:s'),
                    'api_response' => $apiResult,
                ];
                
                return [
                    'success' => true,
                    'data' => $validationResult
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
     * Validate IBAN number
     *
     * @param string $iban
     * @return array
     */
    private function validateIban(string $iban): array
    {
        // Basic IBAN validation for Iran
        $isValid = $this->isValidIranianIban($iban);
        
        if ($isValid) {
            $bankInfo = $this->extractBankInfo($iban);
            
            return [
                'iban' => $iban,
                'is_valid' => true,
                'bank_name' => $bankInfo['bank_name'],
                'account_number' => $bankInfo['account_number'],
                'bank_code' => $bankInfo['bank_code'],
                'validation_date' => Verta::now()->format('Y/n/j H:i:s'),
            ];
        }
        
        return [
            'iban' => $iban,
            'is_valid' => false,
            'error_message' => 'شماره شبا نامعتبر است.',
            'validation_date' => Verta::now()->format('Y/n/j H:i:s'),
        ];
    }

    /**
     * Check if IBAN is a valid Iranian IBAN
     *
     * @param string $iban
     * @return bool
     */
    private function isValidIranianIban(string $iban): bool
    {
        // Check if it starts with IR and has correct length
        if (!preg_match('/^IR\d{24}$/', $iban)) {
            return false;
        }

        // Extract check digits and account number
        $checkDigits = substr($iban, 2, 2);
        $bankCode = substr($iban, 6, 4);
        $accountNumber = substr($iban, 10, 10);

        // Basic validation - in real implementation, you would use proper IBAN validation algorithm
        return strlen($accountNumber) === 10 && is_numeric($accountNumber);
    }

    /**
     * Extract bank information from IBAN
     *
     * @param string $iban
     * @return array
     */
    private function extractBankInfo(string $iban): array
    {
        $bankCode = substr($iban, 6, 4);
        $accountNumber = substr($iban, 10, 10);
        
        return [
            'bank_name' => $this->getBankNameFromCode($bankCode),
            'account_number' => $accountNumber,
            'bank_code' => $bankCode,
        ];
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

        return view('front.services.result', [
            'service' => $service,
            'result' => $formattedResult,
            'inputData' => $result->input_data,
            'resultId' => $resultId,
        ]);
    }

    /**
     * Show progress page (default implementation for interface compatibility)
     * IBAN validator service doesn't use background processing
     */
    public function showProgress(Request $request, Service $service, string $hash)
    {
        return redirect()->route('services.show', $service->slug)
            ->withErrors(['error' => 'این سرویس از صفحه پیشرفت پشتیبانی نمی‌کند.']);
    }

    /**
     * Handle OTP submission (default implementation for interface compatibility)
     * IBAN validator service doesn't require OTP verification
     */
    public function handleOtpSubmission(Request $request, Service $service)
    {
        return redirect()->route('services.show', $service->slug)
            ->withErrors(['error' => 'این سرویس از احراز هویت با کد یکبار مصرف پشتیبانی نمی‌کند.']);
    }

    /**
     * Show SMS verification page (default implementation for interface compatibility)
     * IBAN validator service doesn't require SMS verification
     */
    public function showSmsVerification(Request $request, Service $service, string $hash)
    {
        return redirect()->route('services.show', $service->slug)
            ->withErrors(['error' => 'این سرویس از احراز هویت پیامکی پشتیبانی نمی‌کند.']);
    }

    /**
     * Handle SMS OTP verification (default implementation for interface compatibility)
     * IBAN validator service doesn't require SMS verification
     */
    public function handleSmsOtpVerification(Request $request, Service $service, string $hash)
    {
        return redirect()->route('services.show', $service->slug)
            ->withErrors(['error' => 'این سرویس از احراز هویت پیامکی پشتیبانی نمی‌کند.']);
    }

    /**
     * Show SMS result page (default implementation for interface compatibility)
     * IBAN validator service uses standard result display
     */
    public function showSmsResult(Request $request, Service $service, string $id)
    {
        // Redirect to standard result page
        return $this->show($id, $service);
    }
} 