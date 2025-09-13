# Telegram Bot Setup Guide

## Problem
Your server cannot connect directly to Telegram API (connection timeout), which prevents the webhook from working properly.

## Solution: Local Polling Bot

I've created a Node.js bot that you can run on your local machine or any server with unrestricted internet access.

### Option 1: Run on Your Local Machine

1. **Install Node.js** (if not already installed)
   ```bash
   # On Windows: Download from nodejs.org
   # On Mac: brew install node
   # On Ubuntu: sudo apt install nodejs
   ```

2. **Get your bot token**
   - Find your `TELEGRAM_BOT_TOKEN` in your `.env` file

3. **Run the bot**
   ```bash
   # Navigate to project directory
   cd /path/to/pishkhanak.com
   
   # Set the bot token
   export TELEGRAM_BOT_TOKEN="your_bot_token_here"
   
   # Run the bot
   node telegram-bot-local.js
   ```

4. **Test it**
   - Open Telegram
   - Send `/start` to your bot
   - You should see the welcome message!

### Option 2: Run on Server with PM2

If you have another server with unrestricted internet:

```bash
# Install PM2 globally
npm install -g pm2

# Start the bot with PM2
TELEGRAM_BOT_TOKEN="your_token" pm2 start telegram-bot-local.js --name telegram-bot

# Save PM2 configuration
pm2 save
pm2 startup
```

### Option 3: Use a VPS or Cloud Service

You can run this bot on:
- A small VPS (DigitalOcean, Linode, etc.)
- Heroku free tier
- Google Cloud Run
- Any server outside Iran with internet access

### How It Works

The bot:
1. Uses **long polling** to fetch updates from Telegram
2. Processes commands locally (`/start`, `/help`, `/status`, etc.)
3. Sends responses directly back to users
4. Works around network restrictions

### Available Commands

- `/start` - Welcome message
- `/help` - Show help
- `/status` - Show service status  
- `/ticket` - Create support ticket
- `/admin` - Admin panel info

### Troubleshooting

**Bot not responding?**
- Check your bot token is correct
- Make sure Node.js is installed (`node --version`)
- Check for error messages in console

**Connection errors?**
- Try running from a different network
- Use a VPN if necessary
- Consider using a cloud service

### Making Changes

To modify bot responses, edit the `telegram-bot-local.js` file:
- Find the `switch (command)` section
- Modify the response text for each command
- Save and restart the bot

### Production Deployment

For production, consider:
1. Using environment variables for configuration
2. Setting up proper logging
3. Adding error recovery
4. Using a process manager (PM2, systemd)
5. Setting up monitoring

## Alternative: Fix Server Connection

If you want to fix the server connection instead:

1. **Check if a proxy is needed**
   - Many servers in Iran need a proxy for Telegram API
   - Configure proxy in your application

2. **Use Cloudflare Workers**
   - Create a worker that forwards requests to Telegram
   - Point your webhook to the worker URL

3. **Use a reverse proxy**
   - Set up a server outside restrictions
   - Forward webhook calls through it

---

**Note:** The local polling solution is the quickest way to get your bot working. Once running, you can explore other options for production deployment.