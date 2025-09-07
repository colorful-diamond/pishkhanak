<?php

namespace Database\Factories;

use App\Models\Language;
use App\Models\User;
use App\Models\Video;
use Illuminate\Database\Eloquent\Factories\Factory;

class VideoFactory extends Factory
{
    protected $model = Video::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'file_path' => 'videos/' . $this->faker->uuid . '.mp4',
            'language_id' => Language::factory(),
            'user_id' => User::factory(),
            'duration' => $this->faker->time(),
            'is_public' => $this->faker->boolean,
        ];
    }
}