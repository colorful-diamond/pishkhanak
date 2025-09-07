import Redis from 'ioredis';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import dotenv from 'dotenv';

// Get current directory for ES modules
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Load environment variables
const envPath = path.resolve(__dirname, '../.env');
dotenv.config({ path: envPath });

// Redis configuration
const redisConfig = {
    host: process.env.REDIS_HOST || '127.0.0.1',
    port: process.env.REDIS_PORT || 6379,
    password: process.env.REDIS_PASSWORD || null,
    db: process.env.REDIS_DB || 0,
    retryDelayOnFailover: 100,
    maxRetriesPerRequest: 3,
    lazyConnect: true
};

// Initialize Redis connection
const redis = new Redis(redisConfig);

// Redis connection error handling
redis.on('error', (error) => {
    console.error('🔴 [REQUEST-LOCK] Redis connection error:', error.message);
});

redis.on('connect', () => {
    console.log('🟢 [REQUEST-LOCK] Connected to Redis successfully');
});

/**
 * Request Lock Manager Class
 * Manages unique request processing based on mobile + national_id + service_slug
 */
class RequestLockManager {
    constructor() {
        this.lockPrefix = 'request_lock:';
        this.lockTtl = 600; // 10 minutes TTL for locks
        this.cleanupInterval = 60000; // 1 minute cleanup interval
        this.isCleanupRunning = false;
        this.startCleanupJob();
    }

    /**
     * Generate unique lock key for a request
     * @param {string} mobile - Mobile number
     * @param {string} nationalId - National ID
     * @param {string} serviceSlug - Service slug
     * @returns {string} Unique lock key
     */
    generateLockKey(mobile, nationalId, serviceSlug) {
        // Sanitize inputs and create a unique key
        const sanitizedMobile = mobile ? mobile.replace(/[^0-9]/g, '') : '';
        const sanitizedNationalId = nationalId ? nationalId.replace(/[^0-9]/g, '') : '';
        const sanitizedServiceSlug = serviceSlug ? serviceSlug.replace(/[^a-z0-9-]/g, '') : '';
        
        return `${this.lockPrefix}${sanitizedMobile}:${sanitizedNationalId}:${sanitizedServiceSlug}`;
    }

    /**
     * Acquire lock for a request
     * @param {string} mobile - Mobile number
     * @param {string} nationalId - National ID
     * @param {string} serviceSlug - Service slug
     * @param {string} requestHash - Optional request hash for tracking
     * @returns {Promise<Object>} Lock result
     */
    async acquireLock(mobile, nationalId, serviceSlug, requestHash = null) {
        try {
            const lockKey = this.generateLockKey(mobile, nationalId, serviceSlug);
            
            console.log(`🔒 [REQUEST-LOCK] Attempting to acquire lock: ${lockKey}`);
            
            // Check if lock already exists
            const existingLock = await redis.get(lockKey);
            
            if (existingLock) {
                const lockData = JSON.parse(existingLock);
                console.log(`⚠️ [REQUEST-LOCK] Lock already exists for key: ${lockKey}`);
                console.log(`📋 [REQUEST-LOCK] Existing lock data:`, lockData);
                
                // Check if the request hash matches (page refresh scenario)
                if (requestHash && lockData.requestHash === requestHash) {
                    console.log(`🔄 [REQUEST-LOCK] Same request hash detected - returning current progress transparently`);
                    console.log(`📋 [REQUEST-LOCK] Returning current status for hash: ${requestHash}`);
                    
                    return {
                        success: true,
                        isPageRefresh: true,
                        lockKey: lockKey,
                        lockData: lockData,
                        transparentReturn: true // Flag to indicate this should be handled transparently
                    };
                }
                
                // Different request hash - true duplicate request
                console.log(`🚫 [REQUEST-LOCK] Different request detected - true duplicate`);
                console.log(`📋 [REQUEST-LOCK] Existing hash: ${lockData.requestHash}, New hash: ${requestHash}`);
                
                return {
                    success: false,
                    error: 'REQUEST_ALREADY_PROCESSING',
                    requestHash: lockData.requestHash,
                    message: 'درخواست مشابه در حال پردازش است. لطفاً منتظر تکمیل پردازش قبلی باشید.',
                    existingData: lockData
                };
            }
            
            // Create new lock
            const lockData = {
                mobile: mobile,
                nationalId: nationalId,
                serviceSlug: serviceSlug,
                requestHash: requestHash,
                createdAt: new Date().toISOString(),
                expiresAt: new Date(Date.now() + this.lockTtl * 1000).toISOString(),
                status: 'processing'
            };
            
            // Set lock with TTL
            await redis.setex(lockKey, this.lockTtl, JSON.stringify(lockData));
            
            console.log(`✅ [REQUEST-LOCK] Lock acquired successfully: ${lockKey}`);
            console.log(`📋 [REQUEST-LOCK] Lock data:`, lockData);
            
            return {
                success: true,
                lockKey: lockKey,
                lockData: lockData
            };
            
        } catch (error) {
            console.error(`❌ [REQUEST-LOCK] Error acquiring lock:`, error.message);
            return {
                success: false,
                error: 'LOCK_ACQUISITION_ERROR',
                message: 'خطا در ایجاد قفل درخواست',
                details: error.message
            };
        }
    }

