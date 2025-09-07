# 📚 Complete Google Vertex AI Setup Guide for Image Generation
## For pishkhanak.com - Step by Step Instructions

---

## 🎯 What You'll Achieve
By following this guide, you'll be able to:
- Generate images using Google's Imagen 4 (Ultra, Standard, Fast)
- Integrate image generation into your Laravel application
- Set up proper authentication and security

---

## 📋 Prerequisites Checklist
Before starting, ensure you have:
- [ ] Google Account (Gmail)
- [ ] Credit/Debit card for billing (you get $300 free credit)
- [ ] Access to your server via SSH or FTP
- [ ] Basic understanding of editing files

---

## 🚀 PART 1: Google Cloud Setup

### Step 1: Create Google Cloud Account
1. **Open your browser** and go to: https://console.cloud.google.com
2. **Sign in** with your Google account
3. **Accept terms** when prompted
4. You'll automatically receive **$300 free credit** valid for 90 days

### Step 2: Create Your Project
1. **Look at the top bar** where it says "Select a project"
2. **Click on it** and then click **"NEW PROJECT"**
3. **Fill in the details**:
   ```
   Project name: pishkhanak-ai
   Organization: Leave as is (No organization)
   ```
4. **Click "CREATE"** and wait 30 seconds
5. **IMPORTANT**: Note down your Project ID (it will look like: `pishkhanak-ai-426318`)
   
   ⚠️ **SAVE THIS PROJECT ID** - You'll need it later!

### Step 3: Set Up Billing
1. **Click the menu** (☰) in top-left corner
2. **Navigate to**: Billing
3. **Click**: "LINK A BILLING ACCOUNT"
4. **Click**: "CREATE BILLING ACCOUNT"
5. **Fill in your details**:
   - Country: Your country
   - Account type: Individual
   - Name and Address: Your details
6. **Add payment method**: Enter your credit/debit card
7. **Click**: "START MY FREE TRIAL"

### Step 4: Enable Required APIs
You need to enable 4 APIs. Here's how:

1. **Go to**: APIs & Services > Library (from the menu ☰)

2. **Search and enable each of these APIs**:
   
   **API 1: Vertex AI API**
   - Search for: "Vertex AI API"
   - Click on it
   - Click "ENABLE"
   - Wait for it to enable
   
   **API 2: Cloud Resource Manager API**
   - Go back to Library
   - Search for: "Cloud Resource Manager API"
   - Click on it
   - Click "ENABLE"
   
   **API 3: IAM Service Account Credentials API**
   - Go back to Library
   - Search for: "IAM Service Account Credentials API"
   - Click on it
   - Click "ENABLE"
   
   **API 4: Compute Engine API**
   - Go back to Library
   - Search for: "Compute Engine API"
   - Click on it
   - Click "ENABLE"

### Step 5: Create Service Account (For Authentication)
1. **Go to**: IAM & Admin > Service Accounts (from menu ☰)
2. **Click**: "+ CREATE SERVICE ACCOUNT"
3. **Fill in Step 1**:
   ```
   Service account name: vertex-ai-imagen
   Service account ID: vertex-ai-imagen (auto-fills)
   Description: Service account for AI image generation
   ```
4. **Click**: "CREATE AND CONTINUE"
5. **In Step 2 (Grant access)**, add these 3 roles:
   - Click "Select a role"
   - Search for "Vertex AI User" and select it
   - Click "ADD ANOTHER ROLE"
   - Search for "Storage Object Viewer" and select it
   - Click "ADD ANOTHER ROLE"
   - Search for "Service Account Token Creator" and select it
6. **Click**: "CONTINUE"
7. **Click**: "DONE" (skip step 3)

