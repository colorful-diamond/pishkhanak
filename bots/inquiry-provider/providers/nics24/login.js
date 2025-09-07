/**
 * NICS24 Login Handler
 * Handles authentication with captcha solving
 */

import { chromium } from 'playwright';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import { 
    getRandomUserAgent, 
    getStealthLaunchArgs, 
    getStealthContextConfig, 
    setupStealthMode, 
    addRandomMouseMovements, 
    humanType, 
    humanSubmit, 
    humanDelay 
} from '../../utils/stealthUtils.js';

// Get current directory for ES modules
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

/**
 * Solve captcha using local API
 */
async function solveCaptcha(captchaBase64) {
    try {
        console.log('🔍 [NICS24-LOGIN] Solving captcha...');
        
        const response = await fetch('http://localhost:9090/predict', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                image: captchaBase64
            })
        });

        const result = await response.json();
        if (!response.ok) {
            throw new Error(`Captcha API returned ${response.status}`);
        }
        console.log('✅ [NICS24-LOGIN] Captcha solved successfully');
        
        return result.predicted_text;
    } catch (error) {
        console.error('❌ [NICS24-LOGIN] Captcha solving failed:', error.message);
        throw error;
    }
}



/**
 * Get authenticated page with session management
 */
async function getAuthenticatedPage(browser, mobile = null) {
    console.log('🌐 [NICS24-LOGIN] Creating new browser page...');
    
    const context = await browser.newContext(getStealthContextConfig(null, null, true));
    const page = await context.newPage();
    
    // Apply stealth mode
    await setupStealthMode(page);

    try {
        console.log('🔗 [NICS24-LOGIN] Navigating to login page...');
        await page.goto('https://etebarito.nics24.ir/login-username', {
            waitUntil: 'networkidle',
            timeout: 120000
        });

        console.log('✅ [NICS24-LOGIN] Successfully loaded login page');

        // Wait for the form to be ready
        await page.waitForSelector('form', { timeout: 10000 });
        console.log('📝 [NICS24-LOGIN] Login form detected');
        
        // Add some random mouse movements and human-like delays after page load
        await addRandomMouseMovements(page);
        await humanDelay(page, 500, 1500);

        return page;

    } catch (error) {
        console.error('❌ [NICS24-LOGIN] Failed to get authenticated page:', error.message);
        await context.close();
        throw error;
    }
}

/**
 * Perform login with national code and captcha
 */
