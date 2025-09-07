<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServicePricingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Set pricing for IBAN-related services
        $services = [
            'card-iban' => [
                            'price' => 5000, // 5,000 IRT
            'cost' => 2000,  // 2,000 IRT (API cost)
            'is_paid' => true,
            'currency' => 'IRT'
            ],
            'card-account' => [
                'price' => 5000,
                'cost' => 2000,
                'is_paid' => true,
                'currency' => 'IRT'
            ],
            'iban-account' => [
                'price' => 5000,
                'cost' => 2000,
                'is_paid' => true,
                'currency' => 'IRT'
            ],
            'sheba-account' => [
                'price' => 5000,
                'cost' => 2000,
                'is_paid' => true,
                'currency' => 'IRT'
            ],
            'card-to-sheba' => [
                'price' => 5000,
                'cost' => 2000,
                'is_paid' => true,
                'currency' => 'IRT'
            ],
            'card-to-account' => [
                'price' => 5000,
                'cost' => 2000,
                'is_paid' => true,
                'currency' => 'IRT'
            ],
            'iban-validator' => [
                'price' => 3000, // 3,000 IRT (cheaper for validation)
                'cost' => 1500,
                'is_paid' => true,
                'currency' => 'IRT'
            ],
            'sheba-validator' => [
                'price' => 3000,
                'cost' => 1500,
                'is_paid' => true,
                'currency' => 'IRT'
            ],
        ];

        foreach ($services as $slug => $pricing) {
            $service = Service::where('slug', $slug)->first();
            
            if ($service) {
                $service->update($pricing);
                $this->command->info("Updated pricing for service: {$service->title}");
            } else {
                $this->command->warn("Service with slug '{$slug}' not found");
            }
        }

        $this->command->info('Service pricing updated successfully!');
    }
} 