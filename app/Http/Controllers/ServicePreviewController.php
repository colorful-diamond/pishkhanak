<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\PaymentGateway;
use App\Contracts\ServicePreviewInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class ServicePreviewController extends Controller
{
    /**
     * Show service preview page for guests
     */
    public function showGuestPreview(Request $request, Service $service, $hash = null)
    {
        // Try to get service request by hash first
        $serviceRequest = null;
        $serviceData = [];
        $phoneNumber = null;

        if ($hash) {
            $serviceRequest = \App\Models\ServiceRequest::findByHash($hash);
            if ($serviceRequest && $serviceRequest->service_id == $service->id && $serviceRequest->status == 'guest') {
                $serviceData = $serviceRequest->input_data;
                // Get phone number from metadata if available
                $phoneNumber = $serviceData['phone'] ?? null;
            } else {
                return redirect()->route('services.show', $service->slug)
                    ->with('error', 'درخواست سرویس یافت نشد یا منقضی شده است.');
            }
        } else {
            // Fallback to session-based approach for backward compatibility
            $serviceData = Session::get('guest_service_data', []);
            $mobile = Session::get('guest_mobile');
            $serviceRequestId = Session::get('guest_service_request_id');

            // Validate that we have service data
            if (empty($serviceData) || !$serviceRequestId) {
                return redirect()->route('services.show', $service->slug)
                    ->with('error', 'لطفاً ابتدا فرم سرویس را تکمیل کنید.');
            }
        }

        // Get preview data if service supports it
        $previewData = $this->getServicePreviewData($service, $serviceData);


        $gateways = PaymentGateway::active()
            ->forCurrency('IRT')
            ->get()
            ->filter(function ($gateway) use ($service) {
                return $gateway->supportsAmount($service->price);
            });

        if ($gateways->isEmpty()) {
            return redirect()->back()->with('error', 'هیچ درگاه پرداخت مناسبی یافت نشد.');
        }

        // Get the preview template from service controller
        $previewTemplate = $this->getServicePreviewTemplate($service);

        // Get bank data for services that support bank-specific functionality
        $banks = $this->getBankDataForService($service);

        return view($previewTemplate, [
            'service' => $service,
            'requestDetails' => $serviceData,
            'previewData' => $previewData,
            'phoneNumber' => $phoneNumber,
            'gateways' => $gateways,
            'requestHash' => $hash,
            'sessionKey' => 'guest_service_data',
            'banks' => $banks
        ]);
    }

    /**
     * Show service preview page for authenticated users with insufficient balance
     */
    public function showUserPreview(Request $request, Service $service, $hash = null)
    {
        if (!Auth::check()) {
            return redirect()->route('services.preview.guest', ['service' => $service->id, 'hash' => $hash]);
        }

        $user = Auth::user();
        $serviceRequest = null;
        $serviceData = [];

        if ($hash) {
            $serviceRequest = \App\Models\ServiceRequest::findByHash($hash);
            if ($serviceRequest && $serviceRequest->service_id == $service->id && 
                $serviceRequest->user_id == $user->id && 
                $serviceRequest->status == 'insufficient_balance') {
                $serviceData = $serviceRequest->input_data;
            } else {
                return redirect()->route('services.show', $service->slug)
                    ->with('error', 'درخواست سرویس یافت نشد یا منقضی شده است.');
            }
        } else {
            // Fallback to session-based approach
            $serviceData = Session::get('pending_service_data', []);
            $serviceRequestId = Session::get('pending_service_request_id');

            // Validate that we have service data
            if (empty($serviceData) || !$serviceRequestId) {
                return redirect()->route('services.show', $service->slug)
                    ->with('error', 'لطفاً ابتدا فرم سرویس را تکمیل کنید.');
            }
        }

        // Check if user now has sufficient balance to process the service automatically
        if ($user->balance >= $service->price) {
            Log::info('User has sufficient balance, processing service automatically', [
                'user_id' => $user->id,
                'service_id' => $service->id,
                'user_balance' => $user->balance,
                'service_price' => $service->price,
                'request_hash' => $hash
            ]);

            try {
                // Process the service using ServicePaymentService
                $servicePaymentService = app(\App\Services\ServicePaymentService::class);
                
                // Create a request object with the service data
                $processingRequest = new Request();
                $processingRequest->replace($serviceData);
                $processingRequest->setUserResolver(function () use ($user) {
                    return $user;
                });

                $result = $servicePaymentService->handleServiceSubmission($processingRequest, $service, $serviceData);

                // Clean up session data
                Session::forget(['pending_service_data', 'pending_service_request_id']);
                
                // Update ServiceRequest status if it exists
                if ($serviceRequest) {
                    $serviceRequest->update(['status' => 'processed']);
                }

                if ($result['success'] && isset($result['redirect'])) {
                    Log::info('Service processed successfully for user with sufficient balance', [
                        'user_id' => $user->id,
                        'service_id' => $service->id,
                        'redirect' => $result['redirect']
                    ]);
                    
                    return redirect($result['redirect'])
                        ->with('success', 'سرویس با موفقیت پردازش شد.');
                } else {
                    Log::error('Service processing failed despite sufficient balance', [
                        'user_id' => $user->id,
                        'service_id' => $service->id,
                        'error' => $result['message'] ?? 'نامشخص'
                    ]);
                    
                    // Fall through to show the preview page with error
                    $processingError = $result['message'] ?? 'خطا در پردازش سرویس';
                }

            } catch (\Exception $e) {
                Log::error('Exception during automatic service processing', [
                    'user_id' => $user->id,
                    'service_id' => $service->id,
                    'error' => $e->getMessage()
                ]);
                
                $processingError = 'خطا در پردازش سرویس: ' . $e->getMessage();
            }
        }

        // If we reach here, either user doesn't have sufficient balance or processing failed
        // Calculate required amount
        $shortfall = max(0, $service->price - $user->balance);
        $suggestedAmount = $service->price;

        // Get preview data if service supports it
        $previewData = $this->getServicePreviewData($service, $serviceData);

        // Get available payment gateways
        $gateways = PaymentGateway::active()
            ->forCurrency('IRT')
            ->get()
            ->filter(function ($gateway) use ($suggestedAmount) {
                return $gateway->supportsAmount($suggestedAmount);
            });

        if ($gateways->isEmpty()) {
            return redirect()->back()->with('error', 'هیچ درگاه پرداخت مناسبی یافت نشد.');
        }



        // Get bank data for services that support bank-specific functionality
        $banks = $this->getBankDataForService($service);

        $viewData = [
            'service' => $service,
            'requestDetails' => $serviceData,
            'previewData' => $previewData,
            'user' => $user,
            'shortfall' => $shortfall,
            'suggestedAmount' => $suggestedAmount,
            'gateways' => $gateways,
            'requestHash' => $hash,
            'sessionKey' => 'pending_service_data',
            'banks' => $banks
        ];

        // Add processing error to view if it exists
        if (isset($processingError)) {
            $viewData['processingError'] = $processingError;
        }

        // Get the preview template from service controller
        $previewTemplate = $this->getServicePreviewTemplate($service);

        return view($previewTemplate, $viewData);
    }

    /**
     * Get preview data for a service
     */
    protected function getServicePreviewData(Service $service, array $serviceData): array
    {
        try {
            // Get the service controller
            $controllerClass = $this->getServiceControllerClass($service);
            
            if (!$controllerClass || !class_exists($controllerClass)) {
                return [];
            }

            $controller = app($controllerClass);

            // Check if controller implements ServicePreviewInterface
            if (!($controller instanceof ServicePreviewInterface)) {
                return [];
            }

            // Check if service supports preview
            if (!$controller->supportsPreview()) {
                return [];
            }

            // Get preview data
            $result = $controller->getPreviewData($serviceData, $service);

            if ($result['success'] ?? false) {
                return $result['preview_data'] ?? [];
            }

            return [];

        } catch (\Exception $e) {
            Log::error('Failed to get service preview data', [
                'service_id' => $service->id,
                'service_slug' => $service->slug,
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Get service controller class name
     */
    protected function getServiceControllerClass(Service $service): ?string
    {
        // Map service slugs to controller classes
        $controllerMap = [
            'card-iban' => \App\Http\Controllers\Services\CardIbanController::class,
            'card-to-iban' => \App\Http\Controllers\Services\CardIbanController::class,
            'card-account' => \App\Http\Controllers\Services\CardAccountController::class,
            'card-to-account' => \App\Http\Controllers\Services\CardAccountController::class,
            'iban-account' => \App\Http\Controllers\Services\IbanAccountController::class,
            'iban-to-account' => \App\Http\Controllers\Services\IbanAccountController::class,
            'account-iban' => \App\Http\Controllers\Services\AccountIbanController::class,
            'account-to-iban' => \App\Http\Controllers\Services\AccountIbanController::class,
            'iban-check' => \App\Http\Controllers\Services\IbanCheckController::class,
            'iban-validator' => \App\Http\Controllers\Services\IbanCheckController::class,
            'sheba-validator' => \App\Http\Controllers\Services\IbanCheckController::class,
            
            // Vehicle & Insurance Services
            'third-party-insurance-history' => \App\Http\Controllers\Services\ThirdPartyInsuranceHistoryController::class,
            'vehicle-info-inquiry' => \App\Http\Controllers\Services\VehicleInfoInquiryController::class,
            'car-violation-inquiry' => \App\Http\Controllers\Services\CarViolationInquiryController::class,
            
            // Financial Services with Custom Preview Pages
            'loan-inquiry' => \App\Http\Controllers\Services\LoanInquiryController::class,
            'cheque-inquiry' => \App\Http\Controllers\Services\ChequeInquiryController::class,
            'credit-score-rating' => \App\Http\Controllers\Services\CreditScoreRatingController::class,
            
            // Add more services as needed
        ];

        // First try current service slug
        if (isset($controllerMap[$service->slug])) {
            return $controllerMap[$service->slug];
        }

        // If it's a sub-service, try the parent service slug
        if ($service->parent_id && $service->parent) {
            return $controllerMap[$service->parent->slug] ?? null;
        }

        return null;
    }

    /**
     * Get preview template for a service with fallback logic
     */
    protected function getServicePreviewTemplate(Service $service): string
    {
        try {
            // First, check if custom template exists for this service
            $customTemplate = "front.services.custom.{$service->slug}.preview";
            if (view()->exists($customTemplate)) {
                return $customTemplate;
            }
            
            // If it's a sub-service, try the parent service template
            if ($service->parent_id && $service->parent) {
                $parentTemplate = "front.services.custom.{$service->parent->slug}.preview";
                if (view()->exists($parentTemplate)) {
                    return $parentTemplate;
                }
            }
            
            // Then try to get the service controller
            $controllerClass = $this->getServiceControllerClass($service);
            
            if ($controllerClass && class_exists($controllerClass)) {
                $controller = app($controllerClass);
                
                // Check if controller implements ServicePreviewInterface
                if ($controller instanceof ServicePreviewInterface) {
                    $template = $controller->getPreviewTemplate();
                    
                    // Return template from controller if it exists
                    if (view()->exists($template)) {
                        return $template;
                    }
                }
            }
            
            // Fallback to default template
            return 'services.preview';
            
        } catch (\Exception $e) {
            Log::error('Error getting service preview template', [
                'service_id' => $service->id,
                'service_slug' => $service->slug,
                'error' => $e->getMessage()
            ]);
            
            return 'services.preview';
        }
    }

    /**
     * Get bank data for services that support bank-specific functionality
     */
    protected function getBankDataForService(Service $service): ?array
    {
        // Services that support bank-specific functionality
        $bankSupportedServices = [
            'loan-inquiry',
            'cheque-inquiry', 
            'credit-score-rating'
        ];

        // Check current service slug first
        $serviceSlugToCheck = $service->slug;
        
        // If it's a sub-service, check the parent service slug instead
        if ($service->parent_id && $service->parent) {
            $serviceSlugToCheck = $service->parent->slug;
        }

        if (in_array($serviceSlugToCheck, $bankSupportedServices)) {
            try {
                $bankService = app(\App\Services\BankService::class);
                return $bankService->getBanksForSlider();
            } catch (\Exception $e) {
                Log::error('Error getting bank data for service preview', [
                    'service_id' => $service->id,
                    'service_slug' => $service->slug,
                    'parent_slug' => $service->parent->slug ?? null,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return null;
    }
} 