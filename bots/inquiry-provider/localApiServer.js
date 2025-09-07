import express from 'express';
import cors from 'cors';
import rateLimit from 'express-rate-limit';
import helmet from 'helmet';
import compression from 'compression';
import { fileURLToPath } from 'url';
import path from 'path';
import fs from 'fs';
import dotenv from 'dotenv';
import requestLockManager from './services/requestLockManager.js';

// Get current directory for ES modules
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Load environment variables
dotenv.config({ path: path.resolve(__dirname, '.env') });

const app = express();
const PORT = process.env.LOCAL_API_PORT || 9999;
const ALLOWED_IPS = ['127.0.0.1', '::1', 'localhost'];

// Security middleware
app.use(helmet({
    contentSecurityPolicy: false
}));

// Compression middleware
app.use(compression());

// Rate limiting
const limiter = rateLimit({
    windowMs: 1 * 60 * 1000, // 1 minute
    max: 100, // Limit each IP to 100 requests per windowMs
    message: {
        error: 'Too many requests from this IP, please try again later.',
        code: 'RATE_LIMIT_EXCEEDED'
    },
    standardHeaders: true,
    legacyHeaders: false,
});

app.use('/api/', limiter);

// CORS - only allow local connections
app.use(cors({
    origin: function (origin, callback) {
        // Allow requests with no origin (like mobile apps or curl requests)
        if (!origin) return callback(null, true);
        
        const url = new URL(origin);
        if (ALLOWED_IPS.includes(url.hostname) || url.hostname.endsWith('.localhost')) {
            return callback(null, true);
        }
        
        callback(new Error('Not allowed by CORS'));
    },
    credentials: true
}));

// Body parser middleware
app.use(express.json({ limit: '10mb' }));
app.use(express.urlencoded({ extended: true, limit: '10mb' }));

// IP validation middleware
app.use((req, res, next) => {
    const clientIP = req.ip || req.connection.remoteAddress || req.socket.remoteAddress;
    const forwardedIPs = req.headers['x-forwarded-for'];
    
    console.log('Request from IP:', clientIP, 'Forwarded:', forwardedIPs);
    
    // Allow localhost and 127.0.0.1
    if (clientIP === '::1' || clientIP === '127.0.0.1' || clientIP === 'localhost' || 
        clientIP === '::ffff:127.0.0.1' || !clientIP) {
        return next();
    }
    
    return res.status(403).json({
        status: 'error',
        code: 'FORBIDDEN',
        message: 'Access denied. Local API server only accepts local connections.'
    });
});

// Request logging middleware
app.use((req, res, next) => {
    const timestamp = new Date().toISOString();
    console.log(`[${timestamp}] ${req.method} ${req.path} - IP: ${req.ip}`);
    
    if (process.env.DEBUG_MODE === 'true') {
        console.log('Headers:', req.headers);
        console.log('Body:', req.body);
    }
    
    next();
});

// Health check endpoint
app.get('/health', (req, res) => {
    res.json({
        status: 'success',
        message: 'Local API server is running',
        timestamp: new Date().toISOString(),
        uptime: process.uptime()
    });
});

// Debug endpoint to check active locks
app.get('/api/debug/locks', async (req, res) => {
    try {
        if (process.env.DEBUG_MODE !== 'true') {
            return res.status(403).json({
                status: 'error',
                code: 'DEBUG_DISABLED',
                message: 'Debug endpoints are disabled'
            });
        }
        
        const activeLocks = await requestLockManager.getAllActiveLocks();
        
        res.json({
            status: 'success',
            message: 'Active locks retrieved successfully',
            data: {
                total_locks: activeLocks.length,
                locks: activeLocks
            },
            timestamp: new Date().toISOString()
        });
        
    } catch (error) {
        console.error('Error retrieving active locks:', error);
        res.status(500).json({
            status: 'error',
            code: 'LOCKS_RETRIEVAL_ERROR',
            message: 'Failed to retrieve active locks'
        });
    }
});

