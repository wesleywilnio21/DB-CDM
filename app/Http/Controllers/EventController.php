<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Contact;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::withCount('contacts')->orderBy('date', 'desc')->paginate(15);
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
        $event->load('contacts');
        $allContacts = Contact::whereNotIn('id', $event->contacts->pluck('id'))->get();
        return view('events.show', compact('event', 'allContacts'));
    }

    public function addContact(Request $request, Event $event)
    {
        $request->validate([
            'contact_ids' => 'required|array',
            'contact_ids.*' => 'exists:contacts,id'
        ]);

        $event->contacts()->syncWithoutDetaching($request->contact_ids);
        return redirect()->route('events.show', $event)->with('success', count($request->contact_ids) . ' contacts added successfully.');
    }
    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('events.index')->with('success', 'Event deleted successfully.');
    }
}
