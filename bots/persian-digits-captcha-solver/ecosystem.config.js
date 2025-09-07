// PM2 Ecosystem Configuration for Persian Digit Captcha API
// =========================================================

module.exports = {
  apps: [{
    name: 'captcha-api',
    script: 'captcha_api_production.py',
    interpreter: 'python3',
    cwd: '/home/seyed/persian-digit-captcha-solver',
    
    // Instance configuration
    instances: 1,  // Single instance due to model loading
    exec_mode: 'fork',  // Fork mode for Python apps
    
    // Auto restart configuration
    autorestart: true,
    watch: false,  // Don't watch files in production
    max_memory_restart: '2G',  // Restart if memory usage exceeds 2GB
    
    // Environment variables
    env: {
      NODE_ENV: 'development',
      COLOR_TOLERANCE: '30',
      USE_PATTERN_SHADOW_DETECTION: 'true',
      LOG_LEVEL: 'INFO',
      PORT: '8989'
    },
    
    env_production: {
      NODE_ENV: 'production',
      COLOR_TOLERANCE: '30',
      USE_PATTERN_SHADOW_DETECTION: 'true',
      LOG_LEVEL: 'INFO',
      PORT: '8989'
    },
    
    // Logging configuration
    log_file: './logs/combined.log',
    out_file: './logs/out.log',
    error_file: './logs/error.log',
    log_date_format: 'YYYY-MM-DD HH:mm:ss Z',
    merge_logs: true,
    
    // Process management
    kill_timeout: 10000,  // 10 seconds to gracefully shutdown
    wait_ready: true,     // Wait for app to be ready
    listen_timeout: 8000, // 8 seconds to wait for listen
    
    // Restart configuration
    min_uptime: '10s',    // Minimum uptime before restart
    max_restarts: 5,      // Maximum restarts within unstable_restarts window
    unstable_restarts: '5m', // Time window for unstable_restarts
    
    // Health monitoring
    health_check_url: 'http://localhost:9090/health',
    health_check_grace_period: 3000,
    
    // Additional PM2 features
    vizion: false,        // Disable versioning
    post_update: ['pip install -r requirements_production.txt']
  }],

  // Deployment configuration (optional)
  deploy: {
    production: {
      user: 'seyed',
      host: 'localhost',
      ref: 'origin/main',
      repo: 'git@github.com:username/persian-digit-captcha-solver.git',
      path: '/home/seyed/persian-digit-captcha-solver',
      'post-deploy': 'pip install -r requirements_production.txt && pm2 reload ecosystem.config.js --env production'
    }
  }
};