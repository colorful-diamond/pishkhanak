<?php

namespace App\Console\Commands\Backup;

use App\Services\BackupService;
use Illuminate\Console\Command;

class BackupCommand extends Command
{
    protected $signature = 'backup:create 
                           {type=hourly : Backup type (hourly, daily, weekly, monthly)}
                           {--force : Force backup even if disabled}
                           {--stats : Show backup statistics}';

    protected $description = 'Create comprehensive backup of website files, database, and Redis';

    public function handle(BackupService $backupService): int
    {
        if ($this->option('stats')) {
            return $this->showBackupStatistics($backupService);
        }

        $type = $this->argument('type');
        
        if (!in_array($type, ['hourly', 'daily', 'weekly', 'monthly'])) {
            $this->error('Invalid backup type. Must be: hourly, daily, weekly, or monthly');
            return 1;
        }

        if (!config('backup.enabled') && !$this->option('force')) {
            $this->warn('Backup system is disabled. Use --force to override.');
            return 1;
        }

        $this->info("Starting {$type} backup...");
        $this->newLine();

        // Show progress
        $bar = $this->output->createProgressBar(5);
        $bar->setFormat('verbose');
        
        $bar->setMessage('Initializing backup...');
        $bar->start();
        
        $result = $backupService->createCompleteBackup($type);
        
        $bar->setMessage('Backup completed');
        $bar->finish();
        
        $this->newLine(2);

        if ($result['success']) {
            $this->info('✅ Backup completed successfully!');
            $this->table(['Property', 'Value'], [
                ['Backup Name', $result['backup_name']],
                ['Type', $result['type']],
                ['Timestamp', $result['timestamp']],
                ['Size', $result['size']],
                ['Google Drive ID', $result['google_drive_id']],
            ]);
        } else {
            $this->error('❌ Backup failed!');
            $this->error('Error: ' . $result['error']);
            return 1;
        }

        return 0;
    }

    protected function showBackupStatistics(BackupService $backupService): int
    {
        $this->info('📊 Backup Statistics');
        $this->newLine();

        $stats = $backupService->getBackupStatistics();
        
        $tableData = [];
        foreach ($stats as $type => $data) {
            $tableData[] = [
                ucfirst($type),
                $data['count'],
                $data['total_size'],
                isset($data['error']) ? '❌ ' . $data['error'] : '✅ OK'
            ];
        }

        $this->table(
            ['Type', 'Count', 'Total Size', 'Status'],
            $tableData
        );

        return 0;
    }
}