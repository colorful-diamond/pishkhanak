<?php

namespace Database\Seeders;

use App\Models\PaymentGateway;
use Illuminate\Database\Seeder;

class SepGatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if SEP gateway already exists
        $existingGateway = PaymentGateway::where('slug', 'sep')->first();
        
        if ($existingGateway) {
            $this->command->info('SEP gateway already exists. Updating configuration...');
            
            // Update existing gateway configuration
            $existingGateway->update([
                'name' => 'Saman Electronic Payment Gateway',
                'driver' => 'App\Services\PaymentGateways\SepGateway',
                'description' => 'Saman Electronic Payment Gateway - Secure payment processing with advanced features',
                'is_active' => true,
                'config' => [
                    'terminal_id' => env('SEP_TERMINAL_ID', ''),
                    'sandbox' => env('SEP_SANDBOX', true),
                    'token_expiry_minutes' => env('SEP_TOKEN_EXPIRY', 20),
                    'refund_enabled' => env('SEP_REFUND_ENABLED', false),
                    'api_version' => 'v4.1',
                    'timeout' => 30,
                    'retry_attempts' => 3,
                ],
                'supported_currencies' => ['IRT'],
                'fee_percentage' => 0.0, // 0% fee
                'fee_fixed' => 0, // No fixed fee
                'min_amount' => 1000, // 1,000 IRT minimum
                'max_amount' => 500000000, // 500,000,000 IRT maximum
                'logo_url' => '/assets/images/gateways/sep.svg',
            ]);
            
            $this->command->info('SEP gateway configuration updated successfully.');
            return;
        }

        // Create SEP payment gateway
        PaymentGateway::create([
            'name' => 'Saman Electronic Payment Gateway',
            'slug' => 'sep',
            'driver' => 'App\Services\PaymentGateways\SepGateway',
            'description' => 'Saman Electronic Payment Gateway - Secure payment processing with advanced features',
            'is_active' => true,
            'is_default' => false,
            'config' => [
                'terminal_id' => env('SEP_TERMINAL_ID', ''),
                'sandbox' => env('SEP_SANDBOX', true),
                'token_expiry_minutes' => env('SEP_TOKEN_EXPIRY', 20),
                'refund_enabled' => env('SEP_REFUND_ENABLED', false),
                'api_version' => 'v4.1',
                'timeout' => 30,
                'retry_attempts' => 3,
            ],
            'supported_currencies' => ['IRT'],
            'fee_percentage' => 0.0, // 0% fee
            'fee_fixed' => 0, // No fixed fee
            'min_amount' => 1000, // 1,000 IRT minimum
            'max_amount' => 500000000, // 500,000,000 IRT maximum
            'logo_url' => '/assets/images/gateways/sep.svg',
            'sort_order' => 4, // After Asan Pardakht, Jibit, and Sepehr
        ]);

        $this->command->info('✅ SEP payment gateway created successfully!');
        $this->command->line('');
        $this->command->info('📋 Configuration Notes:');
        $this->command->line('• Set SEP_TERMINAL_ID in your .env file');
        $this->command->line('• Configure SEP_SANDBOX=true for testing');
        $this->command->line('• Contact SEP to whitelist your server IP');
        $this->command->line('• Enable SEP_REFUND_ENABLED if refunds are activated');
        $this->command->line('');
        $this->command->info('🔗 SEP Documentation: https://sep.shaparak.ir');
    }
} 