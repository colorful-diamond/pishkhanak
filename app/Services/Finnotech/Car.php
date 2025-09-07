<?php

namespace App\Services\Finnotech;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use App\Services\Finnotech\Token;

class Car
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
     * Get driving offense inquiry
     *
     * @param string $plateNumber Plate number (9 digits)
     * @param string $nationalId National ID
     * @param string $mobile Mobile number
     * @return array
     *
     * @throws \Exception
     *
     * Sample output:
     * [
     *     "trackId" => "4ce7fa61-1734-4ced-b343-554a9ec5bad9",
     *     "result" => [
     *         "Amount" => 1000000,
     *         "BillID" => "123456789",
     *         "PaymentID" => "987654321",
     *         "PlateNumber" => "12ب345ایران67",
     *         "Offenses" => [
     *             [
     *                 "Amount" => 500000,
     *                 "Date" => "1400/01/01",
     *                 "Description" => "سرعت غیر مجاز",
     *                 "Location" => "تهران - اتوبان همت",
     *                 "OffenseCode" => "2034"
     *             ],
     *             // ... more offenses
     *         ]
     *     ],
     *     "status" => "DONE"
     * ]
     */
    public function getDrivingOffenseInquiry(string $plateNumber, string $nationalId, string $mobile): array
    {
        $endpoint = "/billing/v2/clients/{$this->clientId}/drivingOffense";
        $queryParams = [
            'plateNumber' => $plateNumber,
            'nationalID' => $nationalId,
            'mobile' => $mobile,
        ];

        return $this->makeRequest('GET', $endpoint, $queryParams);
    }

    /**
     * Get riding offense inquiry
     *
     * @param string $plateNumber Plate number (9 digits)
     * @param string $nationalId National ID
     * @param string $mobile Mobile number
     * @param string|null $trackId Optional tracking ID
     * @return array
     *
     * @throws \Exception
     *
     * Sample output:
     * [
     *     "trackId" => "266dcf92-753c-47db-b0f9-cbbe57abffdd",
     *     "result" => [
     *         "Amount" => 500000,
     *         "BillID" => "123456789",
     *         "PaymentID" => "987654321",
     *         "PlateNumber" => "12345ایران67",
     *     ],
     *     "status" => "DONE"
     * ]
     */
    public function getRidingOffenseInquiry(string $plateNumber, string $nationalId, string $mobile): array
    {
        $endpoint = "/billing/v2/clients/{$this->clientId}/ridingOffense";
        $queryParams = [
            'plateNumber' => $plateNumber,
            'nationalID' => $nationalId,
            'mobile' => $mobile,
        ];

        return $this->makeRequest('GET', $endpoint, $queryParams);
    }

    /**
     * Get negative score inquiry
     *
     * @param string $licenseNumber License number
     * @param string $nationalId National ID
     * @param string $mobile Mobile number
     * @param string|null $trackId Optional tracking ID
     * @return array
     *
     * @throws \Exception
     *
     * Sample output:
     * [
     *     "trackId" => "266dcf92-753c-47db-b0f9-cbbe57abffdd",
     *     "result" => [
     *         "LicenseNumber" => "9607173353",
     *         "NegativeScore" => "0",
     *         "OffenseCount" => null,
     *         "Rule" => "-"
     *     ],
     *     "status" => "DONE"
     * ]
     */
    public function getNegativeScoreInquiry(string $licenseNumber, string $nationalId, string $mobile): array
    {
        $endpoint = "/billing/v2/clients/{$this->clientId}/negativeScore";
        $queryParams = [
            'licenseNumber' => $licenseNumber,
            'nationalID' => $nationalId,
            'mobile' => $mobile,
        ];

        return $this->makeRequest('GET', $endpoint, $queryParams);
    }

    /**
     * Get traffic offense image
     *
     * @param string $offenseId Offense ID
     * @param string|null $trackId Optional tracking ID
     * @return array
     *
     * @throws \Exception
     *
     * Sample output:
     * [
     *     "trackId" => "46d3470b-eb14-4a17-8e77-6052031527d6",
     *     "result" => [
     *         "OffenseId" => "47691362",
     *         "PlateImageUrl" => "https://najaservices.payment.ayantech.ir/pages/files.aspx?id=37CBC84868E842FD813C123564B9A7ED",
     *         "VehicleImageUrl" => "https://najaservices.payment.ayantech.ir/pages/files.aspx?id=2E1EC5139E364E4090637FF3D7E3BAC6"
     *     ],
     *     "status" => "DONE"
     * ]
     */
    public function getTrafficOffenseImage(string $offenseId): array
    {
        $endpoint = "/billing/v2/clients/{$this->clientId}/trafficOffenseImage";
        $queryParams = [
            'offenseId' => $offenseId,
        ];

        return $this->makeRequest('GET', $endpoint, $queryParams);
    }

    /**
     * Get freeway toll inquiry
     *
     * @param string $plateNumber Plate number (9 digits)
     * @param string|null $trackId Optional tracking ID
     * @return array
     *
     * @throws \Exception
     *
     * Sample output:
     * [
     *     "trackId" => "12b52a3de-4ac19f637d08",
     *     "result" => [
     *         "bills" => [
     *             [
     *                 "paymentId" => "9529451",
     *                 "date" => "2023-07-21 09:46:23",
     *                 "price" => 12000,
     *                 "gateway" => null,
     *                 "freeway" => "21"
     *             ]
     *         ],
     *         "totalAmount" => 12000
     *     ],
     *     "status" => "DONE"
     * ]
     */
    public function getFreewayTollInquiry(string $plateNumber): array
    {
        $endpoint = "/ecity/v2/clients/{$this->clientId}/freewayTollInquiry";
        $queryParams = [
            'plateNumber' => $plateNumber,
        ];

        return $this->makeRequest('GET', $endpoint, $queryParams);
    }

    /**
     * Get active plate numbers
     *
     * @param string $nationalId National ID
     * @param string $mobile Mobile number
     * @param string|null $trackId Optional tracking ID
     * @return array
     *
     * @throws \Exception
     *
     * Sample output:
     * [
     *     "trackId" => "410a3431-526e-4a7f-92fd-af9bcdabf70f",
     *     "result" => [
     *         "plateNumbers" => [
     *             [
     *                 "nationalId" => "-",
     *                 "plateNumber" => "ایران ۹۹ - ۲۸۳ ن  ۸۰",
     *                 "revoked" => true,
     *                 "revokedDateTime" => "12/26/2018 13:23:40",
     *                 "revokedDescription" => "شماره گذاري بوکان",
     *                 "serialNumber" => "1010029296607"
     *             ],
     *             // ... more plate numbers
     *         ]
     *     ],
     *     "status" => "DONE"
     * ]
     */
    public function getActivePlateNumbers(string $nationalId, string $mobile): array
    {
        $endpoint = "/vehicle/v2/clients/{$this->clientId}/activePlateNumbers";
        $queryParams = [
            'nationalId' => $nationalId,
            'mobile' => $mobile,
        ];

        return $this->makeRequest('GET', $endpoint, $queryParams);
    }

    /**
     * Match vehicle ownership
     *
     * @param string $plateNumber Plate number (9 digits)
     * @param string $nationalId National ID
     * @param string|null $trackId Optional tracking ID
     * @return array
     *
     * @throws \Exception
     *
     * Sample output:
     * [
     *     "trackId" => "1c78eb0c-253f-486f-2f1b8",
     *     "result" => [
     *         "isValid" => true,
     *         "vehicleType" => "سايپا",
     *         "vehicleTip" => "SAINA S MT",
     *         "vehicleModel" => "1401",
     *     ],
     *     "status" => "DONE"
     * ]
     */
    public function matchVehicleOwnership(string $plateNumber, string $nationalId): array
    {
        $endpoint = "/vehicle/v2/clients/{$this->clientId}/matchingVehicleOwnership";
        $queryParams = [
            'plateNumber' => $plateNumber,
            'nationalId' => $nationalId,
        ];

        return $this->makeRequest('GET', $endpoint, $queryParams);
    }

    /**
     * Get license check inquiry
     *
     * @param string $nationalId National ID
     * @param string $mobile Mobile number
     * @param string|null $trackId Optional tracking ID
     * @return array
     *
     * @throws \Exception
     *
     * Sample output:
     * [
     *     "trackId" => "2ed30b25-85fc-4022-bad9-9c9a7beec475",
     *     "result" => [
     *         "licenses" => [
     *             [
     *                 "barCode" => "00692767417000000000",
     *                 "issueDate" => "06/13/2050 00:00:00",
     *                 "firstname" => "کریم",
     *                 "lastname" => "کریمی",
     *                 "nationalCode" => "000000000",
     *                 "licenseNumber" => "0000000000",
     *                 "licenseStatus" => "اسکن شده ناجي پاس",
     *                 "licenseType" => "پايه سوم",
     *                 "licenseValidityPeriod" => "10"
     *             ],
     *             // ... more licenses
     *         ],
     *         "licenseStatus" => [
     *             "code" => "G00000",
     *             "description" => "درخواست با موفقیت انجام شد."
     *         ]
     *     ],
     *     "status" => "DONE"
     * ]
     */
    public function getLicenseCheckInquiry(string $nationalId, string $mobile): array
    {
        $endpoint = "/kyc/v2/clients/{$this->clientId}/licenseCheckInquiry";
        $data = [
            'nationalID' => $nationalId,
            'mobile' => $mobile,
        ];

        return $this->makeRequest('POST', $endpoint, [], $data);
    }

    /**
     * Get traffic toll inquiry
     *
     * @param string $plateNumber Plate number (9 digits)
     * @param string $mobile Mobile number
     * @param string $nationalId National ID
     * @param string|null $trackId Optional tracking ID
     * @return array
     *
     * @throws \Exception
     *
     * Sample output:
     * [
     *     "trackId" => "52328-fe63-46bd-b1cc-e27f48f31016",
     *     "result" => [
     *         "plateNumber" => "ایران 40 - 123 ص 51",
     *         "bills" => [
     *             [
     *                 "amount" => 887851,
     *                 "fee" => 0,
     *                 "date" => "05/14/2023 00:00:00",
     *                 "paymentStatus" => "Unpaid",
     *                 "totalAmount" => 887851,
     *                 "uniqueId" => "c0e803181409f98a74e808dbf9724602"
     *             ]
     *         ]
     *     ],
     *     "status" => "DONE"
     * ]
     */
    public function getTrafficTollInquiry(string $plateNumber, string $mobile, string $nationalId): array
    {
        $endpoint = "/vehicle/v2/clients/{$this->clientId}/trafficTollInquiry";
        $queryParams = [
            'plateNumber' => $plateNumber,
            'mobile' => $mobile,
            'nationalId' => $nationalId,
        ];

        return $this->makeRequest('GET', $endpoint, $queryParams);
    }

    /**
     * Get vehicle info inquiry
     *
     * @param string $plateNumber Plate number (9 digits)
     * @param string $nationalId National ID
     * @param string|null $trackId Optional tracking ID
     * @return array
     *
     * @throws \Exception
     *
     * Sample output:
     * [
     *     "trackId" => "1c7-253f-486f-2f1b8",
     *     "result" => [
     *         "axelNo" => "2",
     *         "wheelNo" => "4",
     *         "capacity" => "جمعا 5 نفر",
     *         "cylinderNo" => "4",
     *         "mainColor" => "سفيد",
     *         "secondColor" => "سفيد",
     *         "usageCodeByInsuranceCompany" => "1",
     *         "usageCodeByCentralInsurance" => "8",
     *         "usageNameByInsuranceCompany" => "شخصي",
     *         "usageNameByNaja" => "سواري",
     *         "modelByNaja" => "1401",
     *         "insuranceUniqueCode" => "10443945765",
     *         "insurancePrintNumber" => "1001/1401/000115/1300032",
     *         "insuranceCompanyTitle" => "بیمه آرمان",
     *         "insuranceCompanyCode" => 21,
     *         "discountLifeYearNumber" => "0",
     *         "discountPersonYearNumber" => "0",
     *         "discountFinancialYearNumber" => "0",
     *         "discountLifeYearPercent" => "0",
     *         "discountPersonYearPercent" => "0",
     *         "discountFinancialYearPercent" => "0",
     *         "plateInstallDate" => "1401/12/21",
     *         "subUsage" => "سواري",
     *         "vehicleTypeNameByInsuranceCompany" => "سواري",
     *         "vehicleTypeCodeByInsuranceCompany" => "1",
     *         "vehicleGroupCode" => "2",
     *         "systemByInsuranceCompany" => "سايپا",
     *         "tipByCentralInsurance" => "ساينا",
     *         "vehicleSystemCode" => "7",
     *         "systemNameByNaja" => "سايپا",
     *         "tipByNaja" => "SAINA S MT",
     *         "tipCodeByCompany" => "14696",
     *         "chassisNumber" => "NAS851100N5000587",
     *         "engineNumber" => "M15/9002809",
     *         "vin" => "NAS800100N5700087",
     *         "beginDate" => "1401/12/24",
     *         "endDate" => "1402/12/24"
     *     ],
     *     "status" => "DONE"
     * ]
     */
    public function getVehicleInfoInquiry(string $plateNumber, string $nationalId): array
    {
        $endpoint = "/vehicle/v2/clients/{$this->clientId}/vehicleInfoInquiry";
        $queryParams = [
            'plateNumber' => $plateNumber,
            'nationalId' => $nationalId,
        ];

        return $this->makeRequest('GET', $endpoint, $queryParams);
    }

    /**
     * Get plate number history
     *
     * @param string $plateNumber Plate number (9 digits)
     * @param string $nationalId National ID
     * @param string|null $trackId Optional tracking ID
     * @return array
     *
     * @throws \Exception
     *
     * Sample output:
     * [
     *     "trackId" => "dece6d37-297f-429f-moho-f6eb106ff6ee",
     *     "result" => [
     *         "plateHistory" => [
     *             [
     *                 "vehicleSystem" => "پژو",
     *                 "vehicleType" => "206",
     *                 "installDate" => "1378/02/05",
     *                 "detachDate" => "1400/01/11",
     *                 "vehicleModel" => "1377"
     *             ],
     *         ],
     *         "plateStatus" => "داراي مالک - نصب برروي وسيله",
     *         "tracePlate" => "10100100101001"
     *     ],
     *     "status" => "DONE"
     * ]
     */
    public function getPlateNumberHistory(string $plateNumber, string $nationalId): array
    {
        $endpoint = "/vehicle/v2/clients/{$this->clientId}/plateNumberHistory";
        $queryParams = [
            'plateNumber' => $plateNumber,
            'nationalId' => $nationalId,
        ];

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

        Redis::lpush('finnotech:failed_requests:car', json_encode($failedRequest));
    }
}