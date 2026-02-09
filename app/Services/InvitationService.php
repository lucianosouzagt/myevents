<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Invitation;
use App\Models\InvitationLog;
use App\Repositories\InvitationRepository;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvitationSent;
use Exception;

class InvitationService extends BaseService
{
    protected $invitationRepository;
    protected $whatsAppService;

    public function __construct(InvitationRepository $invitationRepository, WhatsAppService $whatsAppService)
    {
        $this->invitationRepository = $invitationRepository;
        $this->whatsAppService = $whatsAppService;
    }

    // Deprecated but kept for compatibility with existing bulk send route
    public function sendInvitations(Event $event, array $emails)
    {
        $invitations = [];

        foreach ($emails as $email) {
            // Check if already invited
            $exists = $event->invitations()->where('email', $email)->exists();
            if ($exists) {
                continue;
            }

            $token = Str::random(60);

            $invitation = $this->invitationRepository->create([
                'event_id' => $event->id,
                'email' => $email,
                'token' => $token,
                'status' => 'pending',
            ]);

            // Send Email (Queued)
            try {
                Mail::to($email)->send(new InvitationSent($invitation));
                $this->logInvitation($invitation->id, 'email', 'sent', 'Queued via legacy sender');
            } catch (\Exception $e) {
                $this->logInvitation($invitation->id, 'email', 'failed', $e->getMessage());
            }

            $invitations[] = $invitation;
        }

        return $invitations;
    }

    public function addGuest(Event $event, array $data)
    {
        $token = Str::random(60);
        
        return $this->invitationRepository->create([
            'event_id' => $event->id,
            'email' => $data['email'],
            'guest_name' => $data['guest_name'],
            'whatsapp' => $data['whatsapp'] ?? null,
            'allowed_guests' => $data['allowed_guests'] ?? 0,
            'token' => $token,
            'status' => 'pending',
        ]);
    }

    public function sendSingleInvitation(Invitation $invitation, array $channels, bool $debug = false)
    {
        $results = [];

        if (in_array('email', $channels) && $invitation->email) {
            if ($debug) {
                $results['email'] = [
                    'status' => 'debug',
                    'content' => 'Email would be sent to ' . $invitation->email . ' with RSVP link: ' . route('invitations.show', $invitation->token)
                ];
                $this->logInvitation($invitation->id, 'email', 'debug', 'Debug mode: Email logged.');
            } else {
                try {
                    Mail::to($invitation->email)->send(new InvitationSent($invitation));
                    $this->logInvitation($invitation->id, 'email', 'sent', 'Queued');
                    $results['email'] = 'sent';
                } catch (\Exception $e) {
                    $this->logInvitation($invitation->id, 'email', 'failed', $e->getMessage());
                    $results['email'] = 'failed: ' . $e->getMessage();
                }
            }
        }

        if (in_array('whatsapp', $channels) && $invitation->whatsapp) {
            $rsvpLink = route('invitations.show', $invitation->token);
            $message = "Olá {$invitation->guest_name}! Você foi convidado para o evento {$invitation->event->title}.\n\n";
            $message .= "Data: {$invitation->event->start_time->format('d/m/Y H:i')}\n";
            $message .= "Local: {$invitation->event->location}\n\n";
            $message .= "Confirme sua presença aqui: {$rsvpLink}";

            $response = $this->whatsAppService->sendMessage($invitation->whatsapp, $message, $debug);
            
            $this->logInvitation(
                $invitation->id, 
                'whatsapp', 
                $response['status'] == 'sent' || $response['status'] == 'debug' ? $response['status'] : 'failed', 
                json_encode($response)
            );

            $results['whatsapp'] = $response;
        }

        return $results;
    }

    protected function logInvitation($invitationId, $channel, $status, $response)
    {
        InvitationLog::create([
            'invitation_id' => $invitationId,
            'channel' => $channel,
            'status' => $status,
            'response' => $response
        ]);
    }

    public function confirmRSVP(string $token, string $status, int $confirmedGuests = 0)
    {
        $invitation = $this->invitationRepository->findByToken($token);

        if (!$invitation) {
            throw new Exception("Convite inválido ou expirado.");
        }
        
        if (!in_array($status, ['confirmed', 'declined'])) {
             throw new Exception("Status inválido.");
        }

        // Validate confirmed guests if confirming
        if ($status === 'confirmed') {
            if ($confirmedGuests < 0) {
                throw new Exception("Número de acompanhantes inválido.");
            }
            if ($confirmedGuests > $invitation->allowed_guests) {
                throw new Exception("Número de acompanhantes excede o permitido ({$invitation->allowed_guests}).");
            }
        } else {
            $confirmedGuests = 0;
        }

        $invitation->update([
            'status' => $status,
            'confirmed_guests' => $confirmedGuests
        ]);
        
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
