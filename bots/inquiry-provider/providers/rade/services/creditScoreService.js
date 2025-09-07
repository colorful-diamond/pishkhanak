import rade from '../index.js';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import dotenv from 'dotenv';
import { solveCaptchaComplete } from '../captchaSolver.js';
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
  console.log('🔧 [RADE-CREDIT] Environment file path:', envPath);
  console.log('🔧 [RADE-CREDIT] Environment file exists:', fs.existsSync(envPath));
  console.log('🔧 [RADE-CREDIT] DEBUG_MODE enabled - detailed logging active');
  console.log('🔧 [RADE-CREDIT] Redis config:', {
    host: process.env.REDIS_HOST || '127.0.0.1',
    port: process.env.REDIS_PORT || 6379,
    db: process.env.REDIS_DB || 0
  });
}

/**
 * Ensures the user is logged in to my.rade.ir and returns the page object if successful.
 * If already logged in, returns the page. Otherwise, attempts login and returns the page if successful.
 * Throws an error if login fails.
 * @param {import('playwright').Browser} browser - An instance of Playwright's Browser.
 * @param {string} mobile - Mobile number for session identification
 * @returns {Promise<import('playwright').Page>} The logged-in page object.
 */
async function getPage(browser, mobile) {
  console.log('\n🔐 [RADE-AUTH] Starting authentication process');

  // Try checkLogin first
  console.log('🔍 [RADE-AUTH] Checking existing login status...');
  const checkResult = await rade.checkLogin(mobile);
  console.log('📊 [RADE-AUTH] Login check result:', checkResult);

  if (checkResult && checkResult.status === 'success') {
    console.log('✅ [RADE-AUTH] Already logged in, using existing session');
    // Already logged in, create context and page from session
    const user = credintals.users[0];
    console.log('👤 [RADE-AUTH] Using user:', user.mobile);
    const storageStatePath = path.resolve(__dirname + '/../sessions/' + `session-${mobile}.json`);
    console.log('📂 [RADE-AUTH] Looking for session file:', storageStatePath);

    if (!fs.existsSync(storageStatePath)) {
      console.log('❌ [RADE-AUTH] Session file does not exist after successful checkLogin');
      throw new Error('Session file does not exist after successful checkLogin');
    }
    console.log('✅ [RADE-AUTH] Session file found, creating browser context');

    const context = await browser.newContext({ storageState: storageStatePath });
    const page = await context.newPage();
    console.log('✅ [RADE-AUTH] Page created with existing session');
    return page;
  } else {
    console.log('🔑 [RADE-AUTH] Not logged in, attempting login...');
    // Not logged in, try to login
    const loginResult = await rade.login(mobile);
    console.log('📊 [RADE-AUTH] Login attempt result:', loginResult);

    if (loginResult && loginResult.status === 'success') {
      console.log('✅ [RADE-AUTH] Login successful, creating session context');
      const user = credintals.users[0];
      console.log('👤 [RADE-AUTH] Using user:', user.mobile);
      const storageStatePath = path.resolve(__dirname + '/../sessions/' + `session-${mobile}.json`);
      console.log('📂 [RADE-AUTH] Looking for new session file:', storageStatePath);

      if (!fs.existsSync(storageStatePath)) {
        console.log('❌ [RADE-AUTH] Session file does not exist after successful login');
        throw new Error('Session file does not exist after successful login');
      }
      console.log('✅ [RADE-AUTH] New session file found, creating browser context');

      const context = await browser.newContext({ storageState: storageStatePath });
      const page = await context.newPage();
      console.log('✅ [RADE-AUTH] Page created with new session');
      return page;
    } else {
      console.log('❌ [RADE-AUTH] Login failed');
      const errorMsg = 'Login failed: ' + (loginResult && loginResult.message ? loginResult.message : 'Unknown error');
      console.error('💥 [RADE-AUTH]', errorMsg);
      throw new Error(errorMsg);
    }
  }
}

/**
 * Main credit score inquiry function with polling for OTP
 * This function handles the complete flow from SMS to final result
 */
