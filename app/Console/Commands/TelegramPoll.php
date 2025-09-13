<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\TelegramBotController;

class TelegramPoll extends Command
{
    protected $signature = 'telegram:poll {--timeout=30 : Long polling timeout in seconds}';
    protected $description = 'Poll Telegram for updates using long polling';
    
    private $botToken;
    private $lastUpdateId = 0;
    private $controller;
    
    public function __construct()
    {
        parent::__construct();
        $this->botToken = env('TELEGRAM_BOT_TOKEN');
        $this->controller = new TelegramBotController();
    }
    
    public function handle()
    {
        if (!$this->botToken) {
            $this->error('TELEGRAM_BOT_TOKEN not configured in .env');
            return 1;
        }
        
        $this->info('Starting Telegram bot polling...');
        $this->info('Press Ctrl+C to stop');
        
        // Get last processed update ID from cache
        $this->lastUpdateId = Cache::get('telegram_last_update_id', 0);
        
        while (true) {
            try {
                $this->pollUpdates();
            } catch (\Exception $e) {
                $this->error('Polling error: ' . $e->getMessage());
                Log::error('Telegram polling error', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                // Wait before retrying
                sleep(5);
            }
        }
    }
    
    private function pollUpdates()
    {
        $timeout = $this->option('timeout');
        
        // Make request to Telegram API
        $url = "https://api.telegram.org/bot{$this->botToken}/getUpdates";
        $params = [
            'offset' => $this->lastUpdateId + 1,
            'timeout' => $timeout,
            'allowed_updates' => ['message', 'callback_query']
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout + 10);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            throw new \Exception("HTTP error: $httpCode");
        }
        
        $data = json_decode($response, true);
        
        if (!$data || !$data['ok']) {
            throw new \Exception('Invalid response from Telegram API');
        }
        
        $updates = $data['result'] ?? [];
        
        foreach ($updates as $update) {
            $this->processUpdate($update);
            
            // Update last processed ID
            if (isset($update['update_id'])) {
                $this->lastUpdateId = $update['update_id'];
                Cache::put('telegram_last_update_id', $this->lastUpdateId, 86400);
            }
        }
        
        if (count($updates) > 0) {
            $this->info('Processed ' . count($updates) . ' update(s)');
        }
    }
    
