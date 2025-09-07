import { chromium } from 'playwright';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

// Get current directory for ES modules
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

/**
 * Checks if a valid session exists for a random user and verifies login status.
 * @returns {Promise<Object>} result object with status, code, and message
 */
async function checkLogin(user , mobile = null) {
  console.log('\n🔍 [RADE-CHECK] Starting login status check');
  console.log('👤 [RADE-CHECK] User:', { mobile: user.mobile, username: user.username });
  
  let browser;
  let context;
  let page;
  let result = {
    status: 'error',
    code: 500,
    message: 'Unknown error'
  };

  try {
    const storageStatePath = path.resolve(__dirname + '/sessions/' + `session-${mobile}.json`);
    console.log('📂 [RADE-CHECK] Session file path:', storageStatePath);

    if (!fs.existsSync(storageStatePath)) {
      console.log('❌ [RADE-CHECK] Session file does not exist');
      result = {
        status: 'error',
        code: 404,
        message: 'Session file does not exist'
      };
      return result;
    }

    console.log('✅ [RADE-CHECK] Session file exists, testing validity...');
    console.log('🌐 [RADE-CHECK] Launching browser...');
    browser = await chromium.launch({
      headless: true
    });
    console.log('✅ [RADE-CHECK] Browser launched successfully');

    console.log('📋 [RADE-CHECK] Loading session context...');
    context = await browser.newContext({ storageState: storageStatePath });
    page = await context.newPage();
    console.log('✅ [RADE-CHECK] Session context loaded');

    try {
      console.log('🌐 [RADE-CHECK] Navigating to main page...');
      await page.goto('https://my.rade.ir', { waitUntil: 'domcontentloaded', timeout: 60000 });
      console.log('✅ [RADE-CHECK] Page loaded successfully');
    } catch (err) {
      console.log('❌ [RADE-CHECK] Page navigation failed:', err.message);
      result = {
        status: 'error',
        code: 408,
        message: 'Timeout or network error during page load: ' + err.message
      };
      return result;
    }

    // Try to check for a known element that only appears when logged in
    try {
      console.log('🔍 [RADE-CHECK] Checking for logged-in user indicator...');
      await page.waitForSelector('app-profile-top-bar', { timeout: 20000 });
      console.log('✅ [RADE-CHECK] User indicator found - session is valid');
      result = {
        status: 'success',
        code: 200,
        message: 'Session is valid and user is logged in'
      };
    } catch (err) {
      console.log('❌ [RADE-CHECK] User indicator not found - session invalid:', err.message);
      result = {
        status: 'error',
        code: 401,
        message: 'Session invalid or user not logged in: ' + err.message
      };
    }

    console.log('📊 [RADE-CHECK] Final check result:', result);
    // Save the current storage state to persist session changes
    await context.storageState({ path: storageStatePath });
    return result;
  } catch (err) {
    console.log('💥 [RADE-CHECK] Unexpected error in check process:', err.message);
    console.error('📋 [RADE-CHECK] Full error stack:', err);
    result = {
      status: 'error',
      code: 500,
      message: 'Unexpected error: ' + err.message
    };
    return result;
  } finally {
    if (browser) {
      console.log('🧹 [RADE-CHECK] Closing browser...');
      await browser.close();
      console.log('✅ [RADE-CHECK] Browser closed');
    }
  }
}

export { checkLogin };
