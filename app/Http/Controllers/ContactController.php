<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exports\ContactsExport;
use App\Http\Requests\ImportFileRequest;
use App\Http\Requests\QuickAddToEventRequest;
use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Imports\ContactsImport;
use App\Models\Contact;
use App\Models\Event;
use App\Models\Tag;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(\Illuminate\Http\Request $request): View
    {
        $query = Contact::with(['tags', 'events']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('phones', function ($pq) use ($search): void {
                        $pq->where('phone', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('tag')) {
            $query->whereHas('tags', function ($q) use ($request): void {
                $q->where('tags.id', $request->tag);
            });
        }

        if ($request->filled('event')) {
            $query->whereHas('events', function ($q) use ($request): void {
                $q->where('events.id', $request->event);
            });
        }

        $contacts = $query->paginate(15)->withQueryString();
        $tags     = Tag::all();
        $events   = Event::all();

        return view('contacts.index', compact('contacts', 'tags', 'events'));
    }

    public function create(): View
    {
        $tags   = Tag::all();
        $events = Event::all();

        return view('contacts.create', compact('tags', 'events'));
    }

    public function store(StoreContactRequest $request): RedirectResponse
    {
        $contact = Contact::create($request->except('phones'));

        if ($request->has('phones')) {
            $phonesData = array_map(function ($phone, $index) {
                return ['phone' => $phone, 'is_primary' => $index === 0];
            }, $request->phones, array_keys($request->phones));
            $contact->phones()->createMany($phonesData);
        }

        if ($request->has('tags')) {
            $contact->tags()->sync($request->tags);
        }

        if ($request->has('events')) {
            $contact->events()->sync($request->events);
        }

        ActivityLogger::log('created', $contact, "Created contact: {$contact->name}");

        return redirect()->route('contacts.index')->with('success', 'Contact created successfully.');
    }

    public function show(Contact $contact): View
    {
        $contact->load(['tags', 'events', 'bloodDonor']);

        return view('contacts.show', compact('contact'));
    }

    public function edit(Contact $contact): View
    {
        $tags   = Tag::all();
        $events = Event::all();
        $contact->load(['tags', 'events']);

        return view('contacts.edit', compact('contact', 'tags', 'events'));
    }

    public function update(UpdateContactRequest $request, Contact $contact): RedirectResponse
    {
        $oldData           = $contact->only(['name', 'email', 'organization']);
        $oldData['phones'] = $contact->phones->pluck('phone')->toArray();

        $contact->update($request->except('phones'));

        if ($request->has('phones')) {
            $contact->phones()->delete();
            $phonesData = array_map(function ($phone, $index) {
                return ['phone' => $phone, 'is_primary' => $index === 0];
            }, $request->phones, array_keys($request->phones));
            $contact->phones()->createMany($phonesData);
        }

        $contact->tags()->sync($request->tags ?? []);
        $contact->events()->sync($request->events ?? []);

        $newData           = $contact->only(['name', 'email', 'organization']);
        $newData['phones'] = $request->phones;

        ActivityLogger::log('updated', $contact, "Updated contact: {$contact->name}", [
            'old' => $oldData,
            'new' => $newData,
        ]);

        return redirect()->route('contacts.index')->with('success', 'Contact updated successfully.');
    }

    public function destroy(Contact $contact): RedirectResponse
    {
        $name = $contact->name;
        ActivityLogger::log('deleted', $contact, "Deleted contact: {$name}");
        $contact->delete();

        return redirect()->route('contacts.index')->with('success', 'Contact deleted successfully.');
    }

    // --- Excel Features ---

    public function export(): \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\Response
    {
        return (new ContactsExport)->download();
    }

    public function downloadTemplate(): \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\Response
    {
        return (new ContactsExport)->template();
    }

    public function import(ImportFileRequest $request): RedirectResponse
    {
        $count = (new ContactsImport)->upload($request->file('file')->getRealPath());

        return back()->with('success', "Successfully imported {$count} contacts.");
    }

    public function quickAddToEvent(QuickAddToEventRequest $request, Contact $contact): RedirectResponse
    {
        $contact->events()->syncWithoutDetaching([$request->event_id]);

        return back()->with('success', 'Contact added to event.');
    }
}
