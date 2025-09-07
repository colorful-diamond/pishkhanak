# NICS24 Provider for Credit Score Service

This provider integrates with the NICS24 (اعتباریتو) platform to provide credit score inquiry services.

## Overview

The NICS24 provider implements the complete credit score inquiry flow:
1. **Authentication**: Handles captcha-based login to NICS24 platform
2. **OTP Sending**: Sends OTP request using ShareReportWithOtp API
3. **OTP Verification**: Verifies OTP with captcha and retrieves credit score
4. **Redis Integration**: Uses Redis for progress updates and OTP polling

## Files Structure

```
nics24/
├── index.js                    # Main provider export with session management
├── config.js                   # Provider configuration
├── login.js                    # Authentication with session persistence
├── checkLogin.js               # Session validation
├── refresh.js                  # Session refresh logic
├── sessionManager.js           # Automatic session monitoring
├── sessions/                   # Directory for saved sessions
│   └── .gitkeep               # Placeholder for session files
├── services/
│   └── creditScoreService.js   # Main credit score service
└── README.md                   # This file
```

## Configuration

Edit `config.js` to update provider settings:

```javascript
export const nics24Config = {
  users: [
    {
      username: "your_nics24_username",
      password: "your_nics24_password",
      description: "NICS24 primary user account - UPDATE WITH REAL CREDENTIALS"
    },
    {
      username: "backup_username",
      password: "backup_password", 
      description: "NICS24 backup user account - UPDATE WITH REAL CREDENTIALS"
    }
  ],
  provider: "nics24",
  baseUrl: "https://etebarito.nics24.ir",
  endpoints: {
    login: "/login-username",
    loginWithNationalCode: "/login",
    sendOtp: "/api/ShareReportWithOtp/SendOtp-payment",
    getCaptcha: "/api/Captcha/GetCaptcha",
    verifyOtp: "/api/ShareReportWithOtp",
    dashboard: "/pishkhan"
  },
  captchaApiUrl: "http://localhost:9090/predict",
  sessionConfig: {
    maxAge: 2 * 60 * 60 * 1000, // 2 hours in milliseconds
    refreshBeforeExpiry: 30 * 60 * 1000, // Refresh 30 minutes before expiry
    checkInterval: 10 * 60 * 1000 // Check every 10 minutes
  },
  description: "NICS24 Credit Score Provider Configuration"
};
```

⚠️ **Important**: Update the username and password fields with your actual NICS24 credentials!

## Session Management

The NICS24 provider includes comprehensive session management to avoid repeated logins:

### Features

- **Session Persistence**: Browser sessions are saved to disk and reused
- **Automatic Validation**: Sessions are checked for validity before use
- **Auto Refresh**: Sessions are automatically refreshed before expiration
- **Session Monitoring**: Background monitoring of session health
- **Multiple Users**: Support for backup user accounts

### Session Files

Sessions are stored in the `sessions/` directory:
- Format: `{username}_session.json`
- Contains: Browser storage state, cookies, and metadata
- Max Age: 2 hours (configurable)
- Auto-cleanup: Invalid/expired sessions are automatically removed

### Session Manager

The session manager runs in the background to:
- Monitor registered sessions every 10 minutes
- Refresh sessions 30 minutes before expiry
- Clean up invalid sessions automatically

### Usage Examples

```javascript
// Manual session operations
const nics24 = require('./providers/nics24');

// Check if session is valid
const checkResult = await nics24.checkLogin('09123456789');

// Manually refresh session
const refreshResult = await nics24.refresh('09123456789');

// Get session manager status
const status = nics24.getSessionStatus();

// Start/stop session manager
nics24.startSessionManager();
nics24.stopSessionManager();

// Refresh all monitored sessions
await nics24.refreshAllSessions();
```

## API Integration

The provider integrates with these NICS24 API endpoints:

### 1. Send OTP
```
POST /api/ShareReportWithOtp/SendOtp-payment
```
Sends OTP to user's mobile number.

### 2. Get Captcha
```
GET /api/Captcha/GetCaptcha?nocache={timestamp}
```
Retrieves captcha image for verification.

### 3. Verify OTP
```
POST /api/ShareReportWithOtp
```
Verifies OTP with captcha and returns credit score data.

## Usage

### In Credit Score Service

The provider is automatically used when `provider: 'nics24'` is specified:

```javascript
const result = await creditScoreService.handle({
  mobile: '09123456789',
  national_code: '1234567890',
  provider: 'nics24',
  requestHash: 'unique-request-hash'
});
```

### API Request Example

```bash
curl -X POST http://localhost:9999/api/services/credit-score-rating \
  -H "Content-Type: application/json" \
  -d '{
    "mobile": "09123456789",
    "national_code": "1234567890",
    "provider": "nics24",
    "requestHash": "unique-request-hash"
  }'
```

## Flow Description

1. **Initialize**: Create browser session and navigate to NICS24
2. **Send OTP**: Submit national code and mobile to receive OTP
3. **Wait for OTP**: Poll Redis for OTP submission by user
4. **Get Captcha**: Retrieve and solve captcha using local AI service
5. **Verify**: Submit OTP + captcha to get credit score
6. **Return Result**: Format and return credit score data

## Dependencies

- **Playwright**: For browser automation
- **Redis**: For progress updates and OTP polling
- **Local Captcha API**: Running on `localhost:9090` for captcha solving

## Redis Integration

The provider uses Redis for:
- **Progress Updates**: `service_progress:{requestHash}`
- **OTP Polling**: `otp_submission:{requestHash}`

## Error Handling

The provider handles various error scenarios:
- Network timeouts
- Captcha solving failures
- Invalid OTP codes
- API response errors
- Browser automation failures

## Captcha Solving

The provider requires a local captcha solving service running on port 8989:

```bash
# Expected API format
POST http://localhost:9090/predict
Content-Type: application/json

{
  "image": "base64_encoded_image_data"
}
```

Response format:
```json
{
  "prediction": "solved_captcha_text"
}
```

## Debugging

Enable debug mode by setting environment variable:
```bash
DEBUG_MODE=true
```

This will enable detailed logging for:
- HTTP requests/responses
- Browser automation steps
- Redis operations
- Error details

## Notes

- The provider requires valid NICS24 access
- Captcha solving accuracy affects success rate
- OTP timeout is set to 5 minutes
- Maximum 3 retry attempts for failed requests
- Browser runs in headless mode for production