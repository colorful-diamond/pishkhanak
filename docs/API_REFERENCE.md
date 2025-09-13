# 🔌 API Reference - Pishkhanak Platform

> **Comprehensive API documentation for financial services integration and platform interaction**

## 📋 Table of Contents
- [Authentication](#authentication)
- [Core Endpoints](#core-endpoints)
- [Service APIs](#service-apis)
- [Payment Integration](#payment-integration)
- [Webhook APIs](#webhook-apis)
- [Bot Communication](#bot-communication)
- [Error Handling](#error-handling)

---

## 🔐 Authentication

### **OTP-Based Authentication**
The platform uses SMS-based OTP authentication via Finnotech integration.

#### **Request OTP**
```http
POST /auth/request-otp
Content-Type: application/json

{
    "mobile": "09123456789",
    "captcha": "resolved_captcha_value"
}
```

**Response**:
```json
{
    "status": "success", 
    "message": "OTP sent successfully",
    "expires_in": 120,
    "request_id": "otp_request_uuid"
}
```

#### **Verify OTP**
```http
POST /auth/verify-otp
Content-Type: application/json

{
    "mobile": "09123456789",
    "otp": "123456",
    "request_id": "otp_request_uuid"
}
```

**Response**:
```json
{
    "status": "success",
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "user": {
        "id": 1,
        "mobile": "09123456789",
        "wallet_balance": 50000
    }
}
```

### **API Authentication Headers**
```http
Authorization: Bearer {jwt_token}
Accept: application/json
Content-Type: application/json
X-Requested-With: XMLHttpRequest
```

---

## 🌐 Core Endpoints

### **Service Listing**
```http
GET /api/services
Authorization: Bearer {token}
```

**Response**:
```json
{
    "data": [
        {
            "id": 1,
            "slug": "credit-score-rating",
            "title": "استعلام اعتبار سنجی",
            "price": 20000,
            "currency": "IRT",
            "description": "بررسی وضعیت اعتباری از طریق بانک مرکزی",
            "category": {
                "id": 1,
                "name": "خدمات بانکی"
            },
            "providers": ["rade", "nics24"],
            "processing_time": "2-5 minutes"
        }
    ],
    "pagination": {
        "current_page": 1,
        "total_pages": 5,
        "total_items": 67
    }
}
```

### **Service Details**
```http
GET /api/services/{slug}
Authorization: Bearer {token}
```

**Response**:
```json
{
    "data": {
        "id": 1,
        "slug": "credit-score-rating", 
        "title": "استعلام اعتبار سنجی",
        "price": 20000,
        "fields": [
            {
                "name": "national_code",
                "type": "string",
                "label": "کد ملی",
                "required": true,
                "validation": "iranian_national_code"
            },
            {
                "name": "mobile",
                "type": "string", 
                "label": "شماره موبایل",
                "required": true,
                "validation": "iranian_mobile"
            }
        ],
        "providers": [
            {
                "name": "rade",
                "status": "active",
                "success_rate": 0.95
            }
        ]
    }
}
```

---

## ⚙️ Service APIs

### **Service Request Submission**
```http
POST /api/services/{slug}/request
Authorization: Bearer {token}
Content-Type: application/json

{
    "national_code": "1234567890",
    "mobile": "09123456789",
    "provider": "rade"  // optional, auto-selected if omitted
}
```

**Response**:
```json
{
    "status": "processing",
    "request_id": "req_ABC123DEF456",
    "estimated_completion": "2025-09-08T12:35:00Z",
    "tracking_url": "/api/requests/req_ABC123DEF456",
    "message": "درخواست شما در حال پردازش است"
}
```

### **Request Status Tracking**
```http
GET /api/requests/{request_id}
Authorization: Bearer {token}
```

**Processing Response**:
```json
{
    "status": "processing",
    "request_id": "req_ABC123DEF456",
    "progress": 65,
    "current_step": "solving_captcha",
    "estimated_completion": "2025-09-08T12:33:00Z"
}
```

**Completed Response**:
```json
{
    "status": "completed",
    "request_id": "req_ABC123DEF456", 
    "result": {
        "credit_score": 750,
        "risk_level": "low",
        "bank_relationships": [
            {
                "bank": "بانک ملی ایران",
                "account_status": "active",
                "last_transaction": "2025-09-01"
            }
        ],
        "recommendations": [
            "وضعیت اعتباری مناسب برای دریافت تسهیلات"
        ]
    },
    "metadata": {
        "provider": "rade",
        "processing_time": 127,
        "confidence": 0.98
    }
}
```

### **Service Preview (Guest Mode)**
```http
POST /api/services/preview/{service_id}
Content-Type: application/json

{
    "amount": 200000,  // Wallet charge amount
    "return_url": "https://example.com/callback"
}
```

**Response**:
```json
{
    "status": "success",
    "preview_id": "preview_XYZ789",
    "payment_url": "https://pishkhanak.com/guest/payment/preview_XYZ789",
    "expires_at": "2025-09-08T13:00:00Z"
}
```

---

## 💳 Payment Integration

### **Wallet Charge Request**
```http
POST /api/user/wallet/charge
Authorization: Bearer {token}
Content-Type: application/json

{
    "amount": 100000,
    "gateway": "jibit",  // jibit, sep, saman
    "return_url": "https://yourapp.com/payment/callback"
}
```

**Response**:
```json
{
    "status": "pending",
    "transaction_id": "txn_ABC123",
    "gateway_url": "https://api.jibit.ir/ppg/payments/pay/ABC123",
    "expires_at": "2025-09-08T12:45:00Z"
}
```

### **Payment Gateway Callback**
```http
POST /api/payments/callback/{gateway}
Content-Type: application/json

{
    "transaction_id": "txn_ABC123",
    "gateway_reference": "jibit_ref_456789",
    "status": "success",
    "amount": 100000,
    "signature": "gateway_signature_hash"
}
```

### **Transaction History**
```http
GET /api/user/transactions
Authorization: Bearer {token}
```

**Response**:
```json
{
    "data": [
        {
            "id": "txn_ABC123",
            "type": "wallet_charge",
            "amount": 100000,
            "status": "completed",
            "gateway": "jibit",
            "created_at": "2025-09-08T11:30:00Z",
            "description": "شارژ کیف پول"
        },
        {
            "id": "req_DEF456", 
            "type": "service_payment",
            "amount": -20000,
            "status": "completed",
            "service": "استعلام اعتبار سنجی",
            "created_at": "2025-09-08T11:35:00Z"
        }
    ]
}
```

---

## 🔗 Webhook APIs

### **Payment Status Webhook**
**Endpoint**: `POST /api/webhooks/payment/{gateway}`
**Headers**: `X-Webhook-Signature: {hmac_signature}`

```json
{
    "transaction_id": "txn_ABC123",
    "status": "completed",
    "amount": 100000,
    "gateway_reference": "jibit_ref_456789",
    "timestamp": "2025-09-08T12:00:00Z"
}
```

### **Service Completion Webhook** 
**Endpoint**: `POST /api/webhooks/service-completed`
**Headers**: `X-API-Key: {internal_api_key}`

```json
{
    "request_id": "req_ABC123DEF456",
    "service_slug": "credit-score-rating",
    "status": "completed",
    "result_data": { /* service specific result */ },
    "processing_time": 127,
    "provider": "rade"
}
```

---

## 🤖 Bot Communication

### **Local API Server Interface**
**Base URL**: `http://localhost:9999`
**Authentication**: Internal API key

#### **Service Request Processing**
```http
POST /api/process-service
Content-Type: application/json
Authorization: Bearer {internal_api_key}

{
    "service": "credit-score-rating",
    "provider": "rade",
    "data": {
        "national_code": "1234567890",
        "mobile": "09123456789"
    },
    "request_id": "req_ABC123DEF456",
    "callback_url": "https://pishkhanak.com/api/webhooks/service-completed"
}
```

**Bot Response**:
```json
{
    "status": "processing",
    "bot_request_id": "bot_req_789",
    "estimated_time": 180,
    "provider_status": "active"
}
```

#### **CAPTCHA Solving Interface**
```http
POST /api/solve-captcha
Content-Type: multipart/form-data

image: [binary_image_data]
type: "persian_digits"
```

**Response**:
```json
{
    "status": "success",
    "solution": "12456",
    "confidence": 0.95,
    "processing_time": 234
}
```

### **Provider Health Check**
```http
GET /api/providers/status
Authorization: Bearer {internal_api_key}
```

**Response**:
```json
{
    "providers": {
        "rade": {
            "status": "online",
            "response_time": 1.23,
            "success_rate": 0.97,
            "last_check": "2025-09-08T12:00:00Z"
        },
        "nics24": {
            "status": "degraded", 
            "response_time": 5.67,
            "success_rate": 0.78,
            "last_error": "Timeout after 30s"
        }
    }
}
```

---

## 🚨 Error Handling

### **Standard Error Response Format**
```json
{
    "status": "error",
    "error": {
        "code": "VALIDATION_ERROR",
        "message": "ورودی های ارسالی معتبر نیستند",
        "details": {
            "national_code": ["کد ملی وارد شده معتبر نمی باشد"],
            "mobile": ["شماره موبایل باید با 09 شروع شود"]
        }
    },
    "request_id": "req_error_123"
}
```

### **Common Error Codes**

#### **Authentication Errors**
- `AUTH_REQUIRED` (401) - Authentication token missing or invalid
- `OTP_EXPIRED` (400) - OTP verification code expired
- `OTP_INVALID` (400) - Incorrect OTP code provided
- `RATE_LIMITED` (429) - Too many requests, slow down

#### **Validation Errors**
- `VALIDATION_ERROR` (422) - Input validation failed
- `NATIONAL_CODE_INVALID` (400) - Iranian national code format invalid
- `MOBILE_INVALID` (400) - Mobile number format invalid
- `INSUFFICIENT_BALANCE` (400) - Wallet balance too low

#### **Service Errors**
- `SERVICE_UNAVAILABLE` (503) - Service temporarily unavailable
- `PROVIDER_ERROR` (500) - External provider returned error
- `PROCESSING_TIMEOUT` (408) - Request processing timed out
- `CAPTCHA_FAILED` (400) - CAPTCHA solving failed

#### **Payment Errors**
- `PAYMENT_FAILED` (400) - Payment gateway rejected transaction
- `GATEWAY_TIMEOUT` (408) - Payment gateway timeout
- `INVALID_AMOUNT` (400) - Invalid payment amount
- `DUPLICATE_TRANSACTION` (409) - Transaction already processed

### **Error Handling Best Practices**

#### **Client-Side Handling**
```javascript
// Recommended error handling pattern
try {
    const response = await fetch('/api/services/credit-score-rating/request', {
        method: 'POST',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(requestData)
    });
    
    const data = await response.json();
    
    if (!response.ok) {
        handleApiError(data.error, response.status);
        return;
    }
    
    // Handle successful response
    processServiceResult(data);
    
} catch (error) {
    // Handle network errors
    handleNetworkError(error);
}
```

#### **Retry Logic**
- **Transient Errors** (5xx, timeouts) - Exponential backoff retry
- **Rate Limiting** (429) - Respect Retry-After header
- **Provider Errors** - Automatic failover to backup providers
- **Authentication** (401) - Refresh token and retry once

---

## 📊 Rate Limiting

### **Rate Limit Headers**
```http
X-RateLimit-Limit: 100
X-RateLimit-Remaining: 95  
X-RateLimit-Reset: 1694174400
Retry-After: 60
```

### **Rate Limit Rules**
- **Authenticated Users**: 100 requests/hour per user
- **Guest Users**: 10 requests/hour per IP
- **Service Requests**: 5 concurrent requests per user
- **OTP Requests**: 3 requests/5 minutes per mobile number

---

## 🔄 API Versioning

### **Version Strategy**
- **Current Version**: v1 (default)
- **Header Versioning**: `Accept: application/vnd.pishkhanak.v1+json`
- **Backward Compatibility**: 6 months minimum support
- **Deprecation Notice**: 90 days advance warning

### **Version-Specific Endpoints**
```http
# Default (v1)
GET /api/services

# Explicit versioning  
GET /api/v1/services
GET /api/v2/services  (future)
```

---

*📅 Last Updated: 2025-09-08 | 📖 API Reference v1.0 | 🔄 Generated via SuperClaude /sc:index*