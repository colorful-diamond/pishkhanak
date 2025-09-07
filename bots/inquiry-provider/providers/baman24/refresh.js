import { chromium } from 'playwright';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

// Get current directory for ES modules
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

/**
 * Refreshes the Playwright browser context for a baman24 user.
 * If a session file exists, it loads the context and updates the session file.
 * If not, it returns an error.
 * @returns {Promise<Object>} result object with status, code, and message
 */
async function refresh(user , mobile = null) {
  console.log('\n🔄 [BMN24-REFRESH] Starting session refresh');
  console.log('👤 [BMN24-REFRESH] User:', { mobile: user.mobile, username: user.username });
  
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
    console.log('📂 [BMN24-REFRESH] Session file path:', storageStatePath);

    if (!fs.existsSync(storageStatePath)) {
      console.log('❌ [BMN24-REFRESH] Session file does not exist');
      result = {
        status: 'error',
        code: 404,
        message: 'Session file does not exist'
      };
      return result;
    }

    console.log('✅ [BMN24-REFRESH] Session file exists, refreshing...');
    console.log('🌐 [BMN24-REFRESH] Launching browser...');
    browser = await chromium.launch({
      headless: true
    });
    console.log('✅ [BMN24-REFRESH] Browser launched successfully');

    console.log('📋 [BMN24-REFRESH] Loading session context...');
    context = await browser.newContext({ storageState: storageStatePath });
    page = await context.newPage();
    console.log('✅ [BMN24-REFRESH] Session context loaded');

    try {
      console.log('🌐 [BMN24-REFRESH] Navigating to main page...');
      await page.goto('https://baman24.ir/pwa', { waitUntil: 'domcontentloaded', timeout: 60000 });
      console.log('✅ [BMN24-REFRESH] Page loaded successfully');
    } catch (err) {
      console.log('❌ [BMN24-REFRESH] Page navigation failed:', err.message);
      result = {
        status: 'error',
        code: 408,
        message: 'Timeout or network error during page load: ' + err.message
      };
      return result;
    }

    // Try to check for a known element that only appears when logged in
    try {
      console.log('🔍 [BMN24-REFRESH] Checking session validity...');
      await page.waitForSelector('div.homeSection', { timeout: 20000 });
      console.log('✅ [BMN24-REFRESH] Home section found - session is valid, saving refreshed state...');
      
      // Save the refreshed context/session
      await context.storageState({ path: storageStatePath });
      console.log('✅ [BMN24-REFRESH] Refreshed session saved to:', storageStatePath);
      
      result = {
        status: 'success',
        code: 200,
        message: 'Session refreshed and saved successfully'
      };
      console.log('🎉 [BMN24-REFRESH] Session refresh completed successfully');
    } catch (err) {
      console.log('❌ [BMN24-REFRESH] Session validation failed:', err.message);
      result = {
        status: 'error',
        code: 401,
        message: 'Session invalid or user not logged in: ' + err.message
      };
    }

    console.log('📊 [BMN24-REFRESH] Final refresh result:', result);
    return result;
  } catch (err) {
    console.log('💥 [BMN24-REFRESH] Unexpected error in refresh process:', err.message);
    console.error('📋 [BMN24-REFRESH] Full error stack:', err);
    result = {
      status: 'error',
      code: 500,
      message: 'Unexpected error: ' + err.message
    };
    return result;
  } finally {
    if (browser) {
      console.log('🧹 [BMN24-REFRESH] Closing browser...');
      await browser.close();
      console.log('✅ [BMN24-REFRESH] Browser closed');
    }
  }
}

export { refresh };
