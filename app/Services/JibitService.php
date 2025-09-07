<?php

namespace App\Services;

use App\Services\Jibit\JibitService as BaseJibitService;
use App\Services\Jibit\Token;
use App\Services\Jibit\CardIban;
use App\Services\Jibit\Matching;
use App\Services\Jibit\Identity;
use App\Services\Jibit\Account;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Class JibitService
 * 
 * Main service class that provides convenient access to all Jibit API services.
 * This class serves as a facade for all individual Jibit service classes.
 */
class JibitService
{
    /**
     * @var BaseJibitService
     */
    private $baseService;

    /**
     * @var Token
     */
    private $tokenService;

    /**
     * @var CardIban
     */
    private $cardIbanService;

    /**
     * @var Matching
     */
    private $matchingService;

    /**
     * @var Identity
     */
    private $identityService;

    /**
     * @var Account
     */
    private $accountService;

    /**
     * JibitService constructor.
     */
    public function __construct()
    {
        $this->baseService = new BaseJibitService();
        $this->tokenService = new Token($this->baseService);
        $this->cardIbanService = new CardIban($this->baseService);
        $this->matchingService = new Matching($this->baseService);
        $this->identityService = new Identity($this->baseService);
        $this->accountService = new Account($this->baseService);
    }

    // ===== TOKEN MANAGEMENT =====

    /**
     * Generate a new access token.
     *
     * @return object|null
     */
    public function generateToken(): ?object
    {
        return $this->tokenService->generate();
    }

    /**
     * Refresh the access token.
     *
     * @return object|null
     */
    public function refreshToken(): ?object
    {
        return $this->tokenService->refresh();
    }

    /**
     * Ensure we have a valid token.
     *
     * @return bool
     */
    public function ensureValidToken(): bool
    {
        return $this->tokenService->ensureValidToken();
    }

    // ===== CARD & IBAN SERVICES =====

    /**
     * Get card details by card number.
     *
     * @param string $cardNumber
     * @return object|null
     */
    public function getCard(string $cardNumber): ?object
    {
        return $this->cardIbanService->getCardInquiry($cardNumber);
    }

    /**
     * Get IBAN details by IBAN number.
     *
     * @param string $iban
     * @return object|null
     */
    public function getSheba(string $iban): ?object
    {
        return $this->cardIbanService->getIbanInquiry($iban);
    }

    /**
     * Convert card to account number.
     *
     * @param string $cardNumber
     * @return object|null
     */
    public function getCardToAccount(string $cardNumber): ?object
    {
        return $this->cardIbanService->cardToAccount($cardNumber);
    }

    /**
     * Convert card to IBAN.
     *
     * @param string $cardNumber
     * @return object|null
     */
    public function getCardToIban(string $cardNumber): ?object
    {
        return $this->cardIbanService->cardToIban($cardNumber);
    }

    /**
     * Convert account to IBAN.
     *
     * @param string $bankId
     * @param string $accountNumber
     * @return object|null
     */
    public function getAccountToIban(string $bankId, string $accountNumber): ?object
    {
        return $this->cardIbanService->accountToIban($bankId, $accountNumber);
    }

    /**
     * Get complete card information including all conversions.
     *
     * @param string $cardNumber
     * @return array
     */
    public function getCompleteCardInfo(string $cardNumber): array
    {
        return $this->cardIbanService->getCompleteCardInfo($cardNumber);
    }

    // ===== MATCHING & VERIFICATION SERVICES =====

    /**
     * Match card with national code and birth date.
     *
     * @param string $cardNumber
     * @param string $nationalCode
     * @param string $birthDate
     * @return object|null
     */
    public function matchCardWithNationalCode(string $cardNumber, string $nationalCode, string $birthDate): ?object
    {
        return $this->matchingService->matchCardWithNationalCodeAndBirthDate($cardNumber, $nationalCode, $birthDate);
    }

    /**
     * Match IBAN with national code and birth date.
     *
     * @param string $iban
     * @param string $nationalCode
     * @param string $birthDate
     * @return object|null
     */
    public function matchIbanWithNationalCode(string $iban, string $nationalCode, string $birthDate): ?object
    {
        return $this->matchingService->matchIbanWithNationalCodeAndBirthDate($iban, $nationalCode, $birthDate);
    }

    /**
     * Match national code with mobile number (Shahkar).
     *
     * @param string $nationalCode
     * @param string $mobileNumber
     * @return object|null
     */
    public function matchNationalCodeWithMobile(string $nationalCode, string $mobileNumber): ?object
    {
        return $this->matchingService->matchNationalCodeWithMobileNumber($nationalCode, $mobileNumber);
    }

