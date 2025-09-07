<?php

namespace App\Services\Finnotech;

use App\Services\Finnotech\FinnotechService;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Class CardIban
 * 
 * Handles Finnotech Card & IBAN Services including card to IBAN conversion,
 * IBAN inquiry, card inquiry, and account to IBAN conversion.
 */
class CardIban
{
    /**
     * @var FinnotechService
     */
    protected $finnotechService;

    /**
     * CardIban constructor.
     *
     * @param FinnotechService $finnotechService
     */
    public function __construct(FinnotechService $finnotechService)
    {
        $this->finnotechService = $finnotechService;
    }

    /**
     * Get card details by card number (card to IBAN).
     *
     * @param string $card
     * @return object|null
     */
    public function getCardToIban(string $card): ?object
    {
        try {
            $endpoint = "/facility/{$this->finnotechService->getApiVersion()}/clients/{$this->finnotechService->getClientId()}/cardToIban";
            $params = ['card' => $card];
            
            return $this->finnotechService->makeApiRequest($endpoint, $params);
        } catch (Exception $e) {
            Log::error('Error in card to IBAN conversion: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get IBAN details by IBAN number.
     *
     * @param string $iban
     * @return object|null
     */
    public function getIbanInquiry(string $iban): ?object
    {
        try {
            $endpoint = "/oak/{$this->finnotechService->getApiVersion()}/clients/{$this->finnotechService->getClientId()}/ibanInquiry";
            $params = ['iban' => $iban];
            
            return $this->finnotechService->makeApiRequest($endpoint, $params);
        } catch (Exception $e) {
            Log::error('Error in IBAN inquiry: ' . $e->getMessage());
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
            $endpoint = "/facility/{$this->finnotechService->getApiVersion()}/clients/{$this->finnotechService->getClientId()}/cardToDeposit";
            $params = ['card' => $card];
            
            return $this->finnotechService->makeApiRequest($endpoint, $params);
        } catch (Exception $e) {
            Log::error('Error in card to deposit conversion: ' . $e->getMessage());
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
            $endpoint = "/mpg/{$this->finnotechService->getApiVersion()}/clients/{$this->finnotechService->getClientId()}/cards/{$card}";
            
            return $this->finnotechService->makeApiRequest($endpoint);
        } catch (Exception $e) {
            Log::error('Error in card check: ' . $e->getMessage());
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
            $endpoint = "/facility/{$this->finnotechService->getApiVersion()}/clients/{$this->finnotechService->getClientId()}/depositToIban";
            $params = [
                'deposit' => $account,
                'bankCode' => $bankCode
            ];
            
            return $this->finnotechService->makeApiRequest($endpoint, $params);
        } catch (Exception $e) {
            Log::error('Error in deposit to IBAN conversion: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get complete card information including all conversions.
     *
     * @param string $cardNumber
     * @return array
     */
    public function getCompleteCardInfo(string $cardNumber): array
    {
        $result = [
            'card_number' => $cardNumber,
            'card_to_iban' => null,
            'card_to_deposit' => null,
            'card_check' => null,
            'error' => null
        ];

        try {
            // Get card to IBAN
            $cardToIban = $this->getCardToIban($cardNumber);
            if ($cardToIban) {
                $result['card_to_iban'] = $cardToIban;
            }

            // Get card to deposit
            $cardToDeposit = $this->getCardToDeposit($cardNumber);
            if ($cardToDeposit) {
                $result['card_to_deposit'] = $cardToDeposit;
            }

            // Check card
            $cardCheck = $this->checkCard($cardNumber);
            if ($cardCheck) {
                $result['card_check'] = $cardCheck;
            }

        } catch (Exception $e) {
            $result['error'] = $e->getMessage();
            Log::error('Error getting complete card info: ' . $e->getMessage());
        }

        return $result;
    }

    /**
     * Validate IBAN format.
     *
     * @param string $iban
     * @return bool
     */
    public function validateIbanFormat(string $iban): bool
    {
        // Basic IBAN format validation for Iran
        // Iranian IBAN format: IR + 2 check digits + 22 digits
        return preg_match('/^IR\d{24}$/', $iban);
    }

    /**
     * Validate card number format.
     *
     * @param string $cardNumber
     * @return bool
     */
    public function validateCardFormat(string $cardNumber): bool
    {
        // Basic card number format validation (16 digits)
        return preg_match('/^\d{16}$/', $cardNumber);
    }
} 