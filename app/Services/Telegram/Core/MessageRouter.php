<?php

namespace App\Services\Telegram\Core;

use App\Services\Telegram\Contracts\CommandHandlerInterface;
use App\Services\Telegram\Handlers\CallbackQueryHandler;
use App\Services\Telegram\Handlers\InlineQueryHandler;
use App\Services\Telegram\Handlers\MessageHandler;
use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;

/**
 * Message Router
 * 
 * Routes incoming Telegram updates to appropriate handlers
 * based on update type and content analysis
 */
class MessageRouter
{
    private array $commandHandlers = [];
    private array $defaultHandlers = [];

    public function __construct(
        private MessageHandler $messageHandler,
        private CallbackQueryHandler $callbackQueryHandler,
        private InlineQueryHandler $inlineQueryHandler,
        private ?LoggerInterface $logger = null
    ) {
        $this->logger = $logger ?? Log::getFacadeRoot();
    }

    /**
     * Register a command handler
     */
    public function registerCommand(string $command, CommandHandlerInterface $handler): void
    {
        $this->commandHandlers[strtolower($command)] = $handler;
        
        $this->logger->debug('Command handler registered', [
            'command' => $command,
            'handler' => get_class($handler),
        ]);
    }

    /**
     * Register multiple command handlers
     */
    public function registerCommands(array $handlers): void
    {
        foreach ($handlers as $command => $handler) {
            $this->registerCommand($command, $handler);
        }
    }

    /**
     * Register a default handler for specific update types
     */
    public function registerDefaultHandler(string $updateType, callable $handler): void
    {
        $this->defaultHandlers[$updateType] = $handler;
    }

