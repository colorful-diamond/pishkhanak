# API Documentation

## Overview

The Pishkhanak API provides programmatic access to Iranian financial, governmental, and administrative inquiry services. The API follows REST principles and returns JSON responses.

## Base URL

```
Production: https://pishkhanak.com/api/v1
Staging: https://staging.pishkhanak.com/api/v1
```

## Authentication

### API Key Authentication
```http
Authorization: Bearer your_api_token_here
Content-Type: application/json
Accept: application/json
```

### Getting API Keys
API keys can be obtained from your dashboard at `/admin/tokens` or by contacting support.

## Rate Limiting

- **Default**: 100 requests per minute per API key
- **Payment Services**: 10 requests per minute
- **AI Services**: 5 requests per minute

Rate limit headers are included in all responses:
```http
X-RateLimit-Limit: 100
X-RateLimit-Remaining: 95
X-RateLimit-Reset: 1640995200
```

## Error Handling

### Error Response Format
```json
{
    "success": false,
    "error": {
        "code": "VALIDATION_ERROR",
        "message": "The given data was invalid",
        "details": {
            "iban": ["The iban field must be 24 characters"]
        }
    },
    "timestamp": "2024-01-15T10:30:00Z"
}
```

### Common Error Codes
- `VALIDATION_ERROR`: Input validation failed
- `PAYMENT_REQUIRED`: Payment needed for service
- `SERVICE_UNAVAILABLE`: External service temporarily unavailable
- `RATE_LIMIT_EXCEEDED`: Too many requests
- `INVALID_API_KEY`: Authentication failed
- `INSUFFICIENT_CREDIT`: Not enough account balance

## Core Endpoints

### 1. Service Information

#### List Available Services
```http
GET /services
```

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "IBAN Check",
            "slug": "iban-check",
            "category": "Financial",
            "price": 500,
            "currency": "IRR",
            "description": "Validate IBAN and get account holder information",
            "requires_sms": false,
            "processing_time": "1-30 seconds",
            "fields": [
                {
                    "name": "iban",
                    "type": "string",
                    "required": true,
                    "pattern": "^IR\\d{22}$",
                    "description": "24-character Iranian IBAN"
                }
            ]
        }
    ],
    "meta": {
        "total": 52,
        "per_page": 20,
        "current_page": 1
    }
}
```

#### Get Service Details
```http
GET /services/{slug}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "IBAN Check",
        "slug": "iban-check",
        "price": 500,
        "fields": [...],
        "sample_request": {
            "iban": "IR123456789012345678901234"
        },
        "sample_response": {
            "iban": "IR123456789012345678901234",
            "bank_name": "Bank Mellat",
            "account_holder": "John Doe",
            "is_valid": true
        }
    }
}
```

### 2. Service Requests

#### Create Service Request
```http
POST /services/{slug}/request
```

**Request Body:**
```json
{
    "iban": "IR123456789012345678901234",
    "mobile": "09123456789"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "request_id": "req_ABC123DEF456",
        "status": "pending",
        "payment_required": true,
        "payment_amount": 500,
        "payment_url": "https://pishkhanak.com/payment/req_ABC123DEF456",
        "expires_at": "2024-01-15T11:30:00Z"
    }
}
```

#### Get Request Status
```http
GET /requests/{request_id}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "request_id": "req_ABC123DEF456",
        "status": "completed",
        "service": "iban-check",
        "created_at": "2024-01-15T10:30:00Z",
        "completed_at": "2024-01-15T10:30:15Z",
        "result": {
            "iban": "IR123456789012345678901234",
            "bank_name": "Bank Mellat",
            "account_holder": "احمد احمدی",
            "is_valid": true,
            "inquiry_date": "2024-01-15 10:30:15"
        }
    }
}
```

## Service Categories

### Financial Services

#### IBAN Check
```http
POST /services/iban-check/request
Content-Type: application/json

{
    "iban": "IR123456789012345678901234"
}
```

#### Card to Account Mapping
```http
POST /services/card-account/request

{
    "card_number": "6037991234567890",
    "mobile": "09123456789"
}
```

#### Credit Score Inquiry
```http
POST /services/credit-score/request

{
    "national_code": "1234567890",
    "mobile": "09123456789",
    "birth_date": "1990-01-01"
}
```

### Vehicle Services

#### Traffic Violations
```http
POST /services/traffic-violations/request

