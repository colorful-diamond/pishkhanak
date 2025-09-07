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

class GenerateSummaryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes for summary
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
            Log::info('Starting summary generation job', [
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
                    65,
                    'summary_generation',
                    'PERSIAN_TEXT_a1942c58'
                );
            }
            
            // Update database progress
            $aiContent->update(['generation_progress' => 65]);

            // Combine all sections into content for summary
            $fullContent = '';
            if (is_array($aiContent->ai_sections)) {
                $sections = array_filter($aiContent->ai_sections ?? []);
                if (empty($sections)) {
                    Log::warning('No sections available for summary, using title and description', [
                        'ai_content_id' => $this->aiContentId
                    ]);
                    $fullContent = $aiContent->title . "\n\n" . ($aiContent->short_description ?? '');
                } else {
                    foreach ($aiContent->ai_sections as $index => $section) {
                        if (!empty($section)) {
                            // Section is HTML, extract text content
                            $sectionText = strip_tags($section);
                            
                            // Get heading - it's a complex structure with title and sub_headlines
                            $heading = '';
                            if (is_array($aiContent->ai_headings) && isset($aiContent->ai_headings[$index])) {
                                $h = $aiContent->ai_headings[$index];
                                if (is_array($h)) {
                                    // Extract main title from the heading structure
                                    if (isset($h['title'])) {
                                        $heading = $h['title'];
                                    } else {
                                        // Fallback to first value if no 'title' key
                                        $heading = is_array($h) ? reset($h) : (string)$h;
                                    }
                                } else {
                                    $heading = (string)$h;
                                }
                            }
                            
                            $fullContent .= "## {$heading}\n\n{$sectionText}\n\n";
                        }
                    }
                }
            } else {
                // If sections is not an array, use title and description
                $fullContent = $aiContent->title . "\n\n" . ($aiContent->short_description ?? '');
            }

            // Generate summary
            if (!empty($fullContent)) {
                $summary = $aiService->generateSummary(
                    $fullContent,
                    $aiContent->language ?? 'fa',
                    $aiContent->model_type ?? 'gpt-4'
                );

                // Update AI content with summary
                $aiContent->ai_summary = $summary;
                $aiContent->save();

                Log::info('Summary generated successfully', [
                    'ai_content_id' => $this->aiContentId,
                    'summary_length' => strlen($summary)
                ]);

                // Update Redis progress
                if ($this->session_hash) {
                    $progressService->updateProgress(
                        $this->session_hash,
                        75,
                        'summary_completed',
                        'PERSIAN_TEXT_d7fdf66c'
                    );
                }
                
                // Update database progress
                $aiContent->update(['generation_progress' => 75]);
            }

            // Auto-proceed to next step (Meta generation)
            if ($this->auto_process) {
                GenerateMetaJob::dispatch($this->aiContentId, $this->session_hash, $this->auto_process)
                    ->onQueue('ai-content')
                    ->delay(now()->addSeconds(1));
                    
                Log::info('Auto-proceeding to meta generation', [
                    'ai_content_id' => $this->aiContentId
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Summary generation job failed', [
                'ai_content_id' => $this->aiContentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Update Redis with error
            if ($this->session_hash) {
                $progressService->updateProgress(
                    $this->session_hash,
                    75,
                    'summary_failed',
                    'PERSIAN_TEXT_850dc3ba'
                );
            }

            // Even if summary fails, continue to next step
            if ($this->auto_process) {
                GenerateMetaJob::dispatch($this->aiContentId, $this->session_hash, $this->auto_process)
                    ->onQueue('ai-content')
                    ->delay(now()->addSeconds(2));
                    
                Log::warning('Summary failed but proceeding to meta generation', [
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
        Log::error('Summary generation job completely failed', [
            'ai_content_id' => $this->aiContentId,
            'error' => $exception->getMessage()
        ]);

        // Even on complete failure, continue to next step
        if ($this->auto_process) {
            GenerateMetaJob::dispatch($this->aiContentId, $this->session_hash, $this->auto_process)
                ->onQueue('ai-content')
                ->delay(now()->addSeconds(3));
        }
    }
}