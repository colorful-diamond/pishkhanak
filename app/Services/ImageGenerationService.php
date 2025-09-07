<?php

namespace App\Services;

use App\Models\AiSetting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Str;

class ImageGenerationService
{
    protected $apiKey;
    protected $projectId;
    protected $location;
    protected $aiService;

    public function __construct(AiService $aiService)
    {
        $this->aiService = $aiService;
        $this->apiKey = config('services.google_cloud.api_key');
        $this->projectId = config('services.google_cloud.project_id');
        $this->location = config('services.google_cloud.location', 'us-central1');
    }

    /**
     * Generate image prompts for all headings
     */
    public function generateImagePrompts(array $headings, string $language = 'Persian', array $settings = [])
    {
        $prompts = [];
        $styleOptions = $this->getStyleOptions();
        
        // Get style prompt based on settings
        $selectedStyle = $settings['style_option'] ?? 'vector';
        if ($selectedStyle === 'custom') {
            $stylePrompt = $settings['custom_style_prompt'] ?? 'modern, professional, high quality, clean design';
        } else {
            $stylePrompt = $styleOptions[$selectedStyle]['prompt'] ?? 'modern, professional, high quality, clean design';
        }
        
        foreach ($headings as $index => $heading) {
            try {
                $title = $heading['title'];
                
                // Create a prompt for AI to generate image description
                $aiPrompt = "Create a perfect English image generation prompt for the title: '{$title}'. 
                            The prompt should be descriptive, visual, and suitable for AI image generation.
                            Style: {$stylePrompt}
                            Keep it under 200 characters and focus on visual elements, composition, and mood.
                            Do not include any text or typography in the description.
                            Response format: Just the prompt text, no additional explanation.";

                $imagePrompt = $this->aiService->generateText($aiPrompt, 'English', 'fast');
                
                if ($imagePrompt) {
                    // Combine AI-generated prompt with style prompt
                    $finalPrompt = trim($imagePrompt) . ', ' . $stylePrompt;
                    
                    $prompts[$index] = [
                        'title' => $title,
                        'prompt' => $finalPrompt,
                        'index' => $index
                    ];
                }
                
                Log::info('Generated image prompt', [
                    'title' => $title,
                    'ai_prompt' => $imagePrompt,
                    'final_prompt' => $finalPrompt ?? null,
                    'style' => $selectedStyle
                ]);
                
            } catch (\Exception $e) {
                Log::error('Failed to generate image prompt', [
                    'title' => $heading['title'],
                    'error' => $e->getMessage()
                ]);
                
                // Fallback prompt
                $fallbackPrompt = "Professional illustration related to: {$heading['title']}, {$stylePrompt}";
                $prompts[$index] = [
                    'title' => $heading['title'],
                    'prompt' => $fallbackPrompt,
                    'index' => $index
                ];
            }
        }
        
        return $prompts;
    }

