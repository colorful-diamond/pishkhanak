<?php

namespace App\Http\Controllers;

use App\Services\TelegramBotFixed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramAdminWebhookController extends Controller
{
    protected $bot;
    
    public function __construct()
    {
        $this->bot = new TelegramBotFixed();
    }
    
    /**
     * Handle incoming webhook from Telegram
     */
    public function handle(Request $request)
    {
        try {
            $update = $request->all();
            
            Log::info('Admin Bot Webhook received', $update);
            
            // Process the update
            $this->bot->processUpdate($update);
            
            return response()->json(['ok' => true], 200);
            
        } catch (\Exception $e) {
            Log::error('Admin Bot Webhook error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'ok' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Set webhook URL
     */
    public function setWebhook(Request $request)
    {
        try {
            $webhookUrl = url('/telegram/admin-webhook');
            
            $result = $this->bot->setWebhook($webhookUrl);
            
            if ($result) {
                return response()->json([
                    'ok' => true,
                    'message' => 'Admin webhook set successfully',
                    'webhook_url' => $webhookUrl
                ]);
            }
            
            return response()->json([
                'ok' => false,
                'message' => 'Failed to set admin webhook'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get webhook info
     */
    public function getWebhookInfo()
    {
        try {
            $info = $this->bot->getWebhookInfo();
            
            return response()->json([
                'ok' => true,
                'info' => $info
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Delete webhook
     */
    public function deleteWebhook()
    {
        try {
            $result = $this->bot->deleteWebhook();
            
            return response()->json([
                'ok' => true,
                'message' => 'Admin webhook deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}