<?php

namespace App\Repositories;

use App\Models\Invitation;

class InvitationRepository extends BaseRepository
{
    public function __construct(Invitation $model)
    {
        parent::__construct($model);
    }

    public function findByToken(string $token)
    {
        return $this->model->where('token', $token)->first();
    }
    
    public function getByEvent(int $eventId)
    {
        return $this->model->where('event_id', $eventId)->get();
    }
}
