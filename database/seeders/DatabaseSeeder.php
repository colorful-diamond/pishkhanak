<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            AdminRoleSeeder::class,
            LanguageSeeder::class,
            ProficiencyLevelSeeder::class,
            PaymentSystemSeeder::class,
            JibitGatewaySeeder::class,
            AiSettingSeeder::class,
            ServiceCategorySeeder::class,
            ServiceSeeder::class,
            ServiceHiddenFieldsSeeder::class,
            ServiceShortTitlesSeeder::class,
            ServiceCostSeeder::class,
            SettingsSeeder::class,
            RedirectSeeder::class,
            FooterDataSeeder::class,
        ]);
    }
}