{
    "plate_number": "12A345-67",
    "national_code": "1234567890"
}
```

#### Vehicle Information
```http
POST /services/vehicle-info/request

{
    "plate_number": "12A345-67",
    "national_code": "1234567890"
}
```

### Identity Services

#### National ID Verification
```http
POST /services/national-id-check/request

{
    "national_code": "1234567890",
    "mobile": "09123456789"
}
```

#### Passport Status
```http
POST /services/passport-status/request

{
    "passport_number": "A12345678",
    "national_code": "1234567890"
}
```

## Payment Integration

### Check Payment Status
```http
GET /payments/{transaction_id}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "transaction_id": "txn_123456789",
        "status": "completed",
        "amount": 500,
        "gateway": "jibit",
        "paid_at": "2024-01-15T10:29:45Z",
        "receipt_url": "https://pishkhanak.com/receipt/txn_123456789"
    }
}
```

### Process Guest Payment
```http
POST /payments/guest

{
    "service_slug": "iban-check",
    "mobile": "09123456789",
    "amount": 500,
    "gateway": "jibit"
}
```

## SMS Verification Services

For services that require SMS verification:

### Send SMS Token
```http
POST /services/{slug}/sms/send

{
    "national_code": "1234567890",
    "mobile": "09123456789"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "verification_id": "sms_ABC123DEF",
        "message": "SMS token sent to 09123456789",
        "expires_at": "2024-01-15T10:35:00Z"
    }
}
```

### Verify SMS Token
```http
POST /services/{slug}/sms/verify

{
    "verification_id": "sms_ABC123DEF",
    "code": "12345",
    "original_request_data": {
        "national_code": "1234567890",
        "mobile": "09123456789"
    }
}
```

## AI Services

### Search Services
```http
POST /ai/search

{
    "query": "چگونه IBAN را بررسی کنم؟",
    "language": "persian",
    "limit": 5
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "results": [
            {
                "service": "iban-check",
                "title": "بررسی IBAN",
                "description": "با این سرویس می‌توانید اعتبار IBAN خود را بررسی کنید",
                "relevance_score": 0.95,
                "url": "/services/iban-check"
            }
        ],
        "query_time": "0.15s",
        "suggestions": ["استعلام کارت", "بررسی حساب بانکی"]
    }
}
```

### Generate Content
```http
POST /ai/content/generate

{
    "type": "blog_post",
    "title": "مزایای استعلام آنلاین IBAN",
    "keywords": ["IBAN", "بانک", "استعلام"],
    "language": "persian",
    "length": "medium"
}
```

## WebHooks

### Webhook Configuration
Register webhook URLs in your dashboard to receive real-time updates.

### Webhook Events
- `service.completed`: Service processing completed
- `payment.successful`: Payment completed successfully
- `payment.failed`: Payment failed
- `sms.sent`: SMS verification sent
- `request.expired`: Service request expired

### Webhook Payload
```json
{
    "event": "service.completed",
    "data": {
        "request_id": "req_ABC123DEF456",
        "service_slug": "iban-check",
        "user_id": 123,
        "completed_at": "2024-01-15T10:30:15Z",
        "result": {...}
    },
    "timestamp": "2024-01-15T10:30:15Z",
    "signature": "sha256=..."
}
```

### Verifying Webhooks
```php
$signature = hash_hmac('sha256', $payload, $webhookSecret);
$expected = 'sha256=' . $signature;

if (!hash_equals($expected, $receivedSignature)) {
    throw new InvalidSignatureException();
}
```

## Real-time Updates

### WebSocket Connection
```javascript
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

const echo = new Echo({
    broadcaster: 'reverb',
    key: 'your_reverb_app_key',
    wsHost: 'pishkhanak.com',
    wsPort: 80,
    wssPort: 443,
    forceTLS: true
});

// Listen for service updates
echo.private(`service-request.${requestId}`)
    .listen('ServiceProcessingUpdate', (e) => {
        console.log('Status:', e.status);
        console.log('Progress:', e.progress);
    });
```

## SDK Examples

### PHP SDK
```php
use Pishkhanak\SDK\PishkhanakClient;

$client = new PishkhanakClient('your_api_token');

