<?php

namespace App\Services\Finnotech;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use App\Services\Finnotech\Token;

class Inquiries
{
    private $baseUrl;
    private $clientId;
    private $token;
    private $maxRetries = 2;

    public function __construct()
    {
        $this->baseUrl = config('finnotech.base_url');
        $this->clientId = config('finnotech.client_id');
        $this->token = new Token();
    }

    /**
     * Get card information
     *
     * @param string $card 16-digit card number
     * @param string|null $trackId Optional tracking ID
     * @return array
     *
     * @throws \Exception
     *
     * Sample output:
     * [
     *     "result" => [
     *         "destCard" => "xxxx-xxxx-xxxx-3899",
     *         "name" => "علی آقایی",
     *         "result" => "0",
     *         "description" => "موفق",
     *         "doTime" => "1396/06/15 12:32:04",
     *         "bankName" => "بانک تجارت"
     *     ],
     *     "status" => "DONE",
     *     "trackId" => "get-cardInfo-0232"
     * ]
     */
    public function getCardInformation(string $card, ?string $trackId = null): array
    {
        $endpoint = "/mpg/v2/clients/{$this->clientId}/cards/{$card}";
        $queryParams = $trackId ? ['trackId' => $trackId] : [];

        return $this->makeRequest('GET', $endpoint, $queryParams);
    }

    /**
     * Get postal code information
     *
     * @param string $postalCode 10-digit postal code
     * @param string|null $trackId Optional tracking ID
     * @return array
     *
     * @throws \Exception
     *
     * Sample output:
     * [
     *     "result" => [
     *         "address" => "تهران خیابان ولیعصر",
     *         "city" => "تهران",
     *         "province" => "تهران",
     *         "postalCode" => "1234567890"
     *     ],
     *     "status" => "DONE",
     *     "trackId" => "get-postalInfo-0123"
     * ]
     */
    public function getPostalCodeInformation(string $postalCode, ?string $trackId = null): array
    {
        $endpoint = "/ecity/v2/clients/{$this->clientId}/postalCode/{$postalCode}";
        $queryParams = $trackId ? ['trackId' => $trackId] : [];

        return $this->makeRequest('GET', $endpoint, $queryParams);
    }

    /**
     * Get facility bank information
     *
     * @param string $loanNumber Loan number
     * @param string|null $trackId Optional tracking ID
     * @return array
     *
     * @throws \Exception
     *
     * Sample output:
     * [
     *     "result" => [
     *         "loanNumber" => "123456789",
     *         "remainedInstallments" => 10,
     *         "nextInstallmentAmount" => 1000000,
     *         "nextInstallmentDate" => "1400/01/01",
     *         "totalDebt" => 10000000
     *     ],
     *     "status" => "DONE",
     *     "trackId" => "get-facilityInfo-0345"
     * ]
     */
    public function getFacilityBankInfo(string $loanNumber, ?string $trackId = null): array
    {
        $endpoint = "/facility/v2/clients/{$this->clientId}/loans/{$loanNumber}";
        $queryParams = $trackId ? ['trackId' => $trackId] : [];

        return $this->makeRequest('GET', $endpoint, $queryParams);
    }

    /**
     * Convert card number to deposit number
     *
     * @param string $card 16-digit card number
     * @param string|null $trackId Optional tracking ID
     * @return array
     *
     * @throws \Exception
     *
     * Sample output:
     * [
     *     "result" => [
     *         "deposit" => "0123456789",
     *         "card" => "6037991123456789"
     *     ],
     *     "status" => "DONE",
     *     "trackId" => "card-to-deposit-0456"
     * ]
     */
    public function cardToDeposit(string $card, ?string $trackId = null): array
    {
        $endpoint = "/mpg/v2/clients/{$this->clientId}/cards/{$card}/deposits";
        $queryParams = $trackId ? ['trackId' => $trackId] : [];

        return $this->makeRequest('GET', $endpoint, $queryParams);
    }

