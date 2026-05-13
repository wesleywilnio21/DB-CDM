<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Register Blood Donor') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg max-w-2xl mx-auto">
                <div class="p-6 text-gray-900">
                    
                    <form method="POST" action="{{ route('blood-donors.store') }}">
                        @csrf
                        
                        <div class="space-y-6">
                            <div>
                                <x-input-label for="contact_id" :value="__('Select Contact')" />
                                <select id="contact_id" name="contact_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required autofocus>
                                    <option value="">-- Choose Contact --</option>
                                    @foreach($contacts as $contact)
                                        <option value="{{ $contact->id }}" {{ old('contact_id') == $contact->id ? 'selected' : '' }}>{{ $contact->name }} ({{ $contact->phone }})</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('contact_id')" class="mt-2" />
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="blood_type" :value="__('Blood Type')" />
                                    <select id="blood_type" name="blood_type" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                        <option value="A" {{ old('blood_type') == 'A' ? 'selected' : '' }}>A</option>
                                        <option value="B" {{ old('blood_type') == 'B' ? 'selected' : '' }}>B</option>
                                        <option value="AB" {{ old('blood_type') == 'AB' ? 'selected' : '' }}>AB</option>
                                        <option value="O" {{ old('blood_type') == 'O' ? 'selected' : '' }}>O</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('blood_type')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="rhesus" :value="__('Rhesus Factor')" />
                                    <select id="rhesus" name="rhesus" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                        <option value="+" {{ old('rhesus') == '+' ? 'selected' : '' }}>Positive (+)</option>
                                        <option value="-" {{ old('rhesus') == '-' ? 'selected' : '' }}>Negative (-)</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('rhesus')" class="mt-2" />
                                </div>
                            </div>

                            <div>
                                <x-input-label for="last_donation_date" :value="__('Last Donation Date (Optional)')" />
                                <x-text-input id="last_donation_date" class="block mt-1 w-full" type="date" name="last_donation_date" :value="old('last_donation_date')" />
                                <x-input-error :messages="$errors->get('last_donation_date')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-6">
                            <x-primary-button>
                                {{ __('Register') }}
                            </x-primary-button>
                            <a href="{{ route('blood-donors.index') }}" class="ml-4 text-gray-600 hover:text-gray-900">Cancel</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
