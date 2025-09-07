/**
 * NICS24 Session Refresh
 * Refreshes user session if it's about to expire
 */

import { login } from './login.js';
import { checkLogin } from './checkLogin.js';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

// Get current directory for ES modules
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

/**
 * Refresh user session
 */
export async function refresh(user, mobile = null) {
    console.log('🔄 [NICS24-REFRESH] Starting session refresh...');
    
    try {
        // First check current session status
        const checkResult = await checkLogin(user, mobile);
        
        if (checkResult.status === 'success') {
            console.log('✅ [NICS24-REFRESH] Session is still valid, no refresh needed');
            return {
                status: 'success',
                message: 'Session is still valid',
                data: {
                    refreshed: false,
                    ...checkResult.data
                }
            };
        }
        
        console.log('🔄 [NICS24-REFRESH] Session invalid or expired, performing fresh login...');
        
        // Clean up any existing session files
        const sessionDir = path.resolve(__dirname, 'sessions');
        const sessionFile = path.join(sessionDir, `${user.username}_session.json`);
        
        if (fs.existsSync(sessionFile)) {
            fs.unlinkSync(sessionFile);
            console.log('🗑️ [NICS24-REFRESH] Cleaned up expired session file');
        }
        
        // Perform fresh login
        const loginResult = await login(user, mobile);
        
        if (loginResult.status === 'success') {
            console.log('✅ [NICS24-REFRESH] Session refreshed successfully');
            return {
                status: 'success',
                message: 'Session refreshed successfully',
                data: {
                    refreshed: true,
                    ...loginResult.data
                }
            };
        } else {
            console.error('❌ [NICS24-REFRESH] Failed to refresh session');
            return {
                status: 'error',
                code: 'REFRESH_FAILED',
                message: 'Failed to refresh session',
                details: loginResult
            };
        }
        
    } catch (error) {
        console.error('❌ [NICS24-REFRESH] Refresh failed:', error.message);
        
        return {
            status: 'error',
            code: 'REFRESH_ERROR',
            message: `Session refresh error: ${error.message}`
        };
    }
}