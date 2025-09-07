<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class IranianCardNumber implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Clean the value
        $cardNumber = preg_replace('/\D/', '', $value);
        
        // Check length
        if (strlen($cardNumber) !== 16) {
            $fail('شماره کارت باید 16 رقم باشد');
            return;
        }
        
        // Check for repeated digits
        if (preg_match('/^(\d)\1{15}$/', $cardNumber)) {
            $fail('شماره کارت نمی‌تواند تمام ارقام یکسان باشد');
            return;
        }
        
        // Luhn algorithm validation
        if (!$this->luhnCheck($cardNumber)) {
            $fail('شماره کارت معتبر نیست');
            return;
        }
        
        // Check Iranian bank prefixes
        $iranianPrefixes = [
            '627760', '627761', '627762', '627763', '627764', // بانک پست
            '627353', '627381', '627412', '627419', '627488', // بانک پاسارگاد
            '627648', '627649', '627648', '627649', '627653', // بانک صنعت و معدن
            '627760', '627761', '627762', '627763', '627764', // بانک پست
            '627381', '627412', '627488', '627648', '627649', // سایر بانک‌ها
            '622106', '627884', '639607', '627760', '627761', // بانک‌های مختلف
        ];
        
        $prefix = substr($cardNumber, 0, 6);
        $found = false;
        
        foreach ($iranianPrefixes as $iranianPrefix) {
            if (str_starts_with($prefix, $iranianPrefix)) {
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            $fail('شماره کارت متعلق به بانک ایرانی نیست');
        }
    }
    
    /**
     * Luhn algorithm implementation
     */
    private function luhnCheck(string $cardNumber): bool
    {
        $sum = 0;
        $length = strlen($cardNumber);
        
        for ($i = 0; $i < $length; $i++) {
            $digit = (int) $cardNumber[$i];
            
            if (($length - $i) % 2 === 0) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }
            
            $sum += $digit;
        }
        
        return $sum % 10 === 0;
    }
} 