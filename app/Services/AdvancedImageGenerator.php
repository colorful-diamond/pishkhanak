<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\PersianRender;

class AdvancedImageGenerator
{
    private $backgroundImages = [];
    private static $selectedBackground = null; // Store selected background for entire content
    
    /**
     * Reset background selection for new content
     */
    public static function resetBackgroundSelection()
    {
        self::$selectedBackground = null;
    }
    
    /**
     * Generate an image with background and text overlay
     */
    public function generateImageWithBackground(string $mainTitle, string $subtitle, array $options = [])
    {
        $width = $options['width'] ?? 1280;
        $height = $options['height'] ?? 720;
        
        // Create the base image
        $image = imagecreatetruecolor($width, $height);
        
        // Get available background images from storage
        $this->loadAvailableBackgrounds();
        
        // Use the same background for all images in this content
        if (self::$selectedBackground === null && !empty($this->backgroundImages)) {
            self::$selectedBackground = $this->backgroundImages[array_rand($this->backgroundImages)];
        }
        $backgroundPath = self::$selectedBackground;
        if (file_exists($backgroundPath)) {
            $bgImage = $this->loadBackgroundImage($backgroundPath);
            if ($bgImage) {
                // Resize and apply background
                imagecopyresampled($image, $bgImage, 0, 0, 0, 0, $width, $height, imagesx($bgImage), imagesy($bgImage));
                imagedestroy($bgImage);
            } else {
                // Fallback to gradient if background fails
                $this->createGradientBackground($image, $width, $height);
            }
        } else {
            // Fallback to gradient
            $this->createGradientBackground($image, $width, $height);
        }
        
        // Removed overlay - using full brightness of background images
        
        // Analyze background brightness
        $isDarkBackground = $this->analyzeBackgroundBrightness($image, $width, $height);
        
        // Add decorative frame
        $this->addDecorativeFrame($image, $width, $height);
        
        // Add text overlays with smart color selection
        $this->addTextWithBackground($image, $mainTitle, $subtitle, $width, $height, $isDarkBackground);
        
        // Save the image
        $filename = 'img_' . Str::random(16) . '.png';
        $relativePath = 'ai_images/generated/' . date('Y/m/d') . '/' . $filename;
        
        // Ensure directory exists
        $directory = dirname(storage_path('app/public/' . $relativePath));
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        
        // Save image
        $fullPath = storage_path('app/public/' . $relativePath);
        imagepng($image, $fullPath, 9);
        imagedestroy($image);
        
        return [
            'path' => $relativePath,
            'url' => 'https://pishkhanak.com/storage/' . $relativePath,
            'filename' => $filename,
            'width' => $width,
            'height' => $height,
            'main_title' => $mainTitle,
            'subtitle' => $subtitle
        ];
    }
    
    /**
     * Load available backgrounds from storage
     */
    private function loadAvailableBackgrounds()
    {
        $backgroundsPath = storage_path('app/public/backgrounds/');
        if (is_dir($backgroundsPath)) {
            $files = glob($backgroundsPath . '*.{jpg,jpeg,png,webp}', GLOB_BRACE);
            $this->backgroundImages = $files;
        }
    }
    
    /**
     * Load background image
     */
    private function loadBackgroundImage($path)
    {
        $info = getimagesize($path);
        if (!$info) return null;
        
        switch ($info['mime']) {
            case 'image/jpeg':
                return imagecreatefromjpeg($path);
            case 'image/png':
                return imagecreatefrompng($path);
            case 'image/webp':
                return imagecreatefromwebp($path);
            default:
                return null;
        }
    }
    
    /**
     * Create gradient background (fallback)
     */
    private function createGradientBackground($image, $width, $height)
    {
        $colors = [
            ['r' => 41, 'g' => 128, 'b' => 185],   // Blue
            ['r' => 142, 'g' => 68, 'b' => 173]    // Purple
        ];
        
        for ($y = 0; $y < $height; $y++) {
            $ratio = $y / $height;
            $r = $colors[0]['r'] + ($colors[1]['r'] - $colors[0]['r']) * $ratio;
            $g = $colors[0]['g'] + ($colors[1]['g'] - $colors[0]['g']) * $ratio;
            $b = $colors[0]['b'] + ($colors[1]['b'] - $colors[0]['b']) * $ratio;
            
            $color = imagecolorallocate($image, $r, $g, $b);
            imageline($image, 0, $y, $width, $y, $color);
        }
    }
    
