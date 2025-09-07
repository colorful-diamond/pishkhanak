<?php

namespace App\Helpers;

use App\Models\Setting;

class SettingsHelper
{
    /**
     * Get contact information
     *
     * @return array
     */
    public static function getContactInfo(): array
    {
        return Setting::getContactInfo();
    }

    /**
     * Get business information
     *
     * @return array
     */
    public static function getBusinessInfo(): array
    {
        try {
            return Setting::getBusinessInfo();
        } catch (\Exception $e) {
            // If database is unavailable, return default business info
            \Log::error('Failed to get business info from database: ' . $e->getMessage());
            return [
                'name' => 'پیشخوانک',
                'email' => 'support@pishkhanak.com',
                'phone' => '021-71057704',
                'address' => 'تهران',
                'description' => 'ارائه دهنده خدمات آنلاین'
            ];
        }
    }

    /**
     * Get social media links
     *
     * @return array
     */
    public static function getSocialLinks(): array
    {
        try {
            return Setting::getSocialLinks();
        } catch (\Exception $e) {
            // If database is unavailable, return empty array
            \Log::error('Failed to get social links from database: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get a specific setting value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        try {
            return Setting::getValue($key, $default);
        } catch (\Exception $e) {
            // If database is unavailable, return the default value
            \Log::error('Failed to get setting from database: ' . $e->getMessage());
            return $default;
        }
    }

    /**
     * Get phone number
     *
     * @return string
     */
    public static function getPhone(): string
    {
        return self::get('contact.phone', '021-71057704');
    }

    /**
     * Get mobile number
     *
     * @return string
     */
    public static function getMobile(): string
    {
        return self::get('contact.mobile', '09123456789');
    }

    /**
     * Get email
     *
     * @return string
     */
    public static function getEmail(): string
    {
        return self::get('contact.email', 'info@pishkhanak.com');
    }

    /**
     * Get support email
     *
     * @return string
     */
    public static function getSupportEmail(): string
    {
        return self::get('contact.support_email', 'support@pishkhanak.com');
    }

    /**
     * Get address
     *
     * @return string
     */
    public static function getAddress(): string
    {
        return self::get('contact.address', 'تهران، ایران');
    }

    /**
     * Get working hours
     *
     * @return string
     */
    public static function getWorkingHours(): string
    {
        return self::get('contact.working_hours', 'شنبه تا چهارشنبه ۹:۳۰ الی ۱۴:۳۰');
    }

    /**
     * Get telegram username
     *
     * @return string
     */
    public static function getTelegram(): string
    {
        return self::get('contact.telegram', 'pishkhanak_support');
    }

    /**
     * Get telegram full URL
     *
     * @return string
     */
    public static function getTelegramUrl(): string
    {
        $username = self::getTelegram();
        return "https://t.me/{$username}";
    }

    /**
     * Get company name
     *
     * @return string
     */
    public static function getCompanyName(): string
    {
        return self::get('business.company_name', 'شرکت انفورماتیک توسعه گستر ایرانیان');
    }

    /**
     * Get company name in English
     *
     * @return string
     */
    public static function getCompanyNameEn(): string
    {
        return self::get('business.company_name_en', 'Pishkhanak');
    }

    /**
     * Get legal company name
     *
     * @return string
     */
    public static function getLegalName(): string
    {
        return self::get('business.legal_name', 'شرکت انفورماتیک توسعه گستر ایرانیان ( پیشخوانک )');
    }

    /**
     * Get site title
     *
     * @return string
     */
    public static function getSiteTitle(): string
    {
        return self::get('site.title', 'پیشخوانک - خدمات آنلاین استعلامی و مالی');
    }

    /**
     * Get site description
     *
     * @return string
     */
    public static function getSiteDescription(): string
    {
        return self::get('site.description', 'پیشخوانک، راهکاری جامع برای دسترسی آنلاین به انواع خدمات استعلامی و مالی است.');
    }

    /**
     * Get site keywords
     *
     * @return string
     */
    public static function getSiteKeywords(): string
    {
        return self::get('site.keywords', 'استعلام چک, خلافی خودرو, استعلام بانکی, کدپستی, پیشخوانک');
    }
} 