# Jibit Payment Gateway Setup Guide

## Overview

This document provides a complete guide for setting up and using the Jibit Payment Gateway in the Pishkhanak project. The Jibit gateway has been fully integrated with support for payment creation, verification, refunds, and webhook handling.

## Features Implemented

### ✅ Core Payment Functions
- **Payment Creation**: Create new payments with Jibit API
- **Payment Verification**: Verify payments via webhook and API calls
- **Payment Refunds**: Full and partial refund support
- **Payment Status**: Real-time payment status checking
- **Webhook Handling**: Secure webhook signature validation

### ✅ Advanced Features
- **Multi-Currency Support**: IRR, USD, EUR
- **Sandbox Mode**: Test environment support
- **Comprehensive Logging**: Detailed transaction logging
- **Error Handling**: Robust error handling and recovery
- **Security**: Webhook signature validation
- **Amount Limits**: Configurable min/max amounts

## Installation & Setup

### 1. Database Setup

Run the seeder to add Jibit gateway to the database:

```bash
php artisan db:seed --class=JibitGatewaySeeder
```

### 2. Environment Configuration

Add the following environment variables to your `.env` file:

```env
# Jibit Payment Gateway Configuration
JIBIT_ACCESS_TOKEN=your_jibit_access_token_here
JIBIT_WEBHOOK_SECRET=your_webhook_secret_here
JIBIT_SANDBOX=true  # Set to false for production

# Existing Jibit API Configuration (if not already set)
JIBIT_API_KEY=your_api_key_here
JIBIT_SECRET_KEY=your_secret_key_here
JIBIT_BASE_URL=https://napi.jibit.ir/ide
```

### 3. Gateway Configuration

The gateway will be automatically configured with:
- **Supported Currencies**: IRR, USD, EUR
- **Fee Structure**: 0% percentage, 0 fixed fee
- **Amount Limits**: 1,000 - 500,000,000 IRR
- **Payment Methods**: Cards, Wallets, Bank Transfers

## API Integration

### Payment Creation

```php
use App\Models\PaymentGateway;
use App\Models\GatewayTransaction;

$gateway = PaymentGateway::where('slug', 'jibit')->first();
$jibitGateway = $gateway->getGatewayInstance();

$transaction = GatewayTransaction::create([
    'user_id' => $user->id,
    'payment_gateway_id' => $gateway->id,
    'total_amount' => 50000, // 50,000 IRR
    'currency_id' => $currency->id,
    'description' => 'Test payment',
    'reference_id' => 'TEST_' . uniqid(),
]);

$result = $jibitGateway->createPayment($transaction);

if ($result['success']) {
    $paymentUrl = $result['payment_url'];
    $paymentId = $result['payment_id'];
    // Redirect user to payment URL
}
```

### Payment Verification

```php
// Webhook callback handling
$result = $jibitGateway->verifyPayment($transaction, $callbackData);

if ($result['success']) {
    // Payment verified successfully
    $transaction->update(['status' => 'completed']);
} else {
    // Payment verification failed
    $transaction->update(['status' => 'failed']);
}
```

### Payment Refund

```php
$refundResult = $jibitGateway->refund($transaction, 25000); // Partial refund

if ($refundResult['success']) {
    $refundId = $refundResult['refund_id'];
    // Refund processed successfully
}
```

### Payment Status Check

```php
$statusResult = $jibitGateway->getPaymentStatus($transaction);

if ($statusResult['success']) {
    $status = $statusResult['data']['status'];
    // Handle different statuses: SUCCESSFUL, FAILED, PENDING, etc.
}
```

## Webhook Configuration

### 1. Webhook URL

Set your webhook URL in Jibit dashboard:
```
https://yourdomain.com/payment/callback/jibit
```

### 2. Webhook Secret

Configure the webhook secret in your environment:
```env
JIBIT_WEBHOOK_SECRET=your_webhook_secret_here
```

### 3. Webhook Payload

The gateway expects webhook payloads with the following structure:
```json
{
    "paymentId": "payment_123456",
    "referenceId": "REF_123456",
    "status": "SUCCESSFUL",
    "amount": 50000,
    "currency": "IRR",
    "signature": "webhook_signature_here"
}
```

## Error Handling

### Common Error Codes

| Error Code | Description | Solution |
|------------|-------------|----------|
| `INVALID_TOKEN` | Access token is invalid or expired | Refresh or regenerate access token |
| `INVALID_SIGNATURE` | Webhook signature validation failed | Check webhook secret configuration |
| `PAYMENT_NOT_FOUND` | Payment ID not found | Verify payment ID in transaction metadata |
| `AMOUNT_MISMATCH` | Payment amount doesn't match | Verify transaction amount |
| `REFERENCE_MISMATCH` | Reference ID doesn't match | Verify reference ID |

### Error Response Format

```php
[
    'success' => false,
    'gateway' => 'jibit',
    'message' => 'Error description',
    'error_code' => 'ERROR_CODE',
    'data' => []
]
```

## Testing

### Sandbox Mode

1. Set `JIBIT_SANDBOX=true` in your environment
2. Use test credentials provided by Jibit
3. Test all payment flows in sandbox environment

### Test Cards

Use Jibit's test card numbers for testing:
- **Success**: `6037991234567890`
- **Failure**: `6037991234567891`
- **Pending**: `6037991234567892`

## Security Considerations

### 1. Webhook Security
- Always validate webhook signatures
- Use HTTPS for webhook URLs
- Implement idempotency to prevent duplicate processing

### 2. Token Security
- Store access tokens securely
- Implement token refresh mechanism
- Rotate tokens regularly

### 3. Data Validation
- Validate all incoming webhook data
- Verify payment amounts and reference IDs
- Log all payment activities

## Monitoring & Logging

### Transaction Logs

All payment activities are logged in `gateway_transaction_logs` table:
- Payment creation attempts
- Gateway responses
- Webhook receipts
- Verification results
- Refund operations

### Log Levels

- **INFO**: Successful operations
- **WARNING**: Non-critical issues
- **ERROR**: Failed operations
- **DEBUG**: Detailed debugging information

## Troubleshooting

### Common Issues

1. **Access Token Expired**
   - Regenerate access token from Jibit dashboard
   - Update environment variable

2. **Webhook Not Receiving**
   - Verify webhook URL is accessible
   - Check webhook secret configuration
   - Ensure HTTPS is enabled

3. **Payment Verification Fails**
   - Check webhook signature validation
   - Verify payment ID in transaction metadata
   - Confirm amount and reference ID match

4. **Refund Fails**
   - Ensure payment is in SUCCESSFUL status
   - Verify refund amount doesn't exceed original amount
   - Check refund policy and time limits

### Debug Mode

Enable debug logging by setting:
```env
LOG_LEVEL=debug
```

## Support

For technical support:
1. Check Jibit API documentation
2. Review transaction logs
3. Contact Jibit support team
4. Check project documentation

## Changelog

### v1.0.0 (Initial Release)
- Complete payment gateway implementation
- Webhook handling with signature validation
- Refund support
- Comprehensive logging
- Multi-currency support
- Sandbox mode support 