<?php

namespace App\Services\Finnotech;

use App\Services\Finnotech\Token;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Str;

class Finance
{
    private Token $token;
    private string $baseUrl;
    private int $retryAttempts = 1;
    private string $redisKeyPrefix = 'finnotech:finance:';

    public function __construct(Token $token)
    {
        $this->token = $token;
        $this->baseUrl = config('services.finnotech.base_url');
    }

    /**
     * Get the token instance
     *
     * @return Token
     */
    public function getToken(): Token
    {
        return $this->token;
    }

    /**
     * Inquire about a Sayad cheque as the issuer.
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
     *     "trackId" => "7113b244-3c01-44f1-b395-e3ab464cfeaa",
     *     "result" => [
     *         "referenceId" => "11111111111111",
     *         "inquiryResult" => [
     *             "sayadId" => "5000000000000000",
     *             "branchCode" => "0000000",
     *             "bankCode" => "62",
     *             "amount" => 000000,
     *             "dueDate" => "00000000",
     *             "description" => "جهت تست سرویس",
     *             "serialNo" => "000000",
     *             "seriesNo" => "0000",
     *             "fromIban" => "IR000000000000000000000000",
     *             "reason" => null,
     *             "currency" => 1,
     *             "chequeStatus" => 1,
     *             "chequeType" => 1,
     *             "chequeMedia" => 1,
     *             "blockStatus" => 0,
     *             "guaranteeStatus" => 1,
     *             "locked" => 0,
     *             "receivers" => [
     *                 [
     *                     "idCode" => "0000000000",
     *                     "idType" => 1,
     *                     "name" => "آگاتا کریستی"
     *                 ]
     *             ],
     *             "issueDate" => "14001218095015"
     *         ]
     *     ],
     *     "status" => "DONE"
     * ]
     */
    public function inquireSayadChequeAsIssuer(string $clientId, array $requestData): array
    {
        $endpoint = "/credit/v2/clients/{$clientId}/users/{$requestData['user']}/sayadIssuerInquiryCheque";

        return $this->makeRequest('POST', $endpoint, [], $requestData, $this->token->getClientCredentialToken());
    }

    /**
     * Inquire about a chequebook.
     *
     * @param string $clientId
     * @param string $deposit
     * @param string|null $trackId
     * @return array
     *
     * @throws RequestException
     *
     * Sample output:
     * [
     *     "responseCode" => "FN-CTKZ-20002800000",
     *     "trackId" => "8b60188f-6d46a0-8e9b-b130dc8",
     *     "result" => [
     *         "chequeDetails" => [
     *             [
     *                 "accountNo" => "0101252337009",
     *                 "branchCodeAndName" => "0127/گاندی - بیمه رازی",
     *                 "branchCode" => "0127",
     *                 "branchName" => "گاندی-بیمه رازی",
     *                 "approvedDate" => "1401/09/23",
     *                 "customerName" => "محمدعلي دى پير",
     *                 "customerNo" => "0053682167",
     *                 "customerType" => "1",
     *                 "deliveryBranchCode" => "0297",
     *                 "expireDate" => "14040923",
     *                 "issuanceDate" => "14010923",
     *                 "mediaType" => "BANS",
     *                 "nationalCode" => "0010009007",
     *                 "serialNoFrom" => "8785410145586299",
     *                 "serialNoTo" => "6480010145586308",
     *                 "serialNumbers" => "868401016546299,4768013265586300,17270101467986301,4482064535586302,4098010174126303,2807589645586304,1959010146356305,5199010145326306,3737452145586307,6480058795586308",
     *                 "iban" => "IR600620000000101276454009",
     *                 "sheetCount" => "10",
     *                 "stateDescription" => "دسته چک چاپ شد "
     *             ]
     *         ]
     *     ],
     *     "status" => "DONE"
     * ]
     */
    public function inquireChequebook(string $clientId, string $deposit): array
    {
        $endpoint = "/credit/v2/clients/{$clientId}/chequebookInquiry";
        $queryParams = [
            'deposit' => $deposit,
        ];

        return $this->makeRequest('GET', $endpoint, $queryParams, [], $this->token->getClientCredentialToken());
    }

