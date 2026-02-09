<?php

namespace Tests\Feature\Web;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventWebTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_redirects_to_login_if_not_authenticated()
    {
        $response = $this->get('/');
        $response->assertRedirect('/login');
    }

    public function test_homepage_loads_for_authenticated_user()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/');
        // Redirects to my-events
        $response->assertRedirect(route('events.my'));
    }

    public function test_login_page_loads()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_user_can_view_my_events()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/my-events');
        $response->assertStatus(200);
    }

    public function test_user_can_view_my_events_with_data()
    {
        $user = User::factory()->create();
        Event::factory()->create([
            'organizer_id' => $user->id,
            'title' => 'My Test Event',
            'start_time' => now(),
            'end_time' => now()->addHour(),
        ]);

        $response = $this->actingAs($user)->get('/my-events');
        $response->assertStatus(200);
        $response->assertSee('My Test Event');
    }

    public function test_user_can_create_event_via_form()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->post('/events', [
            'title' => 'Web Event',
            'description' => 'Test',
            'location' => 'Web',
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHour(),
            'capacity' => 10,
            'is_public' => true,
        ]);

        $response->assertRedirect(route('events.my'));
        $this->assertDatabaseHas('events', ['title' => 'Web Event']);
    }
}
