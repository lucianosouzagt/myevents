<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\InvitationService;
use App\Models\Event;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Mail;
use App\Mail\InvitationQrCodeMail;

class InvitationController extends Controller
{
    protected $invitationService;

    public function __construct(InvitationService $invitationService)
    {
        $this->invitationService = $invitationService;
    }

    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'emails_string' => 'required|string',
        ]);

        $event = Event::findOrFail($request->event_id);
        
        if ($event->organizer_id !== auth()->id()) {
            abort(403);
        }

        // Convert string "email1, email2" to array
        $emails = array_map('trim', explode(',', $request->emails_string));
        
        // Filter valid emails
        $emails = array_filter($emails, function($email) {
            return filter_var($email, FILTER_VALIDATE_EMAIL);
        });

        if (empty($emails)) {
            return back()->with('error', 'Nenhum e-mail válido fornecido.');
        }

        $this->invitationService->sendInvitations($event, $emails);

        return back()->with('success', count($emails) . ' convites enviados!');
    }

    public function show($token)
    {
        $invitation = $this->invitationService->getInvitationByToken($token);

        if (!$invitation) {
            abort(404, 'Convite não encontrado.');
        }

        $event = $invitation->event;
        return view('invitations.rsvp', compact('invitation', 'event'));
    }

    public function rsvp(Request $request, $token)
    {
        try {
            $confirmedGuests = $request->input('confirmed_guests', 0);
            $this->invitationService->confirmRSVP($token, $request->status, (int)$confirmedGuests);
            return back()->with('success', 'Sua resposta foi registrada!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function sendQrCode(Request $request, $token)
    {
        $invitation = $this->invitationService->getInvitationByToken($token);

        if (!$invitation || $invitation->status !== 'confirmed') {
            return back()->with('error', 'Convite não confirmado ou inválido.');
        }

        if (empty($invitation->email)) {
            return back()->with('error', 'Nenhum e-mail cadastrado para este convite.');
        }

        try {
            Mail::to($invitation->email)->send(new InvitationQrCodeMail($invitation));
            return back()->with('success', 'QR Code enviado com sucesso para ' . $invitation->email);
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao enviar e-mail: ' . $e->getMessage());
        }
    }
}
