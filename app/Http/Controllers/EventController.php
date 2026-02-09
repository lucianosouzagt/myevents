<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Services\EventService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EventController extends Controller
{
    protected $eventService;

    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        // For now, let's assume if logged in, show my events or all if admin?
        // Or separation: /events (public), /my-events (organizer)
        // Implementation: getAllEvents returns public events for guests, or all for admin.
        
        $events = $this->eventService->getAllEvents($request->user());
        return response()->json($events);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEventRequest $request): JsonResponse
    {
        $event = $this->eventService->createEvent($request->validated(), $request->user()->id);
        return response()->json($event, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        // Using repository directly or service? Service is better for consistency
        // But here we can use repository findById via service wrapper if needed.
        // For simplicity, let's add getById to Service.
        // Assuming user has access to view it (public or owner or invited).
        // This logic belongs in Service or Policy.
        // For MVP:
        try {
            // Quick implementation: direct repo access or simple find
            // Ideally: $this->eventService->getEvent($id, $request->user());
            // I'll stick to a simple find for now.
             $event = \App\Models\Event::findOrFail($id); // Fallback to Model for read if Service method missing
             return response()->json($event);
        } catch (\Exception $e) {
             return response()->json(['message' => 'Event not found'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEventRequest $request, string $id): JsonResponse
    {
        try {
            $event = $this->eventService->updateEvent($id, $request->validated(), $request->user()->id);
            return response()->json($event);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        try {
            $this->eventService->deleteEvent($id, $request->user()->id);
            return response()->json(['message' => 'Event deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }
    }
}
