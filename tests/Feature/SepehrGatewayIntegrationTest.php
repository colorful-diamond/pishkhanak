<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\PaymentGateway;
use App\Models\GatewayTransaction;
use App\Models\Currency;
use App\Models\User;
use App\Services\SepehrPaymentTypes;
use App\Services\PaymentGatewayManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class SepehrGatewayIntegrationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected PaymentGateway $sepehrGateway;
    protected Currency $currency;
    protected User $user;
    protected SepehrPaymentTypes $sepehrPayments;

    protected function setUp(): void
    {
        parent::setUp();

        // Create currency
        $this->currency = Currency::factory()->create([
            'code' => 'IRT',
            'name' => 'Iranian Toman',
            'symbol' => 'تومان',
        ]);

        // Create test user
        $this->user = User::factory()->create();

        // Create Sepehr gateway
        $this->sepehrGateway = PaymentGateway::factory()->create([
            'name' => 'Sepehr Electronic Payment Gateway',
            'slug' => 'sepehr',
            'driver' => 'App\Services\PaymentGateways\SepehrGateway',
            'is_active' => true,
            'config' => [
                'terminal_id' => '12345678',
                'sandbox' => true,
                'get_method' => false,
                'rollback_enabled' => false,
            ],
            'supported_currencies' => ['IRT'],
        ]);

        $this->sepehrPayments = new SepehrPaymentTypes();
    }

    /** @test */
    public function it_creates_sepehr_gateway_successfully()
    {
        $this->assertDatabaseHas('payment_gateways', [
            'slug' => 'sepehr',
            'driver' => 'App\Services\PaymentGateways\SepehrGateway',
        ]);
    }

    /** @test */
    public function it_instantiates_gateway_class_successfully()
    {
        $gatewayManager = app(PaymentGatewayManager::class);
        $gateway = $gatewayManager->gateway('sepehr');

        $this->assertInstanceOf('App\Services\PaymentGateways\SepehrGateway', $gateway);
    }

    /** @test */
    public function it_creates_standard_purchase_transaction()
    {
        $transaction = $this->sepehrPayments->createPurchase(
            amount: 50000,
            description: 'Test purchase',
            user: $this->user
        );

        $this->assertInstanceOf(GatewayTransaction::class, $transaction);
        $this->assertEquals(50000, $transaction->total_amount);
        $this->assertEquals('purchase', $transaction->metadata['transaction_type']);
        $this->assertEquals($this->user->id, $transaction->user_id);
        $this->assertEquals($this->sepehrGateway->id, $transaction->payment_gateway_id);
    }

    /** @test */
    public function it_creates_bill_payment_transaction()
    {
        $transaction = $this->sepehrPayments->createBillPayment(
            billId: '1234567890123',
            payId: '9876543210',
            amount: 25000,
            user: $this->user
        );

        $this->assertEquals('bill_payment', $transaction->metadata['transaction_type']);
        $this->assertEquals('1234567890123', $transaction->metadata['bill_id']);
        $this->assertEquals('9876543210', $transaction->metadata['pay_id']);
        $this->assertEquals(25000, $transaction->total_amount);
    }

    /** @test */
    public function it_validates_bill_id_format()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Bill ID must be 13 or 18 digits');

        $this->sepehrPayments->createBillPayment(
            billId: '12345', // Invalid length
            payId: '9876543210',
            amount: 25000,
            user: $this->user
        );
    }

    /** @test */
    public function it_creates_batch_bill_payment_transaction()
    {
        $bills = [
            ['BillID' => '1234567890123', 'PayID' => '9876543210'],
            ['BillID' => '1234567890124', 'PayID' => '9876543211'],
        ];

        $transaction = $this->sepehrPayments->createBatchBillPayment(
            bills: $bills,
            totalAmount: 50000,
            user: $this->user
        );

        $this->assertEquals('batch_bill_payment', $transaction->metadata['transaction_type']);
        $this->assertEquals($bills, $transaction->metadata['bills']);
        $this->assertEquals(2, $transaction->metadata['bill_count']);
    }

    /** @test */
    public function it_validates_batch_bill_limit()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Maximum 10 bills allowed in batch payment');

        // Create 11 bills (exceeds limit)
        $bills = [];
        for ($i = 0; $i < 11; $i++) {
            $bills[] = ['BillID' => '123456789012' . $i, 'PayID' => '987654321' . $i];
        }

        $this->sepehrPayments->createBatchBillPayment(
            bills: $bills,
            totalAmount: 110000,
            user: $this->user
        );
    }

    /** @test */
    public function it_creates_mobile_topup_transaction()
    {
        $transaction = $this->sepehrPayments->createMobileTopup(
            mobileNumber: '09123456789',
            amount: 20000,
            operator: 0, // MTN
            user: $this->user
        );

        $this->assertEquals('mobile_topup', $transaction->metadata['transaction_type']);
        $this->assertEquals('09123456789', $transaction->metadata['mobile_data']['mobile']);
        $this->assertEquals(0, $transaction->metadata['mobile_data']['operator']);
        $this->assertEquals('ایرانسل', $transaction->metadata['operator_name']);
    }

    /** @test */
    public function it_validates_mobile_number_format()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Mobile number must be in format 09xxxxxxxxx');

        $this->sepehrPayments->createMobileTopup(
            mobileNumber: '0912345', // Invalid format
            amount: 20000,
            operator: 0,
            user: $this->user
        );
    }

    /** @test */
    public function it_creates_identified_purchase_transaction()
    {
        $transaction = $this->sepehrPayments->createIdentifiedPurchase(
            amount: 30000,
            description: 'Test identified purchase',
            nationalCode: '1234567890',
            user: $this->user
        );

        $this->assertEquals('identified_purchase', $transaction->metadata['transaction_type']);
        $this->assertEquals('1234567890', $transaction->metadata['national_code']);
    }

    /** @test */
    public function it_validates_national_code_format()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('National code must be 10 digits');

        $this->sepehrPayments->createIdentifiedPurchase(
            amount: 30000,
            description: 'Test',
            nationalCode: '123', // Invalid format
            user: $this->user
        );
    }

    /** @test */
    public function it_creates_fund_splitting_transaction()
    {
        $splitAccounts = [
            ['Account' => 'M01', 'Amount' => '30000'],
            ['Account' => 'M02', 'Amount' => '20000'],
        ];

        $transaction = $this->sepehrPayments->createFundSplitting(
            totalAmount: 50000,
            splitAccounts: $splitAccounts,
            user: $this->user
        );

        $this->assertEquals('fund_splitting', $transaction->metadata['transaction_type']);
        $this->assertEquals($splitAccounts, $transaction->metadata['split_accounts']);
        $this->assertEquals(2, $transaction->metadata['account_count']);
    }

    /** @test */
    public function it_validates_fund_splitting_amounts()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Total split amount must equal transaction amount');

        $splitAccounts = [
            ['Account' => 'M01', 'Amount' => '30000'],
            ['Account' => 'M02', 'Amount' => '15000'], // Total 45000 != 50000
        ];

        $this->sepehrPayments->createFundSplitting(
            totalAmount: 50000,
            splitAccounts: $splitAccounts,
            user: $this->user
        );
    }

    /** @test */
    public function it_adds_guest_payment_hash()
    {
        $transaction = $this->sepehrPayments->createPurchase(
            amount: 40000,
            description: 'Guest purchase',
            user: null
        );

        $guestHash = 'test_guest_hash_123456';
        $updatedTransaction = $this->sepehrPayments->addGuestPaymentHash($transaction, $guestHash);

        $this->assertEquals($guestHash, $updatedTransaction->metadata['guest_payment_hash']);
    }

    /** @test */
    public function it_generates_reference_ids_with_correct_format()
    {
        $transaction = $this->sepehrPayments->createPurchase(
            amount: 10000,
            description: 'Test',
            user: $this->user
        );

        $this->assertStringStartsWith('PURCHASE_', $transaction->reference_id);
        $this->assertMatchesRegularExpression('/^PURCHASE_\d{14}_\d{4}$/', $transaction->reference_id);
    }

    /** @test */
    public function it_validates_operator_values()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Operator must be 0 (MTN), 1 (MCI), or 2 (Rightel)');

        $this->sepehrPayments->createMobileTopup(
            mobileNumber: '09123456789',
            amount: 20000,
            operator: 5, // Invalid operator
            user: $this->user
        );
    }

    /** @test */
    public function it_provides_correct_operators_list()
    {
        $operators = SepehrPaymentTypes::getOperators();

        $this->assertIsArray($operators);
        $this->assertArrayHasKey(0, $operators);
        $this->assertArrayHasKey(1, $operators);
        $this->assertArrayHasKey(2, $operators);
        $this->assertEquals('ایرانسل (MTN)', $operators[0]);
        $this->assertEquals('همراه اول (MCI)', $operators[1]);
        $this->assertEquals('رایتل (Rightel)', $operators[2]);
    }

    /** @test */
    public function it_provides_validation_methods()
    {
        $this->assertTrue(SepehrPaymentTypes::validateBillId('1234567890123'));
        $this->assertTrue(SepehrPaymentTypes::validateBillId('123456789012345678'));
        $this->assertFalse(SepehrPaymentTypes::validateBillId('12345'));

        $this->assertTrue(SepehrPaymentTypes::validateMobileNumber('09123456789'));
        $this->assertFalse(SepehrPaymentTypes::validateMobileNumber('0912345'));

        $this->assertTrue(SepehrPaymentTypes::validateNationalCode('1234567890'));
        $this->assertFalse(SepehrPaymentTypes::validateNationalCode('123'));
    }

    /** @test */
    public function it_handles_gateway_configuration_properly()
    {
        $gatewayManager = app(PaymentGatewayManager::class);
        $gateway = $gatewayManager->gateway('sepehr');

        $requirements = $gateway->getConfigRequirements();
        $this->assertContains('terminal_id', $requirements);
        $this->assertContains('sandbox', $requirements);

        $currencies = $gateway->getSupportedCurrencies();
        $this->assertContains('IRT', $currencies);

        $limits = $gateway->getAmountLimits();
        $this->assertEquals(1000, $limits['min']);
        $this->assertEquals(500000000, $limits['max']);
    }

    /** @test */
    public function it_generates_correct_callback_url()
    {
        $gatewayManager = app(PaymentGatewayManager::class);
        $gateway = $gatewayManager->gateway('sepehr');

        $callbackUrl = $gateway->getCallbackUrl();
        $this->assertStringContainsString('/payment/callback/sepehr', $callbackUrl);
    }

    /** @test */
    public function it_validates_configuration_requirements()
    {
        $gatewayManager = app(PaymentGatewayManager::class);
        $gateway = $gatewayManager->gateway('sepehr');

        $this->assertTrue($gateway->validateConfig([
            'terminal_id' => '12345678',
            'sandbox' => true
        ]));

        $this->assertFalse($gateway->validateConfig([
            'sandbox' => true
            // Missing terminal_id
        ]));
    }

    /** @test */
    public function it_handles_null_user_properly()
    {
        $transaction = $this->sepehrPayments->createPurchase(
            amount: 15000,
            description: 'Guest transaction',
            user: null
        );

        $this->assertNull($transaction->user_id);
        $this->assertEquals('pending', $transaction->status);
    }

    /** @test */
    public function it_sets_correct_transaction_fields()
    {
        $transaction = $this->sepehrPayments->createPurchase(
            amount: 25000,
            description: 'Test transaction',
            user: $this->user
        );

        $this->assertEquals('payment', $transaction->type);
        $this->assertEquals('pending', $transaction->status);
        $this->assertEquals(0, $transaction->tax_amount);
        $this->assertEquals(0, $transaction->gateway_fee);
        $this->assertEquals(25000, $transaction->amount);
        $this->assertEquals(25000, $transaction->total_amount);
        $this->assertNotNull($transaction->uuid);
        $this->assertNotNull($transaction->reference_id);
    }
} 