<?php

namespace App\Services\Telegram\Handlers;

use App\Services\Telegram\Core\UpdateContext;
use App\Services\Telegram\Core\ProcessingResult;

/**
 * General Command Handler
 * 
 * Handles basic bot commands like /start, /help, /about
 * Available to all users in both private and group chats
 */
class GeneralCommandHandler extends AbstractCommandHandler
{
    protected array $commandNames = ['start', 'help', 'about', 'راهنما', 'درباره'];
    protected string $description = 'دستورات عمومی ربات';
    protected bool $requiresAdmin = false;
    protected bool $availableInGroups = true;
    protected array $usageExamples = [
        '/start - شروع استفاده از ربات',
        '/help - نمایش راهنمای کامل',
        '/about - اطلاعات ربات',
    ];

    protected function execute(UpdateContext $context): ProcessingResult
    {
        $command = strtolower($context->getCommand());

        return match ($command) {
            'start' => $this->handleStart($context),
            'help', 'راهنما' => $this->handleHelp($context),
            'about', 'درباره' => $this->handleAbout($context),
            default => $this->handleUnknownCommand($context),
        };
    }

    /**
     * Handle /start command
     */
    private function handleStart(UpdateContext $context): ProcessingResult
    {
        $firstName = $context->getFrom()['first_name'] ?? 'کاربر';
        
        $message = "🌟 سلام {$firstName}، به ربات پیشخوانک خوش آمدید!\n\n";
        $message .= "📋 امکانات موجود:\n";
        $message .= "• تیکت پشتیبانی (/tickets)\n";
        $message .= "• پرسش و پاسخ (/ask)\n";
        $message .= "• راهنما (/help)\n\n";
        $message .= "برای شروع، یکی از دستورات بالا را انتخاب کنید.";

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

        return $this->sendResponse($context, $message, [
            'reply_markup' => json_encode($keyboard)
        ]);
    }

    /**
     * Handle /help command
     */
    private function handleHelp(UpdateContext $context): ProcessingResult
    {
        $isAdmin = $context->isFromAdmin();
        
        $message = "📖 راهنمای ربات پیشخوانک\n\n";
        
        // General commands
        $message .= "🔹 دستورات عمومی:\n";
        $message .= "/start - شروع استفاده از ربات\n";
        $message .= "/help - نمایش این راهنما\n";
        $message .= "/about - اطلاعات ربات\n\n";
        
        // User commands
        $message .= "👤 دستورات کاربری:\n";
        $message .= "/tickets - مدیریت تیکت‌های شما\n";
        $message .= "/ask [سوال] - پرسش از ربات\n";
        $message .= "/status - وضعیت حساب کاربری\n\n";
        
        // Admin commands (only shown to admins)
        if ($isAdmin) {
            $message .= "👑 دستورات مدیریتی:\n";
            $message .= "/admin - پنل مدیریت\n";
            $message .= "/stats - آمار ربات\n";
            $message .= "/users - مدیریت کاربران\n";
            $message .= "/broadcast [پیام] - ارسال پیام همگانی\n\n";
        }
        
        $message .= "💡 برای اطلاعات بیشتر از /about استفاده کنید.";

        return $this->sendResponse($context, $message);
    }

    /**
     * Handle /about command
     */
    private function handleAbout($context): ProcessingResult
    {
        $message = "ℹ️ درباره ربات پیشخوانک\n\n";
        $message .= "🏢 این ربات برای خدمات مالی پیشخوانک طراحی شده است.\n\n";
        $message .= "🔧 امکانات:\n";
        $message .= "• سیستم تیکت پشتیبانی\n";
        $message .= "• پاسخگویی هوشمند\n";
        $message .= "• پشتیبانی از زبان فارسی\n";
        $message .= "• امنیت بالا\n\n";
        $message .= "🚀 نسخه: 2.0\n";
        $message .= "📅 آخرین بروزرسانی: " . date('Y/m/d');

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '🌐 وبسایت', 'url' => 'https://pishkhanak.com'],
                    ['text' => '📞 تماس', 'callback_data' => 'contact_info'],
                ],
                [
                    ['text' => '🔙 بازگشت', 'callback_data' => 'main_menu'],
                ]
            ]
        ];

        return $this->sendResponse($context, $message, [
            'reply_markup' => json_encode($keyboard)
        ]);
    }

    /**
     * Handle unknown general commands
     */
    private function handleUnknownCommand(UpdateContext $context): ProcessingResult
    {
        $command = $context->getCommand();
        
        $message = "❓ دستور '{$command}' شناخته نشده است.\n\n";
        $message .= "از /help برای مشاهده دستورات موجود استفاده کنید.";

        return $this->sendResponse($context, $message);
    }
}