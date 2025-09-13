<?php

namespace App\Services\Telegram\Handlers;

use App\Services\Telegram\Contracts\TelegramApiClientInterface;
use App\Services\Telegram\Core\UpdateContext;
use App\Services\Telegram\Core\ProcessingResult;
use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;

/**
 * Inline Query Handler
 * 
 * Handles inline queries when users type @botname in any chat
 * Provides search results and quick actions
 */
class InlineQueryHandler
{
    public function __construct(
        private TelegramApiClientInterface $apiClient,
        private ?LoggerInterface $logger = null
    ) {
        $this->logger = $logger ?? Log::getFacadeRoot();
    }

    /**
     * Handle inline query
     */
    public function handle(UpdateContext $context): ProcessingResult
    {
        try {
            $query = $context->getInlineQuery() ?? '';
            $inlineQueryId = $context->getInlineQueryId();

            $this->logger->info('Processing inline query', [
                'user_id' => $context->getUserId(),
                'query' => $query,
                'query_length' => mb_strlen($query),
            ]);

            // Generate results based on query
            $results = $this->generateInlineResults($query);

            // Answer inline query
            $response = $this->apiClient->answerInlineQuery($inlineQueryId, $results);

            if ($response->isError()) {
                $this->logger->error('Failed to answer inline query', [
                    'user_id' => $context->getUserId(),
                    'query' => $query,
                    'error' => $response->getError(),
                ]);

                return ProcessingResult::error(
                    'خطا در پاسخ به درخواست',
                    ProcessingResult::ERROR_INTERNAL
                );
            }

            return ProcessingResult::success([
                'query' => $query,
                'results_count' => count($results),
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Inline query handling error', [
                'user_id' => $context->getUserId(),
                'query' => $context->getInlineQuery(),
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
     * Generate inline query results
     */
    private function generateInlineResults(string $query): array
    {
        $query = mb_strtolower(trim($query));
        $results = [];

        // Default help result
        if (empty($query) || str_contains($query, 'help') || str_contains($query, 'راهنما')) {
            $results[] = [
                'type' => 'article',
                'id' => 'help',
                'title' => '📚 راهنمای ربات پیشخوانک',
                'description' => 'مشاهده راهنمای کامل ربات و دستورات موجود',
                'input_message_content' => [
                    'message_text' => $this->getHelpText(),
                    'parse_mode' => 'HTML',
                ],
                'reply_markup' => [
                    'inline_keyboard' => [
                        [
                            ['text' => '🎫 تیکت جدید', 'callback_data' => 'new_ticket'],
                            ['text' => '❓ پرسش', 'callback_data' => 'ask_question'],
                        ]
                    ]
                ]
            ];
        }

        // Ticket-related results
        if (str_contains($query, 'ticket') || str_contains($query, 'تیکت')) {
            $results[] = [
                'type' => 'article',
                'id' => 'new_ticket',
                'title' => '🎫 ایجاد تیکت جدید',
                'description' => 'ایجاد تیکت پشتیبانی برای حل مشکلات',
                'input_message_content' => [
                    'message_text' => 'برای ایجاد تیکت جدید از دستور زیر استفاده کنید:' . "\n\n" .
                                    '<code>/ticket new [موضوع تیکت]</code>' . "\n\n" .
                                    'مثال: <code>/ticket new مشکل در پرداخت</code>',
                    'parse_mode' => 'HTML',
                ]
            ];

            $results[] = [
                'type' => 'article',
                'id' => 'ticket_list',
                'title' => '📋 مشاهده تیکت‌ها',
                'description' => 'مشاهده لیست تمام تیکت‌های شما',
                'input_message_content' => [
                    'message_text' => 'برای مشاهده تیکت‌های خود از دستور زیر استفاده کنید:' . "\n\n" .
                                    '<code>/tickets</code>',
                    'parse_mode' => 'HTML',
                ]
            ];
        }

        // Contact information
        if (str_contains($query, 'contact') || str_contains($query, 'تماس')) {
            $results[] = [
                'type' => 'article',
                'id' => 'contact',
                'title' => '📞 اطلاعات تماس',
                'description' => 'راه‌های ارتباط با پشتیبانی پیشخوانک',
                'input_message_content' => [
                    'message_text' => $this->getContactText(),
                    'parse_mode' => 'HTML',
                ]
            ];
        }

        // About information
        if (str_contains($query, 'about') || str_contains($query, 'درباره')) {
            $results[] = [
                'type' => 'article',
                'id' => 'about',
                'title' => 'ℹ️ درباره ربات پیشخوانک',
                'description' => 'اطلاعات کامل درباره ربات و امکانات آن',
                'input_message_content' => [
                    'message_text' => $this->getAboutText(),
                    'parse_mode' => 'HTML',
                ]
            ];
        }

        // Quick actions
        if (empty($query)) {
            $results[] = [
                'type' => 'article',
                'id' => 'quick_support',
                'title' => '🚀 پشتیبانی سریع',
                'description' => 'دسترسی سریع به امکانات پشتیبانی',
                'input_message_content' => [
                    'message_text' => '🚀 <strong>پشتیبانی سریع پیشخوانک</strong>' . "\n\n" .
                                    '🎫 تیکت جدید: <code>/ticket new [موضوع]</code>' . "\n" .
                                    '📋 مشاهده تیکت‌ها: <code>/tickets</code>' . "\n" .
                                    '❓ پرسش: فقط سوال خود را بنویسید' . "\n" .
                                    '📚 راهنما: <code>/help</code>',
                    'parse_mode' => 'HTML',
                ],
                'reply_markup' => [
                    'inline_keyboard' => [
                        [
                            ['text' => '🎫 تیکت جدید', 'callback_data' => 'new_ticket'],
                            ['text' => '📋 تیکت‌ها', 'callback_data' => 'ticket_list'],
                        ]
                    ]
                ]
            ];
        }

        // Search in FAQ (placeholder)
        if (!empty($query) && !in_array($query, ['help', 'راهنما', 'ticket', 'تیکت', 'contact', 'تماس', 'about', 'درباره'])) {
            $results[] = [
                'type' => 'article',
                'id' => 'search_' . md5($query),
                'title' => '🔍 جستجو: ' . $query,
                'description' => 'جستجو در پایگاه دانش و سوالات متداول',
                'input_message_content' => [
                    'message_text' => '🔍 <strong>نتیجه جستجو برای:</strong> ' . htmlspecialchars($query) . "\n\n" .
                                    'متأسفانه نتیجه‌ای یافت نشد.' . "\n\n" .
                                    '💡 پیشنهاد‌ها:' . "\n" .
                                    '• سوال خود را مستقیماً در چت بپرسید' . "\n" .
                                    '• تیکت پشتیبانی ایجاد کنید: <code>/ticket new [سوال شما]</code>' . "\n" .
                                    '• راهنما را مطالعه کنید: <code>/help</code>',
                    'parse_mode' => 'HTML',
                ]
            ];
        }

        return $results;
    }

    /**
     * Get help text
     */
    private function getHelpText(): string
    {
        return '📖 <strong>راهنمای ربات پیشخوانک</strong>' . "\n\n" .
               '🔹 <strong>دستورات عمومی:</strong>' . "\n" .
               '/start - شروع استفاده از ربات' . "\n" .
               '/help - نمایش این راهنما' . "\n" .
               '/about - اطلاعات ربات' . "\n\n" .
               '🎫 <strong>مدیریت تیکت:</strong>' . "\n" .
               '/tickets - مشاهده تیکت‌های شما' . "\n" .
               '/ticket new [موضوع] - ایجاد تیکت جدید' . "\n" .
               '/ticket show [ID] - جزئیات تیکت' . "\n\n" .
               '💬 <strong>گفتگو:</strong>' . "\n" .
               'می‌تونید سوالات خود را مستقیماً بپرسید!';
    }

    /**
     * Get contact text
     */
    private function getContactText(): string
    {
        return '📞 <strong>اطلاعات تماس پیشخوانک</strong>' . "\n\n" .
               '🌐 وبسایت: https://pishkhanak.com' . "\n" .
               '📧 ایمیل: support@pishkhanak.com' . "\n" .
               '📱 تلگرام: @PishkhanakSupport' . "\n\n" .
               '🕐 <strong>ساعات پاسخگویی:</strong>' . "\n" .
               'شنبه تا پنج‌شنبه: ۸ تا ۱۸' . "\n" .
               'جمعه: ۹ تا ۱۳' . "\n\n" .
               '🎫 برای پیگیری بهتر، تیکت ایجاد کنید!';
    }

    /**
     * Get about text
     */
    private function getAboutText(): string
    {
        return 'ℹ️ <strong>درباره ربات پیشخوانک</strong>' . "\n\n" .
               '🏢 این ربات برای ارائه خدمات مالی پیشخوانک طراحی شده است.' . "\n\n" .
               '🔧 <strong>امکانات:</strong>' . "\n" .
               '• سیستم تیکت پشتیبانی پیشرفته' . "\n" .
               '• پاسخگویی هوشمند و خودکار' . "\n" .
               '• پشتیبانی کامل از زبان فارسی' . "\n" .
               '• امنیت بالا و محافظت از اطلاعات' . "\n\n" .
               '🚀 نسخه: 2.0' . "\n" .
               '📅 آخرین بروزرسانی: ' . date('Y/m/d') . "\n" .
               '🔒 کاملاً امن و محرمانه';
    }
}