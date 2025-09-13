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

class GenerateFAQJob implements ShouldQueue
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
            Log::info('Starting FAQ generation job', [
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
                    88,
                    'faq_generation',
                    'در حال تولید سوالات متداول...'
                );
            }
            
            // Update database progress
            $aiContent->update(['generation_progress' => 88]);

            // Combine sections for FAQ generation with UTF-8 cleaning
            $contentForFAQ = mb_convert_encoding($aiContent->title, 'UTF-8', 'UTF-8') . "\n\n"705af6f0"\n\n";
                }
            }

            if (is_array($aiContent->ai_sections)) {
                foreach ($aiContent->ai_sections as $index => $section) {
                    if (!empty($section)) {
                        // Handle both old format (string) and new format (array with content/heading)
                        $sectionText = '';
                        $heading = '';
                        
                        if (is_array($section)) {
                            // New format: section is array with 'content' and 'heading' keys
                            if (isset($section['content'])) {
                                $cleanContent = mb_convert_encoding($section['content'], 'UTF-8', 'UTF-8');
                                $sectionText = strip_tags($cleanContent);
                            }
                            if (isset($section['heading'])) {
                                // Extract heading from section itself
                                $h = $section['heading'];
                                if (is_array($h) && isset($h['title'])) {
                                    $heading = $h['title'];
                                } elseif (is_string($h)) {
                                    $heading = $h;
                                }
                            }
                        } elseif (is_string($section)) {
                            // Old format: section is HTML string
                            $cleanSection = mb_convert_encoding($section, 'UTF-8', 'UTF-8');
                            $sectionText = strip_tags($cleanSection);
                            
                            // Get heading from ai_headings array
                            if (is_array($aiContent->ai_headings) && isset($aiContent->ai_headings[$index])) {
                                $h = $aiContent->ai_headings[$index];
                                if (is_array($h)) {
                                    if (isset($h['title'])) {
                                        $heading = $h['title'];
                                    } else {
                                        $heading = is_array($h) ? reset($h) : (string)$h;
                                    }
                                } else {
                                    $heading = (string)$h;
                                }
                            }
                        }
                        
                        if ($sectionText) {
                            $contentForFAQ .= "{$heading}: {$sectionText}\n\n";
                        }
                    }
                }
            }

            // Generate FAQ (method signature: content, count, language, generation_mode)
            // Generate between 10-15 FAQs based on content length
            $faqCount = min(15, max(10, intval(strlen($contentForFAQ) / 1000))); // Scale with content, reduced count
            
            Log::info('Starting FAQ generation', [
                'ai_content_id' => $this->aiContentId,
                'faq_count_requested' => $faqCount,
                'content_length' => strlen($contentForFAQ),
                'language' => $aiContent->language ?? 'fa'
            ]);
            
            $faqJson = $aiService->generateFAQ(
                $contentForFAQ, // Use ALL content, no substr limit!
                $faqCount, // Dynamic FAQ count (15-30)
                $aiContent->language ?? 'fa',
                'offline' // generation_mode
            );

            // Check if FAQ generation failed
            if ($faqJson === false || empty($faqJson)) {
                Log::warning('FAQ generation returned empty or false, retrying with shorter content', [
                    'ai_content_id' => $this->aiContentId
                ]);
                
                // Retry with shorter content but still aim for 15 FAQs minimum
                $shortContent = $aiContent->title . "\n\n" . 
                               substr($aiContent->short_description ?? '', 0, 1000) . "\n\n" .
                               substr(strip_tags(is_array($aiContent->ai_sections[0]) && isset($aiContent->ai_sections[0]['content']) 
                                   ? $aiContent->ai_sections[0]['content'] 
                                   : ($aiContent->ai_sections[0] ?? '')), 0, 2000);
                               
                $faqJson = $aiService->generateFAQ(
                    $shortContent,
                    15, // Minimum 15 FAQs even on retry
                    $aiContent->language ?? 'fa',
                    'offline'
                );
            }

            // Parse FAQ JSON
            if ($faqJson && $faqJson !== false) {
                Log::info('FAQ generation returned data', [
                    'ai_content_id' => $this->aiContentId,
                    'raw_response_length' => strlen($faqJson),
                    'raw_response_preview' => substr($faqJson, 0, 500)
                ]);
                
                $cleanedJson = $aiService->cleanJson($faqJson);
                
                Log::info('After cleanJson', [
                    'ai_content_id' => $this->aiContentId,
                    'cleaned_json_length' => strlen($cleanedJson),
                    'cleaned_json_preview' => substr($cleanedJson, 0, 500)
                ]);
                
                $faqData = json_decode($cleanedJson, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($faqData)) {
                    $aiContent->faq = $faqData;
                    Log::info('FAQ parsed successfully', [
                        'ai_content_id' => $this->aiContentId,
                        'faq_count' => count($faqData)
                    ]);
                } else {
                    $aiContent->faq = [];
                    Log::warning('FAQ JSON parsing failed', [
                        'ai_content_id' => $this->aiContentId,
                        'json_error' => json_last_error_msg()
                    ]);
                }
            } else {
                $aiContent->faq = [];
                Log::error('FAQ generation completely failed', [
                    'ai_content_id' => $this->aiContentId
                ]);
            }

            // Mark content as completed (use valid enum value)
            $aiContent->status = 'completed';
            $aiContent->generation_completed_at = now();
            $aiContent->save();

            Log::info('FAQ generated successfully, content completed', [
                'ai_content_id' => $this->aiContentId,
                'faq_count' => count($aiContent->faq ?? [])
            ]);

            // Update Redis progress - COMPLETED
            if ($this->session_hash) {
                $progressService->updateProgress(
                    $this->session_hash,
                    100,
                    'completed',
                    'سوالات متداول با موفقیت تولید شد'
                );
            }
            
            // Update database progress to 100%
            $aiContent->update(['generation_progress' => 100]);
            
            // Embed images in content if they exist
            if (!empty($aiContent->ai_thumbnails) && is_array($aiContent->ai_thumbnails)) {
                Log::info('Dispatching image embedding job', [
                    'ai_content_id' => $this->aiContentId,
                    'thumbnails_count' => count($aiContent->ai_thumbnails)
                ]);
                \App\Jobs\EmbedImagesInContentJob::dispatch($this->aiContentId)
                    ->onQueue('ai-content')
                    ->delay(now()->addSeconds(2));
            }

            // Send notification if needed
            $this->sendCompletionNotification($aiContent);

        } catch (\Exception $e) {
            Log::error('FAQ generation job failed', [
                'ai_content_id' => $this->aiContentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Update Redis with error but mark as complete anyway
            if ($this->session_hash) {
                $progressService->updateProgress(
                    $this->session_hash,
                    100,
                    'completed_with_errors',
                    'تولید سوالات متداول با خطا مواجه شد'
                );
            }

            // Mark content as completed even with errors (use valid enum value)
            $aiContent = AiContent::find($this->aiContentId);
            if ($aiContent) {
                $aiContent->status = 'completed'; // Valid enum value
                if (!is_array($aiContent->faq)) {
                    $aiContent->faq = [];
                }
                $aiContent->generation_completed_at = now();
                $aiContent->save();
            }

            // Don't rethrow - process is complete
        }
    }

    /**
     * Send completion notification
     */
    protected function sendCompletionNotification($aiContent)
    {
        try {
            // You can add email or other notifications here
            Log::info('AI content generation completed', [
                'ai_content_id' => $aiContent->id,
                'title' => $aiContent->title,
                'user_id' => $aiContent->user_id
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send completion notification', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception)
    {
        Log::error('FAQ generation job completely failed', [
            'ai_content_id' => $this->aiContentId,
            'error' => $exception->getMessage()
        ]);

        // Mark as completed anyway (use valid enum value)
        try {
            $aiContent = AiContent::find($this->aiContentId);
            if ($aiContent) {
                $aiContent->status = 'completed'; // Valid enum value
                if (!is_array($aiContent->faq)) {
                    $aiContent->faq = [];
                }
                $aiContent->generation_completed_at = now();
                $aiContent->save();
            }
        } catch (\Exception $e) {
            Log::error('Failed to update content status in failed handler', [
                'ai_content_id' => $this->aiContentId,
                'error' => $e->getMessage()
            ]);
        }

        // Update Redis
        if ($this->session_hash) {
            $progressService = app(AiContentProgressService::class);
            $progressService->updateProgress(
                $this->session_hash,
                100,
                'completed_with_errors',
                'خطا در تولید سوالات متداول'
            );
        }
    }
}