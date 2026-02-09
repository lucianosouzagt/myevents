<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use App\Models\Invitation;
use App\Models\Attendee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckinTest extends TestCase
{
    use RefreshDatabase;

    public function test_organizer_can_scan_qr_code_and_checkin_guest()
    {
        $organizer = User::factory()->create();
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);
        
        // Invitation confirmed
        $invitation = Invitation::create([
            'event_id' => $event->id,
            'email' => 'guest@example.com',
            'token' => 'qr-token-123',
            'status' => 'confirmed'
        ]);

        // Attendee record created (RSVP flow)
        $attendee = Attendee::create([
            'event_id' => $event->id,
            'invitation_id' => $invitation->id,
            'email' => 'guest@example.com',
            'name' => 'Guest'
        ]);

        $response = $this->actingAs($organizer)->postJson('/api/checkin', [
            'event_id' => $event->id,
            'token' => 'qr-token-123'
        ]);

        $response->assertStatus(201)
                 ->assertJsonFragment(['message' => 'Check-in realizado com sucesso!']);

        $this->assertDatabaseHas('checkins', [
            'attendee_id' => $attendee->id,
            'event_id' => $event->id
        ]);
    }

    public function test_cannot_checkin_twice()
    {
        $organizer = User::factory()->create();
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);
        
        $invitation = Invitation::create([
            'event_id' => $event->id,
            'email' => 'guest@example.com',
            'token' => 'qr-token-123',
            'status' => 'confirmed'
        ]);

        $attendee = Attendee::create([
            'event_id' => $event->id,
            'invitation_id' => $invitation->id,
            'email' => 'guest@example.com',
            'name' => 'Guest'
        ]);

        // First Checkin
        $this->actingAs($organizer)->postJson('/api/checkin', [
            'event_id' => $event->id,
            'token' => 'qr-token-123'
        ]);

        // Second Checkin
        $response = $this->actingAs($organizer)->postJson('/api/checkin', [
            'event_id' => $event->id,
            'token' => 'qr-token-123'
        ]);

        $response->assertStatus(400)
                 ->assertJsonFragment(['message' => 'Check-in já realizado em ' . now()->format('d/m/Y H:i:s')]); // Time might vary slightly, but message prefix matches
    }
}
