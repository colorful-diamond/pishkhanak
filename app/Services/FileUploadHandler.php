<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

class FileUploadHandler
{
    // File size limits (in bytes)
    private const MAX_IMAGE_SIZE = 10 * 1024 * 1024; // 10MB
    private const MAX_DOCUMENT_SIZE = 20 * 1024 * 1024; // 20MB
    
    // Allowed MIME types
    private const ALLOWED_IMAGE_TYPES = [
        'image/jpeg',
        'image/png', 
        'image/webp',
        'image/gif'
    ];
    
    private const ALLOWED_DOCUMENT_TYPES = [
        'application/pdf',
        'text/plain',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ];
    
    // Dangerous file extensions
    private const DANGEROUS_EXTENSIONS = [
        'exe', 'bat', 'cmd', 'com', 'pif', 'scr', 'vbs', 'js', 'jar', 
        'php', 'html', 'htm', 'asp', 'aspx', 'jsp', 'sh', 'py', 'rb'
    ];
    
    // Magic number signatures for file type validation
    private const FILE_SIGNATURES = [
        'image/jpeg' => [
            'ffd8ff',
            'ffd8ffe0',
            'ffd8ffe1',
            'ffd8ffdb'
        ],
        'image/png' => [
            '89504e47'
        ],
        'image/gif' => [
            '474946383761',
            '474946383961'
        ],
        'image/webp' => [
            '52494646'
        ],
        'application/pdf' => [
            '25504446'
        ]
    ];
    
