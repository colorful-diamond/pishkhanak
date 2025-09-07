<?php

namespace App\Services;

use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Post;
use App\Models\AiSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Exception;

class AiChatService
{
    protected GeminiService $geminiService;
    protected ServiceFormAnalyzer $formAnalyzer;
    protected ConversationManager $conversationManager;
    protected IntentClassifier $intentClassifier;
    protected SmartValidator $smartValidator;
    protected ServiceUrlGenerator $urlGenerator;
    protected ?AiSetting $aiSetting;
    
    // Rate limiting constants
    private const RATE_LIMIT_PER_USER_HOURLY = 100;
    private const RATE_LIMIT_PER_IP_HOURLY = 50;
    private const RATE_LIMIT_PER_USER_DAILY = 500;
    private const RATE_LIMIT_ANONYMOUS_DAILY = 200;
    
    // File upload constants
    private const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10MB
    private const ALLOWED_IMAGE_TYPES = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    private const ALLOWED_DOCUMENT_TYPES = ['application/pdf', 'text/plain', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    
    // Cache TTL
    private const CACHE_TTL_SERVICES = 3600; // 1 hour
    private const CACHE_TTL_CONVERSATIONS = 7200; // 2 hours
    
    public function __construct(
        GeminiService $geminiService,
        ServiceFormAnalyzer $formAnalyzer,
        ConversationManager $conversationManager,
        IntentClassifier $intentClassifier,
        SmartValidator $smartValidator,
        ServiceUrlGenerator $urlGenerator
    ) {
        $this->geminiService = $geminiService;
        $this->formAnalyzer = $formAnalyzer;
        $this->conversationManager = $conversationManager;
        $this->intentClassifier = $intentClassifier;
        $this->smartValidator = $smartValidator;
        $this->urlGenerator = $urlGenerator;
        $this->aiSetting = AiSetting::where('is_active', true)->first();
    }
    
    /**
     * Main chat method that handles all types of conversations
     */
    public function chat(string $message, array $options = []): array
    {
        $sessionId = $options['session_id'] ?? null;
        $userId = $options['user_id'] ?? null;
        $ipAddress = $options['ip_address'] ?? null;
        $files = $options['files'] ?? [];
        
        // Log the incoming request for debugging
        Log::info('🤖 AI Chat Request Started', [
            'message' => $message,
            'session_id' => $sessionId,
            'user_id' => $userId,
            'ip_address' => $ipAddress,
            'has_files' => !empty($files),
            'timestamp' => now()->toISOString()
        ]);
        
        try {
            // Initialize conversation manager
            $this->conversationManager = new ConversationManager($sessionId, $userId, $ipAddress);
            
            // Log conversation manager state
            Log::info('📋 Conversation Manager State', [
                'session_id' => $this->conversationManager->getSessionId(),
                'current_service' => $this->conversationManager->getCurrentService(),
                'current_step' => $this->conversationManager->getCurrentStep(),
                'conversation_stats' => $this->conversationManager->getConversationStats()
            ]);
            
            // Check rate limits
            if (!$this->checkRateLimit($userId, $ipAddress)) {
                throw new Exception('محدودیت تعداد درخواست. لطفاً کمی صبر کنید.');
            }
            
            // Process uploaded files
            $processedFiles = $this->processUploadedFiles($files);
            
            // Get conversation context
            $context = $this->conversationManager->getContext();
            
            // Log conversation context
            Log::info('🗂️ Conversation Context Retrieved', [
                'context_keys' => array_keys($context),
                'current_service' => $context['current_service'] ?? 'none',
                'step' => $context['step'] ?? 'initial',
                'has_service_data' => isset($context['service_data']) && !empty($context['service_data'])
            ]);
            
            // Store user message
            $this->conversationManager->storeMessage($message, true, [
                'files' => $processedFiles,
                'ip_address' => $ipAddress
            ]);
            
            // Analyze user intent using new classifier
            $intentAnalysis = $this->intentClassifier->classifyIntent($message, $context);
            
            // Handle context reset if detected
            if (isset($intentAnalysis['reset_context']) && $intentAnalysis['reset_context']) {
                Log::info('🔄 Context reset detected, clearing conversation state');
                $this->conversationManager->updateContext([
                    'current_service' => null,
                    'step' => 'initial',
                    'service_data' => [],
                    'reset_at' => now()->toISOString()
                ]);
                $context = $this->conversationManager->getContext(); // Refresh context
            }
            
            // Log intent analysis results
            Log::info('🎯 Intent Analysis Results', [
                'intent' => $intentAnalysis['intent'],
                'confidence' => $intentAnalysis['confidence'],
                'detected_service' => $intentAnalysis['detected_service'],
                'extracted_data_types' => array_keys($intentAnalysis['extracted_data'] ?? []),
                'is_continuation' => $intentAnalysis['is_continuation'] ?? false,
                'context_used' => $intentAnalysis['context_used'] ?? false,
                'reset_context' => $intentAnalysis['reset_context'] ?? false
            ]);
            
            // Generate AI response based on intent
            $response = $this->generateEnhancedResponse($message, $intentAnalysis, $context, $processedFiles);
            
            // Log response generation
            Log::info('💬 Response Generated', [
                'intent' => $intentAnalysis['intent'],
                'response_length' => strlen($response['message'] ?? ''),
                'requires_input' => $response['requires_input'] ?? false,
                'has_service_url' => isset($response['service_url']) && !empty($response['service_url']),
                'next_field' => $response['next_field'] ?? null
            ]);
            
            // Store AI response
            $this->conversationManager->storeMessage($response['message'] ?? '', false, [
                'intent' => $intentAnalysis['intent'],
                'confidence' => $intentAnalysis['confidence'],
                'service_detected' => $intentAnalysis['detected_service']
            ]);
            
            // Update conversation context
            $this->updateConversationContext($intentAnalysis, $response, $context);
            
            // Clean up temporary files
            $this->cleanupTemporaryFiles($processedFiles);
            
            $result = [
                'success' => true,
                'response' => $this->cleanResponse($response['message'] ?? ''),
                'intent' => $intentAnalysis['intent'],
                'confidence' => $intentAnalysis['confidence'],
                'detected_service' => $intentAnalysis['detected_service'],
                'service_confidence' => $intentAnalysis['service_confidence'],
                'extracted_data' => $intentAnalysis['extracted_data'],
                'suggested_services' => $intentAnalysis['suggested_services'] ?? [],
                'service_form_data' => $response['service_form_data'] ?? null,
                'service_url' => $response['service_url'] ?? null,
                'requires_input' => $response['requires_input'] ?? false,
                'next_field' => $response['next_field'] ?? null,
                'field_validation' => $response['field_validation'] ?? null,
                'conversation_id' => $this->conversationManager->getSessionId(),
                'conversation_stats' => $this->conversationManager->getConversationStats(),
                'processed_files' => $this->formatProcessedFiles($processedFiles),
                'timestamp' => now()->toISOString()
            ];
            
            // Log successful completion
            Log::info('✅ AI Chat Request Completed Successfully', [
                'session_id' => $this->conversationManager->getSessionId(),
                'intent' => $intentAnalysis['intent'],
                'response_length' => strlen($result['response']),
                'conversation_stats' => $result['conversation_stats']
            ]);
            
            return $result;
            
        } catch (Exception $e) {
            // Log error details
            Log::error('❌ AI Chat Request Failed', [
                'message' => $message,
                'session_id' => $sessionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'timestamp' => now()->toISOString()
            ]);
            
            return [
                'success' => false,
                'response' => 'متاسفانه خطایی رخ داده است. لطفاً دوباره تلاش کنید.',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString()
            ];
        }
    }
    
    /**
     * Process uploaded files with security checks
     */
    protected function processUploadedFiles(array $files): array
    {
        $processedFiles = [];
        
        foreach ($files as $file) {
            if (!$file instanceof UploadedFile) {
                continue;
            }
            
            // Security checks
            if (!$this->isFileSecure($file)) {
                throw new Exception('فایل آپلود شده امن نیست.');
            }
            
            // Size check
            if ($file->getSize() > self::MAX_FILE_SIZE) {
                throw new Exception('حجم فایل بیش از حد مجاز است.');
            }
            
            // Type check
            if (!$this->isFileTypeAllowed($file)) {
                throw new Exception('نوع فایل مجاز نیست.');
            }
            
            // Store file temporarily
            $filename = 'chat_uploads/' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('temp', $filename, 'local');
            
            $processedFile = [
                'original_name' => $file->getClientOriginalName(),
                'filename' => $filename,
                'path' => $path,
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'type' => $this->getFileType($file),
                'url' => null,
                'content' => null
            ];
            
            // Extract content for text analysis
            if ($processedFile['type'] === 'image') {
                $processedFile['url'] = Storage::url($path);
                $processedFile['content'] = $this->extractImageContent($file);
            } elseif ($processedFile['type'] === 'document') {
                $processedFile['content'] = $this->extractDocumentContent($file);
            }
            
            $processedFiles[] = $processedFile;
        }
        
        return $processedFiles;
    }
    
    /**
     * Analyze user intent and find relevant services
     */
    protected function analyzeUserIntent(string $message, array $context, array $files): array
    {
        $services = $this->getAllServices();
        $systemPrompt = $this->buildEnhancedSystemPrompt($services);
        $userPrompt = $this->buildUserPrompt($message, $context, $files);
        
        try {
            // Build messages with full conversation history
            $messages = $this->buildConversationMessages(
                $userPrompt, 
                $context, 
                $systemPrompt
            );
            
            $response = $this->geminiService->chatCompletion(
                $messages,
                'google/gemini-2.5-flash',
                [
                    'temperature' => 0.3,
                    'max_tokens' => 1500,
                    'json' => true
                ]
            );
            
            $analysis = json_decode($response, true);
            
            if (!$analysis) {
                throw new Exception('Invalid AI response format');
            }
            
            // Enhance analysis with service form data
            if (isset($analysis['selected_service'])) {
                $service = Service::where('slug', $analysis['selected_service'])->first();
                if ($service) {
                    $analysis['service_form_fields'] = $this->formAnalyzer->analyzeServiceForm($service);
                }
            }
            
            return $analysis;
            
        } catch (Exception $e) {
            Log::error('Intent analysis failed: ' . $e->getMessage());
            return $this->getFallbackAnalysis($message);
        }
    }
    
    /**
     * Generate enhanced AI response with step-by-step field collection
     */
    protected function generateEnhancedResponse(string $message, array $intentAnalysis, array $context, array $files): array
    {
        $responseData = [
            'message' => '',
            'requires_input' => false,
            'service_form_data' => null,
            'service_url' => null,
            'next_field' => null,
            'field_validation' => null,
            'step' => 'initial'
        ];
        
        // Handle different intents with enhanced logic
        switch ($intentAnalysis['intent']) {
            case IntentClassifier::INTENT_SERVICE_REQUEST:
                $responseData = $this->handleServiceRequestEnhanced($intentAnalysis, $message, $files);
                break;
                
                                     case IntentClassifier::INTENT_SERVICE_INQUIRY:
                $responseData = $this->handleServiceInquiry($intentAnalysis, $message);
                break;
                 
             case IntentClassifier::INTENT_GENERAL_QUESTION:
                 $responseData = $this->handleGeneralQuestion($message, $context, $files);
                 break;
                 
             case IntentClassifier::INTENT_FILE_ANALYSIS:
                 $responseData = $this->handleFileAnalysis($files, $message);
                 break;
                 
             case IntentClassifier::INTENT_GENERAL_CONVERSATION:
                 $responseData = $this->handleGeneralConversation($message, $context);
                 break;
                 
             case IntentClassifier::INTENT_NAVIGATION:
                 $responseData = $this->handleGeneralConversation($message, $context);
                 break;
                 
             case IntentClassifier::INTENT_COMPLAINT:
                 $responseData = $this->handleGeneralConversation($message, $context);
                 break;
                 
             case IntentClassifier::INTENT_SUPPORT:
                 $responseData = $this->handleGeneralConversation($message, $context);
                 break;
                 
             case IntentClassifier::INTENT_PRICING:
                 $responseData = $this->handleGeneralConversation($message, $context);
                 break;
                 
             case IntentClassifier::INTENT_FEEDBACK:
                 $responseData = $this->handleGeneralConversation($message, $context);
                 break;
                 
             default:
                 $responseData = $this->handleGeneralConversation($message, $context);
        }
        
        return $responseData;
    }
    
    /**
     * Handle service request with enhanced step-by-step field collection
     */
    protected function handleServiceRequestEnhanced(array $intentAnalysis, string $message, array $files): array
    {
        $serviceSlug = $intentAnalysis['detected_service'];
        $extractedData = $intentAnalysis['extracted_data'] ?? [];
        $isContinuation = $intentAnalysis['is_continuation'] ?? false;
        
        // If this is a continuation but no service detected, try to infer from context
        if ($isContinuation && !$serviceSlug) {
            $currentService = $this->conversationManager->getCurrentService();
            if ($currentService) {
                Log::info('Service continuation: Using current service from context', [
                    'current_service' => $currentService,
                    'extracted_data' => array_keys($extractedData)
                ]);
                $serviceSlug = $currentService;
            }
        }
        
        if (!$serviceSlug) {
            return [
                'message' => '<p>متاسفانه نتوانستم سرویس مورد نظر شما را شناسایی کنم.</p><p><strong>لطفاً دقیق‌تر توضیح دهید که چه سرویسی می‌خواهید.</strong></p>',
                'requires_input' => true
            ];
        }
        
        $service = Service::where('slug', $serviceSlug)->first();
        
        if (!$service) {
            return [
                'message' => '<p>متاسفانه سرویس مورد نظر در حال حاضر در دسترس نیست.</p><p><strong>لطفاً بعداً تلاش کنید.</strong></p>',
                'requires_input' => false
            ];
        }
        
        // Analyze the service form to get required fields
        $formFields = $this->formAnalyzer->analyzeServiceForm($service);
        $requiredFields = array_filter($formFields, function($field) {
            return $field['required'] ?? false;
        });
        
        // Check if we have all required data
        $missingFields = $this->getMissingRequiredFields($formFields, $extractedData);
        
        // If this is a continuation and we have extracted data, process it
        if ($isContinuation && !empty($extractedData)) {
            Log::info('Processing service continuation with data', [
                'service' => $serviceSlug,
                'extracted_data' => array_keys($extractedData),
                'missing_fields' => count($missingFields)
            ]);
            
            // Store the extracted data
            foreach ($extractedData as $fieldName => $value) {
                $this->conversationManager->storeFieldData($fieldName, $value, [
                    'validated' => true,
                    'source' => 'user_input'
                ]);
            }
            
            // If we have all required data, proceed with service execution
            if (empty($missingFields)) {
                // Generate service URL or execute the service
                $serviceUrl = $this->urlGenerator->generateServiceUrl($service, $extractedData);
                
                return [
                    'message' => "<p><strong>عالی!</strong> اطلاعات شما دریافت شد. ✅</p>
                                <p>در حال پردازش درخواست تبدیل شماره کارت به شماره شبا...</p>
                                <p><em>لطفاً کمی صبر کنید...</em></p>",
                    'requires_input' => false,
                    'service_url' => $serviceUrl,
                    'service_form_data' => $extractedData,
                    'step' => 'service_complete'
                ];
            } else {
                // We still need more data
                $nextField = $missingFields[0];
                $fieldLabel = $nextField['label'] ?? $nextField['name'] ?? 'اطلاعات';
                
                return [
                    'message' => "<p>اطلاعات دریافت شد! ✅</p>
                                <p>برای تکمیل فرآیند، لطفاً <strong>{$fieldLabel}</strong> را نیز ارسال کنید.</p>",
                    'requires_input' => true,
                    'next_field' => $nextField,
                    'step' => 'field_collection'
                ];
            }
        }
        
        // Initial service request - show what we need
        if (!empty($requiredFields)) {
            $fieldsList = collect($requiredFields)->map(function($field) {
                $label = $field['label'] ?? $field['name'] ?? 'نامشخص';
                $placeholder = $field['placeholder'] ?? '';
                return $placeholder ? "$label ($placeholder)" : $label;
            })->implode('، ');
            
            $response = "<p>برای استفاده از سرویس <strong>{$service->title}</strong> نیاز به اطلاعات زیر دارم:</p>";
            $response .= "<p><strong>{$fieldsList}</strong></p>";
            $response .= "<p>لطفاً این اطلاعات را ارسال کنید تا بتوانم سرویس را برای شما اجرا کنم.</p>";
            
            // Set the service context for future requests
            $this->conversationManager->setServiceContext($serviceSlug, []);
            
            return [
                'message' => $response,
                'requires_input' => true,
                'next_field' => $requiredFields[0] ?? null,
                'step' => 'field_collection'
            ];
        }
        
        // Service doesn't need any fields - execute immediately
        $serviceUrl = $this->urlGenerator->generateServiceUrl($service, []);
        
        return [
            'message' => "<p>سرویس <strong>{$service->title}</strong> آماده استفاده است!</p>",
            'requires_input' => false,
            'service_url' => $serviceUrl,
            'step' => 'service_complete'
        ];
    }
    
    /**
     * Handle service inquiry with enhanced responses
     */
    protected function handleServiceInquiry(array $intentAnalysis, string $message): array
    {
        $serviceSlug = $intentAnalysis['detected_service'];
        
        if ($serviceSlug) {
            $service = Service::where('slug', $serviceSlug)->first();
            
            if ($service) {
                // Create a comprehensive response about the service
                $servicePrompt = "کاربر در مورد سرویس \"{$service->title}\" سوال پرسیده است.

پیام کاربر: \"{$message}\"

لطفاً پاسخ کاملی بدهید که شامل:
1. توضیح مختصر سرویس
2. اطلاعات مورد نیاز برای استفاده
3. نحوه کار با سرویس
4. راهنمایی عملی

پاسخ باید:
- کامل و مفصل باشد (حداقل 3-4 جمله)
- به صورت HTML فرمت شود
- دوستانه و راهنمایی‌کننده باشد
- شامل مراحل عملی استفاده باشد";

                try {
                    $messages = [
                        ['role' => 'user', 'content' => $servicePrompt]
                    ];
                    
                    $response = $this->geminiService->chatCompletion(
                        $messages,
                        'google/gemini-2.5-flash-preview-04-17',
                        [
                            'temperature' => 0.7,
                            'max_tokens' => 1000
                        ]
                    );
                    
                    return [
                        'message' => $response,
                        'requires_input' => false,
                        'detected_service' => $serviceSlug
                    ];
                    
                } catch (Exception $e) {
                    Log::error('Service inquiry AI generation failed', [
                        'service' => $serviceSlug,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }
        
        // Enhanced fallback responses for common service types
        $lowerMessage = strtolower($message);
        
        if (strpos($lowerMessage, 'شبا') !== false || strpos($lowerMessage, 'شهاب') !== false || strpos($lowerMessage, 'iban') !== false) {
            return [
                'message' => '<p>خدمات شماره شبا در پیشخوانک آماده استفاده است! 🏦</p>
                            <p><strong>سرویس‌های موجود:</strong></p>
                            <ul>
                                <li>دریافت شماره شبا از شماره کارت</li>
                                <li>دریافت شماره شبا از شماره حساب</li>
                                <li>بررسی اعتبار شماره شبا</li>
                                <li>استعلام اطلاعات بانکی</li>
                            </ul>
                            <p>برای استفاده کافیه شماره کارت یا حساب بانکی‌تون رو بفرستید تا شماره شبا رو براتون تهیه کنم.</p>',
                'requires_input' => false
            ];
        }
        
        if (strpos($lowerMessage, 'کارت') !== false) {
            return [
                'message' => '<p>خدمات مربوط به کارت بانکی در پیشخوانک موجوده! 💳</p>
                            <p><strong>سرویس‌های کارت بانکی:</strong></p>
                            <ul>
                                <li>دریافت شماره شبا از کارت</li>
                                <li>استعلام اطلاعات کارت</li>
                                <li>بررسی بانک صادرکننده کارت</li>
                            </ul>
                            <p>برای استفاده، شماره کارت ۱۶ رقمی‌تون رو بفرستید.</p>',
                'requires_input' => false
            ];
        }
        
        // Generic service inquiry response
        return [
            'message' => '<p>چطور می‌تونم در مورد خدمات پیشخوانک کمکتون کنم؟</p>
                        <p><strong>خدمات اصلی ما شامل:</strong></p>
                        <ul>
                            <li>🏦 خدمات بانکی (شبا، کارت، حساب)</li>
                            <li>🚗 استعلام خلافی خودرو</li>
                            <li>📱 خدمات اپراتورها</li>
                            <li>📋 استعلامات اداری</li>
                        </ul>
                        <p>کدوم خدمت رو نیاز دارید؟</p>',
            'requires_input' => false
        ];
    }
    
    /**
     * Handle general questions with enhanced responses for service categories
     */
    protected function handleGeneralQuestion(string $message, array $context, array $files): array
    {
        $lowerMessage = strtolower($message);
        
        // Handle specific service category questions
        if (strpos($lowerMessage, 'استعلامات اداری') !== false || strpos($lowerMessage, 'استعلام اداری') !== false) {
            return [
                'message' => '<p><strong>استعلامات اداری</strong> شامل خدمات مختلفی است که در پیشخوانک ارائه می‌دهیم:</p>
                            <ul>
                                <li>🆔 <strong>استعلام کد ملی:</strong> بررسی اعتبار و اطلاعات کد ملی</li>
                                <li>🏢 <strong>استعلام شرکت‌ها:</strong> اطلاعات شناسه ملی و وضعیت شرکت‌ها</li>
                                <li>📋 <strong>استعلام سوابق قضایی:</strong> بررسی سوابق کیفری و قضایی</li>
                                <li>🎓 <strong>استعلام مدارک تحصیلی:</strong> تایید مدارک دانشگاهی و دیپلم</li>
                                <li>🏥 <strong>استعلام بیمه:</strong> وضعیت بیمه تامین اجتماعی</li>
                                <li>🚗 <strong>استعلام خودرو:</strong> مشخصات و وضعیت خودرو</li>
                            </ul>
                            <p>کدوم یک از این استعلامات رو نیاز دارید؟</p>',
                'requires_input' => false
            ];
        }
        
        if (strpos($lowerMessage, 'خدمات بانکی') !== false || strpos($lowerMessage, 'بانکی') !== false) {
            return [
                'message' => '<p><strong>خدمات بانکی</strong> پیشخوانک شامل:</p>
                            <ul>
                                <li>💳 <strong>تبدیل کارت به شبا:</strong> دریافت شماره شبا از شماره کارت</li>
                                <li>🏦 <strong>تبدیل حساب به شبا:</strong> دریافت شماره شبا از شماره حساب</li>
                                <li>✅ <strong>بررسی اعتبار شبا:</strong> تایید صحت شماره شبا</li>
                                <li>📊 <strong>استعلام اطلاعات بانکی:</strong> نام بانک و اطلاعات مالک حساب</li>
                                <li>💰 <strong>رتبه اعتباری:</strong> بررسی وضعیت اعتباری با کد ملی</li>
                            </ul>
                            <p>کدوم سرویس بانکی رو نیاز دارید؟</p>',
                'requires_input' => false
            ];
        }
        
        if (strpos($lowerMessage, 'خلافی') !== false || strpos($lowerMessage, 'ترافیک') !== false) {
            return [
                'message' => '<p><strong>خدمات خلافی و ترافیک:</strong></p>
                            <ul>
                                <li>🚗 <strong>استعلام خلافی با پلاک:</strong> لیست کامل جریمه‌های خودرو</li>
                                <li>🆔 <strong>استعلام خلافی با کد ملی:</strong> تمام خلافی‌های ثبت شده</li>
                                <li>💰 <strong>محاسبه جریمه:</strong> مبلغ کل جریمه‌های قابل پرداخت</li>
                                <li>📱 <strong>خلافی با کد رهگیری:</strong> جزئیات خلافی خاص</li>
                            </ul>
                            <p>برای استعلام، پلاک خودرو یا کد ملی خود را ارسال کنید.</p>',
                'requires_input' => false
            ];
        }
        
        // Enhanced system prompt for general questions with HTML formatting
        $systemPrompt = "شما دستیار هوشمند پیشخوانک هستید که خدمات مختلف بانکی، مالی و اداری ارائه می‌دهد.

وظایف شما:
1. پاسخ به سوالات عمومی در زمینه‌های مالی، بانکی، اداری و تکنولوژی
2. ارائه اطلاعات مفید و دقیق
3. راهنمایی کاربر به خدمات مرتبط پیشخوانک در صورت امکان
4. پاسخ‌های کامل و مفصل (حداقل 2-3 جمله)

قوانین مهم:
- پاسخ را به صورت HTML فرمت کنید
- از تگ‌های <p>، <strong>، <em>، <ul>، <li>، <br> استفاده کنید
- برای فهرست‌ها از <ul> و <li> استفاده کنید
- برای تأکید از <strong> استفاده کنید
- برای اطلاعات مهم از <em> استفاده کنید
- مستقیماً به موضوع بپردازید
- پاسخ‌های مفصل و کاربردی ارائه دهید

مثال فرمت پاسخ:
<p>اطلاعات اصلی در مورد موضوع...</p>
<ul>
<li><strong>نکته اول:</strong> توضیح مفصل</li>
<li><strong>نکته دوم:</strong> توضیح مفصل</li>
</ul>
<p><em>نکته مهم:</em> اطلاعات تکمیلی</p>";
        
        try {
            // Build messages with full conversation history
            $messages = $this->buildConversationMessages(
                "سوال: " . $message, 
                $context, 
                $systemPrompt
            );
            
            $response = $this->geminiService->chatCompletion(
                $messages,
                'google/gemini-2.5-flash-preview-04-17',
                [
                    'temperature' => 0.7,
                    'max_tokens' => 1000
                ]
            );
            
            return [
                'message' => $response,
                'requires_input' => false
            ];
            
        } catch (Exception $e) {
            Log::error('General question handling failed', [
                'message' => $message,
                'error' => $e->getMessage()
            ]);
            
            return [
                'message' => '<p>متاسفانه در حال حاضر نمی‌توانم به این سوال پاسخ کاملی بدهم.</p><p><strong>آیا می‌توانم در مورد خدمات پیشخوانک کمکتان کنم؟</strong></p>',
                'requires_input' => false
            ];
        }
    }
    
    /**
     * Handle file analysis
     */
    protected function handleFileAnalysis(array $files, string $message): array
    {
        if (empty($files)) {
            return [
                'message' => '<p>فایلی برای تحلیل ارسال نشده است.</p>',
                'requires_input' => false
            ];
        }
        
        $analysis = [];
        foreach ($files as $file) {
            if ($file['type'] === 'image') {
                $analysis[] = "<li><strong>تصویر {$file['original_name']}</strong> دریافت شد. " . 
                             ($file['content'] ? "محتوای شناسایی شده: " . $file['content'] : "تصویر قابل مشاهده است.") . "</li>";
            } elseif ($file['type'] === 'document') {
                $analysis[] = "<li><strong>سند {$file['original_name']}</strong> دریافت شد. " . 
                             ($file['content'] ? "محتوای استخراج شده موجود است." : "") . "</li>";
            }
        }
        
        $response = "<ul>" . implode("", $analysis) . "</ul>";
        $response .= "<p><em>آیا می‌خواهید من این فایل‌ها را در یکی از خدمات پیشخوانک استفاده کنم؟</em></p>";
        
        return [
            'message' => $response,
            'requires_input' => false
        ];
    }
    
    /**
     * Handle general conversation with enhanced context awareness
     */
    protected function handleGeneralConversation(string $message, array $context): array
    {
        // Enhanced conversational prompt for natural, friendly responses
        $conversationPrompt = "شما یک دستیار هوشمند و دوستانه هستید که با کاربران به صورت طبیعی و گرم گفتگو می‌کنید.

شخصیت شما:
- دوستانه، مودب و صمیمی
- پاسخ‌های طبیعی و انسانی می‌دهید
- به احوال‌پرسی‌ها پاسخ مناسب می‌دهید
- از زبان محاورات فارسی استفاده می‌کنید
- کمک‌رسان و راهنما هستید

قوانین گفتگو:
1. اگر کاربر سلام کرد یا احوال پرسید، طبیعی پاسخ دهید
2. اگر سوالی از شما کرد، صادقانه جواب دهید
3. در صورت نیاز، خدمات پیشخوانک را معرفی کنید
4. پاسخ‌هایتان کامل و مفصل باشد (حداقل 2-3 جمله)
5. از تکرار جملات تشریفاتی خودداری کنید
6. مثل یک دوست صحبت کنید

پیام کاربر: \"" . $message . "\"

لطفاً پاسخ کاملی به صورت HTML بدهید:";
        
        try {
            // Build messages with full conversation history for better context
            $messages = $this->buildConversationMessages(
                $message, 
                $context, 
                $conversationPrompt
            );
            
            // Log the conversation context being used
            Log::info('Natural conversation with enhanced prompting', [
                'message' => $message,
                'context_keys' => array_keys($context),
                'current_service' => $context['current_service'] ?? 'none',
                'step' => $context['step'] ?? 'initial',
                'messages_count' => count($messages)
            ]);
            
            $response = $this->geminiService->chatCompletion(
                $messages,
                'google/gemini-2.5-flash-preview-04-17',
                [
                    'temperature' => 0.9, // Higher temperature for more natural responses
                    'max_tokens' => 1200 // More tokens for longer responses
                ]
            );
            
            // Ensure the response is not too short
            if (strlen(strip_tags($response)) < 20) {
                // Fallback to a more natural response
                if (strpos(strtolower($message), 'سلام') !== false || strpos(strtolower($message), 'خوبی') !== false) {
                    $response = '<p>سلام! خوشحالم که با شما صحبت می‌کنم. من خوبم، ممنون که پرسیدید!</p><p>امیدوارم شما هم حال خوبی داشته باشید. چطور می‌تونم کمکتون کنم؟</p>';
                } else {
                    $response = '<p>چطور می‌تونم کمکتون کنم؟</p><p>از خدمات مختلف پیشخوانک می‌تونید استفاده کنید یا هر سوالی داشته باشید بپرسید.</p>';
                }
            }
            
            return [
                'message' => $response,
                'requires_input' => false
            ];
            
        } catch (Exception $e) {
            // Log the error for debugging
            Log::error('General conversation failed', [
                'message' => $message,
                'context' => $context,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Provide a more contextual and natural fallback based on conversation state
            $currentService = $context['current_service'] ?? null;
            
            // Natural fallback responses based on message content
            if (strpos(strtolower($message), 'سلام') !== false || strpos(strtolower($message), 'خوبی') !== false || strpos(strtolower($message), 'چطوری') !== false) {
                return [
                    'message' => '<p>سلام! خوشحالم که با شما صحبت می‌کنم. من خوبم، ممنون! 😊</p><p>شما چطورید؟ چطور می‌تونم کمکتون کنم؟</p>',
                    'requires_input' => false
                ];
            }
            
            if ($currentService) {
                return [
                    'message' => '<p>ببخشید، یه مشکل فنی پیش اومده. به نظر می‌رسد در حال استفاده از سرویس هستید.</p><p><strong>آیا نیاز به کمک دارید؟ می‌توانید اطلاعات مورد نیاز را ارسال کنید.</strong></p>',
                    'requires_input' => false
                ];
            }
            
            return [
                'message' => '<p>ببخشید، یه مشکل کوچیک پیش اومده.</p><p><strong>چطور می‌تونم کمکتون کنم؟ از خدمات مختلف پیشخوانک می‌تونید استفاده کنید.</strong></p>',
                'requires_input' => false
            ];
        }
    }
    
    /**
     * Build enhanced system prompt with comprehensive validation and conditions
     */
    protected function buildEnhancedSystemPrompt(array $services): string
    {
        $servicesList = collect($services)->map(function($category) {
            $categoryServices = collect($category['services'])->map(function($service) {
                $keywordsStr = implode(', ', $service['keywords']);
                $serviceInfo = "  - {$service['title']} (address: {$service['url']}) - Keywords: {$keywordsStr}";
                
                // Add bank-specific sub-services if they exist
                if ($service['has_sub_services'] && !empty($service['sub_services'])) {
                    $subServicesList = collect($service['sub_services'])->map(function($subService) {
                        $subKeywordsStr = implode(', ', $subService['keywords']);
                        return "    • {$subService['title']} (address: {$subService['url']}) - Bank: {$subService['bank_name']} - Keywords: {$subKeywordsStr}";
                    })->implode("\n");
                    
                    $serviceInfo .= "\n" . $subServicesList;
                }
                
                return $serviceInfo;
            })->implode("\n");
            
            return "## {$category['category_name']}\n\n{$categoryServices}";
        })->implode("\n\n");
        
        return "شما دستیار هوشمند پیشخوانک هستید که خدمات مختلف بانکی، مالی و اداری ارائه می‌دهد.

خدمات موجود در سایت همراه با کلمات کلیدی و آدرس آنها:
{$servicesList}

## وظایف اصلی:
1. تحلیل دقیق قصد کاربر
2. تشخیص نوع درخواست (سوال یا درخواست عملیاتی)
3. انتخاب دقیق سرویس اصلی بر اساس کلمات کلیدی
4. بررسی وجود داده‌های مورد نیاز
5. راهنمایی مناسب کاربر

## 60+ شرط مهم برای تحلیل صحیح:

### شرایط انتخاب سرویس اصلی:
1. برای 'کارت به شبا' یا 'تبدیل شماره کارت به شبا' → انتخاب سرویسی با slug مربوط به card-to-sheba
2. برای 'حساب به شبا' → انتخاب سرویسی با slug مربوط به account-to-sheba  
3. برای 'شبا به حساب' → انتخاب سرویسی با slug مربوط به sheba-to-account
4. برای 'خلافی خودرو' → انتخاب سرویسی با slug مربوط به traffic violations
5. مطابقت کلمات کلیدی با عبارت کاربر بسیار مهم است
6. سرویس اصلی باید در selected_service قرار گیرد، نه در suggested_services

### شرایط تشخیص نوع درخواست:
7. اگر کاربر فقط نام سرویس را گفت (مثل 'کارت به شبا') → این یک SERVICE_INQUIRY است، نه SERVICE_REQUEST
8. اگر کاربر سوال کرد ('چطور...', 'چگونه...', 'آیا...') → این SERVICE_INQUIRY است
9. اگر کاربر درباره قیمت پرسید → این SERVICE_INQUIRY است
10. اگر کاربر فقط توضیح خواست → این SERVICE_INQUIRY است
11. اگر کاربر گفت 'می‌خواهم' بدون ارائه داده → این SERVICE_INQUIRY است

### شرایط تشخیص SERVICE_REQUEST:
12. فقط زمانی SERVICE_REQUEST است که کاربر داده‌های عملیاتی ارائه داده باشد
13. برای کارت به شبا: باید شماره کارت 16 رقمی موجود باشد (مثال: 1234567890123456)
14. برای استعلام خلافی: باید شماره پلاک و کد ملی موجود باشد
15. برای استعلام کارت ملی: باید کد ملی 10 رقمی موجود باشد
16. برای محاسبه شبا: باید شماره حساب و کد بانک موجود باشد
17. اگر کاربر فقط عدد 16 رقمی داد بدون توضیح، احتمالاً شماره کارت است → SERVICE_REQUEST برای card-to-sheba
18. اگر کاربر فقط عدد 10 رقمی داد، احتمالاً کد ملی است → SERVICE_REQUEST برای مربوطه

### شرایط بررسی داده‌های مورد نیاز:
17. هرگز نگویید 'تمام اطلاعات را دریافت کردم' بدون بررسی واقعی داده‌ها
18. شماره کارت باید دقیقاً 16 رقم باشد
19. کد ملی باید دقیقاً 10 رقم باشد
20. شماره پلاک باید فرمت صحیح ایرانی داشته باشد
21. شماره شبا باید با IR شروع شود و 24 کاراکتر باشد
22. شماره حساب باید حداقل 6 رقم باشد
23. کد بانک باید 3 رقم باشد

### شرایط تشخیص عدم وجود داده:
24. اگر کاربر فقط نام سرویس گفت → داده‌ای موجود نیست
25. اگر کاربر سوال کرد → داده‌ای موجود نیست
26. اگر کاربر توضیح خواست → داده‌ای موجود نیست
27. اگر کاربر گفت 'نمی‌دانم' → داده‌ای موجود نیست
28. اگر کاربر گفت 'چطور کار می‌کند' → داده‌ای موجود نیست

### شرایط پاسخ مناسب:
29. برای SERVICE_INQUIRY: توضیح سرویس و اطلاعات مورد نیاز
30. برای SERVICE_REQUEST: بررسی تکمیل بودن داده‌ها
31. هرگز نگویید 'عالی' بدون داده واقعی
32. هرگز نگویید 'تمام اطلاعات را دریافت کردم' بدون داده
33. همیشه درخواست اطلاعات مفقود کنید

### شرایط راهنمایی:
34. برای کارت به شبا: توضیح نیاز به شماره کارت 16 رقمی
35. برای استعلام خلافی: توضیح نیاز به شماره پلاک و کد ملی
36. برای استعلام کارت ملی: توضیح نیاز به کد ملی 10 رقمی
37. برای محاسبه شبا: توضیح نیاز به شماره حساب و کد بانک
38. همیشه نوع داده‌های مورد نیاز را مشخص کنید

### شرایط تشخیص سوالات عمومی:
39. سوالات درباره بانکداری → GENERAL_QUESTION
40. سوالات درباره قوانین → GENERAL_QUESTION
41. سوالات درباره نحوه استفاده → GENERAL_QUESTION
42. سوالات تکنولوژی → GENERAL_QUESTION
43. سوالات مالی عمومی → GENERAL_QUESTION

### شرایط تشخیص مکالمه عمومی:
44. سلام و احوال‌پرسی → GENERAL_CONVERSATION
45. تشکر و قدردانی → GENERAL_CONVERSATION
46. شکایت یا انتقاد → GENERAL_CONVERSATION
47. درخواست راهنمایی کلی → GENERAL_CONVERSATION

### شرایط ایمنی و محدودیت:
48. هرگز داده‌های حساس را ذخیره نکنید
49. هرگز اطلاعات مالی واقعی ارائه ندهید
50. فقط به خدمات موجود اشاره کنید
51. از تولید محتوا خودداری کنید
52. از پاسخ‌های سیاسی خودداری کنید

### شرایط بهبود تجربه کاربری:
53. پاسخ‌ها کوتاه و مفید باشند
54. از تکرار کلمات تشریفاتی خودداری کنید
55. مستقیماً به موضوع بپردازید
56. راهنمایی‌های عملی ارائه دهید

### شرایط اضافی کیفیت:
57. همیشه confidence score واقعی ارائه دهید
58. در صورت شک، اطلاعات بیشتری درخواست کنید
59. پاسخ‌ها به صورت HTML فرمت شوند
60. لیست خدمات پیشنهادی مرتبط باشد
61. هرگز اطلاعات نادرست ارائه ندهید
62. سرویس اصلی باید در selected_service باشد، نه در suggested_services
63. suggested_services برای خدمات مرتبط و مکمل است

## قوانین اساسی:
- SERVICE_INQUIRY: زمانی که کاربر سوال می‌کند یا توضیح می‌خواهد
- SERVICE_REQUEST: فقط زمانی که داده‌های عملیاتی کامل ارائه شده باشد
- GENERAL_QUESTION: سوالات عمومی غیرخدماتی
- GENERAL_CONVERSATION: مکالمه عادی و احوال‌پرسی
- FILE_ANALYSIS: زمانی که فایل ارسال شده باشد

## فرمت پاسخ JSON:
{
  \"intent\": \"نوع قصد دقیق (service_inquiry|service_request|general_question|general_conversation|file_analysis)\",
  \"confidence\": \"میزان اطمینان از 0.0 تا 1.0\",
  \"response\": \"پاسخ HTML فرمت شده مفید و مناسب\",
  \"selected_service\": \"slug سرویس اصلی که کاربر درخواست کرده (مهم: این سرویس اصلی است)\",
  \"suggested_services\": [\"لیست slug های خدمات مرتبط و مکمل (نه سرویس اصلی)\"],
  \"requires_data\": [\"لیست دقیق فیلدهای مورد نیاز\"],
  \"has_required_data\": false,
  \"data_validation_status\": \"وضعیت بررسی داده‌ها\"
}

## مثال‌های درست:
- کاربر: 'کارت به شبا' → intent: 'service_inquiry', selected_service: 'card-to-sheba-slug', has_required_data: false
- کاربر: 'تبدیل شماره کارت به شبا' → intent: 'service_inquiry', selected_service: 'card-to-sheba-slug', has_required_data: false  
- کاربر: 'شماره کارت من 1234567890123456 است' → intent: 'service_request', selected_service: 'card-to-sheba-slug', has_required_data: true
- کاربر: '1234567890123456' → intent: 'service_request', selected_service: 'card-to-sheba-slug', has_required_data: true
- کاربر: '۵۸۹۲۱۰۱۴۴۷۰۸۶۸۷۳' → intent: 'service_request', selected_service: 'card-to-sheba-slug', has_required_data: true
- کاربر: 'چطور کار می‌کند؟' → intent: 'general_question', has_required_data: false
- کاربر: 'سلام' → intent: 'general_conversation', has_required_data: false

توجه: همیشه بر اساس محتوای واقعی پیام تصمیم بگیرید، نه فرضیات! سرویس اصلی در selected_service و خدمات مرتبط در suggested_services قرار گیرد.";
    }
    
    /**
     * Build user prompt with context and files
     */
    protected function buildUserPrompt(string $message, array $context, array $files): string
    {
        $prompt = "پیام کاربر: \"{$message}\"";
        
        if (!empty($context)) {
            $contextSummary = $this->summarizeContext($context);
            $prompt .= "\n\nزمینه مکالمه: {$contextSummary}";
        }
        
        if (!empty($files)) {
            $fileInfo = collect($files)->map(function($file) {
                return "{$file['type']}: {$file['original_name']}";
            })->implode(', ');
            $prompt .= "\n\nفایل‌های ارسال شده: {$fileInfo}";
        }
        
        return $prompt;
    }
    
    /**
     * Get all services with categories dynamically from database
     */
    protected function getAllServices(): array
    {
        return Cache::remember('ai_chat_services_with_categories', self::CACHE_TTL_SERVICES, function() {
            // Get categories with their main services (parent_id is null)
            $categories = \App\Models\ServiceCategory::with(['services' => function($query) {
                $query->where('status', 'active')
                      ->whereNull('parent_id')
                      ->orderBy('views', 'desc');
            }])
            ->active()
            ->ordered()
            ->get();

            $servicesData = [];
            
            foreach ($categories as $category) {
                $categoryData = [
                    'category_name' => $category->name,
                    'category_slug' => $category->slug,
                    'services' => []
                ];
                
                foreach ($category->services as $service) {
                    // Add main service
                    $serviceData = [
                        'id' => $service->id,
                        'title' => $service->title,
                        'short_title' => $service->short_title,
                        'slug' => $service->slug,
                        'summary' => $service->summary,
                        'description' => strip_tags($service->description),
                        'price' => $service->price,
                        'keywords' => $this->generateServiceKeywords($service),
                        'url' => $service->getUrl(),
                        'has_sub_services' => false,
                        'sub_services' => []
                    ];
                    
                    // Check if this service has bank-specific sub-services
                    $subServices = \App\Models\Service::where('parent_id', $service->id)
                                                    ->where('status', 'active')
                                                    ->with('parent')
                                                    ->get();
                    
                    if ($subServices->isNotEmpty()) {
                        $serviceData['has_sub_services'] = true;
                        
                        foreach ($subServices as $subService) {
                            // Extract bank name from sub-service title
                            $bankName = $this->extractBankNameFromTitle($subService->title, $service->title);
                            
                            $serviceData['sub_services'][] = [
                                'id' => $subService->id,
                                'title' => $subService->title,
                                'short_title' => $subService->short_title,
                                'slug' => $subService->slug,
                                'bank_name' => $bankName,
                                'url' => $subService->getUrl(),
                                'keywords' => array_merge(
                                    $this->generateServiceKeywords($service),
                                    [$bankName, $subService->slug]
                                )
                            ];
                        }
                    }
                    
                    $categoryData['services'][] = $serviceData;
                }
                
                if (!empty($categoryData['services'])) {
                    $servicesData[] = $categoryData;
                }
            }
            
            return $servicesData;
        });
    }
    
    /**
     * Extract bank name from sub-service title
     */
    protected function extractBankNameFromTitle(string $subServiceTitle, string $mainServiceTitle): string
    {
        // Remove the main service title from sub-service title to get bank name
        $bankName = str_replace($mainServiceTitle, '', $subServiceTitle);
        $bankName = trim($bankName);
        
        // If still contains service-related words, try to extract bank name
        $serviceWords = ['سرویس', 'خدمات', 'استعلام', 'بررسی', 'اطلاعات', 'تبدیل'];
        foreach ($serviceWords as $word) {
            $bankName = str_replace($word, '', $bankName);
        }
        
        $bankName = trim($bankName);
        
        // If empty or too short, try alternative extraction
        if (empty($bankName) || strlen($bankName) < 2) {
            // Look for known bank names in the title
            $knownBanks = [
                'ملی', 'ملت', 'سپه', 'پارسیان', 'پاسارگاد', 'سامان', 'کشاورزی',
                'صادرات', 'تجارت', 'رفاه', 'مسکن', 'شهر', 'دی', 'پست', 'توسعه',
                'اقتصاد', 'نوین', 'آینده', 'سینا', 'کارآفرین', 'ایران', 'زمین',
                'قوامین', 'حکمت', 'گردشگری', 'صنعت', 'معدن', 'مرکزی', 'رسالت',
                'انصار', 'کوثر', 'مهر', 'ایرانیان', 'تعاون'
            ];
            
            foreach ($knownBanks as $bank) {
                if (strpos($subServiceTitle, $bank) !== false) {
                    $bankName = $bank;
                    break;
                }
            }
        }
        
        return $bankName ?: 'نامشخص';
    }

    /**
     * Generate service keywords for better matching
     */
    protected function generateServiceKeywords(\App\Models\Service $service): array
    {
        $keywords = [];
        
        // Add title words
        $keywords = array_merge($keywords, explode(' ', $service->title));
        
        // Add short title words if available
        if ($service->short_title) {
            $keywords = array_merge($keywords, explode(' ', $service->short_title));
        }
        
        // Add specific keywords based on service content
        $titleLower = mb_strtolower($service->title);
        
        // Card to SHEBA service keywords
        if (str_contains($titleLower, 'کارت') && str_contains($titleLower, 'شبا')) {
            $keywords = array_merge($keywords, [
                'کارت به شبا', 'تبدیل کارت', 'شماره کارت', 'کارت بانکی',
                'تبدیل شماره کارت', 'شماره شبا', 'کارت شبا'
            ]);
        }
        
        // Account to SHEBA keywords
        if (str_contains($titleLower, 'حساب') && str_contains($titleLower, 'شبا')) {
            $keywords = array_merge($keywords, [
                'حساب به شبا', 'تبدیل حساب', 'شماره حساب', 'حساب بانکی'
            ]);
        }
        
        // SHEBA to account keywords
        if (str_contains($titleLower, 'شبا') && str_contains($titleLower, 'حساب')) {
            $keywords = array_merge($keywords, [
                'شبا به حساب', 'تبدیل شبا', 'شماره شبا', 'شبا بانکی'
            ]);
        }
        
        // Traffic violation keywords
        if (str_contains($titleLower, 'خلافی') || str_contains($titleLower, 'ترافیک')) {
            $keywords = array_merge($keywords, [
                'خلافی خودرو', 'جریمه رانندگی', 'ترافیک', 'پلاک'
            ]);
        }
        
        // National ID inquiry keywords
        if (str_contains($titleLower, 'کارت ملی') || str_contains($titleLower, 'ملی')) {
            $keywords = array_merge($keywords, [
                'کارت ملی', 'استعلام ملی', 'کد ملی', 'شناسه ملی'
            ]);
        }
        
        return array_unique($keywords);
    }
    
    /**
     * Security checks for uploaded files
     */
    protected function isFileSecure(UploadedFile $file): bool
    {
        // Check file extension
        $extension = strtolower($file->getClientOriginalExtension());
        $dangerousExtensions = ['exe', 'bat', 'cmd', 'com', 'pif', 'scr', 'vbs', 'js', 'jar', 'php', 'html'];
        
        if (in_array($extension, $dangerousExtensions)) {
            return false;
        }
        
        // Check MIME type
        $mimeType = $file->getMimeType();
        $allowedMimes = array_merge(self::ALLOWED_IMAGE_TYPES, self::ALLOWED_DOCUMENT_TYPES);
        
        return in_array($mimeType, $allowedMimes);
    }
    
    /**
     * Check if file type is allowed
     */
    protected function isFileTypeAllowed(UploadedFile $file): bool
    {
        $mimeType = $file->getMimeType();
        $allowedMimes = array_merge(self::ALLOWED_IMAGE_TYPES, self::ALLOWED_DOCUMENT_TYPES);
        
        return in_array($mimeType, $allowedMimes);
    }
    
    /**
     * Get file type category
     */
    protected function getFileType(UploadedFile $file): string
    {
        $mimeType = $file->getMimeType();
        
        if (in_array($mimeType, self::ALLOWED_IMAGE_TYPES)) {
            return 'image';
        } elseif (in_array($mimeType, self::ALLOWED_DOCUMENT_TYPES)) {
            return 'document';
        }
        
        return 'unknown';
    }
    
    /**
     * Extract content from images (placeholder for OCR)
     */
    protected function extractImageContent(UploadedFile $file): ?string
    {
        // Placeholder for OCR implementation
        // In production, integrate with Google Vision API or similar
        return null;
    }
    
    /**
     * Extract content from documents
     */
    protected function extractDocumentContent(UploadedFile $file): ?string
    {
        $mimeType = $file->getMimeType();
        
        if ($mimeType === 'text/plain') {
            return file_get_contents($file->getRealPath());
        }
        
        // For other document types, implement specific extractors
        return null;
    }
    
    /**
     * Extract data from user message based on form fields
     */
    protected function extractDataFromMessage(string $message, array $formFields, array $files): array
    {
        $extractedData = [];
        
        // Use AI to extract structured data from message
        $extractionPrompt = "از متن زیر، اطلاعات مربوط به فیلدهای فرم را استخراج کن:\n\n";
        $extractionPrompt .= "فیلدهای مورد نیاز:\n";
        
        foreach ($formFields as $field) {
            $extractionPrompt .= "- {$field['name']}: {$field['label']} ({$field['type']})\n";
        }
        
        $extractionPrompt .= "\nمتن کاربر: {$message}\n\n";
        $extractionPrompt .= "پاسخ را به صورت JSON ارائه دهید: {\"field_name\": \"value\", ...}";
        
        try {
            // Build messages with full conversation history for better context
            $messages = $this->buildConversationMessages(
                $message, 
                [], 
                $extractionPrompt
            );
            
            $response = $this->geminiService->chatCompletion(
                $messages,
                'google/gemini-2.5-flash',
                [
                    'temperature' => 0.1,
                    'max_tokens' => 800,
                    'json' => true
                ]
            );
            
            $extracted = json_decode($response, true);
            if (is_array($extracted)) {
                $extractedData = $extracted;
            }
            
        } catch (Exception $e) {
            Log::error('Data extraction failed: ' . $e->getMessage());
        }
        
        return $extractedData;
    }
    
    /**
     * Get missing required fields with enhanced validation
     */
    protected function getMissingRequiredFields(array $formFields, array $extractedData): array
    {
        $missing = [];
        
        foreach ($formFields as $field) {
            if (!($field['required'] ?? false)) {
                continue;
            }
            
            $fieldName = $field['name'];
            $fieldValue = $extractedData[$fieldName] ?? '';
            
            // Check if field exists and has valid content
            if (empty($fieldValue) || !$this->isValidFieldData($fieldName, $fieldValue, $field['type'] ?? 'text')) {
                $missing[] = $field;
            }
        }
        
        return $missing;
    }
    
    /**
     * Validate field data based on field type and requirements
     */
    protected function isValidFieldData(string $fieldName, string $value, string $fieldType): bool
    {
        $value = trim($value);
        
        if (empty($value)) {
            return false;
        }
        
        // Field-specific validation based on common service fields
        switch ($fieldName) {
            case 'card_number':
                // Card number should be exactly 16 digits
                return preg_match('/^\d{16}$/', $value);
                
            case 'national_id':
            case 'owner_national_id':
                // National ID should be exactly 10 digits
                return preg_match('/^\d{10}$/', $value);
                
            case 'iban':
                // IBAN should start with IR and be 24 characters
                return preg_match('/^IR\d{22}$/', strtoupper($value));
                
            case 'account_number':
                // Account number should be at least 6 digits
                return preg_match('/^\d{6,}$/', $value);
                
            case 'bank_code':
                // Bank code should be exactly 3 digits
                return preg_match('/^\d{3}$/', $value);
                
            case 'plate_number':
                // Iranian plate number formats
                return preg_match('/^\d{2}[A-Z]\d{3}-\d{2}$/', $value) || 
                       preg_match('/^\d{3}[A-Z]\d{2}-\d{2}$/', $value);
                
            case 'phone':
            case 'mobile':
                // Iranian mobile number
                return preg_match('/^09\d{9}$/', $value);
                
            case 'postal_code':
                // Iranian postal code
                return preg_match('/^\d{10}$/', $value);
                
            case 'birth_date':
                // Date validation (YYYY/MM/DD or YYYY-MM-DD)
                return preg_match('/^\d{4}[\/\-]\d{2}[\/\-]\d{2}$/', $value);
                
            case 'amount':
                // Amount should be numeric and positive
                return is_numeric($value) && floatval($value) > 0;
                
            case 'email':
                // Email validation
                return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
                
            default:
                // For other fields, check minimum length based on type
                switch ($fieldType) {
                    case 'number':
                        return is_numeric($value);
                    case 'email':
                        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
                    case 'text':
                    case 'textarea':
                        return strlen($value) >= 2; // Minimum 2 characters
                    default:
                        return !empty($value);
                }
        }
    }
    
    /**
     * Format processed files for response
     */
    protected function formatProcessedFiles(array $files): array
    {
        return collect($files)->map(function($file) {
            return [
                'name' => $file['original_name'],
                'type' => $file['type'],
                'size' => $file['size'],
                'url' => $file['url'] ?? null
            ];
        })->toArray();
    }
    
    /**
     * Get conversation context
     */
    protected function getConversationContext(string $sessionId): array
    {
        if (!$sessionId) return [];
        
        $contextKey = "ai_chat_context:{$sessionId}";
        return Cache::get($contextKey, []);
    }
    
    /**
     * Update conversation context
     */
    protected function updateConversationContext(array $intentAnalysis, array $response, array $context): void
    {
        // Update service context if service was detected
        if ($intentAnalysis['detected_service']) {
            $this->conversationManager->setServiceContext(
                $intentAnalysis['detected_service'],
                $intentAnalysis['extracted_data'] ?? []
            );
        }
        
        // Update conversation step based on intent and response
        $step = $this->determineConversationStep($intentAnalysis, $response, $context);
        $this->conversationManager->setStep($step);
        
        // Store extracted data if any
        if (!empty($intentAnalysis['extracted_data'])) {
            foreach ($intentAnalysis['extracted_data'] as $fieldName => $value) {
                $this->conversationManager->storeFieldData($fieldName, $value, [
                    'validated' => false,
                    'source' => 'extracted_from_message'
                ]);
            }
        }
        
        // Store field validation results if any
        if (isset($response['field_validation'])) {
            $fieldName = $response['field_validation']['field_name'] ?? null;
            if ($fieldName) {
                $this->conversationManager->storeFieldData($fieldName, $response['field_validation']['value'], [
                    'validated' => $response['field_validation']['valid'],
                    'validation_errors' => $response['field_validation']['errors'] ?? [],
                    'source' => 'user_input'
                ]);
            }
        }
        
        // Extend TTL for active conversations
        $this->conversationManager->extendTTL();
    }
    
    /**
     * Determine conversation step based on intent and response
     */
    protected function determineConversationStep(array $intentAnalysis, array $response, array $context): string
    {
        // If service was detected and requires field collection
        if ($intentAnalysis['detected_service'] && $intentAnalysis['intent'] === IntentClassifier::INTENT_SERVICE_REQUEST) {
            if (isset($response['next_field'])) {
                return 'field_collection';
            }
            if (isset($response['service_url'])) {
                return 'service_complete';
            }
            return 'service_identified';
        }
        
        // If service inquiry
        if ($intentAnalysis['intent'] === IntentClassifier::INTENT_SERVICE_INQUIRY) {
            return 'service_inquiry';
        }
        
        // If general conversation
        if ($intentAnalysis['intent'] === IntentClassifier::INTENT_GENERAL_CONVERSATION) {
            return 'conversation';
        }
        
        // Default step
        return 'initial';
    }
    
    /**
     * Summarize conversation context
     */
    protected function summarizeContext(array $context): string
    {
        if (empty($context)) return '';
        
        $recent = array_slice($context, -3);
        return collect($recent)->map(function($exchange) {
            return "کاربر: {$exchange['user_message']} | پاسخ: " . Str::limit($exchange['ai_response'], 100);
        })->implode(' | ');
    }
    
    /**
     * Check rate limits
     */
    protected function checkRateLimit(?string $userId, ?string $ipAddress): bool
    {
        $now = now();
        $hourKey = $now->format('Y-m-d-H');
        $dayKey = $now->format('Y-m-d');
        
        if ($userId) {
            $userHourlyKey = "ai_chat:user:{$userId}:hour:{$hourKey}";
            $userHourlyCount = Cache::get($userHourlyKey, 0);
            
            if ($userHourlyCount >= self::RATE_LIMIT_PER_USER_HOURLY) {
                return false;
            }
            
            $userDailyKey = "ai_chat:user:{$userId}:day:{$dayKey}";
            $userDailyCount = Cache::get($userDailyKey, 0);
            
            if ($userDailyCount >= self::RATE_LIMIT_PER_USER_DAILY) {
                return false;
            }
            
            Cache::put($userHourlyKey, $userHourlyCount + 1, 3600);
            Cache::put($userDailyKey, $userDailyCount + 1, 86400);
            
        } else {
            $ipHourlyKey = "ai_chat:ip:{$ipAddress}:hour:{$hourKey}";
            $ipHourlyCount = Cache::get($ipHourlyKey, 0);
            
            if ($ipHourlyCount >= self::RATE_LIMIT_PER_IP_HOURLY) {
                return false;
            }
            
            $ipDailyKey = "ai_chat:ip:{$ipAddress}:day:{$dayKey}";
            $ipDailyCount = Cache::get($ipDailyKey, 0);
            
            if ($ipDailyCount >= self::RATE_LIMIT_ANONYMOUS_DAILY) {
                return false;
            }
            
            Cache::put($ipHourlyKey, $ipHourlyCount + 1, 3600);
            Cache::put($ipDailyKey, $ipDailyCount + 1, 86400);
        }
        
        return true;
    }
    
    /**
     * Clean up temporary files
     */
    protected function cleanupTemporaryFiles(array $files): void
    {
        foreach ($files as $file) {
            if (isset($file['path']) && Storage::disk('local')->exists($file['path'])) {
                Storage::disk('local')->delete($file['path']);
            }
        }
    }
    
    /**
     * Clean HTML markup and unwanted characters from response
     */
    protected function cleanResponse($response): string
    {
        // Handle non-string inputs
        if (is_array($response)) {
            $response = json_encode($response);
        } elseif (!is_string($response)) {
            $response = (string) $response;
        }
        
        // If response is empty, return default message
        if (empty($response)) {
            return 'متاسفانه نتوانستم پاسخ مناسبی تولید کنم. لطفاً دوباره تلاش کنید.';
        }
        
        // Remove HTML code blocks with ```html and ```
        $response = preg_replace('/```html\s*\n?(.*?)\n?```/s', '$1', $response);
        $response = preg_replace('/```\s*\n?(.*?)\n?```/s', '$1', $response);
        
        // Remove extra quotes at the beginning and end
        $response = trim($response, '"\'`');
        
        // Remove unnecessary HTML comments
        $response = preg_replace('/<!--.*?-->/s', '', $response);
        
        // Clean up multiple spaces and line breaks
        $response = preg_replace('/\s+/', ' ', $response);
        $response = str_replace(['  ', '   '], ' ', $response);
        
        // Remove empty HTML tags
        $response = preg_replace('/<([^>]+)>\s*<\/\1>/', '', $response);
        
        // Fix common HTML entities
        $response = html_entity_decode($response, ENT_QUOTES, 'UTF-8');
        
        return trim($response);
    }
    
    /**
     * Get fallback analysis when AI fails
     */
    protected function getFallbackAnalysis(string $message): array
    {
        return [
            'intent' => 'general_conversation',
            'confidence' => 0.5,
            'response' => 'چطور می‌تونم کمکتون کنم؟',
            'suggested_services' => [],
            'requires_data' => []
        ];
    }

    /**
     * Validate extracted data from user message
     */
    protected function validateExtractedData(array $extractedData, array $requiredFields): array
    {
        return $this->smartValidator->validateFields($extractedData);
    }

    /**
     * Format field validation response for user
     */
    protected function formatFieldValidationResponse(array $validationResults): string
    {
        if ($validationResults['valid']) {
            return '<p>✅ اطلاعات شما تأیید شد!</p>';
        }

        $message = '<p>❌ در اطلاعات ارسالی مشکلاتی وجود دارد:</p><ul>';
        
        foreach ($validationResults['fields'] as $fieldName => $result) {
            if (!$result['valid']) {
                $errors = implode('، ', $result['errors']);
                $message .= "<li><strong>{$result['field_type']}</strong>: {$errors}</li>";
            }
        }
        
        if (!empty($validationResults['global_errors'])) {
            foreach ($validationResults['global_errors'] as $error) {
                $message .= "<li>{$error}</li>";
            }
        }
        
        $message .= '</ul><p>لطفاً اطلاعات صحیح را ارسال کنید.</p>';
        
        // Add suggestions if available
        if (!empty($validationResults['suggestions'])) {
            $message .= '<p><strong>پیشنهادات:</strong></p><ul>';
            foreach ($validationResults['suggestions'] as $fieldName => $suggestions) {
                foreach ($suggestions as $suggestion) {
                    $message .= "<li>{$suggestion}</li>";
                }
            }
            $message .= '</ul>';
        }
        
        return $message;
    }

    /**
     * Generate field request message for user
     */
    protected function generateFieldRequestMessage(array $fieldInfo, string $fieldName): string
    {
        $description = $fieldInfo['description'] ?? $fieldName;
        $example = $this->getFieldExample($fieldName);
        
        $message = "<p>لطفاً <strong>{$description}</strong> خود را ارسال کنید.</p>";
        
        if ($example) {
            $message .= "<p><em>مثال: {$example}</em></p>";
        }
        
        return $message;
    }

    /**
     * Get example for specific field type
     */
    protected function getFieldExample(string $fieldName): ?string
    {
        $examples = [
            'card_number' => '6219-8610-5867-3242',
            'iban' => '627648001234567890123456',
            'national_code' => '0123456789',
            'mobile' => '09123456789',
            'account_number' => '12345678901234567890',
            'company_id' => '12345678901',
            'customer_type' => 'حقیقی یا حقوقی'
        ];
        
        return $examples[$fieldName] ?? null;
    }

    /**
     * Build conversation messages with full history for API calls
     */
    protected function buildConversationMessages(string $currentMessage, array $context, string $systemPrompt = ''): array
    {
        $messages = [];
        
        // Add system prompt if provided
        if (!empty($systemPrompt)) {
            $messages[] = ['role' => 'user', 'content' => $systemPrompt];
        }
        
        // Get conversation history formatted for API
        $historyMessages = $this->conversationManager->getConversationHistoryForAPI(20);
        
        // Log conversation history details
        Log::info('🔗 Building Conversation Messages', [
            'system_prompt_length' => strlen($systemPrompt),
            'history_messages_count' => count($historyMessages),
            'current_message_length' => strlen($currentMessage),
            'session_id' => $this->conversationManager->getSessionId()
        ]);
        
        // Add conversation history if available
        if (!empty($historyMessages)) {
            $messages = array_merge($messages, $historyMessages);
            Log::info('📜 Added conversation history to API call', [
                'history_count' => count($historyMessages),
                'total_messages' => count($messages)
            ]);
        } else {
            Log::info('📝 No conversation history found, starting fresh conversation');
        }
        
        // Add current message
        $messages[] = [
            'role' => 'user', 
            'content' => $currentMessage
        ];
        
        // Log final message structure
        Log::info('📨 Final API Messages Structure', [
            'total_messages' => count($messages),
            'roles' => array_count_values(array_column($messages, 'role')),
            'message_lengths' => array_map(function($msg) {
                return strlen($msg['content']);
            }, $messages)
        ]);
        
        return $messages;
    }
} 