<?php

namespace App\Services\Telegram\Handlers;

use App\Services\Telegram\Contracts\TelegramApiClientInterface;
use App\Services\Telegram\Core\UpdateContext;
use App\Services\Telegram\Core\ProcessingResult;
use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;

/**
 * Callback Query Handler
 * 
 * Handles inline keyboard button presses and
 * callback queries from Telegram bot interactions
 */
class CallbackQueryHandler
{
    public function __construct(
        private TelegramApiClientInterface $apiClient,
        private ?LoggerInterface $logger = null
    ) {
        $this->logger = $logger ?? Log::getFacadeRoot();
    }

    /**
     * Handle callback query
     */
    public function handle(UpdateContext $context): ProcessingResult
    {
        try {
            $callbackData = $context->getCallbackData();
            $callbackQueryId = $context->getCallbackQueryId();

            $this->logger->info('Processing callback query', [
                'user_id' => $context->getUserId(),
                'chat_id' => $context->getChatId(),
                'callback_data' => $callbackData,
            ]);

            // Answer the callback query first
            $this->apiClient->answerCallbackQuery($callbackQueryId, '', false);

            // Route based on callback data
            return $this->routeCallback($context, $callbackData);

        } catch (\Exception $e) {
            $this->logger->error('Callback query handling error', [
                'user_id' => $context->getUserId(),
                'callback_data' => $context->getCallbackData(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return ProcessingResult::error(
                'خطایی در پردازش درخواست رخ داد',
                ProcessingResult::ERROR_INTERNAL
            );
        }
    }

    /**
     * Route callback based on data
     */
    private function routeCallback(UpdateContext $context, string $callbackData): ProcessingResult
    {
        // Parse callback data (format: action:param1:param2)
        $parts = explode(':', $callbackData);
        $action = $parts[0] ?? '';

        return match ($action) {
            'new_ticket' => $this->handleNewTicketCallback($context),
            'ask_question' => $this->handleAskQuestionCallback($context),
            'help' => $this->handleHelpCallback($context),
            'about' => $this->handleAboutCallback($context),
            'main_menu' => $this->handleMainMenuCallback($context),
            'contact_info' => $this->handleContactInfoCallback($context),
            'contact_support' => $this->handleContactSupportCallback($context),
            'ticket_list' => $this->handleTicketListCallback($context),
            'ticket_show' => $this->handleTicketShowCallback($context, $parts[1] ?? ''),
            'ticket_close' => $this->handleTicketCloseCallback($context, $parts[1] ?? ''),
            default => $this->handleUnknownCallback($context, $callbackData),
        };
    }

    /**
     * Handle new ticket callback
     */
    private function handleNewTicketCallback(UpdateContext $context): ProcessingResult
    {
        $message = "🎫 ایجاد تیکت جدید\n\n";
        $message .= "برای ایجاد تیکت پشتیبانی، از دستور زیر استفاده کنید:\n\n";
        $message .= "📝 <code>/ticket new [موضوع تیکت]</code>\n\n";
        $message .= "مثال:\n";
        $message .= "<code>/ticket new مشکل در پرداخت</code>\n\n";
        $message .= "یا می‌تونید موضوع تیکت رو به صورت پیام ساده ارسال کنید.";

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '📋 مشاهده تیکت‌ها', 'callback_data' => 'ticket_list'],
                ],
                [
                    ['text' => '🔙 منوی اصلی', 'callback_data' => 'main_menu'],
                ]
            ]
        ];

