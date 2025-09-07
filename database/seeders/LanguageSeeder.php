<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    public function run()
    {
        $languages = ['English', 'Spanish', 'French', 'German', 'Italian'];
        
        foreach ($languages as $language) {
            Language::create(['name' => $language]);
        }
    }
}