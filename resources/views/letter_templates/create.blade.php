<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-900 leading-tight tracking-tight">
                Create Letter Template
            </h2>
            <a href="{{ route('letter-templates.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">
                &larr; Back to Templates
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <form action="{{ route('letter-templates.store') }}" method="POST" class="p-6 md:p-8 space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-900 mb-1">Template Title <span class="text-red-500">*</span></label>
                            <input type="text" name="title" value="{{ old('title') }}" required class="block w-full border-gray-200 focus:border-gray-400 focus:ring-0 rounded-xl shadow-sm text-sm text-gray-900" placeholder="e.g., Surat Undangan Peserta" />
                            <x-input-error :messages="$errors->get('title')" class="mt-1" />
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-900 mb-1">Letter Body <span class="text-red-500">*</span></label>
                            <p class="text-xs text-gray-500 mb-2">Use <code>{nama}</code> where you want the recipient's name to automatically appear.</p>
                            <div class="bg-white text-gray-900 rounded-xl overflow-hidden border border-gray-200 focus-within:border-gray-400 transition-colors">
                                <div id="quill-editor" style="height: 300px;">{!! old('body') !!}</div>
                            </div>
                            <input type="hidden" name="body" id="body-input" value="{{ old('body') }}">
                            <x-input-error :messages="$errors->get('body')" class="mt-1" />
                        </div>

                        <div class="md:col-span-2 mt-8">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-bold text-gray-900">Header & Closing</h3>
                                <a href="{{ route('letter-assets.index') }}" target="_blank" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">Manage Assets &rarr;</a>
                            </div>
                            
                            <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100 space-y-6">
                                
                                <div>
                                    <label class="block text-sm font-semibold text-gray-900 mb-1">Header Logo (Optional)</label>
                                    <select name="logo_asset_id" class="block w-full border-gray-200 focus:border-gray-400 focus:ring-0 rounded-xl shadow-sm text-sm text-gray-900 bg-white">
                                        <option value="">-- No Logo / Use Placeholder --</option>
                                        @foreach($logos as $logo)
                                            <option value="{{ $logo->id }}" {{ old('logo_asset_id') == $logo->id ? 'selected' : '' }}>{{ $logo->name }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('logo_asset_id')" class="mt-1" />
                                </div>

                                <hr class="border-gray-200">

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-semibold text-gray-900 mb-1">Text Above Signature</label>
                                        <input type="text" name="sig_text_above" value="{{ old('sig_text_above', 'Hormat Kami,') }}" class="block w-full border-gray-200 focus:border-gray-400 focus:ring-0 rounded-xl shadow-sm text-sm text-gray-900 bg-white" placeholder="e.g. Hormat Kami," />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-900 mb-1">Kop / Stempel (Optional)</label>
                                        <select name="kop_asset_id" class="block w-full border-gray-200 focus:border-gray-400 focus:ring-0 rounded-xl shadow-sm text-sm text-gray-900 bg-white">
                                            <option value="">-- No Kop --</option>
                                            @foreach($kops as $kop)
                                                <option value="{{ $kop->id }}" {{ old('kop_asset_id') == $kop->id ? 'selected' : '' }}>{{ $kop->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-900 mb-1">Signature / TTD (Optional)</label>
                                        <select name="ttd_asset_id" class="block w-full border-gray-200 focus:border-gray-400 focus:ring-0 rounded-xl shadow-sm text-sm text-gray-900 bg-white">
                                            <option value="">-- No Signature --</option>
                                            @foreach($ttds as $ttd)
                                                <option value="{{ $ttd->id }}" {{ old('ttd_asset_id') == $ttd->id ? 'selected' : '' }}>{{ $ttd->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-900 mb-1">Signer Name</label>
                                        <input type="text" name="sig_name" value="{{ old('sig_name') }}" class="block w-full border-gray-200 focus:border-gray-400 focus:ring-0 rounded-xl shadow-sm text-sm text-gray-900 bg-white" placeholder="e.g. Pandita Ariaguna" />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-900 mb-1">Signer Position</label>
                                        <input type="text" name="sig_position" value="{{ old('sig_position') }}" class="block w-full border-gray-200 focus:border-gray-400 focus:ring-0 rounded-xl shadow-sm text-sm text-gray-900 bg-white" placeholder="e.g. Ketua Panitia" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-100 flex justify-end gap-3">
                        <a href="{{ route('letter-templates.index') }}" class="px-5 py-2.5 border border-gray-200 rounded-full font-medium text-sm text-gray-700 hover:bg-gray-50 focus:outline-none transition-all shadow-sm">Cancel</a>
                        <button type="submit" onclick="document.getElementById('body-input').value = quill.root.innerHTML" class="px-5 py-2.5 bg-gray-900 border border-transparent rounded-full font-medium text-sm text-white hover:bg-gray-800 focus:outline-none transition-all shadow-sm">Save Template</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Include Quill Stylesheet & Script -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        var quill = new Quill('#quill-editor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'align': [] }],
                    ['clean']
                ]
            }
        });
    </script>
    <style>
        .ql-editor { 
            font-size: 15px; 
            color: inherit !important;
            background-color: transparent !important;
        }
        .ql-editor * {
            color: inherit !important;
        }
        .ql-editor.ql-blank::before {
            color: inherit !important;
            opacity: 0.6;
            font-style: normal !important;
        }
    </style>
</x-app-layout>
