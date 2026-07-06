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
use App\Services\ContactService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ContactController extends Controller
{
    public function __construct(
        private readonly ContactService $contactService
    ) {}

    public function index(Request $request): View
    {
        $contacts = Contact::with(['tags', 'events'])
            ->filter([
                'search' => $request->input('search'),
                'tag' => $request->input('tag'),
                'event' => $request->input('event'),
            ])
            ->paginate(15)
            ->withQueryString();

        $tags = Tag::all();
        $events = Event::all();

        return view('contacts.index', compact('contacts', 'tags', 'events'));
    }

    public function create(): View
    {
        $tags = Tag::all();
        $events = Event::all();

        return view('contacts.create', compact('tags', 'events'));
    }

    public function store(StoreContactRequest $request): RedirectResponse
    {
        $this->contactService->createContact(
            $request->except(['phones', 'tags', 'events']),
            $request->input('phones'),
            $request->input('tags'),
            $request->input('events')
        );

        return redirect()->route('contacts.index')->with('success', 'Contact created successfully.');
    }

    public function show(Contact $contact): View
    {
        $contact->load(['tags', 'events', 'bloodDonor']);

        return view('contacts.show', compact('contact'));
    }

    public function edit(Contact $contact): View
    {
        $tags = Tag::all();
        $events = Event::all();
        $contact->load(['tags', 'events']);

        return view('contacts.edit', compact('contact', 'tags', 'events'));
    }

    public function update(UpdateContactRequest $request, Contact $contact): RedirectResponse
    {
        $this->contactService->updateContact(
            $contact,
            $request->except(['phones', 'tags', 'events']),
            $request->input('phones'),
            $request->input('tags'),
            $request->input('events')
        );

        return redirect()->route('contacts.index')->with('success', 'Contact updated successfully.');
    }

    public function destroy(Contact $contact): RedirectResponse
    {
        $this->authorizeSuperAdmin();

        $name = $contact->name;
        ActivityLogger::log('deleted', $contact, "Deleted contact: {$name}");
        $contact->delete();

        return redirect()->route('contacts.index')->with('success', 'Contact deleted successfully.');
    }

    // --- Excel Features ---

    public function export(): StreamedResponse|Response
    {
        return (new ContactsExport)->download();
    }

    public function downloadTemplate(): StreamedResponse|Response
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
        $this->contactService->quickAddToEvent($contact, (int) $request->event_id);

        return back()->with('success', 'Contact added to event.');
    }
}