    /**
     * Generate images using Google Imagen API
     */
    public function generateImages(string $prompt, array $settings = [])
    {
        $imageCount = $settings['image_count'] ?? 4;
        $imageQuality = $settings['image_quality'] ?? 'standard';
        
        try {
            $endpoint = "https://{$this->location}-aiplatform.googleapis.com/v1/projects/{$this->projectId}/locations/{$this->location}/publishers/google/models/imagegeneration:predict";
            
            $requestData = [
                'instances' => [
                    [
                        'prompt' => $prompt
                    ]
                ],
                'parameters' => [
                    'sampleCount' => $imageCount,
                    'aspectRatio' => '16:9', // Good for section images
                    'safetyFilterLevel' => 'block_some',
                    'personGeneration' => 'allow_adult'
                ]
            ];
            
            // Add quality settings
            if ($imageQuality === 'high') {
                $requestData['parameters']['guidanceScale'] = 15;
                $requestData['parameters']['seed'] = random_int(1, 1000000);
            }
            
            $response = $this->makeImageGenerationRequest($endpoint, $requestData);
            
            if ($response && isset($response['predictions'])) {
                return $this->processImageResponse($response['predictions']);
            }
            
            throw new \Exception('Invalid response from Imagen API');
            
        } catch (\Exception $e) {
            Log::error('Image generation failed', [
                'prompt' => $prompt,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    /**
     * Make HTTP request to Imagen API
     */
    protected function makeImageGenerationRequest(string $endpoint, array $data)
    {
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->getAccessToken(),
                'Content-Type: application/json'
            ],
            CURLOPT_TIMEOUT => 60
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            throw new \Exception("cURL error: {$error}");
        }
        
        if ($httpCode !== 200) {
            throw new \Exception("HTTP error: {$httpCode}, Response: {$response}");
        }
        
        return json_decode($response, true);
    }

    /**
     * Get Google Cloud access token
     */
    protected function getAccessToken()
    {
        // In production, you should use proper OAuth2 flow
        // For now, we'll use service account key
        return $this->apiKey;
    }

    /**
     * Process image response from API
     */
    protected function processImageResponse(array $predictions)
    {
        $images = [];
        
        foreach ($predictions as $index => $prediction) {
            if (isset($prediction['bytesBase64Encoded'])) {
                $imageData = base64_decode($prediction['bytesBase64Encoded']);
                $filename = 'temp_image_' . Str::random(10) . '.png';
                $path = 'ai_images/temp/' . $filename;
                
                Storage::disk('public')->put($path, $imageData);
                
                $images[] = [
                    'index' => $index,
                    'path' => $path,
                    'url' => asset('storage/' . $path),
                    'filename' => $filename
                ];
            }
        }
        
        return $images;
    }

    /**
     * Add title text to selected image (if enabled)
     */
    public function addTitleToImage(string $imagePath, string $title, array $options = [])
    {
        try {
            // Check if text overlay is enabled
            $addTextOverlay = $options['add_text_overlay'] ?? true;
            
            if (!$addTextOverlay) {
                // Return image without text overlay
                $filename = 'section_' . Str::random(10) . '.jpg';
                $processedPath = 'ai_images/sections/' . $filename;
                
                Storage::disk('public')->makeDirectory('ai_images/sections');
                
                // Simply copy the original image
                Storage::disk('public')->copy($imagePath, $processedPath);
                
                return [
                    'path' => $processedPath,
                    'url' => asset('storage/' . $processedPath),
                    'filename' => $filename
                ];
            }
            
            $fullPath = Storage::disk('public')->path($imagePath);
            
            if (!file_exists($fullPath)) {
                throw new \Exception("Image file not found: {$fullPath}");
            }
            
            // Get image info
            $imageInfo = getimagesize($fullPath);
            if (!$imageInfo) {
                throw new \Exception("Could not get image information");
            }
            
            $width = $imageInfo[0];
            $height = $imageInfo[1];
            $mimeType = $imageInfo['mime'];
            
            // Create image resource based on type
            switch ($mimeType) {
                case 'image/jpeg':
                    $image = imagecreatefromjpeg($fullPath);
                    break;
                case 'image/png':
                    $image = imagecreatefrompng($fullPath);
                    break;
                case 'image/gif':
                    $image = imagecreatefromgif($fullPath);
                    break;
                default:
                    throw new \Exception("Unsupported image type: {$mimeType}");
            }
            
            if (!$image) {
                throw new \Exception("Could not create image resource");
            }
            
            // Font settings
            $fontSize = $options['font_size'] ?? max(18, $width * 0.03);
            $fontPath = public_path('assets/fonts/IRANSansWeb_Bold.ttf');
            
            // Get overlay settings
            $overlayPosition = $options['text_overlay_position'] ?? 'bottom';
            $overlayStyle = $options['text_overlay_style'] ?? 'dark';
            
            // Create semi-transparent overlay
            $overlayHeight = $options['overlay_height'] ?? max(80, $height * 0.15);
            
            // Calculate overlay position
            switch ($overlayPosition) {
                case 'top':
                    $overlayY = 0;
                    break;
                case 'center':
                    $overlayY = ($height - $overlayHeight) / 2;
                    break;
                case 'bottom':
                default:
                    $overlayY = $height - $overlayHeight;
                    break;
            }
            
            // Create overlay with different styles
            switch ($overlayStyle) {
                case 'light':
                    $overlay = imagecolorallocatealpha($image, 255, 255, 255, 50); // 50% white
                    $textColor = imagecolorallocate($image, 0, 0, 0); // Black text
                    break;
                case 'gradient':
                    // Create gradient effect (simplified)
                    $overlay = imagecolorallocatealpha($image, 0, 0, 0, 30); // 30% black
                    $textColor = imagecolorallocate($image, 255, 255, 255); // White text
                    break;
                case 'dark':
                default:
                    $overlay = imagecolorallocatealpha($image, 0, 0, 0, 50); // 50% black
                    $textColor = imagecolorallocate($image, 255, 255, 255); // White text
                    break;
            }
            
            imagefilledrectangle($image, 0, $overlayY, $width, $overlayY + $overlayHeight, $overlay);
            
            // Add title text
            $textY = $overlayY + ($overlayHeight / 2);
            
            if (file_exists($fontPath) && function_exists('imagettftext')) {
                // Use TTF font if available
                $textBox = imagettfbbox($fontSize, 0, $fontPath, $title);
                $textWidth = $textBox[4] - $textBox[0];
                $textX = ($width - $textWidth) / 2;
                
                imagettftext($image, $fontSize, 0, $textX, $textY, $textColor, $fontPath, $title);
            } else {
                // Use built-in font
                $textWidth = strlen($title) * 10; // Approximate width
                $textX = ($width - $textWidth) / 2;
                
                imagestring($image, 5, $textX, $textY - 10, $title, $textColor);
            }
            
            // Save processed image
            $processedFilename = 'section_' . Str::random(10) . '.jpg';
            $processedPath = 'ai_images/sections/' . $processedFilename;
            
            Storage::disk('public')->makeDirectory('ai_images/sections');
            $fullProcessedPath = Storage::disk('public')->path($processedPath);
            
            // Save as JPEG
            imagejpeg($image, $fullProcessedPath, 85);
            
            // Clean up memory
            imagedestroy($image);
            
            return [
                'path' => $processedPath,
                'url' => asset('storage/' . $processedPath),
                'filename' => $processedFilename
            ];
            
        } catch (\Exception $e) {
            Log::error('Failed to add title to image', [
                'image_path' => $imagePath,
                'title' => $title,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    /**
     * Clean up temporary images
     */
    public function cleanupTempImages(array $tempImagePaths)
    {
        foreach ($tempImagePaths as $path) {
            try {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            } catch (\Exception $e) {
                Log::warning('Failed to delete temp image', [
                    'path' => $path,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Get predefined style options
     */
    public function getStyleOptions()
    {
        return [
            'vector' => [
                'name' => 'Vector / Flat Design',
                'prompt' => 'vector illustration, flat design, geometric shapes, solid colors, minimal shadows, clean lines, modern iconography, 2D style, simple gradients'
            ],
            'photography' => [
                'name' => 'Photography / Realistic',
                'prompt' => 'high-quality photography, realistic, professional lighting, sharp focus, detailed textures, natural colors, depth of field, cinematic composition'
            ],
            'illustration' => [
                'name' => 'Illustration / Artistic',
                'prompt' => 'digital illustration, artistic style, creative composition, vibrant colors, stylized elements, hand-drawn feel, expressive brushstrokes'
            ],
            'minimalist' => [
                'name' => 'Minimalist / Clean',
                'prompt' => 'minimalist design, clean composition, white space, simple elements, monochromatic or limited color palette, elegant simplicity, modern aesthetic'
            ],
            'corporate' => [
                'name' => 'Corporate / Professional',
                'prompt' => 'professional business style, corporate design, clean modern look, sophisticated colors, polished finish, executive aesthetic, formal presentation'
            ],
            'custom' => [
                'name' => 'Custom Style',
                'prompt' => '' // Will be filled by user input
            ]
        ];
    }

    /**
     * Get image generation settings from database
     */
    public function getImageSettings()
    {
        try {
            $settings = AiSetting::where('key', 'image_generation_settings')->first();
            
            if ($settings) {
                return json_decode($settings->value, true);
            }
            
            // Default settings
            return [
                'image_quality' => 'standard',
                'image_count' => 4,
                'style_option' => 'vector',
                'custom_style_prompt' => '',
                'add_text_overlay' => true,
                'text_overlay_position' => 'bottom',
                'text_overlay_style' => 'dark'
            ];
            
        } catch (\Exception $e) {
            Log::error('Failed to get image settings', [
                'error' => $e->getMessage()
            ]);
            
            return [
                'image_quality' => 'standard',
                'image_count' => 4,
                'style_option' => 'vector',
                'custom_style_prompt' => '',
                'add_text_overlay' => true,
                'text_overlay_position' => 'bottom',
                'text_overlay_style' => 'dark'
            ];
        }
    }

    /**
     * Save image generation settings
     */
    public function saveImageSettings(array $settings)
    {
        try {
            AiSetting::updateOrCreate(
                ['key' => 'image_generation_settings'],
                ['value' => json_encode($settings)]
            );
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Failed to save image settings', [
                'settings' => $settings,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }
} 