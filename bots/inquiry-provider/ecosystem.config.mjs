export default {
  apps: [
    {
      // Application name
      name: 'pishkhanak-local-api',
      
      // Entry point of the application - use absolute path to be sure
      script: 'localApiServer.js',
      
      // Working directory - PM2 will use this as the base for the script
      cwd: '/home/pishkhanak/htdocs/pishkhanak.com/bots/inquiry-provider',
      
      // Node.js arguments
      node_args: '--max-old-space-size=1024',
      
      // Environment variables for production
      env: {
        NODE_ENV: 'production',
        PORT: 9999,
        DEBUG_MODE: 'false'
      },
      
      // Development environment variables
      env_development: {
        NODE_ENV: 'development',
        PORT: 9999,
        DEBUG_MODE: 'true'
      },
      
      // Number of instances (single instance for localhost-only service)
      instances: 1,
      
      // Execution mode (fork mode for localhost-only service)
      exec_mode: 'fork',
      
      // Auto restart configuration
      autorestart: true,
      watch: false, // Disable file watching in production
      max_memory_restart: '500M',
      
      // Restart configuration
      restart_delay: 2000,
      max_restarts: 10,
      min_uptime: '10s',
      
      // Logging configuration
      log_file: './logs/combined.log',
      out_file: './logs/out.log',
      error_file: './logs/error.log',
      log_date_format: 'YYYY-MM-DD HH:mm:ss Z',
      merge_logs: true,
      
      // Timeouts
      kill_timeout: 5000,
      listen_timeout: 3000,
      
      // Watch ignore patterns (if watch is enabled)
      ignore_watch: [
        'node_modules',
        'logs',
        'services/*/files',
        '*.log',
        '.git'
      ],
      
      // Source map support
      source_map_support: true,
      
      // Instance variables
      instance_var: 'INSTANCE_ID',
      
      // Graceful shutdown
      shutdown_with_message: true,
      
      // Health check grace period
      health_check_grace_period: 3000
    }
  ],
  
  // Deployment configuration (optional)
  deploy: {
    production: {
      user: 'pishkhanak',
      host: '109.206.254.170',
      ref: 'origin/main',
      repo: 'git@github.com:your-repo/pishkhanak.git',
      path: '/var/www/pishkhanak',
      'pre-deploy-local': '',
      'post-deploy': 'cd bots/inquiry-provider && npm install && pm2 reload ecosystem.config.mjs --env production',
      'pre-setup': ''
    }
  }
}; 