# Saman Electronic Payment (SEP) Gateway Setup Guide

## Overview

This document provides a complete guide for setting up and using the Saman Electronic Payment (SEP) Gateway in the Pishkhanak project. The SEP gateway has been fully integrated with support for token-based payments, receipt retrieval, transaction verification, and refund processing.

## Features Implemented

### ✅ Core Payment Functions
- **Payment Creation**: Create new payments with SEP Token API
- **Payment Verification**: Two-step verification via Receipt API and Verify API
- **Payment Refunds**: Full refund support using Reverse API
- **Payment Status**: Payment status checking via callback data
- **Guest Payments**: Support for guest payment integration

### ✅ Advanced SEP Features
- **Standard Purchase**: Basic card payment processing
- **Wage Integration**: Support for additional fees/wages
- **Mobile Integration**: Payment flows with mobile number integration
- **Restricted Cards**: Payment restriction to specific card numbers (MD5 hashed)
- **Custom Token Expiry**: Configurable token expiration (20-3600 minutes)
- **Direct Redirect**: Simple GET-based redirect to payment page

### ✅ Security & Compliance
- **IP Whitelisting**: Ensures only authorized servers can call APIs
- **Digital Receipt Validation**: Unique receipt handling to prevent replay attacks
- **Amount Verification**: Cross-verification of amounts in all API calls
- **Two-Step Verification**: Mandatory receipt + verify API calls
- **Auto-Reversal Protection**: Handles 30-minute timeout for unverified transactions

## Installation & Setup

### 1. Database Setup

Run the seeder to add SEP gateway to the database:

```bash
php artisan db:seed --class=SepGatewaySeeder
```

### 2. Environment Configuration

Add the following environment variables to your `.env` file:

```env
# SEP (Saman Electronic Payment) Gateway Configuration
SEP_TERMINAL_ID=12345678          # Your 8-digit terminal ID from SEP
SEP_SANDBOX=true                  # Set to false for production
SEP_TOKEN_EXPIRY=20               # Token expiry in minutes (20-3600)
SEP_REFUND_ENABLED=false          # Contact SEP to enable refunds
```

### 3. IP Whitelisting

**Critical**: Contact Saman Electronic Payment support to whitelist your server IP(s). The API will reject calls from non-whitelisted IPs.

### 4. Gateway Configuration

The gateway will be automatically configured with:
- **Supported Currency**: IRT (Iranian Toman/Rial only)
- **Fee Structure**: 0% percentage, 0 fixed fee
- **Amount Limits**: 1,000 - 500,000,000 Rials
- **Payment Methods**: Card payments

## API Integration

### Basic Payment Creation

```php
use App\Models\PaymentGateway;
use App\Models\GatewayTransaction;
use App\Services\SepPaymentTypes;

$gateway = PaymentGateway::where('slug', 'sep')->first();
$sepPayments = new SepPaymentTypes();

$transaction = $sepPayments->createPurchase(
    amount: 50000, // 50,000 Rials
    description: 'Test payment',
    user: auth()->user()
);

$sepGateway = $gateway->getGatewayInstance();
$result = $sepGateway->createPayment($transaction);

if ($result['success']) {
    $token = $result['token'];
    $paymentUrl = $result['payment_url'];
    
    // Redirect user to payment page
    return redirect($paymentUrl);
}
```

### Payment Page Integration

After getting the token, SEP uses direct GET redirect:

```php
// Direct redirect approach (recommended)
$paymentUrl = "https://sep.shaparak.ir/OnlinePG/SendToken?token=" . urlencode($token);
return redirect($paymentUrl);
```

Alternative POST form approach:

```html
<form method="post" action="https://sep.shaparak.ir/OnlinePG/OnlinePG" id="sep-payment-form">
    <input type="hidden" name="Token" value="{{$token}}" />
    <button type="submit">پرداخت</button>
</form>

<script>
document.getElementById('sep-payment-form').submit();
</script>
```

### Payment Verification (Callback Handling)

```php
// Controller method for handling SEP callbacks
public function handleSepCallback(Request $request, $transactionUuid)
{
    $transaction = GatewayTransaction::where('uuid', $transactionUuid)->firstOrFail();
    $gateway = $transaction->paymentGateway->getGatewayInstance();
    
    $callbackData = $request->all(); // Contains Token and/or RefNum
    $result = $gateway->verifyPayment($transaction, $callbackData);
    
    if ($result['success']) {
        // Payment verified and settled successfully
        $transaction->markAsCompleted();
        
        $digitalReceipt = $result['digital_receipt'];
        $rrn = $result['rrn'];
        $amount = $result['amount'];
        $settled = $result['settled'] ?? false; // Settlement status
        
        return redirect()->route('payment.success', $transaction->uuid);
    } else {
        // Payment verification failed
        $transaction->markAsFailed($result['message']);
        
        return redirect()->route('payment.failed', $transaction->uuid);
    }
}
```

