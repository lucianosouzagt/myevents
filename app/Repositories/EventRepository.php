<?php

namespace App\Repositories;

use App\Models\Event;

class EventRepository extends BaseRepository
{
    public function __construct(Event $model)
    {
        parent::__construct($model);
    }

    public function getByOrganizer(int $organizerId)
    {
        return $this->model->where('organizer_id', $organizerId)->get();
    }

    public function getPublicEvents()
    {
        return $this->model->where('is_public', true)->get();
    }
}
