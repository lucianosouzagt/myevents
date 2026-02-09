<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\CheckinService;
use Illuminate\Http\Request;
use App\Models\Invitation;

class CheckinController extends Controller
{
    protected $checkinService;

    public function __construct(CheckinService $checkinService)
    {
        $this->checkinService = $checkinService;
    }

    public function showQrCode($token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();
        
        // Ensure confirmed
        if ($invitation->status !== 'confirmed') {
            return redirect()->route('invitations.show', $token)->with('error', 'Você precisa confirmar presença antes.');
        }

        $qrCode = $this->checkinService->generateQrCode($token);
        $event = $invitation->event;

        return view('invitations.qrcode', compact('qrCode', 'token', 'event'));
    }

    public function store(Request $request)
    {
        // Manual Checkin by Organizer
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'token' => 'required|string',
        ]);
        
        $event = \App\Models\Event::findOrFail($request->event_id);
        if ($event->organizer_id !== auth()->id()) {
            abort(403);
        }

        try {
            $checkin = $this->checkinService->processCheckin($request->token, $request->event_id);
            return back()->with('success', 'Check-in realizado com sucesso para: ' . ($checkin->attendee->name ?? $checkin->attendee->email));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
