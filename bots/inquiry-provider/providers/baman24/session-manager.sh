#!/bin/bash

# Baman24 Session Manager Control Script
# Usage: ./session-manager.sh {start|stop|restart|status|logs}

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
SESSION_MANAGER_SCRIPT="$SCRIPT_DIR/start-session-manager.js"
PID_FILE="$SCRIPT_DIR/session-manager.pid"
LOG_FILE="$SCRIPT_DIR/session-manager.log"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[BMN24-CTRL]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[BMN24-CTRL]${NC} $1"
}

print_error() {
    echo -e "${RED}[BMN24-CTRL]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[BMN24-CTRL]${NC} $1"
}

# Check if session manager is running
is_running() {
    if [ -f "$PID_FILE" ]; then
        local pid=$(cat "$PID_FILE")
        if ps -p $pid > /dev/null 2>&1; then
            return 0
        else
            # PID file exists but process is not running
            rm -f "$PID_FILE"
            return 1
        fi
    else
        return 1
    fi
}

# Start the session manager
start_session_manager() {
    print_status "Starting Baman24 Session Manager..."
    
    if is_running; then
        print_warning "Session manager is already running (PID: $(cat "$PID_FILE"))"
        return 1
    fi
    
    # Create log file if it doesn't exist
    touch "$LOG_FILE"
    
    # Start the session manager in background
    nohup node "$SESSION_MANAGER_SCRIPT" > "$LOG_FILE" 2>&1 &
    local pid=$!
    
    # Save PID to file
    echo $pid > "$PID_FILE"
    
    # Wait a moment and check if it's still running
    sleep 2
    if is_running; then
        print_success "Session manager started successfully (PID: $pid)"
        print_status "Log file: $LOG_FILE"
        print_status "Use './session-manager.sh logs' to view logs"
        return 0
    else
        print_error "Failed to start session manager"
        rm -f "$PID_FILE"
        return 1
    fi
}

# Stop the session manager
stop_session_manager() {
    print_status "Stopping Baman24 Session Manager..."
    
    if ! is_running; then
        print_warning "Session manager is not running"
        return 1
    fi
    
    local pid=$(cat "$PID_FILE")
    
    # Send SIGTERM for graceful shutdown
    kill -TERM $pid 2>/dev/null
    
    # Wait for graceful shutdown
    local count=0
    while [ $count -lt 10 ]; do
        if ! ps -p $pid > /dev/null 2>&1; then
            break
        fi
        sleep 1
        count=$((count + 1))
    done
    
    # Force kill if still running
    if ps -p $pid > /dev/null 2>&1; then
        print_warning "Graceful shutdown failed, forcing termination..."
        kill -KILL $pid 2>/dev/null
        sleep 1
    fi
    
    # Clean up PID file
    rm -f "$PID_FILE"
    
    print_success "Session manager stopped successfully"
    return 0
}

# Restart the session manager
restart_session_manager() {
    print_status "Restarting Baman24 Session Manager..."
    stop_session_manager
    sleep 2
    start_session_manager
}

# Show status
show_status() {
    print_status "Baman24 Session Manager Status:"
    echo "================================"
    
    if is_running; then
        local pid=$(cat "$PID_FILE")
        print_success "Status: RUNNING (PID: $pid)"
        
        # Show process details
        if command -v ps > /dev/null; then
            echo "Process details:"
            ps -p $pid -o pid,ppid,etime,cpu,rss,cmd 2>/dev/null | tail -n +2
        fi
        
        # Show log file info
        if [ -f "$LOG_FILE" ]; then
            echo ""
            echo "Log file: $LOG_FILE"
            echo "Log size: $(du -h "$LOG_FILE" | cut -f1)"
            echo "Last modified: $(stat -c %y "$LOG_FILE" 2>/dev/null || stat -f %Sm "$LOG_FILE" 2>/dev/null)"
        fi
        
        # Show recent log lines
        if [ -f "$LOG_FILE" ]; then
            echo ""
            echo "Recent log entries (last 5 lines):"
            echo "-----------------------------------"
            tail -n 5 "$LOG_FILE"
        fi
    else
        print_error "Status: NOT RUNNING"
        
        # Check for leftover files
        if [ -f "$PID_FILE" ]; then
            print_warning "Stale PID file found, removing: $PID_FILE"
            rm -f "$PID_FILE"
        fi
    fi
    
    echo ""
    echo "Files:"
    echo "  Script: $SESSION_MANAGER_SCRIPT"
    echo "  PID file: $PID_FILE"
    echo "  Log file: $LOG_FILE"
    echo "  Sessions dir: $SCRIPT_DIR/sessions/"
    
    # Show session files
    if [ -d "$SCRIPT_DIR/sessions" ]; then
        local session_count=$(ls -1 "$SCRIPT_DIR/sessions"/session-*.json 2>/dev/null | wc -l)
        echo "  Active session files: $session_count"
    fi
}

# Show logs
show_logs() {
    if [ ! -f "$LOG_FILE" ]; then
        print_error "Log file not found: $LOG_FILE"
        return 1
    fi
    
    print_status "Showing Baman24 Session Manager logs..."
    print_status "Press Ctrl+C to stop following logs"
    echo "================================================"
    
    # Follow logs
    tail -f "$LOG_FILE"
}

# Main script logic
case "$1" in
    start)
        start_session_manager
        ;;
    stop)
        stop_session_manager
        ;;
    restart)
        restart_session_manager
        ;;
    status)
        show_status
        ;;
    logs)
        show_logs
        ;;
    *)
        echo "Usage: $0 {start|stop|restart|status|logs}"
        echo ""
        echo "Commands:"
        echo "  start   - Start the session manager"
        echo "  stop    - Stop the session manager"
        echo "  restart - Restart the session manager"
        echo "  status  - Show current status"
        echo "  logs    - Follow log output"
        echo ""
        echo "Examples:"
        echo "  $0 start"
        echo "  $0 status"
        echo "  $0 logs"
        exit 1
        ;;
esac

exit $? 