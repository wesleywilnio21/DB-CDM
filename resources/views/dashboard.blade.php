<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-900 leading-tight tracking-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Welcome Banner (Gradient based on screenshot style) -->
            <div class="relative rounded-3xl overflow-hidden bg-gradient-to-tr from-orange-200 via-orange-50 to-yellow-50 p-8 sm:p-12 shadow-sm border border-orange-100/50">
                <!-- Decorative SVG line from screenshot idea -->
                <div class="absolute inset-0 opacity-20 pointer-events-none">
                    <svg class="absolute w-full h-full" preserveAspectRatio="none" viewBox="0 0 800 300" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M-100 300 L300 -100 M0 400 L400 0 M200 400 Q400 200 600 300 T800 100" stroke="#f97316" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
                
                <div class="relative z-10 max-w-2xl">
                    <h3 class="text-4xl font-bold text-gray-900 tracking-tight mb-4">The database for modern applications</h3>
                    <p class="text-lg text-gray-700 mb-8 max-w-xl">
                        Centralize data from your entire tech stack creating one clear view of performance. Manage contacts, events, and blood donors in one place.
                    </p>
                    
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('contacts.index') }}" class="inline-flex items-center justify-center px-6 py-3 bg-white border border-gray-200 rounded-full font-medium text-sm text-gray-900 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-all shadow-sm">
                            Manage Contacts
                        </a>
                        <a href="{{ route('blood-donors.index') }}" class="inline-flex items-center justify-center px-6 py-3 bg-white border border-gray-200 rounded-full font-medium text-sm text-gray-900 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-all shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                            View Blood Donors
                        </a>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Stat Card 1 -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div class="text-gray-500 text-sm font-medium">Total Contacts</div>
                        <div class="w-10 h-10 rounded-full bg-orange-50 flex items-center justify-center text-orange-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                    </div>
                    <div class="mt-4 text-3xl font-bold text-gray-900">{{ number_format($totalContacts) }}</div>
                </div>

                <!-- Stat Card 2 -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div class="text-gray-500 text-sm font-medium">Total Events</div>
                        <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                    </div>
                    <div class="mt-4 text-3xl font-bold text-gray-900">{{ number_format($totalEvents) }}</div>
                </div>

                <!-- Stat Card 3 -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div class="text-gray-500 text-sm font-medium">Blood Donors</div>
                        <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center text-red-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                        </div>
                    </div>
                    <div class="mt-4 text-3xl font-bold text-gray-900">{{ number_format($totalDonors) }}</div>
                </div>

                <!-- Stat Card 4 -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div class="text-gray-500 text-sm font-medium">Eligible Donors Now</div>
                        <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center text-green-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                    <div class="mt-4 text-3xl font-bold text-gray-900">{{ number_format($eligibleDonors) }}</div>
                </div>
            </div>

            <!-- Bottom Section Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Recent Contacts -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Contacts</h3>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse($recentContacts as $contact)
                            <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 font-medium">
                                        {{ substr($contact->name, 0, 1) }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $contact->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $contact->phone }}</div>
                                    </div>
                                </div>
                                <a href="{{ route('contacts.show', $contact) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">View &rarr;</a>
                            </div>
                        @empty
                            <div class="px-6 py-8 text-center text-gray-500 text-sm">No contacts added yet.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Upcoming Events -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">Upcoming Events</h3>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse($upcomingEvents as $event)
                            <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <div class="flex items-start">
                                    <div class="flex flex-col items-center justify-center bg-gray-50 rounded-lg p-2 min-w-[3rem] border border-gray-100">
                                        <span class="text-xs font-semibold text-red-500 uppercase">{{ $event->date->format('M') }}</span>
                                        <span class="text-lg font-bold text-gray-900 leading-none mt-1">{{ $event->date->format('d') }}</span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $event->name }}</div>
                                        <div class="text-xs text-gray-500 mt-0.5">{{ $event->location ?: 'No location specified' }}</div>
                                    </div>
                                </div>
                                <a href="{{ route('events.show', $event) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">Details &rarr;</a>
                            </div>
                        @empty
                            <div class="px-6 py-8 text-center text-gray-500 text-sm">No upcoming events scheduled.</div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
