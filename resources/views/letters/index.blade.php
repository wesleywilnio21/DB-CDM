<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-900 leading-tight tracking-tight">
            {{ __('All Letters') }}
        </h2>
    </x-slot>

    <div class="py-10" x-data="{ showCreateModal: false, showDelete: false, deleteUrl: '', deleteName: '' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if (session('success'))
                <div class="text-green-700 bg-green-50 border border-green-200 p-4 rounded-xl flex items-center">
                    <svg class="w-5 h-5 mr-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 md:p-8 border-b border-gray-100 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 tracking-tight">Manage All Letters</h3>
                        <p class="text-xs text-gray-500 mt-1">View, filter, and print event letters.</p>
                    </div>
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                        <form method="GET" action="{{ route('letters.global.index') }}" class="flex items-center">
                            <select name="event_id" onchange="this.form.submit()" class="block w-full sm:w-64 border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-full text-xs text-gray-900 bg-gray-50/50 hover:bg-gray-50 transition-colors">
                                <option value="">All Events (No Filter)</option>
                                @foreach($events as $event)
                                    <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                                        {{ $event->name }} ({{ $event->date->format('M d, Y') }})
                                    </option>
                                @endforeach
                            </select>
                        </form>
                        <button @click="showCreateModal = true" class="inline-flex items-center justify-center px-5 py-2.5 bg-gray-900 border border-transparent rounded-full font-medium text-sm text-white hover:bg-gray-800 focus:outline-none transition-all shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Create Letter
                        </button>
                    </div>
                </div>

                <div class="p-6 md:p-8">
                    <div class="overflow-hidden border border-gray-100 rounded-2xl">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50/50">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Letter Details</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Event</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date Created</th>
                                    <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse($letters as $letter)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-semibold text-gray-900">{{ $letter->title }}</div>
                                            <div class="text-xs text-gray-500">To: {{ $letter->recipient_name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100 mb-1">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                {{ $letter->event->name }}
                                            </div>
                                            <div class="text-xs text-gray-500 font-medium">{{ $letter->event->date->format('M d, Y') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $letter->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center justify-end gap-3">
                                                <a href="{{ route('letters.pdf', $letter) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 transition-colors font-semibold">PDF</a>
                                                <a href="{{ route('letters.edit', [$letter->event_id, $letter]) }}" class="text-gray-500 hover:text-gray-900 transition-colors">Edit</a>
                                                
                                                <button @click="
                                                        deleteUrl = '{{ route('letters.destroy', [$letter->event_id, $letter]) }}';
                                                        deleteName = '{{ addslashes($letter->title) }}';
                                                        showDelete = true;
                                                    " class="text-red-500 hover:text-red-700 transition-colors">Delete</button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                            <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                            No letters found. Create your first letter.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-6">
                        {{ $letters->links() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Letter (Select Event) Modal -->
        <div x-show="showCreateModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div x-show="showCreateModal" x-transition.opacity class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-900 opacity-50"></div>
                </div>

                <div x-show="showCreateModal" x-transition.scale.origin.bottom class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full">
                    <div class="bg-white px-6 pt-5 pb-4 sm:p-6 sm:pb-4 text-center">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-indigo-100 mb-5">
                            <svg class="h-8 w-8 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        </div>
                        <h3 class="text-xl leading-6 font-bold text-gray-900 mb-2">Create New Letter</h3>
                        <p class="text-sm text-gray-500 mb-6">
                            Select an event to create a letter for.
                        </p>
                        
                        <div class="mb-4">
                            <select id="eventSelect" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm sm:text-sm text-left text-gray-900 bg-white">
                                <option value="" class="text-gray-500">Select an Event...</option>
                                @foreach($events as $event)
                                    <option value="{{ $event->id }}" class="text-gray-900">{{ $event->name }} ({{ $event->date->format('M d, Y') }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex justify-center gap-3 w-full mt-6">
                            <button type="button" @click="showCreateModal = false" class="w-1/2 px-4 py-3 border border-gray-300 rounded-xl font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none transition-all">
                                Cancel
                            </button>
                            <button type="button" onclick="const e = document.getElementById('eventSelect').value; if(e) window.location.href='/events/'+e+'/letters/create'" class="w-1/2 px-4 py-3 border border-transparent rounded-xl font-medium text-white bg-gray-900 hover:bg-gray-800 focus:outline-none transition-all shadow-sm">
                                Continue
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div x-show="showDelete" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div x-show="showDelete" x-transition.opacity class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-900 opacity-50"></div>
                </div>

                <div x-show="showDelete" x-transition.scale.origin.bottom class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full">
                    <div class="bg-white px-6 pt-5 pb-4 sm:p-6 sm:pb-4 text-center">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-5">
                            <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        </div>
                        <h3 class="text-xl leading-6 font-bold text-gray-900 mb-2">Delete Letter</h3>
                        <p class="text-sm text-gray-500 mb-6">
                            Are you sure you want to delete the letter <strong class="text-gray-900" x-text="deleteName"></strong>? This action cannot be undone.
                        </p>
                        
                        <form :action="deleteUrl" method="POST" class="flex justify-center gap-3 w-full">
                            @csrf
                            @method('DELETE')
                            <button type="button" @click="showDelete = false" class="w-1/2 px-4 py-3 border border-gray-300 rounded-xl font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none transition-all">
                                Cancel
                            </button>
                            <button type="submit" class="w-1/2 px-4 py-3 border border-transparent rounded-xl font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none transition-all shadow-sm">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
