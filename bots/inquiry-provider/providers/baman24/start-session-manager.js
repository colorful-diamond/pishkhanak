#!/usr/bin/env node
/**
 * Baman24 Session Manager Startup Script
 * 
 * This script starts the baman24 session manager to automatically refresh
 * sessions every 5 minutes to prevent logout.
 * 
 * Usage:
 *   node start-session-manager.js
 *   
 * Or run in background:
 *   nohup node start-session-manager.js > session-manager.log 2>&1 &
 */

import { sessionManager } from './sessionManager.js';

console.log('🚀 [BMN24-STARTUP] Starting Baman24 Session Manager');
console.log('⏰ [BMN24-STARTUP] Sessions will be refreshed every 5 minutes');
console.log('📁 [BMN24-STARTUP] Session files will be monitored in: ./sessions/');

// Start the session manager
sessionManager.start();

// Log initial status
setTimeout(() => {
    const status = sessionManager.getStatus();
    console.log('📊 [BMN24-STARTUP] Initial status:', JSON.stringify(status, null, 2));
}, 2000);

// Handle graceful shutdown
process.on('SIGINT', () => {
    console.log('\n🛑 [BMN24-STARTUP] Received SIGINT (Ctrl+C), shutting down gracefully...');
    sessionManager.stop();
    console.log('✅ [BMN24-STARTUP] Session manager stopped. Goodbye!');
    process.exit(0);
});

process.on('SIGTERM', () => {
    console.log('\n🛑 [BMN24-STARTUP] Received SIGTERM, shutting down gracefully...');
    sessionManager.stop();
    console.log('✅ [BMN24-STARTUP] Session manager stopped. Goodbye!');
    process.exit(0);
});

// Log status every 10 minutes
setInterval(() => {
    const status = sessionManager.getStatus();
    console.log('📊 [BMN24-STARTUP] Status update:', {
        timestamp: new Date().toISOString(),
        activeSessions: status.activeSessions,
        isRunning: status.isRunning
    });
    
    if (status.sessions.length > 0) {
        console.log('📋 [BMN24-STARTUP] Active sessions:');
        status.sessions.forEach(session => {
            console.log(`  - ${session.mobile}: refreshed ${session.refreshCount} times, last: ${session.lastRefresh}`);
        });
    }
}, 10 * 60 * 1000); // Every 10 minutes

// Log heartbeat every minute to show it's running
setInterval(() => {
    const now = new Date().toISOString();
    console.log(`💓 [BMN24-STARTUP] Heartbeat: ${now} - Session manager running with ${sessionManager.getStatus().activeSessions} active sessions`);
}, 60 * 1000); // Every minute

console.log('✅ [BMN24-STARTUP] Session manager is now running');
console.log('💡 [BMN24-STARTUP] Press Ctrl+C to stop the service');
console.log('📝 [BMN24-STARTUP] Monitor the logs to see refresh activities'); 