    /**
     * Convert card number to IBAN
     *
     * @param string $card 16-digit card number
     * @param string|null $trackId Optional tracking ID
     * @return array
     *
     * @throws \Exception
     *
     * Sample output:
     * [
     *     "result" => [
     *         "IBAN" => "IR123456789012345678901234",
     *         "bankName" => "بانک ملی ایران",
     *         "deposit" => "0123456789",
     *         "card" => "6037991123456789"
     *     ],
     *     "status" => "DONE",
     *     "trackId" => "card-to-iban-0567"
     * ]
     */
    public function cardToIban(string $card, ?string $trackId = null): array
    {
        $endpoint = "/mpg/v2/clients/{$this->clientId}/cards/{$card}/iban";
        $queryParams = $trackId ? ['trackId' => $trackId] : [];

        return $this->makeRequest('GET', $endpoint, $queryParams);
    }

    /**
     * Get account information
     *
     * @param string $deposit Deposit number
     * @param string|null $trackId Optional tracking ID
     * @return array
     *
     * @throws \Exception
     *
     * Sample output:
     * [
     *     "result" => [
     *         "depositStatus" => "فعال",
     *         "depositOwner" => "علی محمدی",
     *         "sheba" => "IR123456789012345678901234",
     *         "affiliation" => "شخصی",
     *         "depositType" => "قرض الحسنه"
     *     ],
     *     "status" => "DONE",
     *     "trackId" => "account-info-0678"
     * ]
     */
    public function getAccountInfo(string $deposit, ?string $trackId = null): array
    {
        $endpoint = "/facility/v2/clients/{$this->clientId}/deposits/{$deposit}";
        $queryParams = $trackId ? ['trackId' => $trackId] : [];

        return $this->makeRequest('GET', $endpoint, $queryParams);
    }

    /**
     * Convert deposit number to IBAN
     *
     * @param string $deposit Deposit number
     * @param string|null $trackId Optional tracking ID
     * @return array
     *
     * @throws \Exception
     *
     * Sample output:
     * [
     *     "result" => [
     *         "IBAN" => "IR123456789012345678901234",
     *         "bankName" => "بانک ملی ایران",
     *         "deposit" => "0123456789"
     *     ],
     *     "status" => "DONE",
     *     "trackId" => "deposit-to-iban-0789"
     * ]
     */
    public function depositToIban(string $deposit, ?string $trackId = null): array
    {
        $endpoint = "/facility/v2/clients/{$this->clientId}/deposits/{$deposit}/iban";
        $queryParams = $trackId ? ['trackId' => $trackId] : [];

        return $this->makeRequest('GET', $endpoint, $queryParams);
    }

    /**
     * Group card to IBAN inquiry
     *
     * @param array $cards Array of 16-digit card numbers
     * @param string|null $trackId Optional tracking ID
     * @return array
     *
     * @throws \Exception
     *
     * Sample output:
     * [
     *     "result" => [
     *         [
     *             "card" => "6037991123456789",
     *             "iban" => "IR123456789012345678901234",
     *             "bankName" => "بانک ملی ایران"
     *         ],
     *         [
     *             "card" => "6037992987654321",
     *             "iban" => "IR987654321098765432109876",
     *             "bankName" => "بانک صادرات ایران"
     *         ]
     *     ],
     *     "status" => "DONE",
     *     "trackId" => "group-card-to-iban-0890"
     * ]
     */
    public function groupCardToIban(array $cards, ?string $trackId = null): array
    {
        $endpoint = "/facility/v2/clients/{$this->clientId}/cards/iban";
        $queryParams = $trackId ? ['trackId' => $trackId] : [];
        $data = ['cards' => $cards];

        return $this->makeRequest('POST', $endpoint, $queryParams, $data);
    }

    /**
     * Oak black list inquiry
     *
     * @param string $nationalCode National code
     * @param string|null $trackId Optional tracking ID
     * @return array
     *
     * @throws \Exception
     *
     * Sample output:
     * [
     *     "result" => [
     *         "isBlackListed" => false,
     *         "reason" => null
     *     ],
     *     "status" => "DONE",
     *     "trackId" => "oak-blacklist-0901"
     * ]
     */
    public function oakBlackListInquiry(string $nationalCode, ?string $trackId = null): array
    {
        $endpoint = "/oak/v2/clients/{$this->clientId}/users/{$nationalCode}/blackList";
        $queryParams = $trackId ? ['trackId' => $trackId] : [];

        return $this->makeRequest('GET', $endpoint, $queryParams);
    }

