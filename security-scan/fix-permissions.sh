#!/bin/bash

# Security: Fix File Permissions Script
# This script sets secure permissions for Laravel project files and directories

echo "═══════════════════════════════════════════════════════════"
echo "          Laravel Security: File Permissions Fix"
echo "═══════════════════════════════════════════════════════════"
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}⚠ WARNING: This script will modify file permissions${NC}"
echo "Make sure you have proper backups before proceeding."
echo ""
read -p "Do you want to continue? (yes/no): " confirm

if [ "$confirm" != "yes" ]; then
    echo "Operation cancelled."
    exit 0
fi

echo ""
echo -e "${GREEN}Starting permission fixes...${NC}"
echo ""

# Fix .env file permissions (should be readable only by owner)
echo "1. Securing .env file..."
if [ -f .env ]; then
    chmod 600 .env
    echo -e "${GREEN}✓${NC} .env file secured (600)"
else
    echo -e "${RED}✗${NC} .env file not found"
fi

# Fix .env.example permissions
if [ -f .env.example ]; then
    chmod 644 .env.example
    echo -e "${GREEN}✓${NC} .env.example secured (644)"
fi

echo ""
echo "2. Securing storage directory..."
# Storage directories should be writable by web server but not world-writable
find storage -type d -exec chmod 755 {} \;
find storage -type f -exec chmod 644 {} \;
echo -e "${GREEN}✓${NC} Storage directories set to 755"
echo -e "${GREEN}✓${NC} Storage files set to 644"

# Storage subdirectories that need write access
chmod -R 775 storage/app/public
chmod -R 775 storage/framework/cache
chmod -R 775 storage/framework/sessions
chmod -R 775 storage/framework/views
chmod -R 775 storage/logs
echo -e "${GREEN}✓${NC} Storage write directories set to 775"

echo ""
echo "3. Securing bootstrap/cache directory..."
chmod 755 bootstrap
chmod -R 775 bootstrap/cache
echo -e "${GREEN}✓${NC} Bootstrap cache secured"

echo ""
echo "4. Securing database directory..."
chmod 755 database
find database -type d -exec chmod 755 {} \;
find database -type f -exec chmod 644 {} \;
# Database file should not be world-readable if it contains sensitive data
if [ -f database/database.sqlite ]; then
    chmod 600 database/database.sqlite
    echo -e "${GREEN}✓${NC} SQLite database secured (600)"
fi
echo -e "${GREEN}✓${NC} Database directory secured"

echo ""
echo "5. Securing configuration files..."
chmod 755 config
find config -type f -exec chmod 644 {} \;
echo -e "${GREEN}✓${NC} Config files secured (644)"

echo ""
echo "6. Securing application code..."
# Application directories
for dir in app resources routes; do
    if [ -d "$dir" ]; then
        chmod 755 "$dir"
        find "$dir" -type d -exec chmod 755 {} \;
        find "$dir" -type f -exec chmod 644 {} \;
        echo -e "${GREEN}✓${NC} $dir directory secured"
    fi
done

echo ""
echo "7. Securing vendor directory..."
if [ -d vendor ]; then
    chmod 755 vendor
    find vendor -type d -exec chmod 755 {} \; 2>/dev/null
    find vendor -type f -exec chmod 644 {} \; 2>/dev/null
    echo -e "${GREEN}✓${NC} Vendor directory secured"
fi

echo ""
echo "8. Securing public directory..."
if [ -d public ]; then
    chmod 755 public
    find public -type d -exec chmod 755 {} \;
    find public -type f -exec chmod 644 {} \;
    # Index file needs to be readable
    chmod 644 public/index.php
    echo -e "${GREEN}✓${NC} Public directory secured"
fi

echo ""
echo "9. Setting artisan executable..."
if [ -f artisan ]; then
    chmod 755 artisan
    echo -e "${GREEN}✓${NC} Artisan command secured"
fi

echo ""
echo "10. Securing security scan directory..."
if [ -d security-scan ]; then
    chmod 700 security-scan
    find security-scan -type f -exec chmod 600 {} \;
    # Make scripts executable for owner only
    find security-scan -name "*.sh" -exec chmod 700 {} \;
    echo -e "${GREEN}✓${NC} Security scan directory secured"
fi

echo ""
echo "═══════════════════════════════════════════════════════════"
echo -e "${GREEN}✓ File permissions have been secured!${NC}"
echo "═══════════════════════════════════════════════════════════"
echo ""
echo "IMPORTANT NOTES:"
echo "1. If your web server runs as a different user (e.g., www-data),"
echo "   you may need to adjust group ownership:"
echo "   ${YELLOW}sudo chgrp -R www-data storage bootstrap/cache${NC}"
echo ""
echo "2. If you encounter permission errors, you may need to add"
echo "   your web server user to your group or vice versa"
echo ""
echo "3. Never set directories to 777 or files to 666 in production"
echo ""
echo "4. Consider using ACLs for more granular permission control"
echo "═══════════════════════════════════════════════════════════"