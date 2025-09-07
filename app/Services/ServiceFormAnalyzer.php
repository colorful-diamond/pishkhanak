<?php

namespace App\Services;

use App\Models\Service;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ServiceFormAnalyzer
{
    protected const CACHE_PREFIX = 'service_form_analysis:';
    protected const CACHE_TTL = 3600; // 1 hour

    protected array $formFieldPatterns = [
        'card_number' => [
            'patterns' => [
                'card[_-]?number',
                'card[_-]?no',
                'cardnumber',
                'pan',
                'شماره[_\s]*کارت',
                'کارت[_\s]*شماره'
            ],
            'type' => 'card_number',
            'validation' => 'required|card_number',
            'description' => 'شماره 16 رقمی کارت بانکی'
        ],
        'iban' => [
            'patterns' => [
                'iban',
                'شبا',
                'sheba',
                'شماره[_\s]*شبا',
                'شبا[_\s]*شماره'
            ],
            'type' => 'iban',
            'validation' => 'required|iban',
            'description' => 'شماره شبا 24 رقمی'
        ],
        'account_number' => [
            'patterns' => [
                'account[_-]?number',
                'account[_-]?no',
                'حساب[_\s]*شماره',
                'شماره[_\s]*حساب'
            ],
            'type' => 'account_number',
            'validation' => 'required|account_number',
            'description' => 'شماره حساب بانکی'
        ],
        'national_code' => [
            'patterns' => [
                'national[_-]?code',
                'national[_-]?id',
                'کد[_\s]*ملی',
                'شناسه[_\s]*ملی'
            ],
            'type' => 'national_code',
            'validation' => 'required|iranian_national_code',
            'description' => 'کد ملی 10 رقمی'
        ],
        'mobile' => [
            'patterns' => [
                'mobile',
                'phone',
                'موبایل',
                'تلفن[_\s]*همراه',
                'شماره[_\s]*موبایل'
            ],
            'type' => 'mobile',
            'validation' => 'required|iranian_mobile',
            'description' => 'شماره موبایل ایرانی'
        ],
        'company_id' => [
            'patterns' => [
                'company[_-]?id',
                'company[_-]?code',
                'شناسه[_\s]*شرکت',
                'کد[_\s]*شرکت'
            ],
            'type' => 'company_id',
            'validation' => 'required|iranian_company_id',
            'description' => 'شناسه ملی شرکت'
        ],
        'customer_type' => [
            'patterns' => [
                'customer[_-]?type',
                'نوع[_\s]*مشتری',
                'personal',
                'corporate'
            ],
            'type' => 'customer_type',
            'validation' => 'required|in:personal,corporate',
            'description' => 'نوع مشتری (حقیقی یا حقوقی)'
        ],
        'bank_id' => [
            'patterns' => [
                'bank[_-]?id',
                'bank[_-]?code',
                'بانک[_\s]*کد',
                'کد[_\s]*بانک'
            ],
            'type' => 'bank_id',
            'validation' => 'required|exists:banks,id',
            'description' => 'بانک مورد نظر'
        ]
    ];

    public function analyzeServiceForm(Service $service): array
    {
        $cacheKey = self::CACHE_PREFIX . $service->slug;
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function() use ($service) {
            return $this->performAnalysis($service);
        });
    }

    protected function performAnalysis(Service $service): array
    {
        $formViewPath = $this->getFormViewPath($service);
        
        if (!$formViewPath) {
            return $this->getEmptyAnalysis();
        }

        $viewContent = $this->getViewContent($formViewPath);
        
        if (!$viewContent) {
            return $this->getEmptyAnalysis();
        }

        $fields = $this->extractFieldsFromView($viewContent);
        $includes = $this->extractIncludes($viewContent);
        
        // تحلیل فایل‌های include شده
        foreach ($includes as $include) {
            $includeFields = $this->analyzeIncludeFile($include);
            // Merge fields while avoiding duplicates by key
            foreach ($includeFields as $fieldName => $fieldInfo) {
                if (!isset($fields[$fieldName])) {
                    $fields[$fieldName] = $fieldInfo;
                }
            }
        }

        // Ensure required_fields are in the correct format and remove duplicates by type
        $requiredFields = array_filter($fields, fn($field) => $field['required']);
        
        // Remove duplicate field types (keep only one field per type)
        $uniqueRequiredFields = [];
        $seenTypes = [];
        
        foreach ($requiredFields as $field) {
            $fieldType = $field['type'];
            if (!in_array($fieldType, $seenTypes)) {
                $uniqueRequiredFields[] = $field;
                $seenTypes[] = $fieldType;
            }
        }
        
        // Same for optional fields
        $optionalFields = array_filter($fields, fn($field) => !$field['required']);
        $uniqueOptionalFields = [];
        $seenOptionalTypes = [];
        
        foreach ($optionalFields as $field) {
            $fieldType = $field['type'];
            if (!in_array($fieldType, $seenOptionalTypes)) {
                $uniqueOptionalFields[] = $field;
                $seenOptionalTypes[] = $fieldType;
            }
        }
        
        return [
            'service_id' => $service->id,
            'service_slug' => $service->slug,
            'form_path' => $formViewPath,
            'fields' => $fields,
            'field_count' => count($fields),
            'required_fields' => array_values($uniqueRequiredFields),
            'optional_fields' => array_values($uniqueOptionalFields),
            'includes' => $includes,
            'analyzed_at' => now()->toISOString(),
        ];
    }

    protected function getFormViewPath(Service $service): ?string
    {
        $possiblePaths = [
            "front.services.custom.{$service->slug}.upper",
            "front.services.custom.{$service->slug}.form",
            "front.services.{$service->slug}.upper",
            "front.services.{$service->slug}.form",
        ];

        foreach ($possiblePaths as $path) {
            $filePath = resource_path('views/' . str_replace('.', '/', $path) . '.blade.php');
            if (File::exists($filePath)) {
                return $filePath;
            }
        }

        return null;
    }

    protected function getViewContent(string $filePath): ?string
    {
        try {
            return File::get($filePath);
        } catch (\Exception $e) {
            Log::error("خطا در خواندن فایل فرم: {$filePath}", ['error' => $e->getMessage()]);
            return null;
        }
    }

    protected function extractFieldsFromView(string $content): array
    {
        $fields = [];
        
        // استخراج فیلدهای input
        preg_match_all('/name=["\']([^"\']+)["\']/', $content, $nameMatches);
        preg_match_all('/id=["\']([^"\']+)["\']/', $content, $idMatches);
        
        $fieldNames = array_unique(array_merge($nameMatches[1], $idMatches[1]));
        
        foreach ($fieldNames as $fieldName) {
            $fieldInfo = $this->analyzeFieldName($fieldName);
            if ($fieldInfo) {
                $fieldInfo['extracted_from'] = 'input_field';
                $fieldInfo['original_name'] = $fieldName;
                $fields[$fieldName] = $fieldInfo;
            }
        }

        // استخراج فیلدهای label
        preg_match_all('/for=["\']([^"\']+)["\']/', $content, $labelMatches);
        foreach ($labelMatches[1] as $labelFor) {
            if (!isset($fields[$labelFor])) {
                $fieldInfo = $this->analyzeFieldName($labelFor);
                if ($fieldInfo) {
                    $fieldInfo['extracted_from'] = 'label';
                    $fieldInfo['original_name'] = $labelFor;
                    $fields[$labelFor] = $fieldInfo;
                }
            }
        }

        // استخراج data-validate attributes
        preg_match_all('/data-validate=["\']([^"\']+)["\']/', $content, $validateMatches);
        foreach ($validateMatches[1] as $index => $validation) {
            // پیدا کردن فیلد مربوطه
            $fieldName = $this->findFieldForValidation($content, $validation);
            if ($fieldName && isset($fields[$fieldName])) {
                $fields[$fieldName]['validation_rules'] = $validation;
                $fields[$fieldName]['required'] = str_contains($validation, 'required');
            }
        }

        return $fields;
    }

    protected function analyzeFieldName(string $fieldName): ?array
    {
        $fieldName = strtolower($fieldName);
        
        // Skip error fields, success fields, and other non-input fields
        if (preg_match('/(error|errors|success|message|alert|warning|status)$/i', $fieldName)) {
            return null;
        }
        
        // Skip fields that contain error/success indicators
        if (preg_match('/([-_](error|errors|success|message|alert|warning|status)[-_]?|[-_]?error[-_]?)/i', $fieldName)) {
            return null;
        }
        
        foreach ($this->formFieldPatterns as $type => $config) {
            foreach ($config['patterns'] as $pattern) {
                if (preg_match('/' . $pattern . '/i', $fieldName)) {
                    return [
                        'name' => $fieldName,
                        'type' => $config['type'],
                        'validation' => $config['validation'],
                        'description' => $config['description'],
                        'label' => $this->getFieldLabel($config['type']),
                        'required' => str_contains($config['validation'], 'required'),
                        'pattern_matched' => $pattern,
                        'field_category' => $type
                    ];
                }
            }
        }

        return null;
    }

    /**
     * Get Persian field label for display
     */
    protected function getFieldLabel(string $fieldType): string
    {
        $labels = [
            'card_number' => 'شماره کارت',
            'iban' => 'شماره شبا',
            'account_number' => 'شماره حساب',
            'national_code' => 'کد ملی',
            'mobile' => 'شماره موبایل',
            'company_id' => 'شناسه شرکت',
            'customer_type' => 'نوع مشتری',
            'bank_id' => 'بانک',
            'amount' => 'مبلغ',
            'currency' => 'واحد پول',
            'email' => 'ایمیل',
            'first_name' => 'نام',
            'last_name' => 'نام خانوادگی',
            'birth_date' => 'تاریخ تولد',
            'address' => 'آدرس',
            'postal_code' => 'کد پستی',
            'city' => 'شهر',
            'province' => 'استان',
            'plate_number' => 'شماره پلاک',
            'chassis_number' => 'شماره شاسی',
            'engine_number' => 'شماره موتور',
            'vehicle_id' => 'شناسه خودرو'
        ];
        
        return $labels[$fieldType] ?? $fieldType;
    }

    /**
     * Clear analysis cache for a specific service
     */
    public function clearServiceCache(Service $service): void
    {
        $cacheKey = 'service_form_analysis_' . $service->id . '_' . $service->updated_at->timestamp;
        Cache::forget($cacheKey);
    }

    /**
     * Clear all analysis cache
     */
    public function clearAllCache(): void
    {
        Cache::flush();
    }

    protected function extractIncludes(string $content): array
    {
        $includes = [];
        
        // استخراج @include
        preg_match_all('/@include\(["\']([^"\']+)["\']/', $content, $includeMatches);
        foreach ($includeMatches[1] as $include) {
            $includes[] = $include;
        }

        return $includes;
    }

    protected function analyzeIncludeFile(string $includePath): array
    {
        $filePath = resource_path('views/' . str_replace('.', '/', $includePath) . '.blade.php');
        
        if (!File::exists($filePath)) {
            return [];
        }

        $content = File::get($filePath);
        return $this->extractFieldsFromView($content);
    }

    protected function findFieldForValidation(string $content, string $validation): ?string
    {
        // جستجو برای فیلد با data-validate مشخص
        if (preg_match('/name=["\']([^"\']+)["\'][^>]*data-validate=["\']' . preg_quote($validation, '/') . '["\']/', $content, $matches)) {
            return $matches[1];
        }
        
        if (preg_match('/data-validate=["\']' . preg_quote($validation, '/') . '["\'][^>]*name=["\']([^"\']+)["\']/', $content, $matches)) {
            return $matches[1];
        }

        return null;
    }

    protected function getEmptyAnalysis(): array
    {
        return [
            'service_id' => null,
            'service_slug' => null,
            'form_path' => null,
            'fields' => [],
            'field_count' => 0,
            'required_fields' => [],
            'optional_fields' => [],
            'includes' => [],
            'analyzed_at' => now()->toISOString(),
        ];
    }

    public function getFieldsByService(string $serviceSlug): array
    {
        $service = Service::where('slug', $serviceSlug)->first();
        
        if (!$service) {
            return [];
        }

        $analysis = $this->analyzeServiceForm($service);
        return $analysis['fields'] ?? [];
    }

    public function getRequiredFieldsByService(string $serviceSlug): array
    {
        $analysis = $this->analyzeServiceForm(Service::where('slug', $serviceSlug)->first());
        return $analysis['required_fields'] ?? [];
    }

    public function validateFieldData(string $serviceSlug, array $data): array
    {
        $fields = $this->getFieldsByService($serviceSlug);
        $errors = [];
        $validatedData = [];

        foreach ($fields as $fieldName => $fieldInfo) {
            $value = $data[$fieldName] ?? null;
            
            if ($fieldInfo['required'] && empty($value)) {
                $errors[$fieldName] = "{$fieldInfo['description']} اجباری است";
                continue;
            }

            if (!empty($value)) {
                $validationResult = $this->validateSingleField($fieldInfo, $value);
                if ($validationResult['valid']) {
                    $validatedData[$fieldName] = $validationResult['value'];
                } else {
                    $errors[$fieldName] = $validationResult['error'];
                }
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'validated_data' => $validatedData,
            'fields_analyzed' => count($fields)
        ];
    }

    protected function validateSingleField(array $fieldInfo, $value): array
    {
        switch ($fieldInfo['type']) {
            case 'card_number':
                return $this->validateCardNumber($value);
            case 'iban':
                return $this->validateIban($value);
            case 'national_code':
                return $this->validateNationalCode($value);
            case 'mobile':
                return $this->validateMobile($value);
            case 'account_number':
                return $this->validateAccountNumber($value);
            case 'company_id':
                return $this->validateCompanyId($value);
            default:
                return ['valid' => true, 'value' => $value];
        }
    }

    protected function validateCardNumber(string $value): array
    {
        $cleaned = preg_replace('/\D/', '', $value);
        
        if (strlen($cleaned) !== 16) {
            return ['valid' => false, 'error' => 'شماره کارت باید 16 رقم باشد'];
        }

        // اعتبارسنجی Luhn algorithm
        $sum = 0;
        for ($i = 0; $i < 16; $i++) {
            $digit = (int) $cleaned[$i];
            if (($i % 2) === 0) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }
            $sum += $digit;
        }

        if ($sum % 10 !== 0) {
            return ['valid' => false, 'error' => 'شماره کارت معتبر نیست'];
        }

        return ['valid' => true, 'value' => $cleaned];
    }

    protected function validateIban(string $value): array
    {
        $cleaned = preg_replace('/\D/', '', $value);
        
        if (strlen($cleaned) !== 24) {
            return ['valid' => false, 'error' => 'شماره شبا باید 24 رقم باشد'];
        }

        return ['valid' => true, 'value' => $cleaned];
    }

    protected function validateNationalCode(string $value): array
    {
        $cleaned = preg_replace('/\D/', '', $value);
        
        if (strlen($cleaned) !== 10) {
            return ['valid' => false, 'error' => 'کد ملی باید 10 رقم باشد'];
        }

        // اعتبارسنجی کد ملی ایرانی
        $check = 0;
        for ($i = 0; $i < 9; $i++) {
            $check += ((int) $cleaned[$i]) * (10 - $i);
        }
        $check = $check % 11;
        
        if ($check < 2) {
            $valid = ((int) $cleaned[9]) === $check;
        } else {
            $valid = ((int) $cleaned[9]) === (11 - $check);
        }

        if (!$valid) {
            return ['valid' => false, 'error' => 'کد ملی معتبر نیست'];
        }

        return ['valid' => true, 'value' => $cleaned];
    }

    protected function validateMobile(string $value): array
    {
        $cleaned = preg_replace('/\D/', '', $value);
        
        if (!preg_match('/^09\d{9}$/', $cleaned)) {
            return ['valid' => false, 'error' => 'شماره موبایل باید با 09 شروع شود و 11 رقم باشد'];
        }

        return ['valid' => true, 'value' => $cleaned];
    }

    protected function validateAccountNumber(string $value): array
    {
        $cleaned = preg_replace('/\D/', '', $value);
        
        if (strlen($cleaned) < 8 || strlen($cleaned) > 20) {
            return ['valid' => false, 'error' => 'شماره حساب باید بین 8 تا 20 رقم باشد'];
        }

        return ['valid' => true, 'value' => $cleaned];
    }

    protected function validateCompanyId(string $value): array
    {
        $cleaned = preg_replace('/\D/', '', $value);
        
        if (strlen($cleaned) !== 11) {
            return ['valid' => false, 'error' => 'شناسه ملی شرکت باید 11 رقم باشد'];
        }

        return ['valid' => true, 'value' => $cleaned];
    }

    public function clearCache(string $serviceSlug = null): void
    {
        if ($serviceSlug) {
            Cache::forget(self::CACHE_PREFIX . $serviceSlug);
        } else {
            Cache::flush();
        }
    }
} 