<?php

namespace App\Services\Finnotech;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use App\Services\Finnotech\Token;

class MobileCharge
{
    private string $baseUrl;
    private string $clientId;
    private Token $token;
    private int $maxRetries = 2;

    public function __construct()
    {
        $this->baseUrl = config('finnotech.base_url');
        $this->clientId = config('finnotech.client_id');
        $this->token = new Token();
    }

    /**
     * Send a charge request
     *
     * @param array $data Request data
     * @param string|null $trackId Optional tracking ID
     * @return array
     *
     * @throws \Exception
     *
     * Sample output:
     * [
     *     "responseCode" => "FN-BGTP-20001100000",
     *     "trackId" => "f3de371e-fb73-443f-a848-f7a02556b133",
     *     "result" => [
     *         "completed" => true,
     *         "status" => "Ok",
     *         "message" => "",
     *         "orderId" => "110"
     *     ],
     *     "status" => "DONE"
     * ]
     */
    public function sendChargeRequest(array $data, ?string $trackId = null): array
    {
        $endpoint = "/billing/v2/clients/{$this->clientId}/chargeSendRequest";
        $queryParams = $trackId ? ['trackId' => $trackId] : [];

        return $this->makeRequest('POST', $endpoint, $queryParams, $data);
    }

    /**
     * Approve a charge request
     *
     * @param string $orderId Order ID to approve
     * @param string|null $trackId Optional tracking ID
     * @return array
     *
     * @throws \Exception
     *
     * Sample output:
     * [
     *     "responseCode" => "FN-BGTP-20099900000",
     *     "trackId" => "74ff9fea-882d-4693-8461-db35caeee430",
     *     "result" => [
     *         "completed" => true,
     *         "status" => "Ok",
     *         "message" => "عمليات قبلا با موفقيت انجام شده است.",
     *         "orderId" => "109"
     *     ],
     *     "status" => "DONE"
     * ]
     */
    public function approveChargeRequest(string $orderId, ?string $trackId = null): array
    {
        $endpoint = "/billing/v2/clients/{$this->clientId}/chargeApprove";
        $queryParams = $trackId ? ['trackId' => $trackId] : [];
        $data = ['orderId' => $orderId];

        return $this->makeRequest('POST', $endpoint, $queryParams, $data);
    }

    /**
     * Inquire about a charge request
     *
     * @param string $orderId Order ID to inquire about
     * @param string|null $trackId Optional tracking ID
     * @return array
     *
     * @throws \Exception
     *
     * Sample output:
     * [
     *     "responseCode" => "FN-BGTP-20001300000",
     *     "trackId" => "e325b243-9d5b-4fab-974e-123191567660",
     *     "result" => [
     *         "mobile" => "",
     *         "completed" => true,
     *         "status" => "Ok",
     *         "finalStatus" => "Confirmed"
     *     ],
     *     "status" => "DONE"
     * ]
     */
    public function inquireChargeRequest(string $orderId, ?string $trackId = null): array
    {
        $endpoint = "/billing/v2/clients/{$this->clientId}/chargeInquiry";
        $queryParams = [
            'orderId' => $orderId,
        ];

        if ($trackId) {
            $queryParams['trackId'] = $trackId;
        }

        return $this->makeRequest('GET', $endpoint, $queryParams);
    }

    /**
     * Get pin charge products list
     *
     * @param string|null $trackId Optional tracking ID
     * @return array
     *
     * @throws \Exception
     *
     * Sample output:
     * [
     *     "responseCode" => "FN-PGFH-20000700000",
     *     "trackId" => "iam-track-id-in-uuid-format",
     *     "result" => [
     *         [
     *             "amount" => 100000,
     *             "productCode" => 11,
     *             "operator" => "MTN"
     *         ],
     *         // ... more products
     *     ],
     *     "status" => "DONE"
     * ]
     */
    public function getPinChargeProductsList(?string $trackId = null): array
    {
        $endpoint = "/charge/v2/clients/{$this->clientId}/pinProductsList";
        $queryParams = $trackId ? ['trackId' => $trackId] : [];

        return $this->makeRequest('GET', $endpoint, $queryParams);
    }

    /**
     * Request a pin charge
     *
     * @param int $productCode Product code for the desired pin charge
     * @param string|null $trackId Optional tracking ID
     * @return array
     *
     * @throws \Exception
     *
     * Sample output:
     * [
     *     "responseCode" => "FN-PGFH-20000000000",
     *     "trackId" => "8267738a-9efd-4995-8581-63b638a40f66",
     *     "result" => [
     *         "pin" => "111222333444555666777",
     *         "serial" => "29194000358477769"
     *     ],
     *     "status" => "DONE"
     * ]
     */
    public function requestPinCharge(int $productCode, ?string $trackId = null): array
    {
        $endpoint = "/charge/v2/clients/{$this->clientId}/pinCharge";
        $queryParams = $trackId ? ['trackId' => $trackId] : [];
        $data = ['productCode' => $productCode];

        return $this->makeRequest('POST', $endpoint, $queryParams, $data);
    }

    /**
     * Inquire about a pin charge
     *
     * @param string $inquiryTrackId Track ID of the pin charge request to inquire about
     * @param string|null $trackId Optional tracking ID for this inquiry
     * @return array
     *
     * @throws \Exception
     *
     * Sample output:
     * [
     *     "responseCode" => "FN-PGFH-20000100000",
     *     "trackId" => "8267738a-9efd-4995-8581-63b638a40f66",
     *     "result" => [
     *         "status" => "SUCCESS",
     *         "pin" => "111222333444555666777",
     *         "serial" => "29194000358477769"
     *     ],
     *     "status" => "DONE"
     * ]
     */
    public function inquirePinCharge(string $inquiryTrackId, ?string $trackId = null): array
    {
        $endpoint = "/charge/v2/clients/{$this->clientId}/pinChargeInquiry";
        $queryParams = [
            'inquiryTrackId' => $inquiryTrackId,
        ];

        if ($trackId) {
            $queryParams['trackId'] = $trackId;
        }

        return $this->makeRequest('GET', $endpoint, $queryParams);
    }

    /**
     * Inquire about the charge wallet balance
     *
     * @param string|null $trackId Optional tracking ID
     * @return array
     *
     * @throws \Exception
     *
     * Sample output:
     * [
     *     "responseCode" => "FN-PGFH-20099900000",
     *     "trackId" => "8267738a-9efd-4995-8581-63b638a40f66",
     *     "result" => [
     *         "balance" => "50000"
     *     ],
     *     "status" => "DONE"
     * ]
     */
    public function inquireChargeWalletBalance(?string $trackId = null): array
    {
        $endpoint = "/charge/v2/clients/{$this->clientId}/chargeWalletInquiry";
        $queryParams = $trackId ? ['trackId' => $trackId] : [];

        return $this->makeRequest('GET', $endpoint, $queryParams);
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
    private function makeRequest(string $method, string $endpoint, array $queryParams = [], array $data = []): array
    {
        $url = $this->baseUrl . $endpoint;
        $headers = [
            'Authorization' => 'Bearer ' . $this->token->getClientCredentialToken(),
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

        Redis::lpush('finnotech:failed_requests:mobile_charge', json_encode($failedRequest));
    }
}