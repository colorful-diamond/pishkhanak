# Pishkhanak (پیشخوانک) - Financial Services Platform

<p align="center">
  <a href="https://pishkhanak.com">
    <img src="https://img.shields.io/badge/Website-pishkhanak.com-blue" alt="Website">
  </a>
  <a href="https://packagist.org/packages/laravel/framework">
    <img src="https://img.shields.io/packagist/l/laravel/framework" alt="License">
  </a>
  <img src="https://img.shields.io/badge/PHP-8.1%2B-blue" alt="PHP Version">
  <img src="https://img.shields.io/badge/Laravel-11-red" alt="Laravel Version">
</p>

## Overview

Pishkhanak is a comprehensive financial services platform built with Laravel 11, offering a wide range of Iranian financial, administrative, and governmental inquiry services. The platform integrates with multiple payment gateways and features an advanced AI content generation system.

## 🚀 Key Features

### Core Services
- **Financial Inquiries**: IBAN validation, card-to-account mapping, credit scoring
- **Vehicle Services**: Traffic violations, vehicle information, insurance history
- **Identity Services**: National ID validation, passport tracking, military service status
- **Banking Services**: Account inquiries, cheque status, facility information
- **Government Services**: Social security, tax inquiries, postal code validation

### Advanced Capabilities
- **AI Content Generation**: Automated blog content creation with Gemini AI
- **Multi-Gateway Payments**: Jibit, Finnotech, and Sepehr integrations
- **Real-time Processing**: WebSocket support via Laravel Reverb
- **Admin Dashboard**: Comprehensive Filament-based administration
- **Background Processing**: Queue-based service handling
- **Cache Management**: Redis-powered performance optimization

### Technical Features
- **SMS Authentication**: Multi-provider SMS verification
- **Wallet System**: User credit management with transaction history
- **Token Management**: Automatic API token refresh and health monitoring
- **Backup System**: Automated database and file backups
- **Telegram Integration**: Bot-based notifications and support
- **Image Generation**: AI-powered thumbnail and content images

## 🏗️ Architecture

### Project Structure
```
app/
├── Console/Commands/     # Artisan commands and scheduled tasks
├── Filament/            # Admin panel resources and pages
├── Http/Controllers/    # Request handling and business logic
├── Models/             # Eloquent models and database relationships
├── Services/           # Business logic and external API integrations
├── Jobs/               # Queue-based background processing
└── Helpers/            # Utility functions and helpers

resources/views/        # Blade templates
routes/                # Route definitions
database/              # Migrations, seeders, and factories
config/                # Configuration files
```

### Service Layer Architecture

#### Payment Services
- `PaymentService`: Core payment processing
- `ServicePaymentService`: Service-specific payment handling
- `GuestServiceClaimService`: Anonymous user payment processing

#### AI Services
- `AiService`: Core AI functionality
- `GeminiService`: Google Gemini integration
- `ContentEnhancementService`: Content improvement and SEO
- `ImageGenerationService`: AI image creation
- `BlogImageService`: Automated blog thumbnails

#### External API Services
- `Finnotech/`: Complete Finnotech API integration
- `Jibit/`: Jibit payment gateway services
- `TelegramBotService`: Telegram bot functionality
- `SmsService`: Multi-provider SMS handling

## 📋 Requirements

### System Requirements
- **PHP**: 8.1, 8.2, 8.3, or 8.4
- **Database**: PostgreSQL (recommended), MySQL, or SQLite
- **Redis**: For caching, sessions, and queues
- **Node.js**: For asset compilation
- **Composer**: PHP dependency management

### External Dependencies
- **Filament 3.2+**: Admin panel framework
- **Laravel Reverb**: WebSocket server
- **Intervention Image**: Image processing
- **Spatie Packages**: Permissions, media library, tags
- **OpenAI PHP**: AI content generation
- **Telegram Bot API**: Bot integration

## 🔧 Installation

### 1. Clone and Setup
```bash
git clone https://github.com/colorful-diamond/pishkhanak.git
cd pishkhanak
composer install
npm install
```

### 2. Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Database Setup
```bash
php artisan migrate
php artisan db:seed
php artisan storage:link
```

### 4. External Services Configuration

#### API Keys Required
```env
# AI Services
OPENROUTER_API_KEY=your_openrouter_key
GEMINI_API_KEY=your_gemini_key

# Payment Gateways
JIBIT_CLIENT_ID=your_jibit_client_id
JIBIT_CLIENT_SECRET=your_jibit_secret
SEPEHR_GATEWAY_KEY=your_sepehr_key

# SMS Services
SMS_API_KEY=your_sms_api_key

# Telegram Bot
TELEGRAM_BOT_TOKEN=your_telegram_token
```

