<?php

namespace App\Services;

use App\Models\GatewayTransaction;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Verta;

class TelegramProxyService
{
    protected $proxyUrl;
    protected $apiKey;
    protected $channelId;
    protected $useProxy;

    public function __construct()
    {
        // Use proxy server configuration
        $this->useProxy = config('services.telegram.use_proxy_server', true);
        $this->proxyUrl = config('services.telegram.proxy_server_url', 'https://your-proxy-server.com/telegram-proxy');
        $this->apiKey = config('services.telegram.proxy_api_key', 'your-secret-api-key-here');
        $this->channelId = config('services.telegram.channel_id', '-1003097450288');
    }

    /**
     * Send a new order notification to Telegram channel via proxy
     */
    public function sendNewOrderNotification(GatewayTransaction $transaction)
    {
        try {
            $message = $this->formatOrderMessage($transaction);
            $keyboard = $this->createOrderKeyboard($transaction);

            $params = [
                'action' => 'sendMessage',
                'api_key' => $this->apiKey,
                'chat_id' => $this->channelId,
                'text' => $message,
                'parse_mode' => 'HTML',
                'reply_markup' => json_encode($keyboard)
            ];

            $response = $this->sendToProxy($params);

            if ($response && isset($response['ok']) && $response['ok']) {
                Log::info('Telegram notification sent via proxy successfully', [
                    'transaction_id' => $transaction->id,
                    'message_id' => $response['result']['message_id'] ?? null
                ]);
                return true;
            }

            Log::error('Telegram proxy API returned error', [
                'transaction_id' => $transaction->id,
                'response' => $response
            ]);
            return false;
            
        } catch (\Exception $e) {
            Log::error('Failed to send Telegram notification via proxy', [
                'error' => $e->getMessage(),
                'transaction_id' => $transaction->id
            ]);
            return false;
        }
    }

    /**
     * Send request to proxy server
     */
    protected function sendToProxy($params)
    {
        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'X-API-Key' => $this->apiKey
                ])
                ->asForm()
                ->post($this->proxyUrl, $params);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Proxy server returned error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('Proxy request failed', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Format order message for Telegram
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

        if ($transaction->service) {
            $message .= "🛍️ سرویس: {$transaction->service->name}\n";
        }

        return $message;
    }

    /**
     * Create inline keyboard for order
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
                ],
                [
                    [
                        'text' => '✅ تایید',
                        'callback_data' => "approve_transaction_{$transaction->id}"
                    ],
                    [
                        'text' => '❌ رد',
                        'callback_data' => "reject_transaction_{$transaction->id}"
                    ]
                ]
            ]
        ];
    }

    /**
     * Send general notification
     */
    public function sendNotification($text, $parseMode = 'HTML')
    {
        $params = [
            'action' => 'sendMessage',
            'api_key' => $this->apiKey,
            'chat_id' => $this->channelId,
            'text' => $text,
            'parse_mode' => $parseMode
        ];

        return $this->sendToProxy($params);
    }

    /**
     * Send photo
     */
    public function sendPhoto($photoUrl, $caption = '')
    {
        $params = [
            'action' => 'sendPhoto',
            'api_key' => $this->apiKey,
            'chat_id' => $this->channelId,
            'photo' => $photoUrl,
            'caption' => $caption,
            'parse_mode' => 'HTML'
        ];

        return $this->sendToProxy($params);
    }

    /**
     * Get updates (for polling)
     */
    public function getUpdates($offset = 0)
    {
        $params = [
            'action' => 'getUpdates',
            'api_key' => $this->apiKey,
            'offset' => $offset,
            'limit' => 100,
            'timeout' => 30
        ];

        return $this->sendToProxy($params);
    }

    /**
     * Set webhook
     */
    public function setWebhook($webhookUrl = null)
    {
        $params = [
            'action' => 'setWebhook',
            'api_key' => $this->apiKey,
            'webhook_url' => $webhookUrl ?? route('telegram.webhook')
        ];

        return $this->sendToProxy($params);
    }

    /**
     * Get webhook info
     */
    public function getWebhookInfo()
    {
        $params = [
            'action' => 'getWebhookInfo',
            'api_key' => $this->apiKey
        ];

        return $this->sendToProxy($params);
    }

    /**
     * Delete webhook
     */
    public function deleteWebhook()
    {
        $params = [
            'action' => 'deleteWebhook',
            'api_key' => $this->apiKey
        ];

        return $this->sendToProxy($params);
    }

    /**
     * Handle webhook update
     */
    public function handleWebhookUpdate($update)
    {
        try {
            // Process message
            if (isset($update['message'])) {
                $this->handleMessage($update['message']);
            }

            // Process callback query
            if (isset($update['callback_query'])) {
                $this->handleCallbackQuery($update['callback_query']);
            }

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to handle webhook update', [
                'error' => $e->getMessage(),
                'update' => $update
            ]);
            return false;
        }
    }

    /**
     * Handle incoming message
     */
    protected function handleMessage($message)
    {
        // Implement message handling logic
        Log::info('Telegram message received', ['message' => $message]);
    }

    /**
     * Handle callback query (button clicks)
     */
    protected function handleCallbackQuery($callbackQuery)
    {
        $data = $callbackQuery['data'] ?? '';
        
        // Handle transaction approval/rejection
        if (strpos($data, 'approve_transaction_') === 0) {
            $transactionId = str_replace('approve_transaction_', '', $data);
            // Implement approval logic
            Log::info('Transaction approval requested', ['transaction_id' => $transactionId]);
        } elseif (strpos($data, 'reject_transaction_') === 0) {
            $transactionId = str_replace('reject_transaction_', '', $data);
            // Implement rejection logic
            Log::info('Transaction rejection requested', ['transaction_id' => $transactionId]);
        }
    }
}