<?php

namespace App\Services\Telegram\Handlers;

use App\Services\Telegram\Core\UpdateContext;
use App\Services\Telegram\Core\ProcessingResult;
use App\Services\Telegram\Contracts\TelegramApiClientInterface;
use App\Services\Telegram\Contracts\TicketRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Ticket Command Handler
 * 
 * Handles all ticket-related commands including creation,
 * viewing, updating, and closing support tickets
 */
class TicketCommandHandler extends AbstractCommandHandler
{
    protected array $commandNames = ['tickets', 'ticket', 'تیکت', 'تیکت‌ها'];
    protected string $description = 'مدیریت تیکت‌های پشتیبانی';
    protected bool $requiresAdmin = false;
    protected bool $availableInGroups = false; // Only in private chats
    protected array $usageExamples = [
        '/tickets - مشاهده تیکت‌های شما',
        '/ticket new [موضوع] - ایجاد تیکت جدید',
        '/ticket reply [ID] [پیام] - پاسخ به تیکت',
        '/ticket close [ID] - بستن تیکت',
    ];

    public function __construct(
        TelegramApiClientInterface $apiClient,
        private TicketRepositoryInterface $ticketRepository,
        ?LoggerInterface $logger = null
    ) {
        parent::__construct($apiClient, $logger);
    }

    protected function execute(UpdateContext $context): ProcessingResult
    {
        $args = $context->getCommandArgs();
        $command = strtolower($context->getCommand());

        // Handle different ticket commands
        if (empty($args)) {
            return $this->showUserTickets($context);
        }

        $action = strtolower($args[0]);

        return match ($action) {
            'new', 'جدید' => $this->createNewTicket($context, array_slice($args, 1)),
            'reply', 'پاسخ' => $this->replyToTicket($context, array_slice($args, 1)),
            'close', 'بستن' => $this->closeTicket($context, array_slice($args, 1)),
            'show', 'نمایش' => $this->showTicketDetails($context, array_slice($args, 1)),
            default => $this->showTicketHelp($context),
        };
    }

    /**
     * Show user's tickets
     */
    private function showUserTickets(UpdateContext $context): ProcessingResult
    {
        $userId = $context->getUserId();
        
        try {
            $tickets = $this->ticketRepository->getUserTickets($userId);
            
            if (empty($tickets)) {
                $message = "📋 شما هیچ تیکتی ندارید.\n\n";
                $message .= "برای ایجاد تیکت جدید از دستور زیر استفاده کنید:\n";
                $message .= "/ticket new [موضوع تیکت]";
                
                return $this->sendResponse($context, $message);
            }

            $message = "🎫 تیکت‌های شما:\n\n";
            
            foreach ($tickets as $ticket) {
                $status = $this->getStatusEmoji($ticket['status']);
                $createdAt = date('Y/m/d H:i', strtotime($ticket['created_at']));
                
                $message .= "{$status} تیکت #{$ticket['id']}\n";
                $message .= "📝 موضوع: {$ticket['subject']}\n";
                $message .= "🗓 تاریخ: {$createdAt}\n";
                $message .= "💬 پیام‌ها: {$ticket['messages_count']}\n\n";
            }
            
            $message .= "برای مشاهده جزئیات: /ticket show [ID]\n";
            $message .= "برای تیکت جدید: /ticket new [موضوع]";

            return $this->sendResponse($context, $message);
            
        } catch (\Exception $e) {
            $this->logger->error('Failed to retrieve user tickets', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);

            return ProcessingResult::error(
                'خطا در دریافت تیکت‌ها',
                ProcessingResult::ERROR_INTERNAL
            );
        }
    }

    /**
     * Create new ticket
     */
    private function createNewTicket(UpdateContext $context, array $args): ProcessingResult
    {
        if (empty($args)) {
            return ProcessingResult::error(
                'لطفاً موضوع تیکت را وارد کنید:\n/ticket new [موضوع تیکت]',
                ProcessingResult::ERROR_INVALID_INPUT
            );
        }

        $subject = implode(' ', $args);
        $userId = $context->getUserId();
        $userName = $context->getFrom()['first_name'] ?? 'کاربر';

        // Validate subject length
        if (mb_strlen($subject) < 5) {
            return ProcessingResult::error(
                'موضوع تیکت باید حداقل ۵ کاراکتر باشد',
                ProcessingResult::ERROR_INVALID_INPUT
            );
        }

        if (mb_strlen($subject) > 200) {
            return ProcessingResult::error(
                'موضوع تیکت نمی‌تواند بیش از ۲۰۰ کاراکتر باشد',
                ProcessingResult::ERROR_INVALID_INPUT
            );
        }

        try {
            $ticketId = $this->ticketRepository->createTicket([
                'user_id' => $userId,
                'user_name' => $userName,
                'subject' => $subject,
                'status' => 'open',
                'priority' => 'normal',
                'created_at' => now(),
            ]);

            $message = "✅ تیکت با موفقیت ایجاد شد!\n\n";
            $message .= "🎫 شماره تیکت: #{$ticketId}\n";
            $message .= "📝 موضوع: {$subject}\n\n";
            $message .= "برای ارسال پیام: /ticket reply {$ticketId} [پیام شما]\n";
            $message .= "برای مشاهده وضعیت: /ticket show {$ticketId}";

            // Notify admins
            $this->notifyAdminsNewTicket($ticketId, $subject, $userName);

            return $this->sendResponse($context, $message);
            
        } catch (\Exception $e) {
            $this->logger->error('Failed to create ticket', [
                'user_id' => $userId,
                'subject' => $subject,
                'error' => $e->getMessage(),
            ]);

            return ProcessingResult::error(
                'خطا در ایجاد تیکت. لطفاً دوباره تلاش کنید',
                ProcessingResult::ERROR_INTERNAL
            );
        }
    }