    /**
     * Perform comprehensive verification.
     *
     * @param string $cardNumber
     * @param string $nationalCode
     * @param string $birthDate
     * @param string $mobileNumber
     * @return array
     */
    public function comprehensiveVerification(string $cardNumber, string $nationalCode, string $birthDate, string $mobileNumber): array
    {
        return $this->matchingService->comprehensiveVerification($cardNumber, $nationalCode, $birthDate, $mobileNumber);
    }

    // ===== IDENTITY & INFORMATION SERVICES =====

    /**
     * Get postal code details.
     *
     * @param string $postalCode
     * @return object|null
     */
    public function getPostalCode(string $postalCode): ?object
    {
        return $this->identityService->getPostalCodeInquiry($postalCode);
    }

    /**
     * Get military service status.
     *
     * @param string $nationalCode
     * @return object|null
     */
    public function getMilitary(string $nationalCode): ?object
    {
        return $this->identityService->getMilitaryServiceInquiry($nationalCode);
    }

    /**
     * Get SANA code status.
     *
     * @param string $nationalCode
     * @param string $mobileNumber
     * @return object|null
     */
    public function getSanaCode(string $nationalCode, string $mobileNumber): ?object
    {
        return $this->identityService->getSanaCodeInquiry($nationalCode, $mobileNumber);
    }

    /**
     * Get comprehensive identity information.
     *
     * @param string $nationalCode
     * @param string $mobileNumber
     * @param string|null $postalCode
     * @return array
     */
    public function getIdentityInfo(string $nationalCode, string $mobileNumber, ?string $postalCode = null): array
    {
        return $this->identityService->getComprehensiveIdentityInfo($nationalCode, $mobileNumber, $postalCode);
    }

    // ===== ACCOUNT & REPORTING SERVICES =====

    /**
     * Get account balance.
     *
     * @return object|null
     */
    public function getBalance(): ?object
    {
        return $this->accountService->getAccountBalance();
    }

    /**
     * Get formatted account balance.
     *
     * @return array
     */
    public function getFormattedBalance(): array
    {
        return $this->accountService->getFormattedBalance();
    }

    /**
     * Get daily consumption report.
     *
     * @param string $yearMonthDay
     * @return object|null
     */
    public function getDailyReport(string $yearMonthDay): ?object
    {
        return $this->accountService->getDailyConsumptionReport($yearMonthDay);
    }

    /**
     * Get formatted daily report.
     *
     * @param string $yearMonthDay
     * @return array
     */
    public function getFormattedDailyReport(string $yearMonthDay): array
    {
        return $this->accountService->getFormattedDailyReport($yearMonthDay);
    }

    /**
     * Get account summary.
     *
     * @param string|null $reportDate
     * @return array
     */
    public function getAccountSummary(?string $reportDate = null): array
    {
        return $this->accountService->getAccountSummary($reportDate);
    }

    // ===== UTILITY METHODS =====

    /**
     * Generate a unique track ID.
     *
     * @return string
     */
    public function generateTrackId(): string
    {
        return $this->baseService->generateTrackId();
    }

    /**
     * Get the current access token.
     *
     * @return string
     */
    public function getToken(): string
    {
        return $this->baseService->getToken();
    }

    /**
     * Get the current refresh token.
     *
     * @return string
     */
    public function getRefreshToken(): string
    {
        return $this->baseService->getRefreshToken();
    }

    /**
     * Make a direct API request (for advanced usage).
     *
     * @param string $endpoint
     * @param array $params
     * @param string $method
     * @return object|null
     */
    public function makeApiRequest(string $endpoint, array $params = [], string $method = 'GET'): ?object
    {
        return $this->baseService->makeApiRequest($endpoint, $params, $method);
    }

    // ===== SERVICE INSTANCES ACCESS =====

    /**
     * Get the token service instance.
     *
     * @return Token
     */
    public function tokenService(): Token
    {
        return $this->tokenService;
    }

    /**
     * Get the card/IBAN service instance.
     *
     * @return CardIban
     */
    public function cardIbanService(): CardIban
    {
        return $this->cardIbanService;
    }

    /**
     * Get the matching service instance.
     *
     * @return Matching
     */
    public function matchingService(): Matching
    {
        return $this->matchingService;
    }

    /**
     * Get the identity service instance.
     *
     * @return Identity
     */
    public function identityService(): Identity
    {
        return $this->identityService;
    }

    /**
     * Get the account service instance.
     *
     * @return Account
     */
    public function accountService(): Account
    {
        return $this->accountService;
    }
} 