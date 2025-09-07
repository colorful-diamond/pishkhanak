<?php

namespace App\Services\Finnotech;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use App\Services\Finnotech\Token;

class Bill
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
     * Get detailed billing inquiry
     *
     * @param string $type Bill type (e.g., 'Water', 'Electricity', 'Gas')
     * @param string $parameter Bill identifier
     * @return array
     *
     * @throws \Exception
     *
     * Sample output:
     * [
     *     "trackId" => "561e5d7a-af5a-4e99-8a32-ce0d9be94019",
     *     "result" => [
     *         "Amount" => "590000",
     *         "BillId" => "9030635704127",
     *         "PayId" => "59010572",
     *         "Date" => "1401/07/03",
     *         "Info" => [
     *             "CompanyName" => "توزيع نيروی برق تهران بزرگ",
     *             "CustomerName" => "سيدمحمدرضا گلزار",
     *             "CustomerFamily" => "",
     *             "CustomerType" => "حقیقی",
     *             "Address" => "خواجه عبداله انصاري ابوذرغفاري شمالي",
     *             "PostalCode" => "1236598127",
     *             "FileNumber" => "656379",
     *             "ComputerPassword" => "23569741",
     *             "IdentificationNumber" => "30226984",
     *             "TariffType" => "خانگی",
     *             "Phase" => "1",
     *             "Amper" => "15",
     *             "VoltageType" => "ثانویه",
     *             "ContractDemand" => "3",
     *             // ... (other fields as per the documentation)
     *         ]
     *     ],
     *     "status" => "DONE"
     * ]
     */
    public function getDetailedBillingInquiry(string $type, string $parameter): array
    {
        $endpoint = "/billing/v2/clients/{$this->clientId}/detailBillingInquiry";
        $queryParams = [
            'type' => $type,
            'parameter' => $parameter,
        ];

        return $this->makeRequest('GET', $endpoint, $queryParams);
    }

    /**
     * Get billing inquiry
     *
     * @param string $type Bill type (e.g., 'Water', 'Electricity', 'Gas', 'Tel', 'Mobile')
     * @param string $parameter Bill identifier
     * @param string|null $secondParameter Optional second parameter (required for mobile bills)
     * @return array
     *
     * @throws \Exception
     *
     * Sample output:
     * [
     *     "trackId" => "46d3470b-eb14-4a17-8e77-6052031527d6",
     *     "result" => [
     *         "Amount" => 50000,
     *         "BillId" => 931564121146,
     *         "PayId" => 5080129,
     *         "Date" => ""
     *     ],
     *     "status" => "DONE"
     * ]
     */
    public function getBillingInquiry(string $type, string $parameter, ?string $secondParameter = null): array
    {
        $endpoint = "/billing/v2/clients/{$this->clientId}/billingInquiry";
        $queryParams = [
            'type' => $type,
            'parameter' => $parameter,
        ];

        if ($secondParameter) {
            $queryParams['secondParameter'] = $secondParameter;
        }

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

        Redis::lpush('finnotech:failed_requests:bill', json_encode($failedRequest));
    }
}