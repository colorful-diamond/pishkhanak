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
    console.error('🔴 [REDIS-UPDATER] Redis connection error:', error.message);
});

redis.on('connect', () => {
    console.log('🟢 [REDIS-UPDATER] Connected to Redis successfully');
});

redis.on('ready', () => {
    console.log('✅ [REDIS-UPDATER] Redis is ready to accept commands');
});

/**
 * Redis updater class for managing local request status
 */
class RedisUpdater {
    constructor() {
        this.redisPrefix = 'local_request:';
        this.redisTtl = 1800; // 30 minutes TTL
    }

    /**
     * Update request progress in Redis
     * @param {string} hash - Request hash
     * @param {number} progress - Progress percentage (0-100)
     * @param {string} step - Current step
     * @param {string} message - Status message
     */
    async updateProgress(hash, progress, step, message) {
        try {
            console.log(`📊 [REDIS-UPDATER] Updating progress for ${hash}:`, {
                progress,
                step,
                message
            });

            const redisKey = this.redisPrefix + hash;
            
            // Get existing data
            const existingData = await redis.get(redisKey);
            let requestData = existingData ? JSON.parse(existingData) : {};

            // Update progress data
            requestData = {
                ...requestData,
                progress: Math.min(100, Math.max(0, progress)),
                step: step,
                current_message: message,
                updated_at: new Date().toISOString()
            };

            // Store updated data in Redis
            await redis.setex(redisKey, this.redisTtl, JSON.stringify(requestData));

            // Publish update to channel for real-time updates
            const channelName = `local_request_updates:${hash}`;
            await redis.publish(channelName, JSON.stringify(requestData));

            console.log(`✅ [REDIS-UPDATER] Progress updated successfully for ${hash}`);
            return true;

        } catch (error) {
            console.error(`❌ [REDIS-UPDATER] Error updating progress for ${hash}:`, error.message);
            return false;
        }
    }

    /**
     * Mark request as requiring OTP
     * @param {string} hash - Request hash
     * @param {object} otpData - OTP data including hash and expiry
     */
    async markAsOtpRequired(hash, otpData) {
        try {
            console.log(`🔐 [REDIS-UPDATER] Marking ${hash} as OTP required`);

            const redisKey = this.redisPrefix + hash;
            
            // Get existing data
            const existingData = await redis.get(redisKey);
            let requestData = existingData ? JSON.parse(existingData) : {};

            // Update with OTP requirement
            requestData = {
                ...requestData,
                status: 'otp_required',
                step: 'waiting_otp',
                progress: 70,
                current_message: 'در انتظار دریافت کد تایید',
                otp_data: otpData,
                requires_otp: true,
                updated_at: new Date().toISOString()
            };

            // Store updated data in Redis
            await redis.setex(redisKey, this.redisTtl, JSON.stringify(requestData));

            // Publish update to channel
            const channelName = `local_request_updates:${hash}`;
            await redis.publish(channelName, JSON.stringify(requestData));

            console.log(`✅ [REDIS-UPDATER] OTP requirement set for ${hash}`);
            return true;

        } catch (error) {
            console.error(`❌ [REDIS-UPDATER] Error setting OTP requirement for ${hash}:`, error.message);
            return false;
        }
    }

    /**
     * Mark request as completed
     * @param {string} hash - Request hash
     * @param {object} resultData - Final result data
     */
    async markAsCompleted(hash, resultData) {
        try {
            console.log(`🎉 [REDIS-UPDATER] Marking ${hash} as completed`);

            const redisKey = this.redisPrefix + hash;
            
            // Get existing data
            const existingData = await redis.get(redisKey);
            let requestData = existingData ? JSON.parse(existingData) : {};

            // Update with completion data
            requestData = {
                ...requestData,
                status: 'completed',
                step: 'completed',
                progress: 100,
                current_message: 'پردازش با موفقیت کامل شد',
                result_data: resultData,
                is_completed: true,
                completed_at: new Date().toISOString(),
                updated_at: new Date().toISOString()
            };

            // Store updated data in Redis
            await redis.setex(redisKey, this.redisTtl, JSON.stringify(requestData));

            // Publish update to channel
            const channelName = `local_request_updates:${hash}`;
            await redis.publish(channelName, JSON.stringify(requestData));

            console.log(`✅ [REDIS-UPDATER] Request ${hash} marked as completed`);
            return true;

        } catch (error) {
            console.error(`❌ [REDIS-UPDATER] Error marking ${hash} as completed:`, error.message);
            return false;
        }
    }

