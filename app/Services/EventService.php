<?php

namespace App\Services;

use App\Repositories\EventRepository;
use Illuminate\Support\Facades\DB;
use Exception;

use Illuminate\Support\Facades\Storage;

class EventService extends BaseService
{
    protected $eventRepository;

    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function getAllEvents($user = null)
    {
        if ($user && $user->hasRole('admin')) {
            return $this->eventRepository->getAll();
        }
        
        // Public events for everyone
        return $this->eventRepository->getPublicEvents();
    }

    public function getMyEvents($userId)
    {
        return $this->eventRepository->getByOrganizer($userId);
    }

    public function createEvent(array $data, string $organizerId)
    {
        $data['organizer_id'] = $organizerId;
        
        if (isset($data['invitation_image'])) {
            $path = $data['invitation_image']->store('invitation_images', 'public');
            $data['invitation_image_path'] = $path;
            unset($data['invitation_image']);
        }
        
        return DB::transaction(function () use ($data) {
            return $this->eventRepository->create($data);
        });
    }

    public function updateEvent(string $id, array $data, string $userId)
    {
        $event = $this->eventRepository->findById($id);

        if (!$event) {
            throw new Exception("Event not found.");
        }

        if ($event->organizer_id !== $userId) { // Simple authorization check
            throw new Exception("Unauthorized.");
        }

        if (isset($data['invitation_image'])) {
            // Delete old image if exists
            if ($event->invitation_image_path) {
                Storage::disk('public')->delete($event->invitation_image_path);
            }
            
            $path = $data['invitation_image']->store('invitation_images', 'public');
            $data['invitation_image_path'] = $path;
            unset($data['invitation_image']);
        }

        return $this->eventRepository->update($id, $data);
    }

    public function deleteEvent(string $id, string $userId)
    {
        $event = $this->eventRepository->findById($id);

        if (!$event) {
            throw new Exception("Event not found.");
        }

        if ($event->organizer_id !== $userId) {
            throw new Exception("Unauthorized.");
        }

        if ($event->invitation_image_path) {
            Storage::disk('public')->delete($event->invitation_image_path);
        }

        return $this->eventRepository->delete($id);
    }
}
