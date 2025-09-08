import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import dotenv from 'dotenv';
import redisUpdater from '../redisUpdater.js';
import requestLockManager from '../requestLockManager.js';

// Get current directory for ES modules
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Load environment variables from .env file in inquiry-provider root
const envPath = path.resolve(__dirname, '../../.env');
dotenv.config({ path: envPath });

/**
 * Provider-agnostic credit score rating service
 * Supports Redis Pub/Sub for seamless OTP handling
 */
async function handle(data) {
  console.log('🚀 [CREDIT-SCORE] Starting credit score rating process with Redis pub/sub');

  const { 
    mobile
  } = data;
  let provider;
  if (mobile == '09153887809' || mobile == '09112697701' || mobile == '09104775864' || mobile == '09920860628Failed to send OTP: "محدودیت در درخواست گزارش') {
    provider = 'rade';
  } else {
    provider = 'rade';
  }

  const {
    national_code, 
    hash, 
    otp, 
    requestHash, 
    serviceSlug = 'credit-score-rating',
    resendSms = false // Resend SMS flag
  } = data;

  // if (mobile !== '09153887809') {
  //   return {
  //     status: 'error',
  //     code: 'CAPTCHA_ERROR',
  //     message: 'سرویس در حال به روزسانی است لطفا ساعت ۷ مجدد درخواست ارسال کنید',
  //     data: {
  //       mobile: mobile,
  //       nationalCode: national_code,
  //       hash: hash,
  //       otp: otp,
  //       requestHash: requestHash,
  //     }
  //   }
  // }
  
  const nationalCode = national_code; // For compatibility with existing code
  
  console.log('📋 [CREDIT-SCORE] Input data:', {
    mobile: mobile ? `${mobile.slice(0, 4)}***${mobile.slice(-2)}` : 'N/A',
    nationalCode: nationalCode ? `${nationalCode.slice(0, 3)}***${nationalCode.slice(-2)}` : 'N/A',
    hasHash: !!hash,
    hasOtp: !!otp,
    hashValue: hash ? `${hash.slice(0, 8)}...` : 'N/A',
    otpValue: otp ? `${otp.slice(0, 2)}***` : 'N/A',
    requestHash: requestHash,
    serviceSlug: serviceSlug,
    provider: provider
  });

  // Basic validation
  if (!mobile || !nationalCode) {
    console.log('❌ [CREDIT-SCORE] Missing required fields');
    return {
      status: 'error',
      code: 'VALIDATION_ERROR',
      message: 'شماره موبایل و کد ملی الزامی است'
    };
  }

  // Handle resend SMS request
  if (resendSms) {
    console.log('🔄 [CREDIT-SCORE] Resend SMS request detected');
    
    // For resend, we only need to send SMS again with same data
    // The hash parameter should contain the OTP hash from previous request
    if (!hash) {
      console.log('❌ [CREDIT-SCORE] Missing hash for resend SMS');
      return {
        status: 'error',
        code: 'MISSING_HASH',
        message: 'هش درخواست برای ارسال مجدد پیام یافت نشد'
      };
    }
    
    console.log('📱 [CREDIT-SCORE] Processing resend SMS request...');
    return await handleResendSms(mobile, nationalCode, hash, provider, requestHash);
  }

  // Check for OTP parameters - this indicates legacy call
  if (hash && otp) {
    console.log('⚠️ [CREDIT-SCORE] Legacy OTP verification call detected - this should not happen with Redis pub/sub');
    return {
      status: 'error',
      code: 'LEGACY_CALL_DETECTED',
      message: 'OTP verification should be handled via Redis pub/sub, not separate API calls'
    };
  }

  // Redis pub/sub flow - single continuous process
  console.log('🔄 [CREDIT-SCORE] Starting Redis pub/sub flow...');

  // Check if provider supports Redis pub/sub flow
  const supportedProviders = ['baman24', 'nics24'];
  if (!supportedProviders.includes(provider)) {
    console.log(`⚠️ [CREDIT-SCORE] Provider ${provider} not yet updated for Redis pub/sub, using legacy flow`);
    return await handleLegacyFlow(data, provider);
  }

  // Request lock to prevent multiple simultaneous requests for same mobile
  const lockResult = await requestLockManager.acquireLock(mobile, nationalCode, serviceSlug, requestHash);

  if (!lockResult.success) {
    console.log('🔒 [CREDIT-SCORE] Request already in progress for this mobile');
    return {
      status: 'error',
      code: 'REQUEST_IN_PROGRESS',
      message: lockResult.message || 'درخواست شما در حال پردازش است. لطفاً صبر کنید.',
      ...(lockResult.existingData && { existingData: lockResult.existingData })
    };
  }

  // Handle page refresh scenario - return current progress transparently
  if (lockResult.isPageRefresh) {
    console.log('🔄 [CREDIT-SCORE] Same request detected, returning current progress transparently');
    
    try {
      // Get current progress from Redis
      const currentProgress = await redisUpdater.getProgress(requestHash);
      
      if (currentProgress) {
        console.log('📊 [CREDIT-SCORE] Current progress found:', currentProgress);
        
        // Return current status as if it's a normal response
        return {
          status: currentProgress.status,
          step: currentProgress.step,
          progress: currentProgress.progress,
          message: currentProgress.message,
          requires_otp: currentProgress.requires_otp,
          otp_data: currentProgress.otp_data,
          is_completed: currentProgress.is_completed,
          is_failed: currentProgress.is_failed,
          result_data: currentProgress.result_data,
          error_data: currentProgress.error_data,
          // Add transparent flag for debugging
          _transparent_return: true
        };
      } else {
        console.log('⚠️ [CREDIT-SCORE] No progress found, starting fresh');
        // If no progress data, continue with normal flow
      }
    } catch (error) {
      console.error('❌ [CREDIT-SCORE] Error getting current progress:', error.message);
      // If error getting progress, continue with normal flow
    }
  }

  try {
    console.log(`📡 [CREDIT-SCORE] Processing with provider: ${provider}`);
    
    // Load the provider module
    const providerModule = await import(`../../providers/${provider}/services/creditScoreService.js`);
    
    if (!providerModule.handleCreditScoreInquiry) {
      console.log(`❌ [CREDIT-SCORE] Provider ${provider} does not support Redis pub/sub flow`);
      await requestLockManager.releaseLock(mobile, nationalCode, serviceSlug);
      return {
        status: 'error',
        code: 'PROVIDER_NOT_SUPPORTED',
        message: `Provider ${provider} does not support Redis pub/sub flow`
      };
    }

    // Prepare data for the provider's complete flow function
    const providerData = {
      mobile,
      nationalCode,
      requestHash
    };

    let result;
    
    try {
      console.log('🔄 [CREDIT-SCORE] Starting complete credit score inquiry with Redis pub/sub');
      
      // Call the provider's complete flow function (handles SMS, OTP wait, verification, result)
      result = await providerModule.handleCreditScoreInquiry(providerData);
      
      console.log('✅ [CREDIT-SCORE] Complete flow processing completed');
      
    } catch (providerError) {
      console.error('❌ [CREDIT-SCORE] Provider processing error:', providerError.message);
      console.error('📋 [CREDIT-SCORE] Full provider error:', providerError);
      
      result = {
        status: 'error',
        code: 'PROVIDER_PROCESSING_ERROR',
        message: `خطا در پردازش توسط ${provider}: ${providerError.message}`
      };
    }

    // Log the result
    console.log('📊 [CREDIT-SCORE] Final result:', {
      status: result?.status,
      code: result?.code,
      hasData: !!result?.data,
      message: result?.message?.substring(0, 100) || 'N/A'
    });

    // Release the lock
    await requestLockManager.releaseLock(mobile, nationalCode, serviceSlug);

    return result;

  } catch (error) {
    console.error('💥 [CREDIT-SCORE] Unexpected error:', error.message);
    console.error('📋 [CREDIT-SCORE] Full error details:', error);
    
    // Release the lock in case of error
    await requestLockManager.releaseLock(mobile, nationalCode, serviceSlug);
    
    return {
      status: 'error',
      code: 'UNEXPECTED_ERROR',
      message: 'خطای غیرمنتظره در پردازش درخواست'
    };
  }
}

