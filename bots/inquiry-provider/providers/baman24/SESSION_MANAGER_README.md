# Baman24 Session Manager

## Overview
The Baman24 Session Manager automatically refreshes login sessions every 5 minutes to prevent logout from the baman24.ir website. This ensures continuous service availability without manual intervention.

## Features
- ✅ Automatic session refresh every 5 minutes
- ✅ Monitors multiple sessions simultaneously
- ✅ Automatic detection of new session files
- ✅ Graceful session cleanup when invalid
- ✅ Comprehensive logging and status reporting
- ✅ Graceful shutdown handling

## Quick Start

### 1. Start the Session Manager
```bash
# Navigate to baman24 provider directory
cd pishkhanak.com/bots/inquiry-provider/providers/baman24/

# Start the session manager
node start-session-manager.js
```

### 2. Run in Background
```bash
# Run as background process with logging
nohup node start-session-manager.js > session-manager.log 2>&1 &

# Check if it's running
ps aux | grep session-manager

# View logs
tail -f session-manager.log
```

### 3. Stop the Session Manager
```bash
# If running in foreground, press Ctrl+C

# If running in background, find and kill the process
pkill -f "session-manager"
```

## How It Works

### Automatic Session Registration
When a user logs in through the baman24 provider:
1. Login success triggers automatic session registration
2. Session file is created in `./sessions/session-{mobile}.json`
3. Session manager detects the new file and starts monitoring it
4. Refresh timer begins (5-minute intervals)

### Session Refresh Process
Every 5 minutes, for each monitored session:
1. **Check Login Status**: Verify the session is still valid
2. **Refresh Session**: Update the session cookies and tokens
3. **Save State**: Store the refreshed session data
4. **Log Results**: Record success/failure with timestamps

### Session Cleanup
- Invalid sessions are automatically unregistered
- Session files that no longer exist are removed from monitoring
- Failed refreshes are logged but don't immediately remove the session

## File Structure
```
baman24/
├── sessionManager.js           # Core session manager class
├── start-session-manager.js    # Startup script
├── sessions/                   # Session storage directory
│   ├── session-{mobile1}.json  # Individual session files
│   └── session-{mobile2}.json
├── refresh.js                  # Session refresh logic
├── checkLogin.js              # Session validation logic
└── SESSION_MANAGER_README.md   # This documentation
```

## API Usage

### Programmatic Control
```javascript
import baman24 from './index.js';

// Start session manager
baman24.startSessionManager();

// Check status
const status = baman24.getSessionStatus();
console.log('Active sessions:', status.activeSessions);

// Manual refresh all sessions
await baman24.refreshAllSessions();

// Stop session manager
baman24.stopSessionManager();
```

### Direct Session Manager Access
```javascript
import { sessionManager } from './sessionManager.js';

// Manual session registration
sessionManager.registerSession('09123456789');

// Unregister session
sessionManager.unregisterSession('09123456789');

// Get detailed status
const status = sessionManager.getStatus();
```

## Configuration

### Refresh Interval
The refresh interval is set to 5 minutes (300,000ms) in `sessionManager.js`:
```javascript
this.refreshInterval = 5 * 60 * 1000; // 5 minutes
```

To change the interval, modify this value and restart the session manager.

### Session Timeout
Baman24 sessions typically expire after 10 minutes of inactivity. The 5-minute refresh interval provides a safe margin to prevent logout.

## Logging

### Log Levels
- 🚀 **Startup**: Service initialization
- 🔄 **Refresh**: Session refresh activities  
- ✅ **Success**: Successful operations
- ❌ **Error**: Failed operations
- ⚠️ **Warning**: Non-critical issues
- 📊 **Status**: Periodic status reports
- 💓 **Heartbeat**: Service alive indicators

### Log Format
```
[TIMESTAMP] [LEVEL] [BMN24-SESSION-MANAGER] Message: Details
```

Example:
```
🔄 [BMN24-SESSION-MANAGER] Refreshing session: 09123456789 (refresh #3)
✅ [BMN24-SESSION-MANAGER] Session 09123456789 refreshed successfully (count: 3)
```

## Monitoring

### Status Checks
The session manager provides real-time status information:
```javascript
{
  "isRunning": true,
  "activeSessions": 2,
  "refreshInterval": 300,
  "sessions": [
    {
      "mobile": "09123456789",
      "lastRefresh": "2025-01-23T10:30:00.000Z",
      "refreshCount": 5,
      "isActive": true
    }
  ]
}
```

### Health Monitoring
- **Heartbeat**: Logged every minute
- **Status Report**: Detailed status every 10 minutes
- **Session Activity**: Each refresh attempt is logged

## Troubleshooting

### Common Issues

1. **Session Manager Won't Start**
   - Check if Node.js supports ES modules
   - Verify all dependencies are available
   - Ensure sessions directory is writable

2. **Sessions Not Being Refreshed**
   - Check if session files exist in `./sessions/`
   - Verify the session manager is running
   - Review logs for error messages

3. **High Memory Usage**
   - Monitor the number of active sessions
   - Check for memory leaks in logs
   - Restart the session manager periodically

### Debug Mode
Set `DEBUG_MODE=true` in your environment to enable detailed logging:
```bash
DEBUG_MODE=true node start-session-manager.js
```

## Security Considerations

- Session files contain sensitive authentication data
- Ensure proper file permissions on the sessions directory
- Monitor access to session manager logs
- Regularly rotate log files to prevent disk space issues

## Best Practices

1. **Run as System Service**: Use systemd or similar to ensure automatic restart
2. **Log Rotation**: Implement log rotation to manage disk space
3. **Monitoring**: Set up alerts for session manager failures
4. **Backup**: Regularly backup session files if persistence is critical
5. **Updates**: Keep the session manager updated with the latest fixes

## Support

For issues or questions about the session manager:
1. Check the logs for error messages
2. Review this documentation
3. Test with a single session first
4. Contact the development team with specific error details 