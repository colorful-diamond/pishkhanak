<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class IranianNationalCode implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Clean the value
        $nationalCode = preg_replace('/\D/', '', $value);
        
        // Check length
        if (strlen($nationalCode) !== 10) {
            $fail('کد ملی باید 10 رقم باشد');
            return;
        }
        
        // Check for repeated digits
        if (preg_match('/^(\d)\1{9}$/', $nationalCode)) {
            $fail('کد ملی نمی‌تواند تمام ارقام یکسان باشد');
            return;
        }
        
        // Calculate check digit
        $check = 0;
        for ($i = 0; $i < 9; $i++) {
            $check += ((int) $nationalCode[$i]) * (10 - $i);
        }
        $check = $check % 11;
        
        $checkDigit = (int) $nationalCode[9];
        
        if ($check < 2) {
            $valid = $checkDigit === $check;
        } else {
            $valid = $checkDigit === (11 - $check);
        }
        
        if (!$valid) {
            $fail('کد ملی وارد شده معتبر نیست');
        }
    }
} 