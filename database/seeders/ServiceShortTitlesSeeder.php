<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceShortTitlesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            'card-iban' => 'کارت به شبا',
            'card-account' => 'کارت به حساب',
            'iban-account' => 'شبا به حساب',
            'iban-validator' => 'اعتبارسنجی شبا',
        ];

        foreach ($services as $slug => $shortTitle) {
            $service = Service::where('slug', $slug)->first();
            if ($service && !$service->short_title) {
                $service->update(['short_title' => $shortTitle]);
            }
        }

        $this->command->info('Service short titles have been set up successfully.');
    }
} 