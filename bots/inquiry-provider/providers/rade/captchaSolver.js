import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import fetch from 'node-fetch';
import { SocksProxyAgent } from 'socks-proxy-agent';

// Get current directory for ES modules
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Proxy configuration for external requests
const proxyAgent = new SocksProxyAgent('socks5://127.0.0.1:1080');

// Proxy-enabled fetch function
const fetchWithProxy = (url, options = {}) => {
    return fetch(url, {
        ...options,
        agent: proxyAgent
    });
};

// Removed proxy testing - using proxy directly

/**
 * Handles captcha image capture and saving
 * @param {import('playwright').Page} page - The page object
 * @param {string} mobile - Mobile number for file naming
 * @param {string} nationalCode - National code for file naming
 * @param {string} captchaSelector - CSS selector for captcha image (optional)
 * @returns {Promise<Object>} Captcha image data
 */
async function handleCaptcha(page, mobile, nationalCode, captchaSelector = 'img[src^="https://api.rade.ir/captcha/"]') {
    console.log('\n📸 [CAPTCHA-SOLVER] Starting captcha image handling');
    console.log('📋 [CAPTCHA-SOLVER] Parameters:', { 
        mobile: mobile ? `${mobile.slice(0, 4)}***${mobile.slice(-2)}` : 'N/A',
        nationalCode: nationalCode ? `${nationalCode.slice(0, 3)}***${nationalCode.slice(-2)}` : 'N/A',
        selector: captchaSelector
    });
    
    try {
        let captchaImage;
        let captchaImageBuffer;
        console.log('🔍 [CAPTCHA-SOLVER] Looking for captcha image with selector:', captchaSelector);
        if(page.url().includes('https://api.rade.ir/captcha/default')){
            console.log('✅ [CAPTCHA-SOLVER] Captcha image found on page');
            captchaImageBuffer = await page.screenshot({ type: 'png' });
        }else{
            captchaImage = page.locator(captchaSelector);
            if (!(await captchaImage.isVisible())) {
                console.log('❌ [CAPTCHA-SOLVER] Captcha image not found or not visible');
                throw new Error('Captcha image not found or not visible');
            }
            captchaImageBuffer = await captchaImage.screenshot({ type: 'png' });
        }
        
        console.log('🔄 [CAPTCHA-SOLVER] Converting to base64...');
        const captchaImageBase64 = Buffer.from(captchaImageBuffer).toString('base64');
        const captchaImageUrl = `data:image/png;base64,${captchaImageBase64}`;
        console.log('✅ [CAPTCHA-SOLVER] Base64 conversion complete (length:', captchaImageBase64.length, 'chars)');

        const captchaImageName = `${mobile}-${nationalCode}-${Date.now()}.png`;
        const captchaImagePath = path.resolve(__dirname + '/../../files/captcha/' + captchaImageName);
        console.log('📂 [CAPTCHA-SOLVER] Saving to file:', captchaImagePath);
        
        // Ensure the captcha directory exists
        const captchaDir = path.dirname(captchaImagePath);
        if (!fs.existsSync(captchaDir)) {
            console.log('📁 [CAPTCHA-SOLVER] Creating captcha directory:', captchaDir);
            fs.mkdirSync(captchaDir, { recursive: true });
            console.log('✅ [CAPTCHA-SOLVER] Directory created successfully');
        } else {
            console.log('✅ [CAPTCHA-SOLVER] Directory already exists');
        }
        
        console.log('💾 [CAPTCHA-SOLVER] Writing image file...');
        fs.writeFileSync(captchaImagePath, captchaImageBuffer);
        console.log('✅ [CAPTCHA-SOLVER] Image file saved successfully');
        
        console.log('🎉 [CAPTCHA-SOLVER] Captcha handling completed');
        return {
            status: 'success',
            imagePath: captchaImagePath,
            imageBase64: captchaImageBase64,
            imageUrl: captchaImageUrl,
            imageName: captchaImageName
        };
        
    } catch (error) {
        console.error('💥 [CAPTCHA-SOLVER] Error handling captcha image:', error.message);
        console.error('📋 [CAPTCHA-SOLVER] Full error details:', error);
        return {
            status: 'error',
            message: error.message
        };
    }
}

/**
 * Solves captcha using AI (Google Gemini)
 * @param {string} captchaImagePath - Path to captcha image file
 * @returns {Promise<Object>} Captcha solution result
 */
