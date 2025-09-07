# Finnotech SMS Authorization System

This document describes the comprehensive SMS authorization system implemented for Finnotech services that require AUTHORIZATION SMS authentication.

## Overview

The system provides a complete solution for managing SMS-based authentication tokens for Finnotech services, including:

- Automatic token acquisition and storage
- Token refresh and expiration handling
- Redis-based token storage with cleanup
- Service-specific implementations for all SMS auth scopes
- Comprehensive API endpoints for token management

## Supported Services

The system supports all Finnotech services that use AUTHORIZATION SMS:

### Credit Services
- `credit:sms-facility-inquiry:get` - Credit SMS Facility Inquiry
- `credit:sms-sayad-issuer-inquiry-cheque:post` - Sayad Issuer Inquiry Cheque
- `credit:sms-back-cheques:get` - Credit SMS Back Cheques
- `credit:sms-sayady-cheque-inquiry:get` - Sayady Cheque Inquiry SMS
- `credit:sms-sayad-accept-cheque:post` - Sayad Accept Cheque SMS
- `credit:sms-sayad-cancel-cheque:post` - Sayad Cancel Cheque SMS

### KYC Services
- `kyc:sms-nid-verification:get` - KYC NID Verification SMS

### Oak Services
- `oak:sms-shahab-inquiry:get` - Oak Shahab Inquiry SMS

## Architecture

### Core Components

1. **SmsAuthorizationService** - Main service for token management
2. **CreditSmsService** - Wrapper for credit-related SMS services
3. **KycSmsService** - Wrapper for KYC SMS services
4. **OakSmsService** - Wrapper for Oak SMS services
5. **FinnotechSmsAuthController** - API controller for token management
6. **CleanupExpiredSmsTokensCommand** - Scheduled command for token cleanup

### Token Storage

Tokens are stored in Redis with the following structure:
- **Key Pattern**: `finnotech:sms_auth:{hash}`
- **Expiration**: Automatic based on token expiry time
- **Data**: JSON containing access token, refresh token, expiry info, and metadata

## Usage

### 1. Basic Flow

The typical flow for using any SMS-authenticated service:

1. **Check Token Status**: Verify if user has valid token for the scope
2. **Authorization**: If no token, redirect user to authorization URL
3. **Token Exchange**: Handle callback and exchange code for token
4. **API Call**: Use token to make authenticated API calls

### 2. Example: Facility Inquiry

```php
use App\Services\Finnotech\CreditSmsService;

// Inject the service
public function __construct(CreditSmsService $creditService)
{
    $this->creditService = $creditService;
}

// Check token status
$tokenStatus = $this->creditService->checkTokenStatus($nationalId, $mobile);

if (!$tokenStatus['credit:sms-facility-inquiry:get']['has_token']) {
    // Generate authorization URL
    $authUrls = $this->creditService->generateAuthorizationUrls($nationalId, $mobile);
    return response()->json([
        'authorization_required' => true,
        'url' => $authUrls['credit:sms-facility-inquiry:get']['url']
    ]);
}

// Make API call
$result = $this->creditService->getFacilityInquiry($nationalId, $mobile, $trackId);
```

### 3. API Endpoints

#### SMS Authorization Management

```
GET /api/finnotech/sms-auth/scopes
POST /api/finnotech/sms-auth/authorize
POST /api/finnotech/sms-auth/callback
POST /api/finnotech/sms-auth/token/check
POST /api/finnotech/sms-auth/token/refresh
POST /api/finnotech/sms-auth/token/revoke
POST /api/finnotech/sms-auth/call
GET /api/finnotech/sms-auth/statistics
```

#### Example Services

```
POST /api/finnotech/examples/facility-inquiry
POST /api/finnotech/examples/kyc-verification
POST /api/finnotech/examples/shahab-inquiry
POST /api/finnotech/examples/token-status
```

## Configuration

### Environment Variables

Add these to your `.env` file:

```env
# Finnotech Configuration
FINNOTECH_BASE_URL=https://api.finnotech.ir
FINNOTECH_SANDBOX_URL=https://sandboxapi.finnotech.ir
FINNOTECH_CLIENT_ID=pishkhanak
FINNOTECH_CLIENT_SECRET=your_secret_here
FINNOTECH_SANDBOX=false

# SMS Auth Configuration
FINNOTECH_SMS_REDIRECT_URI=https://pishkhanak.com/api/finnotech/sms-auth/callback
FINNOTECH_SMS_TOKEN_EXPIRY=60
FINNOTECH_SMS_CLEANUP_SCHEDULE=03:30
```

### Service Configuration

The services are configured in `config/services.php`:

```php
'finnotech' => [
    'base_url' => env('FINNOTECH_BASE_URL', 'https://api.finnotech.ir'),
    'sandbox_url' => env('FINNOTECH_SANDBOX_URL', 'https://sandboxapi.finnotech.ir'),
    'client_id' => env('FINNOTECH_CLIENT_ID', 'pishkhanak'),
    'client_secret' => env('FINNOTECH_CLIENT_SECRET'),
    'sandbox' => env('FINNOTECH_SANDBOX', false),
    
    'sms_auth' => [
        'redirect_uri' => env('FINNOTECH_SMS_REDIRECT_URI'),
        'token_expiry_minutes' => env('FINNOTECH_SMS_TOKEN_EXPIRY', 60),
        'cleanup_schedule' => env('FINNOTECH_SMS_CLEANUP_SCHEDULE', '03:30'),
    ],
],
```

