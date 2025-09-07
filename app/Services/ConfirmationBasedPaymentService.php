<?php

namespace App\Services;

use App\Models\User;
use App\Models\Service;
use App\Models\ServiceResult;
use App\Models\ServiceRequest;
use Bavix\Wallet\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Currency;
use App\Models\PaymentGateway;

/**
 * Confirmation-based payment service that eliminates deduction-refund cycles
 * 
 * Uses Bavix wallet's unconfirmed transactions to hold funds until service completion
 */
class ConfirmationBasedPaymentService
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Process service with confirmation-based payment
     * 
     * @param Request $request
     * @param Service $service
     * @param array $serviceData
     * @param User $user
     * @return array
     */
    public function processServiceWithConfirmation(Request $request, Service $service, array $serviceData, User $user): array
    {
        try {
            // Check if user has sufficient balance (no transaction needed for this check)
            if ($user->balance < $service->price) {
                return [
                    'success' => false,
                    'message' => 'موجودی کیف پول کافی نیست.',
                    'redirect' => null
                ];
            }

            // FIRST: Check if this requires SMS authentication (BEFORE payment)
            $controller = \App\Http\Controllers\Services\ServiceControllerFactory::getController($service);
            
            if (!$controller) {
                return [
                    'success' => false,
                    'message' => 'سرویس از سمت تامین کننده دولتی دچار اختلال است، به زودی مشکل بر طرف می شود.لطفا ساعاتی دیگر تلاش کنید'                ];
            }

            // SMS-based services should be handled separately before reaching this point
            // If we get here with an SMS service, something went wrong
            if ($controller instanceof \App\Http\Controllers\Services\BaseSmsFinnotechController) {
                Log::error('SMS-based service reached payment service - this should not happen', [
                    'service_slug' => $service->slug,
                    'controller_class' => get_class($controller)
                ]);
                
                return [
                    'success' => false,
                    'message' => 'سرویس SMS باید از طریق مسیر جداگانه پردازش شود'
                ];
            }

            // For NON-SMS services, continue with the existing payment flow
            Log::info('Regular service detected, proceeding with payment flow', [
                'service_slug' => $service->slug,
                'controller_class' => get_class($controller)
            ]);
            
            // Step 1: Create ServiceRequest (simple insert - no transaction)
            $serviceRequest = ServiceRequest::create([
                'service_id' => $service->id,
                'user_id' => $user->id,
                'input_data' => $serviceData,
                'status' => 'pending'
            ]);

            Log::info('ServiceRequest created', [
                'service_request_id' => $serviceRequest->id,
                'service_id' => $service->id,
                'user_id' => $user->id
            ]);

            // Step 2: Create PENDING (unconfirmed) transaction
            $pendingTransaction = $this->createPendingTransaction($user, $service);
            
            Log::info('Created pending withdrawal transaction', [
                'user_id' => $user->id,
                'service_id' => $service->id,
                'transaction_id' => $pendingTransaction->id,
                'amount' => $service->price
            ]);

            // Step 3: Process the actual service
            $result = $controller->process($serviceData, $service);

            if ($result['success']) {
                Log::info('🔍 [PAYMENT-SERVICE] Processing successful service result', [
                    'service_slug' => $service->slug,
                    'result_data' => $result['data'] ?? [],
                    'has_data' => isset($result['data']),
                    'data_code' => $result['data']['code'] ?? 'no_code'
                ]);

                // 🚨 CHECK FOR SMS_SENT BEFORE CREATING SERVICERESULT
                if (isset($result['data']['code']) && $result['data']['code'] === 'SMS_SENT') {
                    Log::info('📱 [PAYMENT-SERVICE] SMS_SENT detected - cancelling transaction and redirecting to OTP', [
                        'service_slug' => $service->slug,
                        'hash' => $result['data']['hash'] ?? 'no_hash',
                        'transaction_id' => $pendingTransaction->id
                    ]);

                    // Cancel the pending transaction since we need OTP first
                    $this->cancelTransaction($pendingTransaction, $service, 'OTP verification required');

                    // Mark service request as pending OTP
                    $serviceRequest->update([
                        'status' => 'pending_otp'
                    ]);

                    // Store session data for OTP verification  
                    \Illuminate\Support\Facades\Session::put('local_api_otp_data', [
                        'service_id' => $service->id,
                        'service_slug' => $service->slug,
                        'hash' => $result['data']['hash'],
                        'expiry' => $result['data']['expiry'],
                        'mobile' => $serviceData['mobile'] ?? null,
                        'national_code' => $serviceData['national_code'] ?? null,
                    ]);

                    // Redirect to OTP verification page
                    return [
                        'success' => true,
                        'message' => 'کد تایید ارسال شد',
                        'redirect' => route('services.progress.otp', [
                            'service' => $service->slug,
                            'hash' => $result['data']['hash']
                        ])
                    ];
                }

                // Step 4a: Regular service succeeded - CONFIRM the transaction
                $this->confirmTransaction($pendingTransaction, $service, 'service_payment');
                
                // Update service request (no transaction wrapper)
                $serviceRequest->update([
                    'processed_at' => now(),
                    'status' => 'completed'
                ]);

                Log::info('Main operations completed, now creating ServiceResult directly', [
                    'service_id' => $service->id,
                    'transaction_id' => $pendingTransaction->id,
                ]);

                // Step 5: Create ServiceResult directly (no transaction - let Laravel handle it)
                try {
                    Log::info('Creating ServiceResult with standard Laravel create method', [
                        'user_id' => $user->id,
                        'service_id' => $service->id,
                        'transaction_id' => $pendingTransaction->id,
                    ]);

                    $serviceResult = ServiceResult::create([
                        'service_id' => $service->id,
                        'user_id' => $user->id,
                        'input_data' => $serviceData,
                        'output_data' => $result['data'],
                        'status' => 'success',
                        'processed_at' => now(),
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'wallet_transaction_id' => $pendingTransaction->id,
                    ]);

                    Log::info('ServiceResult created using standard Laravel method', [
                        'service_result_id' => $serviceResult->id,
                        'result_hash' => $serviceResult->result_hash,
                        'created_at' => $serviceResult->created_at,
                    ]);

                    // Verify creation immediately
                    if (!$serviceResult || !$serviceResult->id || !$serviceResult->result_hash) {
                        throw new \Exception('ServiceResult creation failed - missing ID or hash');
                    }

                    Log::info('Service processed successfully - no transactions used', [
                        'user_id' => $user->id,
                        'service_id' => $service->id,
                        'transaction_id' => $pendingTransaction->id,
                        'result_id' => $serviceResult->id,
                        'result_hash' => $serviceResult->result_hash
                    ]);

                    return [
                        'success' => true,
                        'message' => 'سرویس با موفقیت پردازش شد.',
                        'redirect' => route('services.result', ['id' => $serviceResult->result_hash])
                    ];

                } catch (\Exception $e) {
                    Log::error('ServiceResult creation failed - cleaning up', [
                        'user_id' => $user->id,
                        'service_id' => $service->id,
                        'transaction_id' => $pendingTransaction->id,
                        'error' => $e->getMessage(),
                    ]);

                    // If ServiceResult creation failed, clean up by cancelling the transaction
                    $this->cancelTransaction($pendingTransaction, $service, 'خطا در ذخیره نتیجه: ' . $e->getMessage());

                    return [
                        'success' => false,
                        'message' => 'خطا در ذخیره نتیجه سرویس: ' . $e->getMessage()
                    ];
                }

            } else {
                // Step 4b: Service failed - CANCEL the transaction (delete it)
                $this->cancelTransaction($pendingTransaction, $service, $result['message']);

                // Mark service request as failed (no transaction wrapper)
                $serviceRequest->update([
                    'processed_at' => now(),
                    'status' => 'failed',
                    'error_message' => $result['message']
                ]);

                Log::info('Service failed - cancelled pending transaction', [
                    'user_id' => $user->id,
                    'service_id' => $service->id,
                    'transaction_id' => $pendingTransaction->id,
                    'error' => $result['message']
                ]);

                return [
                    'success' => false,
                    'message' => $result['message'],
                    'redirect' => null
                ];
            }

        } catch (\Exception $e) {
            Log::error('Service payment processing failed - no rollback needed', [
                'user_id' => $user->id,
                'service_id' => $service->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'در حال حاضر سرویس دهنده دولتی در حال بروزرسانی است.لطفا ساعاتی دیگر تلاش کنید',
                'redirect' => null
            ];
        }
    }

    /**
     * Confirm a pending transaction
     */
    private function confirmTransaction(Transaction $transaction, Service $service, string $finalType): void
    {
        // Update transaction metadata and confirm it properly for Bavix
        $existingMeta = $transaction->meta ?? [];
        $enhancedMeta = array_merge($existingMeta, [
            'type' => $finalType,
            'confirmed_at' => now()->toISOString(),
            'description' => "پرداخت سرویس: {$service->title}",
            'original_description' => $existingMeta['description'] ?? null,
            'payment_status' => 'completed',
            'service_processed' => true,
            'completion_timestamp' => now()->toISOString(),
            // Ensure source tracking is preserved and enhanced
            'source_tracking' => array_merge($existingMeta['source_tracking'] ?? [], [
                'transaction_status' => 'confirmed',
                'service_execution_result' => 'success',
                'final_transaction_type' => $finalType,
                'confirmed_at' => now()->toISOString()
            ])
        ]);

        $transaction->update([
            'confirmed' => true,
            'meta' => $enhancedMeta
        ]);

        // No need to create payment gateway record for wallet operations
        // The bavix wallet transaction is sufficient for tracking wallet-based payments
        Log::info('Payment confirmed via bavix wallet transaction', [
            'transaction_id' => $transaction->id,
            'service_id' => $service->id,
            'payment_method' => 'bavix_wallet',
            'amount' => $service->price
        ]);
        
        // Force refresh the wallet balance to include confirmed transactions
        $wallet = $transaction->wallet;
        if ($wallet) {
            $wallet->refreshBalance();
        }

        Log::info('Transaction confirmed and wallet balance refreshed', [
            'transaction_id' => $transaction->id,
            'service_id' => $service->id,
            'final_type' => $finalType,
            'wallet_balance_after' => $wallet ? $wallet->balance : 'unknown',
            'source_tracking' => $enhancedMeta['source_tracking'] ?? null
        ]);
    }

    /**
     * Cancel a pending transaction
     */
    private function cancelTransaction(Transaction $transaction, ?Service $service, string $reason): void
    {
        // Update transaction meta before deletion to keep a record
        $transaction->update([
            'meta' => array_merge($transaction->meta ?? [], [
                'cancelled_at' => now()->toISOString(),
                'cancellation_reason' => $reason,
                'original_description' => $transaction->meta['description'] ?? null,
            ])
        ]);

        // Delete the transaction completely - this restores the balance without creating a refund record
        $transaction->delete();

        Log::info('Transaction cancelled and deleted', [
            'transaction_id' => $transaction->id,
            'service_id' => $service->id,
            'reason' => $reason
        ]);
    }

    /**
     * Store service request with transaction reference
     */
    private function storeServiceRequest(Service $service, array $serviceData, string $status, int $userId, int $transactionId): ServiceRequest
    {
        return ServiceRequest::create([
            'service_id' => $service->id,
            'user_id' => $userId,
            'input_data' => $serviceData,
            'status' => $status,
            'request_hash' => \Illuminate\Support\Str::random(32),
            'wallet_transaction_id' => $transactionId,
            'created_at' => now(),
        ]);
    }

    

    /**
     * Check if user can afford the service
     */
    public function canAffordService(User $user, Service $service): bool
    {
        return $user->balance >= $service->price;
    }

    /**
     * Get user's available balance (confirmed transactions only)
     */
    public function getAvailableBalance(User $user): int
    {
        return $user->balance; // Bavix automatically handles confirmed transactions only
    }

    /**
     * Get pending/unconfirmed transactions for a user
     */
    public function getPendingTransactions(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return $user->transactions()
            ->where('confirmed', false)
            ->where('type', 'withdraw')
            ->latest()
            ->get();
    }

    /**
     * Force confirm a transaction (admin function)
     */
    public function forceConfirmTransaction(int $transactionId, string $reason = 'Manual admin confirmation'): bool
    {
        try {
            $transaction = Transaction::find($transactionId);
            
            if (!$transaction || $transaction->confirmed) {
                return false;
            }

            $transaction->update([
                'confirmed' => true,
                'meta' => array_merge($transaction->meta ?? [], [
                    'force_confirmed_at' => now()->toISOString(),
                    'force_confirmation_reason' => $reason,
                ])
            ]);

            Log::info('Transaction force confirmed', [
                'transaction_id' => $transactionId,
                'reason' => $reason
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to force confirm transaction', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Force cancel a transaction (admin function)
     */
    public function forceCancelTransaction(int $transactionId, string $reason = 'Manual admin cancellation'): bool
    {
        try {
            $transaction = Transaction::find($transactionId);
            
            if (!$transaction || $transaction->confirmed) {
                return false;
            }

            $this->cancelTransaction($transaction, null, $reason);
            return true;

        } catch (\Exception $e) {
            Log::error('Failed to force cancel transaction', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Create a pending (unconfirmed) transaction
     */
    private function createPendingTransaction(User $user, Service $service)
    {
        return $user->withdraw($service->price, [
            'description' => "نگهداری وجه برای سرویس: {$service->title}",
            'service_id' => $service->id,
            'service_title' => $service->title,
            'service_slug' => $service->slug,
            'service_category_id' => $service->category_id,
            'service_price' => $service->price,
            'service_cost' => $service->cost,
            'type' => 'service_payment_hold',
            'payment_source' => 'service_preview',
            'payment_method' => 'wallet',
            'request_ip' => request()->ip(),
            'request_user_agent' => request()->userAgent(),
            'processed_at' => now()->toISOString(),
            'source_tracking' => [
                'source_type' => 'service',
                'source_id' => $service->id,
                'source_title' => $service->title,
                'source_category' => $service->category?->name ?? 'بدون دسته‌بندی',
                'payment_flow' => 'wallet_balance',
                'user_type' => 'authenticated',
                'transaction_context' => 'service_preview_payment'
            ]
        ], false); // false = unconfirmed transaction
    }

    // Removed getWalletGateway() method - using bavix wallet directly without payment gateway dependency
} 