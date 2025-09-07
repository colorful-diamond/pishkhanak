# 🚀 PM2 Setup Guide for Pishkhanak Local API Server

## Overview
This guide shows you how to use PM2 (Process Manager 2) to manage the Pishkhanak Local API Server for production deployment. PM2 provides automatic restarts, load balancing, logging, and monitoring.

## 🎯 Quick Start

### 1. **Setup (First Time Only)**
```bash
# Run the setup command - it will install PM2, dependencies, and check environment
./pm2-manage.sh setup
```

### 2. **Add Your API Key**
```bash
# Edit the .env file to add your Gemini API key
nano pishkhanak.com/bots/inquiry-provider/.env

# Add this line:
GEMINI_API_KEY=your_actual_api_key_here
```

### 3. **Start the Server**
```bash
# Start the server with PM2
./pm2-manage.sh start
```

### 4. **Check Health**
```bash
# Verify everything is working
./pm2-manage.sh health
```

## 📋 Available Commands

### **Management Commands**
```bash
./pm2-manage.sh start      # Start the application
./pm2-manage.sh stop       # Stop the application  
./pm2-manage.sh restart    # Restart the application
./pm2-manage.sh reload     # Graceful reload (zero-downtime)
./pm2-manage.sh delete     # Remove from PM2
```

### **Monitoring Commands**
```bash
./pm2-manage.sh status     # Show application status
./pm2-manage.sh logs       # Show real-time logs
./pm2-manage.sh monitor    # Open PM2 dashboard
./pm2-manage.sh health     # Perform health check
```

### **Maintenance Commands**
```bash
./pm2-manage.sh setup      # Full setup process
./pm2-manage.sh save       # Save PM2 process list
./pm2-manage.sh startup    # Setup auto-start on boot
```

## 🔧 Direct PM2 Commands

If you prefer using PM2 directly:

### **Basic Operations**
```bash
# Start with ecosystem file
cd pishkhanak.com/bots/inquiry-provider
pm2 start ecosystem.config.js --env production

# Alternative ways to start
pm2 start ecosystem.config.js --env development  # Development mode
pm2 start local-api-server.js --name pishkhanak-local-api  # Direct start

# Control the application
pm2 stop pishkhanak-local-api
pm2 restart pishkhanak-local-api
pm2 reload pishkhanak-local-api    # Graceful restart
pm2 delete pishkhanak-local-api
```

### **Monitoring & Logs**
```bash
# View logs
pm2 logs pishkhanak-local-api
pm2 logs pishkhanak-local-api --lines 100
pm2 logs pishkhanak-local-api --err        # Error logs only

# Monitor in real-time
pm2 monit

# Show detailed info
pm2 describe pishkhanak-local-api
pm2 list
```

### **Process Management**
```bash
# Save current process list
pm2 save

# Resurrect saved processes
pm2 resurrect

# Setup auto-start on system boot
pm2 startup
# Follow the instructions shown

# Stop all processes
pm2 stop all
pm2 delete all
```

## 📊 PM2 Ecosystem Configuration

The `ecosystem.config.js` file contains all the configuration:

```javascript
module.exports = {
  apps: [{
    name: 'pishkhanak-local-api',
    script: './local-api-server.js',
    cwd: '/f/Projects/Pishkhanak/pishkhanak.com/bots/inquiry-provider',
    
    // Environment variables
    env: {
      NODE_ENV: 'production',
      PORT: 9999,
      DEBUG_MODE: 'false'
    },
    
    // Resource limits
    max_memory_restart: '500M',
    instances: 1,
    exec_mode: 'fork',
    
    // Auto-restart configuration  
    autorestart: true,
    max_restarts: 10,
    min_uptime: '10s',
    
    // Logging
    log_file: './logs/combined.log',
    out_file: './logs/out.log',
    error_file: './logs/error.log'
  }]
};
```

## 🔍 Troubleshooting

### **Common Issues**

#### **1. PM2 Not Found**
```bash
# Install PM2 globally
npm install -g pm2

# Or use our setup script
./pm2-manage.sh setup
```

