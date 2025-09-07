# 📡 HTTP Proxy Testing Guide

This guide shows you how to test your local HTTP proxies (127.0.0.1:8001-8004) that route through your public IPs.

## 🔧 Your Setup

You have configured:
- **HTTP proxy on 127.0.0.1:8001** → Expected to show **109.206.254.170**
- **HTTP proxy on 127.0.0.1:8002** → Expected to show **109.206.254.171**
- **HTTP proxy on 127.0.0.1:8003** → Expected to show **109.206.254.172**
- **HTTP proxy on 127.0.0.1:8004** → Expected to show **109.206.254.173**

## 🚀 Quick Testing Commands

### 1. Test All HTTP Proxies
```bash
node testProxies.js proxies
```

**Expected Output:**
```
🚀 [PROXY-TEST] Starting HTTP proxy testing...
📡 [PROXY-TEST] Testing 4 HTTP proxies

🔍 [PROXY-TEST] Testing HTTP proxy: http://127.0.0.1:8001 (expected IP: 109.206.254.170)
✅ [PROXY-TEST] Proxy http://127.0.0.1:8001 is working
🎯 [PROXY-TEST] Expected IP: 109.206.254.170
📍 [PROXY-TEST] Actual IP: 109.206.254.170
✅ [PROXY-TEST] IP Match: YES

📊 [PROXY-TEST] Test Results Summary:
✅ Working proxies: 4/4
❌ Failed proxies: 0/4
🎯 IP matching proxies: 4/4
```

### 2. Test HTTP Proxy Rotation
```bash
node testProxies.js rotation
```

This tests that different proxies give different public IPs.

### 3. Compare Direct vs Proxy IP
```bash
node checkIP.js compare
```

**Expected Output:**
```
🔄 [IP-CHECK] Comparing direct IP vs HTTP proxy IP...

1️⃣ Getting direct IP...
📍 [IP-CHECK] Direct IP: 109.206.254.170

2️⃣ Getting IP through HTTP proxy...
🌐 [STEALTH] Using HTTP proxy: http://127.0.0.1:8002
🎯 [STEALTH] Expected public IP: 109.206.254.171
📍 [IP-CHECK] Proxy IP: 109.206.254.171

📊 [IP-CHECK] Comparison Results:
Direct IP:     109.206.254.170
Proxy IP:      109.206.254.171
IPs Different: ✅ YES
✅ [IP-CHECK] HTTP proxy is working correctly - IP changed!
```

## 🧪 All Available Tests

### HTTP Proxy Tests
```bash
# Test all HTTP proxies
node testProxies.js proxies
node testProxies.js http-proxies

# Test proxy rotation
node testProxies.js rotation
node testProxies.js proxy-rotation
```

### IP Checking
```bash
# Get current direct IP
node checkIP.js check

# Get IP through random HTTP proxy
node checkIP.js proxy

# Compare direct vs proxy
node checkIP.js compare
```

### Browser Tests
```bash
# Test basic browser functionality
node testProxies.js test

# Test with NICS24 website
node testProxies.js nics24

# Monitor connectivity
node testProxies.js monitor 30
```

## 🛠️ Manual Testing with curl

You can also test your HTTP proxies manually:

```bash
# Test each proxy individually
curl --proxy http://127.0.0.1:8001 https://httpbin.org/ip
curl --proxy http://127.0.0.1:8002 https://httpbin.org/ip
curl --proxy http://127.0.0.1:8003 https://httpbin.org/ip
curl --proxy http://127.0.0.1:8004 https://httpbin.org/ip
```

**Expected Results:**
```json
{"origin": "109.206.254.170"}
{"origin": "109.206.254.171"}
{"origin": "109.206.254.172"}
{"origin": "109.206.254.173"}
```

## ✅ Success Indicators

Your HTTP proxies are working correctly if:

1. **All proxies accessible**: `node testProxies.js proxies` shows 4/4 working
2. **IP matching**: Each proxy shows its expected public IP
3. **Rotation working**: `node testProxies.js rotation` shows multiple unique IPs
4. **Different from direct**: `node checkIP.js compare` shows different IPs

## ❌ Troubleshooting

### Problem: "Connection refused" errors
**Solution:** Make sure your HTTP proxy servers are running:
```bash
# Check if proxies are listening
netstat -an | grep :8001
netstat -an | grep :8002
netstat -an | grep :8003
netstat -an | grep :8004
```

### Problem: Wrong IP addresses
**Possible causes:**
- HTTP proxy not configured correctly
- Proxy routing through wrong interface
- Multiple network interfaces

**Debug steps:**
1. Test individual proxies with curl
2. Check proxy server logs
3. Verify network interface configuration

### Problem: Same IP as direct connection
**Possible causes:**
- HTTP proxy not intercepting traffic
- Proxy bypassing local traffic
- Configuration issue

**Debug steps:**
1. Test with external site: `curl --proxy http://127.0.0.1:8001 https://httpbin.org/ip`
2. Check proxy configuration
3. Verify proxy is binding to correct interface

## 🎯 Integration with NICS24

Once your HTTP proxies are working, you can use them in your NICS24 automation:

```javascript
// Enable HTTP proxy in login
const context = await browser.newContext(getStealthContextConfig(null, null, true));

// Console output will show:
// 🌐 [STEALTH] Using HTTP proxy: http://127.0.0.1:8002
// 🎯 [STEALTH] Expected public IP: 109.206.254.171
```

## 📊 Expected Performance

With working HTTP proxies, you should see:
- ✅ 4/4 proxies working
- ✅ Each proxy showing correct public IP
- ✅ Different IPs on rotation tests
- ✅ NICS24 website accessible through proxies

## 🚀 Quick Start Checklist

1. **Start your HTTP proxy servers** on ports 8001-8004
2. **Run quick test**: `node testProxies.js proxies`
3. **Test rotation**: `node testProxies.js rotation`
4. **Compare IPs**: `node checkIP.js compare`
5. **Test with NICS24**: `node testProxies.js nics24`

If all tests pass, your HTTP proxy setup is ready for use with your public IPs! 🎉