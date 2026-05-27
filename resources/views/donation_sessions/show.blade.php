<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-900 leading-tight tracking-tight">
                {{ __('Session Details') }}: {{ $donationSession->display_name }}
            </h2>
            <a href="{{ route('donation-sessions.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">
                &larr; Back to Sessions
            </a>
        </div>
    </x-slot>

    <div class="py-10" x-data="{ showAddModal: false, activeTab: 'existing' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if (session('success'))
                <div class="text-green-700 bg-green-50 border border-green-200 p-4 rounded-xl flex items-center">
                    <svg class="w-5 h-5 mr-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            <!-- Session Information Card -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 md:p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="text-sm font-medium text-gray-500 mb-1">Date</div>
                            <div class="text-lg text-gray-900 font-semibold">{{ $donationSession->session_date->format('l, F j, Y') }}</div>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-500 mb-1">Location</div>
                            <div class="text-lg text-gray-900 font-semibold">{{ $donationSession->location ?: '-' }}</div>
                        </div>
                        <div class="md:col-span-2">
                            <div class="text-sm font-medium text-gray-500 mb-1">Notes</div>
                            <div class="text-base text-gray-800">{{ $donationSession->notes ?: '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Donors Section -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 tracking-tight">Donors</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            <span class="font-bold text-red-600">{{ $donationSession->donors->count() }}</span> Donors participated
                        </p>
                    </div>
                    <button @click="showAddModal = true" class="inline-flex items-center justify-center px-6 py-3 bg-red-600 border border-transparent rounded-full font-semibold text-sm text-white hover:bg-red-700 transition-all shadow-md">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Add Donors
                    </button>
                </div>
                
                <div class="p-6">
                    @if($donationSession->donors->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($donationSession->donors as $donor)
                                <div class="relative flex flex-col p-4 rounded-2xl border border-gray-100 bg-gray-50/50 hover:bg-white hover:shadow-md transition-all group">
                                    
                                    <!-- Delete Button (Top Right) -->
                                    <form action="{{ route('donation-sessions.remove-donor', [$donationSession, $donor]) }}" method="POST" class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Remove this donor from session?')" class="text-gray-400 hover:text-red-500 transition-colors bg-white rounded-full p-1 shadow-sm border border-gray-100">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </form>

                                    <div class="flex items-center mb-3">
                                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-red-100 flex items-center justify-center text-red-700 font-bold text-lg">
                                            {{ $donor->blood_type }}{{ $donor->rhesus }}
                                        </div>
                                        <div class="ml-3 flex-1 overflow-hidden">
                                            <a href="{{ route('contacts.show', $donor->contact) }}" class="text-sm font-bold text-gray-900 hover:text-red-600 transition-colors truncate block">{{ $donor->contact->name }}</a>
                                            <div class="text-xs text-gray-500 truncate">{{ $donor->contact->phone }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 text-gray-500 bg-gray-50/50 rounded-2xl border border-dashed border-gray-200">
                            <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            <p class="text-lg font-medium text-gray-900">No donors yet</p>
                            <p class="mt-1">Add donors who participated in this session.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Add Donors Modal -->
        <div x-show="showAddModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div x-show="showAddModal" x-transition.opacity class="fixed inset-0 transition-opacity bg-gray-900 opacity-50"></div>

                <div x-show="showAddModal" x-transition.scale.origin.bottom class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
                    <div class="bg-white px-6 pt-6 pb-4 sm:p-8 sm:pb-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-bold text-gray-900">Add Donors</h3>
                            <button @click="showAddModal = false" type="button" class="text-gray-400 hover:text-gray-500">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>

                        <!-- Tabs -->
                        <div class="flex border-b border-gray-200 mb-6">
                            <button @click="activeTab = 'existing'" :class="{ 'border-red-600 text-red-600': activeTab === 'existing', 'border-transparent text-gray-500 hover:text-gray-700': activeTab !== 'existing' }" class="flex-1 pb-3 text-center border-b-2 font-semibold text-sm transition-colors">
                                Add from Existing Donors
                            </button>
                            <button @click="activeTab = 'new'" :class="{ 'border-red-600 text-red-600': activeTab === 'new', 'border-transparent text-gray-500 hover:text-gray-700': activeTab !== 'new' }" class="flex-1 pb-3 text-center border-b-2 font-semibold text-sm transition-colors">
                                Register New Donor
                            </button>
                        </div>

                        <!-- Tab Content: Existing Donors -->
                        <div x-show="activeTab === 'existing'" x-data="{ search: '' }">
                            <form action="{{ route('donation-sessions.add-donor', $donationSession) }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <input type="text" x-model="search" placeholder="Search donors..." class="w-full border-gray-200 focus:border-red-500 rounded-xl shadow-sm text-sm text-gray-900">
                                </div>
                                <div class="max-h-72 overflow-y-auto pr-2 mb-6 space-y-2">
                                    @forelse($allDonors as $donor)
                                        <div x-show="search === '' || '{{ strtolower($donor->contact->name) }}'.includes(search.toLowerCase()) || '{{ strtolower($donor->contact->phone) }}'.includes(search.toLowerCase())" class="flex items-center justify-between p-3 border border-gray-100 rounded-xl hover:bg-gray-50 transition-colors">
                                            <label class="flex items-center flex-1 cursor-pointer">
                                                <input type="checkbox" name="donor_ids[]" value="{{ $donor->id }}" class="w-5 h-5 text-red-600 border-gray-300 rounded focus:ring-red-500">
                                                <div class="ml-3">
                                                    <div class="text-sm font-bold text-gray-900">{{ $donor->contact->name }}</div>
                                                    <div class="text-xs text-gray-500">{{ $donor->contact->phone }}</div>
                                                </div>
                                            </label>
                                            <div class="flex items-center ml-4">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-red-100 text-red-800">
                                                    {{ $donor->blood_type }}{{ $donor->rhesus }}
                                                </span>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-4 text-gray-500 text-sm">No existing donors available.</div>
                                    @endforelse
                                </div>
                                <div class="flex justify-end gap-3">
                                    <button type="button" @click="showAddModal = false" class="px-5 py-2.5 border border-gray-200 rounded-full font-medium text-sm text-gray-700 hover:bg-gray-50">Cancel</button>
                                    <button type="submit" class="px-5 py-2.5 bg-red-600 border border-transparent rounded-full font-medium text-sm text-white hover:bg-red-700">Add Selected</button>
                                </div>
                            </form>
                        </div>

                        <!-- Tab Content: New Donor -->
                        <div x-show="activeTab === 'new'" style="display: none;">
                            <form action="{{ route('donation-sessions.create-donor', $donationSession) }}" method="POST">
                                @csrf
                                <div class="space-y-4 mb-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-900 mb-1">Full Name <span class="text-red-500">*</span></label>
                                        <input type="text" name="name" required class="w-full border-gray-200 focus:border-red-500 rounded-xl shadow-sm text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-900 mb-1">Phone Number <span class="text-red-500">*</span></label>
                                        <input type="text" name="phone" required class="w-full border-gray-200 focus:border-red-500 rounded-xl shadow-sm text-sm">
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-900 mb-1">Blood Type <span class="text-red-500">*</span></label>
                                            <select name="blood_type" required class="w-full border-gray-200 focus:border-red-500 rounded-xl shadow-sm text-sm">
                                                <option value="A">A</option>
                                                <option value="B">B</option>
                                                <option value="AB">AB</option>
                                                <option value="O">O</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-900 mb-1">Rhesus <span class="text-red-500">*</span></label>
                                            <select name="rhesus" required class="w-full border-gray-200 focus:border-red-500 rounded-xl shadow-sm text-sm">
                                                <option value="+">+</option>
                                                <option value="-">-</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex justify-end gap-3">
                                    <button type="button" @click="showAddModal = false" class="px-5 py-2.5 border border-gray-200 rounded-full font-medium text-sm text-gray-700 hover:bg-gray-50">Cancel</button>
                                    <button type="submit" class="px-5 py-2.5 bg-red-600 border border-transparent rounded-full font-medium text-sm text-white hover:bg-red-700">Save & Add Donor</button>
                                </div>
                            </form>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
