<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\Finnotech\CreditSmsService;
use App\Services\Finnotech\KycSmsService;
use App\Services\Finnotech\OakSmsService;
use App\Exceptions\FinnotechException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

/**
 * Class FinnotechExampleController
 * 
 * Example controller demonstrating how to use the SMS authorization services.
 * This shows the complete flow from checking tokens to making API calls.
 */
class FinnotechExampleController extends Controller
{
    private CreditSmsService $creditService;
    private KycSmsService $kycService;
    private OakSmsService $oakService;

    public function __construct(
        CreditSmsService $creditService,
        KycSmsService $kycService,
        OakSmsService $oakService
    ) {
        $this->creditService = $creditService;
        $this->kycService = $kycService;
        $this->oakService = $oakService;
    }

    /**
     * Example: Get facility inquiry with automatic SMS auth handling.
     * This method demonstrates the complete flow:
     * 1. Check if user has valid token
     * 2. If not, provide authorization URL
     * 3. If yes, make the API call
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getFacilityInquiryExample(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'national_id' => 'required|string|size:10',
            'mobile' => 'required|string|regex:/^09[0-9]{9}$/',
            'track_id' => 'nullable|string|max:40'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $nationalId = $request->national_id;
            $mobile = $request->mobile;
            $trackId = $request->track_id;

            // Step 1: Check token status
            $tokenStatus = $this->creditService->checkTokenStatus($nationalId, $mobile);
            
            if (!$tokenStatus['credit:sms-facility-inquiry:get']['has_token']) {
                // Step 2: User needs to authorize first
                $authUrls = $this->creditService->generateAuthorizationUrls($nationalId, $mobile);
                
                return response()->json([
                    'status' => 'authorization_required',
                    'message' => 'SMS authorization required before making API calls',
                    'data' => [
                        'authorization_url' => $authUrls['credit:sms-facility-inquiry:get']['url'],
                        'scope' => 'credit:sms-facility-inquiry:get',
                        'instructions' => 'Please visit the authorization URL to complete SMS verification'
                    ]
                ], 200);
            }

            // Step 3: Token exists, make the API call
            $result = $this->creditService->getFacilityInquiry($nationalId, $mobile, $trackId);

            return response()->json([
                'status' => 'success',
                'data' => $result,
                'token_info' => [
                    'expires_at' => $tokenStatus['credit:sms-facility-inquiry:get']['expires_at'],
                    'created_at' => $tokenStatus['credit:sms-facility-inquiry:get']['created_at']
                ]
            ]);

        } catch (FinnotechException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'error_type' => 'finnotech_error'
            ], 400);
        } catch (\Exception $e) {
            Log::error('Facility inquiry example failed', [
                'national_id' => $request->national_id,
                'mobile' => $request->mobile,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred'
            ], 500);
        }
    }

    /**
     * Example: KYC NID verification with automatic SMS auth handling.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyNationalIdExample(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'national_id' => 'required|string|size:10',
            'mobile' => 'required|string|regex:/^09[0-9]{9}$/',
            'birth_date' => 'required|string|size:8', // YYYYMMDD
            'gender' => 'nullable|string|in:M,F',
            'full_name' => 'nullable|string|max:255',
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'father_name' => 'nullable|string|max:100',
            'track_id' => 'nullable|string|max:40'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $nationalId = $request->national_id;
            $mobile = $request->mobile;

            // Check token status
            $tokenStatus = $this->kycService->checkTokenStatus($nationalId, $mobile);
            
            if (!$tokenStatus['has_token']) {
                // Generate authorization URL
                $authUrl = $this->kycService->generateAuthorizationUrl($nationalId, $mobile);
                
                return response()->json([
                    'status' => 'authorization_required',
                    'message' => 'SMS authorization required for KYC verification',
                    'data' => [
                        'authorization_url' => $authUrl,
                        'scope' => 'kyc:sms-nid-verification:get',
                        'instructions' => 'Please visit the authorization URL to complete SMS verification'
                    ]
                ], 200);
            }

            // Make the API call
            $result = $this->kycService->verifyNationalId(
                $nationalId,
                $mobile,
                $request->birth_date,
                $request->gender,
                $request->full_name,
                $request->first_name,
                $request->last_name,
                $request->father_name,
                $request->track_id
            );

            return response()->json([
                'status' => 'success',
                'data' => $result,
                'token_info' => [
                    'expires_at' => $tokenStatus['expires_at'],
                    'created_at' => $tokenStatus['created_at']
                ]
            ]);

        } catch (FinnotechException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'error_type' => 'finnotech_error'
            ], 400);
        } catch (\Exception $e) {
            Log::error('KYC verification example failed', [
                'national_id' => $request->national_id,
                'mobile' => $request->mobile,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred'
            ], 500);
        }
    }

    /**
     * Example: Shahab inquiry with automatic SMS auth handling.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getShahabInquiryExample(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'national_id' => 'required|string|size:10',
            'mobile' => 'required|string|regex:/^09[0-9]{9}$/',
            'birth_date' => 'required|string|size:8', // YYYYMMDD
            'identity_no' => 'nullable|string', // For pre-1968 births
            'track_id' => 'nullable|string|max:40'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $nationalId = $request->national_id;
            $mobile = $request->mobile;

            // Check token status
            $tokenStatus = $this->oakService->checkTokenStatus($nationalId, $mobile);
            
            if (!$tokenStatus['has_token']) {
                // Generate authorization URL
                $authUrl = $this->oakService->generateAuthorizationUrl($nationalId, $mobile);
                
                return response()->json([
                    'status' => 'authorization_required',
                    'message' => 'SMS authorization required for Shahab inquiry',
                    'data' => [
                        'authorization_url' => $authUrl,
                        'scope' => 'oak:sms-shahab-inquiry:get',
                        'instructions' => 'Please visit the authorization URL to complete SMS verification'
                    ]
                ], 200);
            }

            // Make the API call
            $result = $this->oakService->getShahabInquiry(
                $nationalId,
                $mobile,
                $request->birth_date,
                $request->identity_no,
                $request->track_id
            );

            return response()->json([
                'status' => 'success',
                'data' => $result,
                'token_info' => [
                    'expires_at' => $tokenStatus['expires_at'],
                    'created_at' => $tokenStatus['created_at']
                ]
            ]);

        } catch (FinnotechException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'error_type' => 'finnotech_error'
            ], 400);
        } catch (\Exception $e) {
            Log::error('Shahab inquiry example failed', [
                'national_id' => $request->national_id,
                'mobile' => $request->mobile,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred'
            ], 500);
        }
    }

    /**
     * Get comprehensive token status for all SMS services.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getAllTokenStatus(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'national_id' => 'required|string|size:10',
            'mobile' => 'required|string|regex:/^09[0-9]{9}$/'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $nationalId = $request->national_id;
            $mobile = $request->mobile;

            $allTokenStatus = [
                'credit' => $this->creditService->checkTokenStatus($nationalId, $mobile),
                'kyc' => $this->kycService->checkTokenStatus($nationalId, $mobile),
                'oak' => $this->oakService->checkTokenStatus($nationalId, $mobile)
            ];

            // Generate authorization URLs for services that need them
            $authorizationUrls = [];

            // Credit services
            $creditTokens = $allTokenStatus['credit'];
            $needsCreditAuth = false;
            foreach ($creditTokens as $scope => $status) {
                if (!$status['has_token']) {
                    $needsCreditAuth = true;
                    break;
                }
            }
            if ($needsCreditAuth) {
                $authorizationUrls['credit'] = $this->creditService->generateAuthorizationUrls($nationalId, $mobile);
            }

            // KYC service
            if (!$allTokenStatus['kyc']['has_token']) {
                $authorizationUrls['kyc'] = $this->kycService->generateAuthorizationUrl($nationalId, $mobile);
            }

            // Oak service
            if (!$allTokenStatus['oak']['has_token']) {
                $authorizationUrls['oak'] = $this->oakService->generateAuthorizationUrl($nationalId, $mobile);
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'token_status' => $allTokenStatus,
                    'authorization_urls' => $authorizationUrls,
                    'national_id' => $nationalId,
                    'mobile' => $mobile
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Get all token status failed', [
                'national_id' => $request->national_id,
                'mobile' => $request->mobile,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred'
            ], 500);
        }
    }
} 