    /**
     * Inquire about cheque color status.
     *
     * @param string $clientId
     * @param string $idCode
     * @param string|null $trackId
     * @return array
     *
     * @throws RequestException
     *
     * Sample output:
     * [
     *     "responseCode" => "FN-CTFH-20099900000",
     *     "trackId" => "1ccdb-4924-4ab4-9a61-9ed6573",
     *     "result" => [
     *         "chequeColor" => "2"
     *     ],
     *     "status" => "DONE"
     * ]
     */
    public function inquireChequeColor(string $clientId, string $idCode): array
    {
        $endpoint = "/credit/v2/clients/{$clientId}/chequeColorInquiry";
        $queryParams = [
            'idCode' => $idCode,
        ];

        return $this->makeRequest('GET', $endpoint, $queryParams, [], $this->token->getClientCredentialToken());
    }

    /**
     * Get guarantee details.
     *
     * @param string $clientId
     * @param string $guaranteeId
     * @param string|null $trackId
     * @return array
     *
     * @throws RequestException
     *
     * Sample output:
     * [
     *     "trackId" => "feba236a-d4b5-4f93-97e9-0b0f8e993d2a",
     *     "result" => [
     *         "retCodeDescription" => null,
     *         "guaranteeId" => "6000034392235",
     *         "sepamId" => "0297665987813241",
     *         "branchCode" => "0387",
     *         "centralBankTypeCode" => "02",
     *         "guaranteeSubTypeCode" => "0632",
     *         "guaranteeSubTypeDesc" => "ضمانتنامه‌حسن‌‌اجر‌ای‌تعهدخدمات‌",
     *         "cif" => "0031254687",
     *         "debtAmount" => "500000000",
     *         "issueDate" => "14000824",
     *         "maturityDate" => "14010301",
     *         "renewDate" => " ",
     *         "companyPreDebtAmount" => " ",
     *         "issueChargeAmount" => "600000",
     *         "lastRenewChargeAmount" => " ",
     *         "totalCharge" => "600000",
     *         "guaranteeStatusCode" => "1",
     *         "guaranteeStatusDescription" => " وضعیت تع‌هد",
     *         "economicDesc" => "بخش‌ خدمات‌",
     *         "economicSubsectionDesc" => "بخش‌ خدمات‌-سایر",
     *     ],
     *     "status" => "DONE"
     * ]
     */
    public function getGuaranteeDetails(string $clientId, string $guaranteeId, ?string $trackId = null): array
    {
        $endpoint = "/credit/v2/clients/{$clientId}/guaranteeDetails";
        $queryParams = [
            'guaranteeId' => $guaranteeId,
            'trackId' => $trackId ?? $this->generateUuid(),
        ];

        return $this->makeRequest('GET', $endpoint, $queryParams, [], $this->token->getClientCredentialToken());
    }

    /**
     * Get guarantee collaterals.
     *
     * @param string $clientId
     * @param string $guaranteeId
     * @param string|null $trackId
     * @return array
     *
     * @throws RequestException
     *
     * Sample output:
     * [
     *     "trackId" => "get-guarantee-collaterals-029",
     *     "retCodeDescription" => null,
     *     "guaranteeId" => "6000034390000",
     *     "collaterals" => [
     *         [
     *             "collateralId" => "6545645646",
     *             "collateralTypeCode" => "01",
     *             "centralBankCollateralTypeCode" => "02",
     *             "collateralTypeDescription" => "شرح",
     *             "evaluatedAmount" => "10000",
     *             "debtAmount" => "10000",
     *             "interestRate" => "000.21",
     *             "receiveDate" => "14000101",
     *             "issueDate" => "13991202",
     *             "assignDate" => "14000203"
     *         ]
     *     ],
     *     "alertCode" => "00000",
     *     "messageOut" => null,
     *     "status" => "DONE"
     * ]
     */
    public function getGuaranteeCollaterals(string $clientId, string $guaranteeId, ?string $trackId = null): array
    {
        $endpoint = "/credit/v2/clients/{$clientId}/guaranteeCollaterals";
        $queryParams = [
            'guaranteeId' => $guaranteeId,
            'trackId' => $trackId ?? $this->generateUuid(),
        ];

        return $this->makeRequest('GET', $endpoint, $queryParams, [], $this->token->getClientCredentialToken());
    }

