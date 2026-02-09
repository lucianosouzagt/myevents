<?php

namespace App\Services;

use App\Repositories\EventRepository;
use Illuminate\Support\Facades\DB;
use Exception;

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

    public function createEvent(array $data, int $organizerId)
    {
        $data['organizer_id'] = $organizerId;
        
        return DB::transaction(function () use ($data) {
            return $this->eventRepository->create($data);
        });
    }

    public function updateEvent(int $id, array $data, int $userId)
    {
        $event = $this->eventRepository->findById($id);

        if (!$event) {
            throw new Exception("Event not found.");
        }

        if ($event->organizer_id !== $userId) { // Simple authorization check
            throw new Exception("Unauthorized.");
        }

        return $this->eventRepository->update($id, $data);
    }

    public function deleteEvent(int $id, int $userId)
    {
        $event = $this->eventRepository->findById($id);

        if (!$event) {
            throw new Exception("Event not found.");
        }

        if ($event->organizer_id !== $userId) {
            throw new Exception("Unauthorized.");
        }

        return $this->eventRepository->delete($id);
    }
}