        return $this->editMessage($context, $message, $keyboard);
    }

    /**
     * Handle ask question callback
     */
    private function handleAskQuestionCallback(UpdateContext $context): ProcessingResult
    {
        $message = "❓ پرسش از ربات\n\n";
        $message .= "می‌تونید سوال خود را مستقیماً در چت بپرسید.\n\n";
        $message .= "🤖 ربات سعی می‌کند پاسخ مناسب ارائه دهد.\n";
        $message .= "🎫 برای پیگیری دقیق‌تر، تیکت پشتیبانی ایجاد کنید.\n\n";
        $message .= "💡 نکته: سوالات واضح‌تر، پاسخ‌های بهتری دریافت می‌کنند!";

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '🎫 ایجاد تیکت', 'callback_data' => 'new_ticket'],
                ],
                [
                    ['text' => '🔙 منوی اصلی', 'callback_data' => 'main_menu'],
                ]
            ]
        ];

        return $this->editMessage($context, $message, $keyboard);
    }

    /**
     * Handle help callback
     */
    private function handleHelpCallback(UpdateContext $context): ProcessingResult
    {
        $message = "📖 راهنمای ربات پیشخوانک\n\n";
        $message .= "🔹 دستورات عمومی:\n";
        $message .= "/start - شروع استفاده\n";
        $message .= "/help - نمایش راهنما\n";
        $message .= "/about - اطلاعات ربات\n\n";
        $message .= "🎫 مدیریت تیکت:\n";
        $message .= "/tickets - مشاهده تیکت‌ها\n";
        $message .= "/ticket new [موضوع] - تیکت جدید\n";
        $message .= "/ticket show [ID] - جزئیات تیکت\n\n";
        $message .= "💬 می‌تونید سوالات خود را مستقیماً بپرسید!";

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => 'ℹ️ درباره ربات', 'callback_data' => 'about'],
                ],
                [
                    ['text' => '🔙 منوی اصلی', 'callback_data' => 'main_menu'],
                ]
            ]
        ];

        return $this->editMessage($context, $message, $keyboard);
    }

    /**
     * Handle about callback
     */
    private function handleAboutCallback(UpdateContext $context): ProcessingResult
    {
        $message = "ℹ️ درباره ربات پیشخوانک\n\n";
        $message .= "🏢 این ربات برای ارائه خدمات مالی پیشخوانک طراحی شده است.\n\n";
        $message .= "🔧 امکانات:\n";
        $message .= "• سیستم تیکت پشتیبانی\n";
        $message .= "• پاسخگویی هوشمند\n";
        $message .= "• پشتیبانی کامل از فارسی\n";
        $message .= "• امنیت پیشرفته\n\n";
        $message .= "🚀 نسخه: 2.0\n";
        $message .= "📅 بروزرسانی: " . date('Y/m/d') . "\n";
        $message .= "🔒 امن و محرمانه";

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '🌐 وبسایت', 'url' => 'https://pishkhanak.com'],
                    ['text' => '📞 تماس', 'callback_data' => 'contact_info'],
                ],
                [
                    ['text' => '🔙 منوی اصلی', 'callback_data' => 'main_menu'],
                ]
            ]
        ];

        return $this->editMessage($context, $message, $keyboard);
    }

    /**
     * Handle main menu callback
     */
    private function handleMainMenuCallback(UpdateContext $context): ProcessingResult
    {
        $firstName = $context->getFrom()['first_name'] ?? 'کاربر';
        
        $message = "🌟 سلام {$firstName}، به ربات پیشخوانک خوش آمدید!\n\n";
        $message .= "📋 امکانات موجود:\n";
        $message .= "• تیکت پشتیبانی\n";
        $message .= "• پرسش و پاسخ\n";
        $message .= "• راهنمای کامل\n\n";
        $message .= "یکی از گزینه‌های زیر را انتخاب کنید:";

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '🎫 تیکت جدید', 'callback_data' => 'new_ticket'],
                    ['text' => '❓ پرسش', 'callback_data' => 'ask_question'],
                ],
                [
                    ['text' => '📚 راهنما', 'callback_data' => 'help'],
                    ['text' => 'ℹ️ درباره ما', 'callback_data' => 'about'],
                ]
            ]
        ];

        return $this->editMessage($context, $message, $keyboard);
    }

    /**
     * Handle contact info callback
     */
    private function handleContactInfoCallback(UpdateContext $context): ProcessingResult
    {
        $message = "📞 اطلاعات تماس\n\n";
        $message .= "🌐 وبسایت: https://pishkhanak.com\n";
        $message .= "📧 ایمیل: support@pishkhanak.com\n";
        $message .= "📱 تلگرام: @PishkhanakSupport\n\n";
        $message .= "🕐 ساعات پاسخگویی:\n";
        $message .= "شنبه تا پنج‌شنبه: ۸ تا ۱۸\n";
        $message .= "جمعه: ۹ تا ۱۳\n\n";
        $message .= "🎫 برای پیگیری سریع‌تر، تیکت ایجاد کنید.";

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '🎫 ایجاد تیکت', 'callback_data' => 'new_ticket'],
                ],
                [
                    ['text' => '🔙 بازگشت', 'callback_data' => 'about'],
                ]
            ]
        ];

        return $this->editMessage($context, $message, $keyboard);
    }

    /**
     * Handle contact support callback
     */
    private function handleContactSupportCallback(UpdateContext $context): ProcessingResult
    {
        $message = "📞 ارتباط با پشتیبانی\n\n";
        $message .= "برای دریافت پشتیبانی سریع:\n\n";
        $message .= "🎫 <strong>تیکت پشتیبانی (توصیه شده)</strong>\n";
        $message .= "• پاسخ تخصصی و دقیق\n";
        $message .= "• قابلیت پیگیری\n";
        $message .= "• ثبت تاریخچه\n\n";
        $message .= "📱 <strong>تماس مستقیم</strong>\n";
        $message .= "• برای موارد فوری\n";
        $message .= "• در ساعات اداری\n\n";
        $message .= "💡 تیکت پشتیبانی بهترین روش برای حل مشکلات شماست!";

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '🎫 ایجاد تیکت', 'callback_data' => 'new_ticket'],
                ],
                [
                    ['text' => '📞 اطلاعات تماس', 'callback_data' => 'contact_info'],
                ],
                [
                    ['text' => '🔙 منوی اصلی', 'callback_data' => 'main_menu'],
                ]
            ]
        ];

        return $this->editMessage($context, $message, $keyboard);
    }

    /**
     * Handle ticket list callback
     */
    private function handleTicketListCallback(UpdateContext $context): ProcessingResult
    {
        $message = "📋 مدیریت تیکت‌ها\n\n";
        $message .= "برای مشاهده تیکت‌های خود:\n";
        $message .= "<code>/tickets</code>\n\n";
        $message .= "برای ایجاد تیکت جدید:\n";
        $message .= "<code>/ticket new [موضوع]</code>\n\n";
        $message .= "مثال:\n";
        $message .= "<code>/ticket new مشکل در ورود به حساب</code>";

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '🎫 تیکت جدید', 'callback_data' => 'new_ticket'],
                ],
                [
                    ['text' => '🔙 منوی اصلی', 'callback_data' => 'main_menu'],
                ]
            ]
        ];

        return $this->editMessage($context, $message, $keyboard);
    }

    /**
     * Handle ticket show callback
     */
    private function handleTicketShowCallback(UpdateContext $context, string $ticketId): ProcessingResult
    {
        if (empty($ticketId)) {
            return ProcessingResult::error(
                'شناسه تیکت معتبر نیست',
                ProcessingResult::ERROR_INVALID_INPUT
            );
        }

        $message = "🎫 جزئیات تیکت #{$ticketId}\n\n";
        $message .= "برای مشاهده کامل تیکت:\n";
        $message .= "<code>/ticket show {$ticketId}</code>\n\n";
        $message .= "برای ارسال پاسخ:\n";
        $message .= "<code>/ticket reply {$ticketId} [پیام شما]</code>";

        return $this->editMessage($context, $message);
    }

    /**
     * Handle ticket close callback
     */
    private function handleTicketCloseCallback(UpdateContext $context, string $ticketId): ProcessingResult
    {
        if (empty($ticketId)) {
            return ProcessingResult::error(
                'شناسه تیکت معتبر نیست',
                ProcessingResult::ERROR_INVALID_INPUT
            );
        }

        $message = "❌ بستن تیکت #{$ticketId}\n\n";
        $message .= "آیا مطمئن هستید که می‌خواهید این تیکت را ببندید؟\n\n";
        $message .= "برای بستن تیکت:\n";
        $message .= "<code>/ticket close {$ticketId}</code>";

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '❌ بستن تیکت', 'callback_data' => "confirm_close:{$ticketId}"],
                ],
                [
                    ['text' => '🔙 انصراف', 'callback_data' => 'ticket_list'],
                ]
            ]
        ];

        return $this->editMessage($context, $message, $keyboard);
    }

    /**
     * Handle unknown callback
     */
    private function handleUnknownCallback(UpdateContext $context, string $callbackData): ProcessingResult
    {
        $this->logger->warning('Unknown callback data received', [
            'user_id' => $context->getUserId(),
            'callback_data' => $callbackData,
        ]);

        // Answer with error message
        $this->apiClient->answerCallbackQuery(
            $context->getCallbackQueryId(),
            '⚠️ گزینه نامعتبر',
            true
        );

        return ProcessingResult::success();
    }

    /**
     * Edit message with new content and keyboard
     */
    private function editMessage(UpdateContext $context, string $message, array $keyboard = null): ProcessingResult
    {
        $options = ['parse_mode' => 'HTML'];
        
        if ($keyboard) {
            $options['reply_markup'] = json_encode($keyboard);
        }

        $response = $this->apiClient->editMessage(
            $context->getChatId(),
            $context->getMessageId(),
            $message,
            $options
        );

        if ($response->isError()) {
            $this->logger->error('Failed to edit callback message', [
                'user_id' => $context->getUserId(),
                'chat_id' => $context->getChatId(),
                'error' => $response->getError(),
            ]);

            // Fallback: send new message if edit fails
            $sendResponse = $this->apiClient->sendMessage(
                $context->getChatId(),
                $message,
                $options
            );

            if ($sendResponse->isError()) {
                return ProcessingResult::error(
                    'خطا در ارسال پاسخ',
                    ProcessingResult::ERROR_INTERNAL
                );
            }

            return ProcessingResult::success([
                'message_id' => $sendResponse->getMessageId(),
                'sent_message' => $message,
            ]);
        }

        return ProcessingResult::success([
            'message_id' => $context->getMessageId(),
            'edited_message' => $message,
        ]);
    }
}