<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvitationRequest;
use App\Services\InvitationService;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class InvitationController extends Controller
{
    protected $invitationService;

    public function __construct(InvitationService $invitationService)
    {
        $this->invitationService = $invitationService;
    }

    /**
     * Send invitations for an event.
     */
    public function store(StoreInvitationRequest $request): JsonResponse
    {
        $event = Event::findOrFail($request->event_id);

        // Authorization: Only organizer can invite
        if ($event->organizer_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $invitations = $this->invitationService->sendInvitations($event, $request->emails);

        return response()->json(['message' => count($invitations) . ' convites enviados.', 'data' => $invitations], 201);
    }

    /**
     * Get invitation details by token (Public).
     */
    public function show(string $token): JsonResponse
    {
        $invitation = $this->invitationService->getInvitationByToken($token);

        if (!$invitation) {
            return response()->json(['message' => 'Convite inválido'], 404);
        }

        return response()->json($invitation->load('event'));
    }

    /**
     * RSVP (Public).
     */
    public function rsvp(Request $request, string $token): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:confirmed,declined'
        ]);

        try {
            $invitation = $this->invitationService->confirmRSVP($token, $request->status);
            return response()->json(['message' => 'Presença ' . ($request->status == 'confirmed' ? 'confirmada' : 'recusada') . ' com sucesso.', 'data' => $invitation]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
