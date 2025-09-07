<?php

namespace Database\Seeders;

use App\Models\PaymentGateway;
use Illuminate\Database\Seeder;

class SepehrGatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if Sepehr gateway already exists
        $existingGateway = PaymentGateway::where('slug', 'sepehr')->first();
        
        if ($existingGateway) {
            $this->command->info('Sepehr gateway already exists. Updating configuration...');
            
            // Update existing gateway configuration
            $existingGateway->update([
                'name' => 'Sepehr Electronic Payment Gateway',
                'driver' => 'App\Services\PaymentGateways\SepehrGateway',
                'description' => 'Sepehr Electronic Payment Gateway - Secure payment processing with support for cards, bill payments, and mobile top-ups',
                'is_active' => true,
                'config' => [
                    'terminal_id' => env('SEPEHR_TERMINAL_ID', ''),
                    'sandbox' => env('SEPEHR_SANDBOX', true),
                    'get_method' => env('SEPEHR_GET_METHOD', false),
                    'rollback_enabled' => env('SEPEHR_ROLLBACK_ENABLED', false),
                    'api_version' => 'v3.0.6',
                    'timeout' => 30,
                    'retry_attempts' => 3,
                ],
                'supported_currencies' => ['IRT'],
                'fee_percentage' => 0.0, // 0% fee
                'fee_fixed' => 0, // No fixed fee
                'min_amount' => 1000, // 1,000 IRT minimum
                'max_amount' => 500000000, // 500,000,000 IRT maximum
                'logo_url' => '/assets/images/gateways/sepehr.svg',
            ]);
            
            $this->command->info('Sepehr gateway configuration updated successfully.');
            return;
        }

        // Create Sepehr payment gateway
        PaymentGateway::create([
            'name' => 'Sepehr Electronic Payment Gateway',
            'slug' => 'sepehr',
            'driver' => 'App\Services\PaymentGateways\SepehrGateway',
            'description' => 'Sepehr Electronic Payment Gateway - Secure payment processing with support for cards, bill payments, and mobile top-ups',
            'is_active' => true,
            'is_default' => false,
            'config' => [
                'terminal_id' => env('SEPEHR_TERMINAL_ID', ''),
                'sandbox' => env('SEPEHR_SANDBOX', true),
                'get_method' => env('SEPEHR_GET_METHOD', false),
                'rollback_enabled' => env('SEPEHR_ROLLBACK_ENABLED', false),
                'api_version' => 'v3.0.6',
                'timeout' => 30,
                'retry_attempts' => 3,
            ],
            'supported_currencies' => ['IRT'],
            'fee_percentage' => 0.0, // 0% fee
            'fee_fixed' => 0, // No fixed fee
            'min_amount' => 1000, // 1,000 IRT minimum
            'max_amount' => 500000000, // 500,000,000 IRT maximum
            'logo_url' => '/assets/images/gateways/sepehr.svg',
            'sort_order' => 3, // After Asan Pardakht and Jibit
        ]);

        $this->command->info('Sepehr gateway created successfully.');
        $this->command->newLine();
        $this->command->info('Next steps:');
        $this->command->info('1. Add your Sepehr Terminal ID to .env file:');
        $this->command->info('   SEPEHR_TERMINAL_ID=your_8_digit_terminal_id');
        $this->command->info('2. Configure other optional settings:');
        $this->command->info('   SEPEHR_SANDBOX=true (for testing)');
        $this->command->info('   SEPEHR_GET_METHOD=false (use POST for callbacks)');
        $this->command->info('   SEPEHR_ROLLBACK_ENABLED=false (contact Sepehr to enable)');
        $this->command->info('3. Ensure your server IP is whitelisted with Sepehr');
        $this->command->info('4. Test the integration with: php artisan test:sepehr-gateway');
    }
} 