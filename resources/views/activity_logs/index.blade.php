<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-900 leading-tight tracking-tight">
            {{ __('Activity Log') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 md:p-8 border-b border-gray-100">
                    <h3 class="text-xl font-bold text-gray-900 tracking-tight mb-6">Audit Trail</h3>
                    
                    <!-- Filters -->
                    <form method="GET" action="{{ route('activity-logs.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-gray-50 p-6 rounded-2xl border border-gray-100">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Action</label>
                            <select name="action" class="block w-full border-gray-200 focus:border-gray-400 focus:ring-0 rounded-xl shadow-sm text-sm">
                                <option value="">All Actions</option>
                                <option value="created" {{ request('action') == 'created' ? 'selected' : '' }}>Created</option>
                                <option value="updated" {{ request('action') == 'updated' ? 'selected' : '' }}>Updated</option>
                                <option value="deleted" {{ request('action') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Model</label>
                            <select name="model" class="block w-full border-gray-200 focus:border-gray-400 focus:ring-0 rounded-xl shadow-sm text-sm">
                                <option value="">All Types</option>
                                <option value="Contact" {{ request('model') == 'Contact' ? 'selected' : '' }}>Contact</option>
                                <option value="Event" {{ request('model') == 'Event' ? 'selected' : '' }}>Event</option>
                                <option value="User" {{ request('model') == 'User' ? 'selected' : '' }}>User</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">User</label>
                            <select name="user_id" class="block w-full border-gray-200 focus:border-gray-400 focus:ring-0 rounded-xl shadow-sm text-sm">
                                <option value="">All Users</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="submit" class="flex-1 px-4 py-2 bg-gray-900 text-white rounded-xl font-medium text-sm hover:bg-gray-800 transition-all shadow-sm">Filter</button>
                            <a href="{{ route('activity-logs.index') }}" class="px-4 py-2 text-gray-500 hover:text-gray-900 text-sm font-medium">Reset</a>
                        </div>
                    </form>
                </div>

                <div class="overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-tight">User</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-tight">Action</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-tight">Entity</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-tight">Description</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-tight">Time</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($logs as $log)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-900">{{ $log->user_name ?: 'System' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                                            {{ $log->action === 'created' ? 'bg-green-100 text-green-700' : '' }}
                                            {{ $log->action === 'updated' ? 'bg-blue-100 text-blue-700' : '' }}
                                            {{ $log->action === 'deleted' ? 'bg-red-100 text-red-700' : '' }}">
                                            {{ $log->action }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 font-bold">{{ $log->model_type }}</div>
                                        <div class="text-xs text-gray-600 font-bold">ID: {{ $log->model_id }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 font-medium">{{ $log->description }}</div>
                                        @if($log->metadata)
                                            <details class="mt-2 text-xs text-gray-700 cursor-pointer">
                                                <summary class="hover:text-gray-900 font-bold underline">View Changes</summary>
                                                @if(isset($log->metadata['old']) && isset($log->metadata['new']))
                                                    <div class="mt-3 bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                                                        <table class="min-w-full divide-y divide-gray-200">
                                                            <thead class="bg-gray-50">
                                                                <tr>
                                                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Field</th>
                                                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Old Value</th>
                                                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">New Value</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="bg-white divide-y divide-gray-100">
                                                                @foreach($log->metadata['new'] as $key => $newValue)
                                                                    @php
                                                                        $oldValue = $log->metadata['old'][$key] ?? '-';
                                                                        $isChanged = json_encode($oldValue) !== json_encode($newValue);
                                                                    @endphp
                                                                    @if($isChanged)
                                                                        <tr>
                                                                            <td class="px-4 py-3 text-xs font-bold text-gray-900 uppercase">{{ str_replace('_', ' ', $key) }}</td>
                                                                            <td class="px-4 py-3 text-xs text-red-600 line-through bg-red-50/50">
                                                                                {{ is_array($oldValue) ? implode(', ', $oldValue) : ($oldValue ?: '-') }}
                                                                            </td>
                                                                            <td class="px-4 py-3 text-xs text-green-600 bg-green-50/50 font-bold">
                                                                                {{ is_array($newValue) ? implode(', ', $newValue) : ($newValue ?: '-') }}
                                                                            </td>
                                                                        </tr>
                                                                    @endif
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @else
                                                    <div class="mt-3 bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                                                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-4">
                                                            @foreach($log->metadata as $key => $value)
                                                                <div class="sm:col-span-1">
                                                                    <dt class="text-xs font-bold text-gray-500 uppercase">{{ str_replace('_', ' ', $key) }}</dt>
                                                                    <dd class="mt-1 text-sm text-gray-900">{{ is_array($value) ? implode(', ', $value) : ($value ?: '-') }}</dd>
                                                                </div>
                                                            @endforeach
                                                        </dl>
                                                    </div>
                                                @endif
                                            </details>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">
                                        {{ $log->created_at->diffForHumans() }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500 italic">No activity logs found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-6 border-t border-gray-100">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
