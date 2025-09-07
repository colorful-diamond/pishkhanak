#!/bin/bash

# PM2 Management Script for Pishkhanak Local API Server
# This script provides easy management of the Node.js local API server using PM2

# Colors for output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Project paths
PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
API_DIR="$PROJECT_ROOT/bots/inquiry-provider"
ECOSYSTEM_FILE="$API_DIR/ecosystem.config.js"
APP_NAME="pishkhanak-local-api"

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

# Check if PM2 is installed
check_pm2() {
    if ! command -v pm2 &> /dev/null; then
        print_error "PM2 is not installed. Installing PM2 globally..."
        npm install -g pm2
        if [ $? -eq 0 ]; then
            print_success "PM2 installed successfully"
        else
            print_error "Failed to install PM2. Please install it manually: npm install -g pm2"
            exit 1
        fi
    fi
}

# Check Node.js version
check_node() {
    if ! command -v node &> /dev/null; then
        print_error "Node.js is not installed. Please install Node.js 18+ first."
        exit 1
    fi
    
    NODE_VERSION=$(node --version | cut -d'v' -f2 | cut -d'.' -f1)
    if [ "$NODE_VERSION" -lt 18 ]; then
        print_warning "Node.js version is $NODE_VERSION. Recommended version is 18 or higher."
    fi
}

# Install dependencies
install_deps() {
    print_status "Installing dependencies..."
    cd "$API_DIR"
    
    if [ ! -f "package.json" ]; then
        print_error "package.json not found in $API_DIR"
        exit 1
    fi
    
    npm install
    if [ $? -eq 0 ]; then
        print_success "Dependencies installed successfully"
    else
        print_error "Failed to install dependencies"
        exit 1
    fi
}

# Check environment file
check_env() {
    if [ ! -f "$API_DIR/.env" ]; then
        print_warning ".env file not found. Creating from .env.example..."
        if [ -f "$API_DIR/.env.example" ]; then
            cp "$API_DIR/.env.example" "$API_DIR/.env"
            print_success ".env file created. Please add your GEMINI_API_KEY."
        else
            print_warning "No .env.example found. Please create .env file manually."
        fi
    fi
}

# Start the application
start_app() {
    print_status "Starting Pishkhanak Local API Server with PM2..."
    
    cd "$API_DIR"
    
    # Check if app is already running
    if pm2 describe "$APP_NAME" &> /dev/null; then
        print_warning "Application is already running. Use 'restart' to restart it."
        show_status
        return
    fi
    
    # Start with ecosystem file
    pm2 start "$ECOSYSTEM_FILE" --env production
    
    if [ $? -eq 0 ]; then
        print_success "Application started successfully"
        sleep 2
        show_status
        show_logs_info
    else
        print_error "Failed to start application"
        exit 1
    fi
}

# Stop the application
stop_app() {
    print_status "Stopping Pishkhanak Local API Server..."
    
    pm2 stop "$APP_NAME"
    
    if [ $? -eq 0 ]; then
        print_success "Application stopped successfully"
    else
        print_error "Failed to stop application"
    fi
}

# Restart the application
restart_app() {
    print_status "Restarting Pishkhanak Local API Server..."
    
    pm2 restart "$APP_NAME"
    
    if [ $? -eq 0 ]; then
        print_success "Application restarted successfully"
        sleep 2
        show_status
    else
        print_error "Failed to restart application"
    fi
}

# Reload the application (graceful restart)
reload_app() {
    print_status "Reloading Pishkhanak Local API Server..."
    
    pm2 reload "$APP_NAME"
    
    if [ $? -eq 0 ]; then
        print_success "Application reloaded successfully"
        sleep 2
        show_status
    else
        print_error "Failed to reload application"
    fi
}

# Delete the application from PM2
delete_app() {
    print_status "Deleting Pishkhanak Local API Server from PM2..."
    
    pm2 delete "$APP_NAME"
    
    if [ $? -eq 0 ]; then
        print_success "Application deleted from PM2"
    else
        print_error "Failed to delete application"
    fi
}

# Show application status
show_status() {
    print_status "Application Status:"
    pm2 describe "$APP_NAME"
    echo ""
    pm2 list
}

# Show real-time logs
show_logs() {
    print_status "Showing real-time logs (Press Ctrl+C to exit)..."
    pm2 logs "$APP_NAME" --lines 50
}