    /**
     * Mark request as failed
     * @param {string} hash - Request hash
     * @param {string} errorMessage - Error message
     * @param {object} errorData - Additional error data
     */
    async markAsFailed(hash, errorMessage, errorData = {}) {
        try {
            console.log(`💥 [REDIS-UPDATER] Marking ${hash} as failed:`, errorMessage);

            const redisKey = this.redisPrefix + hash;
            
            // Get existing data
            const existingData = await redis.get(redisKey);
            let requestData = existingData ? JSON.parse(existingData) : {};

            // Update with failure data
            requestData = {
                ...requestData,
                status: 'failed',
                current_message: errorMessage,
                error_data: {
                    message: errorMessage,
                    ...errorData
                },
                is_failed: true,
                completed_at: new Date().toISOString(),
                updated_at: new Date().toISOString()
            };

            // Store updated data in Redis
            await redis.setex(redisKey, this.redisTtl, JSON.stringify(requestData));

            // Publish update to channel
            const channelName = `local_request_updates:${hash}`;
            await redis.publish(channelName, JSON.stringify(requestData));

            console.log(`✅ [REDIS-UPDATER] Request ${hash} marked as failed`);
            return true;

        } catch (error) {
            console.error(`❌ [REDIS-UPDATER] Error marking ${hash} as failed:`, error.message);
            return false;
        }
    }

    /**
     * Get request status from Redis
     * @param {string} hash - Request hash
     * @returns {object|null} Request data or null if not found
     */
    async getRequestStatus(hash) {
        try {
            const redisKey = this.redisPrefix + hash;
            const data = await redis.get(redisKey);
            
            if (data) {
                return JSON.parse(data);
            }
            
            return null;
        } catch (error) {
            console.error(`❌ [REDIS-UPDATER] Error getting status for ${hash}:`, error.message);
            return null;
        }
    }

    /**
     * Get request progress data (for page refresh scenarios)
     * @param {string} hash - Request hash
     * @returns {object|null} Progress data or null if not found
     */
    async getProgress(hash) {
        try {
            console.log(`📊 [REDIS-UPDATER] Getting progress for ${hash}`);
            
            const redisKey = this.redisPrefix + hash;
            const data = await redis.get(redisKey);
            
            if (data) {
                const requestData = JSON.parse(data);
                
                // Extract relevant progress information
                const progressData = {
                    progress: requestData.progress || 0,
                    step: requestData.step || 'unknown',
                    message: requestData.current_message || 'در حال پردازش...',
                    status: requestData.status || 'pending',
                    requires_otp: requestData.requires_otp || false,
                    otp_data: requestData.otp_data || null,
                    is_completed: requestData.is_completed || false,
                    is_failed: requestData.is_failed || false,
                    result_data: requestData.result_data || null,
                    error_data: requestData.error_data || null,
                    updated_at: requestData.updated_at
                };

                console.log(`✅ [REDIS-UPDATER] Progress retrieved for ${hash}:`, {
                    progress: progressData.progress,
                    step: progressData.step,
                    status: progressData.status
                });

                return progressData;
            }
            
            console.log(`⚠️ [REDIS-UPDATER] No progress data found for ${hash}`);
            return null;
            
        } catch (error) {
            console.error(`❌ [REDIS-UPDATER] Error getting progress for ${hash}:`, error.message);
            return null;
        }
    }

