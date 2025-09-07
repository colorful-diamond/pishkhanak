#!/bin/bash

# Restart Captcha API Script
# Kills existing captcha API and restarts with fixes

CAPTCHA_DIR="pishkhanak.com/bots/persian-digits-captcha-solver"
CAPTCHA_SCRIPT="captcha_api_production.py"

echo "🔄 Restarting Persian Captcha API..."
echo "=================================="

# Change to captcha directory
cd "$CAPTCHA_DIR" || {
    echo "❌ Error: Could not change to captcha directory: $CAPTCHA_DIR"
    exit 1
}

# Kill existing processes
echo "🛑 Stopping existing captcha API processes..."
pkill -f "$CAPTCHA_SCRIPT" 2>/dev/null || true
pkill -f "python.*captcha" 2>/dev/null || true
sleep 2

# Check if processes are still running
if pgrep -f "$CAPTCHA_SCRIPT" > /dev/null; then
    echo "⚠️  Force killing remaining processes..."
    pkill -9 -f "$CAPTCHA_SCRIPT" 2>/dev/null || true
    sleep 1
fi

# Check for required files
if [ ! -f "$CAPTCHA_SCRIPT" ]; then
    echo "❌ Error: $CAPTCHA_SCRIPT not found in $(pwd)"
    exit 1
fi

if [ ! -f "persian_digit.keras" ]; then
    echo "❌ Error: persian_digit.keras model file not found"
    echo "💡 Make sure the trained model file is in the captcha directory"
    exit 1
fi

if [ ! -f "captcha_processor.py" ]; then
    echo "❌ Error: captcha_processor.py not found"
    exit 1
fi

# Check Python dependencies
echo "🔍 Checking Python dependencies..."
python3 -c "import flask, cv2, keras, numpy, PIL, tensorflow" 2>/dev/null || {
    echo "❌ Error: Missing Python dependencies"
    echo "💡 Install required packages:"
    echo "   pip install flask opencv-python keras numpy pillow tensorflow"
    exit 1
}

# Start the captcha API
echo "🚀 Starting captcha API with fixes..."
echo "📍 Directory: $(pwd)"
echo "🌐 URL: http://127.0.0.1:9090"
echo ""

# Run in background and capture PID
nohup python3 "$CAPTCHA_SCRIPT" > captcha_api.log 2>&1 &
CAPTCHA_PID=$!

# Wait a moment for startup
sleep 3

# Check if process is running
if kill -0 $CAPTCHA_PID 2>/dev/null; then
    echo "✅ Captcha API started successfully!"
    echo "📋 Process ID: $CAPTCHA_PID"
    echo "📝 Log file: $(pwd)/captcha_api.log"
    
    # Test API endpoint
    echo ""
    echo "🧪 Testing API endpoint..."
    if curl -s -X GET "http://127.0.0.1:9090/health" > /dev/null 2>&1; then
        echo "✅ API health check passed"
        
        # Show health response
        echo ""
        echo "📊 API Health Status:"
        curl -s -X GET "http://127.0.0.1:9090/health" | python3 -m json.tool 2>/dev/null || echo "Could not format JSON response"
    else
        echo "⚠️  API health check failed - may still be starting up"
        echo "💡 Check the log file for details: tail -f captcha_api.log"
    fi
    
    echo ""
    echo "🎯 Ready to test NICS24 provider!"
    echo "📋 To monitor logs: tail -f $(pwd)/captcha_api.log"
    echo "🛑 To stop: kill $CAPTCHA_PID"
    
else
    echo "❌ Failed to start captcha API"
    echo "📝 Check log file for errors: $(pwd)/captcha_api.log"
    if [ -f "captcha_api.log" ]; then
        echo ""
        echo "🔍 Last 10 lines of log:"
        tail -10 "captcha_api.log"
    fi
    exit 1
fi