<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\User;

class TelegramBotCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:bot {action : The action to perform (setup|info|test|link)}
                            {--user= : User ID to link with Telegram}
                            {--chat-id= : Telegram chat ID to link}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage Telegram ticket bot';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');
        
        switch ($action) {
            case 'setup':
                return $this->setupBot();
            case 'info':
                return $this->getBotInfo();
            case 'test':
                return $this->testBot();
            case 'link':
                return $this->linkUser();
            default:
                $this->error("Unknown action: {$action}");
                return 1;
        }
    }
    
    /**
     * Set up the bot webhook and configuration
     */
    protected function setupBot()
    {
        $this->info('Setting up Telegram bot...');
        
        // Get bot info first
        $botInfo = $this->callBotApi('getMe');
        
        if (!$botInfo['ok']) {
            $this->error('Failed to get bot info. Check your bot token.');
            return 1;
        }
        
        $bot = $botInfo['result'];
        $this->info("Bot: @{$bot['username']} ({$bot['first_name']})");
        
        // Set webhook
        $webhookUrl = config('app.url') . '/api/telegram/webhook';
        $this->info("Setting webhook to: {$webhookUrl}");
        
        // Set webhook
        $setWebhookResponse = $this->callBotApi('setWebhook', ['url' => $webhookUrl]);
        if ($setWebhookResponse['ok']) {
            $this->info("de651f36");
            
            // Get webhook info
            $webhookInfoResponse = $this->callBotApi('getWebhookInfo');
            if ($webhookInfoResponse['ok']) {
                $info = $webhookInfoResponse['result'];
                $this->info("\nWebhook Information:");
                $this->table(
                    ['Property', 'Value'],
                    [
                        ['URL', $info['url'] ?? 'Not set'],
                        ['Pending Updates', $info['pending_update_count'] ?? 0],
                        ['Last Error', $info['last_error_message'] ?? 'None'],
                        ['Last Error Date', isset($info['last_error_date']) ? date('Y-m-d H:i:s', $info['last_error_date']) : 'N/A'],
                        ['Max Connections', $info['max_connections'] ?? 40],
                        ['Allowed Updates', implode(', ', $info['allowed_updates'] ?? [])],
                    ]
                );
            }
        } else {
            $this->error('Failed to set webhook: ' . ($setWebhookResponse['description'] ?? 'Unknown error'));
        }
        
        return 0;
    }
    
    /**
     * Test the bot by sending a message
     */
    protected function testBot()
    {
        $this->info('Testing bot...');
        
        $adminIds = explode(',', config('services.telegram.admin_chat_ids', ''));
        
        if (empty($adminIds[0])) {
            $this->error('No admin chat IDs configured. Please set TELEGRAM_ADMIN_CHAT_IDS in .env');
            return 1;
        }
        
        foreach ($adminIds as $chatId) {
            if (empty($chatId)) continue;
            
            $this->info("Sending test message to chat ID: {$chatId}");
            
            $message = "7f00eda6";
            $message .= "PERSIAN_TEXT_8ee97cda";
            $message .= "f98fd3c5" . \Verta::now()->format('Y/m/d H:i:s') . "\n";
            $message .= "05756b0b" . config('app.url') . "\n\n";
            $message .= "96aa469d";
            
            $result = $this->callBotApi('sendMessage', [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown'
            ]);
            
            if ($result['ok']) {
                $this->info("✅ Message sent successfully to {$chatId}");
            } else {
                $this->error("❌ Failed to send message to {$chatId}: " . ($result['description'] ?? 'Unknown error'));
            }
        }
        
        return 0;
    }
    
    /**
     * Link a user account with Telegram
     */
    protected function linkUser()
    {
        $userId = $this->option('user');
        $chatId = $this->option('chat-id');
        
        if (!$userId || !$chatId) {
            $this->error('Both --user and --chat-id options are required');
            return 1;
        }
        
        $user = User::find($userId);
        
        if (!$user) {
            $this->error("User with ID {$userId} not found");
            return 1;
        }
        
        $user->telegram_chat_id = $chatId;
        $user->telegram_notifications_enabled = true;
        $user->save();
        
        $this->info("✅ User {$user->name} linked with Telegram chat ID: {$chatId}");
        
        // Send confirmation message
        $message = "3df375cb";
        $message .= "PERSIAN_TEXT_ff0a3dd0";
        $message .= "4928b7eb";
        $message .= "PERSIAN_TEXT_a3768d94";
        
        $result = $this->callBotApi('sendMessage', [
            'chat_id' => $chatId,
            'text' => $message
        ]);
        
        if (!$result['ok']) {
            $this->warn('Failed to send confirmation message to user');
        }
        
        return 0;
    }
    
    /**
     * Call Telegram Bot API
     */
    protected function callBotApi($method, $params = [])
    {
        $botToken = config('services.telegram.bot_token');
        
        // Use external proxy if configured
        if (config('services.telegram.external_proxy.enabled')) {
            $proxyUrl = config('services.telegram.external_proxy.url');
            $apiKey = config('services.telegram.external_proxy.api_key');
            
            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'X-API-KEY' => $apiKey
                ])
                ->post($proxyUrl, [
                    'bot_token' => $botToken,
                    'method' => $method,
                    'params' => $params
                ]);
        } else {
            $url = "https://api.telegram.org/bot{$botToken}/{$method}";
            
            if (empty($params)) {
                $response = Http::get($url);
            } else {
                $response = Http::post($url, $params);
            }
        }
        
        return $response->json() ?? ['ok' => false, 'description' => 'Failed to get response'];
    }
}