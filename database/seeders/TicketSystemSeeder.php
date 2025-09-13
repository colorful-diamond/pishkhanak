<?php

namespace Database\Seeders;

use App\Models\TicketCategory;
use App\Models\TicketPriority;
use App\Models\TicketStatus;
use App\Models\TicketTemplate;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class TicketSystemSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles if they don't exist
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $supportRole = Role::firstOrCreate(['name' => 'support']);

        // Create ticket categories
        $categories = [
            [
                'name' => 'مسائل فنی',
                'slug' => 'technical',
                'description' => 'مشکلات فنی و باگ‌های سیستم',
                'color' => '#EF4444',
                'icon' => 'heroicon-o-cog-6-tooth',
                'estimated_response_time' => 60,
                'sort_order' => 1,
            ],
            [
                'name' => 'مسائل مالی',
                'slug' => 'billing',
                'description' => 'مشکلات پرداخت و تراکنش‌های مالی',
                'color' => '#F59E0B',
                'icon' => 'heroicon-o-credit-card',
                'estimated_response_time' => 120,
                'sort_order' => 2,
            ],
            [
                'name' => 'سوالات عمومی',
                'slug' => 'general',
                'description' => 'سوالات عمومی و راهنمایی',
                'color' => '#3B82F6',
                'icon' => 'heroicon-o-question-mark-circle',
                'estimated_response_time' => 240,
                'sort_order' => 3,
            ],
            [
                'name' => 'گزارش خطا',
                'slug' => 'bug-report',
                'description' => 'گزارش خطاها و مشکلات سیستم',
                'color' => '#DC2626',
                'icon' => 'heroicon-o-bug-ant',
                'estimated_response_time' => 30,
                'sort_order' => 4,
            ],
            [
                'name' => 'درخواست ویژگی',
                'slug' => 'feature-request',
                'description' => 'درخواست ویژگی‌های جدید',
                'color' => '#8B5CF6',
                'icon' => 'heroicon-o-light-bulb',
                'estimated_response_time' => 480,
                'sort_order' => 5,
            ],
        ];

        foreach ($categories as $categoryData) {
            TicketCategory::firstOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );
        }

        // Create ticket priorities
        $priorities = [
            [
                'name' => 'کم',
                'slug' => 'low',
                'description' => 'اولویت پایین - غیرضروری',
                'color' => '#10B981',
                'level' => 2,
                'sort_order' => 1,
                'icon' => 'heroicon-o-minus',
            ],
            [
                'name' => 'متوسط',
                'slug' => 'medium',
                'description' => 'اولویت متوسط - معمولی',
                'color' => '#F59E0B',
                'level' => 5,
                'sort_order' => 2,
                'icon' => 'heroicon-o-equals',
            ],
            [
                'name' => 'زیاد',
                'slug' => 'high',
                'description' => 'اولویت بالا - مهم',
                'color' => '#F97316',
                'level' => 7,
                'auto_escalate_after' => 480, // 8 hours
                'sort_order' => 3,
                'icon' => 'heroicon-o-plus',
            ],
            [
                'name' => 'فوری',
                'slug' => 'urgent',
                'description' => 'اولویت فوری - بحرانی',
                'color' => '#EF4444',
                'level' => 9,
                'auto_escalate_after' => 120, // 2 hours
                'sort_order' => 4,
                'icon' => 'heroicon-o-exclamation-triangle',
            ],
        ];

        foreach ($priorities as $priorityData) {
            TicketPriority::firstOrCreate(
                ['slug' => $priorityData['slug']],
                $priorityData
            );
        }

        // Escalation chains removed - escalate_to_priority_id column doesn't exist

        // Create ticket statuses (simplified to only use existing columns)
        $statuses = [
            ['name' => 'باز', 'slug' => 'open', 'color' => '#3B82F6', 'is_active' => true],
            ['name' => 'در حال بررسی', 'slug' => 'in-progress', 'color' => '#F59E0B', 'is_active' => true],
            ['name' => 'در انتظار پاسخ کاربر', 'slug' => 'waiting-for-user', 'color' => '#8B5CF6', 'is_active' => true],
            ['name' => 'حل شده', 'slug' => 'resolved', 'color' => '#10B981', 'is_active' => true],
            ['name' => 'بسته', 'slug' => 'closed', 'color' => '#6B7280', 'is_active' => true],
        ];

        foreach ($statuses as $statusData) {
            TicketStatus::firstOrCreate(
                ['slug' => $statusData['slug']],
                $statusData
            );
        }

        // Assign admin role to main admin user
        $adminUser = User::where('email', 'khoshdel.net@gmail.com')->first();
        if ($adminUser && !$adminUser->hasRole('admin')) {
            $adminUser->assignRole('admin');
        }

        // Template creation skipped - ticket_templates table doesn't exist

        $this->command->info('Ticket system seeded successfully!');
    }
} 