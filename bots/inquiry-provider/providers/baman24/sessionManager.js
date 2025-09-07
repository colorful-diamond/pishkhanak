import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import { refresh } from './refresh.js';
import { checkLogin } from './checkLogin.js';

// Get current directory for ES modules
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Read credentials JSON file
const credintalsPath = path.resolve(__dirname, 'credintals.json');
const credintals = JSON.parse(fs.readFileSync(credintalsPath, 'utf8'));
const user = credintals.users[Math.floor(Math.random() * credintals.users.length)];

class Baman24SessionManager {
    constructor() {
        this.activeSessions = new Map(); // mobile -> { lastRefresh, refreshTimer, isActive }
        this.refreshInterval = 5 * 60 * 1000; // 5 minutes in milliseconds
        this.sessionsPath = path.resolve(__dirname, 'sessions');
        this.isRunning = false;
        
        console.log('🔧 [BMN24-SESSION-MANAGER] Initializing session manager');
        console.log('⏰ [BMN24-SESSION-MANAGER] Refresh interval set to:', this.refreshInterval / 1000, 'seconds');
        
        // Ensure sessions directory exists
        if (!fs.existsSync(this.sessionsPath)) {
            fs.mkdirSync(this.sessionsPath, { recursive: true });
            console.log('📁 [BMN24-SESSION-MANAGER] Created sessions directory');
        }
    }

    /**
     * Start the session manager
     */
    start() {
        if (this.isRunning) {
            console.log('⚠️ [BMN24-SESSION-MANAGER] Session manager is already running');
            return;
        }

        console.log('🚀 [BMN24-SESSION-MANAGER] Starting session manager...');
        this.isRunning = true;
        
        // Scan for existing sessions on startup
        this.scanExistingSessions();
        
        // Start the main monitoring loop
        this.startMonitoring();
        
        console.log('✅ [BMN24-SESSION-MANAGER] Session manager started successfully');
    }

    /**
     * Stop the session manager
     */
    stop() {
        if (!this.isRunning) {
            console.log('⚠️ [BMN24-SESSION-MANAGER] Session manager is not running');
            return;
        }

        console.log('🛑 [BMN24-SESSION-MANAGER] Stopping session manager...');
        this.isRunning = false;
        
        // Clear all refresh timers
        for (const [mobile, sessionInfo] of this.activeSessions) {
            if (sessionInfo.refreshTimer) {
                clearInterval(sessionInfo.refreshTimer);
                console.log(`⏹️ [BMN24-SESSION-MANAGER] Stopped refresh timer for session: ${mobile}`);
            }
        }
        
        this.activeSessions.clear();
        console.log('✅ [BMN24-SESSION-MANAGER] Session manager stopped');
    }

    /**
     * Scan for existing session files and register them for monitoring
     */
    scanExistingSessions() {
        console.log('🔍 [BMN24-SESSION-MANAGER] Scanning for existing sessions...');
        
        try {
            const files = fs.readdirSync(this.sessionsPath);
            const sessionFiles = files.filter(file => file.startsWith('session-') && file.endsWith('.json'));
            
            console.log(`📊 [BMN24-SESSION-MANAGER] Found ${sessionFiles.length} existing session files`);
            
            for (const file of sessionFiles) {
                const mobile = file.replace('session-', '').replace('.json', '');
                this.registerSession(mobile);
                console.log(`📝 [BMN24-SESSION-MANAGER] Registered existing session: ${mobile}`);
            }
        } catch (error) {
            console.error('❌ [BMN24-SESSION-MANAGER] Error scanning existing sessions:', error.message);
        }
    }

    /**
     * Register a session for automatic refresh monitoring
     * @param {string} mobile - Mobile number for the session
     */
    registerSession(mobile) {
        if (this.activeSessions.has(mobile)) {
            console.log(`⚠️ [BMN24-SESSION-MANAGER] Session ${mobile} is already registered`);
            return;
        }

        console.log(`➕ [BMN24-SESSION-MANAGER] Registering session for monitoring: ${mobile}`);
        
        const sessionInfo = {
            lastRefresh: new Date(),
            refreshTimer: null,
            isActive: true,
            refreshCount: 0
        };

        this.activeSessions.set(mobile, sessionInfo);
        
        // Start immediate refresh timer for this session
        this.startSessionRefreshTimer(mobile);
        
        console.log(`✅ [BMN24-SESSION-MANAGER] Session ${mobile} registered successfully`);
    }

    /**
     * Unregister a session from monitoring
     * @param {string} mobile - Mobile number for the session
     */
    unregisterSession(mobile) {
        if (!this.activeSessions.has(mobile)) {
            console.log(`⚠️ [BMN24-SESSION-MANAGER] Session ${mobile} is not registered`);
            return;
        }

        console.log(`➖ [BMN24-SESSION-MANAGER] Unregistering session: ${mobile}`);
        
        const sessionInfo = this.activeSessions.get(mobile);
        if (sessionInfo.refreshTimer) {
            clearInterval(sessionInfo.refreshTimer);
        }
        
        this.activeSessions.delete(mobile);
        console.log(`✅ [BMN24-SESSION-MANAGER] Session ${mobile} unregistered successfully`);
    }

