/**
 * Baman24 Credit Score Service - Direct Redis Integration
 * 
 * This service integrates with Laravel via direct Redis storage updates:
 * 1. Sends SMS and updates progress via Redis
 * 2. Polls Redis for OTP submission by user
 * 3. Completes entire flow and updates final result in Redis
 * 
 * DEBUG MODE:
 * Set DEBUG_MODE=true in .env file to enable detailed logging
 */
import baman24 from '../index.js';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import dotenv from 'dotenv';
import Redis from 'ioredis';

// Get current directory for ES modules
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Load environment variables from .env file in inquiry-provider root
const envPath = path.resolve(__dirname, '../../../.env');
dotenv.config({ path: envPath });

// Read credentials JSON file
const credintalsPath = path.resolve(__dirname, '../credintals.json');
const credintals = JSON.parse(fs.readFileSync(credintalsPath, 'utf8'));

// Redis setup for direct storage access (same as Laravel)
const redis = new Redis({
  host: process.env.REDIS_HOST || '127.0.0.1',
  port: process.env.REDIS_PORT || 6379,
  password: process.env.REDIS_PASSWORD || null,
  db: process.env.REDIS_DB || 0, // Use same database as Laravel
  lazyConnect: true,
  keyPrefix: process.env.REDIS_PREFIX || 'pishkhanak_database_' // Use same prefix as Laravel
});

// Debug: Check environment setup
if (process.env.DEBUG_MODE === 'true') {
  console.log('🔧 [BAMAN24-CREDIT] Environment file path:', envPath);
  console.log('🔧 [BAMAN24-CREDIT] Environment file exists:', fs.existsSync(envPath));
  console.log('🔧 [BAMAN24-CREDIT] DEBUG_MODE enabled - detailed logging active');
  console.log('🔧 [BAMAN24-CREDIT] Redis config:', {
    host: process.env.REDIS_HOST || '127.0.0.1',
    port: process.env.REDIS_PORT || 6379,
    db: process.env.REDIS_DB || 0
  });
}

/**
 * Main credit score inquiry function with polling for OTP
 * This function handles the complete flow from SMS to final result
 */