    /**
     * Oak customer cards inquiry
     *
     * @param string $nationalCode National code
     * @param string|null $trackId Optional tracking ID
     * @return array
     *
     * @throws \Exception
     *
     * Sample output:
     * [
     *     "result" => [
     *         "cards" => [
     *             [
     *                 "cardNumber" => "6037991123456789",
     *                 "bankName" => "بانک ملی ایران"
     *             ],
     *             [
     *                 "cardNumber" => "6037992987654321",
     *                 "bankName" => "بانک صادرات ایران"
     *             ]
     *         ]
     *     ],
     *     "status" => "DONE",
     *     "trackId" => "oak-customer-cards-1012"
     * ]
     */
    public function oakCustomerCardsInquiry(string $nationalCode, ?string $trackId = null): array
    {
        $endpoint = "/oak/v2/clients/{$this->clientId}/users/{$nationalCode}/cards";
        $queryParams = $trackId ? ['trackId' => $trackId] : [];

        return $this->makeRequest('GET', $endpoint, $queryParams);
    }

    /**
     * Oak CIF inquiry
     *
     * @param string $nationalCode National code
     * @param string|null $trackId Optional tracking ID
     * @return array
     *
     * @throws \Exception
     *
     * Sample output:
     * [
     *     "result" => [
     *         "cif" => "123456789",
     *         "firstName" => "علی",
     *         "lastName" => "محمدی",
     *         "fatherName" => "محمد",
     *         "birthDate" => "1370/01/01"
     *     ],
     *     "status" => "DONE",
     *     "trackId" => "oak-cif-inquiry-1123"
     * ]
     */
    public function oakCifInquiry(string $nationalCode, ?string $trackId = null): array
    {
        $endpoint = "/oak/v2/clients/{$this->clientId}/users/{$nationalCode}/cif";
        $queryParams = $trackId ? ['trackId' => $trackId] : [];

        return $this->makeRequest('GET', $endpoint, $queryParams);
    }

    /**
     * Oak customer info inquiry
     *
     * @param string $nationalCode National code
     * @param string|null $trackId Optional tracking ID
     * @return array
     *
     * @throws \Exception
     *
     * Sample output:
     * [
     *     "result" => [
     *         "firstName" => "علی",
     *         "lastName" => "محمدی",
     *         "fatherName" => "محمد",
     *         "birthDate" => "1370/01/01",
     *         "nationalCode" => "1234567890",
     *         "mobileNumber" => "09123456789",
     *         "address" => "تهران، خیابان ولیعصر"
     *     ],
     *     "status" => "DONE",
     *     "trackId" => "oak-customer-info-1234"
     * ]
     */
    public function oakCustomerInfoInquiry(string $nationalCode, ?string $trackId = null): array
    {
        $endpoint = "/oak/v2/clients/{$this->clientId}/users/{$nationalCode}/info";
        $queryParams = $trackId ? ['trackId' => $trackId] : [];

        return $this->makeRequest('GET', $endpoint, $queryParams);
    }

    /**
     * Oak customer type info inquiry
     *
     * @param string $nationalCode National code
     * @param string|null $trackId Optional tracking ID
     * @return array
     *
     * @throws \Exception
     *
     * Sample output:
     * [
     *     "result" => [
     *         "customerType" => "حقیقی",
     *         "customerSubType" => "عادی"
     *     ],
     *     "status" => "DONE",
     *     "trackId" => "oak-customer-type-1345"
     * ]
     */
    public function oakCustomerTypeInfoInquiry(string $nationalCode, ?string $trackId = null): array
    {
        $endpoint = "/oak/v2/clients/{$this->clientId}/users/{$nationalCode}/customerType";
        $queryParams = $trackId ? ['trackId' => $trackId] : [];

        return $this->makeRequest('GET', $endpoint, $queryParams);
    }

