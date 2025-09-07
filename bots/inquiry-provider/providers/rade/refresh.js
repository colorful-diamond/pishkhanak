import { chromium } from 'playwright';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

// Get current directory for ES modules
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

/**
 * Refreshes the Playwright browser context for a random user.
 * If a session file exists, it loads the context and updates the session file.
 * If not, it returns an error.
 * @returns {Promise<Object>} result object with status, code, and message
 */
async function refresh(user , mobile = null) {
  console.log('\n🔄 [RADE-REFRESH] Starting session refresh');
  console.log('👤 [RADE-REFRESH] User:', { mobile: user.mobile, username: user.username });
  
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
    console.log('📂 [RADE-REFRESH] Session file path:', storageStatePath);

    if (!fs.existsSync(storageStatePath)) {
      console.log('❌ [RADE-REFRESH] Session file does not exist');
      result = {
        status: 'error',
        code: 404,
        message: 'Session file does not exist'
      };
      return result;
    }

    console.log('✅ [RADE-REFRESH] Session file exists, refreshing...');
    console.log('🌐 [RADE-REFRESH] Launching browser...');
    browser = await chromium.launch({
      headless: true
    });
    console.log('✅ [RADE-REFRESH] Browser launched successfully');

    console.log('📋 [RADE-REFRESH] Loading session context...');
    context = await browser.newContext({ storageState: storageStatePath });
    page = await context.newPage();
    console.log('✅ [RADE-REFRESH] Session context loaded');

    try {
      console.log('🌐 [RADE-REFRESH] Navigating to main page...');
      await page.goto('https://my.rade.ir', { waitUntil: 'networkidle', timeout: 60000 });
      console.log('✅ [RADE-REFRESH] Page loaded successfully');
    } catch (err) {
      console.log('❌ [RADE-REFRESH] Page navigation failed:', err.message);
      result = {
        status: 'error',
        code: 408,
        message: 'Timeout or network error during page load: ' + err.message
      };
      return result;
    }

    // Try to check for a known element that only appears when logged in
    try {
      console.log('🔍 [RADE-REFRESH] Checking session validity...');
      await page.waitForSelector('text=Seyed Ali Khoshdel', { state: 'visible', timeout: 15000 });
      console.log('✅ [RADE-REFRESH] Session is valid, saving refreshed state...');
      
      // Save the refreshed context/session
      await context.storageState({ path: storageStatePath });
      console.log('✅ [RADE-REFRESH] Refreshed session saved to:', storageStatePath);
      
      result = {
        status: 'success',
        code: 200,
        message: 'Session refreshed and saved successfully'
      };
      console.log('🎉 [RADE-REFRESH] Session refresh completed successfully');
    } catch (err) {
      console.log('❌ [RADE-REFRESH] Session validation failed:', err.message);
      result = {
        status: 'error',
        code: 401,
        message: 'Session invalid or user not logged in: ' + err.message
      };
    }

    console.log('📊 [RADE-REFRESH] Final refresh result:', result);
    return result;
  } catch (err) {
    console.log('💥 [RADE-REFRESH] Unexpected error in refresh process:', err.message);
    console.error('📋 [RADE-REFRESH] Full error stack:', err);
    result = {
      status: 'error',
      code: 500,
      message: 'Unexpected error: ' + err.message
    };
    return result;
  } finally {
    if (browser) {
      console.log('🧹 [RADE-REFRESH] Closing browser...');
      await browser.close();
      console.log('✅ [RADE-REFRESH] Browser closed');
    }
  }
}

export { refresh };
