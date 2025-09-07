/**
 * IP Checking Utility
 * Quick way to verify your current IP through proxy
 */

import { chromium } from 'playwright';
import { getStealthLaunchArgs, getStealthContextConfig, setupStealthMode, getRandomHttpProxy } from './stealthUtils.js';

/**
 * Get current IP address (direct connection)
 */
export async function getCurrentIP() {
    console.log('🔍 [IP-CHECK] Getting current IP address (direct)...');
    
    const browser = await chromium.launch({ 
        headless: true,
        args: getStealthLaunchArgs()
    });

    try {
        const context = await browser.newContext(getStealthContextConfig());
        const page = await context.newPage();
        await setupStealthMode(page);

        // Use httpbin.org for quick IP check
        await page.goto('https://httpbin.org/ip', { 
            waitUntil: 'networkidle',
            timeout: 15000 
        });

        const response = await page.textContent('pre');
        const data = JSON.parse(response);
        const ip = data.origin;

        console.log(`📍 [IP-CHECK] Direct IP: ${ip}`);
        
        await browser.close();
        return ip;

    } catch (error) {
        console.error(`❌ [IP-CHECK] Failed to get IP: ${error.message}`);
        await browser.close();
        throw error;
    }
}

/**
 * Get current IP address through HTTP proxy
 */
export async function getCurrentIPThroughProxy() {
    console.log('🔍 [IP-CHECK] Getting current IP address through HTTP proxy...');
    
    const browser = await chromium.launch({ 
        headless: true,
        args: getStealthLaunchArgs()
    });

    try {
        const context = await browser.newContext(getStealthContextConfig(null, null, true));
        const page = await context.newPage();
        await setupStealthMode(page);

        // Use httpbin.org for quick IP check
        await page.goto('https://httpbin.org/ip', { 
            waitUntil: 'networkidle',
            timeout: 15000 
        });

        const response = await page.textContent('pre');
        const data = JSON.parse(response);
        const ip = data.origin;

        console.log(`📍 [IP-CHECK] Proxy IP: ${ip}`);
        
        await browser.close();
        return ip;

    } catch (error) {
        console.error(`❌ [IP-CHECK] Failed to get IP through proxy: ${error.message}`);
        await browser.close();
        throw error;
    }
}

/**
 * Compare direct IP vs HTTP proxy IP
 */
export async function compareDirectVsProxy() {
    console.log('🔄 [IP-CHECK] Comparing direct IP vs HTTP proxy IP...\n');
    
    try {
        // Get direct IP
        console.log('1️⃣ Getting direct IP...');
        const directIP = await getCurrentIP();
        
        await new Promise(resolve => setTimeout(resolve, 2000));
        
        // Get IP through HTTP proxy
        console.log('\n2️⃣ Getting IP through HTTP proxy...');
        const proxyIP = await getCurrentIPThroughProxy();
        
        console.log('\n📊 [IP-CHECK] Comparison Results:');
        console.log(`Direct IP:     ${directIP}`);
        console.log(`Proxy IP:      ${proxyIP}`);
        console.log(`IPs Different: ${directIP !== proxyIP ? '✅ YES' : '❌ NO'}`);
        
        if (directIP === proxyIP) {
            console.log('⚠️  [IP-CHECK] WARNING: HTTP proxy may not be working correctly!');
            return false;
        } else {
            console.log('✅ [IP-CHECK] HTTP proxy is working correctly - IP changed!');
            return true;
        }
        
    } catch (error) {
        console.error(`❌ [IP-CHECK] Comparison failed: ${error.message}`);
        return false;
    }
}

/**
 * Verify IP consistency (multiple checks should return same IP)
 */
export async function verifyIPConsistency() {
    console.log('🔄 [IP-CHECK] Verifying IP consistency...\n');
    
    try {
        const ips = [];
        
        // Get IP multiple times
        console.log('1️⃣ First IP check...');
        const ip1 = await getCurrentIP();
        ips.push(ip1);
        
        await new Promise(resolve => setTimeout(resolve, 2000));
        
        console.log('\n2️⃣ Second IP check...');
        const ip2 = await getCurrentIP();
        ips.push(ip2);
        
        await new Promise(resolve => setTimeout(resolve, 2000));
        
        console.log('\n3️⃣ Third IP check...');
        const ip3 = await getCurrentIP();
        ips.push(ip3);
        
        console.log('\n📊 [IP-CHECK] Consistency Results:');
        console.log(`First IP:      ${ip1}`);
        console.log(`Second IP:     ${ip2}`);
        console.log(`Third IP:      ${ip3}`);
        
        const allSame = ips.every(ip => ip === ips[0]);
        console.log(`IPs Consistent: ${allSame ? '✅ YES' : '❌ NO'}`);
        
        if (allSame) {
            console.log('✅ [IP-CHECK] IP consistency verified!');
            return true;
        } else {
            console.log('⚠️  [IP-CHECK] WARNING: IP addresses are inconsistent!');
            return false;
        }
        
    } catch (error) {
        console.error(`❌ [IP-CHECK] Consistency check failed: ${error.message}`);
        return false;
    }
}

/**
 * Quick browser verification (can be used in your main scripts)
 */
export async function verifyBrowserWorking() {
    try {
        const ip = await getCurrentIP();
        console.log(`✅ [IP-CHECK] Browser verification successful - IP: ${ip}`);
        return { success: true, ip: ip };
    } catch (error) {
        console.error(`❌ [IP-CHECK] Browser verification failed: ${error.message}`);
        return { success: false, error: error.message };
    }
}