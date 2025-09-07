<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class SmartValidator
{
    protected GeminiService $geminiService;
    protected ServiceFormAnalyzer $formAnalyzer;
    
    protected array $validationRules = [
        'card_number' => [
            'rules' => 'required|string|regex:/^\d{16}$/',
            'messages' => [
                'required' => 'شماره کارت الزامی است',
                'regex' => 'شماره کارت باید 16 رقم باشد'
            ]
        ],
        'iban' => [
            'rules' => 'required|string|regex:/^\d{24}$/',
            'messages' => [
                'required' => 'شماره شبا الزامی است',
                'regex' => 'شماره شبا باید 24 رقم باشد'
            ]
        ],
        'national_code' => [
            'rules' => 'required|string|regex:/^\d{10}$/',
            'messages' => [
                'required' => 'کد ملی الزامی است',
                'regex' => 'کد ملی باید 10 رقم باشد'
            ]
        ],
        'mobile' => [
            'rules' => 'required|string|regex:/^09\d{9}$/',
            'messages' => [
                'required' => 'شماره موبایل الزامی است',
                'regex' => 'شماره موبایل باید با 09 شروع شود و 11 رقم باشد'
            ]
        ],
        'account_number' => [
            'rules' => 'required|string|regex:/^\d{8,20}$/',
            'messages' => [
                'required' => 'شماره حساب الزامی است',
                'regex' => 'شماره حساب باید بین 8 تا 20 رقم باشد'
            ]
        ],
        'company_id' => [
            'rules' => 'required|string|regex:/^\d{11}$/',
            'messages' => [
                'required' => 'شناسه ملی شرکت الزامی است',
                'regex' => 'شناسه ملی شرکت باید 11 رقم باشد'
            ]
        ],
        'customer_type' => [
            'rules' => 'required|string|in:personal,corporate',
            'messages' => [
                'required' => 'نوع مشتری الزامی است',
                'in' => 'نوع مشتری باید حقیقی یا حقوقی باشد'
            ]
        ],
        'bank_id' => [
            'rules' => 'required|integer|exists:banks,id',
            'messages' => [
                'required' => 'انتخاب بانک الزامی است',
                'exists' => 'بانک انتخاب شده معتبر نیست'
            ]
        ]
    ];

    public function __construct(GeminiService $geminiService, ServiceFormAnalyzer $formAnalyzer)
    {
        $this->geminiService = $geminiService;
        $this->formAnalyzer = $formAnalyzer;
    }

    /**
     * Validate single field with AI enhancement
     */
    public function validateField(string $fieldName, $value, array $context = []): array
    {
        $result = [
            'valid' => false,
            'value' => $value,
            'errors' => [],
            'suggestions' => [],
            'confidence' => 0.0,
            'ai_used' => false,
            'field_type' => null
        ];

        // Basic validation
        $basicValidation = $this->performBasicValidation($fieldName, $value);
        $result = array_merge($result, $basicValidation);

        if (!$result['valid']) {
            // Try AI enhancement for error correction
            $aiEnhancement = $this->performAIEnhancement($fieldName, $value, $context);
            
            if ($aiEnhancement['suggested_value']) {
                $result['suggestions'] = $aiEnhancement['suggestions'];
                $result['ai_used'] = true;
                $result['confidence'] = $aiEnhancement['confidence'];
                
                // Re-validate with AI suggestion
                $suggestedValue = $aiEnhancement['suggested_value'];
                $revalidation = $this->performBasicValidation($fieldName, $suggestedValue);
                
                if ($revalidation['valid']) {
                    $result['valid'] = true;
                    $result['value'] = $suggestedValue;
                    $result['errors'] = [];
                    $result['suggestions'][] = "تصحیح شده به: {$suggestedValue}";
                }
            }
        }

        // Advanced validation
        if ($result['valid']) {
            $advancedValidation = $this->performAdvancedValidation($fieldName, $result['value']);
            $result = array_merge($result, $advancedValidation);
        }

        return $result;
    }

    /**
     * Validate multiple fields together
     */
    public function validateFields(array $fields, array $context = []): array
    {
        $results = [];
        $allValid = true;
        $globalErrors = [];

        foreach ($fields as $fieldName => $value) {
            $result = $this->validateField($fieldName, $value, $context);
            $results[$fieldName] = $result;
            
            if (!$result['valid']) {
                $allValid = false;
            }
        }

        // Cross-field validation
        $crossValidation = $this->performCrossFieldValidation($fields, $results);
        if (!empty($crossValidation)) {
            $globalErrors = array_merge($globalErrors, $crossValidation);
            $allValid = false;
        }

        return [
            'valid' => $allValid,
            'fields' => $results,
            'global_errors' => $globalErrors,
            'validated_data' => $this->extractValidatedData($results),
            'suggestions' => $this->extractSuggestions($results)
        ];
    }

    /**
     * Perform basic Laravel validation
     */
    protected function performBasicValidation(string $fieldName, $value): array
    {
        $result = [
            'valid' => false,
            'value' => $value,
            'errors' => [],
            'field_type' => $fieldName
        ];

        if (!isset($this->validationRules[$fieldName])) {
            $result['valid'] = true;
            return $result;
        }

        $rules = $this->validationRules[$fieldName];
        $validator = Validator::make(
            [$fieldName => $value],
            [$fieldName => $rules['rules']],
            $rules['messages'] ?? []
        );

        if ($validator->fails()) {
            $result['errors'] = $validator->errors()->get($fieldName);
        } else {
            $result['valid'] = true;
            $result['value'] = $this->cleanValue($fieldName, $value);
        }

        return $result;
    }

    /**
     * Perform AI enhancement for validation
     */
    protected function performAIEnhancement(string $fieldName, $value, array $context = []): array
    {
        try {
            $prompt = $this->buildAIValidationPrompt($fieldName, $value, $context);
            
            $response = $this->geminiService->chatCompletion(
                [['role' => 'user', 'content' => $prompt]],
                'google/gemini-2.5-flash',
                [
                    'temperature' => 0.3,
                    'max_tokens' => 200,
                    'json' => true
                ]
            );

            $aiResult = json_decode($response, true);
            
            return [
                'suggested_value' => $aiResult['corrected_value'] ?? null,
                'confidence' => $aiResult['confidence'] ?? 0.0,
                'suggestions' => $aiResult['suggestions'] ?? [],
                'error_type' => $aiResult['error_type'] ?? 'unknown'
            ];

        } catch (\Exception $e) {
            Log::error('AI validation enhancement failed', [
                'field' => $fieldName,
                'value' => $value,
                'error' => $e->getMessage()
            ]);

            return [
                'suggested_value' => null,
                'confidence' => 0.0,
                'suggestions' => [],
                'error_type' => 'ai_error'
            ];
        }
    }

    /**
     * Build AI validation prompt
     */
    protected function buildAIValidationPrompt(string $fieldName, $value, array $context): string
    {
        $fieldDescriptions = [
            'card_number' => 'شماره کارت بانکی 16 رقمی',
            'iban' => 'شماره شبا 24 رقمی',
            'national_code' => 'کد ملی ایرانی 10 رقمی',
            'mobile' => 'شماره موبایل ایرانی که با 09 شروع می‌شود',
            'account_number' => 'شماره حساب بانکی بین 8 تا 20 رقم',
            'company_id' => 'شناسه ملی شرکت 11 رقمی',
        ];

        $description = $fieldDescriptions[$fieldName] ?? $fieldName;
        
        return "شما یک سیستم اعتبارسنجی هوشمند هستید. کاربر برای فیلد '{$description}' مقدار '{$value}' وارد کرده است.

وظیفه شما:
1. تشخیص نوع خطا (فرمت، طول، کاراکتر اضافی، etc.)
2. تصحیح مقدار در صورت امکان
3. ارائه پیشنهادات مفید

قوانین:
- اگر مقدار فقط مشکلات فرمت دارد (مثل فاصله، خط تیره) تصحیح کنید
- اگر مقدار کاملاً نادرست است، null برگردانید
- confidence بین 0 تا 1 باشد
- suggestions عملی و مفید باشند

پاسخ را به صورت JSON برگردانید:
{
  \"corrected_value\": \"مقدار تصحیح شده یا null\",
  \"confidence\": 0.8,
  \"suggestions\": [\"پیشنهاد 1\", \"پیشنهاد 2\"],
  \"error_type\": \"format|length|invalid|partial\"
}";
    }

    /**
     * Perform advanced validation (business logic)
     */
    protected function performAdvancedValidation(string $fieldName, $value): array
    {
        $result = [
            'advanced_valid' => true,
            'advanced_errors' => [],
            'warnings' => []
        ];

        switch ($fieldName) {
            case 'card_number':
                $result = $this->validateCardNumber($value);
                break;
            case 'iban':
                $result = $this->validateIban($value);
                break;
            case 'national_code':
                $result = $this->validateNationalCode($value);
                break;
            case 'mobile':
                $result = $this->validateMobile($value);
                break;
            case 'account_number':
                $result = $this->validateAccountNumber($value);
                break;
            case 'company_id':
                $result = $this->validateCompanyId($value);
                break;
        }

        return $result;
    }

    /**
     * Advanced card number validation (Luhn algorithm)
     */
    protected function validateCardNumber(string $cardNumber): array
    {
        $result = [
            'advanced_valid' => true,
            'advanced_errors' => [],
            'warnings' => []
        ];

        // Luhn algorithm
        $sum = 0;
        $alternate = false;
        
        for ($i = strlen($cardNumber) - 1; $i >= 0; $i--) {
            $digit = (int) $cardNumber[$i];
            
            if ($alternate) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }
            
            $sum += $digit;
            $alternate = !$alternate;
        }

        if ($sum % 10 !== 0) {
            $result['advanced_valid'] = false;
            $result['advanced_errors'][] = 'شماره کارت از نظر الگوریتم Luhn معتبر نیست';
        }

        // Check bank prefix
        $bankInfo = $this->getBankByCardPrefix($cardNumber);
        if ($bankInfo) {
            $result['warnings'][] = "بانک تشخیص داده شده: {$bankInfo['name']}";
        }

        return $result;
    }

    /**
     * Advanced IBAN validation
     */
    protected function validateIban(string $iban): array
    {
        $result = [
            'advanced_valid' => true,
            'advanced_errors' => [],
            'warnings' => []
        ];

        // Check if starts with valid Iranian bank codes
        $bankCode = substr($iban, 0, 3);
        $validBankCodes = ['627', '639', '622', '627', '636']; // Sample codes
        
        if (!in_array($bankCode, $validBankCodes)) {
            $result['warnings'][] = 'کد بانک در شماره شبا ممکن است معتبر نباشد';
        }

        return $result;
    }

    /**
     * Advanced national code validation
     */
    protected function validateNationalCode(string $nationalCode): array
    {
        $result = [
            'advanced_valid' => true,
            'advanced_errors' => [],
            'warnings' => []
        ];

        // Check digit calculation
        $check = 0;
        for ($i = 0; $i < 9; $i++) {
            $check += ((int) $nationalCode[$i]) * (10 - $i);
        }
        $check = $check % 11;
        
        if ($check < 2) {
            $valid = ((int) $nationalCode[9]) === $check;
        } else {
            $valid = ((int) $nationalCode[9]) === (11 - $check);
        }

        if (!$valid) {
            $result['advanced_valid'] = false;
            $result['advanced_errors'][] = 'کد ملی از نظر الگوریتم بررسی معتبر نیست';
        }

        return $result;
    }

    /**
     * Advanced mobile validation
     */
    protected function validateMobile(string $mobile): array
    {
        $result = [
            'advanced_valid' => true,
            'advanced_errors' => [],
            'warnings' => []
        ];

        // Check mobile operator
        $operator = $this->getMobileOperator($mobile);
        if ($operator) {
            $result['warnings'][] = "اپراتور: {$operator}";
        }

        return $result;
    }

    /**
     * Advanced account number validation
     */
    protected function validateAccountNumber(string $accountNumber): array
    {
        return [
            'advanced_valid' => true,
            'advanced_errors' => [],
            'warnings' => []
        ];
    }

    /**
     * Advanced company ID validation
     */
    protected function validateCompanyId(string $companyId): array
    {
        return [
            'advanced_valid' => true,
            'advanced_errors' => [],
            'warnings' => []
        ];
    }

    /**
     * Cross-field validation
     */
    protected function performCrossFieldValidation(array $fields, array $results): array
    {
        $errors = [];

        // Example: Check if customer type matches with ID type
        if (isset($fields['customer_type']) && isset($fields['national_code']) && isset($fields['company_id'])) {
            $customerType = $fields['customer_type'];
            $hasNationalCode = !empty($fields['national_code']);
            $hasCompanyId = !empty($fields['company_id']);

            if ($customerType === 'personal' && $hasCompanyId) {
                $errors[] = 'برای مشتری حقیقی نباید شناسه شرکت وارد کنید';
            }

            if ($customerType === 'corporate' && $hasNationalCode) {
                $errors[] = 'برای مشتری حقوقی نباید کد ملی وارد کنید';
            }
        }

        return $errors;
    }

    /**
     * Clean value based on field type
     */
    protected function cleanValue(string $fieldName, $value): string
    {
        $value = trim($value);
        
        switch ($fieldName) {
            case 'card_number':
            case 'iban':
            case 'national_code':
            case 'mobile':
            case 'account_number':
            case 'company_id':
                return preg_replace('/\D/', '', $value);
            default:
                return $value;
        }
    }

    /**
     * Extract validated data from results
     */
    protected function extractValidatedData(array $results): array
    {
        $validated = [];
        
        foreach ($results as $fieldName => $result) {
            if ($result['valid']) {
                $validated[$fieldName] = $result['value'];
            }
        }
        
        return $validated;
    }

    /**
     * Extract suggestions from results
     */
    protected function extractSuggestions(array $results): array
    {
        $suggestions = [];
        
        foreach ($results as $fieldName => $result) {
            if (!empty($result['suggestions'])) {
                $suggestions[$fieldName] = $result['suggestions'];
            }
        }
        
        return $suggestions;
    }

    /**
     * Get bank information by card prefix
     */
    protected function getBankByCardPrefix(string $cardNumber): ?array
    {
        try {
            $prefix = substr($cardNumber, 0, 6);
            
            $banks = Cache::remember('bank_prefixes', 3600, function() {
                return \App\Models\Bank::where('is_active', true)
                    ->whereNotNull('card_prefixes')
                    ->get()
                    ->toArray();
            });

            foreach ($banks as $bank) {
                if (isset($bank['card_prefixes']) && is_array($bank['card_prefixes'])) {
                    foreach ($bank['card_prefixes'] as $bankPrefix) {
                        if (str_starts_with($cardNumber, $bankPrefix)) {
                            return $bank;
                        }
                    }
                }
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get mobile operator
     */
    protected function getMobileOperator(string $mobile): ?string
    {
        $operators = [
            '0901' => 'همراه اول',
            '0902' => 'همراه اول',
            '0903' => 'همراه اول',
            '0905' => 'همراه اول',
            '0990' => 'همراه اول',
            '0991' => 'همراه اول',
            '0992' => 'همراه اول',
            '0993' => 'همراه اول',
            '0994' => 'همراه اول',
            '0910' => 'ایرانسل',
            '0911' => 'ایرانسل',
            '0912' => 'ایرانسل',
            '0913' => 'ایرانسل',
            '0914' => 'ایرانسل',
            '0915' => 'ایرانسل',
            '0916' => 'ایرانسل',
            '0917' => 'ایرانسل',
            '0918' => 'ایرانسل',
            '0919' => 'ایرانسل',
            '0920' => 'رایتل',
            '0921' => 'رایتل',
            '0922' => 'رایتل',
        ];

        $prefix = substr($mobile, 0, 4);
        return $operators[$prefix] ?? null;
    }

    /**
     * Get validation summary
     */
    public function getValidationSummary(array $validationResult): array
    {
        $summary = [
            'total_fields' => count($validationResult['fields']),
            'valid_fields' => 0,
            'invalid_fields' => 0,
            'ai_corrections' => 0,
            'warnings' => 0,
            'errors' => []
        ];

        foreach ($validationResult['fields'] as $fieldName => $result) {
            if ($result['valid']) {
                $summary['valid_fields']++;
            } else {
                $summary['invalid_fields']++;
                $summary['errors'] = array_merge($summary['errors'], $result['errors']);
            }

            if ($result['ai_used']) {
                $summary['ai_corrections']++;
            }

            if (isset($result['warnings'])) {
                $summary['warnings'] += count($result['warnings']);
            }
        }

        return $summary;
    }
} 