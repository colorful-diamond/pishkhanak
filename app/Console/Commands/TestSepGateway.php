<?php

namespace App\Console\Commands;

use App\Models\PaymentGateway;
use App\Models\Currency;
use App\Models\User;
use App\Services\SepPaymentTypes;
use App\Services\PaymentGatewayManager;
use App\Services\SepFormHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Exception;

class TestSepGateway extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:sep-gateway 
                           {--amount=10000 : Test amount in Rials} 
                           {--skip-api : Skip API connectivity test}
                           {--verbose : Show detailed output}';

    /**
     * The console command description.
     */
    protected $description = 'Test SEP (Saman Electronic Payment) gateway configuration and connectivity';

    protected PaymentGateway $gateway;
    protected SepPaymentTypes $sepPayments;
    protected PaymentGatewayManager $gatewayManager;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing SEP (Saman Electronic Payment) Gateway');
        $this->info('================================================');
        $this->newLine();

        try {
            // Initialize services
            $this->initializeServices();
            
            // Run tests
            $this->testGatewayConfiguration();
            $this->testEnvironmentVariables();
            $this->testDatabaseConfiguration();
            $this->testGatewayInstance();
            $this->testPaymentTypeCreation();
            $this->testFormHelpers();
            
            if (!$this->option('skip-api')) {
                $this->testApiConnectivity();
            }
            
            $this->newLine();
            $this->info('🎉 All SEP Gateway tests completed successfully!');
            $this->newLine();
            $this->displayNextSteps();

            return Command::SUCCESS;

        } catch (Exception $e) {
            $this->error('❌ Test failed: ' . $e->getMessage());
            if ($this->option('verbose')) {
                $this->error('Stack trace: ' . $e->getTraceAsString());
            }
            return Command::FAILURE;
        }
    }

    protected function initializeServices()
    {
        $this->info('🔧 Initializing services...');
        
        $this->gateway = PaymentGateway::where('slug', 'sep')->first();
        if (!$this->gateway) {
            throw new Exception('SEP gateway not found in database. Please run: php artisan db:seed --class=SepGatewaySeeder');
        }

        $this->sepPayments = new SepPaymentTypes();
        $this->gatewayManager = app(PaymentGatewayManager::class);
        
        $this->line('✅ Services initialized');
    }

    protected function testGatewayConfiguration()
    {
        $this->info('📋 Testing gateway configuration...');
        
        // Test basic configuration
        $config = $this->gateway->config;
        $requiredFields = ['terminal_id', 'sandbox'];
        
        foreach ($requiredFields as $field) {
            if (empty($config[$field]) && $field !== 'sandbox') {
                $this->warn("⚠️  Required field '{$field}' is not configured");
            } else {
                $this->line("✅ {$field}: " . ($field === 'sandbox' ? ($config[$field] ? 'true' : 'false') : '***configured***'));
            }
        }

        // Test optional fields
        $optionalFields = ['token_expiry_minutes', 'refund_enabled', 'timeout'];
        foreach ($optionalFields as $field) {
            $value = $config[$field] ?? 'not set';
            $this->line("ℹ️  {$field}: {$value}");
        }
    }

    protected function testEnvironmentVariables()
    {
        $this->info('🔐 Testing environment variables...');
        
        $envVars = [
            'SEP_TERMINAL_ID' => env('SEP_TERMINAL_ID'),
            'SEP_SANDBOX' => env('SEP_SANDBOX'),
            'SEP_TOKEN_EXPIRY' => env('SEP_TOKEN_EXPIRY'),
            'SEP_REFUND_ENABLED' => env('SEP_REFUND_ENABLED'),
        ];

        foreach ($envVars as $key => $value) {
            if ($value !== null) {
                if ($key === 'SEP_TERMINAL_ID') {
                    $this->line("✅ {$key}: " . (strlen($value) === 8 ? '8 digits configured' : 'configured but may not be 8 digits'));
                } else {
                    $this->line("✅ {$key}: {$value}");
                }
            } else {
                $this->warn("⚠️  {$key}: not set");
            }
        }

        // Validate terminal ID format
        $terminalId = env('SEP_TERMINAL_ID');
        if ($terminalId && !preg_match('/^\d{8}$/', $terminalId)) {
            $this->error('❌ SEP_TERMINAL_ID must be exactly 8 digits');
        }
    }

    protected function testDatabaseConfiguration()
    {
        $this->info('🗄️  Testing database configuration...');
        
        // Test gateway record
        $this->line("✅ Gateway ID: {$this->gateway->id}");
        $this->line("✅ Gateway Name: {$this->gateway->name}");
        $this->line("✅ Gateway Slug: {$this->gateway->slug}");
        $this->line("✅ Driver Class: {$this->gateway->driver}");
        $this->line("✅ Is Active: " . ($this->gateway->is_active ? 'Yes' : 'No'));
        $this->line("✅ Supported Currencies: " . implode(', ', $this->gateway->supported_currencies));
        $this->line("✅ Fee Percentage: {$this->gateway->fee_percentage}%");
        $this->line("✅ Fee Fixed: {$this->gateway->fee_fixed}");
        $this->line("✅ Min Amount: {$this->gateway->min_amount}");
        $this->line("✅ Max Amount: {$this->gateway->max_amount}");

        // Test currency support
        $currency = Currency::where('code', 'IRT')->first();
        if ($currency) {
            $this->line("✅ IRT Currency: Available (ID: {$currency->id})");
        } else {
            $this->error('❌ IRT Currency not found in database');
        }
    }

    protected function testGatewayInstance()
    {
        $this->info('⚙️  Testing gateway instance...');
        
        try {
            $gatewayInstance = $this->gatewayManager->gateway('sep');
            $this->line('✅ Gateway instance created successfully');

            // Test configuration requirements
            $requirements = $gatewayInstance->getConfigRequirements();
            $this->line('✅ Config requirements: ' . implode(', ', $requirements));

            // Test supported currencies
            $currencies = $gatewayInstance->getSupportedCurrencies();
            $this->line('✅ Supported currencies: ' . implode(', ', $currencies));

            // Test amount limits
            $limits = $gatewayInstance->getAmountLimits();
            $this->line("✅ Amount limits: {$limits['min']} - {$limits['max']}");

        } catch (Exception $e) {
            $this->error('❌ Gateway instance creation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function testPaymentTypeCreation()
    {
        $this->info('💳 Testing payment type creation...');
        
        try {
            $testAmount = (int) $this->option('amount');
            
            // Test standard purchase
            $transaction = $this->sepPayments->createPurchase(
                $testAmount,
                'Test payment for SEP gateway'
            );
            $this->line("✅ Standard purchase created: {$transaction->uuid}");

            // Test purchase with wage
            $wageTransaction = $this->sepPayments->createPurchaseWithWage(
                $testAmount,
                500,
                'Test payment with wage'
            );
            $this->line("✅ Purchase with wage created: {$wageTransaction->uuid}");

            // Test mobile purchase
            $mobileTransaction = $this->sepPayments->createMobilePurchase(
                $testAmount,
                '09123456789',
                'Test mobile payment'
            );
            $this->line("✅ Mobile purchase created: {$mobileTransaction->uuid}");

            // Test custom expiry purchase
            $customTransaction = $this->sepPayments->createCustomExpiryPurchase(
                $testAmount,
                30,
                'Test custom expiry payment'
            );
            $this->line("✅ Custom expiry purchase created: {$customTransaction->uuid}");

            // Test restricted card purchase
            $restrictedTransaction = $this->sepPayments->createRestrictedCardPurchase(
                $testAmount,
                ['1234567890123456'],
                'Test restricted card payment'
            );
            $this->line("✅ Restricted card purchase created: {$restrictedTransaction->uuid}");

        } catch (Exception $e) {
            $this->error('❌ Payment type creation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function testFormHelpers()
    {
        $this->info('📄 Testing form helpers...');
        
        try {
            $testToken = 'test_token_123456789abcdef';
            
            // Test payment URL generation
            $paymentUrl = SepFormHelper::generatePaymentUrl($testToken);
            $this->line('✅ Payment URL generated: ' . substr($paymentUrl, 0, 50) . '...');

            // Test form generation
            $form = SepFormHelper::generatePaymentForm($testToken);
            $this->line('✅ Payment form generated (length: ' . strlen($form) . ' chars)');

            // Test minimal redirect
            $redirect = SepFormHelper::generateMinimalRedirect($testToken);
            $this->line('✅ Minimal redirect generated (length: ' . strlen($redirect) . ' chars)');

            // Test callback validation
            $validCallback = ['Token' => $testToken];
            $isValid = SepFormHelper::validateCallbackData($validCallback);
            $this->line('✅ Callback validation: ' . ($isValid ? 'Valid' : 'Invalid'));

        } catch (Exception $e) {
            $this->error('❌ Form helper test failed: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function testApiConnectivity()
    {
        $this->info('🌐 Testing API connectivity...');
        
        try {
            // Test basic connectivity to SEP endpoints
            $endpoints = [
                'Token API' => 'https://sep.shaparak.ir/onlinepg/onlinepg',
                'Receipt API' => 'https://sep.shaparak.ir/verifyTxnRandomSessionkey/api/v2/ipg/payment/receipt',
                'Verify API' => 'https://sep.shaparak.ir/verifyTxnRandomSessionkey/ipg/VerifyTransaction',
                'Reverse API' => 'https://sep.shaparak.ir/verifyTxnRandomSessionkey/ipg/ReverseTransaction',
            ];

            foreach ($endpoints as $name => $url) {
                try {
                    $response = Http::timeout(10)->head($url);
                    $statusCode = $response->status();
                    
                    if ($statusCode === 200) {
                        $this->line("✅ {$name}: Reachable (HTTP {$statusCode})");
                    } elseif ($statusCode === 405) {
                        // Method not allowed is expected for HEAD requests
                        $this->line("✅ {$name}: Reachable (HTTP {$statusCode} - Expected)");
                    } else {
                        $this->warn("⚠️  {$name}: HTTP {$statusCode}");
                    }
                } catch (Exception $e) {
                    $this->warn("⚠️  {$name}: Connection failed - " . $e->getMessage());
                }
            }

            $this->newLine();
            $this->warn('⚠️  Note: Full API testing requires valid terminal credentials and IP whitelisting');

        } catch (Exception $e) {
            $this->error('❌ API connectivity test failed: ' . $e->getMessage());
        }
    }

    protected function displayNextSteps()
    {
        $this->info('📋 Next Steps:');
        $this->line('1. Configure SEP_TERMINAL_ID in your .env file');
        $this->line('2. Contact SEP to whitelist your server IP address');
        $this->line('3. Test with real transactions in sandbox mode');
        $this->line('4. Enable refunds if needed: SEP_REFUND_ENABLED=true');
        $this->line('5. Switch to production: SEP_SANDBOX=false');
        $this->newLine();
        
        $this->info('📚 Resources:');
        $this->line('• SEP Documentation: https://sep.shaparak.ir');
        $this->line('• Payment Gateway Guide: See docs/ directory');
        $this->line('• Gateway Configuration: /access/payment-gateways');
        $this->newLine();
        
        if (env('SEP_TERMINAL_ID') && env('SEP_TERMINAL_ID') !== '') {
            $this->info('✅ SEP Gateway is ready for testing!');
        } else {
            $this->warn('⚠️  Configure SEP_TERMINAL_ID to enable payment processing');
        }
    }
} 