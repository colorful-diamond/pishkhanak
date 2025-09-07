<?php

namespace App\Console\Commands;

use App\Models\PaymentGateway;
use Illuminate\Console\Command;

class SeedPaymentGateways extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'payment:seed-gateways';

    /**
     * The console command description.
     */
    protected $description = 'Seed payment gateways if they don\'t exist';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Checking existing payment gateways...');
        
        $existingCount = PaymentGateway::count();
        $this->info("Found {$existingCount} existing gateways");

        $gateways = [
            [
                'name' => 'آسان پرداخت',
                'slug' => 'asanpardakht',
                'driver' => 'App\Services\PaymentGateways\AsanPardakhtGateway',
                'description' => 'درگاه پرداخت آسان پرداخت - پرداخت اینترنتی امن',
                'is_active' => true,
                'is_default' => true,
                'config' => [
                    'merchant_id' => env('ASANPARDAKHT_MERCHANT_ID', ''),
                    'username' => env('ASANPARDAKHT_USERNAME', ''),
                    'password' => env('ASANPARDAKHT_PASSWORD', ''),
                    'sandbox' => env('ASANPARDAKHT_SANDBOX', true),
                ],
                'supported_currencies' => ['IRT'],
                'fee_percentage' => 2.5,
                'fee_fixed' => 500,
                'min_amount' => 1000,
                'max_amount' => 1000000000, // 1B IRT
                'logo_url' => '/assets/images/gateways/asanpardakht.svg',
                'sort_order' => 1,
            ],
            [
                'name' => 'جیبیت',
                'slug' => 'jibit',
                'driver' => 'App\Services\PaymentGateways\JibitGateway',
                'description' => 'درگاه پرداخت جیبیت - پرداخت اینترنتی و کیف پول',
                'is_active' => true,
                'is_default' => false,
                'config' => [
                    'api_key' => env('JIBIT_API_KEY', ''),
                    'merchant_id' => env('JIBIT_MERCHANT_ID', ''),
                    'sandbox' => env('JIBIT_SANDBOX', true),
                ],
                'supported_currencies' => ['IRT', 'USD', 'EUR'],
                'fee_percentage' => 0.0,
                'fee_fixed' => 0,
                'min_amount' => 1000,
                'max_amount' => 500000000, // 500M IRT
                'logo_url' => '/assets/images/gateways/jibit.svg',
                'sort_order' => 2,
            ],
            [
                'name' => 'سپهر الکترونیک',
                'slug' => 'sepehr',
                'driver' => 'App\Services\PaymentGateways\SepehrGateway',
                'description' => 'درگاه پرداخت سپهر - پرداخت امن با پشتیبانی از قبض و شارژ',
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
                'fee_percentage' => 0.0,
                'fee_fixed' => 0,
                'min_amount' => 1000,
                'max_amount' => 500000000, // 500M IRT
                'logo_url' => '/assets/images/gateways/sepehr.svg',
                'sort_order' => 3,
            ],
        ];

        $created = 0;
        
        foreach ($gateways as $gatewayData) {
            $existing = PaymentGateway::where('slug', $gatewayData['slug'])->first();
            
            if (!$existing) {
                PaymentGateway::create($gatewayData);
                $this->info("✅ Created gateway: {$gatewayData['name']}");
                $created++;
            } else {
                $this->info("⚠️  Gateway already exists: {$gatewayData['name']}");
            }
        }

        if ($created > 0) {
            $this->info("🎉 Created {$created} new payment gateways");
        } else {
            $this->info("ℹ️  All payment gateways already exist");
        }

        // Show final status
        $finalCount = PaymentGateway::count();
        $activeCount = PaymentGateway::where('is_active', true)->count();
        
        $this->info("📊 Final status:");
        $this->info("   Total gateways: {$finalCount}");
        $this->info("   Active gateways: {$activeCount}");
        
        return Command::SUCCESS;
    }
} 