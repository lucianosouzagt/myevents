<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use App\Mail\InvitationSent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class InvitationTest extends TestCase
{
    use RefreshDatabase;

    public function test_organizer_can_send_invitations()
    {
        Mail::fake();

        $organizer = User::factory()->create();
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);

        $response = $this->actingAs($organizer)->postJson('/api/invitations', [
            'event_id' => $event->id,
            'emails' => ['guest@example.com']
        ]);

        $response->assertStatus(201);

        Mail::assertQueued(InvitationSent::class, function ($mail) {
            return $mail->hasTo('guest@example.com');
        });

        $this->assertDatabaseHas('invitations', [
            'email' => 'guest@example.com',
            'event_id' => $event->id
        ]);
    }

    public function test_guest_can_rsvp()
    {
        $organizer = User::factory()->create();
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);
        
        // Create invitation manually
        $invitation = \App\Models\Invitation::create([
            'event_id' => $event->id,
            'email' => 'guest@example.com',
            'token' => 'valid-token',
            'status' => 'pending'
        ]);

        $response = $this->postJson("/api/invitations/{$invitation->token}/rsvp", [
            'status' => 'confirmed'
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('invitations', [
            'id' => $invitation->id,
            'status' => 'confirmed'
        ]);

        $this->assertDatabaseHas('attendees', [
            'email' => 'guest@example.com',
            'event_id' => $event->id
        ]);
    }
}
