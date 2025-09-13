<?php

namespace App\Services\Telegram\Contracts;

use App\Services\Telegram\Core\UpdateContext;
use App\Services\Telegram\Core\ProcessingResult;

/**
 * Command Handler Contract
 * 
 * Defines the interface for all Telegram bot command handlers
 * enabling the command pattern for extensible functionality
 */
interface CommandHandlerInterface
{
    /**
     * Handle the command execution
     */
    public function handle(UpdateContext $context): ProcessingResult;
    
    /**
     * Get command names this handler can process
     */
    public function getCommandNames(): array;
    
    /**
     * Get command description for help system
     */
    public function getDescription(): string;
    
    /**
     * Check if command requires admin privileges
     */
    public function requiresAdmin(): bool;
    
    /**
     * Check if command is available in group chats
     */
    public function isAvailableInGroups(): bool;
    
    /**
     * Get command usage examples
     */
    public function getUsageExamples(): array;
}