    private function processUpdate($update)
    {
        try {
            // Log the update
            Log::info('Telegram polling received update', [
                'update_id' => $update['update_id'] ?? null,
                'type' => isset($update['message']) ? 'message' : 'other'
            ]);
            
            // Check for message
            if (!isset($update['message']['text'])) {
                return;
            }
            
            $message = $update['message'];
            $text = $message['text'];
            $chatId = $message['chat']['id'];
            $userId = $message['from']['id'] ?? null;
            $userName = $message['from']['first_name'] ?? 'کاربر';
            
            // Check if it's a command
            if (!str_starts_with($text, '/')) {
                return;
            }
            
            // Parse command
            $command = ltrim(explode(' ', $text)[0], '/');
            $command = explode('@', $command)[0]; // Remove bot username if present
            $command = strtolower($command);
            
            $this->info("Processing command: /$command from user: $userName ($userId)");
            
            // Process commands
            switch ($command) {
                case 'start':
                    $this->sendMessage($chatId, 
                        "سلام {$userName}! 🎉\n\n" .
                        "به ربات پیشخوانک خوش آمدید!\n\n" .
                        "**دستورات کاربری:**\n" .
                        "• /help - راهنمای کامل\n" .
                        "• /ticket - ایجاد تیکت پشتیبانی\n" .
                        "• /status - وضعیت سرویس\n\n" .
                        "**دستورات مدیریت:**\n" .
                        "• /admin - ورود به پنل مدیریت\n\n" .
                        "برای شروع، یکی از دستورات بالا را انتخاب کنید."
                    );
                    break;
                    
                case 'help':
                    $this->sendMessage($chatId,
                        "📚 **راهنمای استفاده از ربات پیشخوانک**\n\n" .
                        "**دستورات اصلی:**\n" .
                        "• /start - شروع مجدد ربات\n" .
                        "• /help - نمایش این راهنما\n" .
                        "• /status - بررسی وضعیت سرویس‌ها\n" .
                        "• /ticket - ایجاد تیکت پشتیبانی جدید\n\n" .
                        "**مدیریت (مخصوص مدیران):**\n" .
                        "• /admin - ورود به پنل مدیریت\n\n" .
                        "برای اطلاعات بیشتر، با پشتیبانی تماس بگیرید."
                    );
                    break;
                    
                case 'status':
                    $this->sendMessage($chatId,
                        "📊 **وضعیت سرویس‌های پیشخوانک**\n\n" .
                        "🟢 **سرویس اصلی:** آنلاین\n" .
                        "🟢 **پایگاه داده:** فعال\n" .
                        "🟢 **ربات تلگرام:** متصل (Polling Mode)\n" .
                        "🟢 **سیستم پرداخت:** آماده\n\n" .
                        "⏰ آخرین بروزرسانی: " . now()->format('Y/m/d H:i') . "\n\n" .
                        "در صورت مشاهده مشکل، تیکت پشتیبانی ارسال کنید."
                    );
                    break;
                    
                case 'ticket':
                    $this->sendMessage($chatId,
                        "🎫 **ایجاد تیکت پشتیبانی**\n\n" .
                        "برای ایجاد تیکت پشتیبانی، موضوع و توضیحات خود را به صورت زیر ارسال کنید:\n\n" .
                        "**مثال:**\n" .
                        "موضوع: مشکل در پرداخت\n" .
                        "توضیحات: در هنگام پرداخت با خطای 500 مواجه می‌شوم\n\n" .
                        "تیم پشتیبانی در اسرع وقت پاسخگوی شما خواهد بود."
                    );
                    break;
                    
                case 'admin':
                    // Check if user is admin
                    $adminIds = env('TELEGRAM_ADMIN_CHAT_IDS', '');
                    $adminChatIds = array_filter(array_map('trim', explode(',', $adminIds)));
                    
                    if (!in_array($userId, $adminChatIds)) {
                        $this->sendMessage($chatId,
                            "🚫 **دسترسی مجاز نیست**\n\n" .
                            "شما مجاز به استفاده از پنل مدیریت نیستید.\n\n" .
                            "شناسه شما: `{$userId}`"
                        );
                    } else {
                        $this->sendMessage($chatId,
                            "🎛️ **پنل مدیریت پیشخوانک**\n\n" .
                            "خوش آمدید {$userName}!\n\n" .
                            "**دستورات موجود:**\n" .
                            "📊 /dashboard - داشبورد مدیریت\n" .
                            "📈 /stats - آمار سیستم\n" .
                            "👥 /users - مدیریت کاربران\n" .
                            "💰 /wallets - مدیریت کیف پول‌ها\n" .
                            "🎫 /tickets - مدیریت تیکت‌ها\n\n" .
                            "**وضعیت سیستم:**\n" .
                            "🟢 ربات آنلاین و آماده (Polling Mode)"
                        );
                    }
                    break;
                    
                default:
                    $this->sendMessage($chatId,
                        "❌ دستور ناشناخته: /{$command}\n\n" .
                        "برای مشاهده دستورات موجود، /help را ارسال کنید."
                    );
            }
            
        } catch (\Exception $e) {
            Log::error('Error processing update in polling', [
                'update_id' => $update['update_id'] ?? null,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    private function sendMessage($chatId, $text)
    {
        $url = "https://api.telegram.org/bot{$this->botToken}/sendMessage";
        
        $data = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'Markdown'
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200) {
            $this->info("✅ Message sent to chat $chatId");
            Log::info('Message sent via polling', ['chat_id' => $chatId]);
        } else {
            $this->error("Failed to send message: HTTP $httpCode");
            Log::error('Failed to send message in polling', [
                'chat_id' => $chatId,
                'http_code' => $httpCode,
                'response' => $response
            ]);
        }
        
        return $httpCode === 200;
    }
}