<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Helpers\NumberConverter;

class UserUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $input = $this->all();

        // Convert Persian/Arabic numbers to English for numeric fields
        if (isset($input['mobile'])) {
            $input['mobile'] = NumberConverter::cleanMobile($input['mobile']);
        }

        if (isset($input['card_number'])) {
            $input['card_number'] = NumberConverter::cleanCardNumber($input['card_number']);
        }

        if (isset($input['national_code'])) {
            $input['national_code'] = NumberConverter::cleanNationalCode($input['national_code']);
        }

        if (isset($input['iban'])) {
            $input['iban'] = NumberConverter::cleanIban($input['iban']);
        }

        // Apply any other numeric field conversions
        foreach ($input as $key => $value) {
            if (is_string($value) && $this->shouldConvertField($key)) {
                $input[$key] = NumberConverter::toEnglish($value);
            }
        }

        $this->replace($input);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->user()?->id,
            'mobile' => [
                'nullable',
                'string',
                'max:15',
                function ($attribute, $value, $fail) {
                    if ($value && !NumberConverter::isValidIranianMobile($value)) {
                        $fail('شماره موبایل معتبر نیست. باید با 09 شروع شود و 11 رقم باشد.');
                    }
                },
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'نام الزامی است.',
            'email.required' => 'ایمیل الزامی است.',
            'email.email' => 'فرمت ایمیل معتبر نیست.',
            'email.unique' => 'این ایمیل قبلاً استفاده شده است.',
            'mobile.max' => 'شماره موبایل نباید بیش از 15 کاراکتر باشد.',
        ];
    }

    /**
     * Determine if a field should have its numbers converted
     */
    private function shouldConvertField(string $fieldName): bool
    {
        $numericFields = [
            'mobile', 'phone', 'card_number', 'national_code', 'iban', 
            'sheba', 'amount', 'price', 'cost', 'fee'
        ];

        foreach ($numericFields as $field) {
            if (str_contains($fieldName, $field)) {
                return true;
            }
        }

        return false;
    }
} 