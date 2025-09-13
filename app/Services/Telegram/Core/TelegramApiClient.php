<?php

namespace App\Services\Telegram\Core;

use App\Services\Telegram\Contracts\TelegramApiClientInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Psr\Log\LoggerInterface;

/**
 * Telegram API Client Implementation
 * 
 * Handles all communication with Telegram Bot API with proxy support,
 * retry logic, rate limiting, and comprehensive error handling.
 */
class TelegramApiClient implements TelegramApiClientInterface
{
    private const API_BASE_URL = 'https://api.telegram.org/bot';
    private const DEFAULT_TIMEOUT = 30;
    private const DEFAULT_CONNECT_TIMEOUT = 10;
    private const MAX_RETRIES = 3;
    
    public function __construct(
        private string $botToken,
        private array $proxyConfig = [],
        private ?LoggerInterface $logger = null
    ) {
        $this->logger = $logger ?? Log::getFacadeRoot();
    }

    /**
     * Send a text message to a chat
     */
    public function sendMessage(string $chatId, string $text, array $options = []): TelegramResponse
    {
        $params = array_merge([
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
        ], $options);

        return $this->sendRequest('sendMessage', $params);
    }

    /**
     * Edit an existing message
     */
    public function editMessage(string $chatId, int $messageId, string $text, array $options = []): TelegramResponse
    {
        $params = array_merge([
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'text' => $text,
            'parse_mode' => 'HTML',
        ], $options);

        return $this->sendRequest('editMessageText', $params);
    }

    /**
     * Delete a message
     */
    public function deleteMessage(string $chatId, int $messageId): TelegramResponse
    {
        $params = [
            'chat_id' => $chatId,
            'message_id' => $messageId,
        ];

        return $this->sendRequest('deleteMessage', $params);
    }

    /**
     * Send a photo with caption
     */
    public function sendPhoto(string $chatId, string $photo, string $caption = '', array $options = []): TelegramResponse
    {
        $params = array_merge([
            'chat_id' => $chatId,
            'photo' => $photo,
            'caption' => $caption,
            'parse_mode' => 'HTML',
        ], $options);

        return $this->sendRequest('sendPhoto', $params);
    }

    /**
     * Send a document
     */
    public function sendDocument(string $chatId, string $document, string $caption = '', array $options = []): TelegramResponse
    {
        $params = array_merge([
            'chat_id' => $chatId,
            'document' => $document,
            'caption' => $caption,
            'parse_mode' => 'HTML',
        ], $options);

        return $this->sendRequest('sendDocument', $params);
    }

    /**
     * Answer callback query (for inline keyboards)
     */
    public function answerCallbackQuery(string $callbackQueryId, string $text = '', bool $showAlert = false): TelegramResponse
    {
        $params = [
            'callback_query_id' => $callbackQueryId,
            'text' => $text,
            'show_alert' => $showAlert,
        ];

        return $this->sendRequest('answerCallbackQuery', $params);
    }

    /**
     * Get bot information
     */
    public function getMe(): TelegramResponse
    {
        return $this->sendRequest('getMe');
    }

    /**
     * Set webhook URL
     */
    public function setWebhook(string $url, array $options = []): TelegramResponse
    {
        $params = array_merge([
            'url' => $url,
        ], $options);

        return $this->sendRequest('setWebhook', $params);
    }

    /**
     * Remove webhook
     */
    public function deleteWebhook(): TelegramResponse
    {
        return $this->sendRequest('deleteWebhook');
    }

    /**
     * Get current webhook info
     */
    public function getWebhookInfo(): TelegramResponse
    {
        return $this->sendRequest('getWebhookInfo');
    }

    /**
     * Send chat action (typing, upload_photo, etc.)
     */
    public function sendChatAction(string $chatId, string $action): TelegramResponse
    {
        $params = [
            'chat_id' => $chatId,
            'action' => $action,
        ];

        return $this->sendRequest('sendChatAction', $params);
    }

    /**
     * Get chat information
     */
    public function getChat(string $chatId): TelegramResponse
    {
        $params = ['chat_id' => $chatId];
        return $this->sendRequest('getChat', $params);
    }

    /**
     * Get chat member information
     */
    public function getChatMember(string $chatId, string $userId): TelegramResponse
    {
        $params = [
            'chat_id' => $chatId,
            'user_id' => $userId,
        ];

        return $this->sendRequest('getChatMember', $params);
    }

