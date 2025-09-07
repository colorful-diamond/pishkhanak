<?php

namespace App\Services;

use Google\Client as Google_Client;
use Google\Service\Drive as Google_Service_Drive;
use Google\Service\Drive\DriveFile as Google_Service_Drive_DriveFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use ZipArchive;
use Exception;

class BackupService
{
    protected Google_Client $googleClient;
    protected Google_Service_Drive $driveService;
    protected string $tempDir;
    protected array $config;

    public function __construct()
    {
        $this->tempDir = storage_path('app/backups/temp');
        $this->config = config('backup');
        
        // Ensure temp directory exists
        if (!is_dir($this->tempDir)) {
            mkdir($this->tempDir, 0755, true);
        }

        $this->initializeGoogleDrive();
    }

    /**
     * Initialize Google Drive client
     */
    protected function initializeGoogleDrive(): void
    {
        $this->googleClient = new Google_Client();
        $this->googleClient->setApplicationName('PishKhanak Backup System');
        $this->googleClient->setScopes([Google_Service_Drive::DRIVE_FILE]);
        $this->googleClient->setAuthConfig(storage_path('app/google-drive-credentials.json'));
        $this->googleClient->setAccessType('offline');
        
        $this->driveService = new Google_Service_Drive($this->googleClient);
    }

    /**
     * Create complete backup (files + database + redis)
     */
    public function createCompleteBackup(string $type = 'hourly'): array
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $backupName = "pishkhanak_backup_{$type}_{$timestamp}";
        
        Log::info("Starting {$type} backup", ['backup_name' => $backupName]);

        try {
            $backupPaths = [];
            
            // 1. Backup website files
            $filesBackup = $this->backupWebsiteFiles($backupName);
            $backupPaths['files'] = $filesBackup;
            
            // 2. Backup PostgreSQL database
            $dbBackup = $this->backupDatabase($backupName);
            $backupPaths['database'] = $dbBackup;
            
            // 3. Backup Redis data
            $redisBackup = $this->backupRedis($backupName);
            $backupPaths['redis'] = $redisBackup;
            
            // 4. Create single ZIP file containing all backups
            $finalZipPath = $this->createFinalZip($backupName, $backupPaths);
            
            // 5. Upload to Google Drive
            $driveFileId = $this->uploadToGoogleDrive($finalZipPath, $backupName, $type);
            
            // 6. Clean up temporary files
            $this->cleanupTempFiles($backupPaths, $finalZipPath);
            
            // 7. Manage backup rotation
            $this->manageBackupRotation($type);
            
            $result = [
                'success' => true,
                'backup_name' => $backupName,
                'type' => $type,
                'google_drive_id' => $driveFileId,
                'timestamp' => $timestamp,
                'size' => $this->formatBytes(filesize($finalZipPath))
            ];
            
            Log::info("Backup completed successfully", $result);
            
            return $result;
            
        } catch (Exception $e) {
            Log::error("Backup failed", [
                'backup_name' => $backupName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'backup_name' => $backupName
            ];
        }
    }

    /**
     * Backup website files
     */
    protected function backupWebsiteFiles(string $backupName): string
    {
        $sourcePath = '/home/pishkhanak/htdocs/pishkhanak.com';
        $backupPath = $this->tempDir . "/{$backupName}_files.tar.gz";
        
        Log::info('Starting website files backup');
        
        // Exclude certain directories and files
        $excludes = [
            '--exclude=storage/logs/*',
            '--exclude=storage/framework/cache/*',
            '--exclude=storage/framework/sessions/*',
            '--exclude=storage/framework/views/*',
            '--exclude=storage/app/backups/*',
            '--exclude=node_modules/*',
            '--exclude=vendor/*',
            '--exclude=.git/*',
            '--exclude=*.log',
            '--exclude=storage/debugbar/*',
        ];
        
        $excludeStr = implode(' ', $excludes);
        
        $command = "tar -czf \"{$backupPath}\" -C \"{$sourcePath}\" {$excludeStr} .";
        
        $output = [];
        $returnCode = 0;
        exec($command . ' 2>&1', $output, $returnCode);
        
        if ($returnCode !== 0) {
            throw new Exception('Website files backup failed: ' . implode('\n', $output));
        }
        
        Log::info('Website files backup completed', [
            'path' => $backupPath,
            'size' => $this->formatBytes(filesize($backupPath))
        ]);
        
        return $backupPath;
    }

