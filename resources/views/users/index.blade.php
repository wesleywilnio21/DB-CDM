<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-900 leading-tight tracking-tight">
            {{ __('User Management') }}
        </h2>
    </x-slot>

    <div class="py-10" x-data="{ 
        showCreate: {{ $errors->any() && !old('_method') ? 'true' : 'false' }}, 
        showEdit: {{ $errors->any() && old('_method') == 'PATCH' ? 'true' : 'false' }}, 
        editUser: { id: '', name: '', email: '', role: '' }
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
                    <h3 class="text-xl font-bold text-gray-900 tracking-tight">System Users</h3>
                    <button @click="showCreate = true" class="inline-flex items-center justify-center px-5 py-2.5 bg-gray-900 border border-transparent rounded-full font-bold text-sm text-white hover:bg-black transition-all shadow-md">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Add New User
                    </button>
                </div>
                <div class="p-6 md:p-8">
                    <div class="overflow-hidden border border-gray-100 rounded-2xl">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50/50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">Role</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-900 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach($users as $user)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="h-10 w-10 rounded-full bg-gray-900 flex items-center justify-center text-white font-bold">
                                                    {{ substr($user->name, 0, 1) }}
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-bold text-gray-900">
                                                        {{ $user->name }}
                                                        @if($user->id === Auth::id())
                                                            <span class="ml-2 text-[10px] bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full uppercase font-black">You</span>
                                                        @endif
                                                    </div>
                                                    <div class="text-xs text-gray-700 font-bold">{{ $user->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                                                {{ $user->role === 'super_admin' ? 'bg-amber-100 text-amber-800 border border-amber-200' : 
                                                   ($user->role === 'admin' ? 'bg-purple-100 text-purple-800 border border-purple-200' : 
                                                   ($user->role === 'staff' ? 'bg-gray-100 text-gray-800 border border-gray-200' : 'bg-blue-100 text-blue-800 border border-blue-200' )) }}">
                                                {{ str_replace('_', ' ', $user->role) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold">
                                            <div class="flex items-center justify-end gap-3">
                                                <button @click="
                                                        editUser = { id: {{ $user->id }}, name: '{{ addslashes($user->name) }}', email: '{{ $user->email }}', role: '{{ $user->role }}' }; 
                                                        showEdit = true;
                                                    " 
                                                    class="text-indigo-600 hover:text-indigo-900">Edit</button>
                                                
                                                @if(Auth::user()->isSuperAdmin() && $user->id !== Auth::id())
                                                    <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('WARNING: This action cannot be undone. Delete this user?');" class="inline-block">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="inline-flex items-center px-3 py-1 bg-red-50 text-red-700 border border-red-200 rounded-lg hover:bg-red-100 transition-colors">
                                                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                            Delete
                                                        </button>
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
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Modal -->
        <div x-show="showCreate" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-900 opacity-50"></div>
                <div class="bg-white rounded-3xl shadow-xl transform transition-all max-w-lg w-full p-8 z-10">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-gray-900">Add New User</h3>
                        <button @click="showCreate = false" class="text-gray-400 hover:text-gray-600">&times;</button>
                    </div>
                    <form method="POST" action="{{ route('users.store') }}" class="space-y-4">
                        @csrf
                        <div>
                            <x-input-label for="name" :value="__('Name')" class="text-gray-900 font-bold" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full rounded-xl" :value="old('name')" required />
                            <x-input-error :messages="$errors->get('name')" />
                        </div>
                        <div>
                            <x-input-label for="email" :value="__('Email')" class="text-gray-900 font-bold" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full rounded-xl" :value="old('email')" required />
                            <x-input-error :messages="$errors->get('email')" />
                        </div>
                        <div>
                            <x-input-label for="role" :value="__('Role')" class="text-gray-900 font-bold" />
                            <select name="role" class="mt-1 block w-full border-gray-300 rounded-xl focus:border-gray-900 focus:ring-0">
                                @foreach($roles as $role)
                                    @if($role->name === 'super_admin' && !Auth::user()->isSuperAdmin())
                                        @continue
                                    @endif
                                    <option value="{{$role->name}}">{{ucwords(str_replace('_', ' ', $role->name))}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="password" :value="__('Password')" class="text-gray-900 font-bold" />
                            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full rounded-xl" required />
                        </div>
                        <div>
                            <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-gray-900 font-bold" />
                            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full rounded-xl" required />
                        </div>
                        <div class="pt-4 flex justify-end gap-3">
                            <button type="button" @click="showCreate = false" class="px-5 py-2.5 border border-gray-200 rounded-full font-bold text-gray-700">Cancel</button>
                            <button type="submit" class="px-5 py-2.5 bg-gray-900 text-white rounded-full font-bold">Create User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div x-show="showEdit" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-900 opacity-50"></div>
                <div class="bg-white rounded-3xl shadow-xl transform transition-all max-w-lg w-full p-8 z-10">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-gray-900">Edit User</h3>
                        <button @click="showEdit = false" class="text-gray-400 hover:text-gray-600">&times;</button>
                    </div>
                    <form method="POST" :action="`/users/${editUser.id}`" class="space-y-4">
                        @csrf
                        @method('PATCH')
                        <div>
                            <x-input-label for="edit_name" :value="__('Name')" class="text-gray-900 font-bold" />
                            <x-text-input id="edit_name" name="name" type="text" class="mt-1 block w-full rounded-xl" x-model="editUser.name" required />
                        </div>
                        <div>
                            <x-input-label for="edit_email" :value="__('Email')" class="text-gray-900 font-bold" />
                            <x-text-input id="edit_email" name="email" type="email" class="mt-1 block w-full rounded-xl" x-model="editUser.email" required />
                        </div>
                        <div>
                            <x-input-label for="edit_role" :value="__('Role')" class="text-gray-900 font-bold" />
                            <select name="role" x-model="editUser.role" class="mt-1 block w-full border-gray-300 rounded-xl focus:border-gray-900 focus:ring-0">
                                @foreach($roles as $role)
                                    <option value="{{$role->name}}" :disabled="'{{$role->name}}' === 'super_admin' && !{{ Auth::user()->isSuperAdmin() ? 'true' : 'false' }}">
                                        {{ucwords(str_replace('_', ' ', $role->name))}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="pt-4 border-t border-gray-100">
                            <p class="text-xs text-gray-500 mb-2 font-bold">Leave password blank to keep current password.</p>
                            <x-input-label for="edit_password" :value="__('New Password')" class="text-gray-900 font-bold" />
                            <x-text-input id="edit_password" name="password" type="password" class="mt-1 block w-full rounded-xl" />
                        </div>
                        <div>
                            <x-input-label for="edit_password_confirmation" :value="__('Confirm New Password')" class="text-gray-900 font-bold" />
                            <x-text-input id="edit_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full rounded-xl" />
                        </div>
                        <div class="pt-4 flex justify-end gap-3">
                            <button type="button" @click="showEdit = false" class="px-5 py-2.5 border border-gray-200 rounded-full font-bold text-gray-700">Cancel</button>
                            <button type="submit" class="px-5 py-2.5 bg-gray-900 text-white rounded-full font-bold">Update User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
