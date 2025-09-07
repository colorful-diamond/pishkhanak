<?php

namespace App\Services;

use App\Models\ServiceRequest;
use App\Models\GatewayTransaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class GuestServiceClaimService
{
    /**
     * Claim and process a guest service request after login.
     * Should be called after successful login/phone verification.
     */
    public function claimPendingGuestRequest(User $user)
    {
        // Try to find pending request using session first, then fallback to hash-based lookup
        $serviceRequest = $this->findPendingServiceRequest();
        $transaction = $this->findPendingTransaction();

        if (!$serviceRequest || !$transaction) {
            return null; // Nothing to claim
        }

        // Security: Ensure the phone number matches (if possible)
        if (!$this->validatePhoneMatch($user, $serviceRequest, $transaction)) {
            return null;
        }

        DB::beginTransaction();
        try {
            // Update user_id on both records
            $serviceRequest->user_id = $user->id;
            $serviceRequest->save();

            $transaction->user_id = $user->id;
            $transaction->save();

            // Process wallet/service as needed (call your existing logic)
            if (method_exists($transaction, 'isPaid') ? $transaction->isPaid() : $transaction->status === 'paid') {
                app(\App\Services\ServicePaymentService::class)->processPaymentCallback($transaction);
            }

            DB::commit();

            // Clean up session
            Session::forget([
                'pending_service_request_id', 
                'pending_gateway_transaction_id', 
                'pending_guest_phone',
                'pending_service_request_hash'
            ]);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Guest service claim failed', [
                'user_id' => $user->id,
                'service_request_id' => $serviceRequest->id ?? null,
                'transaction_id' => $transaction->id ?? null,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Find pending service request using session or hash-based lookup
     */
    protected function findPendingServiceRequest(): ?ServiceRequest
    {
        // Try session first
        $pendingServiceRequestId = Session::get('pending_service_request_id');
        if ($pendingServiceRequestId) {
            $serviceRequest = ServiceRequest::find($pendingServiceRequestId);
            if ($serviceRequest) {
                return $serviceRequest;
            }
        }

        // Fallback to hash-based lookup
        $pendingTransactionId = Session::get('pending_gateway_transaction_id');
        if ($pendingTransactionId) {
            $transaction = GatewayTransaction::find($pendingTransactionId);
            if ($transaction) {
                $metadata = $transaction->metadata ?? [];
                $requestHash = $metadata['service_request_hash'] ?? null;
                
                if ($requestHash) {
                    return ServiceRequest::findByHash($requestHash);
                }
            }
        }

        return null;
    }

    /**
     * Find pending transaction using session
     */
    protected function findPendingTransaction(): ?GatewayTransaction
    {
        $pendingTransactionId = Session::get('pending_gateway_transaction_id');
        if ($pendingTransactionId) {
            return GatewayTransaction::find($pendingTransactionId);
        }

        return null;
    }

    /**
     * Validate that the user's phone matches the payment phone
     */
    protected function validatePhoneMatch(User $user, ServiceRequest $serviceRequest, GatewayTransaction $transaction): bool
    {
        // Check session phone first
        $pendingPhone = Session::get('pending_guest_phone');
        if ($pendingPhone && $user->mobile !== $pendingPhone) {
            return false;
        }

        // Check transaction metadata phone as fallback
        $metadata = $transaction->metadata ?? [];
        $paymentPhone = $metadata['guest_phone'] ?? null;
        
        if ($paymentPhone && $user->mobile !== $paymentPhone) {
            return false;
        }

        return true;
    }

    /**
     * Claim service request by hash (for direct gateway callbacks)
     * This can be used when gateway returns the request hash in callback
     */
    public function claimServiceRequestByHash(string $requestHash, User $user): bool
    {
        $serviceRequest = ServiceRequest::findByHash($requestHash);
        
        if (!$serviceRequest) {
            Log::warning('Service request not found by hash', [
                'request_hash' => $requestHash,
                'user_id' => $user->id
            ]);
            return false;
        }

        // Find associated transaction
        $transaction = GatewayTransaction::where('metadata->service_request_hash', $requestHash)->first();
        
        if (!$transaction) {
            Log::warning('Transaction not found for service request hash', [
                'request_hash' => $requestHash,
                'user_id' => $user->id
            ]);
            return false;
        }

        // Validate phone match
        if (!$this->validatePhoneMatch($user, $serviceRequest, $transaction)) {
            Log::warning('Phone mismatch for service request hash', [
                'request_hash' => $requestHash,
                'user_id' => $user->id,
                'user_phone' => $user->mobile,
                'payment_phone' => $transaction->metadata['guest_phone'] ?? null
            ]);
            return false;
        }

        DB::beginTransaction();
        try {
            // Update user_id on both records
            $serviceRequest->user_id = $user->id;
            $serviceRequest->save();

            $transaction->user_id = $user->id;
            $transaction->save();

            // Process wallet/service as needed
            if (method_exists($transaction, 'isPaid') ? $transaction->isPaid() : $transaction->status === 'paid') {
                app(\App\Services\ServicePaymentService::class)->processPaymentCallback($transaction);
            }

            DB::commit();

            Log::info('Service request claimed by hash successfully', [
                'request_hash' => $requestHash,
                'user_id' => $user->id,
                'service_request_id' => $serviceRequest->id,
                'transaction_id' => $transaction->id
            ]);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Service request claim by hash failed', [
                'request_hash' => $requestHash,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
} 