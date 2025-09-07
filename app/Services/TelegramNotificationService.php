<?php

namespace App\Services;

use App\Models\GatewayTransaction;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Verta;

class TelegramNotificationService
{
    protected $botToken;
    protected $channelId;
    protected $proxyUrl;
    protected $baseUrl = 'https://api.telegram.org/bot';

    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token', '7696804096:AAFUuZaXsoLDYIb8KJ7w-eLlhq4D422C1oc');
        $this->channelId = config('services.telegram.channel_id', '-1003097450288');
        
        // Configure proxy if enabled
        if (config('services.telegram.proxy.enabled', true)) {
            $proxyHost = config('services.telegram.proxy.host', '127.0.0.1');
            $proxyPort = config('services.telegram.proxy.port', 1080);
            $proxyType = config('services.telegram.proxy.type', 'socks5');
            $this->proxyUrl = "{$proxyType}://{$proxyHost}:{$proxyPort}";
        }
    }

    /**
     * Send a new order notification to Telegram channel
     */
    public function sendNewOrderNotification(GatewayTransaction $transaction)
    {
        try {
            $message = $this->formatOrderMessage($transaction);
            $keyboard = $this->createOrderKeyboard($transaction);

            $params = [
                'chat_id' => $this->channelId,
                'text' => $message,
                'parse_mode' => 'HTML',
                'reply_markup' => json_encode($keyboard)
            ];

            $response = $this->sendRequest('sendMessage', $params);

            if ($response && isset($response['ok']) && $response['ok']) {
                Log::info('Telegram notification sent successfully', [
                    'transaction_id' => $transaction->id,
                    'message_id' => $response['result']['message_id'] ?? null
                ]);
                return true;
            }

            Log::error('Telegram API returned error', [
                'transaction_id' => $transaction->id,
                'response' => $response
            ]);
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
     * Send request to Telegram API
     */
    protected function sendRequest($method, $params = [])
    {
        $url = $this->baseUrl . $this->botToken . '/' . $method;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        // Set proxy if configured
        if ($this->proxyUrl) {
            curl_setopt($ch, CURLOPT_PROXY, $this->proxyUrl);
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            throw new \Exception('cURL Error: ' . $error);
        }
        
        if ($httpCode !== 200) {
            throw new \Exception('HTTP Error: ' . $httpCode . ' - ' . $response);
        }
        
        return json_decode($response, true);
    }

    /**
     * Format the order message with beautiful styling
     */
    protected function formatOrderMessage(GatewayTransaction $transaction): string
    {
        $user = $transaction->user;
        $metadata = $transaction->metadata ?? [];
        $verta = new Verta($transaction->created_at);
        
        // Get transaction type description
        $typeDescription = $this->getTransactionTypeDescription($transaction);
        
        // Format amount with thousand separator
        $amount = number_format($transaction->amount);
        $totalAmount = number_format($transaction->total_amount);
        $gatewayFee = number_format($transaction->gateway_fee);
        
        // Get gateway name
        $gatewayName = $transaction->gateway->name ?? 'نامشخص';
        
        // Build the message
        $message = "🎉 <b>سفارش جدید دریافت شد!</b> 🎉\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━\n\n";
        
        // Order details section
        $message .= "📋 <b>جزئیات سفارش:</b>\n";
        $message .= "├ 🔢 <b>شناسه تراکنش:</b> <code>{$transaction->uuid}</code>\n";
        $message .= "├ 📅 <b>تاریخ:</b> {$verta->format('Y/m/d H:i:s')}\n";
        $message .= "├ 🏦 <b>درگاه پرداخت:</b> {$gatewayName}\n";
        $message .= "└ 📝 <b>نوع تراکنش:</b> {$typeDescription}\n\n";
        
        // User information section
        $message .= "👤 <b>اطلاعات کاربر:</b>\n";
        if ($user) {
            $message .= "├ 🆔 <b>شناسه کاربر:</b> <code>#{$user->id}</code>\n";
            $message .= "├ 👨 <b>نام:</b> {$user->name}\n";
            $message .= "├ 📧 <b>ایمیل:</b> {$user->email}\n";
            if ($user->mobile) {
                $message .= "├ 📱 <b>موبایل:</b> {$user->mobile}\n";
            }
            $message .= "└ 💼 <b>موجودی کیف پول:</b> " . number_format($user->balance) . " تومان\n\n";
        } else {
            $message .= "└ 🚫 کاربر مهمان\n\n";
        }
        
        // Financial details section
        $message .= "💰 <b>اطلاعات مالی:</b>\n";
        $message .= "├ 💵 <b>مبلغ اصلی:</b> {$amount} تومان\n";
        $message .= "├ 🏷️ <b>کارمزد درگاه:</b> {$gatewayFee} تومان\n";
        $message .= "├ 💳 <b>مبلغ کل:</b> {$totalAmount} تومان\n";
        
        if ($transaction->gateway_reference_id) {
            $message .= "└ 🔖 <b>شماره مرجع:</b> <code>{$transaction->gateway_reference_id}</code>\n\n";
        } else {
            $message .= "\n";
        }
        
        // Service details if available
        if ($transaction->type === 'service_payment' && isset($metadata['service_name'])) {
            $message .= "🎯 <b>سرویس درخواستی:</b>\n";
            $message .= "├ 📦 <b>نام سرویس:</b> {$metadata['service_name']}\n";
            if (isset($metadata['service_category'])) {
                $message .= "├ 🏷️ <b>دسته‌بندی:</b> {$metadata['service_category']}\n";
            }
            if (isset($metadata['request_data'])) {
                $message .= "└ 📊 <b>داده‌های ورودی:</b> موجود در سیستم\n\n";
            } else {
                $message .= "\n";
            }
        }
        
        // Success status
        $message .= "✅ <b>وضعیت:</b> پرداخت موفق\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━\n";
        $message .= "🌐 <b>پیشخوانک</b> | سامانه ارائه خدمات آنلاین";
        
        return $message;
    }

    /**
     * Create inline keyboard for the order message
     */
    protected function createOrderKeyboard(GatewayTransaction $transaction): array
    {
        $buttons = [];
        
        // View transaction details button
        $buttons[] = [
            [
                'text' => '👁 مشاهده جزئیات',
                'url' => route('filament.admin.resources.gateway-transactions.view', $transaction->id)
            ]
        ];
        
        // View user profile button if user exists
        if ($transaction->user) {
            $buttons[] = [
                [
                    'text' => '👤 پروفایل کاربر',
                    'url' => route('filament.admin.resources.users.view', $transaction->user_id)
                ]
            ];
        }
        
        // View service result if it's a service payment
        if ($transaction->type === 'service_payment' && isset($transaction->metadata['service_result_id'])) {
            $buttons[] = [
                [
                    'text' => '📊 نتیجه سرویس',
                    'url' => route('filament.admin.resources.service-results.view', $transaction->metadata['service_result_id'])
                ]
            ];
        }
        
        // Dashboard button
        $buttons[] = [
            [
                'text' => '📈 داشبورد مدیریت',
                'url' => route('filament.admin.pages.dashboard')
            ]
        ];
        
        return [
            'inline_keyboard' => $buttons
        ];
    }

    /**
     * Get transaction type description in Persian
     */
    protected function getTransactionTypeDescription(GatewayTransaction $transaction): string
    {
        $metadata = $transaction->metadata ?? [];
        
        switch ($transaction->type) {
            case 'wallet_charge':
                if (isset($metadata['type']) && $metadata['type'] === 'wallet_charge_for_service') {
                    return '💳 شارژ کیف پول برای سرویس';
                }
                return '💳 شارژ کیف پول';
                
            case 'service_payment':
                return '🛍️ پرداخت سرویس';
                
            case 'subscription':
                return '📅 اشتراک';
                
            default:
                return '💰 پرداخت';
        }
    }

    /**
     * Send a test notification
     */
    public function sendTestNotification(): bool
    {
        try {
            $message = "🧪 <b>تست اتصال ربات تلگرام</b>\n\n";
            $message .= "✅ ربات با موفقیت به کانال متصل شد!\n";
            $message .= "🤖 نام ربات: @pishkhanak_bot\n";
            $message .= "📢 کانال: {$this->channelId}\n";
            $message .= "🌐 پروکسی: فعال (SOCKS5)\n";
            $message .= "📅 زمان: " . Verta::now()->format('Y/m/d H:i:s');

            $params = [
                'chat_id' => $this->channelId,
                'text' => $message,
                'parse_mode' => 'HTML'
            ];

            $response = $this->sendRequest('sendMessage', $params);
            
            return $response && isset($response['ok']) && $response['ok'];
            
        } catch (\Exception $e) {
            Log::error('Test notification failed', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}