<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-900 leading-tight tracking-tight">
            {{ __('All Contacts') }}
        </h2>
    </x-slot>

    <div class="py-10" x-data="{ 
        showCreate: {{ $errors->any() && !old('_method') && !old('tag_name') ? 'true' : 'false' }}, 
        showEdit: {{ $errors->any() && old('_method') == 'PATCH' ? 'true' : 'false' }}, 
        showImport: false,
        showEvents: false,
        showTags: {{ $errors->has('tag_name') ? 'true' : 'false' }},
        editContact: { id: '', name: '', phone: '', email: '', organization: '', address: '', notes: '', birthdate: '', tags: [] },
        eventsContact: { id: '', name: '', events: [] }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if (session('success'))
                <div class="text-green-700 bg-green-50 border border-green-200 p-4 rounded-xl flex items-center">
                    <svg class="w-5 h-5 mr-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 md:p-8 border-b border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <h3 class="text-xl font-bold text-gray-900 tracking-tight">Manage Contacts</h3>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('contacts.export') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-emerald-50 border border-emerald-200 rounded-full font-medium text-sm text-emerald-700 hover:bg-emerald-100 transition-all shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Export
                        </a>
                        <button @click="showImport = true" class="inline-flex items-center justify-center px-4 py-2.5 bg-blue-50 border border-blue-200 rounded-full font-medium text-sm text-blue-700 hover:bg-blue-100 transition-all shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            Import
                        </button>
                        <button @click="showTags = true" class="inline-flex items-center justify-center px-4 py-2.5 bg-white border border-gray-200 rounded-full font-medium text-sm text-gray-700 hover:bg-gray-50 focus:outline-none transition-all shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                            Manage Tags
                        </button>
                        <button @click="showCreate = true" class="inline-flex items-center justify-center px-5 py-2.5 bg-gray-900 border border-transparent rounded-full font-medium text-sm text-white hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-all shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Add New Contact
                        </button>
                    </div>
                </div>
                <div class="p-6 md:p-8">
                    <!-- Search and Filter Form -->
                    <form method="GET" action="{{ route('contacts.index') }}" class="mb-8 flex flex-col md:flex-row gap-4 items-end bg-gray-50 p-4 rounded-2xl border border-gray-100">
                        <div class="flex-1 w-full">
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Name/Phone</label>
                            <input id="search" name="search" type="text" class="block w-full border-gray-200 focus:border-gray-400 focus:ring-0 rounded-xl shadow-sm text-sm text-gray-900" value="{{ request('search') }}" placeholder="e.g. John Doe" />
                        </div>
                        
                        <div class="w-full md:w-48">
                            <label for="tag" class="block text-sm font-medium text-gray-700 mb-1">Filter by Tag</label>
                            <select name="tag" id="tag" class="block w-full border-gray-200 focus:border-gray-400 focus:ring-0 rounded-xl shadow-sm text-sm text-gray-900">
                                <option value="">All Tags</option>
                                @foreach($tags as $tag)
                                    <option value="{{ $tag->id }}" {{ request('tag') == $tag->id ? 'selected' : '' }}>{{ $tag->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="w-full md:w-48">
                            <label for="event" class="block text-sm font-medium text-gray-700 mb-1">Filter by Event</label>
                            <select name="event" id="event" class="block w-full border-gray-200 focus:border-gray-400 focus:ring-0 rounded-xl shadow-sm text-sm text-gray-900">
                                <option value="">All Events</option>
                                @foreach($events as $event)
                                    <option value="{{ $event->id }}" {{ request('event') == $event->id ? 'selected' : '' }}>{{ $event->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center gap-2 w-full md:w-auto">
                            <button type="submit" class="inline-flex items-center justify-center px-4 py-2.5 bg-white border border-gray-200 rounded-xl font-medium text-sm text-gray-900 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 transition-all shadow-sm w-full md:w-auto">
                                Search
                            </button>
                            <a href="{{ route('contacts.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-transparent border border-transparent rounded-xl font-medium text-sm text-gray-500 hover:text-gray-900 transition-all">Clear</a>
                        </div>
                    </form>

                    <div class="overflow-hidden border border-gray-100 rounded-2xl">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50/50">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Contact</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Contact Info</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tags & History</th>
                                    <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse($contacts as $contact)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gradient-to-tr from-gray-100 to-gray-200 flex items-center justify-center text-gray-600 font-medium">
                                                    {{ substr($contact->name, 0, 1) }}
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-semibold text-gray-900">{{ $contact->name }}</div>
                                                    <div class="text-xs text-gray-500">{{ $contact->organization ?: 'No Organization' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $contact->phone }}</div>
                                            <div class="text-xs text-gray-500">{{ $contact->email ?: 'No Email' }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                                @forelse($contact->tags as $t)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-700 border border-gray-200">{{ $t->name }}</span>
                                                @empty
                                                    <span class="text-xs text-gray-400">No tags</span>
                                                @endforelse
                                            </div>
                                            <!-- Event History Shortcut -->
                                            <button @click="
                                                    eventsContact = { id: {{ $contact->id }}, name: '{{ addslashes($contact->name) }}', events: {{ json_encode($contact->events->map(function($e){ return ['id'=>$e->id, 'name'=>$e->name, 'date'=>$e->date->format('M d, Y')]; })) }} }; 
                                                    showEvents = true;
                                                "
                                                class="inline-flex items-center text-xs font-medium text-indigo-600 hover:text-indigo-900 transition-colors">
                                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                Event History ({{ $contact->events->count() }})
                                            </button>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center justify-end gap-3">
                                                <a href="{{ route('contacts.show', $contact) }}" class="text-gray-500 hover:text-gray-900 transition-colors">View</a>
                                                
                                                <button @click="
                                                        editContact = {{ json_encode($contact->only(['id','name','phone','email','organization','address','notes','birthdate'])) }}; 
                                                        editContact.tags = {{ json_encode($contact->tags->pluck('id')) }};
                                                        if(editContact.birthdate) editContact.birthdate = editContact.birthdate.split('T')[0];
                                                        showEdit = true;
                                                    " 
                                                    class="text-indigo-600 hover:text-indigo-900 transition-colors">
                                                    Edit
                                                </button>

                                                <form action="{{ route('contacts.destroy', $contact) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this contact?');" class="inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-500 hover:text-red-700 transition-colors">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                            <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                            No contacts found. Get started by adding a new one.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-6">
                        {{ $contacts->links() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Event History & Quick Add Modal -->
        <div x-show="showEvents" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <!-- Backdrop -->
                <div x-show="showEvents" x-transition.opacity class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-900 opacity-50"></div>
                </div>

                <!-- Modal Panel -->
                <div x-show="showEvents" x-transition.scale.origin.bottom class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl w-full">
                    <div class="bg-white px-6 pt-6 pb-4 sm:p-8 sm:pb-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-bold text-gray-900 tracking-tight">Event History: <span x-text="eventsContact.name" class="text-indigo-600"></span></h3>
                            <button @click="showEvents = false" type="button" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                                <span class="sr-only">Close</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>

                        <!-- Events List -->
                        <div class="mb-6 max-h-60 overflow-y-auto pr-2 border-b border-gray-100 pb-4">
                            <template x-if="eventsContact.events.length === 0">
                                <p class="text-sm text-gray-500 text-center py-4">No events attended yet.</p>
                            </template>
                            <template x-for="ev in eventsContact.events" :key="ev.id">
                                <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                                    <div class="flex items-center gap-3">
                                        <div class="bg-indigo-50 p-2 rounded-lg text-indigo-500">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900" x-text="ev.name"></p>
                                            <p class="text-xs text-gray-500" x-text="ev.date"></p>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Add to New Event Form -->
                        <form method="POST" :action="`/contacts/${eventsContact.id}/add-event`" class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                            @csrf
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Quick Add to Event</label>
                            <div class="flex gap-2">
                                <select name="event_id" required class="flex-1 border-gray-200 focus:border-indigo-400 focus:ring-0 rounded-xl shadow-sm text-sm text-gray-900">
                                    <option value="">-- Select Event --</option>
                                    @foreach($events as $event)
                                        <template x-if="!eventsContact.events.find(e => e.id == {{ $event->id }})">
                                            <option value="{{ $event->id }}">{{ $event->name }} ({{ $event->date->format('M d') }})</option>
                                        </template>
                                    @endforeach
                                </select>
                                <button type="submit" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-xl font-medium text-sm text-white hover:bg-indigo-700 focus:outline-none transition-all shadow-sm">
                                    Add
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Modal -->
        <div x-show="showCreate" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <!-- Backdrop -->
                <div x-show="showCreate" x-transition.opacity class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-900 opacity-50"></div>
                </div>

                <!-- Modal Panel -->
                <div x-show="showCreate" x-transition.scale.origin.bottom class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
                    <div class="bg-white px-6 pt-6 pb-4 sm:p-8 sm:pb-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-bold text-gray-900 tracking-tight" id="modal-title">Add New Contact</h3>
                            <button @click="showCreate = false" type="button" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                                <span class="sr-only">Close</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                        <form method="POST" action="{{ route('contacts.store') }}">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" value="{{ old('name') }}" required class="block w-full border-gray-200 focus:border-gray-400 focus:ring-0 rounded-xl shadow-sm text-sm text-gray-900" />
                                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone <span class="text-red-500">*</span></label>
                                    <input type="text" name="phone" value="{{ old('phone') }}" required class="block w-full border-gray-200 focus:border-gray-400 focus:ring-0 rounded-xl shadow-sm text-sm text-gray-900" />
                                    <x-input-error :messages="$errors->get('phone')" class="mt-1" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                    <input type="email" name="email" value="{{ old('email') }}" class="block w-full border-gray-200 focus:border-gray-400 focus:ring-0 rounded-xl shadow-sm text-sm text-gray-900" />
                                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Organization</label>
                                    <input type="text" name="organization" value="{{ old('organization') }}" class="block w-full border-gray-200 focus:border-gray-400 focus:ring-0 rounded-xl shadow-sm text-sm text-gray-900" />
                                    <x-input-error :messages="$errors->get('organization')" class="mt-1" />
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Birthdate (Optional)</label>
                                    <input type="date" name="birthdate" value="{{ old('birthdate') }}" class="block w-full border-gray-200 focus:border-gray-400 focus:ring-0 rounded-xl shadow-sm text-sm text-gray-900" />
                                    <x-input-error :messages="$errors->get('birthdate')" class="mt-1" />
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                    <textarea name="address" rows="2" class="block w-full border-gray-200 focus:border-gray-400 focus:ring-0 rounded-xl shadow-sm text-sm text-gray-900">{{ old('address') }}</textarea>
                                    <x-input-error :messages="$errors->get('address')" class="mt-1" />
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                    <textarea name="notes" rows="2" class="block w-full border-gray-200 focus:border-gray-400 focus:ring-0 rounded-xl shadow-sm text-sm text-gray-900">{{ old('notes') }}</textarea>
                                    <x-input-error :messages="$errors->get('notes')" class="mt-1" />
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tags</label>
                                    <div class="flex flex-wrap gap-2">
                                        @forelse($tags as $tag)
                                            <label class="inline-flex items-center p-2 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                                <input type="checkbox" name="tags[]" value="{{ $tag->id }}" class="w-4 h-4 text-gray-900 border-gray-300 rounded focus:ring-gray-900" {{ is_array(old('tags')) && in_array($tag->id, old('tags')) ? 'checked' : '' }}>
                                                <span class="ml-2 text-sm text-gray-700">{{ $tag->name }}</span>
                                            </label>
                                        @empty
                                            <span class="text-sm text-gray-500 italic">No tags available. Create some via "Manage Tags" first.</span>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                            <div class="mt-8 flex justify-end gap-3">
                                <button type="button" @click="showCreate = false" class="px-5 py-2.5 border border-gray-200 rounded-full font-medium text-sm text-gray-700 hover:bg-gray-50 focus:outline-none transition-all shadow-sm">Cancel</button>
                                <button type="submit" class="px-5 py-2.5 bg-gray-900 border border-transparent rounded-full font-medium text-sm text-white hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-all shadow-sm">Save Contact</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div x-show="showEdit" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <!-- Backdrop -->
                <div x-show="showEdit" x-transition.opacity class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-900 opacity-50"></div>
                </div>

                <!-- Modal Panel -->
                <div x-show="showEdit" x-transition.scale.origin.bottom class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
                    <div class="bg-white px-6 pt-6 pb-4 sm:p-8 sm:pb-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-bold text-gray-900 tracking-tight" id="modal-title">Edit Contact</h3>
                            <button @click="showEdit = false" type="button" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                                <span class="sr-only">Close</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                        <form method="POST" :action="`/contacts/${editContact.id}`">
                            @csrf
                            @method('PATCH')
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" x-model="editContact.name" required class="block w-full border-gray-200 focus:border-gray-400 focus:ring-0 rounded-xl shadow-sm text-sm text-gray-900" />
                                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone <span class="text-red-500">*</span></label>
                                    <input type="text" name="phone" x-model="editContact.phone" required class="block w-full border-gray-200 focus:border-gray-400 focus:ring-0 rounded-xl shadow-sm text-sm text-gray-900" />
                                    <x-input-error :messages="$errors->get('phone')" class="mt-1" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                    <input type="email" name="email" x-model="editContact.email" class="block w-full border-gray-200 focus:border-gray-400 focus:ring-0 rounded-xl shadow-sm text-sm text-gray-900" />
                                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Organization</label>
                                    <input type="text" name="organization" x-model="editContact.organization" class="block w-full border-gray-200 focus:border-gray-400 focus:ring-0 rounded-xl shadow-sm text-sm text-gray-900" />
                                    <x-input-error :messages="$errors->get('organization')" class="mt-1" />
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Birthdate (Optional)</label>
                                    <input type="date" name="birthdate" x-model="editContact.birthdate" class="block w-full border-gray-200 focus:border-gray-400 focus:ring-0 rounded-xl shadow-sm text-sm text-gray-900" />
                                    <x-input-error :messages="$errors->get('birthdate')" class="mt-1" />
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                    <textarea name="address" x-model="editContact.address" rows="2" class="block w-full border-gray-200 focus:border-gray-400 focus:ring-0 rounded-xl shadow-sm text-sm text-gray-900"></textarea>
                                    <x-input-error :messages="$errors->get('address')" class="mt-1" />
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                    <textarea name="notes" x-model="editContact.notes" rows="2" class="block w-full border-gray-200 focus:border-gray-400 focus:ring-0 rounded-xl shadow-sm text-sm text-gray-900"></textarea>
                                    <x-input-error :messages="$errors->get('notes')" class="mt-1" />
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tags</label>
                                    <div class="flex flex-wrap gap-2">
                                        @forelse($tags as $tag)
                                            <label class="inline-flex items-center p-2 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                                <input type="checkbox" name="tags[]" value="{{ $tag->id }}" x-model="editContact.tags" class="w-4 h-4 text-gray-900 border-gray-300 rounded focus:ring-gray-900">
                                                <span class="ml-2 text-sm text-gray-700">{{ $tag->name }}</span>
                                            </label>
                                        @empty
                                            <span class="text-sm text-gray-500 italic">No tags available. Create some via "Manage Tags" first.</span>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                            <div class="mt-8 flex justify-end gap-3">
                                <button type="button" @click="showEdit = false" class="px-5 py-2.5 border border-gray-200 rounded-full font-medium text-sm text-gray-700 hover:bg-gray-50 focus:outline-none transition-all shadow-sm">Cancel</button>
                                <button type="submit" class="px-5 py-2.5 bg-gray-900 border border-transparent rounded-full font-medium text-sm text-white hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-all shadow-sm">Update Contact</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Manage Tags Modal -->
        <div x-show="showTags" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div x-show="showTags" x-transition.opacity class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-900 opacity-50"></div>
                </div>

                <div x-show="showTags" x-transition.scale.origin.bottom class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    <div class="bg-white px-6 pt-6 pb-4 sm:p-8 sm:pb-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-bold text-gray-900 tracking-tight">Manage Tags</h3>
                            <button @click="showTags = false" type="button" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                                <span class="sr-only">Close</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>

                        <!-- Add Tag Form -->
                        <form method="POST" action="{{ route('tags.store') }}" class="mb-6 flex gap-2">
                            @csrf
                            <input type="text" name="name" value="{{ old('name') }}" placeholder="New tag name" required class="flex-1 border-gray-200 focus:border-gray-400 focus:ring-0 rounded-xl shadow-sm text-sm text-gray-900" />
                            <button type="submit" class="px-4 py-2 bg-gray-900 border border-transparent rounded-xl font-medium text-sm text-white hover:bg-gray-800 focus:outline-none shadow-sm">Add</button>
                        </form>
                        <x-input-error :messages="$errors->get('name')" class="mb-4" />

                        <!-- Tags List -->
                        <div class="max-h-64 overflow-y-auto pr-2 space-y-2">
                            @forelse($tags as $tag)
                                <div class="flex items-center justify-between p-3 border border-gray-100 rounded-xl bg-gray-50/50">
                                    <span class="text-sm font-medium text-gray-700">{{ $tag->name }}</span>
                                    <form action="{{ route('tags.destroy', $tag) }}" method="POST" onsubmit="return confirm('Delete this tag?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 p-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500 text-center py-4">No tags created yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Import Modal -->
        <div x-show="showImport" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div x-show="showImport" x-transition.opacity class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-900 opacity-50"></div>
                </div>

                <div x-show="showImport" x-transition.scale.origin.bottom class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    <div class="bg-white px-6 pt-6 pb-4 sm:p-8 sm:pb-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-bold text-gray-900 tracking-tight">Import Contacts</h3>
                            <button @click="showImport = false" type="button" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                                <span class="sr-only">Close</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>

                        <form method="POST" action="{{ route('contacts.import') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Excel File (.xlsx, .xls)</label>
                                <input type="file" name="file" accept=".xlsx,.xls" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gray-900 file:text-white hover:file:bg-gray-800 transition-all cursor-pointer border border-gray-200 rounded-2xl p-1" />
                            </div>

                            <div class="bg-blue-50 p-4 rounded-2xl border border-blue-100 mb-6">
                                <h4 class="text-sm font-bold text-blue-900 mb-1">Instructions:</h4>
                                <ul class="text-xs text-blue-800 space-y-1 list-disc list-inside">
                                    <li>Please use our standard template for best results.</li>
                                    <li>Existing contacts will be updated based on their <strong>Phone Number</strong>.</li>
                                    <li>Required columns: <strong>Name</strong> and <strong>Phone</strong>.</li>
                                </ul>
                                <a href="{{ route('contacts.template') }}" class="mt-3 inline-flex items-center text-xs font-bold text-blue-600 hover:text-blue-800">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0L8 8m4-4v12"></path></svg>
                                    Download Template
                                </a>
                            </div>

                            <div class="flex justify-end gap-3">
                                <button type="button" @click="showImport = false" class="px-5 py-2.5 border border-gray-200 rounded-full font-medium text-sm text-gray-700 hover:bg-gray-50">Cancel</button>
                                <button type="submit" class="px-5 py-2.5 bg-gray-900 border border-transparent rounded-full font-medium text-sm text-white hover:bg-gray-800 shadow-sm">Start Import</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
