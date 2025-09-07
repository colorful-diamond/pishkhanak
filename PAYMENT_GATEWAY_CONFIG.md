# Payment Gateway Configuration Guide

## 🔧 Environment Variables Required

Add these variables to your `.env` file:

```bash
# Sepehr Electronic Payment Gateway
SEPEHR_TERMINAL_ID=12345678
SEPEHR_SANDBOX=true
SEPEHR_GET_METHOD=false
SEPEHR_ROLLBACK_ENABLED=false

# AsanPardakht Payment Gateway
ASANPARDAKHT_MERCHANT_ID=your_merchant_id
ASANPARDAKHT_USERNAME=your_username
ASANPARDAKHT_PASSWORD=your_password
ASANPARDAKHT_SANDBOX=true

# Jibit Payment Gateway (PPG)
JIBIT_PPG_API_KEY=your_jibit_ppg_api_key
JIBIT_PPG_API_SECRET=your_jibit_ppg_secret
JIBIT_PPG_SANDBOX=true

# Jibit IDE Services (different from PPG)
JIBIT_API_KEY=your_jibit_ide_api_key
JIBIT_SECRET_KEY=your_jibit_ide_secret
JIBIT_SANDBOX=true
```

## 🚀 Setup Commands

### 1. Seed Payment Gateways
```bash
php artisan payment:seed-gateways
```

### 2. Test Gateway Configuration
```bash
php artisan payment:test-flow --gateway=sepehr
```

### 3. Debug Gateway Fees
Visit: `/debug-gateway-fees` to check current gateway configurations.

## 🏦 Gateway Fee Structure

| Gateway | Fee Percentage | Fee Fixed | Notes |
|---------|---------------|-----------|--------|
| Sepehr | 0.0% | 0 IRT | No fees |
| AsanPardakht | 2.5% | 500 IRT | Standard merchant fees |
| Jibit | 0.0% | 0 IRT | No fees |

## ⚠️ Common Issues

### Issue: "terminalID":0 in Sepehr requests
**Solution:** Set `SEPEHR_TERMINAL_ID` in your .env file

### Issue: Amount becomes 109,000 instead of 100,000  
**Solution:** Check gateway fee settings - should be 0% for most gateways

### Issue: "Status":-2 from Sepehr
**Solutions:**
- Verify terminal ID is correct (8 digits)
- Check sandbox mode setting
- Verify Sepehr credentials

## 🔍 Debug Commands

### Check Gateway Status
```bash
php artisan tinker
>>> \App\Models\PaymentGateway::all()->pluck('name', 'id')
>>> \App\Models\PaymentGateway::find(4) // Check specific gateway
```

### Test Fee Calculation
```bash
php artisan tinker
>>> $gateway = \App\Models\PaymentGateway::find(4)
>>> $gateway->calculateFee(100000) // Should return fee amount
```

## 🛠️ Fix High Fees

If a gateway has incorrect fees (like 9%), update it:

```bash
php artisan tinker
>>> $gateway = \App\Models\PaymentGateway::find(4)
>>> $gateway->update(['fee_percentage' => 0.0, 'fee_fixed' => 0])
```

## 📝 Notes

- Gateway fees should typically be 0% for wallet charges
- Merchant fees are usually handled by the payment provider separately
- Fees shown to users should be informational, not added to payment amount 