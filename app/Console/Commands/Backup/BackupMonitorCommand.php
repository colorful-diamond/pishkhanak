<?php

namespace App\Console\Commands\Backup;

use App\Services\BackupService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class BackupMonitorCommand extends Command
{
    protected $signature = 'backup:monitor 
                           {--telegram : Send notifications via Telegram}
                           {--details : Show detailed information}';

    protected $description = 'Monitor backup system status and send notifications';

    public function handle(BackupService $backupService): int
    {
        $this->info('🔍 Monitoring Backup System Status');
        $this->newLine();

        $stats = $backupService->getBackupStatistics();
        $issues = $this->checkForIssues($stats);

        if ($this->option('details')) {
            $this->showDetailedStats($stats);
        } else {
            $this->showSummaryStats($stats, $issues);
        }

        if ($this->option('telegram') && config('backup.notifications.telegram.enabled')) {
            $this->sendTelegramNotification($stats, $issues);
        }

        return empty($issues) ? 0 : 1;
    }

    protected function checkForIssues(array $stats): array
    {
        $issues = [];
        $now = now();

        // Check if backups are recent enough
        $thresholds = [
            'hourly' => 2, // Should have backup within 2 hours
            'daily' => 25, // Should have backup within 25 hours  
            'weekly' => 8 * 24, // Should have backup within 8 days
            'monthly' => 32 * 24, // Should have backup within 32 days
        ];

        foreach ($stats as $type => $data) {
            if (isset($data['error'])) {
                $issues[] = "❌ {$type} backups have errors: {$data['error']}";
            } elseif ($data['count'] === 0) {
                $issues[] = "⚠️ No {$type} backups found";
            }
        }

        return $issues;
    }

    protected function showDetailedStats(array $stats): void
    {
        foreach ($stats as $type => $data) {
            $this->info("📊 " . ucfirst($type) . " Backups");
            
            if (isset($data['error'])) {
                $this->error("  Status: ❌ Error - " . $data['error']);
            } else {
                $this->info("  Status: ✅ OK");
                $this->info("  Count: {$data['count']} backups");
                $this->info("  Total Size: {$data['total_size']}");
            }
            
            $this->newLine();
        }
    }

    protected function showSummaryStats(array $stats, array $issues): void
    {
        $tableData = [];
        foreach ($stats as $type => $data) {
            $status = isset($data['error']) ? '❌ Error' : '✅ OK';
            $tableData[] = [
                ucfirst($type),
                $data['count'],
                $data['total_size'],
                $status
            ];
        }

        $this->table(['Type', 'Count', 'Size', 'Status'], $tableData);

        if (!empty($issues)) {
            $this->newLine();
            $this->warn('⚠️ Issues Found:');
            foreach ($issues as $issue) {
                $this->warn('  • ' . $issue);
            }
        } else {
            $this->newLine();
            $this->info('✅ All backup systems are working properly!');
        }
    }

    protected function sendTelegramNotification(array $stats, array $issues): void
    {
        $botToken = config('backup.notifications.telegram.bot_token');
        $chatId = config('backup.notifications.telegram.chat_id');

        if (!$botToken || !$chatId) {
            $this->warn('Telegram credentials not configured');
            return;
        }

        $message = $this->buildTelegramMessage($stats, $issues);

        try {
            $response = Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML'
            ]);

            if ($response->successful()) {
                $this->info('📱 Telegram notification sent successfully');
            } else {
                $this->error('📱 Failed to send Telegram notification');
            }
        } catch (\Exception $e) {
            $this->error('📱 Telegram notification error: ' . $e->getMessage());
        }
    }

    protected function buildTelegramMessage(array $stats, array $issues): string
    {
        $message = "🛡️ <b>PishKhanak Backup Status Report</b>\n";
        $message .= "📅 " . now()->format('Y-m-d H:i:s') . "\n\n";

        if (empty($issues)) {
            $message .= "✅ <b>All systems operational</b>\n\n";
        } else {
            $message .= "⚠️ <b>Issues detected:</b>\n";
            foreach ($issues as $issue) {
                $message .= "• " . strip_tags($issue) . "\n";
            }
            $message .= "\n";
        }

        $message .= "<b>📊 Current Status:</b>\n";
        foreach ($stats as $type => $data) {
            $status = isset($data['error']) ? '❌' : '✅';
            $message .= "{$status} <b>" . ucfirst($type) . ":</b> {$data['count']} backups ({$data['total_size']})\n";
        }

        $message .= "\n🏠 PishKhanak.com";

        return $message;
    }
}