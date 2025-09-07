<?php

namespace App\Services\Finnotech;

use App\Services\Finnotech\SmsAuthorizationService;
use App\Exceptions\FinnotechException;
use Illuminate\Support\Facades\Log;

/**
 * Class OakSmsService
 * 
 * Provides methods for Finnotech Oak services that require SMS authorization.
 * This service handles the SMS authentication flow automatically.
 */
class OakSmsService
{
    private SmsAuthorizationService $smsAuthService;

    public function __construct(SmsAuthorizationService $smsAuthService)
    {
        $this->smsAuthService = $smsAuthService;
    }

    /**
     * Get Shahab inquiry with automatic SMS auth token management.
     *
     * @param string $nationalId National ID of the user
     * @param string $mobile Mobile number of the user
     * @param string $birthDate Birth date in YYYYMMDD format
     * @param string|null $identityNo Identity number for pre-1968 births
     * @param string|null $trackId Optional tracking ID
     * @return array
     * @throws FinnotechException
     *
     * Sample output:
     * [
     *     "result" => [
     *         "shahabCode" => "12345678901234567890",
     *         "nid" => "0012345678",
     *         "birthDate" => "13451215",
     *         "status" => "ACTIVE"
     *     ],
     *     "status" => "DONE",
     *     "trackId" => "shahab-inquiry-123"
     * ]
     */
    public function getShahabInquiry(
        string $nationalId,
        string $mobile,
        string $birthDate,
        ?string $identityNo = null,
        ?string $trackId = null
    ): array {
        $scope = 'oak:sms-shahab-inquiry:get';
        
        try {
            $queryParams = [
                'birthDate' => $birthDate
            ];

            if ($identityNo) {
                $queryParams['identityNo'] = $identityNo;
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
            Log::error('Oak Shahab inquiry failed', [
                'national_id' => $nationalId,
                'mobile' => $mobile,
                'birth_date' => $birthDate,
                'identity_no' => $identityNo,
                'track_id' => $trackId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Check if user has valid token for Oak SMS scope.
     *
     * @param string $nationalId National ID of the user
     * @param string $mobile Mobile number of the user
     * @return array
     */
    public function checkTokenStatus(string $nationalId, string $mobile): array
    {
        try {
            $scope = 'oak:sms-shahab-inquiry:get';
            $tokenData = $this->smsAuthService->getToken($scope, $nationalId, $mobile);
            
            return [
                'has_token' => !is_null($tokenData),
                'scope' => $scope,
                'expires_at' => $tokenData['expires_at'] ?? null,
                'created_at' => $tokenData['created_at'] ?? null
            ];
        } catch (\Exception $e) {
            return [
                'has_token' => false,
                'scope' => 'oak:sms-shahab-inquiry:get',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Generate authorization URL for Oak SMS scope.
     *
     * @param string $nationalId National ID of the user
     * @param string $mobile Mobile number of the user
     * @param string|null $state Optional state parameter
     * @return string
     * @throws FinnotechException
     */
    public function generateAuthorizationUrl(string $nationalId, string $mobile, ?string $state = null): string
    {
        return $this->smsAuthService->generateAuthorizationUrl(
            'oak:sms-shahab-inquiry:get',
            $mobile,
            $nationalId,
            $state
        );
    }
} 