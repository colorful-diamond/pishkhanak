/**
 * NICS24 Credit Score Service - Direct Redis Integration
 * 
 * This service integrates with Laravel via direct Redis storage updates:
 * 1. Sends OTP and updates progress via Redis
 * 2. Polls Redis for OTP submission by user
 * 3. Completes entire flow and updates final result in Redis
 * 
 * Based on NICS24 API endpoints and flow
 */
import nics24 from '../index.js';
import { loginWithUserName } from '../login.js';
import fs from 'fs';    
import path from 'path';
import { fileURLToPath } from 'url';
import dotenv from 'dotenv';
import Redis from 'ioredis';
import { 
    getRandomUserAgent, 
    getStealthLaunchArgs 
} from '../../../utils/stealthUtils.js';

// Get current directory for ES modules
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Load environment variables from .env file in inquiry-provider root
const envPath = path.resolve(__dirname, '../../../.env');
dotenv.config({ path: envPath });

// Get NICS24 configuration
const config = nics24.getConfig();
const credentials = nics24.getCredentials();

// Import Redis connection pool instead of creating new connection
import redisPool from '../../../services/redisConnectionPool.js';

// We'll use the pooled connection instead of creating a new one
// This prevents file descriptor exhaustion from too many Redis connections

// Debug: Check environment setup
if (process.env.DEBUG_MODE === 'true') {
  console.log('🔧 [NICS24-CREDIT] Environment file path:', envPath);
  console.log('🔧 [NICS24-CREDIT] Environment file exists:', fs.existsSync(envPath));
  console.log('🔧 [NICS24-CREDIT] DEBUG_MODE enabled - detailed logging active');
  console.log('🔧 [NICS24-CREDIT] Redis config:', {
    host: process.env.REDIS_HOST || '127.0.0.1',
    port: process.env.REDIS_PORT || 6379,
    db: process.env.REDIS_DB || 0
  });
}

/**
 * Update Redis progress for Laravel integration (matches baman24)
 */
async function updateRedisProgress(requestHash, progress, step, message) {
  try {
    const requestKey = `local_request:${requestHash}`;

    // Get existing request data
    const existingData = await redisPool.execute('get', requestKey);
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
    await redisPool.execute('setex', requestKey, 1800, JSON.stringify(requestData)); // 30 minutes TTL

    // Publish update to Laravel channels for real-time updates
    const channelName = `local_request_updates:${requestHash}`;
    await redisPool.execute('publish', channelName, JSON.stringify(requestData));

    if (process.env.DEBUG_MODE === 'true') {
      console.log('📡 [NICS24-REDIS] Progress update stored:', {
        progress,
        step,
        message: message.substring(0, 50) + '...'
      });
    }
  } catch (error) {
    console.error('❌ [NICS24-REDIS] Error updating progress:', error);
  }
}

/**
 * Poll Redis for OTP submission by user (matches baman24 structure)
 */
async function pollForOtpFromRedis(requestHash, timeoutSeconds = 300) {
  const startTime = Date.now();
  const pollInterval = 2000; // Check every 2 seconds

  console.log(`🔔 [NICS24-OTP-POLL] Starting OTP polling for ${timeoutSeconds} seconds`);

  while ((Date.now() - startTime) < timeoutSeconds * 1000) {
    try {
      // Get request data from Redis (using Laravel's key format - same as baman24)
      const requestKey = `local_request:${requestHash}`;
      const requestData = await redisPool.execute('get', requestKey);

      if (!requestData) {
        console.log('⚠️ [NICS24-OTP-POLL] Request not found in Redis');
        return null;
      }

      const parsedData = JSON.parse(requestData);

      // Check if OTP has been submitted (same structure as baman24)
      if (parsedData.received_otp && parsedData.received_otp.otp) {
        console.log('✅ [NICS24-OTP-POLL] OTP found in Redis!', {
          otp_length: parsedData.received_otp.otp.length,
          elapsed_time: Math.round((Date.now() - startTime) / 1000)
        });

        // Store the OTP data before clearing it
        const otpData = { ...parsedData.received_otp };

        // Clear the OTP from Redis to prevent reuse
        delete parsedData.received_otp;
        parsedData.updated_at = new Date().toISOString();
        await redisPool.execute('setex', requestKey, 1800, JSON.stringify(parsedData)); // 30 minutes TTL

        return otpData.otp; // Return just the OTP string
      }

      // Check if request was cancelled or failed
      if (parsedData.status === 'failed' || parsedData.status === 'cancelled') {
        console.log('🛑 [NICS24-OTP-POLL] Request was cancelled or failed', {
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
        console.log('🔄 [NICS24-OTP-POLL] Still waiting for OTP...', {
          status: parsedData.status,
          step: parsedData.step,
          elapsed: Math.round((Date.now() - startTime) / 1000)
        });
      }
    } catch (error) {
      console.error('❌ [NICS24-OTP-POLL] Error checking for OTP:', error.message);
    }

    // Wait before next check
    await new Promise(resolve => setTimeout(resolve, pollInterval));
  }

  console.log('⏰ [NICS24-OTP-POLL] OTP polling timeout reached');
  return null;
}

/**
 * Solve captcha using local API
 */
async function solveCaptcha(captchaBase64) {
    try {
        console.log('🔍 [NICS24-CREDIT] Solving captcha...');
        
        const response = await fetch(config.captchaApiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                image: captchaBase64
            })
        });

        if (!response.ok) {
            throw new Error(`Captcha API returned ${response.status}`);
        }

        const result = await response.json();
        console.log('✅ [NICS24-CREDIT] Captcha solved successfully');
        
        return result.predicted_text ;
    } catch (error) {
        console.error('❌ [NICS24-CREDIT] Captcha solving failed:', error.message);
        throw error;
    }
}

