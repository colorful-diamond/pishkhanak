#!/usr/bin/env node

/**
 * Test NICS24 with HTTP Proxy
 * Quick test to verify NICS24 is using HTTP proxies correctly
 */

import { login } from './providers/nics24/login.js';
import { checkLogin } from './providers/nics24/checkLogin.js';

async function testNics24WithProxy() {
    console.log('🚀 Testing NICS24 with HTTP Proxy');
    console.log('==================================\n');

    try {
        // Test login with proxy
        console.log('1️⃣ Testing NICS24 login with HTTP proxy...\n');
        
        // You'll need to provide actual credentials for this test
        const testUser = {
            userName: 'YOUR_USERNAME',
            password: 'YOUR_PASSWORD'
        };

        console.log('📝 Note: Please update testUser credentials in testNics24Proxy.js for actual testing');
        console.log('🌐 This will use random HTTP proxy from ports 8001-8004');
        console.log('🎯 Expected to show different public IPs (109.206.254.170-173)\n');

        // Uncomment when you have valid credentials:
        // const loginResult = await login(testUser);
        // console.log('Login result:', loginResult ? '✅ SUCCESS' : '❌ FAILED');

        // Test checkLogin with proxy
        console.log('2️⃣ Testing NICS24 checkLogin with HTTP proxy...\n');
        
        // Uncomment when you have valid credentials:
        // const checkResult = await checkLogin(testUser);
        // console.log('Check login result:', checkResult ? '✅ LOGGED IN' : '❌ NOT LOGGED IN');

        console.log('📊 Test completed!');
        console.log('\n💡 Tips:');
        console.log('  - Check console output for proxy usage messages');
        console.log('  - Look for: "🌐 [STEALTH] Using HTTP proxy: http://127.0.0.1:800X"');
        console.log('  - Look for: "🎯 [STEALTH] Expected public IP: 109.206.254.XXX"');
        console.log('  - Run multiple times to see different proxies being used');

    } catch (error) {
        console.error('❌ Test failed:', error.message);
        process.exit(1);
    }
}

// Run the test
testNics24WithProxy();