    /**
     * Release lock for a request
     * @param {string} mobile - Mobile number  
     * @param {string} nationalId - National ID
     * @param {string} serviceSlug - Service slug
     * @returns {Promise<boolean>} Success status
     */
    async releaseLock(mobile, nationalId, serviceSlug) {
        try {
            const lockKey = this.generateLockKey(mobile, nationalId, serviceSlug);
            
            console.log(`🔓 [REQUEST-LOCK] Releasing lock: ${lockKey}`);
            
            const result = await redis.del(lockKey);
            
            if (result > 0) {
                console.log(`✅ [REQUEST-LOCK] Lock released successfully: ${lockKey}`);
                return true;
            } else {
                console.log(`⚠️ [REQUEST-LOCK] Lock not found or already released: ${lockKey}`);
                return false;
            }
            
        } catch (error) {
            console.error(`❌ [REQUEST-LOCK] Error releasing lock:`, error.message);
            return false;
        }
    }

    /**
     * Release lock by direct key
     * @param {string} lockKey - Direct lock key
     * @returns {Promise<boolean>} Success status
     */
    async releaseLockByKey(lockKey) {
        try {
            console.log(`🔓 [REQUEST-LOCK] Releasing lock by key: ${lockKey}`);
            
            const result = await redis.del(lockKey);
            
            if (result > 0) {
                console.log(`✅ [REQUEST-LOCK] Lock released successfully by key: ${lockKey}`);
                return true;
            } else {
                console.log(`⚠️ [REQUEST-LOCK] Lock not found or already released by key: ${lockKey}`);
                return false;
            }
            
        } catch (error) {
            console.error(`❌ [REQUEST-LOCK] Error releasing lock by key:`, error.message);
            return false;
        }
    }

    /**
     * Update lock status
     * @param {string} lockKey - Lock key
     * @param {string} status - New status
     * @param {Object} additionalData - Additional data to store
     */
    async updateLockStatus(lockKey, status, additionalData = {}) {
        try {
            console.log(`📝 [REQUEST-LOCK] Updating lock status: ${lockKey} -> ${status}`);
            
            const existingData = await redis.get(lockKey);
            
            if (!existingData) {
                console.log(`⚠️ [REQUEST-LOCK] Lock not found for status update: ${lockKey}`);
                return false;
            }
            
            const lockData = JSON.parse(existingData);
            const updatedData = {
                ...lockData,
                status: status,
                updatedAt: new Date().toISOString(),
                ...additionalData
            };
            
            // Update lock with original TTL
            await redis.setex(lockKey, this.lockTtl, JSON.stringify(updatedData));
            
            console.log(`✅ [REQUEST-LOCK] Lock status updated: ${lockKey}`);
            return true;
            
        } catch (error) {
            console.error(`❌ [REQUEST-LOCK] Error updating lock status:`, error.message);
            return false;
        }
    }

    /**
     * Get lock information
     * @param {string} mobile - Mobile number
     * @param {string} nationalId - National ID
     * @param {string} serviceSlug - Service slug
     * @returns {Promise<Object|null>} Lock data or null
     */
    async getLockInfo(mobile, nationalId, serviceSlug) {
        try {
            const lockKey = this.generateLockKey(mobile, nationalId, serviceSlug);
            const lockData = await redis.get(lockKey);
            
            if (lockData) {
                return JSON.parse(lockData);
            }
            
            return null;
            
        } catch (error) {
            console.error(`❌ [REQUEST-LOCK] Error getting lock info:`, error.message);
            return null;
        }
    }

    /**
     * Start cleanup job to remove expired locks
     */
    startCleanupJob() {
        console.log(`🧹 [REQUEST-LOCK] Starting cleanup job (interval: ${this.cleanupInterval}ms)`);
        
        setInterval(async () => {
            if (this.isCleanupRunning) {
                console.log(`⏳ [REQUEST-LOCK] Cleanup already running, skipping...`);
                return;
            }
            
            await this.cleanupExpiredLocks();
        }, this.cleanupInterval);
    }