### Transaction Settlement

**IMPORTANT**: The SEP gateway requires an explicit settlement step after verification to approve transactions. Our implementation automatically calls the settlement API after successful verification to ensure transactions don't remain in "waiting" state.

The verification flow now includes:
1. **Verification**: Check if payment was successful
2. **Settlement**: Approve/finalize the transaction on gateway side
3. **Completion**: Mark transaction as completed in our system

```php
// The settlement process is automatically handled in verifyPayment()
// but you can check settlement status in the response:
if ($result['success'] && $result['verified']) {
    $settlementSuccessful = $result['settled'] ?? false;
    
    if (!$settlementSuccessful) {
        Log::warning('Payment verified but settlement failed', [
            'transaction_id' => $transaction->id,
            'ref_num' => $result['digital_receipt']
        ]);
        // Transaction is still considered successful
        // Settlement failure is logged but doesn't fail the payment
    }
}
```

### Advanced Features

#### Purchase with Wage/Fee

```php
$transaction = $sepPayments->createPurchaseWithWage(
    amount: 50000,
    wage: 2500,      // Additional fee
    description: 'Purchase with service fee',
    user: auth()->user()
);
```

#### Mobile Number Integration

```php
$transaction = $sepPayments->createMobilePurchase(
    amount: 30000,
    mobileNumber: '09123456789',
    description: 'Mobile-integrated payment',
    user: auth()->user()
);
```

#### Restricted Card Purchase

```php
$transaction = $sepPayments->createRestrictedCardPurchase(
    amount: 75000,
    allowedCardNumbers: ['1234567890123456', '6543210987654321'],
    description: 'Restricted card payment',
    user: auth()->user()
);
```

#### Custom Token Expiry

```php
$transaction = $sepPayments->createCustomExpiryPurchase(
    amount: 40000,
    tokenExpiryMinutes: 60, // 1 hour
    description: 'Extended expiry payment',
    user: auth()->user()
);
```

### Refund Processing

```php
// Process full refund
$gateway = $transaction->paymentGateway->getGatewayInstance();
$result = $gateway->refund($transaction);

if ($result['success']) {
    $refundAmount = $result['refund_amount'];
    $refundReference = $result['refund_reference'];
    
    // Handle successful refund
    $transaction->markAsRefunded();
} else {
    // Handle refund failure
    $errorMessage = $result['message'];
}
```

## Testing

### Run Gateway Tests

```bash
# Run comprehensive gateway tests
php artisan test:sep-gateway

# Skip API connectivity tests
php artisan test:sep-gateway --skip-api

# Test with custom amount
php artisan test:sep-gateway --amount=25000

# Verbose output
php artisan test:sep-gateway --verbose
```

### Manual Testing Steps

1. **Configure Environment**: Set `SEP_TERMINAL_ID` and `SEP_SANDBOX=true`
2. **Create Test Payment**: Use the payment types service
3. **Verify IP Whitelisting**: Ensure your server IP is whitelisted
4. **Test Payment Flow**: Complete a test transaction
5. **Test Verification**: Verify callback handling works correctly

## Configuration Options

| Setting | Environment Variable | Default | Description |
|---------|---------------------|---------|-------------|
| Terminal ID | `SEP_TERMINAL_ID` | `""` | 8-digit terminal ID from SEP |
| Sandbox Mode | `SEP_SANDBOX` | `true` | Enable/disable sandbox mode |
| Token Expiry | `SEP_TOKEN_EXPIRY` | `20` | Token expiry in minutes (20-3600) |
| Refund Enabled | `SEP_REFUND_ENABLED` | `false` | Enable refund functionality |

## Error Handling

### Common Errors and Solutions

#### Token Request Errors

| Error Code | Description | Solution |
|------------|-------------|----------|
| `-1` | Transaction not found | Check transaction details |
| `-2` | IP address invalid | Whitelist your server IP |
| `-3` | General error | Check request parameters |
| `-4` | Operation not allowed | Verify terminal configuration |
| `-5` | Invalid IP address | Contact SEP for IP whitelisting |

#### Receipt Status Codes

| Status | State | Description |
|--------|-------|-------------|
| `0` | `InProgress` | Transaction not finalized |
| `1` | `CanceledByUser` | User canceled payment |
| `2` | `OK` | Transaction successful |
| `3` | `Failed` | Transaction failed |

#### Verification Result Codes

| Code | Description | Action |
|------|-------------|--------|
| `0` | Success | Payment verified |
| `2` | Duplicate request | Already verified |
| `-2` | Transaction not found | Check RefNum |
| `-6` | Timeout (30+ minutes) | Auto-reversed |
| `-104` | Terminal disabled | Contact SEP |
| `-105` | Terminal not found | Verify terminal ID |
| `-106` | IP not authorized | Whitelist IP address |

