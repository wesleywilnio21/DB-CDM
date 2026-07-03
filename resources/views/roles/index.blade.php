<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-900 leading-tight tracking-tight">
            {{ __('Role Management') }}
        </h2>
    </x-slot>

    <div class="py-10" x-data="{ 
        showCreate: {{ $errors->any() && !old('_method') ? 'true' : 'false' }}, 
        showEdit: {{ $errors->any() && old('_method') == 'PUT' ? 'true' : 'false' }}, 
        editRole: { id: '', name: '', permissions: [] }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if (session('success'))
                <div class="text-green-900 bg-green-50 border border-green-200 p-4 rounded-xl flex items-center font-bold">
                    <svg class="w-5 h-5 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="text-red-900 bg-red-50 border border-red-200 p-4 rounded-xl flex items-center font-bold">
                    <svg class="w-5 h-5 mr-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 md:p-8 border-b border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <h3 class="text-xl font-bold text-gray-900 tracking-tight">System Roles</h3>
                    <button @click="showCreate = true" class="inline-flex items-center justify-center px-5 py-2.5 bg-gray-900 border border-transparent rounded-full font-bold text-sm text-white hover:bg-black transition-all shadow-md">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Create New Role
                    </button>
                </div>
                <div class="p-6 md:p-8">
                    <div class="overflow-hidden border border-gray-100 rounded-2xl">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50/50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">Role Name</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">Permissions Given</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-900 uppercase tracking-wider w-32">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach($roles as $role)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="font-bold text-gray-900 text-sm">
                                                {{ ucwords(str_replace('_', ' ', $role->name)) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-wrap gap-2">
                                                @forelse($role->permissions as $permission)
                                                    <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-blue-50 text-blue-800 border border-blue-200">
                                                        {{ ucwords(str_replace('_', ' ', $permission->name)) }}
                                                    </span>
                                                @empty
                                                    <span class="text-xs text-gray-400 font-medium italic">No custom permissions explicitly assigned</span>
                                                @endforelse
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold">
                                            <div class="flex items-center justify-end gap-3">
                                                @php
                                                    $roleData = [
                                                        'id' => $role->id,
                                                        'name' => $role->name,
                                                        'permissions' => $role->permissions->pluck('name')->toArray()
                                                    ];
                                                @endphp
                                                <button @click="editRole = JSON.parse(atob('{{ base64_encode(json_encode($roleData)) }}')); showEdit = true" class="text-indigo-600 hover:text-indigo-900 transition-colors">Edit</button>
                                                
                                                @if($role->name !== 'super_admin')
                                                    <form action="{{ route('roles.destroy', $role->id) }}" method="POST" onsubmit="return confirm('Delete this role?');" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-500 hover:text-red-800 transition-colors">Delete</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6">
                        {{ $roles->links() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Modal -->
        <div x-show="showCreate" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-900 opacity-50" @click="showCreate = false"></div>
                <div class="bg-white rounded-3xl shadow-xl transform transition-all w-full max-w-2xl p-8 z-10">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-gray-900">Create New Role</h3>
                        <button @click="showCreate = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    
                    <form method="POST" action="{{ route('roles.store') }}" class="space-y-6">
                        @csrf
                        <div>
                            <x-input-label for="name" :value="__('Role Name')" class="text-gray-900 font-bold" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full rounded-xl" :value="old('name')" required placeholder="e.g editor" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>
                        
                        <div>
                            <x-input-label :value="__('Assign Permissions (Optional)')" class="text-gray-900 font-bold mb-3" />
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                @foreach($permissions as $permission)
                                    <label class="inline-flex items-center p-3 border border-gray-200 rounded-xl hover:bg-gray-50 cursor-pointer transition-colors">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" 
                                            class="rounded border-gray-300 text-gray-900 shadow-sm focus:ring-gray-900 focus:ring-offset-0 w-5 h-5"
                                            {{ (is_array(old('permissions')) && in_array($permission->name, old('permissions'))) ? 'checked' : '' }}>
                                        <span class="ml-3 text-sm font-medium text-gray-700">{{ ucwords(str_replace('_', ' ', $permission->name)) }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="pt-6 border-t border-gray-100 flex justify-end gap-3">
                            <button type="button" @click="showCreate = false" class="px-5 py-2.5 bg-white border border-gray-300 rounded-full font-bold text-gray-700 hover:bg-gray-50 transition-all text-sm">Cancel</button>
                            <button type="submit" class="px-5 py-2.5 bg-gray-900 border border-transparent rounded-full font-bold text-white hover:bg-black transition-all shadow-md text-sm">Save Role</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div x-show="showEdit" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-900 opacity-50" @click="showEdit = false"></div>
                <div class="bg-white rounded-3xl shadow-xl transform transition-all w-full max-w-2xl p-8 z-10">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-gray-900">Edit Role</h3>
                        <button @click="showEdit = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    
                    <form method="POST" :action="`/roles/${editRole.id}`" class="space-y-6">
                        @csrf
                        @method('PUT')
                        <div>
                            <x-input-label for="edit_name" :value="__('Role Name')" class="text-gray-900 font-bold" />
                            <x-text-input id="edit_name" name="name" type="text" class="mt-1 block w-full rounded-xl" x-model="editRole.name" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            <p class="text-xs text-gray-500 mt-2" x-show="editRole.name === 'super_admin'">Warning: super_admin permissions are generally locked conceptually. Renaming it is not recommended.</p>
                        </div>
                        
                        <div>
                            <x-input-label :value="__('Assign Permissions')" class="text-gray-900 font-bold mb-3" />
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                @foreach($permissions as $permission)
                                    <label class="inline-flex items-center p-3 border border-gray-200 rounded-xl hover:bg-gray-50 cursor-pointer transition-colors" 
                                           :class="editRole.permissions.includes('{{ $permission->name }}') ? 'bg-blue-50/30 border-blue-200' : ''">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" 
                                            class="rounded border-gray-300 text-gray-900 shadow-sm focus:ring-gray-900 focus:ring-offset-0 w-5 h-5"
                                            :checked="editRole.permissions.includes('{{ $permission->name }}')">
                                        <span class="ml-3 text-sm font-medium" :class="editRole.permissions.includes('{{ $permission->name }}') ? 'text-blue-900' : 'text-gray-700'">{{ ucwords(str_replace('_', ' ', $permission->name)) }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="pt-6 border-t border-gray-100 flex justify-end gap-3">
                            <button type="button" @click="showEdit = false" class="px-5 py-2.5 bg-white border border-gray-300 rounded-full font-bold text-gray-700 hover:bg-gray-50 transition-all text-sm">Cancel</button>
                            <button type="submit" class="px-5 py-2.5 bg-gray-900 border border-transparent rounded-full font-bold text-white hover:bg-black transition-all shadow-md text-sm">Update Role</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>