// Debug endpoint to manually release a lock
app.delete('/api/debug/locks/:mobile/:nationalId/:serviceSlug', async (req, res) => {
    try {
        if (process.env.DEBUG_MODE !== 'true') {
            return res.status(403).json({
                status: 'error',
                code: 'DEBUG_DISABLED',
                message: 'Debug endpoints are disabled'
            });
        }
        
        const { mobile, nationalId, serviceSlug } = req.params;
        
        const released = await requestLockManager.releaseLock(mobile, nationalId, serviceSlug);
        
        res.json({
            status: 'success',
            message: released ? 'Lock released successfully' : 'Lock not found or already released',
            data: {
                mobile: mobile,
                nationalId: nationalId,
                serviceSlug: serviceSlug,
                released: released
            },
            timestamp: new Date().toISOString()
        });
        
    } catch (error) {
        console.error('Error releasing lock:', error);
        res.status(500).json({
            status: 'error',
            code: 'LOCK_RELEASE_ERROR',
            message: 'Failed to release lock'
        });
    }
});

// Debug endpoint to clear all locks
app.delete('/api/debug/locks', async (req, res) => {
    try {
        if (process.env.DEBUG_MODE !== 'true') {
            return res.status(403).json({
                status: 'error',
                code: 'DEBUG_DISABLED',
                message: 'Debug endpoints are disabled'
            });
        }
        
        console.log('🧹 [DEBUG] Manual request to clear all locks received');
        const clearResult = await requestLockManager.clearAllLocks();
        
        res.json({
            status: 'success',
            message: `Successfully cleared ${clearResult.cleared} locks`,
            data: {
                cleared: clearResult.cleared,
                total: clearResult.total,
                success: clearResult.success
            },
            timestamp: new Date().toISOString()
        });
        
    } catch (error) {
        console.error('Error clearing all locks:', error);
        res.status(500).json({
            status: 'error',
            code: 'LOCKS_CLEAR_ERROR',
            message: 'Failed to clear all locks'
        });
    }
});

// Service handler endpoint
app.post('/api/services/:serviceSlug', async (req, res) => {
    const { serviceSlug } = req.params;
    const serviceData = req.body;
    
    try {
        console.log(`Processing service: ${serviceSlug}`);
        
        // Validate service slug
        if (!serviceSlug || typeof serviceSlug !== 'string') {
            return res.status(400).json({
                status: 'error',
                code: 'INVALID_SERVICE_SLUG',
                message: 'Invalid service slug provided'
            });
        }
        
        // Sanitize service slug (remove any path traversal attempts)
        const sanitizedSlug = serviceSlug.replace(/[^a-z0-9-]/g, '');
        if (sanitizedSlug !== serviceSlug) {
            return res.status(400).json({
                status: 'error',
                code: 'INVALID_SERVICE_SLUG',
                message: 'Service slug contains invalid characters'
            });
        }
        
        // Convert kebab-case to camelCase for directory name
        function kebabToCamelCase(str) {
            return str.replace(/-([a-z])/g, (match, letter) => letter.toUpperCase());
        }
        
        // Get the actual directory name by converting kebab-case to camelCase
        const serviceDirectory = kebabToCamelCase(sanitizedSlug);
        
        // Check if service exists
        const servicePath = path.resolve(__dirname, `services/${serviceDirectory}/index.js`);
        
        if (!fs.existsSync(servicePath)) {
            console.log(`Service not found: ${servicePath}`);
            console.log(`Requested service slug: '${serviceSlug}' -> Directory: '${serviceDirectory}'`);
            return res.status(404).json({
                status: 'error',
                code: 'SERVICE_NOT_FOUND',
                message: `Service '${serviceSlug}' not found`
            });
        }
        
        // Dynamic import of service module
        const serviceModule = await import(servicePath);
        
        // Check if service has required functions
        if (!serviceModule.handle || typeof serviceModule.handle !== 'function') {
            return res.status(500).json({
                status: 'error',
                code: 'INVALID_SERVICE_MODULE',
                message: 'Service module does not export a handle function'
            });
        }
        
        // Add service slug to service data for request locking
        const serviceDataWithSlug = {
            ...serviceData,
            serviceSlug: sanitizedSlug
        };
        
        // Call service handler
        const result = await serviceModule.handle(serviceDataWithSlug);
        
        // Validate result
        if (!result || typeof result !== 'object') {
            throw new Error('Service returned invalid result');
        }
        
        // Log result for debugging
        if (process.env.DEBUG_MODE === 'true') {
            console.log('Service result:', result);
        }
        
        res.json(result);
        
    } catch (error) {
        console.error(`Error processing service ${serviceSlug}:`, error);
        
        res.status(500).json({
            status: 'error',
            code: 'SERVICE_ERROR',
            message: 'Internal service error',
            details: process.env.DEBUG_MODE === 'true' ? error.message : undefined
        });
    }
});

