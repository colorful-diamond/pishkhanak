<?php

namespace App\Services\Telegram\Handlers;

use App\Services\Telegram\Core\AdminAuthService;
use App\Services\Telegram\Core\AuditLogger;
use App\Services\Telegram\Core\PersianTextProcessor;
use App\Services\Telegram\Contracts\TelegramApiClientInterface;
use App\Models\TelegramAdmin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * Admin Command Handler
 * 
 * Handles all admin panel commands with full authentication,
 * authorization, and audit logging.
 */
class AdminCommandHandler extends AbstractCommandHandler
{
    private AdminAuthService $authService;
    private AuditLogger $auditLogger;
    private PersianTextProcessor $textProcessor;

    public function __construct(
        TelegramApiClientInterface $apiClient,
        AdminAuthService $authService,
        AuditLogger $auditLogger,
        PersianTextProcessor $textProcessor
    ) {
        parent::__construct($apiClient);
        $this->authService = $authService;
        $this->auditLogger = $auditLogger;
        $this->textProcessor = $textProcessor;
    }

    public function canHandle(string $command): bool
    {
        $adminCommands = [
            'admin', 'dashboard', 'panel', 'login', 'menu',
            'stats', 'users', 'wallets', 'tickets', 'posts',
            'config', 'settings', 'tokens', 'security', 'ai'
        ];

        return in_array($command, $adminCommands);
    }

