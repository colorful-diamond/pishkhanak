# 🌐 Server IP Rotation Setup Guide

This guide explains how to configure and use your 5 server IPs (109.206.254.170-174) for Playwright automation instead of SOCKS5 proxies.

## 📋 Server IP Configuration

Your available server IPs:
- `109.206.254.170` (main)
- `109.206.254.171`
- `109.206.254.172`
- `109.206.254.173`
- `109.206.254.174`

## 🔧 Option 1: HTTP Proxy Setup (Recommended)

### Install Squid Proxy on Each IP

1. **Install Squid on your server:**
```bash
# Ubuntu/Debian
sudo apt update
sudo apt install squid

# CentOS/RHEL
sudo yum install squid
```

2. **Configure Squid for each IP:**

Create configuration files for each IP:

```bash
# For IP 109.206.254.170 (port 3128)
sudo cp /etc/squid/squid.conf /etc/squid/squid-170.conf

# For IP 109.206.254.171 (port 3129)
sudo cp /etc/squid/squid.conf /etc/squid/squid-171.conf

# For IP 109.206.254.172 (port 3130)
sudo cp /etc/squid/squid.conf /etc/squid/squid-172.conf

# For IP 109.206.254.173 (port 3131)
sudo cp /etc/squid/squid.conf /etc/squid/squid-173.conf

# For IP 109.206.254.174 (port 3132)
sudo cp /etc/squid/squid.conf /etc/squid/squid-174.conf
```

3. **Edit each configuration file:**

For `/etc/squid/squid-170.conf`:
```bash
# Basic configuration
http_port 109.206.254.170:3128
coredump_dir /var/spool/squid

# Allow access from your client machines
acl allowed_clients src 0.0.0.0/0  # Adjust this for security
http_access allow allowed_clients
http_access deny all

# Optional: Add authentication
# auth_param basic program /usr/lib/squid/basic_ncsa_auth /etc/squid/passwd
# acl authenticated proxy_auth REQUIRED
# http_access allow authenticated

# Outbound IP binding
tcp_outgoing_address 109.206.254.170
```

4. **Create systemd services for each proxy:**

Create `/etc/systemd/system/squid-170.service`:
```ini
[Unit]
Description=Squid Proxy Server on IP 170
After=network.target

[Service]
Type=forking
ExecStart=/usr/sbin/squid -f /etc/squid/squid-170.conf
ExecReload=/bin/kill -HUP $MAINPID
PIDFile=/var/run/squid-170.pid

[Install]
WantedBy=multi-user.target
```

Repeat for all IPs (171, 172, 173, 174) with appropriate ports and config files.

5. **Start all proxy services:**
```bash
sudo systemctl daemon-reload
sudo systemctl enable squid-170 squid-171 squid-172 squid-173 squid-174
sudo systemctl start squid-170 squid-171 squid-172 squid-173 squid-174
```

## 🔧 Option 2: Simple HTTP Proxy with Python

Create a simple HTTP proxy script for each IP:

```python
#!/usr/bin/env python3
# save as proxy_server.py

import sys
import socket
import threading
from urllib.parse import urlparse
import requests

class HTTPProxy:
    def __init__(self, bind_ip, port):
        self.bind_ip = bind_ip
        self.port = port
        
    def start(self):
        server = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        server.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)
        server.bind((self.bind_ip, self.port))
        server.listen(100)
        
        print(f"HTTP Proxy listening on {self.bind_ip}:{self.port}")
        
        while True:
            client, addr = server.accept()
            thread = threading.Thread(target=self.handle_client, args=(client,))
            thread.daemon = True
            thread.start()
    
    def handle_client(self, client):
        # Basic HTTP proxy implementation
        # This is a simplified version - use Squid for production
        pass

if __name__ == "__main__":
    if len(sys.argv) != 3:
        print("Usage: python3 proxy_server.py <bind_ip> <port>")
        sys.exit(1)
    
    proxy = HTTPProxy(sys.argv[1], int(sys.argv[2]))
    proxy.start()
```

## 🔧 Option 3: Direct IP Binding (Advanced)

If you want to bind directly to different IPs without proxy servers, you'll need OS-level configuration:

```bash
# Configure multiple IP addresses on your interface
sudo ip addr add 109.206.254.171/24 dev eth0
sudo ip addr add 109.206.254.172/24 dev eth0
sudo ip addr add 109.206.254.173/24 dev eth0
sudo ip addr add 109.206.254.174/24 dev eth0

# Set up routing rules for each IP
sudo ip rule add from 109.206.254.170 table 170
sudo ip rule add from 109.206.254.171 table 171
sudo ip rule add from 109.206.254.172 table 172
sudo ip rule add from 109.206.254.173 table 173
sudo ip rule add from 109.206.254.174 table 174

# Configure routing tables
sudo ip route add default via <gateway_ip> dev eth0 table 170
sudo ip route add default via <gateway_ip> dev eth0 table 171
sudo ip route add default via <gateway_ip> dev eth0 table 172
sudo ip route add default via <gateway_ip> dev eth0 table 173
sudo ip route add default via <gateway_ip> dev eth0 table 174
```

