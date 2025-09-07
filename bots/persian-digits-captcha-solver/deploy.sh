#!/bin/bash
# Persian Digit Captcha API - Production Deployment Script
# ========================================================

set -e  # Exit on any error

echo "🚀 Persian Digit Captcha API - Production Deployment"
echo "====================================================="

# Check if Python 3 is available
if ! command -v python3 &> /dev/null; then
    echo "❌ Python 3 is required but not installed"
    exit 1
fi

# Check if model file exists
if [ ! -f "persian_digit.keras" ]; then
    echo "❌ Model file 'persian_digit.keras' not found"
    echo "   Please ensure the trained model is in the current directory"
    exit 1
fi

# Create virtual environment if it doesn't exist
if [ ! -d "venv_production" ]; then
    echo "📦 Creating production virtual environment..."
    python3 -m venv venv_production
fi

# Activate virtual environment
echo "🔄 Activating virtual environment..."
source venv_production/bin/activate

# Upgrade pip
echo "⬆️  Upgrading pip..."
pip install --upgrade pip

# Install production requirements
echo "📥 Installing production requirements..."
pip install -r requirements_production.txt

# Test model loading
echo "🧪 Testing model loading..."
python3 -c "
import keras
import os
if os.path.exists('persian_digit.keras'):
    try:
        model = keras.models.load_model('persian_digit.keras')
        print('✅ Model loaded successfully')
    except Exception as e:
        print(f'❌ Model loading failed: {e}')
        exit(1)
else:
    print('❌ Model file not found')
    exit(1)
"

# Test API server
echo "🧪 Testing API server startup..."
timeout 10s python3 captcha_api_production.py &
SERVER_PID=$!
sleep 5

# Check if server started
if kill -0 $SERVER_PID 2>/dev/null; then
    echo "✅ Server startup test successful"
    kill $SERVER_PID
    wait $SERVER_PID 2>/dev/null || true
else
    echo "❌ Server startup test failed"
    exit 1
fi

# Install PM2 if not already installed
echo "📦 Installing/updating PM2..."
if ! command -v pm2 &> /dev/null; then
    echo "   Installing PM2 globally..."
    sudo npm install -g pm2
else
    echo "   PM2 already installed"
fi

# Create logs directory
echo "📁 Creating logs directory..."
mkdir -p logs

echo ""
echo "✅ Deployment completed successfully!"
echo ""
echo "🚀 To start the production server:"
echo "   ./start_production.sh start"
echo "   # OR directly with PM2:"
echo "   pm2 start ecosystem.config.js --env production"
echo ""
echo "🔧 Environment variables are configured in ecosystem.config.js"
echo ""
echo "📊 Monitor with PM2:"
echo "   pm2 status           # Show process status"
echo "   pm2 logs captcha-api # Show logs"
echo "   pm2 monit           # Real-time monitoring"
echo ""
echo "🛑 To stop:"
echo "   ./start_production.sh stop"
echo "   # OR directly with PM2:"
echo "   pm2 stop captcha-api"
echo ""
echo "💡 Other useful PM2 commands:"
echo "   pm2 restart captcha-api  # Restart application"
echo "   pm2 reload captcha-api   # Zero-downtime reload"
echo "   pm2 delete captcha-api   # Remove from PM2"
echo ""
echo "====================================================="