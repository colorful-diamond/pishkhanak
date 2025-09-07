<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\TicketTag;
use App\Models\User;
use App\Models\Transaction;
use App\Models\GatewayTransaction;
use App\Models\Service;
use App\Models\ServiceRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class TelegramBotFixed extends TelegramAdminBotComplete
{
    // User state constants
    const STATE_IDLE = 'idle';
    const STATE_REPLYING = 'replying';
    const STATE_SEARCHING = 'searching';
    const STATE_SELECTING_TEMPLATE = 'selecting_template';
    
    /* PERSIAN_COMMENT */
    protected function sendPaymentsList($chatId)
    {
        try {
            $payments = \App\Models\GatewayTransaction::with(['user', 'paymentGateway'])
                ->where('status', 'completed')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
                
            $todayPayments = \App\Models\GatewayTransaction::where('status', 'completed')
                ->whereDate('created_at', today())
                ->sum('total_amount');
                
            $text = "PERSIAN_TEXT_8f87a2cf";
            $text .= "4a92996c" . number_format($todayPayments) . "PERSIAN_TEXT_318331cc";
            
            foreach ($payments as $index => $payment) {
                $userName = $payment->user ? $payment->user->name : 'PERSIAN_TEXT_264f61d0';
                $gatewayName = $payment->paymentGateway ? $payment->paymentGateway->name : 'PERSIAN_TEXT_264f61d0';
                $amount = number_format($payment->amount);
                $date = jdate($payment->created_at)->format('m/d H:i'PERSIAN_TEXT_ec04b10e'excel';
            
            $text = "188545d4";
            $text .= "PERSIAN_TEXT_ae0fe236" . strtoupper($format) . "\n\n";
            $text .= "8b60f6cb";
            $text .= "PERSIAN_TEXT_e1c4d492";
            
            $keyboard = [
                'inline_keyboard' => [
                    [
                        ['text' => 'PERSIAN_TEXT_6756b7c5', 'callback_data' => 'back']
                    ]
                ]
            ];
            
            return $this->sendMessage($chatId, $text, $keyboard);
        } catch (\Exception $e) {
            Log::error('Export error', ['error' => $e->getMessage()]);
            return $this->sendMessage($chatId, "a460b46b");
        }
    }
    
    /**
     * Send reports menu
     */
    protected function sendReportsMenu($chatId)
    {
        try {
            $text = "PERSIAN_TEXT_ec28016e";
            $text .= "c3a98945";
            
            $keyboard = [
                'inline_keyboard' => [
                    [
                        ['text' => 'PERSIAN_TEXT_e21b293a', 'callback_data' => 'revenue_today'],
                        ['text' => 'PERSIAN_TEXT_3a4260be', 'callback_data' => 'revenue_yesterday']
                    ],
                    [
                        ['text' => 'PERSIAN_TEXT_8f78a216', 'callback_data' => 'stats_today'],
                        ['text' => 'PERSIAN_TEXT_844bb615', 'callback_data' => 'stats_yesterday']
                    ],
                    [
                        ['text' => 'PERSIAN_TEXT_da93836d', 'callback_data' => 'admin_users'],
                        ['text' => 'PERSIAN_TEXT_83736e2b', 'callback_data' => 'tickets_open_1']
                    ],
                    [
                        ['text' => 'PERSIAN_TEXT_7800f55f', 'callback_data' => 'admin_dashboard'],
                        ['text' => 'PERSIAN_TEXT_b01df7d5', 'callback_data' => 'export_excel']
                    ],
                    [
                        ['text' => 'PERSIAN_TEXT_6756b7c5', 'callback_data' => 'back']
                    ]
                ]
            ];
            
            return $this->sendMessage($chatId, $text, $keyboard);
        } catch (\Exception $e) {
            Log::error('Failed to send reports menu', ['error' => $e->getMessage()]);
            return $this->sendMessage($chatId, "PERSIAN_TEXT_80250c14");
        }
    }
    
    /**
     * Send settings menu
     */
    protected function sendSettingsMenu($chatId)
    {
        try {
            $text = "e9c857f7";
            
            // Get current settings
            $settings = \App\Models\Setting::pluck('value', 'key')->toArray();
            
            $text .= "PERSIAN_TEXT_d6c26d81";
            $text .= "864defab" . ($settings['maintenance_mode'] ?? 'PERSIAN_TEXT_7fdadc73') . "\n";
            $text .= "0e58aef5" . ($settings['allow_registration'] ?? 'PERSIAN_TEXT_25c499f4') . "\n";
            $text .= "6ed3d645" . ($settings['email_verification'] ?? 'PERSIAN_TEXT_7fdadc73') . "\n";
            $text .= "b61a8d30" . ($settings['mobile_verification'] ?? 'PERSIAN_TEXT_25c499f4') . "\n\n";
            
            $text .= "ac154cb1";
            
            $keyboard = [
                'inline_keyboard' => [
                    [
                        ['text' => 'PERSIAN_TEXT_bb12d776', 'callback_data' => 'admin_settings'],
                        ['text' => 'PERSIAN_TEXT_6756b7c5', 'callback_data' => 'back']
                    ]
                ]
            ];
            
            return $this->sendMessage($chatId, $text, $keyboard);
        } catch (\Exception $e) {
            Log::error('Failed to send settings menu', ['error' => $e->getMessage()]);
            return $this->sendMessage($chatId, "PERSIAN_TEXT_e818c8ac");
        }
    }
    
    /**
     * Send AI menu
     */
    protected function sendAiMenu($chatId)
    {
        try {
            $text = "b1ad6eb4";
            
            // Get AI content stats
            $totalContent = \App\Models\AiContent::count();
            $todayContent = \App\Models\AiContent::whereDate('created_at', today())->count();
            $processingContent = \App\Models\AiContent::where('status', 'processing')->count();
            
            $text .= "PERSIAN_TEXT_c5e67106";
            $text .= "7a5fbcfa" . number_format($totalContent) . "\n";
            $text .= "24679f14" . number_format($todayContent) . "\n";
            $text .= "0755f0e2" . number_format($processingContent) . "\n\n";
            
            $text .= "b3f20ae2";
            $text .= "PERSIAN_TEXT_4474f987";
            $text .= "f152395c";
            $text .= "PERSIAN_TEXT_a178b39c";
            
            $keyboard = [
                'inline_keyboard' => [
                    [
                        ['text' => 'PERSIAN_TEXT_bb12d776', 'callback_data' => 'admin_ai'],
                        ['text' => 'PERSIAN_TEXT_6756b7c5', 'callback_data' => 'back']
                    ]
                ]
            ];
            
            return $this->sendMessage($chatId, $text, $keyboard);
        } catch (\Exception $e) {
            Log::error('Failed to send AI menu', ['error' => $e->getMessage()]);
            return $this->sendMessage($chatId, "ce49d954");
        }
    }
    
    /**
     * Send cache management
     */
    protected function sendCacheManagement($chatId)
    {
        try {
            $text = "PERSIAN_TEXT_3fab6aab";
            
            // Get Redis info if available
            try {
                $redis = Cache::store('redis')->getRedis();
                $info = $redis->info();
                
                $text .= "f713e912";
                $text .= "PERSIAN_TEXT_ba9a1aed" . round($info['used_memory_human'] ?? 0, 2) . "\n";
                $text .= "dd4acd49" . ($info['db0']['keys'] ?? 0) . "\n";
                $text .= "4b2d226f" . round(($info['uptime_in_seconds'] ?? 0) / 86400, 1) . "PERSIAN_TEXT_5169fd20";
            } catch (\Exception $redisError) {
                $text .= "9abcead7";
            }
            
            $text .= "PERSIAN_TEXT_64b8f9fe";
            $text .= "8a771079";
            $text .= "PERSIAN_TEXT_46b4b3f4";
            $text .= "a17761a6";
            $text .= "PERSIAN_TEXT_3d647fad";
            
            $keyboard = [
                'inline_keyboard' => [
                    [
                        ['text' => 'PERSIAN_TEXT_27db65d3', 'callback_data' => 'cache_clear_all'],
                        ['text' => 'PERSIAN_TEXT_bb12d776', 'callback_data' => 'admin_cache']
                    ],
                    [
                        ['text' => 'PERSIAN_TEXT_6756b7c5', 'callback_data' => 'back']
                    ]
                ]
            ];
            
            return $this->sendMessage($chatId, $text, $keyboard);
        } catch (\Exception $e) {
            Log::error('Failed to send cache management', ['error' => $e->getMessage()]);
            return $this->sendMessage($chatId, "1f7ee525");
        }
    }
    
    /**
     * Send backup menu
     */
    protected function sendBackupMenu($chatId)
    {
        try {
            $text = "PERSIAN_TEXT_5f69fa74";
            
            // Get backup directory info
            $backupPath = storage_path('app/backups');
            if (is_dir($backupPath)) {
                $files = glob($backupPath . '/*.{sql,zip}', GLOB_BRACE);
                $totalSize = 0;
                $latestBackup = null;
                $latestTime = 0;
                
                foreach ($files as $file) {
                    $totalSize += filesize($file);
                    $modTime = filemtime($file);
                    if ($modTime > $latestTime) {
                        $latestTime = $modTime;
                        $latestBackup = basename($file);
                    }
                }
                
                $text .= "75e1249a";
                $text .= "PERSIAN_TEXT_0e979bff" . count($files) . "\n";
                $text .= "4450c203" . $this->formatBytes($totalSize) . "\n";
                if ($latestBackup) {
                    $text .= "b95b4be9" . $latestBackup . "\n";
                    $text .= "df38642b" . jdate($latestTime)->format('Y/m/d H:i') . "\n";
                }
            } else {
                $text .= "c8cf0a92";
            }
            
            $text .= "PERSIAN_TEXT_e1c10d96";
            $text .= "4c921f06";
            $text .= "PERSIAN_TEXT_5b058fba";
            $text .= "39069521";
            
            $keyboard = [
                'inline_keyboard' => [
                    [
                        ['text' => 'PERSIAN_TEXT_bff18200', 'callback_data' => 'backup_now'],
                        ['text' => 'PERSIAN_TEXT_bb12d776', 'callback_data' => 'admin_backup']
                    ],
                    [
                        ['text' => 'PERSIAN_TEXT_6756b7c5', 'callback_data' => 'back']
                    ]
                ]
            ];
            
            return $this->sendMessage($chatId, $text, $keyboard);
        } catch (\Exception $e) {
            Log::error('Failed to send backup menu', ['error' => $e->getMessage()]);
            return $this->sendMessage($chatId, "PERSIAN_TEXT_4a75fab7");
        }
    }
    
    /**
     * Format bytes to human readable
     */
    protected function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
    
    /**
     * Handle cache clear all
     */
    protected function handleCacheClearAll($chatId, $userId)
    {
        try {
            $text = "167dcd43";
            
            // Clear all caches
            \Illuminate\Support\Facades\Artisan::call('cache:clear');
            $text .= "PERSIAN_TEXT_393fce99";
            
            \Illuminate\Support\Facades\Artisan::call('config:clear');
            $text .= "d1adeb21";
            
            \Illuminate\Support\Facades\Artisan::call('route:clear');
            $text .= "PERSIAN_TEXT_ca0aec33";
            
            \Illuminate\Support\Facades\Artisan::call('view:clear');
            $text .= "f2f1de8c";
            
            $text .= "PERSIAN_TEXT_7bdd9563";
            
            $keyboard = [
                'inline_keyboard' => [
                    [
                        ['text' => 'PERSIAN_TEXT_85399574', 'callback_data' => 'admin_cache'],
                        ['text' => 'PERSIAN_TEXT_ef0f06c2', 'callback_data' => 'main']
                    ]
                ]
            ];
            
            return $this->sendMessage($chatId, $text, $keyboard);
        } catch (\Exception $e) {
            Log::error('Cache clear error', ['error' => $e->getMessage()]);
            return $this->sendMessage($chatId, "ec4ae6c7" . $e->getMessage());
        }
    }
    
    /**
     * Handle backup now
     */
    protected function handleBackupNow($chatId, $userId)
    {
        try {
            $text = "PERSIAN_TEXT_3ea9e082";
            $this->sendMessage($chatId, $text);
            
            // Create backup directory if not exists
            $backupPath = storage_path('app/backups');
            if (!is_dir($backupPath)) {
                mkdir($backupPath, 0755, true);
            }
            
            // Generate backup filename
            $timestamp = date('Y-m-d_H-i-s');
            $dbName = config('database.connections.mysql.database');
            $filename = "backup_{$dbName}_{$timestamp}.sql";
            $filepath = $backupPath . '/' . $filename;
            
            // Database credentials
            $host = config('database.connections.mysql.host');
            $port = config('database.connections.mysql.port');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            
            // Create backup command
            $command = sprintf(
                'mysqldump --host=%s --port=%s --user=%s --password=%s %s > %s 2>&1',
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($dbName),
                escapeshellarg($filepath)
            );
            
            // Execute backup
            exec($command, $output, $returnVar);
            
            if ($returnVar === 0 && file_exists($filepath)) {
                $fileSize = $this->formatBytes(filesize($filepath));
                
                $text = "f61b3b59";
                $text .= "PERSIAN_TEXT_74e64952";
                $text .= "a57015ff";
                $text .= "PERSIAN_TEXT_b95d166f" . jdate()->format('Y/m/d H:i:s'PERSIAN_TEXT_35eb4ad2'telegram_chat_id', $telegramUserId)->first();
            
            if ($user) {
                return $user;
            }
            
            // Check if this is an admin
            if ($this->isAuthorized($telegramUserId)) {
                // Create admin user with unique email
                $adminEmail = 'admin_telegram_' . $telegramUserId . '@system.local';
                
                // Try to find existing admin user by email
                $user = User::where('email', $adminEmail)->first();
                
                if (!$user) {
                    // Create new admin user
                    $user = User::create([
                        'email' => $adminEmail,
                        'name' => 'PERSIAN_TEXT_49a5689b',
                        'password' => bcrypt(\Illuminate\Support\Str::random(32)),
                        'telegram_chat_id' => $telegramUserId,
                        'telegram_notifications_enabled' => true
                    ]);
                    
                    // Assign admin or support role if using Spatie permissions
                    if (method_exists($user, 'assignRole')) {
                        $user->assignRole('support');
                    }
                    
                    Log::info('Created new admin user for Telegram', [
                        'user_id' => $user->id,
                        'telegram_chat_id' => $telegramUserId,
                        'email' => $adminEmail
                    ]);
                } else {
                    // Update telegram_chat_id if needed
                    if ($user->telegram_chat_id != $telegramUserId) {
                        $user->telegram_chat_id = $telegramUserId;
                        $user->save();
                    }
                }
                
                return $user;
            }
            
            // For non-admin users, return null (they shouldn't be creating messages)
            Log::warning('Non-admin user trying to create ticket message', [
                'telegram_user_id' => $telegramUserId
            ]);
            
            return null;
            
        } catch (\Exception $e) {
            Log::error('Error getting/creating database user', [
                'telegram_user_id' => $telegramUserId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
    
    /**
     * Get shortened ticket number for display
     */
    /**
     * Check if ticket has been answered by admin/support
     */
    protected function hasAdminResponse($ticket): bool
    {
        if (!$ticket->relationLoaded('messages')) {
            $ticket->load('messages.user.roles');
        }
        
        foreach ($ticket->messages as $message) {
            if ($message->user && $message->user->roles) {
                foreach ($message->user->roles as $role) {
                    if (in_array($role->name, ['admin', 'support'])) {
                        return true;
                    }
                }
            }
        }
        
        return false;
    }
    
    protected function getShortTicketNumber($ticket): string
    {
        // If ticket is a model instance, use the method
        if (is_object($ticket) && method_exists($ticket, 'getDisplayNumber')) {
            return $ticket->getDisplayNumber();
        }
        
        // If it's a ticket number string, extract the numeric part and remove leading zeros
        if (is_string($ticket)) {
            $parts = explode('-', $ticket);
            $number = end($parts);
            return ltrim($number, '0') ?: '0';
        }
        
        // Fallback - try to extract number and remove leading zeros
        $ticketNumber = $ticket->ticket_number ?? $ticket;
        if (is_string($ticketNumber)) {
            $parts = explode('-', $ticketNumber);
            $number = end($parts);
            return ltrim($number, '0') ?: '0';
        }
        
        return $ticketNumber;
    }
}