<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EventValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_event_requires_end_time_when_checkbox_checked()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/events', [
            'title' => 'Test Event',
            'location' => 'Online',
            'start_time' => now()->addDay(),
            'has_end_time' => 1,
            'end_time' => null, // Should fail
            'capacity' => 10,
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['end_time']);
    }

    public function test_event_does_not_require_end_time_when_checkbox_unchecked()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/events', [
            'title' => 'Test Event',
            'location' => 'Online',
            'start_time' => now()->addDay(),
            'has_end_time' => false,
            'end_time' => null, // Should pass
            'capacity' => 10,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('events', [
            'title' => 'Test Event',
            'end_time' => null,
        ]);
    }

    public function test_event_end_time_must_be_after_start_time()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/events', [
            'title' => 'Test Event',
            'location' => 'Online',
            'start_time' => now()->addDay(),
            'has_end_time' => true,
            'end_time' => now(), // Before start time
            'capacity' => 10,
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['end_time']);
    }
}
