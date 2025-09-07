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
        
        // TEMPORARY: Disable proxy for testing
        // Comment out the proxy configuration
        /*
        if (config('services.telegram.proxy.enabled', true)) {
            $proxyHost = config('services.telegram.proxy.host', '127.0.0.1');
            $proxyPort = config('services.telegram.proxy.port', 1080);
            $proxyType = config('services.telegram.proxy.type', 'socks5'PERSIAN_TEXT_8b78d3c1'service_payment' && isset($metadata['service_name'])) {
            $message .= "PERSIAN_TEXT_96a94bac";
            $message .= "f7d36849";
            if (isset($metadata['service_category'])) {
                $message .= "PERSIAN_TEXT_149b402a";
            }
            if (isset($metadata['request_data'])) {
                $message .= "839e86da";
            } else {
                $message .= "\n";
            }
        }
        
        // Success status
        $message .= "ce779ac4";
        $message .= "━━━━━━━━━━━━━━━━━━━━\n";
        $message .= "ddb6cf1c";
        
        return $message;
    }

    /**
     * Create inline keyboard for the order message
     */
    protected function createOrderKeyboard(GatewayTransaction $transaction): array
    {
        $buttons = [];
        
        // Get base URL - fallback to pishkhanak.com if not set
        $baseUrl = rtrim(config('app.url', 'https://pishkhanak.com'), '/');
        
        // View transaction details button
        $buttons[] = [
            [
                'text' => 'PERSIAN_TEXT_d268d62c',
                'url' => $baseUrl . '/admin/gateway-transactions/' . $transaction->id
            ]
        ];
        
        // View user profile button if user exists
        if ($transaction->user) {
            $buttons[] = [
                [
                    'text' => 'PERSIAN_TEXT_389d38c7',
                    'url' => $baseUrl . '/admin/users/' . $transaction->user_id
                ]
            ];
        }
        
        // View service result if it's a service payment
        if ($transaction->type === 'service_payment' && isset($transaction->metadata['service_result_id'])) {
            $buttons[] = [
                [
                    'text' => 'PERSIAN_TEXT_cd00c2d4',
                    'url' => $baseUrl . '/admin/service-results/' . $transaction->metadata['service_result_id']
                ]
            ];
        }
        
        // Dashboard button
        $buttons[] = [
            [
                'text' => 'PERSIAN_TEXT_292372d4',
                'url' => $baseUrl . '/admin'
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
                    return 'PERSIAN_TEXT_ee81260a';
                }
                return 'PERSIAN_TEXT_9f2642b2';
                
            case 'service_payment':
                return 'PERSIAN_TEXT_a84f9c23';
                
            case 'subscription':
                return 'PERSIAN_TEXT_7bbc30d9';
                
            default:
                return 'PERSIAN_TEXT_fae09337';
        }
    }

    /**
     * Send a test notification
     */
    public function sendTestNotification(): bool
    {
        try {
            $message = "PERSIAN_TEXT_ff25fd35";
            $message .= "cdac3dfa";
            $message .= "PERSIAN_TEXT_15fa3b37";
            $message .= "4c86e833";
            $message .= "PERSIAN_TEXT_da6388e7";
            $message .= "PERSIAN_TEXT_f98fd3c5" . Verta::now()->format('Y/m/d H:i:s');

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