/**
 * Handle legacy flow for providers that haven't been updated to Redis pub/sub yet
 */
async function handleLegacyFlow(data, provider) {
  const { 
    mobile, 
    national_code: nationalCode, 
    hash, 
    otp, 
    requestHash, 
    serviceSlug = 'credit-score-rating'
  } = data;

  console.log(`🔄 [CREDIT-SCORE-LEGACY] Processing with legacy provider: ${provider}`);

  // Request lock to prevent multiple simultaneous requests for same mobile
  const lockResult = await requestLockManager.acquireLock(mobile, nationalCode, serviceSlug, requestHash);

  if (!lockResult.success) {
    console.log('🔒 [CREDIT-SCORE-LEGACY] Request already in progress for this mobile');
    return {
      status: 'error',
      code: 'REQUEST_IN_PROGRESS',
      message: lockResult.message || 'درخواست شما در حال پردازش است. لطفاً صبر کنید.',
      ...(lockResult.existingData && { existingData: lockResult.existingData })
    };
  }

  // Handle page refresh scenario - return current progress transparently
  if (lockResult.isPageRefresh) {
    console.log('🔄 [CREDIT-SCORE-LEGACY] Same request detected, returning current progress transparently');
    
    try {
      // Get current progress from Redis
      const currentProgress = await redisUpdater.getProgress(requestHash);
      
      if (currentProgress) {
        console.log('📊 [CREDIT-SCORE-LEGACY] Current progress found:', currentProgress);
        
        // Return current status as if it's a normal response
        return {
          status: currentProgress.status,
          step: currentProgress.step,
          progress: currentProgress.progress,
          message: currentProgress.message,
          requires_otp: currentProgress.requires_otp,
          otp_data: currentProgress.otp_data,
          is_completed: currentProgress.is_completed,
          is_failed: currentProgress.is_failed,
          result_data: currentProgress.result_data,
          error_data: currentProgress.error_data,
          // Add transparent flag for debugging
          _transparent_return: true
        };
      } else {
        console.log('⚠️ [CREDIT-SCORE-LEGACY] No progress found, starting fresh');
        // If no progress data, continue with normal flow
      }
    } catch (error) {
      console.error('❌ [CREDIT-SCORE-LEGACY] Error getting current progress:', error.message);
      // If error getting progress, continue with normal flow
    }
  }

  try {
    // Load the provider module
    const providerModule = await import(`../../providers/${provider}/services/creditScoreService.js`);
    
    // Prepare data for the provider
    const providerData = {
      mobile,
      nationalCode,
      hash,
      otp,
      requestHash,
      serviceSlug
    };

    let result;
    
    try {
      // Check if it's an OTP verification request
      if (hash && otp) {
        console.log('🔐 [CREDIT-SCORE-LEGACY] OTP verification mode detected');
        result = await providerModule.handleOtpVerification(providerData);
      } else {
        console.log('📨 [CREDIT-SCORE-LEGACY] SMS send mode detected');
        result = await providerModule.sendOtpSms(providerData);
      }
      
      console.log('✅ [CREDIT-SCORE-LEGACY] Provider processing completed');
      
    } catch (providerError) {
      console.error('❌ [CREDIT-SCORE-LEGACY] Provider processing error:', providerError.message);
      console.error('📋 [CREDIT-SCORE-LEGACY] Full provider error:', providerError);
      
      result = {
        status: 'error',
        code: 'PROVIDER_PROCESSING_ERROR',
        message: `خطا در پردازش توسط ${provider}: ${providerError.message}`
      };
    }

    // Release the lock
    await requestLockManager.releaseLock(mobile, nationalCode, serviceSlug);

    return result;

  } catch (error) {
    console.error('💥 [CREDIT-SCORE-LEGACY] Unexpected error:', error.message);
    console.error('📋 [CREDIT-SCORE-LEGACY] Full error details:', error);
    
    // Release the lock in case of error
    await requestLockManager.releaseLock(mobile, nationalCode, serviceSlug);
    
    return {
      status: 'error',
      code: 'UNEXPECTED_ERROR',
      message: 'خطای غیرمنتظره در پردازش درخواست'
    };
  }
}

