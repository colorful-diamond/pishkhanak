<?php

namespace App\Services\Telegram\Core;

/**
 * Telegram API Response Value Object
 * 
 * Encapsulates Telegram API response data with success/failure state
 * Provides type safety and consistent error handling
 */
class TelegramResponse
{
    public function __construct(
        private bool $success,
        private array $data = [],
        private ?string $error = null,
        private ?int $errorCode = null,
        private array $metadata = []
    ) {}

    /**
     * Create successful response
     */
    public static function success(array $data = [], array $metadata = []): self
    {
        return new self(true, $data, null, null, $metadata);
    }

    /**
     * Create error response
     */
    public static function error(string $error, int $errorCode = 0, array $metadata = []): self
    {
        return new self(false, [], $error, $errorCode, $metadata);
    }

    /**
     * Create from Telegram API response array
     */
    public static function fromTelegramResponse(array $response, array $metadata = []): self
    {
        if (isset($response['ok']) && $response['ok']) {
            return self::success($response['result'] ?? [], $metadata);
        } else {
            return self::error(
                $response['description'] ?? 'Unknown error',
                $response['error_code'] ?? 0,
                $metadata
            );
        }
    }

    /**
     * Check if response is successful
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * Check if response is an error
     */
    public function isError(): bool
    {
        return !$this->success;
    }

    /**
     * Get response data
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Get specific data field
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * Get error message
     */
    public function getError(): ?string
    {
        return $this->error;
    }

    /**
     * Get error code
     */
    public function getErrorCode(): ?int
    {
        return $this->errorCode;
    }

    /**
     * Get response metadata
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * Get specific metadata field
     */
    public function getMetadataValue(string $key, mixed $default = null): mixed
    {
        return $this->metadata[$key] ?? $default;
    }

    /**
     * Get message ID from send message response
     */
    public function getMessageId(): ?int
    {
        return $this->get('message_id');
    }

    /**
     * Get message from response
     */
    public function getMessage(): ?array
    {
        return $this->get('message');
    }

    /**
     * Get callback query from response
     */
    public function getCallbackQuery(): ?array
    {
        return $this->get('callback_query');
    }

    /**
     * Get inline query from response
     */
    public function getInlineQuery(): ?array
    {
        return $this->get('inline_query');
    }

    /**
     * Get chat from response
     */
    public function getChat(): ?array
    {
        return $this->get('chat');
    }

    /**
     * Get user from response
     */
    public function getUser(): ?array
    {
        return $this->get('from') ?? $this->get('user');
    }

    /**
     * Convert to array representation
     */
    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'data' => $this->data,
            'error' => $this->error,
            'error_code' => $this->errorCode,
            'metadata' => $this->metadata,
        ];
    }

    /**
     * Convert to JSON string
     */
    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Check if response contains specific error type
     */
    public function hasError(string $errorType): bool
    {
        return $this->isError() && str_contains(strtolower($this->error ?? ''), strtolower($errorType));
    }

    /**
     * Check if error is retryable
     */
    public function isRetryableError(): bool
    {
        if (!$this->isError()) {
            return false;
        }

        $retryableErrors = [
            'timeout',
            'network error',
            'connection reset',
            'temporary failure',
            'service unavailable',
            'too many requests',
        ];

        $error = strtolower($this->error ?? '');
        foreach ($retryableErrors as $retryableError) {
            if (str_contains($error, $retryableError)) {
                return true;
            }
        }

        // HTTP 5xx errors are typically retryable
        return $this->errorCode >= 500 && $this->errorCode < 600;
    }

    /**
     * Check if error is due to rate limiting
     */
    public function isRateLimited(): bool
    {
        return $this->hasError('too many requests') || $this->errorCode === 429;
    }

    /**
     * Get retry after seconds from rate limit error
     */
    public function getRetryAfter(): ?int
    {
        if (!$this->isRateLimited()) {
            return null;
        }

        // Try to extract retry_after from error response
        $retryAfter = $this->get('parameters.retry_after');
        if (is_numeric($retryAfter)) {
            return (int) $retryAfter;
        }

        // Default retry after for rate limits
        return 60;
    }

    /**
     * Magic method for property access
     */
    public function __get(string $name): mixed
    {
        return match ($name) {
            'success' => $this->success,
            'data' => $this->data,
            'error' => $this->error,
            'errorCode' => $this->errorCode,
            'metadata' => $this->metadata,
            default => $this->get($name),
        };
    }

    /**
     * Check if property exists
     */
    public function __isset(string $name): bool
    {
        return match ($name) {
            'success', 'data', 'error', 'errorCode', 'metadata' => true,
            default => isset($this->data[$name]),
        };
    }

    /**
     * String representation for debugging
     */
    public function __toString(): string
    {
        if ($this->success) {
            return sprintf('TelegramResponse[SUCCESS]: %d data fields', count($this->data));
        } else {
            return sprintf('TelegramResponse[ERROR %d]: %s', $this->errorCode ?? 0, $this->error ?? 'Unknown error');
        }
    }
}