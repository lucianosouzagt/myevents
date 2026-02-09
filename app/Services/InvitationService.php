<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Invitation;
use App\Repositories\InvitationRepository;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvitationSent;
use Exception;

class InvitationService extends BaseService
{
    protected $invitationRepository;

    public function __construct(InvitationRepository $invitationRepository)
    {
        $this->invitationRepository = $invitationRepository;
    }

    public function sendInvitations(Event $event, array $emails)
    {
        $invitations = [];

        foreach ($emails as $email) {
            // Check if already invited
            $exists = $event->invitations()->where('email', $email)->exists();
            if ($exists) {
                continue;
            }

            $token = Str::random(60); // Signed Token Logic can be here or simpler token

            $invitation = $this->invitationRepository->create([
                'event_id' => $event->id,
                'email' => $email,
                'token' => $token,
                'status' => 'pending',
            ]);

            // Send Email (Queued)
            Mail::to($email)->send(new InvitationSent($invitation));

            $invitations[] = $invitation;
        }

        return $invitations;
    }

    public function confirmRSVP(string $token, string $status)
    {
        $invitation = $this->invitationRepository->findByToken($token);

        if (!$invitation) {
            throw new Exception("Convite inválido ou expirado.");
        }
        
        if (!in_array($status, ['confirmed', 'declined'])) {
             throw new Exception("Status inválido.");
        }

        $invitation->update(['status' => $status]);
        
        // If confirmed, maybe create Attendee record?
        if ($status === 'confirmed') {
            // Check if already attendee
            $existingAttendee = $invitation->event->attendees()->where('email', $invitation->email)->first();
            
            if (!$existingAttendee) {
                $invitation->event->attendees()->create([
                    'invitation_id' => $invitation->id,
                    'email' => $invitation->email,
                    'name' => $invitation->guest_name ?? explode('@', $invitation->email)[0],
                ]);
            }
        } else {
             // If declined, remove from attendees if exists?
             $invitation->event->attendees()->where('email', $invitation->email)->delete();
        }

        return $invitation;
    }
    
    public function getInvitationByToken(string $token)
    {
        return $this->invitationRepository->findByToken($token);
    }
}