## Security Considerations

### Best Practices

1. **IP Whitelisting**: Always whitelist your production server IPs
2. **HTTPS Only**: Use HTTPS for all callback URLs
3. **Amount Validation**: Always verify amounts in verification step
4. **RefNum Uniqueness**: Check for duplicate RefNum to prevent double spending
5. **Timeout Handling**: Verify transactions within 30 minutes
6. **Error Logging**: Log all gateway interactions for debugging

### Data Protection

- **Never Store**: Card numbers, CVV2, or PIN codes
- **Hash Only**: Use MD5 hashes for card number restrictions
- **Secure Callbacks**: Validate all callback data
- **Audit Trail**: Maintain transaction logs for compliance

## Production Checklist

- [ ] **Environment Variables**: All SEP_* variables configured
- [ ] **IP Whitelisting**: Production server IPs whitelisted with SEP
- [ ] **SSL Certificate**: Valid SSL on callback URLs
- [ ] **Sandbox Mode**: `SEP_SANDBOX=false` for production
- [ ] **Error Handling**: Comprehensive error handling implemented
- [ ] **Logging**: Transaction logging enabled
- [ ] **Testing**: Full payment flow tested
- [ ] **Refunds**: Refund service enabled if needed
- [ ] **Monitoring**: Payment monitoring and alerts set up

## API Endpoints

### SEP API Endpoints

| Purpose | URL |
|---------|-----|
| Token Request | `https://sep.shaparak.ir/onlinepg/onlinepg` |
| Payment Page | `https://sep.shaparak.ir/OnlinePG/SendToken` |
| Receipt Retrieval | `https://sep.shaparak.ir/verifyTxnRandomSessionkey/api/v2/ipg/payment/receipt` |
| Transaction Verify | `https://sep.shaparak.ir/verifyTxnRandomSessionkey/ipg/VerifyTransaction` |
| Transaction Reverse | `https://sep.shaparak.ir/verifyTxnRandomSessionkey/ipg/ReverseTransaction` |

### Callback URL Format

```
https://yourdomain.com/payment/callback/sep/{transaction_uuid}?Token={token}&RefNum={refnum}
```

## Troubleshooting

### Issue: Token Request Fails with IP Error
**Solution**: Contact SEP support to whitelist your server IP address

### Issue: Transactions Staying in Waiting State
**Problem**: After successful payment, transactions remain in "waiting" state on the gateway and are not approved.

**Root Cause**: SEP gateway requires an explicit settlement/approval step after verification to finalize transactions.

**Solution**: 
- ✅ **FIXED**: Our implementation now automatically calls `settleTransaction()` after successful verification
- The settlement process uses the SEP Verify API to confirm and approve the transaction
- Transactions are marked as both "verified" and "settled" in the response
- Check logs for settlement status: `Log::info('SEP Settlement successful')` or `Log::warning('SEP settlement failed')`

**Verification Steps**:
1. Check `gateway_transaction_logs` table for settlement success
2. Look for `settlement_success: true` in transaction logs  
3. Verify `gateway_response` contains settlement data
4. Monitor SEP merchant portal for approved transactions

**If Settlement Fails**:
- Payment is still considered successful (settlement failure doesn't void the payment)
- Warning is logged but transaction proceeds normally
- Contact SEP support if settlement consistently fails

### Issue: Payment Verification Fails
**Solutions**:
- Check that RefNum is correctly received in callback
- Verify terminal ID is correct
- Ensure transaction exists in database
- Check if transaction was already verified

### Issue: Refund Fails
**Solutions**:
- Verify refund service is enabled (`SEP_REFUND_ENABLED=true`)
- Check that transaction was successfully verified
- Ensure refund is attempted within allowed timeframe
- Contact SEP if refund service needs activation

### Issue: Amount Mismatch
**Solutions**:
- Verify amounts are in correct currency (Rials)
- Check for any additional fees or wages
- Ensure total_amount includes all fees

## Support and Resources

### SEP Documentation
- **Official Documentation**: SEP API v4.1 Documentation
- **Support Contact**: Contact SEP Electronic Payment support
- **Merchant Portal**: `https://report.sep.ir`

### Internal Resources
- **Gateway Configuration**: `/access/payment-gateways`
- **Transaction Logs**: Check `gateway_transaction_logs` table
- **Test Commands**: `php artisan test:sep-gateway`
- **Debug Mode**: Enable verbose logging for troubleshooting

### Contact Information

For technical issues with the gateway implementation:
- Check the transaction logs in the admin panel
- Run the test command for configuration validation
- Review the Laravel logs for detailed error messages

For SEP-specific issues:
- Contact SEP technical support
- Use the merchant portal for transaction inquiries
- Reference the official SEP documentation 