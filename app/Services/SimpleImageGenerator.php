<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SimpleImageGenerator
{
    /**
     * Generate a colorful text-based image
     */
    public function generateTextImage(string $text, array $options = [])
    {
        $width = $options['width'] ?? 1280;
        $height = $options['height'] ?? 720;
        
        // Create the image
        $image = imagecreatetruecolor($width, $height);
        
        // Generate random gradient background
        $colors = $this->generateRandomGradient($image);
        $this->fillGradientBackground($image, $width, $height, $colors);
        
        // Add decorative elements
        $this->addDecorativeShapes($image, $width, $height);
        
        // Add text overlay
        $this->addTextOverlay($image, $text, $width, $height);
        
        // Save the image
        $filename = 'img_' . Str::random(16) . '.png';
        $relativePath = 'ai_images/generated/' . date('Y/m/d') . '/' . $filename;
        
        // Ensure directory exists
        $directory = dirname(storage_path('app/public/' . $relativePath));
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        
        // Save image to file
        $fullPath = storage_path('app/public/' . $relativePath);
        imagepng($image, $fullPath, 9);
        imagedestroy($image);
        
        // Return the image data
        return [
            'path' => $relativePath,
            'url' => asset('storage/' . $relativePath),
            'filename' => $filename,
            'width' => $width,
            'height' => $height
        ];
    }
    
    /**
     * Generate random gradient colors
     */
    private function generateRandomGradient($image)
    {
        // Generate random vibrant colors
        $palettes = [
            // Sunset palette
            [
                ['r' => 255, 'g' => 94, 'b' => 77],   // Coral
                ['r' => 255, 'g' => 154, 'b' => 0],   // Orange
                ['r' => 237, 'g' => 117, 'b' => 57],  // Burnt orange
                ['r' => 95, 'g' => 39, 'b' => 205]    // Purple
            ],
            // Ocean palette
            [
                ['r' => 0, 'g' => 119, 'b' => 190],   // Ocean blue
                ['r' => 0, 'g' => 180, 'b' => 216],   // Sky blue
                ['r' => 144, 'g' => 224, 'b' => 239], // Light blue
                ['r' => 202, 'g' => 240, 'b' => 248]  // Pale blue
            ],
            // Forest palette
            [
                ['r' => 34, 'g' => 139, 'b' => 34],   // Forest green
                ['r' => 107, 'g' => 142, 'b' => 35],  // Olive
                ['r' => 154, 'g' => 205, 'b' => 50],  // Yellow green
                ['r' => 173, 'g' => 255, 'b' => 47]   // Green yellow
            ],
            // Purple dream palette
            [
                ['r' => 123, 'g' => 31, 'b' => 162],  // Violet
                ['r' => 103, 'g' => 58, 'b' => 183],  // Deep purple
                ['r' => 156, 'g' => 39, 'b' => 176],  // Purple
                ['r' => 233, 'g' => 30, 'b' => 99]    // Pink
            ],
            // Warm palette
            [
                ['r' => 255, 'g' => 193, 'b' => 7],   // Amber
                ['r' => 255, 'g' => 152, 'b' => 0],   // Orange
                ['r' => 255, 'g' => 87, 'b' => 34],   // Deep orange
                ['r' => 244, 'g' => 67, 'b' => 54]    // Red
            ]
        ];
        
        $palette = $palettes[array_rand($palettes)];
        $colors = [];
        
        foreach ($palette as $color) {
            $colors[] = imagecolorallocate($image, $color['r'], $color['g'], $color['b']);
        }
        
        return $colors;
    }
    
    /**
     * Fill background with gradient
     */
    private function fillGradientBackground($image, $width, $height, $colors)
    {
        // Choose gradient type randomly
        $gradientType = rand(0, 3);
        
        switch ($gradientType) {
            case 0: // Vertical gradient
                $this->verticalGradient($image, $width, $height, $colors[0], $colors[1]);
                break;
            case 1: // Horizontal gradient
                $this->horizontalGradient($image, $width, $height, $colors[1], $colors[2]);
                break;
            case 2: // Diagonal gradient
                $this->diagonalGradient($image, $width, $height, $colors[0], $colors[2]);
                break;
            case 3: // Radial gradient
                $this->radialGradient($image, $width, $height, $colors[1], $colors[3]);
                break;
        }
    }
    
    /**
     * Create vertical gradient
     */
    private function verticalGradient($image, $width, $height, $color1, $color2)
    {
        $r1 = ($color1 >> 16) & 0xFF;
        $g1 = ($color1 >> 8) & 0xFF;
        $b1 = $color1 & 0xFF;
        
        $r2 = ($color2 >> 16) & 0xFF;
        $g2 = ($color2 >> 8) & 0xFF;
        $b2 = $color2 & 0xFF;
        
        for ($y = 0; $y < $height; $y++) {
            $ratio = $y / $height;
            $r = $r1 + ($r2 - $r1) * $ratio;
            $g = $g1 + ($g2 - $g1) * $ratio;
            $b = $b1 + ($b2 - $b1) * $ratio;
            
            $color = imagecolorallocate($image, $r, $g, $b);
            imageline($image, 0, $y, $width, $y, $color);
        }
    }
    
    /**
     * Create horizontal gradient
     */
    private function horizontalGradient($image, $width, $height, $color1, $color2)
    {
        $r1 = ($color1 >> 16) & 0xFF;
        $g1 = ($color1 >> 8) & 0xFF;
        $b1 = $color1 & 0xFF;
        
        $r2 = ($color2 >> 16) & 0xFF;
        $g2 = ($color2 >> 8) & 0xFF;
        $b2 = $color2 & 0xFF;
        
        for ($x = 0; $x < $width; $x++) {
            $ratio = $x / $width;
            $r = $r1 + ($r2 - $r1) * $ratio;
            $g = $g1 + ($g2 - $g1) * $ratio;
            $b = $b1 + ($b2 - $b1) * $ratio;
            
            $color = imagecolorallocate($image, $r, $g, $b);
            imageline($image, $x, 0, $x, $height, $color);
        }
    }
    
    /**
     * Create diagonal gradient
     */
    private function diagonalGradient($image, $width, $height, $color1, $color2)
    {
        $r1 = ($color1 >> 16) & 0xFF;
        $g1 = ($color1 >> 8) & 0xFF;
        $b1 = $color1 & 0xFF;
        
        $r2 = ($color2 >> 16) & 0xFF;
        $g2 = ($color2 >> 8) & 0xFF;
        $b2 = $color2 & 0xFF;
        
        $diagonal = sqrt($width * $width + $height * $height);
        
        for ($i = 0; $i < $diagonal * 2; $i++) {
            $ratio = $i / ($diagonal * 2);
            $r = $r1 + ($r2 - $r1) * $ratio;
            $g = $g1 + ($g2 - $g1) * $ratio;
            $b = $b1 + ($b2 - $b1) * $ratio;
            
            $color = imagecolorallocate($image, $r, $g, $b);
            imageline($image, $i, 0, 0, $i, $color);
        }
    }
    
    /**
     * Create radial gradient
     */
    private function radialGradient($image, $width, $height, $color1, $color2)
    {
        $r1 = ($color1 >> 16) & 0xFF;
        $g1 = ($color1 >> 8) & 0xFF;
        $b1 = $color1 & 0xFF;
        
        $r2 = ($color2 >> 16) & 0xFF;
        $g2 = ($color2 >> 8) & 0xFF;
        $b2 = $color2 & 0xFF;
        
        $cx = $width / 2;
        $cy = $height / 2;
        $maxRadius = sqrt($cx * $cx + $cy * $cy);
        
        for ($radius = $maxRadius; $radius > 0; $radius--) {
            $ratio = 1 - ($radius / $maxRadius);
            $r = $r1 + ($r2 - $r1) * $ratio;
            $g = $g1 + ($g2 - $g1) * $ratio;
            $b = $b1 + ($b2 - $b1) * $ratio;
            
            $color = imagecolorallocate($image, $r, $g, $b);
            imagefilledellipse($image, $cx, $cy, $radius * 2, $radius * 2, $color);
        }
    }
    
    /**
     * Add decorative shapes
     */
    private function addDecorativeShapes($image, $width, $height)
    {
        // Add semi-transparent shapes
        $shapeCount = rand(3, 8);
        
        for ($i = 0; $i < $shapeCount; $i++) {
            $shapeType = rand(0, 3);
            $alpha = rand(10, 40); // Semi-transparent
            
            // Random color
            $r = rand(0, 255);
            $g = rand(0, 255);
            $b = rand(0, 255);
            $color = imagecolorallocatealpha($image, $r, $g, $b, $alpha);
            
            switch ($shapeType) {
                case 0: // Circle
                    $cx = rand(0, $width);
                    $cy = rand(0, $height);
                    $radius = rand(50, 200);
                    imagefilledellipse($image, $cx, $cy, $radius * 2, $radius * 2, $color);
                    break;
                    
                case 1: // Rectangle
                    $x1 = rand(0, $width);
                    $y1 = rand(0, $height);
                    $x2 = $x1 + rand(100, 300);
                    $y2 = $y1 + rand(100, 300);
                    imagefilledrectangle($image, $x1, $y1, $x2, $y2, $color);
                    break;
                    
                case 2: // Triangle
                    $points = [
                        rand(0, $width), rand(0, $height),
                        rand(0, $width), rand(0, $height),
                        rand(0, $width), rand(0, $height)
                    ];
                    imagefilledpolygon($image, $points, 3, $color);
                    break;
                    
                case 3: // Lines
                    imagesetthickness($image, rand(2, 5));
                    for ($j = 0; $j < 5; $j++) {
                        imageline($image, 
                            rand(0, $width), rand(0, $height),
                            rand(0, $width), rand(0, $height),
                            $color
                        );
                    }
                    imagesetthickness($image, 1);
                    break;
            }
        }
    }
    
    /**
     * Add text overlay
     */
    private function addTextOverlay($image, $text, $width, $height)
    {
        // Try to use Persian font if available
        $fontPath = '/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf';
        if (!file_exists($fontPath)) {
            // Fallback to built-in font
            $this->addBuiltInText($image, $text, $width, $height);
            return;
        }
        
        // Prepare text
        $lines = $this->wrapText($text, 40);
        $fontSize = 32;
        $lineHeight = $fontSize * 1.5;
        
        // Calculate text position
        $totalHeight = count($lines) * $lineHeight;
        $startY = ($height - $totalHeight) / 2 + $fontSize;
        
        // Add semi-transparent background for text
        $bgColor = imagecolorallocatealpha($image, 0, 0, 0, 60);
        $padding = 20;
        imagefilledrectangle($image, 
            $padding, 
            $startY - $fontSize - $padding,
            $width - $padding,
            $startY + $totalHeight + $padding,
            $bgColor
        );
        
        // Add text
        $textColor = imagecolorallocate($image, 255, 255, 255);
        $shadowColor = imagecolorallocatealpha($image, 0, 0, 0, 50);
        
        foreach ($lines as $i => $line) {
            $y = $startY + ($i * $lineHeight);
            
            // Get text box for centering
            $textBox = imagettfbbox($fontSize, 0, $fontPath, $line);
            $textWidth = $textBox[2] - $textBox[0];
            $x = ($width - $textWidth) / 2;
            
            // Add shadow
            imagettftext($image, $fontSize, 0, $x + 2, $y + 2, $shadowColor, $fontPath, $line);
            // Add text
            imagettftext($image, $fontSize, 0, $x, $y, $textColor, $fontPath, $line);
        }
    }
    
    /**
     * Add built-in text (fallback)
     */
    private function addBuiltInText($image, $text, $width, $height)
    {
        $lines = $this->wrapText($text, 50);
        $font = 5;
        $lineHeight = 20;
        
        // Calculate position
        $totalHeight = count($lines) * $lineHeight;
        $startY = ($height - $totalHeight) / 2;
        
        // Add background
        $bgColor = imagecolorallocatealpha($image, 0, 0, 0, 60);
        imagefilledrectangle($image, 0, $startY - 10, $width, $startY + $totalHeight + 10, $bgColor);
        
        // Add text
        $textColor = imagecolorallocate($image, 255, 255, 255);
        foreach ($lines as $i => $line) {
            $textWidth = imagefontwidth($font) * strlen($line);
            $x = ($width - $textWidth) / 2;
            $y = $startY + ($i * $lineHeight);
            imagestring($image, $font, $x, $y, $line, $textColor);
        }
    }
    
    /**
     * Wrap text into lines
     */
    private function wrapText($text, $maxChars = 40)
    {
        $words = explode(' ', $text);
        $lines = [];
        $currentLine = '';
        
        foreach ($words as $word) {
            if (strlen($currentLine . ' ' . $word) <= $maxChars) {
                $currentLine .= ($currentLine ? ' ' : '') . $word;
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
}