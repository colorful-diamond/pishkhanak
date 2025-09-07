<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\PaymentService;
use App\Models\GatewayTransaction;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Create a new payment
     */
    public function createPayment(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'amount' => 'required|integer|min:1000', // Minimum 1000 rials
                'currency' => 'required|string|size:3|in:IRT,USD,EUR',
                'description' => 'nullable|string|max:255',
                'gateway_id' => 'nullable|exists:payment_gateways,id',
                'callback_url' => 'nullable|url',
                'metadata' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid input data',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $paymentData = $validator->validated();
            $paymentData['user_id'] = Auth::id();

            $result = $this->paymentService->createPayment($paymentData);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'data' => [
                        'transaction_uuid' => $result['transaction']->uuid,
                        'payment_url' => $result['payment_url'],
                        'amount_breakdown' => $result['amount_breakdown'],
                        'gateway' => $result['transaction']->paymentGateway->name,
                        'currency' => $result['transaction']->currency->code,
                    ],
                ], 201);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message'],
                'error' => $result['error'] ?? null,
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment creation failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verify payment
     */
    public function verifyPayment(Request $request, string $transactionUuid): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'gateway_data' => 'required|array',
                'gateway_slug' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid verification data',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $result = $this->paymentService->verifyPayment(
                $transactionUuid,
                $request->input('gateway_data'),
                $request->input('gateway_slug')
            );

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'data' => [
                        'transaction_uuid' => $result['transaction']->uuid,
                        'status' => $result['transaction']->status,
                        'amount' => $result['transaction']->getFormattedTotalAmount(),
                        'reference_id' => $result['reference_id'] ?? null,
                        'completed_at' => $result['transaction']->completed_at?->toISOString(),
                    ],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message'],
                'error' => $result['error'] ?? null,
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در تأیید پرداخت',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get transaction details
     */
    public function getTransaction(string $transactionUuid): JsonResponse
    {
        try {
            $transaction = $this->paymentService->getTransaction($transactionUuid);

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction not found',
                ], 404);
            }

            // Check if user owns this transaction
            if ($transaction->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to transaction',
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'uuid' => $transaction->uuid,
                    'amount' => $transaction->amount,
                    'tax_amount' => $transaction->tax_amount,
                    'gateway_fee' => $transaction->gateway_fee,
                    'total_amount' => $transaction->total_amount,
                    'formatted_total' => $transaction->getFormattedTotalAmount(),
                    'status' => $transaction->status,
                    'status_label' => $transaction->getStatusLabel(),
                    'type' => $transaction->type,
                    'description' => $transaction->description,
                    'gateway' => [
                        'name' => $transaction->paymentGateway->name,
                        'slug' => $transaction->paymentGateway->slug,
                    ],
                    'currency' => [
                        'code' => $transaction->currency->code,
                        'name' => $transaction->currency->name,
                    ],
                    'gateway_transaction_id' => $transaction->gateway_transaction_id,
                    'gateway_reference' => $transaction->gateway_reference,
                    'created_at' => $transaction->created_at->toISOString(),
                    'processed_at' => $transaction->processed_at?->toISOString(),
                    'completed_at' => $transaction->completed_at?->toISOString(),
                    'failed_at' => $transaction->failed_at?->toISOString(),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve transaction',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get user transactions
     */
    public function getUserTransactions(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'per_page' => 'nullable|integer|min:1|max:100',
                'status' => 'nullable|string|in:pending,processing,completed,failed,cancelled,refunded,partially_refunded,expired',
                'type' => 'nullable|string|in:payment,refund,partial_refund,wallet_charge,wallet_charge_for_service',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid parameters',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $perPage = $request->input('per_page', 15);
            
            // Build query with filters
            $query = GatewayTransaction::where('user_id', Auth::id())
                ->with(['paymentGateway', 'currency'])
                ->orderBy('created_at', 'desc');

            // Apply filters if provided
            if ($request->has('status')) {
                $query->where('status', $request->input('status'));
            }

            if ($request->has('type')) {
                $query->where('type', $request->input('type'));
            }

            $transactions = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $transactions->items(),
                'pagination' => [
                    'current_page' => $transactions->currentPage(),
                    'last_page' => $transactions->lastPage(),
                    'per_page' => $transactions->perPage(),
                    'total' => $transactions->total(),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve transactions',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Refund payment
     */
    public function refundPayment(Request $request, string $transactionUuid): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'amount' => 'nullable|integer|min:1000',
                'reason' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid refund data',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $result = $this->paymentService->refundPayment(
                $transactionUuid,
                $request->input('amount'),
                $request->input('reason')
            );

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'data' => [
                        'refund_transaction_uuid' => $result['refund_transaction']->uuid,
                        'original_transaction_uuid' => $result['original_transaction']->uuid,
                        'refund_amount' => $result['refund_transaction']->total_amount,
                        'original_status' => $result['original_transaction']->status,
                    ],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message'],
                'error' => $result['error'] ?? null,
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Refund processing failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get available payment gateways
     */
    public function getGateways(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'amount' => 'nullable|integer|min:1000',
                'currency' => 'nullable|string|size:3|in:IRT,USD,EUR',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid parameters',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $amount = $request->input('amount');
            $currency = $request->input('currency', 'IRT');

            $query = PaymentGateway::where('is_active', true);

            if ($amount && $currency) {
                $query->where(function ($q) use ($amount, $currency) {
                    $q->where('min_amount', '<=', $amount)
                      ->where('max_amount', '>=', $amount)
                      ->whereJsonContains('supported_currencies', $currency);
                });
            }

            $gateways = $query->get()->map(function ($gateway) use ($amount) {
                return [
                    'id' => $gateway->id,
                    'name' => $gateway->name,
                    'slug' => $gateway->slug,
                    'description' => $gateway->description,
                    'logo_url' => $gateway->logo_url,
                    'min_amount' => $gateway->min_amount,
                    'max_amount' => $gateway->max_amount,
                    'supported_currencies' => $gateway->supported_currencies,
                    'fee' => $amount ? $gateway->calculateFee($amount) : null,
                    'fee_percentage' => $gateway->fee_percentage,
                    'fee_fixed' => $gateway->fee_fixed,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $gateways,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve gateways',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
} 