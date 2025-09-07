# Service Payment System Implementation

This document describes the implementation of a comprehensive service payment system that integrates with the existing payment infrastructure and connects services to real APIs.

## Overview

The service payment system provides:

1. **Service Pricing**: Each service can have a price and cost associated with it
2. **Wallet Integration**: Users can pay for services using their wallet balance
3. **Guest Payment Flow**: Guest users are redirected to payment gateways
4. **Real API Integration**: IBAN-related services use Jibit API
5. **Payment Gateway Integration**: Uses existing Asan Pardakht gateway
6. **Data Persistence**: Service data is stored in payment metadata and recovered after payment

## System Architecture

### Core Components

1. **ServicePaymentService**: Main service that handles payment flow logic
2. **Updated Service Controllers**: Modified to use real APIs (Jibit)
3. **Payment Controller Integration**: Handles service payment callbacks
4. **Database Schema**: Added pricing fields to services table
5. **Service Request Tracking**: Stores pending service requests

### Payment Flow

#### For Authenticated Users with Sufficient Balance:
1. User submits service form
2. System deducts amount from wallet
3. Service is processed with real API
4. Result is stored and displayed

#### For Authenticated Users with Insufficient Balance:
1. User submits service form
2. System redirects to payment gateway
3. After payment, wallet is charged and service is processed
4. Result is stored and displayed

#### For Guest Users:
1. Guest submits service form
2. System redirects to payment gateway
3. After payment, service is processed
4. Result is stored and displayed

## Database Changes

### New Migration: `add_pricing_fields_to_services_table`

```php
Schema::table('services', function (Blueprint $table) {
    $table->boolean('is_paid')->default(false)->after('parent_id');
    $table->integer('price')->default(0)->after('is_paid');
    $table->integer('cost')->default(0)->after('price');
    $table->string('currency', 3)->default('IRR')->after('cost');
});
```

### New Migration: `create_service_requests_table`

```php
Schema::create('service_requests', function (Blueprint $table) {
    $table->id();
    $table->foreignId('service_id')->constrained()->onDelete('cascade');
    $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
    $table->json('input_data');
    $table->string('status'); // free, guest, insufficient_balance, wallet
    $table->string('payment_transaction_id')->nullable();
    $table->timestamp('processed_at')->nullable();
    $table->timestamps();
});
```

## API Integration

### Jibit API Integration

All IBAN-related services now use the Jibit API:

- **Card to IBAN**: `$jibitService->getCardToIban($cardNumber)`
- **Card to Account**: `$jibitService->getCardToAccount($cardNumber)`
- **IBAN to Account**: `$jibitService->getSheba($iban)`
- **IBAN Validation**: `$jibitService->getSheba($iban)`

### Service Pricing

Default pricing for IBAN services:

- **Card/IBAN Services**: 5,000 IRR (API cost: 2,000 IRR)
- **Validation Services**: 3,000 IRR (API cost: 1,500 IRR)

## Installation & Setup

### 1. Run Migrations

```bash
php artisan migrate
```

### 2. Seed Service Pricing

```bash
php artisan db:seed --class=ServicePricingSeeder
```

### 3. Configure Jibit API

Add to your `.env` file:

```env
JIBIT_API_KEY=your_jibit_api_key
JIBIT_SECRET_KEY=your_jibit_secret_key
```

### 4. Configure Asan Pardakht

Ensure your Asan Pardakht configuration is set up in the admin panel or `.env`:

```env
ASANPARDAKHT_MERCHANT_ID=your_merchant_id
ASANPARDAKHT_USERNAME=your_username
ASANPARDAKHT_PASSWORD=your_password
ASANPARDAKHT_SANDBOX=true
```

## Usage

### For Users

1. **Browse Services**: Visit any service page
2. **View Pricing**: See service cost and wallet status
3. **Submit Form**: Enter required data and submit
4. **Payment Flow**: 
   - If authenticated with sufficient balance: Direct processing
   - If authenticated with insufficient balance: Redirect to payment
   - If guest: Redirect to payment
5. **View Results**: See processed results on dedicated result page

### For Developers

#### Adding New Paid Services