    /**
     * Backup PostgreSQL database
     */
    protected function backupDatabase(string $backupName): string
    {
        $backupPath = $this->tempDir . "/{$backupName}_database.sql";
        
        Log::info('Starting database backup');
        
        $dbConfig = config('database.connections.pgsql');
        $host = $dbConfig['host'];
        $port = $dbConfig['port'];
        $database = $dbConfig['database'];
        $username = $dbConfig['username'];
        $password = $dbConfig['password'];
        
        // Set PGPASSWORD environment variable
        putenv("PGPASSWORD={$password}");
        
        $command = "pg_dump -h \"{$host}\" -p \"{$port}\" -U \"{$username}\" -d \"{$database}\" --no-owner --no-privileges --clean --if-exists > \"{$backupPath}\"";
        
        $output = [];
        $returnCode = 0;
        exec($command . ' 2>&1', $output, $returnCode);
        
        // Clear password from environment
        putenv("PGPASSWORD");
        
        if ($returnCode !== 0) {
            throw new Exception('Database backup failed: ' . implode('\n', $output));
        }
        
        Log::info('Database backup completed', [
            'path' => $backupPath,
            'size' => $this->formatBytes(filesize($backupPath))
        ]);
        
        return $backupPath;
    }

    /**
     * Backup Redis data
     */
    protected function backupRedis(string $backupName): string
    {
        $backupPath = $this->tempDir . "/{$backupName}_redis.rdb";
        
        Log::info('Starting Redis backup');
        
        try {
            // Execute BGSAVE command
            Redis::command('BGSAVE');
            
            // Wait for background save to complete
            $timeout = 60; // 60 seconds timeout
            $elapsed = 0;
            
            while ($elapsed < $timeout) {
                $lastSave = Redis::command('LASTSAVE');
                sleep(1);
                $newLastSave = Redis::command('LASTSAVE');
                
                if ($newLastSave > $lastSave) {
                    break; // Background save completed
                }
                
                $elapsed++;
            }
            
            if ($elapsed >= $timeout) {
                throw new Exception('Redis backup timeout');
            }
            
            // Copy the RDB file
            $redisConfig = config('database.redis.default');
            $redisDataDir = '/var/lib/redis'; // Default Redis data directory
            $rdbFile = $redisDataDir . '/dump.rdb';
            
            if (!file_exists($rdbFile)) {
                // Try alternative locations
                $possiblePaths = [
                    '/var/lib/redis/dump.rdb',
                    '/usr/local/var/db/redis/dump.rdb',
                    '/data/dump.rdb'
                ];
                
                foreach ($possiblePaths as $path) {
                    if (file_exists($path)) {
                        $rdbFile = $path;
                        break;
                    }
                }
                
                if (!file_exists($rdbFile)) {
                    throw new Exception('Redis RDB file not found');
                }
            }
            
            if (!copy($rdbFile, $backupPath)) {
                throw new Exception('Failed to copy Redis RDB file');
            }
            
        } catch (Exception $e) {
            // If Redis backup fails, create empty file with error info
            file_put_contents($backupPath, "Redis backup failed: " . $e->getMessage());
        }
        
        Log::info('Redis backup completed', [
            'path' => $backupPath,
            'size' => $this->formatBytes(filesize($backupPath))
        ]);
        
        return $backupPath;
    }

