<?php

namespace App\Services;

use App\Models\GatewayTransaction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Verta;

class TelegramHttpProxyService
{
    protected $proxyUrl;
    protected $apiKey;
    protected $botToken;
    protected $channelId;

    public function __construct()
    {
        // HTTP API Proxy configuration
        $this->proxyUrl = config('services.telegram.http_proxy_url', 'http://46.62.173.170/telegram-api.php');
        $this->apiKey = config('services.telegram.http_proxy_key', 'your-secure-api-key');
        $this->botToken = config('services.telegram.bot_token', '7696804096:AAFUuZaXsoLDYIb8KJ7w-eLlhq4D422C1oc');
        $this->channelId = config('services.telegram.channel_id', '-1003097450288');
    }

    /**
     * Send request through HTTP proxy
     */
    protected function sendRequest($method, $params = [])
    {
        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'X-API-KEY' => $this->apiKey
                ])
                ->asForm()
                ->post($this->proxyUrl, [
                    'api_key' => $this->apiKey,
                    'bot_token' => $this->botToken,
                    'method' => $method,
                    'params' => json_encode($params)
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Telegram HTTP proxy error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('Telegram HTTP proxy exception', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Send a new order notification
     */
    public function sendNewOrderNotification(GatewayTransaction $transaction)
    {
        try {
            $message = $this->formatOrderMessage($transaction);
            $keyboard = $this->createOrderKeyboard($transaction);

            $result = $this->sendRequest('sendMessage', [
                'chat_id' => $this->channelId,
                'text' => $message,
                'parse_mode' => 'HTML',
                'reply_markup' => json_encode($keyboard)
            ]);

            if ($result && isset($result['ok']) && $result['ok']) {
                Log::info('Telegram notification sent via HTTP proxy', [
                    'transaction_id' => $transaction->id,
                    'message_id' => $result['result']['message_id'] ?? null
                ]);
                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error('Failed to send Telegram notification', [
                'error' => $e->getMessage(),
                'transaction_id' => $transaction->id
            ]);
            return false;
        }
    }

    /**
     * Send a simple text message
     */
    public function sendMessage($text, $parseMode = 'HTML')
    {
        return $this->sendRequest('sendMessage', [
            'chat_id' => $this->channelId,
            'text' => $text,
            'parse_mode' => $parseMode
        ]);
    }

    /**
     * Format order message
     */
    protected function formatOrderMessage(GatewayTransaction $transaction)
    {
        $verta = new Verta($transaction->created_at);
        $userInfo = $transaction->user 
            ? "@{$transaction->user->username} ({$transaction->user->name})" 
            : "4a826c06";
        
        $status = $transaction->status === 'completed' ? '✅ تکمیل شده' : '⏳ در حال بررسی';
        $amount = number_format($transaction->amount) . ' ریال';

        $message = "🔔 تراکنش جدید\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━━\n";
        $message .= "💳 مبلغ: {$amount}\n";
        $message .= "👤 کاربر: {$userInfo}\n";
        $message .= "📅 تاریخ: {$verta->format('Y/m/d - H:i')}\n";
        $message .= "🔸 شماره: {$transaction->id}\n";
        $message .= "⚡ وضعیت: {$status}\n";
        
        if ($transaction->description) {
            $message .= "📝 توضیحات: {$transaction->description}\n";
        }

        return $message;
    }

    /**
     * Create inline keyboard
     */
    protected function createOrderKeyboard(GatewayTransaction $transaction)
    {
        return [
            'inline_keyboard' => [
                [
                    [
                        'text' => '👀 مشاهده جزئیات',
                        'url' => route('admin.transactions.show', $transaction->id)
                    ]
                ]
            ]
        ];
    }
}