1. **Update Service Model**: Set `is_paid = true` and `price` field
2. **Create Service Controller**: Implement `BaseServiceController` interface
3. **Add API Integration**: Connect to appropriate external API
4. **Update Pricing**: Add to `ServicePricingSeeder`

#### Service Controller Example

```php
class YourServiceController extends Controller implements BaseServiceController
{
    public function handle(Request $request, Service $service)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'field_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Process with API
        $apiResult = $this->callExternalApi($request->input('field_name'));
        
        // Store result
        $serviceResult = ServiceResult::create([
            'service_id' => $service->id,
            'input_data' => $request->all(),
            'output_data' => $apiResult,
            'status' => 'success',
            'processed_at' => now(),
        ]);

        return redirect()->route('services.result', ['id' => $serviceResult->result_hash]);
    }

    public function show(string $resultId, Service $service)
    {
        $result = ServiceResult::where('result_hash', $resultId)
            ->where('service_id', $service->id)
            ->firstOrFail();

        return view('front.services.result', [
            'service' => $service,
            'result' => $result->getFormattedResult(),
            'inputData' => $result->input_data,
        ]);
    }
}
```

## Payment Gateway Integration

### Asan Pardakht Extra Data

The system uses Asan Pardakht's `additionalData` field to store service information:

```php
'additionalData' => json_encode([
    'service_id' => $service->id,
    'service_request_id' => $serviceRequest->id,
    'service_data' => $serviceData,
    'type' => 'service_payment',
])
```

This data is recovered after payment completion to process the service.

### Callback Handling

Payment callbacks are handled in `PaymentController::handleCallback()` and route to:

1. **Service Payment Processing**: For service payments
2. **Wallet Charging**: For regular wallet charges

## Security Features

1. **Input Validation**: All service inputs are validated
2. **CSRF Protection**: Forms include CSRF tokens
3. **Payment Verification**: All payments are verified with gateways
4. **Data Encryption**: Sensitive data is encrypted in database
5. **Rate Limiting**: API calls are rate-limited
6. **Error Handling**: Comprehensive error handling and logging

## Monitoring & Logging

### Key Log Events

- Service submission attempts
- Payment processing
- API call results
- Error conditions

### Monitoring Points

- Payment success/failure rates
- API response times
- Service usage statistics
- Error rates

## Testing

### Test Commands

```bash
# Test service controllers
php artisan services:test card-iban --input=6037991234567890

# List available controllers
php artisan services:list-controllers

# Cleanup expired results
php artisan services:cleanup-results --days=30
```

### Test Scenarios

1. **Free Services**: Should process immediately
2. **Paid Services with Sufficient Balance**: Should deduct from wallet
3. **Paid Services with Insufficient Balance**: Should redirect to payment
4. **Guest Services**: Should redirect to payment
5. **Payment Failures**: Should handle gracefully
6. **API Failures**: Should provide meaningful errors

## Troubleshooting

### Common Issues

1. **Payment Gateway Errors**: Check gateway configuration
2. **API Connection Issues**: Verify Jibit API credentials
3. **Wallet Balance Issues**: Check Bavix wallet configuration
4. **Service Processing Errors**: Check service controller implementation

### Debug Commands

```bash
# Check service pricing
php artisan tinker
>>> App\Models\Service::where('is_paid', true)->get(['slug', 'price', 'currency'])

# Check payment transactions
>>> App\Models\GatewayTransaction::where('type', 'like', '%service%')->latest()->get()

# Check service results
>>> App\Models\ServiceResult::latest()->limit(5)->get()
```

## Future Enhancements

1. **Multiple Payment Gateways**: Support for additional gateways
2. **Service Bundles**: Package multiple services together
3. **Subscription Services**: Recurring service payments
4. **Advanced Analytics**: Detailed usage and revenue analytics
5. **Mobile App Integration**: API endpoints for mobile apps
6. **Webhook Support**: Real-time notifications for service completion

## Support

For technical support or questions about the service payment system, please refer to:

- Service Controller Documentation: `docs/SERVICE_CONTROLLERS.md`
- Payment System Documentation: `PAYMENT_SETUP.md`
- API Documentation: Jibit and Asan Pardakht official docs 