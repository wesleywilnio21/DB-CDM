<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\AddContactToEventRequest;
use App\Http\Requests\CreateAndAddContactRequest;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateGuestCountRequest;
use App\Models\Contact;
use App\Models\Event;
use App\Services\EventService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EventController extends Controller
{
    public function __construct(
        private readonly EventService $eventService
    ) {}

    public function index(): View
    {
        $events = Event::withCount(['contacts', 'letters'])
            ->withSum('contacts as guests_count', 'contact_event.guest_count')
            ->orderBy('date', 'desc')
            ->paginate(15);

        return view('events.index', compact('events'));
    }

    public function create(): View
    {
        return view('events.create');
    }

    public function store(StoreEventRequest $request): RedirectResponse
    {
        Event::create($request->validated());

        return redirect()->route('events.index')->with('success', 'Event created successfully.');
    }

    public function show(Event $event): View
    {
        $event->load(['contacts' => function ($q): void {
            $q->orderBy('name');
        }]);
        $allContacts = Contact::orderBy('name')->get();
        $totalAttendees = $event->contacts->count() + $event->contacts->sum('pivot.guest_count');

        return view('events.show', compact('event', 'allContacts', 'totalAttendees'));
    }

    public function addContact(AddContactToEventRequest $request, Event $event): RedirectResponse
    {
        $data = $request->validated();
        $syncData = [];

        foreach ($data['contact_ids'] as $contactId) {
            $syncData[$contactId] = ['guest_count' => (int) $request->input("guest_counts.{$contactId}", 0)];
        }

        $this->eventService->addContacts($event, $syncData);

        return redirect()->route('events.show', $event)
            ->with('success', count($data['contact_ids']).' participants added successfully.');
    }

    public function updateGuestCount(UpdateGuestCountRequest $request, Event $event, Contact $contact): RedirectResponse
    {
        $event->contacts()->updateExistingPivot($contact->id, ['guest_count' => $request->validated()['guest_count']]);

        return back()->with('success', "Guest count updated for {$contact->name}.");
    }

    public function removeContact(Event $event, Contact $contact): RedirectResponse
    {
        $event->contacts()->detach($contact->id);

        return back()->with('success', "{$contact->name} removed from event.");
    }

    public function createAndAddContact(CreateAndAddContactRequest $request, Event $event): RedirectResponse
    {
        $contact = $this->eventService->createAndAddContact($event, $request->validated());

        return redirect()->route('events.show', $event)
            ->with('success', "{$contact->name} created and added to event.");
    }

    public function destroy(Event $event): RedirectResponse
    {
        $this->authorizeSuperAdmin();

        $event->delete();

        return redirect()->route('events.index')->with('success', 'Event deleted successfully.');
    }
}