    /**
     * Inquire about MACNA (Integrated Customer Information System).
     *
     * @param string $clientId
     * @param string $nationalCode
     * @param string|null $trackId
     * @return array
     *
     * @throws RequestException
     *
     * Sample output:
     * [
     *     "trackId" => "feba236a-d4b5-4f93-97e9-0b0f8e993d2a",
     *     "result" => [
     *         [
     *             "trace" => 14645,
     *             "firstName" => "محمد",
     *             "lastName" => "قریشی",
     *             "nationalCode" => "2132131323",
     *             "remain" => [
     *                 "iran" => 100000000,
     *                 "bank" => 2000000000
     *             ],
     *             "history" => [
     *                 [
     *                     "macnaCode" => "1113810072693",
     *                     "issueDate" => "2011-08-17",
     *                     "updated" => "2015-08-23",
     *                     "cardType" => 2,
     *                     "pan" => "636214******0256",
     *                     "bankCode" => 62,
     *                     "bankName" => "بانک تات",
     *                     "stateCode" => 3,
     *                     "stateDescription" => "باطل",
     *                     "messageCode" => 14,
     *                     "messageDescription" => "درخواست المثنی"
     *                 ]
     *             ]
     *         ]
     *     ],
     *     "status" => "DONE"
     * ]
     */
    public function inquireMacna(string $clientId, string $nationalCode, ?string $trackId = null): array
    {
        $endpoint = "/credit/v2/clients/{$clientId}/macnaInquiry";
        $queryParams = [
            'nationalCode' => $nationalCode,
            'trackId' => $trackId ?? $this->generateUuid(),
        ];

        return $this->makeRequest('GET', $endpoint, $queryParams, [], $this->token->getClientCredentialToken());
    }

    /**
     * Issue a Sayad cheque.
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
     *     "trackId" => "7b244-3c01-44f1-b395-e3aeaa",
     *     "result" => [
     *         "message" => "ثبت چک با موفقیت انجام شد",
     *         "referenceId" => "6546542684564"
     *     ],
     *     "status" => "DONE"
     * ]
     */
    public function issueSayadCheque(string $clientId, array $requestData, ?string $trackId = null): array
    {
        $endpoint = "/credit/v2/clients/{$clientId}/ac/sayadIssueCheque";
        $queryParams = ['trackId' => $trackId ?? $this->generateUuid()];

        return $this->makeRequest('POST', $endpoint, $queryParams, $requestData, $this->token->getAuthorizationCodeToken());
    }

    /**
     * Inquire about a Sayad cheque serial.
     *
     * @param string $clientId
     * @param string $sayadId
     * @param string|null $trackId
     * @return array
     *
     * @throws RequestException
     *
     * Sample output:
     * [
     *     "trackId" => "7b244-3c01-44f1-b395-e3aeaa",
     *     "result" => [
     *         "iban" => "IR020620000000100925869001",
     *         "issuedDate" => "14000803",
     *         "expirationDate" => "null",
     *         "serialNo" => "878535",
     *         "seriesNo" => "1234",
     *         "mediaType" => "BANS",
     *         "branchCode" => "6299996",
     *         "name" => "کامران تفتی",
     *         "returnedCheques" => null
     *     ],
     *     "status" => "DONE"
     * ]
     */
    public function inquireSayadChequeSerial(string $clientId, string $sayadId, ?string $trackId = null): array
    {
        $endpoint = "/credit/v2/clients/{$clientId}/sayadSerialInquiry";
        $queryParams = [
            'sayadId' => $sayadId,
            'trackId' => $trackId ?? $this->generateUuid(),
        ];

        return $this->makeRequest('GET', $endpoint, $queryParams, [], $this->token->getClientCredentialToken());
    }

