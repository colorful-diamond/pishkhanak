<?php

namespace App\Services;

use App\Models\User;
use App\Models\ServiceRequest;
use App\Models\ServiceResult;
use App\Models\ContactMessage;
use App\Models\Bank;
use App\Models\Currency;
use App\Models\Token;
use App\Models\Redirect;
use Bavix\Wallet\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Verta;

class TelegramAdminBotExtended extends TelegramAdminBot
{
    /* PERSIAN_COMMENT */
    protected function handleBankList($chatId, $userId, $args)
    {
        try {
            $banks = Bank::where('is_active', true)->get();
            
            $message = "PERSIAN_TEXT_1b2a936e";
            $message .= "━━━━━━━━━━━━━━━━\n\n";
            
            foreach ($banks as $bank) {
                $status = $bank->is_active ? '✅' : '🔴';
                
                $message .= "{$status} *{$bank->name}*\n";
                $message .= "55554b45";
                $message .= "PERSIAN_TEXT_d0a5c410";
                $message .= "d3bca203";
                
                if ($bank->sheba) {
                    $message .= "PERSIAN_TEXT_5a0c19b2";
                }
                
                $message .= "────────────\n";
            }
            
            $message .= "c1c58ce3" . $banks->count() . "PERSIAN_TEXT_7f30cc02";
            
            $keyboard = [
                'inline_keyboard' => [
                    [
                        ['text' => 'PERSIAN_TEXT_3c941df2', 'callback_data' => 'add_bank'],
                        ['text' => 'PERSIAN_TEXT_487a2081', 'callback_data' => 'edit_banks']
                    ],
                    [
                        ['text' => 'PERSIAN_TEXT_cf9ec871', 'callback_data' => 'admin_settings']
                    ]
                ]
            ];
            
            return $this->sendMessage($chatId, $message, $keyboard, 'Markdown');
            
        } catch (\Exception $e) {
            Log::error('Bank list error', ['error' => $e->getMessage()]);
            return $this->sendMessage($chatId, "821d55c7");
        }
    }

    /**
     * Handle currency list
     */
    protected function handleCurrencyList($chatId, $userId, $args)
    {
        try {
            $currencies = Currency::all();
            
            $message = "PERSIAN_TEXT_1e04e7e8";
            $message .= "━━━━━━━━━━━━━━━━\n\n";
            
            foreach ($currencies as $currency) {
                $status = $currency->is_active ? '✅' : '🔴';
                $isDefault = $currency->is_default ? '⭐' : '';
                
                $message .= "{$status} {$isDefault} *{$currency->name}*\n";
                $message .= "0d170b58";
                $message .= "PERSIAN_TEXT_7c0adb8e";
                $message .= "c8c67341" . number_format($currency->exchange_rate, 4) . "\n";
                $message .= "d93a8be5";
                $message .= "────────────\n";
            }
            
            $message .= "c1c58ce3" . $currencies->count() . "PERSIAN_TEXT_534d7322";
            
            $keyboard = [
                'inline_keyboard' => [
                    [
                        ['text' => 'PERSIAN_TEXT_67cf60c9', 'callback_data' => 'add_currency'],
                        ['text' => 'PERSIAN_TEXT_1f5bb50e', 'callback_data' => 'update_rates']
                    ],
                    [
                        ['text' => 'PERSIAN_TEXT_487a2081', 'callback_data' => 'edit_currencies'],
                        ['text' => 'PERSIAN_TEXT_62fe6635', 'callback_data' => 'currency_report']
                    ],
                    [
                        ['text' => 'PERSIAN_TEXT_cf9ec871', 'callback_data' => 'admin_settings']
                    ]
                ]
            ];
            
            return $this->sendMessage($chatId, $message, $keyboard, 'Markdown');
            
        } catch (\Exception $e) {
            Log::error('Currency list error', ['error' => $e->getMessage()]);
            return $this->sendMessage($chatId, "5bbe6e22");
        }
    }

