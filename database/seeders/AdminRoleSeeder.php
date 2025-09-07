<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminRoleSeeder extends Seeder
{
    public function run()
    {
        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create basic roles
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $customerRole = Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);
        $partnerRole = Role::firstOrCreate(['name' => 'partner', 'guard_name' => 'web']);

        // Create basic permissions
        $accessAdminPermission = Permission::firstOrCreate(['name' => 'access_admin_panel', 'guard_name' => 'web']);
        $manageUsersPermission = Permission::firstOrCreate(['name' => 'manage_users', 'guard_name' => 'web']);

        // Assign permissions to admin role
        $adminRole->givePermissionTo([$accessAdminPermission, $manageUsersPermission]);

        // Assign admin role to super admin user
        $superAdminUser = User::where('email', 'khoshdel.net@gmail.com')->first();
        if ($superAdminUser) {
            $superAdminUser->assignRole('admin');
            $this->command->info('Admin role assigned to khoshdel.net@gmail.com');
        }

        $this->command->info('Basic roles and permissions created successfully!');
        $this->command->info('Roles: admin, customer, partner');
    }
} 