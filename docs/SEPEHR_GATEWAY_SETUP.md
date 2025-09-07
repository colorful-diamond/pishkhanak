# Sepehr Payment Gateway Setup Guide

## Overview

This document provides a complete guide for setting up and using the Sepehr Electronic Payment Gateway in the Pishkhanak project. The Sepehr gateway has been fully integrated with support for all major Sepehr API features including standard payments, bill payments, mobile top-ups, identified purchases, and fund splitting.

## Features Implemented

### ✅ Core Payment Functions
- **Payment Creation**: Create new payments with Sepehr GetToken API
- **Payment Verification**: Verify payments via callback with mandatory Advice API call
- **Payment Refunds**: Full refund support using Rollback API
- **Payment Status**: Payment status checking via callback data
- **Guest Payments**: Support for guest payment hash in payload system

### ✅ Advanced Sepehr Features
- **Standard Purchase**: Basic card payment processing
- **Bill Payment**: Single and batch bill payment support
- **Mobile Top-up**: Mobile charge and internet package purchases
- **Identified Purchase**: Payments with national ID verification
- **Fund Splitting**: Split payments across multiple merchant accounts
- **Mobile Integration**: Payment flows with mobile number integration

### ✅ Security & Compliance
- **IP Whitelisting**: Ensures only authorized servers can call APIs
- **Digital Receipt Validation**: Unique receipt handling to prevent replay attacks
- **Amount Verification**: Cross-verification of amounts in all API calls
- **Advice Mandatory**: Automatic advice call for purchase transactions
- **Auto-Reversal Protection**: Handles 30-minute timeout for unadvised transactions

## Installation & Setup

### 1. Database Setup

Run the seeder to add Sepehr gateway to the database:

```bash
php artisan db:seed --class=SepehrGatewaySeeder
```

### 2. Environment Configuration

Add the following environment variables to your `.env` file:

```env
# Sepehr Payment Gateway Configuration
SEPEHR_TERMINAL_ID=your_8_digit_terminal_id_here
SEPEHR_SANDBOX=true  # Set to false for production
SEPEHR_GET_METHOD=false  # Use POST for callbacks (recommended)
SEPEHR_ROLLBACK_ENABLED=false  # Contact Sepehr to enable refunds
```

### 3. IP Whitelisting

**Critical**: Contact Sepehr Electronic Payment support to whitelist your server IP(s). The API will reject calls from non-whitelisted IPs.

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

$gateway = PaymentGateway::where('slug', 'sepehr')->first();
$sepehrGateway = $gateway->getGatewayInstance();

$transaction = GatewayTransaction::create([
    'user_id' => $user->id,
    'payment_gateway_id' => $gateway->id,
    'total_amount' => 50000, // 50,000 Rials
    'currency_id' => $currency->id,
    'description' => 'Test payment',
    'reference_id' => 'ORDER_' . uniqid(),
]);

$result = $sepehrGateway->createPayment($transaction);

if ($result['success']) {
    $accessToken = $result['access_token'];
    $paymentUrl = $result['payment_url'];
    $terminalId = $result['terminal_id'];
    
    // Redirect user to payment page with POST form
    // See "Payment Page Integration" section below
}
```

### Payment Page Integration

After getting the access token, you need to create an HTML form to redirect the user to Sepehr's payment page:

```html
<form method="post" action="{{$paymentUrl}}" id="sepehr-payment-form">
    <input type="hidden" name="TerminalID" value="{{$terminalId}}" />
    <input type="hidden" name="token" value="{{$accessToken}}" />
    
    <!-- Optional: For identified purchases -->
    @if(isset($nationalCode))
    <input type="hidden" name="nationalCode" value="{{$nationalCode}}" />
    @endif
    
    <!-- Optional: For GET method callbacks -->
    @if($getMethod)
    <input type="hidden" name="getMethod" value="1" />
    @endif
    
    <button type="submit">پرداخت</button>
</form>

<script>
// Auto-submit the form
document.getElementById('sepehr-payment-form').submit();
</script>
```

### Payment Verification (Callback Handling)

```php
// Controller method for handling Sepehr callbacks
public function handleSepehrCallback(Request $request, $transactionUuid)
{
    $transaction = GatewayTransaction::where('uuid', $transactionUuid)->firstOrFail();
    $gateway = $transaction->paymentGateway->getGatewayInstance();
    
    $callbackData = $request->all();
    $result = $gateway->verifyPayment($transaction, $callbackData);
    
    if ($result['success']) {
        // Payment verified successfully
        $transaction->markAsCompleted();
        
        // For purchase transactions, advice was automatically called
        $digitalReceipt = $result['digital_receipt'];
        $rrn = $result['rrn'];
        $cardNumber = $result['card_number']; // Masked
        
        return redirect()->route('payment.success', $transaction->uuid);
    } else {
        // Payment verification failed
        $transaction->markAsFailed($result['message']);
        
        return redirect()->route('payment.failed', $transaction->uuid);
    }
}
```

### Advanced Features

#### Bill Payment

```php
$transaction = GatewayTransaction::create([
    // ... basic transaction data
    'metadata' => [
        'transaction_type' => 'bill_payment',
        'bill_id' => '6605164900135', // 13 or 18 digit Bill ID
        'pay_id' => '2070394', // Payment ID
    ]
]);

