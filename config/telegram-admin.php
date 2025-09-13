<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Telegram Admin Panel Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the Telegram bot admin panel system including
    | permissions, rate limits, security settings, and feature toggles.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Permission Levels
    |--------------------------------------------------------------------------
    |
    | Define the hierarchy of admin permission levels. Higher numbers indicate
    | more permissions. Used for role-based access control.
    |
    */
    'permission_levels' => [
        'read_only' => 1,
        'support' => 2,
        'moderator' => 3,
        'admin' => 4,
        'super_admin' => 5,
    ],

    /*
    |--------------------------------------------------------------------------
    | Command Permissions
    |--------------------------------------------------------------------------
    |
    | Define the minimum permission level required for each admin command.
    | Commands not listed here default to 'admin' level.
    |
    */
    'command_permissions' => [
        // Dashboard & Analytics
        'dashboard' => 'read_only',
        'panel' => 'read_only',
        'menu' => 'read_only',
        'stats' => 'read_only',
        'analytics' => 'support',

        // User Management
        'users' => 'support',
        'user_view' => 'support',
        'user_ban' => 'moderator',
        'user_unban' => 'moderator',
        'user_delete' => 'admin',
        'user_edit' => 'moderator',

        // Wallet Management
        'wallets' => 'support',
        'wallet_view' => 'support',
        'wallet_balance' => 'support',
        'wallet_transactions' => 'support',
        'wallet_balance_adjust' => 'admin',
        'wallet_freeze' => 'moderator',
        'wallet_unfreeze' => 'moderator',

        // Ticket System
        'tickets' => 'read_only',
        'ticket_view' => 'read_only',
        'ticket_create' => 'support',
        'ticket_assign' => 'support',
        'ticket_resolve' => 'support',
        'ticket_escalate' => 'moderator',
        'ticket_delete' => 'admin',

        // Content Management
        'posts' => 'support',
        'post_view' => 'support',
        'post_create' => 'support',
        'post_edit' => 'support',
        'post_publish' => 'moderator',
        'post_schedule' => 'moderator',
        'post_delete' => 'admin',

        // AI Content Generation
        'ai' => 'support',
        'ai_generate' => 'support',
        'ai_templates' => 'moderator',
        'ai_template_create' => 'moderator',
        'ai_template_edit' => 'moderator',
        'ai_settings' => 'admin',

        // System Configuration
        'config' => 'admin',
        'settings' => 'admin',
        'config_update' => 'admin',
        'settings_change' => 'admin',

        // Security & Tokens
        'security' => 'super_admin',
        'tokens' => 'super_admin',
        'token_create' => 'super_admin',
        'token_revoke' => 'super_admin',
        'admin_create' => 'super_admin',
        'admin_role_change' => 'super_admin',

        // System Operations
        'system_backup' => 'super_admin',
        'system_maintenance' => 'super_admin',
        'broadcast_send' => 'admin',
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limits
    |--------------------------------------------------------------------------
    |
    | Define rate limiting rules for different types of admin operations.
    | Format: 'max_attempts:decay_minutes'
    |
    */
    'rate_limits' => [
        'admin_commands' => '60:1',         // 60 commands per minute
        'sensitive_operations' => '10:1',   // 10 sensitive ops per minute
        'user_operations' => '30:1',        // 30 user ops per minute
        'wallet_operations' => '20:1',      // 20 wallet ops per minute
        'config_changes' => '5:1',          // 5 config changes per minute
        'broadcasts' => '5:60',             // 5 broadcasts per hour
        'ai_generation' => '15:1',          // 15 AI generations per minute
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    |
    | Security configuration for the admin panel including session management,
    | audit logging, and access controls.
    |
    */
    'security' => [
        'session_duration' => 3600,        // Session duration in seconds (1 hour)
        'max_failed_attempts' => 5,        // Max failed login attempts
        'lockout_duration' => 1800,        // Account lockout duration in seconds (30 minutes)
        'require_session_validation' => true,
        'log_all_actions' => true,
        'sensitive_actions_require_confirmation' => [
            'user_delete', 'admin_create', 'admin_role_change',
            'token_create', 'config_update', 'broadcast_send'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Audit & Compliance
    |--------------------------------------------------------------------------
    |
    | Settings for audit logging and compliance requirements.
    |
    */
    'audit' => [
        'retention_days' => 90,             // How long to keep audit logs
        'cleanup_enabled' => true,         // Automatically cleanup old logs
        'export_format' => 'json',         // Format for audit log exports
        'critical_actions' => [            // Actions that require detailed logging
            'user_delete', 'wallet_balance_adjust', 'admin_create',
            'admin_role_change', 'token_create', 'token_revoke',
            'config_update', 'system_maintenance', 'broadcast_send'
        ],
        'compliance_mode' => true,          // Enable strict compliance logging
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Toggles
    |--------------------------------------------------------------------------
    |
    | Enable or disable specific admin panel features.
    |
    */
    'features' => [
        'user_management' => true,
        'wallet_management' => true,
        'ticket_system' => true,
        'post_management' => true,
        'ai_content_generation' => true,
        'system_configuration' => true,
        'security_monitoring' => true,
        'analytics_dashboard' => true,
        'broadcast_messaging' => true,
        'advanced_reporting' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | UI Customization
    |--------------------------------------------------------------------------
    |
    | Customize the appearance and behavior of the admin panel.
    |
    */
    'ui' => [
        'items_per_page' => 10,             // Default pagination size
        'date_format' => 'Y/m/d H:i',       // Persian date format
        'timezone' => 'Asia/Tehran',        // Default timezone
        'language' => 'fa',                 // Interface language
        'rtl_support' => true,              // Right-to-left text support
        'emoji_support' => true,            // Use emojis in interface
    ],

    /*
    |--------------------------------------------------------------------------
    | Integration Settings
    |--------------------------------------------------------------------------
    |
    | Settings for integration with external services and APIs.
    |
    */
    'integrations' => [
        'ai_services' => [
            'openai_enabled' => true,
            'max_tokens' => 1500,
            'temperature' => 0.7,
            'model' => 'gpt-4',
        ],
        'notification_channels' => [
            'telegram' => true,
            'email' => false,
            'sms' => false,
        ],
        'external_apis' => [
            'timeout' => 30,                // API timeout in seconds
            'retry_attempts' => 3,
            'retry_delay' => 2,             // Delay between retries in seconds
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Dashboard Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for the admin dashboard display and metrics.
    |
    */
    'dashboard' => [
        'refresh_interval' => 30,           // Auto-refresh interval in seconds
        'chart_colors' => [
            'primary' => '#3B82F6',
            'success' => '#10B981', 
            'warning' => '#F59E0B',
            'danger' => '#EF4444',
            'info' => '#8B5CF6',
        ],
        'widgets' => [
            'system_stats' => true,
            'recent_activity' => true,
            'user_metrics' => true,
            'financial_overview' => true,
            'security_alerts' => true,
            'performance_metrics' => true,
        ],
        'metrics_history_days' => 30,       // Days of metrics to keep
    ],

    /*
    |--------------------------------------------------------------------------
    | Localization
    |--------------------------------------------------------------------------
    |
    | Persian language and localization settings.
    |
    */
    'localization' => [
        'number_format' => [
            'decimal_separator' => '.',
            'thousands_separator' => ',',
            'currency_symbol' => 'تومان',
            'currency_position' => 'after',    // 'before' or 'after'
        ],
        'date_format' => [
            'short' => 'Y/m/d',
            'long' => 'l، d F Y',
            'time' => 'H:i',
            'datetime' => 'Y/m/d H:i',
        ],
        'messages' => [
            'success_prefix' => '✅',
            'error_prefix' => '❌',
            'warning_prefix' => '⚠️',
            'info_prefix' => 'ℹ️',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for performance optimization.
    |
    */
    'performance' => [
        'cache_enabled' => true,
        'cache_duration' => 300,            // Cache duration in seconds (5 minutes)
        'lazy_loading' => true,
        'pagination_enabled' => true,
        'compress_responses' => true,
        'query_optimization' => true,
    ],
];