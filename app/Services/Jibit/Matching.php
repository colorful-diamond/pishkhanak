<?php

namespace App\Services\Jibit;

use App\Services\Jibit\JibitService;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Class Matching
 * 
 * Handles Jibit Matching & Verification Services including card matching with national code & birth date,
 * IBAN matching with national code & birth date, and national code matching with mobile number (Shahkar).
 */
class Matching
{
    /**
     * @var JibitService
     */
    protected $jibitService;

    /**
     * Matching constructor.
     *
     * @param JibitService $jibitService
     */
    public function __construct(JibitService $jibitService)
    {
        $this->jibitService = $jibitService;
    }

    /**
     * Verify that a card number, national code, and birth date belong to the same person.
     *
     * @param string $cardNumber The 16-digit card number
     * @param string $nationalCode The 10-digit national code
     * @param string $birthDate Birth date in YYYYMMDD format
     * @return object|null
     */
    public function matchCardWithNationalCodeAndBirthDate(string $cardNumber, string $nationalCode, string $birthDate): ?object
    {
        try {
            $params = [
                'cardNumber' => $cardNumber,
                'nationalCode' => $nationalCode,
                'birthDate' => $birthDate
            ];
            return $this->jibitService->makeApiRequest('/v1/services/matching', $params);
        } catch (Exception $e) {
            Log::error('Error in card matching with national code and birth date: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Verify that an IBAN, national code, and birth date belong to the same person.
     *
     * @param string $iban The IBAN
     * @param string $nationalCode The 10-digit national code
     * @param string $birthDate Birth date in YYYYMMDD format
     * @return object|null
     */
    public function matchIbanWithNationalCodeAndBirthDate(string $iban, string $nationalCode, string $birthDate): ?object
    {
        try {
            $params = [
                'iban' => $iban,
                'nationalCode' => $nationalCode,
                'birthDate' => $birthDate
            ];
            return $this->jibitService->makeApiRequest('/v1/services/matching', $params);
        } catch (Exception $e) {
            Log::error('Error in IBAN matching with national code and birth date: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Verify the ownership match between a SIM card and a national code (Shahkar service).
     *
     * @param string $nationalCode The 10-digit national code
     * @param string $mobileNumber The mobile number
     * @return object|null
     */
    public function matchNationalCodeWithMobileNumber(string $nationalCode, string $mobileNumber): ?object
    {
        try {
            $params = [
                'nationalCode' => $nationalCode,
                'mobileNumber' => $mobileNumber
            ];
            return $this->jibitService->makeApiRequest('/v1/services/matching', $params);
        } catch (Exception $e) {
            Log::error('Error in national code matching with mobile number: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Perform comprehensive verification with card, national code, birth date, and mobile number.
     *
     * @param string $cardNumber The 16-digit card number
     * @param string $nationalCode The 10-digit national code
     * @param string $birthDate Birth date in YYYYMMDD format
     * @param string $mobileNumber The mobile number
     * @return array
     */
    public function comprehensiveVerification(string $cardNumber, string $nationalCode, string $birthDate, string $mobileNumber): array
    {
        $result = [
            'card_match' => null,
            'mobile_match' => null,
            'all_verified' => false,
            'errors' => []
        ];

        try {
            // Check card with national code and birth date
            $cardMatch = $this->matchCardWithNationalCodeAndBirthDate($cardNumber, $nationalCode, $birthDate);
            if ($cardMatch) {
                $result['card_match'] = $cardMatch;
            } else {
                $result['errors'][] = 'Failed to verify card with national code and birth date';
            }

            // Check national code with mobile number
            $mobileMatch = $this->matchNationalCodeWithMobileNumber($nationalCode, $mobileNumber);
            if ($mobileMatch) {
                $result['mobile_match'] = $mobileMatch;
            } else {
                $result['errors'][] = 'Failed to verify national code with mobile number';
            }

            // Check if all verifications passed
            $result['all_verified'] = 
                ($cardMatch && isset($cardMatch->matched) && $cardMatch->matched) &&
                ($mobileMatch && isset($mobileMatch->matched) && $mobileMatch->matched);

        } catch (Exception $e) {
            $result['errors'][] = 'Error in comprehensive verification: ' . $e->getMessage();
            Log::error('Error in comprehensiveVerification: ' . $e->getMessage());
        }

        return $result;
    }

    /**
     * Perform comprehensive IBAN verification with national code, birth date, and mobile number.
     *
     * @param string $iban The IBAN
     * @param string $nationalCode The 10-digit national code
     * @param string $birthDate Birth date in YYYYMMDD format
     * @param string $mobileNumber The mobile number
     * @return array
     */
    public function comprehensiveIbanVerification(string $iban, string $nationalCode, string $birthDate, string $mobileNumber): array
    {
        $result = [
            'iban_match' => null,
            'mobile_match' => null,
            'all_verified' => false,
            'errors' => []
        ];

        try {
            // Check IBAN with national code and birth date
            $ibanMatch = $this->matchIbanWithNationalCodeAndBirthDate($iban, $nationalCode, $birthDate);
            if ($ibanMatch) {
                $result['iban_match'] = $ibanMatch;
            } else {
                $result['errors'][] = 'Failed to verify IBAN with national code and birth date';
            }

            // Check national code with mobile number
            $mobileMatch = $this->matchNationalCodeWithMobileNumber($nationalCode, $mobileNumber);
            if ($mobileMatch) {
                $result['mobile_match'] = $mobileMatch;
            } else {
                $result['errors'][] = 'Failed to verify national code with mobile number';
            }

            // Check if all verifications passed
            $result['all_verified'] = 
                ($ibanMatch && isset($ibanMatch->matched) && $ibanMatch->matched) &&
                ($mobileMatch && isset($mobileMatch->matched) && $mobileMatch->matched);

        } catch (Exception $e) {
            $result['errors'][] = 'Error in comprehensive IBAN verification: ' . $e->getMessage();
            Log::error('Error in comprehensiveIbanVerification: ' . $e->getMessage());
        }

        return $result;
    }
} 