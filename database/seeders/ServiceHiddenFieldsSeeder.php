<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceHiddenFieldsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Card to IBAN service - hide card_number since it's shown in input data
        $cardIbanService = Service::where('slug', 'card-iban')->first();
        if ($cardIbanService) {
            $cardIbanService->update([
                'hidden_fields' => ['card_number']
            ]);
        }

        // Card to Account service - hide card_number since it's shown in input data
        $cardAccountService = Service::where('slug', 'card-account')->first();
        if ($cardAccountService) {
            $cardAccountService->update([
                'hidden_fields' => ['card_number']
            ]);
        }

        // IBAN to Account service - hide iban since it's shown in input data
        $ibanAccountService = Service::where('slug', 'iban-account')->first();
        if ($ibanAccountService) {
            $ibanAccountService->update([
                'hidden_fields' => ['iban']
            ]);
        }

        // IBAN Validator service - hide iban since it's shown in input data
        $ibanValidatorService = Service::where('slug', 'iban-validator')->first();
        if ($ibanValidatorService) {
            $ibanValidatorService->update([
                'hidden_fields' => ['iban']
            ]);
        }

        $this->command->info('Service hidden fields have been set up successfully.');
    }
} 