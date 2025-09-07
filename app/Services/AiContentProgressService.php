<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use App\Models\AiContent;

class AiContentProgressService
{
    protected string $redisPrefix = 'ai_content_progress:';
    protected int $redisTtl = 3600; // 1 hour TTL

    /**
     * Update AI content generation progress in Redis
     */
    public function updateProgress(string $sessionHash, int $progress, string $step, string $message, array $additionalData = [])
    {
        try {
            Log::info("📊 [AI-PROGRESS] Updating progress for {$sessionHash}", [
                'progress' => $progress,
                'step' => $step,
                'message' => $message
            ]);

            $redisKey = $this->redisPrefix . $sessionHash;
            
            // Get existing data
            $existingData = Redis::get($redisKey);
            $progressData = $existingData ? json_decode($existingData, true) : [];

            // Update progress data
            $progressData = array_merge($progressData, [
                'progress' => max(0, min(100, $progress)),
                'step' => $step,
                'current_message' => $message,
                'updated_at' => now()->toISOString(),
                'session_hash' => $sessionHash
            ], $additionalData);

            // Store updated data in Redis
            Redis::setex($redisKey, $this->redisTtl, json_encode($progressData));

            // Publish update to channel for real-time updates
            $channelName = "ai_content_updates:{$sessionHash}";
            Redis::publish($channelName, json_encode($progressData));

            Log::info("✅ [AI-PROGRESS] Progress updated successfully for {$sessionHash}");
            return true;

        } catch (\Exception $e) {
            Log::error("❌ [AI-PROGRESS] Error updating progress for {$sessionHash}", [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Mark AI content generation as completed
     */
    public function markAsCompleted(string $sessionHash, int $aiContentId, array $resultData = [])
    {
        try {
            Log::info("🎉 [AI-PROGRESS] Marking {$sessionHash} as completed"7e71cacd"ai_content_updates:{$sessionHash}";
            Redis::publish($channelName, json_encode($progressData));

            Log::info("✅ [AI-PROGRESS] AI content {$sessionHash} marked as completed");
            return true;

        } catch (\Exception $e) {
            Log::error("❌ [AI-PROGRESS] Error marking {$sessionHash} as completed", [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Mark AI content generation as failed
     */
    public function markAsFailed(string $sessionHash, string $errorMessage, array $errorData = [])
    {
        try {
            Log::info("💥 [AI-PROGRESS] Marking {$sessionHash} as failed: {$errorMessage}");

            $redisKey = $this->redisPrefix . $sessionHash;
            
            // Get existing data
            $existingData = Redis::get($redisKey);
            $progressData = $existingData ? json_decode($existingData, true) : [];

            // Update with failure data
            $progressData = array_merge($progressData, [
                'status' => 'failed',
                'current_message' => $errorMessage,
                'error_data' => array_merge([
                    'message' => $errorMessage,
                ], $errorData),
                'is_failed' => true,
                'completed_at' => now()->toISOString(),
                'updated_at' => now()->toISOString()
            ]);

            // Store updated data in Redis
            Redis::setex($redisKey, $this->redisTtl, json_encode($progressData));

            // Publish update to channel
            $channelName = "ai_content_updates:{$sessionHash}";
            Redis::publish($channelName, json_encode($progressData));

            Log::info("✅ [AI-PROGRESS] AI content {$sessionHash} marked as failed");
            return true;

        } catch (\Exception $e) {
            Log::error("❌ [AI-PROGRESS] Error marking {$sessionHash} as failed", [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get AI content generation progress data
     */
    public function getProgress(string $sessionHash)
    {
        try {
            Log::info("📊 [AI-PROGRESS] Getting progress for {$sessionHash}"aa5c9c43"✅ [AI-PROGRESS] Progress retrieved for {$sessionHash}", [
                    'progress' => $responseData['progress'],
                    'step' => $responseData['step'],
                    'status' => $responseData['status']
                ]);

                return $responseData;
            }
            
            Log::info("⚠️ [AI-PROGRESS] No progress data found for {$sessionHash}");
            return null;
            
        } catch (\Exception $e) {
            Log::error("❌ [AI-PROGRESS] Error getting progress for {$sessionHash}"6563aded"🚀 [AI-PROGRESS] Session initialized for {$sessionHash}", [
                'ai_content_id' => $aiContentId
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error("❌ [AI-PROGRESS] Error initializing session {$sessionHash}", [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Update step-specific data
     */
    public function updateStepData(string $sessionHash, string $step, array $stepData)
    {
        try {
            $redisKey = $this->redisPrefix . $sessionHash;
            
            // Get existing data
            $existingData = Redis::get($redisKey);
            $progressData = $existingData ? json_decode($existingData, true) : [];

            // Update step-specific data
            $progressData = array_merge($progressData, $stepData, [
                'updated_at' => now()->toISOString()
            ]);

            // Store updated data in Redis
            Redis::setex($redisKey, $this->redisTtl, json_encode($progressData));

            Log::info("🔄 [AI-PROGRESS] Step data updated for {$sessionHash}", [
                'step' => $step,
                'data_keys' => array_keys($stepData)
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error("❌ [AI-PROGRESS] Error updating step data for {$sessionHash}", [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Clean up expired sessions with memory optimization
     */
    public function cleanupExpiredSessions()
    {
        try {
            $pattern = $this->redisPrefix . '*';
            $cleanedCount = 0;
            $batchSize = 100; // Process keys in batches to manage memory
            
            // Use SCAN instead of KEYS for better performance
            $cursor = 0;
            do {
                $result = Redis::scan($cursor, 'MATCH', $pattern, 'COUNT', $batchSize);
                if (!is_array($result) || count($result) < 2) {
                    Log::warning("Invalid SCAN result, stopping cleanup", ['result' => $result]);
                    break;
                }
                $keys = is_array($result[1]) ? $result[1] : [];
                $cursor = is_numeric($result[0]) ? (int)$result[0] : 0;
                
                if (!empty($keys)) {
                    $this->cleanupKeyBatch($keys, $cleanedCount);
                }
                
                // Small delay to prevent overwhelming Redis
                if ($cursor !== 0) {
                    usleep(10000); // 10ms delay
                }
                
            } while ($cursor !== 0);

            Log::info("🧹 [AI-PROGRESS] Cleaned up {$cleanedCount} expired sessions");

        } catch (\Exception $e) {
            Log::error("❌ [AI-PROGRESS] Error cleaning up sessions", [
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Clean up a batch of keys
     */
    private function cleanupKeyBatch(array $keys, int &$cleanedCount)
    {
        try {
            // Get all data in one batch operation
            $pipeline = Redis::pipeline();
            foreach ($keys as $key) {
                $pipeline->get($key);
            }
            $dataArray = $pipeline->execute();
            
            $keysToDelete = [];
            foreach ($keys as $index => $key) {
                $data = $dataArray[$index] ?? null;
                if ($data) {
                    $progressData = json_decode($data, true);
                    $updatedAt = $progressData['updated_at'] ?? null;
                    
                    if ($updatedAt) {
                        $lastUpdate = \Carbon\Carbon::parse($updatedAt);
                        if ($lastUpdate->diffInHours(now()) > 2) {
                            $keysToDelete[] = $key;
                        }
                    }
                } else {
                    // Delete empty/null entries
                    $keysToDelete[] = $key;
                }
            }
            
            // Delete expired keys in batch (with limit to prevent memory issues)
            if (!empty($keysToDelete)) {
                // Process in smaller chunks to prevent memory exhaustion
                $chunks = array_chunk($keysToDelete, 50);
                foreach ($chunks as $chunk) {
                    try {
                        Redis::del(...$chunk);
                        $cleanedCount += count($chunk);
                    } catch (\Exception $e) {
                        Log::error("Error deleting key chunk", ['error' => $e->getMessage()]);
                    }
                }
            }
            
        } catch (\Exception $e) {
            Log::error("❌ [AI-PROGRESS] Error cleaning batch", [
                'error' => $e->getMessage(),
                'batch_size' => count($keys)
            ]);
        }
    }
    
    /**
     * Monitor memory usage and cleanup if needed
     */
    public function monitorMemoryUsage()
    {
        $memoryUsage = memory_get_usage(true);
        $memoryLimit = $this->getMemoryLimit();
        
        if ($memoryUsage > ($memoryLimit * 0.8)) { // 80% threshold
            Log::warning("⚠️ [AI-PROGRESS] High memory usage detected", [
                'current_usage' => $this->formatBytes($memoryUsage),
                'memory_limit' => $this->formatBytes($memoryLimit),
                'usage_percentage' => round(($memoryUsage / $memoryLimit) * 100, 2)
            ]);
            
            // Trigger cleanup
            $this->cleanupExpiredSessions();
            
            // Force garbage collection
            if (function_exists('gc_collect_cycles')) {
                gc_collect_cycles();
            }
        }
    }
    
    /**
     * Get PHP memory limit in bytes
     */
    private function getMemoryLimit(): int
    {
        $memoryLimit = ini_get('memory_limit');
        
        if ($memoryLimit == -1) {
            return PHP_INT_MAX; // No limit
        }
        
        $value = (int) $memoryLimit;
        $unit = strtolower(substr($memoryLimit, -1));
        
        switch ($unit) {
            case 'g':
                $value *= 1024 * 1024 * 1024;
                break;
            case 'm':
                $value *= 1024 * 1024;
                break;
            case 'k':
                $value *= 1024;
                break;
        }
        
        return $value;
    }
    
    /**
     * Format bytes for human-readable output
     */
    private function formatBytes(int $bytes): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }
        
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $factor = floor(log($bytes, 1024));
        $factor = min($factor, count($units) - 1); // Prevent array index out of bounds
        
        return sprintf("%.2f %s", $bytes / pow(1024, $factor), $units[$factor]);
    }
    
    /**
     * Clear progress data for a session
     */
    public function clearProgress(string $sessionHash)
    {
        try {
            $redisKey = $this->redisPrefix . $sessionHash;
            Redis::del($redisKey);
            
            Log::info("🧹 [AI-PROGRESS] Cleared progress for {$sessionHash}");
            return true;
        } catch (\Exception $e) {
            Log::error("❌ [AI-PROGRESS] Error clearing progress for {$sessionHash}", [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Alias for initializeSession for backward compatibility
     */
    public function initializeProgress(string $sessionHash, int $aiContentId, array $initialData = [])
    {
        return $this->initializeSession($sessionHash, $aiContentId, $initialData);
    }
}
