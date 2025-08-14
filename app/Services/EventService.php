<?php

namespace App\Services;

use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EventService
{
    /**
     * Create a new event
     */
    public function createEvent(array $data, User $user): Event
    {
        if (!$user->hasRole('Event Manager') && !$user->hasRole('Administrator') && !$user->hasRole('SuperAdmin')) {
            throw new \Exception('Only Event Managers and Administrators can create events.');
        }

        return DB::transaction(function () use ($data, $user) {
            $event = Event::create([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'category' => $data['category'],
                'event_date' => $data['event_date'],
                'event_time' => $data['event_time'] ?? null,
                'location' => $data['location'] ?? null,
                'is_deadline' => $data['is_deadline'] ?? false,
                'is_active' => $data['is_active'] ?? true,
                'created_by' => $user->id,
            ]);

            Log::info("Event created: {$event->title} (ID: {$event->id}) by {$user->full_name}");

            return $event;
        });
    }

    /**
     * Update an existing event
     */
    public function updateEvent(Event $event, array $data, User $user): bool
    {
        if (!$user->hasRole('Event Manager') && !$user->hasRole('Administrator') && !$user->hasRole('SuperAdmin')) {
            throw new \Exception('Only Event Managers and Administrators can update events.');
        }

        return DB::transaction(function () use ($event, $data) {
            $event->update([
                'title' => $data['title'] ?? $event->title,
                'description' => $data['description'] ?? $event->description,
                'category' => $data['category'] ?? $event->category,
                'event_date' => $data['event_date'] ?? $event->event_date,
                'event_time' => $data['event_time'] ?? $event->event_time,
                'location' => $data['location'] ?? $event->location,
                'is_deadline' => $data['is_deadline'] ?? $event->is_deadline,
                'is_active' => $data['is_active'] ?? $event->is_active,
            ]);

            Log::info("Event updated: {$event->title} (ID: {$event->id})");

            return true;
        });
    }

    /**
     * Delete an event
     */
    public function deleteEvent(Event $event, User $user): bool
    {
        if (!$user->hasRole('Event Manager') && !$user->hasRole('Administrator') && !$user->hasRole('SuperAdmin')) {
            throw new \Exception('Only Event Managers and Administrators can delete events.');
        }

        $eventTitle = $event->title;
        $eventId = $event->id;

        $deleted = $event->delete();

        if ($deleted) {
            Log::info("Event deleted: {$eventTitle} (ID: {$eventId})");
        }

        return $deleted;
    }

    /**
     * Get events for homepage by category
     */
    public function getEventsForHomepage(): array
    {
        $upcomingEvents = Event::active()
            ->upcoming()
            ->orderBy('event_date', 'asc')
            ->orderBy('event_time', 'asc')
            ->get();

        return [
            'university' => $upcomingEvents->where('category', Event::CATEGORY_UNIVERSITY)->take(5),
            'internal_campus' => $upcomingEvents->where('category', Event::CATEGORY_INTERNAL_CAMPUS)->take(5),
            'external_partners' => $upcomingEvents->where('category', Event::CATEGORY_EXTERNAL_PARTNERS)->take(5),
            'deadlines' => $upcomingEvents->where('is_deadline', true)->take(5),
        ];
    }

    /**
     * Get upcoming deadlines
     */
    public function getUpcomingDeadlines(int $days = 7): \Illuminate\Database\Eloquent\Collection
    {
        return Event::active()
            ->where('is_deadline', true)
            ->whereBetween('event_date', [
                now()->toDateString(),
                now()->addDays($days)->toDateString()
            ])
            ->orderBy('event_date', 'asc')
            ->get();
    }

    /**
     * Get events by category
     */
    public function getEventsByCategory(string $category): \Illuminate\Database\Eloquent\Collection
    {
        return Event::active()
            ->category($category)
            ->upcoming()
            ->orderBy('event_date', 'asc')
            ->orderBy('event_time', 'asc')
            ->get();
    }

    /**
     * Get all events with pagination
     */
    public function getAllEvents(int $perPage = 15): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return Event::with('creator')
            ->orderBy('event_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Search events
     */
    public function searchEvents(string $search, ?string $category = null, ?bool $deadlinesOnly = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = Event::active()
            ->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });

        if ($category) {
            $query->category($category);
        }

        if ($deadlinesOnly !== null) {
            $query->where('is_deadline', $deadlinesOnly);
        }

        return $query->orderBy('event_date', 'asc')->get();
    }

    /**
     * Get event statistics
     */
    public function getEventStatistics(): array
    {
        return [
            'total_events' => Event::count(),
            'active_events' => Event::active()->count(),
            'upcoming_events' => Event::active()->upcoming()->count(),
            'deadlines' => Event::active()->where('is_deadline', true)->upcoming()->count(),
            'university_events' => Event::active()->category(Event::CATEGORY_UNIVERSITY)->upcoming()->count(),
            'internal_events' => Event::active()->category(Event::CATEGORY_INTERNAL_CAMPUS)->upcoming()->count(),
            'external_events' => Event::active()->category(Event::CATEGORY_EXTERNAL_PARTNERS)->upcoming()->count(),
            'events_this_week' => Event::active()
                ->whereBetween('event_date', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])->count(),
            'events_this_month' => Event::active()
                ->whereBetween('event_date', [
                    now()->startOfMonth(),
                    now()->endOfMonth()
                ])->count(),
        ];
    }
}
