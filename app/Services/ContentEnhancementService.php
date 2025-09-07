<?php

namespace App\Services;

use App\Models\AiContent;
use App\Services\AiService;
use App\Services\GeminiService;
use App\Services\OpenRouterService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use App\Jobs\GenerateSectionContentJob;
use App\Jobs\GenerateImageJob;
use App\Jobs\GenerateMetaJob;
use App\Jobs\GenerateFAQJob;

class ContentEnhancementService
{
    protected AiService $aiService;
    protected GeminiService $geminiService;
    protected OpenRouterService $openRouterService;
    
    public function __construct()
    {
        $this->aiService = app(AiService::class);
        $this->geminiService = app(GeminiService::class);
        $this->openRouterService = app(OpenRouterService::class);
    }
    
    /**
     * Clean UTF-8 string to fix encoding issues
     */
    protected function cleanUtf8($string)
    {
        if (!$string) return '';
        
        // Remove any non-UTF-8 characters
        $string = mb_convert_encoding($string, 'UTF-8', 'UTF-8');
        
        // Remove invalid UTF-8 sequences
        $string = iconv('UTF-8', 'UTF-8//IGNORE', $string);
        
        // Remove control characters except newlines and tabs
        $string = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $string);
        
        // Trim any remaining issues
        return trim($string);
    }
    
    /**
     * Enhance existing pipeline content with AI
     * This is the main entry point for blog pipeline enhancement
     */
    public function enhancePipelineContent(
        string $title,
        string $existingContent,
        array $metadata = [],
        string $language = 'Persian',
        string $modelType = 'advanced'
    ): array {
        // Clean UTF-8 for all inputs
        $title = $this->cleanUtf8($title);
        $existingContent = $this->cleanUtf8($existingContent);
        
        Log::info('Starting pipeline content enhancement', [
            'title' => $title,
            'content_length' => strlen($existingContent),
            'language' => $language,
            'model' => $modelType
        ]);
        
        try {
            // Simplified approach - single API call for efficiency
            // Build a comprehensive prompt for all enhancements at once
            $enhancementPrompt = $this->buildComprehensiveEnhancementPrompt(
                $title,
                $existingContent,
                $language
            );
            
            Log::info('Calling Gemini API for enhancement', [
                'prompt_length' => strlen($enhancementPrompt),
                'model' => $modelType
            ]);
            
            // Make a single API call with higher token limit
            $response = $this->geminiService->generateContent($enhancementPrompt, $modelType, [
                'max_tokens' => 32000, // Use 32k tokens for now
                'temperature' => 0.7,
                'timeout' => 60 // 60 second timeout
            ]);
            
            if (empty($response)) {
                throw new \Exception('Empty response from Gemini API');
            }
            
            Log::info('Enhancement response received', [
                'response_length' => strlen($response)
            ]);
            
            // Parse the enhanced content
            $enhanced = [
                'title' => $title,
                'enhanced_content' => $response,
                'original_content' => $existingContent
            ];
            
            // Generate meta, schema, FAQ in parallel or skip for now
            $enhanced['meta'] = [
                'meta_title' => substr($title, 0, 60),
                'meta_description' => substr(strip_tags($response), 0, 160),
                'meta_keywords' => ''
            ];
            
            $enhanced['schema'] = [
                '@context' => 'https://schema.org',
                '@type' => 'Article',
                'headline' => $title,
                'datePublished' => date('c')
            ];
            
            $enhanced['faq'] = [];
            $enhanced['images'] = $this->planImageGeneration($response, []);
            
            return [
                'success' => true,
                'original_content' => $existingContent,
                'enhanced_content' => $enhanced,
                'analysis' => [],
                'gaps' => [],
                'plan' => []
            ];
            
        } catch (\Exception $e) {
            Log::error('Content enhancement failed', [
                'error' => $e->getMessage(),
                'title' => $title
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Analyze existing content structure
     */
    protected function analyzeContentStructure(string $content, string $language): array
    {
        $prompt = $language === 'Persian' ? 
            "PERSIAN_TEXT_51fe1ac3" :
            "Analyze the following content and extract its structure:\n\n$content\n\n
            JSON output:
            {
                \"sections_count\": number of sections,
                \"has_introduction\": boolean,
                \"has_conclusion\": boolean,
                \"headings\": [list of headings],
                \"word_count\": word count,
                \"has_images\": boolean,
                \"has_lists\": boolean,
                \"tone\": writing tone,
                \"main_topics\": [main topics],
                \"keywords\": [keywords]
            }";
        
        try {
            $response = $this->geminiService->generateContent($prompt, 'fast', [
                'json' => true,
                'max_tokens' => 2000
            ]);
            
            return json_decode($response, true) ?? [];
        } catch (\Exception $e) {
            Log::error('Content analysis failed: ' . $e->getMessage());
            return [
                'sections_count' => 0,
                'has_introduction' => false,
                'has_conclusion' => false,
                'headings' => [],
                'word_count' => str_word_count($content)
            ];
        }
    }
    
    /**
     * Identify gaps in existing content
     */
    protected function identifyContentGaps(array $analysis, string $content, string $title, string $language): array
    {
        $gaps = [];
        
        // Check structural requirements
        if (($analysis['sections_count'] ?? 0) < 8) {
            $gaps['missing_sections'] = 8 - ($analysis['sections_count'] ?? 0);
        }
        
        if (!($analysis['has_introduction'] ?? false)) {
            $gaps['needs_introduction'] = true;
        }
        
        if (!($analysis['has_conclusion'] ?? false)) {
            $gaps['needs_conclusion'] = true;
        }
        
        if (!($analysis['has_images'] ?? false)) {
            $gaps['needs_images'] = true;
            $gaps['images_needed'] = 16; // 2 per section for 8 sections
        }
        
        // Check for subsections
        $prompt = $language === 'Persian' ?
            "4fcb8792" :
            "For the following content, determine if each section has sufficient subsections (minimum 4):\n\n$content"PERSIAN_TEXT_d33a51d1"Add {$gaps['missing_sections']} new sections to reach 8 total"
            ];
        }
        
        if ($gaps['needs_subsections'] ?? false) {
            $plan['actions'][] = [
                'type' => 'add_subsections',
                'description' => 'Add 4 subsections to each main section'
            ];
        }
        
        if ($gaps['needs_introduction'] ?? false) {
            $plan['actions'][] = [
                'type' => 'add_introduction',
                'description' => 'Create comprehensive introduction'
            ];
        }
        
        if ($gaps['needs_conclusion'] ?? false) {
            $plan['actions'][] = [
                'type' => 'add_conclusion',
                'description' => 'Create compelling conclusion'
            ];
        }
        
        if ($gaps['needs_images'] ?? false) {
            $plan['actions'][] = [
                'type' => 'add_images',
                'count' => $gaps['images_needed'],
                'description' => 'Add 2 images per section'
            ];
        }
        
        $plan['actions'][] = [
            'type' => 'generate_meta',
            'description' => 'Generate SEO meta tags'
        ];
        
        $plan['actions'][] = [
            'type' => 'generate_schema',
            'description' => 'Generate schema markup'
        ];
        
        $plan['actions'][] = [
            'type' => 'generate_faq',
            'count' => $gaps['faq_count'],
            'description' => 'Generate FAQ section'
        ];
        
        return $plan;
    }
    
    /**
     * Execute the enhancement plan
     */
    protected function executeEnhancement(
        string $title,
        string $existingContent,
        array $plan,
        array $analysis,
        string $language,
        string $modelType
    ): array {
        $enhanced = [
            'title' => $title,
            'original_content' => $existingContent,
            'sections' => [],
            'meta' => [],
            'schema' => [],
            'faq' => [],
            'images' => []
        ];
        
        // Create comprehensive enhancement prompt
        $enhancementPrompt = $this->buildEnhancementPrompt(
            $title,
            $existingContent,
            $plan,
            $analysis,
            $language
        );
        
        // Generate enhanced structure with 100k token support
        try {
            $response = $this->geminiService->generateContent($enhancementPrompt, $modelType, [
                'max_tokens' => 100000,
                'temperature' => 0.7
            ]);
            
            // Parse and structure the response
            $enhanced['enhanced_content'] = $response;
            
            // Generate additional components
            $enhanced['meta'] = $this->generateEnhancedMeta($title, $response, $language);
            $enhanced['schema'] = $this->generateEnhancedSchema($title, $response, $language);
            $enhanced['faq'] = $this->generateEnhancedFAQ($title, $response, $language);
            $enhanced['images'] = $this->planImageGeneration($response, $plan);
            
        } catch (\Exception $e) {
            Log::error('Enhancement execution failed: ' . $e->getMessage());
            throw $e;
        }
        
        return $enhanced;
    }
    
    /**
     * Build comprehensive enhancement prompt (simplified version)
     */
    protected function buildComprehensiveEnhancementPrompt(
        string $title,
        string $existingContent,
        string $language
    ): string {
        // Truncate existing content if too long to avoid token limit
        $maxContentLength = 5000;
        if (strlen($existingContent) > $maxContentLength) {
            $existingContent = substr($existingContent, 0, $maxContentLength) . '...';
        }
        
        $prompt = $language === 'Persian' ?
            "f3744e97" :
            
            "Title: $title

Existing content (summary):
$existingContent

Task: Transform this content into a complete 8-section article.

Requirements:
- 8 main sections with H2 headings
- Each section has 4 H3 subsections  
- Each section 600 words
- Clean simple HTML
- Preserve useful existing information

Output complete HTML without extra explanation.";
        
        return $prompt;
    }
    
    /**
     * Build comprehensive enhancement prompt (original complex version - kept for reference)
     */
    protected function buildEnhancementPrompt(
        string $title,
        string $existingContent,
        array $plan,
        array $analysis,
        string $language
    ): string {
        $prompt = $language === 'Persian' ?
            "b491ee44" :
            
            "Title: $title\n\n
            Existing content to enhance:\n
            ================\n
            $existingContent\n
            ================\n\n
            
            Your task: Use this existing content as the primary resource and transform it into a comprehensive, well-structured article.
            
            Mandatory requirements:
            ✅ Preserve all valuable existing content
            ✅ Create exactly 8 main sections (H2)
            ✅ Each section must have 4 subsections (H3)
            ✅ Each section 600-1000 words
            ✅ Each subsection 150-200 words
            ✅ 2 images per section (placeholders)
            ✅ Engaging introduction (200 words)
            ✅ Strong conclusion (200 words)
            
            Required HTML structure:
            [Same structure as Persian]
            
            Important notes:
            - Use existing content as the foundation
            - Integrate existing content into appropriate sections
            - Add new sections to complete the structure
            - Create sufficient subsections for each section
            - Enrich and improve the content";
        
        return $prompt;
    }
    
    /**
     * Generate enhanced meta tags
     */
    protected function generateEnhancedMeta(string $title, string $content, string $language): array
    {
        $prompt = $language === 'Persian'PERSIAN_TEXT_e4943e58'fast', [
                'json' => true,
                'max_tokens' => 2000
            ]);
            
            $faqs = json_decode($response, true);
            return $faqs['faqs'] ?? [];
        } catch (\Exception $e) {
            return [];
        }
    }
    
    /**
     * Plan image generation
     */
    protected function planImageGeneration(string $content, array $plan): array
    {
        $images = [];
        
        // Plan 2 images per section (16 total)
        for ($i = 1; $i <= 8; $i++) {
            $images[] = [
                'section' => $i,
                'position' => 'start',
                'url' => "/images/section-$i-1.jpg"
            ];
            $images[] = [
                'section' => $i,
                'position' => 'end',
                'url' => "/images/section-$i-2.jpg"
            ];
        }
        
        return $images;
    }
}