    /**
     * Clean up expired locks (older than 10 minutes)
     */
    async cleanupExpiredLocks() {
        try {
            this.isCleanupRunning = true;
            console.log(`🧹 [REQUEST-LOCK] Starting cleanup of expired locks...`);
            
            // Get all lock keys
            const lockKeys = await redis.keys(`${this.lockPrefix}*`);
            
            if (lockKeys.length === 0) {
                console.log(`✅ [REQUEST-LOCK] No locks found for cleanup`);
                return;
            }
            
            console.log(`📊 [REQUEST-LOCK] Found ${lockKeys.length} locks to check`);
            
            let cleanedCount = 0;
            const tenMinutesAgo = Date.now() - (10 * 60 * 1000); // 10 minutes ago
            
            for (const lockKey of lockKeys) {
                try {
                    const lockData = await redis.get(lockKey);
                    
                    if (!lockData) {
                        continue;
                    }
                    
                    const parsedData = JSON.parse(lockData);
                    const createdTime = new Date(parsedData.createdAt).getTime();
                    
                    // If lock is older than 10 minutes, remove it
                    if (createdTime < tenMinutesAgo) {
                        await redis.del(lockKey);
                        cleanedCount++;
                        console.log(`🗑️ [REQUEST-LOCK] Cleaned expired lock: ${lockKey}`);
                        console.log(`📋 [REQUEST-LOCK] Lock was created at: ${parsedData.createdAt}`);
                    }
                    
                } catch (lockError) {
                    console.error(`❌ [REQUEST-LOCK] Error processing lock ${lockKey}:`, lockError.message);
                }
            }
            
            console.log(`✅ [REQUEST-LOCK] Cleanup completed. Cleaned ${cleanedCount} expired locks`);
            
        } catch (error) {
            console.error(`❌ [REQUEST-LOCK] Error during cleanup:`, error.message);
        } finally {
            this.isCleanupRunning = false;
        }
    }

    /**
     * Get all active locks (for debugging)
     */
    async getAllActiveLocks() {
        try {
            const lockKeys = await redis.keys(`${this.lockPrefix}*`);
            const locks = [];
            
            for (const key of lockKeys) {
                const data = await redis.get(key);
                if (data) {
                    locks.push({
                        key: key,
                        data: JSON.parse(data)
                    });
                }
            }
            
            return locks;
            
        } catch (error) {
            console.error(`❌ [REQUEST-LOCK] Error getting all locks:`, error.message);
            return [];
        }
    }

    /**
     * Clear all request locks (for fresh startup)
     */
    async clearAllLocks() {
        try {
            console.log(`🧹 [REQUEST-LOCK] Clearing all request locks on fresh startup...`);
            
            // Get all lock keys
            const lockKeys = await redis.keys(`${this.lockPrefix}*`);
            
            if (lockKeys.length === 0) {
                console.log(`✅ [REQUEST-LOCK] No existing locks found to clear`);
                return { cleared: 0, total: 0 };
            }
            
            console.log(`📊 [REQUEST-LOCK] Found ${lockKeys.length} locks to clear`);
            
            // Delete all locks at once
            const deletedCount = await redis.del(...lockKeys);
            
            console.log(`✅ [REQUEST-LOCK] Successfully cleared ${deletedCount} locks out of ${lockKeys.length}`);
            
            return {
                cleared: deletedCount,
                total: lockKeys.length,
                success: true
            };
            
        } catch (error) {
            console.error(`❌ [REQUEST-LOCK] Error clearing all locks:`, error.message);
            return {
                cleared: 0,
                total: 0,
                success: false,
                error: error.message
            };
        }
    }

    /**
     * Close Redis connection
     */
    async close() {
        try {
            await redis.quit();
            console.log('🔴 [REQUEST-LOCK] Redis connection closed');
        } catch (error) {
            console.error('❌ [REQUEST-LOCK] Error closing Redis connection:', error.message);
        }
    }
}

// Create and export singleton instance
const requestLockManager = new RequestLockManager();

// Graceful shutdown handling
process.on('SIGINT', async () => {
    console.log('\n🛑 [REQUEST-LOCK] Received SIGINT, closing Redis connection...');
    await requestLockManager.close();
});

process.on('SIGTERM', async () => {
    console.log('\n🛑 [REQUEST-LOCK] Received SIGTERM, closing Redis connection...');
    await requestLockManager.close();
});

export default requestLockManager; 