### 5. Queue and Cache Setup
```bash
# Redis configuration
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis

# Start queue workers
php artisan queue:work --daemon

# Start Reverb WebSocket server
php artisan reverb:start
```

### 6. Build Assets
```bash
npm run build
```

### 7. Start Application
```bash
php artisan serve
```

## 📚 Documentation

### Service Controllers
The platform uses a factory pattern for service controllers:

```php
// Service Controller Factory
ServiceControllerFactory::create('iban-check', $request);
```

### Payment Flow
1. **Service Request**: User initiates service request
2. **Payment Validation**: Gateway-specific validation
3. **API Processing**: External API call with retry logic
4. **Result Storage**: Secure result caching
5. **User Notification**: SMS/email confirmation

### AI Content Generation
```php
// Generate blog content
$aiService = new AiService();
$content = $aiService->generateBlogPost($title, $keywords);

// Generate images
$imageService = new ImageGenerationService();
$thumbnail = $imageService->generateThumbnail($content);
```

## 🔐 Security Features

### Data Protection
- **API Token Encryption**: Secure token storage and rotation
- **Request Signing**: Webhook signature verification
- **Rate Limiting**: API request throttling
- **Input Validation**: Comprehensive request validation

### Authentication
- **Multi-factor SMS**: SMS-based verification
- **Social Login**: OAuth integration (Discord, Google)
- **Guest Services**: Anonymous service access
- **Admin 2FA**: Two-factor admin authentication

## 📊 Monitoring & Logging

### Health Monitoring
- **Token Health**: Automatic API token validation
- **Queue Monitoring**: Background job status tracking
- **Performance Metrics**: Response time monitoring
- **Error Tracking**: Comprehensive error logging

### Admin Dashboard Features
- **Service Statistics**: Usage analytics and reporting
- **Payment Tracking**: Transaction monitoring
- **User Management**: Account administration
- **Content Management**: Blog and page editing
- **AI Content Pipeline**: Content generation monitoring

## 🤖 AI Integration

### Content Generation Pipeline
1. **Topic Analysis**: Keyword and trend analysis
2. **Content Creation**: AI-powered article generation
3. **SEO Optimization**: Meta tags and structure optimization
4. **Image Generation**: Automated thumbnail creation
5. **Publication**: Automated publishing workflow

### AI Services
- **Blog Content**: Automated article generation
- **Service Descriptions**: Dynamic service documentation
- **User Support**: AI-powered auto-responses
- **Image Generation**: Custom thumbnails and graphics

## 🚀 Deployment

### Production Setup
```bash
# Optimize for production
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Queue workers with supervisor
php artisan queue:work --daemon --tries=3 --timeout=60

# Schedule cron jobs
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

### Environment Considerations
- **Database**: Use PostgreSQL for production
- **Cache**: Redis cluster for high availability
- **Storage**: S3-compatible object storage
- **Monitoring**: Set up application monitoring
- **Backup**: Automated daily backups

## 📈 Performance Optimization

### Caching Strategy
- **Service Results**: 24-hour result caching
- **API Responses**: Intelligent response caching
- **Static Content**: CDN-optimized asset delivery
- **Database Queries**: Query result caching

### Queue Optimization
- **Background Processing**: Heavy operations in queues
- **Retry Logic**: Intelligent failure handling
- **Priority Queues**: Critical operation prioritization
- **Batch Processing**: Bulk operation optimization

## 🛠️ Maintenance

### Regular Tasks
```bash
# Token refresh (daily)
php artisan tokens:refresh

# Cleanup expired data
php artisan cleanup:expired-results
php artisan cleanup:expired-otps

# Backup (daily)
php artisan backup:run

# Health checks
php artisan tokens:health-check
php artisan system:status
```

### Monitoring Commands
```bash
# Check queue status
php artisan queue:monitor

# View logs
php artisan telescope:publish
php artisan horizon:publish
```

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Development Guidelines
- Follow PSR-12 coding standards
- Write comprehensive tests
- Update documentation for new features
- Use meaningful commit messages

## 📄 License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## 🙋 Support

For support and questions:
- **Email**: support@pishkhanak.com
- **Telegram**: @PishkhanakSupport
- **Issues**: GitHub Issues tracker

---

**Pishkhanak** - Simplifying financial services for everyone 🚀