async function handleCreditScoreInquiry(data) {
  console.log('\n🚀 [RADE-CREDIT-FLOW] Starting complete credit score inquiry with polling');

  const { mobile, nationalCode, requestHash, resendSms = false, hash } = data;
  
  // Handle resend SMS request - only send SMS, don't wait for OTP
  if (resendSms) {
    console.log('🔄 [RADE-CREDIT-FLOW] Resend SMS request detected');
    return await handleResendSmsOnly(mobile, nationalCode, requestHash);
  }
  
  const maxRetries = 3;
  let retryCount = 0;

  // Initialize browser
  console.log('🌐 [RADE-CREDIT-FLOW] Initializing browser...');
  const { chromium } = await import('playwright');
  const browser = await chromium.launch({ headless: true });
  console.log('✅ [RADE-CREDIT-FLOW] Browser initialized successfully');

  try {
    // Start the flow with retries
    console.log(`🔄 [RADE-CREDIT-FLOW] Starting main process loop (max ${maxRetries} attempts)`);
    while (retryCount < maxRetries) {
      console.log(`\n📊 [RADE-CREDIT-FLOW] === ATTEMPT ${retryCount + 1}/${maxRetries} ===`);
      try {
        // Update progress via Redis
        if (requestHash) {
          await updateRedisProgress(requestHash, 30, 'authentication', 'ارسال درخواست به درگاه دولت هوشمند...');
        }

        console.log('🔑 [RADE-CREDIT-FLOW] Getting authenticated page...');
        const page = await getPage(browser, mobile);
        console.log('✅ [RADE-CREDIT-FLOW] Successfully got authenticated page');

        // Update progress
        if (requestHash) {
          await updateRedisProgress(requestHash, 50, 'authentication', 'پردازش درخواست...');
        }

        // Send SMS (Step 1)
        console.log('📱 [RADE-CREDIT-FLOW] Step 1: Sending SMS...');
        const smsResult = await sendSmsForOtp(page, mobile, nationalCode, requestHash);

        if (!smsResult.success) {
          throw new Error(`SMS sending failed: ${smsResult.message || 'Unknown error'}`);
        }

        console.log('✅ [RADE-CREDIT-FLOW] SMS sent successfully, now waiting for OTP via polling...');

        // Update progress and mark as OTP required
        if (requestHash) {
          console.log('🎯 [RADE-DEBUG] About to mark OTP required with data:', {
            requestHash: requestHash,
            hash: smsResult.data.hash
          });
          await markOtpRequired(requestHash, smsResult.data);
          console.log('✅ [RADE-DEBUG] Successfully marked OTP required in Redis');
        }

        // Wait for OTP via polling (Step 2)
        console.log('🔔 [RADE-CREDIT-FLOW] Step 2: Polling for OTP submission...');
        const otpData = await pollForOtpSubmission(requestHash, 300); // 5 minutes timeout

        if (!otpData) {
          console.log('❌ [RADE-CREDIT-FLOW] OTP timeout or cancelled');
          await browser.close();
          return {
            status: 'error',
            code: 'OTP_TIMEOUT',
            message: 'زمان انتظار کد تایید به پایان رسید'
          };
        }

        console.log('✅ [RADE-CREDIT-FLOW] OTP received via polling, processing...');

        // Update progress
        if (requestHash) {
          await updateRedisProgress(requestHash, 80, 'waiting_otp', 'تایید کد دریافتی...');
        }

        // Verify OTP and get final result (Step 3)
        console.log('🔐 [RADE-CREDIT-FLOW] Step 3: Verifying OTP and getting result...');
        const finalResult = await verifyOtpAndGetResult(
          page,
          mobile,
          nationalCode,
          smsResult.data.hash,
          otpData.otp,
          requestHash
        );

        await browser.close();
        console.log('🎉 [RADE-CREDIT-FLOW] Complete flow finished successfully!');

        return finalResult;

      } catch (error) {
        retryCount++;
        console.error(`❌ [RADE-CREDIT-FLOW] Error in attempt ${retryCount}:`, error.message);
        console.error('📋 [RADE-CREDIT-FLOW] Full error details:', error);

        if (retryCount < maxRetries) {
          console.log(`🔄 [RADE-CREDIT-FLOW] Retrying attempt ${retryCount + 1}/${maxRetries}...`);
          continue;
        } else {
          console.log('🚫 [RADE-CREDIT-FLOW] Maximum general retries reached, aborting');
          await browser.close();
          throw error;
        }
      }
    }

    console.log('🚫 [RADE-CREDIT-FLOW] All retry attempts exhausted');
    await browser.close();
    return {
      status: 'error',
      code: 'MAX_RETRIES_REACHED',
      message: 'Maximum retry attempts reached'
    };

  } catch (error) {
    console.error('💥 [RADE-CREDIT-FLOW] Unexpected error in complete flow:', error.message);
    console.error('📋 [RADE-CREDIT-FLOW] Full error stack:', error);

    try {
      await browser.close();
    } catch (closeError) {
      console.error('❌ [RADE-CREDIT-FLOW] Error closing browser:', closeError.message);
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
  console.log('📱 [RADE-SMS] Starting SMS sending process...');

  try {
    console.log('🌐 [RADE-SMS] Navigating to credit score service...');
    await page.goto('https://api.rade.ir/captcha/default');
    console.log('✅ [RADE-SMS] Successfully loaded credit score page');

    // Solve captcha (provider-specific)
    const captchaSolution = await solveCaptchaComplete(page, mobile, nationalCode);
    
    if (!captchaSolution || !captchaSolution.text) {
      throw new Error('Failed to solve captcha');
    }
    
    // Prepare payload for OTP send request using actual input data and solved captcha
    const otpPayload = {
      nid: nationalCode,
      mobile: mobile,
      captcha: captchaSolution.text,
      legal_national_id: ""
    };

    // Use Playwright's page.evaluate to send the fetch request in the browser context
    const otpSendResult = await page.evaluate(async (payload) => {
      const response = await fetch("https://api.rade.ir/api/v2/service/creditscore/otp/send", {
        headers: {
          "accept": "application/json, text/plain, */*",
          "content-type": "application/json",
          "rade-device-type": "web",
          "sec-ch-ua": "\"Not)A;Brand\";v=\"8\", \"Chromium\";v=\"138\", \"Google Chrome\";v=\"138\"",
          "sec-ch-ua-mobile": "?0",
          "sec-ch-ua-platform": "\"Windows\"",
          "Referer": "https://my.rade.ir/"
        },
        body: JSON.stringify(payload),
        method: "POST"
      });
      return await response.json();
    }, otpPayload);

    console.log('📨 [RADE-SMS] OTP send result:', otpSendResult);

    if (otpSendResult.traceId !== null && (otpSendResult.message === null || otpSendResult.message === undefined)) {
      return {
        success: true,
        data: {
          hash: otpSendResult.traceId,
          expiry: otpSendResult.expiry,
          message: 'کد تایید ارسال شد'
        }
      };
    } else {
      throw new Error(`OTP send failed: ${otpSendResult.message || 'Unknown error'}`);
    }

  } catch (error) {
    console.error('💥 [RADE-SMS] Error in SMS sending:', error.message);
    return {
      success: false,
      message: error.message || 'خطایی در ارسال پیامک استعلام رخ داد لطفا مجدد تلاش کنید'
    };
  }
}

/**
 * Poll for OTP submission by checking Redis storage
 */
async function pollForOtpSubmission(requestHash, timeoutSeconds = 300) {
  const startTime = Date.now();
  const pollInterval = 2000; // Check every 2 seconds

  console.log(`🔔 [RADE-OTP-POLL] Starting OTP polling for ${timeoutSeconds} seconds`);

  while ((Date.now() - startTime) < timeoutSeconds * 1000) {
    try {
      // Get request data from Redis (using Laravel's key format)
      const requestKey = `local_request:${requestHash}`;
      const requestData = await redis.get(requestKey);

      if (!requestData) {
        console.log('⚠️ [RADE-OTP-POLL] Request not found in Redis');
        return null;
      }

      const parsedData = JSON.parse(requestData);

      // Check if OTP has been submitted
      if (parsedData.received_otp && parsedData.received_otp.otp) {
        console.log('✅ [RADE-OTP-POLL] OTP found in Redis!', {
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
        console.log('🛑 [RADE-OTP-POLL] Request was cancelled or failed', {
          status: parsedData.status
        });
        return null;
      }

      if (process.env.DEBUG_MODE === 'true') {
        console.log('🔄 [RADE-OTP-POLL] Still waiting for OTP...', {
          status: parsedData.status,
          step: parsedData.step,
          elapsed: Math.round((Date.now() - startTime) / 1000)
        });
      }
    } catch (error) {
      console.error('❌ [RADE-OTP-POLL] Error checking for OTP:', error.message);
    }

    // Wait before next check
    await new Promise(resolve => setTimeout(resolve, pollInterval));
  }

  console.log('⏰ [RADE-OTP-POLL] OTP polling timeout reached');
  return null;
}

/**
 * Verify OTP and get final result
 */
async function verifyOtpAndGetResult(page, mobile, nationalCode, traceId, otp, requestHash) {
  console.log('🔐 [RADE-OTP-VERIFY] Starting OTP verification...');

  try {
    // Navigate to verification page with traceId
    const verificationUrl = `https://my.rade.ir/service/creditScore/verification/${traceId}`;
    console.log('🌐 [RADE-OTP-VERIFY] Navigating to verification URL:', verificationUrl);
    await page.goto(verificationUrl);
    console.log('✅ [RADE-OTP-VERIFY] Successfully loaded verification page');

    // Wait for OTP form to load
    console.log('⏰ [RADE-OTP-VERIFY] Waiting for OTP form fields to load...');
    await page.waitForSelector('input[formcontrolname="code1"]', { timeout: 10000 });
    console.log('✅ [RADE-OTP-VERIFY] OTP form fields loaded successfully');

    // Fill OTP in the 5 separate fields
    if (otp && otp.length === 5) {
      console.log('📝 [RADE-OTP-VERIFY] Filling OTP in 5 separate fields...');
      for (let i = 0; i < 5; i++) {
        const fieldName = `code${i + 1}`;
        console.log(`📝 [RADE-OTP-VERIFY] Filling field ${fieldName} with digit: ${otp[i]}`);
        await page.locator(`input[formcontrolname="${fieldName}"]`).fill(otp[i]);
        if (i === 4) {
          await page.locator(`input[formcontrolname="${fieldName}"]`).focus();
          await page.keyboard.press('Enter');
          await page.waitForTimeout(1000);
        }
      }
      console.log('✅ [RADE-OTP-VERIFY] All OTP digits filled successfully');
    } else {
      console.log('❌ [RADE-OTP-VERIFY] Invalid OTP format - must be 5 digits');
      return {
        status: 'error',
        code: 'INVALID_OTP_FORMAT',
        message: 'OTP must be 5 digits'
      };
    }

    await page.waitForTimeout(2000);

    // Wait for app-back-cheques-report tag to appear
    console.log('⏰ [RADE-OTP-VERIFY] Waiting for app-back-cheques-report tag to appear...');
    try {
      await page.waitForSelector('app-back-cheques-report', { timeout: 120000 });
      console.log('✅ [RADE-OTP-VERIFY] app-back-cheques-report tag found successfully');
    } catch (waitError) {
      console.log('⚠️ [RADE-OTP-VERIFY] app-back-cheques-report tag not found within timeout, continuing...');
    }

    // Submit OTP form
    console.log('🚀 [RADE-OTP-VERIFY] Submitting OTP form...');

    // Wait for response
    await page.waitForTimeout(3000);
    console.log('⏰ [RADE-OTP-VERIFY] Wait complete, checking for OTP errors...');

    // Check for OTP errors
    console.log('🔍 [RADE-OTP-VERIFY] Analyzing OTP submission response...');
    const otpErrorCheck = await checkForOtpErrors(page);
    console.log('📊 [RADE-OTP-VERIFY] OTP error check result:', otpErrorCheck);

    if (otpErrorCheck.type === 'otp_incorrect') {
      console.log('❌ [RADE-OTP-VERIFY] Incorrect OTP detected');
      return {
        status: 'error',
        code: 'INVALID_OTP',
        message: 'رمز یکبار مصرف اشتباه است'
      };
    } else if (otpErrorCheck.type === 'otp_expired') {
      console.log('⏰ [RADE-OTP-VERIFY] OTP expired detected');
      return {
        status: 'error',
        code: 'OTP_EXPIRED',
        message: 'اعتبار رمز یکبارمصرف به پایان رسیده است'
      };
    } else if (otpErrorCheck.type === 'otp_service_unavailable') {
      console.log('❌ [RADE-OTP-VERIFY] Service unavailable during OTP verification');
      return {
        status: 'error',
        code: 'OTP_SERVICE_UNAVAILABLE',
        message: 'سرویس قادر به پاسخ‌دهی نیست، چند لحظه دیگر مجددا تلاش کنید'
      };
    } else if (otpErrorCheck.type === 'no_error') {
      // Success - Credit score inquiry completed and result will be sent via SMS
      console.log('🎉 [RADE-OTP-VERIFY] OTP verification successful!');
      console.log('📱 [RADE-OTP-VERIFY] Credit score result will be sent via SMS');

      // Update progress to completed
      if (requestHash) {
        await markAsCompleted(requestHash, {
          message: 'درخواست شما با موفقیت ثبت شد. نتیجه استعلام در حداکثر ۱۵ دقیقه به شماره موبایل شما ارسال خواهد شد.',
          data: {
            mobile: mobile || 'شماره موبایل شما',
            estimated_delivery: '15 دقیقه',
            inquiry_type: 'استعلام امتیاز اعتباری',
            status: 'در حال پردازش'
          },
          code: 'CREDIT_SCORE_SMS_SENT'
        });
      }

      return {
        status: 'success',
        code: 'CREDIT_SCORE_SMS_SENT',
        message: 'درخواست شما با موفقیت ثبت شد. نتیجه استعلام در حداکثر ۱۵ دقیقه به شماره موبایل شما ارسال خواهد شد.',
        data: {
          mobile: mobile || 'شماره موبایل شما',
          estimated_delivery: '15 دقیقه',
          inquiry_type: 'استعلام امتیاز اعتباری',
          status: 'در حال پردازش'
        }
      };
    }

    console.log('❓ [RADE-OTP-VERIFY] Unknown OTP error type, aborting');
    return {
      status: 'error',
      code: 'UNKNOWN_OTP_ERROR',
      message: 'Unknown error during OTP verification'
    };

  } catch (error) {
    console.error('💥 [RADE-OTP-VERIFY] Unexpected error in OTP verification:', error.message);
    console.error('📋 [RADE-OTP-VERIFY] Full error stack:', error);
    
    return {
      status: 'error',
      code: 'OTP_VERIFICATION_ERROR',
      message: error.message
    };
  }
}

/**
 * Checks for various errors and success scenarios on the page
 * @param {import('playwright').Page} page - The page object
 * @returns {Promise<Object>} Error type and details
 */
async function checkForErrors(page) {
  console.log('\n🔍 [RADE-ERROR-CHECK] Starting error analysis');
  try {
    const currentUrl = page.url();
    console.log('🌐 [RADE-ERROR-CHECK] Current URL:', currentUrl);

    await page.screenshot({ path: 'error-check.png' });
    // Check for captcha error
    console.log('🔍 [RADE-ERROR-CHECK] Checking for captcha error...');
    const captchaError = await page.locator('.text-danger:has-text("کد وارد شده اشتباه است")').first();
    if (await captchaError.isVisible().catch(() => false)) {
      console.log('❌ [RADE-ERROR-CHECK] Captcha error detected');
      return { type: 'captcha_error' };
    }
    console.log('✅ [RADE-ERROR-CHECK] No captcha error');

    // Check for national code error
    console.log('🔍 [RADE-ERROR-CHECK] Checking for national code error...');
    const nationalCodeError = await page.locator('.text-danger:has-text("کد ملی وارد شده اشتباه است")').first();
    if (await nationalCodeError.isVisible().catch(() => false)) {
      console.log('❌ [RADE-ERROR-CHECK] National code error detected');
      return { type: 'national_code_error' };
    }
    console.log('✅ [RADE-ERROR-CHECK] No national code error');

    // Check for service unavailable error
    console.log('🔍 [RADE-ERROR-CHECK] Checking for service unavailable error...');
    const serviceError = await page.locator('.text-danger:has-text("سرویس قادر به پاسخ‌دهی نیست، چند لحظه دیگر مجددا تلاش کنید")').first();
    if (await serviceError.isVisible().catch(() => false)) {
      console.log('❌ [RADE-ERROR-CHECK] Service unavailable error detected');
      return { type: 'service_unavailable' };
    }
    console.log('✅ [RADE-ERROR-CHECK] Service is available');

    // Check for verification redirect
    console.log('🔍 [RADE-ERROR-CHECK] Checking for OTP verification redirect...');
    const verificationMatch = currentUrl.match(/my\.rade\.ir\/service\/creditScore\/verification\/([a-f0-9-]+)\?expire=(\d+)/);
    if (verificationMatch) {
      console.log('✅ [RADE-ERROR-CHECK] OTP verification redirect detected');
      console.log('📱 [RADE-ERROR-CHECK] Hash:', verificationMatch[1]);
      console.log('⏰ [RADE-ERROR-CHECK] Expiry:', verificationMatch[2]);
      return {
        type: 'verification_redirect',
        hash: verificationMatch[1],
        expiry: verificationMatch[2]
      };
    }
    console.log('✅ [RADE-ERROR-CHECK] No redirect detected');

    // No errors detected
    console.log('🎉 [RADE-ERROR-CHECK] No errors detected - success!');
    return { type: 'no_error' };

  } catch (error) {
    console.error('💥 [RADE-ERROR-CHECK] Error during error checking:', error.message);
    console.error('📋 [RADE-ERROR-CHECK] Full error details:', error);
    return { type: 'check_error', error: error.message };
  }
}

/**
 * Checks for OTP-specific errors
 * @param {import('playwright').Page} page - The page object
 * @returns {Promise<Object>} OTP error type
 */
async function checkForOtpErrors(page) {
  console.log('\n🔍 [RADE-OTP-ERROR-CHECK] Starting OTP error analysis');
  try {
    const currentUrl = page.url();
    console.log('🌐 [RADE-OTP-ERROR-CHECK] Current URL:', currentUrl);

    // Check for incorrect OTP error
    console.log('🔍 [RADE-OTP-ERROR-CHECK] Checking for incorrect OTP error...');
    const otpIncorrectError = await page.locator('.text-danger:has-text("رمز یکبار مصرف اشتباه است")').first();
    if (await otpIncorrectError.isVisible().catch(() => false)) {
      console.log('❌ [RADE-OTP-ERROR-CHECK] Incorrect OTP error detected');
      return { type: 'otp_incorrect' };
    }
    console.log('✅ [RADE-OTP-ERROR-CHECK] No incorrect OTP error');

    // Check for OTP expired error
    console.log('🔍 [RADE-OTP-ERROR-CHECK] Checking for expired OTP error...');
    const otpExpiredError = await page.locator('.text-danger:has-text("اعتبار رمز یکبارمصرف به پایان رسیده است")').first();
    if (await otpExpiredError.isVisible().catch(() => false)) {
      console.log('⏰ [RADE-OTP-ERROR-CHECK] Expired OTP error detected');
      return { type: 'otp_expired' };
    }
    console.log('✅ [RADE-OTP-ERROR-CHECK] OTP not expired');

    // Check for service unavailable error during OTP verification
    console.log('🔍 [RADE-OTP-ERROR-CHECK] Checking for service unavailable error...');
    const serviceUnavailableError = await page.locator('.text-danger:has-text("سرویس قادر به پاسخ‌دهی نیست، چند لحظه دیگر مجددا تلاش کنید")').first();
    if (await serviceUnavailableError.isVisible().catch(() => false)) {
      console.log('❌ [RADE-OTP-ERROR-CHECK] Service unavailable error detected');
      return { type: 'otp_service_unavailable' };
    }
    console.log('✅ [RADE-OTP-ERROR-CHECK] Service is available');

    // No OTP errors detected
    console.log('🎉 [RADE-OTP-ERROR-CHECK] No OTP errors detected - success!');
    return { type: 'no_error' };

  } catch (error) {
    console.error('💥 [RADE-OTP-ERROR-CHECK] Error during OTP error checking:', error.message);
    console.error('📋 [RADE-OTP-ERROR-CHECK] Full error details:', error);
    return { type: 'check_error', error: error.message };
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
      console.log('📡 [RADE-REDIS] Progress update stored:', {
        progress,
        step,
        message: message.substring(0, 50) + '...'
      });
    }
  } catch (error) {
    console.error('❌ [RADE-REDIS] Error updating progress:', error);
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

    console.log('🔍 [RADE-REDIS-DEBUG] Stored OTP required data in Redis:', {
      key: requestKey,
      status: requestData.status,
      step: requestData.step,
      progress: requestData.progress,
      message: requestData.current_message
    });

    // Publish update to Laravel channels
    const channelName = `local_request_updates:${requestHash}`;
    await redis.publish(channelName, JSON.stringify(requestData));

    console.log('📡 [RADE-REDIS-DEBUG] Published update to channel:', channelName);

    if (process.env.DEBUG_MODE === 'true') {
      console.log('📡 [RADE-REDIS] OTP required status stored:', {
        hash: otpData.hash?.substring(0, 8) + '...'
      });
    }
  } catch (error) {
    console.error('❌ [RADE-REDIS] Error marking OTP required:', error);
  }
}

async function markAsCompleted(requestHash, result) {
  try {
    const requestKey = `local_request:${requestHash}`;

    // Get existing request data
    const existingData = await redis.get(requestKey);
    let requestData = existingData ? JSON.parse(existingData) : {};

    // Update with completion status
    requestData = {
      ...requestData,
      status: 'completed',
      step: 'completed',
      progress: 100,
      current_message: result.message,
      result: result.data,
      completed_at: new Date().toISOString(),
      updated_at: new Date().toISOString()
    };

    // Store updated data in Redis
    await redis.setex(requestKey, 1800, JSON.stringify(requestData)); // 30 minutes TTL

    // Publish update to Laravel channels
    const channelName = `local_request_updates:${requestHash}`;
    await redis.publish(channelName, JSON.stringify(requestData));

    if (process.env.DEBUG_MODE === 'true') {
      console.log('📡 [RADE-REDIS] Completion status stored and published');
    }
  } catch (error) {
    console.error('❌ [RADE-REDIS] Error marking as completed:', error);
  }
}

/**
 * Handle resend SMS only - just send SMS again without waiting for OTP
 */
async function handleResendSmsOnly(mobile, nationalCode, requestHash) {
  console.log('📱 [RADE-RESEND] Starting resend SMS process...');
  
  // Initialize browser
  console.log('🌐 [RADE-RESEND] Initializing browser...');
  const { chromium } = await import('playwright');
  const browser = await chromium.launch({ headless: true });
  console.log('✅ [RADE-RESEND] Browser initialized successfully');

  try {
    // Update progress
    if (requestHash) {
      await updateRedisProgress(requestHash, 30, 'authentication', 'ارسال مجدد کد تایید...');
    }

    console.log('🔑 [RADE-RESEND] Getting authenticated page...');
    const page = await getPage(browser, mobile);
    console.log('✅ [RADE-RESEND] Successfully got authenticated page');

    // Update progress
    if (requestHash) {
      await updateRedisProgress(requestHash, 50, 'authentication', 'در حال ارسال مجدد پیام...');
    }

    // Send SMS (just SMS, no OTP waiting)
    console.log('📱 [RADE-RESEND] Sending SMS...');
    const smsResult = await sendSmsForOtp(page, mobile, nationalCode, requestHash);

    if (!smsResult.success) {
      throw new Error(`خطا در ارسال مجدد پیام: ${smsResult.message || 'خطای نامشخص'}`);
    }

    console.log('✅ [RADE-RESEND] SMS resent successfully');

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
    console.error('❌ [RADE-RESEND] Error during resend:', error.message);
    
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
      console.log('🔒 [RADE-RESEND] Browser closed');
    }
  }
}

// Legacy function exports for backward compatibility
export async function sendOtpSms(data) {
  console.log('⚠️ [RADE-LEGACY] sendOtpSms called - redirecting to new Redis pub/sub flow');
  return await handleCreditScoreInquiry(data);
}

export async function handleOtpVerification(data) {
  console.log('⚠️ [RADE-LEGACY] handleOtpVerification called - this should not happen with Redis pub/sub flow');
  return {
    status: 'error',
    code: 'LEGACY_FUNCTION_CALLED',
    message: 'خطایی در تایید کد پیامک رخ داد لطفا مجدد تلاش کنید'
  };
}

// Main export for the new Redis pub/sub flow
export { handleCreditScoreInquiry, getPage, checkForErrors, checkForOtpErrors }; 