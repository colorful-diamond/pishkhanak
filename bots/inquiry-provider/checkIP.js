#!/usr/bin/env node

/**
 * Quick IP Checker Script
 * Check your IP with and without proxy
 */

import { getCurrentIP, getCurrentIPThroughProxy, compareDirectVsProxy, verifyIPConsistency, verifyBrowserWorking } from './utils/ipChecker.js';

async function main() {
    console.log('🌐 IP Checker & HTTP Proxy Tester');
    console.log('==================================\n');

    const args = process.argv.slice(2);
    const command = args[0] || 'check';

    try {
        switch (command) {
            case 'check':
            case 'current':
                console.log('📍 Getting your current IP (direct)...\n');
                const currentIP = await getCurrentIP();
                console.log(`\nYour current IP: ${currentIP}`);
                break;

            case 'proxy':
                console.log('🔒 Getting your IP through HTTP proxy...\n');
                const proxyIP = await getCurrentIPThroughProxy();
                console.log(`\nYour proxy IP: ${proxyIP}`);
                break;

            case 'compare':
                console.log('🔄 Comparing direct vs HTTP proxy IP...\n');
                const isWorking = await compareDirectVsProxy();
                process.exit(isWorking ? 0 : 1);
                break;

            case 'consistency':
                console.log('🔄 Checking IP consistency...\n');
                const isConsistent = await verifyIPConsistency();
                process.exit(isConsistent ? 0 : 1);
                break;

            case 'verify':
                console.log('✅ Quick browser verification...\n');
                const result = await verifyBrowserWorking();
                console.log(`\nBrowser ${result.success ? 'is working' : 'failed'}`);
                process.exit(result.success ? 0 : 1);
                break;

            case 'help':
            default:
                console.log('Available commands:');
                console.log('  check       - Get your current IP address (direct)');
                console.log('  current     - Same as check');
                console.log('  proxy       - Get your IP through HTTP proxy');
                console.log('  compare     - Compare direct vs HTTP proxy IP');
                console.log('  consistency - Check IP consistency (multiple tests)');
                console.log('  verify      - Quick browser verification');
                console.log('  help        - Show this help\n');
                console.log('Examples:');
                console.log('  node checkIP.js check');
                console.log('  node checkIP.js proxy');
                console.log('  node checkIP.js compare');
                console.log('  node checkIP.js consistency');
                console.log('  node checkIP.js verify');
                break;
        }
    } catch (error) {
        console.error('❌ IP check failed:', error.message);
        process.exit(1);
    }
}

main();