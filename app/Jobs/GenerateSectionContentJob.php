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
use Illuminate\Support\Facades\Redis;

class GenerateSectionContentJob implements ShouldQueue
{
    use Batchable;
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $heading;
    protected $title;
    protected $shortDescription;
    protected $number;
    protected $count;
    protected $ai_content_id;
    protected $language;
    protected $model_type;
    protected $generation_mode;

    public function __construct($heading, $title, $shortDescription, $language, $model_type, $number, $count, $ai_content_id, $generation_mode = 'offline')
    {
        $this->heading = $heading;
        $this->title = $title;
        $this->shortDescription = $shortDescription;
        $this->number = $number;
        $this->count = $count;
        $this->ai_content_id = $ai_content_id;
        $this->language = $language;
        $this->model_type = $model_type;
        $this->generation_mode = $generation_mode;
    }

    public function handle(AiService $aiService)
    {
        try {
            // Find the AiContent record
            $aiContent = AiContent::find($this->ai_content_id);
            
            if (!$aiContent) {
                Log::error("AiContent not found with ID: {$this->ai_content_id}");
                return;
            }

            // Generate the section content
            $res = $aiService->generateSectionContent(
                $this->heading,
                $this->title,
                $this->shortDescription,
                (int) $this->number,
                (int) $this->count,
                $this->language,
                $this->model_type,
                $this->generation_mode
            );

            if (!$res) {
                Log::error("Failed to generate section content for heading: " . json_encode($this->heading));
                return;
            }
            
            // Use Redis lock to prevent concurrent updates
            $this->updateAiContentWithLock($aiContent, $res);
            
            Log::info("Section {$this->number} generated successfully for AiContent ID: {$this->ai_content_id}");
            
        } catch (\Exception $e) {
            Log::error("Error in GenerateSectionContentJob: " . $e->getMessage(), [
                'ai_content_id' => $this->ai_content_id,
                'section_number' => $this->number,
                'error' => $e->getTraceAsString()
            ]);
            
            // Optionally, you can re-throw the exception to mark the job as failed
            // throw $e;
        }
    }

    /**
     * Update AI content with Redis lock protection
     */
    private function updateAiContentWithLock($aiContent, $sectionContent)
    {
        $lockKey = "ai_content_lock:{$this->ai_content_id}";
        $lockTimeout = 30; // Lock timeout in seconds
        $maxWaitTime = 120; // Maximum time to wait for lock in seconds
        $waitInterval = 0.1; // Wait interval in seconds (100ms)
        
        $startTime = time();
        $lockAcquired = false;
        
        Log::info("Attempting to acquire lock for AiContent ID: {$this->ai_content_id}, Section: {$this->number}");
        
        // Keep trying to acquire lock until timeout
        while (!$lockAcquired && (time() - $startTime) < $maxWaitTime) {
            try {
                // Try to acquire lock with SET NX EX command
                $lockAcquired = Redis::set($lockKey, $this->number, 'EX', $lockTimeout, 'NX');
                
                if ($lockAcquired) {
                    Log::info("Lock acquired for AiContent ID: {$this->ai_content_id}, Section: {$this->number}");
                    break;
                }
                
                // Wait before trying again
                usleep($waitInterval * 1000000); // Convert to microseconds
                
            } catch (\Exception $e) {
                Log::error("Redis lock error: " . $e->getMessage());
                // Fall back to direct update if Redis is not available
                $this->updateAiContentDirectly($aiContent, $sectionContent);
                return;
            }
        }
        
        if (!$lockAcquired) {
            Log::error("Failed to acquire lock for AiContent ID: {$this->ai_content_id}, Section: {$this->number} after {$maxWaitTime} seconds");
            throw new \Exception("Could not acquire lock for AI content update");
        }
        
        try {
            // Refresh the model to get latest data
            $aiContent->refresh();
            
            // Update the sections using 0-based indexing
            $ai_sections = $aiContent->ai_sections ?? [];
            $ai_sections[$this->number - 1] = $sectionContent; // Convert to 0-based indexing
            $aiContent->ai_sections = $ai_sections;
            
            $aiContent->save();
            
            Log::info("Successfully updated AiContent ID: {$this->ai_content_id}, Section: {$this->number} (Index: " . ($this->number - 1) . ")");
            
        } catch (\Exception $e) {
            Log::error("Error updating AiContent with lock: " . $e->getMessage());
            throw $e;
        } finally {
            // Always release the lock
            $this->releaseLock($lockKey);
        }
    }
    
    /**
     * Release the Redis lock
     */
    private function releaseLock($lockKey)
    {
        try {
            $released = Redis::del($lockKey);
            if ($released) {
                Log::info("Lock released for key: {$lockKey}");
            } else {
                Log::warning("Lock may have already expired for key: {$lockKey}");
            }
        } catch (\Exception $e) {
            Log::error("Error releasing lock: " . $e->getMessage());
        }
    }
    
    /**
     * Fallback method for direct update when Redis is not available
     */
    private function updateAiContentDirectly($aiContent, $sectionContent)
    {
        Log::warning("Using direct update fallback for AiContent ID: {$this->ai_content_id}, Section: {$this->number}");
        
        // Add a small random delay to reduce collision probability
        usleep(rand(100000, 500000)); // 0.1 to 0.5 seconds
        
        // Refresh and update using 0-based indexing
        $aiContent->refresh();
        $ai_sections = $aiContent->ai_sections ?? [];
        $ai_sections[$this->number - 1] = $sectionContent; // Convert to 0-based indexing
        $aiContent->ai_sections = $ai_sections;
        $aiContent->save();
    }
}