/**
 * Send OTP request to NICS24
 */
async function sendOtpRequest(page, nationalCode, mobile, requestHash) {
  try {
    console.log('📱 [NICS24-CREDIT] Sending OTP request...');
    
    // Navigate to new OTP page
    // Listen for requests to extract Bearer token from UserProfileGet API call
    let authToken = null;
    const tokenListener = async (request) => {
      try {
        console.log('🔍 [NICS24-CREDIT] Request:', request.url());
        const url = request.url();
        if (
          url.includes('etebarito.nics24.ir/api/version')
        ) {
          console.log('🔍 [NICS24-CREDIT] Request:', request.headers());
          authToken = request.headers()['authorization'];
        }
      } catch (e) {
        // Ignore errors
      }
    };
    page.on('request', tokenListener);

    await page.goto('https://etebarito.nics24.ir/share/new-otp', {
      waitUntil: 'networkidle',
      timeout: 120000
    });

    await page.screenshot({ path: 'screenshots/new-otp-page.png' });

    // Wait for the token to be captured (max 5 seconds)
    const maxWait = 5000;
    const pollInterval = 100;
    let waited = 0;
    while (!authToken && waited < maxWait) {
      await new Promise((res) => setTimeout(res, pollInterval));
      waited += pollInterval;
    }
    page.off('request', tokenListener);

    if (!authToken) {
      throw new Error('Could not extract Bearer token from UserProfileGet request');
    }

    // Get authorization token from page context

    

    if (!authToken) {
      throw new Error('Could not find authorization token');
    }

    console.log('🔍 [NICS24-CREDIT] authToken:', authToken);
    // Send OTP using fetch API within page context
    const otpResult = await page.evaluate(async ({ nationalCode, mobile, authToken }) => {
      const response = await fetch("https://etebarito.nics24.ir/api/ShareReportWithOtp/SendOtp-payment", {
        "credentials": "include",
        "headers": {
          "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0",
          "Accept": "application/json, text/plain, */*",
          "Accept-Language": "en-US,en;q=0.5",
          "Content-Type": "application/json",
          "Access-Control-Allow-Origin": "*",
          "Sec-Fetch-Dest": "empty",
          "Sec-Fetch-Mode": "cors",
          "Sec-Fetch-Site": "same-origin",
          "Authorization": authToken,
          "Priority": "u=0"
        },
        "referrer": "https://etebarito.nics24.ir/factor?from=otp",
        "body": JSON.stringify({
          "nationalCode": nationalCode,
          "phoneNumber": mobile,
          "isSeenCheck": false
        }),
        "method": "POST",
        "mode": "cors"
      });

      if(response.status === 401) {
        throw new Error('Unauthorized Error');
      }

      const responseData = await response.json();
      return {
        status: response.status,
        message: responseData.message,
        ok: response.ok,
        data: responseData
      };
    }, { nationalCode, mobile, authToken });

    if (!otpResult.ok) {
      throw new Error(`OTP request failed with status ${otpResult.status}`);
    }

    if(otpResult.message !== "رمز یکبار مصرف با موفقیت ارسال شد") {
      throw new Error(otpResult.message);
    }

    console.log('✅ [NICS24-CREDIT] OTP sent successfully');
    await updateRedisProgress(requestHash, 40, 'otp_sent', 'کد تایید ارسال شد. لطفاً کد دریافتی را وارد کنید...');

    return {
      success: true,
      authToken: authToken,
      data: otpResult.data
    };

  } catch (error) {
    console.error('❌ [NICS24-CREDIT] Failed to send OTP:', error.message);
    await updateRedisProgress(requestHash, 40, 'otp_sent', error.message + " لطفا مجدداً بعد از 30 دقیقه دوباره تلاش کنید");
    throw error;
  }
}

