<?php

namespace App\Services\Telegram\Core;

use App\Services\PersianTextValidator;
use App\Services\TelegramAdminAuth;
use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;

/**
 * Telegram Webhook Processing Pipeline
 * 
 * Processes incoming Telegram updates with security validation,
 * Persian text processing, and intelligent routing to handlers.
 */
class WebhookProcessor
{
    public function __construct(
        private MessageRouter $router,
        private PersianTextValidator $textValidator,
        private TelegramAdminAuth $adminAuth,
        private ?LoggerInterface $logger = null
    ) {
        $this->logger = $logger ?? Log::getFacadeRoot();
    }

    /**
     * Process incoming webhook update
     */
    public function process(array $update): ProcessingResult
    {
        $startTime = microtime(true);
        $updateId = $update['update_id'] ?? null;

        try {
            // Step 1: Basic structure validation (already done by middleware)
            $validationResult = $this->validateUpdateStructure($update);
            if (!$validationResult->isSuccess()) {
                return $validationResult;
            }

            // Step 2: Extract update context
            $context = $this->extractUpdateContext($update);

            // Step 3: Security validation and rate limiting
            $securityResult = $this->performSecurityValidation($context);
            if (!$securityResult->isSuccess()) {
                return $securityResult;
            }

            // Step 4: Persian text validation (if applicable)
            if ($context->hasText()) {
                $textValidation = $this->validatePersianText($context);
                if (!$textValidation->isSuccess()) {
                    return $textValidation;
                }
            }

            // Step 5: Route to appropriate handler
            $routingResult = $this->router->route($context);

            // Step 6: Log successful processing
            $this->logProcessingMetrics($updateId, $context, $startTime, true);

            return $routingResult;

        } catch (\Exception $e) {
            $this->logger->error('Webhook processing error', [
                'update_id' => $updateId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'processing_time_ms' => round((microtime(true) - $startTime) * 1000, 2),
            ]);

            $this->logProcessingMetrics($updateId, null, $startTime, false);

            return ProcessingResult::error(
                'Internal processing error',
                ProcessingResult::ERROR_INTERNAL,
                ['update_id' => $updateId]
            );
        }
    }

    /**
     * Validate update structure
     */
    private function validateUpdateStructure(array $update): ProcessingResult
    {
        if (!isset($update['update_id'])) {
            return ProcessingResult::error('Missing update_id', ProcessingResult::ERROR_INVALID_STRUCTURE);
        }

        $expectedFields = ['message', 'callback_query', 'inline_query', 'edited_message', 'channel_post'];
        $hasValidContent = false;

        foreach ($expectedFields as $field) {
            if (isset($update[$field])) {
                $hasValidContent = true;
                break;
            }
        }

        if (!$hasValidContent) {
            return ProcessingResult::error('No valid update content', ProcessingResult::ERROR_INVALID_STRUCTURE);
        }

        return ProcessingResult::success();
    }

    /**
     * Extract update context for processing
     */
    private function extractUpdateContext(array $update): UpdateContext
    {
        return UpdateContext::fromArray($update);
    }

    /**
     * Perform security validation
     */
    private function performSecurityValidation(UpdateContext $context): ProcessingResult
    {
        // Check if this is an admin command
        if ($context->isCommand() && $this->isAdminCommand($context->getText())) {
            $isAuthorized = $this->adminAuth->verifyAdmin(
                $context->getUserId(),
                $context->getText()
            );

            if (!$isAuthorized) {
                $this->logger->warning('Unauthorized admin command attempt', [
                    'user_id' => $context->getUserId(),
                    'command' => $context->getText(),
                    'chat_id' => $context->getChatId(),
                ]);

                return ProcessingResult::error(
                    'Unauthorized access to admin command',
                    ProcessingResult::ERROR_UNAUTHORIZED
                );
            }
        }

        // Additional security checks can be added here
        // - User blacklist validation
        // - Content filtering
        // - Spam detection

        return ProcessingResult::success();
    }