### Step 6: Download Authentication Key
1. **Find your service account** in the list (vertex-ai-imagen@...)
2. **Click on the email** to open it
3. **Click**: "KEYS" tab
4. **Click**: "ADD KEY" > "Create new key"
5. **Select**: JSON
6. **Click**: "CREATE"
7. **A file will download** - SAVE IT! (it's named something like `pishkhanak-ai-xxxx.json`)
8. **Rename the file** to: `vertex-ai-credentials.json`

---

## 🖥️ PART 2: Server Setup

### Step 7: Upload Credentials to Your Server

#### Option A: Using FTP Client (FileZilla, etc.)
1. Connect to your server
2. Navigate to: `/home/pishkhanak/htdocs/pishkhanak.com/storage/app/`
3. Upload the `vertex-ai-credentials.json` file

#### Option B: Using SSH
1. SSH into your server
2. Create the file:
   ```bash
   nano /home/pishkhanak/htdocs/pishkhanak.com/storage/app/vertex-ai-credentials.json
   ```
3. Paste the contents of your JSON file
4. Save (Ctrl+X, then Y, then Enter)

### Step 8: Set Correct Permissions
Run these commands via SSH:
```bash
cd /home/pishkhanak/htdocs/pishkhanak.com
chmod 600 storage/app/vertex-ai-credentials.json
chown pishkhanak:pishkhanak storage/app/vertex-ai-credentials.json
```

### Step 9: Update Your .env File
Edit your `.env` file:
```bash
nano /home/pishkhanak/htdocs/pishkhanak.com/.env
```

Find and update these lines (or add them if they don't exist):
```env
# Google Cloud Configuration
GOOGLE_CLOUD_PROJECT_ID=YOUR-ACTUAL-PROJECT-ID-HERE
VERTEX_AI_LOCATION=us-central1
GOOGLE_APPLICATION_CREDENTIALS=/home/pishkhanak/htdocs/pishkhanak.com/storage/app/vertex-ai-credentials.json

# Proxy Settings (if you use proxy)
VERTEX_AI_PROXY_ENABLED=true
VERTEX_AI_PROXY_URL=socks5://127.0.0.1:1080

# Image Model Selection
IMAGEN_MODEL=imagen-4.0-fast-generate-001
```

⚠️ **IMPORTANT**: Replace `YOUR-ACTUAL-PROJECT-ID-HERE` with your project ID from Step 2!

Example:
```env
GOOGLE_CLOUD_PROJECT_ID=pishkhanak-ai-426318
```

### Step 10: Clear Laravel Cache
Run these commands:
```bash
cd /home/pishkhanak/htdocs/pishkhanak.com
php artisan config:clear
php artisan cache:clear
```

---

## 🧪 PART 3: Testing

### Step 11: Test Your Setup
1. **SSH into your server**
2. **Run Laravel Tinker**:
   ```bash
   cd /home/pishkhanak/htdocs/pishkhanak.com
   php artisan tinker
   ```

3. **Test the service**:
   ```php
   // First, check if service loads
   $service = app(\App\Services\VertexAIImageService::class);
   echo "Service loaded successfully!";
   
   // Now try to generate a test image
   $result = $service->generateImage("A beautiful sunset over mountains");
   print_r($result);
   
   // Exit tinker
   exit
   ```

4. **Check for errors**:
   ```bash
   tail -50 storage/logs/laravel.log
   ```

---

## 🔧 PART 4: Troubleshooting

### Common Errors and Solutions

#### Error: "Permission denied" or "403 Forbidden"
**Solution**: Your service account needs more permissions
1. Go to IAM & Admin > IAM
2. Find your service account
3. Click Edit (pencil icon)
4. Add role: "Vertex AI Administrator"
5. Save

#### Error: "API not enabled"
**Solution**: Enable the API
1. Go to APIs & Services > Library
2. Search for "Vertex AI API"
3. Click ENABLE

#### Error: "Invalid credentials"
**Solution**: Check your JSON file
1. Verify the file exists:
   ```bash
   ls -la /home/pishkhanak/htdocs/pishkhanak.com/storage/app/vertex-ai-credentials.json
   ```
2. Check it's valid JSON:
   ```bash
   cat /home/pishkhanak/htdocs/pishkhanak.com/storage/app/vertex-ai-credentials.json | python3 -m json.tool
   ```

#### Error: "Billing not enabled"
**Solution**: 
1. Go to Billing in Google Cloud Console
2. Ensure your project is linked to a billing account
3. Check you have credits or valid payment method

#### Error: "Quota exceeded"
**Solution**:
1. Go to IAM & Admin > Quotas
2. Filter by "Vertex AI"
3. Request quota increase

---

## 💰 PART 5: Cost Management

### Understanding Costs
Imagen 4 pricing (as of 2025):
- **Ultra Quality**: ~$0.06 per image
- **Standard Quality**: ~$0.04 per image  
- **Fast Quality**: ~$0.02 per image

### Set Up Budget Alerts
1. **Go to**: Billing > Budgets & alerts
2. **Click**: "CREATE BUDGET"
3. **Set up**:
   - Name: "Vertex AI Budget"
   - Amount: $10 (or your preference)
   - Alert at: 50%, 90%, 100%
4. **Add your email** for notifications
5. **Click**: "FINISH"

---

## 🎮 PART 6: Using in Your Application

### Available Settings in Your Laravel App

You can use these parameters when generating images:

```php
$imageService->generateImage("your prompt", [
    'model' => 'imagen-4.0-ultra-generate-001',  // or 'standard' or 'fast'
    'aspectRatio' => '1:1',  // Options: '1:1', '16:9', '9:16', '4:3', '3:4'
    'sampleCount' => 2,  // Number of images (1-8)
    'negativePrompt' => 'blurry, low quality',  // What to avoid
    'safetyFilterLevel' => 'block_some',  // Safety level
    'addWatermark' => false,  // Add Google watermark
]);
```

### Model Options
- `imagen-4.0-ultra-generate-001` - Highest quality, slower
- `imagen-4.0-standard-generate-001` - Good quality, moderate speed
- `imagen-4.0-fast-generate-001` - Fast generation, good for testing

---

## 📱 PART 7: Quick Test in Browser

After setup, you can test in your AI Content Generator:
1. Go to your admin panel
2. Navigate to AI Content Generator
3. In image settings, you should see quality options
4. Try generating content with images

---

## 🔐 PART 8: Security Best Practices

### Important Security Steps
1. **Never share your JSON credentials file**
2. **Add to .gitignore**:
   ```bash
   echo "storage/app/*.json" >> .gitignore
   ```
3. **Regularly rotate keys** (every 90 days):
   - Create new key in Google Cloud Console
   - Upload new file to server
   - Delete old key from Google Cloud

---

## 📞 PART 9: Getting Help

### If you need help:
1. **Check logs**:
   ```bash
   tail -100 storage/logs/laravel.log | grep -i vertex
   ```

2. **Google Cloud Support**: https://cloud.google.com/support

3. **Community Forums**: https://www.googlecloudcommunity.com/

4. **Check service status**: https://status.cloud.google.com/

---

## ✅ Final Checklist

Make sure you've completed:
- [ ] Created Google Cloud account
- [ ] Created project and noted Project ID
- [ ] Enabled billing
- [ ] Enabled all 4 required APIs
- [ ] Created service account with proper roles
- [ ] Downloaded JSON credentials
- [ ] Uploaded credentials to server
- [ ] Updated .env with your Project ID
- [ ] Set correct file permissions
- [ ] Cleared Laravel cache
- [ ] Tested with tinker
- [ ] Set up budget alerts

---

## 🎉 Congratulations!

If everything is working, you now have:
- ✅ Google Vertex AI configured
- ✅ Image generation capability
- ✅ Secure authentication
- ✅ Cost monitoring

Your AI Content Generator can now create images using Google's latest Imagen 4 technology!

---

## 📝 Notes

- **Free Credits**: You have $300 for 90 days - enough for ~5000-15000 images
- **After Free Trial**: Set strict budgets to control costs
- **Best Practice**: Start with 'fast' model for testing, use 'ultra' for production
- **Rate Limits**: Default is 60 requests/minute (can be increased)

---

**Last Updated**: August 2025
**For**: pishkhanak.com
**Version**: 2.0