/**
 * Handle resend SMS request
 * @param {string} mobile - Mobile number
 * @param {string} nationalCode - National code
 * @param {string} hash - OTP hash from previous request
 * @param {string} provider - Provider name
 * @param {string} requestHash - Request hash for progress updates
 * @returns {object} Result object
 */
async function handleResendSms(mobile, nationalCode, hash, provider, requestHash) {
  console.log('📱 [CREDIT-SCORE-RESEND] Starting resend SMS process...');
  
  try {
    // Update progress
    if (requestHash) {
      await redisUpdater.updateProgress(requestHash, 30, 'authentication', 'ارسال مجدد کد تایید...');
    }
    
    // Load the provider module
    const providerPath = `../providers/${provider}/services/creditScoreService.js`;
    console.log(`📂 [CREDIT-SCORE-RESEND] Loading provider: ${providerPath}`);
    
    const providerModule = await import(providerPath);
    
    if (!providerModule.handleCreditScoreInquiry) {
      throw new Error(`Provider ${provider} does not export handleCreditScoreInquiry function`);
    }
    
    console.log(`✅ [CREDIT-SCORE-RESEND] Provider ${provider} loaded successfully`);
    
    // Prepare data for resend (this will only send SMS, not complete flow)
    const providerData = {
      mobile,
      nationalCode,
      requestHash,
      resendSms: true,  // This tells the provider to only send SMS
      hash  // Previous OTP hash for context
    };
    
    console.log('🔄 [CREDIT-SCORE-RESEND] Calling provider for resend SMS...');
    const result = await providerModule.handleCreditScoreInquiry(providerData);
    
    if (result.status === 'success' || result.status === 'sms_sent') {
      console.log('✅ [CREDIT-SCORE-RESEND] SMS resent successfully');
      
      // Update progress to indicate SMS was resent
      if (requestHash) {
        await redisUpdater.updateProgress(requestHash, 70, 'waiting_otp', 'کد تایید مجدد ارسال شد');
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
    } else {
      console.log('❌ [CREDIT-SCORE-RESEND] Failed to resend SMS:', result.message);
      return {
        status: 'error',
        code: 'RESEND_FAILED',
        message: result.message || 'خطا در ارسال مجدد کد تایید'
      };
    }
    
  } catch (error) {
    console.error('❌ [CREDIT-SCORE-RESEND] Error during resend SMS:', error.message);
    
    return {
      status: 'error',
      code: 'RESEND_ERROR', 
      message: 'خطا در ارسال مجدد کد تایید: ' + error.message
    };
  }
}

// Export the main handler
export { handle };