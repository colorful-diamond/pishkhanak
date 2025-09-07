<?php

namespace App\Services\Finnotech;

use App\Services\Finnotech\FinnotechService;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Class Inquiry
 * 
 * Handles Finnotech Inquiry Services including postal code lookup,
 * IBAN validation, card verification, and other inquiry operations.
 */
class Inquiry
{
    /**
     * @var FinnotechService
     */
    protected $finnotechService;

    /**
     * Inquiry constructor.
     *
     * @param FinnotechService $finnotechService
     */
    public function __construct(FinnotechService $finnotechService)
    {
        $this->finnotechService = $finnotechService;
    }

    /**
     * Get postal code details.
     *
     * @param string $postalCode
     * @return object|null
     */
    public function getPostalCode(string $postalCode): ?object
    {
        try {
            return $this->finnotechService->makeApiRequest(
                "/ecity/" . $this->finnotechService->getApiVersion() . "/clients/" . $this->finnotechService->getClientId() . "/postalCode",
                ['postalCode' => $postalCode]
            );
        } catch (Exception $e) {
            Log::error('Error getting postal code details: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get IBAN details by IBAN number.
     *
     * @param string $iban
     * @return object|null
     */
    public function getIbanDetails(string $iban): ?object
    {
        try {
            return $this->finnotechService->makeApiRequest(
                "/oak/" . $this->finnotechService->getApiVersion() . "/clients/" . $this->finnotechService->getClientId() . "/ibanInquiry",
                ['iban' => $iban]
            );
        } catch (Exception $e) {
            Log::error('Error getting IBAN details: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Check card details.
     *
     * @param string $card
     * @return object|null
     */
    public function checkCard(string $card): ?object
    {
        try {
            return $this->finnotechService->makeApiRequest(
                "/mpg/" . $this->finnotechService->getApiVersion() . "/clients/" . $this->finnotechService->getClientId() . "/cards/" . $card
            );
        } catch (Exception $e) {
            Log::error('Error checking card: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get military service status for a national ID.
     *
     * @param string $nationalId
     * @return object|null
     */
    public function getMilitary(string $nationalId): ?object
    {
        try {
            return $this->finnotechService->makeApiRequest(
                "/kyc/" . $this->finnotechService->getApiVersion() . "/clients/" . $this->finnotechService->getClientId() . "/militaryInquiry",
                [
                    'nationalCode' => $nationalId,
                    'trackId' => $this->finnotechService->generateTrackId()
                ]
            );
        } catch (Exception $e) {
            Log::error('Error getting military service status: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Validate postal code format.
     *
     * @param string $postalCode
     * @return bool
     */
    public function validatePostalCode(string $postalCode): bool
    {
        // Iranian postal codes are 10 digits
        $cleaned = preg_replace('/\D/', '', $postalCode);
        return strlen($cleaned) === 10 && ctype_digit($cleaned);
    }

    /**
     * Validate IBAN format.
     *
     * @param string $iban
     * @return bool
     */
    public function validateIban(string $iban): bool
    {
        // Remove spaces and convert to uppercase
        $cleaned = strtoupper(preg_replace('/\s/', '', $iban));
        
        // Iranian IBANs start with IR and have 24 characters
        return preg_match('/^IR\d{22}$/', $cleaned);
    }

    /**
     * Format postal code for display.
     *
     * @param string $postalCode
     * @return string
     */
    public function formatPostalCode(string $postalCode): string
    {
        $cleaned = preg_replace('/\D/', '', $postalCode);
        
        if (strlen($cleaned) === 10) {
            return substr($cleaned, 0, 5) . '-' . substr($cleaned, 5);
        }
        
        return $postalCode;
    }

    /**
     * Format IBAN for display.
     *
     * @param string $iban
     * @return string
     */
    public function formatIban(string $iban): string
    {
        $cleaned = strtoupper(preg_replace('/\s/', '', $iban));
        
        if (strlen($cleaned) === 24 && substr($cleaned, 0, 2) === 'IR') {
            return substr($cleaned, 0, 4) . ' ' . 
                   substr($cleaned, 4, 4) . ' ' . 
                   substr($cleaned, 8, 4) . ' ' . 
                   substr($cleaned, 12, 4) . ' ' . 
                   substr($cleaned, 16, 4) . ' ' . 
                   substr($cleaned, 20, 4);
        }
        
        return $iban;
    }

    /**
     * Get multiple postal code details in batch.
     *
     * @param array $postalCodes
     * @return array
     */
    public function getBatchPostalCodeDetails(array $postalCodes): array
    {
        $results = [];
        
        foreach ($postalCodes as $postalCode) {
            if (!$this->validatePostalCode($postalCode)) {
                $results[] = [
                    'postalCode' => $postalCode,
                    'success' => false,
                    'error' => 'Invalid postal code format'
                ];
                continue;
            }

            $result = $this->getPostalCode($postalCode);
            $results[] = [
                'postalCode' => $postalCode,
                'success' => $result !== null,
                'data' => $result
            ];
        }

        return $results;
    }

    /**
     * Get multiple IBAN details in batch.
     *
     * @param array $ibans
     * @return array
     */
    public function getBatchIbanDetails(array $ibans): array
    {
        $results = [];
        
        foreach ($ibans as $iban) {
            if (!$this->validateIban($iban)) {
                $results[] = [
                    'iban' => $iban,
                    'success' => false,
                    'error' => 'Invalid IBAN format'
                ];
                continue;
            }

            $result = $this->getIbanDetails($iban);
            $results[] = [
                'iban' => $iban,
                'success' => $result !== null,
                'data' => $result
            ];
        }

        return $results;
    }

    /**
     * Comprehensive inquiry for national ID.
     *
     * @param string $nationalId
     * @return array
     */
    public function getComprehensiveInquiry(string $nationalId): array
    {
        $result = [
            'nationalId' => $nationalId,
            'military' => null,
            'errors' => []
        ];

        // Validate national ID
        if (!$this->validateNationalId($nationalId)) {
            $result['errors'][] = 'Invalid national ID format';
            return $result;
        }

        // Get military service status
        try {
            $result['military'] = $this->getMilitary($nationalId);
        } catch (Exception $e) {
            $result['errors'][] = 'Error getting military status: ' . $e->getMessage();
        }

        return $result;
    }

    /**
     * Validate national ID format.
     *
     * @param string $nationalId
     * @return bool
     */
    public function validateNationalId(string $nationalId): bool
    {
        $cleaned = preg_replace('/\D/', '', $nationalId);
        return strlen($cleaned) === 10 && ctype_digit($cleaned);
    }

    /**
     * Clean and normalize input data.
     *
     * @param string $input
     * @param string $type
     * @return string
     */
    public function normalizeInput(string $input, string $type): string
    {
        switch ($type) {
            case 'postal_code':
            case 'national_id':
                return preg_replace('/\D/', '', $input);
            case 'iban':
                return strtoupper(preg_replace('/\s/', '', $input));
            case 'card':
                return preg_replace('/[\s-]/', '', $input);
            default:
                return trim($input);
        }
    }
} 