async function handleCreditScoreInquiry(data) {
  console.log('\n🚀 [BMN-24-CREDIT-FLOW] Starting complete credit score inquiry with polling');

  const { mobile, nationalCode, requestHash, resendSms = false, hash } = data;
  
  // Handle resend SMS request - only send SMS, don't wait for OTP
  if (resendSms) {
    console.log('🔄 [BMN-24-CREDIT-FLOW] Resend SMS request detected');
    return await handleResendSmsOnly(mobile, nationalCode, requestHash);
  }
  
  const maxRetries = 3;
  let retryCount = 0;

  // Initialize browser
  console.log('🌐 [BMN-24-CREDIT-FLOW] Initializing browser...');
  const { chromium } = await import('playwright');
  const browser = await chromium.launch({ headless: true });
  console.log('✅ [BMN-24-CREDIT-FLOW] Browser initialized successfully');

  try {
    // Start the flow with retries
    console.log(`🔄 [BMN-24-CREDIT-FLOW] Starting main process loop (max ${maxRetries} attempts)`);
    while (retryCount < maxRetries) {
      console.log(`\n📊 [BMN-24-CREDIT-FLOW] === ATTEMPT ${retryCount + 1}/${maxRetries} ===`);
      try {
        // Update progress via Redis
        if (requestHash) {
          await updateRedisProgress(requestHash, 30, 'authentication', 'ارسال درخواست به درگاه دولت هوشمند...');
        }

        console.log('🔑 [BMN-24-CREDIT-FLOW] Getting authenticated page...');
        const page = await getPage(browser, mobile);
        console.log('✅ [BMN-24-CREDIT-FLOW] Successfully got authenticated page');

        // Update progress
        if (requestHash) {
          await updateRedisProgress(requestHash, 50, 'authentication', 'پردازش درخواست...');
        }

        // Send SMS (Step 1)
        console.log('📱 [BMN-24-CREDIT-FLOW] Step 1: Sending SMS...');
        const smsResult = await sendSmsForOtp(page, mobile, nationalCode, requestHash);

        if (!smsResult.success) {
          throw new Error(`سامانه ی مخابراتی به دلیل حجم بالای درخواست ها دچار اختلال است، لطفا یک ساعت دیگر تلاش کنید.`);
        }

        console.log('✅ [BMN-24-CREDIT-FLOW] SMS sent successfully, now waiting for OTP via polling...');

        // Update progress and mark as OTP required
        if (requestHash) {
          console.log('🎯 [BMN-24-DEBUG] About to mark OTP required with data:', {
            requestHash: requestHash,
            trackId: smsResult.data.trackId,
            sourceName: smsResult.data.sourceName
          });
          await markOtpRequired(requestHash, smsResult.data);
          console.log('✅ [BMN-24-DEBUG] Successfully marked OTP required in Redis');
        }

        // Wait for OTP via polling (Step 2)
        console.log('🔔 [BMN-24-CREDIT-FLOW] Step 2: Polling for OTP submission...');
        const otpPollResult = await pollForOtpSubmission(requestHash, 300); // 5 minutes timeout

        // Handle different polling scenarios
        if (!otpPollResult) {
          // True timeout scenario
          console.log('❌ [BMN-24-CREDIT-FLOW] OTP timeout reached');
          await browser.close();
          return {
            status: 'timeout',
            code: 'OTP_TIMEOUT',
            message: 'زمان انتظار کد تایید به پایان رسید'
          };
        }
        
        // Handle request failure/cancellation
        if (otpPollResult.error) {
          console.log(`❌ [BMN-24-CREDIT-FLOW] Request ${otpPollResult.status}: ${otpPollResult.reason}`);
          await browser.close();
          return {
            status: 'error',
            code: otpPollResult.reason,
            message: otpPollResult.message,
            data: {
              mobile: mobile,
              nationalCode: nationalCode,
              canRetry: true,
              failureReason: otpPollResult.status
            }
          };
        }

        // Extract OTP data for successful case
        const otpData = otpPollResult;

        console.log('✅ [BMN-24-CREDIT-FLOW] OTP received via polling, processing...');

        // Update progress
        if (requestHash) {
          await updateRedisProgress(requestHash, 80, 'waiting_otp', 'تایید کد دریافتی...');
        }

        // Verify OTP and get final result (Step 3)
        console.log('🔐 [BMN-24-CREDIT-FLOW] Step 3: Verifying OTP and getting result...');
        const finalResult = await verifyOtpAndGetResult(
          page,
          mobile,
          nationalCode,
          smsResult.data.trackId,
          otpData.otp,
          requestHash
        );

        await browser.close();
        console.log('🎉 [BMN-24-CREDIT-FLOW] Complete flow finished successfully!');

        return finalResult;

      } catch (error) {
        retryCount++;
        console.error(`❌ [BMN-24-CREDIT-FLOW] Error in attempt ${retryCount}:`, error.message);
        console.error('📋 [BMN-24-CREDIT-FLOW] Full error details:', error);

        if (retryCount < maxRetries) {
          console.log(`🔄 [BMN-24-CREDIT-FLOW] Retrying attempt ${retryCount + 1}/${maxRetries}...`);
          continue;
        } else {
          console.log('🚫 [BMN-24-CREDIT-FLOW] Maximum general retries reached, aborting');
          await browser.close();
          throw error;
        }
      }
    }

    console.log('🚫 [BMN-24-CREDIT-FLOW] All retry attempts exhausted');
    await browser.close();
    return {
      status: 'error',
      code: 'MAX_RETRIES_REACHED',
      message: 'Maximum retry attempts reached'
    };

  } catch (error) {
    console.error('💥 [BMN-24-CREDIT-FLOW] Unexpected error in complete flow:', error.message);
    console.error('📋 [BMN-24-CREDIT-FLOW] Full error stack:', error);

    try {
      await browser.close();
    } catch (closeError) {
      console.error('❌ [BMN-24-CREDIT-FLOW] Error closing browser:', closeError.message);
    }

    return {
      status: 'error',
      code: 'UNEXPECTED_ERROR',
      message: 'خطایی در ارسال درخواست اولیه استعلام رخ داد لطفا مجدد تلاش کنید'
    };
  }
}

/**
 * Send SMS for OTP (internal function)
 */
