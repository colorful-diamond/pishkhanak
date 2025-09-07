<?php

namespace Database\Seeders;

use App\Models\PaymentGateway;
use Illuminate\Database\Seeder;

class JibitGatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if Jibit gateway already exists
        $existingGateway = PaymentGateway::where('slug', 'jibit')->first();
        
        if ($existingGateway) {
            $this->command->info('Jibit gateway already exists. Skipping...');
            return;
        }

        // Create Jibit payment gateway
        PaymentGateway::create([
            'name' => 'Jibit Payment Gateway',
            'slug' => 'jibit',
            'driver' => 'App\Services\PaymentGateways\JibitGateway',
            'description' => 'Jibit Payment Gateway - Secure payment processing with support for cards, wallets, and bank transfers',
            'is_active' => true,
            'is_default' => false,
            'config' => [
                'api_key' => env('JIBIT_PPG_API_KEY', ''),
                'api_secret' => env('JIBIT_PPG_API_SECRET', ''),
                'sandbox' => env('JIBIT_PPG_SANDBOX', true),
                'api_version' => 'v3',
                'timeout' => 30,
                'retry_attempts' => 3,
            ],
            'supported_currencies' => ['IRT', 'USD', 'EUR'],
            'fee_percentage' => 0.0, // 0% fee
            'fee_fixed' => 0, // No fixed fee
            'min_amount' => 1000, // 1,000 IRT minimum
            'max_amount' => 500000000, // 500,000,000 IRT maximum
            'logo_url' => '/assets/images/gateways/jibit.svg',
            'sort_order' => 2, // After Asan Pardakht
        ]);

        $this->command->info('Jibit payment gateway created successfully!');
        $this->command->info('Please configure the following environment variables for PPG API:');
        $this->command->info('- JIBIT_PPG_API_KEY: Your Jibit PPG API key');
        $this->command->info('- JIBIT_PPG_API_SECRET: Your Jibit PPG API secret');
        $this->command->info('- JIBIT_PPG_SANDBOX: Set to true for sandbox mode, false for production');
        $this->command->info('');
        $this->command->info('Note: These are different from JIBIT_API_KEY and JIBIT_SECRET_KEY which are used for IDE services.');
    }
} 