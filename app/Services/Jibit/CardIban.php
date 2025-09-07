<?php

namespace App\Services\Jibit;

use App\Services\Jibit\JibitService;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Class CardIban
 * 
 * Handles Jibit Card & IBAN Services including card inquiry, IBAN inquiry,
 * card to account conversion, card to IBAN conversion, and account to IBAN conversion.
 */
class CardIban
{
    /**
     * @var JibitService
     */
    protected $jibitService;

    /**
     * CardIban constructor.
     *
     * @param JibitService $jibitService
     */
    public function __construct(JibitService $jibitService)
    {
        $this->jibitService = $jibitService;
    }

    /**
     * Retrieve information about a bank card.
     *
     * @param string $cardNumber The 16-digit card number
     * @return object|null
     */
    public function getCardInquiry(string $cardNumber): ?object
    {
        try {
            $params = ['number' => $cardNumber];
            return $this->jibitService->makeApiRequest('/v1/cards', $params);
        } catch (Exception $e) {
            Log::error('Error in card inquiry: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Retrieve information for a given IBAN.
     *
     * @param string $iban The IBAN to be checked
     * @return object|null
     */
    public function getIbanInquiry(string $iban): ?object
    {
        try {
            $params = ['value' => $iban];
            return $this->jibitService->makeApiRequest('/v1/ibans', $params);
        } catch (Exception $e) {
            Log::error('Error in IBAN inquiry: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Convert a card number to its corresponding bank account number.
     *
     * @param string $cardNumber The 16-digit card number
     * @return object|null
     */
    public function cardToAccount(string $cardNumber): ?object
    {
        try {
            $params = [
                'number' => $cardNumber,
                'deposit' => true
            ];
            return $this->jibitService->makeApiRequest('/v1/cards', $params);
        } catch (Exception $e) {
            Log::error('Error in card to account conversion: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Convert a debit card number to its corresponding IBAN.
     *
     * @param string $cardNumber The 16-digit card number
     * @return object|null
     */
    public function cardToIban(string $cardNumber): ?object
    {
        try {
            $params = [
                'number' => $cardNumber,
                'iban' => true
            ];
            return $this->jibitService->makeApiRequest('/v1/cards', $params);
        } catch (Exception $e) {
            Log::error('Error in card to IBAN conversion: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Convert a bank account number to an IBAN.
     *
     * @param string $bankId The bank identifier (e.g., MELLI)
     * @param string $accountNumber The valid account number
     * @return object|null
     */
    public function accountToIban(string $bankId, string $accountNumber): ?object
    {
        try {
            $params = [
                'bank' => $bankId,
                'number' => $accountNumber,
                'iban' => true
            ];
            return $this->jibitService->makeApiRequest('/v1/deposits', $params);
        } catch (Exception $e) {
            Log::error('Error in account to IBAN conversion: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Check if the Card-to-IBAN service is available for different banks.
     *
     * @return object|null
     */
    public function checkCardToIbanAvailability(): ?object
    {
        try {
            $params = ['cardToIBAN' => true];
            return $this->jibitService->makeApiRequest('/v1/services/availability', $params);
        } catch (Exception $e) {
            Log::error('Error checking card to IBAN availability: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get comprehensive card information including all conversions.
     *
     * @param string $cardNumber The 16-digit card number
     * @return array
     */
    public function getCompleteCardInfo(string $cardNumber): array
    {
        $result = [
            'card_info' => null,
            'account_number' => null,
            'iban' => null,
            'errors' => []
        ];

        try {
            // Get basic card information
            $cardInfo = $this->getCardInquiry($cardNumber);
            if ($cardInfo) {
                $result['card_info'] = $cardInfo;
            } else {
                $result['errors'][] = 'Failed to get card information';
            }

            // Get account number
            $accountInfo = $this->cardToAccount($cardNumber);
            if ($accountInfo) {
                $result['account_number'] = $accountInfo;
            } else {
                $result['errors'][] = 'Failed to convert card to account';
            }

            // Get IBAN
            $ibanInfo = $this->cardToIban($cardNumber);
            if ($ibanInfo) {
                $result['iban'] = $ibanInfo;
            } else {
                $result['errors'][] = 'Failed to convert card to IBAN';
            }
        } catch (Exception $e) {
            $result['errors'][] = 'Error getting complete card info: ' . $e->getMessage();
            Log::error('Error in getCompleteCardInfo: ' . $e->getMessage());
        }

        return $result;
    }
} 