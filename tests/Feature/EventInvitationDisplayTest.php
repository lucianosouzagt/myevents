<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EventInvitationDisplayTest extends TestCase
{
    use RefreshDatabase;

    public function test_invitation_image_is_displayed_in_sidebar_when_exists()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $event = Event::factory()->create([
            'organizer_id' => $user->id,
            'invitation_image_path' => 'invitation_images/test.jpg'
        ]);

        $response = $this->actingAs($user)->get(route('events.show', $event->id));

        $response->assertStatus(200);
        
        // Assert image is present
        $response->assertSee(Storage::url($event->invitation_image_path));
        
        // Assert structure: Sidebar container
        // We look for the "Convite" heading which we added in the sidebar card
        $response->assertSeeText('Convite');
        $response->assertSeeText('Clique para ampliar');
    }

    public function test_invitation_section_is_not_displayed_when_no_image()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create([
            'organizer_id' => $user->id,
            'invitation_image_path' => null
        ]);

        $response = $this->actingAs($user)->get(route('events.show', $event->id));

        $response->assertStatus(200);
        
        // Should not see the specific sidebar section text
        // "Convite" word might appear elsewhere, so we check for the specific structure or image tag absence
        $response->assertDontSee('Clique para ampliar');
        $response->assertDontSee('alt="Convite: ' . $event->title . '"', false);
    }
}
