<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class SetupRolesAndPermissions extends Command
{
    protected $signature = 'setup:roles-permissions {--fresh : Delete existing roles and permissions}';
    protected $description = 'Setup comprehensive roles and permissions for the admin panel';

    public function handle()
    {
        if ($this->option('fresh')) {
            $this->info('Deleting existing roles and permissions...');
            Permission::query()->delete();
            Role::query()->delete();
        }

        $this->info('Setting up permissions...');
        $this->createPermissions();

        $this->info('Setting up roles...');
        $this->createRoles();

        $this->info('Assigning permissions to roles...');
        $this->assignPermissionsToRoles();

        $this->info('Setting up super admin user...');
        $this->setupSuperAdmin();

        $this->info('✅ Roles and permissions setup completed successfully!');
    }

    protected function createPermissions()
    {
        $permissions = [
            // Dashboard permissions
            'dashboard.overview.view' => 'PERSIAN_TEXT_45d49478',
            'dashboard.users.view' => 'PERSIAN_TEXT_85164b17',
            'dashboard.payments.view' => 'PERSIAN_TEXT_f3e0c662',
            'dashboard.tickets.view' => 'PERSIAN_TEXT_00028691',
            'dashboard.wallets.view' => 'PERSIAN_TEXT_cd659ef7',

            // User Management
            'users.view' => 'PERSIAN_TEXT_21b147f2',
            'users.create' => 'PERSIAN_TEXT_1024750f',
            'users.edit' => 'PERSIAN_TEXT_1f97b4ac',
            'users.delete' => 'PERSIAN_TEXT_6dec7fba',
            'users.impersonate' => 'PERSIAN_TEXT_0e905807',

            // Role & Permission Management
            'roles.view' => 'PERSIAN_TEXT_4be552b1',
            'roles.create' => 'PERSIAN_TEXT_c852a162',
            'roles.edit' => 'PERSIAN_TEXT_d0b5e3c9',
            'roles.delete' => 'PERSIAN_TEXT_655f79be',
            'permissions.assign' => 'PERSIAN_TEXT_e8f601e2',

            // Ticket Management
            'tickets.view' => 'PERSIAN_TEXT_2a0a23de',
            'tickets.create' => 'PERSIAN_TEXT_1ffbf6a3',
            'tickets.edit' => 'PERSIAN_TEXT_5ef126c1',
            'tickets.delete' => 'PERSIAN_TEXT_217c9404',
            'tickets.respond' => 'PERSIAN_TEXT_12a1138b',
            'tickets.close' => 'PERSIAN_TEXT_976aa94c',
            'tickets.assign' => 'PERSIAN_TEXT_e0fff88a',
            'tickets.priority.change' => 'PERSIAN_TEXT_6f7f77ae',
            'tickets.status.change' => 'PERSIAN_TEXT_da84d002',
            
            // Ticket Categories
            'ticket-categories.view' => 'PERSIAN_TEXT_578858d7',
            'ticket-categories.create' => 'PERSIAN_TEXT_4f949656',
            'ticket-categories.edit' => 'PERSIAN_TEXT_fd386afc',
            'ticket-categories.delete' => 'PERSIAN_TEXT_c6963abf',

            // Ticket Priorities
            'ticket-priorities.view' => 'PERSIAN_TEXT_ed3f5eea',
            'ticket-priorities.create' => 'PERSIAN_TEXT_dfe6ed4e',
            'ticket-priorities.edit' => 'PERSIAN_TEXT_c37f8c7d',
            'ticket-priorities.delete' => 'PERSIAN_TEXT_4b679e6b',

            // Ticket Statuses
            'ticket-statuses.view' => 'PERSIAN_TEXT_d24e984c',
            'ticket-statuses.create' => 'PERSIAN_TEXT_3589b3ff',
            'ticket-statuses.edit' => 'PERSIAN_TEXT_56c8236b',
            'ticket-statuses.delete' => 'PERSIAN_TEXT_36cf03b8',

            // Ticket Templates
            'ticket-templates.view' => 'PERSIAN_TEXT_40bb7c27',
            'ticket-templates.create' => 'PERSIAN_TEXT_fe605aef',
            'ticket-templates.edit' => 'PERSIAN_TEXT_7eca3266',
            'ticket-templates.delete' => 'PERSIAN_TEXT_a0b9c516',

            // Service Management
            'services.view' => 'PERSIAN_TEXT_080e9ea9',
            'services.create' => 'PERSIAN_TEXT_31de0f7f',
            'services.edit' => 'PERSIAN_TEXT_64cd460d',
            'services.delete' => 'PERSIAN_TEXT_6a4ac164',
            'services.publish' => 'PERSIAN_TEXT_a6076855',
            'services.settings' => 'PERSIAN_TEXT_966f3b2a',

            // Service Categories
            'service-categories.view' => 'PERSIAN_TEXT_37181ccb',
            'service-categories.create' => 'PERSIAN_TEXT_3a6123f9',
            'service-categories.edit' => 'PERSIAN_TEXT_34cb9497',
            'service-categories.delete' => 'PERSIAN_TEXT_bee3523c',

            // Financial Management
            'payments.view' => 'PERSIAN_TEXT_905f4607',
            'payments.manage' => 'PERSIAN_TEXT_20b51d38',
            'payments.refund' => 'PERSIAN_TEXT_7168e869',
            
            // Gateway Transactions
            'gateway-transactions.view' => 'PERSIAN_TEXT_b419f6e4',
            'gateway-transactions.edit' => 'PERSIAN_TEXT_86f0e81c',
            'gateway-transactions.logs' => 'PERSIAN_TEXT_1273b8fd',

            // Wallet Management
            'wallets.view' => 'PERSIAN_TEXT_2b9d6cbf',
            'wallets.create' => 'PERSIAN_TEXT_cc9858e9',
            'wallets.edit' => 'PERSIAN_TEXT_81bc9753',
            'wallets.delete' => 'PERSIAN_TEXT_59821957',
            'wallets.charge' => 'PERSIAN_TEXT_b0a260fe',
            'wallets.withdraw' => 'PERSIAN_TEXT_e4d976da',

            // Wallet Transactions
            'wallet-transactions.view' => 'PERSIAN_TEXT_a02174ff',
            'wallet-transactions.create' => 'PERSIAN_TEXT_37b7ef7b',
            'wallet-transactions.edit' => 'PERSIAN_TEXT_6c39cbec',
            'wallet-transactions.delete' => 'PERSIAN_TEXT_4de9d455',

            // Payment Gateways
            'payment-gateways.view' => 'PERSIAN_TEXT_ee9bfa6f',
            'payment-gateways.create' => 'PERSIAN_TEXT_7835e4ad',
            'payment-gateways.edit' => 'PERSIAN_TEXT_fa709f3c',
            'payment-gateways.delete' => 'PERSIAN_TEXT_ca4b7ee1',
            'payment-gateways.settings' => 'PERSIAN_TEXT_73fcaa5c',

            // Payment Sources
            'payment-sources.view' => 'PERSIAN_TEXT_fa5ee3b7',

            // Content Management
            'posts.view' => 'PERSIAN_TEXT_f8ddd341',
            'posts.create' => 'PERSIAN_TEXT_ddc27ce9',
            'posts.edit' => 'PERSIAN_TEXT_022b8669',
            'posts.delete' => 'PERSIAN_TEXT_57d8e418',
            'posts.publish' => 'PERSIAN_TEXT_3bf9bf02',

            // Categories & Tags
            'categories.view' => 'PERSIAN_TEXT_71d75337',
            'categories.create' => 'PERSIAN_TEXT_baf9aec1',
            'categories.edit' => 'PERSIAN_TEXT_64d1d30b',
            'categories.delete' => 'PERSIAN_TEXT_b2ff1784',

            'tags.view' => 'PERSIAN_TEXT_64d518d7',
            'tags.create' => 'PERSIAN_TEXT_e6d1c6ab',
            'tags.edit' => 'PERSIAN_TEXT_09e84e18',
            'tags.delete' => 'PERSIAN_TEXT_6f189e39',

            // Comments
            'comments.view' => 'PERSIAN_TEXT_2cf71acf',
            'comments.create' => 'PERSIAN_TEXT_2385e5e1',
            'comments.edit' => 'PERSIAN_TEXT_bee6ee90',
            'comments.delete' => 'PERSIAN_TEXT_983f222b',
            'comments.approve' => 'PERSIAN_TEXT_63baa5d0',
            'comments.moderate' => 'PERSIAN_TEXT_fe898ab6',

            // Pages Management
            'pages.view' => 'PERSIAN_TEXT_1ff7158a',
            'pages.create' => 'PERSIAN_TEXT_099599a3',
            'pages.edit' => 'PERSIAN_TEXT_660d4663',
            'pages.delete' => 'PERSIAN_TEXT_fcee1a4a',

            // Contact Messages
            'contact-messages.view' => 'PERSIAN_TEXT_ce5202c6',
            'contact-messages.respond' => 'PERSIAN_TEXT_5eb34161',
            'contact-messages.delete' => 'PERSIAN_TEXT_b4c86605',

            // AI Content Management
            'ai-content.view' => 'PERSIAN_TEXT_32338d9d',
            'ai-content.create' => 'PERSIAN_TEXT_e0c3132e',
            'ai-content.edit' => 'PERSIAN_TEXT_16d02a32',
            'ai-content.delete' => 'PERSIAN_TEXT_768c6401',
            'ai-content.generate' => 'PERSIAN_TEXT_915a70c7',
            'ai-content.settings' => 'PERSIAN_TEXT_09129151',

            // AI Settings
            'ai-settings.view' => 'PERSIAN_TEXT_3dfd4cc0',
            'ai-settings.edit' => 'PERSIAN_TEXT_39a5bf97',

            // Auto Response Management
            'auto-responses.view' => 'PERSIAN_TEXT_678f0a12',
            'auto-responses.create' => 'PERSIAN_TEXT_be3786c3',
            'auto-responses.edit' => 'PERSIAN_TEXT_5cba6ca2',
            'auto-responses.delete' => 'PERSIAN_TEXT_4694118d',

            'auto-response-contexts.view' => 'PERSIAN_TEXT_4e82b4f9',
            'auto-response-contexts.create' => 'PERSIAN_TEXT_9127e9ba',
            'auto-response-contexts.edit' => 'PERSIAN_TEXT_3f8f2bd6',
            'auto-response-contexts.delete' => 'PERSIAN_TEXT_619c7eab',

            // Token Management
            'tokens.view' => 'PERSIAN_TEXT_4e39c1d5',
            'tokens.create' => 'PERSIAN_TEXT_4359c771',
            'tokens.edit' => 'PERSIAN_TEXT_7b69ec1e',
            'tokens.delete' => 'PERSIAN_TEXT_a5487f02',
            'tokens.refresh' => 'PERSIAN_TEXT_d07b28a4',
            'tokens.logs' => 'PERSIAN_TEXT_be91ff8a',

            // System Settings
            'settings.view' => 'PERSIAN_TEXT_9165fbdf',
            'settings.edit' => 'PERSIAN_TEXT_02c7817f',
            'settings.advanced' => 'PERSIAN_TEXT_ec8892c9',

            // Banks & Currencies
            'banks.view' => 'PERSIAN_TEXT_dec983d1',
            'banks.create' => 'PERSIAN_TEXT_bcd754a8',
            'banks.edit' => 'PERSIAN_TEXT_2b3ad620',
            'banks.delete' => 'PERSIAN_TEXT_b764cde4',

            'currencies.view' => 'PERSIAN_TEXT_1cf7cc40',
            'currencies.create' => 'PERSIAN_TEXT_8e730ae1',
            'currencies.edit' => 'PERSIAN_TEXT_8a259a3b',
            'currencies.delete' => 'PERSIAN_TEXT_d7cd834a',

            // Footer Management
            'footer-sections.view' => 'PERSIAN_TEXT_85ad79df',
            'footer-sections.create' => 'PERSIAN_TEXT_04b32b6a',
            'footer-sections.edit' => 'PERSIAN_TEXT_7ca356d5',
            'footer-sections.delete' => 'PERSIAN_TEXT_43d89c76',

            'footer-links.view' => 'PERSIAN_TEXT_6f1acc39',
            'footer-links.create' => 'PERSIAN_TEXT_2a7d522e',
            'footer-links.edit' => 'PERSIAN_TEXT_ac031b5e',
            'footer-links.delete' => 'PERSIAN_TEXT_e448ccb1',

            'footer-contents.view' => 'PERSIAN_TEXT_fffe8279',
            'footer-contents.create' => 'PERSIAN_TEXT_7952a377',
            'footer-contents.edit' => 'PERSIAN_TEXT_4e74155c',
            'footer-contents.delete' => 'PERSIAN_TEXT_ef9ec3af',

            // Site Links
            'site-links.view' => 'PERSIAN_TEXT_608b50b3',
            'site-links.create' => 'PERSIAN_TEXT_84b190fe',
            'site-links.edit' => 'PERSIAN_TEXT_b0438f48',
            'site-links.delete' => 'PERSIAN_TEXT_cd37319c',

            // Redirects
            'redirects.view' => 'PERSIAN_TEXT_6505cf4e',
            'redirects.create' => 'PERSIAN_TEXT_39f664c8',
            'redirects.edit' => 'PERSIAN_TEXT_097395e8',
            'redirects.delete' => 'PERSIAN_TEXT_5f1edd7b',

            // System Monitoring & Reports
            'reports.payments' => 'PERSIAN_TEXT_79391f51',
            'reports.users' => 'PERSIAN_TEXT_dfe7fc95',
            'reports.services' => 'PERSIAN_TEXT_117f262c',
            'reports.tickets' => 'PERSIAN_TEXT_8ee25d6e',

            // Special Admin Functions
            'admin.maintenance' => 'PERSIAN_TEXT_5d4b2850',
            'admin.cache.clear' => 'PERSIAN_TEXT_50487218',
            'admin.logs.view' => 'PERSIAN_TEXT_5138f1ec',
            'admin.backup' => 'PERSIAN_TEXT_94cca83c',
            'admin.security' => 'PERSIAN_TEXT_b716b84b',
        ];

        foreach ($permissions as $name => $displayName) {
            Permission::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web'
            ], [
                'display_name' => $displayName
            ]);
        }

        $this->info('✅ Created ' . count($permissions) . ' permissions');
    }

    protected function createRoles()
    {
        $roles = [
            'super-admin' => [
                'display_name' => 'PERSIAN_TEXT_fa62efff',
                'description' => 'PERSIAN_TEXT_1e40ee87',
                'color' => 'danger'
            ],
            'admin' => [
                'display_name' => 'PERSIAN_TEXT_068fa950',
                'description' => 'PERSIAN_TEXT_eb0a9779',
                'color' => 'warning'
            ],
            'support' => [
                'display_name' => 'PERSIAN_TEXT_53ae02ed',
                'description' => 'PERSIAN_TEXT_14edf5e2',
                'color' => 'info'
            ],
            'content-manager' => [
                'display_name' => 'PERSIAN_TEXT_03c339ed',
                'description' => 'PERSIAN_TEXT_f90e7743',
                'color' => 'success'
            ],
            'financial-manager' => [
                'display_name' => 'PERSIAN_TEXT_c4dc6347',
                'description' => 'PERSIAN_TEXT_cc792c87',
                'color' => 'primary'
            ]
        ];

        foreach ($roles as $name => $attributes) {
            Role::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web'
            ], $attributes);
        }

        $this->info('✅ Created ' . count($roles) . ' roles');
    }

    protected function assignPermissionsToRoles()
    {
        // Super Admin - All permissions
        $superAdmin = Role::findByName('super-admin');
        $superAdmin->syncPermissions(Permission::all());

        // Admin - All except super admin functions
        $admin = Role::findByName('admin');
        $adminPermissions = Permission::where('name', 'not like', 'admin.%')
            ->where('name', '!=', 'roles.delete')
            ->where('name', '!=', 'permissions.assign')
            ->get();
        $admin->syncPermissions($adminPermissions);

        // Support - Ticket management and basic user view
        $support = Role::findByName('support');
        $supportPermissions = [
            // Dashboard access
            'dashboard.overview.view',
            'dashboard.tickets.view',
            
            // Basic user viewing (no editing/creating)
            'users.view',
            
            // Comprehensive ticket management
            'tickets.view',
            'tickets.create',
            'tickets.edit',
            'tickets.respond',
            'tickets.close',
            'tickets.assign',
            'tickets.priority.change',
            'tickets.status.change',
            
            // Ticket-related configurations (view only, no settings)
            'ticket-categories.view',
            'ticket-priorities.view',
            'ticket-statuses.view',
            'ticket-templates.view',
            
            // Contact messages (support function)
            'contact-messages.view',
            'contact-messages.respond',
            
            // Auto responses (for customer support)
            'auto-responses.view',
            'auto-response-contexts.view',
            
            // Basic service viewing (to help with support)
            'services.view',
            'service-categories.view',
            
            // Basic payment viewing (for support purposes)
            'payments.view',
            'wallet-transactions.view',
            'gateway-transactions.view',
            
            // Support reports
            'reports.tickets',
            'reports.users',
        ];
        $support->syncPermissions($supportPermissions);

        // Content Manager - Content and media management
        $contentManager = Role::findByName('content-manager');
        $contentManagerPermissions = [
            'dashboard.overview.view',
            'posts.view', 'posts.create', 'posts.edit', 'posts.delete', 'posts.publish',
            'categories.view', 'categories.create', 'categories.edit', 'categories.delete',
            'tags.view', 'tags.create', 'tags.edit', 'tags.delete',
            'comments.view', 'comments.edit', 'comments.delete', 'comments.approve', 'comments.moderate',
            'pages.view', 'pages.create', 'pages.edit', 'pages.delete',
            'ai-content.view', 'ai-content.create', 'ai-content.edit', 'ai-content.generate',
            'footer-sections.view', 'footer-sections.create', 'footer-sections.edit',
            'footer-links.view', 'footer-links.create', 'footer-links.edit',
            'footer-contents.view', 'footer-contents.create', 'footer-contents.edit',
            'site-links.view', 'site-links.create', 'site-links.edit',
            'redirects.view', 'redirects.create', 'redirects.edit',
        ];
        $contentManager->syncPermissions($contentManagerPermissions);

        // Financial Manager - Financial operations
        $financialManager = Role::findByName('financial-manager');
        $financialManagerPermissions = [
            'dashboard.overview.view',
            'dashboard.payments.view',
            'dashboard.wallets.view',
            'payments.view', 'payments.manage', 'payments.refund',
            'gateway-transactions.view', 'gateway-transactions.edit', 'gateway-transactions.logs',
            'wallets.view', 'wallets.create', 'wallets.edit', 'wallets.charge', 'wallets.withdraw',
            'wallet-transactions.view', 'wallet-transactions.create', 'wallet-transactions.edit',
            'payment-gateways.view', 'payment-gateways.create', 'payment-gateways.edit', 'payment-gateways.settings',
            'payment-sources.view',
            'banks.view', 'banks.create', 'banks.edit',
            'currencies.view', 'currencies.create', 'currencies.edit',
            'reports.payments',
            'users.view', // Need to see users for financial operations
        ];
        $financialManager->syncPermissions($financialManagerPermissions);

        $this->info('✅ Assigned permissions to all roles');
    }

    protected function setupSuperAdmin()
    {
        // Assign admin role to specific mobile number
        $adminPhone = '09153887809';
        $adminUser = User::where('mobile', $adminPhone)->first();

        if ($adminUser) {
            // Remove all existing roles first
            $adminUser->syncRoles([]);
            // Assign admin role
            $adminUser->assignRole('admin');
            $this->info('✅ Assigned admin role to user: ' . $adminUser->name . ' (' . $adminPhone . ')');
        } else {
            $this->warn('⚠️  User with phone number ' . $adminPhone . ' not found.');
        }

        // Check if there's already a super admin
        $superAdminRole = Role::findByName('super-admin');
        $existingSuperAdmin = User::role('super-admin')->first();

        if (!$existingSuperAdmin) {
            // Find the first admin user by email or create one
            $superUser = User::where('email', 'admin@pishkhanak.com')
                ->orWhere('email', 'khoshdel.net@gmail.com')
                ->first();

            if (!$superUser) {
                $this->info('No super admin user found. You can manually assign super-admin role later.');
                return;
            }

            $superUser->assignRole('super-admin');
            $this->info('✅ Assigned super-admin role to: ' . $superUser->email);
        } else {
            $this->info('✅ Super admin already exists: ' . $existingSuperAdmin->email);
        }
    }
}