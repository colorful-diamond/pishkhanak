<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Automatic Token Refresh Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for the automatic token refresh
    | system that manages API tokens for various providers.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Refresh Schedule
    |--------------------------------------------------------------------------
    |
    | Configure when and how often tokens should be automatically refreshed.
    |
    */
    'schedule' => [
        'interval' => 'everyTwelveHours', // Laravel schedule expression
        'timeout' => 600, // Maximum job runtime in seconds (10 minutes)
        'retries' => 3, // Number of retry attempts
        'backoff' => 60, // Seconds to wait between retries
    ],

    /*
    |--------------------------------------------------------------------------
    | Providers Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for each API provider that requires token management.
    |
    */
    'providers' => [
        'jibit' => [
            'enabled' => true,
            'refresh_threshold_minutes' => 5, // Refresh if expires within X minutes
            'critical_threshold_hours' => 2, // Consider critical if expires within X hours
        ],
        'finnotech' => [
            'enabled' => true,
            'refresh_threshold_minutes' => 5,
            'critical_threshold_hours' => 2,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Settings
    |--------------------------------------------------------------------------
    |
    | Configure when and how to send notifications about token refresh status.
    |
    */
    'notifications' => [
        'enabled' => env('TOKEN_REFRESH_NOTIFICATIONS_ENABLED', true),
        'email' => env('ADMIN_EMAIL', 'admin@pishkhanak.com'),
        'channels' => ['mail'], // Available: mail, slack, discord
        
        // When to send notifications
        'triggers' => [
            'on_failure' => true,
            'on_critical' => true, // When token expires within critical threshold
            'on_success' => false, // Don't spam on success
            'daily_summary' => true, // Send daily summary report
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Configure how refresh attempts are logged and stored.
    |
    */
    'logging' => [
        'enabled' => true,
        'cleanup_after_days' => 30, // Delete logs older than X days
        'detailed_metadata' => true, // Store detailed token metadata
        'log_level' => 'info', // Minimum log level: debug, info, warning, error
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Configuration
    |--------------------------------------------------------------------------
    |
    | Configure queue settings for token refresh jobs.
    |
    */
    'queue' => [
        'connection' => env('QUEUE_CONNECTION', 'database'),
        'queue_name' => 'tokens',
        'run_in_background' => true,
        'prevent_overlap_minutes' => 60, // Prevent job overlap for X minutes
    ],

    /*
    |--------------------------------------------------------------------------
    | Health Monitoring
    |--------------------------------------------------------------------------
    |
    | Configure health checks and monitoring for the token refresh system.
    |
    */
    'health' => [
        'enabled' => true,
        'check_interval_minutes' => 30, // How often to check token health
        'alert_on_consecutive_failures' => 3, // Alert after X consecutive failures
        'success_rate_threshold' => 80, // Alert if success rate falls below X%
    ],

    /*
    |--------------------------------------------------------------------------
    | Backup & Recovery
    |--------------------------------------------------------------------------
    |
    | Configure backup strategies for when automatic refresh fails.
    |
    */
    'backup' => [
        'enabled' => true,
        'use_legacy_job' => true, // Use legacy RefreshApiTokensJob as backup
        'backup_schedule' => 'twiceDaily', // When to run backup refresh
        'only_on_failure' => true, // Only run backup if automatic refresh failed
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Settings
    |--------------------------------------------------------------------------
    |
    | Configure performance-related settings for the refresh system.
    |
    */
    'performance' => [
        'concurrent_providers' => true, // Refresh providers concurrently
        'cache_token_status' => true, // Cache token health status
        'cache_ttl_minutes' => 5, // Cache TTL for token status
        'batch_size' => 10, // Number of tokens to process in batch
    ],
]; 