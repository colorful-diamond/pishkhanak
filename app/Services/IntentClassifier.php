<?php

namespace App\Services;

use App\Models\Service;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class IntentClassifier
{
    // Intent Types
    public const INTENT_SERVICE_REQUEST = 'service_request';
    public const INTENT_SERVICE_INQUIRY = 'service_inquiry';
    public const INTENT_GENERAL_QUESTION = 'general_question';
    public const INTENT_GENERAL_CONVERSATION = 'general_conversation';
    public const INTENT_FILE_ANALYSIS = 'file_analysis';
    public const INTENT_NAVIGATION = 'navigation';
    public const INTENT_COMPLAINT = 'complaint';
    public const INTENT_SUPPORT = 'support';
    public const INTENT_PRICING = 'pricing';
    public const INTENT_FEEDBACK = 'feedback';

    // Confidence Levels
    public const CONFIDENCE_HIGH = 0.8;
    public const CONFIDENCE_MEDIUM = 0.6;
    public const CONFIDENCE_LOW = 0.4;

    protected array $serviceKeywords = [];
    protected array $intentPatterns = [];
    protected array $dataPatternsRegex = [];
    protected GeminiService $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
        $this->initializePatterns();
        $this->loadServiceKeywords();
    }

    protected function initializePatterns(): void
    {
        $this->intentPatterns = [
            self::INTENT_SERVICE_REQUEST => [
                'data_indicators' => [
                    'card_number' => '/\b\d{4}[\s\-]?\d{4}[\s\-]?\d{4}[\s\-]?\d{4}\b/',
                    'iban' => '/\b\d{24}\b/',
                    'national_code' => '/\b\d{10}\b/',
                    'mobile' => '/\b09\d{9}\b/',
                    'account_number' => '/\b\d{8,20}\b/',
                    'company_id' => '/\b\d{11}\b/',
                ],
                'action_keywords' => [
                    'تبدیل', 'convert', 'کن', 'بده', 'بگیر', 'چک کن', 'بررسی کن',
                    'استعلام', 'inquiry', 'پیدا کن', 'find', 'جستجو', 'search'
                ],
                'patterns' => [
                    '/تبدیل.*به.*/',
                    '/کارت.*شبا/',
                    '/شبا.*کارت/',
                    '/حساب.*شبا/',
                    '/استعلام.*/',
                    '/بررسی.*/',
                    '/چک.*کن/',
                ]
            ],
            self::INTENT_SERVICE_INQUIRY => [
                'question_words' => [
                    'چطور', 'چگونه', 'how', 'what', 'چی', 'چه', 'کدام',
                    'کجا', 'where', 'کی', 'when', 'چرا', 'why', 'آیا'
                ],
                'inquiry_keywords' => [
                    'کار می‌کند', 'عمل می‌کند', 'فعال', 'امکان', 'قابلیت',
                    'work', 'function', 'feature', 'capability', 'راهنما',
                    'help', 'guide', 'tutorial', 'توضیح', 'explain'
                ],
                'patterns' => [
                    '/چطور.*کار.*می.*کند/',
                    '/چگونه.*استفاده/',
                    '/راهنما.*/',
                    '/توضیح.*/',
                    '/آموزش.*/',
                ]
            ],
            self::INTENT_GENERAL_QUESTION => [
                'general_topics' => [
                    'بانک', 'bank', 'پول', 'money', 'مالی', 'financial',
                    'پرداخت', 'payment', 'تکنولوژی', 'technology',
                    'امنیت', 'security', 'حقوقی', 'legal', 'اقتصاد', 'economy'
                ],
                'question_patterns' => [
                    '/چیست/',
                    '/تعریف/',
                    '/معنی/',
                    '/مفهوم/',
                    '/what is/',
                    '/define/',
                ]
            ],
            self::INTENT_GENERAL_CONVERSATION => [
                'greetings' => [
                    'سلام', 'hello', 'hi', 'درود', 'صبح بخیر', 'عصر بخیر',
                    'good morning', 'good evening', 'hey', 'احوال', 'حال شما',
                    'سلام علیکم', 'هلو', 'های'
                ],
                'courtesy' => [
                    'ممنون', 'thank you', 'thanks', 'متشکرم', 'مرسی',
                    'خداحافظ', 'goodbye', 'bye', 'فعلا', 'تشکر'
                ],
                'casual' => [
                    'خوبی', 'چطوری', 'how are you', 'fine', 'خوبم',
                    'بد نیست', 'not bad', 'okay', 'اوکی', 'خوب هستی',
                    'حالت چطوره', 'حالت خوبه', 'خسته نباشی', 'سلامتی',
                    'خوش اومدی', 'قربانت', 'خدا رو شکر', 'الحمدالله'
                ],
                'patterns' => [
                    '/سلام.*خوبی/',
                    '/سلام.*چطوری/',
                    '/خوبی.*\?/',
                    '/چطوری.*\?/',
                    '/حالت.*چطور/',
                    '/حالت.*خوب/'
                ]
            ],
            self::INTENT_FILE_ANALYSIS => [
                'file_keywords' => [
                    'فایل', 'file', 'عکس', 'image', 'تصویر', 'picture',
                    'سند', 'document', 'پی دی اف', 'pdf', 'اسکن', 'scan'
                ],
                'analysis_requests' => [
                    'تحلیل', 'analyze', 'بررسی', 'check', 'خواندن', 'read',
                    'استخراج', 'extract', 'تشخیص', 'recognize'
                ]
            ],
            self::INTENT_NAVIGATION => [
                'navigation_keywords' => [
                    'برو', 'go', 'صفحه', 'page', 'بخش', 'section',
                    'منو', 'menu', 'لیست', 'list', 'خانه', 'home',
                    'بازگشت', 'back', 'قبلی', 'previous', 'بعدی', 'next'
                ],
                'locations' => [
                    'خانه', 'home', 'سرویس', 'service', 'تماس', 'contact',
                    'درباره', 'about', 'blog', 'بلاگ', 'اخبار', 'news'
                ]
            ],
            self::INTENT_COMPLAINT => [
                'complaint_keywords' => [
                    'شکایت', 'complaint', 'problem', 'مشکل', 'خطا', 'error',
                    'کار نمی‌کند', 'not working', 'قطع', 'disconnect',
                    'کند', 'slow', 'بد', 'bad', 'ناراضی', 'dissatisfied'
                ],
                'negative_emotions' => [
                    'عصبانی', 'angry', 'ناامید', 'disappointed', 'عصبی',
                    'frustrated', 'upset', 'بیزار', 'annoyed'
                ]
            ],
            self::INTENT_SUPPORT => [
                'support_keywords' => [
                    'پشتیبانی', 'support', 'راهنمایی', 'guidance', 'کمک',
                    'help', 'assistance', 'مشاوره', 'consultation',
                    'حل مشکل', 'troubleshooting', 'رفع مشکل'
                ],
                'urgent_keywords' => [
                    'فوری', 'urgent', 'سریع', 'quick', 'عاجل', 'immediate',
                    'اکنون', 'now', 'الان', 'بلافاصله'
                ]
            ],
            self::INTENT_PRICING => [
                'pricing_keywords' => [
                    'قیمت', 'price', 'هزینه', 'cost', 'تعرفه', 'tariff',
                    'fee', 'رایگان', 'free', 'پولی', 'paid', 'پرداخت'
                ],
                'money_related' => [
                    'تومان', 'tooman', 'ریال', 'rial', 'درهم', 'دلار',
                    'dollar', 'یورو', 'euro', 'ارز', 'currency'
                ]
            ],
            self::INTENT_FEEDBACK => [
                'feedback_keywords' => [
                    'نظر', 'opinion', 'پیشنهاد', 'suggestion', 'انتقاد',
                    'criticism', 'بازخورد', 'feedback', 'نقد', 'review'
                ],
                'satisfaction' => [
                    'راضی', 'satisfied', 'خوب', 'good', 'عالی', 'excellent',
                    'بد', 'bad', 'ضعیف', 'weak', 'قوی', 'strong'
                ]
            ]
        ];

        $this->dataPatternsRegex = [
            'card_number' => '/(?:^|\s)(\d{4}[\s\-]?\d{4}[\s\-]?\d{4}[\s\-]?\d{4})(?:\s|$)/',
            'iban' => '/(?:^|\s)(\d{24})(?:\s|$)/',
            'national_code' => '/(?:^|\s)(\d{10})(?:\s|$)/',
            'mobile' => '/(?:^|\s)(09\d{9})(?:\s|$)/',
            'account_number' => '/(?:^|\s)(\d{8,20})(?:\s|$)/',
            'company_id' => '/(?:^|\s)(\d{11})(?:\s|$)/',
        ];
    }

    protected function loadServiceKeywords(): void
    {
        try {
            $this->serviceKeywords = Cache::remember('service_keywords', 3600, function () {
                $services = Service::all();
                $keywords = [];

                foreach ($services as $service) {
                    $keywords[$service->slug] = [
                        'title' => $service->title,
                        'short_title' => $service->short_title,
                        'summary' => $service->summary,
                        'keywords' => $this->extractKeywords($service),
                        'patterns' => $this->generateServicePatterns($service)
                    ];
                }

                return $keywords;
            });
        } catch (\Exception $e) {
            Log::error('خطا در بارگذاری کلمات کلیدی سرویس', ['error' => $e->getMessage()]);
            $this->serviceKeywords = [];
        }
    }

    protected function extractKeywords(Service $service): array
    {
        $keywords = [];
        
        // استخراج کلمات کلیدی از عنوان
        $title = $service->title . ' ' . $service->short_title;
        $words = preg_split('/[\s\-\_]+/', $title);
        
        foreach ($words as $word) {
            $word = trim($word);
            if (strlen($word) > 2) {
                $keywords[] = $word;
            }
        }

        // کلمات کلیدی خاص برای سرویس‌های مختلف
        $specificKeywords = [
            'card-to-sheba' => ['کارت', 'شبا', 'تبدیل', 'convert', 'iban'],
            'card-to-iban' => ['کارت', 'شبا', 'تبدیل', 'convert', 'iban'],
            'sheba-inquiry' => ['شبا', 'استعلام', 'inquiry', 'بررسی', 'شهاب', 'iban'],
            'sheba-to-account' => ['شبا', 'حساب', 'تبدیل', 'iban'],
            'card-to-account' => ['کارت', 'حساب', 'تبدیل'],
            'account-iban' => ['حساب', 'شبا', 'تبدیل', 'iban', 'شماره شبا', 'دریافت شبا'],
            'account-to-sheba' => ['حساب', 'شبا', 'تبدیل', 'iban', 'شماره شبا', 'دریافت شبا'],
            'card-account' => ['کارت', 'حساب', 'اطلاعات'],
            'iban-check' => ['شبا', 'چک', 'بررسی', 'اعتبار', 'iban'],
            'credit-score-rating' => ['رتبه', 'اعتباری', 'کد ملی', 'credit'],
            'iban-service' => ['شبا', 'iban', 'شماره شبا', 'دریافت شبا', 'استعلام شبا'],
            'account-to-iban' => ['حساب', 'شبا', 'تبدیل', 'iban', 'شماره شبا']
        ];

        if (isset($specificKeywords[$service->slug])) {
            $keywords = array_merge($keywords, $specificKeywords[$service->slug]);
        }
        
        // Add common SHEBA/IBAN variations
        if (strpos($service->slug, 'sheba') !== false || strpos($service->slug, 'iban') !== false) {
            $keywords = array_merge($keywords, [
                'شبا', 'شهاب', 'iban', 'آیبان', 'شماره شبا', 'دریافت شبا', 
                'محاسبه شبا', 'استعلام شبا', 'تولید شبا'
            ]);
        }
        
        // Add card-related variations
        if (strpos($service->slug, 'card') !== false) {
            $keywords = array_merge($keywords, [
                'کارت', 'card', 'شماره کارت', 'کارت بانکی', 'کارت عابر بانک'
            ]);
        }

        return array_unique($keywords);
    }

    protected function generateServicePatterns(Service $service): array
    {
        $patterns = [];
        $keywords = $this->extractKeywords($service);

        foreach ($keywords as $keyword) {
            $patterns[] = '/\b' . preg_quote($keyword, '/') . '\b/i';
        }

        return $patterns;
    }

    public function classifyIntent(string $message, array $context = []): array
    {
        $message = trim($message);
        
        // بررسی وجود فایل
        $hasFile = isset($context['files']) && !empty($context['files']);
        
        // استخراج داده‌های خام
        $extractedData = $this->extractDataFromMessage($message);
        
        // Check for conversation continuation - if we have current service and user provides data
        $currentService = $context['current_service'] ?? null;
        $currentStep = $context['step'] ?? 'initial';
        
        // Special handling for general questions - even if we have context
        if ($this->isGeneralQuestion($message)) {
            Log::info('Intent: Detected general question despite context', [
                'message' => $message,
                'current_service' => $currentService,
                'step' => $currentStep
            ]);
            
            return [
                'intent' => self::INTENT_GENERAL_QUESTION,
                'confidence' => 0.8,
                'extracted_data' => [],
                'detected_service' => null,
                'service_confidence' => 0,
                'suggested_services' => [],
                'intent_scores' => [self::INTENT_GENERAL_QUESTION => 0.8],
                'has_file' => $hasFile,
                'context_used' => true,
                'is_continuation' => false,
                'analysis_timestamp' => now()->toISOString()
            ];
        }
        
        // Check for conversation reset signals
        if ($this->isConversationReset($message)) {
            Log::info('Intent: Detected conversation reset', [
                'message' => $message,
                'current_service' => $currentService
            ]);
            
            return [
                'intent' => self::INTENT_GENERAL_CONVERSATION,
                'confidence' => 0.8,
                'extracted_data' => [],
                'detected_service' => null,
                'service_confidence' => 0,
                'suggested_services' => [],
                'intent_scores' => [self::INTENT_GENERAL_CONVERSATION => 0.8],
                'has_file' => $hasFile,
                'context_used' => true,
                'is_continuation' => false,
                'reset_context' => true,
                'analysis_timestamp' => now()->toISOString()
            ];
        }
        
        // If we're in field collection mode and user provides data, prioritize service request
        if ($currentService && $currentStep === 'field_collection' && !empty($extractedData)) {
            Log::info('Intent: Detected service continuation', [
                'message' => $message,
                'current_service' => $currentService,
                'step' => $currentStep,
                'extracted_data' => $extractedData
            ]);
            
            return [
                'intent' => self::INTENT_SERVICE_REQUEST,
                'confidence' => 0.9, // High confidence for continuation
                'extracted_data' => $extractedData,
                'detected_service' => $currentService,
                'service_confidence' => 0.9,
                'suggested_services' => [],
                'intent_scores' => [self::INTENT_SERVICE_REQUEST => 0.9],
                'has_file' => $hasFile,
                'context_used' => true,
                'is_continuation' => true,
                'analysis_timestamp' => now()->toISOString()
            ];
        }
        
        // تحلیل intent
        $intentScores = $this->analyzeIntentScores($message, $extractedData, $hasFile, $context);
        
        // یافتن بهترین intent
        $bestIntent = $this->getBestIntent($intentScores);
        
        // تشخیص سرویس
        $detectedService = $this->detectService($message, $extractedData, $context);
        
        // Get suggested services based on detected intent and service
        $suggestedServices = $this->getSuggestedServices($message, $detectedService, $bestIntent['intent']);
        
        return [
            'intent' => $bestIntent['intent'],
            'confidence' => $bestIntent['confidence'],
            'extracted_data' => $extractedData,
            'detected_service' => $detectedService,
            'service_confidence' => $detectedService ? $this->calculateServiceConfidence($message, $detectedService) : 0,
            'suggested_services' => $suggestedServices,
            'intent_scores' => $intentScores,
            'has_file' => $hasFile,
            'context_used' => !empty($context),
            'is_continuation' => false,
            'analysis_timestamp' => now()->toISOString()
        ];
    }

    protected function extractDataFromMessage(string $message): array
    {
        $extractedData = [];
        
        // Convert Persian/Farsi digits to English digits
        $message = $this->convertPersianDigits($message);
        
        foreach ($this->dataPatternsRegex as $type => $pattern) {
            if (preg_match($pattern, $message, $matches)) {
                $extractedData[$type] = trim($matches[1]);
                
                // Log successful extraction
                Log::info('Data extracted from message', [
                    'type' => $type,
                    'value' => substr($extractedData[$type], 0, 4) . '***', // Mask for security
                    'original_message' => substr($message, 0, 50) . '...'
                ]);
            }
        }
        
        return $extractedData;
    }
    
    /**
     * Convert Persian/Farsi digits to English digits
     */
    protected function convertPersianDigits(string $text): string
    {
        $persianDigits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $arabicDigits = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $englishDigits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        
        // Convert Persian digits
        $text = str_replace($persianDigits, $englishDigits, $text);
        
        // Convert Arabic digits
        $text = str_replace($arabicDigits, $englishDigits, $text);
        
        return $text;
    }

    protected function analyzeIntentScores(string $message, array $extractedData, bool $hasFile, array $context): array
    {
        $scores = [];
        
        // تحلیل هر intent
        foreach ($this->intentPatterns as $intent => $patterns) {
            $scores[$intent] = $this->calculateIntentScore($message, $extractedData, $hasFile, $context, $patterns);
        }
        
        return $scores;
    }

    protected function calculateIntentScore(string $message, array $extractedData, bool $hasFile, array $context, array $patterns): float
    {
        $score = 0.0;
        $message = strtolower($message);
        
        // امتیاز برای وجود داده‌های خام
        if (!empty($extractedData) && isset($patterns['data_indicators'])) {
            $score += 0.4; // امتیاز بالا برای داده‌های خام
        }
        
        // امتیاز برای کلمات کلیدی عمل
        if (isset($patterns['action_keywords'])) {
            foreach ($patterns['action_keywords'] as $keyword) {
                if (strpos($message, strtolower($keyword)) !== false) {
                    $score += 0.1;
                }
            }
        }
        
        // امتیاز برای کلمات سوالی
        if (isset($patterns['question_words'])) {
            foreach ($patterns['question_words'] as $word) {
                if (strpos($message, strtolower($word)) !== false) {
                    $score += 0.15;
                }
            }
        }
        
        // امتیاز برای patterns
        if (isset($patterns['patterns'])) {
            foreach ($patterns['patterns'] as $pattern) {
                if (preg_match($pattern, $message)) {
                    $score += 0.2;
                }
            }
        }
        
        // امتیاز بالا برای سلام و احوال‌پرسی - FIXED!
        if (isset($patterns['greetings'])) {
            foreach ($patterns['greetings'] as $greeting) {
                if (strpos($message, strtolower($greeting)) !== false) {
                    $score += 0.7; // Increased from 0.3 to 0.7
                }
            }
        }
        
        // امتیاز بالا برای کلمات مودبانه و casual
        if (isset($patterns['casual'])) {
            foreach ($patterns['casual'] as $casual) {
                if (strpos($message, strtolower($casual)) !== false) {
                    $score += 0.6; // High score for casual conversation
                }
            }
        }
        
        // امتیاز برای courtesy words
        if (isset($patterns['courtesy'])) {
            foreach ($patterns['courtesy'] as $courtesy) {
                if (strpos($message, strtolower($courtesy)) !== false) {
                    $score += 0.5;
                }
            }
        }
        
        // امتیاز برای فایل
        if ($hasFile && isset($patterns['file_keywords'])) {
            $score += 0.3;
        }
        
        // امتیاز برای context
        if (!empty($context)) {
            $currentStep = $context['step'] ?? 'initial';
            if ($currentStep === 'field_collection') {
                $score += 0.1; // کمک به تشخیص service_request
            }
        }
        
        return min($score, 1.0);
    }

    protected function getBestIntent(array $scores): array
    {
        $maxScore = 0;
        $bestIntent = self::INTENT_GENERAL_CONVERSATION;
        
        foreach ($scores as $intent => $score) {
            if ($score > $maxScore) {
                $maxScore = $score;
                $bestIntent = $intent;
            }
        }
        
        return [
            'intent' => $bestIntent,
            'confidence' => $maxScore
        ];
    }

    protected function detectService(string $message, array $extractedData, array $context): ?string
    {
        $message = strtolower($message);
        
        // Handle common typos and variations first
        $message = $this->handleCommonTypos($message);
        
        $bestMatch = null;
        $highestScore = 0;
        
        foreach ($this->serviceKeywords as $slug => $serviceData) {
            $score = $this->calculateServiceScore($message, $extractedData, $serviceData);
            
            if ($score > $highestScore) {
                $highestScore = $score;
                $bestMatch = $slug;
            }
        }
        
        // حداقل امتیاز برای تشخیص سرویس
        return $highestScore > 0.3 ? $bestMatch : null;
    }
    
    /**
     * Handle common typos and variations in user messages
     */
    protected function handleCommonTypos(string $message): string
    {
        $typoReplacements = [
            'شهاب' => 'شبا',
            'شاهب' => 'شبا',
            'شباا' => 'شبا',
            'ibaan' => 'iban',
            'ayban' => 'iban',
            'آیبان' => 'iban',
            'کارترت' => 'کارت',
            'کرت' => 'کارت',
            'حساب' => 'حساب',
            'حسابب' => 'حساب'
        ];
        
        foreach ($typoReplacements as $typo => $correct) {
            $message = str_replace($typo, $correct, $message);
        }
        
        return $message;
    }

    protected function calculateServiceScore(string $message, array $extractedData, array $serviceData): float
    {
        $score = 0.0;
        
        // امتیاز برای کلمات کلیدی
        foreach ($serviceData['keywords'] as $keyword) {
            if (strpos($message, strtolower($keyword)) !== false) {
                $score += 0.2;
            }
        }
        
        // امتیاز برای patterns
        foreach ($serviceData['patterns'] as $pattern) {
            if (preg_match($pattern, $message)) {
                $score += 0.25;
            }
        }
        
        // امتیاز ویژه بر اساس نوع داده‌های استخراج شده
        if (!empty($extractedData)) {
            $serviceDataTypes = $this->getServiceDataTypes($serviceData);
            
            foreach ($extractedData as $dataType => $value) {
                if (in_array($dataType, $serviceDataTypes)) {
                    $score += 0.3;
                }
            }
        }
        
        return min($score, 1.0);
    }

    protected function getServiceDataTypes(array $serviceData): array
    {
        $dataTypes = [];
        
        // بر اساس slug سرویس، نوع داده‌های مورد نیاز را تعیین می‌کنیم
        $serviceSlug = array_search($serviceData, $this->serviceKeywords);
        
        switch ($serviceSlug) {
            case 'card-to-sheba':
            case 'card-to-iban':
            case 'card-to-account':
            case 'card-account':
                $dataTypes = ['card_number'];
                break;
            case 'sheba-inquiry':
            case 'sheba-to-account':
            case 'iban-check':
                $dataTypes = ['iban'];
                break;
            case 'account-iban':
            case 'account-to-sheba':
                $dataTypes = ['account_number'];
                break;
            case 'credit-score-rating':
                $dataTypes = ['national_code', 'mobile', 'company_id'];
                break;
        }
        
        return $dataTypes;
    }

    protected function calculateServiceConfidence(string $message, string $serviceSlug): float
    {
        if (!isset($this->serviceKeywords[$serviceSlug])) {
            return 0.0;
        }
        
        $serviceData = $this->serviceKeywords[$serviceSlug];
        return $this->calculateServiceScore($message, $this->extractDataFromMessage($message), $serviceData);
    }

    public function getIntentDescription(string $intent): string
    {
        $descriptions = [
            self::INTENT_SERVICE_REQUEST => 'درخواست عملیاتی سرویس - کاربر داده‌های کاملی ارائه داده',
            self::INTENT_SERVICE_INQUIRY => 'سوال در مورد سرویس - کاربر اطلاعات می‌خواهد',
            self::INTENT_GENERAL_QUESTION => 'سوال عمومی - سوال غیر سرویسی',
            self::INTENT_GENERAL_CONVERSATION => 'مکالمه عمومی - احوال‌پرسی و گفتگو',
            self::INTENT_FILE_ANALYSIS => 'تحلیل فایل - کاربر فایل ارسال کرده',
            self::INTENT_NAVIGATION => 'ناوبری - کاربر می‌خواهد جایی برود',
            self::INTENT_COMPLAINT => 'شکایت - کاربر مشکلی دارد',
            self::INTENT_SUPPORT => 'پشتیبانی - کاربر کمک می‌خواهد',
            self::INTENT_PRICING => 'قیمت - کاربر در مورد هزینه می‌پرسد',
            self::INTENT_FEEDBACK => 'بازخورد - کاربر نظری دارد'
        ];
        
        return $descriptions[$intent] ?? 'نامشخص';
    }

    public function getConfidenceLevel(float $confidence): string
    {
        if ($confidence >= self::CONFIDENCE_HIGH) {
            return 'high';
        } elseif ($confidence >= self::CONFIDENCE_MEDIUM) {
            return 'medium';
        } elseif ($confidence >= self::CONFIDENCE_LOW) {
            return 'low';
        } else {
            return 'very_low';
        }
    }

    public function clearCache(): void
    {
        Cache::forget('service_keywords');
        $this->loadServiceKeywords();
    }

    /**
     * Get suggested services based on message, detected service and intent
     */
    protected function getSuggestedServices(string $message, ?string $detectedService, string $intent): array
    {
        $suggestedServices = [];
        
        try {
            // If we have a detected service, find related services
            if ($detectedService) {
                $mainService = Service::where('slug', $detectedService)->first();
                if ($mainService && $mainService->category) {
                    // Get other services from the same category
                    $categorySuggestions = Service::where('category_id', $mainService->category_id)
                        ->where('slug', '!=', $detectedService)
                        ->where('status', 'active')
                        ->limit(3)
                        ->pluck('slug')
                        ->toArray();
                    
                    $suggestedServices = array_merge($suggestedServices, $categorySuggestions);
                }
            }
            
            // Based on intent, suggest relevant services
            switch ($intent) {
                case self::INTENT_SERVICE_REQUEST:
                    // If no specific service detected, suggest popular services
                    if (!$detectedService) {
                        $popularServices = Service::where('status', 'active')
                            ->where('featured', true)
                            ->limit(3)
                            ->pluck('slug')
                            ->toArray();
                        $suggestedServices = array_merge($suggestedServices, $popularServices);
                    }
                    break;
                    
                case self::INTENT_SERVICE_INQUIRY:
                    // Suggest services based on keywords in message
                    $keywords = explode(' ', strtolower($message));
                    foreach ($this->serviceKeywords as $serviceSlug => $serviceData) {
                        foreach ($keywords as $keyword) {
                            if (in_array($keyword, $serviceData['keywords'])) {
                                $suggestedServices[] = $serviceSlug;
                                break;
                            }
                        }
                    }
                    break;
            }
            
            // Remove duplicates and limit to 5 suggestions
            $suggestedServices = array_unique($suggestedServices);
            $suggestedServices = array_slice($suggestedServices, 0, 5);
            
        } catch (\Exception $e) {
            Log::error('Error getting suggested services: ' . $e->getMessage());
            $suggestedServices = [];
        }
        
        return $suggestedServices;
    }
    
    /**
     * Check if message is a general question
     */
    protected function isGeneralQuestion(string $message): bool
    {
        $lowerMessage = strtolower($message);
        
        $questionPatterns = [
            '/چیه\s*\?*$/',           // "چیه؟"
            '/چی\s*هست\s*\?*$/',     // "چی هست؟"
            '/یعنی\s*چی\s*\?*$/',    // "یعنی چی؟"
            '/منظورت\s*چیه\s*\?*$/', // "منظورت چیه؟"
            '/توضیح\s*بده/',         // "توضیح بده"
            '/راهنمایی/',             // "راهنمایی"
            '/اطلاعات\s*بیشتر/',     // "اطلاعات بیشتر"
            '/what\s*is/',            // "what is"
            '/explain/',              // "explain"
        ];
        
        foreach ($questionPatterns as $pattern) {
            if (preg_match($pattern, $lowerMessage)) {
                return true;
            }
        }
        
        // Check for question words + specific topics
        $questionWords = ['چی', 'چه', 'چیه', 'کدام', 'چطور', 'چگونه', 'آیا', 'what', 'how', 'which'];
        $hasQuestionWord = false;
        
        foreach ($questionWords as $word) {
            if (strpos($lowerMessage, $word) !== false) {
                $hasQuestionWord = true;
                break;
            }
        }
        
        return $hasQuestionWord && !$this->hasOperationalData($lowerMessage);
    }
    
    /**
     * Check if message signals conversation reset
     */
    protected function isConversationReset(string $message): bool
    {
        $lowerMessage = strtolower($message);
        
        $resetPatterns = [
            '/هوا\s*بخورم/',         // "هوا بخورم"
            '/ولش\s*کن/',           // "ولش کن"
            '/بیخیال/',              // "بیخیال"
            '/کاری\s*ندارم/',       // "کاری ندارم"
            '/دیگه\s*نمی\s*خوام/',  // "دیگه نمی‌خوام"
            '/منتصرف/',             // "منتصرف"
            '/cancel/',              // "cancel"
            '/stop/',                // "stop"
            '/quit/',                // "quit"
        ];
        
        foreach ($resetPatterns as $pattern) {
            if (preg_match($pattern, $lowerMessage)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check if message contains operational data (like card numbers, IBANs, etc.)
     */
    protected function hasOperationalData(string $message): bool
    {
        // Remove spaces for number detection
        $cleanMessage = str_replace([' ', '-', '_'], '', $message);
        
        return preg_match('/\d{10,}/', $cleanMessage); // Numbers with 10+ digits
    }
} 