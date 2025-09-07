<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentSystemSeeder extends Seeder
{
    public function run(): void
    {
        // Seed currencies
        $currencies = [
            [
                'code' => 'IRT',
                'name' => 'Iranian Toman',
                'symbol' => 'تومان',
                'exchange_rate' => 1.0000,
                'is_base_currency' => true,
                'is_active' => true,
                'decimal_places' => 0,
                'position' => 'after',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'USD',
                'name' => 'US Dollar',
                'symbol' => '$',
                'exchange_rate' => 0.000024, // Example rate - should be updated regularly
                'is_base_currency' => false,
                'is_active' => true,
                'decimal_places' => 2,
                'position' => 'before',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'EUR',
                'name' => 'Euro',
                'symbol' => '€',
                'exchange_rate' => 0.000022, // Example rate - should be updated regularly
                'is_base_currency' => false,
                'is_active' => true,
                'decimal_places' => 2,
                'position' => 'before',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('currencies')->insert($currencies);

        // Seed payment gateways
        $gateways = [
            [
                'name' => 'آسان پرداخت',
                'slug' => 'asanpardakht',
                'driver' => 'App\\Services\\PaymentGateways\\AsanpardakhtGateway',
                'description' => 'درگاه پرداخت آسان پرداخت - ارائه‌دهنده خدمات پرداخت اینترنتی',
                'is_active' => true,
                'is_default' => true,
                'config' => json_encode([
                    'merchant_id' => env('ASANPARDAKHT_MERCHANT_ID', ''),
                    'username' => env('ASANPARDAKHT_USERNAME', ''),
                    'password' => env('ASANPARDAKHT_PASSWORD', ''),
                    'sandbox' => env('ASANPARDAKHT_SANDBOX', true),
                ]),
                'supported_currencies' => json_encode(['IRT']),
                'fee_percentage' => 1.5,
                'fee_fixed' => 0,
                'min_amount' => 1000,
                'max_amount' => 500000000, // 500M IRT
                'logo_url' => '/assets/images/gateways/asanpardakht.png',
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('payment_gateways')->insert($gateways);

        // Seed tax rules
        $taxRules = [
            [
                'name' => 'مالیات بر ارزش افزوده',
                'type' => 'percentage',
                'rate' => 9.0000, // 9% VAT
                'is_active' => true,
                'is_default' => true,
                'applicable_currencies' => json_encode(['IRT']),
                'min_amount' => 0,
                'max_amount' => null,
                'description' => 'مالیات بر ارزش افزوده ۹ درصد',
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'عوارض خدمات',
                'type' => 'fixed',
                'rate' => 1000.0000, // 1000 IRT fixed fee
                'is_active' => false,
                'is_default' => false,
                'applicable_currencies' => json_encode(['IRT']),
                'min_amount' => 10000,
                'max_amount' => null,
                'description' => 'عوارض ثابت خدمات ۱۰۰۰ ریال',
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('tax_rules')->insert($taxRules);
    }
} 