// List available services endpoint
app.get('/api/services', (req, res) => {
    try {
        const servicesDir = path.resolve(__dirname, 'services');
        
        if (!fs.existsSync(servicesDir)) {
            return res.json({
                status: 'success',
                services: [],
                message: 'Services directory not found'
            });
        }
        
        // Convert camelCase to kebab-case for API slugs
        function camelToKebabCase(str) {
            return str.replace(/([A-Z])/g, (match, letter) => `-${letter.toLowerCase()}`);
        }
        
        const services = fs.readdirSync(servicesDir, { withFileTypes: true })
            .filter(dirent => dirent.isDirectory())
            .map(dirent => {
                const servicePath = path.resolve(servicesDir, dirent.name, 'index.js');
                const serviceSlug = camelToKebabCase(dirent.name);
                return {
                    slug: serviceSlug,
                    directory: dirent.name,
                    available: fs.existsSync(servicePath)
                };
            });
        
        res.json({
            status: 'success',
            services: services,
            count: services.length
        });
        
    } catch (error) {
        console.error('Error listing services:', error);
        res.status(500).json({
            status: 'error',
            code: 'SERVICES_LIST_ERROR',
            message: 'Failed to list services'
        });
    }
});

// 404 handler
app.use((req, res) => {
    res.status(404).json({
        status: 'error',
        code: 'NOT_FOUND',
        message: 'Endpoint not found'
    });
});

// Error handler
app.use((error, req, res, next) => {
    console.error('Unhandled error:', error);
    
    res.status(500).json({
        status: 'error',
        code: 'INTERNAL_ERROR',
        message: 'Internal server error',
        details: process.env.DEBUG_MODE === 'true' ? error.message : undefined
    });
});

// Graceful shutdown
process.on('SIGTERM', () => {
    console.log('SIGTERM received, shutting down gracefully');
    server.close(() => {
        console.log('Process terminated');
        process.exit(0);
    });
});

process.on('SIGINT', () => {
    console.log('SIGINT received, shutting down gracefully');
    server.close(() => {
        console.log('Process terminated');
        process.exit(0);
    });
});

// Start server
const server = app.listen(PORT, '127.0.0.1', async () => {
    console.log(`🚀 Local API Server running on http://127.0.0.1:${PORT}`);
    console.log(`📁 Services directory: ${path.resolve(__dirname, 'services')}`);
    console.log(`🔒 Only accepting connections from localhost`);
    console.log(`🐛 Debug mode: ${process.env.DEBUG_MODE === 'true' ? 'ON' : 'OFF'}`);
    
    // Clear all existing request locks on fresh startup
    try {
        console.log(`\n🧹 [STARTUP] Clearing all request locks from previous sessions...`);
        const clearResult = await requestLockManager.clearAllLocks();
        
        if (clearResult.success !== false) {
            if (clearResult.cleared > 0) {
                console.log(`✅ [STARTUP] Cleared ${clearResult.cleared} existing locks`);
            } else {
                console.log(`✅ [STARTUP] No existing locks found - clean start`);
            }
        } else {
            console.log(`⚠️ [STARTUP] Warning: Could not clear locks - ${clearResult.error}`);
        }
        
        console.log(`🎉 [STARTUP] Server fully initialized and ready to accept requests!\n`);
        
    } catch (error) {
        console.error(`❌ [STARTUP] Error during lock cleanup:`, error.message);
        console.log(`⚠️ [STARTUP] Server running but lock cleanup failed\n`);
    }
});

export default app; 