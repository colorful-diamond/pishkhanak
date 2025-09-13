<?php

namespace App\Services\Telegram\Handlers;

use App\Services\Telegram\Contracts\CommandHandlerInterface;
use App\Services\Telegram\Contracts\TelegramApiClientInterface;
use App\Services\Telegram\Core\UpdateContext;
use App\Services\Telegram\Core\ProcessingResult;
use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;

/**
 * Abstract Command Handler Base Class
 * 
 * Provides common functionality for all command handlers
 * including API client access, logging, and validation
 */
abstract class AbstractCommandHandler implements CommandHandlerInterface
{
    protected array $commandNames = [];
    protected string $description = '';
    protected bool $requiresAdmin = false;
    protected bool $availableInGroups = true;
    protected array $usageExamples = [];

    public function __construct(
        protected TelegramApiClientInterface $apiClient,
        protected ?LoggerInterface $logger = null
    ) {
        $this->logger = $logger ?? Log::getFacadeRoot();
    }

    /**
     * Handle the command with common validation
     */
    final public function handle(UpdateContext $context): ProcessingResult
    {
        try {
            // Pre-execution validation
            $validationResult = $this->validateExecution($context);
            if (!$validationResult->isSuccess()) {
                return $validationResult;
            }

            // Log command execution
            $this->logCommandExecution($context);

            // Execute the specific command logic
            $result = $this->execute($context);

            // Post-execution logging
            $this->logCommandResult($context, $result);

            return $result;

        } catch (\Exception $e) {
            $this->logger->error('Command execution error', [
                'command' => $context->getCommand(),
                'user_id' => $context->getUserId(),
                'chat_id' => $context->getChatId(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return ProcessingResult::error(
                'خطایی در اجرای دستور رخ داده است',
                ProcessingResult::ERROR_INTERNAL,
                ['command' => $context->getCommand()]
            );
        }
    }

    /**
     * Execute the specific command logic (implemented by subclasses)
     */
    abstract protected function execute(UpdateContext $context): ProcessingResult;

    /**
     * Validate command execution requirements
     */
    protected function validateExecution(UpdateContext $context): ProcessingResult
    {
        // Check admin requirement
        if ($this->requiresAdmin && !$context->isFromAdmin()) {
            return ProcessingResult::error(
                'این دستور فقط برای مدیران قابل استفاده است',
                ProcessingResult::ERROR_UNAUTHORIZED
            );
        }

        // Check group availability
        if (!$this->availableInGroups && $context->isGroupChat()) {
            return ProcessingResult::error(
                'این دستور در گروه‌ها قابل استفاده نیست',
                ProcessingResult::ERROR_INVALID_INPUT
            );
        }

        return ProcessingResult::success();
    }

    /**
     * Send response message to user
     */
    protected function sendResponse(UpdateContext $context, string $message, array $options = []): ProcessingResult
    {
        $response = $this->apiClient->sendMessage(
            $context->getChatId(),
            $message,
            $options
        );

        if ($response->isError()) {
            $this->logger->error('Failed to send command response', [
                'command' => $context->getCommand(),
                'user_id' => $context->getUserId(),
                'chat_id' => $context->getChatId(),
                'error' => $response->getError(),
            ]);

            return ProcessingResult::error(
                'خطا در ارسال پاسخ',
                ProcessingResult::ERROR_INTERNAL
            );
        }

        return ProcessingResult::success([
            'message_id' => $response->getMessageId(),
            'sent_message' => $message,
        ]);
    }

    /**
     * Send typing indicator
     */
    protected function sendTyping(UpdateContext $context): void
    {
        $this->apiClient->sendChatAction($context->getChatId(), 'typing');
    }

    /**
     * Parse command arguments with validation
     */
    protected function parseArguments(UpdateContext $context, int $requiredCount = 0): array
    {
        $args = $context->getCommandArgs();
        
        if (count($args) < $requiredCount) {
            throw new \InvalidArgumentException(
                "دستور حداقل {$requiredCount} آرگومان نیاز دارد"
            );
        }

        return $args;
    }

    /**
     * Get command names this handler can process
     */
    public function getCommandNames(): array
    {
        return $this->commandNames;
    }

    /**
     * Get command description for help system
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Check if command requires admin privileges
     */
    public function requiresAdmin(): bool
    {
        return $this->requiresAdmin;
    }

    /**
     * Check if command is available in group chats
     */
    public function isAvailableInGroups(): bool
    {
        return $this->availableInGroups;
    }

    /**
     * Get command usage examples
     */
    public function getUsageExamples(): array
    {
        return $this->usageExamples;
    }

    /**
     * Log command execution
     */
    private function logCommandExecution(UpdateContext $context): void
    {
        $this->logger->info('Command execution started', [
            'handler' => static::class,
            'command' => $context->getCommand(),
            'user_id' => $context->getUserId(),
            'chat_id' => $context->getChatId(),
            'args' => $context->getCommandArgs(),
            'is_admin' => $context->isFromAdmin(),
            'is_group' => $context->isGroupChat(),
        ]);
    }

    /**
     * Log command result
     */
    private function logCommandResult(UpdateContext $context, ProcessingResult $result): void
    {
        $logData = [
            'handler' => static::class,
            'command' => $context->getCommand(),
            'user_id' => $context->getUserId(),
            'success' => $result->isSuccess(),
        ];

        if ($result->isError()) {
            $logData['error'] = $result->getError();
            $logData['error_code'] = $result->getErrorCode();
        }

        if ($result->isSuccess()) {
            $this->logger->info('Command executed successfully', $logData);
        } else {
            $this->logger->error('Command execution failed', $logData);
        }
    }
}