#### **2. Port 9999 Already in Use**
```bash
# Check what's using the port
netstat -tulpn | grep 9999
lsof -i :9999

# Kill the process
sudo kill -9 PID_NUMBER
```

#### **3. Application Won't Start**
```bash
# Check logs for errors
pm2 logs pishkhanak-local-api --err

# Check application status
pm2 describe pishkhanak-local-api

# Restart with fresh logs
pm2 delete pishkhanak-local-api
pm2 start ecosystem.config.js --env production
```

#### **4. Health Check Fails**
```bash
# Check if server is running
pm2 list

# Test manually
curl http://127.0.0.1:9999/health

# Check logs
pm2 logs pishkhanak-local-api
```

#### **5. Environment Variables Not Loading**
```bash
# Check .env file exists
ls -la pishkhanak.com/bots/inquiry-provider/.env

# Verify contents
cat pishkhanak.com/bots/inquiry-provider/.env

# Restart to reload environment
pm2 restart pishkhanak-local-api
```

### **Log Locations**
```
📁 pishkhanak.com/bots/inquiry-provider/logs/
  📄 combined.log    # All output
  📤 out.log         # Standard output
  ❌ error.log       # Error output
```

## 🎯 Production Setup

### **1. Auto-Start on Boot**
```bash
# Setup PM2 to start on system boot
pm2 startup

# Save current process list
pm2 save
```

### **2. Monitoring Setup**
```bash
# Install PM2 monitoring (optional)
pm2 install pm2-server-monit

# View monitoring dashboard
pm2 monit
```

### **3. Log Rotation**
```bash
# Install log rotation module
pm2 install pm2-logrotate

# Configure log rotation
pm2 set pm2-logrotate:max_size 10M
pm2 set pm2-logrotate:retain 30
pm2 set pm2-logrotate:compress true
```

## 🚀 Advanced Usage

### **Development Mode**
```bash
# Start in development mode with file watching
pm2 start ecosystem.config.js --env development --watch

# Or directly with watch
pm2 start local-api-server.js --name pishkhanak-local-api-dev --watch
```

### **Load Balancing (If Needed)**
```javascript
// In ecosystem.config.js
module.exports = {
  apps: [{
    name: 'pishkhanak-local-api',
    script: './local-api-server.js',
    instances: 'max',  // Use all CPU cores
    exec_mode: 'cluster'  // Enable load balancing
  }]
};
```

### **Custom Environment**
```bash
# Create custom environment in ecosystem.config.js
env_staging: {
  NODE_ENV: 'staging',
  PORT: 9999,
  DEBUG_MODE: 'true'
}

# Start with custom environment
pm2 start ecosystem.config.js --env staging
```

## 💡 Best Practices

1. **Always use ecosystem file** for consistent deployments
2. **Set up log rotation** to prevent disk space issues
3. **Monitor memory usage** with `pm2 monit`
4. **Use graceful reloads** (`pm2 reload`) for zero-downtime updates
5. **Save process list** after making changes (`pm2 save`)
6. **Test health checks** regularly (`./pm2-manage.sh health`)

## 🔗 Useful Resources

- **PM2 Documentation**: https://pm2.keymetrics.io/docs/
- **PM2 Ecosystem File**: https://pm2.keymetrics.io/docs/usage/application-declaration/
- **PM2 Monitoring**: https://pm2.keymetrics.io/docs/usage/monitoring/

---

## ✅ Quick Reference

```bash
# 🚀 Start everything
./pm2-manage.sh setup
./pm2-manage.sh start
./pm2-manage.sh health

# 📊 Monitor
./pm2-manage.sh status
./pm2-manage.sh logs
./pm2-manage.sh monitor

# 🔄 Restart/Reload
./pm2-manage.sh restart
./pm2-manage.sh reload

# 🛑 Stop/Clean
./pm2-manage.sh stop
./pm2-manage.sh delete
```

**Your local API server is now properly managed with PM2! 🎉** 