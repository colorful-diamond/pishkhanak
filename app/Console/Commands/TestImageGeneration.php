<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ImageGenerationService;
use App\Services\GeminiService;
use Illuminate\Support\Facades\Log;

class TestImageGeneration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:image-generation {prompt?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the background image generation jobs with Gemini Imagen 4';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $prompt = $this->argument('prompt') ?? 'A beautiful modern vector illustration of artificial intelligence and technology';
        
        $this->info("Testing image generation with prompt: {$prompt}");
        $this->info("Using Gemini API for Imagen image generation...");
        
        try {
            $imageService = app(ImageGenerationService::class);
            
            // Test settings
            $settings = [
                'image_quality' => 'high',
                'image_count' => 2,
                'style_option' => 'vector'
            ];
            
            $this->info("Starting image generation...");
            
            $images = $imageService->generateImages($prompt, $settings);
            
            if (!empty($images)) {
                $this->info("✅ Image generation successful!");
                $this->info("Generated " . count($images) . " images:");
                
                foreach ($images as $index => $image) {
                    $this->line("  Image " . ($index + 1) . ": " . $image['url']);
                }
                
                $this->info("\nImage generation test completed successfully!");
                return 0;
            } else {
                $this->error("❌ No images were generated");
                return 1;
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Image generation failed: " . $e->getMessage());
            $this->error("Error details: " . $e->getTraceAsString());
            
            Log::error('Image generation test failed', [
                'prompt' => $prompt,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return 1;
        }
    }
}
