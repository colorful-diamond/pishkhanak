<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceCostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            'card-iban' => 5000, // 5,000 تومان
            'card-account' => 5000, // 5,000 تومان
            'iban-account' => 5000, // 5,000 تومان
            'iban-validator' => 3000, // 3,000 تومان
        ];

        foreach ($services as $slug => $cost) {
            $service = Service::where('slug', $slug)->first();
            if ($service && !$service->cost) {
                $service->update(['cost' => $cost]);
            }
        }

        $this->command->info('Service costs have been set up successfully.');
    }
} 