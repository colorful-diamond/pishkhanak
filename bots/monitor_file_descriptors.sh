#!/bin/bash

# Monitor file descriptors for Node.js and Python processes
# This script helps identify which processes are consuming too many file descriptors

echo "======================================"
echo "File Descriptor Monitoring Script"
echo "Date: $(date)"
echo "======================================"

# Check system limits
echo -e "\n1. System File Descriptor Limits:"
echo "   Current open files: $(cat /proc/sys/fs/file-nr 2>/dev/null | awk '{print $1}' || lsof 2>/dev/null | wc -l)"
echo "   Maximum allowed: $(cat /proc/sys/fs/file-max 2>/dev/null || ulimit -n)"

# Check user limits
echo -e "\n2. User Limits:"
ulimit -a | grep "open files"

# Monitor Node.js processes
echo -e "\n3. Node.js Processes File Descriptors:"
for pid in $(pgrep -f "node.*inquiry-provider" 2>/dev/null); do
    if [ -n "$pid" ]; then
        process_name=$(ps -p $pid -o comm= 2>/dev/null)
        fd_count=$(ls /proc/$pid/fd 2>/dev/null | wc -l || lsof -p $pid 2>/dev/null | wc -l)
        echo "   PID $pid ($process_name): $fd_count file descriptors"
        
        # Show top file descriptor types if count is high
        if [ "$fd_count" -gt 100 ]; then
            echo "   WARNING: High file descriptor count!"
            echo "   Top file types:"
            lsof -p $pid 2>/dev/null | awk '{print $5}' | sort | uniq -c | sort -nr | head -5
        fi
    fi
done

# Monitor Python processes
echo -e "\n4. Python Processes File Descriptors:"
for pid in $(pgrep -f "python.*captcha" 2>/dev/null); do
    if [ -n "$pid" ]; then
        process_name=$(ps -p $pid -o comm= 2>/dev/null)
        fd_count=$(ls /proc/$pid/fd 2>/dev/null | wc -l || lsof -p $pid 2>/dev/null | wc -l)
        echo "   PID $pid ($process_name): $fd_count file descriptors"
        
        # Show top file descriptor types if count is high
        if [ "$fd_count" -gt 100 ]; then
            echo "   WARNING: High file descriptor count!"
            echo "   Top file types:"
            lsof -p $pid 2>/dev/null | awk '{print $5}' | sort | uniq -c | sort -nr | head -5
        fi
    fi
done

# Check for socket leaks
echo -e "\n5. Socket Connections:"
echo "   Total TCP connections: $(netstat -ant 2>/dev/null | grep -c ESTABLISHED || ss -t 2>/dev/null | grep -c ESTAB)"
echo "   Redis connections: $(netstat -ant 2>/dev/null | grep :6379 | grep -c ESTABLISHED || ss -t 2>/dev/null | grep :6379 | grep -c ESTAB)"

# Check for file leaks
echo -e "\n6. Open File Handles:"
echo "   Total open files: $(lsof 2>/dev/null | wc -l)"
echo "   Files by process:"
lsof 2>/dev/null | awk '{print $1}' | sort | uniq -c | sort -nr | head -10

# Recommendations
echo -e "\n7. Recommendations:"
current_open=$(cat /proc/sys/fs/file-nr 2>/dev/null | awk '{print $1}' || lsof 2>/dev/null | wc -l)
max_allowed=$(cat /proc/sys/fs/file-max 2>/dev/null || ulimit -n)

if [ -n "$current_open" ] && [ -n "$max_allowed" ]; then
    usage_percent=$((current_open * 100 / max_allowed))
    if [ "$usage_percent" -gt 80 ]; then
        echo "   ⚠️  CRITICAL: File descriptor usage is at ${usage_percent}%!"
        echo "   - Restart services to release file descriptors"
        echo "   - Check for resource leaks in the code"
    elif [ "$usage_percent" -gt 60 ]; then
        echo "   ⚠️  WARNING: File descriptor usage is at ${usage_percent}%"
        echo "   - Monitor closely for increases"
        echo "   - Consider restarting services during low traffic"
    else
        echo "   ✅ File descriptor usage is at ${usage_percent}% - Normal"
    fi
fi

echo -e "\n======================================"
echo "To increase file descriptor limits:"
echo "1. Temporary: ulimit -n 65535"
echo "2. Permanent: Add to /etc/security/limits.conf:"
echo "   * soft nofile 65535"
echo "   * hard nofile 65535"
echo "3. For systemd services, add to service file:"
echo "   LimitNOFILE=65535"
echo "======================================
