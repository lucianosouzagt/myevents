<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Invitation;
use App\Services\InvitationService;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    protected $invitationService;

    public function __construct(InvitationService $invitationService)
    {
        $this->invitationService = $invitationService;
    }

    public function index(Request $request, $eventId)
    {
        $event = Event::with('invitations.logs')->findOrFail($eventId);

        if ($event->organizer_id !== auth()->id()) {
            abort(403);
        }

        $invitations = $event->invitations()->orderBy('created_at', 'desc')->paginate(10);

        return view('events.guests.index', compact('event', 'invitations'));
    }

    public function create($eventId)
    {
        $event = Event::findOrFail($eventId);
        if ($event->organizer_id !== auth()->id()) {
            abort(403);
        }
        return view('events.guests.create', compact('event'));
    }

    public function store(Request $request, $eventId)
    {
        $event = Event::findOrFail($eventId);
        if ($event->organizer_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'guest_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'whatsapp' => ['nullable', 'string', 'regex:/^(\+?55|0)?\s?\(?\d{2}\)?\s?\d{4,5}-?\d{4}$/'], // Basic BR validation
            'allowed_guests' => 'required|integer|min:0|max:10',
        ]);

        // Check duplicate email
        if ($event->invitations()->where('email', $request->email)->exists()) {
             return back()->withErrors(['email' => 'Este e-mail já foi adicionado à lista.']);
        }

        $this->invitationService->addGuest($event, $request->all());

        return redirect()->route('events.guests.index', $eventId)->with('success', 'Convidado adicionado com sucesso.');
    }

    public function edit($eventId, $invitationId)
    {
        $event = Event::findOrFail($eventId);
        if ($event->organizer_id !== auth()->id()) {
            abort(403);
        }

        $invitation = $event->invitations()->findOrFail($invitationId);

        return view('events.guests.edit', compact('event', 'invitation'));
    }

    public function update(Request $request, $eventId, $invitationId)
    {
        $event = Event::findOrFail($eventId);
        if ($event->organizer_id !== auth()->id()) {
            abort(403);
        }

        $invitation = $event->invitations()->findOrFail($invitationId);

        $request->validate([
            'guest_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:invitations,email,' . $invitationId . ',id,event_id,' . $eventId,
            'whatsapp' => ['nullable', 'string', 'regex:/^(\+?55|0)?\s?\(?\d{2}\)?\s?\d{4,5}-?\d{4}$/'],
            'allowed_guests' => 'required|integer|min:0|max:10',
        ]);

        $invitation->update($request->only(['guest_name', 'email', 'whatsapp', 'allowed_guests']));

        return redirect()->route('events.guests.index', $eventId)->with('success', 'Convidado atualizado.');
    }

    public function destroy($eventId, $invitationId)
    {
        $event = Event::findOrFail($eventId);
        if ($event->organizer_id !== auth()->id()) {
            abort(403);
        }

        $invitation = $event->invitations()->findOrFail($invitationId);
        $invitation->delete();

        return back()->with('success', 'Convidado removido.');
    }

    public function send(Request $request, $eventId, $invitationId)
    {
        $event = Event::findOrFail($eventId);
        if ($event->organizer_id !== auth()->id()) {
            abort(403);
        }

        $invitation = $event->invitations()->findOrFail($invitationId);
        
        $request->validate([
            'channels' => 'required|array',
            'channels.*' => 'in:email,whatsapp'
        ]);

        $debug = $request->has('debug');

        $results = $this->invitationService->sendSingleInvitation($invitation, $request->channels, $debug);

        if ($debug) {
            // In debug mode, we might want to show what would be sent
             return back()->with('success', 'Modo Debug: ' . json_encode($results));
        }

        return back()->with('success', 'Convite enviado para fila de processamento.');
    }
}
