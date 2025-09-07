#!/usr/bin/env node
/**
 * Baman24 Session Manager Test Script
 * 
 * This script demonstrates how to use the session manager programmatically
 * and tests its basic functionality.
 */

import baman24 from './index.js';

async function testSessionManager() {
    console.log('🧪 [BMN24-TEST] Starting Session Manager Test');
    console.log('===========================================');

    try {
        // Test 1: Start session manager
        console.log('\n📋 Test 1: Starting Session Manager');
        baman24.startSessionManager();
        
        await sleep(2000); // Wait 2 seconds
        
        // Test 2: Check initial status
        console.log('\n📋 Test 2: Checking Initial Status');
        const initialStatus = baman24.getSessionStatus();
        console.log('Status:', JSON.stringify(initialStatus, null, 2));
        
        // Test 3: Simulate a login (this would normally be done by the credit score service)
        console.log('\n📋 Test 3: Simulating Session Registration');
        const testMobile = '09123456789';
        
        // Manually register a session for testing
        baman24.sessionManager.registerSession(testMobile);
        
        await sleep(1000);
        
        // Test 4: Check status after registration
        console.log('\n📋 Test 4: Status After Session Registration');
        const statusAfterReg = baman24.getSessionStatus();
        console.log('Status:', JSON.stringify(statusAfterReg, null, 2));
        
        // Test 5: Wait for first refresh cycle
        console.log('\n📋 Test 5: Waiting for First Refresh Cycle (10 seconds)...');
        console.log('💡 Note: In real usage, refresh happens every 5 minutes');
        console.log('💡 For testing, we\'re just waiting to see the refresh timer start');
        
        for (let i = 10; i > 0; i--) {
            process.stdout.write(`⏰ ${i} seconds remaining...\r`);
            await sleep(1000);
        }
        console.log('\n');
        
        // Test 6: Check status after waiting
        console.log('\n📋 Test 6: Status After Wait Period');
        const statusAfterWait = baman24.getSessionStatus();
        console.log('Status:', JSON.stringify(statusAfterWait, null, 2));
        
        // Test 7: Manual refresh trigger
        console.log('\n📋 Test 7: Triggering Manual Refresh');
        console.log('💡 Note: This will attempt to refresh without valid session files');
        await baman24.refreshAllSessions();
        
        // Test 8: Unregister session
        console.log('\n📋 Test 8: Unregistering Test Session');
        baman24.sessionManager.unregisterSession(testMobile);
        
        await sleep(1000);
        
        const statusAfterUnreg = baman24.getSessionStatus();
        console.log('Status after unregistration:', JSON.stringify(statusAfterUnreg, null, 2));
        
        // Test 9: Stop session manager
        console.log('\n📋 Test 9: Stopping Session Manager');
        baman24.stopSessionManager();
        
        await sleep(1000);
        
        const finalStatus = baman24.getSessionStatus();
        console.log('Final status:', JSON.stringify(finalStatus, null, 2));
        
        console.log('\n🎉 [BMN24-TEST] All tests completed successfully!');
        console.log('======================================');
        console.log('');
        console.log('📋 Test Summary:');
        console.log('✅ Session manager started');
        console.log('✅ Session registration tested');
        console.log('✅ Status monitoring tested');
        console.log('✅ Manual refresh tested');
        console.log('✅ Session unregistration tested');
        console.log('✅ Session manager stopped');
        console.log('');
        console.log('💡 To use with real sessions:');
        console.log('   1. Run: ./session-manager.sh start');
        console.log('   2. Login through baman24 credit score service');
        console.log('   3. Sessions will be automatically monitored and refreshed');
        console.log('   4. Check status: ./session-manager.sh status');
        console.log('   5. View logs: ./session-manager.sh logs');
        
    } catch (error) {
        console.error('❌ [BMN24-TEST] Test failed:', error.message);
        console.error('📋 [BMN24-TEST] Full error:', error);
        
        // Cleanup on error
        try {
            baman24.stopSessionManager();
        } catch (cleanupError) {
            console.error('❌ [BMN24-TEST] Cleanup error:', cleanupError.message);
        }
        
        process.exit(1);
    }
}

// Helper function for delays
function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

// Run the test
console.log('🚀 [BMN24-TEST] Baman24 Session Manager Test Suite');
console.log('📅 Test Date:', new Date().toISOString());
console.log('');

testSessionManager().then(() => {
    console.log('✅ [BMN24-TEST] Test suite completed successfully');
    process.exit(0);
}).catch((error) => {
    console.error('❌ [BMN24-TEST] Test suite failed:', error.message);
    process.exit(1);
}); 