    /**
     * Oak deposits to IBAN inquiry
     *
     * @param string $nationalCode National code
     * @param string|null $trackId Optional tracking ID
     * @return array
     *
     * @throws \Exception
     *
     * Sample output:
     * [
     *     "result" => [
     *         "deposits" => [
     *             [
     *                 "depositNumber" => "0123456789",
     *                 "iban" => "IR123456789012345678901234"
     *             ],
     *             [
     *                 "depositNumber" => "9876543210",
     *                 "iban" => "IR987654321098765432109876"
     *             ]
     *         ]
     *     ],
     *     "status" => "DONE",
     *     "trackId" => "oak-deposits-to-iban-1456"
     * ]
     */
    public function oakDepositsToIbanInquiry(string $nationalCode, ?string $trackId = null): array
    {
        $endpoint = "/oak/v2/clients/{$this->clientId}/users/{$nationalCode}/deposits/iban";
        $queryParams = $trackId ? ['trackId' => $trackId] : [];

        return $this->makeRequest('GET', $endpoint, $queryParams);
    }

    /**
     * Oak deposits inquiry
     *
     * @param string $nationalCode National code
     * @param string|null $trackId Optional tracking ID
     * @return array
     *
     * @throws \Exception
     *
     * Sample output:
     * [
     *     "result" => [
     *         "deposits" => [
     *             [
     *                 "depositNumber" => "0123456789",
     *                 "depositType" => "قرض الحسنه",
     *                 "balance" => 1000000
     *             ],
     *             [
     *                 "depositNumber" => "9876543210",
     *                 "depositType" => "سپرده کوتاه مدت",
     *                 "balance" => 5000000
     *             ]
     *         ]
     *     ],
     *     "status" => "DONE",
     *     "trackId" => "oak-deposits-1567"
     * ]
     */
    public function oakDepositsInquiry(string $nationalCode, ?string $trackId = null): array
    {
        $endpoint = "/oak/v2/clients/{$this->clientId}/users/{$nationalCode}/deposits";
        $queryParams = $trackId ? ['trackId' => $trackId] : [];

        return $this->makeRequest('GET', $endpoint, $queryParams);
    }

    /**
     * Oak group IBAN inquiry
     *
     * @param array $ibans Array of IBAN numbers
     * @param string|null $trackId Optional tracking ID
     * @return array
     *
     * @throws \Exception
     *
     * Sample output:
     * [
     *     "result" => [
     *         [
     *             "iban" => "IR123456789012345678901234",
     *             "bankName" => "بانک ملی ایران",
     *             "depositNumber" => "0123456789"
     *         ],
     *         [
     *             "iban" => "IR987654321098765432109876",
     *             "bankName" => "بانک صادرات ایران",
     *             "depositNumber" => "9876543210"
     *         ]
     *     ],
     *     "status" => "DONE",
     *     "trackId" => "oak-group-iban-1678"
     * ]
     */
    public function oakGroupIbanInquiry(array $ibans, ?string $trackId = null): array
    {
        $endpoint = "/oak/v2/clients/{$this->clientId}/ibans";
        $queryParams = $trackId ? ['trackId' => $trackId] : [];
        $data = ['ibans' => $ibans];

        return $this->makeRequest('POST', $endpoint, $queryParams, $data);
    }

    /**
     * Oak IBAN inquiry
     *
     * @param string $iban IBAN number
     * @param string|null $trackId Optional tracking ID
     * @return array
     *
     * @throws \Exception
     *
     * Sample output:
     * [
     *     "result" => [
     *         "iban" => "IR123456789012345678901234",
     *         "bankName" => "بانک ملی ایران",
     *         "depositNumber" => "0123456789",
     *         "depositOwner" => "علی محمدی"
     *     ],
     *     "status" => "DONE",
     *     "trackId" => "oak-iban-inquiry-1789"
     * ]
     */
    public function oakIbanInquiry(string $iban, ?string $trackId = null): array
    {
        $endpoint = "/oak/v2/clients/{$this->clientId}/ibans/{$iban}";
        $queryParams = $trackId ? ['trackId' => $trackId] : [];

        return $this->makeRequest('GET', $endpoint, $queryParams);
    }

    /**
     * Oak Shahab inquiry
     *
     * @param string $nationalCode National code
     * @param string|null $trackId Optional tracking ID
     * @return array
     *
     * @throws \Exception
     *
     * Sample output:
     * [
     *     "result" => [
     *         "shahab" => "123456789012",
     *         "firstName" => "علی",
     *         "lastName" => "محمدی",
     *         "fatherName" => "محمد",
     *         "birthDate" => "1370/01/01"
     *     ],
     *     "status" => "DONE",
     *     "trackId" => "oak-shahab-inquiry-1890"
     * ]
     */
    public function oakShahabInquiry(string $nationalCode, ?string $trackId = null): array
    {
        $endpoint = "/oak/v2/clients/{$this->clientId}/users/{$nationalCode}/shahab";
        $queryParams = $trackId ? ['trackId' => $trackId] : [];

        return $this->makeRequest('GET', $endpoint, $queryParams);
    }

