<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Background job for logging Telegram chat IDs
 * 
 * Removes file I/O operations from the critical webhook response path
 * while maintaining chat ID logging for administrative purposes.
 */
class LogTelegramChatIdJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 30;
    public $tries = 3;
    public $backoff = [10, 30, 60];

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $chatId,
        public string $userName,
        public string $userId
    ) {
        $this->onQueue('telegram_logging');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Log to Laravel logs (structured logging)
            Log::info('TELEGRAM_CHAT_ID_CAPTURED', [
                'chat_id' => $this->chatId,
                'user_name' => $this->userName,
                'user_id' => $this->userId,
                'timestamp' => now()->toISOString()
            ]);

            // Also maintain file log for easy admin access
            $logEntry = sprintf(
                "Chat ID: %s | User: %s | User ID: %s | Time: %s\n",
                $this->chatId,
                $this->userName,
                $this->userId,
                now()->format('Y-m-d H:i:s')
            );

            file_put_contents(
                storage_path('app/telegram_chat_ids.txt'), 
                $logEntry,
                FILE_APPEND | LOCK_EX
            );

        } catch (\Exception $e) {
            Log::error('Failed to log Telegram chat ID', [
                'chat_id' => $this->chatId,
                'error' => $e->getMessage()
            ]);
            
            // Don't retry for this non-critical operation
            $this->fail($e);
        }
    }
}