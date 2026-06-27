<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\VolunteerOpportunity;
use App\Models\VolunteerApplication;
use App\Models\VolunteerHour;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class EventController extends Controller
{
    /**
     * Display a listing of events.
     */
    public function index(): View
    {
        $events = Event::with('organizer')
            ->latest()
            ->paginate(15);

        return view('admin.events.index', compact('events'));
    }

    /**
     * Show the form for creating a new event.
     */
    public function create(): View
    {
        return view('admin.events.create');
    }

    /**
     * Store a newly created event.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:events,slug',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'location' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'max_attendees' => 'nullable|integer|min:1',
            'registration_deadline' => 'nullable|date',
            'featured_image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published,cancelled,completed',
            'is_virtual' => 'boolean',
            'virtual_link' => 'nullable|url|max:500',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $validated['organizer_id'] = auth()->id();

        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('events', 'public');
            $validated['featured_image'] = $path;
        }

        Event::create($validated);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event created successfully.');
    }

    /**
     * Display the specified event.
     */
    public function show(Event $event): View
    {
        $event->load(['organizer', 'registrations']);

        return view('admin.events.show', compact('event'));
    }

    /**
     * Show the form for editing the specified event.
     */
    public function edit(Event $event): View
    {
        return view('admin.events.edit', compact('event'));
    }

    /**
     * Update the specified event.
     */
    public function update(Request $request, Event $event): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|unique:events,slug,' . $event->id,
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'location' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'max_attendees' => 'nullable|integer|min:1',
            'registration_deadline' => 'nullable|date',
            'featured_image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published,cancelled,completed',
            'is_virtual' => 'boolean',
            'virtual_link' => 'nullable|url|max:500',
        ]);

        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('events', 'public');
            $validated['featured_image'] = $path;
        }

        $event->update($validated);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event updated successfully.');
    }

    /**
     * Remove the specified event.
     */
    public function destroy(Event $event): RedirectResponse
    {
        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('success', 'Event deleted successfully.');
    }

    /**
     * Display event registrations.
     */
    public function registrations(Event $event): View
    {
        $registrations = EventRegistration::with('user')
            ->where('event_id', $event->id)
            ->latest()
            ->paginate(20);

        return view('admin.events.registrations.index', compact('event', 'registrations'));
    }

    /**
     * Display volunteer opportunities.
     */
    public function volunteerOpportunities(): View
    {
        $opportunities = VolunteerOpportunity::with('creator')
            ->latest()
            ->paginate(15);

        return view('admin.volunteers.opportunities.index', compact('opportunities'));
    }

    /**
     * Display volunteer applications.
     */
    public function volunteerApplications(): View
    {
        $applications = VolunteerApplication::with(['user', 'opportunity'])
            ->latest()
            ->paginate(15);

        return view('admin.volunteers.applications.index', compact('applications'));
    }

    /**
     * Approve a volunteer application.
     */
    public function approveVolunteerApplication(VolunteerApplication $application): RedirectResponse
    {
        $application->approve();

        return redirect()->route('admin.volunteers.applications')
            ->with('success', 'Volunteer application approved successfully.');
    }

    /**
     * Display volunteer hours log.
     */
    public function volunteerHours(): View
    {
        $hours = VolunteerHour::with(['volunteer', 'opportunity'])
            ->latest()
            ->paginate(20);

        return view('admin.volunteers.hours.index', compact('hours'));
    }
}
