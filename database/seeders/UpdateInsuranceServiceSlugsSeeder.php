<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class UpdateInsuranceServiceSlugsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $slugUpdates = [
            'motor-insurance-inquiry' => 'motor-insurance',
            'car-body-insurance-inquiry' => 'body-insurance',
            'fire-insurance-inquiry' => 'fire-insurance',
            'earthquake-insurance-inquiry' => 'earthquake-insurance',
            'karbala-travel-insurance-inquiry' => 'karbala-insurance',
            'supplementary-health-insurance-inquiry' => 'health-insurance',
            'travel-insurance-inquiry' => 'travel-insurance',
            'medical-liability-insurance-inquiry' => 'medical-insurance',
            'elevator-insurance-inquiry' => 'elevator-insurance',
            'sports-insurance-inquiry' => 'sports-insurance',
            'personal-accident-insurance-inquiry' => 'accident-insurance',
            'pension-insurance-inquiry' => 'pension-insurance',
            'war-building-insurance-inquiry' => 'war-insurance',
        ];

        foreach ($slugUpdates as $oldSlug => $newSlug) {
            $service = Service::where('slug', $oldSlug)->first();
            if ($service) {
                $service->update(['slug' => $newSlug]);
                echo "Updated {$oldSlug} → {$newSlug}\n";
            }
        }

        echo "Insurance service slugs updated successfully!\n";
    }
}