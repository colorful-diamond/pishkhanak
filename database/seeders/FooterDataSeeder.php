<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FooterSection;
use App\Models\FooterLink;
use App\Models\SiteLink;
use App\Models\FooterContent;

class FooterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Footer Sections
        $sections = [
            [
                'title' => 'خدمات بانکی',
                'slug' => 'banking-services',
                'description' => 'انواع خدمات بانکی و مالی',
                'icon' => 'heroicon-o-credit-card',
                'sort_order' => 1,
                'location' => 'footer',
            ],
            [
                'title' => 'خدمات چک',
                'slug' => 'check-services',
                'description' => 'استعلام و بررسی چک',
                'icon' => 'heroicon-o-document-text',
                'sort_order' => 2,
                'location' => 'footer',
            ],
            [
                'title' => 'خدمات خودرو',
                'slug' => 'vehicle-services',
                'description' => 'استعلام خودرو و خلافی',
                'icon' => 'heroicon-o-truck',
                'sort_order' => 3,
                'location' => 'footer',
            ],
            [
                'title' => 'سایر خدمات',
                'slug' => 'other-services',
                'description' => 'سایر خدمات استعلامی',
                'icon' => 'heroicon-o-cog',
                'sort_order' => 4,
                'location' => 'footer',
            ],
        ];

        foreach ($sections as $sectionData) {
            $section = FooterSection::create($sectionData);
            
            // Create links for each section
            $this->createLinksForSection($section);
        }

        // Create Site Links
        $siteLinks = [
            [
                'title' => 'درباره ما',
                'url' => '/about',
                'location' => 'footer',
                'icon' => 'heroicon-o-information-circle',
                'sort_order' => 1,
            ],
            [
                'title' => 'تماس با ما',
                'url' => '/contact',
                'location' => 'footer',
                'icon' => 'heroicon-o-phone',
                'sort_order' => 2,
            ],
            [
                'title' => 'وبلاگ',
                'url' => '/blog',
                'location' => 'footer',
                'icon' => 'heroicon-o-newspaper',
                'sort_order' => 3,
            ],
            [
                'title' => 'حریم خصوصی',
                'url' => '/privacy-policy',
                'location' => 'footer',
                'icon' => 'heroicon-o-shield-check',
                'sort_order' => 4,
            ],
            [
                'title' => 'قوانین و مقررات',
                'url' => '/terms-conditions',
                'location' => 'footer',
                'icon' => 'heroicon-o-document',
                'sort_order' => 5,
            ],
            [
                'title' => 'صفحه اصلی',
                'url' => '/',
                'location' => 'header',
                'icon' => 'heroicon-o-home',
                'sort_order' => 1,
            ],
            [
                'title' => 'خدمات',
                'url' => '#services',
                'location' => 'header',
                'icon' => 'heroicon-o-cube',
                'sort_order' => 2,
            ],
            [
                'title' => 'خانه',
                'url' => '/',
                'location' => 'mobile_nav',
                'icon' => 'heroicon-o-home',
                'sort_order' => 1,
            ],
            [
                'title' => 'خدمات',
                'url' => '#services',
                'location' => 'mobile_nav',
                'icon' => 'heroicon-o-cube',
                'sort_order' => 2,
            ],
            [
                'title' => 'کیف‌پول',
                'url' => '/wallet',
                'location' => 'mobile_nav',
                'icon' => 'heroicon-o-wallet',
                'sort_order' => 3,
            ],
            [
                'title' => 'سوابق',
                'url' => '/history',
                'location' => 'mobile_nav',
                'icon' => 'heroicon-o-folder',
                'sort_order' => 4,
            ],
            [
                'title' => 'پروفایل',
                'url' => '/profile',
                'location' => 'mobile_nav',
                'icon' => 'heroicon-o-user',
                'sort_order' => 5,
            ],
        ];

        foreach ($siteLinks as $linkData) {
            SiteLink::create($linkData);
        }

        // Create Footer Content
        $footerContents = [
            [
                'key' => 'company_name',
                'value' => 'پیشخوانک',
                'type' => 'text',
                'section' => 'general',
            ],
            [
                'key' => 'description',
                'value' => 'راهکاری جامع برای دسترسی آنلاین به انواع خدمات استعلامی و مالی. از استعلام وضعیت چک تا خدمات مربوط به خودرو و سایر نیازهای روزمره، پیشخوانک فرآیند دریافت این خدمات را برای شما ساده و سریع می‌کند.',
                'type' => 'text',
                'section' => 'general',
            ],
            [
                'key' => 'phone',
                'value' => '051-12345678',
                'type' => 'text',
                'section' => 'contact',
            ],
            [
                'key' => 'mobile',
                'value' => '09153887809',
                'type' => 'text',
                'section' => 'contact',
            ],
            [
                'key' => 'address',
                'value' => 'مشهد، خیابان جلال آل احمد، جلال آل احمد۱۰، پلاک ۵۴۳',
                'type' => 'text',
                'section' => 'contact',
            ],
            [
                'key' => 'email',
                'value' => 'info@pishkhanak.com',
                'type' => 'text',
                'section' => 'contact',
            ],
            [
                'key' => 'privacy_policy',
                'value' => '/privacy-policy',
                'type' => 'text',
                'section' => 'legal',
            ],
            [
                'key' => 'terms_conditions',
                'value' => '/terms-conditions',
                'type' => 'text',
                'section' => 'legal',
            ],
            [
                'key' => 'instagram',
                'value' => 'https://instagram.com/pishkhanak',
                'type' => 'text',
                'section' => 'social',
            ],
            [
                'key' => 'telegram',
                'value' => 'https://t.me/pishkhanak',
                'type' => 'text',
                'section' => 'social',
            ],
        ];

        foreach ($footerContents as $contentData) {
            FooterContent::create($contentData);
        }

        $this->command->info('Footer data seeded successfully!');
    }

    private function createLinksForSection(FooterSection $section): void
    {
        $links = [];

        switch ($section->slug) {
            case 'banking-services':
                $links = [
                    ['title' => 'کارت به شبا', 'url' => '/services/card-iban', 'sort_order' => 1],
                    ['title' => 'کارت به حساب', 'url' => '/services/card-account', 'sort_order' => 2],
                    ['title' => 'شبا به حساب', 'url' => '/services/iban-account', 'sort_order' => 3],
                    ['title' => 'حساب به شبا', 'url' => '/services/account-iban', 'sort_order' => 4],
                    ['title' => 'بررسی شبا', 'url' => '/services/iban-check', 'sort_order' => 5],
                ];
                break;
            case 'check-services':
                $links = [
                    ['title' => 'استعلام چک برگشتی', 'url' => '/services/check-inquiry', 'sort_order' => 1],
                    ['title' => 'استعلام مکنا', 'url' => '/services/mekna-inquiry', 'sort_order' => 2],
                    ['title' => 'وضعیت رنگ چک', 'url' => '/services/check-color', 'sort_order' => 3],
                    ['title' => 'اعتبارسنجی بانکی', 'url' => '/services/bank-validation', 'sort_order' => 4],
                ];
                break;
            case 'vehicle-services':
                $links = [
                    ['title' => 'استعلام خلافی', 'url' => '/services/traffic-violation', 'sort_order' => 1],
                    ['title' => 'استعلام بیمه', 'url' => '/services/insurance-inquiry', 'sort_order' => 2],
                    ['title' => 'استعلام معاینه فنی', 'url' => '/services/technical-inspection', 'sort_order' => 3],
                    ['title' => 'استعلام پلاک', 'url' => '/services/plate-inquiry', 'sort_order' => 4],
                ];
                break;
            case 'other-services':
                $links = [
                    ['title' => 'استعلام کدپستی', 'url' => '/services/postal-code', 'sort_order' => 1],
                    ['title' => 'استعلام کد ملی', 'url' => '/services/national-id', 'sort_order' => 2],
                    ['title' => 'استعلام نظام وظیفه', 'url' => '/services/military-service', 'sort_order' => 3],
                    ['title' => 'استعلام تلفن', 'url' => '/services/phone-inquiry', 'sort_order' => 4],
                ];
                break;
        }

        foreach ($links as $linkData) {
            $linkData['footer_section_id'] = $section->id;
            FooterLink::create($linkData);
        }
    }
} 