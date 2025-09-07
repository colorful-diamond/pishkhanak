<?php

namespace App\Services\Finnotech;

use App\Services\Finnotech\Token;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Client\RequestException;

class Promissory
{
    private Token $token;
    private string $baseUrl;
    private int $retryAttempts = 1;
    private string $redisKeyPrefix = 'finnotech:promissory:';

    public function __construct(Token $token)
    {
        $this->token = $token;
        $this->baseUrl = config('services.finnotech.base_url');
    }

    /**
     * Delete a promissory note.
     *
     * @param string $clientId
     * @param string $requestId
     * @param string|null $trackId
     * @return array
     *
     * @throws RequestException
     *
     * Sample output:
     * [
     *     "responseCode" => "FN-PSKZ-20000300000",
     *     "trackId" => "526e-4a7f-92fd-af9f70f",
     *     "result" => [
     *         "message" => "عملیات مورد نظر با موفقیت انجام شد"
     *     ],
     *     "status" => "DONE"
     * ]
     */
    public function deletePromissory(string $clientId, string $requestId, ?string $trackId = null): array
    {
        $endpoint = "/promissory/v2/clients/{$clientId}/promissoryDelete";
        $queryParams = [
            'requestId' => $requestId,
            'trackId' => $trackId ?? $this->generateUuid(),
        ];

        return $this->makeRequest('GET', $endpoint, $queryParams);
    }

    /**
     * Submit a promissory guarantee request.
     *
     * @param string $clientId
     * @param array $requestData
     * @param string|null $trackId
     * @return array
     *
     * @throws RequestException
     *
     * Sample output:
     * [
     *     "trackId" => "ef226c90-20cc-48c1676a",
     *     "result" => [
     *         "requestId" => "123456789",
     *         "status" => "DONE",
     *         "message" => "درخواست با موفقیت ثبت شد"
     *     ],
     *     "status" => "DONE"
     * ]
     */
    public function submitGuaranteeRequest(string $clientId, array $requestData, ?string $trackId = null): array
    {
        $endpoint = "/promissory/v2/clients/{$clientId}/guaranteeRequest";
        $queryParams = ['trackId' => $trackId ?? $this->generateUuid()];

        return $this->makeRequest('POST', $endpoint, $queryParams, $requestData);
    }

    /**
     * Finalize a promissory note.
     *
     * @param string $clientId
     * @param array $requestData
     * @param string|null $trackId
     * @return array
     *
     * @throws RequestException
     *
     * Sample output:
     * [
     *     "trackId" => "ef226c90-20cc-48c1676a",
     *     "result" => [
     *         "requestId" => "123456789",
     *         "status" => "DONE",
     *         "message" => "سفته با موفقیت نهایی شد"
     *     ],
     *     "status" => "DONE"
     * ]
     */
    public function finalizePromissory(string $clientId, array $requestData, ?string $trackId = null): array
    {
        $endpoint = "/promissory/v2/clients/{$clientId}/finalize";
        $queryParams = ['trackId' => $trackId ?? $this->generateUuid()];

        return $this->makeRequest('POST', $endpoint, $queryParams, $requestData);
    }

    /**
     * Inquire about a promissory note.
     *
     * @param string $clientId
     * @param string $requestId
     * @param string|null $trackId
     * @return array
     *
     * @throws RequestException
     *
     * Sample output:
     * [
     *     "trackId" => "ef226c90-20cc-48c1676a",
     *     "result" => [
     *         "requestId" => "123456789",
     *         "status" => "DONE",
     *         "promissoryInfo" => [
     *             // Promissory note details
     *         ]
     *     ],
     *     "status" => "DONE"
     * ]
     */
    public function inquirePromissory(string $clientId, string $requestId, ?string $trackId = null): array
    {
        $endpoint = "/promissory/v2/clients/{$clientId}/inquiry";
        $queryParams = [
            'requestId' => $requestId,
            'trackId' => $trackId ?? $this->generateUuid(),
        ];

        return $this->makeRequest('GET', $endpoint, $queryParams);
    }

    /**
     * Inquire about a promissory publish request.
     *
     * @param string $clientId
     * @param string $requestId
     * @param string|null $trackId
     * @return array
     *
     * @throws RequestException
     *
     * Sample output:
     * [
     *     "trackId" => "ef226c90-20cc-48c1676a",
     *     "result" => [
     *         "requestId" => "123456789",
     *         "status" => "DONE",
     *         "publishRequestInfo" => [
     *             // Publish request details
     *         ]
     *     ],
     *     "status" => "DONE"
     * ]
     */
    public function inquirePublishRequest(string $clientId, string $requestId, ?string $trackId = null): array
    {
        $endpoint = "/promissory/v2/clients/{$clientId}/publishRequestInquiry";
        $queryParams = [
            'requestId' => $requestId,
            'trackId' => $trackId ?? $this->generateUuid(),
        ];

        return $this->makeRequest('GET', $endpoint, $queryParams);
    }

    /**
     * Submit a promissory publish request.
     *
     * @param string $clientId
     * @param array $requestData
     * @param string|null $trackId
     * @return array
     *
     * @throws RequestException
     *
     * Sample output:
     * [
     *     "trackId" => "ef226c90-20cc-48c1676a",
     *     "result" => [
     *         "requestId" => "123456789",
     *         "status" => "DONE",
     *         "message" => "درخواست انتشار با موفقیت ثبت شد"
     *     ],
     *     "status" => "DONE"
     * ]
     */
    public function submitPublishRequest(string $clientId, array $requestData, ?string $trackId = null): array
    {
        $endpoint = "/promissory/v2/clients/{$clientId}/publishRequest";
        $queryParams = ['trackId' => $trackId ?? $this->generateUuid()];

        return $this->makeRequest('POST', $endpoint, $queryParams, $requestData);
    }

