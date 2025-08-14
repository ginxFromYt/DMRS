<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\EventService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    protected $eventService;

    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }

    /**
     * Display a listing of events
     */
    public function index()
    {
        $events = $this->eventService->getAllEvents();
        $stats = $this->eventService->getEventStatistics();

        return view('events.index', compact('events', 'stats'));
    }

    /**
     * Show the form for creating a new event
     */
    public function create()
    {
        return view('events.create');
    }

    /**
     * Store a newly created event
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category' => 'required|in:university,internal_campus,external_partners',
            'event_date' => 'required|date|after_or_equal:today',
            'event_time' => 'nullable|date_format:H:i',
            'location' => 'nullable|string|max:255',
            'is_deadline' => 'boolean',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $user = auth()->user();
            $event = $this->eventService->createEvent($request->all(), $user);

            return redirect()->route('events.index')
                ->with('success', 'Event created successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified event
     */
    public function show(Event $event)
    {
        return view('events.show', compact('event'));
    }

    /**
     * Show the form for editing the specified event
     */
    public function edit(Event $event)
    {
        $user = auth()->user();
        if (!$user->hasRole('Event Manager') && !$user->hasRole('Administrator') && !$user->hasRole('SuperAdmin')) {
            abort(403, 'Unauthorized to edit events.');
        }

        return view('events.edit', compact('event'));
    }

    /**
     * Update the specified event
     */
    public function update(Request $request, Event $event)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category' => 'required|in:university,internal_campus,external_partners',
            'event_date' => 'required|date',
            'event_time' => 'nullable|date_format:H:i',
            'location' => 'nullable|string|max:255',
            'is_deadline' => 'boolean',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $user = auth()->user();
            $this->eventService->updateEvent($event, $request->all(), $user);

            return redirect()->route('events.index')
                ->with('success', 'Event updated successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified event
     */
    public function destroy(Event $event)
    {
        try {
            $user = auth()->user();
            $this->eventService->deleteEvent($event, $user);

            return redirect()->route('events.index')
                ->with('success', 'Event deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Get events for homepage
     */
    public function getHomepageEvents()
    {
        $events = $this->eventService->getEventsForHomepage();
        return response()->json($events);
    }

    /**
     * Search events
     */
    public function search(Request $request)
    {
        $search = $request->get('search');
        $category = $request->get('category');
        $deadlinesOnly = $request->get('deadlines_only');

        $events = $this->eventService->searchEvents($search, $category, $deadlinesOnly);

        return response()->json($events);
    }

    /**
     * Get events by category
     */
    public function byCategory($category)
    {
        $events = $this->eventService->getEventsByCategory($category);
        return view('events.category', compact('events', 'category'));
    }

    /**
     * Get upcoming deadlines
     */
    public function upcomingDeadlines()
    {
        $deadlines = $this->eventService->getUpcomingDeadlines();
        return view('events.deadlines', compact('deadlines'));
    }
}
