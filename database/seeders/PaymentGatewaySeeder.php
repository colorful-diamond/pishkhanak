<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentGatewaySeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $gateways = [
            [
                'name' => 'آسان پرداخت',
                'slug' => 'asanpardakht',
                'driver' => 'App\Services\PaymentGateways\AsanPardakhtGateway',
                'description' => 'درگاه پرداخت آسان پرداخت - پرداخت اینترنتی امن',
                'is_active' => true,
                'is_default' => true,
                'config' => json_encode([
                    'merchant_id' => env('ASANPARDAKHT_MERCHANT_ID', ''),
                    'username' => env('ASANPARDAKHT_USERNAME', ''),
                    'password' => env('ASANPARDAKHT_PASSWORD', ''),
                    'sandbox' => env('ASANPARDAKHT_SANDBOX', true),
                ]),
                'supported_currencies' => json_encode(['IRT']),
                'fee_percentage' => 2.5,
                'fee_fixed' => 500,
                'min_amount' => 1000,
                'max_amount' => 1000000000, // 1B IRT
                'logo_url' => '/assets/images/gateways/asanpardakht.svg',
                'sort_order' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'جیبیت',
                'slug' => 'jibit',
                'driver' => 'App\Services\PaymentGateways\JibitGateway',
                'description' => 'درگاه پرداخت جیبیت - پرداخت اینترنتی و کیف پول',
                'is_active' => true,
                'is_default' => false,
                'config' => json_encode([
                    'api_key' => env('JIBIT_API_KEY', ''),
                    'merchant_id' => env('JIBIT_MERCHANT_ID', ''),
                    'sandbox' => env('JIBIT_SANDBOX', true),
                ]),
                'supported_currencies' => json_encode(['IRT', 'USD', 'EUR']),
                'fee_percentage' => 0.0,
                'fee_fixed' => 0,
                'min_amount' => 1000,
                'max_amount' => 500000000, // 500M IRT
                'logo_url' => '/assets/images/gateways/jibit.svg',
                'sort_order' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'سپهر الکترونیک',
                'slug' => 'sepehr',
                'driver' => 'App\Services\PaymentGateways\SepehrGateway',
                'description' => 'درگاه پرداخت سپهر - پرداخت امن با پشتیبانی از قبض و شارژ',
                'is_active' => true,
                'is_default' => false,
                'config' => json_encode([
                    'terminal_id' => env('SEPEHR_TERMINAL_ID', ''),
                    'sandbox' => env('SEPEHR_SANDBOX', true),
                    'get_method' => env('SEPEHR_GET_METHOD', false),
                    'rollback_enabled' => env('SEPEHR_ROLLBACK_ENABLED', false),
                    'api_version' => 'v3.0.6',
                    'timeout' => 30,
                    'retry_attempts' => 3,
                ]),
                'supported_currencies' => json_encode(['IRT']),
                'fee_percentage' => 0.0,
                'fee_fixed' => 0,
                'min_amount' => 1000,
                'max_amount' => 500000000, // 500M IRT
                'logo_url' => '/assets/images/gateways/sepehr.svg',
                'sort_order' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'سامان الکترونیک',
                'slug' => 'sep',
                'driver' => 'App\Services\PaymentGateways\SepGateway',
                'description' => 'درگاه پرداخت سامان - پرداخت امن با قابلیت‌های پیشرفته',
                'is_active' => true,
                'is_default' => false,
                'config' => json_encode([
                    'terminal_id' => env('SEP_TERMINAL_ID', ''),
                    'sandbox' => env('SEP_SANDBOX', true),
                    'token_expiry_minutes' => env('SEP_TOKEN_EXPIRY', 20),
                    'refund_enabled' => env('SEP_REFUND_ENABLED', false),
                    'api_version' => 'v4.1',
                    'timeout' => 30,
                    'retry_attempts' => 3,
                ]),
                'supported_currencies' => json_encode(['IRT']),
                'fee_percentage' => 0.0,
                'fee_fixed' => 0,
                'min_amount' => 1000,
                'max_amount' => 500000000, // 500M IRT
                'logo_url' => '/assets/images/gateways/sep.svg',
                'sort_order' => 4,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('payment_gateways')->insert($gateways);
    }
}