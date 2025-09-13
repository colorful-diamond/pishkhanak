<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class InsuranceServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first() ?? User::factory()->create();
        $insuranceCategory = ServiceCategory::where('slug', 'insurance')->first();

        if (!$insuranceCategory) {
            echo "Insurance category not found!\n";
            return;
        }

        $insuranceServices = [
            [
                'title' => 'استعلام بیمه موتور | بیمه نامه موتور | جزییات بیمه موتور',
                'short_title' => 'موتور',
                'slug' => 'motor-insurance-inquiry',
                'summary' => 'استعلام و بررسی وضعیت بیمه موتورسیکلت و دریافت جزییات بیمه نامه',
                'content' => 'با استفاده از این سرویس می‌توانید وضعیت بیمه موتورسیکلت خود را استعلام کرده و جزییات کامل بیمه نامه موتور از جمله تاریخ انقضا، شرکت بیمه و میزان پوشش را مشاهده کنید.',
                'status' => 'active',
                'featured' => true,
                'price' => 10000,
                'cost' => 5000,
                'is_paid' => true,
            ],
            [
                'title' => 'استعلام بیمه بدنه | بیمه نامه بدنه خودرو | جزییات بیمه بدنه',
                'short_title' => 'بدنه',
                'slug' => 'car-body-insurance-inquiry',
                'summary' => 'استعلام و بررسی وضعیت بیمه بدنه خودرو و دریافت اطلاعات کامل پوشش',
                'content' => 'از طریق این سرویس می‌توانید وضعیت بیمه بدنه خودرو خود را بررسی کنید و اطلاعات دقیق در مورد پوشش بیمه، تاریخ انقضا و میزان خسارت قابل پرداخت را دریافت کنید.',
                'status' => 'active',
                'featured' => true,
                'price' => 10000,
                'cost' => 5000,
                'is_paid' => true,
            ],
            [
                'title' => 'استعلام بیمه آتش سوزی | بیمه نامه آتش سوزی ساختمان | جزییات پوشش آتش سوزی',
                'short_title' => 'آتش سوزی',
                'slug' => 'fire-insurance-inquiry',
                'summary' => 'استعلام بیمه آتش سوزی املاک و ساختمان و بررسی میزان پوشش',
                'content' => 'با این سرویس می‌توانید وضعیت بیمه آتش سوزی املاک و ساختمان خود را استعلام کنید و از جزییات پوشش، حدود مسئولیت و شرایط جبران خسارت آگاه شوید.',
                'status' => 'active',
                'featured' => true,
                'price' => 10000,
                'cost' => 5000,
                'is_paid' => true,
            ],
            [
                'title' => 'استعلام بیمه زلزله | بیمه نامه زلزله ساختمان | پوشش خسارات زلزله',
                'short_title' => 'زلزله',
                'slug' => 'earthquake-insurance-inquiry',
                'summary' => 'استعلام بیمه زلزله املاک و ساختمان و بررسی پوشش خسارات ناشی از زلزله',
                'content' => 'از طریق این سرویس می‌توانید وضعیت بیمه زلزله املاک خود را بررسی کنید و از جزییات پوشش، میزان خسارت قابل دریافت و شرایط جبران خسارت در صورت وقوع زلزله مطلع شوید.',
                'status' => 'active',
                'featured' => true,
                'price' => 10000,
                'cost' => 5000,
                'is_paid' => true,
            ],
            [
                'title' => 'استعلام بیمه سفر کربلا | بیمه نامه سفر کربلا | پوشش سفر زیارتی',
                'short_title' => 'سفر کربلا',
                'slug' => 'karbala-travel-insurance-inquiry',
                'summary' => 'استعلام بیمه سفر زیارتی کربلا و بررسی پوشش‌های سفر',
                'content' => 'با این سرویس می‌توانید وضعیت بیمه سفر زیارتی کربلا خود را استعلام کنید و از جزییات پوشش درمانی، حوادث و سایر خدمات مربوط به سفر زیارتی اطلاع یابید.',
                'status' => 'active',
                'featured' => true,
                'price' => 10000,
                'cost' => 5000,
                'is_paid' => true,
            ],
            [
                'title' => 'استعلام بیمه تکمیلی درمان | بیمه نامه تکمیلی درمان | پوشش درمانی اضافی',
                'short_title' => 'تکمیلی درمان',
                'slug' => 'supplementary-health-insurance-inquiry',
                'summary' => 'استعلام بیمه تکمیلی درمان و بررسی پوشش‌های درمانی اضافی',
                'content' => 'از طریق این سرویس می‌توانید وضعیت بیمه تکمیلی درمان خود را بررسی کنید و از جزییات پوشش‌های درمانی، میزان فرانشیز و خدمات قابل استفاده آگاه شوید.',
                'status' => 'active',
                'featured' => true,
                'price' => 10000,
                'cost' => 5000,
                'is_paid' => true,
            ],
            [
                'title' => 'استعلام بیمه مسافرتی | بیمه نامه مسافرت | پوشش بیمه سفر',
                'short_title' => 'مسافرتی',
                'slug' => 'travel-insurance-inquiry',
                'summary' => 'استعلام بیمه مسافرتی و بررسی پوشش‌های سفر داخلی و خارجی',
                'content' => 'با استفاده از این سرویس می‌توانید وضعیت بیمه مسافرتی خود را استعلام کنید و از جزییات پوشش درمانی، لغو سفر، تاخیر پرواز و سایر خدمات مربوط به سفر اطلاع یابید.',
                'status' => 'active',
                'featured' => true,
                'price' => 10000,
                'cost' => 5000,
                'is_paid' => true,
            ],
            [
                'title' => 'استعلام بیمه مسئولیت پزشکان | بیمه حرفه‌ای پزشکی | پوشش مسئولیت پزشکی',
                'short_title' => 'مسئولیت پزشکان',
                'slug' => 'medical-liability-insurance-inquiry',
                'summary' => 'استعلام بیمه مسئولیت حرفه‌ای پزشکان و بررسی پوشش مسئولیت',
                'content' => 'از طریق این سرویس می‌توانید وضعیت بیمه مسئولیت حرفه‌ای پزشکی خود را بررسی کنید و از جزییات پوشش، حدود مسئولیت و شرایط جبران خسارت آگاه شوید.',
                'status' => 'active',
                'featured' => true,
                'price' => 10000,
                'cost' => 5000,
                'is_paid' => true,
            ],
            [
                'title' => 'استعلام بیمه آسانسور | بیمه نامه آسانسور | پوشش حوادث آسانسور',
                'short_title' => 'آسانسور',
                'slug' => 'elevator-insurance-inquiry',
                'summary' => 'استعلام بیمه آسانسور و بررسی پوشش حوادث و خسارات آسانسور',
                'content' => 'با این سرویس می‌توانید وضعیت بیمه آسانسور خود را استعلام کنید و از جزییات پوشش حوادث، خسارات فنی و مسئولیت مدنی آسانسور اطلاع یابید.',
                'status' => 'active',
                'featured' => true,
                'price' => 10000,
                'cost' => 5000,
                'is_paid' => true,
            ],
            [
                'title' => 'استعلام بیمه ورزشی | بیمه نامه ورزش | پوشش حوادث ورزشی',
                'short_title' => 'ورزشی',
                'slug' => 'sports-insurance-inquiry',
                'summary' => 'استعلام بیمه ورزشی و بررسی پوشش حوادث و آسیب‌های ورزشی',
                'content' => 'از طریق این سرویس می‌توانید وضعیت بیمه ورزشی خود را بررسی کنید و از جزییات پوشش حوادث ورزشی، درمان آسیب‌ها و خدمات توانبخشی آگاه شوید.',
                'status' => 'active',
                'featured' => true,
                'price' => 10000,
                'cost' => 5000,
                'is_paid' => true,
            ],
            [
                'title' => 'استعلام بیمه حوادث انفرادی | بیمه نامه حوادث شخصی | پوشش حوادث فردی',
                'short_title' => 'حوادث انفرادی',
                'slug' => 'personal-accident-insurance-inquiry',
                'summary' => 'استعلام بیمه حوادث انفرادی و بررسی پوشش حوادث شخصی',
                'content' => 'با استفاده از این سرویس می‌توانید وضعیت بیمه حوادث انفرادی خود را استعلام کنید و از جزییات پوشش حوادث، میزان غرامت و شرایط پرداخت آگاه شوید.',
                'status' => 'active',
                'featured' => true,
                'price' => 10000,
                'cost' => 5000,
                'is_paid' => true,
            ],
            [
                'title' => 'استعلام بیمه مستمری | بیمه نامه عمر و مستمری | پوشش مستمری',
                'short_title' => 'مستمری',
                'slug' => 'pension-insurance-inquiry',
                'summary' => 'استعلام بیمه مستمری و عمر و بررسی وضعیت مستمری',
                'content' => 'از طریق این سرویس می‌توانید وضعیت بیمه مستمری و عمر خود را بررسی کنید و از جزییات مستمری، میزان پرداخت و شرایط دریافت اطلاع یابید.',
                'status' => 'active',
                'featured' => true,
                'price' => 10000,
                'cost' => 5000,
                'is_paid' => true,
            ],
            [
                'title' => 'استعلام بیمه جنگ ساختمان | بیمه نامه جنگ | پوشش خسارات جنگی',
                'short_title' => 'جنگ ساختمان',
                'slug' => 'war-building-insurance-inquiry',
                'summary' => 'استعلام بیمه جنگ ساختمان و بررسی پوشش خسارات ناشی از جنگ',
                'content' => 'با این سرویس می‌توانید وضعیت بیمه جنگ ساختمان خود را استعلام کنید و از جزییات پوشش خسارات ناشی از جنگ، حدود مسئولیت و شرایط جبران خسارت آگاه شوید.',
                'status' => 'active',
                'featured' => true,
                'price' => 10000,
                'cost' => 5000,
                'is_paid' => true,
            ],
        ];

        foreach ($insuranceServices as $service) {
            Service::firstOrCreate(
                ['slug' => $service['slug']],
                array_merge($service, [
                    'category_id' => $insuranceCategory->id,
                    'author_id' => $user->id,
                ])
            );
        }

        echo "Insurance services seeded successfully!\n";
    }
}