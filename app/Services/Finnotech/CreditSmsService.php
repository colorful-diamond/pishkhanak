<?php

namespace App\Services\Finnotech;

use App\Services\Finnotech\SmsAuthorizationService;
use App\Exceptions\FinnotechException;
use Illuminate\Support\Facades\Log;

/**
 * Class CreditSmsService
 * 
 * Provides methods for Finnotech Credit services that require SMS authorization.
 * This service handles the SMS authentication flow automatically.
 */
class CreditSmsService
{
    private SmsAuthorizationService $smsAuthService;

    public function __construct(SmsAuthorizationService $smsAuthService)
    {
        $this->smsAuthService = $smsAuthService;
    }

    /**
     * Get facility inquiry with automatic SMS auth token management.
     *
     * @param string $nationalId National ID of the user
     * @param string $mobile Mobile number of the user
     * @param string|null $trackId Optional tracking ID
     * @return array
     * @throws FinnotechException
     *
     * Sample output:
     * [
     *     "responseCode" => "FN-CTKZ-20000100000",
     *     "trackId" => "95d682a0-9c48-4927-moho-ecc70e134272",
     *     "result" => [
     *         "user" => "0012345678",
     *         "legalId" => null,
     *         "name" => "حسین رحمتی شادان",
     *         "facilityTotalAmount" => "324921522",
     *         "facilityDebtTotalAmount" => "101017180",
     *         "facilityPastExpiredTotalAmount" => "0",
     *         "facilityDeferredTotalAmount" => "0",
     *         "facilitySuspiciousTotalAmount" => "0",
     *         "dishonored" => "",
     *         "facilityList" => [...],
     *     ],
     *     "status" => "DONE",
     * ]
     */
    public function getFacilityInquiry(string $nationalId, string $mobile, ?string $trackId = null): array
    {
        $scope = 'credit:sms-facility-inquiry:get';
        
        try {
            $queryParams = [];
            if ($trackId) {
                $queryParams['trackId'] = $trackId;
            }

            return $this->smsAuthService->makeAuthorizedApiCall(
                $scope,
                $nationalId,
                $mobile,
                [], // No POST params for GET request
                $queryParams
            );
        } catch (FinnotechException $e) {
            Log::error('Credit facility inquiry failed', [
                'national_id' => $nationalId,
                'mobile' => $mobile,
                'track_id' => $trackId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get back cheques inquiry with automatic SMS auth token management.
     *
     * @param string $nationalId National ID of the user
     * @param string $mobile Mobile number of the user
     * @param string|null $trackId Optional tracking ID
     * @return array
     * @throws FinnotechException
     */
    public function getBackCheques(string $nationalId, string $mobile, ?string $trackId = null): array
    {
        $scope = 'credit:sms-back-cheques:get';
        
        try {
            $queryParams = [];
            if ($trackId) {
                $queryParams['trackId'] = $trackId;
            }

            return $this->smsAuthService->makeAuthorizedApiCall(
                $scope,
                $nationalId,
                $mobile,
                [], // No POST params for GET request
                $queryParams
            );
        } catch (FinnotechException $e) {
            Log::error('Back cheques inquiry failed', [
                'national_id' => $nationalId,
                'mobile' => $mobile,
                'track_id' => $trackId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Sayad cheque inquiry with automatic SMS auth token management.
     *
     * @param string $nationalId National ID of the user
     * @param string $mobile Mobile number of the user
     * @param string $sayadId Sayad cheque ID
     * @param string $idCode ID code for verification
     * @param string|null $shahabId Optional Shahab ID
     * @param string|null $idType Optional ID type
     * @param string|null $trackId Optional tracking ID
     * @return array
     * @throws FinnotechException
     */
    public function getSayadChequeInquiry(
        string $nationalId,
        string $mobile,
        string $sayadId,
        string $idCode,
        ?string $shahabId = null,
        ?string $idType = null,
        ?string $trackId = null
    ): array {
        $scope = 'credit:sms-sayady-cheque-inquiry:get';
        
        try {
            $queryParams = [
                'sayadId' => $sayadId,
                'idCode' => $idCode
            ];

            if ($shahabId) {
                $queryParams['shahabId'] = $shahabId;
            }

            if ($idType) {
                $queryParams['idType'] = $idType;
            }

            if ($trackId) {
                $queryParams['trackId'] = $trackId;
            }

            return $this->smsAuthService->makeAuthorizedApiCall(
                $scope,
                $nationalId,
                $mobile,
                [], // No POST params for GET request
                $queryParams
            );
        } catch (FinnotechException $e) {
            Log::error('Sayad cheque inquiry failed', [
                'national_id' => $nationalId,
                'mobile' => $mobile,
                'sayad_id' => $sayadId,
                'id_code' => $idCode,
                'track_id' => $trackId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Sayad issuer cheque inquiry with automatic SMS auth token management.
     *
     * @param string $nationalId National ID of the user
     * @param string $mobile Mobile number of the user
     * @param array $chequeData Cheque data for inquiry
     * @param string|null $trackId Optional tracking ID
     * @return array
     * @throws FinnotechException
     */
    public function getSayadIssuerChequeInquiry(
        string $nationalId,
        string $mobile,
        array $chequeData,
        ?string $trackId = null
    ): array {
        $scope = 'credit:sms-sayad-issuer-inquiry-cheque:post';
        
        try {
            $queryParams = [];
            if ($trackId) {
                $queryParams['trackId'] = $trackId;
            }

            return $this->smsAuthService->makeAuthorizedApiCall(
                $scope,
                $nationalId,
                $mobile,
                $chequeData,
                $queryParams
            );
        } catch (FinnotechException $e) {
            Log::error('Sayad issuer cheque inquiry failed', [
                'national_id' => $nationalId,
                'mobile' => $mobile,
                'cheque_data' => $chequeData,
                'track_id' => $trackId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Accept Sayad cheque with automatic SMS auth token management.
     *
     * @param string $nationalId National ID of the user
     * @param string $mobile Mobile number of the user
     * @param array $chequeData Cheque data for acceptance
     * @param string|null $trackId Optional tracking ID
     * @return array
     * @throws FinnotechException
     */
    public function acceptSayadCheque(
        string $nationalId,
        string $mobile,
        array $chequeData,
        ?string $trackId = null
    ): array {
        $scope = 'credit:sms-sayad-accept-cheque:post';
        
        try {
            $queryParams = [];
            if ($trackId) {
                $queryParams['trackId'] = $trackId;
            }

            return $this->smsAuthService->makeAuthorizedApiCall(
                $scope,
                $nationalId,
                $mobile,
                $chequeData,
                $queryParams
            );
        } catch (FinnotechException $e) {
            Log::error('Sayad cheque acceptance failed', [
                'national_id' => $nationalId,
                'mobile' => $mobile,
                'cheque_data' => $chequeData,
                'track_id' => $trackId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Cancel Sayad cheque with automatic SMS auth token management.
     *
     * @param string $nationalId National ID of the user
     * @param string $mobile Mobile number of the user
     * @param array $chequeData Cheque data for cancellation
     * @param string|null $trackId Optional tracking ID
     * @return array
     * @throws FinnotechException
     */
    public function cancelSayadCheque(
        string $nationalId,
        string $mobile,
        array $chequeData,
        ?string $trackId = null
    ): array {
        $scope = 'credit:sms-sayad-cancel-cheque:post';
        
        try {
            $queryParams = [];
            if ($trackId) {
                $queryParams['trackId'] = $trackId;
            }

            return $this->smsAuthService->makeAuthorizedApiCall(
                $scope,
                $nationalId,
                $mobile,
                $chequeData,
                $queryParams
            );
        } catch (FinnotechException $e) {
            Log::error('Sayad cheque cancellation failed', [
                'national_id' => $nationalId,
                'mobile' => $mobile,
                'cheque_data' => $chequeData,
                'track_id' => $trackId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Check if user has valid tokens for all credit SMS scopes.
     *
     * @param string $nationalId National ID of the user
     * @param string $mobile Mobile number of the user
     * @return array
     */
    public function checkTokenStatus(string $nationalId, string $mobile): array
    {
        $scopes = [
            'credit:sms-facility-inquiry:get',
            'credit:sms-back-cheques:get',
            'credit:sms-sayady-cheque-inquiry:get',
            'credit:sms-sayad-issuer-inquiry-cheque:post',
            'credit:sms-sayad-accept-cheque:post',
            'credit:sms-sayad-cancel-cheque:post'
        ];

        $tokenStatus = [];

        foreach ($scopes as $scope) {
            try {
                $tokenData = $this->smsAuthService->getToken($scope, $nationalId, $mobile);
                $tokenStatus[$scope] = [
                    'has_token' => !is_null($tokenData),
                    'expires_at' => $tokenData['expires_at'] ?? null,
                    'created_at' => $tokenData['created_at'] ?? null
                ];
            } catch (\Exception $e) {
                $tokenStatus[$scope] = [
                    'has_token' => false,
                    'error' => $e->getMessage()
                ];
            }
        }

        return $tokenStatus;
    }

    /**
     * Generate authorization URLs for all credit SMS scopes.
     *
     * @param string $nationalId National ID of the user
     * @param string $mobile Mobile number of the user
     * @param string|null $state Optional state parameter
     * @return array
     */
    public function generateAuthorizationUrls(string $nationalId, string $mobile, ?string $state = null): array
    {
        $scopes = [
            'credit:sms-facility-inquiry:get' => 'Facility Inquiry',
            'credit:sms-back-cheques:get' => 'Back Cheques',
            'credit:sms-sayady-cheque-inquiry:get' => 'Sayady Cheque Inquiry',
            'credit:sms-sayad-issuer-inquiry-cheque:post' => 'Sayad Issuer Cheque Inquiry',
            'credit:sms-sayad-accept-cheque:post' => 'Sayad Accept Cheque',
            'credit:sms-sayad-cancel-cheque:post' => 'Sayad Cancel Cheque'
        ];

        $authUrls = [];

        foreach ($scopes as $scope => $name) {
            try {
                $authUrls[$scope] = [
                    'name' => $name,
                    'url' => $this->smsAuthService->generateAuthorizationUrl($scope, $mobile, $nationalId, $state),
                    'scope' => $scope
                ];
            } catch (FinnotechException $e) {
                $authUrls[$scope] = [
                    'name' => $name,
                    'error' => $e->getMessage(),
                    'scope' => $scope
                ];
            }
        }

        return $authUrls;
    }
} 