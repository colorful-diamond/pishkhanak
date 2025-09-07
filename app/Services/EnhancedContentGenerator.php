<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Services\PersianContentPromptBuilder;

class EnhancedContentGenerator
{
    protected AiService $aiService;
    protected GeminiService $geminiService;
    protected ?PersianContentPromptBuilder $persianPromptBuilder = null;
    
    public function __construct()
    {
        $this->aiService = app(AiService::class);
        $this->geminiService = app(GeminiService::class);
        $this->persianPromptBuilder = app(PersianContentPromptBuilder::class);
    }
    
    /**
     * Generate enhanced section content with rich HTML formatting and extensive internal linking
     */
    public function generateEnhancedSectionContent(
        array $heading,
        string $title,
        string $shortDescription,
        int $number,
        int $count,
        string $language = 'Persian',
        string $model_type = 'advanced',
        string $generation_mode = 'offline',
        array $servicesForLinking = [],
        array $usedLinks = [], // Track already used links
        ?array $researchData = null, // Add research data parameter
        array $keywords = [] // Add keywords parameter
    ) {
        $subHeadingsCount = count($heading['sub_headlines']);
        
        // Enhanced section context
        $sectionContext = $this->getSectionContext($number, $count);
        
        // Build the enhanced prompt - use Persian builder for Persian content
        if ($language === 'Persian' && $this->persianPromptBuilder && $researchData) {
            $enhancedPrompt = $this->persianPromptBuilder->buildSectionPrompt(
                $heading,
                $title,
                $shortDescription,
                $number,
                $keywords,
                $researchData
            );
        } else {
            $enhancedPrompt = $this->buildEnhancedPrompt(
                $heading,
                $title,
                $shortDescription,
                $number,
                $subHeadingsCount,
                $language,
                $sectionContext,
                $servicesForLinking
            );
        }
        
        // Select appropriate model
        $modelName = $model_type === 'advanced' ? 'gemini-2.5-pro' : 'gemini-2.5-flash';
        
        // Generate content
        $rawContent = $this->geminiService->generateContent($enhancedPrompt, $modelName);
        
        // Clean up HTML code blocks if any
        $cleanedContent = $this->cleanHtmlCodeBlocks($rawContent);
        
        // Enforce brand mention balance for Persian content
        if ($language === 'Persian') {
            $cleanedContent = $this->enforceBrandBalance($cleanedContent);
        }
        
        Log::info("Enhanced content generated for section {$number}", [
            'section_title' => $heading['title'] ?? $heading['headline'] ?? 'Unknown',
            'content_length' => strlen($cleanedContent),
            'model_used' => $modelName,
            'has_research_data' => !empty($researchData)
        ]);
        
        return $cleanedContent;
    }
    
    /**
     * Clean HTML code blocks from generated content
     */
    private function cleanHtmlCodeBlocks(string $content): string
    {
        // Remove ```html and ``` code block markers
        $content = preg_replace('/```html\s*/', '', $content);
        $content = preg_replace('/```\s*/', '', $content);
        $content = preg_replace('/```[a-z]+\s*/', '', $content);
        
        // Clean up excessive newlines
        $content = preg_replace('/\n\s*\n\s*\n/', "\n\n", $content);
        
        return trim($content);
    }
    
    /**
     * Enforce brand mention balance (3-4 پیشخوانک, 1 pishkhanak.com per section)
     * Optimized for memory efficiency with large Persian text content
     */
    private function enforceBrandBalance(string $content): string
    {
        // Memory optimization: work in chunks for large content
        if (strlen($content) > 10000) {
            return $this->processBrandBalanceInChunks($content);
        }
        
        $pishkhanakCount = substr_count($content, 'پیشخوانک');
        $domainCount = substr_count($content, 'pishkhanak.com');
        
        // If too many brand mentions, reduce them
        if ($pishkhanakCount > 4) {
            // Find last occurrence and replace some with generic terms
            $content = preg_replace('/پیشخوانک/', 'این سامانه', $content, $pishkhanakCount - 4);
        }
        
        // Ensure at least 3 brand mentions
        if ($pishkhanakCount < 3) {
            // Add brand mentions in natural places
            $content = preg_replace(
                '/این سرویس/',
                'سرویس پیشخوانک',
                $content,
                max(0, 3 - $pishkhanakCount)
            );
        }
        
        // Ensure exactly 1 domain mention
        if ($domainCount === 0) {
            // Add domain mention
            $content = preg_replace(
                '/پیشخوانک([^.]*)/',
                'پیشخوانک$1 در وب‌سایت pishkhanak.com',
                $content,
                1
            );
        } elseif ($domainCount > 1) {
            // Remove extra domain mentions
            $content = preg_replace('/pishkhanak\.com/', 'این وب‌سایت', $content, $domainCount - 1);
        }
        
        // Force garbage collection for large content processing
        if (strlen($content) > 5000) {
            gc_collect_cycles();
        }
        
        return $content;
    }
    
