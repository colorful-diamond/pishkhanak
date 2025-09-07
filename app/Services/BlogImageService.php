<?php

namespace App\Services;

use App\Models\BlogContentPipeline;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class BlogImageService
{
    protected ThumbnailGeneratorService $thumbnailGenerator;
    protected ?ImageAiService $aiImageService;
    
    public function __construct()
    {
        $this->thumbnailGenerator = app(ThumbnailGeneratorService::class);
        
        try {
            $this->aiImageService = app(ImageAiService::class);
        } catch (\Exception $e) {
            $this->aiImageService = null;
            Log::warning('AI Image service not available', ['error' => $e->getMessage()]);
        }
    }
    
    /**
     * Generate image for blog post
     */
    public function generateBlogImage(
        BlogContentPipeline $pipeline,
        string $method = 'programmatic',
        array $options = []
    ): ?string {
        try {
            return match($method) {
                'ai' => $this->generateAiImage($pipeline, $options),
                'programmatic' => $this->generateProgrammaticImage($pipeline, $options),
                'auto' => $this->generateAutoImage($pipeline, $options),
                default => null
            };
        } catch (\Exception $e) {
            Log::error('Blog image generation failed', [
                'pipeline_id' => $pipeline->id,
                'method' => $method,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
    
    /**
     * Generate AI image using Vertex
     */
    protected function generateAiImage(BlogContentPipeline $pipeline, array $options): ?string
    {
        if (!$this->aiImageService) {
            throw new \Exception('AI Image service not available');
        }
        
        // Prepare prompt for AI image generation
        $prompt = $this->prepareAiImagePrompt($pipeline, $options);
        
        // Generate image using AI service
        $imageUrl = $this->aiImageService->generateImage($prompt, $options['style'] ?? 'realistic');
        
        if ($imageUrl) {
            // Download and store image
            $contents = file_get_contents($imageUrl);
            $filename = 'ai_images/' . $pipeline->id . '_' . time() . '.jpg';
            Storage::disk('public')->put($filename, $contents);
            
            return $filename;
        }
        
        return null;
    }
    
    /**
     * Generate programmatic image with text overlay
     */
    protected function generateProgrammaticImage(BlogContentPipeline $pipeline, array $options): string
    {
        // Get main title and subtitle
        $mainTitle = $pipeline->ai_title ?: $pipeline->title;
        $subtitle = $this->getSubtitle($pipeline, $options);
        
        // Generate thumbnail
        return $this->thumbnailGenerator->generateThumbnail(
            mainTitle: $mainTitle,
            subtitle: $subtitle,
            backgroundPath: $options['background'] ?? null,
            options: array_merge([
                'text_color' => '#ffffff',
                'overlay_opacity' => 65,
                'main_font_size' => 52,
                'subtitle_font_size' => 28,
            ], $options)
        );
    }
    
    /**
     * Auto decide which method to use
     */
    protected function generateAutoImage(BlogContentPipeline $pipeline, array $options): string
    {
        // Use AI for featured posts or when explicitly requested
        if (($options['prefer_ai'] ?? false) && $this->aiImageService) {
            try {
                $result = $this->generateAiImage($pipeline, $options);
                if ($result) {
                    return $result;
                }
            } catch (\Exception $e) {
                Log::warning('AI image generation failed, falling back to programmatic', [
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        // Fallback to programmatic
        return $this->generateProgrammaticImage($pipeline, $options);
    }
    
    /**
     * Get subtitle for the image
     */
    protected function getSubtitle(BlogContentPipeline $pipeline, array $options): ?string
    {
        // Use provided subtitle
        if (!empty($options['subtitle'])) {
            return $options['subtitle'];
        }
        
        // Use first heading if available
        if (!empty($pipeline->ai_headings) && is_array($pipeline->ai_headings)) {
            return $pipeline->ai_headings[0];
        }
        
        // Use category name
        if ($pipeline->category) {
            return $pipeline->category->name;
        }
        
        return null;
    }
    
    /**
     * Prepare AI image prompt
     */
    protected function prepareAiImagePrompt(BlogContentPipeline $pipeline, array $options): string
    {
        $title = $pipeline->ai_title ?: $pipeline->title;
        $style = $options['style'] ?? 'professional blog header';
        
        $prompt = "Create a {$style} image for a blog post about: {$title}. ";
        
        // Add keywords if available
        if (!empty($pipeline->meta_keywords)) {
            $keywords = is_array($pipeline->meta_keywords) 
                ? implode(', ', array_slice($pipeline->meta_keywords, 0, 5))
                : $pipeline->meta_keywords;
            $prompt .= "Keywords: {$keywords}. ";
        }
        
        // Add style guidelines
        $prompt .= "The image should be professional, engaging, and suitable for a blog header. ";
        $prompt .= "Avoid text in the image. Focus on visual representation of the concept.";
        
        return $prompt;
    }
    
    /**
     * Generate multiple images for blog sections
     */
    public function generateSectionImages(BlogContentPipeline $pipeline, string $method = 'programmatic'): array
    {
        $images = [];
        
        if (empty($pipeline->ai_sections) || !is_array($pipeline->ai_sections)) {
            return $images;
        }
        
        // Generate images for first 3 sections
        $sections = array_slice($pipeline->ai_sections, 0, 3);
        
        foreach ($sections as $index => $section) {
            try {
                $image = $this->generateBlogImage(
                    $pipeline,
                    $method,
                    [
                        'subtitle' => $section['title'] ?? "Section " . ($index + 1),
                        'prefer_ai' => false, // Use programmatic for sections
                    ]
                );
                
                if ($image) {
                    $images[] = [
                        'section' => $section['title'] ?? '',
                        'path' => $image,
                        'url' => Storage::url($image)
                    ];
                }
            } catch (\Exception $e) {
                Log::warning('Section image generation failed', [
                    'section' => $section['title'] ?? '',
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        return $images;
    }
}