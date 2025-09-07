/**
 * Browser Testing Utilities (No Proxy)
 * Test and verify browser functionality without proxy
 */

import { chromium } from 'playwright';
import { getStealthLaunchArgs, getStealthContextConfig, setupStealthMode, getAllHttpProxies, getRandomHttpProxy } from './stealthUtils.js';

/**
 * Test browser connectivity and IP detection
 */
async function testBrowserConnectivity(timeout = 30000) {
    console.log('🔍 [BROWSER-TEST] Testing browser connectivity...');
    
    const browser = await chromium.launch({ 
        headless: true,
        args: getStealthLaunchArgs()
    });

    try {
        const context = await browser.newContext(getStealthContextConfig());
        const page = await context.newPage();
        await setupStealthMode(page);

        // Test: Check IP through httpbin.org
        console.log('🌐 [BROWSER-TEST] Checking current IP...');
        
        await page.goto('https://httpbin.org/ip', { 
            waitUntil: 'networkidle',
            timeout: timeout 
        });

        const response = await page.textContent('pre');
        const data = JSON.parse(response);
        const ip = data.origin;

        console.log(`✅ [BROWSER-TEST] Browser connectivity working - IP: ${ip}`);
        
        await browser.close();
        return {
            success: true,
            ip: ip.trim(),
            message: 'Browser connectivity is working correctly'
        };

    } catch (error) {
        console.error(`❌ [BROWSER-TEST] Browser test failed: ${error.message}`);
        await browser.close();
        return {
            success: false,
            error: error.message
        };
    }
}

/**
 * Test a single HTTP proxy
 */
async function testSingleHttpProxy(proxyConfig, timeout = 30000) {
    console.log(`🔍 [PROXY-TEST] Testing HTTP proxy: ${proxyConfig.server} (expected IP: ${proxyConfig.expectedIP})`);
    
    const browser = await chromium.launch({ 
        headless: true,
        args: getStealthLaunchArgs()
    });

    try {
        const context = await browser.newContext({
            proxy: { server: proxyConfig.server },
            userAgent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            viewport: { width: 1920, height: 1080 }
        });

        const page = await context.newPage();
        await setupStealthMode(page);

        // Test IP through httpbin.org
        console.log(`🌐 [PROXY-TEST] Checking IP through ${proxyConfig.server}...`);
        
        await page.goto('https://httpbin.org/ip', { 
            waitUntil: 'networkidle',
            timeout: timeout 
        });

        const response = await page.textContent('pre');
        const data = JSON.parse(response);
        const actualIP = data.origin;

        const isExpectedIP = actualIP === proxyConfig.expectedIP;
        
        console.log(`✅ [PROXY-TEST] Proxy ${proxyConfig.server} is working`);
        console.log(`🎯 [PROXY-TEST] Expected IP: ${proxyConfig.expectedIP}`);
        console.log(`📍 [PROXY-TEST] Actual IP: ${actualIP}`);
        console.log(`${isExpectedIP ? '✅' : '⚠️'} [PROXY-TEST] IP Match: ${isExpectedIP ? 'YES' : 'NO'}`);
        
        await browser.close();
        return {
            success: true,
            proxy: proxyConfig.server,
            expectedIP: proxyConfig.expectedIP,
            actualIP: actualIP.trim(),
            ipMatch: isExpectedIP,
            message: 'HTTP proxy is working correctly'
        };

    } catch (error) {
        console.error(`❌ [PROXY-TEST] Proxy ${proxyConfig.server} failed: ${error.message}`);
        await browser.close();
        return {
            success: false,
            proxy: proxyConfig.server,
            expectedIP: proxyConfig.expectedIP,
            error: error.message
        };
    }
}

/**
 * Test all HTTP proxies
 */
export async function testAllHttpProxies() {
    console.log('🚀 [PROXY-TEST] Starting HTTP proxy testing...');
    
    const proxies = getAllHttpProxies();
    console.log(`📡 [PROXY-TEST] Testing ${proxies.length} HTTP proxies`);
    
    const results = [];
    
    for (const proxy of proxies) {
        const result = await testSingleHttpProxy(proxy);
        results.push(result);
        
        // Wait between tests to avoid overwhelming the services
        await new Promise(resolve => setTimeout(resolve, 2000));
    }

    // Summary
    const working = results.filter(r => r.success);
    const failed = results.filter(r => !r.success);
    const ipMatching = results.filter(r => r.success && r.ipMatch);

    console.log('\n📊 [PROXY-TEST] Test Results Summary:');
    console.log(`✅ Working proxies: ${working.length}/${proxies.length}`);
    console.log(`❌ Failed proxies: ${failed.length}/${proxies.length}`);
    console.log(`🎯 IP matching proxies: ${ipMatching.length}/${working.length}`);

    if (working.length > 0) {
        console.log('\n🟢 Working Proxies:');
        working.forEach(r => {
            const status = r.ipMatch ? '✅' : '⚠️';
            console.log(`  ${r.proxy} → Expected: ${r.expectedIP}, Actual: ${r.actualIP} ${status}`);
        });
    }

    if (failed.length > 0) {
        console.log('\n🔴 Failed Proxies:');
        failed.forEach(r => {
            console.log(`  ${r.proxy} → Error: ${r.error}`);
        });
    }

    return {
        total: proxies.length,
        working: working.length,
        failed: failed.length,
        ipMatching: ipMatching.length,
        results: results
    };
}

