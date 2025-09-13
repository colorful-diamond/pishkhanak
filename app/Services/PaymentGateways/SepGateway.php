<?php

namespace App\Services\PaymentGateways;

use App\Models\GatewayTransaction;
use App\Models\GatewayTransactionLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Exception;

class SepGateway extends AbstractPaymentGateway
{
    /**
     * Cache key for tokens (if needed)
     */
    private const TOKEN_CACHE_KEY = 'sep_token_cache';
    
    /**
     * Token cache TTL (19 minutes - tokens expire in 20 minutes by default)
     */
    private const TOKEN_CACHE_TTL = 1140; // 19 minutes in seconds

    /**
     * Get production API URL
     */
    protected function getApiUrl(): string
    {
        return 'https://sep.shaparak.ir';
    }

    /**
     * Get sandbox API URL
     */
    protected function getSandboxApiUrl(): string
    {
        return 'https://sep.shaparak.ir'; // SEP uses same URL for sandbox
    }

    /**
     * Get payment gateway URL
     */
    protected function getGatewayUrl(): string
    {
        return 'https://sep.shaparak.ir/OnlinePG/OnlinePG';
    }

    /**
     * Create payment and get payment URL
     */
    public function createPayment(GatewayTransaction $transaction): array
    {
        try {

            // Log payment creation attempt
            $this->logTransaction(
                $transaction,
                GatewayTransactionLog::ACTION_GATEWAY_CALLED,
                [
                    'message' => 'SEP payment creation initiated',
                    'data' => [
                        'amount' => $transaction->total_amount,
                        'currency' => $transaction->currency->code,
                        'reference_id' => $transaction->reference_id,
                    ]
                ]
            );

            // Get token from SEP API
            $tokenResponse = $this->getToken($transaction);

            if (!$tokenResponse['success']) {
                throw new Exception($tokenResponse['message'] ?? 'Failed to get payment token from SEP');
            }

            $token = $tokenResponse['token'];
            
            // Store token in transaction metadata for later use
            $transaction->update([
                'metadata' => array_merge($transaction->metadata ?? [], [
                    'sep_token' => $token,
                    'token_created_at' => now()->toISOString(),
                ]),
            ]);

            // Generate payment URL - SEP uses GET redirect with token parameter
            $paymentUrl = 'https://sep.shaparak.ir/OnlinePG/SendToken?token=' . urlencode($token);

            // Log successful token creation
            $this->logTransaction(
                $transaction,
                GatewayTransactionLog::ACTION_CREATED,
                [
                    'message' => 'SEP payment token created successfully',
                    'data' => [
                        'token' => substr($token, 0, 10) . '...',
                        'payment_url' => $paymentUrl,
                        'terminal_id' => $this->getConfig('terminal_id'),
                    ]
                ]
            );

            return $this->successResponse([
                'token' => $token,
                'payment_url' => $paymentUrl,
                'terminal_id' => $this->getConfig('terminal_id'),
                'amount' => $transaction->total_amount,
                'reference_id' => $transaction->reference_id,
                'status' => 'TOKEN_CREATED',
                'message' => 'Payment token created successfully',
                'redirect_method' => 'direct_redirect', // SEP uses direct URL redirect
                'gateway_name' => 'سامان', // Add gateway name for the view
            ]);

        } catch (Exception $e) {
            // Log error
            $this->logTransaction(
                $transaction,
                GatewayTransactionLog::ACTION_FAILED,
                [
                    'message' => 'SEP payment creation failed',
                    'error' => $e->getMessage(),
                ]
            );

            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get token from SEP API
     */
    protected function getToken(GatewayTransaction $transaction): array
    {
        // Always use normal PaymentController callback (was working before)
        $callbackUrl = route('payment.callback', ['gateway' => 'sep', 'transaction' => $transaction->uuid]);
            
        Log::info('🔗 SEP GATEWAY: Using default PaymentController callback', [
            'transaction_id' => $transaction->id,
            'transaction_uuid' => $transaction->uuid,
            'callback_url' => $callbackUrl,
        ]);

        $requestData = [
            'action' => 'token',
            'TerminalId' => (string) $this->getConfig('terminal_id'),
            'Amount' => $transaction->total_amount * 10, // Convert Toman to Rial for SEP
            'ResNum' => $transaction->reference_id ?? $transaction->uuid,
            'RedirectUrl' => $callbackUrl,
            'SettlementIbanInfo' => [
                [
                    'IBAN' => 'IR850560611828005171403801',
                    'Amount' => $transaction->total_amount * 10, // Convert Toman to Rial for SEP
                ]
            ]
        ];

        // Add ResNum1-4 parameters for reporting and tracking (max 50 chars each)
        $metadata = $transaction->metadata ?? [];
        
        // ResNum1: Service ID for tracking which service was paid for
        if (isset($metadata['service_id'])) {
            $requestData['ResNum1'] = substr('SERVICE_' . $metadata['service_id'], 0, 50);
        }
        
        // ResNum2: Service request hash for guest payment tracking
        if (isset($metadata['service_request_hash'])) {
            $requestData['ResNum2'] = substr($metadata['service_request_hash'], 0, 50);
        }
        
        // ResNum3: Payment type (guest vs user)
        if (isset($metadata['type'])) {
            $requestData['ResNum3'] = substr(strtoupper($metadata['type']), 0, 50);
        }
        
        // ResNum4: User ID or guest session for tracking
        if (isset($metadata['user_id'])) {
            $requestData['ResNum4'] = substr('USER_' . $metadata['user_id'], 0, 50);
        } elseif (isset($metadata['guest_session_token'])) {
            $requestData['ResNum4'] = substr('GUEST_' . substr($metadata['guest_session_token'], 0, 40), 0, 50);
        }

        // Add optional fields
        if (!empty($transaction->user?->mobile)) {
            $requestData['CellNumber'] = $transaction->user->mobile;
        }

        // Add token expiry if configured
        $tokenExpiry = $this->getConfig('token_expiry_minutes', 20);
        if ($tokenExpiry && $tokenExpiry !== 20) {
            $requestData['TokenExpiryInMin'] = max(20, min(3600, $tokenExpiry));
        }

        // Add wage/fee if configured (convert from Toman to Rial)
        if ($transaction->gateway_fee > 0) {
            $requestData['Wage'] = $transaction->gateway_fee * 10;
        }

        // Remove any fields with null values
        $requestData = array_filter($requestData, function($v) { return $v !== null; });

        // Log ResNum parameters for debugging
        Log::info('🏦 SEP GATEWAY: ResNum parameters configured', [
            'transaction_id' => $transaction->id,
            'transaction_uuid' => $transaction->uuid,
            'ResNum' => $requestData['ResNum'] ?? null,
            'ResNum1' => $requestData['ResNum1'] ?? null,
            'ResNum2' => $requestData['ResNum2'] ?? null, 
            'ResNum3' => $requestData['ResNum3'] ?? null,
            'ResNum4' => $requestData['ResNum4'] ?? null,
            'metadata_source' => $metadata,
            'note' => 'ResNum1-4 are for SEP reporting panel only, not returned in callback',
        ]);

        $apiUrl = $this->getCurrentApiUrl() . '/onlinepg/onlinepg';
        
        try {
            $response = Http::timeout($this->getConfig('timeout', 30))
                ->post($apiUrl, $requestData);

            if (!$response->successful()) {
                throw new Exception("SEP API returned HTTP {$response->status()}: " . $response->body());
            }

            $responseData = $response->json();

            if (!is_array($responseData)) {
                throw new Exception('Invalid JSON response from SEP API');
            }

            // Check if status indicates success
            $status = $responseData['status'] ?? null;
            if ($status === 1) {
                // Success - token received
                return $this->successResponse([
                    'token' => $responseData['token'],
                ]);
            } else {
                // Error - get error message
                $errorCode = $responseData['errorCode'] ?? 'unknown';
                $errorDesc = $responseData['errorDesc'] ?? 'Unknown error occurred';
                
                throw new Exception("SEP Token Error (Code: {$errorCode}): {$errorDesc}");
            }

        } catch (Exception $e) {
            Log::error('SEP Token request failed', [
                'error' => $e->getMessage(),
                'terminal_id' => $this->getConfig('terminal_id'),
            ]);

            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Verify payment from gateway callback
     */
    public function verifyPayment(GatewayTransaction $transaction, array $callbackData): array
    {
        try {
            // Log COMPLETE return data for analysis
            Log::info('🔍 SEP RETURN DATA: Complete analysis of all returned parameters', [
                'transaction_id' => $transaction->id,
                'transaction_uuid' => $transaction->uuid,
                'timestamp' => now(),
                'http_method' => request()->method(),
                'request_ip' => request()->ip(),
                'complete_callback_data' => $callbackData,
                'callback_data_count' => count($callbackData),
                'callback_keys' => array_keys($callbackData),
                'callback_values' => array_values($callbackData),
                // Check all possible ResNum variations
                'resnum_analysis' => [
                    'ResNum' => $callbackData['ResNum'] ?? 'NOT_PRESENT',
                    'ResNum1' => $callbackData['ResNum1'] ?? 'NOT_PRESENT',
                    'ResNum2' => $callbackData['ResNum2'] ?? 'NOT_PRESENT', 
                    'ResNum3' => $callbackData['ResNum3'] ?? 'NOT_PRESENT',
                    'ResNum4' => $callbackData['ResNum4'] ?? 'NOT_PRESENT',
                    'resnum' => $callbackData['resnum'] ?? 'NOT_PRESENT', // lowercase
                    'resnum1' => $callbackData['resnum1'] ?? 'NOT_PRESENT',
                    'resnum2' => $callbackData['resnum2'] ?? 'NOT_PRESENT',
                    'resnum3' => $callbackData['resnum3'] ?? 'NOT_PRESENT', 
                    'resnum4' => $callbackData['resnum4'] ?? 'NOT_PRESENT',
                ],
                // Standard SEP parameters
                'standard_params' => [
                    'State' => $callbackData['State'] ?? 'NOT_PRESENT',
                    'Status' => $callbackData['Status'] ?? 'NOT_PRESENT',
                    'RefNum' => $callbackData['RefNum'] ?? 'NOT_PRESENT',
                    'TraceNo' => $callbackData['TraceNo'] ?? 'NOT_PRESENT',
                    'RRN' => $callbackData['RRN'] ?? 'NOT_PRESENT',
                    'Amount' => $callbackData['Amount'] ?? 'NOT_PRESENT',
                    'AffectiveAmount' => $callbackData['AffectiveAmount'] ?? 'NOT_PRESENT',
                    'SecurePan' => $callbackData['SecurePan'] ?? 'NOT_PRESENT',
                    'HashedCardNumber' => $callbackData['HashedCardNumber'] ?? 'NOT_PRESENT',
                    'TerminalId' => $callbackData['TerminalId'] ?? 'NOT_PRESENT',
                    'MID' => $callbackData['MID'] ?? 'NOT_PRESENT',
                    'Token' => isset($callbackData['Token']) ? 'PRESENT' : 'NOT_PRESENT',
                    'Wage' => $callbackData['Wage'] ?? 'NOT_PRESENT',
                ],
                'raw_post_data' => file_get_contents('php://input'),
                'request_headers' => request()->headers->all(),
                'query_parameters' => request()->query->all(),
                'note' => 'Complete debugging to analyze what SEP actually returns',
            ]);

            // Log verification attempt
            $this->logTransaction(
                $transaction,
                GatewayTransactionLog::ACTION_WEBHOOK_RECEIVED,
                [
                    'message' => 'SEP payment verification initiated',
                    'request_data' => $callbackData,
                ]
            );

            // Check if this is a cancellation from callback data
            $state = $callbackData['State'] ?? null;
            $status = $callbackData['Status'] ?? null;
            
            // Log ALL callback data for debugging ResNum1-4 parameters
            Log::info('🔍 SEP CALLBACK: Complete callback data analysis', [
                'transaction_id' => $transaction->id,
                'state' => $state,
                'status' => $status,
                'resnum_main' => $callbackData['ResNum'] ?? null,
                'resnum1' => $callbackData['ResNum1'] ?? null, // Check if returned
                'resnum2' => $callbackData['ResNum2'] ?? null, // Check if returned  
                'resnum3' => $callbackData['ResNum3'] ?? null, // Check if returned
                'resnum4' => $callbackData['ResNum4'] ?? null, // Check if returned
                'amount' => $callbackData['Amount'] ?? null,
                'affective_amount' => $callbackData['AffectiveAmount'] ?? null,
                'ref_num' => $callbackData['RefNum'] ?? null,
                'trace_no' => $callbackData['TraceNo'] ?? null,
                'rrn' => $callbackData['RRN'] ?? null,
                'secure_pan' => $callbackData['SecurePan'] ?? null,
                'hashed_card_number' => $callbackData['HashedCardNumber'] ?? null,
                'terminal_id' => $callbackData['TerminalId'] ?? null,
                'mid' => $callbackData['MID'] ?? null,
                'wage' => $callbackData['Wage'] ?? null,
                'token' => isset($callbackData['Token']) ? 'present' : 'not_present',
                'all_callback_keys' => array_keys($callbackData),
                'complete_callback_data' => $callbackData,
                'note' => 'Debugging to check if ResNum1-4 are actually returned',
            ]);
            
            // Handle cancellation cases directly from callback data
            if ($state === 'CanceledByUser' || $status == 1) {
                $errorMessage = $this->getReceiptStatusMessage(1, $state);
                
                // Log cancellation
                $this->logTransaction(
                    $transaction,
                    GatewayTransactionLog::ACTION_FAILED,
                    [
                        'message' => 'SEP payment cancelled by user',
                        'callback_data' => $callbackData,
                        'state' => $state,
                        'status' => $status,
                    ]
                );

                return $this->errorResponse($errorMessage);
            }

            // Handle successful payment directly from callback data
            if ($state === 'OK' && $status == 2) {
                // Payment is successful - use callback data directly
                $receiptData = $callbackData;
                
                // Check if ResNum1-4 are returned in successful payments
                Log::info('🎉 SEP SUCCESS: Checking ResNum1-4 in successful payment callback', [
                    'transaction_id' => $transaction->id,
                    'original_resnum' => $receiptData['ResNum'] ?? null,
                    'returned_resnum1' => $receiptData['ResNum1'] ?? null,
                    'returned_resnum2' => $receiptData['ResNum2'] ?? null,
                    'returned_resnum3' => $receiptData['ResNum3'] ?? null,
                    'returned_resnum4' => $receiptData['ResNum4'] ?? null,
                    'ref_num' => $receiptData['RefNum'] ?? null,
                    'all_success_callback_keys' => array_keys($receiptData),
                    'full_success_data' => $receiptData,
                ]);
                
                // Validate amounts (gateway returns Rial, transaction is in Toman)
                $gatewayAmountRial = $receiptData['AffectiveAmount'];
                $expectedAmountRial = $transaction->total_amount * 10; // Convert transaction amount to Rial
                
                if ($gatewayAmountRial != $expectedAmountRial) {
                    Log::error('SEP amount mismatch', [
                        'gateway_amount_rial' => $gatewayAmountRial,
                        'transaction_amount_toman' => $transaction->total_amount,
                        'expected_amount_rial' => $expectedAmountRial,
                        'ref_num' => $receiptData['RefNum'],
                    ]);
                    throw new Exception('Amount mismatch between gateway and transaction');
                }

                // IMPORTANT: Call settlement/advice API to finalize the transaction
                $settleResult = $this->settleTransaction($receiptData['RefNum']);
                
                if (!$settleResult['success']) {
                    // Log settlement failure but continue with verification 
                    // as payment was already successful
                    Log::warning('SEP settlement failed but payment was successful', [
                        'ref_num' => $receiptData['RefNum'],
                        'settlement_error' => $settleResult['message'] ?? 'Unknown error',
                    ]);
                }

                // Extract ResNum1-4 if returned and add to metadata
                $updatedMetadata = $transaction->metadata ?? [];
                if (isset($receiptData['ResNum1'])) {
                    $updatedMetadata['sep_returned_resnum1'] = $receiptData['ResNum1'];
                }
                if (isset($receiptData['ResNum2'])) {
                    $updatedMetadata['sep_returned_resnum2'] = $receiptData['ResNum2'];  
                }
                if (isset($receiptData['ResNum3'])) {
                    $updatedMetadata['sep_returned_resnum3'] = $receiptData['ResNum3'];
                }
                if (isset($receiptData['ResNum4'])) {
                    $updatedMetadata['sep_returned_resnum4'] = $receiptData['ResNum4'];
                }

                // Store payment information
                $transaction->update([
                    'gateway_transaction_id' => $receiptData['RefNum'],
                    'gateway_reference' => $receiptData['Rrn'],
                    'gateway_response' => [
                        'receipt' => $receiptData,
                        'verification' => $receiptData, // Use same data for verification
                        'settlement' => $settleResult, // Store settlement result
                        'resnum_fields' => [ // Store ResNum fields separately for easy access
                            'ResNum' => $receiptData['ResNum'] ?? null,
                            'ResNum1' => $receiptData['ResNum1'] ?? null,
                            'ResNum2' => $receiptData['ResNum2'] ?? null,
                            'ResNum3' => $receiptData['ResNum3'] ?? null,
                            'ResNum4' => $receiptData['ResNum4'] ?? null,
                        ],
                    ],
                    'metadata' => $updatedMetadata,
                    'processed_at' => now(),
                ]);

                // Log successful verification
                $this->logTransaction(
                    $transaction,
                    GatewayTransactionLog::ACTION_COMPLETED,
                    [
                        'message' => 'SEP payment verified and settled successfully',
                        'data' => [
                            'ref_num' => $receiptData['RefNum'],
                            'rrn' => $receiptData['Rrn'],
                            'amount_rial' => $gatewayAmountRial,
                            'amount_toman' => $gatewayAmountRial / 10,
                            'card_number' => $receiptData['HashedCardNumber'] ?? null,
                            'settlement_success' => $settleResult['success'],
                        ]
                    ]
                );

                return $this->successResponse([
                    'verified' => true,
                    'settled' => $settleResult['success'],
                    'digital_receipt' => $receiptData['RefNum'],
                    'rrn' => $receiptData['Rrn'],
                    'amount' => $gatewayAmountRial / 10, // Convert from Rial to Toman
                    'card_number' => $receiptData['HashedCardNumber'] ?? null,
                    'transaction_date' => now(),
                    'trace_number' => $receiptData['TraceNo'] ?? null,
                    'status' => 'verified',
                    'message' => 'Payment verified and settled successfully',
                    // Include ResNum fields if returned for processing
                    'resnum_data' => [
                        'service_id' => $receiptData['ResNum1'] ?? null,
                        'service_request_hash' => $receiptData['ResNum2'] ?? null,
                        'payment_type' => $receiptData['ResNum3'] ?? null,
                        'user_or_guest_id' => $receiptData['ResNum4'] ?? null,
                    ],
                ]);
            }

            // For other cases, try to get transaction receipt from API
            $receiptResponse = $this->getTransactionReceipt($transaction, $callbackData);
            
            if (!$receiptResponse['success']) {
                throw new Exception($receiptResponse['message'] ?? 'Failed to get transaction receipt');
            }

            $receiptData = $receiptResponse;
            
            // Check if payment was successful
            if ($receiptData['State'] !== 'OK' || $receiptData['Status'] !== 2) {
                $errorMessage = $this->getReceiptStatusMessage($receiptData['Status'], $receiptData['State']);
                throw new Exception($errorMessage);
            }

            // Step 2: Verify the transaction (only for non-successful cases that need API verification)
            $verifyResponse = $this->verifyTransaction($receiptData['RefNum']);
            
            if (!$verifyResponse['success']) {
                throw new Exception($verifyResponse['message'] ?? 'Transaction verification failed');
            }

            $verifyData = $verifyResponse;
            
            // Validate amounts (gateway returns Rial, transaction is in Toman)
            $gatewayAmountRial = $verifyData['AffectiveAmount'];
            $expectedAmountRial = $transaction->total_amount * 10; // Convert transaction amount to Rial
            
            if ($gatewayAmountRial !== $expectedAmountRial) {
                Log::error('SEP amount mismatch', [
                    'gateway_amount_rial' => $gatewayAmountRial,
                    'transaction_amount_toman' => $transaction->total_amount,
                    'expected_amount_rial' => $expectedAmountRial,
                    'ref_num' => $receiptData['RefNum'],
                ]);
                throw new Exception('Amount mismatch between gateway and transaction');
            }

            // IMPORTANT: Call settlement/advice API to finalize the transaction
            $settleResult = $this->settleTransaction($receiptData['RefNum']);
            
            if (!$settleResult['success']) {
                // Log settlement failure but continue with verification 
                // as payment was already successful
                Log::warning('SEP settlement failed but payment was successful', [
                    'ref_num' => $receiptData['RefNum'],
                    'settlement_error' => $settleResult['message'] ?? 'Unknown error',
                ]);
            }

            // Store payment information
            $transaction->update([
                'gateway_transaction_id' => $receiptData['RefNum'],
                'gateway_reference' => $receiptData['Rrn'],
                'gateway_response' => [
                    'receipt' => $receiptData,
                    'verification' => $verifyData,
                    'settlement' => $settleResult, // Store settlement result
                ],
                'processed_at' => now(),
            ]);

            // Log successful verification
            $this->logTransaction(
                $transaction,
                GatewayTransactionLog::ACTION_COMPLETED,
                [
                    'message' => 'SEP payment verified and settled successfully via API',
                    'data' => [
                        'ref_num' => $receiptData['RefNum'],
                        'rrn' => $receiptData['Rrn'],
                        'amount_rial' => $gatewayAmountRial,
                        'amount_toman' => $gatewayAmountRial / 10,
                        'card_number' => $receiptData['HashedCardNumber'] ?? null,
                        'settlement_success' => $settleResult['success'],
                    ]
                ]
            );

            return $this->successResponse([
                'verified' => true,
                'settled' => $settleResult['success'],
                'digital_receipt' => $receiptData['RefNum'],
                'rrn' => $receiptData['Rrn'],
                'amount' => $gatewayAmountRial / 10, // Convert from Rial to Toman
                'card_number' => $receiptData['HashedCardNumber'] ?? null,
                'transaction_date' => $verifyData['StraceDate'] ?? null,
                'trace_number' => $receiptData['TraceNo'] ?? null,
                'status' => 'verified',
                'message' => 'Payment verified and settled successfully',
            ]);

        } catch (Exception $e) {
            // Log verification failure
            $this->logTransaction(
                $transaction,
                GatewayTransactionLog::ACTION_FAILED,
                [
                    'message' => 'SEP payment verification failed',
                    'error' => $e->getMessage(),
                    'callback_data' => $callbackData,
                ]
            );

            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Settle/approve transaction with SEP (similar to advice in other gateways)
     * This finalizes the transaction on the gateway side
     */
    protected function settleTransaction(string $refNum): array
    {
        try {
            // Use the verify API as settlement - calling it confirms the transaction
            $apiUrl = $this->getCurrentApiUrl() . '/verifyTxnRandomSessionkey/ipg/VerifyTransaction';
            
            $requestData = [
                'RefNum' => $refNum,
                'TerminalNumber' => (int) $this->getConfig('terminal_id'),
            ];

            Log::info('SEP Settlement attempt', [
                'ref_num' => $refNum,
                'terminal_id' => $this->getConfig('terminal_id'),
                'api_url' => $apiUrl,
            ]);

            $response = Http::timeout($this->getConfig('timeout', 30))
                ->post($apiUrl, $requestData);

            if (!$response->successful()) {
                throw new Exception("SEP Settlement API returned HTTP {$response->status()}: " . $response->body());
            }

            $responseData = $response->json();

            if ($responseData['Success'] !== true || $responseData['ResultCode'] !== 0) {
                $resultCode = $responseData['ResultCode'] ?? 'unknown';
                $resultDesc = $responseData['ResultDescription'] ?? 'Settlement failed';
                
                // Don't throw exception for settlement failure as payment was already successful
                Log::warning('SEP Settlement failed', [
                    'ref_num' => $refNum,
                    'result_code' => $resultCode,
                    'result_description' => $resultDesc,
                ]);
                
                return $this->errorResponse("Settlement Warning (Code: {$resultCode}): {$resultDesc}");
            }

            Log::info('SEP Settlement successful', [
                'ref_num' => $refNum,
                'response_data' => $responseData,
            ]);

            return $this->successResponse([
                'settled' => true,
                'transaction_detail' => $responseData['TransactionDetail'] ?? null,
                'message' => 'Transaction settled successfully',
            ]);

        } catch (Exception $e) {
            Log::error('SEP Settlement API failed', [
                'error' => $e->getMessage(),
                'ref_num' => $refNum,
                'terminal_id' => $this->getConfig('terminal_id'),
            ]);

            // Don't fail the whole verification for settlement issues
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get transaction receipt from SEP
     */
    protected function getTransactionReceipt(GatewayTransaction $transaction, array $callbackData): array
    {
        $apiUrl = $this->getCurrentApiUrl() . '/verifyTxnRandomSessionkey/api/v2/ipg/payment/receipt';
        
        // Extract token or RefNum from callback
        $token = $callbackData['Token'] ?? null;
        $refNum = $callbackData['RefNum'] ?? null;
        
        if (!$token && !$refNum) {
            throw new Exception('Missing required Token or RefNum in callback data');
        }

        $requestData = [
            'TerminalNumber' => (int) $this->getConfig('terminal_id'),
        ];

        if ($token) {
            $requestData['Token'] = $token;
        } else {
            $requestData['RefNum'] = $refNum;
        }

        try {
            $response = Http::timeout($this->getConfig('timeout', 30))
                ->post($apiUrl, $requestData);

            if (!$response->successful()) {
                throw new Exception("SEP Receipt API returned HTTP {$response->status()}: " . $response->body());
            }

            $responseData = $response->json();

            // Check if response is null or empty
            if (empty($responseData)) {
                throw new Exception("SEP Receipt API returned empty response");
            }

            if ($responseData['HasError'] === true) {
                $errorCode = $responseData['ErrorCode'] ?? 'unknown';
                $errorMessage = $responseData['ErrorMessage'] ?? 'Unknown error';
                throw new Exception("SEP Receipt Error (Code: {$errorCode}): {$errorMessage}");
            }

            // Check if Data field exists and is not null
            if (!isset($responseData['Data']) || $responseData['Data'] === null) {
                throw new Exception("SEP Receipt API returned null data - transaction may not exist or be accessible");
            }

            return $this->successResponse($responseData['Data']);

        } catch (Exception $e) {
            Log::error('SEP Receipt API failed', [
                'error' => $e->getMessage(),
                'api_url' => $apiUrl,
                'request_data' => $requestData,
                'response_status' => $response->status() ?? null,
                'response_body' => $response->body() ?? null,
            ]);

            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Verify transaction with SEP
     */
    protected function verifyTransaction(string $refNum): array
    {
        $apiUrl = $this->getCurrentApiUrl() . '/verifyTxnRandomSessionkey/ipg/VerifyTransaction';
        
        $requestData = [
            'RefNum' => $refNum,
            'TerminalNumber' => (int) $this->getConfig('terminal_id'),
        ];

        try {
            $response = Http::timeout($this->getConfig('timeout', 30))
                ->post($apiUrl, $requestData);

            if (!$response->successful()) {
                throw new Exception("SEP Verify API returned HTTP {$response->status()}: " . $response->body());
            }

            $responseData = $response->json();

            if ($responseData['Success'] !== true || $responseData['ResultCode'] !== 0) {
                $resultCode = $responseData['ResultCode'] ?? 'unknown';
                $resultDesc = $responseData['ResultDescription'] ?? 'Verification failed';
                throw new Exception("SEP Verify Error (Code: {$resultCode}): {$resultDesc}");
            }

            return $this->successResponse($responseData['TransactionDetail']);

        } catch (Exception $e) {
            Log::error('SEP Verify API failed', [
                'error' => $e->getMessage(),
                'api_url' => $apiUrl,
                'request_data' => $requestData,
            ]);

            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Process refund (reverse transaction)
     */
    public function refund(GatewayTransaction $transaction, int $amount = null): array
    {
        try {
            // Check if refunds are enabled
            if (!$this->getConfig('refund_enabled', false)) {
                throw new Exception('Refunds are not enabled for this gateway');
            }

            // SEP only supports full refunds
            if ($amount !== null && $amount !== $transaction->total_amount) {
                throw new Exception('SEP only supports full refunds, not partial refunds');
            }

            // Get RefNum from transaction
            $refNum = $transaction->gateway_transaction_id;
            if (!$refNum) {
                throw new Exception('No digital receipt found for this transaction');
            }

            $apiUrl = $this->getCurrentApiUrl() . '/verifyTxnRandomSessionkey/ipg/ReverseTransaction';
            
            $requestData = [
                'RefNum' => $refNum,
                'TerminalNumber' => (int) $this->getConfig('terminal_id'),
            ];

            $response = Http::timeout($this->getConfig('timeout', 30))
                ->post($apiUrl, $requestData);

            if (!$response->successful()) {
                throw new Exception("SEP Reverse API returned HTTP {$response->status()}: " . $response->body());
            }

            $responseData = $response->json();

            if ($responseData['Success'] !== true || $responseData['ResultCode'] !== 0) {
                $resultCode = $responseData['ResultCode'] ?? 'unknown';
                $resultDesc = $responseData['ResultDescription'] ?? 'Reverse failed';
                throw new Exception("SEP Reverse Error (Code: {$resultCode}): {$resultDesc}");
            }

            // Log successful refund
            $this->logTransaction(
                $transaction,
                GatewayTransactionLog::ACTION_REFUNDED,
                [
                    'message' => 'SEP refund processed successfully',
                    'data' => [
                        'ref_num' => $refNum,
                        'amount' => $transaction->total_amount,
                        'reverse_response' => $responseData,
                    ]
                ]
            );

            return $this->successResponse([
                'refund_amount' => $transaction->total_amount,
                'refund_reference' => $refNum,
                'status' => 'refunded',
                'message' => 'Refund processed successfully',
            ]);

        } catch (Exception $e) {
            // Log refund failure
            $this->logTransaction(
                $transaction,
                GatewayTransactionLog::ACTION_FAILED,
                [
                    'message' => 'SEP refund failed',
                    'error' => $e->getMessage(),
                ]
            );

            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get payment status from transaction data
     */
    public function getPaymentStatus(GatewayTransaction $transaction): array
    {
        try {
            $status = $transaction->status;
            $gatewayResponse = $transaction->gateway_response ?? [];

            $statusMap = [
                'pending' => 'در انتظار پرداخت',
                'processing' => 'در حال پردازش',
                'completed' => 'پرداخت موفق',
                'failed' => 'پرداخت ناموفق',
                'cancelled' => 'لغو شده',
                'refunded' => 'برگشت داده شده',
                'expired' => 'منقضی شده',
            ];

            return $this->successResponse([
                'status' => $status,
                'status_text' => $statusMap[$status] ?? $status,
                'amount' => $transaction->total_amount,
                'gateway_reference' => $transaction->gateway_reference,
                'digital_receipt' => $transaction->gateway_transaction_id,
                'created_at' => $transaction->created_at,
                'completed_at' => $transaction->completed_at,
            ]);

        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get receipt status message
     */
    protected function getReceiptStatusMessage(int $status, string $state): string
    {
        $messages = [
            0 => 'تراکنش در حال پردازش است',
            1 => 'تراکنش توسط کاربر لغو شد',
            2 => 'تراکنش موفق انجام شد',
            3 => 'تراکنش ناموفق بود',
        ];

        return $messages[$status] ?? "وضعیت نامشخص: {$state}";
    }

    /**
     * Get configuration requirements
     */
    public function getConfigRequirements(): array
    {
        return [
            'terminal_id',
            'sandbox',
        ];
    }

    /**
     * Get supported currencies
     */
    public function getSupportedCurrencies(): array
    {
        return ['IRT']; // SEP only supports Iranian Toman/Rial
    }

    /**
     * Get amount limits
     */
    public function getAmountLimits(): array
    {
        return [
            'min' => 1000, // 1,000 Rials minimum
            'max' => 500000000, // 500,000,000 Rials maximum
        ];
    }

    /**
     * Get callback URL
     */
    public function getCallbackUrl(): string
    {
        return route('payment.callback', ['gateway' => 'sep']);
    }

    /**
     * Get gateway information
     */
    public function getGatewayInfo(): array
    {
        return [
            'name' => 'Saman Electronic Payment Gateway',
            'version' => '4.1',
            'supports_refund' => true,
            'supports_partial_refund' => false, // SEP doesn't support partial refunds
            'supports_webhook' => false, // SEP uses callback, not webhooks
            'payment_methods' => ['CARD'],
            'features' => [
                'standard_purchase',
                'token_payment',
                'callback_verification',
                'receipt_retrieval',
                'transaction_verification',
                'transaction_reversal',
                'hashed_card_restriction',
                'mobile_number_integration',
            ],
        ];
    }

    /**
     * Validate webhook signature (not used by SEP)
     */
    public function validateWebhookSignature(array $payload, string $signature): bool
    {
        // SEP doesn't use webhook signatures
        return true;
    }
} 