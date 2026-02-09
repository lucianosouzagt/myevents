<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EventImageUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_upload_invitation_image_when_creating_event()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $image = UploadedFile::fake()->image('invitation.jpg')->size(1000); // 1MB

        $response = $this->actingAs($user)->post(route('events.store'), [
            'title' => 'Event with Image',
            'description' => 'Testing upload',
            'location' => 'Test Location',
            'start_time' => now()->addDay()->format('Y-m-d\TH:i'),
            'capacity' => 50,
            'invitation_image' => $image,
        ]);

        $response->assertRedirect(route('events.my'));
        $this->assertDatabaseHas('events', [
            'title' => 'Event with Image',
        ]);

        $event = Event::where('title', 'Event with Image')->first();
        $this->assertNotNull($event->invitation_image_path);
        Storage::disk('public')->assertExists($event->invitation_image_path);
    }

    public function test_image_validation_fails_for_non_image_files()
    {
        $user = User::factory()->create();
        $file = UploadedFile::fake()->create('document.pdf', 1000);

        $response = $this->actingAs($user)->post(route('events.store'), [
            'title' => 'Event with PDF',
            'description' => 'Testing fail',
            'location' => 'Test Location',
            'start_time' => now()->addDay()->format('Y-m-d\TH:i'),
            'capacity' => 50,
            'invitation_image' => $file,
        ]);

        $response->assertSessionHasErrors('invitation_image');
    }

    public function test_user_can_replace_invitation_image_when_updating_event()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $event = Event::factory()->create([
            'organizer_id' => $user->id,
            'invitation_image_path' => 'invitation_images/old.jpg'
        ]);
        
        // Create dummy old file
        Storage::disk('public')->put('invitation_images/old.jpg', 'content');

        $newImage = UploadedFile::fake()->image('new_invitation.png')->size(2000);

        $response = $this->actingAs($user)->put(route('events.update', $event->id), [
            'title' => 'Updated Event',
            'location' => $event->location,
            'capacity' => $event->capacity,
            'invitation_image' => $newImage,
        ]);

        $response->assertRedirect(route('events.my'));
        
        $event->refresh();
        $this->assertNotEquals('invitation_images/old.jpg', $event->invitation_image_path);
        Storage::disk('public')->assertExists($event->invitation_image_path);
        Storage::disk('public')->assertMissing('invitation_images/old.jpg');
    }
}
