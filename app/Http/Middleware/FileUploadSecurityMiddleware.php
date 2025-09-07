<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class FileUploadSecurityMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if request has files
        if ($request->hasFile('files')) {
            $files = $request->file('files');
            
            // Ensure files is an array
            if (!is_array($files)) {
                $files = [$files];
            }
            
            foreach ($files as $file) {
                if (!$this->validateFileUpload($file)) {
                    Log::warning('Malicious file upload attempt blocked', [
                        'ip' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'file_name' => $file->getClientOriginalName(),
                        'file_type' => $file->getMimeType(),
                        'file_size' => $file->getSize()
                    ]);
                    
                    return response()->json([
                        'success' => false,
                        'message' => 'فایل آپلود شده امن نیست.'
                    ], 400);
                }
            }
        }
        
        return $next($request);
    }
    
    /**
     * Validate file upload security
     */
    protected function validateFileUpload($file): bool
    {
        // Check if file is valid
        if (!$file->isValid()) {
            return false;
        }
        
        // Check file size (20MB max)
        if ($file->getSize() > 20 * 1024 * 1024) {
            return false;
        }
        
        // Check dangerous extensions
        $dangerousExtensions = [
            'exe', 'bat', 'cmd', 'com', 'pif', 'scr', 'vbs', 'js', 'jar',
            'php', 'html', 'htm', 'asp', 'aspx', 'jsp', 'sh', 'py', 'rb'
        ];
        
        $extension = strtolower($file->getClientOriginalExtension());
        if (in_array($extension, $dangerousExtensions)) {
            return false;
        }
        
        // Check MIME type
        $allowedMimeTypes = [
            'image/jpeg', 'image/png', 'image/webp', 'image/gif',
            'application/pdf', 'text/plain', 'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
        
        if (!in_array($file->getMimeType(), $allowedMimeTypes)) {
            return false;
        }
        
        // Check file signature for images
        if (strpos($file->getMimeType(), 'image/') === 0) {
            return $this->validateImageFile($file);
        }
        
        return true;
    }
    
    /**
     * Validate image file security
     */
    protected function validateImageFile($file): bool
    {
        // Check if it's a real image
        $imageInfo = @getimagesize($file->getRealPath());
        if ($imageInfo === false) {
            return false;
        }
        
        // Read file content to check for embedded scripts
        $content = file_get_contents($file->getRealPath());
        
        // Check for suspicious patterns
        $suspiciousPatterns = [
            '/<\?php/i',
            '/<script/i',
            '/javascript:/i',
            '/vbscript:/i',
            '/onload=/i',
            '/onerror=/i',
            '/eval\s*\(/i',
            '/base64_decode/i'
        ];
        
        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return false;
            }
        }
        
        return true;
    }
} 