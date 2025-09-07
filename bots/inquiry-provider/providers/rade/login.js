import { chromium } from 'playwright';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import { solveCaptchaComplete, isCaptchaPresent, isCaptchaError } from './captchaSolver.js';

// Get current directory for ES modules
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

/**
 * Logs in to my.rade.ir and saves/loads session for a random user.
 * @returns {Promise<Object>} result object with status, code, and message
 */
async function login(user , mobile = null) {
  console.log('\n🔑 [RADE-LOGIN] Starting login process');
  console.log('👤 [RADE-LOGIN] User:', { mobile: user.mobile, username: user.username });
  
  let browser;
  let context;
  let page;
  let result = {
    status: 'error',
    code: 500,
    message: 'Unknown error'
  };

  try {
    console.log('🌐 [RADE-LOGIN] Launching browser...');
    browser = await chromium.launch({
      headless: true
    });
    console.log('✅ [RADE-LOGIN] Browser launched successfully');

    const storageStatePath = path.resolve(__dirname + '/sessions/' + `session-${mobile}.json`);
    console.log('📂 [RADE-LOGIN] Session file path:', storageStatePath);

    if (fs.existsSync(storageStatePath)) {
      console.log('✅ [RADE-LOGIN] Existing session file found, loading...');
      // Load existing session
      context = await browser.newContext({ storageState: storageStatePath });
      page = await context.newPage();
      console.log('✅ [RADE-LOGIN] Session loaded successfully');
      result = {
        status: 'success',
        code: 200,
        message: 'Session loaded successfully'
      };
    } else {
      console.log('🆕 [RADE-LOGIN] No existing session, creating new login...');
      // No session, do login and save session
      context = await browser.newContext();
      page = await context.newPage();
      console.log('✅ [RADE-LOGIN] New browser context created');

      try {
        console.log('🌐 [RADE-LOGIN] Navigating to login page...');
        await page.goto('https://my.rade.ir?mobile=' + user.mobile, { waitUntil: 'domcontentloaded', timeout: 30000 });
        console.log('✅ [RADE-LOGIN] Login page loaded successfully');
      } catch (err) {
        console.log('❌ [RADE-LOGIN] Page navigation failed:', err.message);
        result = {
          status: 'error',
          code: 408,
          message: 'Timeout or network error during page load: ' + err.message
        };
        return result;
      }

      console.log('🔍 [RADE-LOGIN] Looking for top bar element...');
      const topBar = page.locator('#top-bar');
      await page.waitForSelector('#top-bar', { state: 'visible', timeout: 30000 });
      try {
        if (await topBar.getByRole('img').isVisible()) {
          console.log('✅ [RADE-LOGIN] Top bar login button found, clicking...');
          // click on login button
          await topBar.getByRole('img').click();
          console.log('✅ [RADE-LOGIN] Login button clicked successfully');
        } else {
          console.log('❌ [RADE-LOGIN] Top bar is not visible');
          result = {
            status: 'error',
            code: 404,
            message: 'Top bar is not visible'
          };
          return result;
        }
      } catch (err) {
        console.log('❌ [RADE-LOGIN] Error interacting with top bar:', err.message);
        result = {
          status: 'error',
          code: 500,
          message: 'Error interacting with top bar: ' + err.message
        };
        return result;
      }

      try {
        
        console.log('⏰ [RADE-LOGIN] Waiting for password field...');
        await page.waitForSelector('input[name="password"]', { state: 'visible', timeout: 30000 });
        console.log('✅ [RADE-LOGIN] Password field found');
        
        // fill password
        console.log('🔐 [RADE-LOGIN] Filling password...');
        await page.locator('input[name="password"]').fill(user.password);
        console.log('✅ [RADE-LOGIN] Password filled');
        
        // click on login button
        console.log('🚀 [RADE-LOGIN] Clicking login button...');
        await page.getByRole('dialog').getByRole('button', { name: 'ورود' }).click();
        console.log('✅ [RADE-LOGIN] Login button clicked');
        
        // wait for 3 seconds
        console.log('⏰ [RADE-LOGIN] Waiting for login to process...');
        await page.waitForTimeout(3000);

        // Check for captcha after login attempt
        console.log('🔍 [RADE-LOGIN] Checking if captcha appeared after login...');
        const hasCaptcha = await isCaptchaPresent(page);
        
        if (hasCaptcha) {
          console.log('🤖 [RADE-LOGIN] Captcha detected! Starting captcha solving process...');
          
          // Handle captcha solving with retry logic
          const maxCaptchaRetries = 3;
          let captchaRetryCount = 0;
          let captchaSolved = false;
          
          while (captchaRetryCount < maxCaptchaRetries && !captchaSolved) {
            console.log(`🔄 [RADE-LOGIN] Captcha solving attempt ${captchaRetryCount + 1}/${maxCaptchaRetries}`);
            
            // Solve the captcha
            const captchaSolution = await solveCaptchaComplete(page, user.mobile, 'login');
            
            if (captchaSolution.status === 'success') {
              console.log('✅ [RADE-LOGIN] Captcha solved successfully:', captchaSolution.text);
              
              // Fill the captcha input field
              console.log('📝 [RADE-LOGIN] Filling captcha input field...');
              await page.locator('input[name="captcha"]').fill(captchaSolution.text);
              console.log('✅ [RADE-LOGIN] Captcha text filled');
              
              // Submit the form (click login button again)
              console.log('🚀 [RADE-LOGIN] Clicking login button with captcha...');
              await page.getByRole('dialog').getByRole('button', { name: 'ورود' }).click();
              console.log('✅ [RADE-LOGIN] Login button clicked with captcha');
              
              // Wait for response
              console.log('⏰ [RADE-LOGIN] Waiting for captcha validation response...');

              
              // Check for captcha error
              const hasCaptchaError = await isCaptchaError(page);
              
              if (hasCaptchaError) {
                console.log('❌ [RADE-LOGIN] Captcha was incorrect, retrying...');
                captchaRetryCount++;
              } else {
                console.log('✅ [RADE-LOGIN] Captcha solved successfully!');
                captchaSolved = true;
              }
              
            } else {
              console.log('❌ [RADE-LOGIN] Failed to solve captcha:', captchaSolution.message);
              captchaRetryCount++;
            }
          }
          
          if (!captchaSolved) {
            console.log('🚫 [RADE-LOGIN] Failed to solve captcha after maximum retries');
            result = {
              status: 'error',
              code: 401,
              message: `Failed to solve captcha after ${maxCaptchaRetries} attempts`
            };
            return result;
          }
        } else {
          console.log('✅ [RADE-LOGIN] No captcha detected after login');
        }

        console.log('🔍 [RADE-LOGIN] Waiting for successful login indicator...');
        console.log('🔍 [RADE-LOGIN] Current page URL:', await page.url());
        // Wait for the <app-profile-top-bar> element to appear, indicating successful login/dashboard load
        console.log('⏳ [RADE-LOGIN] Waiting for dashboard top bar (app-profile-top-bar) to appear...');
        await page.waitForSelector('app-profile-top-bar', { timeout: 20000 });
        console.log('✅ [RADE-LOGIN] Dashboard top bar detected!');
        console.log('✅ [RADE-LOGIN] Login successful, user dashboard loaded');
        
        console.log('💾 [RADE-LOGIN] Saving session state...');
        await context.storageState({ path: storageStatePath });
        console.log('✅ [RADE-LOGIN] Session saved to:', storageStatePath);

        result = {
          status: 'success',
          code: 200,
          message: 'Login successful and session saved'
        };
        console.log('🎉 [RADE-LOGIN] Login process completed successfully');
      } catch (err) {
        console.log('❌ [RADE-LOGIN] Login process failed:', err.message);
        result = {
          status: 'error',
          code: 401,
          message: 'Login failed or selector not found: ' + err.message
        };
        return result;
      }
    }

    console.log('📊 [RADE-LOGIN] Final result:', result);
    return result;
  } catch (err) {
    console.log('💥 [RADE-LOGIN] Unexpected error in login process:', err.message);
    console.error('📋 [RADE-LOGIN] Full error stack:', err);
    result = {
      status: 'error',
      code: 500,
      message: 'Unexpected error: ' + err.message
    };
    return result;
  } finally {
    if (browser) {
      console.log('🧹 [RADE-LOGIN] Closing browser...');
      await browser.close();
      console.log('✅ [RADE-LOGIN] Browser closed');
    }
  }
}

export { login };