/**
 * Verify OTP and get credit score
 */
async function verifyOtpAndGetScore(page, nationalCode, mobile, otp, authToken, requestHash) {
  let retries = 0;
  while (retries < 3) {
    try {
      console.log('🔐 [NICS24-CREDIT] Verifying OTP and getting credit score...');


      console.log('🔍 [NICS24-CREDIT] the authToken:', authToken);
      // First get captcha
      const captchaResult = await page.evaluate(async ({
        authToken
      }) => {
        const nocache = Date.now();
        const response = await fetch(`https://etebarito.nics24.ir/api/Captcha/GetCaptcha?nocache=${nocache}`, {
          "credentials": "include",
          "headers": {
            "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0",
            "Accept": "application/json, text/plain, */*",
            "Accept-Language": "en-US,en;q=0.5",
            "Access-Control-Allow-Origin": "*",
            "Sec-Fetch-Dest": "empty",
            "Sec-Fetch-Mode": "cors",
            "Sec-Fetch-Site": "same-origin",
            "Authorization": authToken
          },
          "referrer": "https://etebarito.nics24.ir/share/new-otp/send-otp",
          "method": "GET",
          "mode": "cors"
        });

        console.log('🔍 [NICS24-CREDIT] captcha response:', response.status);
        console.log('🔍 [NICS24-CREDIT] captcha response:', response.ok);
        console.log('🔍 [NICS24-CREDIT] captcha response:', response.headers);
        console.log('🔍 [NICS24-CREDIT] captcha response:', response);

        const data = await response.json();
        return {
          status: response.status,
          ok: response.ok,
          image: data.image,
          code: data.code
        };
      }, {
        authToken
      });

      if (!captchaResult.ok) {
        throw new Error(`Captcha request failed with status ${captchaResult.status}`);
      }

      console.log('🔍 [NICS24-CREDIT] captchaResult:', captchaResult);

      // Solve captcha
      const captchaText = await solveCaptcha(captchaResult.image);
      console.log('🔍 [NICS24-CREDIT] Captcha solved:', captchaText);

      // Verify OTP with captcha
      const verifyResult = await page.evaluate(async ({
        nationalCode,
        mobile,
        otp,
        captchaText,
        captchaCode,
        authToken
      }) => {
        const response = await fetch("https://etebarito.nics24.ir/api/ShareReportWithOtp", {
          "credentials": "include",
          "headers": {
            "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0",
            "Accept": "application/json, text/plain, */*",
            "Accept-Language": "en-US,en;q=0.5",
            "Content-Type": "application/json",
            "Access-Control-Allow-Origin": "*",
            "nomessage": "true",
            "Sec-Fetch-Dest": "empty",
            "Sec-Fetch-Mode": "cors",
            "Sec-Fetch-Site": "same-origin",
            "Authorization": authToken,
            "Priority": "u=0"
          },
          "referrer": "https://etebarito.nics24.ir/share/new-otp/send-otp",
          "body": JSON.stringify({
            "nationalCode": nationalCode,
            "phoneNumber": mobile,
            "otp": otp,
            "captcha": captchaText,
            "code": captchaCode
          }),
          "method": "POST",
          "mode": "cors"
        });

        return {
          status: response.status,
          ok: response.ok,
          data: await response.json()
        };
      }, {
        nationalCode,
        mobile,
        otp,
        captchaText,
        captchaCode: captchaResult.code,
        authToken
      });

      console.log('🔍 [NICS24-CREDIT] OTP verification response:', {
        status: verifyResult.status,
        ok: verifyResult.ok,
        data: verifyResult.data
      });

      if (verifyResult.data.message === 'کد امنیتی اشتباه است') {
        console.log(`❌ [NICS24-CREDIT] Captcha incorrect. Retry attempt ${retries + 1} of 3.`);
        retries++;
        if (requestHash) {
          await updateRedisProgress(requestHash, 75, 'captcha_error', `خطا در تشخیص کپچا. تلاش مجدد (${retries}/3)...`);
        }
        continue;
      }

      // Check for specific error types based on response
      if (!verifyResult.ok || verifyResult.status !== 200) {
        // Check if it's a wrong OTP error
        if (verifyResult.status === 400 ||
          (verifyResult.data &&
            ((verifyResult.data.message && verifyResult.data.message.includes('otp')) ||
              (verifyResult.data.error && verifyResult.data.error.includes('otp')) ||
              (verifyResult.data.errors && JSON.stringify(verifyResult.data.errors).includes('otp'))))) {

          console.log('❌ [NICS24-CREDIT] Invalid OTP entered by user');

          // Update Redis with wrong OTP error
          if (requestHash) {
            await updateRedisProgress(requestHash, 75, 'otp_error', 'کد تایید وارد شده اشتباه است. لطفاً کد صحیح را وارد کنید.');
          }

          return {
            success: false,
            error: 'INVALID_OTP',
            message: 'کد تایید وارد شده اشتباه است. لطفاً کد صحیح را وارد کنید.',
            data: {
              allowRetry: true,
              retryMessage: 'لطفاً کد تایید صحیح را مجدداً وارد کنید'
            }
          };
        }

        // Check if it's a captcha error
        if (verifyResult.data &&
          ((verifyResult.data.message && verifyResult.data.message.includes('captcha')) ||
            (verifyResult.data.error && verifyResult.data.error.includes('captcha')))) {

          console.log('❌ [NICS24-CREDIT] Captcha verification failed');

          // Update Redis with captcha error
          if (requestHash) {
            await updateRedisProgress(requestHash, 75, 'captcha_error', 'خطا در تشخیص کپچا. در حال تلاش مجدد...');
          }

          return {
            success: false,
            error: 'CAPTCHA_ERROR',
            message: 'خطا در تشخیص کپچا. لطفاً مجدداً تلاش کنید.',
            data: {
              allowRetry: true,
              retryMessage: 'سیستم مجدداً تلاش خواهد کرد'
            }
          };
        }

        // Other API errors
        console.log('❌ [NICS24-CREDIT] API error during OTP verification');

        // Update Redis with API error
        if (requestHash) {
          await updateRedisProgress(requestHash, 100, 'api_error', 'خطا در ارتباط با سرور. لطفاً مجدداً تلاش کنید.');
        }

        return {
          success: false,
          error: 'API_ERROR',
          message: 'خطا در ارتباط با سرور. لطفاً مجدداً تلاش کنید.',
          data: {
            allowRetry: false,
            status: verifyResult.status,
            response: verifyResult.data
          }
        };
      }

      // Check the actual response data for success/error indicators
      if (verifyResult.data) {
        // Check for success indicators in the response
        if (verifyResult.data.success === true ||
          verifyResult.data.status === 'success' ||
          (verifyResult.data.message && verifyResult.data.message.includes('موفق')) ||
          (verifyResult.data.data && verifyResult.data.data.creditScore)) {

          console.log('✅ [NICS24-CREDIT] OTP verified and credit score retrieved successfully');

          return {
            success: true,
            data: verifyResult.data
          };
        }

        // Check for business logic errors in successful HTTP response
        // BUT exclude captcha errors which should be retried
        if (verifyResult.data.success === false ||
          verifyResult.data.status === 'error' ||
          verifyResult.data.statusCode === 215 || // Specific error code for invalid OTP
          (verifyResult.data.message && (
            verifyResult.data.message.includes('اشتباه') ||
            verifyResult.data.message.includes('نامعتبر') ||
            verifyResult.data.message.includes('خطا') ||
            verifyResult.data.message.includes('نیست') ||
            verifyResult.data.message.includes('صحیح نیست') ||
            verifyResult.data.message.includes('رمز یکبار صحیح نیست')
          ) && !verifyResult.data.message.includes('کد امنیتی اشتباه است'))) {

          console.log('❌ [NICS24-CREDIT] Business logic error in OTP verification');

          // Check if it's specifically an OTP error (should allow retry)
          if (verifyResult.data.message && (
            verifyResult.data.message.includes('رمز یکبار') ||
            verifyResult.data.message.includes('کد تایید') ||
            verifyResult.data.message.includes('صحیح نیست') ||
            verifyResult.data.statusCode === 215
          )) {
            console.log('❌ [NICS24-CREDIT] Invalid OTP detected - allowing retry');
            
            // Update Redis with OTP error (progress < 100 to allow retry)
            if (requestHash) {
              await updateRedisProgress(requestHash, 75, 'otp_error', verifyResult.data.message || 'کد تایید وارد شده اشتباه است. لطفاً کد صحیح را وارد کنید.');
            }

            return {
              success: false,
              error: 'INVALID_OTP',
              message: verifyResult.data.message || 'کد تایید وارد شده اشتباه است. لطفاً کد صحیح را وارد کنید.',
              data: {
                allowRetry: true,
                retryMessage: 'لطفاً کد تایید صحیح را مجدداً وارد کنید'
              }
            };
          }

          // For other business logic errors (final failure)
          if (requestHash) {
            await updateRedisProgress(requestHash, 100, 'verification_failed', verifyResult.data.message || 'خطا در تایید کد. لطفاً مجدداً تلاش کنید.');
          }

          return {
            success: false,
            error: 'VERIFICATION_FAILED',
            message: verifyResult.data.message || 'خطا در تایید کد. لطفاً مجدداً تلاش کنید.',
            data: {
              allowRetry: false,
              response: verifyResult.data
            }
          };
        }
      }

      // If we get here, it's an unusual response format that doesn't match our success or error patterns
      console.log('⚠️ [NICS24-CREDIT] Unusual response format, treating as error');
      console.log('❌ [NICS24-CREDIT] Response data:', JSON.stringify(verifyResult.data, null, 2));

      // Update Redis with unusual response error
      if (requestHash) {
        await updateRedisProgress(requestHash, 100, 'unusual_response', 'پاسخ نامعتبر از سرور دریافت شد. لطفاً مجدداً تلاش کنید.');
      }

      return {
        success: false,
        error: 'UNUSUAL_RESPONSE',
        message: 'پاسخ نامعتبر از سرور دریافت شد. لطفاً مجدداً تلاش کنید.',
        data: {
          allowRetry: true,
          response: verifyResult.data
        }
      };

    } catch (error) {
      console.error('❌ [NICS24-CREDIT] Failed to verify OTP and get credit score:', error.message);

      // Update Redis with system error
      if (requestHash) {
        await updateRedisProgress(requestHash, 100, 'system_error', 'خطای سیستمی رخ داده است. لطفاً مجدداً تلاش کنید.');
      }

      return {
        success: false,
        error: 'SYSTEM_ERROR',
        message: 'خطای سیستمی رخ داده است. لطفاً مجدداً تلاش کنید.',
        data: {
          allowRetry: false,
          errorDetails: error.message
        }
      };
    }
  }
  // If all retries fail, return a final error
  console.log('❌ [NICS24-CREDIT] All captcha retry attempts failed.');
  if (requestHash) {
    await updateRedisProgress(requestHash, 100, 'captcha_error', 'خطا در تشخیص کپچا. پس از 3 تلاش ناموفق، عملیات متوقف شد.');
  }
  return {
    success: false,
    error: 'CAPTCHA_ERROR',
    message: 'خطا در تشخیص کپچا. لطفاً بعداً مجدداً تلاش کنید.',
    data: {
      allowRetry: false
    }
  };
}

