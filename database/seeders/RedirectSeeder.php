<?php

namespace Database\Seeders;

use App\Models\Redirect;
use App\Models\User;
use Illuminate\Database\Seeder;

class RedirectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first admin user to assign as creator
        $adminUser = User::whereHas('roles', function ($query) {
            $query->where('name', 'super_admin');
        })->first();

        $userId = $adminUser?->id;

        // Define existing redirects from .htaccess
        $redirects = [
            [
                'from_url' => '/services/credit-scoring',
                'to_url' => '/services/credit-score-rating',
                'status_code' => 301,
                'description' => 'تغییر نام سرویس امتیاز اعتباری',
                'is_active' => true,
                'is_exact_match' => true,
                'created_by' => $userId,
            ],
            [
                'from_url' => '/services/card-to-sheba',
                'to_url' => '/services/card-iban',
                'status_code' => 301,
                'description' => 'تغییر نام سرویس تبدیل کارت به شبا',
                'is_active' => true,
                'is_exact_match' => true,
                'created_by' => $userId,
            ],
            [
                'from_url' => '/services/traffic-fines',
                'to_url' => '/services/car-violation-inquiry',
                'status_code' => 301,
                'description' => 'تغییر نام سرویس استعلام خلافی خودرو',
                'is_active' => true,
                'is_exact_match' => true,
                'created_by' => $userId,
            ],
        ];

        foreach ($redirects as $redirectData) {
            // Check if redirect already exists
            $existing = Redirect::where('from_url', $redirectData['from_url'])->first();
            
            if (!$existing) {
                Redirect::create($redirectData);
                echo "Created redirect: {$redirectData['from_url']} -> {$redirectData['to_url']}\n";
            } else {
                echo "Redirect already exists: {$redirectData['from_url']}\n";
            }
        }

        echo "Redirect seeding completed!\n";
    }
}