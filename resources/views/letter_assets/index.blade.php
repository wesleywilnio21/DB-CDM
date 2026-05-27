<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-900 leading-tight tracking-tight">
            Letter Assets Library
        </h2>
    </x-slot>

    <div class="py-10" x-data="{ showUpload: false, uploadType: 'logo', uploadTitle: 'Upload Logo' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if (session('success'))
                <div class="text-green-700 bg-green-50 border border-green-200 p-4 rounded-xl flex items-center">
                    <svg class="w-5 h-5 mr-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- LOGO SECTION -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                        <h3 class="font-bold text-gray-900">Header Logos</h3>
                        <button @click="showUpload = true; uploadType = 'logo'; uploadTitle = 'Upload Header Logo'" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">+ Upload</button>
                    </div>
                    <div class="p-6 space-y-4">
                        @forelse($assets->where('type', 'logo') as $asset)
                            <div class="flex items-center justify-between p-3 border border-gray-100 rounded-xl">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-gray-100 rounded flex items-center justify-center text-xl">🖼</div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">{{ $asset->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $asset->created_at->format('M d, Y') }}</div>
                                    </div>
                                </div>
                                <form action="{{ route('letter-assets.destroy', $asset) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this logo?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 p-2">&times;</button>
                                </form>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 text-center py-4">No logos uploaded yet.</p>
                        @endforelse
                    </div>
                </div>

                <!-- KOP SECTION -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                        <h3 class="font-bold text-gray-900">Kop / Stempel</h3>
                        <button @click="showUpload = true; uploadType = 'kop'; uploadTitle = 'Upload Kop/Stempel'" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">+ Upload</button>
                    </div>
                    <div class="p-6 space-y-4">
                        @forelse($assets->where('type', 'kop') as $asset)
                            <div class="flex items-center justify-between p-3 border border-gray-100 rounded-xl">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-gray-100 rounded flex items-center justify-center text-xl">⭕</div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">{{ $asset->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $asset->created_at->format('M d, Y') }}</div>
                                    </div>
                                </div>
                                <form action="{{ route('letter-assets.destroy', $asset) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this kop?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 p-2">&times;</button>
                                </form>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 text-center py-4">No kop/stempel uploaded yet.</p>
                        @endforelse
                    </div>
                </div>

                <!-- TTD SECTION -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                        <h3 class="font-bold text-gray-900">Signatures (TTD)</h3>
                        <button @click="showUpload = true; uploadType = 'ttd'; uploadTitle = 'Upload Signature'" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">+ Upload</button>
                    </div>
                    <div class="p-6 space-y-4">
                        @forelse($assets->where('type', 'ttd') as $asset)
                            <div class="flex items-center justify-between p-3 border border-gray-100 rounded-xl">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-gray-100 rounded flex items-center justify-center text-xl">✍</div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">{{ $asset->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $asset->created_at->format('M d, Y') }}</div>
                                    </div>
                                </div>
                                <form action="{{ route('letter-assets.destroy', $asset) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this signature?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 p-2">&times;</button>
                                </form>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 text-center py-4">No signatures uploaded yet.</p>
                        @endforelse
                        <p class="text-xs text-gray-400 mt-4 italic">Previews are hidden for security. Signatures are stored in private storage.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upload Modal -->
        <div x-show="showUpload" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div x-show="showUpload" class="fixed inset-0 transition-opacity" aria-hidden="true" @click="showUpload = false">
                    <div class="absolute inset-0 bg-gray-900 opacity-50"></div>
                </div>

                <div x-show="showUpload" class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    <form action="{{ route('letter-assets.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="type" x-model="uploadType">
                        
                        <div class="bg-white px-6 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-xl leading-6 font-bold text-gray-900 mb-6" x-text="uploadTitle"></h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-900 mb-1">Asset Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" required class="block w-full border-gray-200 focus:border-gray-400 focus:ring-0 rounded-xl shadow-sm text-sm text-gray-900" placeholder="e.g., Logo CDM 2026" />
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-semibold text-gray-900 mb-1">Image File <span class="text-red-500">*</span></label>
                                    <input type="file" name="file" accept="image/png, image/jpeg, image/svg+xml" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition-colors" />
                                    <p class="text-xs text-gray-500 mt-1">PNG, JPG, SVG allowed. Max 2MB.</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3">
                            <button type="submit" class="px-5 py-2.5 bg-gray-900 border border-transparent rounded-full font-medium text-sm text-white hover:bg-gray-800 focus:outline-none transition-all shadow-sm">
                                Upload
                            </button>
                            <button type="button" @click="showUpload = false" class="px-5 py-2.5 border border-gray-200 rounded-full font-medium text-sm text-gray-700 hover:bg-gray-50 focus:outline-none transition-all shadow-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
