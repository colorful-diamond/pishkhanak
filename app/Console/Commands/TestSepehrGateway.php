<?php

namespace App\Console\Commands;

use App\Models\PaymentGateway;
use App\Models\GatewayTransaction;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestSepehrGateway extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:sepehr-gateway {--user-id=1} {--amount=10000}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Sepehr payment gateway integration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Sepehr Payment Gateway Integration...');
        $this->newLine();

        // Check if Sepehr gateway exists
        $gateway = PaymentGateway::where('slug', 'sepehr')->first();
        
        if (!$gateway) {
            $this->error('Sepehr gateway not found in database. Run the seeder first:');
            $this->error('php artisan db:seed --class=SepehrGatewaySeeder');
            return 1;
        }

        $this->info('✅ Sepehr gateway found in database');
        $this->info("Gateway ID: {$gateway->id}");
        $this->info("Gateway Name: {$gateway->name}");
        $this->info("Driver: {$gateway->driver}");
        $this->newLine();

        // Check configuration
        $this->info('Checking gateway configuration...');
        $config = $gateway->config;
        
        $requiredFields = ['terminal_id', 'sandbox'];
        $optionalFields = ['get_method', 'rollback_enabled'];
        
        foreach ($requiredFields as $field) {
            if (empty($config[$field])) {
                $this->warn("⚠️  Missing required configuration: {$field}");
            } else {
                $value = $field === 'terminal_id' ? '***' . substr($config[$field], -3) : $config[$field];
                $this->info("✅ {$field}: {$value}");
            }
        }
        
        foreach ($optionalFields as $field) {
            $value = $config[$field] ?? 'not set';
            $this->info("ℹ️  {$field}: {$value}");
        }
        $this->newLine();

        // Check if gateway is active
        if (!$gateway->is_active) {
            $this->warn('⚠️  Gateway is not active');
        } else {
            $this->info('✅ Gateway is active');
        }

        // Check supported currencies
        $this->info('Supported currencies: ' . implode(', ', $gateway->supported_currencies));
        $this->info("Min amount: {$gateway->min_amount} IRT");
        $this->info("Max amount: {$gateway->max_amount} IRT");
        $this->newLine();

        // Check if driver class exists
        $driverClass = $gateway->driver;
        if (!class_exists($driverClass)) {
            $this->error("❌ Gateway driver class {$driverClass} not found");
            return 1;
        }

        $this->info("✅ Gateway driver class exists: {$driverClass}");

        // Try to instantiate the gateway
        try {
            $gatewayInstance = new $driverClass($gateway);
            $this->info('✅ Gateway instance created successfully');
        } catch (\Exception $e) {
            $this->error("❌ Failed to create gateway instance: {$e->getMessage()}");
            return 1;
        }

        // Test gateway configuration validation
        $configValid = $gatewayInstance->validateConfig($config);
        if ($configValid) {
            $this->info('✅ Gateway configuration is valid');
        } else {
            $this->warn('⚠️  Gateway configuration validation failed');
        }

        // Get gateway information
        $gatewayInfo = $gatewayInstance->getGatewayInfo();
        $this->newLine();
        $this->info('Gateway Information:');
        $this->info("Name: {$gatewayInfo['name']}");
        $this->info("Version: {$gatewayInfo['version']}");
        $this->info("Supports Refund: " . ($gatewayInfo['supports_refund'] ? 'Yes' : 'No'));
        $this->info("Supports Partial Refund: " . ($gatewayInfo['supports_partial_refund'] ? 'Yes' : 'No'));
        $this->info("Payment Methods: " . implode(', ', $gatewayInfo['payment_methods']));
        $this->info("Features: " . implode(', ', $gatewayInfo['features']));

        // Test callback URL generation
        $callbackUrl = $gatewayInstance->getCallbackUrl();
        $this->info("Callback URL: {$callbackUrl}");
        $this->newLine();

        // Check if we have a terminal ID for actual API testing
        if (empty($config['terminal_id'])) {
            $this->warn('⚠️  No terminal ID configured. Skipping API tests.');
            $this->warn('   Add SEPEHR_TERMINAL_ID to your .env file to test API calls.');
            $this->newLine();
        } else {
            $this->info('Testing basic API connectivity...');
            
            // Get user and currency for test transaction
            $userId = $this->option('user-id');
            $amount = (int) $this->option('amount');
            
            $user = User::find($userId);
            if (!$user) {
                $this->warn("⚠️  User with ID {$userId} not found. Using guest user for test.");
                $user = null;
            } else {
                $this->info("✅ Test user: {$user->name} (ID: {$user->id})");
            }

            $currency = Currency::where('code', 'IRT')->first();
            if (!$currency) {
                $this->error('❌ IRT currency not found in database');
                return 1;
            }

            // Create a test transaction
            $transaction = GatewayTransaction::create([
                'user_id' => $user?->id,
                'payment_gateway_id' => $gateway->id,
                'currency_id' => $currency->id,
                'amount' => $amount,
                'tax_amount' => 0,
                'gateway_fee' => 0,
                'total_amount' => $amount,
                'type' => 'payment',
                'status' => 'pending',
                'description' => 'Test payment for Sepehr gateway',
                'reference_id' => 'TEST_' . uniqid(),
                'user_ip' => '127.0.0.1',
                'user_agent' => 'Test Agent',
            ]);

            $this->info("✅ Test transaction created: {$transaction->uuid}");

            try {
                // Test payment creation (this will call GetToken API)
                $this->info('Testing payment creation (GetToken API)...');
                $result = $gatewayInstance->createPayment($transaction);

                if ($result['success']) {
                    $this->info('✅ Payment creation successful!');
                    $this->info("Access Token: " . substr($result['access_token'], 0, 10) . '...');
                    $this->info("Payment URL: {$result['payment_url']}");
                    $this->info("Amount: {$result['amount']} IRT");
                    $this->info("Terminal ID: {$result['terminal_id']}");
                } else {
                    $this->error("❌ Payment creation failed: {$result['message']}");
                }

            } catch (\Exception $e) {
                $this->error("❌ API test failed: {$e->getMessage()}");
                Log::error('Sepehr Gateway Test Failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }

            // Clean up test transaction
            $transaction->delete();
            $this->info('✅ Test transaction cleaned up');
        }

        $this->newLine();
        $this->info('🎉 Sepehr gateway test completed!');
        
        if (empty($config['terminal_id'])) {
            $this->newLine();
            $this->warn('⚠️  Remember to:');
            $this->warn('1. Add your Sepehr Terminal ID to .env file');
            $this->warn('2. Ensure your server IP is whitelisted with Sepehr');
            $this->warn('3. Test with actual payment flow once configured');
        }

        return 0;
    }
} 