    /**
     * Transfer a Sayad cheque.
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
     *     "trackId" => "7b244-3c01-44f1-b395-e3aeaa",
     *     "result" => [
     *         "message" => "ثبت چک با موفقیت انجام شد",
     *         "referenceId" => "6546542684564",
     *         "resultCode" => "1"
     *     ],
     *     "status" => "DONE"
     * ]
     */
    public function transferSayadCheque(string $clientId, array $requestData, ?string $trackId = null): array
    {
        $endpoint = "/credit/v2/clients/{$clientId}/ac/sayadTransferCheque";
        $queryParams = ['trackId' => $trackId ?? $this->generateUuid()];

        return $this->makeRequest('POST', $endpoint, $queryParams, $requestData, $this->token->getAuthorizationCodeToken());
    }

    /**
     * Transfer a Sayad cheque using SMS authentication.
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
     *     "responseCode" => "FN-CTFH-20002600000",
     *     "trackId" => "7b244-3c01-44f1-moho-e3aeaa",
     *     "result" => [
     *         "message" => "ثبت چک با موفقیت انجام شد",
     *         "referenceId" => "6546542684564",
     *         "resultCode" => "1"
     *     ],
     *     "status" => "DONE"
     * ]
     */
    public function transferSayadChequeWithSms(string $clientId, array $requestData, ?string $trackId = null): array
    {
        $endpoint = "/credit/v2/clients/{$clientId}/sms/sayadTransferCheque";
        $queryParams = ['trackId' => $trackId ?? $this->generateUuid()];

        return $this->makeRequest('POST', $endpoint, $queryParams, $requestData, $this->token->getAuthorizationSmsToken());
    }

    /**
     * Accept a Sayad cheque using SMS authentication.
     *
     * @param string $clientId
     * @param string $user
     * @param array $requestData
     * @param string|null $trackId
     * @return array
     *
     * @throws RequestException
     *
     * Sample output:
     * [
     *     "trackId" => "7113b244-3c01-44f1-b395-e3ab464cfeaa",
     *     "result" => [
     *         "message" => "تایید چک با موفقیت انجام شد",
     *         "referenceId" => "11111111111111"
     *     ],
     *     "status" => "DONE"
     * ]
     */
    public function acceptSayadChequeWithSms(string $clientId, string $user, array $requestData, ?string $trackId = null): array
    {
        $endpoint = "/credit/v2/clients/{$clientId}/users/{$user}/sms/sayadAcceptCheque";
        $queryParams = ['trackId' => $trackId ?? $this->generateUuid()];

        return $this->makeRequest('POST', $endpoint, $queryParams, $requestData, $this->token->getAuthorizationSmsToken());
    }

    /**
     * Cancel a Sayad cheque using SMS authentication.
     *
     * @param string $clientId
     * @param string $user
     * @param array $requestData
     * @param string|null $trackId
     * @return array
     *
     * @throws RequestException
     *
     * Sample output:
     * [
     *     "trackId" => "7113b244-3c01-44f1-b395-e3ab464cfeaa",
     *     "result" => [
     *         "message" => "لغو چک با موفقیت انجام شد",
     *         "referenceId" => "11111111111111"
     *     ],
     *     "status" => "DONE"
     * ]
     */
    public function cancelSayadChequeWithSms(string $clientId, string $user, array $requestData, ?string $trackId = null): array
    {
        $endpoint = "/credit/v2/clients/{$clientId}/users/{$user}/sms/sayadCancelCheque";
        $queryParams = ['trackId' => $trackId ?? $this->generateUuid()];

        return $this->makeRequest('POST', $endpoint, $queryParams, $requestData, $this->token->getAuthorizationSmsToken());
    }