    /**
     * Route update to appropriate handler
     */
    public function route(UpdateContext $context): ProcessingResult
    {
        try {
            $this->logger->info('Routing update', [
                'update_id' => $context->getUpdateId(),
                'type' => $context->getType(),
                'user_id' => $context->getUserId(),
                'chat_id' => $context->getChatId(),
                'is_command' => $context->isCommand(),
                'command' => $context->getCommand(),
            ]);

            $result = match ($context->getType()) {
                'message', 'edited_message', 'channel_post', 'edited_channel_post' => $this->handleMessage($context),
                'callback_query' => $this->handleCallbackQuery($context),
                'inline_query' => $this->handleInlineQuery($context),
                default => $this->handleUnknownType($context),
            };

            $this->logRoutingResult($context, $result);
            return $result;

        } catch (\Exception $e) {
            $this->logger->error('Routing error', [
                'update_id' => $context->getUpdateId(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return ProcessingResult::error(
                'Routing failed: ' . $e->getMessage(),
                ProcessingResult::ERROR_INTERNAL,
                ['update_id' => $context->getUpdateId()]
            );
        }
    }

    /**
     * Handle message-type updates
     */
    private function handleMessage(UpdateContext $context): ProcessingResult
    {
        // Route commands to specific handlers
        if ($context->isCommand()) {
            return $this->handleCommand($context);
        }

        // Route regular messages to message handler
        return $this->messageHandler->handle($context);
    }

    /**
     * Handle command routing
     */
    private function handleCommand(UpdateContext $context): ProcessingResult
    {
        $command = strtolower($context->getCommand());
        
        // Check for exact command match
        if (isset($this->commandHandlers[$command])) {
            $handler = $this->commandHandlers[$command];
            
            $this->logger->info('Command routed to handler', [
                'command' => $command,
                'handler' => get_class($handler),
                'user_id' => $context->getUserId(),
            ]);

            return $handler->handle($context);
        }

        // Check for partial matches or aliases
        $partialMatch = $this->findPartialCommandMatch($command);
        if ($partialMatch) {
            $handler = $this->commandHandlers[$partialMatch];
            
            $this->logger->info('Command routed to handler via partial match', [
                'command' => $command,
                'matched_command' => $partialMatch,
                'handler' => get_class($handler),
                'user_id' => $context->getUserId(),
            ]);

            return $handler->handle($context);
        }

        // Handle unknown command
        return $this->handleUnknownCommand($context);
    }

    /**
     * Find partial command match for flexibility
     */
    private function findPartialCommandMatch(string $command): ?string
    {
        // Check for command aliases or shortened versions
        $aliases = [
            't' => 'tickets',
            'ticket' => 'tickets',
            'h' => 'help',
            's' => 'start',
            'st' => 'stats',
            'u' => 'users',
        ];

        if (isset($aliases[$command]) && isset($this->commandHandlers[$aliases[$command]])) {
            return $aliases[$command];
        }

        // Check for starts-with matches for compound commands
        foreach (array_keys($this->commandHandlers) as $registeredCommand) {
            if (str_starts_with($registeredCommand, $command) && strlen($command) >= 2) {
                return $registeredCommand;
            }
        }

        return null;
    }

    /**
     * Handle unknown command
     */
    private function handleUnknownCommand(UpdateContext $context): ProcessingResult
    {
        $this->logger->info('Unknown command received', [
            'command' => $context->getCommand(),
            'user_id' => $context->getUserId(),
            'chat_id' => $context->getChatId(),
        ]);

        // Use default message handler for unknown commands
        return $this->messageHandler->handleUnknownCommand($context);
    }

    /**
     * Handle callback query
     */
    private function handleCallbackQuery(UpdateContext $context): ProcessingResult
    {
        return $this->callbackQueryHandler->handle($context);
    }

    /**
     * Handle inline query
     */
    private function handleInlineQuery(UpdateContext $context): ProcessingResult
    {
        return $this->inlineQueryHandler->handle($context);
    }

    /**
     * Handle unknown update type
     */
    private function handleUnknownType(UpdateContext $context): ProcessingResult
    {
        $this->logger->warning('Unknown update type received', [
            'update_type' => $context->getType(),
            'update_id' => $context->getUpdateId(),
        ]);

        // Check for default handler
        if (isset($this->defaultHandlers[$context->getType()])) {
            $handler = $this->defaultHandlers[$context->getType()];
            return $handler($context);
        }

        return ProcessingResult::error(
            'Unsupported update type: ' . $context->getType(),
            ProcessingResult::ERROR_INTERNAL,
            ['update_type' => $context->getType()]
        );
    }

    /**
     * Log routing result for monitoring
     */
    private function logRoutingResult(UpdateContext $context, ProcessingResult $result): void
    {
        $logData = [
            'update_id' => $context->getUpdateId(),
            'type' => $context->getType(),
            'user_id' => $context->getUserId(),
            'success' => $result->isSuccess(),
        ];

        if ($context->isCommand()) {
            $logData['command'] = $context->getCommand();
        }

        if ($result->isError()) {
            $logData['error'] = $result->getError();
            $logData['error_code'] = $result->getErrorCode();
        }

        if ($result->isSuccess()) {
            $this->logger->info('Update routed successfully', $logData);
        } else {
            $this->logger->error('Update routing failed', $logData);
        }
    }

    /**
     * Get all registered commands
     */
    public function getRegisteredCommands(): array
    {
        return array_keys($this->commandHandlers);
    }

    /**
     * Check if command is registered
     */
    public function hasCommand(string $command): bool
    {
        return isset($this->commandHandlers[strtolower($command)]);
    }

    /**
     * Get command handler
     */
    public function getCommandHandler(string $command): ?CommandHandlerInterface
    {
        return $this->commandHandlers[strtolower($command)] ?? null;
    }

    /**
     * Unregister command handler
     */
    public function unregisterCommand(string $command): void
    {
        unset($this->commandHandlers[strtolower($command)]);
        
        $this->logger->debug('Command handler unregistered', [
            'command' => $command,
        ]);
    }

    /**
     * Get routing statistics
     */
    public function getStats(): array
    {
        return [
            'registered_commands' => count($this->commandHandlers),
            'commands' => array_keys($this->commandHandlers),
            'default_handlers' => array_keys($this->defaultHandlers),
        ];
    }
}