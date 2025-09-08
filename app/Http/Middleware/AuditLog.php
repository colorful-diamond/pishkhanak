<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuditLog
{
    /**
     * Security-sensitive actions that should always be logged
     */
    protected array $auditableActions = [
        // Authentication
        'login',
        'logout',
        'register',
        'password.reset',
        'password.update',
        
        // User management
        'user.create',
        'user.update',
        'user.delete',
        'profile.update',
        
        // Payment operations
        'payment.create',
        'payment.callback',
        'transaction.create',
        
        // Admin operations
        'admin.',
        'filament.',
        
        // File operations
        'file.upload',
        'file.delete',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Log the request if it's an auditable action
        if ($this->shouldAudit($request)) {
            $this->logAudit($request, $response);
        }
        
        return $response;
    }

    /**
     * Determine if the request should be audited
     */
    protected function shouldAudit(Request $request): bool
    {
        $routeName = $request->route()?->getName() ?? '';
        $path = $request->path();
        
        // Check if it's a write operation (POST, PUT, PATCH, DELETE)
        $isWriteOperation = in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE']);
        
        // Check if it matches auditable actions
        foreach ($this->auditableActions as $action) {
            if (str_contains($routeName, $action) || str_contains($path, $action)) {
                return true;
            }
        }
        
        // Always audit admin/filament actions
        if (str_starts_with($path, 'admin/') || str_starts_with($path, 'filament/')) {
            return $isWriteOperation;
        }
        
        // Audit failed authentication attempts
        if ($request->is('*/login') && $request->isMethod('POST')) {
            return true;
        }
        
        return false;
    }

    /**
     * Log the audit entry
     */
    protected function logAudit(Request $request, Response $response): void
    {
        $user = Auth::user();
        $routeName = $request->route()?->getName() ?? 'unknown';
        
        // Prepare audit data
        $auditData = [
            'timestamp' => now()->toIso8601String(),
            'user_id' => $user?->id,
            'user_email' => $user?->email,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'method' => $request->method(),
            'path' => $request->path(),
            'route_name' => $routeName,
            'status_code' => $response->getStatusCode(),
            'session_id' => session()->getId(),
        ];
        
        // Add request data (excluding sensitive fields)
        $requestData = $this->sanitizeRequestData($request->all());
        if (!empty($requestData)) {
            $auditData['request_data'] = $requestData;
        }
        
        // Determine log level based on response
        $logLevel = $this->determineLogLevel($response->getStatusCode(), $routeName);
        
        // Log to security channel
        Log::channel('security')->{$logLevel}('Security Audit', $auditData);
        
        // For critical actions, also send alerts
        if ($this->isCriticalAction($routeName, $response)) {
            $this->alertCriticalAction($auditData);
        }
    }

    /**
     * Sanitize request data to remove sensitive information
     */
    protected function sanitizeRequestData(array $data): array
    {
        $sensitiveFields = [
            'password',
            'password_confirmation',
            'current_password',
            'new_password',
            'token',
            'api_key',
            'secret',
            'credit_card',
            'cvv',
            'card_number',
            '_token', // CSRF token
        ];
        
        foreach ($sensitiveFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = '[REDACTED]';
            }
        }
        
        // Recursively sanitize nested arrays
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->sanitizeRequestData($value);
            }
        }
        
        return $data;
    }

    /**
     * Determine the appropriate log level
     */
    protected function determineLogLevel(int $statusCode, string $routeName): string
    {
        // Failed authentication attempts
        if (str_contains($routeName, 'login') && $statusCode >= 400) {
            return 'warning';
        }
        
        // Server errors
        if ($statusCode >= 500) {
            return 'error';
        }
        
        // Client errors (potential security issues)
        if ($statusCode >= 400) {
            return 'warning';
        }
        
        // Successful security-relevant actions
        if (str_contains($routeName, 'admin') || str_contains($routeName, 'delete')) {
            return 'notice';
        }
        
        return 'info';
    }

    /**
     * Check if this is a critical action requiring immediate alerts
     */
    protected function isCriticalAction(string $routeName, Response $response): bool
    {
        $criticalActions = [
            'user.delete',
            'admin.settings',
            'database.reset',
            'payment.refund',
        ];
        
        foreach ($criticalActions as $action) {
            if (str_contains($routeName, $action)) {
                return true;
            }
        }
        
        // Multiple failed login attempts
        if (str_contains($routeName, 'login') && $response->getStatusCode() === 429) {
            return true;
        }
        
        return false;
    }

    /**
     * Send alert for critical actions
     */
    protected function alertCriticalAction(array $auditData): void
    {
        // Log critical alert
        Log::channel('security')->critical('CRITICAL SECURITY ACTION DETECTED', $auditData);
        
        // Here you could also:
        // - Send email notification to admin
        // - Send SMS alert
        // - Trigger webhook to monitoring service
        // - Store in dedicated security events table
    }
}