/**
 * Mark OTP as required in Redis (notify frontend that OTP has been sent)
 */
async function markOtpRequired(requestHash, otpData) {
  try {
    const requestKey = `local_request:${requestHash}`;

    // Get existing request data
    const existingData = await redisPool.execute('get', requestKey);
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
    await redisPool.execute('setex', requestKey, 1800, JSON.stringify(requestData)); // 30 minutes TTL

    console.log('🔍 [NICS24-REDIS-DEBUG] Stored OTP required data in Redis:', {
      key: requestKey,
      status: requestData.status,
      step: requestData.step,
      progress: requestData.progress,
      message: requestData.current_message
    });

    // Publish update to Laravel channels
    const channelName = `local_request_updates:${requestHash}`;
    await redisPool.execute('publish', channelName, JSON.stringify(requestData));

    console.log('📡 [NICS24-REDIS-DEBUG] Published update to channel:', channelName);

    if (process.env.DEBUG_MODE === 'true') {
      console.log('📡 [NICS24-REDIS] OTP required status stored:', {
        authToken: otpData.authToken?.substring(0, 8) + '...',
        success: otpData.success
      });
    }
  } catch (error) {
    console.error('❌ [NICS24-REDIS] Error marking OTP required:', error);
  }
}

/**
 * Main credit score inquiry function with polling for OTP
 */
