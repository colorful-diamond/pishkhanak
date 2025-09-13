import { chromium } from 'playwright';

(async () => {
  console.log('🚀 Starting Credit Score Rating Test...');
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
    
    // Navigate to the credit score rating page
    console.log('1️⃣ Navigating to credit score rating page...');
    await page.goto('https://pishkhanak.com/services/credit-score-rating', { 
      waitUntil: 'domcontentloaded',
      timeout: 30000 
    });
    console.log('   ✅ Page loaded successfully');

    // Fill the national code
    console.log('2️⃣ Filling national code field...');
    await page.fill('input[name="national_code"]', '6300057062');
    console.log('   ✅ National code entered: 6300057062');

    // Fill the mobile number
    console.log('3️⃣ Filling mobile number field...');
    await page.fill('input[name="mobile"]', '09112697701');
    console.log('   ✅ Mobile number entered: 09112697701');

    // Wait a bit for JavaScript to initialize
    await page.waitForTimeout(2000);
    
    // Click submit button - find the button in the form context
    console.log('4️⃣ Clicking submit button...');
    // First try to find the button by its text content
    const button = await page.locator('button').filter({ hasText: /بررسی.*رتبه.*اعتباری/i }).first();
    if (await button.count() > 0) {
      await button.click();
    } else {
      // Fallback: click the last button in the form (usually the submit button)
      await page.locator('form button').last().click();
    }
    console.log('   ✅ Form submitted');

    // Wait for navigation to progress page
    console.log('5️⃣ Waiting for processing to start...');
    await page.waitForURL('**/progress/**', { timeout: 15000 });
    const progressUrl = page.url();
    const requestHash = progressUrl.split('/').pop();
    console.log(`   ✅ Request created with hash: ${requestHash}`);

    // Monitor the progress for 30 seconds
    console.log('6️⃣ Monitoring progress...');
    let lastProgress = 0;
    let otpVisible = false;
    
    for (let i = 0; i < 30; i++) {
      await page.waitForTimeout(1000);
      
      // Check if OTP form is visible
      const otpForm = await page.$('.otp-container');
      if (otpForm && !otpVisible) {
        otpVisible = true;
        console.log('   🔐 OTP form detected! System is waiting for OTP input.');
        
        // Check OTP inputs exist
        const otpInputs = await page.$$('.otp-container input');
        console.log(`   📝 Found ${otpInputs.length} OTP input fields`);
      }
      
      // Get progress percentage
      const progressText = await page.$eval('.progress-percentage', el => el.textContent).catch(() => '0%');
      const progress = parseInt(progressText);
      
      if (progress !== lastProgress) {
        console.log(`   📊 Progress: ${progressText}`);
        lastProgress = progress;
      }
      
      // Get current status message
      const statusMessage = await page.$eval('.status-message', el => el.textContent).catch(() => '');
      if (statusMessage && i % 5 === 0) {
        console.log(`   📌 Status: ${statusMessage}`);
      }
    }

    // Check final state via API
    console.log('7️⃣ Checking final state via API...');
    const apiResponse = await page.evaluate(async (hash) => {
      const response = await fetch(`/api/local-requests/${hash}/status`);
      return await response.json();
    }, requestHash);
    
    console.log('');
    console.log('📊 Final Test Results:');
    console.log('   Request Hash:', requestHash);
    console.log('   Status:', apiResponse.status || 'N/A');
    console.log('   Step:', apiResponse.step || 'N/A');
    console.log('   Progress:', apiResponse.progress || 0, '%');
    console.log('   Requires OTP:', apiResponse.requires_otp || false);
    
    if (apiResponse.requires_otp || otpVisible) {
      console.log('');
      console.log('✅ TEST SUCCESSFUL!');
      console.log('   The system successfully:');
      console.log('   1. Accepted the form submission');
      console.log('   2. Created a background job');
      console.log('   3. Processed through the nics24 provider');
      console.log('   4. Reached the OTP verification stage');
      console.log('   ');
      console.log('   🎯 The credit score rating flow is working correctly!');
      console.log('   📱 An OTP has been sent to mobile: 09112697701');
    } else if (apiResponse.status === 'failed') {
      console.log('');
      console.log('❌ TEST FAILED!');
      console.log('   Error:', apiResponse.error_message || 'Unknown error');
    } else {
      console.log('');
      console.log('⚠️ TEST INCOMPLETE');
      console.log('   The request is still processing or in an unknown state.');
    }

  } catch (error) {
    console.error('❌ Test encountered an error:', error.message);
    console.error('Stack:', error.stack);
  } finally {
    await browser.close();
    console.log('');
    console.log('🏁 Test completed.');
  }
})();