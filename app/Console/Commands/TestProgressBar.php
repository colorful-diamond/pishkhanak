<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AiContent;
use Illuminate\Support\Facades\Log;

class TestProgressBar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:progress-bar {ai_content_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test progress bar updates by simulating different stages';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $aiContentId = $this->argument('ai_content_id');
        
        if (!$aiContentId) {
            // Create a test AI content record
            $aiContent = AiContent::create([
                'title' => 'Test Progress Bar Content',
                'slug' => 'test-progress-' . time(),
                'short_description' => 'Testing progress bar functionality',
                'language' => 'Persian',
                'status' => 'generating',
                'generation_progress' => 0,
                'current_generation_step' => 'testing',
                'generation_started_at' => now(),
                'author_id' => 1,
                'category_id' => 1,
            ]);
            
            $aiContentId = $aiContent->id;
            $this->info("Created test AI content with ID: {$aiContentId}");
        }
        
        $this->info("Testing progress bar updates for AI Content ID: {$aiContentId}");
        
        // Test different progress stages
        $stages = [
            ['step' => 1, 'progress' => 0, 'message' => 'Starting generation'],
            ['step' => 2, 'progress' => 16, 'message' => 'Generating headings'],
            ['step' => 2, 'progress' => 32, 'message' => 'Headings completed'],
            ['step' => 3, 'progress' => 40, 'message' => 'Generating sections'],
            ['step' => 3, 'progress' => 48, 'message' => 'Sections completed'],
            ['step' => 4, 'progress' => 55, 'message' => 'Generating images'],
            ['step' => 4, 'progress' => 65, 'message' => 'Images completed'],
            ['step' => 5, 'progress' => 75, 'message' => 'Generating summary'],
            ['step' => 6, 'progress' => 85, 'message' => 'Generating meta'],
            ['step' => 7, 'progress' => 95, 'message' => 'Generating FAQ'],
            ['step' => 7, 'progress' => 100, 'message' => 'Generation completed'],
        ];
        
        foreach ($stages as $stage) {
            $this->line("Step {$stage['step']}: {$stage['progress']}% - {$stage['message']}");
            
            // Update database
            $aiContent = AiContent::find($aiContentId);
            if ($aiContent) {
                $aiContent->update([
                    'generation_progress' => $stage['progress'],
                    'current_generation_step' => "step_{$stage['step']}"
                ]);
            }
            
            // Log the update
            Log::info('Progress bar test update', [
                'ai_content_id' => $aiContentId,
                'step' => $stage['step'],
                'progress' => $stage['progress'],
                'message' => $stage['message']
            ]);
            
            // Wait 2 seconds between updates
            sleep(2);
        }
        
        $this->info("✅ Progress bar test completed!");
        $this->info("Check the AI Content Generator interface to see if progress updates work correctly.");
        $this->info("AI Content ID: {$aiContentId}");
        
        return 0;
    }
}