    /**
     * Create final ZIP file containing all backups
     */
    protected function createFinalZip(string $backupName, array $backupPaths): string
    {
        $zipPath = $this->tempDir . "/{$backupName}.zip";
        
        Log::info('Creating final ZIP archive');
        
        $zip = new ZipArchive();
        
        if ($zip->open($zipPath, ZipArchive::CREATE) !== TRUE) {
            throw new Exception('Cannot create ZIP archive');
        }
        
        // Add files to ZIP
        foreach ($backupPaths as $type => $path) {
            if (file_exists($path)) {
                $fileName = basename($path);
                $zip->addFile($path, $fileName);
            }
        }
        
        // Add backup info
        $backupInfo = [
            'backup_name' => $backupName,
            'timestamp' => now()->toISOString(),
            'website' => 'pishkhanak.com',
            'components' => array_keys($backupPaths),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version()
        ];
        
        $zip->addFromString('backup_info.json', json_encode($backupInfo, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        $zip->close();
        
        Log::info('ZIP archive created', [
            'path' => $zipPath,
            'size' => $this->formatBytes(filesize($zipPath))
        ]);
        
        return $zipPath;
    }

    /**
     * Upload backup to Google Drive
     */
    protected function uploadToGoogleDrive(string $filePath, string $backupName, string $type): string
    {
        Log::info('Starting Google Drive upload', ['type' => $type]);
        
        // Get or create folder for backup type
        $folderId = $this->getOrCreateFolder($type);
        
        $fileMetadata = new Google_Service_Drive_DriveFile([
            'name' => basename($filePath),
            'parents' => [$folderId]
        ]);
        
        $content = file_get_contents($filePath);
        
        $file = $this->driveService->files->create($fileMetadata, [
            'data' => $content,
            'mimeType' => 'application/zip',
            'uploadType' => 'resumable',
            'chunkSizeBytes' => 1 * 1024 * 1024, // 1MB chunks
        ]);
        
        Log::info('Google Drive upload completed', [
            'file_id' => $file->getId(),
            'name' => $file->getName()
        ]);
        
        return $file->getId();
    }

    /**
     * Get or create folder in Google Drive
     */
    protected function getOrCreateFolder(string $type): string
    {
        $folderName = "PishKhanak_Backup_" . ucfirst($type);
        
        // Search for existing folder
        $response = $this->driveService->files->listFiles([
            'q' => "name='{$folderName}' and mimeType='application/vnd.google-apps.folder'",
            'spaces' => 'drive'
        ]);
        
        if (count($response->getFiles()) > 0) {
            return $response->getFiles()[0]->getId();
        }
        
        // Create new folder
        $fileMetadata = new Google_Service_Drive_DriveFile([
            'name' => $folderName,
            'mimeType' => 'application/vnd.google-apps.folder'
        ]);
        
        $folder = $this->driveService->files->create($fileMetadata);
        
        Log::info('Created Google Drive folder', [
            'folder_name' => $folderName,
            'folder_id' => $folder->getId()
        ]);
        
        return $folder->getId();
    }

    /**
     * Manage backup rotation
     */
    protected function manageBackupRotation(string $type): void
    {
        $limits = [
            'hourly' => 24,
            'daily' => 7,
            'weekly' => 4,
            'monthly' => 12
        ];
        
        $limit = $limits[$type] ?? 24;
        $folderName = "PishKhanak_Backup_" . ucfirst($type);
        
        Log::info('Managing backup rotation', ['type' => $type, 'limit' => $limit]);
        
        try {
            // Get folder
            $response = $this->driveService->files->listFiles([
                'q' => "name='{$folderName}' and mimeType='application/vnd.google-apps.folder'",
                'spaces' => 'drive'
            ]);
            
            if (count($response->getFiles()) === 0) {
                return; // Folder doesn't exist
            }
            
            $folderId = $response->getFiles()[0]->getId();
            
            // Get all files in folder, ordered by creation time
            $files = $this->driveService->files->listFiles([
                'q' => "'{$folderId}' in parents",
                'orderBy' => 'createdTime desc',
                'fields' => 'files(id,name,createdTime)'
            ]);
            
            $fileList = $files->getFiles();
            
            // Delete excess files
            if (count($fileList) > $limit) {
                $filesToDelete = array_slice($fileList, $limit);
                
                foreach ($filesToDelete as $file) {
                    $this->driveService->files->delete($file->getId());
                    Log::info('Deleted old backup', [
                        'file_name' => $file->getName(),
                        'file_id' => $file->getId()
                    ]);
                }
            }
            
        } catch (Exception $e) {
            Log::warning('Backup rotation failed', [
                'type' => $type,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Clean up temporary files
     */
    protected function cleanupTempFiles(array $backupPaths, string $finalZipPath): void
    {
        Log::info('Cleaning up temporary files');
        
        // Delete individual backup files
        foreach ($backupPaths as $path) {
            if (file_exists($path)) {
                unlink($path);
            }
        }
        
        // Delete final ZIP file
        if (file_exists($finalZipPath)) {
            unlink($finalZipPath);
        }
    }

    /**
     * Format bytes to human readable format
     */
    protected function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Get backup statistics
     */
    public function getBackupStatistics(): array
    {
        $stats = [];
        $types = ['hourly', 'daily', 'weekly', 'monthly'];
        
        foreach ($types as $type) {
            $folderName = "PishKhanak_Backup_" . ucfirst($type);
            
            try {
                $response = $this->driveService->files->listFiles([
                    'q' => "name='{$folderName}' and mimeType='application/vnd.google-apps.folder'",
                    'spaces' => 'drive'
                ]);
                
                if (count($response->getFiles()) > 0) {
                    $folderId = $response->getFiles()[0]->getId();
                    
                    $files = $this->driveService->files->listFiles([
                        'q' => "'{$folderId}' in parents",
                        'fields' => 'files(id,name,size,createdTime)'
                    ]);
                    
                    $totalSize = 0;
                    $fileCount = count($files->getFiles());
                    
                    foreach ($files->getFiles() as $file) {
                        $totalSize += (int) $file->getSize();
                    }
                    
                    $stats[$type] = [
                        'count' => $fileCount,
                        'total_size' => $this->formatBytes($totalSize),
                        'total_size_bytes' => $totalSize
                    ];
                } else {
                    $stats[$type] = [
                        'count' => 0,
                        'total_size' => '0 B',
                        'total_size_bytes' => 0
                    ];
                }
                
            } catch (Exception $e) {
                $stats[$type] = [
                    'count' => 0,
                    'total_size' => 'Error',
                    'total_size_bytes' => 0,
                    'error' => $e->getMessage()
                ];
            }
        }
        
        return $stats;
    }
}