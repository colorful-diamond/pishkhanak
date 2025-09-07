<?php

namespace App\Services\Jibit;

use App\Services\Jibit\JibitService;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Class Identity
 * 
 * Handles Jibit Identity & Information Services including postal code inquiry,
 * military service inquiry, and SANA code inquiry.
 */
class Identity
{
    /**
     * @var JibitService
     */
    protected $jibitService;

    /**
     * Identity constructor.
     *
     * @param JibitService $jibitService
     */
    public function __construct(JibitService $jibitService)
    {
        $this->jibitService = $jibitService;
    }

    /**
     * Retrieve the full address for a given postal code.
     *
     * @param string $postalCode The 10-digit postal code
     * @return object|null
     */
    public function getPostalCodeInquiry(string $postalCode): ?object
    {
        try {
            $params = ['code' => $postalCode];
            return $this->jibitService->makeApiRequest('/v1/services/postal', $params);
        } catch (Exception $e) {
            Log::error('Error in postal code inquiry: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Check an individual's military service status.
     * Returns true for females, individuals with exemptions or completion card,
     * and false for those who are absent without leave.
     *
     * @param string $nationalCode The 10-digit national code
     * @return object|null
     */
    public function getMilitaryServiceInquiry(string $nationalCode): ?object
    {
        try {
            $params = ['nationalCode' => $nationalCode];
            return $this->jibitService->makeApiRequest('/v1/services/social/msq', $params);
        } catch (Exception $e) {
            Log::error('Error in military service inquiry: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Check if a SANA code has been issued for an individual based on their national code and mobile number.
     * The service will fail if the national code and mobile number do not match.
     *
     * @param string $nationalCode The 10-digit national code
     * @param string $mobileNumber The mobile number
     * @return object|null
     */
    public function getSanaCodeInquiry(string $nationalCode, string $mobileNumber): ?object
    {
        try {
            $params = [
                'nationalCode' => $nationalCode,
                'mobileNumber' => $mobileNumber
            ];
            return $this->jibitService->makeApiRequest('/v1/services/social/sana', $params);
        } catch (Exception $e) {
            Log::error('Error in SANA code inquiry: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get comprehensive identity information for a person.
     *
     * @param string $nationalCode The 10-digit national code
     * @param string $mobileNumber The mobile number
     * @param string|null $postalCode Optional 10-digit postal code
     * @return array
     */
    public function getComprehensiveIdentityInfo(string $nationalCode, string $mobileNumber, ?string $postalCode = null): array
    {
        $result = [
            'military_service' => null,
            'sana_code' => null,
            'postal_code' => null,
            'errors' => []
        ];

        try {
            // Get military service status
            $militaryService = $this->getMilitaryServiceInquiry($nationalCode);
            if ($militaryService) {
                $result['military_service'] = $militaryService;
            } else {
                $result['errors'][] = 'Failed to get military service information';
            }

            // Get SANA code status
            $sanaCode = $this->getSanaCodeInquiry($nationalCode, $mobileNumber);
            if ($sanaCode) {
                $result['sana_code'] = $sanaCode;
            } else {
                $result['errors'][] = 'Failed to get SANA code information';
            }

            // Get postal code information if provided
            if ($postalCode) {
                $postalInfo = $this->getPostalCodeInquiry($postalCode);
                if ($postalInfo) {
                    $result['postal_code'] = $postalInfo;
                } else {
                    $result['errors'][] = 'Failed to get postal code information';
                }
            }

        } catch (Exception $e) {
            $result['errors'][] = 'Error getting comprehensive identity info: ' . $e->getMessage();
            Log::error('Error in getComprehensiveIdentityInfo: ' . $e->getMessage());
        }

        return $result;
    }

    /**
     * Validate postal code format (should be 10 digits).
     *
     * @param string $postalCode
     * @return bool
     */
    public function validatePostalCode(string $postalCode): bool
    {
        return preg_match('/^[0-9]{10}$/', $postalCode);
    }

    /**
     * Validate national code format (should be 10 digits).
     *
     * @param string $nationalCode
     * @return bool
     */
    public function validateNationalCode(string $nationalCode): bool
    {
        return preg_match('/^[0-9]{10}$/', $nationalCode);
    }

    /**
     * Validate mobile number format (Iranian mobile numbers).
     *
     * @param string $mobileNumber
     * @return bool
     */
    public function validateMobileNumber(string $mobileNumber): bool
    {
        // Iranian mobile numbers: starts with 09 and has 11 digits total
        return preg_match('/^09[0-9]{9}$/', $mobileNumber);
    }

    /**
     * Perform validation and then get identity information.
     *
     * @param string $nationalCode
     * @param string $mobileNumber
     * @param string|null $postalCode
     * @return array
     */
    public function getValidatedIdentityInfo(string $nationalCode, string $mobileNumber, ?string $postalCode = null): array
    {
        $result = [
            'validation_errors' => [],
            'identity_info' => null
        ];

        // Validate inputs
        if (!$this->validateNationalCode($nationalCode)) {
            $result['validation_errors'][] = 'Invalid national code format (should be 10 digits)';
        }

        if (!$this->validateMobileNumber($mobileNumber)) {
            $result['validation_errors'][] = 'Invalid mobile number format (should be 09xxxxxxxxx)';
        }

        if ($postalCode && !$this->validatePostalCode($postalCode)) {
            $result['validation_errors'][] = 'Invalid postal code format (should be 10 digits)';
        }

        // If validation passes, get identity information
        if (empty($result['validation_errors'])) {
            $result['identity_info'] = $this->getComprehensiveIdentityInfo($nationalCode, $mobileNumber, $postalCode);
        }

        return $result;
    }
} 