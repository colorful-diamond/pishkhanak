# SEP Gateway Environment Configuration Guide

## Overview

This document provides detailed information about configuring environment variables for the Saman Electronic Payment (SEP) Gateway integration.

## Required Environment Variables

Add these variables to your `.env` file:

```env
# =====================================================
# SEP (Saman Electronic Payment) Gateway Configuration
# =====================================================

# Terminal ID - 8-digit unique identifier provided by SEP
# Example: 12345678
# Required: YES
# Note: Contact SEP to obtain your terminal ID
SEP_TERMINAL_ID=

# Sandbox Mode - Enable/disable test environment
# Values: true (sandbox), false (production)
# Default: true
# Required: YES
SEP_SANDBOX=true

# Token Expiry - Token validity period in minutes
# Range: 20-3600 minutes (20 minutes to 60 hours)
# Default: 20 minutes
# Required: NO
SEP_TOKEN_EXPIRY=20

# Refund Service - Enable/disable refund functionality
# Values: true (enabled), false (disabled)
# Default: false
# Required: NO
# Note: Contact SEP to activate refund service on your account
SEP_REFUND_ENABLED=false
```

## Environment Variable Details

### SEP_TERMINAL_ID

**Description**: 8-digit unique terminal identifier assigned by Saman Electronic Payment.

**Format**: Exactly 8 digits (e.g., `12345678`)

**How to Obtain**:
1. Contact Saman Electronic Payment
2. Complete merchant registration process
3. Provide required business documentation
4. Receive terminal ID via email or merchant portal

**Validation**:
- Must be exactly 8 digits
- Only numeric characters allowed
- Required for all API calls

**Example**:
```env
SEP_TERMINAL_ID=98765432
```

### SEP_SANDBOX

**Description**: Controls whether to use sandbox (test) or production environment.

**Values**:
- `true`: Use sandbox environment for testing
- `false`: Use production environment for live payments

**Default**: `true`

**Important Notes**:
- Always test in sandbox before production
- Sandbox transactions use fake money
- Production requires live merchant account
- Switch to `false` only after thorough testing

**Examples**:
```env
# For development/testing
SEP_SANDBOX=true

# For production
SEP_SANDBOX=false
```

### SEP_TOKEN_EXPIRY

**Description**: Defines how long payment tokens remain valid before expiring.

**Range**: 20-3600 minutes
- Minimum: 20 minutes
- Maximum: 3600 minutes (60 hours)

**Default**: 20 minutes (if not specified)

**Use Cases**:
- Short expiry (20-30 min): Regular e-commerce transactions
- Medium expiry (60-120 min): Complex checkout processes
- Long expiry (180+ min): Extended reservation systems

**Examples**:
```env
# Standard e-commerce (20 minutes)
SEP_TOKEN_EXPIRY=20

# Extended checkout process (1 hour)
SEP_TOKEN_EXPIRY=60

# Reservation system (4 hours)
SEP_TOKEN_EXPIRY=240
```

### SEP_REFUND_ENABLED

**Description**: Enables or disables refund processing capability.

**Values**:
- `true`: Refund API calls are enabled
- `false`: Refund functionality is disabled

**Default**: `false`

**Prerequisites**:
- Refund service must be activated by SEP
- Additional agreements may be required
- Refunds typically have time restrictions (e.g., 50 minutes)

**Examples**:
```env
# Refunds disabled (default)
SEP_REFUND_ENABLED=false

# Refunds enabled (requires SEP activation)
SEP_REFUND_ENABLED=true
```

## Configuration Examples

### Development Environment

```env
# Development/Testing Configuration
SEP_TERMINAL_ID=12345678
SEP_SANDBOX=true
SEP_TOKEN_EXPIRY=30
SEP_REFUND_ENABLED=false
```

### Staging Environment

```env
# Staging Configuration (production-like testing)
SEP_TERMINAL_ID=87654321
SEP_SANDBOX=true
SEP_TOKEN_EXPIRY=20
SEP_REFUND_ENABLED=true
```

### Production Environment

```env
# Production Configuration
SEP_TERMINAL_ID=98765432
SEP_SANDBOX=false
SEP_TOKEN_EXPIRY=20
SEP_REFUND_ENABLED=true
```

### E-commerce Store Example

```env
# E-commerce Store - Fast checkout required
SEP_TERMINAL_ID=11223344
SEP_SANDBOX=false
SEP_TOKEN_EXPIRY=15  # Short expiry for quick transactions
SEP_REFUND_ENABLED=true
```

### Reservation System Example

```env
# Hotel/Travel Reservation - Extended hold time
SEP_TERMINAL_ID=55667788
SEP_SANDBOX=false
SEP_TOKEN_EXPIRY=300  # 5 hours for reservation completion
SEP_REFUND_ENABLED=true
```

## Validation and Testing

### Validate Configuration

Use the built-in test command to validate your configuration:

```bash
# Run comprehensive configuration test
php artisan test:sep-gateway

# Test with verbose output
php artisan test:sep-gateway --verbose

# Skip API connectivity tests
php artisan test:sep-gateway --skip-api
```

### Environment Validation Checklist

