<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InputValidation
{
    /**
     * Dangerous patterns that should be blocked
     */
    protected array $dangerousPatterns = [
        // SQL Injection patterns
        '/(\bunion\b.*\bselect\b|\bselect\b.*\bfrom\b|\binsert\b.*\binto\b|\bdelete\b.*\bfrom\b|\bdrop\b.*\btable\b|\bupdate\b.*\bset\b)/i',
        '/(\bexec\b|\bexecute\b|\bcast\b|\bdeclare\b|\bwaitfor\b|\bdelay\b)/i',
        
        // XSS patterns
        '/<script[^>]*>.*?<\/script>/is',
        '/javascript:/i',
        '/on\w+\s*=/i', // Event handlers like onclick=
        
        // Command injection patterns
        '/[;&|`$()].*?(cat|ls|wget|curl|bash|sh|cmd|powershell)/i',
        
        // Path traversal patterns
        '/\.\.\/|\.\.\\\\/',
        
        // PHP injection patterns
        '/<\?php|<\?=/i',
        '/eval\s*\(/i',
        '/system\s*\(/i',
        '/exec\s*\(/i',
        '/passthru\s*\(/i',
        '/shell_exec\s*\(/i',
    ];

    /**
     * Fields that should be strictly validated
     */
    protected array $strictFields = [
        'email' => '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
        'phone' => '/^(\+98|0)?9\d{9}$/',
        'mobile' => '/^(\+98|0)?9\d{9}$/',
        'national_code' => '/^\d{10}$/',
        'postal_code' => '/^\d{10}$/',
        'card_number' => '/^\d{16}$/',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip validation for admin panel routes (trusted authenticated users)
        if ($this->isAdminPanelRoute($request)) {
            return $next($request);
        }
        
        // Validate all input parameters
        $this->validateInput($request);
        
        // Sanitize input
        $this->sanitizeInput($request);
        
        // Check for dangerous patterns
        if ($this->hasDangerousPatterns($request)) {
            return response()->json([
                'message' => 'Invalid input detected. Request blocked for security reasons.'
            ], 400);
        }
        
        return $next($request);
    }

    /**
     * Check if the current request is for an admin panel route
     */
    protected function isAdminPanelRoute(Request $request): bool
    {
        $path = $request->path();
        
        // Skip validation for admin panel routes
        $adminRoutes = [
            'access',           // Filament admin panel
            'livewire',         // Livewire requests
            '_debugbar',        // Laravel Debugbar
        ];
        
        foreach ($adminRoutes as $adminRoute) {
            if (str_starts_with($path, $adminRoute)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Validate input based on field rules
     */
    protected function validateInput(Request $request): void
    {
        foreach ($this->strictFields as $field => $pattern) {
            if ($request->has($field)) {
                $value = $request->input($field);
                if (!preg_match($pattern, $value)) {
                    abort(422, "Invalid format for field: {$field}");
                }
            }
        }
    }

    /**
     * Sanitize input data
     */
    protected function sanitizeInput(Request $request): void
    {
        $input = $request->all();
        
        array_walk_recursive($input, function (&$value) {
            if (is_string($value)) {
                // Remove null bytes
                $value = str_replace(chr(0), '', $value);
                
                // Trim whitespace
                $value = trim($value);
                
                // Remove control characters except newline and tab
                $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $value);
                
                // Limit string length to prevent overflow attacks
                if (strlen($value) > 10000) {
                    $value = substr($value, 0, 10000);
                }
            }
        });
        
        $request->merge($input);
    }

    /**
     * Check if request contains dangerous patterns
     */
    protected function hasDangerousPatterns(Request $request): bool
    {
        $input = $request->all();
        
        // Check URL parameters
        $url = $request->fullUrl();
        foreach ($this->dangerousPatterns as $pattern) {
            if (preg_match($pattern, $url)) {
                return true;
            }
        }
        
        // Check all input recursively
        $checkValue = function ($value) use (&$checkValue) {
            if (is_array($value)) {
                foreach ($value as $item) {
                    if ($checkValue($item)) {
                        return true;
                    }
                }
            } elseif (is_string($value)) {
                foreach ($this->dangerousPatterns as $pattern) {
                    if (preg_match($pattern, $value)) {
                        return true;
                    }
                }
            }
            return false;
        };
        
        return $checkValue($input);
    }

    /**
     * Additional validation for file uploads
     */
    protected function validateFileUploads(Request $request): void
    {
        if ($request->hasFile('file') || $request->hasFile('image') || $request->hasFile('document')) {
            $allowedMimes = [
                'image/jpeg',
                'image/png',
                'image/gif',
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ];
            
            foreach ($request->allFiles() as $file) {
                if (!in_array($file->getMimeType(), $allowedMimes)) {
                    abort(422, 'Invalid file type');
                }
                
                // Check file size (max 10MB)
                if ($file->getSize() > 10 * 1024 * 1024) {
                    abort(422, 'File size exceeds maximum allowed size');
                }
                
                // Check for double extensions
                $filename = $file->getClientOriginalName();
                if (preg_match('/\.(php|phtml|php3|php4|php5|php7|phps|phar|exe|sh|bat|cmd|com)/i', $filename)) {
                    abort(422, 'Potentially dangerous file extension detected');
                }
            }
        }
    }
}