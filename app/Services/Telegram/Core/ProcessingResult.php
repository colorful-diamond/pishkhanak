<?php

namespace App\Services\Telegram\Core;

/**
 * Processing Result Value Object
 * 
 * Represents the result of processing a Telegram update or command
 * with success/failure status and associated metadata.
 */
class ProcessingResult
{
    private function __construct(
        private bool $success,
        private ?string $error = null,
        private ?string $errorCode = null,
        private array $metadata = [],
        private ?array $data = null
    ) {}

    /**
     * Create a successful processing result
     */
    public static function success(array $data = [], array $metadata = []): self
    {
        return new self(
            success: true,
            data: $data,
            metadata: $metadata
        );
    }

    /**
     * Create a failed processing result
     */
    public static function failed(
        string $errorCode = 'processing_failed',
        string $error = 'Processing failed',
        array $metadata = []
    ): self {
        return new self(
            success: false,
            error: $error,
            errorCode: $errorCode,
            metadata: $metadata
        );
    }

    /**
     * Create an unauthorized result
     */
    public static function unauthorized(string $message = 'Unauthorized access'): self
    {
        return new self(
            success: false,
            error: $message,
            errorCode: 'unauthorized',
            metadata: ['requires_auth' => true]
        );
    }

    /**
     * Create a rate limited result
     */
    public static function rateLimited(int $retryAfter = 60): self
    {
        return new self(
            success: false,
            error: 'Rate limit exceeded',
            errorCode: 'rate_limited',
            metadata: ['retry_after' => $retryAfter]
        );
    }

    /**
     * Create a validation error result
     */
    public static function validationError(string $message, array $errors = []): self
    {
        return new self(
            success: false,
            error: $message,
            errorCode: 'validation_error',
            metadata: ['validation_errors' => $errors]
        );
    }

    /**
     * Create a not found result
     */
    public static function notFound(string $resource = 'Resource'): self
    {
        return new self(
            success: false,
            error: "{$resource} not found",
            errorCode: 'not_found'
        );
    }

    /**
     * Create a permission denied result
     */
    public static function permissionDenied(string $action = ''): self
    {
        $message = $action ? "Permission denied for action: {$action}" : 'Permission denied';
        
        return new self(
            success: false,
            error: $message,
            errorCode: 'permission_denied'
        );
    }

    /**
     * Check if processing was successful
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * Check if processing failed
     */
    public function isFailure(): bool
    {
        return !$this->success;
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
    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    /**
     * Get processing metadata
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * Get specific metadata value
     */
    public function getMetadataValue(string $key, $default = null)
    {
        return $this->metadata[$key] ?? $default;
    }

    /**
     * Get result data
     */
    public function getData(): ?array
    {
        return $this->data;
    }

    /**
     * Get specific data value
     */
    public function getDataValue(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * Add metadata to result
     */
    public function withMetadata(string $key, $value): self
    {
        $new = clone $this;
        $new->metadata[$key] = $value;
        return $new;
    }

    /**
     * Add data to result
     */
    public function withData(string $key, $value): self
    {
        $new = clone $this;
        if (!is_array($new->data)) {
            $new->data = [];
        }
        $new->data[$key] = $value;
        return $new;
    }

    /**
     * Check if result has specific error code
     */
    public function hasErrorCode(string $code): bool
    {
        return $this->errorCode === $code;
    }

    /**
     * Check if result is unauthorized
     */
    public function isUnauthorized(): bool
    {
        return $this->hasErrorCode('unauthorized');
    }

    /**
     * Check if result is rate limited
     */
    public function isRateLimited(): bool
    {
        return $this->hasErrorCode('rate_limited');
    }

    /**
     * Check if result is validation error
     */
    public function isValidationError(): bool
    {
        return $this->hasErrorCode('validation_error');
    }

    /**
     * Check if result is not found
     */
    public function isNotFound(): bool
    {
        return $this->hasErrorCode('not_found');
    }

    /**
     * Check if result is permission denied
     */
    public function isPermissionDenied(): bool
    {
        return $this->hasErrorCode('permission_denied');
    }

    /**
     * Get retry after seconds for rate limited results
     */
    public function getRetryAfter(): ?int
    {
        return $this->getMetadataValue('retry_after');
    }

    /**
     * Get validation errors for validation error results
     */
    public function getValidationErrors(): array
    {
        return $this->getMetadataValue('validation_errors', []);
    }

    /**
     * Convert to array representation
     */
    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'error' => $this->error,
            'error_code' => $this->errorCode,
            'metadata' => $this->metadata,
            'data' => $this->data,
        ];
    }

    /**
     * Convert to JSON string
     */
    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE);
    }

    /**
     * Create from array (for deserialization)
     */
    public static function fromArray(array $data): self
    {
        return new self(
            success: $data['success'] ?? false,
            error: $data['error'] ?? null,
            errorCode: $data['error_code'] ?? null,
            metadata: $data['metadata'] ?? [],
            data: $data['data'] ?? null
        );
    }

    /**
     * String representation for debugging
     */
    public function __toString(): string
    {
        if ($this->success) {
            return "ProcessingResult[SUCCESS]";
        }
        
        return sprintf(
            "ProcessingResult[FAILED:%s] %s",
            $this->errorCode ?: 'unknown',
            $this->error ?: 'No error message'
        );
    }
}