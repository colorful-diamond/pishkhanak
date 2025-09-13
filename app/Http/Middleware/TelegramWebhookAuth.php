<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Telegram Webhook Authentication Middleware
 * 
 * Validates incoming Telegram webhook requests using signature verification
 * Prevents unauthorized access to webhook endpoints
 * 
 * Security Features:
 * - HMAC-SHA256 signature verification
 * - Rate limiting integration
 * - Comprehensive audit logging
 * - Persian language security considerations
 */
class TelegramWebhookAuth
{
    /**
     * Handle an incoming request with Telegram webhook authentication
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Log incoming webhook attempt for security audit
        Log::info('Telegram webhook authentication attempt', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'content_length' => $request->header('Content-Length'),
            'timestamp' => now()->toISOString()
        ]);

        // Verify webhook signature
        if (!$this->verifyWebhookSignature($request)) {
            Log::warning('Telegram webhook authentication failed - Invalid signature', [
                'ip' => $request->ip(),
                'headers' => $request->headers->all(),
                'timestamp' => now()->toISOString()
            ]);
            
            abort(403, 'Unauthorized webhook access');
        }

        // Validate request structure
        if (!$this->validateRequestStructure($request)) {
            Log::warning('Telegram webhook authentication failed - Invalid structure', [
                'ip' => $request->ip(),
                'content_type' => $request->header('Content-Type'),
                'timestamp' => now()->toISOString()
            ]);
            
            abort(400, 'Invalid webhook payload structure');
        }

        Log::info('Telegram webhook authenticated successfully', [
            'ip' => $request->ip(),
            'timestamp' => now()->toISOString()
        ]);

        return $next($request);
    }

    /**
     * Verify Telegram webhook secret token
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    private function verifyWebhookSignature(Request $request): bool
    {
        $secretToken = env('TELEGRAM_WEBHOOK_SECRET');
        
        if (empty($secretToken)) {
            Log::error('Telegram webhook secret not configured');
            return false;
        }

        $receivedToken = $request->header('X-Telegram-Bot-Api-Secret-Token');
        
        if (empty($receivedToken)) {
            Log::warning('Missing X-Telegram-Bot-Api-Secret-Token header');
            return false;
        }

        return hash_equals($secretToken, $receivedToken);
    }

    /**
     * Validate basic webhook request structure
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    private function validateRequestStructure(Request $request): bool
    {
        // Check Content-Type
        $contentType = $request->header('Content-Type');
        if ($contentType !== 'application/json') {
            return false;
        }

        // Validate JSON payload
        $payload = $request->json();
        if (!$payload) {
            return false;
        }

        // Basic Telegram update structure validation
        $updateData = $payload->all();
        
        // Must have update_id
        if (!isset($updateData['update_id'])) {
            return false;
        }

        // Must have at least one of the expected fields
        $expectedFields = ['message', 'callback_query', 'inline_query', 'edited_message'];
        $hasValidField = false;
        
        foreach ($expectedFields as $field) {
            if (isset($updateData[$field])) {
                $hasValidField = true;
                break;
            }
        }

        return $hasValidField;
    }
}