$result = $sepehrGateway->createPayment($transaction);
// This will use /Bill endpoint automatically
```

#### Batch Bill Payment

```php
$transaction = GatewayTransaction::create([
    // ... basic transaction data
    'metadata' => [
        'transaction_type' => 'batch_bill_payment',
        'bills' => [
            ['BillID' => '6605164900135', 'PayID' => '2070394'],
            ['BillID' => '6605164900136', 'PayID' => '2070395'],
        ]
    ]
]);
```

#### Mobile Top-up

```php
$transaction = GatewayTransaction::create([
    // ... basic transaction data
    'metadata' => [
        'transaction_type' => 'mobile_topup',
        'mobile_data' => [
            'mobile' => '09121111111',
            'operator' => 0, // 0=MTN, 1=MCI, 2=Rightel
            'chargeType' => 0, // 0=Normal, 1=MTN Wow, 2=Rightel Wow
            'requestType' => 0, // 0=Top-up, 1=Voucher, 2=Data plan
            'dataplanId' => 0, // For data plans
            'AdditionalData' => ''
        ]
    ]
]);
```

#### Identified Purchase (with National ID)

```php
$transaction = GatewayTransaction::create([
    // ... basic transaction data
    'metadata' => [
        'national_code' => '1234567890', // 10-digit national ID
    ]
]);
```

#### Fund Splitting

```php
$transaction = GatewayTransaction::create([
    // ... basic transaction data
    'metadata' => [
        'transaction_type' => 'fund_splitting',
        'split_accounts' => [
            ['Account' => 'M01', 'Amount' => '30000'],
            ['Account' => 'M02', 'Amount' => '20000']
        ],
        'split_id' => '0001'
    ]
]);
```

#### Guest Payment Integration

```php
$transaction = GatewayTransaction::create([
    // ... basic transaction data
    'metadata' => [
        'guest_payment_hash' => $yourGuestPaymentHash, // Your existing guest hash system
    ]
]);
```

### Payment Refund

```php
$refundResult = $sepehrGateway->refund($transaction);

if ($refundResult['success']) {
    $refundId = $refundResult['refund_id']; // Same as digital receipt
    $refundedAmount = $refundResult['amount'];
    // Refund processed successfully
}
```

**Note**: Rollback service must be enabled by Sepehr support and can only be used within 30 minutes of successful advice.

## Callback Data Structure

Sepehr sends the following data to your callback URL:

```php
[
    'respcode' => 0, // 0=Success, -1=Cancelled, -2=Timeout
    'respmsg' => 'Payment message',
    'amount' => 50000, // Transaction amount in Rials
    'invoiceid' => 'ORDER_123456', // Your invoice/reference ID
    'terminalid' => 12345678, // Your terminal ID
    'tracenumber' => 123456, // Tracking number
    'rrn' => 123456789012, // Reference Retrieval Number
    'digitalreceipt' => 'unique_receipt_string', // Used for advice/rollback
    'datePaid' => '2024-01-15 14:30:25', // Payment date/time
    'cardnumber' => '603769******0286', // Masked card number
    'payload' => '{"guest_payment_hash":"..."}', // Your payload data
    'issuerbank' => 'بانک ملی ایران', // Issuing bank name
]
```

## Error Handling

### Token Generation Errors

| Error Code | Description | Solution |
|------------|-------------|----------|
| `-1` | Transaction not found | Check transaction data |
| `-2` | Connection error / Invalid IP | Verify IP whitelisting |
| `-3` | General error / Already reversed | Check transaction status |
| `-4` | Operation not allowed | Verify transaction type |
| `-5` | Invalid IP address | Contact Sepehr to whitelist IP |
| `-6` | Rollback service not active | Contact Sepehr to enable rollback |

### Callback Response Codes

| Response Code | Description | Action |
|---------------|-------------|--------|
| `0` | Payment successful | Process order, call advice |
| `-1` | User cancelled | Mark as cancelled |
| `-2` | Payment timeout | Mark as expired |

### Error Response Format

```php
[
    'success' => false,
    'gateway' => 'sepehr',
    'message' => 'Error description in Persian',
    'error_code' => 'ERROR_CODE',
    'data' => []
]
```

## Testing

### Sandbox Environment

1. Set `SEPEHR_SANDBOX=true` in your environment
2. Use your test Terminal ID provided by Sepehr
3. Ensure test server IP is also whitelisted

### Test Payment Flow

```bash
# Run the test command
php artisan test:sepehr-gateway

