# NICS24 Provider Test Script Usage

## 📝 Overview

The `test-nics24-provider.php` script allows you to test the NICS24 credit score provider directly from the command line. It simulates the complete flow including OTP handling.

## 🚀 Quick Start

### Basic Test
```bash
php test-nics24-provider.php
```

### Test with Custom Data
```bash
php test-nics24-provider.php --mobile=09123456789 --national_code=1234567890 --debug
```

### Test with Specific Provider
```bash
php test-nics24-provider.php --provider=nics24 --timeout=600 --debug
```

## ⚙️ Available Options

| Option | Description | Default | Example |
|--------|-------------|---------|---------|
| `--mobile` | Mobile number for OTP | `09123456789` | `--mobile=09121234567` |
| `--national_code` | National code | `1234567890` | `--national_code=0123456789` |
| `--provider` | Provider name | `nics24` | `--provider=nics24` |
| `--timeout` | Timeout in seconds | `300` | `--timeout=600` |
| `--poll_interval` | Progress polling interval | `2` | `--poll_interval=3` |
| `--debug` | Enable debug output | `false` | `--debug` |
| `--help` | Show help message | - | `--help` |

## 📋 Prerequisites

### 1. Node.js Server Running
Make sure the inquiry-provider server is running:
```bash
cd pishkhanak.com/bots/inquiry-provider
npm start
# or
node localApiServer.js
```

### 2. Redis Server Running
Ensure Redis is running on the default port (6379).

### 3. NICS24 Credentials
Update credentials in `pishkhanak.com/bots/inquiry-provider/providers/nics24/config.js`:
```javascript
export const nics24Config = {
  users: [
    {
      username: "your_actual_nics24_username",
      password: "your_actual_nics24_password",
      description: "NICS24 primary account"
    }
  ],
  // ... rest of config
};
```

### 4. Captcha API Running
Make sure the captcha solving API is running on `localhost:9090`.

## 🔄 Test Flow

1. **Server Connectivity** - Tests connection to Node.js server
2. **Request Initiation** - Starts credit score request with NICS24
3. **Progress Monitoring** - Monitors request progress via API
4. **OTP Handling** - Prompts for OTP when needed
5. **Result Display** - Shows final results

## 📊 Example Output

### Successful Test
```
🔍 NICS24 Provider Test Script
===============================

✅ Server connectivity OK
Request started successfully. Hash: abc123def456
Progress: [████████████████████] 100% | Stage: completed | گزارش اعتباری با موفقیت دریافت شد

=== FINAL RESULTS ===
✅ Test Status: SUCCESS
⏱️  Duration: 45 seconds
📊 Final Data: {
  "creditScore": 750,
  "status": "good",
  "provider": "nics24"
}

📋 Log file: /path/to/nics24-test-2024-01-15-14-30-25.log
```

### OTP Input
```
📱 OTP has been sent to 09123456789
Please enter the 5-digit OTP code: 12345
✅ OTP submitted successfully
Progress: [████████████░░░░░░░░] 60% | Stage: processing | در حال دریافت گزارش اعتباری...
```

## 🐛 Troubleshooting

### Common Issues

#### 1. Connection Refused
```
Connection error: Connection refused
```
**Solution**: Make sure Node.js server is running on port 9999.

#### 2. Invalid Response
```
Invalid response format: {"error":"Service not found"}
```
**Solution**: Check if NICS24 provider is properly integrated in the main service.

#### 3. OTP Timeout
```
Request timed out
```
**Solution**: Increase timeout or check if OTP is being sent correctly.

#### 4. Authentication Failed
```
Authentication failed
```
**Solution**: Verify NICS24 credentials in config.js.

### Debug Mode

Enable debug mode for detailed logs:
```bash
php test-nics24-provider.php --debug
```

This will show:
- Detailed API requests/responses
- Progress updates
- Redis interactions
- Session management details

### Log Files

Each test run creates a log file:
- Location: `nics24-test-YYYY-MM-DD-HH-MM-SS.log`
- Contains: All debug information, API calls, errors
- Useful for: Troubleshooting and monitoring

## 🔧 Advanced Usage

### Testing Different Scenarios

#### Test with Invalid OTP
```bash
# Start test, enter wrong OTP to test error handling
php test-nics24-provider.php --debug
```

#### Test Session Management
```bash
# Run multiple tests to verify session reuse
php test-nics24-provider.php --mobile=09123456789
php test-nics24-provider.php --mobile=09123456789  # Should reuse session
```

#### Test with Different Providers
```bash
# Test different providers (if available)
php test-nics24-provider.php --provider=baman24
php test-nics24-provider.php --provider=nics24
```

### Automated Testing

Create a bash script for automated testing:
```bash
#!/bin/bash
# test-all-providers.sh

echo "Testing NICS24 Provider..."
php test-nics24-provider.php --mobile=09123456789 --national_code=1234567890 --debug

if [ $? -eq 0 ]; then
    echo "✅ NICS24 test passed"
else
    echo "❌ NICS24 test failed"
fi
```

## 📞 Support

If you encounter issues:
1. Check the log file for detailed error information
2. Verify all prerequisites are met
3. Test with debug mode enabled
4. Check Node.js server logs
5. Verify Redis connectivity

## 🔗 Related Files

- `pishkhanak.com/bots/inquiry-provider/providers/nics24/` - NICS24 provider code
- `pishkhanak.com/bots/inquiry-provider/localApiServer.js` - Local API server
- `pishkhanak.com/app/Http/Controllers/Services/CreditScoreRatingBackgroundController.php` - Laravel controller