async function solveCaptcha(captchaImagePath) {
    console.log('\n🤖 [AI-CAPTCHA-SOLVER] Starting AI captcha solving process');
    console.log('📂 [AI-CAPTCHA-SOLVER] Image path:', captchaImagePath);
    
    try {
        // Check if API key is available
        console.log('🔑 [AI-CAPTCHA-SOLVER] Checking for Gemini API key...');
        if (!process.env.GEMINI_API_KEY) {
            console.log('❌ [AI-CAPTCHA-SOLVER] GEMINI_API_KEY not found');
            throw new Error('GEMINI_API_KEY not found. Please ensure you have created a .env file in the inquiry-provider root directory with your Gemini API key.');
        }
        console.log('✅ [AI-CAPTCHA-SOLVER] Gemini API key found');

        console.log('🚀 [AI-CAPTCHA-SOLVER] Using direct API call with SOCKS proxy');

        // Check if image file exists
        console.log('🔍 [AI-CAPTCHA-SOLVER] Checking if captcha image exists...');
        if (!fs.existsSync(captchaImagePath)) {
            console.log('❌ [AI-CAPTCHA-SOLVER] Captcha image file not found');
            throw new Error(`Captcha image not found at path: ${captchaImagePath}`);
        }
        console.log('✅ [AI-CAPTCHA-SOLVER] Captcha image file found');

        // Read the image file as base64
        console.log('📖 [AI-CAPTCHA-SOLVER] Reading captcha image file...');
        const imageBuffer = fs.readFileSync(captchaImagePath);
        const imageBase64 = imageBuffer.toString('base64');
        console.log('✅ [AI-CAPTCHA-SOLVER] Image converted to base64 (length:', imageBase64.length, 'chars)');

        // Prepare the image data for Gemini
        console.log('📦 [AI-CAPTCHA-SOLVER] Preparing image data for AI analysis...');
        const imagePart = {
            inlineData: {
                data: imageBase64,
                mimeType: "image/png"
            }
        };

        // Create the prompt for captcha solving
        console.log('💬 [AI-CAPTCHA-SOLVER] Creating AI prompt for captcha solving...');
        const prompt = `Please read the text shown in this captcha image and return only the text characters you see. 
        This is a captcha that typically contains alphanumeric characters. 
        Return your response in JSON format with a single field called "text" containing the extracted text.
        the output are just digits and numbers and they are 5 numbers
        Example: {"text": "12345"}
        
        Important: Only return the JSON response, no additional text or explanation.`;

        // Make direct API call to Google Gemini
        const apiUrl = `https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=${process.env.GEMINI_API_KEY}`;
        console.log('🧠 [AI-CAPTCHA-SOLVER] Making direct API call to Gemini...');
        console.log('🌐 [AI-CAPTCHA-SOLVER] API Endpoint:', apiUrl.split('?')[0]);
        console.log('🔑 [AI-CAPTCHA-SOLVER] Using API key:', process.env.GEMINI_API_KEY ? `${process.env.GEMINI_API_KEY.substring(0, 8)}...` : 'Not found');
        console.log('🌍 [AI-CAPTCHA-SOLVER] Proxy status: ENABLED');
        
        // Prepare request body for direct API call
        const requestBody = {
            contents: [{
                parts: [
                    { text: prompt },
                    {
                        inline_data: {
                            mime_type: "image/png",
                            data: imageBase64
                        }
                    }
                ]
            }]
        };
        
        console.log('📦 [AI-CAPTCHA-SOLVER] Request body prepared, making HTTP request...');
        
        let text;
        try {
            console.log('🚀 [AI-CAPTCHA-SOLVER] Making API call through SOCKS proxy...');
            
            const response = await fetchWithProxy(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'User-Agent': 'Node.js Captcha Solver'
                },
                body: JSON.stringify(requestBody),
                timeout: 30000
            });
            
            console.log('📊 [AI-CAPTCHA-SOLVER] Response status:', response.status, response.statusText);
            
            if (!response.ok) {
                const errorText = await response.text();
                console.error('❌ [AI-CAPTCHA-SOLVER] API error response:', errorText);
                throw new Error(`API request failed: ${response.status} ${response.statusText} - ${errorText}`);
            }
            
            const result = await response.json();
            console.log('✅ [AI-CAPTCHA-SOLVER] API response received successfully');
            
            // Extract text from the response
            if (result.candidates && result.candidates[0] && result.candidates[0].content && result.candidates[0].content.parts) {
                text = result.candidates[0].content.parts[0].text;
                console.log('✅ [AI-CAPTCHA-SOLVER] AI response text:', text.substring(0, 100) + (text.length > 100 ? '...' : ''));
            } else {
                console.error('❌ [AI-CAPTCHA-SOLVER] Unexpected response structure:', JSON.stringify(result, null, 2));
                throw new Error('Unexpected response structure from AI API');
            }
            
        } catch (aiError) {
            console.error('💥 [AI-CAPTCHA-SOLVER] API request failed:', aiError.message);
            throw aiError;
        }

        // Try to parse the JSON response
        console.log('🔍 [AI-CAPTCHA-SOLVER] Parsing AI response...');
        let captchaText;
        try {
            const jsonResponse = JSON.parse(text.trim());
            captchaText = jsonResponse.text;
            console.log('✅ [AI-CAPTCHA-SOLVER] Successfully parsed JSON response, captcha text:', captchaText);
        } catch (parseError) {
            // If JSON parsing fails, try to extract text from the response
            console.log('⚠️ [AI-CAPTCHA-SOLVER] Failed to parse JSON, attempting text extraction...');
            console.log('📝 [AI-CAPTCHA-SOLVER] Raw response:', text);
            // Look for text between quotes or extract alphanumeric characters
            const textMatch = text.match(/[A-Za-z0-9]{3,}/);
            captchaText = textMatch ? textMatch[0] : text.trim();
            console.log('✅ [AI-CAPTCHA-SOLVER] Extracted captcha text:', captchaText);
        }

        // Clean up the image file after processing
        console.log('🧹 [AI-CAPTCHA-SOLVER] Cleaning up temporary captcha file...');
        try {
            fs.unlinkSync(captchaImagePath);
            console.log('✅ [AI-CAPTCHA-SOLVER] Captcha file cleaned up successfully');
        } catch (cleanupError) {
            console.warn('⚠️ [AI-CAPTCHA-SOLVER] Failed to clean up captcha image:', cleanupError.message);
        }

        console.log('🎉 [AI-CAPTCHA-SOLVER] Captcha solving completed successfully');
        return {
            status: 'success',
            text: captchaText,
            originalResponse: text
        };

    } catch (error) {
        console.error('💥 [AI-CAPTCHA-SOLVER] Error solving captcha with Gemini:', error.message);
        console.error('📋 [AI-CAPTCHA-SOLVER] Full error details:', error);
        return {
            status: 'error',
            message: error.message,
            text: null
        };
    }
}

