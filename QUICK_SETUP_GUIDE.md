# Quick Setup Guide - Phone to Mobile & Number Conversion

## 🚀 What's Been Implemented

### ✅ Core Changes:
1. **Replaced `phone_number` with `mobile`** throughout the codebase
2. **Added Persian/Arabic to English number conversion**
3. **Enhanced User management in Filament**
4. **Admin role access to access panel**

## 🔧 Setup Steps

### 1. Run Database Seeder (if needed)
```bash
cd pishkhanak.com
php artisan db:seed --class=AdminRoleSeeder
```

### 2. Clear All Caches
```bash
php artisan cache:clear
php artisan view:clear
php artisan config:clear
php artisan route:clear
```

### 3. Test Admin Access
- Login as `khoshdel.net@gmail.com`
- Access `/access` panel
- You should see Users and Roles sections

## 📱 Number Conversion Features

### Automatic Conversion:
- **Persian digits** (۰۱۲۳۴۵۶۷۸۹) → English (0123456789)
- **Arabic digits** (٠١٢٣٤٥٦٧٨٩) → English (0123456789)
- **Mobile validation** - ensures 11 digits starting with 09

### Usage Example:
```php
use App\Helpers\NumberConverter;

// User enters: ۰۹۱۲۳۴۵۶۷۸۹
$clean = NumberConverter::cleanMobile('۰۹۱۲۳۴۵۶۷۸۹');
// Result: 09123456789

// Validate Iranian mobile
$valid = NumberConverter::isValidIranianMobile('09123456789');
// Result: true
```

## 📋 Testing Checklist

### ✅ Admin Panel:
- [ ] Login with admin email
- [ ] Access `/access` panel
- [ ] View Users section
- [ ] Edit user roles
- [ ] View Roles section

### ✅ Mobile Field:
- [ ] User registration with Persian mobile
- [ ] User profile update
- [ ] Service forms with Persian numbers
- [ ] Guest payments

### ✅ Number Conversion:
- [ ] Card numbers: ۱۲۳۴۵۶۷۸۹۰۱۲۳۴۵۶
- [ ] IBAN/Sheba: ۱۲۳۴۵۶۷۸۹۰۱۲۳۴۵۶۷۸۹۰۱۲۳۴
- [ ] Mobile: ۰۹۱۲۳۴۵۶۷۸۹

## 🎯 Benefits

1. **Consistent Data**: All numbers stored in English format
2. **User Friendly**: Users can input Persian/Arabic digits
3. **Automatic Validation**: Mobile numbers properly validated
4. **Admin Access**: Role-based access to admin panel
5. **Clean Architecture**: Unified mobile field instead of phone_number

## 🔍 Quick Tests

### Test Persian Number Input:
```javascript
// In browser console on any form:
document.querySelector('input[name="mobile"]').value = '۰۹۱۲۳۴۵۶۷۸۹';
// Should convert to: 09123456789
```

### Test Admin Role:
```bash
php artisan tinker
$user = App\Models\User::where('email', 'your-email@domain.com')->first();
$user->assignRole('admin');
// User should now have admin panel access
```

Your system is now ready with:
- ✅ Admin role management
- ✅ Mobile field consistency  
- ✅ Persian number conversion
- ✅ Proper validation

🎉 **Setup Complete!** 