    /**
     * Validate Persian text content
     */
    private function validatePersianText(UpdateContext $context): ProcessingResult
    {
        try {
            $text = $context->getText();
            if (empty($text)) {
                return ProcessingResult::success();
            }

            // Determine validation context based on message type
            $validationContext = $this->determineValidationContext($context);

            $sanitizedText = $this->textValidator->sanitizePersianInput($text, $validationContext);
            
            // Update context with sanitized text
            $context->setSanitizedText($sanitizedText);

            return ProcessingResult::success();

        } catch (\InvalidArgumentException $e) {
            $this->logger->warning('Persian text validation failed', [
                'user_id' => $context->getUserId(),
                'chat_id' => $context->getChatId(),
                'error' => $e->getMessage(),
                'original_text_length' => mb_strlen($context->getText()),
            ]);

            return ProcessingResult::error(
                'متن ارسالی شامل کاراکترهای غیرمجاز است',
                ProcessingResult::ERROR_INVALID_INPUT,
                ['validation_error' => $e->getMessage()]
            );
        }
    }

    /**
     * Determine validation context based on update type
     */
    private function determineValidationContext(UpdateContext $context): string
    {
        if ($context->isCommand()) {
            return 'command';
        }

        // Check if user is in a specific state (ticket creation, etc.)
        $userState = $this->getUserState($context->getUserId());
        
        return match ($userState) {
            'waiting_ticket_subject' => 'ticket_subject',
            'waiting_ticket_description' => 'ticket_description',
            'waiting_reply' => 'message',
            default => 'message',
        };
    }

    /**
     * Get user conversation state
     */
    private function getUserState(string $userId): string
    {
        // This would typically come from cache/database
        // For now, return default state
        return 'idle';
    }

    /**
     * Check if command is admin-only
     */
    private function isAdminCommand(string $text): bool
    {
        if (!str_starts_with($text, '/')) {
            return false;
        }

        $adminCommands = [
            '/admin', '/config', '/stats', '/ban', '/unban', '/reset',
            '/audit', '/logs', '/users', '/tickets_admin', '/system',
            '/security', '/backup', '/maintenance', '/broadcast',
        ];

        $command = explode(' ', $text)[0];
        return in_array(strtolower($command), $adminCommands);
    }

    /**
     * Log processing metrics for monitoring
     */
    private function logProcessingMetrics(?int $updateId, ?UpdateContext $context, float $startTime, bool $success): void
    {
        $processingTime = round((microtime(true) - $startTime) * 1000, 2);
        $memoryUsage = round(memory_get_usage(true) / 1024 / 1024, 2);

        $logData = [
            'update_id' => $updateId,
            'success' => $success,
            'processing_time_ms' => $processingTime,
            'memory_usage_mb' => $memoryUsage,
        ];

        if ($context) {
            $logData = array_merge($logData, [
                'update_type' => $context->getType(),
                'user_id' => $context->getUserId(),
                'chat_id' => $context->getChatId(),
                'is_command' => $context->isCommand(),
                'has_persian_text' => $context->hasPersianText(),
                'is_admin' => $context->isFromAdmin(),
            ]);
        }

        if ($success) {
            $this->logger->info('Telegram webhook processed successfully', $logData);
        } else {
            $this->logger->error('Telegram webhook processing failed', $logData);
        }

        // Performance alerting (if processing takes too long)
        if ($processingTime > 150) { // 150ms threshold
            $this->logger->warning('Slow webhook processing detected', [
                'processing_time_ms' => $processingTime,
                'update_id' => $updateId,
                'update_type' => $context?->getType(),
            ]);
        }
    }
}

/**
 * Processing Result Value Object
 */
class ProcessingResult
{
    public const ERROR_INVALID_STRUCTURE = 'invalid_structure';
    public const ERROR_UNAUTHORIZED = 'unauthorized';
    public const ERROR_INVALID_INPUT = 'invalid_input';
    public const ERROR_RATE_LIMITED = 'rate_limited';
    public const ERROR_INTERNAL = 'internal_error';

    public function __construct(
        private bool $success,
        private ?string $error = null,
        private ?string $errorCode = null,
        private array $data = [],
        private array $metadata = []
    ) {}

    public static function success(array $data = [], array $metadata = []): self
    {
        return new self(true, null, null, $data, $metadata);
    }

    public static function error(string $error, string $errorCode = self::ERROR_INTERNAL, array $metadata = []): self
    {
        return new self(false, $error, $errorCode, [], $metadata);
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function isError(): bool
    {
        return !$this->success;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }
}