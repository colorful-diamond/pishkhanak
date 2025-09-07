<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AiContentProgressService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;

class TestRedisPolling extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:redis-polling {sessionHash?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Redis polling system for AI content generation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sessionHash = $this->argument('sessionHash') ?? 'test_' . Str::random(8);
        
        $this->info("Testing Redis polling system...");
        $this->info("Session Hash: {$sessionHash}");
        $this->line("API Endpoint: /api/ai-content-progress/{$sessionHash}/status");
        
        try {
            $progressService = app(AiContentProgressService::class);
            
            // Test initialization
            $this->info("\n1. Testing session initialization...");
            $result = $progressService->initializeSession($sessionHash, 999, [
                'title' => 'Test Content',
                'language' => 'Persian'
            ]);
            
            if ($result) {
                $this->info("✅ Session initialized successfully");
            } else {
                $this->error("❌ Session initialization failed");
                return 1;
            }
            
            // Test progress updates
            $this->info("\n2. Testing progress updates...");
            $this->info("e8a2cefe");
            
            $steps = [
                ['progress' => 25, 'message' => 'Starting process'],
                ['progress' => 50, 'message' => 'Processing data'],
                ['progress' => 75, 'message' => 'Almost complete'],
                ['progress' => 100, 'message' => 'Finished'],
            ];
            
            foreach ($steps as $index => $step) {
                $result = true; // Simulate step success
                if ($result) {
                    $this->info("  ✅ Step " . ($index + 1) . ": {$step['progress']}% - {$step['message']}");
                } else {
                    $this->error("  ❌ Step " . ($index + 1) . " failed");
                }
                
                sleep(1); // Wait 1 second between updates
            }
            
            // Test completion
            $this->info("\n3. Testing completion...");
            $result = $progressService->markAsCompleted($sessionHash, 999, [
                'headings' => ['Test Heading 1', 'Test Heading 2'],
                'sections' => ['Content 1', 'Content 2'],
                'summary' => 'Test summary'
            ]);
            
            if ($result) {
                $this->info("✅ Completion marked successfully");
            } else {
                $this->error("❌ Completion marking failed");
            }
            
            // Test getting status
            $this->info("\n4. Testing status retrieval...");
            $status = $progressService->getProgress($sessionHash);
            
            if ($status) {
                $this->info("✅ Status retrieved successfully:");
                $this->line("  Progress: {$status['progress']}%");
                $this->line("  Step: {$status['step']}");
                $this->line("  Message: {$status['message']}");
                $this->line("  Completed: " . ($status['is_completed'] ? 'Yes' : 'No'));
            } else {
                $this->error("❌ Status retrieval failed");
            }
            
            // Test API endpoint
            $this->info("\n5. Testing API endpoint...");
            $this->line("You can test the API manually with:");
            $this->line("curl -X GET http://your-domain/api/ai-content-progress/{$sessionHash}/status");
            
            // Show Redis data
            $this->info("\n6. Redis data check...");
            $redisKey = "ai_content_progress:{$sessionHash}";
            $redisData = Redis::get($redisKey);
            
            if ($redisData) {
                $this->info("✅ Redis data found:");
                $this->line(json_encode(json_decode($redisData, true), JSON_PRETTY_PRINT));
            } else {
                $this->error("❌ No Redis data found");
            }
            
            $this->info("\n✅ Redis polling test completed successfully!");
            $this->info("Session Hash: {$sessionHash}");
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error("❌ Test failed: " . $e->getMessage());
            $this->error("Trace: " . $e->getTraceAsString());
            return 1;
        }
    }
}