    /**
     * Oak Shahab inquiry with SMS authentication
     *
     * @param string $nationalCode National code
     * @param string $birthDate Birth date (yyyymmdd)
     * @param string $smsToken SMS token
     * @param string|null $identityNo Identity number (required for birth dates before 1990)
     * @param string|null $trackId Optional tracking ID
     * @return array
     *
     * @throws \Exception
     *
     * Sample output:
     * [
     *     "trackId" => "9f412213-a02d-4d22-b0a1-536942222381",
     *     "result" => [
     *         "nid" => "0013873427",
     *         "birthDate" => "13710607",
     *         "shahabCode" => "1000000024571433",
     *         "identityNo" => "0",
     *         "lastName" => "فرهادی",
     *         "gender" => "F",
     *         "fatherName" => "قلی",
     *         "firstName" => "گلی",
     *         "isConfirmed" => "Y",
     *     ],
     *     "status" => "DONE"
     * ]
     */
    public function oakShahabInquirySms(string $nationalCode, string $birthDate, string $smsToken, ?string $identityNo = null, ?string $trackId = null): array
    {
        $endpoint = "/oak/v2/clients/{$this->clientId}/users/{$nationalCode}/sms/shahabInquiry";
        $queryParams = [
            'birthDate' => $birthDate,
            'identityNo' => $identityNo,
        ];

        if ($trackId) {
            $queryParams['trackId'] = $trackId;
        }

        return $this->makeRequest('GET', $endpoint, $queryParams, [], $smsToken);
    }

    /**
     * Make an HTTP request to the Finnotech API
     *
     * @param string $method HTTP method (GET, POST, etc.)
     * @param string $endpoint API endpoint
     * @param array $queryParams Query parameters
     * @param array $data Request body data (for POST requests)
     * @return array
     *
     * @throws \Exception
     */
    private function makeRequest(string $method, string $endpoint, array $queryParams = [], array $data = [], ?string $token = null): array
    {
        $url = $this->baseUrl . $endpoint;
        $headers = [
            'Authorization' => 'Bearer ' . ($token ?? $this->token->getClientCredentialToken()),
        ];

        $retries = 0;
        while ($retries < $this->maxRetries) {
            try {
                $response = Http::withHeaders($headers)
                    ->withQueryParameters($queryParams)
                    ->{strtolower($method)}($url, $data);

                if ($response->successful()) {
                    return $response->json();
                }

                if ($response->status() === 401) {
                    // Token might be expired, refresh and retry
                    $this->token->refreshToken();
                    $headers['Authorization'] = 'Bearer ' . $this->token->getClientCredentialToken();
                    $retries++;
                    continue;
                }

                throw new \Exception("API request failed: " . $response->body());
            } catch (\Exception $e) {
                Log::error("Finnotech API request failed: " . $e->getMessage());
                $retries++;

                if ($retries >= $this->maxRetries) {
                    $this->storeFailedRequest($method, $endpoint, $queryParams, $data, $e->getMessage());
                    throw $e;
                }
            }
        }

        throw new \Exception("Max retries reached for API request");
    }

    /**
     * Store failed request details in Redis
     *
     * @param string $method
     * @param string $endpoint
     * @param array $queryParams
     * @param array $data
     * @param string $errorMessage
     */
    private function storeFailedRequest(string $method, string $endpoint, array $queryParams, array $data, string $errorMessage): void
    {
        $failedRequest = [
            'method' => $method,
            'endpoint' => $endpoint,
            'queryParams' => $queryParams,
            'data' => $data,
            'error' => $errorMessage,
            'timestamp' => now()->toDateTimeString(),
        ];

        Redis::lpush('finnotech:failed_requests', json_encode($failedRequest));
    }

    /**
     * Get OTP from user input (placeholder method)
     *
     * @return string
     */
    private function getUserInputOtp(): string
    {
        // This method should be implemented in your application's UI
        // For now, we'll just return a dummy OTP
        return '123456';
    }
}