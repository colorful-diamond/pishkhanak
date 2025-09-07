# Dynamic Settings System Setup Guide

This guide will help you set up the dynamic settings system for managing contact information and other site settings throughout your website.

## What's Been Created

### 1. Database Structure
- **Migration**: `2024_01_15_000000_create_settings_table.php`
- **Model**: `app/Models/Setting.php`
- **Seeder**: `database/seeders/SettingsSeeder.php`

### 2. Helper Classes
- **SettingsHelper**: `app/Helpers/SettingsHelper.php`
- **Verification Script**: `verify_settings_setup.php`

### 3. Admin Panel Integration
- **SettingResource**: `app/Filament/Resources/SettingResource.php`
- **List Page**: `app/Filament/Resources/SettingResource/Pages/ListSettings.php`
- **Create Page**: `app/Filament/Resources/SettingResource/Pages/CreateSetting.php`
- **Edit Page**: `app/Filament/Resources/SettingResource/Pages/EditSetting.php`

### 4. Updated Frontend Files
- Footer: `resources/views/front/partials/footer.blade.php`
- Error Pages: `resources/views/errors/500.blade.php`, `resources/views/errors/503.blade.php`
- Legal Pages: `resources/views/front/pages/custom/privacy-policy.blade.php`, `resources/views/front/pages/custom/terms-conditions.blade.php`

## Setup Instructions

### Step 1: Install PHP and Dependencies
Since PHP is not currently available in your environment, you'll need to:

1. **Install PHP 8.1+** (as specified in your `composer.json`)
2. **Install Composer** if not already installed
3. **Install Node.js and npm** for frontend assets

### Step 2: Run Database Migrations
```bash
cd pishkhanak.com
php artisan migrate
```

### Step 3: Seed the Database
```bash
php artisan db:seed --class=SettingsSeeder
```

This will populate the settings table with default values for:
- Contact information (phone, email, address, etc.)
- Business information (company name, legal name, etc.)
- Social media links (Telegram, etc.)
- General site settings (title, description, keywords)

### Step 4: Verify the Setup
```bash
php verify_settings_setup.php
```

This script will test all components of the settings system and confirm everything is working correctly.

### Step 5: Access Admin Panel
1. Navigate to your admin panel (usually `/admin`)
2. Look for "Settings" in the sidebar
3. You can now manage all site settings through the admin interface

## How to Use the Settings System

### In Blade Templates
```php
{{-- Get specific contact info --}}
{{ \App\Helpers\SettingsHelper::getPhone() }}
{{ \App\Helpers\SettingsHelper::getEmail() }}
{{ \App\Helpers\SettingsHelper::getAddress() }}

{{-- Get business info --}}
{{ \App\Helpers\SettingsHelper::getCompanyName() }}
{{ \App\Helpers\SettingsHelper::getSiteTitle() }}

{{-- Get social media --}}
{{ \App\Helpers\SettingsHelper::getTelegram() }}
{{ \App\Helpers\SettingsHelper::getTelegramUrl() }}
```

### In Controllers
```php
use App\Helpers\SettingsHelper;

public function index()
{
    $contactInfo = [
        'phone' => SettingsHelper::getPhone(),
        'email' => SettingsHelper::getEmail(),
        'address' => SettingsHelper::getAddress(),
    ];
    
    return view('contact', compact('contactInfo'));
}
```

### Using the Generic Get Method
```php
// Get any setting by key
$value = SettingsHelper::get('contact.phone', 'default_value');
$value = SettingsHelper::get('business.company_name', 'Default Company');
```

## Settings Groups

The settings are organized into groups for better management:

### Contact Information (`contact`)
- `phone`: Phone number
- `mobile`: Mobile number
- `email`: Email address
- `support_email`: Support email
- `address`: Physical address
- `working_hours`: Working hours
- `telegram`: Telegram username

### Business Information (`business`)
- `company_name`: Company name in Persian
- `company_name_en`: Company name in English
- `legal_name`: Full legal company name

### Site Information (`site`)
- `title`: Site title
- `description`: Site description
- `keywords`: Site keywords

### Social Media (`social`)
- `telegram`: Telegram username
- `instagram`: Instagram username
- `twitter`: Twitter username
- `linkedin`: LinkedIn URL

## Admin Panel Features

The Filament admin panel provides:

1. **CRUD Operations**: Create, read, update, delete settings
2. **Grouped Display**: Settings are organized by groups
3. **Type Support**: Different input types (text, textarea, email, url, phone, boolean, number)
4. **Public/Private Toggle**: Control which settings are publicly accessible
5. **Required Fields**: Mark settings as required
6. **Sorting**: Custom sort order for each setting
7. **Caching**: Automatic cache management

## Caching

The settings system includes automatic caching:
- Settings are cached for 1 hour by default
- Cache is automatically cleared when settings are updated
- Use `Setting::clearCache()` to manually clear all caches

## Troubleshooting

### Common Issues

1. **"Class not found" errors**: Make sure to run `composer dump-autoload`
2. **Settings not showing**: Check if the seeder ran successfully
3. **Cache issues**: Run `php artisan cache:clear`

### Verification Commands
```bash
# Check if settings table exists
php artisan tinker
>>> App\Models\Setting::count()

# Test a specific setting
>>> App\Helpers\SettingsHelper::getPhone()

# Clear all caches
>>> App\Models\Setting::clearCache()
```

## Next Steps

1. **Customize Settings**: Add new settings through the admin panel
2. **Update Views**: Replace any remaining hardcoded contact info with dynamic settings
3. **Add Validation**: Implement validation rules for critical settings
4. **Extend Functionality**: Add more setting types or groups as needed

## Files Modified

The following files have been updated to use dynamic settings:
- `resources/views/front/partials/footer.blade.php`
- `resources/views/errors/500.blade.php`
- `resources/views/errors/503.blade.php`
- `resources/views/front/pages/custom/privacy-policy.blade.php`
- `resources/views/front/pages/custom/terms-conditions.blade.php`

All contact information in these files is now dynamically loaded from the database and can be managed through the admin panel. 