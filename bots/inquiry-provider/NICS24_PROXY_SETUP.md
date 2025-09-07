# 🎯 NICS24 HTTP Proxy Setup - ACTIVATED! 

## ✅ **HTTP Proxy is now ACTIVE in NICS24**

Your NICS24 provider is now configured to use HTTP proxies randomly from ports 8001-8004, routing through your different public IPs.

## 🔧 **What Changed**

### Updated Files:
1. **`providers/nics24/login.js`** - ✅ HTTP proxy enabled
   - `getStealthContextConfig(null, null, true)` in getAuthenticatedPage()
   - `getStealthContextConfig(null, null, true)` in loadSession()

2. **`providers/nics24/checkLogin.js`** - ✅ HTTP proxy enabled  
   - `getStealthContextConfig(null, null, true)` in checkLogin()

3. **`providers/nics24/services/creditScoreService.js`** - ✅ Uses authenticated page from login.js (inherits proxy)

## 🌐 **Your HTTP Proxy Configuration**

| Proxy Server | Public IP | Port |
|--------------|-----------|------|
| `127.0.0.1:8001` | `109.206.254.170` | 8001 |
| `127.0.0.1:8002` | `109.206.254.171` | 8002 |
| `127.0.0.1:8003` | `109.206.254.172` | 8003 |
| `127.0.0.1:8004` | `109.206.254.173` | 8004 |

## 📱 **How It Works**

When you run any NICS24 operation, it will:

1. **Randomly select** one of your HTTP proxies (8001-8004)
2. **Route traffic** through your public IP servers
3. **Log the proxy usage** in console:
   ```
   🌐 [STEALTH] Using HTTP proxy: http://127.0.0.1:8002
   🎯 [STEALTH] Expected public IP: 109.206.254.171
   🔄 [STEALTH] HTTP proxy rotation enabled
   ```

## 🚀 **Usage Examples**

### Regular NICS24 Operations (with proxy):
```javascript
// Login with random HTTP proxy
const result = await login(user);

// Check login with random HTTP proxy  
const isLoggedIn = await checkLogin(user);

// Credit score with random HTTP proxy
const score = await getCreditScore(user, nationalId);
```

### Console Output Example:
```bash
🌐 [NICS24-LOGIN] Creating new browser page...
🌐 [STEALTH] Using HTTP proxy: http://127.0.0.1:8003
🎯 [STEALTH] Expected public IP: 109.206.254.172
🔄 [STEALTH] HTTP proxy rotation enabled
🎭 [STEALTH] Enhanced stealth mode enabled
```

## 🧪 **Testing Your Setup**

### 1. Test HTTP Proxies:
```bash
cd pishkhanak.com/bots/inquiry-provider/

# Test all proxies
node testProxies.js proxies

# Test proxy rotation  
node testProxies.js rotation

# Compare direct vs proxy
node checkIP.js compare
```

### 2. Test NICS24 with Proxy:
```bash
# Test NICS24 functionality (update credentials first)
node testNics24Proxy.js
```

### 3. Manual Verification:
```bash
# Check each proxy manually
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

## 📊 **Monitoring Proxy Usage**

### Real-time Monitoring:
- Watch console output for proxy selection logs
- Each NICS24 operation will show which proxy is being used
- Different operations should use different proxies randomly

### Expected Behavior:
- ✅ **Random proxy selection** on each browser context creation
- ✅ **Different public IPs** across multiple operations  
- ✅ **Stealth mode active** with all proxy connections
- ✅ **Session persistence** works with proxies

## 🔧 **Disable Proxy (if needed)**

To disable HTTP proxy and use direct connection:

**Change in both files:**
```javascript
// FROM (with proxy):
const context = await browser.newContext(getStealthContextConfig(null, null, true));

// TO (direct connection):
const context = await browser.newContext(getStealthContextConfig(null, null, false));
// OR simply:
const context = await browser.newContext(getStealthContextConfig());
```

## ⚡ **Performance Benefits**

With HTTP proxy rotation you get:
- **IP diversity**: Different public IPs for different requests
- **Load distribution**: Traffic spread across multiple servers
- **Anonymity**: Harder to track/block individual sessions  
- **Reliability**: Fallback IPs if one has issues

## ✅ **Verification Checklist**

Before using in production:

- [ ] **HTTP proxies running**: Verify all 4 proxies (8001-8004) are active
- [ ] **IP mapping correct**: Each proxy shows expected public IP
- [ ] **NICS24 login works**: Can successfully login through proxy
- [ ] **Session persistence**: Saved sessions work with proxy
- [ ] **Different IPs**: Multiple operations show different public IPs

## 🎯 **Ready to Use!**

Your NICS24 provider is now configured to automatically use HTTP proxy rotation. Every login, checkLogin, and credit score operation will randomly select one of your 4 public IP addresses! 🚀

**Run your NICS24 operations as normal - proxy usage is now automatic and transparent!**