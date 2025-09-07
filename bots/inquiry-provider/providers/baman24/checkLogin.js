import { chromium } from 'playwright';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

// Get current directory for ES modules
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

/**
 * Checks if a valid session exists for a user and verifies login status.
 * @returns {Promise<Object>} result object with status, code, and message
 */
async function checkLogin(user , mobile = null) {
  console.log('\n🔍 [BMN24-CHECK] Starting login status check');
  console.log('👤 [BMN24-CHECK] User:', { mobile: user.mobile, username: user.username });
  
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
    console.log('📂 [BMN24-CHECK] Session file path:', storageStatePath);

    if (!fs.existsSync(storageStatePath)) {
      console.log('❌ [BMN24-CHECK] Session file does not exist');
      result = {
        status: 'error',
        code: 404,
        message: 'Session file does not exist'
      };
      return result;
    }

    console.log('✅ [BMN24-CHECK] Session file exists, testing validity...');
    console.log('🌐 [BMN24-CHECK] Launching browser...');
    browser = await chromium.launch({
      headless: true
    });
    console.log('✅ [BMN24-CHECK] Browser launched successfully');

    console.log('📋 [BMN24-CHECK] Loading session context...');
    context = await browser.newContext({ storageState: storageStatePath });
    page = await context.newPage();
    console.log('✅ [BMN24-CHECK] Session context loaded');

    try {
      console.log('🌐 [BMN24-CHECK] Navigating to main page...');
      await page.goto('https://baman24.ir/pwa', { waitUntil: 'domcontentloaded', timeout: 60000 });
      console.log('✅ [BMN24-CHECK] Page loaded successfully');
    } catch (err) {
      console.log('❌ [BMN24-CHECK] Page navigation failed:', err.message);
      result = {
        status: 'error',
        code: 408,
        message: 'Timeout or network error during page load: ' + err.message
      };
      return result;
    }

    // Try to check for a known element that only appears when logged in
    try {
      console.log('🔍 [BMN24-CHECK] Checking for logged-in user indicator...');

      await page.waitForTimeout(3000);
      // screenshot
      await page.screenshot({ path: './images/screenshot-3.png' });
      await page.waitForSelector('div.homeSection', { timeout: 10000 });
      console.log('✅ [BMN24-CHECK] Home section found - session is valid');
      result = {
        status: 'success',
        code: 200,
        message: 'Session is valid and user is logged in'
      };
      await context.storageState({ path: storageStatePath });
    } catch (err) {
      console.log('❌ [BMN24-CHECK] User indicator not found - session invalid:', err.message);
      result = {
        status: 'error',
        code: 401,
        message: 'Session invalid or user not logged in: ' + err.message
      };
      await fs.unlinkSync(storageStatePath);
    }

    console.log('📊 [BMN24-CHECK] Final check result:', result);
    // Save the current storage state to persist session changes
    return result;
  } catch (err) {
    console.log('💥 [BMN24-CHECK] Unexpected error in check process:', err.message);
    console.error('📋 [BMN24-CHECK] Full error stack:', err);
    result = {
      status: 'error',
      code: 500,
      message: 'Unexpected error: ' + err.message
    };
    return result;
  } finally {
    if (browser) {
      console.log('🧹 [BMN24-CHECK] Closing browser...');
      await browser.close();
      console.log('✅ [BMN24-CHECK] Browser closed');
    }
  }
}

export { checkLogin };
