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

class GenerateMetaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes
    public $tries = 3;
    public $maxExceptions = 3;
    public $backoff = [10, 30, 60]; // Exponential backoff

    protected $aiContentId;
    protected $session_hash;
    protected $auto_process;

    /**
     * Create a new job instance.
     */
    public function __construct(int $aiContentId, string $session_hash = null, bool $auto_process = true)
    {
        $this->aiContentId = $aiContentId;
        $this->session_hash = $session_hash;
        $this->auto_process = $auto_process;
    }

    /**
     * Execute the job.
     */
    public function handle(AiService $aiService, AiContentProgressService $progressService)
    {
        try {
            Log::info('Starting meta generation job', [
                'ai_content_id' => $this->aiContentId,
                'session_hash' => $this->session_hash
            ]);

            // Get the AI content
            $aiContent = AiContent::find($this->aiContentId);
            if (!$aiContent) {
                throw new \Exception('AI Content not found');
            }

            // Update Redis progress
            if ($this->session_hash) {
                $progressService->updateProgress(
                    $this->session_hash,
                    78,
                    'meta_generation',
                    'PERSIAN_TEXT_70054cbf'
                );
            }
            
            // Update database progress
            $aiContent->update(['generation_progress' => 78]);

            // Generate meta description
            $contentForMeta = $aiContent->ai_summary ?? $aiContent->short_description ?? $aiContent->title;
            
            // Create a post-like object for generateMeta
            $postLikeObject = new \stdClass();
            $postLikeObject->title = $aiContent->title;
            $postLikeObject->slug = $aiContent->slug;
            
            // Extract text content from HTML sections with proper UTF-8 cleaning
            $fullContent = mb_convert_encoding($aiContent->short_description ?? '', 'UTF-8', 'UTF-8') . "\n\n";
            if (is_array($aiContent->ai_sections)) {
                foreach ($aiContent->ai_sections as $section) {
                    if (!empty($section)) {
                        // Handle both old format (string) and new format (array with content key)
                        if (is_array($section) && isset($section['content'])) {
                            $cleanContent = mb_convert_encoding($section['content'], 'UTF-8', 'UTF-8');
                            $fullContent .= strip_tags($cleanContent) . "\n\n";
                        } elseif (is_string($section)) {
                            $cleanSection = mb_convert_encoding($section, 'UTF-8', 'UTF-8');
                            $fullContent .= strip_tags($cleanSection) . "\n\n";
                        }
                    }
                }
            }
            
            // Ensure content is clean and not empty
            $fullContent = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $fullContent);
            if (strlen($fullContent) < 100) {
                // If content is too short, use title and summary
                $fullContent = $aiContent->title . "\n\n" . ($aiContent->ai_summary ?? $aiContent->short_description ?? '');
            }
            
            $postLikeObject->content = $fullContent;
            
            try {
                // AiService->generateMeta() expects a post-like object
                $metaData = $aiService->generateMeta(
                    $postLikeObject,
                    $aiContent->language ?? 'fa',
                    'offline'
                );
                
                // Extract meta description from the result
                $metaDescription = is_array($metaData) 
                    ? ($metaData['meta_description'] ?? substr($contentForMeta, 0, 160))
                    : substr($contentForMeta, 0, 160);
            } catch (\Exception $e) {
                Log::warning('Meta description generation failed, using fallback', [
                    'error' => $e->getMessage()
                ]);
                $metaDescription = substr($contentForMeta, 0, 160);
            }

            // Process all meta data from the response
            if (isset($metaData) && is_array($metaData)) {
                // Meta title
                $aiContent->meta_title = $metaData['meta_title'] ?? $aiContent->title;
                
                // Meta description 
                $aiContent->meta_description = $metaData['meta_description'] ?? $metaDescription;
                
                // Meta keywords
                $metaKeywords = $metaData['meta_keywords'] ?? '';
                $aiContent->meta_keywords = is_string($metaKeywords) ? $metaKeywords : implode(', ', $metaKeywords);
                
                // Tags (array)
                $aiContent->tags = $metaData['tags'] ?? [];
                
                // SEO tags (array)
                $aiContent->seo_tags = $metaData['seo_tags'] ?? [];
                
                // Open Graph tags
                $aiContent->og_title = $metaData['og_title'] ?? $metaData['meta_title'] ?? $aiContent->title;
                $aiContent->og_description = $metaData['og_description'] ?? $metaData['meta_description'] ?? $metaDescription;
                $aiContent->og_type = $metaData['og_type'] ?? 'article';
                $aiContent->og_url = $metaData['og_url'] ?? $metaData['canonical_url'] ?? 'https://pishkhanak.com/' . $aiContent->slug;
                
                // Twitter tags
                $aiContent->twitter_title = $metaData['twitter_title'] ?? $metaData['meta_title'] ?? $aiContent->title;
                $aiContent->twitter_description = $metaData['twitter_description'] ?? $metaData['meta_description'] ?? $metaDescription;
                $aiContent->twitter_card = $metaData['twitter_card'] ?? 'summary_large_image';
                
                // Canonical and robots
                $aiContent->canonical_url = $metaData['canonical_url'] ?? 'https://pishkhanak.com/' . $aiContent->slug;
                $aiContent->robots = $metaData['robots'] ?? 'index, follow';
                
                Log::info('All meta fields populated', [
                    'ai_content_id' => $this->aiContentId,
                    'meta_title' => $aiContent->meta_title,
                    'tags_count' => count($aiContent->tags ?? []),
                    'seo_tags_count' => count($aiContent->seo_tags ?? [])
                ]);
            } else {
                // Fallback if meta generation failed
                $aiContent->meta_title = $aiContent->title;
                $aiContent->meta_description = $metaDescription;
                $aiContent->meta_keywords = '';
                $aiContent->tags = [];
                $aiContent->seo_tags = [];
                
                Log::warning('Using fallback meta data', [
                    'ai_content_id' => $this->aiContentId
                ]);
            }

            // Generate schema markup using the dedicated method
            try {
                $schemaMarkup = $aiService->generateSchema($aiContent);
                $aiContent->schema = $schemaMarkup;
            } catch (\Exception $e) {
                Log::warning('Schema generation failed', ['error' => $e->getMessage()]);
                $aiContent->schema = null;
            }

            // Save all meta data
            $aiContent->save();

            Log::info('Meta and schema generated successfully', [
                'ai_content_id' => $this->aiContentId,
                'meta_title' => $aiContent->meta_title,
                'meta_description_length' => strlen($aiContent->meta_description),
                'tags_count' => count($aiContent->tags ?? []),
                'seo_tags_count' => count($aiContent->seo_tags ?? [])
            ]);

            // Update Redis progress
            if ($this->session_hash) {
                $progressService->updateProgress(
                    $this->session_hash,
                    85,
                    'meta_completed',
                    'PERSIAN_TEXT_56bdfe5f'
                );
            }
            
            // Update database progress
            $aiContent->update(['generation_progress' => 85]);

            // Auto-proceed to next step (FAQ generation)
            if ($this->auto_process) {
                GenerateFAQJob::dispatch($this->aiContentId, $this->session_hash, $this->auto_process)
                    ->onQueue('ai-content')
                    ->delay(now()->addSeconds(1));
                    
                Log::info('Auto-proceeding to FAQ generation', [
                    'ai_content_id' => $this->aiContentId
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Meta generation job failed', [
                'ai_content_id' => $this->aiContentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Update Redis with error
            if ($this->session_hash) {
                $progressService->updateProgress(
                    $this->session_hash,
                    85,
                    'meta_failed',
                    'PERSIAN_TEXT_58d958ae'
                );
            }

            // Even if meta fails, continue to next step
            if ($this->auto_process) {
                GenerateFAQJob::dispatch($this->aiContentId, $this->session_hash, $this->auto_process)
                    ->onQueue('ai-content')
                    ->delay(now()->addSeconds(2));
                    
                Log::warning('Meta failed but proceeding to FAQ generation', [
                    'ai_content_id' => $this->aiContentId
                ]);
            }

            // Don't rethrow - we want to continue the process
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception)
    {
        Log::error('Meta generation job completely failed', [
            'ai_content_id' => $this->aiContentId,
            'error' => $exception->getMessage()
        ]);

        // Even on complete failure, continue to next step
        if ($this->auto_process) {
            GenerateFAQJob::dispatch($this->aiContentId, $this->session_hash, $this->auto_process)
                ->onQueue('ai-content')
                ->delay(now()->addSeconds(3));
        }
    }
}