    /**
     * Check if request is in a state that can handle page refresh
     * @param {string} hash - Request hash
     * @returns {object} Refresh capability info
     */
    async isRefreshable(hash) {
        try {
            const progressData = await this.getProgress(hash);
            
            if (!progressData) {
                return {
                    canRefresh: false,
                    reason: 'REQUEST_NOT_FOUND',
                    message: 'درخواست یافت نشد'
                };
            }

            // Cannot refresh if completed successfully
            if (progressData.is_completed) {
                return {
                    canRefresh: true,
                    reason: 'COMPLETED',
                    message: 'درخواست با موفقیت تکمیل شده',
                    showResult: true,
                    progressData
                };
            }

            // Cannot refresh if failed
            if (progressData.is_failed) {
                return {
                    canRefresh: true,
                    reason: 'FAILED',
                    message: 'درخواست ناموفق',
                    showError: true,
                    progressData
                };
            }

            // Can refresh if still processing
            return {
                canRefresh: true,
                reason: 'PROCESSING',
                message: 'درخواست در حال پردازش',
                progressData
            };

        } catch (error) {
            console.error(`❌ [REDIS-UPDATER] Error checking refresh capability for ${hash}:`, error.message);
            return {
                canRefresh: false,
                reason: 'ERROR',
                message: 'خطا در بررسی وضعیت درخواست'
            };
        }
    }

    /**
     * Handle page refresh request with comprehensive status
     * @param {string} hash - Request hash
     * @returns {object} Complete refresh response
     */
    async handlePageRefresh(hash) {
        try {
            console.log(`🔄 [REDIS-UPDATER] Handling page refresh for ${hash}`);
            
            const refreshInfo = await this.isRefreshable(hash);
            
            if (!refreshInfo.canRefresh) {
                return {
                    status: 'error',
                    isPageRefresh: true,
                    code: refreshInfo.reason,
                    message: refreshInfo.message
                };
            }

            const response = {
                status: 'success',
                isPageRefresh: true,
                message: refreshInfo.message,
                progressData: refreshInfo.progressData,
                refreshReason: refreshInfo.reason
            };

            // Add specific data based on request state
            if (refreshInfo.showResult) {
                response.resultData = refreshInfo.progressData.result_data;
                response.isCompleted = true;
            }

            if (refreshInfo.showError) {
                response.errorData = refreshInfo.progressData.error_data;
                response.isFailed = true;
            }

            console.log(`✅ [REDIS-UPDATER] Page refresh handled for ${hash}:`, {
                reason: refreshInfo.reason,
                status: refreshInfo.progressData?.status,
                progress: refreshInfo.progressData?.progress
            });

            return response;

        } catch (error) {
            console.error(`❌ [REDIS-UPDATER] Error handling page refresh for ${hash}:`, error.message);
            return {
                status: 'error',
                isPageRefresh: true,
                code: 'REFRESH_ERROR',
                message: 'خطا در پردازش رفرش صفحه'
            };
        }
    }

    /**
     * Close Redis connection
     */
    async close() {
        try {
            await redis.quit();
            console.log('🔴 [REDIS-UPDATER] Redis connection closed');
        } catch (error) {
            console.error('❌ [REDIS-UPDATER] Error closing Redis connection:', error.message);
        }
    }
}

// Create and export singleton instance
const redisUpdater = new RedisUpdater();

// Graceful shutdown handling
process.on('SIGINT', async () => {
    console.log('\n🛑 [REDIS-UPDATER] Received SIGINT, closing Redis connection...');
    await redisUpdater.close();
    process.exit(0);
});

process.on('SIGTERM', async () => {
    console.log('\n🛑 [REDIS-UPDATER] Received SIGTERM, closing Redis connection...');
    await redisUpdater.close();
    process.exit(0);
});

export default redisUpdater; 