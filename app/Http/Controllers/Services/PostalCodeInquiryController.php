<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\BaseServiceController;
use App\Models\Service;
use App\Models\ServiceResult;
use App\Services\JibitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\Log;

class PostalCodeInquiryController extends Controller implements BaseServiceController
{
    /**
     * @var JibitService
     */
    private $jibitService;

    /**
     * PostalCodeInquiryController constructor.
     */
    public function __construct()
    {
        $this->jibitService = app(JibitService::class);
    }

    /**
     * Handle the postal code inquiry request
     *
     * @param Request $request
     * @param Service $service
     * @return \Illuminate\Http\Response
     */
    public function handle(Request $request, Service $service)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'postal_code' => 'required|string|digits:10',
        ], [
            'postal_code.required' => 'کد پستی الزامی است',
            'postal_code.digits' => 'کد پستی باید 10 رقم باشد',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $postalCode = $request->input('postal_code');
        $serviceData = ['postal_code' => $postalCode];
        
        // Process the service
        $result = $this->process($serviceData, $service);
        
        if (!$result['success']) {
            return back()
                ->withErrors(['postal_code' => $result['message']])
                ->withInput();
        }

        // Store result and redirect
        $serviceResult = ServiceResult::create([
            'service_id' => $service->id,
            'user_id' => Auth::id(),
            'output_data' => $result['data'],
            'result_hash' => ServiceResult::generateHash(),
            'status' => 'success',
            'processed_at' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('services.result', $serviceResult->result_hash);
    }

    /**
     * Process the service data
     *
     * @param array $serviceData
     * @param Service $service
     * @return array
     */
    public function process(array $serviceData, Service $service): array
    {
        try {
            $postalCode = $serviceData['postal_code'] ?? '';
            
            // Validate postal code
            if (!preg_match('/^\d{10}$/', $postalCode)) {
                return [
                    'success' => false,
                    'message' => 'کد پستی باید 10 رقم باشد'
                ];
            }
            
            // Call Jibit API
            $apiResponse = $this->jibitService->identityService()->getPostalCodeInquiry($postalCode);
            
            if (!$apiResponse) {
                return [
                    'success' => false,
                    'message' => 'خطا در دریافت اطلاعات کد پستی'
                ];
            }
            
            // Format postal code for display (e.g., 1234567890 -> 12345-67890)
            $formattedPostalCode = substr($postalCode, 0, 5) . '-' . substr($postalCode, 5);
            
            $addressInfo = $apiResponse->addressInfo ?? null;
            
            if (!$addressInfo) {
                return [
                    'success' => false,
                    'message' => 'اطلاعاتی برای این کد پستی یافت نشد'
                ];
            }

            $result = [
                'postal_code' => $postalCode,
                'formatted_postal_code' => $formattedPostalCode,
                'status' => 'found',
                'message' => 'اطلاعات کد پستی با موفقیت یافت شد',
                'address_info' => [
                    'full_address' => $addressInfo->address ?? '',
                    'province' => $addressInfo->province ?? '',
                    'city' => $addressInfo->district ?? '',
                    'region_code' => substr($postalCode, 0, 2),
                    'zone_code' => substr($postalCode, 2, 3),
                    'area_code' => substr($postalCode, 5, 3),
                    'building_code' => substr($postalCode, 8, 2),
                ],
                'additional_info' => [
                    'postal_code_structure' => [
                        'region' => substr($postalCode, 0, 2),
                        'zone' => substr($postalCode, 2, 3),
                        'area' => substr($postalCode, 5, 3),
                        'building' => substr($postalCode, 8, 2),
                    ],
                    'validation' => [
                        'is_valid' => true,
                        'format_check' => strlen($postalCode) === 10,
                        'structure_check' => $this->validatePostalCodeStructure($postalCode),
                    ],
                    'usage_tips' => 'این کد پستی برای ارسال مرسولات پستی استفاده می‌شود. در صورت تغییر آدرس، کد پستی جدید را استعلام کنید.',
                ],
                'api_info' => [
                    'provider' => 'Jibit',
                    'timestamp' => now()->toISOString(),
                    'response_time' => 'کمتر از 1 ثانیه',
                ],
            ];
            
            return [
                'success' => true,
                'data' => $result
            ];
            
        } catch (Exception $e) {
            Log::error('Error in PostalCodeInquiryController::process: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'خطا در پردازش اطلاعات کد پستی'
            ];
        }
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
        $result = ServiceResult::where('result_hash', $resultId)->firstOrFail();
        $data = $result->output_data;
        
        return view('front.services.custom.postal-code.result', [
            'data' => $data,
            'result' => $result,
            'service' => $service,
        ]);
    }

    /**
     * Get the result type for this service
     *
     * @return string
     */
    public function getResultType(): string
    {
        return 'postal_code_inquiry';
    }

    /**
     * Validate postal code structure
     *
     * @param string $postalCode
     * @return bool
     */
    private function validatePostalCodeStructure(string $postalCode): bool
    {
        // Basic validation for Iranian postal codes
        if (strlen($postalCode) !== 10) {
            return false;
        }

        // Check if all characters are digits
        if (!ctype_digit($postalCode)) {
            return false;
        }

        // Check for invalid patterns (like all same digits)
        if (count(array_unique(str_split($postalCode))) === 1) {
            return false;
        }

        return true;
    }

    /**
     * Show progress page (default implementation for interface compatibility)
     * Postal code inquiry doesn't use background processing
     */
    public function showProgress(Request $request, Service $service, string $hash)
    {
        return redirect()->route('services.show', $service->slug)
            ->withErrors(['error' => 'این سرویس از صفحه پیشرفت پشتیبانی نمی‌کند.']);
    }

    /**
     * Handle OTP submission (default implementation for interface compatibility)
     * Postal code inquiry doesn't require OTP verification
     */
    public function handleOtpSubmission(Request $request, Service $service)
    {
        return redirect()->route('services.show', $service->slug)
            ->withErrors(['error' => 'این سرویس از احراز هویت با کد یکبار مصرف پشتیبانی نمی‌کند.']);
    }

    /**
     * Show SMS verification page (default implementation for interface compatibility)
     * Postal code inquiry doesn't require SMS verification
     */
    public function showSmsVerification(Request $request, Service $service, string $hash)
    {
        return redirect()->route('services.show', $service->slug)
            ->withErrors(['error' => 'این سرویس از احراز هویت پیامکی پشتیبانی نمی‌کند.']);
    }

    /**
     * Handle SMS OTP verification (default implementation for interface compatibility)
     * Postal code inquiry doesn't require SMS verification
     */
    public function handleSmsOtpVerification(Request $request, Service $service, string $hash)
    {
        return redirect()->route('services.show', $service->slug)
            ->withErrors(['error' => 'این سرویس از احراز هویت پیامکی پشتیبانی نمی‌کند.']);
    }

    /**
     * Show SMS result page (default implementation for interface compatibility)
     * Postal code inquiry uses standard result display
     */
    public function showSmsResult(Request $request, Service $service, string $id)
    {
        // Redirect to standard result page
        return $this->show($id, $service);
    }
} 