    /**
     * Inquire about a Sayad cheque using SMS authentication.
     *
     * @param string $clientId
     * @param string $user
     * @param string $sayadId
     * @param array $requestData
     * @param string|null $trackId
     * @return array
     *
     * @throws RequestException
     *
     * Sample output:
     * [
     *     "trackId" => "7113b244-3c01-44f1-b395-e3ab464cfeaa",
     *     "result" => [
     *         "message" => "استعلام چک با موفقیت انجام شد",
     *         "referenceId" => "25161234064321",
     *         "sayadId" => "0000000000000000",
     *         "branchCode" => "5502227",
     *         "bankCode" => "57",
     *         "amount" => 100000,
     *         "dueDate" => "14001027",
     *         "description" => "تست ",
     *         "serialNo" => "283331",
     *         "seriesNo" => "9999",
     *         "fromIban" => "IR550770000000104272127001",
     *         "reason" => "POSA",
     *         "currency" => 1,
     *         "chequeStatus" => 1,
     *         "chequeType" => 1,
     *         "chequeMedia" => 1,
     *         "blockStatus" => 0,
     *         "guaranteeStatus" => 1,
     *         "locked" => 0,
     *         "holders" => [
     *             [
     *                 "idCode" => "0050000039",
     *                 "idType" => 1,
     *                 "name" => "چارلز بوکوفسکی"
     *             ]
     *         ],
     *         "signers" => [
     *             [
     *                 "name" => "آلبرکامو",
     *                 "legalStamp" => 0
     *             ]
     *         ],
     *         "lockerBankCode" => "",
     *         "lockerBranchCode" => "",
     *         "issueDate" => "14001025143237",
     *         "toIban" => null
     *     ],
     *     "status" => "DONE"
     * ]
     */
    public function inquireSayadChequeWithSms(string $clientId, string $user, string $sayadId, array $requestData, ?string $trackId = null): array
    {
        $endpoint = "/credit/v2/clients/{$clientId}/users/{$user}/sms/sayadChequeInquiry";
        $queryParams = [
            'sayadId' => $sayadId,
            'trackId' => $trackId ?? $this->generateUuid(),
        ];

        return $this->makeRequest('GET', $endpoint, $queryParams, $requestData, $this->token->getAuthorizationSmsToken());
    }

    /**
     * Get Sayad ID by serial number.
     *
     * @param string $clientId
     * @param string $serialNo
     * @param string $seriesNo
     * @param string|null $trackId
     * @return array
     *
     * @throws RequestException
     *
     * Sample output:
     * [
     *     "responseCode" => "FN-CTKZ-20003100000",
     *     "trackId" => "8b60188f-6d46a0-8e9b-b130dc8",
     *     "result" => [
     *         "iban" => "IR720620000000100987654003",
     *         "issuedDate" => "14020718",
     *         "expirationDate" => "14050718",
     *         "serialNo" => "123456",
     *         "seriesNo" => "1234",
     *         "sayadId" => "7981010198526390",
     *         "mediaType" => "BANS",
     *         "branchCode" => "6202013"
     *     ],
     *     "status" => "DONE"
     * ]
     */
    public function getSayadIdBySerial(string $clientId, string $serialNo, string $seriesNo, ?string $trackId = null): array
    {
        $endpoint = "/credit/v2/clients/{$clientId}/sayadIdBySerial";
        $queryParams = [
            'serialNo' => $serialNo,
            'seriesNo' => $seriesNo,
            'trackId' => $trackId ?? $this->generateUuid(),
        ];

        return $this->makeRequest('GET', $endpoint, $queryParams, [], $this->token->getClientCredentialToken());
    }