    /**
     * Add decorative frame
     */
    private function addDecorativeFrame($image, $width, $height)
    {
        // Add corner decorations
        $decorColor = imagecolorallocatealpha($image, 255, 255, 255, 80);
        $decorColor2 = imagecolorallocatealpha($image, 255, 215, 0, 90); // Gold
        
        // Top-left corner
        imagesetthickness($image, 3);
        imageline($image, 0, 50, 50, 50, $decorColor);
        imageline($image, 50, 0, 50, 50, $decorColor);
        imagearc($image, 50, 50, 100, 100, 180, 270, $decorColor2);
        
        // Top-right corner
        imageline($image, $width - 50, 50, $width, 50, $decorColor);
        imageline($image, $width - 50, 0, $width - 50, 50, $decorColor);
        imagearc($image, $width - 50, 50, 100, 100, 270, 0, $decorColor2);
        
        // Bottom-left corner
        imageline($image, 0, $height - 50, 50, $height - 50, $decorColor);
        imageline($image, 50, $height - 50, 50, $height, $decorColor);
        imagearc($image, 50, $height - 50, 100, 100, 90, 180, $decorColor2);
        
        // Bottom-right corner
        imageline($image, $width - 50, $height - 50, $width, $height - 50, $decorColor);
        imageline($image, $width - 50, $height - 50, $width - 50, $height, $decorColor);
        imagearc($image, $width - 50, $height - 50, 100, 100, 0, 90, $decorColor2);
        
        imagesetthickness($image, 1);
    }
    
