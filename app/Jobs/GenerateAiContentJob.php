<?php

namespace App\Jobs;

use App\Models\AiContent;
use App\Services\AiService;
use App\Services\AiContentProgressService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GenerateAiContentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 1200; // 20 minutes timeout (reduced from 1 hour to prevent duplicates)
    public $tries = 1; // Only try once to prevent duplicate content generation
    public $backoff = [60]; // Single backoff value since we only try once

    protected $aiContentId;
    protected $step;
    protected $settings;

    /**
     * Create a new job instance.
     */
    public function __construct($aiContentId, $step = 'headings', $settings = [])
    {
        $this->aiContentId = $aiContentId;
        $this->step = $step;
        $this->settings = $settings;
        $this->onQueue('ai-content'); // Use dedicated queue
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $aiContent = AiContent::find($this->aiContentId);
        
        if (!$aiContent) {
            Log::error('AI Content not found for background generation', [
                'content_id' => $this->aiContentId
            ]);
            return;
        }

        // Update job status
        $aiContent->update([
            'job_status' => 'processing',
            'current_generation_step' => $this->step,
            'last_job_run_at' => now()
        ]);

        try {
            switch ($this->step) {
                case 'headings':
                    $this->generateHeadings($aiContent);
                    // Chain next job
                    GenerateAiContentJob::dispatch($this->aiContentId, 'sections', $this->settings)
                        ->delay(now()->addSeconds(5));
                    break;
                    
                case 'sections':
                    $this->generateSections($aiContent);
                    // Chain next job
                    GenerateAiContentJob::dispatch($this->aiContentId, 'summary', $this->settings)
                        ->delay(now()->addSeconds(5));
                    break;
                    
                case 'summary':
                    $this->generateSummary($aiContent);
                    // Chain next job
                    GenerateAiContentJob::dispatch($this->aiContentId, 'meta', $this->settings)
                        ->delay(now()->addSeconds(5));
                    break;
                    
                case 'meta':
                    $this->generateMetaAndSchema($aiContent);
                    // Chain next job
                    GenerateAiContentJob::dispatch($this->aiContentId, 'faq', $this->settings)
                        ->delay(now()->addSeconds(5));
                    break;
                    
                case 'faq':
                    $this->generateFAQ($aiContent);
                    // Mark as complete
                    $this->completeGeneration($aiContent);
                    break;
                    
                default:
                    Log::warning('Unknown generation step', [
                        'step' => $this->step,
                        'content_id' => $this->aiContentId
                    ]);
            }
        } catch (\Exception $e) {
            Log::error('Background AI content generation failed', [
                'content_id' => $this->aiContentId,
                'step' => $this->step,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $aiContent->update([
                'status' => 'failed',
                'job_status' => 'failed',
                'error_message' => $e->getMessage(),
                'failed_at' => now()
            ]);
            
            // Notify user if needed
            $this->notifyUserOfFailure($aiContent, $e->getMessage());
            
            throw $e; // Re-throw to trigger retry mechanism
        }
    }

    /**
     * Generate headings for the content
     */
    protected function generateHeadings(AiContent $aiContent)
    {
        Log::info('🤖 Background: Generating headings', [
            'content_id' => $aiContent->id
        ]);

        $aiService = app(AiService::class);
        $settings = $aiContent->generation_settings;
        
        $prompt = $this->buildHeadingsPrompt($aiContent);
        
        $response = $aiService->generateContent($prompt, [
            'model' => $settings['model_type'] ?? 'fast',
            'temperature' => 0.7,
            'max_tokens' => 2000
        ]);
        
        $headings = $this->parseHeadingsResponse($response);
        
        $aiContent->update([
            'ai_headings' => $headings,
            'generation_progress' => 20,
            'headings_generated_at' => now()
        ]);
        
        Log::info('✅ Background: Headings generated', [
            'content_id' => $aiContent->id,
            'headings_count' => count($headings)
        ]);
    }

    /**
     * Generate sections for the content
     */
    protected function generateSections(AiContent $aiContent)
    {
        Log::info('🤖 Background: Generating sections', [
            'content_id' => $aiContent->id
        ]);

        if (!$aiContent->ai_headings || !is_array($aiContent->ai_headings)) {
            throw new \Exception('No headings found for section generation');
        }

        $aiService = app(AiService::class);
        $sections = [];
        $totalHeadings = count($aiContent->ai_headings);
        
        foreach ($aiContent->ai_headings as $index => $heading) {
            $prompt = $this->buildSectionPrompt($aiContent, $heading);
            
            $response = $aiService->generateContent($prompt, [
                'model' => $aiContent->generation_settings['model_type'] ?? 'fast',
                'temperature' => 0.7,
                'max_tokens' => 3000
            ]);
            
            $sections[] = [
                'heading' => $heading,
                'content' => $response,
                'generated_at' => now()
            ];
            
            // Update progress
            $progress = 20 + (($index + 1) / $totalHeadings * 40); // 20-60% range
            $aiContent->update([
                'generation_progress' => $progress,
                'ai_sections' => $sections
            ]);
        }
        
        $aiContent->update([
            'ai_sections' => $sections,
            'generation_progress' => 60,
            'sections_generated_at' => now()
        ]);
        
        Log::info('✅ Background: Sections generated', [
            'content_id' => $aiContent->id,
            'sections_count' => count($sections)
        ]);
    }

    /**
     * Generate summary for the content
     */
    protected function generateSummary(AiContent $aiContent)
    {
        Log::info('🤖 Background: Generating summary', [
            'content_id' => $aiContent->id
        ]);

        $aiService = app(AiService::class);
        $prompt = $this->buildSummaryPrompt($aiContent);
        
        $summary = $aiService->generateContent($prompt, [
            'model' => $aiContent->generation_settings['model_type'] ?? 'fast',
            'temperature' => 0.5,
            'max_tokens' => 1000
        ]);
        
        $aiContent->update([
            'ai_summary' => $summary,
            'generation_progress' => 70,
            'summary_generated_at' => now()
        ]);
        
        Log::info('✅ Background: Summary generated', [
            'content_id' => $aiContent->id
        ]);
    }

    /**
     * Generate meta tags and schema markup
     */
    protected function generateMetaAndSchema(AiContent $aiContent)
    {
        Log::info('🤖 Background: Generating meta and schema', [
            'content_id' => $aiContent->id
        ]);

        $aiService = app(AiService::class);
        $prompt = $this->buildMetaPrompt($aiContent);
        
        $response = $aiService->generateContent($prompt, [
            'model' => $aiContent->generation_settings['model_type'] ?? 'fast',
            'temperature' => 0.3,
            'max_tokens' => 2000
        ]);
        
        $meta = $this->parseMetaResponse($response);
        
        $aiContent->update([
            'ai_meta' => $meta['meta'],
            'ai_schema' => $meta['schema'],
            'generation_progress' => 85,
            'meta_generated_at' => now()
        ]);
        
        Log::info('✅ Background: Meta and schema generated', [
            'content_id' => $aiContent->id
        ]);
    }

    /**
     * Generate FAQ section
     */
    protected function generateFAQ(AiContent $aiContent)
    {
        Log::info('🤖 Background: Generating FAQ', [
            'content_id' => $aiContent->id
        ]);

        $aiService = app(AiService::class);
        $prompt = $this->buildFAQPrompt($aiContent);
        
        $response = $aiService->generateContent($prompt, [
            'model' => $aiContent->generation_settings['model_type'] ?? 'fast',
            'temperature' => 0.6,
            'max_tokens' => 2000
        ]);
        
        $faq = $this->parseFAQResponse($response);
        
        $aiContent->update([
            'ai_faq' => $faq,
            'generation_progress' => 95,
            'faq_generated_at' => now()
        ]);
        
        Log::info('✅ Background: FAQ generated', [
            'content_id' => $aiContent->id,
            'faq_count' => count($faq)
        ]);
    }

    /**
     * Complete the generation process
     */
    protected function completeGeneration(AiContent $aiContent)
    {
        $aiContent->update([
            'status' => 'completed',
            'job_status' => 'completed',
            'generation_progress' => 100,
            'completed_at' => now()
        ]);
        
        // Update target model if needed
        if ($aiContent->model_type && $aiContent->model_id) {
            $this->updateTargetModel($aiContent);
        }
        
        // Notify user
        $this->notifyUserOfCompletion($aiContent);
        
        Log::info('🎉 Background: AI content generation completed', [
            'content_id' => $aiContent->id,
            'total_time' => $aiContent->generation_started_at 
                ? now()->diffInSeconds($aiContent->generation_started_at) . ' seconds'
                : 'unknown'
        ]);
    }

    /**
     * Build prompt for headings generation
     */
    protected function buildHeadingsPrompt(AiContent $aiContent)
    {
        $settings = $aiContent->generation_settings ?? $this->settings ?? [];
        $language = $aiContent->language === 'Persian' ? 'PERSIAN_TEXT_66030b73' : $aiContent->language;
        $headingsNumber = $settings['headings_number'] ?? 8;
        $subHeadingsNumber = $settings['sub_headings_number'] ?? 2;
        
        return "Generate {$headingsNumber} main headings for an article about: {$aiContent->title}
                Description: {$aiContent->short_description}
                Language: {$language}
                Each heading should have {$subHeadingsNumber} sub-headings.
                Format the response as a JSON array with 'title' and 'subheadings' keys."17c1a648"Write a detailed section for the heading: {$heading['title']}
                Context: {$aiContent->title}
                Sub-headings to cover: " . implode(', ', $heading['subheadings'] ?? []) . "
                Language: {$language}
                Tone: {$tone}
                Length: {$length}
                Write comprehensive, informative content."2e96374f"Create a comprehensive summary of this article: {$aiContent->title}
                Based on the following sections: " . json_encode($aiContent->ai_headings) . "
                Language: {$language}
                Make it engaging and informative, 2-3 paragraphs."cd7324f1"Generate SEO meta tags and schema markup for: {$aiContent->title}
                Description: {$aiContent->short_description}
                Language: {$language}
                Include: meta title, meta description, keywords, Open Graph tags, Twitter Card tags, and JSON-LD schema.
                Format as JSON with 'meta' and 'schema' keys."PERSIAN_TEXT_2b60b7cb"Generate 5-7 frequently asked questions with answers about: {$aiContent->title}
                Based on the content: " . substr(json_encode($aiContent->ai_sections), 0, 1000) . "
                Language: {$language}
                Format as JSON array with 'question' and 'answer' keys.";
    }

    /**
     * Parse headings response from AI
     */
    protected function parseHeadingsResponse($response)
    {
        try {
            return json_decode($response, true) ?? [];
        } catch (\Exception $e) {
            Log::warning('Failed to parse headings JSON, using fallback parser', [
                'error' => $e->getMessage()
            ]);
            // Fallback parsing logic
            return $this->fallbackParseHeadings($response);
        }
    }

    /**
     * Parse meta response from AI
     */
    protected function parseMetaResponse($response)
    {
        try {
            $data = json_decode($response, true);
            return [
                'meta' => $data['meta'] ?? [],
                'schema' => $data['schema'] ?? []
            ];
        } catch (\Exception $e) {
            return [
                'meta' => ['description' => $response],
                'schema' => []
            ];
        }
    }

    /**
     * Parse FAQ response from AI
     */
    protected function parseFAQResponse($response)
    {
        try {
            return json_decode($response, true) ?? [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Fallback parser for headings
     */
    protected function fallbackParseHeadings($response)
    {
        $headings = [];
        $lines = explode("\n", $response);
        $currentHeading = null;
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            if (preg_match('/^\d+\./', $line)) {
                if ($currentHeading) {
                    $headings[] = $currentHeading;
                }
                $currentHeading = [
                    'title' => preg_replace('/^\d+\.\s*/', '', $line),
                    'subheadings' => []
                ];
            } elseif ($currentHeading && preg_match('/^-|•/', $line)) {
                $currentHeading['subheadings'][] = preg_replace('/^[-•]\s*/', '', $line);
            }
        }
        
        if ($currentHeading) {
            $headings[] = $currentHeading;
        }
        
        return $headings;
    }

    /**
     * Update the target model with generated content
     */
    protected function updateTargetModel(AiContent $aiContent)
    {
        try {
            $modelClass = "App\\Models\\{$aiContent->model_type}";
            if (class_exists($modelClass)) {
                $model = $modelClass::find($aiContent->model_id);
                if ($model) {
                    // Combine all sections into full content
                    $fullContent = $this->combineContentSections($aiContent);
                    
                    $model->update([
                        'ai_generated_content' => $fullContent,
                        'meta_title' => $aiContent->ai_meta['title'] ?? null,
                        'meta_description' => $aiContent->ai_meta['description'] ?? null,
                        'ai_content_id' => $aiContent->id,
                        'ai_generated_at' => now()
                    ]);
                    
                    Log::info('Target model updated with AI content', [
                        'model' => $aiContent->model_type,
                        'model_id' => $aiContent->model_id
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to update target model', [
                'error' => $e->getMessage(),
                'model' => $aiContent->model_type,
                'model_id' => $aiContent->model_id
            ]);
        }
    }

    /**
     * Combine all content sections into full HTML
     */
    protected function combineContentSections(AiContent $aiContent)
    {
        $html = '';
        
        // Add title
        $html .= "<h1>{$aiContent->title}</h1>\n\n";
        
        // Add summary if exists
        if ($aiContent->ai_summary) {
            $html .= "<div class=\"summary\">{$aiContent->ai_summary}</div>\n\n";
        }
        
        // Add sections
        if ($aiContent->ai_sections && is_array($aiContent->ai_sections)) {
            foreach ($aiContent->ai_sections as $section) {
                $html .= "<h2>{$section['heading']['title']}</h2>\n";
                $html .= "<div class=\"content\">{$section['content']}</div>\n\n";
            }
        }
        
        // Add FAQ if exists
        if ($aiContent->ai_faq && is_array($aiContent->ai_faq)) {
            $html .= "2318b7f5";
            $html .= "<div class=\"faq\">\n";
            foreach ($aiContent->ai_faq as $faq) {
                $html .= "<div class=\"faq-item\">\n";
                $html .= "<h3>{$faq['question']}</h3>\n";
                $html .= "<p>{$faq['answer']}</p>\n";
                $html .= "</div>\n";
            }
            $html .= "</div>\n";
        }
        
        return $html;
    }

    /**
     * Notify user of completion
     */
    protected function notifyUserOfCompletion(AiContent $aiContent)
    {
        // TODO: Send notification via email/SMS/push notification
        Log::info('User notification queued for completion', [
            'content_id' => $aiContent->id,
            'user_id' => $aiContent->author_id
        ]);
    }

    /**
     * Notify user of failure
     */
    protected function notifyUserOfFailure(AiContent $aiContent, $error)
    {
        // TODO: Send notification via email/SMS/push notification
        Log::info('User notification queued for failure', [
            'content_id' => $aiContent->id,
            'user_id' => $aiContent->author_id,
            'error' => $error
        ]);
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception)
    {
        Log::error('AI content generation job failed permanently', [
            'content_id' => $this->aiContentId,
            'step' => $this->step,
            'error' => $exception->getMessage()
        ]);
        
        $aiContent = AiContent::find($this->aiContentId);
        if ($aiContent) {
            $aiContent->update([
                'status' => 'failed',
                'job_status' => 'failed_permanently',
                'error_message' => $exception->getMessage(),
                'failed_at' => now()
            ]);
        }
    }
}