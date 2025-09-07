<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class IranianIban implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Clean the value
        $iban = preg_replace('/\s/', '', strtoupper($value));
        
        // Check if it starts with IR
        if (!str_starts_with($iban, 'IR')) {
            $fail('کد شبا باید با IR شروع شود');
            return;
        }
        
        // Check length
        if (strlen($iban) !== 26) {
            $fail('کد شبا باید 26 کاراکتر باشد');
            return;
        }
        
        // Check format
        if (!preg_match('/^IR\d{24}$/', $iban)) {
            $fail('فرمت کد شبا صحیح نیست');
            return;
        }
        
        // IBAN validation algorithm
        if (!$this->validateIbanChecksum($iban)) {
            $fail('کد شبا معتبر نیست');
        }
    }
    
    /**
     * Validate IBAN checksum
     */
    private function validateIbanChecksum(string $iban): bool
    {
        // Move first 4 characters to end
        $rearranged = substr($iban, 4) . substr($iban, 0, 4);
        
        // Replace letters with numbers
        $rearranged = str_replace(['I', 'R'], ['18', '27'], $rearranged);
        
        // Calculate mod 97
        $mod = 0;
        for ($i = 0; $i < strlen($rearranged); $i++) {
            $mod = ($mod * 10 + (int) $rearranged[$i]) % 97;
        }
        
        return $mod === 1;
    }
} 