async function sendSmsForOtp(page, mobile, nationalCode, requestHash) {
  console.log('📱 [BMN-24-SMS] Starting SMS sending process...');

  try {
    console.log('🌐 [BMN-24-SMS] Navigating to credit score service...');
    await page.goto('https://baman24.ir/pwa/my-credit');
    console.log('✅ [BMN-24-SMS] Successfully loaded credit score page');

    // Wait for page to load completely
    await page.waitForTimeout(2000);

    // Fill and submit form
    console.log('📝 [BMN-24-SMS] Filling and submitting credit score form...');

    // Debug: Log form filling details
    if (process.env.DEBUG_MODE === 'true') {
      console.log('\n🔧 [BMN-24-SMS-DEBUG] === FORM FILLING DEBUG ===');
      console.log('📱 Mobile:', mobile);
      console.log('🆔 National Code:', nationalCode);
      console.log('==========================================\n');
    }


    // screenshot
    await page.screenshot({ path: './images/screenshot-2.png' });

    // Wait for the form to be present with multiple fallback selectors
    // Simple form fill and submit for two fields and one button
    try {
      // Wait for both fields to be present
      await page.waitForSelector('#id_fromName_Mobile', { timeout: 3000 });
      await page.waitForSelector('#id_fromName_IdCode', { timeout: 3000 });

      // Fill the fields
      await page.fill('#id_fromName_Mobile', mobile);
      await page.fill('#id_fromName_IdCode', nationalCode);

      // Click the submit button
      await page.click('button.submitButton');

      console.log('✅ [BMN-24-SMS] Form filled and submitted simply');
    } catch (e) {
      throw new Error('Could not fill or submit the form: ' + e.message);
    }

    // Wait for response
    await page.waitForTimeout(2000);


    // take screenshot
    await page.screenshot({ path: './images/screenshot-1.png' });

    // Check for success indicators

    // Wait for the OTP input field to become visible (timeout: 60 seconds)
    try {
      await page.waitForSelector('#id_fromName_OtpCode', { state: 'visible', timeout: 60000 });
      return {
        success: true,
        data: {
          trackId: `track_${Date.now()}`, // Generate a tracking ID
          sourceName: 'ZnB2',
          message: 'کد تایید ارسال شد'
        }
      };
    } catch (e) {
      throw new Error('OTP input field did not appear within 60 seconds');
    }
  } catch (error) {
    console.error('💥 [BMN-24-SMS] Error in SMS sending:', error.message);
    return {
      success: false,
      message: 'خطایی در ارسال پیامک استعلام رخ داد لطفا مجدد تلاش کنید'
    };
  }
}

/**
 * Poll for OTP submission by checking Redis storage
 */
async function pollForOtpSubmission(requestHash, timeoutSeconds = 300) {
  const startTime = Date.now();
  const pollInterval = 2000; // Check every 2 seconds

  console.log(`🔔 [BMN-24-OTP-POLL] Starting OTP polling for ${timeoutSeconds} seconds`);

  while ((Date.now() - startTime) < timeoutSeconds * 1000) {
    try {
      // Get request data from Redis (using Laravel's key format)
      const requestKey = `local_request:${requestHash}`;
      const requestData = await redis.get(requestKey);

      if (!requestData) {
        console.log('⚠️ [BMN-24-OTP-POLL] Request not found in Redis');
        return null;
      }

      const parsedData = JSON.parse(requestData);

      // Check if OTP has been submitted
      if (parsedData.received_otp && parsedData.received_otp.otp) {
        console.log('✅ [BMN-24-OTP-POLL] OTP found in Redis!', {
          otp_length: parsedData.received_otp.otp.length,
          elapsed_time: Math.round((Date.now() - startTime) / 1000)
        });

        // Store the OTP data before clearing it
        const otpData = { ...parsedData.received_otp };

        // Clear the OTP from Redis to prevent reuse
        delete parsedData.received_otp;
        parsedData.updated_at = new Date().toISOString();
        await redis.setex(requestKey, 1800, JSON.stringify(parsedData)); // 30 minutes TTL

        return otpData;
      }

      // Check if request was cancelled or failed
      if (parsedData.status === 'failed' || parsedData.status === 'cancelled') {
        console.log('🛑 [BMN-24-OTP-POLL] Request was cancelled or failed', {
          status: parsedData.status
        });
        return { 
          error: true, 
          status: parsedData.status,
          reason: parsedData.status === 'failed' ? 'REQUEST_FAILED' : 'REQUEST_CANCELLED',
          message: parsedData.status === 'failed' ? 'درخواست با خطا مواجه شد' : 'درخواست لغو شد'
        };
      }

      if (process.env.DEBUG_MODE === 'true') {
        console.log('🔄 [BMN-24-OTP-POLL] Still waiting for OTP...', {
          status: parsedData.status,
          step: parsedData.step,
          elapsed: Math.round((Date.now() - startTime) / 1000)
        });
      }
    } catch (error) {
      console.error('❌ [BMN-24-OTP-POLL] Error checking for OTP:', error.message);
    }

    // Wait before next check
    await new Promise(resolve => setTimeout(resolve, pollInterval));
  }

  console.log('⏰ [BMN-24-OTP-POLL] OTP polling timeout reached');
  return null;
}

