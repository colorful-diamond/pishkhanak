<?php

namespace App\Services;

use App\Models\Service;
use App\Models\Bank;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ServiceUrlGenerator
{
    protected ConversationManager $conversationManager;
    protected SmartValidator $smartValidator;

    public function __construct(ConversationManager $conversationManager, SmartValidator $smartValidator)
    {
        $this->conversationManager = $conversationManager;
        $this->smartValidator = $smartValidator;
    }

    /**
     * Generate service URL with validated parameters
     */
    public function generateServiceUrl($service, array $fieldData = []): ?string
    {
        // Handle both Service model and string slug
        if (is_string($service)) {
            $service = Service::where('slug', $service)->first();
        }
        
        if (!$service) {
            Log::error("Service not found");
            return null;
        }

        // Detect bank-specific service based on field data
        $bankService = $this->detectBankSpecificService($service, $fieldData);
        if ($bankService && $bankService->id !== $service->id) {
            $service = $bankService;
            Log::info("Switched to bank-specific service: {$service->title}");
        }

        $baseUrl = $this->getServiceBaseUrl($service);
        
        if (empty($fieldData)) {
            return $baseUrl;
        }

        // Validate and clean field data
        $validatedData = $this->validateAndCleanFieldData($fieldData);
        
        if (empty($validatedData)) {
            return $baseUrl;
        }

        return $this->buildUrlWithParameters($baseUrl, $validatedData);
    }

    /**
     * Detect bank-specific service based on field data
     */
    protected function detectBankSpecificService(Service $mainService, array $fieldData): ?Service
    {
        // Only process if this is a main service (parent_id is null)
        if ($mainService->parent_id !== null) {
            return $mainService;
        }

        // Check if this service has bank-specific sub-services
        $hasSubServices = Service::where('parent_id', $mainService->id)->exists();
        if (!$hasSubServices) {
            return $mainService;
        }

        $detectedBankSlug = $this->detectBankFromFieldData($fieldData);
        if (!$detectedBankSlug) {
            return $mainService;
        }

        // Find the bank-specific sub-service
        $bankService = Service::where('parent_id', $mainService->id)
                             ->where('slug', $detectedBankSlug)
                             ->first();

        return $bankService ?: $mainService;
    }

    /**
     * Detect bank from field data (card number, IBAN, etc.)
     */
    protected function detectBankFromFieldData(array $fieldData): ?string
    {
        // Check for card number
        if (isset($fieldData['card_number'])) {
            $bank = $this->detectBankFromCardNumber($fieldData['card_number']);
            if ($bank) {
                return $this->generateBankSlug($bank);
            }
        }

        // Check for IBAN
        if (isset($fieldData['iban'])) {
            $bank = $this->detectBankFromIban($fieldData['iban']);
            if ($bank) {
                return $this->generateBankSlug($bank);
            }
        }

        // Check for explicit bank mention in message or context
        if (isset($fieldData['bank_name'])) {
            $bank = $this->findBankByName($fieldData['bank_name']);
            if ($bank) {
                return $this->generateBankSlug($bank);
            }
        }

        return null;
    }

    /**
     * Detect bank from card number prefix
     */
    protected function detectBankFromCardNumber(string $cardNumber): ?Bank
    {
        $cardNumber = preg_replace('/\D/', '', $cardNumber);
        
        if (strlen($cardNumber) < 6) {
            return null;
        }

        $prefix = substr($cardNumber, 0, 6);
        
        return Bank::where('is_active', true)
                   ->where('card_prefixes', 'LIKE', "%{$prefix}%")
                   ->first();
    }

    /**
     * Detect bank from IBAN
     */
    protected function detectBankFromIban(string $iban): ?Bank
    {
        // Remove IR prefix and spaces
        $iban = preg_replace('/[^0-9]/', '', $iban);
        
        if (strlen($iban) < 24) {
            return null;
        }

        // Bank code is typically in positions 3-5 of the IBAN
        $bankCode = substr($iban, 2, 3);
        
        return Bank::where('is_active', true)
                   ->where('bank_id', $bankCode)
                   ->first();
    }

    /**
     * Find bank by name
     */
    protected function findBankByName(string $bankName): ?Bank
    {
        $bankName = trim(strtolower($bankName));
        
        return Bank::where('is_active', true)
                   ->where(function($query) use ($bankName) {
                       $query->where('name', 'LIKE', "%{$bankName}%")
                             ->orWhere('en_name', 'LIKE', "%{$bankName}%");
                   })
                   ->first();
    }

    /**
     * Generate bank slug for sub-service
     */
    protected function generateBankSlug(Bank $bank): string
    {
        // Use English name if available, otherwise use Persian name with transliteration
        $bankIdentifier = $bank->en_name ?: $this->transliteratePersian($bank->name);
        
        // Convert to slug format
        return Str::slug(strtolower($bankIdentifier));
    }

    /**
     * Simple transliteration for Persian bank names
     */
    protected function transliteratePersian(string $text): string
    {
        $persianToEnglish = [
            'ملی' => 'melli',
            'ملت' => 'mellat',
            'سپه' => 'sepah',
            'پارسیان' => 'parsian',
            'پاسارگاد' => 'pasargad',
            'سامان' => 'saman',
            'کشاورزی' => 'keshavarzi',
            'صادرات' => 'saderat',
            'تجارت' => 'tejarat',
            'رفاه' => 'refah',
            'مسکن' => 'maskan',
            'شهر' => 'shahr',
            'دی' => 'day',
            'پست' => 'post',
            'توسعه' => 'tosee',
            'اقتصاد' => 'eghtesad',
            'نوین' => 'novin',
            'آینده' => 'ayandeh',
            'سینا' => 'sina',
            'کار' => 'kar',
            'آفرین' => 'afarin',
            'ایران' => 'iran',
            'زمین' => 'zamin',
            'قوامین' => 'ghavamin',
            'حکمت' => 'hekmat',
            'گردشگری' => 'gardeshgari',
            'صنعت' => 'sanat',
            'معدن' => 'madan',
            'مرکزی' => 'markazi',
            'رسالت' => 'resalat',
            'انصار' => 'ansar',
            'کوثر' => 'kosar',
            'مهر' => 'mehr',
            'ایرانیان' => 'iranian',
            'تعاون' => 'taavon',
        ];
        
        $result = strtolower($text);
        
        foreach ($persianToEnglish as $persian => $english) {
            $result = str_replace($persian, $english, $result);
        }
        
        // Remove extra spaces and replace with hyphens
        $result = preg_replace('/\s+/', '-', trim($result));
        
        // Remove any remaining Persian characters
        $result = preg_replace('/[^\w\-]/', '', $result);
        
        return $result;
    }

    /**
     * Get service base URL
     */
    protected function getServiceBaseUrl(Service $service): string
    {
        // Use the service's getUrl() method which handles parent-child relationships
        return $this->safeUrlGeneration($service->getUrl());
    }

    /**
     * Safely generate URLs that work in both web and console contexts
     */
    protected function safeUrlGeneration(string $path): string
    {
        try {
            // Try to use URL::to() which requires a request context
            return URL::to($path);
        } catch (\Exception $e) {
            // Fallback for console/queue contexts where no request is available
            $baseUrl = config('app.url', 'http://localhost');
            return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
        }
    }

    /**
     * Validate and clean field data
     */
    protected function validateAndCleanFieldData(array $fieldData): array
    {
        $validatedData = [];

        foreach ($fieldData as $fieldName => $value) {
            $cleanedValue = $this->cleanFieldValue($fieldName, $value);
            
            if ($cleanedValue !== null) {
                $validatedData[$fieldName] = $cleanedValue;
            }
        }

        return $validatedData;
    }

    /**
     * Clean individual field value
     */
    protected function cleanFieldValue(string $fieldName, $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        $value = trim($value);

        switch ($fieldName) {
            case 'card_number':
                // Remove spaces and hyphens from card number
                $cleaned = preg_replace('/[\s\-]/', '', $value);
                return preg_match('/^\d{16}$/', $cleaned) ? $cleaned : null;

            case 'iban':
                // Remove spaces and ensure 24 digits
                $cleaned = preg_replace('/\D/', '', $value);
                return preg_match('/^\d{24}$/', $cleaned) ? $cleaned : null;

            case 'national_code':
                // Ensure 10 digits
                $cleaned = preg_replace('/\D/', '', $value);
                return preg_match('/^\d{10}$/', $cleaned) ? $cleaned : null;

            case 'mobile':
                // Ensure proper mobile format
                $cleaned = preg_replace('/\D/', '', $value);
                return preg_match('/^09\d{9}$/', $cleaned) ? $cleaned : null;

            case 'account_number':
                // Clean account number
                $cleaned = preg_replace('/\D/', '', $value);
                return (strlen($cleaned) >= 8 && strlen($cleaned) <= 20) ? $cleaned : null;

            case 'company_id':
                // Ensure 11 digits
                $cleaned = preg_replace('/\D/', '', $value);
                return preg_match('/^\d{11}$/', $cleaned) ? $cleaned : null;

            case 'customer_type':
                // Validate customer type
                return in_array($value, ['personal', 'corporate']) ? $value : null;

            case 'bank_id':
                // Validate bank ID
                return is_numeric($value) ? (string) $value : null;

            default:
                // For other fields, just return cleaned string
                return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }
    }

    /**
     * Build URL with parameters
     */
    protected function buildUrlWithParameters(string $baseUrl, array $parameters): string
    {
        if (empty($parameters)) {
            return $baseUrl;
        }

        $queryString = http_build_query($parameters);
        $separator = strpos($baseUrl, '?') !== false ? '&' : '?';
        
        return $baseUrl . $separator . $queryString;
    }

    /**
     * Generate service URL from conversation data
     */
    public function generateFromConversation(string $serviceSlug): ?string
    {
        $completedFields = $this->conversationManager->getCompletedFields();
        return $this->generateServiceUrl($serviceSlug, $completedFields);
    }

    /**
     * Generate URL with specific field mapping
     */
    public function generateWithMapping(string $serviceSlug, array $fieldData, array $fieldMapping = []): ?string
    {
        $mappedData = [];

        foreach ($fieldData as $inputField => $value) {
            $targetField = $fieldMapping[$inputField] ?? $inputField;
            $mappedData[$targetField] = $value;
        }

        return $this->generateServiceUrl($serviceSlug, $mappedData);
    }

    /**
     * Generate preview URL (for display purposes)
     */
    public function generatePreviewUrl(string $serviceSlug, array $fieldData = []): array
    {
        $url = $this->generateServiceUrl($serviceSlug, $fieldData);
        
        return [
            'url' => $url,
            'display_url' => $this->createDisplayUrl($url),
            'parameters' => $this->extractParametersFromUrl($url),
            'is_valid' => $url !== null
        ];
    }

    /**
     * Create user-friendly display URL
     */
    protected function createDisplayUrl(?string $url): ?string
    {
        if (!$url) {
            return null;
        }

        $parsedUrl = parse_url($url);
        $displayUrl = $parsedUrl['host'] . $parsedUrl['path'];

        if (isset($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $params);
            $maskedParams = [];

            foreach ($params as $key => $value) {
                $maskedParams[$key] = $this->maskSensitiveValue($key, $value);
            }

            if (!empty($maskedParams)) {
                $displayUrl .= '?' . http_build_query($maskedParams);
            }
        }

        return $displayUrl;
    }

    /**
     * Mask sensitive values for display
     */
    protected function maskSensitiveValue(string $fieldName, string $value): string
    {
        switch ($fieldName) {
            case 'card_number':
                // Show first 4 and last 4 digits
                if (strlen($value) === 16) {
                    return substr($value, 0, 4) . '****' . substr($value, -4);
                }
                break;

            case 'iban':
                // Show first 6 and last 4 digits
                if (strlen($value) === 24) {
                    return substr($value, 0, 6) . '****' . substr($value, -4);
                }
                break;

            case 'national_code':
                // Show first 3 and last 2 digits
                if (strlen($value) === 10) {
                    return substr($value, 0, 3) . '***' . substr($value, -2);
                }
                break;

            case 'mobile':
                // Show first 4 and last 3 digits
                if (strlen($value) === 11) {
                    return substr($value, 0, 4) . '****' . substr($value, -3);
                }
                break;

            case 'account_number':
                // Show first 3 and last 3 digits
                if (strlen($value) >= 6) {
                    return substr($value, 0, 3) . '***' . substr($value, -3);
                }
                break;
        }

        return $value;
    }

    /**
     * Extract parameters from URL
     */
    protected function extractParametersFromUrl(?string $url): array
    {
        if (!$url) {
            return [];
        }

        $parsedUrl = parse_url($url);
        
        if (!isset($parsedUrl['query'])) {
            return [];
        }

        parse_str($parsedUrl['query'], $params);
        return $params;
    }

    /**
     * Validate service URL accessibility
     */
    public function validateServiceUrl(string $url): array
    {
        try {
            $parsedUrl = parse_url($url);
            
            if (!$parsedUrl || !isset($parsedUrl['path'])) {
                return [
                    'valid' => false,
                    'error' => 'Invalid URL format'
                ];
            }

            // Check if URL matches expected service pattern
            if (!preg_match('/\/services\/[\w\-]+/', $parsedUrl['path'])) {
                return [
                    'valid' => false,
                    'error' => 'URL does not match service pattern'
                ];
            }

            return [
                'valid' => true,
                'path' => $parsedUrl['path'],
                'parameters' => $this->extractParametersFromUrl($url)
            ];

        } catch (\Exception $e) {
            return [
                'valid' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Generate multiple service URLs for testing
     */
    public function generateTestUrls(string $serviceSlug, array $testData = []): array
    {
        $urls = [];

        // Generate base URL
        $urls['base'] = $this->generateServiceUrl($serviceSlug);

        // Generate URLs with test data
        if (!empty($testData)) {
            foreach ($testData as $testName => $data) {
                $urls[$testName] = $this->generateServiceUrl($serviceSlug, $data);
            }
        }

        return $urls;
    }

    /**
     * Get service URL statistics
     */
    public function getUrlStatistics(string $serviceSlug): array
    {
        try {
            $service = Service::where('slug', $serviceSlug)->first();
            
            if (!$service) {
                return ['error' => 'Service not found'];
            }

            return [
                'service_slug' => $serviceSlug,
                'service_title' => $service->title,
                'base_url' => $this->getServiceBaseUrl($service),
                'url_pattern' => "/services/{$serviceSlug}",
                'supports_parameters' => true,
                'generated_at' => now()->toISOString()
            ];

        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage()
            ];
        }
    }
} 