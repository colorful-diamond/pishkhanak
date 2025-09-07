<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class IranianMobile implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Clean the value
        $mobile = preg_replace('/\D/', '', $value);
        
        // Check if starts with 09 and has 11 digits
        if (!preg_match('/^09\d{9}$/', $mobile)) {
            $fail('شماره موبایل باید با 09 شروع شود و 11 رقم باشد');
            return;
        }
        
        // Check valid Iranian mobile prefixes
        $validPrefixes = [
            '0901', '0902', '0903', '0905', // همراه اول
            '0990', '0991', '0992', '0993', '0994', // همراه اول
            '0910', '0911', '0912', '0913', '0914', // ایرانسل
            '0915', '0916', '0917', '0918', '0919', // ایرانسل
            '0920', '0921', '0922', // رایتل
            '0932', '0933', '0934', '0935', '0936', '0937', '0938', '0939', // تله‌کیش
        ];
        
        $prefix = substr($mobile, 0, 4);
        
        if (!in_array($prefix, $validPrefixes)) {
            $fail('پیش شماره موبایل معتبر نیست');
        }
    }
} 