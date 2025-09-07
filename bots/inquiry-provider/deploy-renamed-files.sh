#!/bin/bash

# Deployment script for renamed camelCase files
# Usage: ./deploy-renamed-files.sh

SERVER_HOST="109.206.254.170"
SERVER_USER="pishkhanak"
SERVER_PASSWORD="UYzHsGYgMN7tnOdUPuOg"
SERVER_PATH="~/htdocs/pishkhanak.com/bots/inquiry-provider"

echo "🚀 Starting deployment of renamed camelCase files..."

# Function to upload file with scp
upload_file() {
    local local_path=$1
    local remote_path=$2
    echo "📤 Uploading $local_path to $remote_path"
    sshpass -p "$SERVER_PASSWORD" scp -P 22 -o StrictHostKeyChecking=no "$local_path" "$SERVER_USER@$SERVER_HOST:$remote_path"
}

# Function to execute commands on remote server
remote_exec() {
    local command=$1
    echo "🔧 Executing: $command"
    sshpass -p "$SERVER_PASSWORD" ssh -o StrictHostKeyChecking=no -p 22 "$SERVER_USER@$SERVER_HOST" "$command"
}

echo "🧹 Cleaning up old files on server..."

# Clean up old files on server
remote_exec "cd $SERVER_PATH && rm -f test-request-locks.js local-api-server.js check-proxy.js"
remote_exec "cd $SERVER_PATH/services && rm -f request-lock-manager.js redis-updater.js credit-score-rating.js && rm -rf credit-score-rating/"
remote_exec "cd $SERVER_PATH/providers/rade && rm -f check-login.js"

echo "📤 Uploading new camelCase files..."

# Upload main files
upload_file "localApiServer.js" "$SERVER_PATH/"
upload_file "testRequestLocks.js" "$SERVER_PATH/"
upload_file "testServiceMapping.js" "$SERVER_PATH/"
upload_file "testProviderPaths.js" "$SERVER_PATH/"
upload_file "testTraceIdExtraction.js" "$SERVER_PATH/"
upload_file "checkProxy.js" "$SERVER_PATH/"
upload_file "package.json" "$SERVER_PATH/"
upload_file "ecosystem.config.mjs" "$SERVER_PATH/"
upload_file "README_REQUEST_LOCKS.md" "$SERVER_PATH/"
upload_file "RENAME_SUMMARY.md" "$SERVER_PATH/"
upload_file "PATH_FIXES_SUMMARY.md" "$SERVER_PATH/"
upload_file "SERVICE_MAPPING_FIX.md" "$SERVER_PATH/"

# Upload services files
upload_file "services/requestLockManager.js" "$SERVER_PATH/services/"
upload_file "services/redisUpdater.js" "$SERVER_PATH/services/"

# Upload services directory (creditScoreRating)
echo "📁 Uploading creditScoreRating directory..."
sshpass -p "$SERVER_PASSWORD" scp -rP 22 -o StrictHostKeyChecking=no "services/creditScoreRating/" "$SERVER_USER@$SERVER_HOST:$SERVER_PATH/services/"

# Upload providers files
upload_file "providers/rade/checkLogin.js" "$SERVER_PATH/providers/rade/"

echo "🔄 Restarting PM2 service..."

# Restart PM2 service
remote_exec "cd $SERVER_PATH && pm2 stop pishkhanak-local-api || true"
remote_exec "cd $SERVER_PATH && pm2 delete pishkhanak-local-api || true"
remote_exec "cd $SERVER_PATH && pm2 start ecosystem.config.mjs --env production"

echo "✅ Checking service status..."

# Check service status
remote_exec "pm2 status"
remote_exec "pm2 logs pishkhanak-local-api --lines 10"

echo "🎉 Deployment completed!"
echo "📋 To check logs: ssh $SERVER_USER@$SERVER_HOST 'pm2 logs pishkhanak-local-api'"
echo "🔍 To check status: ssh $SERVER_USER@$SERVER_HOST 'pm2 status'" 