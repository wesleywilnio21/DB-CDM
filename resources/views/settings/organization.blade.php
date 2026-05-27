<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-900 leading-tight tracking-tight">
            Organization Settings
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-6 text-green-700 bg-green-50 border border-green-200 p-4 rounded-xl flex items-center">
                    <svg class="w-5 h-5 mr-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <form action="{{ route('settings.organization.update') }}" method="POST" class="p-6 md:p-8 space-y-6">
                    @csrf
                    
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-1">Organization Name <span class="text-red-500">*</span></label>
                            <input type="text" name="org_name" value="{{ old('org_name', $settings['org_name']) }}" required class="block w-full border-gray-200 focus:border-gray-400 focus:ring-0 rounded-xl shadow-sm text-sm text-gray-900" />
                            <x-input-error :messages="$errors->get('org_name')" class="mt-1" />
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-1">Address <span class="text-red-500">*</span></label>
                            <input type="text" name="org_address" value="{{ old('org_address', $settings['org_address']) }}" required class="block w-full border-gray-200 focus:border-gray-400 focus:ring-0 rounded-xl shadow-sm text-sm text-gray-900" />
                            <x-input-error :messages="$errors->get('org_address')" class="mt-1" />
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-1">Phone & Contact Info <span class="text-red-500">*</span></label>
                            <input type="text" name="org_phone" value="{{ old('org_phone', $settings['org_phone']) }}" required class="block w-full border-gray-200 focus:border-gray-400 focus:ring-0 rounded-xl shadow-sm text-sm text-gray-900" />
                            <x-input-error :messages="$errors->get('org_phone')" class="mt-1" />
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-1">Tagline / Motto</label>
                            <input type="text" name="org_tagline" value="{{ old('org_tagline', $settings['org_tagline']) }}" class="block w-full border-gray-200 focus:border-gray-400 focus:ring-0 rounded-xl shadow-sm text-sm text-gray-900" />
                            <x-input-error :messages="$errors->get('org_tagline')" class="mt-1" />
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-1">Default City for Letters <span class="text-red-500">*</span></label>
                            <input type="text" name="org_city_default" value="{{ old('org_city_default', $settings['org_city_default']) }}" required class="block w-full border-gray-200 focus:border-gray-400 focus:ring-0 rounded-xl shadow-sm text-sm text-gray-900" />
                            <p class="text-xs text-gray-500 mt-1">E.g., Jakarta, Tangerang</p>
                            <x-input-error :messages="$errors->get('org_city_default')" class="mt-1" />
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-100 flex justify-end">
                        <button type="submit" class="px-5 py-2.5 bg-gray-900 border border-transparent rounded-full font-medium text-sm text-white hover:bg-gray-800 focus:outline-none transition-all shadow-sm">Save Settings</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
