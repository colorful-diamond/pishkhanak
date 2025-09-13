# 👩‍💻 Development Guide - Pishkhanak Platform

> **Complete development setup, workflows, and contribution guidelines for the financial services platform**

## 📋 Table of Contents
- [Environment Setup](#environment-setup)
- [Development Workflow](#development-workflow)
- [Testing Strategy](#testing-strategy)
- [Code Standards](#code-standards)
- [Deployment Process](#deployment-process)
- [Troubleshooting](#troubleshooting)

---

## 🔧 Environment Setup

### **System Requirements**
```bash
Required Software:
├── PHP 8.1+ (with extensions: pdo, pgsql, redis, gd, curl, mbstring)
├── PostgreSQL 13+
├── Redis 6+
├── Node.js 18+ (for bot services)
├── Python 3.8+ (for ML services)
├── Composer 2.x
└── npm/yarn (for asset compilation)

Optional Tools:
├── Docker & Docker Compose (containerized development)
├── PM2 (process management)
└── Supervisor (queue worker management)
```

### **Local Development Setup**

#### **1. Clone and Install Dependencies**
```bash
# Clone the repository
git clone https://github.com/your-org/pishkhanak.git
cd pishkhanak

# Install PHP dependencies
composer install

# Install Node.js dependencies  
npm install

# Install bot service dependencies
cd bots/inquiry-provider && npm install && cd ../..
cd bots/persian-digits-captcha-solver && pip install -r requirements.txt && cd ../..
```

#### **2. Environment Configuration**
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database connection in .env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=pishkhanak
DB_USERNAME=your_username  
DB_PASSWORD=your_password

# Configure Redis connection
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

#### **3. Database Setup**
```bash
# Create database
createdb pishkhanak

# Run migrations
php artisan migrate

# Seed development data
php artisan db:seed --class=DevelopmentSeeder

# Create admin user
php artisan make:filament-user
```

#### **4. Asset Compilation**
```bash
# Development build (with file watching)
npm run dev

# Production build  
npm run build

# Watch for changes (development)
npm run watch
```

### **Bot Services Setup**

#### **Node.js Inquiry Provider**
```bash
# Navigate to bot directory
cd bots/inquiry-provider

# Install dependencies
npm install

# Configure environment
cp .env.example .env

# Start with PM2
pm2 start ecosystem.config.mjs

# Or start directly for development
npm run dev
```

#### **Python CAPTCHA Solver**
```bash
# Navigate to solver directory
cd bots/persian-digits-captcha-solver

# Create virtual environment
python -m venv venv
source venv/bin/activate  # Linux/Mac
# venv\Scripts\activate     # Windows

# Install dependencies
pip install -r requirements.txt

# Start development server
python captcha_api_production.py

# Or start with Gunicorn (production)
gunicorn -c gunicorn.conf.py captcha_api_production:app
```

### **Development Services**

#### **Start All Services**
```bash
# Terminal 1: Laravel development server
php artisan serve

# Terminal 2: Queue worker
php artisan queue:work --verbose

# Terminal 3: Asset compilation
npm run watch

# Terminal 4: Bot services (if using PM2)
pm2 start ecosystem.config.js
pm2 logs
```

#### **Docker Development Environment**
```bash
# Start all services with Docker Compose
docker-compose up -d

# View logs
docker-compose logs -f

# Stop services
docker-compose down
```

---

## 🔄 Development Workflow

### **Git Workflow**
```bash
# Feature branch workflow
git checkout -b feature/service-pricing-update
git add .
git commit -m "feat: update service pricing to 20,000 IRT"
git push origin feature/service-pricing-update

# Create pull request via GitHub/GitLab
# After review and approval, merge to main
```

#### **Commit Message Convention**
```bash
# Format: <type>(<scope>): <description>
feat(services): add credit score rating service
fix(payment): resolve Jibit gateway timeout issue  
docs(api): update authentication documentation
test(models): add Service model unit tests
refactor(controllers): extract payment logic to service
```

### **Branch Protection Rules**
- `main` branch requires pull request reviews
- All tests must pass before merge
- No direct commits to `main` branch
- Feature branches automatically deleted after merge

### **Code Review Checklist**
- [ ] Code follows project style guidelines
- [ ] All tests pass (unit, integration, feature)  
- [ ] Documentation updated for API changes
- [ ] Security considerations addressed
- [ ] Performance impact evaluated
- [ ] Backward compatibility maintained

---

## 🧪 Testing Strategy

### **Testing Pyramid**
```
                    E2E Tests
                   ┌─────────┐
                  │  Feature │
                 │   Tests   │
                └───────────┘
               ┌─────────────┐
              │ Integration  │  
             │    Tests     │
            └─────────────────┘
           ┌───────────────────┐
          │    Unit Tests      │
         │   (Models, Services) │
        └─────────────────────────┘
```

### **Running Tests**

#### **Unit Tests**
```bash
# Run all tests
./vendor/bin/phpunit

# Run specific test suite
./vendor/bin/phpunit --testsuite=Unit

# Run with coverage report
./vendor/bin/phpunit --coverage-html=coverage

# Run specific test class
./vendor/bin/phpunit tests/Unit/Models/ServiceTest.php
```

#### **Feature Tests**
```bash
# Run feature tests
./vendor/bin/phpunit --testsuite=Feature

# Test specific feature
./vendor/bin/phpunit tests/Feature/ServiceRequestTest.php

# Test with database refresh
php artisan test --recreate-databases
```

#### **Browser Tests (Laravel Dusk)**
```bash
# Run browser tests
php artisan dusk

# Run specific browser test
php artisan dusk tests/Browser/ServiceSubmissionTest.php

# Run in headless mode (CI/CD)
php artisan dusk --without-ui
```

### **Test Database Configuration**
```bash
# .env.testing configuration
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1  
DB_PORT=5432
DB_DATABASE=pishkhanak_test
DB_USERNAME=test_user
DB_PASSWORD=test_password

# Create test database
createdb pishkhanak_test

# Run migrations for testing
php artisan migrate --env=testing
```

### **Writing Tests**

#### **Unit Test Example**
```php
<?php
// tests/Unit/Models/ServiceTest.php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ServiceTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function it_can_calculate_display_price()
    {
        $service = Service::factory()->create(['price' => 20000]);
        
        $this->assertEquals('۲۰,۰۰۰ تومان', $service->getDisplayPrice());
    }
    
    /** @test */  
    public function it_has_ai_content_relationship()
    {
        $service = Service::factory()->create();
        
        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\BelongsTo::class,
            $service->aiContent()
        );
    }
}
```

#### **Feature Test Example**
```php
<?php
// tests/Feature/ServiceRequestTest.php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ServiceRequestTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function authenticated_user_can_request_service()
    {
        $user = User::factory()->create();
        $service = Service::factory()->create(['price' => 20000]);
        
        $response = $this->actingAs($user)
            ->postJson("/api/services/{$service->slug}/request", [
                'national_code' => '1234567890',
                'mobile' => '09123456789'
            ]);
            
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'request_id', 
                     'estimated_completion'
                 ]);
    }
}
```

---

## 📏 Code Standards

### **PHP Coding Standards**
Following **PSR-12** coding standard with Laravel-specific conventions.

#### **Laravel Pint Configuration**
```json
{
    "preset": "laravel",
    "rules": {
        "no_unused_imports": true,
        "ordered_imports": true,
        "array_syntax": {"syntax": "short"}
    }
}
```

#### **Run Code Formatting**
```bash
# Format all PHP files
./vendor/bin/pint

# Check formatting without fixing
./vendor/bin/pint --test

# Format specific directory
./vendor/bin/pint app/Services
```

### **Naming Conventions**

#### **File and Class Naming**
```php
// Models: Singular PascalCase
app/Models/Service.php               → class Service
app/Models/GatewayTransaction.php    → class GatewayTransaction

// Controllers: PascalCase with Controller suffix  
app/Http/Controllers/ServiceController.php  → class ServiceController
app/Http/Controllers/PaymentController.php  → class PaymentController

// Services: PascalCase with Service suffix
app/Services/PaymentService.php      → class PaymentService
app/Services/SmsService.php          → class SmsService

// Jobs: PascalCase descriptive name
app/Jobs/ProcessServiceRequest.php   → class ProcessServiceRequest
app/Jobs/SendSmsNotification.php     → class SendSmsNotification
```

#### **Database Naming**
```sql
-- Tables: plural snake_case
users, services, gateway_transactions

-- Columns: snake_case
created_at, national_code, gateway_transaction_id

-- Foreign keys: singular_table_id  
user_id, service_id, payment_gateway_id

-- Indexes: table_column_index
CREATE INDEX services_slug_index ON services (slug);
CREATE INDEX users_mobile_index ON users (mobile);
```

### **Code Organization**

#### **Service Layer Pattern**
```php
<?php
// app/Services/PaymentService.php

namespace App\Services;

use App\Models\User;
use App\Models\GatewayTransaction;
use App\Contracts\PaymentGatewayInterface;

class PaymentService
{
    public function __construct(
        private PaymentGatewayInterface $gateway
    ) {}
    
    public function processPayment(User $user, int $amount): GatewayTransaction
    {
        // Business logic implementation
        $transaction = $this->gateway->createTransaction($user, $amount);
        
        // Additional processing, logging, notifications
        
        return $transaction;
    }
}
```

#### **Repository Pattern (Optional)**
```php
<?php
// app/Repositories/ServiceRepository.php

namespace App\Repositories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Collection;

class ServiceRepository  
{
    public function findActiveServices(): Collection
    {
        return Service::where('status', 'active')
                     ->with(['category', 'author'])
                     ->orderBy('title')
                     ->get();
    }
    
    public function findBySlugWithProviders(string $slug): ?Service
    {
        return Service::where('slug', $slug)
                     ->with(['providers', 'category'])
                     ->first();
    }
}
```

### **Documentation Standards**

#### **PHPDoc Comments**
```php
<?php

/**
 * Process a service request through the bot system
 *
 * @param  Service  $service  The service to be processed
 * @param  array    $data     User input data for the service
 * @param  string|null $provider Preferred provider (optional)
 * @return ServiceResult
 * @throws ServiceUnavailableException When no providers are available
 * @throws ValidationException When input data is invalid
 */
public function processServiceRequest(
    Service $service, 
    array $data, 
    ?string $provider = null
): ServiceResult {
    // Implementation
}
```

#### **API Documentation**
All public APIs must include:
- Endpoint description and purpose
- Request/response examples
- Parameter validation rules
- Error codes and handling
- Rate limiting information

---

## 🚀 Deployment Process

### **Environment Configuration**

#### **Production Environment Variables**
```bash
# Application
APP_ENV=production
APP_DEBUG=false
APP_URL=https://pishkhanak.com

# Database
DB_CONNECTION=pgsql
DB_HOST=localhost
DB_DATABASE=pishkhanak_prod

# Cache & Sessions
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# External Services
FINNOTECH_CLIENT_ID=your_client_id
FINNOTECH_CLIENT_SECRET=your_client_secret
JIBIT_API_KEY=your_jibit_key
```

### **Deployment Pipeline**

#### **Automated Deployment (CI/CD)**
```yaml
# .github/workflows/deploy.yml
name: Deploy to Production

on:
  push:
    branches: [main]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
          
      - name: Install dependencies
        run: composer install --optimize-autoloader --no-dev
        
      - name: Run tests
        run: php artisan test
        
      - name: Deploy to server
        run: |
          php artisan migrate --force
          php artisan config:cache
          php artisan route:cache
          php artisan view:cache
          pm2 restart ecosystem.config.js
```

#### **Manual Deployment Steps**
```bash
# 1. Pull latest code
git pull origin main

# 2. Install/update dependencies  
composer install --optimize-autoloader --no-dev
npm ci --production

# 3. Run migrations
php artisan migrate --force

# 4. Clear and rebuild caches
php artisan config:cache
php artisan route:cache  
php artisan view:cache

# 5. Restart services
pm2 restart ecosystem.config.js
sudo supervisorctl restart laravel-worker:*

# 6. Verify deployment
php artisan about
pm2 status
```

### **Database Migrations**

#### **Migration Best Practices**
```php
<?php
// database/migrations/2024_09_08_120000_update_service_pricing.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Always use transactions for data migrations
        DB::transaction(function () {
            // Update pricing for specific services
            DB::table('services')
                ->where('price', 12500)
                ->update(['price' => 20000]);
        });
    }
    
    public function down()
    {
        DB::transaction(function () {
            DB::table('services')
                ->where('price', 20000) 
                ->update(['price' => 12500]);
        });
    }
};
```

### **Zero-Downtime Deployment**

#### **Blue-Green Deployment Strategy**
```bash
# 1. Deploy to staging environment (green)
git pull origin main
composer install --optimize-autoloader --no-dev

# 2. Run migrations on read replica
php artisan migrate --database=replica

# 3. Switch traffic to green environment  
# (Load balancer configuration)

# 4. Promote replica to primary (if needed)
# 5. Update blue environment as new staging
```

---

## 🔍 Troubleshooting

### **Common Development Issues**

#### **Database Connection Issues**
```bash
# Check PostgreSQL service
sudo systemctl status postgresql
sudo systemctl start postgresql

# Test connection
psql -h localhost -U username -d pishkhanak

# Check Laravel configuration
php artisan config:clear
php artisan config:cache
```

#### **Redis Connection Issues**
```bash
# Check Redis service
sudo systemctl status redis
redis-cli ping

# Clear Redis cache
php artisan cache:clear
php artisan session:flush
```

#### **Queue Worker Issues**
```bash
# Check queue worker status
ps aux | grep queue:work

# Restart queue workers
php artisan queue:restart

# View failed jobs
php artisan queue:failed
php artisan queue:retry all
```

#### **Bot Service Issues**
```bash
# Check PM2 status
pm2 status
pm2 logs localApiServer

# Restart specific service
pm2 restart inquiry-provider

# Check port availability
netstat -tulpn | grep :9999
lsof -i :9999
```

### **Performance Issues**

#### **Database Query Optimization**
```bash
# Enable query log
DB_LOG_QUERIES=true

# Analyze slow queries
php artisan telescope:install  # Development only
php artisan db:show --counts   # Table statistics
```

#### **Cache Optimization**
```bash
# Clear all caches
php artisan optimize:clear

# Rebuild optimized caches
php artisan optimize

# Check cache hit rates
redis-cli info stats
```

### **Security Issues**

#### **Security Headers Check**
```bash
# Test security headers
curl -I https://pishkhanak.com

# Expected headers:
# X-Frame-Options: SAMEORIGIN
# X-Content-Type-Options: nosniff  
# X-XSS-Protection: 1; mode=block
# Strict-Transport-Security: max-age=31536000
```

#### **SSL Certificate Issues**
```bash
# Check certificate expiration
openssl s_client -connect pishkhanak.com:443 | openssl x509 -noout -dates

# Renew Let's Encrypt certificate  
sudo certbot renew --nginx
```

### **Monitoring & Logging**

#### **Application Logs**
```bash
# View Laravel logs
tail -f storage/logs/laravel.log

# View specific log level
grep ERROR storage/logs/laravel-$(date +%Y-%m-%d).log

# Clear old logs
php artisan log:clear --days=30
```

#### **System Monitoring**
```bash
# Check system resources
htop
df -h
free -m

# Check PHP-FPM status
sudo systemctl status php8.2-fpm

# Check Nginx status  
sudo systemctl status nginx
sudo nginx -t  # Test configuration
```

### **Backup & Recovery**

#### **Database Backup**
```bash
# Create database backup
pg_dump -h localhost -U username pishkhanak > backup_$(date +%Y%m%d).sql

# Restore from backup
psql -h localhost -U username pishkhanak < backup_20240908.sql
```

#### **Application Backup**
```bash
# Backup application files
tar -czf app_backup_$(date +%Y%m%d).tar.gz \
    --exclude=node_modules \
    --exclude=vendor \
    --exclude=storage/logs \
    .

# Restore application
tar -xzf app_backup_20240908.tar.gz
composer install
npm install
```

---

## 📞 Support & Resources

### **Development Resources**
- **Laravel Documentation**: https://laravel.com/docs
- **Filament Documentation**: https://filamentphp.com/docs
- **Livewire Documentation**: https://livewire.laravel.com/docs

### **Internal Resources**
- **[Project Index](PROJECT_INDEX.md)** - Platform overview and navigation
- **[Architecture Guide](ARCHITECTURE.md)** - System design and components
- **[API Reference](API_REFERENCE.md)** - Endpoint documentation

### **Getting Help**
- **Technical Issues** - Create GitHub issue with reproduction steps
- **Security Concerns** - Email security team directly
- **Documentation** - Contribute via pull request

---

*📅 Last Updated: 2025-09-08 | 📖 Development Guide v1.0 | 🔄 Generated via SuperClaude /sc:index*