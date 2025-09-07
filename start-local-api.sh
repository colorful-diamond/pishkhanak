#!/bin/bash

# Pishkhanak Local API Server Startup Script
# This script starts the local API server on port 9999

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
LOCAL_API_DIR="./bots/inquiry-provider"
LOG_DIR="./storage/logs"
PID_FILE="./storage/local-api-server.pid"

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Function to check if server is running
is_server_running() {
    if [ -f "$PID_FILE" ]; then
        local pid=$(cat "$PID_FILE")
        if ps -p "$pid" > /dev/null 2>&1; then
            return 0
        else
            rm -f "$PID_FILE"
            return 1
        fi
    fi
    return 1
}

# Function to stop server
stop_server() {
    if is_server_running; then
        local pid=$(cat "$PID_FILE")
        print_status "Stopping local API server (PID: $pid)..."
        
        # Try graceful shutdown first
        kill -TERM "$pid" 2>/dev/null || true
        
        # Wait up to 10 seconds for graceful shutdown
        local count=0
        while [ $count -lt 10 ] && ps -p "$pid" > /dev/null 2>&1; do
            sleep 1
            count=$((count + 1))
        done
        
        # Force kill if still running
        if ps -p "$pid" > /dev/null 2>&1; then
            print_warning "Force killing server..."
            kill -KILL "$pid" 2>/dev/null || true
        fi
        
        rm -f "$PID_FILE"
        print_success "Local API server stopped"
    else
        print_warning "Local API server is not running"
    fi
}

# Function to start server
start_server() {
    if is_server_running; then
        print_warning "Local API server is already running"
        local pid=$(cat "$PID_FILE")
        print_status "Server PID: $pid"
        print_status "Check status: curl http://127.0.0.1:9999/health"
        return 0
    fi
    
    print_status "Starting Pishkhanak Local API Server..."
    
    # Check if Node.js is installed
    if ! command -v node &> /dev/null; then
        print_error "Node.js is not installed. Please install Node.js 18+ to run the local API server."
        exit 1
    fi
    
    # Check Node.js version
    local node_version=$(node --version | cut -d'v' -f2 | cut -d'.' -f1)
    if [ "$node_version" -lt 18 ]; then
        print_error "Node.js version 18+ is required. Current version: $(node --version)"
        exit 1
    fi
    
    # Check if directory exists
    if [ ! -d "$LOCAL_API_DIR" ]; then
        print_error "Local API directory not found: $LOCAL_API_DIR"
        exit 1
    fi
    
    # Change to local API directory
    cd "$LOCAL_API_DIR"
    
    # Check if package.json exists
    if [ ! -f "package.json" ]; then
        print_error "package.json not found in $LOCAL_API_DIR"
        exit 1
    fi
    
    # Check if node_modules exists, install if not
    if [ ! -d "node_modules" ]; then
        print_status "Installing dependencies..."
        if command -v npm &> /dev/null; then
            npm install
        elif command -v yarn &> /dev/null; then
            yarn install
        else
            print_error "Neither npm nor yarn found. Please install npm or yarn."
            exit 1
        fi
    fi
    
    # Create logs directory if it doesn't exist
    mkdir -p "../../$LOG_DIR"
    
    # Start the server in background
    print_status "Launching server..."
    
    # Set environment variables
    export NODE_ENV=production
    export LOCAL_API_PORT=9999
    export DEBUG_MODE=false
    
    # Start server and capture PID
    nohup node local-api-server.js > "../../$LOG_DIR/local-api-server.log" 2>&1 &
    local server_pid=$!
    
    # Save PID
    echo "$server_pid" > "../../$PID_FILE"
    
    # Wait a moment and check if server started successfully
    sleep 2
    
    if ps -p "$server_pid" > /dev/null 2>&1; then
        print_success "Local API server started successfully!"
        print_status "Server PID: $server_pid"
        print_status "Server URL: http://127.0.0.1:9999"
        print_status "Log file: $LOG_DIR/local-api-server.log"
        
        # Test server health
        sleep 1
        if curl -s http://127.0.0.1:9999/health > /dev/null 2>&1; then
            print_success "Server health check passed ✓"
        else
            print_warning "Server health check failed. Check logs for details."
        fi
        
        echo ""
        echo "📋 Available endpoints:"
        echo "   • Health check: http://127.0.0.1:9999/health"
        echo "   • List services: http://127.0.0.1:9999/api/services"
        echo "   • Service endpoint: http://127.0.0.1:9999/api/services/{service-slug}"
        echo ""
        echo "🔍 To check status: ./start-local-api.sh status"
        echo "⏹️  To stop server: ./start-local-api.sh stop"
        echo "📄 View logs: tail -f $LOG_DIR/local-api-server.log"
        
    else
        print_error "Failed to start local API server"
        rm -f "../../$PID_FILE"
        print_status "Check the log file for details: $LOG_DIR/local-api-server.log"
        exit 1
    fi
    
    # Return to original directory
    cd - > /dev/null
}

# Function to show server status
show_status() {
    if is_server_running; then
        local pid=$(cat "$PID_FILE")
        print_success "Local API server is running"
        print_status "PID: $pid"
        print_status "URL: http://127.0.0.1:9999"
        
        # Test health endpoint
        if curl -s http://127.0.0.1:9999/health > /dev/null 2>&1; then
            print_success "Health check: ✓ PASS"
        else
            print_warning "Health check: ✗ FAIL"
        fi
        
        # Show uptime if available
        local uptime_info=$(curl -s http://127.0.0.1:9999/health 2>/dev/null | grep -o '"uptime":[0-9]*' | cut -d':' -f2)
        if [ ! -z "$uptime_info" ]; then
            local uptime_hours=$((uptime_info / 3600))
            local uptime_minutes=$(((uptime_info % 3600) / 60))
            local uptime_seconds=$((uptime_info % 60))
            print_status "Uptime: ${uptime_hours}h ${uptime_minutes}m ${uptime_seconds}s"
        fi
    else
        print_warning "Local API server is not running"
    fi
}

# Function to restart server
restart_server() {
    print_status "Restarting local API server..."
    stop_server
    sleep 1
    start_server
}

# Function to show logs
show_logs() {
    if [ -f "$LOG_DIR/local-api-server.log" ]; then
        print_status "Showing last 50 lines of server logs:"
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
        tail -n 50 "$LOG_DIR/local-api-server.log"
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
        echo ""
        print_status "To follow logs in real-time: tail -f $LOG_DIR/local-api-server.log"
    else
        print_warning "Log file not found: $LOG_DIR/local-api-server.log"
    fi
}

# Function to show help
show_help() {
    echo "Pishkhanak Local API Server Management Script"
    echo ""
    echo "Usage: $0 [COMMAND]"
    echo ""
    echo "Commands:"
    echo "  start     Start the local API server"
    echo "  stop      Stop the local API server"
    echo "  restart   Restart the local API server"
    echo "  status    Show server status"
    echo "  logs      Show recent server logs"
    echo "  help      Show this help message"
    echo ""
    echo "Examples:"
    echo "  $0 start          # Start the server"
    echo "  $0 status         # Check if server is running"
    echo "  $0 logs           # View recent logs"
    echo ""
    echo "Server Info:"
    echo "  Port: 9999"
    echo "  URL: http://127.0.0.1:9999"
    echo "  PID File: $PID_FILE"
    echo "  Log File: $LOG_DIR/local-api-server.log"
}

# Main script logic
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
    help|--help|-h)
        show_help
        ;;
    *)
        print_error "Unknown command: $1"
        echo ""
        show_help
        exit 1
        ;;
esac 