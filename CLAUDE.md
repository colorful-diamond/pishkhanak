# ⚠️ CRITICAL DATABASE SAFETY RULES ⚠️

## 🚫 NEVER RUN THESE COMMANDS - PRODUCTION DATA PROTECTION 🚫

**ABSOLUTELY FORBIDDEN COMMANDS:**
- `php artisan migrate:fresh` - Drops ALL tables and data
- `php artisan migrate:refresh` - Drops and recreates tables  
- `php artisan migrate:reset` - Drops all tables
- `php artisan migrate:rollback --step=10` (or large numbers) - Destructive rollbacks
- `php artisan db:wipe` - Wipes entire database
- `php artisan schema:dump --prune` - Can be destructive
- Any command with `--force` that affects existing data structures

**SAFE COMMANDS ONLY:**
- `php artisan migrate` - Only adds new migrations
- `php artisan migrate --force` - For adding new tables/columns only
- `php artisan db:seed --class=SpecificSeeder` - Only for adding data
- `pg_dump` for backups - Always safe
- Read-only database queries

## 📋 Admin Panel Project Context

**Project**: Pishkhanak Financial Services Platform - Telegram Admin Panel
**Database**: PostgreSQL (pishkhanak) with production data
**Status**: Active production system with live user data

**Current Implementation**:
- ✅ Database tables created (telegram_admins, audit_logs, etc.)
- ✅ Authentication system implemented
- ✅ Admin command handlers created
- 🔄 Building comprehensive admin features

**Admin System Features**:
- Multi-level role-based access (super_admin, admin, moderator, support, read_only)
- Complete audit logging with Persian language support
- Secure authentication with session management
- Rate limiting and security monitoring
- Statistics dashboard with real-time metrics
- User management with ban/unban capabilities
- Wallet management with balance adjustments
- Ticket management system
- Post/content management
- AI content generation integration
- Token management for API access
- System configuration management

**Persian Language Support**:
- RTL text handling and validation
- Persian number formatting
- Localized error messages and UI
- Cultural considerations for financial services

**Security Features**:
- HMAC webhook authentication
- Multi-layer permission system
- Comprehensive audit trails
- Rate limiting protection
- Session-based authentication
- IP-based security logging

## 🛡️ Safety Protocols

**Before ANY database operation:**
1. Always take backup first: `pg_dump -h 127.0.0.1 -U ali_master -d pishkhanak > backup_$(date +%Y%m%d_%H%M%S).sql`
2. Verify the command is on the SAFE list above
3. Test on development database first if possible
4. Document the purpose and expected outcome

**Emergency Recovery:**
- Database backups stored as: `backup_YYYYMMDD_HHMMSS.sql`
- Restore command: `psql -h 127.0.0.1 -U ali_master -d pishkhanak < backup_file.sql`
- Contact system admin if data loss occurs

## 📊 Current System Status

**Environment**: Production Laravel 11 + PostgreSQL
**Telegram Bot**: Active with webhook configured
**Admin Panel**: Development phase - handle with extreme care
**User Data**: Live production data - NEVER DELETE OR MODIFY DESTRUCTIVELY

---

**Remember**: This is a live financial services platform with real user data. Every database operation must preserve existing data integrity.**

## 🚀 Enterprise Content Generation v5

**Latest Command**: `/sc:enterprise-autonomous-v5`

### ⚠️ CRITICAL: Chunk Loading Requirement
**MUST load reference files in 500-line chunks:**
```bash
Read(file_path, limit=500, offset=0)    # Lines 1-500
Read(file_path, limit=500, offset=500)  # Lines 501-1000
Read(file_path, limit=500, offset=1000) # Lines 1001-1500
```

### Critical Requirements:
1. **Table of Contents** (فهرست مطالب) - MANDATORY for all content
2. **50+ Internal Links** throughout content
3. **Minimal UX Design** - Clean, not colorful, proper spacing (p-6, not p-12)
4. **10-15 Keyword Sections** - 150-200 words each, BEFORE FAQs
5. **Standardized FAQs** - EXACT pattern from credit-score-rating
6. **Rich HTML Tags** - em, strong, ul, li, dl, dt, dd throughout
7. **SEO Schema** - Service schema for Google indexing

### Reference Patterns:
- **Pattern Documentation**: `/claudedocs/ENTERPRISE_CONTENT_GENERATION_PATTERNS.md`
- **Reference Design**: Always use `credit-score-rating` service as template
- **Memory**: `enterprise_content_generation_v5` and `superclaude_enterprise_autonomous_v5`

### Command Usage:
```bash
/sc:enterprise-autonomous-v5 --service-id:[ID] \
    keywords:"[comma-separated Persian keywords]" \
    --reference-design="credit-score-rating" \
    --pattern-strict \
    --internal-links=50+ \
    --keyword-sections=auto \
    --comprehensive-faqs=60+ \
    words:12000
```

### Validation Checklist:
✅ Table of Contents with anchor links
✅ 50+ internal links counted
✅ Clean minimal design (50-level backgrounds, 200-level borders)
✅ 10-15 keyword sections present
✅ Standardized FAQ format
✅ Rich HTML semantic tags used
✅ SEO schema included
✅ Mobile responsive design

## 🔑 Test Credentials

**Production Test Account:**
- **URL**: https://pishkhanak.com/access
- **Email**: khoshdel.net@gmail.com
- **Password**: @Alislami67

**Usage**: For Playwright automated testing and manual feature testing
**Access Level**: Admin
**Note**: Use carefully on production environment