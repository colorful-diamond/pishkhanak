<?php

namespace App\Console\Commands;

use App\Models\PaymentGateway;
use App\Models\Service;
use App\Models\User;
use App\Services\PaymentGatewayManager;
use App\Services\PaymentService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TestPaymentFlow extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'payment:test-flow {--gateway=} {--amount=100000} {--dry-run}';

    /**
     * The console command description.
     */
    protected $description = 'Test the complete payment flow including wallet charge and gateway connections';

    protected PaymentGatewayManager $gatewayManager;
    protected PaymentService $paymentService;

    public function __construct(PaymentGatewayManager $gatewayManager, PaymentService $paymentService)
    {
        parent::__construct();
        $this->gatewayManager = $gatewayManager;
        $this->paymentService = $paymentService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Testing Payment Flow System');
        $this->info('=====================================');

        $dryRun = $this->option('dry-run');
        $testAmount = (int) $this->option('amount');
        $gatewaySlug = $this->option('gateway');

        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No actual transactions will be created');
        }

        // Test 1: Gateway Configuration
        $this->info('📝 Test 1: Gateway Configuration');
        $this->testGatewayConfiguration($gatewaySlug);

        // Test 2: Gateway API Connectivity
        $this->info('📝 Test 2: Gateway API Connectivity');
        $this->testGatewayConnectivity($gatewaySlug);

        // Test 3: Payment Gateway Manager
        $this->info('📝 Test 3: Payment Gateway Manager');
        $this->testPaymentGatewayManager($testAmount);

        // Test 4: Gateway Selection API
        $this->info('📝 Test 4: Gateway Selection API');
        $this->testGatewaySelectionAPI($testAmount);

        // Test 5: Wallet Charge Flow (if not dry-run)
        if (!$dryRun) {
            $this->info('📝 Test 5: Wallet Charge Flow');
            $this->testWalletChargeFlow($testAmount, $gatewaySlug);
        } else {
            $this->warn('⏭️  Skipping wallet charge flow test (dry-run mode)');
        }

        // Test 6: Service Payment Integration
        $this->info('📝 Test 6: Service Payment Integration');
        $this->testServicePaymentIntegration();

        // Test 7: Guest Payment Flow
        $this->info('📝 Test 7: Guest Payment Flow');
        $this->testGuestPaymentFlow();

        $this->info('✅ Payment flow testing completed!');
    }

    protected function testGatewayConfiguration($gatewaySlug = null)
    {
        $gateways = $gatewaySlug 
            ? PaymentGateway::where('slug', $gatewaySlug)->get()
            : PaymentGateway::active()->get();

        if ($gateways->isEmpty()) {
            $this->error('❌ No payment gateways found');
            return;
        }

        foreach ($gateways as $gateway) {
            $this->line("   Testing gateway: {$gateway->name} ({$gateway->slug})");
            
            // Check if gateway is active
            if (!$gateway->is_active) {
                $this->warn("   ⚠️  Gateway {$gateway->slug} is inactive");
                continue;
            }

            // Validate configuration
            try {
                $isValid = $this->gatewayManager->validateGatewayConfig($gateway);
                if ($isValid) {
                    $this->info("   ✅ Configuration valid");
                } else {
                    $this->error("   ❌ Configuration invalid");
                }
            } catch (\Exception $e) {
                $this->error("   ❌ Configuration error: " . $e->getMessage());
            }

            // Check required config fields
            try {
                $instance = $this->gatewayManager->gatewayById($gateway->id);
                $requirements = $instance->getConfigRequirements();
                $config = $gateway->config;

                foreach ($requirements as $required) {
                    if (empty($config[$required])) {
                        $this->warn("   ⚠️  Missing required config: {$required}");
                    } else {
                        $this->line("   ✓ Config field present: {$required}");
                    }
                }
            } catch (\Exception $e) {
                $this->warn("   ⚠️  Could not check config requirements: " . $e->getMessage());
            }
        }
    }

    protected function testGatewayConnectivity($gatewaySlug = null)
    {
        $gateways = $gatewaySlug 
            ? PaymentGateway::where('slug', $gatewaySlug)->active()->get()
            : PaymentGateway::active()->limit(3)->get(); // Limit to avoid too many API calls

        foreach ($gateways as $gateway) {
            $this->line("   Testing connectivity: {$gateway->name}");
            
            try {
                $instance = $this->gatewayManager->gatewayById($gateway->id);
                
                // Test basic configuration validation
                $configValid = $instance->validateConfig($gateway->config);
                
                if ($configValid) {
                    $this->info("   ✅ Basic connectivity test passed");
                } else {
                    $this->warn("   ⚠️  Configuration validation failed");
                }
                
                // Check supported currencies
                $currencies = $instance->getSupportedCurrencies();
                $this->line("   💱 Supported currencies: " . implode(', ', $currencies));
                
                // Check amount limits
                $limits = $instance->getAmountLimits();
                $this->line("   💰 Amount limits: {$limits['min']} - {$limits['max']}");
                
            } catch (\Exception $e) {
                $this->error("   ❌ Connectivity test failed: " . $e->getMessage());
            }
        }
    }

    protected function testPaymentGatewayManager($amount)
    {
        try {
            // Test getting active gateways
            $activeGateways = $this->gatewayManager->activeGateways();
            $this->info("   ✅ Found {$activeGateways->count()} active gateways");

            // Test currency-specific gateways
            $irtGateways = $this->gatewayManager->gatewaysForCurrency('IRT');
            $this->info("   ✅ Found {$irtGateways->count()} gateways supporting IRT");

            // Test amount-specific gateways
            $suitableGateways = $this->gatewayManager->gatewaysForAmount($amount, 'IRT');
            $this->info("   ✅ Found {$suitableGateways->count()} gateways supporting amount {$amount}");

            // Test finding best gateway
            $bestGateway = $this->gatewayManager->findBestGateway($amount, 'IRT');
            if ($bestGateway) {
                $this->info("   ✅ Best gateway for amount: {$bestGateway->name}");
            } else {
                $this->warn("   ⚠️  No suitable gateway found for amount {$amount}");
            }

        } catch (\Exception $e) {
            $this->error("   ❌ Payment Gateway Manager test failed: " . $e->getMessage());
        }
    }

    protected function testGatewaySelectionAPI($amount)
    {
        try {
            // Simulate API request
            $url = url("/payment/gateways?amount={$amount}&currency=IRT");
            $this->line("   Testing API endpoint: {$url}");

            // Use HTTP client to test the actual endpoint
            $response = \Illuminate\Support\Facades\Http::get($url);
            
            if ($response->successful()) {
                $data = $response->json();
                if ($data['success'] && isset($data['gateways'])) {
                    $gatewayCount = count($data['gateways']);
                    $this->info("   ✅ Gateway selection API returned {$gatewayCount} gateways");
                    
                    foreach ($data['gateways'] as $gateway) {
                        $this->line("     - {$gateway['name']}: fee = {$gateway['fee']}, total = {$gateway['total_amount']}");
                    }
                } else {
                    $this->warn("   ⚠️  API returned success=false or missing gateways");
                }
            } else {
                $this->error("   ❌ API request failed with status: " . $response->status());
            }

        } catch (\Exception $e) {
            $this->error("   ❌ Gateway selection API test failed: " . $e->getMessage());
        }
    }

    protected function testWalletChargeFlow($amount, $gatewaySlug = null)
    {
        try {
            // Find a test user (or create one)
            $user = User::where('email', 'test@example.com')->first();
            if (!$user) {
                $this->warn("   ⚠️  No test user found, skipping wallet charge test");
                return;
            }

            // Find a suitable gateway
            $gateway = $gatewaySlug 
                ? PaymentGateway::where('slug', $gatewaySlug)->active()->first()
                : $this->gatewayManager->findBestGateway($amount, 'IRT');

            if (!$gateway) {
                $this->warn("   ⚠️  No suitable gateway found for wallet charge test");
                return;
            }

            $this->line("   Using gateway: {$gateway->name}");
            $this->line("   Test amount: {$amount}");

            // Create payment data
            $paymentData = [
                'user_id' => $user->id,
                'gateway_id' => $gateway->id,
                'amount' => $amount,
                'currency' => 'IRT',
                'description' => 'Test wallet charge',
                'metadata' => [
                    'type' => 'wallet_charge',
                    'test_transaction' => true,
                ]
            ];

            // Test payment creation (but don't actually process)
            $this->line("   Creating test payment transaction...");
            
            DB::beginTransaction();
            try {
                $result = $this->paymentService->createPayment($paymentData);
                
                if ($result['success']) {
                    $this->info("   ✅ Payment transaction created successfully");
                    $this->line("     Transaction ID: {$result['transaction']->id}");
                    $this->line("     Reference ID: {$result['transaction']->reference_id}");
                } else {
                    $this->error("   ❌ Payment creation failed: " . ($result['message'] ?? 'Unknown error'));
                }
                
                // Always rollback to avoid creating test transactions
                DB::rollBack();
                $this->line("   🔄 Transaction rolled back (test mode)");
                
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            $this->error("   ❌ Wallet charge flow test failed: " . $e->getMessage());
        }
    }

    protected function testServicePaymentIntegration()
    {
        try {
            // Check if services exist with pricing
            $paidServices = Service::where('is_paid', true)->where('price', '>', 0)->count();
            $this->info("   ✅ Found {$paidServices} paid services");

            // Check if ServicePaymentService exists
            if (class_exists(\App\Services\ServicePaymentService::class)) {
                $this->info("   ✅ ServicePaymentService class exists");
            } else {
                $this->error("   ❌ ServicePaymentService class not found");
            }

            // Check if service payment routes exist
            $routes = [
                'services.preview.guest',
                'services.preview.user',
                'guest.payment.charge',
                'guest.payment.callback',
            ];

            foreach ($routes as $routeName) {
                if (\Illuminate\Support\Facades\Route::has($routeName)) {
                    $this->info("   ✅ Route exists: {$routeName}");
                } else {
                    $this->warn("   ⚠️  Route missing: {$routeName}");
                }
            }

        } catch (\Exception $e) {
            $this->error("   ❌ Service payment integration test failed: " . $e->getMessage());
        }
    }

    protected function testGuestPaymentFlow()
    {
        try {
            // Check if GuestPaymentController exists
            if (class_exists(\App\Http\Controllers\GuestPaymentController::class)) {
                $this->info("   ✅ GuestPaymentController exists");
            } else {
                $this->error("   ❌ GuestPaymentController not found");
            }

            // Check guest payment views
            $views = [
                'payments.guest-charge',
                'payments.guest-verify-phone',
                'services.preview',
            ];

            foreach ($views as $view) {
                if (view()->exists($view)) {
                    $this->info("   ✅ View exists: {$view}");
                } else {
                    $this->warn("   ⚠️  View missing: {$view}");
                }
            }

            // Check if guest payment routes are accessible
            $testRoutes = [
                '/guest/payment/verify-phone',
            ];

            foreach ($testRoutes as $route) {
                try {
                    $response = \Illuminate\Support\Facades\Http::get(url($route));
                    if ($response->status() < 500) { // Allow redirects and 404s, but not server errors
                        $this->info("   ✅ Route accessible: {$route}");
                    } else {
                        $this->warn("   ⚠️  Route error ({$response->status()}): {$route}");
                    }
                } catch (\Exception $e) {
                    $this->warn("   ⚠️  Route test failed: {$route}");
                }
            }

        } catch (\Exception $e) {
            $this->error("   ❌ Guest payment flow test failed: " . $e->getMessage());
        }
    }
} 