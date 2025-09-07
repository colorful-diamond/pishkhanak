# 🚀 PM2 Quick Start - Get Running in 2 Minutes!

## **Based on your logs, the server isn't running. Let's fix that!**

Your Laravel logs show:
```
Local API call failed - Connection refused on port 9999
```

**This means the Node.js server isn't running. Let's start it with PM2!**

---

## ⚡ **Super Quick Start**

### **Step 1: Install PM2 (if not installed)**
```bash
npm install -g pm2
```

### **Step 2: Start the Server**
```bash
# From your project root
cd pishkhanak.com/bots/inquiry-provider

# Start with PM2 ecosystem file
pm2 start ecosystem.config.js --env production

# Or use our management script
cd ../../..
pishkhanak.com/pm2-manage.sh start
```

### **Step 3: Check if it's working**
```bash
# Check PM2 status
pm2 list

# Test the server
curl http://127.0.0.1:9999/health

# Or use our health check
pishkhanak.com/pm2-manage.sh health
```

---

## 🎯 **One-Command Setup**

If you want everything done automatically:

```bash
# This will install dependencies, setup environment, and start the server
pishkhanak.com/pm2-manage.sh setup
pishkhanak.com/pm2-manage.sh start
```

---

## 🔧 **Manual PM2 Commands**

```bash
# Navigate to the API directory
cd pishkhanak.com/bots/inquiry-provider

# Start the server
pm2 start ecosystem.config.js --env production

# Check status
pm2 list
pm2 describe pishkhanak-local-api

# View logs
pm2 logs pishkhanak-local-api

# Stop/Restart
pm2 stop pishkhanak-local-api
pm2 restart pishkhanak-local-api
```

---

## ✅ **Verify Everything Works**

After starting, test these:

1. **PM2 Status**: `pm2 list` should show `pishkhanak-local-api` as `online`
2. **Health Check**: `curl http://127.0.0.1:9999/health` should return `{"status":"ok","server":"running"}`
3. **Laravel Test**: Try your credit score service again

---

## 🆘 **If Still Not Working**

### **Check Common Issues:**

1. **Node.js Version**
   ```bash
   node --version  # Should be 18+
   ```

2. **Port Already in Use**
   ```bash
   netstat -tulpn | grep 9999
   # If something else is using port 9999, kill it:
   sudo kill -9 PID_NUMBER
   ```

3. **Dependencies Missing**
   ```bash
   cd pishkhanak.com/bots/inquiry-provider
   npm install
   ```

4. **Environment File**
   ```bash
   # Check if .env exists and has GEMINI_API_KEY
   cat pishkhanak.com/bots/inquiry-provider/.env
   ```

### **View Detailed Logs**
```bash
# Real-time logs
pm2 logs pishkhanak-local-api

# Error logs only
pm2 logs pishkhanak-local-api --err

# Last 100 lines
pm2 logs pishkhanak-local-api --lines 100
```

---

## 🎉 **Once It's Running**

Your Laravel application will be able to connect to the local API server and:

- ✅ Process credit score requests
- ✅ Handle captcha solving automatically 
- ✅ Manage OTP verification
- ✅ Show beautiful result pages

**The "Connection refused" errors will disappear!**

---

## 📞 **Need Help?**

If you're still having issues:

1. **Check PM2 status**: `pm2 list`
2. **View logs**: `pm2 logs pishkhanak-local-api`
3. **Test manually**: `curl http://127.0.0.1:9999/health`
4. **Restart clean**: `pm2 delete pishkhanak-local-api` then `pm2 start ecosystem.config.js`

**The ecosystem file and management script are ready - just need to start the server!** 🚀 