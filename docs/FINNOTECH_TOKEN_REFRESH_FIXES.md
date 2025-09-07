# Finnotech Token Refresh System Fixes

## Issues Identified and Fixed

### 1. Missing Required Parameters

**Problem:** The refresh token requests were missing required parameters according to Finnotech's API documentation.

**Fixed Parameters:**
- `token_type` - Required field specifying the type of token (CLIENT-CREDENTIAL or CODE)
- `bank` - Required for most token types (except SMS tokens)
- `auth_type` - Required for SMS tokens

**Before:**
```php
$response = $this->makeFinnotechRequest('/dev/v2/oauth2/token', [
    'grant_type' => 'refresh_token',
    'refresh_token' => $token->refresh_token
], 'POST', false);
```

**After:**
```php
$requestData = [
    'grant_type' => 'refresh_token',
    'token_type' => $tokenType, // CLIENT-CREDENTIAL or CODE
    'refresh_token' => $token->refresh_token,
];

// Add bank code if required
if ($bank) {
    $requestData['bank'] = $bank;
}

// Add auth_type for SMS tokens
if ($authType) {
    $requestData['auth_type'] = $authType;
}
```

### 2. Incorrect Response Handling

**Problem:** The code was expecting `access_token` in the response, but Finnotech returns the token in a `value` field within a `result` object.

**Before:**
```php
if ($response && isset($response['access_token'])) {
    $this->saveFinnotechToken(
        $response['access_token'],
        $response['refresh_token'] ?? $token->refresh_token,
        // ...
    );
}
```

**After:**
```php
if ($response && isset($response['result']) && isset($response['result']['value'])) {
    $result = $response['result'];
    
    $this->saveFinnotechToken(
        $result['value'], // access_token is in 'value' field
        $result['refreshToken'] ?? $token->refresh_token,
        // ...
    );
}
```

### 3. Incorrect Token Lifetime Calculation

**Problem:** The code was using `addMilliseconds()` which doesn't exist in Carbon, and the lifetime calculation was incorrect.

**Before:**
```php
isset($result['lifeTime']) ? now()->addMilliseconds($result['lifeTime']) : now()->addHours(24)
```

**After:**
```php
isset($result['lifeTime']) ? now()->addSeconds($result['lifeTime'] / 1000) : now()->addHours(24)
```

### 4. Missing HTTP Headers

**Problem:** The HTTP requests were missing the required `Content-Type` header for JSON requests.

**Before:**
```php
$headers = [];

$response = Http::withHeaders($headers)->$method($url, $data);
```

**After:**
```php
$headers = [
    'Content-Type' => 'application/json',
    'Accept' => 'application/json',
];

$response = Http::withHeaders($headers);

if ($method === 'POST') {
    $response = $response->post($url, $data);
} else {
    $response = $response->get($url, $data);
}
```

### 5. Incorrect Client Credentials

**Problem:** The code was using hardcoded fallback credentials that didn't match the actual configuration.

**Before:**
```php
$clientId = config('services.finnotech.client_id', 'estelam');
$clientSecret = config('services.finnotech.client_secret', 'QoZJRQ5U5PUsCoUZspCw');
```

**After:**
```php
$clientId = config('services.finnotech.client_id', 'pishkhanak');
$clientSecret = config('services.finnotech.client_secret', 'EB9Kx6Z5FUiWgiD1N9z9');
```

## Token Type Mapping

The system now properly handles different token types:

### CLIENT-CREDENTIAL Tokens
- Used for: Main Finnotech token and most category tokens
- Requires: `bank` parameter
- Bank code: `062` (آینده) for most categories

### CODE Tokens
- Used for: SMS-related tokens
- Requires: `auth_type` parameter with value `SMS`
- No bank code required

## Bank Code Mapping

```php
$categoryBankMap = [
    'inquiry' => '062', // آینده
    'credit' => '062',  // آینده
    'kyc' => '062',     // آینده
    'token' => '062',   // آینده
    'promissory' => '062', // آینده
    'vehicle' => '062', // آینده
    'insurance' => '062', // آینده
    // Add more mappings as needed
];
```

## Testing

A new command has been created to test the token refresh functionality:

```bash
# Test all Finnotech tokens
php artisan finnotech:test-token-refresh

# Test a specific token
php artisan finnotech:test-token-refresh --token-name=fino_inquiry
```

## Files Modified

1. `app/Services/TokenService.php` - Main fixes for token refresh logic
2. `app/Console/Commands/TestFinnotechTokenRefresh.php` - New test command

## Next Steps

1. Test the token refresh functionality using the new command
2. Monitor logs for any remaining issues
3. Update bank code mappings if different banks are needed for specific categories
4. Consider implementing retry logic for failed refresh attempts 