<?php

namespace Tests\Feature;

use App\Models\Video;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VideoTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_video()
    {
        $user = User::factory()->create();
        $videoData = Video::factory()->make()->toArray();

        $response = $this->actingAs($user)->postJson('/api/videos', $videoData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('videos', ['title' => $videoData['title']]);
    }
}