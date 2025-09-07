# Jibit API Usage Guide

This document provides comprehensive information about how Jibit API is used in the Pishkhanak project, including the complete API documentation and implementation details.

## Table of Contents

1. [General Information](#general-information)
2. [Authentication](#authentication)
3. [Card & IBAN Services](#card--iban-services)
4. [Implementation in Service Controllers](#implementation-in-service-controllers)
5. [Response Structure](#response-structure)
6. [Error Handling](#error-handling)

---

## General Information

- **Base URL**: `https://napi.jibit.ir/ide`
- **Request Tracking**: Send unique tracking ID in `X-TRACK-ID` header (max 50 characters)
- **Fees**: Only charged for successful requests
- **IP Whitelisting**: Server IP(s) must be whitelisted with Jibit support

---

## Authentication

### 1.1. Get Access Token

**Endpoint**: `POST /v1/tokens/generate`

**Request Body**:
```json
{
    "apiKey": "your_api_key",
    "secretKey": "your_secret_key"
}
```

**Successful Response**:
```json
{
    "accessToken": "bearer_token_here",
    "refreshToken": "refresh_token_here"
}
```

### 1.2. Refresh Access Token

**Endpoint**: `POST /v1/tokens/refresh`

**Request Body**:
```json
{
    "accessToken": "expired_access_token",
    "refreshToken": "valid_refresh_token"
}
```

---

## Card & IBAN Services

All endpoints require `Authorization: Bearer {accessToken}` header.

### 2.1. Card Inquiry

**Endpoint**: `GET /v1/cards?number={cardNumber}`

**Response Structure**:
```json
{
    "number": "6037991234567890",
    "cardInfo": {
        "bank": "MELLI",
        "type": "DEBIT",
        "ownerName": "احمد محمدی",
        "depositNumber": "1234567890"
    }
}
```

### 2.2. IBAN Inquiry

**Endpoint**: `GET /v1/ibans?value={iban}`

**Response Structure**:
```json
{
    "value": "IR123456789012345678901234",
    "ibanInfo": {
        "bank": "MELLI",
        "depositNumber": "1234567890",
        "status": "ACTIVE",
        "owners": [
            {
                "firstName": "احمد",
                "lastName": "محمدی"
            }
        ]
    }
}
```

### 2.3. Card to Account Conversion

**Endpoint**: `GET /v1/cards?number={cardNumber}&deposit=true`

**Response Structure**:
```json
{
    "number": "6037991234567890",
    "cardInfo": {
        "bank": "MELLI",
        "type": "DEBIT",
        "ownerName": "احمد محمدی",
        "depositNumber": "1234567890"
    }
}
```

### 2.4. Card to IBAN Conversion

**Endpoint**: `GET /v1/cards?number={cardNumber}&iban=true`

**Response Structure**:
```json
{
    "number": "6037991234567890",
    "cardInfo": {
        "bank": "MELLI",
        "type": "DEBIT",
        "ownerName": "احمد محمدی",
        "depositNumber": "IR123456789012345678901234"
    }
}
```

### 2.5. Account to IBAN Conversion

**Endpoint**: `GET /v1/deposits?bank={BANK_ID}&number={accountNumber}&iban=true`

**Response Structure**:
```json
{
    "bank": "MELLI",
    "number": "1234567890",
    "iban": "IR123456789012345678901234"
}
```

---

## Implementation in Service Controllers

### CardIbanController

```php
public function process(array $serviceData, Service $service): array
{
    try {
        $cardNumber = $serviceData['card_number'] ?? '';
        
        // Validate card number
        if (!preg_match('/^\d{16}$/', $cardNumber)) {
            return [
                'success' => false,
                'message' => 'شماره کارت باید 16 رقم باشد.'
            ];
        }
        
        // Use Jibit API for real card to IBAN conversion
        $jibitService = app(\App\Services\JibitService::class);
        $apiResult = $jibitService->getCardToIban($cardNumber);
        
        if ($apiResult && isset($apiResult->cardInfo)) {
            $result = [
                'card_number' => $cardNumber,
                'iban' => $apiResult->cardInfo->depositNumber ?? '', // This should be the IBAN
                'bank_name' => $this->getBankNameFromCode($apiResult->cardInfo->bank ?? ''),
                'account_type' => $apiResult->cardInfo->type ?? 'جاری',
                'owner_name' => $apiResult->cardInfo->ownerName ?? '',
                'conversion_date' => now()->format('Y/m/d H:i:s'),
                'api_response' => $apiResult,
            ];
            
            return [
                'success' => true,
                'data' => $result
            ];
        } else {
            return [
                'success' => false,
                'message' => 'خطا در دریافت اطلاعات از سرویس خارجی.'
            ];
        }
    } catch (\Exception $e) {
        return [
            'success' => false,
            'message' => 'خطا در پردازش سرویس: ' . $e->getMessage()
        ];
    }
}
```

### CardAccountController

```php
public function process(array $serviceData, Service $service): array
{
    try {
        $cardNumber = $serviceData['card_number'] ?? '';
        
        // Validate card number
        if (!preg_match('/^\d{16}$/', $cardNumber)) {
            return [
                'success' => false,
                'message' => 'شماره کارت باید 16 رقم باشد.'
            ];
        }
        
        // Use Jibit API for real card to account conversion
        $jibitService = app(\App\Services\JibitService::class);
        $apiResult = $jibitService->getCardToAccount($cardNumber);
        
        if ($apiResult && isset($apiResult->cardInfo)) {
            $result = [
                'card_number' => $cardNumber,
                'account_number' => $apiResult->cardInfo->depositNumber ?? '',
                'bank_name' => $this->getBankNameFromCode($apiResult->cardInfo->bank ?? ''),
                'account_type' => $apiResult->cardInfo->type ?? 'جاری',
                'owner_name' => $apiResult->cardInfo->ownerName ?? '',
                'conversion_date' => now()->format('Y/m/d H:i:s'),
                'api_response' => $apiResult,
            ];
            
            return [
                'success' => true,
                'data' => $result
            ];
        } else {
            return [
                'success' => false,
                'message' => 'خطا در دریافت اطلاعات از سرویس خارجی.'
            ];
        }
    } catch (\Exception $e) {
        return [
            'success' => false,
            'message' => 'خطا در پردازش سرویس: ' . $e->getMessage()
        ];
    }
}
```

### IbanAccountController

```php
public function process(array $serviceData, Service $service): array
{
    try {
        $iban = $serviceData['iban'] ?? '';
        
        // Validate IBAN
        if (!preg_match('/^IR\d{24}$/', $iban)) {
            return [
                'success' => false,
                'message' => 'شماره شبا باید با IR شروع شود و 26 کاراکتر باشد.'
            ];
        }
        
        // Use Jibit API for real IBAN to account conversion
        $jibitService = app(\App\Services\JibitService::class);
        $apiResult = $jibitService->getSheba($iban);
        
        if ($apiResult && isset($apiResult->ibanInfo)) {
            $result = [
                'iban' => $iban,
                'account_number' => $apiResult->ibanInfo->depositNumber ?? '',
                'bank_name' => $this->getBankNameFromCode($apiResult->ibanInfo->bank ?? ''),
                'account_status' => $apiResult->ibanInfo->status ?? 'فعال',
                'owners' => $apiResult->ibanInfo->owners ?? [],
                'conversion_date' => now()->format('Y/m/d H:i:s'),
                'api_response' => $apiResult,
            ];
            
            return [
                'success' => true,
                'data' => $result
            ];
        } else {
            return [
                'success' => false,
                'message' => 'خطا در دریافت اطلاعات از سرویس خارجی.'
            ];
        }
    } catch (\Exception $e) {
        return [
            'success' => false,
            'message' => 'خطا در پردازش سرویس: ' . $e->getMessage()
        ];
    }
}
```

### IbanValidatorController

```php
public function process(array $serviceData, Service $service): array
{
    try {
        $iban = $serviceData['iban'] ?? '';
        
        // Validate IBAN
        if (!preg_match('/^IR\d{24}$/', $iban)) {
            return [
                'success' => false,
                'message' => 'شماره شبا باید با IR شروع شود و 26 کاراکتر باشد.'
            ];
        }
        
        // Use Jibit API for real IBAN validation
        $jibitService = app(\App\Services\JibitService::class);
        $apiResult = $jibitService->getSheba($iban);
        
        if ($apiResult && isset($apiResult->ibanInfo)) {
            $validationResult = [
                'iban' => $iban,
                'is_valid' => true,
                'bank_name' => $this->getBankNameFromCode($apiResult->ibanInfo->bank ?? ''),
                'account_number' => $apiResult->ibanInfo->depositNumber ?? '',
                'account_status' => $apiResult->ibanInfo->status ?? 'فعال',
                'owners' => $apiResult->ibanInfo->owners ?? [],
                'validation_date' => now()->format('Y/m/d H:i:s'),
                'api_response' => $apiResult,
            ];
            
            return [
                'success' => true,
                'data' => $validationResult
            ];
        } else {
            return [
                'success' => false,
                'message' => 'خطا در دریافت اطلاعات از سرویس خارجی.'
            ];
        }
    } catch (\Exception $e) {
        return [
            'success' => false,
            'message' => 'خطا در پردازش سرویس: ' . $e->getMessage()
        ];
    }
}
```

---

## Response Structure

### Bank Code Mapping

All service controllers use the following bank code mapping:

```php
private function getBankNameFromCode(string $bankCode): string
{
    $bankCodes = [
        'MELLI' => 'ملی',
        'SEPAH' => 'سپه',
        'TOSEE_SADERAT' => 'توسعه صادرات',
        'SANAT_VA_MADAN' => 'صنعت و معدن',
        'KESHAVARZI' => 'کشاورزی',
        'MASKAN' => 'مسکن',
        'POST_BANK' => 'پست بانک',
        'GHARZOLHASANEH' => 'قوامین',
        'AYANDEH' => 'آینده',
        'SHAHR' => 'شهر',
        'ASIA' => 'آسیا',
        'GARDESHGARI' => 'گردشگری',
        'EGHTESAD_NOVIN' => 'اقتصاد نوین',
        'IRAN_ZAMIN' => 'ایران زمین',
        'MARKAZI' => 'مرکزی',
        'TOSEE_TAVON' => 'توسعه تعاون',
        'KARAFARIN' => 'کارآفرین',
        'PASARGAD' => 'پاسارگاد',
        'PARSIAN' => 'پارسیان',
        'SAMAN' => 'سامان',
        'SINA' => 'سینا',
        'TOSEE' => 'توسعه',
    ];

    return $bankCodes[$bankCode] ?? 'نامشخص';
}
```

### Expected Response Fields

#### Card Services (Card Inquiry, Card to Account, Card to IBAN)
- `number`: The card number sent
- `cardInfo.bank`: Bank identifier (e.g., "MELLI")
- `cardInfo.type`: Card type (e.g., "DEBIT", "CREDIT")
- `cardInfo.ownerName`: Cardholder name
- `cardInfo.depositNumber`: Account number or IBAN

#### IBAN Services (IBAN Inquiry, IBAN Validation)
- `value`: The IBAN sent
- `ibanInfo.bank`: Bank identifier
- `ibanInfo.depositNumber`: Account number
- `ibanInfo.status`: IBAN status (e.g., "ACTIVE", "BLOCKED")
- `ibanInfo.owners`: Array of owner objects with firstName and lastName

---

## Error Handling

### Common Error Scenarios

1. **Invalid Card Number**: 16-digit validation
2. **Invalid IBAN**: IR + 24 digits validation
3. **API Connection Error**: Network or authentication issues
4. **Invalid Response**: Missing expected fields in API response

### Error Response Structure

```php
return [
    'success' => false,
    'message' => 'خطا در دریافت اطلاعات از سرویس خارجی.'
];
```

### Exception Handling

All service controllers wrap API calls in try-catch blocks:

```php
try {
    // API call
} catch (\Exception $e) {
    return [
        'success' => false,
        'message' => 'خطا در پردازش سرویس: ' . $e->getMessage()
    ];
}
```

---

## Important Notes

1. **Authentication**: Ensure valid access token before making API calls
2. **Rate Limiting**: Respect API rate limits
3. **Error Logging**: All errors are logged for debugging
4. **Response Validation**: Always check for expected response structure
5. **Bank Code Mapping**: Use consistent bank code mapping across all services
6. **Data Privacy**: Handle sensitive data (card numbers, IBANs) securely

---

## Future Enhancements

1. **Caching**: Implement response caching for frequently requested data
2. **Retry Logic**: Add retry mechanism for failed API calls
3. **Monitoring**: Add API usage monitoring and alerting
4. **Fallback**: Implement fallback to alternative data sources
5. **Validation**: Add more comprehensive input validation 