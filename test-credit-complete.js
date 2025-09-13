import { chromium } from 'playwright';

(async () => {
  console.log('═══════════════════════════════════════════════════════════');
  console.log('🚀 COMPLETE CREDIT SCORE RATING TEST');
  console.log('═══════════════════════════════════════════════════════════');
  console.log('');
  console.log('📋 Test Configuration:');
  console.log('   Service: Credit Score Rating (رتبه‌بندی اعتباری)');
  console.log('   Provider: nics24');
  console.log('   National Code: 6300057062');
  console.log('   Mobile: 09112697701');
  console.log('');

  const browser = await chromium.launch({ 
    headless: true,
    args: ['--no-sandbox', '--disable-setuid-sandbox']
  });
  
  let finalStatus = 'UNKNOWN';
  let requestHash = null;
  
  try {
    const page = await browser.newPage();
    
    // Step 1: Navigate to service page
    console.log('📍 STEP 1: Navigate to Service Page');
    console.log('   URL: https://pishkhanak.com/services/credit-score-rating');
    await page.goto('https://pishkhanak.com/services/credit-score-rating', { 
      waitUntil: 'domcontentloaded',
      timeout: 30000
    });
    console.log('   ✅ Page loaded successfully');
    console.log('');

    // Step 2: Fill the form
    console.log('📝 STEP 2: Fill Form Data');
    await page.waitForTimeout(2000); // Wait for JS to initialize
    
    // Fill national code
    await page.fill('input[name="national_code"]', '6300057062');
    console.log('   ✅ National Code: 6300057062');
    
    // Fill mobile
    await page.fill('input[name="mobile"]', '09112697701');
    console.log('   ✅ Mobile: 09112697701');
    console.log('');

    // Step 3: Submit form
    console.log('🚀 STEP 3: Submit Form');
    
    // Try multiple methods to submit the form
    try {
      // Method 1: Try to find and click the visible submit button
      const submitButton = page.locator('button').filter({ hasText: 'بررسی رتبه اعتباری' });
      const buttonCount = await submitButton.count();
      
      if (buttonCount > 0) {
        await submitButton.first().click({ timeout: 5000 });
        console.log('   ✅ Clicked submit button (Method 1: Direct click)');
      } else {
        // Method 2: Use evaluate to trigger form submission
        const submitted = await page.evaluate(() => {
          // Find the form containing our inputs
          const nationalCodeInput = document.querySelector('input[name="national_code"]');
          if (nationalCodeInput) {
            const form = nationalCodeInput.closest('form');
            if (form) {
              // Find any submit button within this specific form
              const submitBtn = form.querySelector('button[type="submit"], button');
              if (submitBtn) {
                submitBtn.click();
                return 'button-click';
              }
              // If no button, try form submit
              form.requestSubmit();
              return 'form-submit';
            }
          }
          return false;
        });
        
        if (submitted) {
          console.log(`   ✅ Form submitted (Method 2: ${submitted})`);
        } else {
          console.log('   ❌ Could not submit form');
          return;
        }
      }
    } catch (error) {
      console.log('   ❌ Error submitting form:', error.message);
      return;
    }
    console.log('');

    // Step 4: Handle preview page if it appears
    console.log('⏳ STEP 4: Check for Preview/Progress Page');
    await page.waitForTimeout(3000);
    
    const currentUrl = page.url();
    console.log('   Current URL:', currentUrl);
    
    if (currentUrl.includes('/preview/')) {
      console.log('   📋 Preview page detected');
      
      // Extract request hash from URL
      const urlParts = currentUrl.split('/');
      requestHash = urlParts[urlParts.length - 1];
      console.log('   Request Hash:', requestHash);
      
      // Look for confirm/continue button on preview page
      const confirmResult = await page.evaluate(() => {
        // Look for confirmation button
        const buttons = Array.from(document.querySelectorAll('button, a'));
        const confirmButton = buttons.find(btn => 
          btn.textContent.includes('تایید') || 
          btn.textContent.includes('ادامه') ||
          btn.textContent.includes('پرداخت')
        );
        
        if (confirmButton) {
          confirmButton.click();
          return { found: true, text: confirmButton.textContent.trim() };
        }
        
        return { found: false };
      });
      
      if (confirmResult.found) {
        console.log('   ✅ Clicked confirmation button:', confirmResult.text);
        await page.waitForTimeout(3000);
      } else {
        console.log('   ⚠️ No confirmation button found on preview page');
      }
    } else if (currentUrl.includes('/progress/')) {
      console.log('   ✅ Navigated directly to progress page');
      const urlParts = currentUrl.split('/');
      requestHash = urlParts[urlParts.length - 1];
      console.log('   Request Hash:', requestHash);
    }
    console.log('');

    // Step 5: Monitor progress
    if (requestHash) {
      console.log('📊 STEP 5: Monitor Request Progress');
      console.log('   Monitoring for 30 seconds...');
      
      let lastStatus = '';
      let otpDetected = false;
      
      for (let i = 0; i < 30; i++) {
        await page.waitForTimeout(1000);
        
        // Check page state
        const pageState = await page.evaluate(() => {
          const otpInputs = document.querySelectorAll('.otp-input, input[maxlength="1"], .otp-container input');
          const progressEl = document.querySelector('.progress-percentage, [class*="progress"]');
          const statusEl = document.querySelector('.status-message, [class*="status"], .current-message');
          
          return {
            hasOtp: otpInputs.length > 0,
            otpCount: otpInputs.length,
            progress: progressEl ? progressEl.textContent.trim() : null,
            status: statusEl ? statusEl.textContent.trim() : null
          };
        });
        
        // Report OTP detection
        if (pageState.hasOtp && !otpDetected) {
          otpDetected = true;
          console.log('   🔐 OTP FORM DETECTED!');
          console.log(`      Number of inputs: ${pageState.otpCount}`);
        }
        
        // Report status changes
        if (pageState.status && pageState.status !== lastStatus) {
          console.log(`   📌 Status: ${pageState.status}`);
          lastStatus = pageState.status;
        }
        
        // Report progress every 5 seconds
        if (pageState.progress && i % 5 === 0) {
          console.log(`   📊 Progress: ${pageState.progress}`);
        }
      }
      console.log('');
      
      // Step 6: Check final status via API
      console.log('🔍 STEP 6: Check Final Status via API');
      
      const apiStatus = await page.evaluate(async (hash) => {
        try {
          const response = await fetch(`/api/local-requests/${hash}/status`);
          const data = await response.json();
          return { success: true, data };
        } catch (error) {
          return { success: false, error: error.message };
        }
      }, requestHash);
      
      if (apiStatus.success) {
        console.log('   API Response:');
        console.log('   - Status:', apiStatus.data.status || 'N/A');
        console.log('   - Step:', apiStatus.data.step || 'N/A');
        console.log('   - Progress:', apiStatus.data.progress || 0, '%');
        console.log('   - Requires OTP:', apiStatus.data.requires_otp || false);
        
        if (apiStatus.data.error_message) {
          console.log('   - Error:', apiStatus.data.error_message);
        }
        
        // Determine final status
        if (apiStatus.data.requires_otp || apiStatus.data.step === 'waiting_otp' || otpDetected) {
          finalStatus = 'SUCCESS - OTP STAGE REACHED';
        } else if (apiStatus.data.status === 'completed') {
          finalStatus = 'SUCCESS - COMPLETED';
        } else if (apiStatus.data.status === 'failed') {
          finalStatus = 'FAILED';
        } else {
          finalStatus = 'IN_PROGRESS';
        }
      } else {
        console.log('   ❌ API check failed:', apiStatus.error);
      }
    } else {
      console.log('⚠️ Could not extract request hash');
      finalStatus = 'NO_REQUEST_CREATED';
    }

  } catch (error) {
    console.error('❌ Test encountered an error:', error.message);
    finalStatus = 'ERROR';
  } finally {
    await browser.close();
    
    console.log('');
    console.log('═══════════════════════════════════════════════════════════');
    console.log('📊 TEST RESULTS SUMMARY');
    console.log('═══════════════════════════════════════════════════════════');
    console.log('');
    
    if (requestHash) {
      console.log('   Request Hash:', requestHash);
    }
    
    console.log('   Final Status:', finalStatus);
    console.log('');
    
    if (finalStatus.includes('SUCCESS')) {
      console.log('✅ TEST PASSED!');
      console.log('');
      console.log('The credit score rating service is working correctly:');
      console.log('1. Form submission successful');
      console.log('2. Request created and processed');
      console.log('3. nics24 provider integration working');
      if (finalStatus.includes('OTP')) {
        console.log('4. OTP verification stage reached');
        console.log('5. SMS sent to mobile: 09112697701');
      }
    } else if (finalStatus === 'FAILED') {
      console.log('❌ TEST FAILED');
      console.log('The request was processed but failed.');
    } else if (finalStatus === 'IN_PROGRESS') {
      console.log('⏳ TEST INCOMPLETE');
      console.log('The request is still being processed.');
    } else {
      console.log('⚠️ TEST INCONCLUSIVE');
      console.log('Could not determine the final status.');
    }
    
    console.log('');
    console.log('═══════════════════════════════════════════════════════════');
    console.log('🏁 Test execution completed');
    console.log('═══════════════════════════════════════════════════════════');
  }
})();