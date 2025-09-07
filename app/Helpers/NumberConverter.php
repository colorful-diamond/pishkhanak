<?php

namespace App\Helpers;

class NumberConverter
{
    /**
     * Persian digits
     */
    private static array $persianDigits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    
    /**
     * Arabic digits
     */
    private static array $arabicDigits = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
    
    /**
     * English digits
     */
    private static array $englishDigits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

    /**
     * Convert Persian/Arabic digits to English digits
     */
    public static function toEnglish(string $input): string
    {
        // Convert Persian digits to English
        $result = str_replace(self::$persianDigits, self::$englishDigits, $input);
        
        // Convert Arabic digits to English
        $result = str_replace(self::$arabicDigits, self::$englishDigits, $result);
        
        return $result;
    }

    /**
     * Convert English digits to Persian digits
     */
    public static function toPersian(string $input): string
    {
        return str_replace(self::$englishDigits, self::$persianDigits, $input);
    }

    /**
     * Convert English digits to Arabic digits
     */
    public static function toArabic(string $input): string
    {
        return str_replace(self::$englishDigits, self::$arabicDigits, $input);
    }

    /**
     * Clean and convert phone number to English format
     */
    public static function cleanMobile(string $mobile): string
    {
        // Convert to English digits first
        $mobile = self::toEnglish($mobile);
        
        // Remove all non-numeric characters
        $mobile = preg_replace('/\D/', '', $mobile);
        
        // Add leading zero if missing for Iranian mobile numbers
        if (strlen($mobile) === 10 && substr($mobile, 0, 1) === '9') {
            $mobile = '0' . $mobile;
        }
        
        return $mobile;
    }

    /**
     * Validate Iranian mobile number format
     */
    public static function isValidIranianMobile(string $mobile): bool
    {
        $cleaned = self::cleanMobile($mobile);
        
        // Iranian mobile should be 11 digits starting with 09
        return preg_match('/^09[0-9]{9}$/', $cleaned);
    }

    /**
     * Format mobile for display
     */
    public static function formatMobileForDisplay(string $mobile): string
    {
        $cleaned = self::cleanMobile($mobile);
        
        if (strlen($cleaned) === 11) {
            return substr($cleaned, 0, 4) . ' ' . substr($cleaned, 4, 3) . ' ' . substr($cleaned, 7);
        }
        
        return $cleaned;
    }

    /**
     * Clean and format card number
     */
    public static function cleanCardNumber(string $cardNumber): string
    {
        // Convert to English digits
        $cardNumber = self::toEnglish($cardNumber);
        
        // Remove all non-numeric characters
        return preg_replace('/\D/', '', $cardNumber);
    }

    /**
     * Clean national code
     */
    public static function cleanNationalCode(string $nationalCode): string
    {
        // Convert to English digits
        $nationalCode = self::toEnglish($nationalCode);
        
        // Remove all non-numeric characters
        return preg_replace('/\D/', '', $nationalCode);
    }

    /**
     * Clean IBAN/Sheba number
     */
    public static function cleanIban(string $iban): string
    {
        // Convert to English digits
        $iban = self::toEnglish($iban);
        
        // Remove spaces and convert to uppercase
        $iban = strtoupper(str_replace(' ', '', $iban));
        
        // Add IR prefix if missing
        if (!str_starts_with($iban, 'IR') && strlen($iban) === 24) {
            $iban = 'IR' . $iban;
        }
        
        return $iban;
    }

    /**
     * Clean numeric input (general purpose)
     */
    public static function cleanNumeric(string $input): string
    {
        // Convert to English digits
        $input = self::toEnglish($input);
        
        // Remove all non-numeric characters
        return preg_replace('/\D/', '', $input);
    }

    /**
     * Check if string contains Persian digits
     */
    public static function hasPersianDigits(string $input): bool
    {
        foreach (self::$persianDigits as $digit) {
            if (str_contains($input, $digit)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if string contains Arabic digits
     */
    public static function hasArabicDigits(string $input): bool
    {
        foreach (self::$arabicDigits as $digit) {
            if (str_contains($input, $digit)) {
                return true;
            }
        }
        return false;
    }
} 