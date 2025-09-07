<?php

namespace App\Jobs;

use App\Services\AiService;
use Illuminate\Bus\Queueable;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\AiContent;
use Illuminate\Support\Facades\Log;

class GenerateFinalContentJob implements ShouldQueue
{
    use Batchable;
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $ai_content_id;
    protected $action;

    public function __construct($ai_content_id, $action)
    {
        $this->ai_content_id = $ai_content_id;
        $this->action = $action;
    }

    public function handle()
    {
        try {
            $aiContent = AiContent::find($this->ai_content_id);
            
            if (!$aiContent) {
                Log::error("AiContent not found with ID: {$this->ai_content_id}");
                return;
            }
            
            switch($this->action){
                case 'summary':
                    $this->generateSummary($aiContent);
                    break;
                case 'beginning':
                    $this->generateBeginning($aiContent);
                    break;
                case 'schema':
                    $this->generateSchema($aiContent);
                    break;
                case 'faqs':
                    $this->generateFAQ($aiContent);
                    break;
                case 'meta':
                    $this->generateMeta($aiContent);
                    break;
                default:
                    Log::warning("Unknown action: {$this->action}");
                    break;
            }
        } catch (\Exception $e) {
            Log::error("Error in GenerateFinalContentJob: " . $e->getMessage(), [
                'ai_content_id' => $this->ai_content_id,
                'action' => $this->action,
                'error' => $e->getTraceAsString()
            ]);
        }
    }

    public function generateSummary(AiContent $aiContent)
    {
        $aiService = app(AiService::class);
        $content = $aiContent->generateUnifiedHtml();
        $summary = $aiService->generateSummary($content, $aiContent->language);
        $aiContent->ai_summary = $summary;
        $aiContent->save();
    }

    public function generateBeginning(AiContent $aiContent)
    {
        $aiService = app(AiService::class);
        $content = $aiContent->generateUnifiedHtml();
        $beginning = $aiService->generateBeginning($content, $aiContent->language);
        $aiContent->short_description = $beginning;
        $aiContent->save();
    }

    public function generateFAQ(AiContent $aiContent)
    {
        $aiService = app(AiService::class);
        $content = $aiContent->generateUnifiedHtml();
        $faqs = $aiService->generateFAQ($content, 5, $aiContent->language);
        $aiContent->faq = is_string($faqs) ? json_decode($faqs, true) : $faqs;
        $aiContent->save();
    }

    public function generateSchema(AiContent $aiContent)
    {
        $aiService = app(AiService::class);
        $schema = $aiService->generateSchema($aiContent);
        $aiContent->schema = $schema;
        $aiContent->save();
    }

    public function generateMeta(AiContent $aiContent)
    {
        $aiService = app(AiService::class);
        $meta = $aiService->generateMeta($aiContent, $aiContent->language);
        
        if (is_array($meta)) {
            $aiContent->meta_title = $meta['title'] ?? $aiContent->title;
            $aiContent->meta_description = $meta['description'] ?? $aiContent->short_description;
            $aiContent->meta_keywords = $meta['keywords'] ?? '';
            $aiContent->og_title = $meta['og_title'] ?? $aiContent->title;
            $aiContent->og_description = $meta['og_description'] ?? $aiContent->short_description;
            $aiContent->twitter_title = $meta['twitter_title'] ?? $aiContent->title;
            $aiContent->twitter_description = $meta['twitter_description'] ?? $aiContent->short_description;
            $aiContent->save();
        }
        
        Log::info('Meta generated for AiContent: ' . $aiContent->id, ['meta' => $meta]);
    }
}