    /**
     * Analyze background brightness and dominant color to determine text color
     */
    private function analyzeBackgroundBrightness($image, $width, $height)
    {
        // Sample the text area more precisely
        $sampleWidth = min(600, $width * 0.8);
        $sampleHeight = min(300, $height * 0.4);
        $startX = ($width - $sampleWidth) / 2;
        $startY = ($height - $sampleHeight) / 2;
        
        $totalBrightness = 0;
        $totalSaturation = 0;
        $sampleCount = 0;
        $colorSum = ['r' => 0, 'g' => 0, 'b' => 0];
        
        // Sample every 5th pixel for more accuracy
        for ($x = $startX; $x < $startX + $sampleWidth; $x += 5) {
            for ($y = $startY; $y < $startY + $sampleHeight; $y += 5) {
                $rgb = imagecolorat($image, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                
                // Track color averages
                $colorSum['r'] += $r;
                $colorSum['g'] += $g;
                $colorSum['b'] += $b;
                
                // Calculate perceived brightness (more accurate formula)
                $brightness = sqrt(0.299 * $r * $r + 0.587 * $g * $g + 0.114 * $b * $b);
                $totalBrightness += $brightness;
                
                // Calculate saturation to detect vibrant colors
                $max = max($r, $g, $b);
                $min = min($r, $g, $b);
                $saturation = $max > 0 ? ($max - $min) / $max : 0;
                $totalSaturation += $saturation;
                
                $sampleCount++;
            }
        }
        
        $avgBrightness = $totalBrightness / $sampleCount;
        $avgSaturation = $totalSaturation / $sampleCount;
        
        // Calculate dominant color
        $avgR = $colorSum['r'] / $sampleCount;
        $avgG = $colorSum['g'] / $sampleCount;
        $avgB = $colorSum['b'] / $sampleCount;
        
        // Detect yellow/bright saturated colors (like yellow)
        $isYellowish = ($avgR > 200 && $avgG > 180 && $avgB < 100);
        $isBrightSaturated = ($avgBrightness > 160 && $avgSaturation > 0.3);
        
        // More nuanced decision
        // For yellow and bright saturated colors, always use dark text
        if ($isYellowish || $isBrightSaturated) {
            return false; // Not dark, use dark text
        }
        
        // For mid-tones (100-180), check saturation
        if ($avgBrightness > 100 && $avgBrightness < 180) {
            // If highly saturated, treat as light (use dark text)
            if ($avgSaturation > 0.4) {
                return false;
            }
        }
        
        // Otherwise use standard brightness threshold (adjusted to 140 for better results)
        return $avgBrightness < 140;
    }
    
    /**
     * Add text with background panel
     */
    private function addTextWithBackground($image, $mainTitle, $subtitle, $width, $height, $isDarkBackground = false)
    {
        // Font settings - Use Persian fonts
        $possibleFonts = [
            storage_path('fonts/IRANSans.ttf'),  // Persian font
            storage_path('fonts/default.ttf'),   // Fallback font
            '/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf',
            '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
        ];
        
        $fontPath = null;
        foreach ($possibleFonts as $font) {
            if (file_exists($font)) {
                $fontPath = $font;
                break;
            }
        }
        
        if (!$fontPath) {
            // Use built-in font as last resort
            $this->addBuiltInText($image, $mainTitle, $subtitle, $width, $height);
            return;
        }
        
        // Calculate text positioning - larger sizes for better visibility
        $mainFontSize = 36;
        $subFontSize = 26;
        
        // Smart color selection based on background
        if ($isDarkBackground) {
            // Dark background - use white text with strong black outline
            $textColor = imagecolorallocate($image, 255, 255, 255);
            $shadowColor = imagecolorallocate($image, 0, 0, 0);
            $accentColor = imagecolorallocate($image, 255, 235, 100); // Bright yellow for subtitle
        } else {
            // Light/bright background - use very dark text with strong white outline
            $textColor = imagecolorallocate($image, 10, 10, 10); // Almost black
            $shadowColor = imagecolorallocate($image, 255, 255, 255);
            $accentColor = imagecolorallocate($image, 0, 50, 150); // Dark blue for subtitle
        }
        
        // Calculate vertical center position
        $totalTextHeight = 150; // Approximate height for text block
        $centerY = ($height - $totalTextHeight) / 2;
        
        // Wrap and add main title
        $mainLines = $this->wrapText($mainTitle, $width - 200, $mainFontSize, $fontPath);
        $mainY = $centerY + 50;
        
        foreach ($mainLines as $i => $line) {
            // Use PersianRender ONLY here, exactly as documented
            $renderedLine = PersianRender::render($line);
            
            // Get text bounds for centering
            $bbox = imagettfbbox($mainFontSize, 0, $fontPath, $renderedLine);
            $textWidth = $bbox[2] - $bbox[0];
            $x = ($width - $textWidth) / 2;
            $y = $mainY + ($i * 45);
            
            // Strong outline effect for maximum readability
            // Create a thick outline by drawing the text multiple times
            for ($sx = -3; $sx <= 3; $sx++) {
                for ($sy = -3; $sy <= 3; $sy++) {
                    if ($sx != 0 || $sy != 0) {
                        imagettftext($image, $mainFontSize, 0, $x + $sx, $y + $sy, $shadowColor, $fontPath, $renderedLine);
                    }
                }
            }
            // Add main text
            imagettftext($image, $mainFontSize, 0, $x, $y, $textColor, $fontPath, $renderedLine);
        }
        
        // Add subtitle (no separator line)
        $subLines = $this->wrapText($subtitle, $width - 200, $subFontSize, $fontPath);
        $subY = $mainY + (count($mainLines) * 45) + 30;
        
        foreach ($subLines as $i => $line) {
            // Use PersianRender ONLY here, exactly as documented
            $renderedLine = PersianRender::render($line);
            
            $bbox = imagettfbbox($subFontSize, 0, $fontPath, $renderedLine);
            $textWidth = $bbox[2] - $bbox[0];
            $x = ($width - $textWidth) / 2;
            $y = $subY + ($i * 35);
            
            // Strong outline effect for subtitle too
            for ($sx = -2; $sx <= 2; $sx++) {
                for ($sy = -2; $sy <= 2; $sy++) {
                    if ($sx != 0 || $sy != 0) {
                        imagettftext($image, $subFontSize, 0, $x + $sx, $y + $sy, $shadowColor, $fontPath, $renderedLine);
                    }
                }
            }
            // Add text in accent color
            imagettftext($image, $subFontSize, 0, $x, $y, $accentColor, $fontPath, $renderedLine);
        }
    }
    
    /**
     * Add built-in text (fallback)
     */
    private function addBuiltInText($image, $mainTitle, $subtitle, $width, $height)
    {
        $font = 5;
        
        // Background for text
        $bgColor = imagecolorallocatealpha($image, 0, 0, 0, 40);
        $textHeight = 100;
        $y = ($height - $textHeight) / 2;
        imagefilledrectangle($image, 50, $y, $width - 50, $y + $textHeight, $bgColor);
        
        // Text colors
        $textColor = imagecolorallocate($image, 255, 255, 255);
        $subColor = imagecolorallocate($image, 255, 215, 0);
        
        // Main title
        $mainLines = $this->wrapTextSimple($mainTitle, 60);
        foreach ($mainLines as $i => $line) {
            $textWidth = imagefontwidth($font) * strlen($line);
            $x = ($width - $textWidth) / 2;
            imagestring($image, $font, $x, $y + 10 + ($i * 15), $line, $textColor);
        }
        
        // Subtitle
        $subLines = $this->wrapTextSimple($subtitle, 70);
        $subY = $y + 10 + (count($mainLines) * 15) + 10;
        foreach ($subLines as $i => $line) {
            $textWidth = imagefontwidth($font - 1) * strlen($line);
            $x = ($width - $textWidth) / 2;
            imagestring($image, $font - 1, $x, $subY + ($i * 12), $line, $subColor);
        }
    }
    
    /**
     * Convert Persian text for proper RTL rendering
     */
    private function convertPersianText($text)
    {
        // Use PersianRender with reverse=false to keep proper word order
        // The library shapes the letters but shouldn't reverse the entire string
        return PersianRender::render($text, false);
    }
    
    /**
     * Wrap text for TTF fonts
     */
    private function wrapText($text, $maxWidth, $fontSize, $fontPath)
    {
        // Don't convert here - let the main rendering handle it
        $words = explode(' ', $text);
        $lines = [];
        $currentLine = '';
        
        foreach ($words as $word) {
            $testLine = $currentLine . ($currentLine ? ' ' : '') . $word;
            $bbox = imagettfbbox($fontSize, 0, $fontPath, $testLine);
            $lineWidth = $bbox[2] - $bbox[0];
            
            if ($lineWidth <= $maxWidth) {
                $currentLine = $testLine;
            } else {
                if ($currentLine) {
                    $lines[] = $currentLine;
                }
                $currentLine = $word;
            }
        }
        
        if ($currentLine) {
            $lines[] = $currentLine;
        }
        
        return $lines;
    }
    
    /**
     * Simple text wrapping for built-in fonts
     */
    private function wrapTextSimple($text, $maxChars)
    {
        return explode("\n", wordwrap($text, $maxChars, "\n", true));
    }
}