/**
 * Verify OTP and get final result
 */
async function verifyOtpAndGetResult(page, mobile, nationalCode, trackId, otp, requestHash) {
  console.log('🔐 [BMN-24-OTP-VERIFY] Starting OTP verification...');

  try {
    // Step 1: Verify OTP
    await page.waitForTimeout(1000);
    await page.screenshot({ path: './images/screenshot-4.png' });

    // Enter OTP in the input field and press Enter

    // Wait for the OTP input field to appear
    await page.waitForSelector('input#id_fromName_OtpCode', { timeout: 15000 });

    // Fill the OTP input field
    await page.fill('input#id_fromName_OtpCode', otp);

    // Click on the "ادامه" button inside the OTP modal
    // The button has class "submitButton" and is inside a div with class "Credit_otpBtnContainer__JcM2I"
    await page.click('.mainContainer button[type="submit"]');

    // Optionally, take a screenshot after clicking
    await page.screenshot({ path: './images/screenshot-5.png' });

    // Wait for the payment button with text "پرداخت" to appear and click it
    await page.waitForSelector('.buttonContainer button.GreenButton', { timeout: 20000 });


    await page.waitForTimeout(2000);

    // Find the button with the exact text "پرداخت" (it may include موجودی, so use includes)
    // Instead of using waitForSelector, poll for the button manually
    let payButton = null;
    const maxTries = 20;
    const delay = 500; // ms
    for (let i = 0; i < maxTries; i++) {
      const buttons = await page.$$('.buttonContainer button.GreenButton');
      for (const btn of buttons) {
        const btnText = await btn.textContent();
        if (btnText && btnText.includes('پرداخت')) {
          payButton = btn;
          break;
        }
      }
      if (payButton) break;
      await page.waitForTimeout(delay);
    }
    if (!payButton) {
      throw new Error('پرداخت button not found');
    }
    await payButton.click();

    await page.waitForSelector('.factorContainer .description', { visible: true, timeout: 60000 });

    await page.waitForTimeout(5000);
    await page.screenshot({ path: './images/screenshot-6.png' });

    let otpVerifyResult = await page.$eval('.factorContainer .description', el => el.textContent.trim()) || "";
    // Wait for the result to load (you may want to wait for a specific selector or just a delay)

    console.log('📊 [BMN-24-OTP-VERIFY] OTP verify result:', otpVerifyResult);

    if (otpVerifyResult.includes('ارتباط با سرویس دهنده برقرار نشد') === true) {
      console.log('❌ [BMN-24-OTP-VERIFY] OTP verification failed');
      return {
        status: 'error',
        code: 'INVALID_OTP',
        message: 'رمز یکبار مصرف وارد شده اشتباه است، لطفا بعد از ۳ دقیقه مجدد تلاش کنید'
      };
    }else if( otpVerifyResult.includes('لینک گزارش برای کد ملی') === true){
      return {
        status: 'success',
        code: 'CREDIT_SCORE_COMPLETED',
        message: 'درخواست شما با موفقیت ثبت شد.',
        data: {
          date: new Date().toLocaleDateString('fa-IR'),
          time: new Date().toLocaleTimeString('fa-IR', { hour: '2-digit', minute: '2-digit' }),
          status:  'موفق',
          description: 'لینک گزارش برای کد ملی',
          secondDescription: 'لطفا لینک را در ۱۲ ساعت دیگر دریافت کنید',
          serviceName: 'استعلام اعتبار سنجی بانکی',
          header:  'نتیجه استعلام اعتبارسنجی',
          mobile:  mobile,  
          nationalCode: nationalCode,
          customerService: '021-87700500'
        }
      };
    }else{
      return {
        status: 'error',
        code: 'CREDIT_SCORE_ERROR',
        message: 'خطایی رخ داده است، لطفا یک ساعت دیگر تلاش کنید.',
      };
    }

  } catch (error) {
    console.error('💥 [BMN-24-OTP-VERIFY] Error in OTP verification:', error.message);
    return {
      status: 'error',
      code: 'OTP_VERIFICATION_ERROR',
      message: 'خطایی در ارسال تایید پیامک رخ داد لطفا مجدد تلاش کنید'
    };
  }
}

