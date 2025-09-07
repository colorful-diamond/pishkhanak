#!/bin/bash
"""
Persian Digit Captcha API - Gunicorn Startup Script
==================================================

Production startup script using gunicorn for better performance and stability.
"""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}🚀 Starting Persian Digit Captcha API with Gunicorn${NC}"
echo "=================================================="

# Check if gunicorn is installed
if ! command -v gunicorn &> /dev/null; then
    echo -e "${RED}❌ Error: gunicorn is not installed${NC}"
    echo -e "${YELLOW}💡 Install with: pip install gunicorn${NC}"
    exit 1
fi

# Check if model file exists
if [ ! -f "persian_digit.keras" ]; then
    echo -e "${RED}❌ Error: persian_digit.keras model file not found${NC}"
    echo -e "${YELLOW}💡 Make sure the model file is in the current directory${NC}"
    exit 1
fi

# Create logs directory
mkdir -p logs

# Set environment variables
export PYTHONPATH=".:$PYTHONPATH"
export FLASK_ENV=production
export LOG_LEVEL=INFO

echo -e "${GREEN}✅ Prerequisites checked${NC}"
echo -e "${BLUE}📁 Working directory: $(pwd)${NC}"
echo -e "${BLUE}🐍 Python path: $PYTHONPATH${NC}"
echo ""

# Start gunicorn with configuration
echo -e "${GREEN}🌟 Starting server...${NC}"
exec gunicorn --config gunicorn.conf.py captcha_api_production:app