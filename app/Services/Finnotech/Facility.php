<?php

namespace App\Services\Finnotech;

use App\Services\Finnotech\FinnotechService;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Class Facility
 * 
 * Handles Finnotech Banking and Financial Services including card to IBAN,
 * deposit to IBAN, and other banking operations.
 */
class Facility
{
    /**
     * @var FinnotechService
     */
    protected $finnotechService;

    /**
     * Facility constructor.
     *
     * @param FinnotechService $finnotechService
     */
    public function __construct(FinnotechService $finnotechService)
    {
        $this->finnotechService = $finnotechService;
    }

    /**
     * Get the card details by card number.
     *
     * @param string $card
     * @return object|null
     */
    public function getCard(string $card): ?object
    {
        try {
            return $this->finnotechService->makeApiRequest(
                "/facility/" . $this->finnotechService->getApiVersion() . "/clients/" . $this->finnotechService->getClientId() . "/cardToIban",
                ['card' => $card]
            );
        } catch (Exception $e) {
            Log::error('Error getting card details: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get card details by card number (alternative endpoint).
     *
     * @param string $card
     * @return object|null
     */
    public function getCardToDeposit(string $card): ?object
    {
        try {
            return $this->finnotechService->makeApiRequest(
                "/facility/" . $this->finnotechService->getApiVersion() . "/clients/" . $this->finnotechService->getClientId() . "/cardToDeposit",
                ['card' => $card]
            );
        } catch (Exception $e) {
            Log::error('Error getting card to deposit details: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get account details by account number and bank code.
     *
     * @param string $account
     * @param string $bankCode
     * @return object|null
     */
    public function getDepositToIban(string $account, string $bankCode): ?object
    {
        try {
            return $this->finnotechService->makeApiRequest(
                "/facility/" . $this->finnotechService->getApiVersion() . "/clients/" . $this->finnotechService->getClientId() . "/depositToIban",
                [
                    'deposit' => $account,
                    'bankCode' => $bankCode
                ]
            );
        } catch (Exception $e) {
            Log::error('Error getting deposit to IBAN: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Validate card number format.
     *
     * @param string $cardNumber
     * @return bool
     */
    public function validateCardNumber(string $cardNumber): bool
    {
        // Remove any spaces or dashes
        $cleaned = preg_replace('/[\s-]/', '', $cardNumber);
        
        // Check if it's 16 digits
        return preg_match('/^\d{16}$/', $cleaned);
    }

    /**
     * Validate bank code format.
     *
     * @param string $bankCode
     * @return bool
     */
    public function validateBankCode(string $bankCode): bool
    {
        // Bank codes are typically 3 digits
        return preg_match('/^\d{3}$/', $bankCode);
    }

    /**
     * Format card number for display (mask middle digits).
     *
     * @param string $cardNumber
     * @return string
     */
    public function formatCardNumber(string $cardNumber): string
    {
        $cleaned = preg_replace('/[\s-]/', '', $cardNumber);
        
        if (strlen($cleaned) === 16) {
            return substr($cleaned, 0, 4) . '-****-****-' . substr($cleaned, -4);
        }
        
        return $cardNumber;
    }

    /**
     * Get multiple card details in batch.
     *
     * @param array $cardNumbers
     * @return array
     */
    public function getBatchCardDetails(array $cardNumbers): array
    {
        $results = [];
        
        foreach ($cardNumbers as $cardNumber) {
            if (!$this->validateCardNumber($cardNumber)) {
                $results[] = [
                    'cardNumber' => $cardNumber,
                    'success' => false,
                    'error' => 'Invalid card number format'
                ];
                continue;
            }

            $result = $this->getCard($cardNumber);
            $results[] = [
                'cardNumber' => $cardNumber,
                'success' => $result !== null,
                'data' => $result
            ];
        }

        return $results;
    }

    /**
     * Get deposit details with validation.
     *
     * @param string $account
     * @param string $bankCode
     * @return array
     */
    public function getValidatedDepositDetails(string $account, string $bankCode): array
    {
        $result = [
            'success' => false,
            'data' => null,
            'errors' => []
        ];

        // Validate inputs
        if (empty($account)) {
            $result['errors'][] = 'Account number is required';
        }

        if (!$this->validateBankCode($bankCode)) {
            $result['errors'][] = 'Invalid bank code format';
        }

        if (!empty($result['errors'])) {
            return $result;
        }

        try {
            $response = $this->getDepositToIban($account, $bankCode);
            if ($response) {
                $result['success'] = true;
                $result['data'] = $response;
            } else {
                $result['errors'][] = 'Failed to retrieve deposit details';
            }
        } catch (Exception $e) {
            $result['errors'][] = 'Error: ' . $e->getMessage();
        }

        return $result;
    }

    /**
     * Clean and format account number.
     *
     * @param string $accountNumber
     * @return string
     */
    public function formatAccountNumber(string $accountNumber): string
    {
        // Remove any non-digit characters
        return preg_replace('/\D/', '', $accountNumber);
    }
} 