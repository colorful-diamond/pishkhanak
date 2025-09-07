<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first() ?? User::factory()->create();
        
        $bankingCategory = ServiceCategory::where('slug', 'banking-services')->first();
        $vehicleCategory = ServiceCategory::where('slug', 'vehicle-services')->first();
        $otherCategory = ServiceCategory::where('slug', 'other-services')->first();

        if ($bankingCategory) {
            $bankingServices = [
                [
                    'title' => 'استعلام وضعیت رنگ چک',
                    'slug' => 'check-status-inquiry',
                    'summary' => 'استعلام وضعیت رنگ چک و بررسی اعتبار آن',
                    'content' => 'محتوای کامل استعلام وضعیت رنگ چک',
                    'status' => 'active',
                    'featured' => true,
                ],
                [
                    'title' => 'وام و تسهیلات',
                    'slug' => 'loan-and-facilities',
                    'summary' => 'درخواست وام و تسهیلات بانکی',
                    'content' => 'محتوای کامل وام و تسهیلات',
                    'status' => 'active',
                    'featured' => true,
                ],
                [
                    'title' => 'اعتبارسنجی بانکی',
                    'slug' => 'bank-validation',
                    'summary' => 'اعتبارسنجی و بررسی وضعیت بانکی',
                    'content' => 'محتوای کامل اعتبارسنجی بانکی',
                    'status' => 'active',
                    'featured' => true,
                ],
                [
                    'title' => 'محاسبه شبا',
                    'slug' => 'sheba-calculation',
                    'summary' => 'محاسبه شماره شبا از شماره کارت',
                    'content' => 'محتوای کامل محاسبه شبا',
                    'status' => 'active',
                    'featured' => false,
                ],
                [
                    'title' => 'استعلام چک برگشتی',
                    'slug' => 'returned-check-inquiry',
                    'summary' => 'استعلام چک‌های برگشتی',
                    'content' => 'محتوای کامل استعلام چک برگشتی',
                    'status' => 'active',
                    'featured' => false,
                ],
                [
                    'title' => 'استعلام مکنا',
                    'slug' => 'mekna-inquiry',
                    'summary' => 'استعلام وضعیت مکنا',
                    'content' => 'محتوای کامل استعلام مکنا',
                    'status' => 'active',
                    'featured' => false,
                ],
            ];

            foreach ($bankingServices as $service) {
                Service::firstOrCreate(
                    ['slug' => $service['slug']],
                    array_merge($service, [
                        'category_id' => $bankingCategory->id,
                        'author_id' => $user->id,
                    ])
                );
            }
        }

        if ($vehicleCategory) {
            $vehicleServices = [
                [
                    'title' => 'خلافی موتور',
                    'slug' => 'motor-violation',
                    'summary' => 'استعلام خلافی موتورسیکلت',
                    'content' => 'محتوای کامل خلافی موتور',
                    'status' => 'active',
                    'featured' => false,
                ],
                [
                    'title' => 'خلافی خودرو',
                    'slug' => 'car-violation',
                    'summary' => 'استعلام خلافی خودرو',
                    'content' => 'محتوای کامل خلافی خودرو',
                    'status' => 'active',
                    'featured' => false,
                ],
                [
                    'title' => 'لیست پلاک‌های فعال',
                    'slug' => 'active-plates',
                    'summary' => 'مشاهده لیست پلاک‌های فعال',
                    'content' => 'محتوای کامل لیست پلاک‌های فعال',
                    'status' => 'active',
                    'featured' => false,
                ],
                [
                    'title' => 'سوابق شخص ثالث',
                    'slug' => 'third-party-records',
                    'summary' => 'استعلام سوابق بیمه شخص ثالث',
                    'content' => 'محتوای کامل سوابق شخص ثالث',
                    'status' => 'active',
                    'featured' => false,
                ],
            ];

            foreach ($vehicleServices as $service) {
                Service::firstOrCreate(
                    ['slug' => $service['slug']],
                    array_merge($service, [
                        'category_id' => $vehicleCategory->id,
                        'author_id' => $user->id,
                    ])
                );
            }
        }

        if ($otherCategory) {
            $otherServices = [
                [
                    'title' => 'استعلام وضعیت حیات',
                    'slug' => 'life-status-inquiry',
                    'summary' => 'استعلام وضعیت حیات افراد',
                    'content' => 'محتوای کامل استعلام وضعیت حیات',
                    'status' => 'active',
                    'featured' => false,
                ],
                [
                    'title' => 'وضعیت نظام وظیفه',
                    'slug' => 'military-service-status',
                    'summary' => 'استعلام وضعیت نظام وظیفه',
                    'content' => 'محتوای کامل وضعیت نظام وظیفه',
                    'status' => 'active',
                    'featured' => false,
                ],
                [
                    'title' => 'استعلام شناسه ملی',
                    'slug' => 'national-id-inquiry',
                    'summary' => 'استعلام شناسه ملی',
                    'content' => 'محتوای کامل استعلام شناسه ملی',
                    'status' => 'active',
                    'featured' => false,
                ],
                [
                    'title' => 'استعلام کدپستی',
                    'slug' => 'postal-code-inquiry',
                    'summary' => 'استعلام اطلاعات کامل آدرس از کد پستی ۱۰ رقمی',
                    'content' => 'با وارد کردن کد پستی ۱۰ رقمی، اطلاعات کامل آدرس شامل استان، شهر، منطقه و آدرس دقیق را دریافت کنید. این سرویس از API معتبر جی‌بیت استفاده می‌کند و اطلاعات دقیق و به‌روز ارائه می‌دهد.',
                    'status' => 'active',
                    'featured' => true,
                    'price' => 1000, // 1000 toman
                    'requires_sms' => false,
                ],
                [
                    'title' => 'استعلام کد پستی',
                    'slug' => 'postal-code',
                    'summary' => 'استعلام اطلاعات کامل آدرس از کد پستی ۱۰ رقمی',
                    'content' => 'با وارد کردن کد پستی ۱۰ رقمی، اطلاعات کامل آدرس شامل استان، شهر، منطقه و آدرس دقیق را دریافت کنید. این سرویس از API معتبر جی‌بیت استفاده می‌کند و اطلاعات دقیق و به‌روز ارائه می‌دهد.',
                    'status' => 'active',
                    'featured' => true,
                    'price' => 1000, // 1000 toman
                    'requires_sms' => false,
                ],
            ];

            foreach ($otherServices as $service) {
                Service::firstOrCreate(
                    ['slug' => $service['slug']],
                    array_merge($service, [
                        'category_id' => $otherCategory->id,
                        'author_id' => $user->id,
                    ])
                );
            }
        }
    }
} 