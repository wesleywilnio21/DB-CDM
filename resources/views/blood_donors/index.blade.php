<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-900 leading-tight tracking-tight">
            {{ __('Blood Donors') }}
        </h2>
    </x-slot>

    <div class="py-10" x-data="{ 
        showCreate: {{ $errors->has('contact_id') || ($errors->any() && !old('_method') && request()->isMethod('post') && !request()->routeIs('blood-donors.donate') && !request()->routeIs('blood-donors.quick-donate')) ? 'true' : 'false' }}, 
        showEdit: {{ $errors->any() && old('_method') == 'PATCH' ? 'true' : 'false' }},
        showView: {{ $errors->any() && request()->routeIs('blood-donors.donate') ? 'true' : 'false' }},
        tab: 'existing',
        editDonor: { id: '', blood_type: '', rhesus: '', last_donation_date: '' },
        viewDonor: { id: '', name: '', phone: '', type: '', rhesus: '', next: '', history: [] },
        
        allDonors: {{ json_encode($allDonors) }},
        searchQuery: '',
        selectedQuickDonor: null,
        get filteredDonors() {
            if (this.searchQuery === '') return [];
            return this.allDonors.filter(d => 
                d.name.toLowerCase().includes(this.searchQuery.toLowerCase()) || 
                d.phone.includes(this.searchQuery)
            ).slice(0, 5);
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if (session('success'))
                <div class="text-green-700 bg-green-50 border border-green-200 p-4 rounded-xl flex items-center">
                    <svg class="w-5 h-5 mr-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 md:p-8 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-xl font-bold text-gray-900 tracking-tight">Manage Blood Donors</h3>
                    <button @click="showCreate = true" class="inline-flex items-center justify-center px-5 py-2.5 bg-red-600 border border-transparent rounded-full font-medium text-sm text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-600 transition-all shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Register Donor
                    </button>
                </div>

                <!-- Quick Donate Section -->
                <div class="p-6 md:px-8 border-b border-gray-100 bg-gray-50/50">
                    <h4 class="text-sm font-bold text-gray-900 mb-3">Quick Donate (Repeat Donors)</h4>
                    <div class="relative max-w-xl">
                        <input type="text" x-model="searchQuery" @focus="selectedQuickDonor = null" placeholder="Search by name or phone..." class="block w-full border-gray-200 focus:border-red-400 focus:ring-0 rounded-xl shadow-sm text-sm text-gray-900" />
                        
                        <!-- Autocomplete Dropdown -->
                        <div x-show="searchQuery.length > 0 && !selectedQuickDonor" class="absolute z-10 w-full mt-1 bg-white border border-gray-100 rounded-xl shadow-lg max-h-60 overflow-y-auto" style="display: none;">
                            <template x-for="donor in filteredDonors" :key="donor.id">
                                <button @click="selectedQuickDonor = donor; searchQuery = donor.name" type="button" class="w-full text-left px-4 py-3 hover:bg-gray-50 border-b border-gray-50 last:border-0 flex justify-between items-center transition-colors">
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900" x-text="donor.name"></div>
                                        <div class="text-xs text-gray-500" x-text="donor.phone"></div>
                                    </div>
                                    <span class="inline-flex items-center justify-center h-6 w-10 rounded bg-red-50 text-red-700 font-bold text-xs border border-red-100" x-text="donor.type"></span>
                                </button>
                            </template>
                            <div x-show="filteredDonors.length === 0" class="px-4 py-3 text-sm text-gray-500 text-center">
                                No donors found. Register them first.
                            </div>
                        </div>
                    </div>

                    <!-- Selected Donor Form -->
                    <form x-show="selectedQuickDonor" method="POST" :action="`/blood-donors/${selectedQuickDonor?.id}/donate`" class="mt-4 p-4 bg-white border border-gray-200 rounded-xl shadow-sm" style="display: none;">
                        @csrf
                        <div class="flex flex-col md:flex-row gap-4 items-end">
                            <div class="w-full md:w-auto">
                                <label class="block text-xs font-medium text-gray-700 mb-1">Date <span class="text-red-500">*</span></label>
                                <input type="date" name="donated_at" value="{{ date('Y-m-d') }}" required class="block w-full border-gray-200 focus:border-red-400 focus:ring-0 rounded-lg shadow-sm text-sm text-gray-900" />
                            </div>
                            <div class="flex-1 w-full">
                                <label class="block text-xs font-medium text-gray-700 mb-1">Location</label>
                                <input type="text" name="location" class="block w-full border-gray-200 focus:border-red-400 focus:ring-0 rounded-lg shadow-sm text-sm text-gray-900" placeholder="Hospital / Camp" />
                            </div>
                            <div class="flex-1 w-full">
                                <label class="block text-xs font-medium text-gray-700 mb-1">Notes</label>
                                <input type="text" name="notes" class="block w-full border-gray-200 focus:border-red-400 focus:ring-0 rounded-lg shadow-sm text-sm text-gray-900" />
                            </div>
                            <button type="submit" class="w-full md:w-auto px-5 py-2.5 bg-gray-900 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-gray-800 focus:outline-none transition-all shadow-sm whitespace-nowrap">
                                Log Donation
                            </button>
                            <button type="button" @click="selectedQuickDonor = null; searchQuery = ''" class="w-full md:w-auto px-4 py-2.5 border border-gray-200 rounded-lg font-medium text-sm text-gray-700 hover:bg-gray-50 focus:outline-none transition-all shadow-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>

                <div class="p-6 md:p-8">
                    <div class="overflow-hidden border border-gray-100 rounded-2xl">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50/50">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Donor</th>
                                    <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Blood Type</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Last Donation</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse($donors as $donor)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-red-50 flex items-center justify-center text-red-600 font-bold border border-red-100">
                                                    {{ substr($donor->contact->name, 0, 1) }}
                                                </div>
                                                <div class="ml-4">
                                                    <a href="{{ route('contacts.show', $donor->contact) }}" class="text-sm font-semibold text-gray-900 hover:text-indigo-600 transition-colors">{{ $donor->contact->name }}</a>
                                                    <div class="text-xs text-gray-500">{{ $donor->contact->phone }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="inline-flex items-center justify-center h-8 w-12 rounded-lg bg-red-100 text-red-800 font-bold text-sm border border-red-200">
                                                {{ $donor->blood_type }}{{ $donor->rhesus }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $donor->last_donation_date ? $donor->last_donation_date->format('M d, Y') : 'Never' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($donor->next_eligible_date)
                                                @if($donor->next_eligible_date->isPast())
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-100">
                                                        Eligible Now
                                                    </span>
                                                @else
                                                    <div class="text-sm text-gray-900 font-medium">Eligible on</div>
                                                    <div class="text-xs text-orange-500">{{ $donor->next_eligible_date->format('M d, Y') }}</div>
                                                @endif
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-100">
                                                    Eligible Now
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center justify-end gap-3">
                                                <button @click="
                                                        viewDonor = { 
                                                            id: {{ $donor->id }}, 
                                                            name: '{{ addslashes($donor->contact->name) }}', 
                                                            phone: '{{ addslashes($donor->contact->phone) }}',
                                                            type: '{{ $donor->blood_type }}', 
                                                            rhesus: '{{ $donor->rhesus }}',
                                                            next: '{{ $donor->next_eligible_date ? ($donor->next_eligible_date->isPast() ? 'Eligible Now' : $donor->next_eligible_date->format('M d, Y')) : 'Eligible Now' }}',
                                                            history: {{ json_encode($donor->donationHistories->map(function($h){ return ['id'=>$h->id, 'date'=>$h->donated_at->format('M d, Y'), 'loc'=>$h->location, 'notes'=>$h->notes]; })) }}
                                                        };
                                                        showView = true;
                                                    " 
                                                    class="text-indigo-600 hover:text-indigo-900 transition-colors">
                                                    View
                                                </button>

                                                <button @click="
                                                        editDonor = {{ json_encode($donor->only(['id','blood_type','rhesus','last_donation_date'])) }}; 
                                                        if(editDonor.last_donation_date) editDonor.last_donation_date = editDonor.last_donation_date.split('T')[0];
                                                        showEdit = true;
                                                    " 
                                                    class="text-gray-500 hover:text-gray-900 transition-colors">
                                                    Edit
                                                </button>

                                                <form action="{{ route('blood-donors.destroy', $donor) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this donor record?');" class="inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-500 hover:text-red-700 transition-colors">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                            <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                            No blood donors registered.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-6">
                        {{ $donors->links() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Modal (Register Donor) -->
        <div x-show="showCreate" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div x-show="showCreate" x-transition.opacity class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-900 opacity-50"></div>
                </div>

                <div x-show="showCreate" x-transition.scale.origin.bottom class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
                    <div class="bg-white px-6 pt-6 pb-4 sm:p-8 sm:pb-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-bold text-gray-900 tracking-tight">Register Blood Donor</h3>
                            <button @click="showCreate = false" type="button" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                                <span class="sr-only">Close</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                        
                        <!-- Tabs -->
                        <div class="flex space-x-4 mb-6 border-b border-gray-100">
                            <button @click="tab = 'existing'" :class="{'border-red-500 text-red-600': tab === 'existing', 'border-transparent text-gray-500 hover:text-gray-700': tab !== 'existing'}" class="pb-3 border-b-2 font-medium text-sm transition-colors">Select Existing Contact</button>
                            <button @click="tab = 'new'" :class="{'border-red-500 text-red-600': tab === 'new', 'border-transparent text-gray-500 hover:text-gray-700': tab !== 'new'}" class="pb-3 border-b-2 font-medium text-sm transition-colors">Create New Contact</button>
                        </div>

                        <!-- Form: Existing Contact -->
                        <form x-show="tab === 'existing'" method="POST" action="{{ route('blood-donors.store') }}">
                            @csrf
                            <div class="space-y-5">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Contact <span class="text-red-500">*</span></label>
                                    <select name="contact_id" class="block w-full border-gray-200 focus:border-gray-400 focus:ring-0 rounded-xl shadow-sm text-sm text-gray-900" required>
                                        <option value="">-- Choose Contact --</option>
                                        @foreach(\App\Models\Contact::doesntHave('bloodDonor')->get() as $contact)
                                            <option value="{{ $contact->id }}" {{ old('contact_id') == $contact->id ? 'selected' : '' }}>{{ $contact->name }} ({{ $contact->phone }})</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('contact_id')" class="mt-1" />
                                </div>
                                <div class="grid grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Blood Type <span class="text-red-500">*</span></label>
                                        <select name="blood_type" class="block w-full border-gray-200 focus:border-gray-400 focus:ring-0 rounded-xl shadow-sm text-sm text-gray-900" required>
                                            <option value="A" {{ old('blood_type') == 'A' ? 'selected' : '' }}>A</option>
                                            <option value="B" {{ old('blood_type') == 'B' ? 'selected' : '' }}>B</option>
                                            <option value="AB" {{ old('blood_type') == 'AB' ? 'selected' : '' }}>AB</option>
                                            <option value="O" {{ old('blood_type') == 'O' ? 'selected' : '' }}>O</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('blood_type')" class="mt-1" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Rhesus Factor <span class="text-red-500">*</span></label>
                                        <select name="rhesus" class="block w-full border-gray-200 focus:border-gray-400 focus:ring-0 rounded-xl shadow-sm text-sm text-gray-900" required>
                                            <option value="+" {{ old('rhesus') == '+' ? 'selected' : '' }}>Positive (+)</option>
                                            <option value="-" {{ old('rhesus') == '-' ? 'selected' : '' }}>Negative (-)</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('rhesus')" class="mt-1" />
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Last Donation Date (Optional)</label>
                                    <input type="date" name="last_donation_date" value="{{ old('last_donation_date') }}" class="block w-full border-gray-200 focus:border-gray-400 focus:ring-0 rounded-xl shadow-sm text-sm text-gray-900" />
                                    <x-input-error :messages="$errors->get('last_donation_date')" class="mt-1" />
                                </div>
                            </div>
                            <div class="mt-8 flex justify-end gap-3">
                                <button type="button" @click="showCreate = false" class="px-5 py-2.5 border border-gray-200 rounded-full font-medium text-sm text-gray-700 hover:bg-gray-50 focus:outline-none transition-all shadow-sm">Cancel</button>
                                <button type="submit" class="px-5 py-2.5 bg-red-600 border border-transparent rounded-full font-medium text-sm text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-600 transition-all shadow-sm">Register Donor</button>
                            </div>
                        </form>

                        <!-- Form: New Contact -->
                        <form x-show="tab === 'new'" method="POST" action="{{ route('blood-donors.store-with-contact') }}" style="display: none;">
                            @csrf
                            <div class="space-y-5">
                                <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100 grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="md:col-span-2"><h4 class="text-sm font-semibold text-gray-900">Contact Details</h4></div>
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
                                </div>

                                <div class="grid grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Blood Type <span class="text-red-500">*</span></label>
                                        <select name="blood_type" class="block w-full border-gray-200 focus:border-gray-400 focus:ring-0 rounded-xl shadow-sm text-sm text-gray-900" required>
                                            <option value="A" {{ old('blood_type') == 'A' ? 'selected' : '' }}>A</option>
                                            <option value="B" {{ old('blood_type') == 'B' ? 'selected' : '' }}>B</option>
                                            <option value="AB" {{ old('blood_type') == 'AB' ? 'selected' : '' }}>AB</option>
                                            <option value="O" {{ old('blood_type') == 'O' ? 'selected' : '' }}>O</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('blood_type')" class="mt-1" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Rhesus Factor <span class="text-red-500">*</span></label>
                                        <select name="rhesus" class="block w-full border-gray-200 focus:border-gray-400 focus:ring-0 rounded-xl shadow-sm text-sm text-gray-900" required>
                                            <option value="+" {{ old('rhesus') == '+' ? 'selected' : '' }}>Positive (+)</option>
                                            <option value="-" {{ old('rhesus') == '-' ? 'selected' : '' }}>Negative (-)</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('rhesus')" class="mt-1" />
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Last Donation Date (Optional)</label>
                                    <input type="date" name="last_donation_date" value="{{ old('last_donation_date') }}" class="block w-full border-gray-200 focus:border-gray-400 focus:ring-0 rounded-xl shadow-sm text-sm text-gray-900" />
                                    <x-input-error :messages="$errors->get('last_donation_date')" class="mt-1" />
                                </div>
                            </div>
                            <div class="mt-8 flex justify-end gap-3">
                                <button type="button" @click="showCreate = false" class="px-5 py-2.5 border border-gray-200 rounded-full font-medium text-sm text-gray-700 hover:bg-gray-50 focus:outline-none transition-all shadow-sm">Cancel</button>
                                <button type="submit" class="px-5 py-2.5 bg-red-600 border border-transparent rounded-full font-medium text-sm text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-600 transition-all shadow-sm">Save & Register Donor</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- View / History Modal -->
        <div x-show="showView" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div x-show="showView" x-transition.opacity class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-900 opacity-50"></div>
                </div>

                <div x-show="showView" x-transition.scale.origin.bottom class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
                    <div class="bg-white px-6 pt-6 pb-4 sm:p-8 sm:pb-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-bold text-gray-900 tracking-tight">Donor Details</h3>
                            <button @click="showView = false" type="button" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                                <span class="sr-only">Close</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 mb-6 bg-gray-50 p-4 rounded-2xl border border-gray-100">
                            <div>
                                <div class="text-xs text-gray-500">Name</div>
                                <div class="font-semibold text-gray-900" x-text="viewDonor.name"></div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500">Blood Type</div>
                                <div class="font-semibold text-red-600"><span x-text="viewDonor.type"></span><span x-text="viewDonor.rhesus"></span></div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500">Eligibility</div>
                                <div class="font-medium text-gray-900" x-text="viewDonor.next"></div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500">Phone</div>
                                <div class="font-medium text-gray-900" x-text="viewDonor.phone"></div>
                            </div>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3 border-b border-gray-100 pb-2">Donation History</h4>
                        <div class="mb-6 max-h-48 overflow-y-auto pr-2">
                            <template x-if="viewDonor.history.length === 0">
                                <p class="text-sm text-gray-500 text-center py-4">No donation records found.</p>
                            </template>
                            <div class="space-y-3">
                                <template x-for="hist in viewDonor.history" :key="hist.id">
                                    <div class="bg-white border border-gray-100 p-3 rounded-xl flex justify-between items-start">
                                        <div>
                                            <div class="font-medium text-sm text-gray-900" x-text="hist.date"></div>
                                            <div class="text-xs text-gray-500 mt-1" x-text="hist.loc ? hist.loc : 'Location not specified'"></div>
                                        </div>
                                        <div class="text-xs text-gray-400 italic max-w-[50%]" x-text="hist.notes"></div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <h4 class="font-semibold text-gray-900 mb-3 border-t border-gray-100 pt-4">Log New Donation</h4>
                        <form method="POST" :action="`/blood-donors/${viewDonor.id}/donate`" class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                            @csrf
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Date <span class="text-red-500">*</span></label>
                                    <input type="date" name="donated_at" required class="block w-full border-gray-200 focus:border-red-400 focus:ring-0 rounded-xl shadow-sm text-sm text-gray-900" />
                                    <x-input-error :messages="$errors->get('donated_at')" class="mt-1" />
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Location</label>
                                    <input type="text" name="location" class="block w-full border-gray-200 focus:border-red-400 focus:ring-0 rounded-xl shadow-sm text-sm text-gray-900" placeholder="Hospital / Camp" />
                                    <x-input-error :messages="$errors->get('location')" class="mt-1" />
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Notes</label>
                                    <input type="text" name="notes" class="block w-full border-gray-200 focus:border-red-400 focus:ring-0 rounded-xl shadow-sm text-sm text-gray-900" />
                                    <x-input-error :messages="$errors->get('notes')" class="mt-1" />
                                </div>
                            </div>
                            <div class="mt-4 flex justify-end">
                                <button type="submit" class="px-4 py-2 bg-gray-900 border border-transparent rounded-full font-medium text-sm text-white hover:bg-gray-800 focus:outline-none transition-all shadow-sm">Save Log</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div x-show="showEdit" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div x-show="showEdit" x-transition.opacity class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-900 opacity-50"></div>
                </div>

                <div x-show="showEdit" x-transition.scale.origin.bottom class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full">
                    <div class="bg-white px-6 pt-6 pb-4 sm:p-8 sm:pb-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-bold text-gray-900 tracking-tight">Edit Donor Info</h3>
                            <button @click="showEdit = false" type="button" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                                <span class="sr-only">Close</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                        <form method="POST" :action="`/blood-donors/${editDonor.id}`">
                            @csrf
                            @method('PATCH')
                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Blood Type</label>
                                        <select name="blood_type" x-model="editDonor.blood_type" required class="block w-full border-gray-200 focus:border-gray-400 focus:ring-0 rounded-xl shadow-sm text-sm text-gray-900">
                                            <option value="A">A</option>
                                            <option value="B">B</option>
                                            <option value="AB">AB</option>
                                            <option value="O">O</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Rhesus</label>
                                        <select name="rhesus" x-model="editDonor.rhesus" required class="block w-full border-gray-200 focus:border-gray-400 focus:ring-0 rounded-xl shadow-sm text-sm text-gray-900">
                                            <option value="+">Positive (+)</option>
                                            <option value="-">Negative (-)</option>
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Last Donation Date</label>
                                    <input type="date" name="last_donation_date" x-model="editDonor.last_donation_date" class="block w-full border-gray-200 focus:border-gray-400 focus:ring-0 rounded-xl shadow-sm text-sm text-gray-900" />
                                    <p class="text-xs text-gray-500 mt-1">Note: Using the View -> Log New Donation is recommended for tracking.</p>
                                </div>
                            </div>
                            <div class="mt-8 flex justify-end gap-3">
                                <button type="button" @click="showEdit = false" class="px-5 py-2.5 border border-gray-200 rounded-full font-medium text-sm text-gray-700 hover:bg-gray-50 focus:outline-none transition-all shadow-sm">Cancel</button>
                                <button type="submit" class="px-5 py-2.5 bg-gray-900 border border-transparent rounded-full font-medium text-sm text-white hover:bg-gray-800 focus:outline-none transition-all shadow-sm">Update Info</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
