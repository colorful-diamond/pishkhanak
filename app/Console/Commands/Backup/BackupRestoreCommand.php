<?php

namespace App\Console\Commands\Backup;

use App\Services\BackupService;
use Google_Client;
use Google_Service_Drive;
use Illuminate\Console\Command;
use ZipArchive;

class BackupRestoreCommand extends Command
{
    protected $signature = 'backup:restore 
                           {file_id : Google Drive file ID}
                           {--components=all : Components to restore (all,files,database,redis)}
                           {--confirm : Skip confirmation prompts}';

    protected $description = 'Restore backup from Google Drive';

    protected $driveService;
    protected string $tempDir;

    public function __construct()
    {
        parent::__construct();
        $this->tempDir = storage_path('app/backups/restore');
        
        if (!is_dir($this->tempDir)) {
            mkdir($this->tempDir, 0755, true);
        }

        $this->initializeGoogleDrive();
    }

    protected function initializeGoogleDrive(): void
    {
        try {
            if (!class_exists('Google_Client') || !class_exists('Google_Service_Drive')) {
                throw new \Exception('Google Client not available');
            }
            
            $client = new \Google_Client();
            $client->setAuthConfig(storage_path('app/google-drive-credentials.json'));
            $client->setScopes([\Google_Service_Drive::DRIVE_FILE]);
            
            $this->driveService = new \Google_Service_Drive($client);
        } catch (\Exception $e) {
            // Gracefully handle missing Google Drive setup
            $this->driveService = null;
        }
    }

    public function handle(): int
    {
        $fileId = $this->argument('file_id');
        $components = $this->option('components');

        if (!$this->option('confirm')) {
            $this->warn('⚠️  DANGER: This will overwrite current data!');
            $this->warn('Components to restore: ' . $components);
            
            if (!$this->confirm('Are you sure you want to proceed with restore?')) {
                $this->info('Restore cancelled.');
                return 0;
            }
        }

        try {
            $this->info('🔄 Starting backup restore process...');
            
            // 1. Download backup from Google Drive
            $backupFile = $this->downloadBackup($fileId);
            
            // 2. Extract backup
            $extractedPath = $this->extractBackup($backupFile);
            
            // 3. Restore components
            $this->restoreComponents($extractedPath, $components);
            
            // 4. Cleanup
            $this->cleanup($backupFile, $extractedPath);
            
            $this->info('✅ Restore completed successfully!');
            return 0;
            
        } catch (\Exception $e) {
            $this->error('❌ Restore failed: ' . $e->getMessage());
            return 1;
        }
    }

    protected function downloadBackup(string $fileId): string
    {
        $this->info('📥 Downloading backup from Google Drive...');
        
        try {
            $file = $this->driveService->files->get($fileId);
            $fileName = $file->getName();
            
            $this->info('File: ' . $fileName);
            
            $response = $this->driveService->files->get($fileId, [
                'alt' => 'media'
            ]);
            
            $backupPath = $this->tempDir . '/' . $fileName;
            file_put_contents($backupPath, $response->getBody());
            
            $this->info('✅ Download completed: ' . $this->formatBytes(filesize($backupPath)));
            
            return $backupPath;
            
        } catch (\Exception $e) {
            throw new \Exception('Failed to download backup: ' . $e->getMessage());
        }
    }

    protected function extractBackup(string $backupFile): string
    {
        $this->info('📦 Extracting backup archive...');
        
        $extractPath = $this->tempDir . '/extracted';
        
        if (!is_dir($extractPath)) {
            mkdir($extractPath, 0755, true);
        }
        
        $zip = new ZipArchive();
        
        if ($zip->open($backupFile) !== TRUE) {
            throw new \Exception('Cannot open backup archive');
        }
        
        if (!$zip->extractTo($extractPath)) {
            throw new \Exception('Failed to extract backup archive');
        }
        
        $zip->close();
        
        $this->info('✅ Archive extracted successfully');
        
        return $extractPath;
    }

    protected function restoreComponents(string $extractedPath, string $components): void
    {
        $componentList = $components === 'all' 
            ? ['files', 'database', 'redis'] 
            : explode(',', $components);

        foreach ($componentList as $component) {
            $component = trim($component);
            
            switch ($component) {
                case 'files':
                    $this->restoreFiles($extractedPath);
                    break;
                    
                case 'database':
                    $this->restoreDatabase($extractedPath);
                    break;
                    
                case 'redis':
                    $this->restoreRedis($extractedPath);
                    break;
                    
                default:
                    $this->warn("Unknown component: {$component}");
            }
        }
    }

