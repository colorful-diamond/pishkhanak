# Payment Gateway System Setup Guide

## Overview

This system provides a dynamic, extensible payment gateway architecture that currently supports Asan Pardakht and can easily be extended to support additional gateways.

## Features

- **Dynamic Gateway Management**: Add new payment gateways without code changes
- **Unified API**: Single controller handles all payment gateways
- **Automatic Wallet Integration**: Successful payments automatically charge user wallets
- **Comprehensive Logging**: Full transaction logging and monitoring
- **Fee Calculation**: Dynamic fee calculation per gateway
- **Multi-Currency Support**: Ready for multiple currencies

## Installation & Setup

### 1. Environment Configuration

Add the following to your `.env` file:

```env
# Asan Pardakht Configuration
ASANPARDAKHT_MERCHANT_ID=your_merchant_configuration_id
ASANPARDAKHT_USERNAME=your_api_username
ASANPARDAKHT_PASSWORD=your_api_password
ASANPARDAKHT_SANDBOX=true
```

### 2. Database Setup

Run the migrations and seeders:

```bash
# Run migrations
php artisan migrate

# Seed the payment system (currencies, gateways, tax rules)
php artisan db:seed --class=PaymentSystemSeeder
```

### 3. Verify Installation

Check that the payment gateway was created:

```bash
php artisan tinker
>>> App\Models\PaymentGateway::where('slug', 'asanpardakht')->first()
```

## Usage

### Frontend Integration

The wallet page automatically loads available payment gateways:

1. User selects amount from dropdown
2. System loads compatible gateways via AJAX
3. User selects preferred gateway
4. System initiates payment with selected gateway
5. User is redirected to gateway for payment
6. After payment, user returns to your site
7. System verifies payment and updates wallet balance

### API Endpoints

#### Get Available Gateways
```
GET /payment/gateways?amount=100000&currency=IRR
```

#### Initialize Payment
```
POST /payment/initialize
{
    "gateway_id": 1,
    "amount": 100000,
    "currency": "IRR",
    "description": "Wallet charge"
}
```

#### Payment Callback
```
GET /payment/callback/{gateway}/{transaction?}
```

#### Check Payment Status
```
GET /payment/status/{transactionId}
```

## Adding New Gateways

### 1. Create Gateway Class

Create a new gateway class in `app/Services/PaymentGateways/`:

```php
<?php

namespace App\Services\PaymentGateways;

use App\Models\GatewayTransaction;

class NewGateway extends AbstractPaymentGateway
{
    protected function getApiUrl(): string
    {
        return 'https://api.newgateway.com/';
    }

    protected function getSandboxApiUrl(): string
    {
        return 'https://sandbox.newgateway.com/';
    }

    public function createPayment(GatewayTransaction $transaction): array
    {
        // Implementation
    }

    public function verifyPayment(GatewayTransaction $transaction, array $callbackData): array
    {
        // Implementation
    }

    // ... implement other required methods
}
```

### 2. Add to Database

Add the gateway to your seeder or create manually:

```php
PaymentGateway::create([
    'name' => 'New Gateway',
    'slug' => 'newgateway',
    'driver' => \App\Services\PaymentGateways\NewGateway::class,
    'description' => 'New payment gateway',
    'is_active' => true,
    'config' => [
        'api_key' => env('NEWGATEWAY_API_KEY'),
        'sandbox' => env('NEWGATEWAY_SANDBOX', true),
    ],
    'supported_currencies' => ['IRR'],
    'fee_percentage' => 2.0,
    'min_amount' => 1000,
    'max_amount' => 1000000000,
]);
```

## Testing

### Test with Sandbox

1. Set `ASANPARDAKHT_SANDBOX=true` in your `.env`
2. Use test credentials provided by Asan Pardakht
3. Go to `/user/wallet` and try to charge your wallet
4. Follow the payment flow

### Test Payment Flow

1. **Create Payment**: POST to `/user/wallet/charge` with amount and gateway_id
2. **Gateway Redirect**: User redirected to payment gateway
3. **Payment Completion**: User completes payment on gateway
4. **Callback Handling**: Gateway calls `/payment/callback/{gateway}`
5. **Verification**: System verifies payment with gateway
6. **Wallet Update**: User wallet balance is updated
7. **User Notification**: Success/failure message shown

## Monitoring & Logs

### Transaction Logs

All payment activities are logged in `gateway_transaction_logs` table:

```php
// View transaction logs
$transaction = GatewayTransaction::find($id);
$logs = $transaction->logs()->orderBy('created_at', 'desc')->get();
```

### Error Handling

The system provides comprehensive error handling:

- Gateway connection failures
- Payment verification failures
- Transaction validation errors
- User wallet update errors

All errors are logged to Laravel's log system.

## Security Considerations

1. **Environment Variables**: Never commit API credentials to version control
2. **Callback Validation**: All payment callbacks are verified with the gateway
3. **Amount Validation**: Payment amounts are validated against stored transaction data
4. **User Authorization**: Users can only access their own transactions
5. **HTTPS**: All payment communications use HTTPS

## Troubleshooting

### Common Issues

1. **"Gateway not available"**: Check if gateway is active in database
2. **"Amount not supported"**: Check gateway min/max amount limits
3. **"Payment verification failed"**: Check API credentials and network connectivity
4. **"Transaction not found"**: Check callback URL configuration

### Debug Mode

Enable debug logging for payments:

```php
// In config/logging.php
'payment' => [
    'driver' => 'daily',
    'path' => storage_path('logs/payment.log'),
    'level' => 'debug',
],
```

## Support

For technical support with the payment system:

1. Check Laravel logs: `storage/logs/laravel.log`
2. Check payment logs: `storage/logs/payment.log`
3. Review transaction logs in database
4. Contact Asan Pardakht support for gateway-specific issues

## Production Checklist

Before going live:

- [ ] Set `ASANPARDAKHT_SANDBOX=false`
- [ ] Update to production API credentials
- [ ] Test with real transactions
- [ ] Verify callback URL is accessible from internet
- [ ] Enable proper logging
- [ ] Set up monitoring for failed payments
- [ ] Configure proper error pages 