<?php

namespace App\Http\Controllers;

use App\Services\CheckinService;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CheckinController extends Controller
{
    protected $checkinService;

    public function __construct(CheckinService $checkinService)
    {
        $this->checkinService = $checkinService;
    }

    /**
     * Generate QR Code for a confirmed invitation (Public or Protected?)
     * Ideally protected or signed URL. For MVP, public with token.
     */
    public function showQrCode(string $token): JsonResponse
    {
        // Return SVG or Base64
        $qrCode = $this->checkinService->generateQrCode($token);
        
        return response()->json([
            'qr_code' => (string) $qrCode,
            'token' => $token
        ]);
    }

    /**
     * Perform Check-in (Organizer Only).
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
            'event_id' => 'required|exists:events,id',
        ]);

        $event = Event::findOrFail($request->event_id);

        // Authorization: Only organizer can check-in people
        if ($event->organizer_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            $checkin = $this->checkinService->processCheckin($request->token, $request->event_id);
            
            return response()->json([
                'message' => 'Check-in realizado com sucesso!',
                'attendee' => $checkin->attendee->name ?? $checkin->attendee->email,
                'time' => $checkin->checked_in_at->format('H:i:s')
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
