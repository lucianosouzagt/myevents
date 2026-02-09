<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_event()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/events', [
            'title' => 'My Event',
            'description' => 'Description',
            'location' => 'Online',
            'start_time' => now()->addDay()->toDateTimeString(),
            'end_time' => now()->addDay()->addHour()->toDateTimeString(),
            'capacity' => 100,
            'is_public' => true,
        ]);

        $response->assertStatus(201)
                 ->assertJsonPath('title', 'My Event');

        $this->assertDatabaseHas('events', [
            'title' => 'My Event',
            'organizer_id' => $user->id,
        ]);
    }

    public function test_public_events_can_be_listed()
    {
        $user = User::factory()->create();
        
        $this->actingAs($user)->postJson('/api/events', [
            'title' => 'Public Event',
            'location' => 'Online',
            'start_time' => now()->addDay()->toDateTimeString(),
            'end_time' => now()->addDay()->addHour()->toDateTimeString(),
            'capacity' => 50,
            'is_public' => true,
        ]);

        $response = $this->getJson('/api/events');

        $response->assertStatus(200)
                 ->assertJsonCount(1);
    }
}
