#!/usr/bin/env node

/**
 * Proxy Testing Script
 * Run this script to test your SOCKS5 proxies
 */

import { 
    testBrowserBasics, 
    testBrowserConsistency, 
    quickBrowserTest, 
    testBrowserWithNICS24,
    monitorBrowserConnectivity,
    testAllHttpProxies,
    testHttpProxyRotation
} from './utils/proxyTester.js';

async function main() {
    console.log('🚀 Browser & HTTP Proxy Testing Suite');
    console.log('=====================================\n');

    const args = process.argv.slice(2);
    const command = args[0] || 'test';

    try {
        switch (command) {
            case 'quick':
                console.log('⚡ Running Quick Browser Test...\n');
                const quickResult = await quickBrowserTest();
                process.exit(quickResult ? 0 : 1);
                break;

            case 'test':
            case 'all':
                console.log('🔍 Testing Browser Basics...\n');
                const testResult = await testBrowserBasics();
                console.log(`\n${testResult.success ? '✅' : '❌'} Browser test: ${testResult.success ? 'PASSED' : 'FAILED'}`);
                process.exit(testResult.success ? 0 : 1);
                break;

            case 'proxies':
            case 'http-proxies':
                console.log('📡 Testing HTTP Proxies (127.0.0.1:8001-8004)...\n');
                const proxyResults = await testAllHttpProxies();
                console.log(`\n✅ HTTP Proxy test completed: ${proxyResults.working}/${proxyResults.total} proxies working`);
                console.log(`🎯 IP matching: ${proxyResults.ipMatching}/${proxyResults.working} proxies show expected IP`);
                process.exit(proxyResults.working > 0 ? 0 : 1);
                break;

            case 'rotation':
            case 'proxy-rotation':
                console.log('🔄 Testing HTTP Proxy Rotation...\n');
                const rotationResults = await testHttpProxyRotation(4);
                console.log(`\n${rotationResults.rotationWorking ? '✅' : '❌'} Rotation test: ${rotationResults.uniqueIPs} unique IPs found`);
                process.exit(rotationResults.rotationWorking ? 0 : 1);
                break;

            case 'consistency':
                console.log('🔄 Testing Browser Consistency...\n');
                const consistencyResults = await testBrowserConsistency(4);
                console.log(`\n${consistencyResults.isConsistent ? '✅' : '⚠️'} Consistency test: ${consistencyResults.isConsistent ? 'GOOD' : 'INCONSISTENT'}`);
                process.exit(consistencyResults.isConsistent ? 0 : 1);
                break;

            case 'nics24':
                console.log('🏦 Testing with NICS24 Website...\n');
                const nicsResult = await testBrowserWithNICS24();
                console.log(`\n${nicsResult.success ? '✅' : '❌'} NICS24 test: ${nicsResult.success ? 'SUCCESS' : nicsResult.error}`);
                process.exit(nicsResult.success ? 0 : 1);
                break;

            case 'monitor':
                const duration = parseInt(args[1]) || 30;
                console.log(`📊 Monitoring Browser Connectivity for ${duration} seconds...\n`);
                const monitorResult = await monitorBrowserConnectivity(duration * 1000);
                const successRate = (monitorResult.successfulTests / monitorResult.totalTests) * 100;
                console.log(`\n${successRate >= 90 ? '✅' : '⚠️'} Monitoring completed: ${successRate.toFixed(1)}% success rate`);
                process.exit(successRate >= 90 ? 0 : 1);
                break;

            case 'help':
            default:
                console.log('Available commands:');
                console.log('');
                console.log('Browser Tests:');
                console.log('  quick        - Quick browser connectivity test');
                console.log('  test         - Test basic browser functionality');
                console.log('  all          - Same as test (test basic browser functionality)');
                console.log('  consistency  - Test browser consistency (multiple runs)');
                console.log('  nics24       - Test browser with NICS24 website');
                console.log('  monitor      - Monitor browser connectivity (duration in seconds)');
                console.log('');
                console.log('HTTP Proxy Tests:');
                console.log('  proxies      - Test all HTTP proxies (127.0.0.1:8001-8004)');
                console.log('  http-proxies - Same as proxies');
                console.log('  rotation     - Test HTTP proxy rotation (different public IPs)');
                console.log('  proxy-rotation - Same as rotation');
                console.log('');
                console.log('  help         - Show this help\n');
                console.log('Examples:');
                console.log('  node testProxies.js quick');
                console.log('  node testProxies.js test');
                console.log('  node testProxies.js proxies');
                console.log('  node testProxies.js rotation');
                console.log('  node testProxies.js nics24');
                console.log('  node testProxies.js monitor 60');
                process.exit(0);
        }
    } catch (error) {
        console.error('❌ Test failed:', error.message);
        process.exit(1);
    }
}

// Handle uncaught errors
process.on('uncaughtException', (error) => {
    console.error('❌ Uncaught exception:', error.message);
    process.exit(1);
});

process.on('unhandledRejection', (error) => {
    console.error('❌ Unhandled rejection:', error.message);
    process.exit(1);
});

main();