    /**
     * Process and validate uploaded file
     */
    public function processUpload(UploadedFile $file, array $options = []): array
    {
        try {
            // Basic validation
            $this->validateFile($file);
            
            // Security checks
            $this->performSecurityChecks($file);
            
            // Store file temporarily
            $storedFile = $this->storeTemporarily($file);
            
            // Extract content if possible
            $content = $this->extractContent($storedFile['path'], $file);
            
            return [
                'success' => true,
                'file_info' => array_merge($storedFile, [
                    'content' => $content,
                    'is_safe' => true
                ])
            ];
            
        } catch (Exception $e) {
            Log::error('File upload failed: ' . $e->getMessage(), [
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Validate file basic properties
     */
    protected function validateFile(UploadedFile $file): void
    {
        if (!$file->isValid()) {
            throw new Exception('فایل آپلود شده معتبر نیست.');
        }
        
        // Check file size
        $maxSize = $this->getMaxSizeForType($file);
        if ($file->getSize() > $maxSize) {
            $maxSizeMB = round($maxSize / (1024 * 1024), 1);
            throw new Exception("حجم فایل نباید بیش از {$maxSizeMB} مگابایت باشد.");
        }
        
        // Check MIME type
        if (!$this->isMimeTypeAllowed($file->getMimeType())) {
            throw new Exception('نوع فایل مجاز نیست.');
        }
        
        // Check file extension
        $extension = strtolower($file->getClientOriginalExtension());
        if (in_array($extension, self::DANGEROUS_EXTENSIONS)) {
            throw new Exception('پسوند فایل مجاز نیست.');
        }
    }
    
    /**
     * Perform security checks on the file
     */
    protected function performSecurityChecks(UploadedFile $file): void
    {
        // Check file signature (magic numbers)
        if (!$this->validateFileSignature($file)) {
            throw new Exception('فایل ممکن است جعلی باشد.');
        }
        
        // Check for embedded scripts in images
        if ($this->isImageFile($file)) {
            $this->checkImageSecurity($file);
        }
        
        // Check for malicious content in documents
        if ($this->isDocumentFile($file)) {
            $this->checkDocumentSecurity($file);
        }
    }
    
    /**
     * Validate file signature using magic numbers
     */
    protected function validateFileSignature(UploadedFile $file): bool
    {
        $mimeType = $file->getMimeType();
        
        if (!isset(self::FILE_SIGNATURES[$mimeType])) {
            // If we don't have signature for this type, allow it
            return true;
        }
        
        $handle = fopen($file->getRealPath(), 'rb');
        if (!$handle) {
            return false;
        }
        
        $bytes = fread($handle, 16);
        fclose($handle);
        
        $hex = bin2hex($bytes);
        $signatures = self::FILE_SIGNATURES[$mimeType];
        
        foreach ($signatures as $signature) {
            if (strpos($hex, $signature) === 0) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check image file security
     */
    protected function checkImageSecurity(UploadedFile $file): void
    {
        $content = file_get_contents($file->getRealPath());
        
        // Check for embedded PHP code
        $suspiciousPatterns = [
            '/<\?php/i',
            '/<script/i',
            '/javascript:/i',
            '/vbscript:/i',
            '/onload=/i',
            '/onerror=/i'
        ];
        
        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                throw new Exception('فایل تصویری حاوی کد مشکوک است.');
            }
        }
        
        // Additional check using getimagesize for images
        $imageInfo = @getimagesize($file->getRealPath());
        if ($imageInfo === false) {
            throw new Exception('فایل تصویری معتبر نیست.');
        }
    }
    
    /**
     * Check document file security
     */
    protected function checkDocumentSecurity(UploadedFile $file): void
    {
        $mimeType = $file->getMimeType();
        
        if ($mimeType === 'text/plain') {
            $content = file_get_contents($file->getRealPath());
            
            // Check for suspicious content in text files
            $suspiciousPatterns = [
                '/eval\s*\(/i',
                '/exec\s*\(/i',
                '/system\s*\(/i',
                '/shell_exec\s*\(/i',
                '/<script/i'
            ];
            
            foreach ($suspiciousPatterns as $pattern) {
                if (preg_match($pattern, $content)) {
                    throw new Exception('فایل متنی حاوی محتوای مشکوک است.');
                }
            }
        }
    }
    
    /**
     * Store file temporarily with secure naming
     */
    protected function storeTemporarily(UploadedFile $file): array
    {
        $extension = $file->getClientOriginalExtension();
        $secureFilename = Str::uuid() . '.' . $extension;
        $directory = 'temp/chat_uploads/' . date('Y/m/d');
        
        $path = $file->storeAs($directory, $secureFilename, 'local');
        
        return [
            'original_name' => $file->getClientOriginalName(),
            'secure_filename' => $secureFilename,
            'path' => $path,
            'full_path' => Storage::disk('local')->path($path),
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'extension' => $extension,
            'type' => $this->getFileTypeCategory($file),
            'url' => $this->generateSecureUrl($path)
        ];
    }
    
    /**
     * Extract content from file for AI analysis
     */
    protected function extractContent(string $path, UploadedFile $originalFile): ?string
    {
        $mimeType = $originalFile->getMimeType();
        $fullPath = Storage::disk('local')->path($path);
        
        try {
            switch ($mimeType) {
                case 'text/plain':
                    return $this->extractTextContent($fullPath);
                    
                case 'application/pdf':
                    return $this->extractPdfContent($fullPath);
                    
                case 'image/jpeg':
                case 'image/png':
                case 'image/webp':
                case 'image/gif':
                    return $this->extractImageContent($fullPath);
                    
                default:
                    return null;
            }
        } catch (Exception $e) {
            Log::warning('Content extraction failed: ' . $e->getMessage(), [
                'file_path' => $path,
                'mime_type' => $mimeType
            ]);
            return null;
        }
    }
    
    /**
     * Extract text content
     */
    protected function extractTextContent(string $filePath): string
    {
        $content = file_get_contents($filePath);
        
        // Limit content size for AI processing
        if (strlen($content) > 10000) {
            $content = substr($content, 0, 10000) . '...';
        }
        
        return $content;
    }
    
    /**
     * Extract PDF content (placeholder)
     */
    protected function extractPdfContent(string $filePath): ?string
    {
        // Placeholder for PDF text extraction
        // In production, use libraries like Smalot\PdfParser or pdftotext
        return 'محتوای PDF شناسایی شد ولی استخراج متن در حال حاضر پشتیبانی نمی‌شود.';
    }
    
    /**
     * Extract image content using OCR (placeholder)
     */
    protected function extractImageContent(string $filePath): ?string
    {
        // Placeholder for OCR implementation
        // In production, integrate with Google Vision API or Tesseract
        return 'تصویر شناسایی شد. برای استخراج متن از تصویر به OCR نیاز است.';
    }
    
    /**
     * Generate secure URL for file access
     */
    protected function generateSecureUrl(string $path): string
    {
        // Generate a temporary signed URL that expires
        $token = Str::random(32);
        $expires = now()->addHours(2)->timestamp;
        
        // Store token mapping temporarily
        $cacheKey = "file_access_token:{$token}";
        cache()->put($cacheKey, $path, 7200); // 2 hours
        
        return url("/api/file-access/{$token}");
    }
    
    /**
     * Get maximum file size for file type
     */
    protected function getMaxSizeForType(UploadedFile $file): int
    {
        if ($this->isImageFile($file)) {
            return self::MAX_IMAGE_SIZE;
        } elseif ($this->isDocumentFile($file)) {
            return self::MAX_DOCUMENT_SIZE;
        }
        
        return self::MAX_IMAGE_SIZE; // Default
    }
    
    /**
     * Check if MIME type is allowed
     */
    protected function isMimeTypeAllowed(string $mimeType): bool
    {
        return in_array($mimeType, array_merge(
            self::ALLOWED_IMAGE_TYPES,
            self::ALLOWED_DOCUMENT_TYPES
        ));
    }
    
    /**
     * Check if file is an image
     */
    protected function isImageFile(UploadedFile $file): bool
    {
        return in_array($file->getMimeType(), self::ALLOWED_IMAGE_TYPES);
    }
    
    /**
     * Check if file is a document
     */
    protected function isDocumentFile(UploadedFile $file): bool
    {
        return in_array($file->getMimeType(), self::ALLOWED_DOCUMENT_TYPES);
    }
    
    /**
     * Get file type category
     */
    protected function getFileTypeCategory(UploadedFile $file): string
    {
        if ($this->isImageFile($file)) {
            return 'image';
        } elseif ($this->isDocumentFile($file)) {
            return 'document';
        }
        
        return 'unknown';
    }
    
    /**
     * Clean up temporary files older than specified time
     */
    public function cleanupOldFiles(int $hoursOld = 24): int
    {
        $deletedCount = 0;
        $directory = 'temp/chat_uploads';
        
        try {
            $files = Storage::disk('local')->allFiles($directory);
            $cutoffTime = now()->subHours($hoursOld)->timestamp;
            
            foreach ($files as $file) {
                $lastModified = Storage::disk('local')->lastModified($file);
                
                if ($lastModified < $cutoffTime) {
                    Storage::disk('local')->delete($file);
                    $deletedCount++;
                }
            }
            
        } catch (Exception $e) {
            Log::error('File cleanup failed: ' . $e->getMessage());
        }
        
        return $deletedCount;
    }
    
    /**
     * Delete specific file
     */
    public function deleteFile(string $path): bool
    {
        try {
            if (Storage::disk('local')->exists($path)) {
                Storage::disk('local')->delete($path);
                return true;
            }
            return false;
        } catch (Exception $e) {
            Log::error('File deletion failed: ' . $e->getMessage(), ['path' => $path]);
            return false;
        }
    }
    
    /**
     * Get file by access token
     */
    public function getFileByToken(string $token): ?array
    {
        $cacheKey = "file_access_token:{$token}";
        $path = cache()->get($cacheKey);
        
        if (!$path || !Storage::disk('local')->exists($path)) {
            return null;
        }
        
        $fullPath = Storage::disk('local')->path($path);
        
        return [
            'path' => $path,
            'full_path' => $fullPath,
            'mime_type' => mime_content_type($fullPath),
            'size' => Storage::disk('local')->size($path)
        ];
    }
} 