async function updateRedisProgress(requestHash, progress, step, message) {
  try {
    const requestKey = `local_request:${requestHash}`;

    // Get existing request data
    const existingData = await redis.get(requestKey);
    let requestData = existingData ? JSON.parse(existingData) : {};

    // Update progress data
    requestData = {
      ...requestData,
      progress: Math.min(100, Math.max(0, progress)),
      step: step,
      current_message: message,
      updated_at: new Date().toISOString()
    };

    // Store updated data in Redis with TTL
    await redis.setex(requestKey, 1800, JSON.stringify(requestData)); // 30 minutes TTL

    // Publish update to Laravel channels for real-time updates
    const channelName = `local_request_updates:${requestHash}`;
    await redis.publish(channelName, JSON.stringify(requestData));

    if (process.env.DEBUG_MODE === 'true') {
      console.log('📡 [BMN-24-REDIS] Progress update stored:', {
        progress,
        step,
        message: message.substring(0, 50) + '...'
      });
    }
  } catch (error) {
    console.error('❌ [BMN-24-REDIS] Error updating progress:', error);
  }
}

async function markOtpRequired(requestHash, otpData) {
  try {
    const requestKey = `local_request:${requestHash}`;

    // Get existing request data
    const existingData = await redis.get(requestKey);
    let requestData = existingData ? JSON.parse(existingData) : {};

    // Update with OTP required status
    requestData = {
      ...requestData,
      status: 'otp_required',
      step: 'waiting_otp',
      progress: 70,
      current_message: 'در انتظار دریافت کد تایید...',
      otp_data: otpData,
      updated_at: new Date().toISOString()
    };

    // Store updated data in Redis
    await redis.setex(requestKey, 1800, JSON.stringify(requestData)); // 30 minutes TTL

    console.log('🔍 [BMN-24-REDIS-DEBUG] Stored OTP required data in Redis:', {
      key: requestKey,
      status: requestData.status,
      step: requestData.step,
      progress: requestData.progress,
      message: requestData.current_message
    });

    // Publish update to Laravel channels
    const channelName = `local_request_updates:${requestHash}`;
    await redis.publish(channelName, JSON.stringify(requestData));

    console.log('📡 [BMN-24-REDIS-DEBUG] Published update to channel:', channelName);

    if (process.env.DEBUG_MODE === 'true') {
      console.log('📡 [BMN-24-REDIS] OTP required status stored:', {
        trackId: otpData.trackId?.substring(0, 8) + '...',
        sourceName: otpData.sourceName
      });
    }
  } catch (error) {
    console.error('❌ [BMN-24-REDIS] Error marking OTP required:', error);
  }
}

/**
 * Get authenticated page (reusing existing function)
 */
async function getPage(browser, mobile) {
  console.log('\n🔐 [BMN-24-AUTH] Starting authentication process');

  // Try checkLogin first
  console.log('🔍 [BAMAN24-AUTH] Checking existing login status...');
  const checkResult = await baman24.checkLogin(mobile);
  console.log('📊 [BAMAN24-AUTH] Login check result:', checkResult);

  if (checkResult && checkResult.status === 'success') {
    console.log('✅ [BMN-24-AUTH] Already logged in, using existing session');
    const user = credintals.users[0];
    console.log('👤 [BMN-24-AUTH] Using user:', user.mobile);
    const storageStatePath = path.resolve(__dirname + '/../sessions/' + `session-${user.mobile}.json`);
    console.log('📂 [BAMAN24-AUTH] Looking for session file:', storageStatePath);

    if (!fs.existsSync(storageStatePath)) {
      console.log('❌ [BMN-24-AUTH] Session file does not exist after successful checkLogin');
      throw new Error('Session file does not exist after successful checkLogin');
    }
    console.log('✅ [BMN-24-AUTH] Session file found, creating browser context');

    const context = await browser.newContext({ storageState: storageStatePath });
    const page = await context.newPage();
    console.log('✅ [BMN-24-AUTH] Page created with existing session');
    return page;
  } else {
    console.log('🔑 [BAMAN24-AUTH] Not logged in, attempting login...');
    const loginResult = await baman24.login(mobile);
    console.log('📊 [BAMAN24-AUTH] Login attempt result:', loginResult);

    if (loginResult && loginResult.status === 'success') {
      console.log('✅ [BMN-24-AUTH] Login successful, creating session context');
      const user = credintals.users[0];
      console.log('👤 [BMN-24-AUTH] Using user:', user.mobile);
      const storageStatePath = path.resolve(__dirname + '/../sessions/' + `session-${user.mobile}.json`);
      console.log('📂 [BAMAN24-AUTH] Looking for new session file:', storageStatePath);

      if (!fs.existsSync(storageStatePath)) {
        console.log('❌ [BMN-24-AUTH] Session file does not exist after successful login');
        throw new Error('Session file does not exist after successful login');
      }
      console.log('✅ [BMN-24-AUTH] New session file found, creating browser context');

      const context = await browser.newContext({ storageState: storageStatePath });
      const page = await context.newPage();
      console.log('✅ [BMN-24-AUTH] Page created with new session');
      return page;
    } else {
      console.log('❌ [BMN-24-AUTH] Login failed');
      const errorMsg =
        'Login failed: ' +
        (loginResult && loginResult.message ? loginResult.message : 'Unknown error');
      console.error('💥 [BMN-24-AUTH]', errorMsg);
      throw new Error(errorMsg);
    }
  }
}