    /**
     * Ban chat member
     */
    public function banChatMember(string $chatId, string $userId, array $options = []): TelegramResponse
    {
        $params = array_merge([
            'chat_id' => $chatId,
            'user_id' => $userId,
        ], $options);

        return $this->sendRequest('banChatMember', $params);
    }

    /**
     * Unban chat member
     */
    public function unbanChatMember(string $chatId, string $userId, array $options = []): TelegramResponse
    {
        $params = array_merge([
            'chat_id' => $chatId,
            'user_id' => $userId,
        ], $options);

        return $this->sendRequest('unbanChatMember', $params);
    }

    /**
     * Forward a message
     */
    public function forwardMessage(string $chatId, string $fromChatId, int $messageId, array $options = []): TelegramResponse
    {
        $params = array_merge([
            'chat_id' => $chatId,
            'from_chat_id' => $fromChatId,
            'message_id' => $messageId,
        ], $options);

        return $this->sendRequest('forwardMessage', $params);
    }

    /**
     * Send location
     */
    public function sendLocation(string $chatId, float $latitude, float $longitude, array $options = []): TelegramResponse
    {
        $params = array_merge([
            'chat_id' => $chatId,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ], $options);

        return $this->sendRequest('sendLocation', $params);
    }

    /**
     * Send venue
     */
    public function sendVenue(string $chatId, float $latitude, float $longitude, string $title, string $address, array $options = []): TelegramResponse
    {
        $params = array_merge([
            'chat_id' => $chatId,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'title' => $title,
            'address' => $address,
        ], $options);

        return $this->sendRequest('sendVenue', $params);
    }

    /**
     * Send contact
     */
    public function sendContact(string $chatId, string $phoneNumber, string $firstName, array $options = []): TelegramResponse
    {
        $params = array_merge([
            'chat_id' => $chatId,
            'phone_number' => $phoneNumber,
            'first_name' => $firstName,
        ], $options);

        return $this->sendRequest('sendContact', $params);
    }

    /**
     * Send poll
     */
    public function sendPoll(string $chatId, string $question, array $options, array $pollOptions = []): TelegramResponse
    {
        $params = array_merge([
            'chat_id' => $chatId,
            'question' => $question,
            'options' => json_encode($options),
        ], $pollOptions);

        return $this->sendRequest('sendPoll', $params);
    }

    /**
     * Stop poll
     */
    public function stopPoll(string $chatId, int $messageId, array $options = []): TelegramResponse
    {
        $params = array_merge([
            'chat_id' => $chatId,
            'message_id' => $messageId,
        ], $options);

        return $this->sendRequest('stopPoll', $params);
    }

    /**
     * Answer inline query
     */
    public function answerInlineQuery(string $inlineQueryId, array $results, array $options = []): TelegramResponse
    {
        $params = array_merge([
            'inline_query_id' => $inlineQueryId,
            'results' => json_encode($results),
        ], $options);

        return $this->sendRequest('answerInlineQuery', $params);
    }

    /**
     * Send HTTP request to Telegram API with retry logic
     */
    private function sendRequest(string $method, array $params = []): TelegramResponse
    {
        $startTime = microtime(true);
        $url = self::API_BASE_URL . $this->botToken . '/' . $method;
        
        // Check rate limit
        if ($this->isRateLimited($method)) {
            return TelegramResponse::error('Rate limit exceeded', 429, [
                'method' => $method,
                'retry_after' => $this->getRateLimitRetryAfter($method),
            ]);
        }

        $lastError = null;
        
        for ($attempt = 1; $attempt <= self::MAX_RETRIES; $attempt++) {
            try {
                $response = $this->makeHttpRequest($url, $params, $method, $attempt);
                
                // Log successful request metrics
                $this->logRequestMetrics($method, $startTime, $attempt, true);
                
                return $response;
                
            } catch (\Exception $e) {
                $lastError = $e;
                
                $this->logger->warning('Telegram API request failed', [
                    'method' => $method,
                    'attempt' => $attempt,
                    'max_attempts' => self::MAX_RETRIES,
                    'error' => $e->getMessage(),
                ]);
                
                // Don't retry on certain errors
                if (!$this->shouldRetry($e, $attempt)) {
                    break;
                }
                
                // Wait before retry (exponential backoff)
                if ($attempt < self::MAX_RETRIES) {
                    sleep(min(pow(2, $attempt), 10)); // Max 10 seconds
                }
            }
        }
        
        // Log failed request metrics
        $this->logRequestMetrics($method, $startTime, self::MAX_RETRIES, false);
        
        return TelegramResponse::error(
            'Request failed after ' . self::MAX_RETRIES . ' attempts: ' . ($lastError?->getMessage() ?? 'Unknown error'),
            0,
            ['method' => $method, 'attempts' => self::MAX_RETRIES]
        );
    }