    /**
     * Process brand balance in chunks for very large content
     */
    private function processBrandBalanceInChunks(string $content): string
    {
        $chunkSize = 5000;
        $chunks = str_split($content, $chunkSize);
        $processedChunks = [];
        
        foreach ($chunks as $chunk) {
            $processedChunks[] = $this->enforceBrandBalance($chunk);
            // Free memory after each chunk
            unset($chunk);
        }
        
        return implode('', $processedChunks);
    }
    
    /**
     * Get section context for prompts
     */
    private function getSectionContext(int $number, int $count): string
    {
        $position = $number === 1 ? 'ابتدایی' : ($number === $count ? 'پایانی' : 'میانی');
        return "بخش {$number} از {$count} بخش - موقعیت: {$position}";
    }
    
    /**
     * Build enhanced prompt for content generation
     */
    private function buildEnhancedPrompt(
        array $heading,
        string $title,
        string $shortDescription,
        int $number,
        int $subHeadingsCount,
        string $language,
        string $sectionContext,
        array $servicesForLinking
    ): string {
        $headingTitle = $heading['title'] ?? $heading['headline'] ?? '';
        
        $prompt = "
Create comprehensive, customer-friendly HTML content for this section: " . $headingTitle . "

⚠️ CRITICAL CONTENT RULES - MUST FOLLOW:
=====================================
1. Write for EVERYDAY CUSTOMERS, not technical experts
2. NO technical infrastructure, APIs, or system architecture details
3. NO programming, database, or backend implementation details
4. Focus on PRACTICAL information: what the service is, how to get it, who needs it
5. Use simple, clear language that regular people understand
6. Include real benefits and step-by-step guidance

📋 SERVICE INFORMATION:
======================
Service: {$title}
Description: {$shortDescription}
Section: {$number} 
Sub-sections needed: {$subHeadingsCount}
Context: {$sectionContext}

🎯 HTML STRUCTURE TEMPLATE:
==========================
<section class=\"content-section\">
    <h2>" . $headingTitle . "</h2>
    
    <!-- Introduction paragraph with <strong> and <em> tags -->
    <p>Section introduction with <strong>important terms</strong> and <em>emphasized concepts</em>...</p>
    <!-- Real image will be embedded here by EmbedImagesInContentJob -->
    
    <h3>First Sub-section</h3>
    <p>Detailed explanation with practical examples...</p>
    <ul>
        <li>Practical point 1 with real benefits</li>
        <li>Practical point 2 with clear steps</li>
        <li>Practical point 3 with user value</li>
    </ul>
    
    <h3>Second Sub-section</h3>
    <p>More detailed content with <strong>key highlights</strong>...</p>
    
    <!-- Add more h3 sections based on {$subHeadingsCount} -->
    
    <h3>Final Sub-section</h3>
    <p>Content with multiple <strong>formatting</strong> and <em>emphasis</em>...</p>
    
    <!-- Blockquote for key takeaway -->
    <blockquote>
        <p>\"Important quote or key insight that summarizes a crucial point about <strong>" . $headingTitle . "</strong>\"</p>
    </blockquote>
    
    <!-- Continue for all {$subHeadingsCount} subsections -->
    
</section>

⚠️ CRITICAL FORMATTING REQUIREMENTS:
===================================
- Use HTML tags: <h2>, <h3>, <p>, <strong>, <em>, <ul>, <li>, <blockquote>
- NO markdown syntax (no ##, **, *, etc.)
- NO code blocks or ```
- Make content scannable with proper headings
- Include at least 1 blockquote per section
- Use <strong> for important terms (3-5 times per section)
- Use <em> for subtle emphasis (2-3 times per section)
- Create numbered or bulleted lists where helpful

Write {$subHeadingsCount} distinct subsections with practical, customer-focused content.
";
        
        return $prompt;
    }
}