async function performLogin(page, userName, password) {
    try {
        console.log('🔑 [NICS24-LOGIN] Starting login process...');

        // Fill national code with human-like typing
        console.log('📝 [NICS24-LOGIN] Filling user data...');
        

        // save page html in .html file
        // Save page HTML in .html file
        const htmlDir = 'screenshots';
        if (!fs.existsSync(htmlDir)) {
            fs.mkdirSync(htmlDir, { recursive: true });
        }
        const htmlPath = `${htmlDir}/login-page.html`;
        const pageContent = await page.content();
        fs.writeFileSync(htmlPath, pageContent, 'utf8');

        // save page screenshot
        await page.screenshot({ path: 'screenshots/login-page.png' });

        // Type username with human-like behavior using the first input of .MuiFormControl-root
        await humanType(page, 'input[type="text"]', userName, { delay: 50, variance: 50 });
        
        // Wait before moving to password
        await humanDelay(page, 300, 800);
        
        // Type password with human-like behavior using the second input of .MuiFormControl-root
        await humanType(page, 'input[type="password"]', password, { delay: 40, variance: 40 });

        // Get captcha
        console.log('🖼️ [NICS24-LOGIN] Getting captcha...');
        
        // Wait for captcha image to load
        await page.waitForSelector('img[alt="captcha"]', { timeout: 10000 });
        
        // Get captcha image source
        const captchaSrc = await page.getAttribute('img[alt="captcha"]', 'src');
        
        if (captchaSrc.startsWith('blob:')) {
            // Handle blob URL captcha
            const captchaBase64 = await page.evaluate(async (blobUrl) => {
                const response = await fetch(blobUrl);
                const blob = await response.blob();
                return new Promise((resolve) => {
                    const reader = new FileReader();
                    reader.onloadend = () => {
                        const base64 = reader.result.split(',')[1];
                        resolve(base64);
                    };
                    reader.readAsDataURL(blob);
                });
            }, captchaSrc);

            // Solve captcha
            const captchaText = await solveCaptcha(captchaBase64);
            console.log('🔍 [NICS24-LOGIN] Captcha solved:', captchaText);

            // Fill captcha with human-like behavior
            await humanDelay(page, 500, 1500);
            await humanType(page, 'input[name="captcha"]', captchaText, { delay: 80, variance: 80 });

        } else {
            throw new Error('Unexpected captcha format');
        }

        // Submit form with human-like behavior
        console.log('📤 [NICS24-LOGIN] Submitting login form...');
        await humanSubmit(page, 'button[type="submit"]');

        // Wait for navigation or response
        try {
            // Wait for URL to change after form submission (login redirect)
            const initialUrl = page.url();
            await humanDelay(page, 6000, 10000);
            console.log('🔍 [NICS24-LOGIN] URL changed :', page.url());
            console.log('✅ [NICS24-LOGIN] Login form submitted and URL changed');
            
            await page.screenshot({ path: 'screenshots/login-page-after.png' });


            await humanDelay(page, 6000, 10000);
            
            // save page screenshot
            await page.screenshot({ path: 'screenshots/login-page-after2.png' });

            // Check if we're on the expected page after login
            const currentUrl = page.url();
            console.log('🔗 [NICS24-LOGIN] Current URL after login:', currentUrl);
            
            // Check for successful login indicators
            if (currentUrl.includes('/pishkhan') || 
                currentUrl.includes('/inquiry') || 
                currentUrl.includes('/dashboard') ||
                currentUrl.includes('/panel')) {
                console.log('✅ [NICS24-LOGIN] Login successful - redirected to authenticated area');
                return { success: true, page };
            } else if (currentUrl.includes('/login')) {
                // Check if there are error messages on login page
                const errorElements = await page.$$('.error, .alert-danger, [class*="error"], .text-danger');
                if (errorElements.length > 0) {
                    const errorText = await errorElements[0].textContent();
                    console.log('❌ [NICS24-LOGIN] Login failed with error:', errorText);
                    return { success: false, error: `Login failed: ${errorText}` };
                }
                console.log('❌ [NICS24-LOGIN] Still on login page - login may have failed');
                return { success: false, error: 'Login failed - still on login page' };
            } else {
                // Check page content for login success indicators
                const pageTitle = await page.title();
                console.log('📄 [NICS24-LOGIN] Page title:', pageTitle);
                
                // Look for authenticated user indicators
                const userIndicators = await page.$$('.user-info, .username, [class*="user"], .profile-menu');
                if (userIndicators.length > 0) {
                    console.log('✅ [NICS24-LOGIN] Login successful - user elements found');
                    return { success: true, page };
                }
                
                console.log('✅ [NICS24-LOGIN] Login appears successful - on new page');
                return { success: true, page };
            }
            
        } catch (error) {
            console.log('⚠️ [NICS24-LOGIN] Timeout waiting for navigation, checking current page state...');
            
            const currentUrl = page.url();
            console.log('🔗 [NICS24-LOGIN] Current URL after timeout:', currentUrl);
            
            // Check for error messages
            const errorElements = await page.$$('.error, .alert-danger, [class*="error"], .text-danger');
            if (errorElements.length > 0) {
                const errorText = await errorElements[0].textContent();
                console.log('❌ [NICS24-LOGIN] Error message found:', errorText);
                return { success: false, error: errorText };
            }
            
            // Check if we're still on login page
            if (currentUrl.includes('/login')) {
                console.log('❌ [NICS24-LOGIN] Still on login page after timeout');
                return { success: false, error: 'Login timeout - still on login page' };
            }
            
            // Check for success indicators even after timeout
            const pageTitle = await page.title();
            console.log('📄 [NICS24-LOGIN] Page title after timeout:', pageTitle);
            
            if (currentUrl.includes('/pishkhan') || 
                currentUrl.includes('/inquiry') || 
                currentUrl.includes('/dashboard')) {
                console.log('✅ [NICS24-LOGIN] Login successful (detected after timeout)');
                return { success: true, page };
            }
            
            console.log('✅ [NICS24-LOGIN] Assuming login successful - proceeding');
            return { success: true, page };
        }

    } catch (error) {
        console.error('❌ [NICS24-LOGIN] Login process failed:', error.message);
        return { success: false, error: error.message };
    }
}

