<?php

namespace App\Services;

use App\Models\GatewayTransaction;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Verta;

/**
 * Comprehensive Telegram Bot Service with Proxy Support
 * Handles all Telegram communications with proper Persian text and proxy configuration
 */
class TelegramBotService
{
    protected $botToken;
    protected $channelId;
    protected $adminChatIds;
    protected $proxyConfig;
    protected $baseUrl;

    // Bot states for conversation management
    const STATE_IDLE = 'idle';
    const STATE_WAITING_REPLY = 'waiting_reply';
    const STATE_WAITING_SEARCH = 'waiting_search';
    const STATE_WAITING_CLOSE_REASON = 'waiting_close_reason';
    const STATE_WAITING_STATUS = 'waiting_status';
    const STATE_WAITING_PRIORITY = 'waiting_priority';

    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token');
        $this->channelId = config('services.telegram.channel_id');
        $this->baseUrl = "https://api.telegram.org/bot{$this->botToken}";
        
        // Load admin chat IDs from config
        $adminIds = config('services.telegram.admin_chat_ids', env('TELEGRAM_ADMIN_CHAT_IDS', ''));
        $this->adminChatIds = array_filter(array_map('trim', explode(',', $adminIds)));
        
        // Configure proxy settings
        $this->proxyConfig = [
            'enabled' => config('services.telegram.proxy.enabled', env('TELEGRAM_PROXY_ENABLED', true)),
            'type' => config('services.telegram.proxy.type', env('TELEGRAM_PROXY_TYPE', 'socks5')),
            'host' => config('services.telegram.proxy.host', env('TELEGRAM_PROXY_HOST', '127.0.0.1')),
            'port' => config('services.telegram.proxy.port', env('TELEGRAM_PROXY_PORT', 1091)),
        ];
    }

    /**
     * Send HTTP request with proxy support
     */
    protected function sendRequest($method, $params = [])
    {
        try {
            $url = $this->baseUrl . '/' . $method;

            // Use cURL directly for better proxy control
            $ch = curl_init();
            
            $curlOptions = [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($params),
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Accept: application/json'
                ],
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_USERAGENT => 'TelegramBot/1.0'
            ];

            // Configure proxy if enabled
            if ($this->proxyConfig['enabled']) {
                $curlOptions[CURLOPT_PROXY] = $this->proxyConfig['host'] . ':' . $this->proxyConfig['port'];
                $curlOptions[CURLOPT_PROXYTYPE] = $this->getCurlProxyType();
                
                Log::info('Telegram request using proxy', [
                    'proxy' => $this->proxyConfig['host'] . ':' . $this->proxyConfig['port'],
                    'type' => $this->proxyConfig['type'],
                    'method' => $method
                ]);
            }

            curl_setopt_array($ch, $curlOptions);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                Log::error('Telegram cURL error', [
                    'error' => $error,
                    'method' => $method
                ]);
                return null;
            }

            if ($httpCode !== 200) {
                Log::error('Telegram HTTP error', [
                    'http_code' => $httpCode,
                    'response' => substr($response, 0, 500),
                    'method' => $method
                ]);
                return null;
            }

            $result = json_decode($response, true);
            if (isset($result['ok']) && $result['ok']) {
                return $result['result'] ?? $result;
            }

            Log::error('Telegram API error', [
                'method' => $method,
                'response' => $result,
                'params' => $params
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('Telegram request failed', [
                'error' => $e->getMessage(),
                'method' => $method,
                'params' => $params
            ]);
            return null;
        }
    }

    /**
     * Get proxy URL based on configuration
     */
    protected function getProxyUrl()
    {
        $type = $this->proxyConfig['type'];
        $host = $this->proxyConfig['host'];
        $port = $this->proxyConfig['port'];
        
        return "{$type}://{$host}:{$port}";
    }

    /**
     * Get CURL proxy type constant
     */
    protected function getCurlProxyType()
    {
        switch ($this->proxyConfig['type']) {
            case 'socks5':
                return CURLPROXY_SOCKS5;
            case 'socks4':
                return CURLPROXY_SOCKS4;
            case 'http':
                return CURLPROXY_HTTP;
            default:
                return CURLPROXY_SOCKS5;
        }
    }

    /**
     * Send new order notification to channel
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

            if ($result) {
                Log::info('Order notification sent successfully', [
                    'transaction_id' => $transaction->id,
                    'message_id' => $result['message_id'] ?? null
                ]);
                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error('Failed to send order notification', [
                'error' => $e->getMessage(),
                'transaction_id' => $transaction->id
            ]);
            return false;
        }
    }

    /**
     * Format order message in Persian
     */
    protected function formatOrderMessage(GatewayTransaction $transaction)
    {
        $verta = new Verta($transaction->created_at);
        $userInfo = $transaction->user 
            ? "@{$transaction->user->username} ({$transaction->user->name})" 
            : "مهمان";
        
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

        if ($transaction->paymentGateway) {
            $message .= "🏦 درگاه: {$transaction->paymentGateway->name}\n";
        }

        return $message;
    }

    /**
     * Create inline keyboard for order management
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
     * Send ticket notification
     */
    public function sendTicketNotification(Ticket $ticket, $action = 'created')
    {
        try {
            $message = $this->formatTicketMessage($ticket, $action);
            $keyboard = $this->createTicketKeyboard($ticket);

            $result = $this->sendRequest('sendMessage', [
                'chat_id' => $this->channelId,
                'text' => $message,
                'parse_mode' => 'HTML',
                'reply_markup' => json_encode($keyboard)
            ]);

            if ($result) {
                Log::info('Ticket notification sent', [
                    'ticket_id' => $ticket->id,
                    'action' => $action
                ]);
                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error('Failed to send ticket notification', [
                'error' => $e->getMessage(),
                'ticket_id' => $ticket->id,
                'action' => $action
            ]);
            return false;
        }
    }

    /**
     * Format ticket message in Persian
     */
    protected function formatTicketMessage(Ticket $ticket, $action)
    {
        $verta = new Verta($ticket->created_at);
        $actionText = $this->getActionText($action);
        
        $message = "🎫 {$actionText}\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━━\n";
        $message .= "🆔 شماره: #{$ticket->id}\n";
        $message .= "📋 موضوع: {$ticket->subject}\n";
        $message .= "👤 کاربر: " . ($ticket->user->name ?? 'مهمان') . "\n";
        $message .= "📧 ایمیل: " . ($ticket->user->email ?? 'ندارد') . "\n";
        $message .= "📅 تاریخ: {$verta->format('Y/m/d - H:i')}\n";
        $message .= "🏷️ وضعیت: " . $this->getStatusText($ticket->status) . "\n";
        $message .= "⚡ اولویت: " . $this->getPriorityText($ticket->priority) . "\n";
        
        if ($ticket->category) {
            $message .= "📁 دسته‌بندی: " . $ticket->category->name . "\n";
        }
        
        if ($ticket->content && strlen($ticket->content) > 0) {
            $preview = mb_substr(strip_tags($ticket->content), 0, 100);
            $message .= "💬 پیش‌نمایش: {$preview}" . (strlen($ticket->content) > 100 ? '...' : '') . "\n";
        }

        return $message;
    }

    /**
     * Create inline keyboard for ticket management
     */
    protected function createTicketKeyboard(Ticket $ticket)
    {
        return [
            'inline_keyboard' => [
                [
                    [
                        'text' => '👀 مشاهده تیکت',
                        'url' => route('admin.tickets.show', $ticket->id)
                    ]
                ],
                [
                    [
                        'text' => '💬 پاسخ سریع',
                        'callback_data' => "reply_ticket_{$ticket->id}"
                    ],
                    [
                        'text' => '🔒 بستن تیکت',
                        'callback_data' => "close_ticket_{$ticket->id}"
                    ]
                ]
            ]
        ];
    }

    /**
     * Send simple message
     */
    public function sendMessage($chatId, $text, $parseMode = 'HTML', $keyboard = null)
    {
        $params = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => $parseMode
        ];

        if ($keyboard) {
            $params['reply_markup'] = json_encode($keyboard);
        }

        return $this->sendRequest('sendMessage', $params);
    }

    /**
     * Send photo
     */
    public function sendPhoto($chatId, $photo, $caption = '', $keyboard = null)
    {
        $params = [
            'chat_id' => $chatId,
            'photo' => $photo,
            'caption' => $caption,
            'parse_mode' => 'HTML'
        ];

        if ($keyboard) {
            $params['reply_markup'] = json_encode($keyboard);
        }

        return $this->sendRequest('sendPhoto', $params);
    }

    /**
     * Set webhook
     */
    public function setWebhook($url = null)
    {
        $webhookUrl = $url ?? route('telegram.webhook');
        
        return $this->sendRequest('setWebhook', [
            'url' => $webhookUrl
        ]);
    }

    /**
     * Delete webhook
     */
    public function deleteWebhook()
    {
        return $this->sendRequest('deleteWebhook');
    }

    /**
     * Get webhook info
     */
    public function getWebhookInfo()
    {
        return $this->sendRequest('getWebhookInfo');
    }

    /**
     * Get bot info
     */
    public function getMe()
    {
        return $this->sendRequest('getMe');
    }

    /**
     * Process incoming webhook update
     */
    public function processUpdate($update)
    {
        try {
            // Handle regular messages
            if (isset($update['message'])) {
                $this->handleMessage($update['message']);
            }

            // Handle callback queries (button presses)
            if (isset($update['callback_query'])) {
                $this->handleCallbackQuery($update['callback_query']);
            }

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to process webhook update', [
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
        $chatId = $message['chat']['id'];
        $text = $message['text'] ?? '';
        $userId = $message['from']['id'] ?? null;

        Log::info('Received Telegram message', [
            'chat_id' => $chatId,
            'user_id' => $userId,
            'text' => $text
        ]);

        // Handle commands
        if (strpos($text, '/') === 0) {
            $this->handleCommand($chatId, $text, $userId);
            return;
        }

        // Handle regular messages based on user state
        $this->handleRegularMessage($chatId, $text, $userId);
    }

    /**
     * Handle bot commands
     */
    protected function handleCommand($chatId, $command, $userId)
    {
        $command = strtolower(trim($command));

        switch ($command) {
            case '/start':
                $this->sendWelcomeMessage($chatId);
                break;

            case '/help':
                $this->sendHelpMessage($chatId);
                break;

            case '/status':
                if ($this->isAdmin($chatId)) {
                    $this->sendSystemStatus($chatId);
                }
                break;

            case '/stats':
                if ($this->isAdmin($chatId)) {
                    $this->sendStatistics($chatId);
                }
                break;

            default:
                $this->sendMessage($chatId, "❓ دستور شناخته شده نیست. /help برای راهنمایی استفاده کنید.");
        }
    }

    /**
     * Handle regular messages
     */
    protected function handleRegularMessage($chatId, $text, $userId)
    {
        // Get user state from cache
        $state = Cache::get("telegram_user_state_{$userId}", self::STATE_IDLE);

        switch ($state) {
            case self::STATE_WAITING_REPLY:
                $this->handleTicketReply($chatId, $text, $userId);
                break;

            case self::STATE_WAITING_SEARCH:
                $this->handleTicketSearch($chatId, $text, $userId);
                break;

            default:
                $this->sendMessage($chatId, "سلام! 👋\nچطور می‌تونم کمکتون کنم؟\n\n/help برای دیدن دستورات موجود");
        }
    }

    /**
     * Handle callback queries (button presses)
     */
    protected function handleCallbackQuery($callbackQuery)
    {
        $data = $callbackQuery['data'] ?? '';
        $chatId = $callbackQuery['message']['chat']['id'] ?? null;
        $messageId = $callbackQuery['message']['message_id'] ?? null;
        $userId = $callbackQuery['from']['id'] ?? null;

        // Answer callback query to remove loading state
        $this->sendRequest('answerCallbackQuery', [
            'callback_query_id' => $callbackQuery['id']
        ]);

        // Handle transaction actions
        if (strpos($data, 'approve_transaction_') === 0) {
            $transactionId = str_replace('approve_transaction_', '', $data);
            $this->handleTransactionApproval($chatId, $transactionId, $messageId);
        } elseif (strpos($data, 'reject_transaction_') === 0) {
            $transactionId = str_replace('reject_transaction_', '', $data);
            $this->handleTransactionRejection($chatId, $transactionId, $messageId);
        }

        // Handle ticket actions
        elseif (strpos($data, 'reply_ticket_') === 0) {
            $ticketId = str_replace('reply_ticket_', '', $data);
            $this->handleQuickReply($chatId, $ticketId, $userId);
        } elseif (strpos($data, 'close_ticket_') === 0) {
            $ticketId = str_replace('close_ticket_', '', $data);
            $this->handleTicketClose($chatId, $ticketId, $messageId);
        }
    }

    /**
     * Send welcome message
     */
    protected function sendWelcomeMessage($chatId)
    {
        $message = "🎉 به ربات پیشخوانک خوش آمدید!\n\n";
        $message .= "این ربات برای مدیریت سفارشات و تیکت‌ها طراحی شده است.\n\n";
        $message .= "دستورات موجود:\n";
        $message .= "• /help - راهنمای کامل\n";
        $message .= "• /status - وضعیت سیستم\n";
        $message .= "• /stats - آمار کلی\n";

        $this->sendMessage($chatId, $message);
    }

    /**
     * Send help message
     */
    protected function sendHelpMessage($chatId)
    {
        $message = "📚 راهنمای استفاده از ربات\n\n";
        $message .= "🔹 دستورات عمومی:\n";
        $message .= "• /start - شروع مجدد\n";
        $message .= "• /help - این راهنما\n\n";
        
        if ($this->isAdmin($chatId)) {
            $message .= "🔸 دستورات مدیریتی:\n";
            $message .= "• /status - وضعیت سیستم\n";
            $message .= "• /stats - آمار و گزارشات\n\n";
        }
        
        $message .= "💡 نکته: با دکمه‌های موجود در پیام‌ها تعامل کنید.";

        $this->sendMessage($chatId, $message);
    }

    /**
     * Check if user is admin
     */
    protected function isAdmin($chatId)
    {
        return in_array((string)$chatId, $this->adminChatIds);
    }

    /**
     * Get Persian text for action
     */
    protected function getActionText($action)
    {
        $actions = [
            'created' => 'تیکت جدید ایجاد شد',
            'updated' => 'تیکت به‌روزرسانی شد',
            'replied' => 'پاسخ جدید دریافت شد',
            'closed' => 'تیکت بسته شد',
            'opened' => 'تیکت باز شد'
        ];

        return $actions[$action] ?? 'تغییر در تیکت';
    }

    /**
     * Get Persian text for status
     */
    protected function getStatusText($status)
    {
        $statuses = [
            'open' => '🟢 باز',
            'closed' => '🔴 بسته',
            'pending' => '🟡 در انتظار',
            'resolved' => '✅ حل شده'
        ];

        return $statuses[$status] ?? $status;
    }

    /**
     * Get Persian text for priority
     */
    protected function getPriorityText($priority)
    {
        $priorities = [
            'low' => '🔵 کم',
            'normal' => '🟡 عادی',
            'high' => '🟠 زیاد',
            'urgent' => '🔴 فوری'
        ];

        return $priorities[$priority] ?? $priority;
    }

    /**
     * Test proxy connection
     */
    public function testConnection()
    {
        try {
            $result = $this->getMe();
            
            if ($result && isset($result['username'])) {
                Log::info('Telegram connection test successful', [
                    'bot_username' => $result['username'],
                    'proxy_enabled' => $this->proxyConfig['enabled'],
                    'proxy_config' => $this->proxyConfig
                ]);
                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error('Telegram connection test failed', [
                'error' => $e->getMessage(),
                'proxy_config' => $this->proxyConfig
            ]);
            return false;
        }
    }

    // Additional helper methods for ticket and transaction handling would go here...
}