    /**
     * Reply to ticket
     */
    private function replyToTicket(UpdateContext $context, array $args): ProcessingResult
    {
        if (count($args) < 2) {
            return ProcessingResult::error(
                'لطفاً شماره تیکت و پیام را وارد کنید:\n/ticket reply [ID] [پیام]',
                ProcessingResult::ERROR_INVALID_INPUT
            );
        }

        $ticketId = (int) $args[0];
        $message = implode(' ', array_slice($args, 1));
        $userId = $context->getUserId();

        // Validate message length
        if (mb_strlen($message) < 1) {
            return ProcessingResult::error(
                'پیام نمی‌تواند خالی باشد',
                ProcessingResult::ERROR_INVALID_INPUT
            );
        }

        if (mb_strlen($message) > 4000) {
            return ProcessingResult::error(
                'پیام نمی‌تواند بیش از ۴۰۰۰ کاراکتر باشد',
                ProcessingResult::ERROR_INVALID_INPUT
            );
        }

        try {
            // Check if ticket exists and belongs to user
            $ticket = $this->ticketRepository->getUserTicket($ticketId, $userId);
            
            if (!$ticket) {
                return ProcessingResult::error(
                    'تیکت مورد نظر یافت نشد یا به شما تعلق ندارد',
                    ProcessingResult::ERROR_INVALID_INPUT
                );
            }

            if ($ticket['status'] === 'closed') {
                return ProcessingResult::error(
                    'این تیکت بسته شده است و قابل پاسخ نیست',
                    ProcessingResult::ERROR_INVALID_INPUT
                );
            }

            // Add reply to ticket
            $this->ticketRepository->addTicketMessage($ticketId, [
                'user_id' => $userId,
                'message' => $message,
                'is_admin' => false,
                'created_at' => now(),
            ]);

            // Update ticket status if it was waiting for user
            if ($ticket['status'] === 'waiting_user') {
                $this->ticketRepository->updateTicketStatus($ticketId, 'open');
            }

            $response = "✅ پیام شما ارسال شد!\n\n";
            $response .= "🎫 تیکت #{$ticketId}\n";
            $response .= "📝 موضوع: {$ticket['subject']}\n\n";
            $response .= "تیم پشتیبانی در اسرع وقت پاسخ خواهد داد.";

            // Notify admins
            $this->notifyAdminsTicketReply($ticketId, $message, $context->getFrom()['first_name'] ?? 'کاربر');

            return $this->sendResponse($context, $response);
            
        } catch (\Exception $e) {
            $this->logger->error('Failed to reply to ticket', [
                'ticket_id' => $ticketId,
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);

            return ProcessingResult::error(
                'خطا در ارسال پاسخ. لطفاً دوباره تلاش کنید',
                ProcessingResult::ERROR_INTERNAL
            );
        }
    }

    /**
     * Close ticket
     */
    private function closeTicket(UpdateContext $context, array $args): ProcessingResult
    {
        if (empty($args)) {
            return ProcessingResult::error(
                'لطفاً شماره تیکت را وارد کنید:\n/ticket close [ID]',
                ProcessingResult::ERROR_INVALID_INPUT
            );
        }

        $ticketId = (int) $args[0];
        $userId = $context->getUserId();

        try {
            $ticket = $this->ticketRepository->getUserTicket($ticketId, $userId);
            
            if (!$ticket) {
                return ProcessingResult::error(
                    'تیکت مورد نظر یافت نشد یا به شما تعلق ندارد',
                    ProcessingResult::ERROR_INVALID_INPUT
                );
            }

            if ($ticket['status'] === 'closed') {
                return ProcessingResult::error(
                    'این تیکت قبلاً بسته شده است',
                    ProcessingResult::ERROR_INVALID_INPUT
                );
            }

            $this->ticketRepository->updateTicketStatus($ticketId, 'closed');

            $message = "✅ تیکت با موفقیت بسته شد\n\n";
            $message .= "🎫 تیکت #{$ticketId}\n";
            $message .= "📝 موضوع: {$ticket['subject']}\n\n";
            $message .= "از استفاده از خدمات پیشخوانک متشکریم!";

            return $this->sendResponse($context, $message);
            
        } catch (\Exception $e) {
            $this->logger->error('Failed to close ticket', [
                'ticket_id' => $ticketId,
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);

            return ProcessingResult::error(
                'خطا در بستن تیکت. لطفاً دوباره تلاش کنید',
                ProcessingResult::ERROR_INTERNAL
            );
        }
    }

    /**
     * Show ticket details
     */
    private function showTicketDetails(UpdateContext $context, array $args): ProcessingResult
    {
        if (empty($args)) {
            return ProcessingResult::error(
                'لطفاً شماره تیکت را وارد کنید:\n/ticket show [ID]',
                ProcessingResult::ERROR_INVALID_INPUT
            );
        }

        $ticketId = (int) $args[0];
        $userId = $context->getUserId();

        try {
            $ticket = $this->ticketRepository->getUserTicketWithMessages($ticketId, $userId);
            
            if (!$ticket) {
                return ProcessingResult::error(
                    'تیکت مورد نظر یافت نشد یا به شما تعلق ندارد',
                    ProcessingResult::ERROR_INVALID_INPUT
                );
            }

            $status = $this->getStatusEmoji($ticket['status']);
            $createdAt = date('Y/m/d H:i', strtotime($ticket['created_at']));

            $message = "🎫 جزئیات تیکت #{$ticket['id']}\n\n";
            $message .= "📝 موضوع: {$ticket['subject']}\n";
            $message .= "🗓 تاریخ ایجاد: {$createdAt}\n";
            $message .= "{$status} وضعیت: " . $this->getStatusText($ticket['status']) . "\n\n";

            // Show last 5 messages
            $messages = array_slice($ticket['messages'], -5);
            $message .= "💬 آخرین پیام‌ها:\n";
            $message .= "━━━━━━━━━━━━━━━━━━━\n";

            foreach ($messages as $msg) {
                $sender = $msg['is_admin'] ? '👤 پشتیبانی' : '👤 شما';
                $date = date('m/d H:i', strtotime($msg['created_at']));
                $text = mb_strlen($msg['message']) > 100 
                    ? mb_substr($msg['message'], 0, 100) . '...' 
                    : $msg['message'];
                
                $message .= "{$sender} ({$date}):\n{$text}\n\n";
            }

            if ($ticket['status'] !== 'closed') {
                $message .= "برای پاسخ: /ticket reply {$ticketId} [پیام]\n";
                $message .= "برای بستن: /ticket close {$ticketId}";
            }

            return $this->sendResponse($context, $message);
            
        } catch (\Exception $e) {
            $this->logger->error('Failed to show ticket details', [
                'ticket_id' => $ticketId,
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);

            return ProcessingResult::error(
                'خطا در نمایش جزئیات تیکت',
                ProcessingResult::ERROR_INTERNAL
            );
        }
    }

    /**
     * Show ticket help
     */
    private function showTicketHelp(UpdateContext $context): ProcessingResult
    {
        $message = "🎫 راهنمای سیستم تیکت\n\n";
        $message .= "📝 دستورات موجود:\n";
        $message .= "/tickets - مشاهده تمام تیکت‌های شما\n";
        $message .= "/ticket new [موضوع] - ایجاد تیکت جدید\n";
        $message .= "/ticket show [ID] - نمایش جزئیات تیکت\n";
        $message .= "/ticket reply [ID] [پیام] - ارسال پاسخ\n";
        $message .= "/ticket close [ID] - بستن تیکت\n\n";
        $message .= "💡 مثال:\n";
        $message .= "/ticket new مشکل در پرداخت\n";
        $message .= "/ticket reply 123 سلام، مشکل حل شد";

        return $this->sendResponse($context, $message);
    }

    /**
     * Get status emoji
     */
    private function getStatusEmoji(string $status): string
    {
        return match ($status) {
            'open' => '🟢',
            'waiting_admin' => '🟡',
            'waiting_user' => '🔵',
            'closed' => '🔴',
            default => '⚪',
        };
    }

    /**
     * Get status text in Persian
     */
    private function getStatusText(string $status): string
    {
        return match ($status) {
            'open' => 'باز',
            'waiting_admin' => 'در انتظار پشتیبانی',
            'waiting_user' => 'در انتظار پاسخ شما',
            'closed' => 'بسته شده',
            default => 'نامشخص',
        };
    }

    /**
     * Notify admins about new ticket
     */
    private function notifyAdminsNewTicket(int $ticketId, string $subject, string $userName): void
    {
        // This would typically send notifications to admin chat
        $this->logger->info('New ticket created', [
            'ticket_id' => $ticketId,
            'subject' => $subject,
            'user_name' => $userName,
        ]);
    }

    /**
     * Notify admins about ticket reply
     */
    private function notifyAdminsTicketReply(int $ticketId, string $message, string $userName): void
    {
        // This would typically send notifications to admin chat
        $this->logger->info('Ticket reply received', [
            'ticket_id' => $ticketId,
            'user_name' => $userName,
            'message_length' => mb_strlen($message),
        ]);
    }
}