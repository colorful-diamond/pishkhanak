<?php

namespace App\Http\Controllers;

use App\Services\TelegramBotService;
use App\Services\TelegramTicketBot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class TelegramBotController extends Controller
{
    protected $bot;
    
    public function __construct()
    {
        $this->bot = new TelegramBotService();
    }
    
    /**
     * Handle incoming webhook from Telegram
     */
    public function webhook(Request $request)
    {
        try {
            $update = $request->all();
            
            // Log incoming update for debugging
            Log::info('Telegram webhook received', ['update' => $update]);
            
            // Temporarily log chat ID for setup
            if (isset($update['message']['chat']['id'])) {
                $chatId = $update['message']['chat']['id'];
                $userName = $update['message']['from']['first_name'] ?? 'Unknown';
                $userId = $update['message']['from']['id'] ?? 'Unknown';
                
                Log::info("TELEGRAM CHAT ID CAPTURED: {$chatId} (User: {$userName}, ID: {$userId})");
                
                // Save to file for easy retrieval
                file_put_contents(storage_path('app/telegram_chat_ids.txt'), 
                    "Chat ID: {$chatId} | User: {$userName} | User ID: {$userId} | Time: " . date('Y-m-d H:i:s') . "\n", 
                    FILE_APPEND);
            }
            
            // Process the update using our comprehensive service
            $this->bot->processUpdate($update);
            
            return response()->json(['ok' => true]);
            
        } catch (\Exception $e) {
            Log::error('Telegram webhook error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Set webhook URL for the bot
     */
    public function setWebhook()
    {
        $webhookUrl = route('telegram.webhook');
        $result = $this->bot->setWebhook($webhookUrl);
        
        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'وب‌هوک با موفقیت تنظیم شد',
                'webhook_url' => $webhookUrl,
                'result' => $result
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'خطا در تنظیم وب‌هوک'
        ], 500);
    }
    
    /**
     * Get webhook info
     */
    public function getWebhookInfo()
    {
        $result = $this->bot->getWebhookInfo();
        
        if ($result) {
            return response()->json([
                'success' => true,
                'result' => $result
            ]);
        }
        
        return response()->json([
            'success' => false,
            'error' => 'خطا در دریافت اطلاعات وب‌هوک'
        ], 500);
    }
    
    /**
     * Delete webhook
     */
    public function deleteWebhook()
    {
        $result = $this->bot->deleteWebhook();
        
        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'وب‌هوک با موفقیت حذف شد',
                'result' => $result
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'خطا در حذف وب‌هوک'
        ], 500);
    }
    
    /**
     * Get bot info
     */
    public function getBotInfo()
    {
        $result = $this->bot->getMe();
        
        if ($result) {
            return response()->json([
                'success' => true,
                'result' => $result
            ]);
        }
        
        return response()->json([
            'success' => false,
            'error' => 'خطا در دریافت اطلاعات ربات'
        ], 500);
    }
    
    /**
     * Test bot by sending a message to admins
     */
    public function testBot()
    {
        // Test connection first
        if (!$this->bot->testConnection()) {
            return response()->json([
                'success' => false,
                'error' => 'ارتباط با ربات تلگرام برقرار نشد'
            ], 500);
        }

        $adminChatIds = explode(',', env('TELEGRAM_ADMIN_CHAT_IDS', ''));
        
        if (empty($adminChatIds[0])) {
            return response()->json([
                'success' => false,
                'error' => 'شناسه چت مدیران مشخص نشده. لطفاً TELEGRAM_ADMIN_CHAT_IDS را در .env تنظیم کنید'
            ], 400);
        }
        
        $results = [];
        
        foreach ($adminChatIds as $chatId) {
            $chatId = trim($chatId);
            if (!$chatId) continue;
            
            try {
                $message = "🤖 تست ربات پیشخوانک\n";
                $message .= "✅ ارتباط برقرار است!\n";
                $message .= "📅 زمان: " . \Verta::now()->format('Y/m/d H:i:s') . "\n";
                $message .= "🌐 سرور: " . request()->getHost() . "\n\n";
                $message .= "🚀 ربات آماده دریافت پیام است!";
                
                $result = $this->bot->sendMessage($chatId, $message);
                
                $results[] = [
                    'chat_id' => $chatId,
                    'success' => $result !== null,
                    'result' => $result
                ];
                
            } catch (\Exception $e) {
                $results[] = [
                    'chat_id' => $chatId,
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'پیام تست برای مدیران ارسال شد',
            'results' => $results
        ]);
    }
}