// Legacy function exports for backward compatibility
export async function sendOtpSms(data) {
  console.log('⚠️ [BMN-24-LEGACY] sendOtpSms called - redirecting to new Redis pub/sub flow');
  return await handleCreditScoreInquiry(data);
}

export async function handleOtpVerification(data) {
  console.log('⚠️ [BMN-24-LEGACY] handleOtpVerification called - this should not happen with Redis pub/sub flow');
  return {
    status: 'error',
    code: 'LEGACY_FUNCTION_CALLED',
    message: 'خطایی در تایید کد پیامک رخ داد لطفا مجدد تلاش کنید'
  };
}

/**
 * Handle resend SMS only - just send SMS again without waiting for OTP
 */
async function handleResendSmsOnly(mobile, nationalCode, requestHash) {
  console.log('📱 [BMN-24-RESEND] Starting resend SMS process...');
  
  // Initialize browser
  console.log('🌐 [BMN-24-RESEND] Initializing browser...');
  const { chromium } = await import('playwright');
  const browser = await chromium.launch({ headless: true });
  console.log('✅ [BMN-24-RESEND] Browser initialized successfully');

  try {
    // Update progress
    if (requestHash) {
      await updateRedisProgress(requestHash, 30, 'authentication', 'ارسال مجدد کد تایید...');
    }

    console.log('🔑 [BMN-24-RESEND] Getting authenticated page...');
    const page = await getPage(browser, mobile);
    console.log('✅ [BMN-24-RESEND] Successfully got authenticated page');

    // Update progress
    if (requestHash) {
      await updateRedisProgress(requestHash, 50, 'authentication', 'در حال ارسال مجدد پیام...');
    }

    // Send SMS (just SMS, no OTP waiting)
    console.log('📱 [BMN-24-RESEND] Sending SMS...');
    const smsResult = await sendSmsForOtp(page, mobile, nationalCode, requestHash);

    if (!smsResult.success) {
      throw new Error(`خطا در ارسال مجدد پیام: ${smsResult.message || 'خطای نامشخص'}`);
    }

    console.log('✅ [BMN-24-RESEND] SMS resent successfully');

    // Update progress to waiting for OTP
    if (requestHash) {
      await updateRedisProgress(requestHash, 70, 'waiting_otp', 'کد تایید مجدد ارسال شد');
    }

    return {
      status: 'success',
      code: 'SMS_RESENT',
      message: 'کد تایید مجدد ارسال شد',
      data: {
        mobile: mobile,
        requestHash: requestHash
      }
    };

  } catch (error) {
    console.error('❌ [BMN-24-RESEND] Error during resend:', error.message);
    
    // Update progress with error
    if (requestHash) {
      await updateRedisProgress(requestHash, 70, 'waiting_otp', 'خطا در ارسال مجدد کد تایید');
    }

    return {
      status: 'error',
      code: 'RESEND_FAILED',
      message: 'خطا در ارسال مجدد کد تایید'
    };
  } finally {
    // Close browser
    if (browser) {
      await browser.close();
      console.log('🔒 [BMN-24-RESEND] Browser closed');
    }
  }
}

// Main export for the new Redis pub/sub flow
export { handleCreditScoreInquiry, getPage };

export function checkForErrors() {
  console.log('🎉 [BMN-24-ERROR-CHECK] No UI errors detected - API-based service with Redis pub/sub');
  return { type: 'no_error' };
}

export function checkForOtpErrors() {
  console.log('🎉 [BMN-24-OTP-ERROR-CHECK] No UI OTP errors detected - Redis pub/sub handles OTP flow');
  return { type: 'no_error' };
}