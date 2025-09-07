import { chromium } from 'playwright';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

// Get current directory for ES modules
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

/**
 * Logs in to my.rade.ir and saves/loads session for a random user.
 * @returns {Promise<Object>} result object with status, code, and message
 */
async function login(user , mobile = null) {
  console.log('\n🔑 [BMN24-LOGIN] Starting login process');
  console.log('👤 [BMN24-LOGIN] User:', { mobile: user.mobile, username: user.username });
  
  let browser;
  let context;
  let page;
  let result = {
    status: 'error',
    code: 500,
    message: 'Unknown error'
  };

  try {
    console.log('🌐 [BMN24-LOGIN] Launching browser...');
    browser = await chromium.launch({
      headless: true
    });
    console.log('✅ [BMN24-LOGIN] Browser launched successfully');

    const storageStatePath = path.resolve(__dirname + '/sessions/' + `session-${user.mobile}.json`);
    console.log('📂 [BMN24-LOGIN] Session file path:', storageStatePath);

    if (fs.existsSync(storageStatePath)) {
      console.log('✅ [BMN24-LOGIN] Existing session file found, loading...');
      // Load existing session
      context = await browser.newContext({ storageState: storageStatePath });
      page = await context.newPage();
      console.log('✅ [BMN24-LOGIN] Session loaded successfully');
      result = {
        status: 'success',
        code: 200,
        message: 'Session loaded successfully'
      };
    } else {
      console.log('🆕 [BMN24-LOGIN] No existing session, creating new login...');
      // No session, do login and save session
      context = await browser.newContext();
      page = await context.newPage();
      console.log('✅ [BMN24-LOGIN] New browser context created');

      try {
        console.log('🌐 [BMN24-LOGIN] Navigating to login page...');
        await page.goto('https://baman24.ir/pwa/auth/login', { waitUntil: 'domcontentloaded', timeout: 30000 });
        console.log('✅ [BMN24-LOGIN] Login page loaded successfully');
      } catch (err) {
        console.log('❌ [BMN24-LOGIN] Page navigation failed:', err.message);
        result = {
          status: 'error',
          code: 408,
          message: 'Timeout or network error during page load: ' + err.message
        };
        return result;
      }

      console.log('🔍 [BMN24-LOGIN] Looking for Login Mobile Field...');
      const mobileInput = page.locator('#id_fromName_Mobile');
      await page.waitForSelector('#id_fromName_Mobile', { state: 'visible', timeout: 5000 });
      try {
        if (await mobileInput.isVisible()) {
          console.log('✅ [BMN24-LOGIN] Mobile input field found, filling...');
          // click on login button
          await mobileInput.fill(user.mobile);
          console.log('✅ [BMN24-LOGIN] Login button clicked successfully');
        } else {
          console.log('❌ [BMN24-LOGIN] Top bar is not visible');
          result = {
            status: 'error',
            code: 404,
            message: 'Top bar is not visible'
          };
          return result;
        }
      } catch (err) {
        console.log('❌ [BMN24-LOGIN] Error interacting with top bar:', err.message);
        result = {
          status: 'error',
          code: 500,
          message: 'Error interacting with top bar: ' + err.message
        };
        return result;
      }

      try {
        
        // Click the "ارسال کد" (Send Code) button
        console.log('📱 [BMN24-LOGIN] Looking for "ارسال کد" (Send Code) button...');
        try {
          await page.locator('button[type="submit"].submitButton').click();
          console.log('✅ [BMN24-LOGIN] "ارسال کد" button clicked successfully');
        } catch (clickError) {
          console.log('❌ [BMN24-LOGIN] Error clicking "ارسال کد" button:', clickError.message);
          // Try alternative selector
          try {
            await page.getByRole('button', { name: 'ارسال کد' }).click();
            console.log('✅ [BMN24-LOGIN] "ارسال کد" button clicked using alternative selector');
          } catch (altError) {
            console.log('❌ [BMN24-LOGIN] Failed to click "ارسال کد" button with both selectors:', altError.message);
          }
        }

        console.log('👁️ [BMN24-LOGIN] Setting up file watcher for sms monitoring...');
        
        const sessionFilePath = path.join(__dirname, 'otps', `${user.mobile}.txt`);
        if (!fs.existsSync(sessionFilePath)) {
          fs.writeFileSync(sessionFilePath, '');
        }
        console.log('📂 [BMN24-LOGIN] Monitoring session file:', sessionFilePath);
        
        let fileWatcher;
        let otp = '';
        try {
          fileWatcher = fs.watchFile(sessionFilePath, { interval: 1000 }, (curr, prev) => {
            if (curr.mtime !== prev.mtime) {
              console.log('🔄 [BMN24-LOGIN] Session file changed, reading updated data...');
              try {
                const updatedData = fs.readFileSync(sessionFilePath, 'utf8');
                console.log('📋 [BMN24-LOGIN] Updated session data loaded:', updatedData);
                otp = updatedData;
              } catch (readError) {
                console.log('❌ [BMN24-LOGIN] Error reading updated session file:', readError.message);
              }
            }
          });
          console.log('✅ [BMN24-LOGIN] File watcher setup completed');
          
          // Wait for 60 seconds for SMS to arrive
          console.log('⏰ [BMN24-LOGIN] Waiting 60 seconds for SMS to arrive...');
          const startTime = Date.now();
          const timeout = 60000; // 60 seconds
          
          while (Date.now() - startTime < timeout && (!otp || otp.trim().length !== 4)) {
            await new Promise(resolve => setTimeout(resolve, 1000)); // Wait 1 second before checking again
            if (otp && otp.trim().length === 4) {
              console.log('📱 [BMN24-LOGIN] Valid OTP received:', otp);
              break;
            }
          }
          
          if (!otp || otp.trim().length !== 4) {
            console.log('⏰ [BMN24-LOGIN] 60-second timeout reached without receiving valid OTP');
          }
          
        } catch (watchError) {
          console.log('⚠️ [BMN24-LOGIN] Could not setup file watcher:', watchError.message);
        }
        
        if (otp && otp.trim().length === 4) {
          console.log('📝 [BMN24-LOGIN] OTP received, filling in the form fields...');
          
          // Fill OTP in the 4 separate fields
          for (let i = 0; i < 4; i++) {
            const fieldName = `number${i + 1}`;
            const fieldId = `id_fromName_number${i + 1}`;
            console.log(`📝 [BMN24-LOGIN] Filling field ${fieldName} with digit: ${otp[i]}`);
            
            try {
              await page.locator(`input[name="${fieldName}"]`).fill(otp[i]);
              console.log(`✅ [BMN24-LOGIN] Successfully filled field ${fieldName}`);
            } catch (fillError) {
              console.log(`❌ [BMN24-LOGIN] Error filling field ${fieldName}:`, fillError.message);
              // Try alternative selector with ID
              try {
                await page.locator(`input[id="${fieldId}"]`).fill(otp[i]);
                console.log(`✅ [BMN24-LOGIN] Successfully filled field ${fieldName} using ID selector`);
              } catch (altFillError) {
                console.log(`❌ [BMN24-LOGIN] Failed to fill field ${fieldName} with both selectors:`, altFillError.message);
              }
            }
          }
          
          console.log('✅ [BMN24-LOGIN] All OTP digits filled successfully');
          
          // Focus on the last field and press Enter to submit
          try {
            await page.locator('input[name="number4"]').focus();
            await page.keyboard.press('Enter');
            console.log('✅ [BMN24-LOGIN] OTP form submitted');
            await page.waitForTimeout(2000);
          } catch (submitError) {
            console.log('❌ [BMN24-LOGIN] Error submitting OTP form:', submitError.message);
          }
          
        } else if (!otp) {
          console.log('❌ [BMN24-LOGIN] OTP not received within timeout period');
        } else {
          console.log('❌ [BMN24-LOGIN] Invalid OTP format - must be 4 digits, received:', otp);
        }
        
        // Clean up file watcher
        if (fileWatcher) {
          fs.unwatchFile(sessionFilePath);
          console.log('🧹 [BMN24-LOGIN] File watcher cleaned up');
        }

        console.log('🔍 [BMN24-LOGIN] Waiting for successful login indicator...');
        console.log('🔍 [BMN24-LOGIN] Current page URL:', await page.url());
        // Wait for the <app-profile-top-bar> element to appear, indicating successful login/dashboard load
        console.log('⏳ [BMN24-LOGIN] Waiting for dashboard top bar (app-profile-top-bar) to appear...');
        await page.waitForSelector('div.homeSection', { timeout: 20000 });
        console.log('✅ [BMN24-LOGIN] Home section detected!');
        console.log('✅ [BMN24-LOGIN] Login successful, user dashboard loaded');
        
        console.log('💾 [BMN24-LOGIN] Saving session state...');
        await context.storageState({ path: storageStatePath });
        console.log('✅ [BMN24-LOGIN] Session saved to:', storageStatePath);

        result = {
          status: 'success',
          code: 200,
          message: 'Login successful and session saved'
        };
        console.log('🎉 [BMN24-LOGIN] Login process completed successfully');
      } catch (err) {
        console.log('❌ [BMN24-LOGIN] Login process failed:', err.message);
        result = {
          status: 'error',
          code: 401,
          message: 'Login failed or selector not found: ' + err.message
        };
        return result;
      }
    }

    console.log('📊 [BMN24-LOGIN] Final result:', result);
    return result;
  } catch (err) {
    console.log('💥 [BMN24-LOGIN] Unexpected error in login process:', err.message);
    console.error('📋 [BMN24-LOGIN] Full error stack:', err);
    result = {
      status: 'error',
      code: 500,
      message: 'Unexpected error: ' + err.message
    };
    return result;
  } finally {
    if (browser) {
      console.log('🧹 [BMN24-LOGIN] Closing browser...');
      await browser.close();
      console.log('✅ [BMN24-LOGIN] Browser closed');
    }
  }
}

export { login };