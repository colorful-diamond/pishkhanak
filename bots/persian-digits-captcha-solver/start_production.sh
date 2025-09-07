#!/bin/bash
# Persian Digit Captcha API - Production Startup Script
# ====================================================

set -e

# Configuration
APP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
VENV_DIR="$APP_DIR/venv_production"
LOG_DIR="$APP_DIR/logs"
APP_NAME="captcha-api"

# Default environment variables
export COLOR_TOLERANCE=${COLOR_TOLERANCE:-30}
export USE_PATTERN_SHADOW_DETECTION=${USE_PATTERN_SHADOW_DETECTION:-true}
export LOG_LEVEL=${LOG_LEVEL:-INFO}
export PORT=${PORT:-8989}

# Function to check if server is running
is_running() {
    pm2 describe "$APP_NAME" > /dev/null 2>&1
}

# Function to start server
start_server() {
    if is_running; then
        echo "❌ Server is already running"
        pm2 describe "$APP_NAME"
        return 1
    fi
    
    echo "🚀 Starting Persian Digit Captcha API Server with PM2..."
    
    # Check requirements
    if ! command -v pm2 &> /dev/null; then
        echo "❌ PM2 not found. Run deploy.sh first."
        return 1
    fi
    
    if [ ! -f "$APP_DIR/persian_digit.keras" ]; then
        echo "❌ Model file not found: persian_digit.keras"
        return 1
    fi
    
    if [ ! -f "$APP_DIR/ecosystem.config.js" ]; then
        echo "❌ PM2 config file not found: ecosystem.config.js"
        return 1
    fi
    
    # Create log directory
    mkdir -p "$LOG_DIR"
    
    # Activate virtual environment for the PM2 process
    if [ -d "$VENV_DIR" ]; then
        export PATH="$VENV_DIR/bin:$PATH"
    fi
    
    # Start with PM2
    cd "$APP_DIR"
    pm2 start ecosystem.config.js --env production
    
    # Wait a moment and check if it started
    sleep 3
    if is_running; then
        echo "✅ Server started successfully with PM2"
        echo "🌐 Listening on: http://127.0.0.1:$PORT"
        echo "📋 Logs: pm2 logs $APP_NAME"
        echo "📊 Monitor: pm2 monit"
        echo "🛑 Stop: $0 stop"
        pm2 describe "$APP_NAME"
        return 0
    else
        echo "❌ Server failed to start. Check PM2 logs:"
        echo "   pm2 logs $APP_NAME"
        return 1
    fi
}

# Function to stop server
stop_server() {
    if ! is_running; then
        echo "ℹ️  Server is not running"
        return 0
    fi
    
    echo "🛑 Stopping Persian Digit Captcha API Server with PM2..."
    
    # Stop with PM2
    pm2 stop "$APP_NAME"
    
    # Wait a moment
    sleep 2
    
    if ! is_running; then
        echo "✅ Server stopped successfully"
        return 0
    else
        echo "⚠️  Server still running, forcing delete..."
        pm2 delete "$APP_NAME"
        echo "✅ Server removed from PM2"
        return 0
    fi
}

# Function to restart server
restart_server() {
    echo "🔄 Restarting Persian Digit Captcha API Server with PM2..."
    if is_running; then
        pm2 restart "$APP_NAME"
        echo "✅ Server restarted successfully"
        pm2 describe "$APP_NAME"
    else
        echo "❌ Server is not running, starting instead..."
        start_server
    fi
}

# Function to show server status
show_status() {
    echo "📊 Persian Digit Captcha API Server Status:"
    echo ""
    
    if is_running; then
        echo "✅ Server is running with PM2"
        echo "🌐 URL: http://127.0.0.1:$PORT"
        echo "📊 Health: curl http://127.0.0.1:$PORT/health"
        echo ""
        echo "📋 PM2 Status:"
        pm2 describe "$APP_NAME"
        echo ""
        echo "💾 Memory Usage:"
        pm2 describe "$APP_NAME" | grep -E "(memory|cpu)"
    else
        echo "❌ Server is not running"
        echo ""
        echo "📋 All PM2 processes:"
        pm2 list
    fi
}

# Function to show logs
show_logs() {
    if is_running; then
        echo "📋 Following PM2 logs for $APP_NAME (Ctrl+C to exit):"
        pm2 logs "$APP_NAME" --lines 50
    else
        echo "❌ Server is not running"
        echo "📋 Recent PM2 logs:"
        pm2 logs "$APP_NAME" --lines 20 || echo "No logs available"
    fi
}

# Main script
case "${1:-start}" in
    start)
        start_server
        ;;
    stop)
        stop_server
        ;;
    restart)
        restart_server
        ;;
    status)
        show_status
        ;;
    logs)
        show_logs
        ;;
    monitor|monit)
        echo "🖥️  Opening PM2 monitoring dashboard..."
        pm2 monit
        ;;
    list)
        echo "📋 PM2 processes:"
        pm2 list
        ;;
    *)
        echo "Usage: $0 {start|stop|restart|status|logs|monitor|list}"
        echo ""
        echo "Commands:"
        echo "  start   - Start the API server with PM2"
        echo "  stop    - Stop the API server"
        echo "  restart - Restart the API server"
        echo "  status  - Show detailed server status"
        echo "  logs    - Follow PM2 logs"
        echo "  monitor - Open PM2 monitoring dashboard"
        echo "  list    - List all PM2 processes"
        echo ""
        echo "Environment variables:"
        echo "  COLOR_TOLERANCE (default: 30)"
        echo "  USE_PATTERN_SHADOW_DETECTION (default: true)"
        echo "  LOG_LEVEL (default: INFO)"
        echo "  PORT (default: 8989)"
        echo ""
        echo "Direct PM2 commands:"
        echo "  pm2 start ecosystem.config.js --env production"
        echo "  pm2 stop captcha-api"
        echo "  pm2 restart captcha-api"
        echo "  pm2 logs captcha-api"
        echo "  pm2 monit"
        exit 1
        ;;
esac