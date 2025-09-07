<?php

namespace App\Observers;

use App\Models\Ticket;
use App\Services\TelegramTicketBot;
use Illuminate\Support\Facades\Log;

class TicketObserver
{
    protected $telegramBot;
    
    public function __construct()
    {
        try {
            // Use the fixed bot with all features
            $this->telegramBot = new \App\Services\TelegramBotFixed();
        } catch (\Exception $e) {
            Log::error('Failed to initialize Telegram bot in observer', ['error' => $e->getMessage()]);
            $this->telegramBot = null;
        }
    }
    
    /**
     * Handle the Ticket "created" event.
     */
    public function created(Ticket $ticket): void
    {
        try {
            // Send notification to admins via Telegram
            if ($this->telegramBot) {
                $this->telegramBot->notifyNewTicket($ticket);
            }
            
            Log::info('New ticket created and notification sent', [
                'ticket_id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to send new ticket notification', [
                'ticket_id' => $ticket->id,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Handle the Ticket "updated" event.
     */
    public function updated(Ticket $ticket): void
    {
        try {
            // Check if status changed to closed
            if ($ticket->isDirty('status') && $ticket->status === 'closed') {
                $this->notifyTicketClosed($ticket);
            }
            
            // Check if priority changed to high/urgent
            if ($ticket->isDirty('priority') && in_array($ticket->priority, ['high', 'urgent'])) {
                $this->notifyPriorityChange($ticket);
            }
            
        } catch (\Exception $e) {
            Log::error('Failed to handle ticket update', [
                'ticket_id' => $ticket->id,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Notify when ticket is closed
     */
    protected function notifyTicketClosed(Ticket $ticket): void
    {
        if (!$this->telegramBot) return;
        
        try {
            // Notify the ticket creator if they have Telegram
            if ($ticket->user && $ticket->user->telegram_chat_id) {
                $message = "aacdf5a1";
                $message .= "PERSIAN_TEXT_8159c1b0";
                $message .= "3099860c";
                $message .= "PERSIAN_TEXT_1a01646f" . \Verta::now()->format('Y/m/d H:i:s') . "\n\n";
                $message .= "d298e2da";
                
                $this->telegramBot->sendMessage(
                    $ticket->user->telegram_chat_id,
                    $message,
                    null,
                    'Markdown'
                );
            }
        } catch (\Exception $e) {
            Log::error('Failed to notify ticket closed', ['error' => $e->getMessage()]);
        }
    }
    
    /**
     * Notify when priority changes to high/urgent
     */
    protected function notifyPriorityChange(Ticket $ticket): void
    {
        if (!$this->telegramBot) return;
        
        try {
            // Get all admin chat IDs
            $adminChatIds = explode(',', config('services.telegram.admin_chat_ids', ''));
            
            foreach ($adminChatIds as $chatId) {
                if (empty($chatId)) continue;
                
                $priorityEmoji = $ticket->priority === 'urgent' ? '🔴' : '🟠';
                $priorityText = $ticket->priority === 'urgent' ? 'PERSIAN_TEXT_b21bd6a1' : 'PERSIAN_TEXT_6986ecfe';
                
                $message = "PERSIAN_TEXT_7ab71700";
                $message .= "6d3b57cb";
                $message .= "PERSIAN_TEXT_3099860c";
                $message .= "2b1d2349";
                $message .= "PERSIAN_TEXT_1c5d3dd0" . ($ticket->user->name ?? 'PERSIAN_TEXT_1789f5ad') . "\n\n";
                $message .= "deb449c1";
                
                $keyboard = [
                    'inline_keyboard' => [
                        [
                            ['text' => 'PERSIAN_TEXT_5aaba3de', 'callback_data' => "view_{$ticket->id}"],
                            ['text' => 'PERSIAN_TEXT_68fd0751', 'callback_data' => "reply_{$ticket->id}"]
                        ]
                    ]
                ];
                
                $this->telegramBot->sendMessage($chatId, $message, $keyboard, 'Markdown');
            }
        } catch (\Exception $e) {
            Log::error('Failed to notify priority change', ['error' => $e->getMessage()]);
        }
    }
}