    /**
     * Make HTTP request using cURL
     */
    private function makeHttpRequest(string $url, array $params, string $method, int $attempt): TelegramResponse
    {
        $ch = curl_init();
        
        $curlOptions = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => self::DEFAULT_TIMEOUT,
            CURLOPT_CONNECTTIMEOUT => self::DEFAULT_CONNECT_TIMEOUT,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($params),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json',
                'User-Agent: PishkhanakBot/1.0',
            ],
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3,
        ];

        // Configure proxy if enabled
        if (!empty($this->proxyConfig['enabled'])) {
            $curlOptions[CURLOPT_PROXY] = $this->proxyConfig['host'] . ':' . $this->proxyConfig['port'];
            $curlOptions[CURLOPT_PROXYTYPE] = $this->getCurlProxyType();
            
            if (!empty($this->proxyConfig['username'])) {
                $curlOptions[CURLOPT_PROXYUSERPWD] = $this->proxyConfig['username'] . ':' . ($this->proxyConfig['password'] ?? '');
            }
        }

        curl_setopt_array($ch, $curlOptions);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        if ($error) {
            throw new \RuntimeException("cURL Error: {$error}");
        }

        if ($httpCode !== 200) {
            throw new \RuntimeException("HTTP Error {$httpCode}: " . ($response ?: 'No response body'));
        }

        $decodedResponse = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Invalid JSON response: ' . json_last_error_msg());
        }

        $metadata = [
            'http_code' => $httpCode,
            'total_time' => $info['total_time'] ?? 0,
            'method' => $method,
            'attempt' => $attempt,
        ];

        return TelegramResponse::fromTelegramResponse($decodedResponse, $metadata);
    }

    /**
     * Get cURL proxy type from config
     */
    private function getCurlProxyType(): int
    {
        return match (strtolower($this->proxyConfig['type'] ?? 'http')) {
            'socks5' => CURLPROXY_SOCKS5,
            'socks4' => CURLPROXY_SOCKS4,
            'http', 'https' => CURLPROXY_HTTP,
            default => CURLPROXY_HTTP,
        };
    }

    /**
     * Check if should retry based on exception
     */
    private function shouldRetry(\Exception $e, int $attempt): bool
    {
        if ($attempt >= self::MAX_RETRIES) {
            return false;
        }

        $message = strtolower($e->getMessage());
        
        // Don't retry client errors (4xx)
        if (str_contains($message, 'http error 4')) {
            return false;
        }
        
        // Retry on network errors and 5xx errors
        $retryableErrors = [
            'timeout',
            'connection reset',
            'connection refused',
            'network error',
            'http error 5',
            'temporary failure',
        ];
        
        foreach ($retryableErrors as $error) {
            if (str_contains($message, $error)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Simple rate limiting check
     */
    private function isRateLimited(string $method): bool
    {
        $key = "telegram_api_rate_limit:{$this->botToken}:{$method}";
        $requests = Cache::get($key, 0);
        
        // Allow 30 requests per minute per method
        return $requests >= 30;
    }

    /**
     * Get rate limit retry after seconds
     */
    private function getRateLimitRetryAfter(string $method): int
    {
        $key = "telegram_api_rate_limit:{$this->botToken}:{$method}";
        return Cache::store('redis')->ttl($key) ?? 60;
    }

    /**
     * Log request metrics for monitoring
     */
    private function logRequestMetrics(string $method, float $startTime, int $attempts, bool $success): void
    {
        $duration = round((microtime(true) - $startTime) * 1000, 2);
        
        $this->logger->info('Telegram API request completed', [
            'method' => $method,
            'success' => $success,
            'duration_ms' => $duration,
            'attempts' => $attempts,
            'proxy_enabled' => !empty($this->proxyConfig['enabled']),
        ]);

        // Increment rate limit counter for successful requests
        if ($success) {
            $key = "telegram_api_rate_limit:{$this->botToken}:{$method}";
            $requests = Cache::get($key, 0) + 1;
            Cache::put($key, $requests, 60); // 1 minute TTL
        }
    }
}