/**
 * NICS24 Session Manager
 * Handles automatic session monitoring and refresh
 */

import { checkLogin } from './checkLogin.js';
import { refresh } from './refresh.js';
import { nics24Config } from './config.js';

// Get user from config
const user = nics24Config.users[Math.floor(Math.random() * nics24Config.users.length)];

class SessionManager {
    constructor() {
        this.sessions = new Map(); // mobile -> session info
        this.isRunning = false;
        this.checkInterval = null;
        this.checkIntervalMs = 10 * 60 * 1000; // Check every 10 minutes
        this.refreshBeforeExpiryMs = 30 * 60 * 1000; // Refresh 30 minutes before expiry
    }

    /**
     * Register a mobile number for session monitoring
     */
    registerSession(mobile) {
        console.log(`📝 [NICS24-SESSION] Registering session for ${mobile}`);
        
        this.sessions.set(mobile, {
            mobile: mobile,
            registeredAt: new Date(),
            lastChecked: null,
            lastRefreshed: null,
            status: 'registered'
        });

        // Start manager if not already running
        if (!this.isRunning) {
            this.start();
        }
    }

    /**
     * Unregister a mobile number from session monitoring
     */
    unregisterSession(mobile) {
        console.log(`🗑️ [NICS24-SESSION] Unregistering session for ${mobile}`);
        this.sessions.delete(mobile);

        // Stop manager if no sessions are being monitored
        if (this.sessions.size === 0 && this.isRunning) {
            this.stop();
        }
    }

    /**
     * Start the session manager
     */
    start() {
        if (this.isRunning) {
            console.log('⚠️ [NICS24-SESSION] Session manager is already running');
            return;
        }

        console.log('🚀 [NICS24-SESSION] Starting session manager...');
        this.isRunning = true;

        this.checkInterval = setInterval(async () => {
            await this.checkAllSessions();
        }, this.checkIntervalMs);

        console.log(`✅ [NICS24-SESSION] Session manager started (checking every ${this.checkIntervalMs / 1000 / 60} minutes)`);
    }

    /**
     * Stop the session manager
     */
    stop() {
        if (!this.isRunning) {
            console.log('⚠️ [NICS24-SESSION] Session manager is not running');
            return;
        }

        console.log('🛑 [NICS24-SESSION] Stopping session manager...');
        
        if (this.checkInterval) {
            clearInterval(this.checkInterval);
            this.checkInterval = null;
        }
        
        this.isRunning = false;
        console.log('✅ [NICS24-SESSION] Session manager stopped');
    }

    /**
     * Check all registered sessions
     */
    async checkAllSessions() {
        if (this.sessions.size === 0) {
            console.log('📊 [NICS24-SESSION] No sessions to check');
            return;
        }

        console.log(`📊 [NICS24-SESSION] Checking ${this.sessions.size} sessions...`);

        for (const [mobile, sessionInfo] of this.sessions) {
            try {
                await this.checkSession(mobile, sessionInfo);
            } catch (error) {
                console.error(`❌ [NICS24-SESSION] Error checking session for ${mobile}:`, error.message);
                sessionInfo.status = 'error';
                sessionInfo.lastError = error.message;
            }
        }
    }

    /**
     * Check a specific session
     */
    async checkSession(mobile, sessionInfo) {
        console.log(`🔍 [NICS24-SESSION] Checking session for ${mobile}...`);
        
        sessionInfo.lastChecked = new Date();
        
        const checkResult = await checkLogin(user, mobile);
        
        if (checkResult.status === 'success') {
            console.log(`✅ [NICS24-SESSION] Session valid for ${mobile}`);
            sessionInfo.status = 'valid';
            
            // Check if session needs refresh soon
            const sessionAge = checkResult.data.sessionAge * 60 * 1000; // Convert to ms
            const maxAge = 2 * 60 * 60 * 1000; // 2 hours in ms
            
            if ((maxAge - sessionAge) < this.refreshBeforeExpiryMs) {
                console.log(`⏰ [NICS24-SESSION] Session for ${mobile} needs refresh soon`);
                await this.refreshSession(mobile, sessionInfo);
            }
            
        } else {
            console.log(`❌ [NICS24-SESSION] Session invalid for ${mobile}, attempting refresh...`);
            sessionInfo.status = 'invalid';
            await this.refreshSession(mobile, sessionInfo);
        }
    }

    /**
     * Refresh a specific session
     */
    async refreshSession(mobile, sessionInfo) {
        console.log(`🔄 [NICS24-SESSION] Refreshing session for ${mobile}...`);
        
        try {
            const refreshResult = await refresh(user, mobile);
            
            if (refreshResult.status === 'success') {
                console.log(`✅ [NICS24-SESSION] Session refreshed for ${mobile}`);
                sessionInfo.status = 'refreshed';
                sessionInfo.lastRefreshed = new Date();
            } else {
                console.error(`❌ [NICS24-SESSION] Failed to refresh session for ${mobile}`);
                sessionInfo.status = 'refresh_failed';
                sessionInfo.lastError = refreshResult.message;
            }
            
        } catch (error) {
            console.error(`❌ [NICS24-SESSION] Error refreshing session for ${mobile}:`, error.message);
            sessionInfo.status = 'refresh_error';
            sessionInfo.lastError = error.message;
        }
    }

    /**
     * Manually refresh all sessions
     */
    async refreshAllSessions() {
        console.log(`🔄 [NICS24-SESSION] Manually refreshing all ${this.sessions.size} sessions...`);
        
        for (const [mobile, sessionInfo] of this.sessions) {
            await this.refreshSession(mobile, sessionInfo);
        }
        
        console.log('✅ [NICS24-SESSION] All sessions refresh complete');
    }

    /**
     * Get current status of session manager
     */
    getStatus() {
        const sessions = Array.from(this.sessions.entries()).map(([mobile, info]) => ({
            mobile,
            ...info,
            registeredAt: info.registeredAt?.toISOString(),
            lastChecked: info.lastChecked?.toISOString(),
            lastRefreshed: info.lastRefreshed?.toISOString()
        }));

        return {
            isRunning: this.isRunning,
            sessionCount: this.sessions.size,
            checkIntervalMinutes: this.checkIntervalMs / 1000 / 60,
            sessions: sessions
        };
    }

    /**
     * Get session info for a specific mobile
     */
    getSessionInfo(mobile) {
        const info = this.sessions.get(mobile);
        if (!info) {
            return null;
        }

        return {
            ...info,
            registeredAt: info.registeredAt?.toISOString(),
            lastChecked: info.lastChecked?.toISOString(),
            lastRefreshed: info.lastRefreshed?.toISOString()
        };
    }
}

// Create and export singleton instance
const sessionManager = new SessionManager();

export { sessionManager };