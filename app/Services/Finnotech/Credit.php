<?php

namespace App\Services\Finnotech;

use App\Services\Finnotech\FinnotechService;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Class Credit
 * 
 * Handles Finnotech Credit Services including cheques, loans, credit rating,
 * and other credit-related operations.
 */
class Credit
{
    /**
     * @var FinnotechService
     */
    protected $finnotechService;

    /**
     * Credit constructor.
     *
     * @param FinnotechService $finnotechService
     */
    public function __construct(FinnotechService $finnotechService)
    {
        $this->finnotechService = $finnotechService;
    }

    /**
     * Get cheque information for a national ID.
     *
     * @param string $nationalId
     * @param string $token
     * @return object|null
     */
    public function getCheque(string $nationalId, string $token): ?object
    {
        try {
            return $this->finnotechService->makeApiRequest(
                "/credit/" . $this->finnotechService->getApiVersion() . "/clients/" . $this->finnotechService->getClientId() . "/users/{$nationalId}/sms/backCheques",
                [],
                'GET',
                false,
                $token
            );
        } catch (Exception $e) {
            Log::error('Error getting cheque information: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get loan information for a national ID.
     *
     * @param string $nationalId
     * @param string $token
     * @return object|null
     */
    public function getLoan(string $nationalId, string $token): ?object
    {
        try {
            return $this->finnotechService->makeApiRequest(
                "/credit/" . $this->finnotechService->getApiVersion() . "/clients/" . $this->finnotechService->getClientId() . "/users/{$nationalId}/sms/facilityInquiry",
                [],
                'GET',
                false,
                $token
            );
        } catch (Exception $e) {
            Log::error('Error getting loan information: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Check rate status.
     *
     * @param string $hash
     * @return object|null
     */
    public function checkRate(string $hash): ?object
    {
        try {
            return $this->finnotechService->makeApiRequest(
                "/credit/" . $this->finnotechService->getApiVersion() . "/clients/" . $this->finnotechService->getClientId() . "/scoreReport/requestStatusCheck",
                [
                    'hashData' => $hash
                ],
                'POST'
            );
        } catch (Exception $e) {
            Log::error('Error checking rate status: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get rate information.
     *
     * @param string $hash
     * @param string $type
     * @return object|null
     */
    public function getRate(string $hash, string $type): ?object
    {
        try {
            return $this->finnotechService->makeApiRequest(
                "/credit/" . $this->finnotechService->getApiVersion() . "/clients/" . $this->finnotechService->getClientId() . "/scoreReport/getRequestData",
                [
                    'hashData' => $hash,
                    'outputType' => $type
                ],
                'POST'
            );
        } catch (Exception $e) {
            Log::error('Error getting rate information: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get Sayad information by ID.
     *
     * @param string $sayadId
     * @return object|null
     */
    public function getSayad(string $sayadId): ?object
    {
        try {
            return $this->finnotechService->makeApiRequest(
                "/credit/" . $this->finnotechService->getApiVersion() . "/clients/" . $this->finnotechService->getClientId() . "/sayadSerialInquiry",
                [
                    'sayadId' => $sayadId
                ]
            );
        } catch (Exception $e) {
            Log::error('Error getting Sayad information: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get Macna information by national ID.
     *
     * @param string $nationalId
     * @return object|null
     */
    public function getMacna(string $nationalId): ?object
    {
        try {
            return $this->finnotechService->makeApiRequest(
                "/credit/" . $this->finnotechService->getApiVersion() . "/clients/" . $this->finnotechService->getClientId() . "/macnaInquiry",
                [
                    'nationalCode' => $nationalId
                ]
            );
        } catch (Exception $e) {
            Log::error('Error getting Macna information: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Validate national ID format.
     *
     * @param string $nationalId
     * @return bool
     */
    public function validateNationalId(string $nationalId): bool
    {
        // Remove any non-digit characters
        $cleaned = preg_replace('/\D/', '', $nationalId);
        
        // Check length (10 digits for personal, 11 for legal entities)
        return in_array(strlen($cleaned), [10, 11]) && ctype_digit($cleaned);
    }

    /**
     * Get comprehensive credit report.
     *
     * @param string $nationalId
     * @param string $token
     * @return array
     */
    public function getCreditReport(string $nationalId, string $token): array
    {
        $report = [
            'nationalId' => $nationalId,
            'cheques' => null,
            'loans' => null,
            'macna' => null,
            'errors' => []
        ];

        if (!$this->validateNationalId($nationalId)) {
            $report['errors'][] = 'Invalid national ID format';
            return $report;
        }

        // Get cheque information
        try {
            $report['cheques'] = $this->getCheque($nationalId, $token);
        } catch (Exception $e) {
            $report['errors'][] = 'Error getting cheques: ' . $e->getMessage();
        }

        // Get loan information
        try {
            $report['loans'] = $this->getLoan($nationalId, $token);
        } catch (Exception $e) {
            $report['errors'][] = 'Error getting loans: ' . $e->getMessage();
        }

        // Get Macna information (doesn't require token)
        try {
            $report['macna'] = $this->getMacna($nationalId);
        } catch (Exception $e) {
            $report['errors'][] = 'Error getting Macna: ' . $e->getMessage();
        }

        return $report;
    }

    /**
     * Check multiple Sayad IDs in batch.
     *
     * @param array $sayadIds
     * @return array
     */
    public function getBatchSayadDetails(array $sayadIds): array
    {
        $results = [];
        
        foreach ($sayadIds as $sayadId) {
            $result = $this->getSayad($sayadId);
            $results[] = [
                'sayadId' => $sayadId,
                'success' => $result !== null,
                'data' => $result
            ];
        }

        return $results;
    }

    /**
     * Format national ID for display.
     *
     * @param string $nationalId
     * @return string
     */
    public function formatNationalId(string $nationalId): string
    {
        $cleaned = preg_replace('/\D/', '', $nationalId);
        
        if (strlen($cleaned) === 10) {
            return substr($cleaned, 0, 3) . '-' . substr($cleaned, 3, 6) . '-' . substr($cleaned, -1);
        }
        
        return $nationalId;
    }

    /**
     * Validate Sayad ID format.
     *
     * @param string $sayadId
     * @return bool
     */
    public function validateSayadId(string $sayadId): bool
    {
        // Sayad IDs are typically numeric strings
        return !empty($sayadId) && ctype_digit($sayadId);
    }
} 