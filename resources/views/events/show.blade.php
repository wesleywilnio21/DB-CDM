<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-900 leading-tight tracking-tight">
            {{ __('Event Details') }}: {{ $event->name }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if (session('success'))
                <div class="text-green-700 bg-green-50 border border-green-200 p-4 rounded-xl flex items-center">
                    <svg class="w-5 h-5 mr-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            <!-- Event Information Card -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 md:p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="text-sm font-medium text-gray-500 mb-1">Date</div>
                            <div class="text-lg text-gray-900 font-semibold">{{ $event->date->format('l, F j, Y') }}</div>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-500 mb-1">Location</div>
                            <div class="text-lg text-gray-900 font-semibold">{{ $event->location ?: '-' }}</div>
                        </div>
                        <div class="md:col-span-2">
                            <div class="text-sm font-medium text-gray-500 mb-1">Description</div>
                            <div class="text-base text-gray-800">{{ $event->description ?: '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendees & Add Section Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Attendees List -->
                <div class="lg:col-span-2 bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-xl font-bold text-gray-900 tracking-tight">Attendees ({{ $event->contacts->count() }})</h3>
                    </div>
                    <div class="p-6">
                        @if($event->contacts->count() > 0)
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach($event->contacts as $contact)
                                    <div class="flex items-center p-3 rounded-2xl border border-gray-100 bg-gray-50/50">
                                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gradient-to-tr from-gray-100 to-gray-200 flex items-center justify-center text-gray-600 font-medium">
                                            {{ substr($contact->name, 0, 1) }}
                                        </div>
                                        <div class="ml-3">
                                            <a href="{{ route('contacts.show', $contact) }}" class="text-sm font-semibold text-gray-900 hover:text-indigo-600 transition-colors">{{ $contact->name }}</a>
                                            <div class="text-xs text-gray-500">{{ $contact->organization ?: 'No Organization' }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <p>No attendees have been added to this event yet.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Add Contacts Form -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden h-fit">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-bold text-gray-900 tracking-tight">Add Contacts</h3>
                        <p class="text-sm text-gray-500 mt-1">Select multiple contacts to add</p>
                    </div>
                    <form action="{{ route('events.add-contact', $event) }}" method="POST" class="p-6">
                        @csrf
                        <div class="max-h-64 overflow-y-auto pr-2 mb-4 space-y-2 border border-gray-100 rounded-xl p-3 bg-gray-50/50">
                            @forelse($allContacts as $contact)
                                <label class="flex items-center p-2 hover:bg-gray-100 rounded-lg cursor-pointer transition-colors">
                                    <input type="checkbox" name="contact_ids[]" value="{{ $contact->id }}" 
                                           class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                           {{ $event->contacts->contains($contact->id) ? 'checked disabled' : '' }}>
                                    <span class="ml-3 text-sm font-medium {{ $event->contacts->contains($contact->id) ? 'text-gray-400' : 'text-gray-900' }}">
                                        {{ $contact->name }}
                                        <span class="text-xs text-gray-500 block">{{ $contact->phone }}</span>
                                    </span>
                                </label>
                            @empty
                                <div class="text-sm text-gray-500 text-center py-2">No contacts available.</div>
                            @endforelse
                        </div>
                        <x-input-error :messages="$errors->get('contact_ids')" class="mb-4" />
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-gray-900 border border-transparent rounded-full font-medium text-sm text-white hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-all shadow-sm">
                            Add Selected Contacts
                        </button>
                    </form>
                </div>
                
            </div>
        </div>
    </div>
</x-app-layout>