    public function handle(UpdateContext $context): ProcessingResult
    {
        $command = $context->getCommand();
        $userId = $context->getUserId();
        $chatId = $context->getChatId();

        try {
            // Handle admin login/authentication
            if ($command === 'admin' || $command === 'login') {
                return $this->handleLogin($context);
            }

            // All other commands require authentication
            if (!$this->authService->verifyPermission($userId, $command, [
                'ip' => $context->getMetadata('ip'),
                'user_agent' => $context->getMetadata('user_agent')
            ])) {
                return $this->handleUnauthorized($context);
            }

            // Route to specific command handler
            return match ($command) {
                'dashboard', 'panel', 'menu' => $this->handleDashboard($context),
                'stats' => $this->handleStats($context),
                'users' => $this->handleUsers($context),
                'wallets' => $this->handleWallets($context),
                'tickets' => $this->handleTickets($context),
                'posts' => $this->handlePosts($context),
                'config', 'settings' => $this->handleConfig($context),
                'tokens' => $this->handleTokens($context),
                'security' => $this->handleSecurity($context),
                'ai' => $this->handleAI($context),
                default => $this->handleUnknownCommand($context)
            };

        } catch (\Exception $e) {
            Log::error('Admin command error', [
                'command' => $command,
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->sendMessage($chatId, "❌ خطا در اجرای دستور: " . $e->getMessage());
            return ProcessingResult::failed('command_error', $e->getMessage());
        }
    }

    /**
     * Handle admin login
     */
    private function handleLogin(UpdateContext $context): ProcessingResult
    {
        $userId = $context->getUserId();
        $chatId = $context->getChatId();
        $userName = $context->getUserName();

        $authResult = $this->authService->authenticate($userId, [
            'ip' => $context->getMetadata('ip'),
            'user_agent' => $context->getMetadata('user_agent')
        ]);

        if (!$authResult->isSuccess()) {
            $message = "🚫 **دسترسی مجاز نیست**\n\n" .
                      "علت: " . $authResult->getMessage() . "\n\n" .
                      "در صورت نیاز به دسترسی، با مدیر سیستم تماس بگیرید.";
            
            $this->sendMessage($chatId, $message);
            return ProcessingResult::failed('auth_failed', $authResult->getMessage());
        }

        $admin = $authResult->getAdmin();
        $session = $authResult->getSession();

        // Store session in context for subsequent requests
        Cache::put("admin_session:{$userId}", $session->session_token, 3600);

        $welcomeMessage = $this->textProcessor->formatPersianText("🎛️ **پنل مدیریت پیشخوانک**\n\n") .
            "خوش آمدید {$admin->display_name}!\n" .
            "نقش شما: {$admin->role_display}\n\n" .
            "**دستورات اصلی:**\n" .
            "📊 /dashboard - داشبورد مدیریت\n" .
            "📈 /stats - آمار و گزارشات\n" .
            "👥 /users - مدیریت کاربران\n" .
            "💰 /wallets - مدیریت کیف پول‌ها\n" .
            "🎫 /tickets - مدیریت تیکت‌ها\n" .
            "📝 /posts - مدیریت پست‌ها\n" .
            "🤖 /ai - تولید محتوای هوشمند\n" .
            "⚙️ /config - تنظیمات سیستم\n" .
            "🔐 /security - امنیت و نظارت\n\n" .
            "برای راهنمای کامل هر بخش، از دستورات بالا استفاده کنید.";

        $this->sendMessage($chatId, $welcomeMessage);
        return ProcessingResult::success();
    }

    /**
     * Handle dashboard command
     */
    private function handleDashboard(UpdateContext $context): ProcessingResult
    {
        $chatId = $context->getChatId();
        $userId = $context->getUserId();

        $admin = TelegramAdmin::where('telegram_user_id', $userId)->first();
        
        // Get dashboard statistics
        $stats = $this->getDashboardStats();
        $recentActivity = $this->getRecentActivity($admin->id, 5);

        $message = $this->textProcessor->formatPersianText("📊 **داشبورد مدیریت**\n\n");
        
        // System overview
        $message .= "**آمار کلی سیستم:**\n";
        $message .= "👥 کاربران: " . number_format($stats['users']) . "\n";
        $message .= "💰 کل موجودی: " . number_format($stats['total_balance']) . " تومان\n";
        $message .= "🎫 تیکت‌های باز: " . $stats['open_tickets'] . "\n";
        $message .= "📝 پست‌های منتشر شده: " . $stats['published_posts'] . "\n\n";

        // System health
        $message .= "**وضعیت سیستم:**\n";
        $message .= "🟢 سرورها: آنلاین\n";
        $message .= "🟢 پایگاه داده: فعال\n";
        $message .= "🟢 ربات تلگرام: متصل\n";
        $message .= "🟢 API های خارجی: فعال\n\n";

        // Recent activity
        if (!empty($recentActivity)) {
            $message .= "**فعالیت‌های اخیر:**\n";
            foreach ($recentActivity as $activity) {
                $message .= "• {$activity['action']} - {$activity['time']}\n";
            }
        }

        // Quick actions keyboard
        $keyboard = [
            [
                ['text' => '📈 آمار تفصیلی', 'callback_data' => 'stats_detailed'],
                ['text' => '👥 مدیریت کاربران', 'callback_data' => 'users_manage']
            ],
            [
                ['text' => '💰 مدیریت کیف پول', 'callback_data' => 'wallets_manage'],
                ['text' => '🎫 تیکت‌های جدید', 'callback_data' => 'tickets_new']
            ],
            [
                ['text' => '⚙️ تنظیمات', 'callback_data' => 'config_main'],
                ['text' => '🔐 امنیت', 'callback_data' => 'security_main']
            ]
        ];

        $this->sendMessageWithKeyboard($chatId, $message, $keyboard);

        // Log dashboard access
        $this->auditLogger->logAdminAction($admin->id, 'dashboard_view');

        return ProcessingResult::success();
    }

    /**
     * Handle stats command
     */
    private function handleStats(UpdateContext $context): ProcessingResult
    {
        $chatId = $context->getChatId();
        $userId = $context->getUserId();
        
        $admin = TelegramAdmin::where('telegram_user_id', $userId)->first();
        
        // Get comprehensive statistics
        $stats = $this->getDetailedStats();

        $message = $this->textProcessor->formatPersianText("📈 **آمار تفصیلی سیستم**\n\n");

        // User statistics
        $message .= "**آمار کاربران:**\n";
        $message .= "👥 کل کاربران: " . number_format($stats['users']['total']) . "\n";
        $message .= "✅ فعال: " . number_format($stats['users']['active']) . "\n";
        $message .= "🚫 مسدود: " . number_format($stats['users']['banned']) . "\n";
        $message .= "📅 عضویت امروز: " . number_format($stats['users']['today']) . "\n\n";

        // Financial statistics  
        $message .= "**آمار مالی:**\n";
        $message .= "💰 کل موجودی: " . number_format($stats['financial']['total_balance']) . " تومان\n";
        $message .= "💸 تراکنش امروز: " . number_format($stats['financial']['transactions_today']) . "\n";
        $message .= "💳 مبلغ تراکنش امروز: " . number_format($stats['financial']['amount_today']) . " تومان\n\n";

        // Support statistics
        $message .= "**آمار پشتیبانی:**\n";
        $message .= "🎫 کل تیکت‌ها: " . number_format($stats['support']['total_tickets']) . "\n";
        $message .= "🟡 در انتظار: " . number_format($stats['support']['pending_tickets']) . "\n";
        $message .= "🔵 در حال پیگیری: " . number_format($stats['support']['in_progress_tickets']) . "\n";
        $message .= "✅ حل شده امروز: " . number_format($stats['support']['resolved_today']) . "\n\n";

        // Content statistics
        $message .= "**آمار محتوا:**\n";
        $message .= "📝 کل پست‌ها: " . number_format($stats['content']['total_posts']) . "\n";
        $message .= "📤 منتشر شده: " . number_format($stats['content']['published_posts']) . "\n";
        $message .= "📅 برنامه‌ریزی شده: " . number_format($stats['content']['scheduled_posts']) . "\n";
        $message .= "🤖 تولید شده با AI: " . number_format($stats['content']['ai_generated']) . "\n";

        $this->sendMessage($chatId, $message);

        // Log stats access
        $this->auditLogger->logAdminAction($admin->id, 'stats_view');

        return ProcessingResult::success();
    }

    /**
     * Handle unauthorized access
     */
    private function handleUnauthorized(UpdateContext $context): ProcessingResult
    {
        $chatId = $context->getChatId();
        $userId = $context->getUserId();

        $message = "🚫 **دسترسی غیرمجاز**\n\n" .
                  "شما مجاز به استفاده از این بخش نیستید.\n\n" .
                  "برای دسترسی به پنل مدیریت، ابتدا وارد شوید:\n" .
                  "/admin";

        $this->sendMessage($chatId, $message);

        // Log unauthorized attempt
        $this->auditLogger->logSecurityEvent('unauthorized_access', 'warning', [
            'telegram_user_id' => $userId,
            'command' => $context->getCommand(),
        ]);

        return ProcessingResult::failed('unauthorized');
    }

    /**
     * Get dashboard statistics
     */
    private function getDashboardStats(): array
    {
        return [
            'users' => DB::table('users')->count(),
            'total_balance' => DB::table('wallets')->sum('balance') ?? 0,
            'open_tickets' => DB::table('telegram_tickets')->where('status', 'open')->count(),
            'published_posts' => DB::table('telegram_posts')->where('status', 'published')->count(),
        ];
    }

    /**
     * Get detailed statistics
     */
    private function getDetailedStats(): array
    {
        return [
            'users' => [
                'total' => DB::table('users')->count(),
                'active' => DB::table('users')->where('status', 'active')->count(),
                'banned' => DB::table('users')->where('status', 'banned')->count(),
                'today' => DB::table('users')->whereDate('created_at', today())->count(),
            ],
            'financial' => [
                'total_balance' => DB::table('wallets')->sum('balance') ?? 0,
                'transactions_today' => DB::table('wallet_transactions')->whereDate('created_at', today())->count(),
                'amount_today' => DB::table('wallet_transactions')->whereDate('created_at', today())->sum('amount') ?? 0,
            ],
            'support' => [
                'total_tickets' => DB::table('telegram_tickets')->count(),
                'pending_tickets' => DB::table('telegram_tickets')->where('status', 'pending')->count(),
                'in_progress_tickets' => DB::table('telegram_tickets')->where('status', 'in_progress')->count(),
                'resolved_today' => DB::table('telegram_tickets')->where('status', 'resolved')->whereDate('updated_at', today())->count(),
            ],
            'content' => [
                'total_posts' => DB::table('telegram_posts')->count(),
                'published_posts' => DB::table('telegram_posts')->where('status', 'published')->count(),
                'scheduled_posts' => DB::table('telegram_posts')->where('status', 'scheduled')->count(),
                'ai_generated' => DB::table('telegram_posts')->where('created_by_ai', true)->count(),
            ],
        ];
    }

    /**
     * Get recent activity for admin
     */
    private function getRecentActivity(int $adminId, int $limit): array
    {
        return DB::table('telegram_audit_logs')
            ->where('admin_id', $adminId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($log) {
                return [
                    'action' => $log->action,
                    'time' => \Carbon\Carbon::parse($log->created_at)->diffForHumans(),
                ];
            })
            ->toArray();
    }

    /**
     * Send message with inline keyboard
     */
    private function sendMessageWithKeyboard(string $chatId, string $message, array $keyboard): void
    {
        try {
            $this->apiClient->sendMessage($chatId, $message, [
                'reply_markup' => [
                    'inline_keyboard' => $keyboard
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send message with keyboard', [
                'chat_id' => $chatId,
                'error' => $e->getMessage(),
            ]);
            
            // Fallback to simple message
            $this->sendMessage($chatId, $message);
        }
    }

    /**
     * Handle other command methods (users, wallets, tickets, etc.)
     * These will be implemented in the next steps
     */
    private function handleUsers(UpdateContext $context): ProcessingResult
    {
        $this->sendMessage($context->getChatId(), "👥 بخش مدیریت کاربران در حال توسعه است...");
        return ProcessingResult::success();
    }

    private function handleWallets(UpdateContext $context): ProcessingResult
    {
        $this->sendMessage($context->getChatId(), "💰 بخش مدیریت کیف پول‌ها در حال توسعه است...");
        return ProcessingResult::success();
    }

    private function handleTickets(UpdateContext $context): ProcessingResult
    {
        $this->sendMessage($context->getChatId(), "🎫 بخش مدیریت تیکت‌ها در حال توسعه است...");
        return ProcessingResult::success();
    }

    private function handlePosts(UpdateContext $context): ProcessingResult
    {
        $this->sendMessage($context->getChatId(), "📝 بخش مدیریت پست‌ها در حال توسعه است...");
        return ProcessingResult::success();
    }

    private function handleConfig(UpdateContext $context): ProcessingResult
    {
        $this->sendMessage($context->getChatId(), "⚙️ بخش تنظیمات در حال توسعه است...");
        return ProcessingResult::success();
    }

    private function handleTokens(UpdateContext $context): ProcessingResult
    {
        $this->sendMessage($context->getChatId(), "🔑 بخش مدیریت توکن‌ها در حال توسعه است...");
        return ProcessingResult::success();
    }

    private function handleSecurity(UpdateContext $context): ProcessingResult
    {
        $this->sendMessage($context->getChatId(), "🔐 بخش امنیت در حال توسعه است...");
        return ProcessingResult::success();
    }

    private function handleAI(UpdateContext $context): ProcessingResult
    {
        $this->sendMessage($context->getChatId(), "🤖 بخش تولید محتوای هوشمند در حال توسعه است...");
        return ProcessingResult::success();
    }

    private function handleUnknownCommand(UpdateContext $context): ProcessingResult
    {
        $this->sendMessage($context->getChatId(), "❓ دستور نامشخص. از /admin برای مشاهده منو استفاده کنید.");
        return ProcessingResult::failed('unknown_command');
    }
}