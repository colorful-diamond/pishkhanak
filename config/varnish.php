<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Varnish Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration settings for Varnish cache integration with Laravel
    |
    */

    'enabled' => env('VARNISH_ENABLED', false),

    'host' => env('VARNISH_HOST', '127.0.0.1'),
    
    'port' => env('VARNISH_PORT', 6081),
    
    'admin_port' => env('VARNISH_ADMIN_PORT', 6082),

    /*
    |--------------------------------------------------------------------------
    | Cache TTL Settings (in seconds)
    |--------------------------------------------------------------------------
    */
    'ttl' => [
        'homepage' => env('VARNISH_TTL_HOMEPAGE', 300),         // 5 minutes
        'static_pages' => env('VARNISH_TTL_STATIC', 86400),     // 1 day
        'blog' => env('VARNISH_TTL_BLOG', 900),                 // 15 minutes
        'services' => env('VARNISH_TTL_SERVICES', 3600),        // 1 hour
        'categories' => env('VARNISH_TTL_CATEGORIES', 3600),    // 1 hour
        'api_public' => env('VARNISH_TTL_API_PUBLIC', 300),     // 5 minutes
        'assets' => env('VARNISH_TTL_ASSETS', 2592000),         // 30 days
    ],

    /*
    |--------------------------------------------------------------------------
    | Routes to Never Cache
    |--------------------------------------------------------------------------
    */
    'bypass_routes' => [
        'admin/*',
        'panel/*',
        'filament/*',
        'livewire/*',
        'login',
        'logout',
        'register',
        'password/*',
        'verify/*',
        'two-factor/*',
        'user/*',
        'dashboard/*',
        'account/*',
        'profile/*',
        'payment/*',
        'wallet/*',
        'gateway/*',
        'transaction/*',
        'checkout/*',
        'api/auth/*',
        'api/user/*',
        'telegram/*',
        'webhook/*',
        'otp/*',
        'sms-verify/*',
        'captcha/*',
        'result/*',
        'preview/*',
    ],

    /*
    |--------------------------------------------------------------------------
    | Cookies to Ignore for Caching
    |--------------------------------------------------------------------------
    */
    'ignore_cookies' => [
        '_ga',
        '_gat',
        '_gid',
        '_gcl_au',
        '_fbp',
        'utm_*',
        'has_js',
    ],

    /*
    |--------------------------------------------------------------------------
    | Query Parameters to Strip
    |--------------------------------------------------------------------------
    */
    'strip_params' => [
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content',
        'gclid',
        'fbclid',
        'msclkid',
        'mc_cid',
        'mc_eid',
        '_ga',
        '_ke',
    ],

    /*
    |--------------------------------------------------------------------------
    | Vary Headers
    |--------------------------------------------------------------------------
    */
    'vary_headers' => [
        'Accept-Encoding',
        'Accept-Language',
        'Cookie',  // Only for authenticated content
    ],

    /*
    |--------------------------------------------------------------------------
    | Grace Period (in seconds)
    |--------------------------------------------------------------------------
    */
    'grace_period' => env('VARNISH_GRACE_PERIOD', 21600), // 6 hours

    /*
    |--------------------------------------------------------------------------
    | Mobile Detection
    |--------------------------------------------------------------------------
    */
    'vary_by_user_agent' => env('VARNISH_VARY_BY_USER_AGENT', true),

    /*
    |--------------------------------------------------------------------------
    | ESI (Edge Side Includes) Support
    |--------------------------------------------------------------------------
    */
    'esi_enabled' => env('VARNISH_ESI_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Debug Mode
    |--------------------------------------------------------------------------
    */
    'debug' => env('VARNISH_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Auto Purge Events
    |--------------------------------------------------------------------------
    */
    'auto_purge' => [
        'on_content_update' => true,
        'on_service_update' => true,
        'on_category_update' => true,
        'on_blog_update' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Warmup URLs (URLs to pre-cache after purge)
    |--------------------------------------------------------------------------
    */
    'warmup_urls' => [
        '/',
        '/services',
        '/blog',
        '/about',
        '/contact',
    ],
];