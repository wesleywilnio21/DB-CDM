<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-900 leading-tight tracking-tight">
                Bulk Generate Letters
            </h2>
            <a href="{{ route('letters.index', $event) }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">
                &larr; Back to Letters Log
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <form action="{{ route('letters.bulk-store', $event) }}" method="POST" enctype="multipart/form-data" class="p-6 md:p-8 space-y-8">
                    @csrf
                    
                    @if ($errors->any())
                        <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl text-sm">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Select Template -->
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4 border-b pb-2">Select Template</h3>
                        <label class="block text-sm font-semibold text-gray-900 mb-1">Which letter template to use?</label>
                        @if($templates->isEmpty())
                            <div class="p-4 bg-amber-50 rounded-xl border border-amber-200 flex items-start">
                                <svg class="w-5 h-5 text-amber-500 mt-0.5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                <div class="text-sm text-amber-800">
                                    You don't have any letter templates yet. 
                                    <a href="{{ route('letter-templates.create') }}" class="underline font-semibold hover:text-amber-900">Create a template first</a>.
                                </div>
                            </div>
                        @else
                            <select name="template_id" required class="block w-full border-gray-200 focus:border-indigo-400 focus:ring-indigo-200 rounded-xl shadow-sm text-sm text-gray-900 bg-white">
                                <option value="">-- Choose a Template --</option>
                                @foreach($templates as $template)
                                    <option value="{{ $template->id }}">{{ $template->title }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>

                    <div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4 border-b pb-2">Provide Recipient Names</h3>
                        <p class="text-sm text-gray-500 mb-6">You can type/paste names manually, upload an Excel file, or do both. A letter will be generated for each unique name.</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Option 1: Manual Input -->
                            <div class="space-y-3">
                                <label class="block text-sm font-semibold text-gray-900">
                                    Option 1: Manual Entry
                                </label>
                                <p class="text-xs text-gray-500">Paste or type names here, one name per line.</p>
                                <textarea name="manual_names" rows="8" class="block w-full border-gray-200 focus:border-indigo-400 focus:ring-indigo-200 rounded-xl shadow-sm text-sm text-gray-900 font-mono" placeholder="Budi Santoso&#10;Siti Aminah&#10;Ahmad Hidayat"></textarea>
                            </div>

                            <!-- Option 2: Excel Upload -->
                            <div class="space-y-3">
                                <label class="block text-sm font-semibold text-gray-900">
                                    Option 2: Excel / CSV Upload
                                </label>
                                <p class="text-xs text-gray-500">Upload an Excel (.xlsx) or CSV file. Ensure the names are in the <strong>first column (Column A)</strong>.</p>
                                
                                <div class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl hover:bg-gray-50 transition-colors">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600 justify-center">
                                            <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                <span>Upload a file</span>
                                                <input id="file-upload" name="excel_file" type="file" class="sr-only" accept=".xlsx,.xls,.csv">
                                            </label>
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">
                                            XLSX, CSV up to 5MB
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-100 flex justify-end">
                        <button type="submit" class="px-6 py-3 bg-indigo-600 border border-transparent rounded-full font-bold text-sm text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all shadow-md flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            Generate Letters (Download ZIP)
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
