# 🔍 SOCKS5 Proxy Testing Guide

This guide shows you multiple ways to verify that your SOCKS5 proxies are working correctly.

## 🚀 Quick Start

### 1. Quick IP Comparison Test
```bash
cd pishkhanak.com/bots/inquiry-provider
node checkIP.js compare
```

**Expected Output:**
```
🔄 Comparing direct vs proxy IP...

1️⃣ Getting IP without proxy...
📍 [IP-CHECK] Current IP: 203.0.113.1

2️⃣ Getting IP through proxy...
🌐 [STEALTH] Using SOCKS5 proxy: socks5://127.0.0.1:1081
📍 [IP-CHECK] Current IP: 198.51.100.1

📊 [IP-CHECK] Comparison Results:
Direct IP:     203.0.113.1
Proxy IP:      198.51.100.1
IPs Different: ✅ YES
✅ [IP-CHECK] Proxy is working correctly - IP changed!
```

### 2. Quick Proxy Test
```bash
node testProxies.js quick
```

## 🔧 Comprehensive Testing

### 1. Test All Proxies
```bash
node testProxies.js all
```

This will test all 4 SOCKS5 proxies:
- `127.0.0.1:1081`
- `127.0.0.1:1082`
- `127.0.0.1:1083`
- `127.0.0.1:1084`

**Expected Output:**
```
🔍 [PROXY-TEST] Testing proxy: socks5://127.0.0.1:1081
✅ [PROXY-TEST] Proxy socks5://127.0.0.1:1081 is working - IP: 198.51.100.1

🔍 [PROXY-TEST] Testing proxy: socks5://127.0.0.1:1082
✅ [PROXY-TEST] Proxy socks5://127.0.0.1:1082 is working - IP: 198.51.100.2

📊 [PROXY-TEST] Test Results Summary:
✅ Working proxies: 4/4
❌ Failed proxies: 0/4

🟢 Working Proxies:
  socks5://127.0.0.1:1081 → IP: 198.51.100.1
  socks5://127.0.0.1:1082 → IP: 198.51.100.2
  socks5://127.0.0.1:1083 → IP: 198.51.100.3
  socks5://127.0.0.1:1084 → IP: 198.51.100.4
```

### 2. Test Proxy Rotation
```bash
node testProxies.js rotation
```

This verifies that different proxies are selected randomly and give different IPs.

### 3. Test with NICS24 Website
```bash
node testProxies.js nics24
```

This tests if proxies work specifically with the NICS24 website.

### 4. Monitor Proxy Usage
```bash
node testProxies.js monitor 30
```

Monitor which proxies are being used over 30 seconds.

## 🔍 During Normal Operations

### Check Console Logs
When running your NICS24 scripts, look for these logs:

```
🌐 [STEALTH] Using SOCKS5 proxy: socks5://127.0.0.1:1081
🔒 [STEALTH] Proxy rotation enabled for enhanced anonymity
```

### Verify IP in Your Scripts
Add this to your scripts to verify proxy is working:

```javascript
import { verifyProxyWorking } from './utils/ipChecker.js';

// Before starting main operations
const proxyCheck = await verifyProxyWorking();
if (!proxyCheck.success) {
    console.error('❌ Proxy not working, aborting...');
    process.exit(1);
}
console.log(`✅ Using proxy IP: ${proxyCheck.ip}`);
```

## 🛠️ Troubleshooting

### Problem: All proxies show the same IP as direct connection

**Possible causes:**
1. SOCKS5 proxy services not running
2. Proxy ports blocked by firewall
3. Incorrect proxy configuration

**Solutions:**
1. Check if your SOCKS5 proxy service is running:
   ```bash
   netstat -an | grep :1081
   netstat -an | grep :1082
   netstat -an | grep :1083
   netstat -an | grep :1084
   ```

2. Test proxy manually with curl:
   ```bash
   curl --socks5 127.0.0.1:1081 https://httpbin.org/ip
   curl --socks5 127.0.0.1:1082 https://httpbin.org/ip
   ```

### Problem: Some proxies work, others don't

**Check individual proxy:**
```bash
# Test specific proxy with curl
curl --socks5 127.0.0.1:1081 https://httpbin.org/ip --connect-timeout 10
```

### Problem: Proxy connection timeout

**Possible causes:**
1. Proxy server down
2. Network connectivity issues
3. Firewall blocking connections

**Debug steps:**
1. Increase timeout in tests
2. Check proxy server logs
3. Test with different websites

## 📊 Monitoring Proxy Health

### Continuous Monitoring Script
Create a monitoring script to check proxy health regularly:

```javascript
import { testAllProxies } from './utils/proxyTester.js';

setInterval(async () => {
    console.log('🔄 Checking proxy health...');
    const results = await testAllProxies();
    
    if (results.working < results.total) {
        console.warn(`⚠️ Warning: Only ${results.working}/${results.total} proxies working`);
    } else {
        console.log(`✅ All ${results.total} proxies healthy`);
    }
}, 300000); // Check every 5 minutes
```

## 🔒 Security Verification

### Verify DNS Leaks
Your DNS requests should also go through the proxy. Test with:

```javascript
// In your browser context
await page.goto('https://dnsleaktest.com/', { waitUntil: 'networkidle' });
```

### Check WebRTC Leaks
WebRTC can leak your real IP even when using proxy:

```javascript
// This is already handled in our stealth configuration
// but you can verify at: https://browserleaks.com/webrtc
```

## 📈 Performance Testing

### Measure Proxy Speed
```bash
# Test download speed through each proxy
for port in 1081 1082 1083 1084; do
    echo "Testing proxy 127.0.0.1:$port"
    time curl --socks5 127.0.0.1:$port https://httpbin.org/bytes/1000000 > /dev/null
done
```

## 🎯 Integration with Your Scripts

### Add Proxy Verification to Login Scripts
```javascript
import { verifyProxyWorking } from './utils/ipChecker.js';
import nics24 from './providers/nics24/index.js';

async function secureLogin() {
    // Verify proxy before login
    console.log('🔒 Verifying proxy connection...');
    const proxyResult = await verifyProxyWorking();
    
    if (!proxyResult.success) {
        throw new Error('Proxy verification failed');
    }
    
    console.log(`✅ Proxy verified - IP: ${proxyResult.ip}`);
    
    // Proceed with login
    const loginResult = await nics24.login();
    return loginResult;
}
```

## 📝 Test Results Interpretation

### ✅ Good Results
- All 4 proxies working
- Different IPs for each proxy
- Proxy IP different from direct IP
- NICS24 website accessible through proxy

### ⚠️ Warning Signs
- Only some proxies working
- Same IP across different proxies
- Timeouts on proxy connections

### ❌ Critical Issues
- No proxies working
- Proxy IP same as direct IP
- Cannot access target websites through proxy

## 🔄 Automated Testing

Add this to your package.json scripts:

```json
{
  "scripts": {
    "test-proxies": "node testProxies.js all",
    "test-proxy-rotation": "node testProxies.js rotation",
    "check-ip": "node checkIP.js compare",
    "quick-proxy-test": "node testProxies.js quick"
  }
}
```

Then run with:
```bash
npm run test-proxies
npm run check-ip
```

## 🎯 Summary

Your proxies are working correctly if:
1. ✅ `node checkIP.js compare` shows different IPs
2. ✅ `node testProxies.js all` shows all proxies working
3. ✅ `node testProxies.js rotation` shows multiple unique IPs
4. ✅ Console logs show proxy usage during operations
5. ✅ `node testProxies.js nics24` successfully accesses NICS24