# 🚀 Pishkhanak Local API System

A comprehensive local API system for handling inquiry services like credit score rating through automated browser interactions and AI-powered captcha solving.

## 📋 Table of Contents

- [Overview](#overview)
- [Architecture](#architecture)
- [Installation](#installation)
- [Usage](#usage)
- [Adding New Services](#adding-new-services)
- [API Documentation](#api-documentation)
- [Troubleshooting](#troubleshooting)
- [Security](#security)

## 🎯 Overview

The Local API system provides a secure, localhost-only API server that handles complex inquiry services requiring browser automation, captcha solving, and OTP verification. It seamlessly integrates with the main Laravel application while keeping sensitive operations isolated.

### ✨ Key Features

- **🔒 Localhost-only security** - Only accepts connections from 127.0.0.1
- **🤖 AI-powered captcha solving** - Uses Google Gemini AI to solve captchas
- **📱 OTP verification flow** - Beautiful UI for SMS verification
- **⚡ Dynamic service loading** - Automatically loads services based on directory structure
- **🎨 Integrated UI** - Consistent styling with the main website
- **📊 Comprehensive logging** - Detailed logging for debugging and monitoring
- **🔄 Graceful error handling** - User-friendly error messages and recovery

## 🏗️ Architecture

```
┌─────────────────┐    HTTP    ┌─────────────────┐    Dynamic   ┌─────────────────┐
│   Laravel App   │ ---------> │  Local API      │   Loading    │    Services     │
│                 │            │  Server         │ -----------> │                 │
│ - Controllers   │            │                 │              │ - credit-score  │
│ - Routes        │            │ - Port 9999     │              │ - loan-inquiry  │
│ - Views         │            │ - Express.js    │              │ - ...           │
│ - Error Handling│            │ - Security      │              │                 │
└─────────────────┘            └─────────────────┘              └─────────────────┘
        │                              │                                │
        │ OTP Verification             │ Service Results                │ Browser
        │                              │                                │ Automation
        v                              v                                v
┌─────────────────┐            ┌─────────────────┐              ┌─────────────────┐
│  OTP Verification│            │   Response      │              │   External      │
│    Page         │            │   Handler       │              │   Services      │
│                 │            │                 │              │                 │
│ - Beautiful UI  │            │ - Success/Error │              │ - Finnotech     │
│ - Real-time     │            │ - Redirects     │              │ - Rade.ir       │
│ - Timer         │            │ - Data Storage  │              │ - ...           │
└─────────────────┘            └─────────────────┘              └─────────────────┘
```

## 🛠️ Installation

### Prerequisites

- **Node.js 18+** (for local API server)
- **PHP 8.1+** (for Laravel application)
- **Playwright** (for browser automation)
- **Google Gemini API Key** (for captcha solving)

### Step 1: Install Dependencies

```bash
# Navigate to inquiry provider directory
cd pishkhanak.com/bots/inquiry-provider

# Install Node.js dependencies
npm install

# Install Playwright browsers
npx playwright install chromium
```

### Step 2: Environment Configuration

Create or update `.env` file in `bots/inquiry-provider/`:

```env
# Google Gemini AI Configuration
GEMINI_API_KEY=your_gemini_api_key_here

# Local API Configuration
LOCAL_API_PORT=9999
DEBUG_MODE=false

# Browser Configuration
HEADLESS_MODE=true
BROWSER_TIMEOUT=30000
```

### Step 3: Make Startup Script Executable

```bash
# From Laravel root directory
chmod +x start-local-api.sh
```

## 🚀 Usage

### Starting the Local API Server

```bash
# Start the server
./start-local-api.sh start

# Check server status
./start-local-api.sh status

# View logs
./start-local-api.sh logs

# Stop the server
./start-local-api.sh stop

# Restart the server
./start-local-api.sh restart
```

### Server Endpoints

Once running, the server provides these endpoints:

- **Health Check**: `GET http://127.0.0.1:9999/health`
- **List Services**: `GET http://127.0.0.1:9999/api/services`
- **Execute Service**: `POST http://127.0.0.1:9999/api/services/{service-slug}`

### Integration with Laravel

The system automatically integrates with your Laravel service system. Services using the local API should extend `BaseLocalApiController`:

```php
// Example: Credit Score Rating Controller
class CreditScoreRatingLocalController extends BaseLocalApiController
{
    protected function configureService(): void
    {
        $this->serviceSlug = 'credit-score-rating';
        $this->requiresOtp = true;
        $this->requiredFields = ['mobile', 'national_code'];
        $this->validationRules = [
            'mobile' => ['required', 'string', new IranianMobile()],
            'national_code' => ['required', 'string', new IranianNationalCode()],
        ];
    }
}
```

### User Flow

1. **User submits form** with mobile and national code
2. **Laravel controller** validates input and calls local API
3. **Local API** loads service module and processes request
4. **Service module** handles browser automation and captcha solving
5. **If SMS required**: User redirected to OTP verification page
6. **User enters OTP** and submits verification
7. **System returns results** or error messages

## 🔧 Adding New Services

### Step 1: Create Service Directory

```bash
mkdir pishkhanak.com/bots/inquiry-provider/services/your-service-name
```

### Step 2: Create Service Module

Create `index.js` in your service directory:

```javascript
// pishkhanak.com/bots/inquiry-provider/services/your-service-name/index.js

import { chromium } from 'playwright';

export async function handle(data) {
    const { mobile, national_code, otp, hash } = data;
    
    try {
        // Your service logic here
        const browser = await chromium.launch({ headless: true });
        const page = await browser.newPage();
        
        // Handle initial request or OTP verification
        if (otp && hash) {
            return await handleOtpVerification(page, data);
        } else {
            return await handleInitialRequest(page, data);
        }
        
    } catch (error) {
        return {
            status: 'error',
            code: 'SERVICE_ERROR',
            message: error.message
        };
    }
}

async function handleInitialRequest(page, data) {
    // Initial request logic
    // Return SMS_SENT for OTP services or completed result
    
    return {
        status: 'success',
        code: 'SMS_SENT',
        message: 'SMS has been sent',
        hash: 'generated-verification-hash',
        expiry: Math.floor(Date.now() / 1000) + 300 // 5 minutes
    };
}

async function handleOtpVerification(page, data) {
    // OTP verification logic
    
    return {
        status: 'success',
        code: 'SERVICE_COMPLETED',
        message: 'Service completed successfully',
        data: {
            // Your service results
        }
    };
}
```

### Step 3: Create Laravel Controller

```php
// app/Http/Controllers/Services/YourServiceController.php

class YourServiceController extends BaseLocalApiController
{
    protected function configureService(): void
    {
        $this->serviceSlug = 'your-service-name';
        $this->requiresOtp = true; // or false
        
        $this->requiredFields = [
            'mobile',
            'national_code'
            // Add your required fields
        ];

        $this->validationRules = [
            'mobile' => ['required', 'string', new IranianMobile()],
            'national_code' => ['required', 'string', new IranianNationalCode()],
            // Add your validation rules
        ];

        $this->validationMessages = [
            'mobile.required' => 'شماره موبایل الزامی است.',
            'national_code.required' => 'کد ملی الزامی است.',
            // Add your validation messages
        ];
    }
}
```

### Step 4: Register in Service Factory

Update `ServiceControllerFactory.php`:

```php
private static $serviceMapping = [
    // ... existing services
    'your-service-name' => YourServiceController::class,
];
```

### Step 5: Test Your Service

```bash
# Restart local API server
./start-local-api.sh restart

# Check if service is detected
curl http://127.0.0.1:9999/api/services

# Test your service
curl -X POST http://127.0.0.1:9999/api/services/your-service-name \
  -H "Content-Type: application/json" \
  -d '{"mobile": "09123456789", "national_code": "1234567890"}'
```

## 📚 API Documentation

### Response Formats

All API responses follow this structure:

```json
{
  "status": "success|error",
  "code": "RESPONSE_CODE",
  "message": "Human readable message",
  "data": {} // Optional, for successful responses
}
```

### Success Codes

- `SMS_SENT` - SMS verification required
- `OTP_VERIFIED` - OTP verified successfully
- `SERVICE_COMPLETED` - Service completed without OTP
- `CREDIT_SCORE_COMPLETED` - Credit score specific completion

### Error Codes

- `INVALID_NATIONAL_CODE` - Invalid national code
- `SERVICE_UNAVAILABLE` - External service unavailable
- `INVALID_OTP` - Incorrect OTP entered
- `OTP_EXPIRED` - OTP has expired
- `CAPTCHA_MAX_RETRIES` - Captcha solving failed multiple times
- `SERVICE_ERROR` - General service error

### Health Check Response

```json
{
  "status": "success",
  "message": "Local API server is running",
  "timestamp": "2024-01-01T12:00:00.000Z",
  "uptime": 3600
}
```

### Services List Response

```json
{
  "status": "success",
  "services": [
    {
      "slug": "credit-score-rating",
      "available": true
    }
  ],
  "count": 1
}
```

## 🐛 Troubleshooting

### Common Issues

#### Server Won't Start

```bash
# Check if port 9999 is in use
lsof -i :9999

# Check Node.js version
node --version  # Should be 18+

# Check dependencies
cd bots/inquiry-provider && npm list
```

#### Health Check Fails

```bash
# Check server logs
./start-local-api.sh logs

# Test with verbose curl
curl -v http://127.0.0.1:9999/health
```

#### Service Not Found

```bash
# List available services
curl http://127.0.0.1:9999/api/services

# Check service directory structure
ls -la bots/inquiry-provider/services/
```

#### Captcha Solving Fails

1. Check Gemini API key is valid
2. Verify proxy settings if using SOCKS5
3. Check debug logs for specific errors

#### OTP Verification Issues

1. Verify hash parameter is correct
2. Check session data in Laravel
3. Ensure timer hasn't expired

### Debug Mode

Enable debug mode for detailed logging:

```env
DEBUG_MODE=true
```

Then restart the server and check logs:

```bash
./start-local-api.sh restart
./start-local-api.sh logs
```

### Log Files

- **Server logs**: `storage/logs/local-api-server.log`
- **Laravel logs**: `storage/logs/laravel.log`
- **Service-specific logs**: Check console output in debug mode

## 🔒 Security

### Security Features

- **Localhost-only access** - Server only binds to 127.0.0.1
- **IP validation** - Rejects non-localhost connections
- **Rate limiting** - 100 requests per minute per IP
- **Input sanitization** - Validates and sanitizes all inputs
- **CORS protection** - Strict CORS policy
- **Process isolation** - Services run in isolated processes

### Security Best Practices

1. **Never expose port 9999** to external networks
2. **Keep API keys secure** in environment files
3. **Regularly update dependencies** to patch vulnerabilities
4. **Monitor logs** for suspicious activity
5. **Use HTTPS** for the main Laravel application

### Production Considerations

- Run server with process management (PM2, systemd)
- Set up log rotation for server logs
- Monitor server health and restart on failures
- Consider running in Docker container for isolation

## 📞 Support

For issues or questions:

1. Check this documentation
2. Review server logs
3. Test with debug mode enabled
4. Contact development team with logs and error details

## 🔄 Updates

To update the system:

1. Pull latest changes
2. Update dependencies: `cd bots/inquiry-provider && npm update`
3. Restart server: `./start-local-api.sh restart`
4. Test functionality

---

**Made with ❤️ by Pishkhanak Team** 