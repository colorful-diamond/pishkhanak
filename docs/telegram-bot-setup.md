# Telegram Bot Setup Guide

## Overview
This guide explains how to set up and configure the Telegram bot for sending payment notifications.

## Configuration

Add these lines to your `.env` file:

```env
# Telegram Bot Configuration
TELEGRAM_BOT_TOKEN=7696804096:AAFUuZaXsoLDYIb8KJ7w-eLlhq4D422C1oc
TELEGRAM_BOT_USERNAME=pishkhanak_bot
TELEGRAM_CHANNEL_ID=-1003097450288

# Telegram Proxy Configuration (Required for Iran)
TELEGRAM_PROXY_ENABLED=true
TELEGRAM_PROXY_TYPE=socks5
TELEGRAM_PROXY_HOST=127.0.0.1
TELEGRAM_PROXY_PORT=1080
```

## Testing

### 1. Test Connection
```bash
php artisan telegram:test --type=connection
```

### 2. Test Notification
```bash
# With mock data
php artisan telegram:test --type=notification

# With real transaction
php artisan telegram:test --type=notification --transaction=12345
```

### 3. Test via Queue
```bash
php artisan telegram:test --type=job --transaction=12345
```

### 4. Direct PHP Test
```bash
php telegram-test.php
```

## Queue Configuration

Make sure you have a queue worker running for the `notifications` queue:

```bash
php artisan queue:work --queue=notifications
```

## Features

The Telegram bot sends beautiful notifications with:
- 🎉 Eye-catching emojis
- 📋 Complete order details
- 👤 User information
- 💰 Financial breakdown
- 🎯 Service details (if applicable)
- 🔗 Quick action buttons

## Troubleshooting

1. **Connection Failed**: Check proxy settings and ensure SOCKS5 proxy is running on port 1080
2. **Permission Denied**: Make sure the bot is added as admin to the channel
3. **Queue Not Processing**: Ensure queue worker is running

## Message Format

Each notification includes:
- Transaction ID and date
- Payment gateway used
- Transaction type
- User details (name, email, mobile)
- Financial details (amount, fees, total)
- Service information (if applicable)
- Action buttons for quick access to admin panel