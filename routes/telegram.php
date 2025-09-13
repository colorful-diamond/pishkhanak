<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TelegramBotController;
// use App\Http\Controllers\TelegramAdminWebhookController;

/*
|--------------------------------------------------------------------------
| Telegram Bot Routes
|--------------------------------------------------------------------------
|
| Secure routes for Telegram bot webhook endpoints with comprehensive
| security middleware including authentication, rate limiting, and
| Persian text input validation.
|
*/

// Main Telegram Bot Webhook
Route::post('/telegram/webhook', [TelegramBotController::class, 'webhook'])
    ->middleware([
        'telegram.webhook.auth',      // Signature verification
        'telegram.rate.limit:webhook', // Rate limiting for webhooks
    ])
    ->name('telegram.webhook');

// Admin Bot Webhook (now handled by main controller)
// Route::post('/telegram/admin/webhook', [TelegramAdminWebhookController::class, 'webhook'])
//     ->middleware([
//         'telegram.webhook.auth',           // Signature verification
//         'telegram.rate.limit:admin_commands', // Stricter rate limiting
//     ])
//     ->name('telegram.admin.webhook');

// Bot setup and management routes (protected by authentication)
Route::prefix('telegram')->middleware(['auth', 'verified'])->group(function () {
    
    // Set webhook URL
    Route::post('/set-webhook', [TelegramBotController::class, 'setWebhook'])
        ->name('telegram.set-webhook');
    
    // Remove webhook
    Route::post('/remove-webhook', [TelegramBotController::class, 'removeWebhook'])
        ->name('telegram.remove-webhook');
    
    // Get webhook info
    Route::get('/webhook-info', [TelegramBotController::class, 'getWebhookInfo'])
        ->name('telegram.webhook-info');
    
    // Test bot connection
    Route::get('/test', [TelegramBotController::class, 'testBot'])
        ->name('telegram.test');
    
    // Send test notification
    Route::post('/test-notification', [TelegramBotController::class, 'sendTestNotification'])
        ->name('telegram.test-notification');
    
    // Admin management routes (super admin only)
    Route::prefix('admin')->middleware('can:manage-telegram-admin')->group(function () {
        
        // List admin users
        Route::get('/users', [TelegramBotController::class, 'listAdminUsers'])
            ->name('telegram.admin.users');
        
        // Add admin user
        Route::post('/users', [TelegramBotController::class, 'addAdminUser'])
            ->name('telegram.admin.add-user');
        
        // Remove admin user
        Route::delete('/users/{userId}', [TelegramBotController::class, 'removeAdminUser'])
            ->name('telegram.admin.remove-user');
        
        // View security logs
        Route::get('/security-logs', [TelegramBotController::class, 'securityLogs'])
            ->name('telegram.admin.security-logs');
        
        // Rate limit management
        Route::post('/clear-rate-limit', [TelegramBotController::class, 'clearRateLimit'])
            ->name('telegram.admin.clear-rate-limit');
        
        // Bot statistics
        Route::get('/stats', [TelegramBotController::class, 'getBotStatistics'])
            ->name('telegram.admin.stats');
        
    });
});

/*
|--------------------------------------------------------------------------
| Public Bot Information (Rate Limited)
|--------------------------------------------------------------------------
*/

Route::prefix('telegram/public')->middleware([
    'telegram.rate.limit:user_commands'
])->group(function () {
    
    // Bot information (for users to get bot username, etc.)
    Route::get('/info', [TelegramBotController::class, 'getBotInfo'])
        ->name('telegram.public.info');
    
    // Service status
    Route::get('/status', [TelegramBotController::class, 'getServiceStatus'])
        ->name('telegram.public.status');
        
});