    /**
     * Submit a promissory publish request with SMS authentication.
     *
     * @param string $clientId
     * @param array $requestData
     * @param string|null $trackId
     * @return array
     *
     * @throws RequestException
     *
     * Sample output:
     * [
     *     "trackId" => "ef226c90-20cc-48c1676a",
     *     "result" => [
     *         "requestId" => "123456789",
     *         "status" => "DONE",
     *         "message" => "کد تایید به شماره موبایل ارسال شد"
     *     ],
     *     "status" => "DONE"
     * ]
     */
    public function submitPublishRequestWithSms(string $clientId, array $requestData, string $smsToken, ?string $trackId = null): array
    {
        $endpoint = "/promissory/v2/clients/{$clientId}/publishRequest/sms";
        $queryParams = ['trackId' => $trackId ?? $this->generateUuid()];
        return $this->makeRequest('POST', $endpoint, $queryParams, $requestData, $smsToken);
    }

    /**
     * Get a signed document.
     *
     * @param string $clientId
     * @param string $requestId
     * @param string|null $trackId
     * @return array
     *
     * @throws RequestException
     *
     * Sample output:
     * [
     *     "trackId" => "ef226c90-20cc-48c1676a",
     *     "result" => [
     *         "requestId" => "123456789",
     *         "status" => "DONE",
     *         "signedDocument" => "base64_encoded_document_content"
     *     ],
     *     "status" => "DONE"
     * ]
     */
    public function getSignedDocument(string $clientId, string $requestId, ?string $trackId = null): array
    {
        $endpoint = "/sign/v2/clients/{$clientId}/signedDocument";
        $queryParams = [
            'requestId' => $requestId,
            'trackId' => $trackId ?? $this->generateUuid(),
        ];

        return $this->makeRequest('GET', $endpoint, $queryParams);
    }

    /**
     * Submit a sign request.
     *
     * @param string $clientId
     * @param array $requestData
     * @param string|null $trackId
     * @return array
     *
     * @throws RequestException
     *
     * Sample output:
     * [
     *     "trackId" => "ef226c90-20cc-48c1676a",
     *     "result" => [
     *         "requestId" => "123456789",
     *         "status" => "DONE",
     *         "message" => "درخواست امضا با موفقیت ثبت شد"
     *     ],
     *     "status" => "DONE"
     * ]
     */
    public function submitSignRequest(string $clientId, array $requestData, ?string $trackId = null): array
    {
        $endpoint = "/sign/v2/clients/{$clientId}/request";
        $queryParams = ['trackId' => $trackId ?? $this->generateUuid()];

        return $this->makeRequest('POST', $endpoint, $queryParams, $requestData);
    }

    /**
     * Inquire about the status of a sign request.
     *
     * @param string $clientId
     * @param string $requestId
     * @param string|null $trackId
     * @return array
     *
     * @throws RequestException
     *
     * Sample output:
     * [
     *     "trackId" => "ef226c90-20cc-48c1676a",
     *     "result" => [
     *         "requestId" => "123456789",
     *         "status" => "DONE",
     *         "signStatus" => "SIGNED",
     *         "message" => "سند با موفقیت امضا شد"
     *     ],
     *     "status" => "DONE"
     * ]
     */
    public function inquireSignStatus(string $clientId, string $requestId, ?string $trackId = null): array
    {
        $endpoint = "/sign/v2/clients/{$clientId}/statusInquiry";
        $queryParams = [
            'requestId' => $requestId,
            'trackId' => $trackId ?? $this->generateUuid(),
        ];

        return $this->makeRequest('GET', $endpoint, $queryParams);
    }

    /**
     * Make an HTTP request to the Finnotech API.
     *
     * @param string $method
     * @param string $endpoint
     * @param array $queryParams
     * @param array $requestData
     * @return array
     *
     * @throws RequestException
     */
    private function makeRequest(string $method, string $endpoint, array $queryParams = [], array $requestData = [], ?string $token = null): array
    {
        $url = $this->baseUrl . $endpoint;
        $headers = [
            'Authorization' => 'Bearer ' . ($token ?? $this->token->getClientCredentialToken()),
        ];

        $attempts = 0;
        do {
            try {
                $response = Http::withHeaders($headers)
                    ->withQueryParameters($queryParams)
                    ->{strtolower($method)}($url, $requestData);

                $response->throw();
                return $response->json();
            } catch (RequestException $e) {
                $attempts++;
                if ($attempts > $this->retryAttempts) {
                    $this->logFailedRequest($method, $url, $queryParams, $requestData, $e);
                    throw $e;
                }
                sleep(1); // Wait for 1 second before retrying
            }
        } while ($attempts <= $this->retryAttempts);
    }

    /**
     * Log a failed request to Redis and application log.
     *
     * @param string $method
     * @param string $url
     * @param array $queryParams
     * @param array $requestData
     * @param RequestException $exception
     * @return void
     */
    private function logFailedRequest(string $method, string $url, array $queryParams, array $requestData, RequestException $exception): void
    {
        $logData = [
            'method' => $method,
            'url' => $url,
            'query_params' => $queryParams,
            'request_data' => $requestData,
            'error' => $exception->getMessage(),
            'timestamp' => now()->toDateTimeString(),
        ];

        $redisKey = $this->redisKeyPrefix . 'failed_requests:' . uniqid();
        Redis::set($redisKey, json_encode($logData));
        Redis::expire($redisKey, 86400); // Expire after 24 hours

        Log::error('Finnotech API request failed', $logData);
    }

    /**
     * Generate a UUID v4.
     *
     * @return string
     */
    private function generateUuid(): string
    {
        return (string) \Illuminate\Support\Str::uuid();
    }
}