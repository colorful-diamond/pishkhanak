import { login } from './login.js';
import { checkLogin } from './checkLogin.js';
import { refresh } from './refresh.js';
import { sessionManager } from './sessionManager.js';
import { nics24Config } from './config.js';

const credentials = nics24Config;

const user = credentials.users[Math.floor(Math.random() * credentials.users.length)];

// Enhanced login wrapper that registers session for monitoring
async function loginWithSessionManager(mobile = null) {
    const result = await login(user, mobile);
    
    // If login is successful, register the session for automatic refresh
    if (result && result.status === 'success' && mobile) {
        console.log(`🔐 [NICS24-INDEX] Login successful for ${mobile}, registering session for monitoring`);
        sessionManager.registerSession(mobile);
    }
    
    return result;
}

const nics24 = {
    login: loginWithSessionManager,
    checkLogin: async (mobile = null) => await checkLogin(user, mobile),
    refresh: async (mobile = null) => await refresh(user, mobile),
    sessionManager: sessionManager, // Expose session manager for external control
    
    // Provider configuration
    config: {
        name: 'nics24',
        baseUrl: credentials.baseUrl,
        endpoints: credentials.endpoints,
        captchaApiUrl: credentials.captchaApiUrl
    },
    
    // Helper methods
    getUser: () => user,
    getConfig: () => nics24.config,
    getCredentials: () => credentials,
    
    // Additional session management helper methods
    startSessionManager: () => sessionManager.start(),
    stopSessionManager: () => sessionManager.stop(),
    getSessionStatus: () => sessionManager.getStatus(),
    refreshAllSessions: () => sessionManager.refreshAllSessions()
}

export default nics24;