/**
 * Test basic browser functionality
 */
export async function testBrowserBasics() {
    console.log('🚀 [BROWSER-TEST] Starting basic browser functionality test...');
    
    const result = await testBrowserConnectivity();
    
    console.log('\n📊 [BROWSER-TEST] Test Results:');
    
    if (result.success) {
        console.log('✅ Browser test PASSED');
        console.log(`🌐 Your current IP: ${result.ip}`);
        console.log('🎭 Stealth mode: Active');
    } else {
        console.log('❌ Browser test FAILED');
        console.log(`Error: ${result.error}`);
    }

    return {
        success: result.success,
        ip: result.ip || null,
        error: result.error || null
    };
}

/**
 * Test HTTP proxy rotation (verify different proxies give different IPs)
 */
export async function testHttpProxyRotation(iterations = 4) {
    console.log(`🔄 [PROXY-TEST] Testing HTTP proxy rotation with ${iterations} iterations...`);
    
    const ips = new Set();
    const results = [];

    for (let i = 0; i < iterations; i++) {
        console.log(`\n🔄 [PROXY-TEST] Iteration ${i + 1}/${iterations}`);
        
        const browser = await chromium.launch({ 
            headless: true,
            args: getStealthLaunchArgs()
        });

        try {
            // Use random HTTP proxy through our stealth config
            const context = await browser.newContext(getStealthContextConfig(null, null, true));
            const page = await context.newPage();
            await setupStealthMode(page);

            // Get IP
            await page.goto('https://httpbin.org/ip', { 
                waitUntil: 'networkidle',
                timeout: 30000 
            });

            const response = await page.textContent('pre');
            const data = JSON.parse(response);
            const ip = data.origin;

            ips.add(ip);
            results.push({ iteration: i + 1, ip: ip });
            
            console.log(`🌐 [PROXY-TEST] Iteration ${i + 1} IP: ${ip}`);

            await browser.close();

        } catch (error) {
            console.error(`❌ [PROXY-TEST] Iteration ${i + 1} failed: ${error.message}`);
            await browser.close();
        }

        // Wait between iterations
        await new Promise(resolve => setTimeout(resolve, 3000));
    }

    console.log(`\n📊 [PROXY-TEST] Rotation Results:`);
    console.log(`Unique IPs found: ${ips.size}/${iterations}`);
    console.log(`Unique IPs: ${Array.from(ips).join(', ')}`);

    const rotationWorking = ips.size > 1;
    console.log(`${rotationWorking ? '✅' : '⚠️'} Proxy rotation: ${rotationWorking ? 'WORKING' : 'NOT WORKING'}`);

    return {
        iterations: iterations,
        uniqueIPs: ips.size,
        allIPs: Array.from(ips),
        results: results,
        rotationWorking: rotationWorking
    };
}

/**
 * Test browser consistency (multiple tests should show same IP)
 */
export async function testBrowserConsistency(iterations = 3) {
    console.log(`🔄 [BROWSER-TEST] Testing browser consistency with ${iterations} iterations...`);
    
    const ips = new Set();
    const results = [];

    for (let i = 0; i < iterations; i++) {
        console.log(`\n🔄 [BROWSER-TEST] Iteration ${i + 1}/${iterations}`);
        
        const browser = await chromium.launch({ 
            headless: true,
            args: getStealthLaunchArgs()
        });

        try {
            const context = await browser.newContext(getStealthContextConfig());
            const page = await context.newPage();
            await setupStealthMode(page);

            // Get IP
            await page.goto('https://httpbin.org/ip', { 
                waitUntil: 'networkidle',
                timeout: 30000 
            });

            const response = await page.textContent('pre');
            const data = JSON.parse(response);
            const ip = data.origin;

            ips.add(ip);
            results.push({ iteration: i + 1, ip: ip });
            
            console.log(`🌐 [BROWSER-TEST] Iteration ${i + 1} IP: ${ip}`);

            await browser.close();

        } catch (error) {
            console.error(`❌ [BROWSER-TEST] Iteration ${i + 1} failed: ${error.message}`);
            await browser.close();
        }

        // Wait between iterations
        await new Promise(resolve => setTimeout(resolve, 2000));
    }

    console.log(`\n📊 [BROWSER-TEST] Consistency Results:`);
    console.log(`Unique IPs found: ${ips.size}/${iterations}`);
    console.log(`IPs: ${Array.from(ips).join(', ')}`);

    const isConsistent = ips.size === 1;
    console.log(`${isConsistent ? '✅' : '⚠️'} IP consistency: ${isConsistent ? 'GOOD' : 'INCONSISTENT'}`);

    return {
        iterations: iterations,
        uniqueIPs: ips.size,
        allIPs: Array.from(ips),
        results: results,
        isConsistent: isConsistent
    };
}