    /**
     * Get returned cheques using SMS authentication.
     *
     * @param string $clientId
     * @param string $user
     * @param string|null $trackId
     * @return array
     *
     * @throws RequestException
     *
     * Sample output:
     * [
     *     "responseCode" => "FN-CTKZ-20000500000",
     *     "trackId" => "ae2ecba2-0d62-486f-moho-deec782f1f55",
     *     "result" => [
     *         "nid" => "0012345678",
     *         "legalId" => null,
     *         "name" => "سپهر صبور",
     *         "chequeList" => [
     *             [
     *                 "accountNumber" => "000000000013152586",
     *                 "amount" => 900000000,
     *                 "backDate" => "13960310",
     *                 "bankCode" => 18,
     *                 "branchCode" => "00167",
     *                 "branchDescription" => "بانک تجارت - فخررازي",
     *                 "date" => "13960310",
     *                 "id" => "090922111",
     *                 "number" => "0000887329"
     *             ]
     *         ]
     *     ],
     *     "status" => "DONE"
     * ]
     */
    public function getReturnedChequesWithSms(string $clientId, string $user, ?string $trackId = null): array
    {
        $endpoint = "/credit/v2/clients/{$clientId}/users/{$user}/sms/backCheques";
        $queryParams = ['trackId' => $trackId ?? $this->generateUuid()];

        return $this->makeRequest('GET', $endpoint, $queryParams, [], $this->token->getAuthorizationSmsToken());
    }

    /**
     * Inquire about cheque color status using SMS authentication.
     *
     * @param string $clientId
     * @param string $idCode
     * @param string|null $trackId
     * @return array
     *
     * @throws RequestException
     *
     * Sample output:
     * [
     *     "responseCode" => "FN-CTKZ-20002700000",
     *     "trackId" => "1ccdb-4924-4ab4-moho-9ed6573",
     *     "result" => [
     *         "chequeColor" => "4"
     *     ],
     *     "status" => "DONE"
     * ]
     */
    public function inquireChequeColorWithSms(string $clientId, string $idCode, ?string $trackId = null): array
    {
        $endpoint = "/credit/v2/clients/{$clientId}/sms/chequeColorInquiry";
        $queryParams = [
            'idCode' => $idCode,
            'trackId' => $trackId ?? $this->generateUuid(),
        ];

        return $this->makeRequest('GET', $endpoint, $queryParams, [], $this->token->getAuthorizationSmsToken());
    }

