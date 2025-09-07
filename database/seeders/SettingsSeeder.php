<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Contact Information
            [
                'key' => 'contact.phone',
                'value' => '021-71057704',
                'type' => 'string',
                'group' => 'contact',
                'label' => 'شماره تلفن',
                'description' => 'شماره تلفن اصلی شرکت',
                'is_public' => true,
            ],
            [
                'key' => 'contact.mobile',
                'value' => '09123456789',
                'type' => 'string',
                'group' => 'contact',
                'label' => 'شماره موبایل',
                'description' => 'شماره موبایل پشتیبانی',
                'is_public' => true,
            ],
            [
                'key' => 'contact.email',
                'value' => 'info@pishkhanak.com',
                'type' => 'string',
                'group' => 'contact',
                'label' => 'ایمیل عمومی',
                'description' => 'ایمیل عمومی شرکت',
                'is_public' => true,
            ],
            [
                'key' => 'contact.support_email',
                'value' => 'support@pishkhanak.com',
                'type' => 'string',
                'group' => 'contact',
                'label' => 'ایمیل پشتیبانی',
                'description' => 'ایمیل پشتیبانی فنی',
                'is_public' => true,
            ],
            [
                'key' => 'contact.address',
                'value' => 'تهران، ایران',
                'type' => 'text',
                'group' => 'contact',
                'label' => 'آدرس',
                'description' => 'آدرس دفتر مرکزی',
                'is_public' => true,
            ],
            [
                'key' => 'contact.working_hours',
                'value' => 'شنبه تا چهارشنبه ۹:۳۰ الی ۱۴:۳۰',
                'type' => 'string',
                'group' => 'contact',
                'label' => 'ساعات کاری',
                'description' => 'ساعات کاری و پاسخگویی',
                'is_public' => true,
            ],
            [
                'key' => 'contact.telegram',
                'value' => 'pishkhanak_support',
                'type' => 'string',
                'group' => 'contact',
                'label' => 'تلگرام',
                'description' => 'شناسه تلگرام پشتیبانی',
                'is_public' => true,
            ],
            [
                'key' => 'contact.whatsapp',
                'value' => '',
                'type' => 'string',
                'group' => 'contact',
                'label' => 'واتساپ',
                'description' => 'شماره واتساپ',
                'is_public' => true,
            ],
            [
                'key' => 'contact.instagram',
                'value' => '',
                'type' => 'string',
                'group' => 'contact',
                'label' => 'اینستاگرام',
                'description' => 'شناسه اینستاگرام',
                'is_public' => true,
            ],
            [
                'key' => 'contact.website',
                'value' => 'https://pishkhanak.com',
                'type' => 'string',
                'group' => 'contact',
                'label' => 'وب‌سایت',
                'description' => 'آدرس وب‌سایت اصلی',
                'is_public' => true,
            ],

            // Business Information
            [
                'key' => 'business.company_name',
                'value' => 'شرکت انفورماتیک توسعه گستر ایرانیان',
                'type' => 'string',
                'group' => 'business',
                'label' => 'نام شرکت',
                'description' => 'نام کامل شرکت به فارسی',
                'is_public' => true,
            ],
            [
                'key' => 'business.company_name_en',
                'value' => 'Pishkhanak',
                'type' => 'string',
                'group' => 'business',
                'label' => 'نام شرکت (انگلیسی)',
                'description' => 'نام شرکت به انگلیسی',
                'is_public' => true,
            ],
            [
                'key' => 'business.legal_name',
                'value' => 'شرکت انفورماتیک توسعه گستر ایرانیان ( پیشخوانک )',
                'type' => 'string',
                'group' => 'business',
                'label' => 'نام قانونی',
                'description' => 'نام قانونی کامل شرکت',
                'is_public' => true,
            ],
            [
                'key' => 'business.tax_id',
                'value' => '',
                'type' => 'string',
                'group' => 'business',
                'label' => 'شناسه مالیاتی',
                'description' => 'شناسه مالیاتی شرکت',
                'is_public' => false,
            ],
            [
                'key' => 'business.national_id',
                'value' => '',
                'type' => 'string',
                'group' => 'business',
                'label' => 'شناسه ملی',
                'description' => 'شناسه ملی شرکت',
                'is_public' => false,
            ],
            [
                'key' => 'business.registration_number',
                'value' => '',
                'type' => 'string',
                'group' => 'business',
                'label' => 'شماره ثبت',
                'description' => 'شماره ثبت شرکت',
                'is_public' => false,
            ],

            // Social Media Links
            [
                'key' => 'social.telegram',
                'value' => 'https://t.me/pishkhanak_support',
                'type' => 'string',
                'group' => 'social',
                'label' => 'تلگرام',
                'description' => 'لینک کانال تلگرام',
                'is_public' => true,
            ],
            [
                'key' => 'social.whatsapp',
                'value' => '',
                'type' => 'string',
                'group' => 'social',
                'label' => 'واتساپ',
                'description' => 'لینک واتساپ',
                'is_public' => true,
            ],
            [
                'key' => 'social.instagram',
                'value' => '',
                'type' => 'string',
                'group' => 'social',
                'label' => 'اینستاگرام',
                'description' => 'لینک اینستاگرام',
                'is_public' => true,
            ],
            [
                'key' => 'social.twitter',
                'value' => '',
                'type' => 'string',
                'group' => 'social',
                'label' => 'توییتر',
                'description' => 'لینک توییتر',
                'is_public' => true,
            ],
            [
                'key' => 'social.linkedin',
                'value' => '',
                'type' => 'string',
                'group' => 'social',
                'label' => 'لینکدین',
                'description' => 'لینک لینکدین',
                'is_public' => true,
            ],
            [
                'key' => 'social.youtube',
                'value' => '',
                'type' => 'string',
                'group' => 'social',
                'label' => 'یوتیوب',
                'description' => 'لینک یوتیوب',
                'is_public' => true,
            ],

            // General Settings
            [
                'key' => 'site.title',
                'value' => 'پیشخوانک - خدمات آنلاین استعلامی و مالی',
                'type' => 'string',
                'group' => 'general',
                'label' => 'عنوان سایت',
                'description' => 'عنوان اصلی وب‌سایت',
                'is_public' => true,
            ],
            [
                'key' => 'site.description',
                'value' => 'پیشخوانک، راهکاری جامع برای دسترسی آنلاین به انواع خدمات استعلامی و مالی است.',
                'type' => 'text',
                'group' => 'general',
                'label' => 'توضیحات سایت',
                'description' => 'توضیحات متا سایت',
                'is_public' => true,
            ],
            [
                'key' => 'site.keywords',
                'value' => 'استعلام چک, خلافی خودرو, استعلام بانکی, کدپستی, پیشخوانک',
                'type' => 'text',
                'group' => 'general',
                'label' => 'کلمات کلیدی',
                'description' => 'کلمات کلیدی متا سایت',
                'is_public' => true,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
} 