## 🧪 Testing Your Server IP Setup

### 1. Test Individual Server IPs

```bash
# Test each proxy/IP individually
node testProxies.js server-ips 3128
node testProxies.js server-ips 3129
node testProxies.js server-ips 3130
```

### 2. Test with curl (manual verification)

```bash
# Test each HTTP proxy
curl --proxy http://109.206.254.170:3128 https://httpbin.org/ip
curl --proxy http://109.206.254.171:3129 https://httpbin.org/ip
curl --proxy http://109.206.254.172:3130 https://httpbin.org/ip
curl --proxy http://109.206.254.173:3131 https://httpbin.org/ip
curl --proxy http://109.206.254.174:3132 https://httpbin.org/ip
```

Expected output - each should show a different IP:
```json
{"origin": "109.206.254.170"}
{"origin": "109.206.254.171"}
{"origin": "109.206.254.172"}
{"origin": "109.206.254.173"}
{"origin": "109.206.254.174"}
```

### 3. Test Server IP Rotation

```bash
# Test rotation through your Playwright setup
node testProxies.js all          # Tests all server IPs
node testProxies.js rotation     # Tests IP rotation
node checkIP.js compare          # Compare direct vs server IP
```

## 🎯 Code Integration

Your Playwright code will now automatically use server IP rotation:

```javascript
import { getStealthContextConfig, PROXY_STRATEGY } from './utils/stealthUtils.js';
import { chromium } from 'playwright';

// Using server IPs (default behavior)
const browser = await chromium.launch({ headless: true });
const context = await browser.newContext(
    getStealthContextConfig(null, null, true, PROXY_STRATEGY.SERVER_IPS, 3128)
);

// Console output:
// 🌐 [STEALTH] Using server IP: 109.206.254.172
// 🔄 [STEALTH] Server IP rotation enabled for enhanced anonymity
```

## 🔧 Configuration Options

### Change Default Strategy

Update your login files to use specific strategies:

```javascript
// In login.js, checkLogin.js, etc.
const context = await browser.newContext(
    getStealthContextConfig(null, null, true, PROXY_STRATEGY.SERVER_IPS, 3128)
);

// Or use mixed strategy (70% server IPs, 30% SOCKS5)
const context = await browser.newContext(
    getStealthContextConfig(null, null, true, PROXY_STRATEGY.MIXED, 3128)
);
```

### Custom Port Configuration

If your HTTP proxies run on different ports:

```javascript
// Port 8080 instead of 3128
const context = await browser.newContext(
    getStealthContextConfig(null, null, true, PROXY_STRATEGY.SERVER_IPS, 8080)
);
```

## 🛡️ Security Considerations

### 1. Firewall Configuration

Only allow proxy access from your client machines:

```bash
# Allow proxy ports only from your client IP
sudo ufw allow from <your_client_ip> to any port 3128:3132
sudo ufw deny 3128:3132
```

### 2. Proxy Authentication (Optional)

Add authentication to your Squid configuration:

```bash
# Create password file
sudo htpasswd -c /etc/squid/passwd username

# Add to squid.conf
auth_param basic program /usr/lib/squid/basic_ncsa_auth /etc/squid/passwd
acl authenticated proxy_auth REQUIRED
http_access allow authenticated
```

Then update your Playwright configuration:

```javascript
// With authentication
const context = await browser.newContext({
    proxy: {
        server: 'http://109.206.254.170:3128',
        username: 'your_username',
        password: 'your_password'
    }
});
```

## 📊 Monitoring and Logging

### Monitor Proxy Usage

```bash
# Monitor Squid access logs
sudo tail -f /var/log/squid/access.log

# Monitor specific proxy
sudo tail -f /var/log/squid-170/access.log
```

### Performance Testing

```bash
# Test speed of each server IP
for ip in 170 171 172 173 174; do
    echo "Testing 109.206.254.$ip"
    time curl --proxy http://109.206.254.$ip:3128 https://httpbin.org/bytes/1000000 > /dev/null
done
```

## 🚀 Quick Start Commands

1. **Test if your setup is working:**
```bash
node testProxies.js all
```

2. **Test IP rotation:**
```bash
node testProxies.js rotation
```

3. **Compare your IPs:**
```bash
node checkIP.js compare
```

4. **Test with NICS24:**
```bash
node testProxies.js nics24
```

## ✅ Verification Checklist

- [ ] HTTP proxy servers running on each IP
- [ ] Each proxy accessible on specified ports
- [ ] Each proxy returns different external IP
- [ ] Playwright tests pass with server IP rotation
- [ ] NICS24 website accessible through proxies
- [ ] No IP leaks or DNS leaks
- [ ] Proper firewall configuration
- [ ] Monitoring and logging setup

Your server IP rotation is now configured! The system will automatically rotate between your 5 server IPs for maximum anonymity and reliability. 🎯