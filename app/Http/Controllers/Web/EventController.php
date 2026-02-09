<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\EventService;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Services\MapService;
use Illuminate\Http\Request;

class EventController extends Controller
{
    protected $eventService;
    protected $mapService;

    public function __construct(EventService $eventService, MapService $mapService)
    {
        $this->eventService = $eventService;
        $this->mapService = $mapService;
    }

    public function index(Request $request)
    {
        // Unused since we redirect to myEvents
        // But keeping it empty or redirecting just in case
        return redirect()->route('events.my');
    }

    public function myEvents(Request $request)
    {
        $events = $this->eventService->getMyEvents($request->user()->id);
        return view('events.my', compact('events'));
    }

    public function show($id)
    {
        $event = \App\Models\Event::with(['organizer', 'attendees'])->findOrFail($id);
        $coordinates = $this->mapService->getCoordinatesFromUrl($event->google_maps_link);
        
        return view('events.show', compact('event', 'coordinates'));
    }

    public function create()
    {
        return view('events.create');
    }

    public function store(StoreEventRequest $request)
    {
        $this->eventService->createEvent($request->validated(), $request->user()->id);
        return redirect()->route('events.my')->with('success', 'Evento criado com sucesso!');
    }

    public function edit($id)
    {
        $event = \App\Models\Event::findOrFail($id);
        
        if ($event->organizer_id !== auth()->id()) {
            abort(403);
        }
        
        return view('events.edit', compact('event'));
    }

    public function update(UpdateEventRequest $request, $id)
    {
        $this->eventService->updateEvent($id, $request->validated(), $request->user()->id);
        return redirect()->route('events.my')->with('success', 'Evento atualizado com sucesso!');
    }

    public function destroy(Request $request, $id)
    {
        $this->eventService->deleteEvent($id, $request->user()->id);
        return redirect()->route('events.my')->with('success', 'Evento excluído com sucesso!');
    }
}
