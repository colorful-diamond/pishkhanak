<?php

namespace App\Services\Telegram\Handlers;

use App\Services\Telegram\Contracts\TelegramApiClientInterface;
use App\Services\Telegram\Core\UpdateContext;
use App\Services\Telegram\Core\ProcessingResult;
use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;

/**
 * Message Handler
 * 
 * Handles non-command text messages and provides
 * intelligent responses using AI integration
 */
class MessageHandler
{
    public function __construct(
        private TelegramApiClientInterface $apiClient,
        private ?LoggerInterface $logger = null
    ) {
        $this->logger = $logger ?? Log::getFacadeRoot();
    }

    /**
     * Handle regular text messages
     */
    public function handle(UpdateContext $context): ProcessingResult
    {
        try {
            $this->logger->info('Processing text message', [
                'user_id' => $context->getUserId(),
                'chat_id' => $context->getChatId(),
                'text_length' => mb_strlen($context->getText() ?? ''),
                'has_persian' => $context->hasPersianText(),
            ]);

            // Check if user is in a conversation state
            $userState = $this->getUserConversationState($context->getUserId());
            
            if ($userState !== 'idle') {
                return $this->handleConversationResponse($context, $userState);
            }

            // Handle regular messages with AI response
            return $this->generateIntelligentResponse($context);

        } catch (\Exception $e) {
            $this->logger->error('Message handling error', [
                'user_id' => $context->getUserId(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return ProcessingResult::error(
                'خطایی در پردازش پیام رخ داد',
                ProcessingResult::ERROR_INTERNAL
            );
        }
    }

    /**
     * Handle unknown commands
     */
    public function handleUnknownCommand(UpdateContext $context): ProcessingResult
    {
        $command = $context->getCommand();
        
        $message = "❓ دستور '/{$command}' شناخته نشده است.\n\n";
        $message .= "📝 دستورات موجود:\n";
        $message .= "/start - شروع استفاده از ربات\n";
        $message .= "/help - راهنمای کامل\n";
        $message .= "/tickets - مدیریت تیکت‌ها\n\n";
        $message .= "یا می‌توانید سوال خود را مستقیماً بپرسید.";

        return $this->sendResponse($context, $message);
    }

    /**
     * Get user conversation state
     */
    private function getUserConversationState(string $userId): string
    {
        // This would typically check cache/database for user state
        // For now, return idle (no active conversation)
        return 'idle';
    }

    /**
     * Handle conversation responses (ticket creation, etc.)
     */
    private function handleConversationResponse(UpdateContext $context, string $state): ProcessingResult
    {
        return match ($state) {
            'waiting_ticket_subject' => $this->handleTicketSubjectInput($context),
            'waiting_ticket_description' => $this->handleTicketDescriptionInput($context),
            'waiting_question' => $this->handleQuestionInput($context),
            default => $this->generateIntelligentResponse($context),
        };
    }

    /**
     * Handle ticket subject input
     */
    private function handleTicketSubjectInput(UpdateContext $context): ProcessingResult
    {
        // This would integrate with ticket creation flow
        $message = "موضوع تیکت دریافت شد. لطفاً توضیحات تکمیلی ارائه دهید:";
        return $this->sendResponse($context, $message);
    }

    /**
     * Handle ticket description input
     */
    private function handleTicketDescriptionInput(UpdateContext $context): ProcessingResult
    {
        // This would complete ticket creation
        $message = "✅ تیکت شما با موفقیت ایجاد شد!\nتیم پشتیبانی در اسرع وقت پاسخ خواهد داد.";
        return $this->sendResponse($context, $message);
    }

    /**
     * Handle question input
     */
    private function handleQuestionInput(UpdateContext $context): ProcessingResult
    {
        // This would integrate with AI question answering
        return $this->generateIntelligentResponse($context);
    }

    /**
     * Generate intelligent AI response
     */
    private function generateIntelligentResponse(UpdateContext $context): ProcessingResult
    {
        $text = $context->getSanitizedText();
        
        if (empty($text)) {
            $message = "سلام! چطور می‌تونم کمکتان کنم؟\n\n";
            $message .= "می‌تونید:\n";
            $message .= "• سوال بپرسید\n";
            $message .= "• تیکت پشتیبانی ایجاد کنید (/tickets)\n";
            $message .= "• راهنما رو مطالعه کنید (/help)";
            
            return $this->sendResponse($context, $message);
        }

        // Check for common patterns and provide appropriate responses
        if ($this->containsGreeting($text)) {
            return $this->handleGreeting($context);
        }

        if ($this->containsQuestion($text)) {
            return $this->handleQuestion($context);
        }

        if ($this->containsProblem($text)) {
            return $this->suggestTicketCreation($context);
        }

        // Default response for unhandled messages
        $message = "پیام شما دریافت شد. 📝\n\n";
        $message .= "برای پاسخ دقیق‌تر می‌تونید:\n";
        $message .= "• از /help برای راهنما استفاده کنید\n";
        $message .= "• تیکت پشتیبانی ایجاد کنید: /tickets\n";
        $message .= "• سوال خود را واضح‌تر بپرسید";

        return $this->sendResponse($context, $message);
    }

    /**
     * Check if text contains greeting
     */
    private function containsGreeting(string $text): bool
    {
        $greetings = ['سلام', 'درود', 'صبح بخیر', 'عصر بخیر', 'شب بخیر', 'hello', 'hi'];
        $lowerText = mb_strtolower($text);
        
        foreach ($greetings as $greeting) {
            if (str_contains($lowerText, $greeting)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Check if text contains question markers
     */
    private function containsQuestion(string $text): bool
    {
        return str_contains($text, '؟') || 
               str_contains($text, '?') ||
               str_starts_with(mb_strtolower($text), 'چطور') ||
               str_starts_with(mb_strtolower($text), 'چگونه') ||
               str_starts_with(mb_strtolower($text), 'آیا');
    }

    /**
     * Check if text contains problem indicators
     */
    private function containsProblem(string $text): bool
    {
        $problemKeywords = ['مشکل', 'خرابی', 'کار نمی‌کند', 'ارور', 'خطا', 'problem', 'issue', 'error'];
        $lowerText = mb_strtolower($text);
        
        foreach ($problemKeywords as $keyword) {
            if (str_contains($lowerText, $keyword)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Handle greeting messages
     */
    private function handleGreeting(UpdateContext $context): ProcessingResult
    {
        $firstName = $context->getFrom()['first_name'] ?? 'کاربر';
        
        $message = "سلام {$firstName}! 👋\n\n";
        $message .= "خوش اومدید به ربات پیشخوانک!\n";
        $message .= "چطور می‌تونم کمکتون کنم؟\n\n";
        $message .= "💡 راهنما: /help";

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '🎫 تیکت جدید', 'callback_data' => 'new_ticket'],
                    ['text' => '❓ پرسش', 'callback_data' => 'ask_question'],
                ],
                [
                    ['text' => '📚 راهنما', 'callback_data' => 'help'],
                ]
            ]
        ];

        return $this->sendResponse($context, $message, [
            'reply_markup' => json_encode($keyboard)
        ]);
    }

    /**
     * Handle question messages
     */
    private function handleQuestion(UpdateContext $context): ProcessingResult
    {
        // This would integrate with AI service for intelligent responses
        $message = "سوال جالبی پرسیدید! 🤔\n\n";
        $message .= "در حال حاضر، برای پاسخ دقیق‌تر می‌تونید:\n";
        $message .= "• تیکت پشتیبانی ایجاد کنید: /ticket new [سوال شما]\n";
        $message .= "• با تیم پشتیبانی در ارتباط باشید\n\n";
        $message .= "به زودی قابلیت پاسخگویی هوشمند اضافه می‌شه! 🚀";

        return $this->sendResponse($context, $message);
    }

    /**
     * Suggest ticket creation for problems
     */
    private function suggestTicketCreation(UpdateContext $context): ProcessingResult
    {
        $message = "به نظر مشکلی دارید! 🔧\n\n";
        $message .= "برای حل سریع‌تر مشکلتون:\n";
        $message .= "• تیکت پشتیبانی ایجاد کنید: /ticket new [شرح مشکل]\n";
        $message .= "• جزئیات بیشتر ارائه دهید تا بتونیم بهتر کمک کنیم\n\n";
        $message .= "تیم پشتیبانی ما آماده کمک هستن! 💪";

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '🎫 ایجاد تیکت', 'callback_data' => 'new_ticket'],
                    ['text' => '📞 تماس با پشتیبانی', 'callback_data' => 'contact_support'],
                ]
            ]
        ];

        return $this->sendResponse($context, $message, [
            'reply_markup' => json_encode($keyboard)
        ]);
    }

    /**
     * Send response message
     */
    private function sendResponse(UpdateContext $context, string $message, array $options = []): ProcessingResult
    {
        $response = $this->apiClient->sendMessage(
            $context->getChatId(),
            $message,
            $options
        );

        if ($response->isError()) {
            $this->logger->error('Failed to send message response', [
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
}