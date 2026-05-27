<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Contact;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::withCount(['contacts', 'letters'])
            ->withSum('contacts as guests_count', 'contact_event.guest_count')
            ->orderBy('date', 'desc')
            ->paginate(15);
        return view('events.index', compact('events'));
    }

    public function create()
    {
        return view('events.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        Event::create($validated);
        return redirect()->route('events.index')->with('success', 'Event created successfully.');
    }

    public function show(Event $event)
    {
        $event->load(['contacts' => function($q) {
            $q->orderBy('name');
        }]);
        $allContacts = Contact::orderBy('name')->get();
        
        $totalAttendees = $event->contacts->count() + $event->contacts->sum('pivot.guest_count');

        return view('events.show', compact('event', 'allContacts', 'totalAttendees'));
    }

    public function addContact(Request $request, Event $event)
    {
        $request->validate([
            'contact_ids' => 'required|array',
            'contact_ids.*' => 'exists:contacts,id',
            'guest_counts' => 'nullable|array',
            'guest_counts.*' => 'integer|min:0'
        ]);

        $syncData = [];
        foreach ($request->contact_ids as $contactId) {
            $guestCount = $request->input("guest_counts.{$contactId}", 0);
            $syncData[$contactId] = ['guest_count' => $guestCount];
        }

        $event->contacts()->syncWithoutDetaching($syncData);
        return redirect()->route('events.show', $event)->with('success', count($request->contact_ids) . ' participants added successfully.');
    }

    public function updateGuestCount(Request $request, Event $event, Contact $contact)
    {
        $request->validate([
            'guest_count' => 'required|integer|min:0'
        ]);

        $event->contacts()->updateExistingPivot($contact->id, ['guest_count' => $request->guest_count]);
        
        return back()->with('success', "Guest count updated for {$contact->name}.");
    }

    public function removeContact(Event $event, Contact $contact)
    {
        $event->contacts()->detach($contact->id);
        return back()->with('success', "{$contact->name} removed from event.");
    }

    public function createAndAddContact(Request $request, Event $event)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'organization' => 'nullable|string|max:255',
            'guest_count' => 'nullable|integer|min:0'
        ]);

        $contact = Contact::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'organization' => $validated['organization']
        ]);

        $contact->phones()->create([
            'phone' => $validated['phone'],
            'is_primary' => true
        ]);

        $event->contacts()->attach($contact->id, ['guest_count' => $validated['guest_count'] ?? 0]);

        return redirect()->route('events.show', $event)->with('success', "{$contact->name} created and added to event.");
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('events.index')->with('success', 'Event deleted successfully.');
    }
}