    /**
     * Inquire about facilities using SMS authentication.
     *
     * @param string $clientId
     * @param string $user
     * @param string|null $trackId
     * @return array
     *
     * @throws RequestException
     *
     * Sample output:
     * [
     *     "responseCode" => "FN-CTKZ-20000100000",
     *     "trackId" => "95d682a0-9c48-4927-moho-ecc70e134272",
     *     "result" => [
     *         "user" => "0012345678",
     *         "legalId" => null,
     *         "name" => "حسین رحمتی شادان",
     *         "facilityTotalAmount" => "324921522",
     *         "facilityDebtTotalAmount" => "101017180",
     *         "facilityPastExpiredTotalAmount" => "0",
     *         "facilityDeferredTotalAmount" => "0",
     *         "facilitySuspiciousTotalAmount" => "0",
     *         "dishonored" => "",
     *         "facilityList" => [
     *             [
     *                 "bankCode" => "14",
     *                 "branchCode" => "91182",
     *                 "branchDescription" => "بانک مسکن - مسکن - تهران  - پارک دانشجوتهران",
     *                 "pastExpiredAmount" => "0",
     *                 "deferredAmount" => "0",
     *                 "suspiciousAmount" => "0",
     *                 "debtorTotalAmount" => "101017180",
     *                 "type" => "18",
     *                 "amountOrginal" => "180000000",
     *                 "benefitAmount" => "101017180",
     *                 "FacilityBankCode" => "14",
     *                 "FacilityBranchCode" => "02159",
     *                 "FacilityBranch" => "بانک مسکن - شهیدنامجوی تهران",
     *                 "FacilityRequestNo" => "0215997049192414",
     *                 "FacilityRequestType" => "3",
     *                 "FacilityCurrencyCode" => "IRR",
     *                 "FacilityPastExpiredAmount" => "0",
     *                 "FacilityDeferredAmount" => "0",
     *                 "FacilitySuspiciousAmount" => "0",
     *                 "FacilityDebtorTotalAmount" => "1151155953",
     *                 "FacilityType" => "18",
     *                 "FacilityStatus" => "جاری",
     *                 "FacilityAmountOrginal" => "600000000",
     *                 "FacilityBenefitAmount" => "752873056",
     *                 "FacilitySetDate" => "13970430",
     *                 "FacilityEndDate" => "14090430",
     *                 "FacilityAmountObligation" => "0",
     *                 "FacilityGroup" => "اصلي",
     *                 "FacilityMoratoriumDate" => "0"
     *             ]
     *         ]
     *     ],
     *     "status" => "DONE"
     * ]
     */
    public function inquireFacilitiesWithSms(string $clientId, string $user, ?string $trackId = null): array
    {
        $endpoint = "/credit/v2/clients/{$clientId}/users/{$user}/sms/facilityInquiry";
        $queryParams = ['trackId' => $trackId ?? $this->generateUuid()];

        return $this->makeRequest('GET', $endpoint, $queryParams, [], $this->token->getAuthorizationSmsToken());
    }

    /**
     * Inquire about guarantees using SMS authentication.
     *
     * @param string $clientId
     * @param string $nid
     * @param string|null $trackId
     * @return array
     *
     * @throws RequestException
     *
     * Sample output:
     * [
     *     "responseCode" => "FN-CTKZ-20002300000",
     *     "trackId" => "0705ae6c-ac95-4574-moho-0e85d149d9bf",
     *     "result" => [
     *         "guarantyFirstName" => "امیرمحمد",
     *         "guarantyLastName" => "اسد",
     *         "guarantyNationalCode" => "1234567890",
     *         "guarantyLegalId" => null,
     *         "inquiryResultId" => "12345678",
     *         "debtorList" => [
     *             [
     *                 "debtorFirstName" => "اکبر",
     *                 "debtorLastName" => "اکبری",
     *                 "totalAmount" => "97488736",
     *                 "benefitAmount" => "90445871",
     *                 "obligationAmount" => "0",
     *                 "suspiciousAmount" => "0",
     *                 "deferredAmount" => "0",
     *                 "orginalAmount" => "300000000",
     *                 "pastExpiredAmount" => "0",
     *                 "bankCode" => "13",
     *                 "setDate" => "13980521",
     *                 "endDate" => "14010621",
     *                 "guarantyPercent" => "100",
     *                 "requestNumber" => "2222222222222222",
     *                 "requestType" => "3",
     *                 "branchCode" => "00144",
     *                 "branchDescription" => "بانک صادرات - شعبه شهید نامجو",
     *                 "guarantyIdNumber" => "0123456789",
     *                 "guarantyLegalId" => "0",
     *                 "defunctAmount" => null,
     *                 "commitmentBalanceAmount" => "0",
     *                 "latePenaltyAmount" => "0"
     *             ]
     *         ]
     *     ],
     *     "status" => "DONE"
     * ]
     */
    public function inquireGuarantiesWithSms(string $clientId, string $nid, ?string $trackId = null): array
    {
        $endpoint = "/credit/v2/clients/{$clientId}/users/{$nid}/sms/guarantyInquiry";
        $queryParams = ['trackId' => $trackId ?? $this->generateUuid()];

        return $this->makeRequest('GET', $endpoint, $queryParams, [], $this->token->getAuthorizationSmsToken());
    }

