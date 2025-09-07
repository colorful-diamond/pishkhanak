<?php

namespace App\Http\Controllers\Services;

use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\GuestPaymentController;

trait ServicePaymentTrait
{
    /**
     * Handle service submission with payment check
     *
     * @param Request $request
     * @param Service $service
     * @param array $serviceData
     * @return \Illuminate\Http\Response|array
     */
    protected function handleServiceWithPayment(Request $request, Service $service, array $serviceData)
    {
        // Use the ServicePaymentService to handle all payment flows
        $servicePaymentService = app(\App\Services\ServicePaymentService::class);
        $result = $servicePaymentService->handleServiceSubmission($request, $service, $serviceData);
        
        if ($result['success'] && $result['redirect']) {
            $redirect = redirect($result['redirect']);
            
            // Add session data based on result type
            if (isset($result['show_preview'])) {
                // Guest payment - show preview page
                $redirect->with('success', $result['message']);
            } elseif (isset($result['show_wallet_charge'])) {
                // Insufficient balance - show wallet charge page
                $redirect->with('error', $result['message']);
                if (isset($result['suggested_amount'])) {
                    $redirect->with('suggested_amount', $result['suggested_amount']);
                }
                if (isset($result['pending_service'])) {
                    $redirect->with('pending_service', $result['pending_service']);
                }
            } else {
                // Service processed successfully
                $redirect->with('success', $result['message']);
            }
            
            return $redirect;
        } else {
            return back()->withErrors(['service_error' => $result['message']])->withInput();
        }
    }

    /**
     * Process service directly (free services)
     *
     * @param Request $request
     * @param Service $service
     * @param array $serviceData
     * @return \Illuminate\Http\Response
     */
    protected function processServiceDirectly(Request $request, Service $service, array $serviceData)
    {
        $result = $this->process($serviceData, $service);
        
        if ($result['success']) {
            // Store the result in database
            $serviceResult = \App\Models\ServiceResult::create([
                'service_id' => $service->id,
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'input_data' => $serviceData,
                'output_data' => $result['data'],
                'status' => 'success',
                'processed_at' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return redirect()->route('services.result', ['id' => $serviceResult->result_hash])
                ->with('success', 'سرویس با موفقیت پردازش شد.');
        } else {
            return back()
                ->withErrors(['service_error' => $result['message']])
                ->withInput();
        }
    }



    /**
     * Process service with wallet payment for logged-in user
     *
     * @param Request $request
     * @param Service $service
     * @param array $serviceData
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    protected function processServiceWithWalletPayment(Request $request, Service $service, array $serviceData, User $user)
    {
        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            // Deduct amount from wallet
            $user->withdraw($service->price, [
                'description' => "پرداخت سرویس: {$service->title}",
                'service_id' => $service->id,
                'type' => 'service_payment'
            ]);

            // Process the service
            $result = $this->process($serviceData, $service);

            if ($result['success']) {
                // Store the result
                $serviceResult = \App\Models\ServiceResult::create([
                    'service_id' => $service->id,
                    'user_id' => $user->id,
                    'input_data' => $serviceData,
                    'output_data' => $result['data'],
                    'status' => 'success',
                    'processed_at' => now(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                \Illuminate\Support\Facades\DB::commit();

                // Clear any pending service data
                Session::forget(['pending_service_id', 'pending_service_data', 'pending_service_redirect']);

                return redirect()->route('services.result', ['id' => $serviceResult->result_hash])
                    ->with('success', 'سرویس با موفقیت پردازش شد.');
            } else {
                // Refund the user if service fails
                $user->deposit($service->price, [
                    'description' => "بازگشت وجه - خطا در پردازش سرویس: {$service->title}",
                    'service_id' => $service->id,
                    'type' => 'service_refund'
                ]);

                \Illuminate\Support\Facades\DB::commit();

                return back()
                    ->withErrors(['service_error' => $result['message']])
                    ->withInput();
            }

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            
            \Illuminate\Support\Facades\Log::error('Service payment processing failed', [
                'user_id' => $user->id,
                'service_id' => $service->id,
                'error' => $e->getMessage()
            ]);

            return back()
                ->withErrors(['service_error' => 'خطا در پردازش سرویس. لطفاً مجدداً تلاش کنید.'])
                ->withInput();
        }
    }

    /**
     * Process pending service after wallet charge
     * This method should be called after successful wallet charge
     *
     * @param User $user
     * @return array|null
     */
    public static function processPendingService(User $user): ?array
    {
        if (!Session::has('pending_service_id') || !Session::has('pending_service_data')) {
            return null;
        }

        try {
            $serviceId = Session::get('pending_service_id');
            $serviceData = Session::get('pending_service_data');
            
            $service = Service::find($serviceId);
            if (!$service) {
                Session::forget(['pending_service_id', 'pending_service_data', 'pending_service_redirect']);
                return ['success' => false, 'message' => 'سرویس یافت نشد'];
            }

            // Check if user has sufficient balance now
            if ($user->balance < $service->price) {
                return ['success' => false, 'message' => 'موجودی کیف پول همچنان کافی نیست'];
            }

            \Illuminate\Support\Facades\DB::beginTransaction();

            // Deduct amount from wallet
            $user->withdraw($service->price, [
                'description' => "پرداخت سرویس: {$service->title}",
                'service_id' => $service->id,
                'type' => 'service_payment'
            ]);

            // Get the appropriate controller and process service
            $controller = \App\Http\Controllers\Services\ServiceControllerFactory::getController($service);
            if (!$controller) {
                throw new \Exception('کنترلر سرویس یافت نشد');
            }

            $result = $controller->process($serviceData, $service);

            if ($result['success']) {
                // Store the result
                $serviceResult = \App\Models\ServiceResult::create([
                    'service_id' => $service->id,
                    'user_id' => $user->id,
                    'input_data' => $serviceData,
                    'output_data' => $result['data'],
                    'status' => 'success',
                    'processed_at' => now(),
                    'ip_address' => Session::get('pending_request_ip', request()->ip()),
                    'user_agent' => Session::get('pending_request_user_agent', request()->userAgent()),
                ]);

                \Illuminate\Support\Facades\DB::commit();

                // Clear pending data
                Session::forget(['pending_service_id', 'pending_service_data', 'pending_service_redirect']);

                return [
                    'success' => true,
                    'message' => 'سرویس با موفقیت پردازش شد.',
                    'redirect' => route('services.result', ['id' => $serviceResult->result_hash])
                ];
            } else {
                // Refund if service fails
                $user->deposit($service->price, [
                    'description' => "بازگشت وجه - خطا در پردازش سرویس: {$service->title}",
                    'service_id' => $service->id,
                    'type' => 'service_refund'
                ]);

                \Illuminate\Support\Facades\DB::commit();

                return ['success' => false, 'message' => $result['message']];
            }

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            
            \Illuminate\Support\Facades\Log::error('Pending service processing failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return ['success' => false, 'message' => 'خطا در پردازش سرویس'];
        }
    }
} 