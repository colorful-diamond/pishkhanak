<?php

namespace App\Services;
use App\Services\OpenRouterService;
use Illuminate\Support\Facades\Bus;
use App\Jobs\GenerateSectionContentJob;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Log;
use App\Models\Post;
use App\Models\AiContent;
use App\Models\AiSetting;
use Throwable;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class AiService
{
    // Constants for AI models
    public const GPT_4O_MINI = 'openai/gpt-4o-mini';
    public const GPT_4O = 'openai/gpt-4o-latest';
    public const GPT_3_5 = 'openai/gpt-3.5-turbo';
    public const GEMINI_FLASH = 'google/gemini-2.5-flash';
    public const GEMINI_PRO = 'google/gemini-2.5-pro';
    public const PERP_SMALL = 'perplexity/llama-3.1-sonar-small-128k-online';
    public const PERP_LARGE = 'perplexity/llama-3.1-sonar-large-128k-online';
    public const PERP_HUGE = 'perplexity/llama-3.1-sonar-huge-128k-online';

    protected $model_types = array('fast' => 'google/gemini-2.5-flash' , 'advanced' => 'google/gemini-2.5-pro');
    protected OpenRouterService $openRouterService;
    protected GeminiService $geminiService;
    protected ?AiSetting $aiSetting;
    protected int $timeout = 360; // Default timeout in seconds

    public function __construct()
    {
        $this->openRouterService = app(OpenRouterService::class);
        $this->geminiService = app(GeminiService::class);
        $this->aiSetting = $this->getActiveAiSetting();
    }

    /**
     * Get active AI setting
     */
    protected function getActiveAiSetting(): ?AiSetting
    {
        try {
            return Cache::remember('active_ai_setting', 3600, function () {
                $setting = AiSetting::where('is_active', true)->first();
                if (!$setting) {
                    Log::warning('No active AI settings found');
                    return null;
                }
                return $setting;
            });
        } catch (\Exception $e) {
            Log::error('Failed to get AI settings: ' . $e->getMessage());
            return AiSetting::where('is_active', true)->first();
        }
    }

    /**
     * Get model configuration from settings
     */
    protected function getModelConfig(string $modelName): ?array
    {
        if (!$this->aiSetting) {
            return null;
        }

        $config = collect($this->aiSetting->model_config)->firstWhere('name', $modelName);

        if (!$config) {
            Log::warning("Model config not found for: {$modelName}");
            return null;
        }

        // Validate required fields
        $requiredFields = ['model', 'title_field', 'searchable_fields'];
        foreach ($requiredFields as $field) {
            if (!isset($config[$field])) {
                Log::error("Missing required field {$field} in model config for: {$modelName}");
                return null;
            }
        }

        return $config;
    }

    /**
     * Get prompt template from settings
     */
    protected function getPromptTemplate(string $templateName, array $variables = []): string
    {
        if (!$this->aiSetting) {
            return '';
        }

        $template = collect($this->aiSetting->prompt_templates)
            ->firstWhere('name', $templateName);

        if (!$template) {
            return '';
        }

        $content = $template['template'];
        foreach ($variables as $key => $value) {
            $content = str_replace('{' . $key . '}', $value, $content);
        }

        return $content;
    }

    public function languageDetector($content)
    {
        if (!$this->aiSetting) {
            throw new \Exception('No active AI settings found');
        }

        $messages = [
            ['role' => 'user', 'content' => 'You are a language detection expert. Return only the ISO 639-1 language code.'],
            ['role' => 'user', 'content' => "Detect the language of this text and return only the ISO 639-1 code: $content"]
        ];

        try {
            $response = $this->geminiService->chatCompletion(
                $messages,
                self::GEMINI_FLASH,
                [
                    'temperature' => $this->aiSetting->temperature,
                    'max_tokens' => $this->aiSetting->max_tokens,
                    'frequency_penalty' => $this->aiSetting->frequency_penalty,
                    'presence_penalty' => $this->aiSetting->presence_penalty,
                ]
            );
            return trim(strtolower($response));
        } catch(\Exception $e) {
            Log::error($e->getMessage());
            try {
                sleep(5);
                $response = $this->openRouterService->chatCompletion($messages, self::GPT_3_5);
                return trim(strtolower($response));
            } catch(\Exception $e) {
                Log::error($e->getMessage());
                return 'en';
            }
        }
    }

    public function translateText(string $text, string $targetLanguage)
    {
        if (!$this->aiSetting) {
            throw new \Exception('No active AI settings found');
        }

        $template = $this->getPromptTemplate('translation', [
            'text' => $text,
            'target_language' => $targetLanguage
        ]);

        $messages = [
            ['role' => 'user', 'content' => 'You are a professional translator.'],
            ['role' => 'user', 'content' => $template ?: "Translate the following text to $targetLanguage: $text"]
        ];

        return $this->openRouterService->chatCompletion(
            $messages,
            self::GPT_4O_MINI,
            [
                'temperature' => $this->aiSetting->temperature,
                'max_tokens' => $this->aiSetting->max_tokens,
                'frequency_penalty' => $this->aiSetting->frequency_penalty,
                'presence_penalty' => $this->aiSetting->presence_penalty,
            ]
        );
    }

    public function generateSlug(string $title, string $language = 'English')
    {
        if (!$this->aiSetting) {
            throw new \Exception('No active AI settings found');
        }

        $template = $this->getPromptTemplate('slug_generation', [
            'title' => $title,
            'language' => $language
        ]);

        $messages = [
            ['role' => 'user', 'content' => 'You are a professional SEO slug generator. Your task is to create SEO-friendly URL slugs in English.'],
            ['role' => 'user', 'content' => $template ?: "
                Generate a perfect SEO-friendly slug in English for the following title: '$title'
                
                Requirements:
                - Convert to English if the title is in another language
                - Use only lowercase letters, numbers, and hyphens
                - Replace spaces with hyphens (-)
                - Remove special characters, punctuation, and symbols
                - Maximum 8 words
                - Should be meaningful and descriptive
                - Should be web-friendly and SEO optimized
                
                Examples:
                - 'How to Download New Films' → 'how-to-download-new-films'
                - 'بهترین فیلم های سال' → 'best-movies-of-the-year'
                - 'Top 10 Programming Tips!' → 'top-10-programming-tips'
                
                Return ONLY the slug, no explanations, no quotes, no additional text.
            "]
        ];

        try {
            $response = $this->geminiService->chatCompletion(
                $messages,
                self::GEMINI_FLASH,
                [
                    'temperature' => 0.3, // Lower temperature for more consistent results
                    'max_tokens' => 100,
                ]
            );
            
            // Clean and format the response
            $slug = $this->cleanSlug($response);
            
            return $slug ?: $this->generateFallbackSlug($title);
            
        } catch(\Exception $e) {
            Log::error('Slug generation failed with Gemini: ' . $e->getMessage());
            try {
                sleep(2);
                $response = $this->openRouterService->chatCompletion(
                    $messages,
                    self::GPT_4O_MINI,
                    [
                        'temperature' => 0.3,
                        'max_tokens' => 100,
                    ]
                );
                
                $slug = $this->cleanSlug($response);
                return $slug ?: $this->generateFallbackSlug($title);
                
            } catch(\Exception $e) {
                Log::error('Slug generation failed with OpenRouter: ' . $e->getMessage());
                return $this->generateFallbackSlug($title);
            }
        }
    }

    /**
     * Clean and format slug response from AI
     */
    private function cleanSlug(string $response): string
    {
        // Remove quotes and extra whitespace
        $slug = trim($response, '"\'` ');
        
        // Convert to lowercase
        $slug = strtolower($slug);
        
        // Remove any remaining special characters except hyphens and alphanumeric
        $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);
        
        // Replace multiple hyphens with single hyphen
        $slug = preg_replace('/-+/', '-', $slug);
        
        // Remove leading/trailing hyphens
        $slug = trim($slug, '-');
        
        // Limit length to reasonable slug size
        if (strlen($slug) > 60) {
            $slug = substr($slug, 0, 60);
            $slug = trim($slug, '-');
        }
        
        return $slug;
    }

    /**
     * Generate fallback slug if AI fails
     */
    private function generateFallbackSlug(string $title): string
    {
        // Basic fallback: transliterate and clean
        $slug = strtolower($title);
        
        // Replace common Persian/Arabic characters with English equivalents
        $persian_to_english = [
            'ا' => 'a', 'ب' => 'b', 'پ' => 'p', 'ت' => 't', 'ث' => 's', 'ج' => 'j',
            'چ' => 'ch', 'ح' => 'h', 'خ' => 'kh', 'د' => 'd', 'ذ' => 'z', 'ر' => 'r',
            'ز' => 'z', 'ژ' => 'zh', 'س' => 's', 'ش' => 'sh', 'ص' => 's', 'ض' => 'z',
            'ط' => 't', 'ظ' => 'z', 'ع' => 'a', 'غ' => 'gh', 'ف' => 'f', 'ق' => 'gh',
            'ک' => 'k', 'گ' => 'g', 'ل' => 'l', 'م' => 'm', 'ن' => 'n', 'و' => 'v',
            'ه' => 'h', 'ی' => 'i', 'ء' => '', 'أ' => 'a', 'إ' => 'i', 'آ' => 'a',
            'ة' => 'h', 'ى' => 'a'
        ];
        
        $slug = str_replace(array_keys($persian_to_english), array_values($persian_to_english), $slug);
        
        // Replace spaces and special characters with hyphens
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        
        // Remove multiple hyphens
        $slug = preg_replace('/-+/', '-', $slug);
        
        // Remove leading/trailing hyphens
        $slug = trim($slug, '-');
        
        // Limit length
        if (strlen($slug) > 50) {
            $slug = substr($slug, 0, 50);
            $slug = trim($slug, '-');
        }
        
        return $slug ?: 'untitled-post';
    }

    public function generateContent(string $title, string $shortDescription, string $language = 'English', string $model_type = 'fast', $new_content = true, $repeat = 0)
    {
        try {
            if (!$this->aiSetting) {
                throw new \Exception('No active AI settings found');
            }

            Log::info("Generate Content: Title: " . $title . "\n Short Description: " . $shortDescription . "\n Repeat: " . $repeat);

            $aiContent = new AiContent();
            $headingsJson = $this->cleanJson($this->generateTitles(
                $title,
                $shortDescription,
                $language,
                $model_type,
                $this->aiSetting->generation_settings['headings_number'] ?? 8,
                $this->aiSetting->generation_settings['sub_headings_number'] ?? 3
            ));

            $headings = json_decode($headingsJson, true);
            if(!is_array($headings)){
                Log::error("Failed Try " . $repeat . " Reason is Generate Content: Headings not array: " . $headingsJson);
                if($repeat < 3){
                    return $this->generateContent($title, $shortDescription, $language, $model_type, $new_content, $repeat + 1);
                }
                return false;
            }

            if(is_array($headings) && count($headings) == 1){
                $headings = $headings['headlines'];
            }

            if($new_content){
                $aiContent->title = $title;
                $aiContent->slug = $this->generateSlug($title, $language);
                $aiContent->short_description = $shortDescription;
                $aiContent->language = $language;
                $aiContent->model_type = $model_type;
                $aiContent->status = 'generating';
                $aiContent->ai_headings = $headings;
                $aiContent->ai_sections = array();
                $aiContent->save();
            }

            $number = 1;
            $jobs = [];
            foreach ($headings as $heading) {
                try {
                    $job = new GenerateSectionContentJob(
                        $heading,
                        $title,
                        $shortDescription,
                        $language,
                        $model_type,
                        $number,
                        count($headings),
                        $aiContent->id
                    );
                    Queue::push($job);
                    $jobs[] = $job;
                } catch (\Exception $e) {
                    Log::error("Failed to create job for heading: " . json_encode($heading), [
                        'error' => $e->getMessage()
                    ]);
                }
                $number++;
            }

            if (empty($jobs)) {
                throw new \Exception('No jobs were created successfully');
            }

            $startTime = time();
            while (time() - $startTime < $this->timeout) {
                $aiContent = AiContent::find($aiContent->id);
                if (count($aiContent->ai_sections ?? []) >= count($jobs)) {
                    break;
                }
                usleep(500000);
            }

            if (count($aiContent->ai_sections ?? []) >= count($jobs)) {
                $aiContent->status = 'completed';
                $aiContent->save();
                $url = route('filament.access.resources.ai-contents.edit', ['record' => $aiContent->id]);
            } else {
                Log::error('Content generation timed out');
                $aiContent->status = 'failed';
                $aiContent->save();
                $url = false;
            }

            return $url;
        } catch (\Exception $e) {
            Log::error("Content generation failed", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    public function generateTitles(
        string $title,
        string $shortDescription,
        string $language = 'English',
        string $model_type = 'advanced',
        int $headings_number = 8,
        int $sub_headings_number = 3,
        string $generation_mode = 'offline'
    ) {
        if (!$this->aiSetting) {
            throw new \Exception('No active AI settings found');
        }

        $modelConfig = $this->getModelConfig($model_type);
        $template = $this->getPromptTemplate('heading_generation', [
            'title' => $title,
            'description' => $shortDescription,
            'language' => $language,
            'headings_count' => $headings_number,
            'sub_headings_count' => $sub_headings_number
        ]);

        // If AiContentGenerator provided a structured service context, emphasize it in system instructions
        $serviceContextInstruction = "";
        if (strpos($shortDescription, '[SERVICE_CONTEXT_BEGIN]') !== false) {
            $serviceContextInstruction = "\nMANDATORY: Use the service context between [SERVICE_CONTEXT_BEGIN] and [SERVICE_CONTEXT_END] to create headings that: (1) introduce the service naturally mentioning the website name, (2) explain how the service works and its benefits for users, (3) describe what makes this service valuable and unique, (4) if pricing is mentioned in context, include a heading about costs in a user-friendly way. Keep all headings informative and conversational for general readers. Avoid technical jargon or FAQ-style headings.";
        }

        $baseHeadingPrompt = $template ?: "
                Create $headings_number informative and engaging section headings in $language for an article about: '$title'
                
                Guidelines:
                - Each heading should have $sub_headings_number related sub-topics
                - Make headings natural and reader-friendly
                - Ensure all headings flow logically and tell a complete story
                - Avoid repetition between headings
                - Use conversational language that appeals to general readers
                - Do not include FAQ-style headings
                - All text must be in proper $language";
        $baseHeadingPrompt .= $serviceContextInstruction . "
                
                IMPORTANT: Return ONLY a valid JSON object with no additional text, explanations, or formatting. 
                DON'T EXPLAIN YOURSELF and don't write any text before and after the json output
                
                Return exactly in this JSON format:
                ```json
                {
                    \"headlines\": [
                        {
                            \"title\": \"\",
                            \"sub_headlines\": []
                        }
                    ]
                }
                ```";

        $messages = [
            ['role' => 'user', 'content' => 'You are a professional H2 headings generator in '.$language.'.'],
            ['role' => 'user', 'content' => "please consider user description for making the content which is : ".$shortDescription],
            ['role' => 'user', 'content' => $baseHeadingPrompt]
        ];

        $online = ($generation_mode === 'online');
        
        try {
            return $this->geminiService->chatCompletion(
                $messages,
                $this->model_types[$model_type] ?? self::GEMINI_FLASH,
                [
                    'online' => $online,
                    'json' => true,
                    'temperature' => $this->aiSetting->temperature,
                    'max_tokens' => $this->aiSetting->max_tokens,
                ]
            );
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            try {
                sleep(3);
                return $this->geminiService->chatCompletion(
                    $messages,
                    $this->model_types[$model_type] ?? self::GEMINI_FLASH,
                    [
                        'online' => $online,
                        'temperature' => $this->aiSetting->temperature,
                        'max_tokens' => $this->aiSetting->max_tokens,
                    ]
                );
            } catch (\Exception $e) {
                return false;
            }
        }
    }

    public function generateSectionContent(
        array $heading,
        string $title,
        string $shortDescription,
        int $number,
        int $count,
        string $language = 'English',
        string $model_type = 'advanced',
        string $generation_mode = 'offline'
    ) {
        if (!$this->aiSetting) {
            throw new \Exception('No active AI settings found');
        }

        $extra = '';
        if($number == 1){
            $extra = '- Start with an engaging introduction that draws readers in
                - Set the context for what readers will learn
                - DO NOT repeat the article title';
        } else if($number == $count){
            $extra = '- Conclude this section naturally
                - You may add a brief closing thought encouraging engagement
                - DO NOT repeat the article title';
        } else {
            $extra = '- Continue building on the topic naturally
                - DO NOT repeat the article title';
        }

        // Extract sub_headline titles if they are objects
        $subHeadlineTitles = [];
        if (isset($heading['sub_headlines']) && is_array($heading['sub_headlines'])) {
            foreach ($heading['sub_headlines'] as $subHeadline) {
                if (is_array($subHeadline) && isset($subHeadline['title'])) {
                    $subHeadlineTitles[] = $subHeadline['title'];
                } elseif (is_string($subHeadline)) {
                    $subHeadlineTitles[] = $subHeadline;
                }
            }
        }

        $template = $this->getPromptTemplate('section_generation', [
            'heading' => $heading['title'],
            'sub_headings' => implode(',', $subHeadlineTitles),
            'title' => $title,
            'description' => $shortDescription,
            'language' => $language,
            'section_number' => $number,
            'total_sections' => $count,
            'context' => $extra
        ]);

        // Strengthen section prompt with service-context requirements if provided
        $sectionServiceInstruction = '';
        if (strpos($shortDescription, '[SERVICE_CONTEXT_BEGIN]') !== false) {
            $sectionServiceInstruction = "\nMANDATORY: If service context is present in user description, naturally mention the website/brand name and service URL. Explain what the service offers, how users can benefit from it, and what makes it valuable. Describe the process in a friendly, informative way without technical jargon. If pricing information is provided in the context, mention it naturally. Keep the tone conversational and helpful.";
        }

        $baseSectionPrompt = $template ?: "
                Research and write informative, engaging content in $language for this section: {$heading['title']}
                Cover these subtopics: ".implode(', ', $heading['sub_headlines'])."
                
                Important guidelines:
                - Write naturally and conversationally for general readers
                - Make the content informative and valuable
                - DO NOT repeat or mention the main article title '$title' in this section
                - Start directly with the section content, not the article title
                $extra";
        $baseSectionPrompt .= $sectionServiceInstruction . " 
                output should be in Spoken $language and wrap the content in <section>, <p>, <h2>, <h3>, <ul>, <li>, <strong>, <em> tags don't use any h1 tags and just make main title of section in h2 tag it means we should have only one h2 tag in the content and some h3 tags for sub headings
                use proper seo attributes for all of the tags
                consider 2025 SEO best practices
                at least 300 - 500 words
                DON'T EXPLAIN YOURSELF NOT AT ALL";

        // Get all services for internal linking
        $servicesForLinking = $this->getServicesForInternalLinking();
        
        $internalLinkingPrompt = "";
        if (!empty($servicesForLinking)) {
            $internalLinkingPrompt = "\n\nINTERNAL LINKING REQUIREMENT:
            You have access to these services/pages from our website. Choose 2-4 most relevant ones to naturally link within this section's content:
            " . implode("\n", array_map(function($service) {
                return "- {$service['title']} (link: {$service['url']})";
            }, $servicesForLinking)) . "
            
            IMPORTANT: Insert these links naturally within sentences where they add value. Use anchor text that flows with the content, not just the service title.
            Example: 'برای بهبود محتوای خود می‌توانید از <a href=\"/url\">ابزار تولید محتوا</a> استفاده کنید' instead of just listing links.";
        }
        
        $messages = [
            ['role' => 'user', 'content' => 'You are a professional content generator in Spoken '.$language.'.'],
            ['role' => 'user', 'content' => 'please consider user description for making the content which is : '.$shortDescription],
            ['role' => 'user', 'content' => $baseSectionPrompt . $internalLinkingPrompt]
        ];

        $online = ($generation_mode === 'online');
        
        try {
            $response = $this->geminiService->chatCompletion(
                $messages,
                $this->model_types[$model_type] ?? self::GEMINI_FLASH,
                [
                    'online' => $online,
                    'temperature' => $this->aiSetting->temperature,
                    'max_tokens' => $this->aiSetting->max_tokens,
                ]
            );
            Log::info($response);
            return $response;
        } catch(\Exception $e) {
            Log::error($e->getMessage());
            try {
                sleep(5);
                $response = $this->geminiService->chatCompletion(
                    $messages,
                    $this->model_types[$model_type] ?? self::GEMINI_FLASH,
                    [
                        'online' => $online,
                        'temperature' => $this->aiSetting->temperature,
                        'max_tokens' => $this->aiSetting->max_tokens,
                    ]
                );
                Log::info($response);
                return $response;
            } catch(\Exception $e) {
                Log::error($e->getMessage());
                return false;
            }
        }
    }

    public function generateSummary(string $content, string $language = 'English', string $generation_mode = 'offline')
    {
        if (!$this->aiSetting) {
            throw new \Exception('No active AI settings found');
        }

        $online = ($generation_mode === 'online');
        
        $template = $this->getPromptTemplate('summary_generation', [
            'content' => $content,
            'language' => $language
        ]);

        $messages = [
            ['role' => 'user', 'content' => 'You are a professional endingsummary generator in '.$language.' Spoken Language.'],
            ['role' => 'user', 'content' => $template ?: "
                Generate a detailed and reach ending summary in $language for the following content: '$content'
                mention the main points and important information in the content and make it more readable and understandable
                summary should be maximum 300 and minimum 200 words in $language
                DON'T EXPLAIN YOURSELF
            "]
        ];

        try {
            return $this->geminiService->chatCompletion(
                $messages,
                self::GEMINI_PRO,
                [
                    'online' => $online,
                    'temperature' => $this->aiSetting->temperature,
                    'max_tokens' => $this->aiSetting->max_tokens,
                ]
            );
        } catch(\Exception $e) {
            try {
                sleep(5);
                return $this->openRouterService->chatCompletion(
                    $messages,
                    self::GPT_4O,
                    [
                        'temperature' => $this->aiSetting->temperature,
                        'max_tokens' => $this->aiSetting->max_tokens,
                    ]
                );
            } catch(\Exception $e) {
                Log::error($e->getMessage());
                return false;
            }
        }
    }

    public function generateFAQ(string $content, int $count, string $language = 'English', string $generation_mode = 'offline')
    {
        if (!$this->aiSetting) {
            throw new \Exception('No active AI settings found');
        }

        $online = ($generation_mode === 'online');
        
        $template = $this->getPromptTemplate('faq_generation', [
            'content' => $content,
            'count' => $count,
            'language' => $language
        ]);

        $messages = [
            ['role' => 'user', 'content' => 'You are a professional FAQ generator in '.$language.' Spoken Language.'],
            ['role' => 'user', 'content' => $template ?: "
                Generate a detailed and reach faq list in $language for the following content: '$content'
                mention the main points and important information in the content and make it more readable and understandable
                Number of faq list should be exactly $count
                Language should be $language
                DON'T EXPLAIN YOURSELF
                output should be in json format like this:
                ```
                [
                    {
                        'question': '',
                        'answer': ''
                    },
                    ...
                ]
                ```"]
        ];

        try {
            return $this->geminiService->chatCompletion(
                $messages,
                self::GEMINI_PRO,
                [
                    'online' => $online,
                    'json' => true,
                    'temperature' => $this->aiSetting->temperature,
                    'max_tokens' => $this->aiSetting->max_tokens,
                ]
            );
        } catch(\Exception $e) {
            Log::error($e->getMessage());
            try {
                sleep(5);
                return $this->openRouterService->chatCompletion(
                    $messages,
                    self::GPT_4O,
                    [
                        'temperature' => $this->aiSetting->temperature,
                        'max_tokens' => $this->aiSetting->max_tokens,
                    ]
                );
            } catch(\Exception $e) {
                Log::error($e->getMessage());
                return false;
            }
        }
    }

    public function generateMeta($post, string $language = 'English', string $generation_mode = 'offline')
    {
        if (!$this->aiSetting) {
            throw new \Exception('No active AI settings found');
        }

        $online = ($generation_mode === 'online');
        
        $template = $this->getPromptTemplate('meta_generation', [
            'title' => $post->title,
            'content' => $post->content,
            'language' => $language,
            'url' => 'https://pishkhanak.com/'.$post->slug,
            'site_name' => config('app.name')
        ]);

        $messages = [
            ['role' => 'user', 'content' => 'You are a professional meta generator in '.$language.'. Generate concise, SEO-optimized meta tags with strict length limits.'],
            ['role' => 'user', 'content' => $template ?: "
                Generate concise meta tags in $language for: '$post->title'
                URL: 'https://pishkhanak.com/".$post->slug."'
                Site: '".config('app.name')."'
                
                STRICT LENGTH LIMITS:
                - title: max 60 characters
                - og_title: max 60 characters  
                - twitter_title: max 60 characters
                - keywords: max 200 characters (comma-separated)
                - description: max 160 characters
                - og_description: max 160 characters
                - twitter_description: max 160 characters
                
                Make content engaging but concise. Focus on main keywords and value proposition.
                
                Output JSON format:
                ```
                {
                    'title' => '',
                    'description' => '',
                    'keywords' => '',
                    'canonical_url' => '',
                    'robots' => '',
                    'og_title' => '',
                    'og_description' => '',
                    'og_type' => '',
                    'og_url' => '',
                    'twitter_title' => '',
                    'twitter_description' => '',
                    'twitter_card' => '',
                }
                ```
                DON'T EXPLAIN YOURSELF
                DON'T WRITE ANYTHING BEFORE OR AFTER THE JSON OUTPUT
            "]
        ];

        try {
            $response = $this->geminiService->chatCompletion(
                $messages,
                self::GEMINI_PRO,
                [
                    'online' => $online,
                    'temperature' => $this->aiSetting->temperature,
                    'max_tokens' => $this->aiSetting->max_tokens,
                ]
            );
            $response = json_decode($this->cleanJson($response), true);
            if(!is_array($response)){
                throw new \Exception('Invalid response format');
            }
            
            // Apply length limitations to ensure database compatibility
            $response = $this->validateAndTrimMeta($response);
            
            return $response;
        } catch(\Exception $e) {
            Log::error($e->getMessage());
            try {
                sleep(5);
                $response = $this->openRouterService->chatCompletion(
                    $messages,
                    self::GPT_4O,
                    [
                        'temperature' => $this->aiSetting->temperature,
                        'max_tokens' => $this->aiSetting->max_tokens,
                    ]
                );
                $response = json_decode($this->cleanJson($response), true);
                if(!is_array($response)){
                    throw new \Exception('Invalid response format');
                }
                
                // Apply length limitations to ensure database compatibility
                $response = $this->validateAndTrimMeta($response);
                
                return $response;
            } catch(\Exception $e) {
                Log::error($e->getMessage());
                return false;
            }
        }
    }

    public function cleanJson($jsonString)
    {
        $jsonString = str_replace('```', '', $jsonString);
        $jsonString = str_replace('json', '', $jsonString);
        $jsonString = str_replace('\n', '', $jsonString);
        $jsonString = str_replace("\n", '', $jsonString);
        $jsonString = preg_replace('/\r\n|\r|\n/', '', $jsonString);
        if(strpos($jsonString, '{') !== false){
            $jsonString = explode('{', $jsonString);
            array_shift($jsonString);
            $jsonString = '{' . implode('{' , $jsonString);
        } else {
            return false;
        }
        return $jsonString;
    }

    /**
     * Validate and trim meta fields to ensure database compatibility
     */
    protected function validateAndTrimMeta($meta)
    {
        $limits = [
            'title' => 60,
            'og_title' => 60,
            'twitter_title' => 60,
            'keywords' => 200,
            'description' => 160,
            'og_description' => 160,
            'twitter_description' => 160
        ];

        foreach ($limits as $field => $maxLength) {
            if (isset($meta[$field]) && !empty($meta[$field])) {
                $value = trim($meta[$field]);
                if (strlen($value) > $maxLength) {
                    $meta[$field] = substr($value, 0, $maxLength - 3) . '...';
                } else {
                    $meta[$field] = $value;
                }
            }
        }

        return $meta;
    }

    // Add cache clear method
    public function clearSettingsCache(): void
    {
        Cache::forget('active_ai_setting');
    }

    public function setTimeout(int $seconds): self
    {
        $this->timeout = $seconds;
        return $this;
    }

    protected function validateModelType(string $model_type): string
    {
        if (!isset($this->model_types[$model_type])) {
            Log::warning("Invalid model type: {$model_type}, falling back to 'fast'");
            return 'fast';
        }
        return $model_type;
    }

    /**
     * Generate text content using AI
     */
    public function generateText(string $prompt, string $language = 'English', string $model_type = 'fast'): string
    {
        if (!$this->aiSetting) {
            throw new \Exception('No active AI settings found');
        }

        $model_type = $this->validateModelType($model_type);
        
        $messages = [
            ['role' => 'user', 'content' => 'You are a helpful AI assistant. Respond in ' . $language . ' language.'],
            ['role' => 'user', 'content' => $prompt]
        ];

        try {
            return $this->geminiService->chatCompletion(
                $messages,
                self::GEMINI_FLASH,
                [
                    'temperature' => $this->aiSetting->temperature,
                    'max_tokens' => 500,
                ]
            );
        } catch(\Exception $e) {
            Log::error('Text generation failed: ' . $e->getMessage());
            try {
                sleep(2);
                return $this->openRouterService->chatCompletion(
                    $messages,
                    self::GPT_4O_MINI,
                    [
                        'temperature' => $this->aiSetting->temperature,
                        'max_tokens' => 500,
                    ]
                );
            } catch(\Exception $e) {
                Log::error('Text generation failed with fallback: ' . $e->getMessage());
                throw $e;
            }
        }
    }

    /**
     * Generate image suggestions for content
     */
    public function generateImageSuggestions(string $content, string $language = 'English'): array
    {
        // Placeholder implementation
        // This would typically generate AI-powered image suggestions
        return [
            'suggestions' => [
                'Featured image based on main topic',
                'Infographic showing key statistics',
                'Before/after comparison images',
                'Step-by-step process illustrations'
            ],
            'alt_texts' => [
                'Descriptive alt text for main image',
                'Chart showing data trends',
                'Visual comparison diagram',
                'Process flow illustration'
            ]
        ];
    }

    /**
     * Predict content performance metrics
     */
    public function predictContentPerformance(string $content, array $metadata = []): array
    {
        // Placeholder implementation
        // This would typically use AI to predict engagement metrics
        return [
            'readability_score' => rand(70, 95),
            'seo_score' => rand(75, 90),
            'engagement_prediction' => rand(60, 85),
            'estimated_reading_time' => ceil(str_word_count($content) / 200),
            'recommendations' => [
                'Consider adding more subheadings for better readability',
                'Include relevant keywords for SEO optimization',
                'Add call-to-action sections to improve engagement'
            ]
        ];
    }

    /**
     * Generate beginning/introduction content
     */
    public function generateBeginning(string $content, string $language = 'English', string $generation_mode = 'offline')
    {
        if (!$this->aiSetting) {
            throw new \Exception('No active AI settings found');
        }

        $online = ($generation_mode === 'online');
        
        $template = $this->getPromptTemplate('beginning_generation', [
            'content' => $content,
            'language' => $language
        ]);

        $messages = [
            ['role' => 'user', 'content' => 'You are a professional content beginning/introduction generator in '.$language.' Spoken Language.'],
            ['role' => 'user', 'content' => $template ?: "
                Generate a compelling introduction/beginning in $language for the following content: '$content'
                
                Requirements:
                - Should be engaging and hook the reader
                - Maximum 150 words
                - Should introduce the main topic clearly
                - Written in $language
                
                DON'T EXPLAIN YOURSELF
                Return only the introduction text, no formatting or extra text.
            "]
        ];

        try {
            return $this->geminiService->chatCompletion(
                $messages,
                self::GEMINI_FLASH,
                [
                    'online' => $online,
                    'temperature' => $this->aiSetting->temperature,
                    'max_tokens' => 300,
                ]
            );
        } catch(\Exception $e) {
            Log::error('Beginning generation failed: ' . $e->getMessage());
            try {
                sleep(2);
                return $this->openRouterService->chatCompletion(
                    $messages,
                    self::GPT_4O_MINI,
                    [
                        'temperature' => $this->aiSetting->temperature,
                        'max_tokens' => 300,
                    ]
                );
            } catch(\Exception $e) {
                Log::error('Beginning generation failed with fallback: ' . $e->getMessage());
                return false;
            }
        }
    }

    /**
     * Generate schema markup
     */
    public function generateSchema(AiContent $aiContent)
    {
        if (!$this->aiSetting) {
            throw new \Exception('No active AI settings found');
        }

        // Generate basic article schema
        $schema = [
            "@context" => "https://schema.org",
            "@type" => "Article",
            "headline" => $aiContent->title,
            "description" => $aiContent->short_description,
            "author" => [
                "@type" => "Organization",
                "name" => config('app.name', 'Pishkhanak')
            ],
            "publisher" => [
                "@type" => "Organization",
                "name" => config('app.name', 'Pishkhanak'),
                "logo" => [
                    "@type" => "ImageObject",
                    "url" => asset('assets/logo.png')
                ]
            ],
            "datePublished" => $aiContent->created_at->toISOString(),
            "dateModified" => $aiContent->updated_at->toISOString(),
            "mainEntityOfPage" => [
                "@type" => "WebPage",
                "@id" => url('/' . $aiContent->slug)
            ],
            "articleBody" => strip_tags($aiContent->generateUnifiedHtml()),
            "wordCount" => str_word_count(strip_tags($aiContent->generateUnifiedHtml())),
            "inLanguage" => $aiContent->language === 'Persian' ? 'fa' : 'en'
        ];

        // Add FAQ schema if FAQs exist
        if (!empty($aiContent->faq)) {
            $schema["mainEntity"] = [
                "@type" => "FAQPage",
                "mainEntity" => array_map(function($faq) {
                    return [
                        "@type" => "Question",
                        "name" => $faq['question'],
                        "acceptedAnswer" => [
                            "@type" => "Answer",
                            "text" => $faq['answer']
                        ]
                    ];
                }, $aiContent->faq)
            ];
        }

        return $schema;
    }

    /**
     * Get all active services for internal linking
     */
    private function getServicesForInternalLinking()
    {
        try {
            // Get all active services
            $services = \App\Models\Service::where('status', 'active')
                ->whereNull('parent_id') // Only main services, not sub-services
                ->select('id', 'title', 'slug')
                ->limit(50) // Limit to prevent overwhelming the AI
                ->get();
            
            $servicesData = [];
            
            foreach ($services as $service) {
                $servicesData[] = [
                    'title' => $service->title,
                    'url' => '/services/' . $service->slug
                ];
            }
            
            // Also get some AI content pages if they exist
            $aiContents = \App\Models\AiContent::where('status', 'completed')
                ->select('id', 'title', 'slug')
                ->limit(20)
                ->get();
            
            foreach ($aiContents as $content) {
                $servicesData[] = [
                    'title' => $content->title,
                    'url' => '/blog/' . $content->slug
                ];
            }
            
            // Shuffle to provide variety
            shuffle($servicesData);
            
            return $servicesData;
            
        } catch (\Exception $e) {
            Log::error('Failed to get services for internal linking: ' . $e->getMessage());
            return [];
        }
    }
}