    /**
     * Request transaction credit inquiry.
     *
     * @param string $clientId
     * @param string $nationalCode
     * @param string $mobile
     * @param string|null $trackId
     * @return array
     *
     * @throws RequestException
     *
     * Sample output:
     * [
     *     "trackId" => "15a7f751-75c4-41c0",
     *     "result" => [
     *         "message" => "OK"
     *     ],
     *     "status" => "DONE"
     * ]
     */
    public function requestTransactionCreditInquiry(string $clientId, string $nationalCode, string $mobile, ?string $trackId = null): array
    {
        $endpoint = "/kyc/v2/clients/{$clientId}/transactionCreditInquiryRequest";
        $queryParams = [
            'nationalCode' => $nationalCode,
            'mobile' => $mobile,
            'trackId' => $trackId ?? $this->generateUuid(),
        ];

        return $this->makeRequest('GET', $endpoint, $queryParams, [], $this->token->getClientCredentialToken());
    }

    /**
     * Verify transaction credit inquiry.
     *
     * @param string $clientId
     * @param string $otp
     * @param string $nationalCode
     * @param string|null $trackId
     * @return array
     *
     * @throws RequestException
     *
     * Sample output:
     * [
     *     "responseCode" => "FN-KCST-20003500000",
     *     "trackId" => "09f47f7c-a990-4de5-97ce-422a280872eb",
     *     "result" => [
     *         "inquiryTrackId" => "0d09f3af-9e86-4a2c-9ff6-9c0bca5ff369"
     *     ],
     *     "status" => "DONE"
     * ]
     */
    public function verifyTransactionCreditInquiry(string $clientId, string $otp, string $nationalCode, ?string $trackId = null): array
    {
        $endpoint = "/kyc/v2/clients/{$clientId}/transactionCreditInquiryVerify";
        $queryParams = ['trackId' => $trackId ?? $this->generateUuid()];
        $requestData = [
            'otp' => $otp,
            'nationalCode' => $nationalCode,
        ];

        return $this->makeRequest('POST', $endpoint, $queryParams, $requestData, $this->token->getClientCredentialToken());
    }

    /**
     * Get transaction credit inquiry report.
     *
     * @param string $clientId
     * @param string $inquiryTrackId
     * @param string|null $trackId
     * @return array
     *
     * @throws RequestException
     *
     * Sample output:
     * [
     *     "responseCode" => "FN-KCST-20003600000",
     *     "trackId" => "109c188-4167-b9c4a7821a31",
     *     "result" => [
     *         "message" => "کد ملی 001111111در قوه قضائیه احراز شده است \nدر مورد این شخص،سابقه منفی یافت نشد"
     *     ],
     *     "status" => "DONE"
     * ]
     */
    public function getTransactionCreditInquiryReport(string $clientId, string $inquiryTrackId, ?string $trackId = null): array
    {
        $endpoint = "/kyc/v2/clients/{$clientId}/transactionCreditInquiryReport";
        $queryParams = [
            'inquiryTrackId' => $inquiryTrackId,
            'trackId' => $trackId ?? $this->generateUuid(),
        ];

        return $this->makeRequest('GET', $endpoint, $queryParams, [], $this->token->getClientCredentialToken());
    }

    /**
     * Make an HTTP request to the Finnotech API.
     *
     * @param string $method
     * @param string $endpoint
     * @param array $queryParams
     * @param array $requestData
     * @param string $token
     * @return array
     *
     * @throws RequestException
     */
    private function makeRequest(string $method, string $endpoint, array $queryParams = [], array $requestData = [], string $token): array
    {
        $url = $this->baseUrl . $endpoint;
        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
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
        return (string) Str::uuid();
    }
}