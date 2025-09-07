<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Str;
use ArPHP\I18N\Arabic;

class ThumbnailGeneratorService
{
    protected ImageManager $manager;
    protected array $backgrounds = [];
    protected ?string $fontPath = null;
    
    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
        // Try to find a suitable font (IRANSans for Persian content)
        $possibleFonts = [
            storage_path('fonts/IRANSans.ttf'),  // Primary font from your website
            storage_path('fonts/default.ttf'),   // Fallback font
            '/usr/share/fonts/truetype/liberation/LiberationSans-Regular.ttf',
            '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
        ];
        
        foreach ($possibleFonts as $font) {
            if (file_exists($font)) {
                $this->fontPath = $font;
                break;
            }
        }
        
        // Fallback to system font
        if (!isset($this->fontPath)) {
            $this->fontPath = null; // Will use default system font
        }
        
        $this->loadBackgrounds();
    }
    
    /**
     * Generate thumbnail with Persian text overlay
     */
    public function generateThumbnail(
        string $mainTitle,
        ?string $subtitle = null,
        ?string $backgroundPath = null,
        array $options = []
    ): string {
        try {
            // Get random background if not specified
            if (!$backgroundPath) {
                $backgroundPath = $this->getRandomBackground();
            }
            
            // Default options
            $options = array_merge([
                'width' => 1200,
                'height' => 630,
                'main_font_size' => 48,
                'subtitle_font_size' => 32,
                'text_color' => '#ffffff',
                'overlay_opacity' => 60,
                'overlay_color' => '#000000',
                'padding' => 60,
                'line_height' => 1.5,
            ], $options);
            
            // Create image from background
            $image = $this->manager->read($backgroundPath);
            
            // Resize to desired dimensions
            $image->resize($options['width'], $options['height']);
            
            // Add dark overlay for better text visibility
            $overlay = $this->manager->create($options['width'], $options['height']);
            $overlay->fill($options['overlay_color']);
            $image->place($overlay, 'top-left', 0, 0, $options['overlay_opacity']);
            
            // Calculate text positions
            $mainTitleY = $this->calculateMainTitlePosition($options['height'], $subtitle !== null);
            $subtitleY = $subtitle ? $mainTitleY + ($options['main_font_size'] * $options['line_height']) + 20 : $mainTitleY;
            
            // Analyze background color in text area and determine best text color
            $textAreaColors = $this->analyzeTextAreaBackground($image, $options['width'], $options['height'], $mainTitleY, $subtitleY);
            $options['text_color'] = $textAreaColors['main'];
            $subtitleColor = $textAreaColors['subtitle'];
            
            // Add main title with word wrap
            $wrappedTitle = $this->wrapPersianText($mainTitle, $options['width'] - (2 * $options['padding']), $options['main_font_size']);
            $this->addPersianText(
                $image,
                $wrappedTitle,
                $options['width'] / 2,
                $mainTitleY,
                $options['main_font_size'],
                $options['text_color'],
                'center'
            );
            
            // Add subtitle if provided
            if ($subtitle) {
                $wrappedSubtitle = $this->wrapPersianText($subtitle, $options['width'] - (2 * $options['padding']), $options['subtitle_font_size']);
                $this->addPersianText(
                    $image,
                    $wrappedSubtitle,
                    $options['width'] / 2,
                    $subtitleY,
                    $options['subtitle_font_size'],
                    $subtitleColor,
                    'center',
                    1.0 // Full opacity to preserve Persian text shaping
                );
            }
            
            // Add website branding
            $this->addBranding($image, $options);
            
            // Save thumbnail
            $filename = 'thumbnails/' . Str::random(32) . '.jpg';
            $path = storage_path('app/public/' . $filename);
            
            // Ensure directory exists
            Storage::disk('public')->makeDirectory('thumbnails');
            
            // Save image
            $image->save($path, quality: 85);
            
            return $filename;
            
        } catch (\Exception $e) {
            \Log::error('Thumbnail generation failed', [
                'error' => $e->getMessage(),
                'title' => $mainTitle
            ]);
            throw $e;
        }
    }
    
    /**
     * Add Persian text to image
     */
    protected function addPersianText(
        $image,
        string $text,
        int $x,
        int $y,
        int $fontSize,
        string $color,
        string $align = 'center',
        float $opacity = 1.0
    ): void {
        // Convert Persian text for proper RTL rendering
        $text = $this->convertPersianText($text);
        
        // Add text to image only if we have a valid font
        if ($this->fontPath && file_exists($this->fontPath)) {
            $image->text($text, $x, $y, function($font) use ($fontSize, $color, $align, $opacity) {
                $font->file($this->fontPath);
                $font->size($fontSize);
                // Apply opacity to color if needed
                if ($opacity < 1.0) {
                    // Convert hex color to rgba with opacity
                    $hexColor = ltrim($color, '#');
                    $r = hexdec(substr($hexColor, 0, 2));
                    $g = hexdec(substr($hexColor, 2, 2));
                    $b = hexdec(substr($hexColor, 4, 2));
                    $alphaValue = (int)($opacity * 255);
                    $font->color("rgba($r, $g, $b, $opacity)");
                } else {
                    $font->color($color);
                }
                $font->align($align);
                $font->valign('middle');
            });
        } else {
            // If no font available, skip text rendering
            \Log::warning('No font available for text rendering', [
                'text' => substr($text, 0, 50)
            ]);
        }
    }
    
    /**
     * Wrap Persian text for proper display
     */
    protected function wrapPersianText(string $text, int $maxWidth, int $fontSize): string
    {
        // Estimate characters per line based on font size
        $charsPerLine = intval($maxWidth / ($fontSize * 0.5));
        
        // Split text into words
        $words = explode(' ', $text);
        $lines = [];
        $currentLine = '';
        
        foreach ($words as $word) {
            $testLine = $currentLine ? $currentLine . ' ' . $word : $word;
            
            if (mb_strlen($testLine) <= $charsPerLine) {
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
        
        return implode("\n", $lines);
    }
    
    /**
     * Convert Persian text for proper RTL rendering
     */
    protected function convertPersianText(string $text): string
    {
        try {
            // Use Arabic text shaper for proper Persian rendering
            $arabic = new Arabic();
            
            // Check if text contains newlines (multi-line text)
            if (strpos($text, "\n") !== false) {
                // Process each line separately to maintain proper shaping
                $lines = explode("\n", $text);
                $shapedLines = [];
                
                foreach ($lines as $line) {
                    // Shape each line individually
                    $shapedLines[] = $arabic->utf8Glyphs($line, 10000, true, false);
                }
                
                return implode("\n", $shapedLines);
            } else {
                // Single line text - process normally
                // The fourth parameter (false) prevents RTL reversal since GD will handle that
                $shapedText = $arabic->utf8Glyphs($text, 10000, true, false);
                return $shapedText;
            }
        } catch (\Exception $e) {
            \Log::warning('Persian text shaping failed', [
                'error' => $e->getMessage(),
                'text' => substr($text, 0, 50)
            ]);
            
            // Fallback: return original text
            return $text;
        }
    }
    
    /**
     * Basic Persian text reversal for RTL
     */
    protected function reversePersiand(string $text): string
    {
        // Split by lines
        $lines = explode("\n", $text);
        $reversed = [];
        
        foreach ($lines as $line) {
            // Reverse each line while preserving word order
            $words = explode(' ', $line);
            $reversed[] = implode(' ', array_reverse($words));
        }
        
        return implode("\n", $reversed);
    }
    
    /**
     * Calculate main title Y position
     */
    protected function calculateMainTitlePosition(int $height, bool $hasSubtitle): int
    {
        if ($hasSubtitle) {
            return intval($height * 0.35); // Higher if subtitle exists
        }
        return intval($height * 0.45); // Center if no subtitle
    }
    
    /**
     * Add website branding
     */
    protected function addBranding($image, array $options): void
    {
        $brandText = 'PishKhanak.com';
        $brandFontSize = 16;
        $brandPadding = 20;
        
        if ($this->fontPath && file_exists($this->fontPath)) {
            $image->text($brandText, $options['width'] - $brandPadding, $options['height'] - $brandPadding, function($font) use ($brandFontSize) {
                $font->file($this->fontPath);
                $font->size($brandFontSize);
                $font->color('rgba(255, 255, 255, 0.7)'); // White with 70% opacity
                $font->align('right');
                $font->valign('bottom');
            });
        }
    }
    
    /**
     * Load available background images
     */
    protected function loadBackgrounds(): void
    {
        $backgroundsPath = storage_path('app/public/backgrounds');
        
        // Create directory if not exists
        if (!file_exists($backgroundsPath)) {
            mkdir($backgroundsPath, 0755, true);
        }
        
        // Get all image files
        $files = glob($backgroundsPath . '/*.{jpg,jpeg,png,webp}', GLOB_BRACE);
        $this->backgrounds = $files;
        
        // Disabled: No longer create default gradients
        // if (empty($this->backgrounds)) {
        //     $this->createDefaultBackgrounds();
        // }
    }
    
    /**
     * Get random background image
     */
    protected function getRandomBackground(): string
    {
        if (empty($this->backgrounds)) {
            $this->loadBackgrounds();
        }
        
        return $this->backgrounds[array_rand($this->backgrounds)];
    }
    
    /**
     * Create default gradient backgrounds
     */
    protected function createDefaultBackgrounds(): void
    {
        $gradients = [
            ['#667eea', '#764ba2'], // Purple gradient
            ['#f093fb', '#f5576c'], // Pink gradient
            ['#4facfe', '#00f2fe'], // Blue gradient
            ['#43e97b', '#38f9d7'], // Green gradient
            ['#fa709a', '#fee140'], // Sunset gradient
            ['#30cfd0', '#330867'], // Ocean gradient
            ['#a8edea', '#fed6e3'], // Soft gradient
            ['#ff9a9e', '#fecfef'], // Rose gradient
        ];
        
        $backgroundsPath = storage_path('app/public/backgrounds');
        
        foreach ($gradients as $index => $colors) {
            // Create a gradient image with solid color for now (Intervention v3 doesn't have gradients)
            $image = $this->manager->create(1200, 630);
            
            // Fill with the first color as a fallback
            $image->fill($colors[0]);
            
            // Add a semi-transparent overlay with the second color for depth
            $overlay = $this->manager->create(1200, 630);
            $overlay->fill($colors[1]);
            $image->place($overlay, 'top-left', 0, 0, 30); // 30% opacity
            
            $filename = $backgroundsPath . '/gradient_' . ($index + 1) . '.jpg';
            $image->save($filename, quality: 90);
            $this->backgrounds[] = $filename;
        }
    }
    
    /**
     * Upload custom background
     */
    public function uploadBackground($file): string
    {
        $filename = 'background_' . Str::random(16) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('backgrounds', $filename, 'public');
        
        // Reload backgrounds
        $this->loadBackgrounds();
        
        return $path;
    }
    
    /**
     * Get all available backgrounds
     */
    public function getBackgrounds(): array
    {
        // Filter out gradient files and reload backgrounds
        $this->loadBackgrounds();
        
        $filteredBackgrounds = array_filter($this->backgrounds, function($path) {
            $filename = basename($path);
            // Exclude gradient files
            return !str_starts_with($filename, 'gradient_');
        });
        
        return array_map(function($path) {
            return [
                'path' => $path,
                'url' => Storage::url(str_replace(storage_path('app/public/'), '', $path)),
                'name' => basename($path)
            ];
        }, array_values($filteredBackgrounds));
    }
    
    /**
     * Generate a simple preview without text (to avoid font issues)
     */
    public function generateSimplePreview(?string $backgroundPath = null): string
    {
        try {
            // Get background
            if (!$backgroundPath) {
                $backgroundPath = $this->getRandomBackground();
            }
            
            // Create image from background
            $image = $this->manager->read($backgroundPath);
            
            // Resize to thumbnail dimensions
            $image->resize(400, 210);
            
            // Add a simple overlay for depth
            $overlay = $this->manager->create(400, 210);
            $overlay->fill('#000000');
            $image->place($overlay, 'top-left', 0, 0, 20); // 20% opacity
            
            // Save preview
            $filename = 'previews/' . Str::random(16) . '.jpg';
            $path = storage_path('app/public/' . $filename);
            
            // Ensure directory exists
            Storage::disk('public')->makeDirectory('previews');
            
            // Save image
            $image->save($path, quality: 75);
            
            return Storage::url($filename);
            
        } catch (\Exception $e) {
            \Log::error('Preview generation failed', [
                'error' => $e->getMessage()
            ]);
            
            // Return a placeholder or default image URL
            return '/images/default-preview.jpg';
        }
    }
    
    /**
     * Analyze background color in text area and determine optimal text colors
     */
    protected function analyzeTextAreaBackground($image, int $width, int $height, int $titleY, int $subtitleY): array
    {
        // Define the sampling area (center 200x200 px around where text will be)
        $sampleWidth = min(200, $width);
        $sampleHeight = min(200, $height);
        $centerX = $width / 2;
        $centerY = ($titleY + $subtitleY) / 2; // Middle of text area
        
        $startX = max(0, $centerX - $sampleWidth / 2);
        $startY = max(0, $centerY - $sampleHeight / 2);
        $endX = min($width, $centerX + $sampleWidth / 2);
        $endY = min($height, $centerY + $sampleHeight / 2);
        
        // Sample colors from the area
        $totalR = 0;
        $totalG = 0;
        $totalB = 0;
        $sampleCount = 0;
        
        // Sample every 10th pixel for performance
        $step = 10;
        
        for ($x = $startX; $x < $endX; $x += $step) {
            for ($y = $startY; $y < $endY; $y += $step) {
                try {
                    // Get pixel color at position
                    $color = $image->pickColor(intval($x), intval($y));
                    
                    // Extract RGB values (convert to integer)
                    $r = $color->red()->value();
                    $g = $color->green()->value();
                    $b = $color->blue()->value();
                    
                    $totalR += $r;
                    $totalG += $g;
                    $totalB += $b;
                    $sampleCount++;
                } catch (\Exception $e) {
                    // Skip if pixel is out of bounds
                    continue;
                }
            }
        }
        
        // Calculate average color
        if ($sampleCount > 0) {
            $avgR = $totalR / $sampleCount;
            $avgG = $totalG / $sampleCount;
            $avgB = $totalB / $sampleCount;
        } else {
            // Fallback to middle gray if no samples
            $avgR = $avgG = $avgB = 128;
        }
        
        // Calculate luminance (perceived brightness)
        // Using ITU-R BT.709 formula
        $luminance = (0.2126 * $avgR + 0.7152 * $avgG + 0.0722 * $avgB) / 255;
        
        // Determine text colors based on background luminance
        if ($luminance > 0.5) {
            // Light background - use dark text
            $mainColor = '#1f2937'; // Dark gray
            $subtitleColor = '#4b5563'; // Medium gray
        } else {
            // Dark background - use light text
            $mainColor = '#ffffff'; // White
            $subtitleColor = '#d1d5db'; // Light gray
        }
        
        // Log the analysis for debugging
        \Log::info('Background analysis', [
            'avg_rgb' => [$avgR, $avgG, $avgB],
            'luminance' => $luminance,
            'main_color' => $mainColor,
            'subtitle_color' => $subtitleColor
        ]);
        
        return [
            'main' => $mainColor,
            'subtitle' => $subtitleColor,
            'luminance' => $luminance,
            'is_light' => $luminance > 0.5
        ];
    }
}