<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
        'youtube_api_key' => env('GOOGLE_YOUTUBE_API_KEY'),
    ],

    'github' => [
        'client_id' => env('GITHUB_CLIENT_ID'),
        'client_secret' => env('GITHUB_CLIENT_SECRET'),
        'redirect' => env('GITHUB_REDIRECT_URI'),
    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect' => env('FACEBOOK_REDIRECT_URI'),
    ],

    'discord' => [    
        'client_id' => env('DISCORD_CLIENT_ID'),  
        'client_secret' => env('DISCORD_CLIENT_SECRET'),  
        'redirect' => env('DISCORD_REDIRECT_URI'),
        
        // optional
        'allow_gif_avatars' => (bool)env('DISCORD_AVATAR_GIF', true),
        'avatar_default_extension' => env('DISCORD_EXTENSION_DEFAULT', 'png'), // only pick from jpg, png, webp
    ],

    'rapidapi' => [
        'key' => env('RAPIDAPI_KEY'),
    ],

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
    ],

    'openrouter' => [
        'api_key' => env('OPENROUTER_API_KEY'),
    ],

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY1') ?? env('GEMINI_API_KEY2') ?? env('GEMINI_API_KEY3') ?? env('GEMINI_API_KEY'),
    ],  

    'finnotech' => [
        'base_url' => env('FINNOTECH_BASE_URL', 'https://api.finnotech.ir'),
        'sandbox_url' => env('FINNOTECH_SANDBOX_URL', 'https://sandboxapi.finnotech.ir'),
        'client_id' => env('FINNOTECH_CLIENT_ID', 'pishkhanak'),
        'client_secret' => env('FINNOTECH_CLIENT_SECRET', 'EB9Kx6Z5FUiWgiD1N9z9'),
        'token' => env('FINNOTECH_TOKEN'),
        'sms_sender' => env('FINNOTECH_SMS_SENDER', '50004001'),
        'sandbox' => env('FINNOTECH_SANDBOX', false),
        
        // SMS Authorization configuration
        'sms_auth' => [
            'redirect_uri' => env('FINNOTECH_SMS_REDIRECT_URI', 'https://pishkhanak.com/api/finnotech/sms-auth/callback'),
            'token_expiry_minutes' => env('FINNOTECH_SMS_TOKEN_EXPIRY', 60), // Default 1 hour
            'cleanup_schedule' => env('FINNOTECH_SMS_CLEANUP_SCHEDULE', '03:30'), // Daily at 3:30 AM
        ],
    ],

    'jibit' => [
        'base_url' => env('JIBIT_BASE_URL', 'https://napi.jibit.ir/ide'),
        'api_key' => env('JIBIT_API_KEY'),
        'secret_key' => env('JIBIT_SECRET_KEY'),
        'access_token' => env('JIBIT_ACCESS_TOKEN'),
        'webhook_secret' => env('JIBIT_WEBHOOK_SECRET'),
        'sandbox' => env('JIBIT_SANDBOX', true),
        
        // PPG (Payment Gateway) configuration
        'ppg' => [
            'api_key' => env('JIBIT_PPG_API_KEY'),
            'api_secret' => env('JIBIT_PPG_API_SECRET'),
            'sandbox' => env('JIBIT_PPG_SANDBOX', true),
        ],
    ],

    'local_api' => [
        'url' => env('LOCAL_API_URL', 'http://127.0.0.1:9999'),
        'timeout' => env('LOCAL_API_TIMEOUT', 180),
        'retries' => env('LOCAL_API_RETRIES', 2),
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Gateway Services
    |--------------------------------------------------------------------------
    |
    | Configuration for payment gateway providers
    |
    */

    'sepehr' => [
        'terminal_id' => env('SEPEHR_TERMINAL_ID'),
        'sandbox' => env('SEPEHR_SANDBOX', true),
        'get_method' => env('SEPEHR_GET_METHOD', false),
        'rollback_enabled' => env('SEPEHR_ROLLBACK_ENABLED', false),
        'api_version' => env('SEPEHR_API_VERSION', 'v3.0.6'),
        'timeout' => env('SEPEHR_TIMEOUT', 30),
        'retry_attempts' => env('SEPEHR_RETRY_ATTEMPTS', 3),
        'base_url' => 'https://sepehr.shaparak.ir:8081',
        'gateway_url' => 'https://sepehr.shaparak.ir:8080',
    ],

    'asanpardakht' => [
        'merchant_id' => env('ASANPARDAKHT_MERCHANT_ID'),
        'username' => env('ASANPARDAKHT_USERNAME'),
        'password' => env('ASANPARDAKHT_PASSWORD'),
        'sandbox' => env('ASANPARDAKHT_SANDBOX', true),
        'timeout' => env('ASANPARDAKHT_TIMEOUT', 30),
        'retry_attempts' => env('ASANPARDAKHT_RETRY_ATTEMPTS', 3),
    ],

    /*
    |--------------------------------------------------------------------------
    | Google Cloud Services
    |--------------------------------------------------------------------------
    |
    | Configuration for Google Cloud Platform services
    |
    */

    'google_cloud' => [
        'api_key' => env('GOOGLE_CLOUD_API_KEY'),
        'project_id' => env('GOOGLE_CLOUD_PROJECT_ID'),
        'location' => env('GOOGLE_CLOUD_LOCATION', 'us-central1'),
        'service_account_key' => env('GOOGLE_CLOUD_SERVICE_ACCOUNT_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Telegram Bot Services
    |--------------------------------------------------------------------------
    |
    | Configuration for Telegram bot and notifications
    |
    */

    'telegram' => [
        'bot_token' => env('TELEGRAM_BOT_TOKEN'),
        'bot_username' => env('TELEGRAM_BOT_USERNAME'),
        'channel_id' => env('TELEGRAM_CHANNEL_ID'),
        'webhook_secret' => env('TELEGRAM_WEBHOOK_SECRET'),
        'proxy' => [
            'enabled' => env('TELEGRAM_PROXY_ENABLED', true),
            'type' => env('TELEGRAM_PROXY_TYPE', 'socks5'),
            'host' => env('TELEGRAM_PROXY_HOST', '127.0.0.1'),
            'port' => env('TELEGRAM_PROXY_PORT', 1080),
        ],
    ],

];
