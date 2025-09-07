<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class QuickResearchService
{
    protected $geminiService;
    
    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }
    
    /**
     * Generate comprehensive research for a service
     */
    public function generateQuickResearch(string $serviceName, string $description = '', array $context = []): array
    {
        Log::info('Starting quick research generation', [
            'service' => $serviceName,
            'has_description' => !empty($description)
        ]);
        
        // Build comprehensive research prompt
        $researchPrompt = $this->buildResearchPrompt($serviceName, $description, $context);
        
        try {
            $messages = [
                ['role' => 'system', 'content' => 'شما یک متخصص خدمات دولتی و بانکی در ایران هستید. اطلاعات دقیق و کاربردی برای سال ۱۴۰۴ ارائه دهید.'],
                ['role' => 'user', 'content' => $researchPrompt]
            ];
            
            $response = $this->geminiService->chatCompletion(
                $messages,
                'google/gemini-2.5-pro',
                [
                    'online' => true,
                    'temperature' => 0.3,
                    'max_tokens' => 2000
                ]
            );
            
            // Parse the response into structured data
            $researchData = $this->parseResearchResponse($response, $serviceName);
            
            // Calculate token count for metadata
            $contentLength = 0;
            foreach ($researchData as $key => $value) {
                if ($key !== '_metadata') {
                    if (is_array($value)) {
                        $contentLength += strlen(json_encode($value, JSON_UNESCAPED_UNICODE));
                    } else {
                        $contentLength += strlen($value);
                    }
                }
            }
            
            // Add metadata
            $researchData['_metadata'] = [
                'service_name' => $serviceName,
                'generated_at' => now()->toIso8601String(),
                'token_count' => max(100, (int)($contentLength / 3)),
                'content_length' => $contentLength,
                'has_sufficient_data' => $contentLength > 500
            ];
            
            Log::info('Quick research completed successfully', [
                'service' => $serviceName,
                'data_size' => $contentLength,
                'token_count' => $researchData['_metadata']['token_count']
            ]);
            
            return $researchData;
            
        } catch (\Exception $e) {
            Log::error('Quick research failed', [
                'service' => $serviceName,
                'error' => $e->getMessage()
            ]);
            
            // Return fallback data instead of failing completely
            return $this->getFallbackResearchData($serviceName, $description);
        }
    }
    
    /**
     * Build research prompt in Persian
     */
    private function buildResearchPrompt(string $serviceName, string $description, array $context): string
    {
        $shortTitle = $context['short_title'] ?? '';
        
        return "
🔍 تحقیق جامع درباره سرویس: {$serviceName}

📝 اطلاعات موجود:
- عنوان کامل: {$serviceName}
- عنوان کوتاه: {$shortTitle}
- توضیحات: {$description}

🎯 لطفاً برای این سرویس، اطلاعات زیر را به صورت دقیق و کاربردی برای سال ۱۴۰۴ ارائه دهید:

## ۱. هدف اصلی سرویس (service_purpose)
هدف و کاربرد اصلی این سرویس چیست؟

## ۲. توضیح تفصیلی (detailed_description)
شرح کامل نحوه عملکرد و کاربرد این سرویس در ایران

## ۳. ویژگی‌های کلیدی (key_features)
مهم‌ترین ویژگی‌های این سرویس (حداقل ۳ ویژگی)

## ۴. مخاطبان هدف (target_audience)
چه کسانی از این سرویس استفاده می‌کنند؟

## ۵. مراحل انجام کار (process_steps)
گام‌های لازم برای استفاده از این سرویس

## ۶. مزایا (benefits)
مزایای استفاده از این سرویس (حداقل ۳ مزیت)

## ۷. محدودیت‌ها (limitations)
محدودیت‌ها و نکات مهم در استفاده

## ۸. سوالات متداول (common_questions)
سوالاتی که کاربران معمولاً می‌پرسند

⚠️ مهم: تمام اطلاعات باید برای سال ۱۴۰۴ و مطابق قوانین جاری ایران باشد.
";
    }
    
    /**
     * Parse research response into structured format
     */
    private function parseResearchResponse(string $response, string $serviceName): array
    {
        $sections = [
            'service_purpose' => '',
            'detailed_description' => '',
            'key_features' => [],
            'target_audience' => '',
            'process_steps' => [],
            'benefits' => [],
            'limitations' => [],
            'common_questions' => []
        ];
        
        // Split by sections and parse
        $lines = explode("\n", $response);
        $currentSection = null;
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            // Detect section headers
            if (strpos($line, '## ۱.') !== false || strpos($line, 'service_purpose') !== false) {
                $currentSection = 'service_purpose';
            } elseif (strpos($line, '## ۲.') !== false || strpos($line, 'detailed_description') !== false) {
                $currentSection = 'detailed_description';
            } elseif (strpos($line, '## ۳.') !== false || strpos($line, 'key_features') !== false) {
                $currentSection = 'key_features';
            } elseif (strpos($line, '## ۴.') !== false || strpos($line, 'target_audience') !== false) {
                $currentSection = 'target_audience';
            } elseif (strpos($line, '## ۵.') !== false || strpos($line, 'process_steps') !== false) {
                $currentSection = 'process_steps';
            } elseif (strpos($line, '## ۶.') !== false || strpos($line, 'benefits') !== false) {
                $currentSection = 'benefits';
            } elseif (strpos($line, '## ۷.') !== false || strpos($line, 'limitations') !== false) {
                $currentSection = 'limitations';
            } elseif (strpos($line, '## ۸.') !== false || strpos($line, 'common_questions') !== false) {
                $currentSection = 'common_questions';
            }
            // Skip section headers
            elseif (strpos($line, '##') === 0 || strpos($line, '#') === 0) {
                continue;
            }
            // Add content to current section
            elseif ($currentSection) {
                if (in_array($currentSection, ['key_features', 'process_steps', 'benefits', 'limitations', 'common_questions'])) {
                    // List items
                    if (strpos($line, '-') === 0 || strpos($line, '•') === 0 || strpos($line, '*') === 0) {
                        $sections[$currentSection][] = trim(ltrim($line, '- •*'));
                    } elseif (!empty($line)) {
                        $sections[$currentSection][] = $line;
                    }
                } else {
                    // Text sections
                    if (!empty($sections[$currentSection])) {
                        $sections[$currentSection] .= " " . $line;
                    } else {
                        $sections[$currentSection] = $line;
                    }
                }
            }
        }
        
        return $sections;
    }
    
    /**
     * Fallback research data when API fails
     */
    private function getFallbackResearchData(string $serviceName, string $description): array
    {
        return [
            'service_purpose' => "سرویس {$serviceName} برای تسهیل و تسریع در ارائه خدمات طراحی شده است.",
            'detailed_description' => $description ?: "این سرویس امکان دسترسی آسان و سریع به اطلاعات مورد نیاز را فراهم می‌کند.",
            'key_features' => [
                'دسترسی آنلاین ۲۴ ساعته',
                'سرعت بالا در ارائه نتایج',
                'امنیت اطلاعات کاربران'
            ],
            'target_audience' => 'کلیه شهروندان ایرانی نیازمند این خدمت',
            'process_steps' => [
                'ورود به سامانه پیشخوانک',
                'انتخاب سرویس مورد نظر',
                'وارد کردن اطلاعات لازم',
                'پرداخت هزینه',
                'دریافت نتیجه'
            ],
            'benefits' => [
                'صرفه‌جویی در زمان',
                'کاهش هزینه‌های جانبی',
                'دقت بالا در نتایج'
            ],
            'limitations' => [
                'نیاز به اتصال اینترنت',
                'محدودیت در ساعات کاری برخی ارگان‌ها'
            ],
            'common_questions' => [
                'آیا این سرویس قابل اعتماد است؟',
                'چگونه می‌توانم نتیجه را دریافت کنم؟',
                'آیا اطلاعات من محفوظ است؟'
            ],
            '_metadata' => [
                'service_name' => $serviceName,
                'generated_at' => now()->toIso8601String(),
                'token_count' => 500,
                'content_length' => 1200,
                'has_sufficient_data' => true,
                'is_fallback' => true
            ]
        ];
    }
}