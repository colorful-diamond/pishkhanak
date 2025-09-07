<?php

namespace App\Services;

use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Services\OpenAIService;


class ImageAiService
{
    protected OpenAIService $openAIService;
    protected string $fontPath;

    public function __construct()
    {
        $this->openAIService = app(OpenAIService::class);
        // Ensure the font is placed in the public/fonts directory
        $this->fontPath = public_path('assets/fonts/IRANSansWeb_Bold.ttf');
        
        if (!file_exists($this->fontPath)) {
            throw new \Exception("Font file not found at {$this->fontPath}");
        }
    }

    /**
     * Generate a thumbnail for a given title.
     *
     * @param string $title
     * @return string $thumbnailPath
     */
    public function generateThumbnail(string $title): string
    {
        // Step 1: Generate illustration/image based on the title
        $imageUrl = $this->generateIllustration($title);

        // Step 2: Download the image
        $imageContents = file_get_contents($imageUrl);
        if ($imageContents === false) {
            throw new \Exception("Failed to download image from {$imageUrl}");
        }

        // Step 3: Create Intervention Image instance
        $image = Image::make($imageContents);

        // Step 4: Resize to 1024x768
        $image->resize(1024, 768, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        // Step 5: Add text to the image
        $image = $this->addTextToImage($image, $title);

        // Step 6: Save the thumbnail to storage
        $thumbnailPath = $this->saveThumbnail($image, $title);

        return $thumbnailPath;
    }

    /**
     * Generate an illustration/image using OpenAI's DALL-E based on the title.
     *
     * @param string $title
     * @return string $imageUrl
     */
    protected function generateIllustration(string $title): string
    {
        $prompt = "Create a professional and visually appealing vector illustration suitable for a website hero image, incorporating the theme: '{$title}'.";

        try {
            $imageUrl = $this->openAIService->generateImage($prompt, '1792x1024');
            return $imageUrl;
        } catch (\Exception $e) {
            Log::error("Image Generation Error: " . $e->getMessage());
            throw new \Exception("Failed to generate illustration.");
        }
    }

    /**
     * Add Persian text to the image using IranianSans font.
     *
     * @param \Intervention\Image\Image $image
     * @param string $text
     * @return \Intervention\Image\Image
     */
    protected function addTextToImage($image, string $text)
    {
        $image->text($text, 512, 700, function ($font) {
            $font->file($this->fontPath);
            $font->size(48);
            $font->color('#FFFFFF');
            $font->align('center');
            $font->valign('middle');
            // $font->shadow(2, 2, '#000000', 0.5);
        });

        return $image;
    }

    /**
     * Save the thumbnail to the storage and return its path.
     *
     * @param \Intervention\Image\Image $image
     * @param string $title
     * @return string $path
     */
    protected function saveThumbnail($image, string $title): string
    {
        // Generate a unique filename based on the title
        $filename = preg_replace('/[^A-Za-z0-9\-]/', '_', strtolower($title)) . '-' . time() . '.jpg';
        $path = 'thumbnails/' . $filename;

        // Save the image to the 'public' disk (storage/app/public/thumbnails)
        Storage::disk('public')->put($path, (string) $image->encode('jpg', 90));

        return Storage::url($path);
    }
}