# Test with specific user and amount
php artisan test:sepehr-gateway --user-id=1 --amount=15000
```

### Manual Testing Steps

1. Create a test transaction
2. Get payment token from GetToken API
3. Redirect to Sepehr payment page
4. Complete payment with test card
5. Verify callback is received
6. Check that advice was called automatically
7. Confirm transaction is marked as completed

## Security Considerations

### 1. Digital Receipt Uniqueness
- Store and validate digital receipts to prevent replay attacks
- Each receipt can only be used once for advice/rollback

### 2. Amount Verification
- Always verify amounts in callback match your transaction
- Verify advice response amount matches original amount

### 3. IP Whitelisting
- Only whitelisted IPs can call Sepehr APIs
- Keep your IP list updated with Sepehr support

### 4. Mandatory Advice
- Purchase transactions MUST call advice within 30 minutes
- Unadvised transactions automatically reverse
- Bill payments and top-ups are auto-advised

### 5. Terminal ID Security
- Keep your Terminal ID secure
- Don't expose it in client-side code
- Verify Terminal ID in all callbacks

## Monitoring & Logging

### Transaction Logs

All Sepehr activities are logged in `gateway_transaction_logs` table:
- GetToken API calls
- Payment page redirections
- Callback receipts
- Advice API calls
- Rollback operations

### Important Log Points

```php
// Monitor these log actions
GatewayTransactionLog::ACTION_GATEWAY_CALLED    // GetToken called
GatewayTransactionLog::ACTION_CREATED           // Token received
GatewayTransactionLog::ACTION_WEBHOOK_RECEIVED  // Callback received
GatewayTransactionLog::ACTION_COMPLETED         // Advice successful
GatewayTransactionLog::ACTION_FAILED            // Any failure
GatewayTransactionLog::ACTION_REFUNDED          // Rollback successful
```

## Production Checklist

- [ ] Terminal ID configured and verified
- [ ] Production server IP whitelisted with Sepehr
- [ ] Sandbox mode disabled (`SEPEHR_SANDBOX=false`)
- [ ] Callback URL accessible and tested
- [ ] SSL certificate valid for callback URL
- [ ] Error handling implemented for all scenarios
- [ ] Digital receipt duplicate detection in place
- [ ] Amount verification implemented
- [ ] Logging enabled and monitored
- [ ] Rollback service enabled if needed
- [ ] Test payment flow end-to-end

## Support

### Sepehr Electronic Payment Support
- **Website**: https://sepehr.shaparak.ir
- **Support**: Contact for IP whitelisting and rollback service
- **Documentation**: API v3.0.6

### Common Issues

1. **IP Not Whitelisted**: Contact Sepehr support
2. **Token Generation Fails**: Verify Terminal ID and IP
3. **Advice Fails**: Check digital receipt and Terminal ID
4. **Auto-Reversal**: Ensure advice is called within 30 minutes
5. **Rollback Disabled**: Contact Sepehr to enable service

### Debug Commands

```bash
# Test gateway configuration
php artisan test:sepehr-gateway

# Check gateway in database
php artisan tinker
>>> App\Models\PaymentGateway::where('slug', 'sepehr')->first()

# View transaction logs
>>> App\Models\GatewayTransactionLog::latest()->take(10)->get()

# Test callback URL
curl -X POST https://yourdomain.com/payment/callback/sepehr/test-uuid \
  -d "respcode=0&amount=10000&digitalreceipt=test123"
```

## Appendix

### Sepehr API Endpoints

| Service | URL |
|---------|-----|
| GetToken | `https://sepehr.shaparak.ir:8081/V1/PeymentApi/GetToken` |
| Advice | `https://sepehr.shaparak.ir:8081/V1/PeymentApi/Advice` |
| Rollback | `https://sepehr.shaparak.ir:8081/V1/PeymentApi/Rollback` |

### Payment Page URLs

| Type | URL |
|------|-----|
| Standard Purchase | `https://sepehr.shaparak.ir:8080/Pay` |
| Bill Payment | `https://sepehr.shaparak.ir:8080/Bill` |
| Mobile Top-up | `https://sepehr.shaparak.ir:8080/Charge` |
| With Mobile No. | `https://sepehr.shaparak.ir:8080/Mpay` |

### Supported Features Matrix

| Feature | Supported | Auto-Advised | Notes |
|---------|-----------|--------------|-------|
| Standard Purchase | ✅ | No | Requires manual advice |
| Bill Payment | ✅ | Yes | No advice needed |
| Batch Bill Payment | ✅ | Yes | Up to 10 bills |
| Mobile Top-up | ✅ | Yes | No advice needed |
| Identified Purchase | ✅ | No | With national ID |
| Fund Splitting | ✅ | No | Up to 9 accounts |
| Mobile Integration | ✅ | Varies | Depends on type |
| Refunds | ✅ | N/A | Within 30 minutes |

This completes the comprehensive Sepehr Payment Gateway setup guide. For any questions or issues, refer to the debug commands above or contact support. 