// Check IBAN
$result = $client->services()->ibanCheck([
    'iban' => 'IR123456789012345678901234'
]);

if ($result->isSuccessful()) {
    echo "Bank: " . $result->data['bank_name'];
    echo "Valid: " . ($result->data['is_valid'] ? 'Yes' : 'No');
}
```

### JavaScript SDK
```javascript
import { PishkhanakClient } from '@pishkhanak/sdk';

const client = new PishkhanakClient('your_api_token');

// Async/await
try {
    const result = await client.services.ibanCheck({
        iban: 'IR123456789012345678901234'
    });
    
    console.log(result.data);
} catch (error) {
    console.error('API Error:', error.message);
}
```

### Python SDK
```python
from pishkhanak import PishkhanakClient

client = PishkhanakClient('your_api_token')

# Check IBAN
result = client.services.iban_check({
    'iban': 'IR123456789012345678901234'
})

if result.success:
    print(f"Bank: {result.data['bank_name']}")
    print(f"Valid: {result.data['is_valid']}")
```

## Testing

### Test API Keys
Use these test API keys for development:
- Test Key: `test_sk_1234567890abcdef`
- Test Environment: `https://test.pishkhanak.com/api/v1`

### Test Data
```json
{
    "test_iban": "IR123456789012345678901234",
    "test_national_code": "1234567890",
    "test_mobile": "09123456789",
    "test_plate_number": "12A345-67"
}
```

### Postman Collection
Download our Postman collection: [Pishkhanak API Collection](https://pishkhanak.com/api/postman.json)

## Rate Limiting Best Practices

### Exponential Backoff
```javascript
async function callWithBackoff(apiCall, maxRetries = 3) {
    for (let i = 0; i < maxRetries; i++) {
        try {
            return await apiCall();
        } catch (error) {
            if (error.status === 429 && i < maxRetries - 1) {
                const delay = Math.pow(2, i) * 1000; // 1s, 2s, 4s
                await new Promise(resolve => setTimeout(resolve, delay));
                continue;
            }
            throw error;
        }
    }
}
```

### Batch Requests
```http
POST /batch
Content-Type: application/json

{
    "requests": [
        {
            "method": "POST",
            "url": "/services/iban-check/request",
            "body": {"iban": "IR123456789012345678901234"}
        },
        {
            "method": "POST", 
            "url": "/services/card-account/request",
            "body": {"card_number": "6037991234567890", "mobile": "09123456789"}
        }
    ]
}
```

## Security Considerations

### HTTPS Only
All API requests must use HTTPS. HTTP requests will be rejected.

### IP Whitelisting
Configure IP whitelisting in your dashboard for additional security.

### API Key Rotation
Rotate your API keys regularly and use different keys for different environments.

### Request Signing
For sensitive operations, implement request signing:
```php
$signature = hash_hmac('sha256', $requestBody, $apiSecret);
$headers['X-Signature'] = 'sha256=' . $signature;
```

## Monitoring and Analytics

### API Usage Dashboard
Access your API usage statistics at `/admin/api-analytics`

### Metrics Available
- Request volume and trends
- Response times and performance
- Error rates by endpoint
- Popular services usage
- Credit consumption tracking

### Alerts
Set up alerts for:
- High error rates (>5%)
- Slow response times (>30s)
- Rate limit violations
- Credit balance low (<1000 IRR)

## Support and Resources

### Documentation
- **API Reference**: https://docs.pishkhanak.com/api
- **SDK Documentation**: https://docs.pishkhanak.com/sdks
- **Integration Guides**: https://docs.pishkhanak.com/guides

### Support Channels
- **Email**: api-support@pishkhanak.com
- **Telegram**: @PishkhanakAPISupport
- **Discord**: https://discord.gg/pishkhanak
- **GitHub Issues**: https://github.com/pishkhanak/api-issues

### Status Page
Monitor API status and incidents: https://status.pishkhanak.com

---

## Changelog

### v1.2.0 (2024-01-15)
- Added batch request support
- Improved error messages
- Added WebSocket real-time updates
- New AI search endpoints

### v1.1.0 (2023-12-01)
- Added SMS verification services
- Enhanced payment gateway integration
- Improved rate limiting
- Added webhook support

### v1.0.0 (2023-11-01)
- Initial API release
- Core financial services
- Payment integration
- Basic authentication