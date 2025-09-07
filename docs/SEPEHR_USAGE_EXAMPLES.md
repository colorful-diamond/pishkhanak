# Sepehr Gateway Usage Examples

This document provides comprehensive examples of using the Sepehr payment gateway with all its features and payment types.

## Table of Contents

1. [Basic Setup](#basic-setup)
2. [Standard Purchase](#standard-purchase)
3. [Bill Payment](#bill-payment)
4. [Batch Bill Payment](#batch-bill-payment)
5. [Mobile Top-up](#mobile-top-up)
6. [Identified Purchase](#identified-purchase)
7. [Fund Splitting](#fund-splitting)
8. [Guest Payment Integration](#guest-payment-integration)
9. [Controller Examples](#controller-examples)
10. [Frontend Integration](#frontend-integration)

---

## Basic Setup

First, ensure you have the Sepehr gateway configured:

```php
// In your .env file
SEPEHR_TERMINAL_ID=12345678
SEPEHR_SANDBOX=true
SEPEHR_GET_METHOD=false
SEPEHR_ROLLBACK_ENABLED=false
```

```php
// Run the seeder
php artisan db:seed --class=SepehrGatewaySeeder

// Test the setup
php artisan test:sepehr-gateway
```

---

## Standard Purchase

### Using PaymentService (Recommended)

```php
use App\Services\PaymentService;

class CheckoutController extends Controller
{
    public function createPayment(Request $request, PaymentService $paymentService)
    {
        $result = $paymentService->createPayment([
            'user_id' => auth()->id(),
            'amount' => 50000, // 50,000 Rials
            'currency' => 'IRT',
            'description' => 'خرید محصول',
            'gateway_id' => PaymentGateway::where('slug', 'sepehr')->first()->id,
            'metadata' => [
                'order_id' => $request->order_id,
                'product_name' => $request->product_name,
            ]
        ]);

        if ($result['success']) {
            return view('payments.sepehr-redirect', [
                'transaction' => $result['transaction'],
                'paymentUrl' => $result['data']['payment_url'],
                'terminalId' => $result['data']['terminal_id'],
                'accessToken' => $result['data']['access_token'],
            ]);
        }

        return back()->withErrors($result['message']);
    }
}
```

### Using SepehrPaymentTypes Service

```php
use App\Services\SepehrPaymentTypes;
use App\Services\PaymentGatewayManager;

class ProductController extends Controller
{
    public function purchaseProduct(Request $request, SepehrPaymentTypes $sepehrPayments)
    {
        $user = auth()->user();
        
        // Create purchase transaction
        $transaction = $sepehrPayments->createPurchase(
            amount: 75000,
            description: "خرید محصول {$request->product_name}",
            user: $user,
            metadata: [
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
            ]
        );

        // Get gateway instance and create payment
        $gateway = app(PaymentGatewayManager::class)->gateway('sepehr');
        $result = $gateway->createPayment($transaction);

        if ($result['success']) {
            return view('payments.sepehr-redirect', [
                'transaction' => $transaction,
                'paymentUrl' => $result['payment_url'],
                'terminalId' => $result['terminal_id'],
                'accessToken' => $result['access_token'],
            ]);
        }

        return back()->withErrors($result['message']);
    }
}
```

---

## Bill Payment

### Single Bill Payment

```php
use App\Services\SepehrPaymentTypes;

class BillController extends Controller
{
    public function payBill(Request $request, SepehrPaymentTypes $sepehrPayments)
    {
        $request->validate([
            'bill_id' => 'required|regex:/^\d{13}$|regex:/^\d{18}$/',
            'pay_id' => 'required|numeric',
            'amount' => 'required|integer|min:1000',
        ]);

        // Validate bill ID format
        if (!SepehrPaymentTypes::validateBillId($request->bill_id)) {
            return back()->withErrors('شناسه قبض نامعتبر است');
        }

        $user = auth()->user();
        
        // Create bill payment transaction
        $transaction = $sepehrPayments->createBillPayment(
            billId: $request->bill_id,
            payId: $request->pay_id,
            amount: $request->amount,
            user: $user,
            metadata: [
                'bill_type' => $request->bill_type ?? 'utility',
                'customer_info' => $request->customer_info,
            ]
        );

        // Process payment
        $gateway = app(PaymentGatewayManager::class)->gateway('sepehr');
        $result = $gateway->createPayment($transaction);

        if ($result['success']) {
            return view('payments.sepehr-redirect', [
                'transaction' => $transaction,
                'paymentUrl' => $result['payment_url'],
                'terminalId' => $result['terminal_id'],
                'accessToken' => $result['access_token'],
            ]);
        }

        return back()->withErrors($result['message']);
    }
}
```

### Bill Inquiry Before Payment

```php
use App\Services\Jibit\JibitService;

class BillController extends Controller
{
    public function inquireBill(Request $request, JibitService $jibitService)
    {
        $request->validate([
            'bill_id' => 'required|regex:/^\d{13}$|regex:/^\d{18}$/',
            'pay_id' => 'required|numeric',
        ]);

        // Use Jibit API to get bill information
        $billInfo = $jibitService->getBillInquiry($request->bill_id, $request->pay_id);
        
        if ($billInfo['success']) {
            return response()->json([
                'success' => true,
                'bill_info' => $billInfo['data'],
                'amount' => $billInfo['data']['amount'],
                'description' => $billInfo['data']['description'],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'امکان استعلام قبض وجود ندارد'
        ]);
    }
}
```

---

## Batch Bill Payment

```php
use App\Services\SepehrPaymentTypes;

class BillController extends Controller
{
    public function payMultipleBills(Request $request, SepehrPaymentTypes $sepehrPayments)
    {
        $request->validate([
            'bills' => 'required|array|max:10',
            'bills.*.bill_id' => 'required|regex:/^\d{13}$|regex:/^\d{18}$/',
            'bills.*.pay_id' => 'required|numeric',
            'bills.*.amount' => 'required|integer|min:1000',
        ]);

        // Validate all bill IDs
        foreach ($request->bills as $bill) {
            if (!SepehrPaymentTypes::validateBillId($bill['bill_id'])) {
                return back()->withErrors("شناسه قبض {$bill['bill_id']} نامعتبر است");
            }
        }

        // Prepare bills array for Sepehr
        $bills = [];
        $totalAmount = 0;
        
        foreach ($request->bills as $bill) {
            $bills[] = [
                'BillID' => $bill['bill_id'],
                'PayID' => $bill['pay_id'],
            ];
            $totalAmount += $bill['amount'];
        }

        $user = auth()->user();
        
        // Create batch bill payment transaction
        $transaction = $sepehrPayments->createBatchBillPayment(
            bills: $bills,
            totalAmount: $totalAmount,
            user: $user,
            metadata: [
                'individual_amounts' => array_column($request->bills, 'amount'),
                'bill_types' => array_column($request->bills, 'type'),
            ]
        );

        // Process payment
        $gateway = app(PaymentGatewayManager::class)->gateway('sepehr');
        $result = $gateway->createPayment($transaction);

        if ($result['success']) {
            return view('payments.sepehr-redirect', [
                'transaction' => $transaction,
                'paymentUrl' => $result['payment_url'],
                'terminalId' => $result['terminal_id'],
                'accessToken' => $result['access_token'],
            ]);
        }

        return back()->withErrors($result['message']);
    }
}
```

---

## Mobile Top-up

### Basic Mobile Top-up

```php
use App\Services\SepehrPaymentTypes;

class TopupController extends Controller
{
    public function chargeMobile(Request $request, SepehrPaymentTypes $sepehrPayments)
    {
        $request->validate([
            'mobile' => 'required|regex:/^09\d{9}$/',
            'amount' => 'required|integer|min:1000',
            'operator' => 'required|in:0,1,2',
            'charge_type' => 'nullable|in:0,1,2',
            'request_type' => 'nullable|in:0,1,2',
        ]);

        $user = auth()->user();
        
        // Create mobile top-up transaction
        $transaction = $sepehrPayments->createMobileTopup(
            mobileNumber: $request->mobile,
            amount: $request->amount,
            operator: $request->operator,
            chargeType: $request->charge_type ?? 0,
            requestType: $request->request_type ?? 0,
            user: $user
        );

        // Process payment
        $gateway = app(PaymentGatewayManager::class)->gateway('sepehr');
        $result = $gateway->createPayment($transaction);

        if ($result['success']) {
            return view('payments.sepehr-redirect', [
                'transaction' => $transaction,
                'paymentUrl' => $result['payment_url'],
                'terminalId' => $result['terminal_id'],
                'accessToken' => $result['access_token'],
            ]);
        }

        return back()->withErrors($result['message']);
    }

    public function getOperators()
    {
        return response()->json([
            'operators' => SepehrPaymentTypes::getOperators(),
            'charge_types' => SepehrPaymentTypes::getChargeTypes(),
            'request_types' => SepehrPaymentTypes::getRequestTypes(),
        ]);
    }
}
```

### Data Package Purchase

```php
class TopupController extends Controller
{
    public function purchaseDataPackage(Request $request, SepehrPaymentTypes $sepehrPayments)
    {
        $request->validate([
            'mobile' => 'required|regex:/^09\d{9}$/',
            'amount' => 'required|integer|min:1000',
            'operator' => 'required|in:0,1,2',
            'dataplan_id' => 'required|integer',
        ]);

        $user = auth()->user();
        
        // Create data package transaction
        $transaction = $sepehrPayments->createMobileTopup(
            mobileNumber: $request->mobile,
            amount: $request->amount,
            operator: $request->operator,
            chargeType: 0,
            requestType: 2, // Data plan
            dataplanId: $request->dataplan_id,
            user: $user,
            metadata: [
                'package_name' => $request->package_name,
                'package_duration' => $request->package_duration,
            ]
        );

        // Process payment
        $gateway = app(PaymentGatewayManager::class)->gateway('sepehr');
        $result = $gateway->createPayment($transaction);

        if ($result['success']) {
            return view('payments.sepehr-redirect', [
                'transaction' => $transaction,
                'paymentUrl' => $result['payment_url'],
                'terminalId' => $result['terminal_id'],
                'accessToken' => $result['access_token'],
            ]);
        }

        return back()->withErrors($result['message']);
    }
}
```

---

## Identified Purchase

```php
use App\Services\SepehrPaymentTypes;

class IdentifiedPurchaseController extends Controller
{
    public function createIdentifiedPurchase(Request $request, SepehrPaymentTypes $sepehrPayments)
    {
        $request->validate([
            'amount' => 'required|integer|min:1000',
            'description' => 'required|string|max:255',
            'national_code' => 'required|regex:/^\d{10}$/',
        ]);

        // Validate national code format
        if (!SepehrPaymentTypes::validateNationalCode($request->national_code)) {
            return back()->withErrors('کد ملی نامعتبر است');
        }

        $user = auth()->user();
        
        // Create identified purchase transaction
        $transaction = $sepehrPayments->createIdentifiedPurchase(
            amount: $request->amount,
            description: $request->description,
            nationalCode: $request->national_code,
            user: $user,
            metadata: [
                'verification_required' => true,
                'customer_verification' => $request->customer_verification,
            ]
        );

        // Process payment
        $gateway = app(PaymentGatewayManager::class)->gateway('sepehr');
        $result = $gateway->createPayment($transaction);

        if ($result['success']) {
            return view('payments.sepehr-redirect', [
                'transaction' => $transaction,
                'paymentUrl' => $result['payment_url'],
                'terminalId' => $result['terminal_id'],
                'accessToken' => $result['access_token'],
                'nationalCode' => $request->national_code, // Pass to view for form
            ]);
        }

        return back()->withErrors($result['message']);
    }
}
```

---

## Fund Splitting

```php
use App\Services\SepehrPaymentTypes;

class MarketplaceController extends Controller
{
    public function createSplitPayment(Request $request, SepehrPaymentTypes $sepehrPayments)
    {
        $request->validate([
            'amount' => 'required|integer|min:1000',
            'description' => 'required|string|max:255',
            'splits' => 'required|array|max:9',
            'splits.*.account' => 'required|string',
            'splits.*.amount' => 'required|integer|min:1',
        ]);

        // Validate split amounts total equals main amount
        $totalSplitAmount = array_sum(array_column($request->splits, 'amount'));
        if ($totalSplitAmount !== $request->amount) {
            return back()->withErrors('مجموع مبالغ تسهیم باید برابر مبلغ کل باشد');
        }

        // Prepare split accounts
        $splitAccounts = [];
        foreach ($request->splits as $split) {
            $splitAccounts[] = [
                'Account' => $split['account'],
                'Amount' => (string) $split['amount'],
            ];
        }

        $user = auth()->user();
        
        // Create fund splitting transaction
        $transaction = $sepehrPayments->createFundSplitting(
            totalAmount: $request->amount,
            splitAccounts: $splitAccounts,
            splitId: $request->split_id ?? '0001',
            description: $request->description,
            user: $user,
            metadata: [
                'marketplace_order_id' => $request->order_id,
                'vendor_info' => $request->vendor_info,
            ]
        );

        // Process payment
        $gateway = app(PaymentGatewayManager::class)->gateway('sepehr');
        $result = $gateway->createPayment($transaction);

        if ($result['success']) {
            return view('payments.sepehr-redirect', [
                'transaction' => $transaction,
                'paymentUrl' => $result['payment_url'],
                'terminalId' => $result['terminal_id'],
                'accessToken' => $result['access_token'],
            ]);
        }

        return back()->withErrors($result['message']);
    }
}
```

---

## Guest Payment Integration

```php
use App\Services\SepehrPaymentTypes;

class GuestPaymentController extends Controller
{
    public function createGuestPayment(Request $request, SepehrPaymentTypes $sepehrPayments)
    {
        $request->validate([
            'amount' => 'required|integer|min:1000',
            'description' => 'required|string|max:255',
            'guest_email' => 'required|email',
            'guest_phone' => 'nullable|regex:/^09\d{9}$/',
        ]);

        // Generate guest payment hash (your existing system)
        $guestPaymentHash = $this->generateGuestPaymentHash([
            'email' => $request->guest_email,
            'phone' => $request->guest_phone,
            'session_id' => session()->getId(),
            'ip_address' => request()->ip(),
        ]);

        // Create purchase transaction
        $transaction = $sepehrPayments->createPurchase(
            amount: $request->amount,
            description: $request->description,
            user: null, // Guest user
            metadata: [
                'guest_email' => $request->guest_email,
                'guest_phone' => $request->guest_phone,
                'session_id' => session()->getId(),
            ]
        );

        // Add guest payment hash
        $transaction = $sepehrPayments->addGuestPaymentHash($transaction, $guestPaymentHash);

        // Process payment
        $gateway = app(PaymentGatewayManager::class)->gateway('sepehr');
        $result = $gateway->createPayment($transaction);

        if ($result['success']) {
            return view('payments.sepehr-redirect', [
                'transaction' => $transaction,
                'paymentUrl' => $result['payment_url'],
                'terminalId' => $result['terminal_id'],
                'accessToken' => $result['access_token'],
            ]);
        }

        return back()->withErrors($result['message']);
    }

    private function generateGuestPaymentHash(array $data): string
    {
        // Your existing guest hash generation logic
        return hash('sha256', json_encode($data) . config('app.key'));
    }
}
```

---

## Controller Examples

### Payment Callback Handler

```php
use App\Http\Controllers\Controller;
use App\Models\GatewayTransaction;
use App\Services\PaymentGatewayManager;

class PaymentCallbackController extends Controller
{
    public function handleSepehrCallback(
        Request $request, 
        string $transactionUuid,
        PaymentGatewayManager $gatewayManager
    ) {
        $transaction = GatewayTransaction::where('uuid', $transactionUuid)->firstOrFail();
        $gateway = $gatewayManager->gateway('sepehr');
        
        $callbackData = $request->all();
        $result = $gateway->verifyPayment($transaction, $callbackData);
        
        if ($result['success']) {
            // Payment verified successfully
            $transaction->markAsCompleted();
            
            // Process based on transaction type
            $this->processSuccessfulPayment($transaction, $result);
            
            return redirect()->route('payment.success', $transaction->uuid)
                ->with('success', 'پرداخت با موفقیت انجام شد');
        } else {
            // Payment verification failed
            $transaction->markAsFailed($result['message']);
            
            return redirect()->route('payment.failed', $transaction->uuid)
                ->with('error', $result['message']);
        }
    }

    private function processSuccessfulPayment(GatewayTransaction $transaction, array $result)
    {
        $metadata = $transaction->metadata ?? [];
        $transactionType = $metadata['transaction_type'] ?? 'purchase';

        switch ($transactionType) {
            case 'bill_payment':
            case 'batch_bill_payment':
                // Bills are auto-advised, just log the success
                Log::info('Bill payment completed', [
                    'transaction_id' => $transaction->uuid,
                    'bills' => $metadata['bills'] ?? null,
                ]);
                break;

            case 'mobile_topup':
                // Mobile top-up is auto-advised, process the top-up
                $this->processMobileTopup($transaction, $metadata);
                break;

            case 'purchase':
            case 'identified_purchase':
                // Standard purchase, process the order
                $this->processOrderCompletion($transaction, $metadata);
                break;

            case 'fund_splitting':
                // Handle marketplace split
                $this->processMarketplaceSplit($transaction, $metadata);
                break;
        }
    }
}
```

---

## Frontend Integration

### Payment Form Examples

#### Standard Payment Form

```html
<!-- resources/views/payments/sepehr-form.blade.php -->
<form action="{{ route('payment.sepehr.create') }}" method="POST" class="payment-form">
    @csrf
    
    <div class="form-group">
        <label>مبلغ پرداخت (تومان)</label>
        <input type="number" name="amount" min="1000" required class="form-control">
    </div>
    
    <div class="form-group">
        <label>توضیحات</label>
        <input type="text" name="description" required class="form-control">
    </div>
    
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-credit-card"></i> پرداخت با سپهر
    </button>
</form>
```

#### Bill Payment Form

```html
<!-- resources/views/bills/payment-form.blade.php -->
<form action="{{ route('bills.pay') }}" method="POST" class="bill-form">
    @csrf
    
    <div class="row">
        <div class="col-md-6">
            <label>شناسه قبض</label>
            <input type="text" name="bill_id" pattern="\d{13}|\d{18}" required class="form-control">
            <small class="text-muted">13 یا 18 رقم</small>
        </div>
        
        <div class="col-md-6">
            <label>شناسه پرداخت</label>
            <input type="text" name="pay_id" pattern="\d+" required class="form-control">
        </div>
    </div>
    
    <div class="form-group">
        <label>مبلغ قبض (تومان)</label>
        <input type="number" name="amount" min="1000" required class="form-control">
    </div>
    
    <button type="button" class="btn btn-info" onclick="inquireBill()">
        <i class="fas fa-search"></i> استعلام قبض
    </button>
    
    <button type="submit" class="btn btn-success">
        <i class="fas fa-file-invoice"></i> پرداخت قبض
    </button>
</form>

<script>
function inquireBill() {
    const billId = document.querySelector('[name="bill_id"]').value;
    const payId = document.querySelector('[name="pay_id"]').value;
    
    if (!billId || !payId) {
        alert('لطفا شناسه قبض و شناسه پرداخت را وارد کنید');
        return;
    }
    
    fetch('{{ route("bills.inquire") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ bill_id: billId, pay_id: payId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.querySelector('[name="amount"]').value = data.amount;
            alert(`مبلغ قبض: ${data.amount.toLocaleString()} تومان`);
        } else {
            alert('خطا در استعلام قبض');
        }
    });
}
</script>
```

#### Mobile Top-up Form

```html
<!-- resources/views/topup/form.blade.php -->
<form action="{{ route('topup.charge') }}" method="POST" class="topup-form">
    @csrf
    
    <div class="form-group">
        <label>شماره موبایل</label>
        <input type="tel" name="mobile" pattern="09\d{9}" required class="form-control">
        <small class="text-muted">مثال: 09123456789</small>
    </div>
    
    <div class="form-group">
        <label>اپراتور</label>
        <select name="operator" required class="form-control">
            <option value="0">ایرانسل</option>
            <option value="1">همراه اول</option>
            <option value="2">رایتل</option>
        </select>
    </div>
    
    <div class="form-group">
        <label>نوع شارژ</label>
        <select name="charge_type" class="form-control">
            <option value="0">عادی</option>
            <option value="1">ایرانسل وو</option>
            <option value="2">رایتل وو</option>
        </select>
    </div>
    
    <div class="form-group">
        <label>نوع درخواست</label>
        <select name="request_type" class="form-control">
            <option value="0">شارژ مستقیم</option>
            <option value="1">رمز شارژ</option>
            <option value="2">بسته اینترنتی</option>
        </select>
    </div>
    
    <div class="form-group">
        <label>مبلغ شارژ (تومان)</label>
        <select name="amount" required class="form-control">
            <option value="10000">10,000 تومان</option>
            <option value="20000">20,000 تومان</option>
            <option value="50000">50,000 تومان</option>
            <option value="100000">100,000 تومان</option>
        </select>
    </div>
    
    <button type="submit" class="btn btn-success">
        <i class="fas fa-mobile-alt"></i> شارژ موبایل
    </button>
</form>
```

### JavaScript Helpers

```javascript
// resources/js/sepehr-helpers.js

// Validate Iranian mobile number
function validateMobile(mobile) {
    return /^09\d{9}$/.test(mobile);
}

// Validate bill ID
function validateBillId(billId) {
    return /^\d{13}$|^\d{18}$/.test(billId);
}

// Validate national code
function validateNationalCode(nationalCode) {
    if (!/^\d{10}$/.test(nationalCode)) {
        return false;
    }
    
    // Iranian national code validation algorithm
    const digits = nationalCode.split('').map(Number);
    const checksum = digits[9];
    
    let sum = 0;
    for (let i = 0; i < 9; i++) {
        sum += digits[i] * (10 - i);
    }
    
    const remainder = sum % 11;
    return (remainder < 2) ? checksum === remainder : checksum === (11 - remainder);
}

// Format amount with thousand separators
function formatAmount(amount) {
    return amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

// Auto-format inputs
document.addEventListener('DOMContentLoaded', function() {
    // Format mobile inputs
    document.querySelectorAll('input[type="tel"]').forEach(input => {
        input.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.length <= 11) {
                this.value = value;
            }
        });
    });
    
    // Format amount inputs
    document.querySelectorAll('input[name="amount"]').forEach(input => {
        input.addEventListener('input', function() {
            let value = this.value.replace(/,/g, '');
            if (!isNaN(value) && value !== '') {
                this.value = formatAmount(value);
            }
        });
    });
});
```

---

## Testing Examples

### Unit Tests

```php
// tests/Feature/SepehrGatewayTest.php

use Tests\TestCase;
use App\Services\SepehrPaymentTypes;
use App\Models\User;
use App\Models\PaymentGateway;

class SepehrGatewayTest extends TestCase
{
    protected SepehrPaymentTypes $sepehrPayments;
    protected User $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->sepehrPayments = new SepehrPaymentTypes();
        $this->user = User::factory()->create();
    }

    public function test_creates_standard_purchase()
    {
        $transaction = $this->sepehrPayments->createPurchase(
            amount: 50000,
            description: 'Test purchase',
            user: $this->user
        );

        $this->assertEquals(50000, $transaction->total_amount);
        $this->assertEquals('purchase', $transaction->metadata['transaction_type']);
        $this->assertEquals($this->user->id, $transaction->user_id);
    }

    public function test_creates_bill_payment()
    {
        $transaction = $this->sepehrPayments->createBillPayment(
            billId: '1234567890123',
            payId: '9876543210',
            amount: 25000,
            user: $this->user
        );

        $this->assertEquals(25000, $transaction->total_amount);
        $this->assertEquals('bill_payment', $transaction->metadata['transaction_type']);
        $this->assertEquals('1234567890123', $transaction->metadata['bill_id']);
    }

    public function test_validates_mobile_number()
    {
        $this->expectException(\InvalidArgumentException::class);
        
        $this->sepehrPayments->createMobileTopup(
            mobileNumber: '0912345', // Invalid
            amount: 10000,
            user: $this->user
        );
    }

    public function test_validates_national_code()
    {
        $this->expectException(\InvalidArgumentException::class);
        
        $this->sepehrPayments->createIdentifiedPurchase(
            amount: 30000,
            description: 'Test',
            nationalCode: '123', // Invalid
            user: $this->user
        );
    }
}
```

This comprehensive guide covers all aspects of using the Sepehr payment gateway with practical examples and best practices. Use these examples as a starting point for your specific implementation needs. 