/**
 * NICS24 Check Login Status
 * Verifies if user session is still valid
 */

import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import { 
    getRandomUserAgent, 
    getStealthLaunchArgs, 
    getStealthContextConfig, 
    setupStealthMode 
} from '../../utils/stealthUtils.js';

// Get current directory for ES modules
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

/**
 * Check if user is logged in by verifying session file and testing page access
 */
export async function checkLogin(user, mobile = null) {
    console.log('🔍 [NICS24-CHECK] Checking login status...');
    
    const sessionDir = path.resolve(__dirname, 'sessions');
    const sessionFile = path.join(sessionDir, `${user.username}_session.json`);
    
    try {
        // Check if session file exists
        if (!fs.existsSync(sessionFile)) {
            console.log('❌ [NICS24-CHECK] No session file found');
            return {
                status: 'error',
                code: 'NO_SESSION',
                message: 'No saved session found'
            };
        }
        
        // Read session data
        const sessionData = JSON.parse(fs.readFileSync(sessionFile, 'utf8'));
        
        // Check if session has expired
        const sessionAge = Date.now() - new Date(sessionData.createdAt).getTime();
        const maxAge = 2 * 60 * 60 * 1000; // 2 hours
        
        if (sessionAge > maxAge) {
            console.log('⏰ [NICS24-CHECK] Session expired');
            // Clean up expired session
            fs.unlinkSync(sessionFile);
            return {
                status: 'error',
                code: 'SESSION_EXPIRED',
                message: 'Session has expired'
            };
        }
        
        // Import playwright here to avoid loading it if not needed
        const { chromium } = await import('playwright');
        const browser = await chromium.launch({ 
            headless: true,
            args: getStealthLaunchArgs(getRandomUserAgent())
        });
        
        try {
            // Create context with saved session and stealth configuration
            const contextConfig = getStealthContextConfig(null, null, true);
            contextConfig.storageState = sessionData.storageState;
            
            const context = await browser.newContext(contextConfig);
            const page = await context.newPage();
            
            // Apply stealth mode
            await setupStealthMode(page);
            
            // Test if we can access a protected page
            await page.goto('https://etebarito.nics24.ir/pishkhan', {
                waitUntil: 'networkidle',
                timeout: 15000
            });
            
            const currentUrl = page.url();
            console.log('🔗 [NICS24-CHECK] Current URL:', currentUrl);
            
            // If we're redirected to login, session is invalid
            if (currentUrl.includes('/login')) {
                console.log('❌ [NICS24-CHECK] Session invalid - redirected to login');
                
                // Clean up invalid session
                if (fs.existsSync(sessionFile)) {
                    fs.unlinkSync(sessionFile);
                }
                
                await browser.close();
                return {
                    status: 'error',
                    code: 'SESSION_INVALID',
                    message: 'Session is no longer valid'
                };
            }
            
            // Check for user info or other indicators of successful login
            const userElement = await page.$('text=/14010570855|پیشخوان|اعتباریتو/');
            
            if (!userElement) {
                console.log('❌ [NICS24-CHECK] No user indicators found on page');
                await browser.close();
                return {
                    status: 'error',
                    code: 'SESSION_QUESTIONABLE',
                    message: 'Could not verify session validity'
                };
            }
            
            console.log('✅ [NICS24-CHECK] Session is valid');
            
            await browser.close();
            return {
                status: 'success',
                message: 'Session is valid',
                data: {
                    sessionFile: sessionFile,
                    createdAt: sessionData.createdAt,
                    sessionAge: Math.round(sessionAge / 1000 / 60), // minutes
                    provider: 'nics24',
                    user: user.username
                }
            };
            
        } catch (error) {
            console.error('❌ [NICS24-CHECK] Error checking session:', error.message);
            await browser.close();
            
            return {
                status: 'error',
                code: 'CHECK_FAILED',
                message: `Failed to verify session: ${error.message}`
            };
        }
        
    } catch (error) {
        console.error('❌ [NICS24-CHECK] Check login failed:', error.message);
        
        return {
            status: 'error',
            code: 'CHECK_ERROR',
            message: `Check login error: ${error.message}`
        };
    }
}