# Show logs info
show_logs_info() {
    echo ""
    print_status "📋 Log Files:"
    echo "  📄 Combined: $API_DIR/logs/combined.log"
    echo "  📤 Output:   $API_DIR/logs/out.log" 
    echo "  ❌ Error:    $API_DIR/logs/error.log"
    echo ""
    print_status "💡 Useful Commands:"
    echo "  pm2 logs $APP_NAME           # Show real-time logs"
    echo "  pm2 logs $APP_NAME --lines 100 # Show last 100 lines"
    echo "  pm2 monit                    # Monitor dashboard"
    echo "  pm2 describe $APP_NAME       # Detailed app info"
}

# Monitor the application
monitor_app() {
    print_status "Opening PM2 monitoring dashboard..."
    pm2 monit
}

# Save PM2 process list
save_pm2() {
    print_status "Saving PM2 process list..."
    pm2 save
    if [ $? -eq 0 ]; then
        print_success "PM2 process list saved"
    fi
}

# Setup PM2 startup
setup_startup() {
    print_status "Setting up PM2 startup script..."
    pm2 startup
    print_warning "Please run the command shown above to complete startup setup"
}

# Show health check
health_check() {
    print_status "Performing health check..."
    
    # Check if process is running
    if ! pm2 describe "$APP_NAME" &> /dev/null; then
        print_error "Application is not running"
        return 1
    fi
    
    # Check if port is accessible
    if command -v curl &> /dev/null; then
        response=$(curl -s -o /dev/null -w "%{http_code}" http://127.0.0.1:9999/health)
        if [ "$response" = "200" ]; then
            print_success "Health check passed - Server is responding on port 9999"
        else
            print_error "Health check failed - Server not responding (HTTP: $response)"
        fi
    else
        print_warning "curl not available, skipping HTTP health check"
    fi
    
    show_status
}

# Show help
show_help() {
    echo ""
    echo "🚀 Pishkhanak Local API Server - PM2 Management Script"
    echo ""
    echo "Usage: $0 [COMMAND]"
    echo ""
    echo "Commands:"
    echo "  start      Start the application with PM2"
    echo "  stop       Stop the application"
    echo "  restart    Restart the application"
    echo "  reload     Graceful reload (zero-downtime)"
    echo "  delete     Remove application from PM2"
    echo "  status     Show application status"
    echo "  logs       Show real-time logs"
    echo "  monitor    Open PM2 monitoring dashboard"
    echo "  health     Perform health check"
    echo "  setup      Install dependencies and setup environment"
    echo "  save       Save PM2 process list"
    echo "  startup    Setup PM2 startup script"
    echo "  help       Show this help message"
    echo ""
    echo "Examples:"
    echo "  $0 setup      # First time setup"
    echo "  $0 start      # Start the server"
    echo "  $0 health     # Check if server is working"
    echo "  $0 logs       # View logs in real-time"
    echo ""
}

# Full setup process
full_setup() {
    print_status "🚀 Setting up Pishkhanak Local API Server..."
    echo ""
    
    check_node
    check_pm2
    install_deps
    check_env
    
    print_success "✅ Setup completed successfully!"
    echo ""
    print_status "Next steps:"
    echo "  1. Add your GEMINI_API_KEY to $API_DIR/.env"
    echo "  2. Run: $0 start"
    echo "  3. Test: $0 health"
    echo ""
}

# Main script logic
case "${1:-help}" in
    start)
        check_pm2
        start_app
        ;;
    stop)
        check_pm2
        stop_app
        ;;
    restart)
        check_pm2
        restart_app
        ;;
    reload)
        check_pm2
        reload_app
        ;;
    delete)
        check_pm2
        delete_app
        ;;
    status)
        check_pm2
        show_status
        ;;
    logs)
        check_pm2
        show_logs
        ;;
    monitor)
        check_pm2
        monitor_app
        ;;
    health)
        check_pm2
        health_check
        ;;
    setup)
        full_setup
        ;;
    save)
        check_pm2
        save_pm2
        ;;
    startup)
        check_pm2
        setup_startup
        ;;
    help|--help|-h)
        show_help
        ;;
    *)
        print_error "Unknown command: $1"
        show_help
        exit 1
        ;;
esac 