    protected function restoreFiles(string $extractedPath): void
    {
        $this->warn('🗂️  Restoring website files...');
        
        if (!$this->option('confirm')) {
            if (!$this->confirm('This will overwrite ALL website files. Continue?')) {
                $this->info('Skipping files restore.');
                return;
            }
        }
        
        // Find files archive
        $filesArchive = null;
        $files = glob($extractedPath . '/*_files.tar.gz');
        
        if (empty($files)) {
            $this->warn('No files backup found in archive');
            return;
        }
        
        $filesArchive = $files[0];
        $websitePath = '/home/pishkhanak/htdocs/pishkhanak.com';
        $backupPath = $websitePath . '_backup_' . date('Y-m-d_H-i-s');
        
        // Create backup of current files
        $this->info("Creating backup of current files at: {$backupPath}");
        exec("cp -r \"{$websitePath}\" \"{$backupPath}\"");
        
        // Extract files to temporary location
        $tempRestore = $this->tempDir . '/files_restore';
        mkdir($tempRestore, 0755, true);
        
        $command = "tar -xzf \"{$filesArchive}\" -C \"{$tempRestore}\"";
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            throw new \Exception('Failed to extract files archive');
        }
        
        // Move files to website directory
        exec("rsync -av --delete \"{$tempRestore}/\" \"{$websitePath}/\"");
        
        $this->info('✅ Website files restored successfully');
        $this->info("💾 Previous files backed up to: {$backupPath}");
    }

    protected function restoreDatabase(string $extractedPath): void
    {
        $this->warn('🗄️  Restoring database...');
        
        if (!$this->option('confirm')) {
            if (!$this->confirm('This will overwrite the ENTIRE database. Continue?')) {
                $this->info('Skipping database restore.');
                return;
            }
        }
        
        // Find database backup
        $sqlFiles = glob($extractedPath . '/*_database.sql');
        
        if (empty($sqlFiles)) {
            $this->warn('No database backup found in archive');
            return;
        }
        
        $sqlFile = $sqlFiles[0];
        
        $dbConfig = config('database.connections.pgsql');
        $host = $dbConfig['host'];
        $port = $dbConfig['port'];
        $database = $dbConfig['database'];
        $username = $dbConfig['username'];
        $password = $dbConfig['password'];
        
        // Set environment variable for password
        putenv("PGPASSWORD={$password}");
        
        // Create database backup
        $backupSqlFile = $this->tempDir . "/current_db_backup_" . date('Y-m-d_H-i-s') . ".sql";
        $backupCommand = "pg_dump -h \"{$host}\" -p \"{$port}\" -U \"{$username}\" -d \"{$database}\" > \"{$backupSqlFile}\"";
        exec($backupCommand);
        
        // Restore from backup
        $restoreCommand = "psql -h \"{$host}\" -p \"{$port}\" -U \"{$username}\" -d \"{$database}\" -f \"{$sqlFile}\"";
        exec($restoreCommand, $output, $returnCode);
        
        // Clear password from environment
        putenv("PGPASSWORD");
        
        if ($returnCode !== 0) {
            throw new \Exception('Database restore failed: ' . implode('\n', $output));
        }
        
        $this->info('✅ Database restored successfully');
        $this->info("💾 Previous database backed up to: {$backupSqlFile}");
    }

    protected function restoreRedis(string $extractedPath): void
    {
        $this->warn('📊 Restoring Redis data...');
        
        if (!$this->option('confirm')) {
            if (!$this->confirm('This will overwrite Redis data. Continue?')) {
                $this->info('Skipping Redis restore.');
                return;
            }
        }
        
        // Find Redis backup
        $rdbFiles = glob($extractedPath . '/*_redis.rdb');
        
        if (empty($rdbFiles)) {
            $this->warn('No Redis backup found in archive');
            return;
        }
        
        $rdbFile = $rdbFiles[0];
        
        // Stop Redis temporarily and replace RDB file
        // This is a simplified approach - adjust based on your Redis setup
        try {
            // Flush current Redis data
            \Illuminate\Support\Facades\Redis::command('FLUSHALL');
            
            // Copy RDB file (requires Redis to be stopped)
            $this->warn('⚠️  Manual Redis restore required:');
            $this->warn("1. Stop Redis service: sudo systemctl stop redis");
            $this->warn("2. Copy file: sudo cp \"{$rdbFile}\" /var/lib/redis/dump.rdb");
            $this->warn("3. Set permissions: sudo chown redis:redis /var/lib/redis/dump.rdb");
            $this->warn("4. Start Redis: sudo systemctl start redis");
            
        } catch (\Exception $e) {
            $this->warn('Redis restore requires manual steps due to: ' . $e->getMessage());
        }
    }

    protected function cleanup(string $backupFile, string $extractedPath): void
    {
        $this->info('🧹 Cleaning up temporary files...');
        
        // Remove downloaded backup
        if (file_exists($backupFile)) {
            unlink($backupFile);
        }
        
        // Remove extracted files
        if (is_dir($extractedPath)) {
            exec("rm -rf \"{$extractedPath}\"");
        }
    }

    protected function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}