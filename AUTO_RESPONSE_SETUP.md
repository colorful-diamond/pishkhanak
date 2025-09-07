# Auto-Response System Setup Guide

## Overview
I've successfully implemented an AI-powered auto-response system for your ticketing system using Google Gemini. This system automatically analyzes incoming tickets and provides relevant responses based on predefined contexts and responses.

## What's Been Created

### 1. Database Structure
- **auto_response_contexts** - Stores different categories/contexts for auto-responses
- **auto_responses** - Stores the actual response templates for each context
- **auto_response_logs** - Tracks usage and effectiveness of auto-responses

### 2. Models
- `AutoResponseContext` - Manages response categories
- `AutoResponse` - Manages individual response templates  
- `AutoResponseLog` - Tracks auto-response usage

### 3. Service Layer
- `GeminiAutoResponseService` - Core service that handles AI analysis and response matching
- Integration with ticket creation flow

### 4. Admin Panel (Filament)
- **Auto-Response Contexts** - Manage different categories/contexts
- **Auto-Responses** - Create and manage response templates

## Setup Instructions

### 1. Environment Configuration
Add these to your `.env` file:

```env
# Gemini API Configuration
GEMINI_API_KEY=your-gemini-api-key-here
GEMINI_MODEL=gemini-1.5-flash
GEMINI_TEMPERATURE=0.3
GEMINI_MAX_TOKENS=1000

# Auto-Response Configuration
AUTO_RESPONSE_MIN_CONFIDENCE=0.7
AUTO_RESPONSE_ENABLED=true
```

### 2. Install Dependencies
Connect to your server and run:

```bash
cd /home/pishkhanak/htdocs/pishkhanak.com
composer update
```

### 3. Run Migrations
```bash
php artisan migrate
```

### 4. Clear Caches
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

## How It Works

### For Users
1. User creates a ticket through the normal ticketing interface
2. The system automatically analyzes the ticket content using Gemini AI
3. If a matching context is found with sufficient confidence:
   - An auto-response is sent immediately
   - The ticket is marked as auto-responded
   - The user sees a success message indicating auto-response was sent
4. If no match is found, the ticket goes to the support team as normal

### For Admins

#### Creating Auto-Response Contexts
1. Go to the admin panel → پشتیبانی → زمینه‌های پاسخ خودکار
2. Click "ایجاد زمینه پاسخ خودکار جدید"
3. Fill in:
   - **نام زمینه**: A descriptive name for this context
   - **توضیحات**: Detailed description of what this context covers
   - **کلمات کلیدی**: Comma-separated keywords (e.g., "رمز عبور, فراموشی رمز, بازیابی رمز")
   - **نمونه سوالات**: Example queries users might ask (one per line)
   - **حد اطمینان**: Minimum confidence score (0-1) for matching

#### Creating Auto-Responses
1. Go to the admin panel → پشتیبانی → پاسخ‌های خودکار
2. Click "ایجاد پاسخ خودکار جدید"
3. Fill in:
   - **زمینه**: Select the context this response belongs to
   - **عنوان پاسخ**: A title for this response
   - **زبان**: Language (Persian/English)
   - **متن پاسخ**: The actual response text (supports variables like {{user_name}})
   - **فایل‌های پیوست**: Optional file attachments
   - **لینک‌های مفید**: Optional helpful links
   - **علامت‌گذاری به عنوان حل شده**: Whether to mark ticket as resolved

## Example Usage

### Example Context: Password Reset
- **Name**: بازیابی رمز عبور
- **Keywords**: رمز عبور, فراموشی رمز, بازیابی رمز, تغییر رمز, password, forgot
- **Example Queries**:
  - رمز عبورم را فراموش کرده‌ام
  - چطور می‌توانم رمز عبورم را تغییر دهم؟
  - نمی‌توانم وارد حسابم شوم

### Example Response:
```
سلام {{user_name}} عزیز،

برای بازیابی رمز عبور خود، لطفا مراحل زیر را دنبال کنید:

1. به صفحه ورود مراجعه کنید
2. روی لینک "رمز عبور خود را فراموش کرده‌اید؟" کلیک کنید
3. ایمیل خود را وارد کنید
4. ایمیل ارسال شده را بررسی و روی لینک بازیابی کلیک کنید
5. رمز عبور جدید خود را تنظیم کنید

در صورت عدم دریافت ایمیل، پوشه spam را بررسی کنید.

با تشکر،
تیم پشتیبانی پیشخوانک
```

## Features

### 1. AI-Powered Context Matching
- Uses Google Gemini to understand user intent
- Analyzes keywords, sentiment, and language
- Provides confidence scores for matches

### 2. Multi-Language Support
- Supports Persian and English responses
- Automatically detects user language
- Falls back to Persian if no response in detected language

### 3. Performance Tracking
- Tracks usage count for each response
- Measures customer satisfaction
- Shows effectiveness percentage
- Logs all auto-response attempts

### 4. Flexible Configuration
- Set confidence thresholds per context
- Enable/disable individual responses
- Priority-based context checking
- Mark tickets as resolved automatically

### 5. Admin Features
- Preview responses before saving
- Bulk activate/deactivate responses
- Filter by context, language, usage
- View detailed statistics

## Monitoring & Optimization

### Key Metrics to Track
1. **Response Usage**: Which responses are used most
2. **Effectiveness**: Percentage of helpful responses
3. **Confidence Scores**: Average confidence for matches
4. **Escalation Rate**: How often tickets need human support

### Optimization Tips
1. Regularly review auto-response logs
2. Update keywords based on actual user queries
3. Improve responses with low satisfaction scores
4. Add new contexts for frequently asked questions
5. Adjust confidence thresholds based on accuracy

## Troubleshooting

### If Auto-Response Isn't Working
1. Check if `AUTO_RESPONSE_ENABLED=true` in `.env`
2. Verify Gemini API key is set correctly
3. Check Laravel logs for errors
4. Ensure contexts have active responses
5. Verify confidence thresholds aren't too high

### Common Issues
- **No matches found**: Add more keywords or example queries
- **Wrong matches**: Increase confidence threshold
- **API errors**: Check Gemini API quota and key

## Security Considerations
1. Keep your Gemini API key secure
2. Regularly review auto-responses for accuracy
3. Monitor for potential abuse
4. Set appropriate rate limits if needed

## Next Steps
1. Get a Google Gemini API key from: https://makersuite.google.com/app/apikey
2. Add the API key to your `.env` file
3. Run the setup commands on your server
4. Create your first contexts and responses
5. Test with sample tickets
6. Monitor and optimize based on usage

The system is now ready to use once you complete the setup steps!