- [ ] `SEP_TERMINAL_ID` is exactly 8 digits
- [ ] `SEP_SANDBOX` is boolean (true/false)
- [ ] `SEP_TOKEN_EXPIRY` is between 20-3600 (if set)
- [ ] `SEP_REFUND_ENABLED` is boolean (if set)
- [ ] All variables are properly quoted if containing special characters
- [ ] No trailing spaces in variable values

### Common Configuration Mistakes

#### ❌ Incorrect Terminal ID Format

```env
# Wrong - contains letters
SEP_TERMINAL_ID=ABC12345

# Wrong - too short
SEP_TERMINAL_ID=1234567

# Wrong - too long
SEP_TERMINAL_ID=123456789

# Correct - exactly 8 digits
SEP_TERMINAL_ID=12345678
```

#### ❌ Invalid Boolean Values

```env
# Wrong - string values
SEP_SANDBOX="yes"
SEP_REFUND_ENABLED="no"

# Wrong - numeric values
SEP_SANDBOX=1
SEP_REFUND_ENABLED=0

# Correct - boolean values
SEP_SANDBOX=true
SEP_REFUND_ENABLED=false
```

#### ❌ Out of Range Token Expiry

```env
# Wrong - below minimum
SEP_TOKEN_EXPIRY=10

# Wrong - above maximum
SEP_TOKEN_EXPIRY=4000

# Correct - within range
SEP_TOKEN_EXPIRY=60
```

## Security Considerations

### Environment File Security

1. **Never commit `.env` files** to version control
2. **Use different terminal IDs** for each environment
3. **Restrict file permissions**: `chmod 600 .env`
4. **Use secure backup** methods for production configurations

### Production Security

```env
# Production security recommendations
SEP_TERMINAL_ID=your_production_terminal_id
SEP_SANDBOX=false  # Never use sandbox in production
SEP_TOKEN_EXPIRY=20  # Use minimum required time
SEP_REFUND_ENABLED=false  # Only enable if absolutely necessary
```

### Development Security

```env
# Development - safe defaults
SEP_TERMINAL_ID=12345678  # Test terminal ID
SEP_SANDBOX=true  # Always use sandbox for development
SEP_TOKEN_EXPIRY=30  # Slightly longer for testing
SEP_REFUND_ENABLED=true  # Enable for testing refund flows
```

## Deployment Considerations

### Environment-Specific Configurations

| Environment | Sandbox | Terminal ID | Token Expiry | Refunds |
|-------------|---------|-------------|--------------|---------|
| Local Dev   | `true`  | Test ID     | 30-60 min    | `true`  |
| Staging     | `true`  | Test ID     | 20-30 min    | `true`  |
| Production  | `false` | Live ID     | 20 min       | As needed |

### Container/Docker Considerations

When using Docker or containers:

```dockerfile
# Dockerfile example
ENV SEP_TERMINAL_ID=${SEP_TERMINAL_ID}
ENV SEP_SANDBOX=${SEP_SANDBOX}
ENV SEP_TOKEN_EXPIRY=${SEP_TOKEN_EXPIRY}
ENV SEP_REFUND_ENABLED=${SEP_REFUND_ENABLED}
```

### CI/CD Pipeline

```yaml
# Example CI/CD environment variables
environment:
  SEP_TERMINAL_ID: ${{ secrets.SEP_TERMINAL_ID }}
  SEP_SANDBOX: true  # Always use sandbox in CI
  SEP_TOKEN_EXPIRY: 20
  SEP_REFUND_ENABLED: false
```

## Troubleshooting

### Configuration Issues

#### Issue: "Terminal ID not found"
```bash
# Check terminal ID format
echo $SEP_TERMINAL_ID | wc -c  # Should return 9 (8 digits + newline)

# Verify only digits
echo $SEP_TERMINAL_ID | grep -E '^[0-9]{8}$'
```

#### Issue: "Environment variable not loading"
```bash
# Check if variable is set
env | grep SEP_

# Clear config cache
php artisan config:clear
php artisan config:cache
```

#### Issue: "Invalid boolean value"
```bash
# Check boolean format
php -r "var_dump(filter_var(env('SEP_SANDBOX'), FILTER_VALIDATE_BOOLEAN));"
```

### Runtime Validation

Add runtime validation in your application:

```php
// app/Providers/AppServiceProvider.php
public function boot()
{
    // Validate SEP configuration on boot
    $terminalId = env('SEP_TERMINAL_ID');
    if ($terminalId && !preg_match('/^\d{8}$/', $terminalId)) {
        throw new Exception('SEP_TERMINAL_ID must be exactly 8 digits');
    }
    
    $tokenExpiry = env('SEP_TOKEN_EXPIRY');
    if ($tokenExpiry && ($tokenExpiry < 20 || $tokenExpiry > 3600)) {
        throw new Exception('SEP_TOKEN_EXPIRY must be between 20 and 3600 minutes');
    }
}
```

## Next Steps

After configuring environment variables:

1. **Run Tests**: `php artisan test:sep-gateway`
2. **Seed Database**: `php artisan db:seed --class=SepGatewaySeeder`
3. **Contact SEP**: Whitelist your server IP addresses
4. **Test Integration**: Create test transactions
5. **Monitor Logs**: Check transaction logs for issues

## Support

For environment configuration issues:
- Check Laravel logs: `storage/logs/laravel.log`
- Run gateway tests: `php artisan test:sep-gateway --verbose`
- Verify .env file syntax and permissions
- Contact SEP support for terminal-related issues 