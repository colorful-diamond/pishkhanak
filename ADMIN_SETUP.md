# Admin Role Setup Guide

## Quick Setup

Your admin panel access is now ready! Follow these steps:

### 1. Run the Admin Role Seeder
```bash
cd pishkhanak.com
php artisan db:seed --class=AdminRoleSeeder
```

This will:
- ✅ Create admin, customer, and partner roles
- ✅ Automatically assign admin role to `khoshdel.net@gmail.com`
- ✅ Set up basic permissions

### 2. Clear Cache (fixes errors)
```bash
php artisan cache:clear
php artisan view:clear
php artisan config:clear
php artisan route:clear
```

**Note:** If you get "Class not found" errors, this step is essential!

### 3. Test Admin Access
1. Login with `khoshdel.net@gmail.com`
2. Access `/access` panel
3. Navigate to Users section
4. You should see role badges and be able to assign roles

## User Role Management

### In Admin Panel (`/access`):
- **Users Section**: View all users with their roles
- **Roles Section**: Create, edit, and manage roles directly
- **Role Assignment**: Edit user → Select roles → Save
- **Role Display**: Color-coded badges (Admin=Red, Customer=Gray, Partner=Green)

### Assign Admin Role to Other Users
```php
// Via Tinker
php artisan tinker
$user = App\Models\User::where('email', 'user@example.com')->first();
$user->assignRole('admin');
exit
```

## Current Setup

### ✅ Admin Panel Access Logic:
```php
// In User model - canAccessPanel method
if($this->hasRole('admin') || $this->email == 'khoshdel.net@gmail.com'){
    return true;
}
```

### ✅ Available Roles:
- **admin**: Full access to admin panel
- **customer**: Default role for regular users  
- **partner**: For future partner panel

### ✅ Features:
- Simple role management in UserResource
- Basic RoleResource for role CRUD operations
- Color-coded role badges
- Basic permission system
- Compatible with Filament 3.2
- Fixed autoloading issues

## Troubleshooting

### If admin can't access panel:
1. Check if role exists: `php artisan tinker` → `App\Models\User::find(1)->roles`
2. Re-run seeder: `php artisan db:seed --class=AdminRoleSeeder`
3. Clear permission cache: `php artisan permission:cache-reset`

### If getting "Class not found" errors:
1. Run all cache clear commands: `php artisan cache:clear && php artisan view:clear && php artisan config:clear && php artisan route:clear`
2. Check if files exist in `app/Filament/Resources/RoleResource/Pages/`
3. Restart web server

### If getting method errors:
- Run cache clear commands above
- Make sure you're using Filament 3.2 compatible syntax
- Restart web server

## ✅ Error Fixes Included:
- ❌ `Class "ListRoles" not found` → ✅ Fixed with proper page classes
- ❌ `TextInput::lowercase does not exist` → ✅ Fixed with cache clearing
- ❌ `Autoloading issues` → ✅ Fixed with proper directory structure

Your admin role system is now ready! 🎉 