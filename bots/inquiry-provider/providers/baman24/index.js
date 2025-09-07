import { login } from './login.js';
import { checkLogin } from './checkLogin.js';
import { refresh } from './refresh.js';
import { sessionManager } from './sessionManager.js';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

// Get current directory for ES modules
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Read credentials JSON file
const credintalsPath = path.resolve(__dirname, 'credintals.json');
const credintals = JSON.parse(fs.readFileSync(credintalsPath, 'utf8'));

const user = credintals.users[Math.floor(Math.random() * credintals.users.length)];

// Enhanced login wrapper that registers session for monitoring
async function loginWithSessionManager(mobile = null) {
    const result = await login(user, mobile);
    
    // If login is successful, register the session for automatic refresh
    if (result && result.status === 'success' && mobile) {
        console.log(`🔐 [BMN24-INDEX] Login successful for ${mobile}, registering session for monitoring`);
        sessionManager.registerSession(mobile);
    }
    
    return result;
}

const baman24 = {
    login: loginWithSessionManager,
    checkLogin: async (mobile = null) => await checkLogin(user, mobile),
    refresh: async (mobile = null) => await refresh(user, mobile),
    sessionManager: sessionManager, // Expose session manager for external control
    
    // Additional helper methods
    startSessionManager: () => sessionManager.start(),
    stopSessionManager: () => sessionManager.stop(),
    getSessionStatus: () => sessionManager.getStatus(),
    refreshAllSessions: () => sessionManager.refreshAllSessions()
}

export default baman24;