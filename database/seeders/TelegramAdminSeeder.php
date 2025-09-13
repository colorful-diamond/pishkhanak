<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TelegramAdmin;
use Illuminate\Support\Facades\DB;

class TelegramAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin chat IDs from config
        $adminIds = env('TELEGRAM_ADMIN_CHAT_IDS', '');
        $adminChatIds = array_filter(array_map('trim', explode(',', $adminIds)));

        if (empty($adminChatIds)) {
            $this->command->warn('No admin chat IDs configured in TELEGRAM_ADMIN_CHAT_IDS');
            return;
        }

        DB::beginTransaction();
        
        try {
            foreach ($adminChatIds as $index => $chatId) {
                if (TelegramAdmin::where('telegram_user_id', $chatId)->exists()) {
                    $this->command->info("Admin with chat ID {$chatId} already exists");
                    continue;
                }

                $admin = TelegramAdmin::create([
                    'telegram_user_id' => $chatId,
                    'username' => null, // Will be populated when user first logs in
                    'first_name' => 'مدیر ' . ($index + 1),
                    'last_name' => null,
                    'role' => $index === 0 ? 'super_admin' : 'admin', // First admin is super_admin
                    'permissions' => [],
                    'is_active' => true,
                    'created_by' => null,
                ]);

                $this->command->info("Created admin: {$admin->display_name} ({$admin->role})");
            }

            DB::commit();
            
            $this->command->info('Telegram admin seeding completed successfully!');
            
        } catch (\Exception $e) {
            DB::rollback();
            $this->command->error('Failed to seed admins: ' . $e->getMessage());
            throw $e;
        }
    }
}