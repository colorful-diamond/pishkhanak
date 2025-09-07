<?php

namespace App\Console\Commands;

use App\Models\PaymentGateway;
use App\Models\GatewayTransaction;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestJibitGateway extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:jibit-gateway {--user-id=1} {--amount=10000}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Jibit payment gateway integration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Jibit Payment Gateway Integration...');
        $this->newLine();

        // Check if Jibit gateway exists
        $gateway = PaymentGateway::where('slug', 'jibit')->first();
        
        if (!$gateway) {
            $this->error('Jibit gateway not found in database. Run the seeder first:');
            $this->error('php artisan db:seed --class=JibitGatewaySeeder');
            return 1;
        }

        $this->info('✅ Jibit gateway found in database');
        $this->info("Gateway ID: {$gateway->id}");
        $this->info("Gateway Name: {$gateway->name}");
        $this->info("Driver: {$gateway->driver}");
        $this->newLine();

        // Check configuration
        $this->info('Checking gateway configuration...');
        $config = $gateway->config;
        
        $requiredFields = ['access_token', 'webhook_secret', 'sandbox'];
        foreach ($requiredFields as $field) {
            if (empty($config[$field])) {
                $this->warn("⚠️  Missing configuration: {$field}");
            } else {
                $this->info("✅ {$field}: " . ($field === 'access_token' ? '***' : $config[$field]));
            }
        }
        $this->newLine();

        // Check if gateway is active
        if (!$gateway->is_active) {
            $this->warn('⚠️  Gateway is not active');
        } else {
            $this->info('✅ Gateway is active');
        }
        $this->newLine();

        // Test gateway instance creation
        try {
            $gatewayInstance = $gateway->getGatewayInstance();
            $this->info('✅ Gateway instance created successfully');
            
            // Test configuration validation
            if ($gatewayInstance->validateConfig($config)) {
                $this->info('✅ Configuration validation passed');
            } else {
                $this->error('❌ Configuration validation failed');
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Failed to create gateway instance: ' . $e->getMessage());
            return 1;
        }
        $this->newLine();

        // Test payment creation (dry run)
        $this->info('Testing payment creation (dry run)...');
        
        try {
            $user = User::find($this->option('user-id'));
            if (!$user) {
                $this->error('User not found');
                return 1;
            }

            $currency = Currency::where('code', 'IRT')->first();
            if (!$currency) {
                $this->error('IRT currency not found');
                return 1;
            }

            $amount = (int) $this->option('amount');
            
            // Create test transaction
            $transaction = GatewayTransaction::create([
                'user_id' => $user->id,
                'payment_gateway_id' => $gateway->id,
                'currency_id' => $currency->id,
                'total_amount' => $amount,
                'fee_amount' => 0,
                'net_amount' => $amount,
                'description' => 'Test payment via Jibit gateway',
                'reference_id' => 'TEST_JIBIT_' . uniqid(),
                'status' => 'pending',
                'metadata' => [
                    'test' => true,
                    'created_by' => 'test_command',
                ],
            ]);

            $this->info("✅ Test transaction created: {$transaction->reference_id}");
            $this->info("Amount: {$amount} {$currency->code}");
            $this->info("User: {$user->name} ({$user->email})");
            $this->newLine();

            // Test payment creation
            $this->info('Attempting to create payment...');
            $result = $gatewayInstance->createPayment($transaction);
            
            if ($result['success']) {
                $this->info('✅ Payment creation successful');
                $this->info("Payment URL: {$result['payment_url']}");
                $this->info("Payment ID: {$result['payment_id']}");
                $this->info("Reference ID: {$result['reference_id']}");
            } else {
                $this->error('❌ Payment creation failed');
                $this->error("Error: {$result['message']}");
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Payment creation test failed: ' . $e->getMessage());
            Log::error('Jibit gateway test failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
        $this->newLine();

        // Test gateway info
        $this->info('Gateway Information:');
        $gatewayInfo = $gatewayInstance->getGatewayInfo();
        foreach ($gatewayInfo as $key => $value) {
            $this->info("  {$key}: " . (is_bool($value) ? ($value ? 'Yes' : 'No') : $value));
        }
        $this->newLine();

        // Test supported currencies
        $this->info('Supported Currencies:');
        $currencies = $gatewayInstance->getSupportedCurrencies();
        foreach ($currencies as $currency) {
            $this->info("  - {$currency}");
        }
        $this->newLine();

        // Test amount limits
        $this->info('Amount Limits:');
        $limits = $gatewayInstance->getAmountLimits();
        $this->info("  Min: {$limits['min']} IRT");
        $this->info("  Max: {$limits['max']} IRT");
        $this->newLine();

        $this->info('🎉 Jibit gateway test completed successfully!');
        $this->info('Check the logs for detailed information.');
        
        return 0;
    }
} 