    /**
     * Start refresh timer for a specific session
     * @param {string} mobile - Mobile number for the session
     */
    startSessionRefreshTimer(mobile) {
        const sessionInfo = this.activeSessions.get(mobile);
        if (!sessionInfo) {
            console.log(`❌ [BMN24-SESSION-MANAGER] Cannot start timer for unregistered session: ${mobile}`);
            return;
        }

        // Clear existing timer if any
        if (sessionInfo.refreshTimer) {
            clearInterval(sessionInfo.refreshTimer);
        }

        console.log(`⏰ [BMN24-SESSION-MANAGER] Starting refresh timer for session: ${mobile}`);
        
        sessionInfo.refreshTimer = setInterval(async () => {
            await this.refreshSession(mobile);
        }, this.refreshInterval);
        
        // Also perform an immediate refresh check
        setTimeout(() => this.refreshSession(mobile), 1000);
    }

    /**
     * Refresh a specific session
     * @param {string} mobile - Mobile number for the session
     */
    async refreshSession(mobile) {
        const sessionInfo = this.activeSessions.get(mobile);
        if (!sessionInfo || !sessionInfo.isActive) {
            console.log(`⚠️ [BMN24-SESSION-MANAGER] Session ${mobile} is not active, skipping refresh`);
            return;
        }

        console.log(`🔄 [BMN24-SESSION-MANAGER] Refreshing session: ${mobile} (refresh #${sessionInfo.refreshCount + 1})`);
        
        try {
            // First check if session is still valid
            const checkResult = await checkLogin(user, mobile);
            
            if (checkResult && checkResult.status === 'success') {
                console.log(`✅ [BMN24-SESSION-MANAGER] Session ${mobile} is valid, performing refresh...`);
                
                const refreshResult = await refresh(user, mobile);
                
                if (refreshResult && refreshResult.status === 'success') {
                    sessionInfo.lastRefresh = new Date();
                    sessionInfo.refreshCount++;
                    console.log(`🎉 [BMN24-SESSION-MANAGER] Session ${mobile} refreshed successfully (count: ${sessionInfo.refreshCount})`);
                } else {
                    console.log(`❌ [BMN24-SESSION-MANAGER] Failed to refresh session ${mobile}:`, refreshResult?.message || 'Unknown error');
                    // Don't unregister on single failure, just log it
                }
            } else {
                console.log(`❌ [BMN24-SESSION-MANAGER] Session ${mobile} is invalid, unregistering from monitoring`);
                this.unregisterSession(mobile);
            }
        } catch (error) {
            console.error(`💥 [BMN24-SESSION-MANAGER] Error refreshing session ${mobile}:`, error.message);
            // Don't unregister on error, just log it
        }
    }

    /**
     * Start the main monitoring loop
     */
    startMonitoring() {
        console.log('👁️ [BMN24-SESSION-MANAGER] Starting monitoring loop...');
        
        // Check for new sessions every minute
        const monitoringInterval = setInterval(() => {
            if (!this.isRunning) {
                clearInterval(monitoringInterval);
                return;
            }
            
            this.scanForNewSessions();
        }, 60000); // Check every minute for new sessions
    }

    /**
     * Scan for new session files that aren't being monitored yet
     */
    scanForNewSessions() {
        try {
            const files = fs.readdirSync(this.sessionsPath);
            const sessionFiles = files.filter(file => file.startsWith('session-') && file.endsWith('.json'));
            
            for (const file of sessionFiles) {
                const mobile = file.replace('session-', '').replace('.json', '');
                if (!this.activeSessions.has(mobile)) {
                    console.log(`🆕 [BMN24-SESSION-MANAGER] Found new session file: ${mobile}`);
                    this.registerSession(mobile);
                }
            }
        } catch (error) {
            console.error('❌ [BMN24-SESSION-MANAGER] Error scanning for new sessions:', error.message);
        }
    }

    /**
     * Get status of all managed sessions
     */
    getStatus() {
        const status = {
            isRunning: this.isRunning,
            activeSessions: this.activeSessions.size,
            refreshInterval: this.refreshInterval / 1000,
            sessions: []
        };

        for (const [mobile, info] of this.activeSessions) {
            status.sessions.push({
                mobile: mobile,
                lastRefresh: info.lastRefresh,
                refreshCount: info.refreshCount,
                isActive: info.isActive
            });
        }

        return status;
    }

    /**
     * Manual refresh trigger for all sessions
     */
    async refreshAllSessions() {
        console.log('🔄 [BMN24-SESSION-MANAGER] Manual refresh triggered for all sessions');
        
        const sessions = Array.from(this.activeSessions.keys());
        for (const mobile of sessions) {
            await this.refreshSession(mobile);
        }
        
        console.log('✅ [BMN24-SESSION-MANAGER] Manual refresh completed for all sessions');
    }
}

// Create and export singleton instance
const sessionManager = new Baman24SessionManager();

// Export the class and instance
export { Baman24SessionManager, sessionManager };

// If this file is run directly, start the session manager
if (import.meta.url === `file://${process.argv[1]}`) {
    console.log('🚀 [BMN24-SESSION-MANAGER] Starting session manager in standalone mode...');
    
    sessionManager.start();
    
    // Handle graceful shutdown
    process.on('SIGINT', () => {
        console.log('\n🛑 [BMN24-SESSION-MANAGER] Received SIGINT, shutting down gracefully...');
        sessionManager.stop();
        process.exit(0);
    });
    
    process.on('SIGTERM', () => {
        console.log('\n🛑 [BMN24-SESSION-MANAGER] Received SIGTERM, shutting down gracefully...');
        sessionManager.stop();
        process.exit(0);
    });
    
    // Log status every 10 minutes
    setInterval(() => {
        const status = sessionManager.getStatus();
        console.log('📊 [BMN24-SESSION-MANAGER] Status:', JSON.stringify(status, null, 2));
    }, 10 * 60 * 1000);
} 