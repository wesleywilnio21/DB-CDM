<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Contact Details') }}: {{ $contact->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-4 text-green-600 bg-green-100 p-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><strong>Name:</strong> {{ $contact->name }}</div>
                    <div><strong>Phone:</strong> {{ $contact->phone }}</div>
                    <div><strong>Email:</strong> {{ $contact->email ?: '-' }}</div>
                    <div><strong>Organization:</strong> {{ $contact->organization ?: '-' }}</div>
                    <div class="md:col-span-2"><strong>Address:</strong> {{ $contact->address ?: '-' }}</div>
                    <div class="md:col-span-2"><strong>Notes:</strong> {{ $contact->notes ?: '-' }}</div>
                    
                    <div class="md:col-span-2">
                        <strong>Tags:</strong>
                        @foreach($contact->tags as $tag)
                            <span class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2">{{ $tag->name }}</span>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Events Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Attended Events</h3>
                    <ul class="list-disc pl-5 mb-4">
                        @forelse($contact->events as $event)
                            <li><a href="{{ route('events.show', $event) }}" class="text-blue-600 hover:underline">{{ $event->name }}</a> - {{ $event->date->format('Y-m-d') }}</li>
                        @empty
                            <li class="text-gray-500">No events attended.</li>
                        @endforelse
                    </ul>

                    <form action="{{ route('contacts.add-event', $contact) }}" method="POST" class="flex gap-4">
                        @csrf
                        <div class="flex-1">
                            <select name="event_id" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">-- Select Event --</option>
                                @foreach(\App\Models\Event::all() as $evt)
                                    @if(!$contact->events->contains($evt->id))
                                        <option value="{{ $evt->id }}">{{ $evt->name }} ({{ $evt->date->format('Y-m-d') }})</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <x-primary-button>Quick Add to Event</x-primary-button>
                    </form>
                </div>
            </div>

            <!-- Blood Donor Info -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Blood Donor Information</h3>
                    @if($contact->bloodDonor)
                        <div><strong>Blood Type:</strong> {{ $contact->bloodDonor->blood_type }}{{ $contact->bloodDonor->rhesus }}</div>
                        <div><strong>Last Donation Date:</strong> {{ $contact->bloodDonor->last_donation_date ? $contact->bloodDonor->last_donation_date->format('Y-m-d') : '-' }}</div>
                        <div><strong>Next Eligible Date:</strong> {{ $contact->bloodDonor->next_eligible_date ? $contact->bloodDonor->next_eligible_date->format('Y-m-d') : '-' }}</div>
                        <div class="mt-4">
                            <a href="{{ route('blood-donors.edit', $contact->bloodDonor) }}" class="text-indigo-600 hover:underline">Edit Donor Info</a>
                        </div>
                    @else
                        <p class="text-gray-500 mb-4">This contact is not registered as a blood donor.</p>
                        <a href="{{ route('blood-donors.create') }}" class="text-indigo-600 hover:underline">Register as Donor</a>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
