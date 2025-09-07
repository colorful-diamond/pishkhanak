import Redis from 'ioredis';
import dotenv from 'dotenv';
import path from 'path';
import { fileURLToPath } from 'url';

// Get current directory for ES modules
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Load environment variables
const envPath = path.resolve(__dirname, '../../.env');
dotenv.config({ path: envPath });

/**
 * Singleton Redis connection pool to prevent file descriptor exhaustion
 * This ensures we reuse connections instead of creating new ones for each service
 */
class RedisConnectionPool {
    constructor() {
        this.redis = null;
        this.isConnected = false;
        this.connectionPromise = null;
    }

    /**
     * Get or create the Redis connection
     * @returns {Promise<Redis>} Redis client instance
     */
    async getConnection() {
        // If already connecting, wait for that connection
        if (this.connectionPromise) {
            return await this.connectionPromise;
        }

        // If already connected, return existing connection
        if (this.isConnected && this.redis) {
            return this.redis;
        }

        // Create new connection
        this.connectionPromise = this._createConnection();
        
        try {
            this.redis = await this.connectionPromise;
            this.isConnected = true;
            return this.redis;
        } finally {
            this.connectionPromise = null;
        }
    }

    /**
     * Create a new Redis connection with proper configuration
     * @private
     */
    async _createConnection() {
        console.log('🔧 [REDIS-POOL] Creating new Redis connection...');

        const redisConfig = {
            host: process.env.REDIS_HOST || '127.0.0.1',
            port: process.env.REDIS_PORT || 6379,
            password: process.env.REDIS_PASSWORD || null,
            db: process.env.REDIS_DB || 0,
            keyPrefix: process.env.REDIS_PREFIX || 'pishkhanak_database_',
            
            // Connection pool settings to prevent too many connections
            maxRetriesPerRequest: 3,
            retryStrategy: (times) => {
                const delay = Math.min(times * 50, 2000);
                return delay;
            },
            
            // Enable lazy connect to prevent immediate connection
            lazyConnect: true,
            
            // Connection keep-alive
            keepAlive: 10000,
            
            // Reconnect on error
            reconnectOnError: (err) => {
                const targetError = 'READONLY';
                if (err.message.includes(targetError)) {
                    return true;
                }
                return false;
            }
        };

        const redis = new Redis(redisConfig);

        // Set up event handlers
        redis.on('error', (error) => {
            console.error('🔴 [REDIS-POOL] Redis connection error:', error.message);
            this.isConnected = false;
        });

        redis.on('connect', () => {
            console.log('✅ [REDIS-POOL] Redis connected successfully');
            this.isConnected = true;
        });

        redis.on('close', () => {
            console.log('🔶 [REDIS-POOL] Redis connection closed');
            this.isConnected = false;
        });

        redis.on('reconnecting', () => {
            console.log('🔄 [REDIS-POOL] Redis reconnecting...');
        });

        // Connect to Redis
        await redis.connect();
        
        return redis;
    }

    /**
     * Close the Redis connection gracefully
     */
    async close() {
        if (this.redis) {
            console.log('🛑 [REDIS-POOL] Closing Redis connection...');
            await this.redis.quit();
            this.redis = null;
            this.isConnected = false;
        }
    }

    /**
     * Execute a Redis command with automatic reconnection
     * @param {string} command - Redis command
     * @param {...any} args - Command arguments
     */
    async execute(command, ...args) {
        const redis = await this.getConnection();
        try {
            return await redis[command](...args);
        } catch (error) {
            console.error(`🔴 [REDIS-POOL] Error executing ${command}:`, error.message);
            
            // If connection error, try to reconnect once
            if (error.message.includes('Connection is closed')) {
                console.log('🔄 [REDIS-POOL] Attempting to reconnect...');
                this.isConnected = false;
                const newRedis = await this.getConnection();
                return await newRedis[command](...args);
            }
            
            throw error;
        }
    }
}

// Create singleton instance
const redisPool = new RedisConnectionPool();

// Handle process termination gracefully
process.on('SIGINT', async () => {
    console.log('\n🛑 [REDIS-POOL] Received SIGINT, closing Redis connection...');
    await redisPool.close();
});

process.on('SIGTERM', async () => {
    console.log('\n🛑 [REDIS-POOL] Received SIGTERM, closing Redis connection...');
    await redisPool.close();
});

// Export the singleton instance
export default redisPool;

// Export a function to get Redis instance (for backward compatibility)
export async function getRedis() {
    return await redisPool.getConnection();
}