export async function handleCreditScoreInquiry(data) {
  console.log('\n🚀 [NICS24-CREDIT-FLOW] Starting complete credit score inquiry with polling');

  const { mobile, nationalCode, requestHash, resendSms = false, hash } = data;
  
  // Handle resend SMS request
  if (resendSms) {
    console.log('🔄 [NICS24-CREDIT-FLOW] Resend SMS request detected');
    return await handleResendSmsOnly(mobile, nationalCode, requestHash);
  }
  
  const maxRetries = 3;
  let retryCount = 0;

  // Initialize browser with stealth configuration
  console.log('🌐 [NICS24-CREDIT-FLOW] Initializing browser with stealth...');
  const { chromium } = await import('playwright');
  const browser = await chromium.launch({ 
    headless: true,
    args: getStealthLaunchArgs(getRandomUserAgent())
  });
  console.log('✅ [NICS24-CREDIT-FLOW] Browser initialized successfully');

  try {
    // Start the flow with retries
    console.log(`🔄 [NICS24-CREDIT-FLOW] Starting main process loop (max ${maxRetries} attempts)`);
    while (retryCount < maxRetries) {
      console.log(`\n📊 [NICS24-CREDIT-FLOW] === ATTEMPT ${retryCount + 1}/${maxRetries} ===`);
      try {
        // Update progress via Redis
        if (requestHash) {
          await updateRedisProgress(requestHash, 30, 'authentication', 'ارسال درخواست به سامانه نیکس۲۴...');
        }

        console.log('🔑 [NICS24-CREDIT-FLOW] Getting authenticated page...');
        const loginResult = await nics24.login();
        
        if (loginResult.status !== 'success') {
          throw new Error('Failed to initialize NICS24 session');
        }
        
        const page = loginResult.data.page;
        console.log('✅ [NICS24-CREDIT-FLOW] Successfully got authenticated page');

        // Update progress
        if (requestHash) {
          await updateRedisProgress(requestHash, 50, 'authentication', 'پردازش درخواست...');
        }

        // Send OTP (Step 1)
        console.log('📱 [NICS24-CREDIT-FLOW] Step 1: Sending OTP...');
        const otpResult = await sendOtpRequest(page, nationalCode, mobile, requestHash);
        
        if (!otpResult.success) {
          throw new Error('Failed to send OTP');
        }

        console.log('✅ [NICS24-CREDIT-FLOW] OTP sent successfully');

        // Mark OTP as required in Redis (notify frontend)
        if (requestHash) {
          console.log('🎯 [NICS24-DEBUG] About to mark OTP required with data:', {
            requestHash: requestHash,
            authToken: otpResult.authToken,
            success: otpResult.success
          });
          await markOtpRequired(requestHash, otpResult.data || otpResult);
          console.log('✅ [NICS24-DEBUG] Successfully marked OTP required in Redis');
        }

        // Wait for OTP from Redis (Step 2)
        console.log('⏳ [NICS24-CREDIT-FLOW] Step 2: Waiting for OTP from user...');
        const otpPollResult = await pollForOtpFromRedis(requestHash, 300); // 5 minutes (now in seconds)

        // Handle different polling scenarios
        if (!otpPollResult) {
          // True timeout scenario
          await updateRedisProgress(requestHash, 100, 'timeout', 'زمان ورود کد تایید به پایان رسید. لطفاً مجدداً تلاش کنید.');
          return {
            status: 'timeout',
            code: 'OTP_TIMEOUT',
            message: 'زمان ورود کد تایید به پایان رسید',
            data: {
              mobile: mobile,
              nationalCode: nationalCode,
              canRetry: true
            }
          };
        }
        
        // Handle request failure/cancellation
        if (otpPollResult.error) {
          console.log(`❌ [NICS24-CREDIT-FLOW] Request ${otpPollResult.status}: ${otpPollResult.reason}`);
          await updateRedisProgress(requestHash, 100, 'failed', otpPollResult.message);
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

        // Extract OTP string for successful case
        const otp = otpPollResult;

        console.log('✅ [NICS24-CREDIT-FLOW] OTP received from user');

        // Update progress
        if (requestHash) {
          await updateRedisProgress(requestHash, 80, 'processing', 'در حال دریافت گزارش اعتباری...');
        }

        // Verify OTP and get credit score (Step 3)
        console.log('🔐 [NICS24-CREDIT-FLOW] Step 3: Verifying OTP and getting credit score...');
        const scoreResult = await verifyOtpAndGetScore(page, nationalCode, mobile, otp, otpResult.authToken, requestHash);

        // Handle different types of verification errors
        if (!scoreResult.success) {
          // Close browser before returning error
          await browser.close();
          
          // Handle specific error types
          if (scoreResult.error === 'INVALID_OTP') {
            console.log('❌ [NICS24-CREDIT-FLOW] User entered wrong OTP');
            return {
              status: 'error',
              code: 'INVALID_OTP',
              message: scoreResult.message,
              data: {
                mobile: mobile,
                nationalCode: nationalCode,
                allowRetry: true,
                retryType: 'otp_only', // Only need to re-enter OTP, not restart entire flow
                requestHash: requestHash,
                ...scoreResult.data
              }
            };
          } else if (scoreResult.error === 'CAPTCHA_ERROR') {
            console.log('❌ [NICS24-CREDIT-FLOW] Captcha error, can retry verification');
            return {
              status: 'error',
              code: 'CAPTCHA_ERROR',
              message: scoreResult.message,
              data: {
                mobile: mobile,
                nationalCode: nationalCode,
                allowRetry: true,
                retryType: 'verification_only', // Retry just the verification step
                requestHash: requestHash,
                ...scoreResult.data
              }
            };
          } else if (scoreResult.error === 'VERIFICATION_FAILED') {
            console.log('❌ [NICS24-CREDIT-FLOW] Business logic error in verification');
            return {
              status: 'error',
              code: 'VERIFICATION_FAILED',
              message: scoreResult.message,
              data: {
                mobile: mobile,
                nationalCode: nationalCode,
                allowRetry: scoreResult.data?.allowRetry || false,
                requestHash: requestHash,
                ...scoreResult.data
              }
            };
          } else {
            // System or API errors
            console.log('❌ [NICS24-CREDIT-FLOW] System/API error in verification');
            
            // For system errors, we might want to retry the entire flow
            if (retryCount < maxRetries - 1) {
              console.log(`🔄 [NICS24-CREDIT-FLOW] Will retry entire flow (attempt ${retryCount + 2}/${maxRetries})`);
              throw new Error(`OTP verification failed: ${scoreResult.message}`);
            } else {
              return {
                status: 'error',
                code: scoreResult.error || 'SYSTEM_ERROR',
                message: scoreResult.message || 'خطا در تایید کد. لطفاً مجدداً تلاش کنید.',
                data: {
                  mobile: mobile,
                  nationalCode: nationalCode,
                  allowRetry: false,
                  requestHash: requestHash,
                  ...scoreResult.data
                }
              };
            }
          }
        }

        console.log('✅ [NICS24-CREDIT-FLOW] Credit score retrieved successfully');

        // Update final progress
        if (requestHash) {
          await updateRedisProgress(requestHash, 100, 'completed', 'گزارش اعتباری با موفقیت دریافت شد');
        }

        // Close browser
        await browser.close();

        return {
          status: 'success',
          message: 'گزارش اعتباری با موفقیت دریافت شد',
          data: {
            mobile: mobile,
            nationalCode: nationalCode,
            creditScore: scoreResult.data,
            provider: 'nics24',
            timestamp: new Date().toISOString(),
            requestHash: requestHash
          }
        };

      } catch (error) {
        console.error(`❌ [NICS24-CREDIT-FLOW] Attempt ${retryCount + 1} failed:`, error.message);
        retryCount++;
        
        if (retryCount >= maxRetries) {
          console.error('💥 [NICS24-CREDIT-FLOW] All attempts failed');
          
          if (requestHash) {
            await updateRedisProgress(requestHash, 100, 'failed', 'خطا در دریافت گزارش اعتباری. لطفاً مجدداً تلاش کنید.');
          }
          
          break;
        } else {
          console.log(`🔄 [NICS24-CREDIT-FLOW] Retrying in 3 seconds... (${maxRetries - retryCount} attempts left)`);
          await new Promise(resolve => setTimeout(resolve, 3000));
        }
      }
    }

    // If we reach here, all retries failed
    await browser.close();
    
    return {
      status: 'error',
      code: 'MAX_RETRIES_EXCEEDED',
      message: 'خطا در دریافت گزارش اعتباری پس از چندین تلاش',
      data: {
        mobile: mobile,
        nationalCode: nationalCode,
        retryCount: retryCount,
        canRetry: true
      }
    };

  } catch (error) {
    console.error('💥 [NICS24-CREDIT-FLOW] Unexpected error:', error.message);
    
    try {
      await browser.close();
    } catch (closeError) {
      console.error('❌ [NICS24-CREDIT-FLOW] Failed to close browser:', closeError.message);
    }
    
    if (requestHash) {
      await updateRedisProgress(requestHash, 100, 'error', 'خطای غیرمنتظره در سیستم');
    }
    
    return {
      status: 'error',
      code: 'UNEXPECTED_ERROR',
      message: 'خطای غیرمنتظره در سیستم',
      data: {
        mobile: mobile,
        nationalCode: nationalCode,
        error: error.message
      }
    };
  }
}

/**
 * Handle resend SMS only (when user requests resend)
 */
async function handleResendSmsOnly(mobile, nationalCode, requestHash) {
  console.log('🔄 [NICS24-CREDIT] Processing resend SMS request...');
  
  const { chromium } = await import('playwright');
  const browser = await chromium.launch({ 
    headless: true,
    args: getStealthLaunchArgs(getRandomUserAgent())
  });

  try {
    const loginResult = await nics24.login();
    if (loginResult.status !== 'success') {
      throw new Error('Failed to initialize NICS24 session');
    }
    
    const page = loginResult.data.page;
    
    // Send OTP again
    const otpResult = await sendOtpRequest(page, nationalCode, mobile, requestHash);
    
    await browser.close();
    
    if (otpResult.success) {
      // Mark OTP as required in Redis (notify frontend about resend)
      if (requestHash) {
        console.log('🎯 [NICS24-RESEND-DEBUG] Marking OTP required after resend');
        await markOtpRequired(requestHash, otpResult.data || otpResult);
        console.log('✅ [NICS24-RESEND-DEBUG] Successfully marked OTP required after resend');
      }

      return {
        status: 'success',
        message: 'کد تایید مجدداً ارسال شد',
        data: {
          mobile: mobile,
          nationalCode: nationalCode,
          resent: true
        }
      };
    } else {
      throw new Error('Failed to resend OTP');
    }
    
  } catch (error) {
    console.error('❌ [NICS24-CREDIT] Resend SMS failed:', error.message);
    
    try {
      await browser.close();
    } catch (closeError) {
      console.error('❌ [NICS24-CREDIT] Failed to close browser:', closeError.message);
    }
    
    return {
      status: 'error',
      code: 'RESEND_FAILED',
      message: 'خطا در ارسال مجدد کد تایید',
      data: {
        mobile: mobile,
        nationalCode: nationalCode
      }
    };
  }
}

/**
 * Handle OTP retry - when user enters wrong OTP and wants to try again
 */
export async function handleOtpRetry(data) {
  console.log('🔄 [NICS24-CREDIT] Processing OTP retry request...');
  
  const { mobile, nationalCode, requestHash, otp, authToken } = data;
  
  if (!otp || !authToken || !requestHash) {
    return {
      status: 'error',
      code: 'MISSING_PARAMETERS',
      message: 'لطفاً کد تایید را وارد کنید',
      data: {
        mobile: mobile,
        nationalCode: nationalCode,
        allowRetry: true
      }
    };
  }

  const { chromium } = await import('playwright');
  const browser = await chromium.launch({ 
    headless: true,
    args: getStealthLaunchArgs(getRandomUserAgent())
  });

  try {
    const loginResult = await nics24.login();
    if (loginResult.status !== 'success') {
      throw new Error('Failed to initialize NICS24 session');
    }
    
    const page = loginResult.data.page;
    
    // Update progress
    if (requestHash) {
      await updateRedisProgress(requestHash, 80, 'retrying_otp', 'در حال تایید کد تایید جدید...');
    }
    
    // Verify the new OTP
    const scoreResult = await verifyOtpAndGetScore(page, nationalCode, mobile, otp, authToken, requestHash);
    
    await browser.close();
    
    if (scoreResult.success) {
      // Update final progress
      if (requestHash) {
        await updateRedisProgress(requestHash, 100, 'completed', 'گزارش اعتباری با موفقیت دریافت شد');
      }
      
      return {
        status: 'success',
        message: 'گزارش اعتباری با موفقیت دریافت شد',
        data: {
          mobile: mobile,
          nationalCode: nationalCode,
          creditScore: scoreResult.data,
          provider: 'nics24',
          timestamp: new Date().toISOString(),
          requestHash: requestHash
        }
      };
    } else {
      // Return the specific error from verification
      return {
        status: 'error',
        code: scoreResult.error || 'OTP_RETRY_FAILED',
        message: scoreResult.message || 'خطا در تایید کد. لطفاً مجدداً تلاش کنید.',
        data: {
          mobile: mobile,
          nationalCode: nationalCode,
          allowRetry: scoreResult.data?.allowRetry || false,
          requestHash: requestHash,
          ...scoreResult.data
        }
      };
    }
    
  } catch (error) {
    console.error('❌ [NICS24-CREDIT] OTP retry failed:', error.message);
    
    try {
      await browser.close();
    } catch (closeError) {
      console.error('❌ [NICS24-CREDIT] Failed to close browser:', closeError.message);
    }
    
    return {
      status: 'error',
      code: 'OTP_RETRY_ERROR',
      message: 'خطا در تایید مجدد کد تایید',
      data: {
        mobile: mobile,
        nationalCode: nationalCode,
        allowRetry: false,
        requestHash: requestHash
      }
    };
  }
}

// Legacy function exports for backward compatibility (matches baman24)
export async function sendOtpSms(data) {
  console.log('⚠️ [NICS24-LEGACY] sendOtpSms called - redirecting to new Redis pub/sub flow');
  return await handleCreditScoreInquiry(data);
}

export async function handleOtpVerification(data) {
  console.log('⚠️ [NICS24-LEGACY] handleOtpVerification called - this should not happen with Redis pub/sub flow');
  return {
    status: 'error',
    code: 'LEGACY_FUNCTION_CALLED',
    message: 'خطایی در تایید کد پیامک رخ داد لطفا مجدد تلاش کنید'
  };
}

export function checkForErrors() {
  console.log('🎉 [NICS24-ERROR-CHECK] No UI errors detected - API-based service with Redis pub/sub');
  return { type: 'no_error' };
}

export function checkForOtpErrors() {
  console.log('🎉 [NICS24-OTP-ERROR-CHECK] No UI OTP errors detected - Redis pub/sub handles OTP flow');
  return { type: 'no_error' };
}