<?php

namespace App\Jobs;

use App\Models\AiContent;
use App\Models\Service;
use App\Services\AiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class GenerateServiceAiContentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $aiContent;
    protected $serviceContext;
    protected $data;

    /**
     * Create a new job instance.
     */
    public function __construct(AiContent $aiContent, array $serviceContext, array $data)
    {
        $this->aiContent = $aiContent;
        $this->serviceContext = $serviceContext;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            $aiService = app(AiService::class);
            
            // Generate service-specific headings with custom prompt
            $headingsJson = $this->generateServiceSpecificHeadings($aiService);
            
            $headlines = json_decode($headingsJson, true);
            
            if (is_array($headlines)) {
                // Save headings
                $this->aiContent->ai_headings = count($headlines) == 1 ? 
                    $headlines[array_key_first($headlines)] : 
                    $headlines;
                $this->aiContent->headings_generated_at = now();
                $this->aiContent->save();
                
                // Prepare minimal context prompt for section generation
                $minimalContext = "PERSIAN_TEXT_453dd3b5" . $this->data['title'];
                if (!empty($this->serviceContext['PERSIAN_TEXT_8ac54d5c'])) {
                    $minimalContext .= "5e50552e" . $this->serviceContext['PERSIAN_TEXT_8ac54d5c'];
                }
                
                // Dispatch content generation jobs for each heading
                $eventHash = Str::random(7);
                foreach ($this->aiContent->ai_headings as $index => $heading) {
                    GenerateSectionContentJob::dispatch(
                        $heading,
                        $this->aiContent->title,
                        $this->aiContent->short_description . $minimalContext,
                        $this->aiContent->language,
                        $this->aiContent->model_type,
                        $index + 1,
                        count($this->aiContent->ai_headings),
                        $this->aiContent->id,
                        $this->data['online_mode'] ? 'online' : 'offline',
                        $eventHash
                    )->onQueue('ai-content');
                }
                
                Log::info('Service AI content headings generated', [
                    'ai_content_id' => $this->aiContent->id,
                    'headings_count' => count($this->aiContent->ai_headings),
                ]);
                
                // Dispatch monitoring job to check when sections are complete
                // and trigger summary, meta, FAQ, and image generation
                \App\Jobs\MonitorServiceContentGenerationJob::dispatch(
                    $this->aiContent->id,
                    $eventHash,
                    count($this->aiContent->ai_headings)
                )->onQueue('ai-content')
                ->delay(now()->addSeconds(30)); // Start monitoring after 30 seconds
                
            } else {
                throw new \Exception('Failed to generate headings');
            }
            
        } catch (\Exception $e) {
            Log::error('Service AI content generation job failed', [
                'error' => $e->getMessage(),
                'ai_content_id' => $this->aiContent->id,
                'trace' => $e->getTraceAsString(),
            ]);
            
            // Update AI content status to failed
            $this->aiContent->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'failed_at' => now(),
            ]);
            
            throw $e;
        }
    }
    
    /**
     * Generate service-specific headings with better focus on the service topic
     */
    protected function generateServiceSpecificHeadings($aiService)
    {
        // Prepare a focused prompt for service content
        $title = $this->data['title'];
        $description = $this->data['short_description'];
        $language = $this->data['language'];
        $headingsNumber = $this->data['headings_number'];
        $subHeadingsNumber = $this->data['sub_headings_number'PERSIAN_TEXT_a36edf31's generateText method for proper access
        try {
            // Combine the system prompt and custom prompt
            $fullPrompt = "You are an expert content writer creating informative headings about government and banking services for everyday users. Focus on practical information, not technical details.\n\n" . $customPrompt;
            
            // Use the public generateText method instead of accessing protected property
            $response = $aiService->generateText(
                $fullPrompt,
                'English', // Generate prompts in English for consistency
                $this->data['model_type'] // 'advanced' or 'fast'
            );
            
            return $aiService->cleanJson($response);
        } catch (\Exception $e) {
            // Fallback to regular generateTitles if custom method fails
            Log::warning('Custom service heading generation failed, falling back to default', [
                'error' => $e->getMessage()
            ]);
            
            return $aiService->cleanJson($aiService->generateTitles(
                $this->data['title'],
                $this->data['short_description'],
                $this->data['language'],
                $this->data['model_type'],
                $this->data['headings_number'],
                $this->data['sub_headings_number'],
                $this->data['online_mode'] ?? false
            ));
        }
    }
}