<?php

namespace App\Services;

use App\Models\Invitation;
use App\Models\Attendee;
use App\Models\Checkin;
use Exception;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CheckinService extends BaseService
{
    public function generateQrCode(string $token)
    {
        // Generates a simple SVG QR Code string
        return QrCode::size(300)->generate($token);
    }

    public function processCheckin(string $token, int $eventId)
    {
        // 1. Find Invitation by Token
        $invitation = Invitation::where('token', $token)->first();

        if (!$invitation) {
            throw new Exception("Token inválido.");
        }

        // 2. Validate Event
        if ($invitation->event_id !== $eventId) {
            throw new Exception("Este convite não pertence a este evento.");
        }

        // 3. Find Attendee (Must have RSVP confirmed)
        $attendee = Attendee::where('invitation_id', $invitation->id)->first();

        if (!$attendee) {
             // Optional: Allow checkin if just invited but not RSVP'd? 
             // Requirement says "Confirm presence (RSVP)". Assuming mandatory.
             if ($invitation->status !== 'confirmed') {
                 throw new Exception("Participante não confirmou presença.");
             }
             
             // If confirmed but attendee record missing (edge case), create it
             $attendee = Attendee::create([
                 'event_id' => $eventId,
                 'invitation_id' => $invitation->id,
                 'email' => $invitation->email,
                 'name' => $invitation->guest_name
             ]);
        }

        // 4. Check if already checked in
        $existingCheckin = Checkin::where('attendee_id', $attendee->id)
                                  ->where('event_id', $eventId)
                                  ->first();

        if ($existingCheckin) {
            throw new Exception("Check-in já realizado em " . $existingCheckin->checked_in_at->format('d/m/Y H:i:s'));
        }

        // 5. Create Checkin
        $checkin = Checkin::create([
            'event_id' => $eventId,
            'attendee_id' => $attendee->id,
            'checked_in_at' => now(),
        ]);

        return $checkin;
    }
}
