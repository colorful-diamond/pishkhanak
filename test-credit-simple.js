import { chromium } from 'playwright';

(async () => {
  console.log('🚀 Starting Simple Credit Score Test...');
  console.log('📋 Test Credentials:');
  console.log('   National Code: 6300057062');
  console.log('   Mobile: 09112697701');
  console.log('');

  const browser = await chromium.launch({ 
    headless: true,
    args: ['--no-sandbox', '--disable-setuid-sandbox']
  });
  
  try {
    const page = await browser.newPage();
    
    // Navigate to the page
    console.log('1️⃣ Navigating to page...');
    await page.goto('https://pishkhanak.com/services/credit-score-rating', { 
      waitUntil: 'domcontentloaded'
    });
    console.log('   ✅ Page loaded');

    // Wait for form to be ready
    await page.waitForTimeout(3000);

    // Use JavaScript to fill and submit the form
    console.log('2️⃣ Filling and submitting form via JavaScript...');
    const result = await page.evaluate(() => {
      // Find the form inputs
      const nationalCodeInput = document.querySelector('input[name="national_code"]');
      const mobileInput = document.querySelector('input[name="mobile"]');
      
      if (!nationalCodeInput || !mobileInput) {
        return { error: 'Could not find form inputs' };
      }
      
      // Fill the values
      nationalCodeInput.value = '6300057062';
      nationalCodeInput.dispatchEvent(new Event('input', { bubbles: true }));
      nationalCodeInput.dispatchEvent(new Event('change', { bubbles: true }));
      
      mobileInput.value = '09112697701';
      mobileInput.dispatchEvent(new Event('input', { bubbles: true }));
      mobileInput.dispatchEvent(new Event('change', { bubbles: true }));
      
      // Find and click the submit button
      const buttons = Array.from(document.querySelectorAll('button'));
      const submitButton = buttons.find(btn => 
        btn.textContent.includes('بررسی') && 
        btn.textContent.includes('رتبه')
      );
      
      if (submitButton) {
        submitButton.click();
        return { success: true, buttonText: submitButton.textContent.trim() };
      }
      
      // If button not found by text, try submitting the form directly
      const form = nationalCodeInput.closest('form');
      if (form) {
        form.submit();
        return { success: true, method: 'form.submit()' };
      }
      
      return { error: 'Could not find submit button or form' };
    });
    
    if (result.error) {
      console.log('   ❌ Error:', result.error);
      return;
    }
    
    console.log('   ✅ Form submitted successfully');
    if (result.buttonText) {
      console.log('   📌 Button clicked:', result.buttonText);
    } else if (result.method) {
      console.log('   📌 Submit method:', result.method);
    }

    // Wait for navigation
    console.log('3️⃣ Waiting for processing page...');
    try {
      await page.waitForURL('**/progress/**', { timeout: 10000 });
      const url = page.url();
      const hash = url.split('/').pop();
      console.log('   ✅ Request created: ' + hash);
      
      // Monitor progress for 20 seconds
      console.log('4️⃣ Monitoring progress...');
      
      for (let i = 0; i < 20; i++) {
        await page.waitForTimeout(1000);
        
        // Check current state
        const state = await page.evaluate(async () => {
          // Check for OTP form
          const otpInputs = document.querySelectorAll('.otp-input, input[type="text"][maxlength="1"]');
          const progressEl = document.querySelector('.progress-percentage, [class*="progress"]');
          const statusEl = document.querySelector('.status-message, [class*="status"]');
          
          return {
            hasOtpForm: otpInputs.length > 0,
            otpInputCount: otpInputs.length,
            progress: progressEl ? progressEl.textContent : null,
            status: statusEl ? statusEl.textContent : null
          };
        });
        
        if (state.hasOtpForm && i % 5 === 0) {
          console.log(`   🔐 OTP form detected with ${state.otpInputCount} inputs`);
        }
        
        if (state.progress && i % 5 === 0) {
          console.log(`   📊 Progress: ${state.progress}`);
        }
        
        if (state.status && i % 5 === 0) {
          console.log(`   📌 Status: ${state.status}`);
        }
      }
      
      // Final check via API
      console.log('5️⃣ Checking API status...');
      const apiStatus = await page.evaluate(async (requestHash) => {
        try {
          const response = await fetch(`/api/local-requests/${requestHash}/status`);
          return await response.json();
        } catch (e) {
          return { error: e.message };
        }
      }, hash);
      
      console.log('');
      console.log('📊 FINAL RESULTS:');
      console.log('   Request Hash:', hash);
      console.log('   API Status:', JSON.stringify(apiStatus, null, 2));
      
      if (apiStatus.requires_otp || apiStatus.step === 'waiting_otp') {
        console.log('');
        console.log('✅ TEST SUCCESSFUL!');
        console.log('   The system reached OTP verification stage.');
        console.log('   Credit score flow is working correctly!');
      } else if (apiStatus.status === 'failed') {
        console.log('');
        console.log('❌ TEST FAILED');
        console.log('   Error:', apiStatus.error_message || 'Unknown');
      } else {
        console.log('');
        console.log('⚠️ TEST STATUS UNCLEAR');
        console.log('   The request is in an unexpected state.');
      }
      
    } catch (error) {
      console.log('   ⚠️ Did not navigate to progress page');
      console.log('   Current URL:', page.url());
      
      // Check if there's an error message on the page
      const errorMsg = await page.$eval('.alert-danger, .error-message', el => el.textContent).catch(() => null);
      if (errorMsg) {
        console.log('   ❌ Error on page:', errorMsg);
      }
    }

  } catch (error) {
    console.error('❌ Test error:', error.message);
  } finally {
    await browser.close();
    console.log('');
    console.log('🏁 Test completed.');
  }
})();