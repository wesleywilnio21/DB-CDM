<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-900 leading-tight tracking-tight">
            {{ __('Event Details') }}: {{ $event->name }}
        </h2>
    </x-slot>

    <div class="py-10" x-data="{ showAddModal: false, activeTab: 'existing' }">
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
                    
                    <div class="mt-6 pt-6 border-t border-gray-100 flex gap-3">
                        <a href="{{ route('letters.index', $event) }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-indigo-600 border border-transparent rounded-full font-medium text-sm text-white hover:bg-indigo-700 focus:outline-none transition-all shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            Manage Letters
                        </a>
                    </div>
                </div>
            </div>

            <!-- Participants Section -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 tracking-tight">Participants</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            <span class="font-semibold text-gray-900">{{ $event->contacts->count() }}</span> Registered Contacts 
                            | <span class="font-bold text-indigo-600">{{ $totalAttendees }}</span> Total Attendees (with guests)
                        </p>
                    </div>
                    <button @click="showAddModal = true" class="inline-flex items-center justify-center px-6 py-3 bg-gray-900 border border-transparent rounded-full font-semibold text-sm text-white hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-all shadow-md">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Add Participants
                    </button>
                </div>
                
                <div class="p-6">
                    @if($event->contacts->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($event->contacts as $contact)
                                <div class="relative flex flex-col p-4 rounded-2xl border border-gray-100 bg-gray-50/50 hover:bg-white hover:shadow-md transition-all group">
                                    
                                    <!-- Delete Button (Top Right) -->
                                    <form action="{{ route('events.remove-contact', [$event, $contact]) }}" method="POST" class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Remove this participant?')" class="text-gray-400 hover:text-red-500 transition-colors bg-white rounded-full p-1 shadow-sm border border-gray-100">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </form>

                                    <div class="flex items-center mb-3">
                                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gradient-to-tr from-indigo-100 to-purple-100 flex items-center justify-center text-indigo-700 font-bold text-lg">
                                            {{ substr($contact->name, 0, 1) }}
                                        </div>
                                        <div class="ml-3 flex-1 overflow-hidden">
                                            <a href="{{ route('contacts.show', $contact) }}" class="text-sm font-bold text-gray-900 hover:text-indigo-600 transition-colors truncate block">{{ $contact->name }}</a>
                                            <div class="text-xs text-gray-500 truncate">{{ $contact->organization ?: 'No Organization' }}</div>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-auto border-t border-gray-100 pt-3 flex items-center justify-between">
                                        <div class="text-sm font-bold text-gray-900">Guests:</div>
                                        <!-- Update Guest Count Form -->
                                        <form action="{{ route('events.update-guest-count', [$event, $contact]) }}" method="POST" class="flex items-center">
                                            @csrf
                                            @method('PATCH')
                                            <div class="flex items-center bg-white border border-gray-200 rounded-lg overflow-hidden">
                                                <button type="button" onclick="let inp = this.nextElementSibling; if(inp.value > 0) { inp.value--; inp.form.submit(); }" class="px-2 py-1 text-gray-500 hover:bg-gray-100">-</button>
                                                <input type="number" name="guest_count" value="{{ $contact->pivot->guest_count }}" min="0" placeholder="0" onchange="this.form.submit()" class="w-12 text-center text-sm font-semibold border-0 py-1 focus:ring-0 text-gray-900 bg-white">
                                                <button type="button" onclick="let inp = this.previousElementSibling; inp.value++; inp.form.submit();" class="px-2 py-1 text-gray-500 hover:bg-gray-100">+</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 text-gray-500 bg-gray-50/50 rounded-2xl border border-dashed border-gray-200">
                            <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            <p class="text-lg font-medium text-gray-900">No participants yet</p>
                            <p class="mt-1">Add participants to start tracking attendance.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Add Participants Modal -->
        <div x-show="showAddModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div x-show="showAddModal" x-transition.opacity class="fixed inset-0 transition-opacity bg-gray-900 opacity-50"></div>

                <div x-show="showAddModal" x-transition.scale.origin.bottom class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
                    <div class="bg-white px-6 pt-6 pb-4 sm:p-8 sm:pb-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-bold text-gray-900">Add Participants</h3>
                            <button @click="showAddModal = false" type="button" class="text-gray-400 hover:text-gray-500">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>

                        <!-- Tabs -->
                        <div class="flex border-b border-gray-200 mb-6">
                            <button @click="activeTab = 'existing'" :class="{ 'border-gray-900 text-gray-900': activeTab === 'existing', 'border-transparent text-gray-500 hover:text-gray-700': activeTab !== 'existing' }" class="flex-1 pb-3 text-center border-b-2 font-semibold text-sm transition-colors">
                                Add from Existing Contacts
                            </button>
                            <button @click="activeTab = 'new'" :class="{ 'border-gray-900 text-gray-900': activeTab === 'new', 'border-transparent text-gray-500 hover:text-gray-700': activeTab !== 'new' }" class="flex-1 pb-3 text-center border-b-2 font-semibold text-sm transition-colors">
                                Create New Contact
                            </button>
                        </div>

                        <!-- Tab Content: Existing Contacts -->
                        <div x-show="activeTab === 'existing'" x-data="{ search: '' }">
                            <form action="{{ route('events.add-contact', $event) }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <input type="text" x-model="search" placeholder="Search contacts..." class="w-full border-gray-200 focus:border-gray-400 rounded-xl shadow-sm text-sm text-gray-900">
                                </div>
                                <div class="max-h-72 overflow-y-auto pr-2 mb-6 space-y-2">
                                    @forelse($allContacts as $contact)
                                        <div x-show="search === '' || '{{ strtolower($contact->name) }}'.includes(search.toLowerCase()) || '{{ strtolower($contact->phone) }}'.includes(search.toLowerCase())" class="flex items-center justify-between p-3 border border-gray-100 rounded-xl hover:bg-gray-50 transition-colors">
                                            <label class="flex items-center flex-1 cursor-pointer">
                                                <input type="checkbox" name="contact_ids[]" value="{{ $contact->id }}" class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                                <div class="ml-3">
                                                    <div class="text-sm font-bold text-gray-900">{{ $contact->name }}</div>
                                                    <div class="text-xs text-gray-500">{{ $contact->phone }}</div>
                                                </div>
                                            </label>
                                            <div class="flex items-center ml-4">
                                                <span class="text-sm font-bold text-gray-900 mr-2">+ Guests (excl. contact)</span>
                                                <input type="number" name="guest_counts[{{ $contact->id }}]" value="0" min="0" placeholder="0" class="w-16 border-gray-200 rounded-lg text-sm text-center py-1">
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-4 text-gray-500 text-sm">No contacts available.</div>
                                    @endforelse
                                </div>
                                <div class="text-xs text-gray-500 mb-4 text-center bg-gray-50 rounded-lg p-2 border border-gray-100">
                                    <svg class="w-4 h-4 inline-block mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Each contact counts as 1 person. Enter any additional guests they bring.
                                </div>
                                <div class="flex justify-end gap-3">
                                    <button type="button" @click="showAddModal = false" class="px-5 py-2.5 border border-gray-200 rounded-full font-medium text-sm text-gray-700 hover:bg-gray-50">Cancel</button>
                                    <button type="submit" class="px-5 py-2.5 bg-gray-900 border border-transparent rounded-full font-medium text-sm text-white hover:bg-gray-800">Add Selected</button>
                                </div>
                            </form>
                        </div>

                        <!-- Tab Content: New Contact -->
                        <div x-show="activeTab === 'new'" style="display: none;">
                            <form action="{{ route('events.create-contact', $event) }}" method="POST">
                                @csrf
                                <div class="space-y-4 mb-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-900 mb-1">Full Name <span class="text-red-500">*</span></label>
                                        <input type="text" name="name" required class="w-full border-gray-200 rounded-xl shadow-sm text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-900 mb-1">Phone Number <span class="text-red-500">*</span></label>
                                        <input type="text" name="phone" required class="w-full border-gray-200 rounded-xl shadow-sm text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-900 mb-1">Email (Optional)</label>
                                        <input type="email" name="email" class="w-full border-gray-200 rounded-xl shadow-sm text-sm">
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-900 mb-1">Organization (Optional)</label>
                                            <input type="text" name="organization" class="w-full border-gray-200 rounded-xl shadow-sm text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-900 mb-1">Additional Guests</label>
                                            <input type="number" name="guest_count" value="0" min="0" class="w-full border-gray-200 rounded-xl shadow-sm text-sm">
                                        </div>
                                    </div>
                                </div>
                                <div class="flex justify-end gap-3">
                                    <button type="button" @click="showAddModal = false" class="px-5 py-2.5 border border-gray-200 rounded-full font-medium text-sm text-gray-700 hover:bg-gray-50">Cancel</button>
                                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 border border-transparent rounded-full font-medium text-sm text-white hover:bg-indigo-700">Save & Add to Event</button>
                                </div>
                            </form>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
