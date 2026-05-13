<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Tag;
use App\Models\Event;
use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $query = Contact::with(['tags', 'events']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('tag')) {
            $query->whereHas('tags', function($q) use ($request) {
                $q->where('tags.id', $request->tag);
            });
        }

        if ($request->filled('event')) {
            $query->whereHas('events', function($q) use ($request) {
                $q->where('events.id', $request->event);
            });
        }

        $contacts = $query->paginate(15)->withQueryString();
        $tags = Tag::all();
        $events = Event::all();

        return view('contacts.index', compact('contacts', 'tags', 'events'));
    }

    public function create()
    {
        $tags = Tag::all();
        $events = Event::all();
        return view('contacts.create', compact('tags', 'events'));
    }

    public function store(StoreContactRequest $request)
    {
        $contact = Contact::create($request->validated());
        
        if ($request->has('tags')) {
            $contact->tags()->sync($request->tags);
        }
        
        if ($request->has('events')) {
            $contact->events()->sync($request->events);
        }

        return redirect()->route('contacts.index')->with('success', 'Contact created successfully.');
    }

    public function show(Contact $contact)
    {
        $contact->load(['tags', 'events', 'bloodDonor']);
        return view('contacts.show', compact('contact'));
    }

    public function edit(Contact $contact)
    {
        $tags = Tag::all();
        $events = Event::all();
        $contact->load(['tags', 'events']);
        return view('contacts.edit', compact('contact', 'tags', 'events'));
    }

    public function update(UpdateContactRequest $request, Contact $contact)
    {
        $contact->update($request->validated());
        
        $contact->tags()->sync($request->tags ?? []);
        $contact->events()->sync($request->events ?? []);

        return redirect()->route('contacts.index')->with('success', 'Contact updated successfully.');
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();
        return redirect()->route('contacts.index')->with('success', 'Contact deleted successfully.');
    }

    public function quickAddToEvent(Request $request, Contact $contact)
    {
        $request->validate(['event_id' => 'required|exists:events,id']);
        $contact->events()->syncWithoutDetaching([$request->event_id]);
        return back()->with('success', 'Contact added to event.');
    }
}
