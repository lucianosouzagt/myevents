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
        // Manual Checkin by Organizer (via Scanner or API simulation)
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

    public function index(Request $request, $eventId)
    {
        $event = \App\Models\Event::with('invitations.checkins')->findOrFail($eventId);
        
        if ($event->organizer_id !== auth()->id()) {
            abort(403);
        }

        $query = $event->invitations()->where('status', 'confirmed');

        // Search Filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('guest_name', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%");
            });
        }

        // Status Filter
        if ($request->has('status')) {
            if ($request->status === 'present') {
                $query->whereHas('checkins');
            } elseif ($request->status === 'absent') {
                $query->whereDoesntHave('checkins');
            }
        }
        
        // Sorting
        $sort = $request->get('sort', 'name');
        if ($sort === 'name') {
            $query->orderBy('guest_name');
        } elseif ($sort === 'arrival') {
             // Complex sort for arrival time, usually done by join or subquery. 
             // For simplicity, let's keep name default and simple checkin sort
             // This is a basic implementation.
        }

        $guests = $query->get();
        
        // Calculate Stats
        $totalConfirmed = $event->invitations()->where('status', 'confirmed')->count();
        $totalPresent = $event->checkins()->count();
        $totalAbsent = $totalConfirmed - $totalPresent;

        return view('events.checkin.index', compact('event', 'guests', 'totalConfirmed', 'totalPresent', 'totalAbsent'));
    }

    public function toggle(Request $request, $eventId, $invitationId)
    {
        $event = \App\Models\Event::findOrFail($eventId);
        if ($event->organizer_id !== auth()->id()) {
            abort(403);
        }

        try {
            $result = $this->checkinService->toggleCheckin($eventId, $invitationId);
            $message = $result['status'] === 'checked_in' ? 'Check-in realizado!' : 'Check-in desfeito!';
            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
    
    public function report($eventId)
    {
        $event = \App\Models\Event::with(['invitations' => function($q) {
            $q->where('status', 'confirmed');
        }, 'invitations.checkins'])->findOrFail($eventId);
        
        if ($event->organizer_id !== auth()->id()) {
            abort(403);
        }

        // Simple PDF generation logic or HTML view for print
        // For MVP, we'll return a print-friendly view
        return view('events.checkin.report', compact('event'));
    }
}