/**
 * Save session to file for reuse
 */
async function saveSession(page, user) {
    try {
        const sessionDir = path.resolve(__dirname, 'sessions');
        
        // Create sessions directory if it doesn't exist
        if (!fs.existsSync(sessionDir)) {
            fs.mkdirSync(sessionDir, { recursive: true });
        }
        
        // Get storage state (cookies, localStorage, etc.)
        const storageState = await page.context().storageState();
        
        const sessionData = {
            storageState: storageState,
            createdAt: new Date().toISOString(),
            user: user.username,
            provider: 'nics24'
        };
        
        const sessionFile = path.join(sessionDir, `${user.username}_session.json`);
        fs.writeFileSync(sessionFile, JSON.stringify(sessionData, null, 2));
        
        console.log('💾 [NICS24-LOGIN] Session saved successfully');
        return sessionFile;
        
    } catch (error) {
        console.error('❌ [NICS24-LOGIN] Failed to save session:', error.message);
        return null;
    }
}

/**
 * Load existing session if available
 */
async function loadSession(browser, user) {
    try {
        const sessionDir = path.resolve(__dirname, 'sessions');
        const sessionFile = path.join(sessionDir, `${user.username}_session.json`);
        
        if (!fs.existsSync(sessionFile)) {
            console.log('📂 [NICS24-LOGIN] No existing session found');
            return null;
        }
        
        const sessionData = JSON.parse(fs.readFileSync(sessionFile, 'utf8'));
        
        // Check if session is too old (more than 2 hours)
        const sessionAge = Date.now() - new Date(sessionData.createdAt).getTime();
        const maxAge = 2 * 60 * 60 * 1000; // 2 hours
        
        if (sessionAge > maxAge) {
            console.log('⏰ [NICS24-LOGIN] Existing session is too old, deleting');
            fs.unlinkSync(sessionFile);
            return null;
        }
        
        // Create context with saved session
        const contextConfig = getStealthContextConfig(null, null, true);
        contextConfig.storageState = sessionData.storageState;
        
        const context = await browser.newContext(contextConfig);
        const page = await context.newPage();
        
        // Apply stealth mode
        await setupStealthMode(page);
        
        console.log('✅ [NICS24-LOGIN] Successfully loaded existing session');
        return page;
        
    } catch (error) {
        console.error('❌ [NICS24-LOGIN] Failed to load session:', error.message);
        return null;
    }
}

/**
 * Main login function
 */
export async function login(user, mobile = null) {
    console.log('🚀 [NICS24-LOGIN] Starting NICS24 login process...');

    const browser = await chromium.launch({ 
        headless: true,
        args: getStealthLaunchArgs(getRandomUserAgent())
    });

    try {
        // First try to load existing session
        console.log('🔍 [NICS24-LOGIN] Checking for existing session...');
        let page = await loadSession(browser, user);
        
        if (page) {
            console.log('✅ [NICS24-LOGIN] Using existing session');
            return {
                status: 'success',
                message: 'NICS24 session loaded from cache',
                data: {
                    page: page,
                    browser: browser,
                    provider: 'nics24',
                    fromCache: true
                }
            };
        }
        
        // If no valid session, perform fresh login
        console.log('🔑 [NICS24-LOGIN] No valid session found, performing fresh login...');
        page = await getAuthenticatedPage(browser, mobile);
        
        // Perform actual login with username and password
        const loginResult = await loginWithUserName(page, user.username, user.password);
        
        if (!loginResult.success) {
            throw new Error(loginResult.error || 'Login failed');
        }
        
        // Save session for future use
        const sessionFile = await saveSession(page, user);
        
        console.log('✅ [NICS24-LOGIN] Fresh login successful');
        
        return {
            status: 'success',
            message: 'NICS24 login successful',
            data: {
                page: page,
                browser: browser,
                provider: 'nics24',
                fromCache: false,
                sessionFile: sessionFile
            }
        };

    } catch (error) {
        console.error('❌ [NICS24-LOGIN] Login failed:', error.message);
        await browser.close();
        
        return {
            status: 'error',
            code: 'LOGIN_FAILED',
            message: `NICS24 login failed: ${error.message}`
        };
    }
}

/**
 * Login with national code (for credit score requests)
 */
export async function loginWithUserName(page, userName, password) {
    return await performLogin(page, userName, password);
}