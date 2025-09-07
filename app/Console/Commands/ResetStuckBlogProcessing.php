<?php

namespace App\Console\Commands;

use App\Models\BlogContentPipeline;
use App\Models\AiProcessingStatus;
use App\Models\BlogProcessingLog;
use Illuminate\Console\Command;
use Carbon\Carbon;

class ResetStuckBlogProcessing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blog:reset-stuck {--minutes=10 : Minutes after which processing is considered stuck}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset blog posts stuck in processing status after timeout';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $minutes = $this->option('minutes');
        $cutoffTime = Carbon::now()->subMinutes($minutes);
        
        $this->info("Looking for blog posts stuck in processing for more than {$minutes} minutes...");
        
        // Find stuck pipelines
        $stuckPipelines = BlogContentPipeline::where('status', BlogContentPipeline::STATUS_PROCESSING)
            ->where('processing_started_at', '<=', $cutoffTime)
            ->get();
        
        if ($stuckPipelines->isEmpty()) {
            $this->info('No stuck blog posts found.');
            return Command::SUCCESS;
        }
        
        $this->warn("Found {$stuckPipelines->count()} stuck blog post(s).");
        
        foreach ($stuckPipelines as $pipeline) {
            $processingTime = Carbon::parse($pipeline->processing_started_at)->diffInMinutes(now());
            
            $this->line("Resetting: {$pipeline->title} (ID: {$pipeline->id})");
            $this->line("  - Started: {$pipeline->processing_started_at}");
            $this->line("  - Processing time: {$processingTime} minutes");
            $this->line("  - Attempts: {$pipeline->processing_attempts}");
            
            // Log the timeout
            BlogProcessingLog::create([
                'pipeline_id' => $pipeline->id,
                'action' => 'timeout_reset',
                'status' => BlogProcessingLog::STATUS_WARNING,
                'details' => [
                    'processing_time_minutes' => $processingTime,
                    'attempts' => $pipeline->processing_attempts,
                    'timeout_threshold' => $minutes,
                ],
                'error_message' => "Processing timed out after {$processingTime} minutes",
            ]);
            
            // Reset the pipeline to imported status
            $pipeline->update([
                'status' => BlogContentPipeline::STATUS_IMPORTED,
                'processing_errors' => array_merge(
                    $pipeline->processing_errors ?? [],
                    [[
                        'type' => 'timeout',
                        'message' => "Processing timed out after {$processingTime} minutes",
                        'timestamp' => now()->toIso8601String(),
                        'attempts' => $pipeline->processing_attempts,
                    ]]
                ),
            ]);
            
            // Mark any active processing status as failed
            $processingStatuses = AiProcessingStatus::where('pipeline_id', $pipeline->id)
                ->whereIn('status', ['pending', 'processing'])
                ->get();
            
            foreach ($processingStatuses as $status) {
                $status->failProcessing(
                    "Processing timed out after {$processingTime} minutes",
                    ['timeout_after_minutes' => $processingTime]
                );
            }
            
            $this->info("  ✓ Reset to 'imported' status");
        }
        
        $this->info("Successfully reset {$stuckPipelines->count()} stuck blog post(s).");
        
        return Command::SUCCESS;
    }
}