/**
 * Quick browser connectivity test
 */
export async function quickBrowserTest() {
    console.log('⚡ [BROWSER-TEST] Quick browser connectivity test...');
    
    try {
        const result = await testBrowserConnectivity(15000);
        
        if (result.success) {
            console.log(`✅ [BROWSER-TEST] Quick test PASSED - Browser is working`);
            console.log(`🌐 Current IP: ${result.ip}`);
            return true;
        } else {
            console.log(`❌ [BROWSER-TEST] Quick test FAILED - ${result.error}`);
            return false;
        }
    } catch (error) {
        console.error(`❌ [BROWSER-TEST] Quick test ERROR: ${error.message}`);
        return false;
    }
}

/**
 * Test browser with NICS24 target website
 */
export async function testBrowserWithNICS24() {
    console.log('🏦 [BROWSER-TEST] Testing browser with NICS24 website...');
    
    const browser = await chromium.launch({ 
        headless: true,
        args: getStealthLaunchArgs()
    });

    try {
        const context = await browser.newContext(getStealthContextConfig());
        const page = await context.newPage();
        await setupStealthMode(page);

        // Test accessing NICS24 login page
        console.log('🌐 [BROWSER-TEST] Accessing NICS24 login page...');
        
        await page.goto('https://etebarito.nics24.ir/login-username', {
            waitUntil: 'networkidle',
            timeout: 30000
        });

        const title = await page.title();
        const url = page.url();

        console.log(`✅ [BROWSER-TEST] NICS24 access successful`);
        console.log(`📄 [BROWSER-TEST] Page title: ${title}`);
        console.log(`🔗 [BROWSER-TEST] Current URL: ${url}`);

        await browser.close();
        return {
            success: true,
            title: title,
            url: url
        };

    } catch (error) {
        console.error(`❌ [BROWSER-TEST] NICS24 access failed: ${error.message}`);
        await browser.close();
        return {
            success: false,
            error: error.message
        };
    }
}

/**
 * Monitor browser connectivity over time
 */
export async function monitorBrowserConnectivity(duration = 60000) {
    console.log(`📊 [BROWSER-TEST] Monitoring browser connectivity for ${duration/1000} seconds...`);
    
    const startTime = Date.now();
    const testResults = [];
    let testCount = 0;

    while (Date.now() - startTime < duration) {
        try {
            testCount++;
            console.log(`🎯 [BROWSER-TEST] Test ${testCount}...`);
            
            const result = await testBrowserConnectivity(10000);
            testResults.push({
                test: testCount,
                success: result.success,
                ip: result.ip,
                timestamp: new Date().toISOString()
            });

            console.log(`${result.success ? '✅' : '❌'} Test ${testCount}: ${result.success ? 'SUCCESS' : 'FAILED'}`);
            if (result.ip) {
                console.log(`🌐 IP: ${result.ip}`);
            }
            
            // Wait between tests
            await new Promise(resolve => setTimeout(resolve, 5000));
            
        } catch (error) {
            console.error(`❌ [BROWSER-TEST] Monitoring error: ${error.message}`);
        }
    }

    const successfulTests = testResults.filter(r => r.success);
    const failedTests = testResults.filter(r => !r.success);

    console.log('\n📈 [BROWSER-TEST] Monitoring Statistics:');
    console.log(`Total tests: ${testCount}`);
    console.log(`Successful: ${successfulTests.length} (${((successfulTests.length / testCount) * 100).toFixed(1)}%)`);
    console.log(`Failed: ${failedTests.length} (${((failedTests.length / testCount) * 100).toFixed(1)}%)`);

    return {
        duration: duration,
        totalTests: testCount,
        successfulTests: successfulTests.length,
        failedTests: failedTests.length,
        results: testResults
    };
}