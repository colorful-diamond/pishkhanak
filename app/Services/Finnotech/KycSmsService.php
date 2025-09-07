<?php

namespace App\Services\Finnotech;

use App\Services\Finnotech\SmsAuthorizationService;
use App\Exceptions\FinnotechException;
use Illuminate\Support\Facades\Log;

/**
 * Class KycSmsService
 * 
 * Provides methods for Finnotech KYC services that require SMS authorization.
 * This service handles the SMS authentication flow automatically.
 */
class KycSmsService
{
    private SmsAuthorizationService $smsAuthService;

    public function __construct(SmsAuthorizationService $smsAuthService)
    {
        $this->smsAuthService = $smsAuthService;
    }

    /**
     * Verify national ID with automatic SMS auth token management.
     *
     * @param string $nationalId National ID to verify
     * @param string $mobile Mobile number of the user
     * @param string $birthDate Birth date in YYYYMMDD format
     * @param string|null $gender Gender (M/F)
     * @param string|null $fullName Full name for verification
     * @param string|null $firstName First name for verification
     * @param string|null $lastName Last name for verification
     * @param string|null $fatherName Father's name for verification
     * @param string|null $trackId Optional tracking ID
     * @return array
     * @throws FinnotechException
     *
     * Sample output:
     * [
     *     "result" => [
     *         "nid" => "0012345678",
     *         "name" => "علی احمدی",
     *         "birthDate" => "13651215",
     *         "gender" => "M",
     *         "similarity" => 100,
     *         "isValid" => true
     *     ],
     *     "status" => "DONE",
     *     "trackId" => "kyc-verification-123"
     * ]
     */
    public function verifyNationalId(
        string $nationalId,
        string $mobile,
        string $birthDate,
        ?string $gender = null,
        ?string $fullName = null,
        ?string $firstName = null,
        ?string $lastName = null,
        ?string $fatherName = null,
        ?string $trackId = null
    ): array {
        $scope = 'kyc:sms-nid-verification:get';
        
        try {
            $queryParams = [
                'birthDate' => $birthDate
            ];

            if ($gender) {
                $queryParams['gender'] = $gender;
            }

            if ($fullName) {
                $queryParams['fullName'] = $fullName;
            }

            if ($firstName) {
                $queryParams['firstName'] = $firstName;
            }

            if ($lastName) {
                $queryParams['lastName'] = $lastName;
            }

            if ($fatherName) {
                $queryParams['fatherName'] = $fatherName;
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
            Log::error('KYC NID verification failed', [
                'national_id' => $nationalId,
                'mobile' => $mobile,
                'birth_date' => $birthDate,
                'track_id' => $trackId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Check if user has valid token for KYC SMS scope.
     *
     * @param string $nationalId National ID of the user
     * @param string $mobile Mobile number of the user
     * @return array
     */
    public function checkTokenStatus(string $nationalId, string $mobile): array
    {
        try {
            $scope = 'kyc:sms-nid-verification:get';
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
                'scope' => 'kyc:sms-nid-verification:get',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Generate authorization URL for KYC SMS scope.
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
            'kyc:sms-nid-verification:get',
            $mobile,
            $nationalId,
            $state
        );
    }
} 