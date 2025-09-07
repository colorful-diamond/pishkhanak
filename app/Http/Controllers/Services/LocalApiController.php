<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessLocalRequestJob;
use App\Models\Service;
use App\Services\LocalRequestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Exception;

abstract class LocalApiController extends Controller implements BaseServiceController
{
    protected string $serviceSlug;
    protected array $requiredFields;
    protected array $validationRules;
    protected array $validationMessages;
    protected int $estimatedDuration = 300; // Default 300 seconds (5 minutes)
    protected LocalRequestService $localRequestService;

    public function __construct()
    {
        $this->localRequestService = app(LocalRequestService::class);
        $this->configureService();
    }

    /**
     * Configure service-specific settings
     * Must be implemented by child classes
     */
    abstract protected function configureService(): void;

    /**
     * Handle the service request by creating background job
     */
    public function handle(Request $request, $service)
    {
        try {
            // Check if this is an OTP submission for existing request
            if ($request->has('otp') && $request->has('hash')) {
                return $this->handleOtpSubmission($request, $service);
            }

            // Validate input for new request
            $validator = Validator::make($request->all(), $this->validationRules, $this->validationMessages);
            
            if ($validator->fails()) {
                return back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $serviceData = $request->only($this->requiredFields);
            
            // Create local request for background processing (Redis-only)
            $localRequest = $this->localRequestService->createRequest(
                serviceSlug: $this->serviceSlug,
                serviceId: $service->id,
                requestData: $serviceData,
                userId: Auth::id(),
                sessionId: Session::getId(),
                estimatedDuration: $this->estimatedDuration,
                ipAddress: $request->ip(),
                userAgent: $request->userAgent()
            );

            // Dispatch background job with request hash (job will handle entire flow including OTP)
            ProcessLocalRequestJob::dispatch($localRequest['hash']);

            Log::info('Background request initiated with pub/sub OTP handling', [
                'service_slug' => $this->serviceSlug,
                'request_hash' => $localRequest['hash'],
                'user_id' => Auth::id()
            ]);

            // Redirect to progress page
            return redirect()->route('services.progress', [
                'service' => $service->slug,
                'hash' => $localRequest['hash']
            ]);

        } catch (Exception $e) {
            Log::error('Error initiating background service', [
                'service_slug' => $this->serviceSlug,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withErrors(['error' => 'خطا در پردازش درخواست. لطفاً مجدداً تلاش کنید.'])
                ->withInput();
        }
    }

    /**
     * Handle OTP submission by publishing to Redis channel
     */
    public function handleOtpSubmission(Request $request, $service)
    {
        try {
            $hash = $request->input('hash');
            $otp = $request->input('otp');

            // Validate OTP
            if (empty($otp) || strlen($otp) < 4) {
                return back()
                    ->withErrors(['otp' => 'کد تایید نامعتبر است'])
                    ->withInput();
            }

            // Check if request exists and is waiting for OTP
            $localRequest = $this->localRequestService->getRequest($hash);
            
            if (!$localRequest) {
                return back()
                    ->withErrors(['otp' => 'درخواست یافت نشد'])
                    ->withInput();
            }

            if ($localRequest['status'] !== 'otp_required') {
                return back()
                    ->withErrors(['otp' => 'این درخواست نیازی به کد تایید ندارد'])
                    ->withInput();
            }

            // Check if request is still waiting for OTP
            if ($localRequest['status'] !== 'otp_required') {
                return back()
                    ->withErrors(['otp' => 'این درخواست دیگر در انتظار کد تایید نیست'])
                    ->withInput();
            }

            Log::info('OTP submission received, publishing to Redis channel', [
                'hash' => $hash,
                'service_slug' => $this->serviceSlug,
                'otp_length' => strlen($otp),
                'user_id' => Auth::id()
            ]);

            // Publish OTP to Redis channel for background job to receive
            $published = $this->localRequestService->publishOtp($hash, $otp, [
                'user_id' => Auth::id(),
                'session_id' => Session::getId(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            if (!$published) {
                Log::warning('Failed to publish OTP to Redis channel', [
                    'hash' => $hash,
                    'service_slug' => $this->serviceSlug
                ]);
                
                return back()
                    ->withErrors(['otp' => 'خطا در ارسال کد تایید. لطفاً مجدداً تلاش کنید.'])
                    ->withInput();
            }

            // Update request status to show OTP was submitted  
            $this->localRequestService->updateProgress(
                $hash, 
                80, 
                'waiting_otp', 
                'کد تایید دریافت شد، در حال پردازش...'
            );

            Log::info('OTP published successfully to Redis channel', [
                'hash' => $hash,
                'service_slug' => $this->serviceSlug,
                'subscribers_notified' => $published
            ]);

            // Redirect back to progress page to show processing
            return redirect()->route('services.progress', [
                'service' => $service->slug,
                'hash' => $hash
            ])->with('success', 'کد تایید دریافت شد، در حال پردازش نتیجه...');

        } catch (Exception $e) {
            Log::error('Error in OTP submission', [
                'hash' => $request->input('hash'),
                'service_slug' => $this->serviceSlug,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withErrors(['otp' => 'خطا در پردازش کد تایید. لطفاً مجدداً تلاش کنید.'])
                ->withInput();
        }
    }

    /**
     * Process service and return result data (for programmatic use)
     */
    public function process(array $serviceData, $service): array
    {
        try {
            // For background services, we create a request and return the hash
            $localRequest = $this->localRequestService->createRequest(
                serviceSlug: $this->serviceSlug,
                serviceId: $service->id,
                requestData: $serviceData,
                estimatedDuration: $this->estimatedDuration
            );

            // Dispatch background job
            ProcessLocalRequestJob::dispatch($localRequest['hash']);

            return [
                'success' => true,
                'type' => 'background_processing',
                'hash' => $localRequest['hash'],
                'estimated_duration' => $this->estimatedDuration,
                'progress_url' => route('services.progress', [
                    'service' => $service->slug,
                    'hash' => $localRequest['hash']
                ])
            ];
        } catch (Exception $e) {
            Log::error('Error processing background service', [
                'service_slug' => $this->serviceSlug,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'خطا در پردازش درخواست'
            ];
        }
    }

    /**
     * Show the service result
     */
    public function show(string $resultId, $service)
    {
        $localRequest = $this->localRequestService->getRequest($resultId);

        if (!$localRequest || $localRequest['status'] !== 'completed') {
            return redirect()->route('services.show', $service->slug)
                ->withErrors(['error' => 'نتیجه مورد نظر یافت نشد.']);
        }

        return view('front.services.local-api.result', [
            'localRequest' => $localRequest,
            'service' => $service,
            'resultData' => $localRequest['result_data']
        ]);
    }

    /**
     * Show progress page
     */
    public function showProgress(Request $request, $service, string $hash)
    {
        // Use getRequestStatus instead of getRequest to get properly formatted data with calculated remaining time
        $localRequest = $this->localRequestService->getRequestStatus($hash);

        if (!$localRequest) {
            return redirect()->route('services.show', $service->slug)
                ->withErrors(['error' => 'درخواست مورد نظر یافت نشد.']);
        }

        // Check if request is expired
        if ($localRequest['is_expired'] ?? false) {
            return redirect()->route('services.show', $service->slug)
                ->withErrors(['error' => 'درخواست منقضی شده است. لطفاً مجدداً تلاش کنید.']);
        }

        // Don't redirect if completed - stay on progress page to show 100% and action buttons
        // User can manually navigate to result page using the "مشاهده نتیجه" button

        // Show progress page with OTP input if required
        return view('front.services.local-api.progress', [
            'localRequest' => $localRequest,
            'service' => $service,
            'showOtpInput' => $localRequest['requires_otp'] ?? false
        ]);
    }

    /**
     * Show OTP verification page (legacy support - now integrated into progress page)
     */
    public function showOtpVerification(Request $request, $service, string $hash)
    {
        // Redirect to progress page which now handles OTP input
        return redirect()->route('services.progress', [
            'service' => $service->slug,
            'hash' => $hash
        ]);
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
     * Handle SMS verification submission (default implementation for interface compatibility) 
     */
    public function handleSmsOtpVerification(Request $request, Service $service, string $hash)
    {
        return redirect()->route('services.show', $service->slug)
            ->withErrors(['error' => 'این سرویس از تایید پیامک پشتیبانی نمی‌کند.']);
    }

    /**
     * Show SMS result page (default implementation for interface compatibility)
     */
    public function showSmsResult(Request $request, Service $service, string $id)
    {
        // Fallback to regular result page
        return redirect()->route('services.result', ['id' => $id]);
    }
} 