    /**
     * Backup database
     */
    protected function backupDatabase()
    {
        $filename = 'backup_db_' . date('Y-m-d_H-i-s') . '.sql';
        $path = storage_path('app/backups/' . $filename);
        
        // Create backups directory if not exists
        if (!file_exists(storage_path('app/backups'))) {
            mkdir(storage_path('app/backups'), 0755, true);
        }
        
        // Get database config
        $database = config('database.connections.pgsql.database');
        $username = config('database.connections.pgsql.username');
        $password = config('database.connections.pgsql.password');
        $host = config('database.connections.pgsql.host');
        $port = config('database.connections.pgsql.port');
        
        // Create backup command
        $command = sprintf(
            'PGPASSWORD=%s pg_dump -h %s -p %s -U %s %s > %s',
            escapeshellarg($password),
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            escapeshellarg($database),
            escapeshellarg($path)
        );
        
        exec($command, $output, $returnVar);
        
        if ($returnVar === 0 && file_exists($path)) {
            return $path;
        }
        
        return null;
    }

    /**
     * Backup files
     */
    protected function backupFiles()
    {
        $filename = 'backup_files_' . date('Y-m-d_H-i-s') . '.zip';
        $path = storage_path('app/backups/' . $filename);
        
        // Create backups directory if not exists
        if (!file_exists(storage_path('app/backups'))) {
            mkdir(storage_path('app/backups'), 0755, true);
        }
        
        // Create zip archive
        $zip = new \ZipArchive();
        if ($zip->open($path, \ZipArchive::CREATE) === TRUE) {
            // Add storage files
            $this->addDirectoryToZip($zip, storage_path('app/public'), 'storage');
            
            // Add config files
            $this->addDirectoryToZip($zip, config_path(), 'config');
            
            $zip->close();
            return $path;
        }
        
        return null;
    }

    /**
     * Full backup
     */
    protected function backupFull()
    {
        $filename = 'backup_full_' . date('Y-m-d_H-i-s') . '.tar.gz';
        $path = storage_path('app/backups/' . $filename);
        
        // Create backups directory if not exists
        if (!file_exists(storage_path('app/backups'))) {
            mkdir(storage_path('app/backups'), 0755, true);
        }
        
        // First create database backup
        $dbBackup = $this->backupDatabase();
        
        // Create tar archive with database and files
        $command = sprintf(
            'tar -czf %s -C %s .',
            escapeshellarg($path),
            escapeshellarg(base_path())
        );
        
        exec($command, $output, $returnVar);
        
        if ($returnVar === 0 && file_exists($path)) {
            return $path;
        }
        
        return null;
    }

    /**
     * Add directory to zip
     */
    protected function addDirectoryToZip($zip, $dir, $base = '')
    {
        $files = scandir($dir);
        
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                $path = $dir . '/' . $file;
                $localPath = $base ? $base . '/' . $file : $file;
                
                if (is_dir($path)) {
                    $zip->addEmptyDir($localPath);
                    $this->addDirectoryToZip($zip, $path, $localPath);
                } else {
                    $zip->addFile($path, $localPath);
                }
            }
        }
    }

    /**
     * Get cache statistics
     */
    protected function getCacheStatistics()
    {
        try {
            $keys = 0;
            $size = 0;
            
            // Try to get Redis stats if available
            if (config('cache.default') === 'redis') {
                try {
                    $redis = Cache::getRedis();
                    $info = $redis->info();
                    $keys = $info['db0']['keys'] ?? 0;
                    $size = round(($info['used_memory'] ?? 0) / 1024 / 1024, 2);
                } catch (\Exception $e) {
                    // Redis not available
                }
            }
            
            $lastClear = Cache::get('last_cache_clear', 'PERSIAN_TEXT_264f61d0');
            
            return [
                'keys' => $keys,
                'size' => $size,
                'last_clear' => $lastClear
            ];
        } catch (\Exception $e) {
            return [
                'keys' => 0,
                'size' => 0,
                'last_clear' => 'PERSIAN_TEXT_264f61d0'
            ];
        }
    }

    /**
     * Check queue workers status
     */
    protected function checkQueueWorkers()
    {
        try {
            // Check if queue:work process is running
            exec("ps aux | grep 'queue:work' | grep -v grep", $output);
            return !empty($output);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get request status emoji
     */
    protected function getRequestStatusEmoji($status)
    {
        $emojis = [
            'pending' => '⏳',
            'processing' => '🔄',
            'completed' => '✅',
            'failed' => '❌',
            'cancelled' => '🚫'
        ];
        
        return $emojis[$status] ?? '❓';
    }
}