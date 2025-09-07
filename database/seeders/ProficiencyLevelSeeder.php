<?php

namespace Database\Seeders;

use App\Models\ProficiencyLevel;
use Illuminate\Database\Seeder;

class ProficiencyLevelSeeder extends Seeder
{
    public function run()
    {
        $levels = ['Beginner', 'Intermediate', 'Advanced', 'Native'];
        
        foreach ($levels as $level) {
            ProficiencyLevel::create(['name' => $level]);
        }
    }
}