/**
 * Complete captcha solving process - capture and solve
 * @param {import('playwright').Page} page - The page object
 * @param {string} mobile - Mobile number for file naming
 * @param {string} nationalCode - National code for file naming
 * @param {string} captchaSelector - CSS selector for captcha image (optional)
 * @returns {Promise<Object>} Complete captcha solution result
 */
async function solveCaptchaComplete(page, mobile, nationalCode, captchaSelector = 'img[src^="https://api.rade.ir/captcha/"]') {
    console.log('\n🎯 [CAPTCHA-SOLVER] Starting complete captcha solving process');
    
    // Step 1: Handle (capture) the captcha
    const captchaData = await handleCaptcha(page, mobile, nationalCode, captchaSelector);
    
    if (captchaData.status !== 'success') {
        console.log('❌ [CAPTCHA-SOLVER] Failed to capture captcha image');
        return captchaData;
    }
    
    // Step 2: Solve the captcha with AI
    const solution = await solveCaptcha(captchaData.imagePath);
    
    console.log('📊 [CAPTCHA-SOLVER] Complete captcha solving result:', {
        status: solution.status,
        text: solution.text || 'N/A',
        hasError: solution.status !== 'success'
    });
    
    return solution;
}

/**
 * Detects if captcha is present on the page
 * @param {import('playwright').Page} page - The page object
 * @param {string} captchaSelector - CSS selector for captcha image (optional)
 * @returns {Promise<boolean>} True if captcha is present
 */
async function isCaptchaPresent(page, captchaSelector = 'img[src^="https://api.rade.ir/captcha/"]') {
    console.log('\n🔍 [CAPTCHA-SOLVER] Checking for captcha presence');
    console.log('🎯 [CAPTCHA-SOLVER] Using selector:', captchaSelector);
    
    try {
        const captchaImage = page.locator(captchaSelector);
        const isVisible = await captchaImage.isVisible({ timeout: 3000 });
        
        if (isVisible) {
            console.log('✅ [CAPTCHA-SOLVER] Captcha detected on page');
        } else {
            console.log('❌ [CAPTCHA-SOLVER] No captcha found on page');
        }
        
        return isVisible;
    } catch (error) {
        console.log('❌ [CAPTCHA-SOLVER] Error checking for captcha:', error.message);
        return false;
    }
}

/**
 * Checks for captcha error message
 * @param {import('playwright').Page} page - The page object
 * @returns {Promise<boolean>} True if captcha error is present
 */
async function isCaptchaError(page) {
    console.log('\n🔍 [CAPTCHA-SOLVER] Checking for captcha error message');
    
    try {
        // Check for the specific error message
        const errorSelectors = [
            '.text-danger:has-text("کد نمایش داده شده را وارد کنید")',
            '.text-danger:has-text("کد وارد شده اشتباه است")',
            'text=کد نمایش داده شده را وارد کنید',
            'text=کد وارد شده اشتباه است'
        ];
        
        for (const selector of errorSelectors) {
            console.log('🔍 [CAPTCHA-SOLVER] Checking selector:', selector);
            const errorElement = page.locator(selector).first();
            if (await errorElement.isVisible().catch(() => false)) {
                console.log('❌ [CAPTCHA-SOLVER] Captcha error detected with selector:', selector);
                return true;
            }
        }
        
        console.log('✅ [CAPTCHA-SOLVER] No captcha error found');
        return false;
    } catch (error) {
        console.log('❌ [CAPTCHA-SOLVER] Error checking for captcha error:', error.message);
        return false;
    }
}

export { 
    handleCaptcha, 
    solveCaptcha, 
    solveCaptchaComplete, 
    isCaptchaPresent, 
    isCaptchaError 
}; 