## Token Management

### Automatic Cleanup

Expired tokens are automatically cleaned up nightly at 3:30 AM by the scheduled command:

```bash
php artisan finnotech:cleanup-sms-tokens --force
```

### Manual Operations

```bash
# Run cleanup manually
php artisan finnotech:cleanup-sms-tokens

# Dry run to see what would be cleaned
php artisan finnotech:cleanup-sms-tokens --dry-run

# Force cleanup without confirmation
php artisan finnotech:cleanup-sms-tokens --force
```

## API Usage Examples

### 1. Check Available Scopes

```bash
curl -X GET "https://pishkhanak.com/api/finnotech/sms-auth/scopes"
```

### 2. Generate Authorization URL

```bash
curl -X POST "https://pishkhanak.com/api/finnotech/sms-auth/authorize" \
  -H "Content-Type: application/json" \
  -d '{
    "scope": "credit:sms-facility-inquiry:get",
    "mobile": "09123456789",
    "national_id": "1234567890"
  }'
```

### 3. Check Token Status

```bash
curl -X POST "https://pishkhanak.com/api/finnotech/sms-auth/token/check" \
  -H "Content-Type: application/json" \
  -d '{
    "scope": "credit:sms-facility-inquiry:get",
    "mobile": "09123456789",
    "national_id": "1234567890"
  }'
```

### 4. Make Authorized API Call

```bash
curl -X POST "https://pishkhanak.com/api/finnotech/sms-auth/call" \
  -H "Content-Type: application/json" \
  -d '{
    "scope": "credit:sms-facility-inquiry:get",
    "mobile": "09123456789",
    "national_id": "1234567890",
    "query_params": {
      "trackId": "custom-track-123"
    }
  }'
```

### 5. Facility Inquiry Example

```bash
curl -X POST "https://pishkhanak.com/api/finnotech/examples/facility-inquiry" \
  -H "Content-Type: application/json" \
  -d '{
    "national_id": "1234567890",
    "mobile": "09123456789",
    "track_id": "facility-inquiry-123"
  }'
```

## Error Handling

The system provides comprehensive error handling:

### Error Types

1. **Validation Errors** (422) - Invalid input parameters
2. **Authorization Required** (200) - User needs to complete SMS auth
3. **Finnotech Errors** (400) - API-specific errors from Finnotech
4. **Server Errors** (500) - Unexpected system errors

### Example Error Response

```json
{
  "status": "authorization_required",
  "message": "SMS authorization required before making API calls",
  "data": {
    "authorization_url": "https://api.finnotech.ir/dev/v2/oauth2/authorize?...",
    "scope": "credit:sms-facility-inquiry:get",
    "instructions": "Please visit the authorization URL to complete SMS verification"
  }
}
```

## Security Considerations

1. **Token Storage**: Tokens are stored in Redis with expiration
2. **Rate Limiting**: All endpoints have rate limiting configured
3. **Validation**: Comprehensive input validation on all endpoints
4. **Logging**: All operations are logged for audit purposes
5. **Cleanup**: Automatic cleanup of expired tokens

## Monitoring

### Logging

All operations are logged with appropriate context:

```php
Log::info('SMS auth token stored', [
    'scope' => $scope,
    'national_id' => $nationalId,
    'mobile' => $mobile,
    'expires_at' => $expirationTime
]);
```

### Statistics

Get token statistics (admin endpoint):

```bash
curl -X GET "https://pishkhanak.com/api/finnotech/sms-auth/statistics" \
  -H "Authorization: Bearer your_admin_token"
```

## Development and Testing

### Service Dependencies

The services use dependency injection:

```php
// In your controller
public function __construct(
    CreditSmsService $creditService,
    KycSmsService $kycService,
    OakSmsService $oakService
) {
    // Services are automatically injected
}
```

### Testing Authorization Flow

1. Use the example endpoints to test the complete flow
2. Check token status before and after authorization
3. Verify token refresh functionality
4. Test token cleanup manually

## Troubleshooting

### Common Issues

1. **Invalid Client Credentials**: Check `FINNOTECH_CLIENT_SECRET`
2. **Token Not Found**: User needs to complete authorization flow
3. **Token Expired**: System should auto-refresh, check logs
4. **Redis Connection**: Ensure Redis is running and accessible

### Debug Commands

```bash
# Check token cleanup statistics
php artisan finnotech:cleanup-sms-tokens --dry-run

# Check Redis keys
redis-cli KEYS "finnotech:sms_auth:*"

# View logs
tail -f storage/logs/laravel.log | grep "SMS auth"
```

## Contributing

When adding new SMS-authenticated services:

1. Add scope to `SMS_AUTH_SCOPES` in `SmsAuthorizationService`
2. Create service-specific wrapper (like `CreditSmsService`)
3. Add endpoints to appropriate controller
4. Update this documentation
5. Add appropriate tests

## License

This implementation is part of the Pishkhanak project and follows the same licensing terms. 