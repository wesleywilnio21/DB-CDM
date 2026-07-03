<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Letter Template') }}
        </h2>
    </x-slot>

    <!-- Include Quill Theme -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <form action="{{ route('letter-templates.store') }}" method="POST" id="template-form" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Template Name</label>
                            <input type="text" name="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Number Format (e.g. {nomor}/CDM/{bulan_romawi}/{tahun})</label>
                            <input type="text" name="number_format" value="{nomor}/CDM/{bulan_romawi}/{tahun}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm font-mono text-sm" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4 pt-4 border-t">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Signatory Name</label>
                            <input type="text" name="signatory_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Signatory Position</label>
                            <input type="text" name="signatory_position" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Signature Image</label>
                            <input type="file" name="signature_image" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-100" accept="image/*">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Stamp Image</label>
                            <input type="file" name="stamp_image" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-100" accept="image/*">
                        </div>
                    </div>

                    <div class="mb-4 flex flex-col md:flex-row gap-4 border-t pt-4">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Template Content</label>
                            <!-- Hidden input to hold the HTML content -->
                            <input type="hidden" name="content" id="content-input">
                            <div class="bg-white border rounded shadow-sm">
                                <div id="editor-container" style="height: 500px;"></div>
                            </div>
                        </div>

                        <div class="w-full md:w-64 shrink-0 mt-6">
                            <div class="bg-gray-50 border rounded-md p-4 sticky top-4">
                                <h3 class="font-semibold text-gray-800 mb-2">Variables</h3>
                                <p class="text-xs text-gray-500 mb-4">Click to insert at cursor.</p>
                                
                                <div class="flex flex-col gap-2">
                                    @php
                                        $variables = [
                                            ['label' => 'Nomor Surat', 'key' => '@{{nomor_surat}}'],
                                            ['label' => 'Tanggal Surat', 'key' => '@{{tanggal_surat}}'],
                                            ['label' => 'Perihal', 'key' => '@{{perihal}}'],
                                            ['label' => 'Lampiran', 'key' => '@{{lampiran}}'],
                                            ['label' => 'Penerima', 'key' => '@{{penerima}}'],
                                            ['label' => 'Nama Acara', 'key' => '@{{nama_acara}}'],
                                            ['label' => 'Peran Penerima', 'key' => '@{{peran_penerima}}'],
                                            ['label' => 'Nama Kegiatan', 'key' => '@{{nama_kegiatan}}'],
                                            ['label' => 'Hari/Tanggal Kegiatan', 'key' => '@{{hari_tanggal_kegiatan}}'],
                                            ['label' => 'Waktu Kegiatan', 'key' => '@{{waktu_kegiatan}}'],
                                            ['label' => 'Tempat Kegiatan', 'key' => '@{{tempat_kegiatan}}'],
                                            ['label' => 'Tema Kegiatan', 'key' => '@{{tema_kegiatan}}'],
                                            ['label' => 'Nama Pengirim', 'key' => '@{{nama_pengirim}}'],
                                            ['label' => 'Jabatan Pengirim', 'key' => '@{{jabatan_pengirim}}'],
                                        ];
                                    @endphp
                                    @foreach($variables as $var)
                                    <button type="button" onclick="insertVariable('{{ str_replace('@', '', $var['key']) }}')" class="text-left px-3 py-2 bg-white hover:bg-gray-100 border border-gray-200 rounded text-sm text-blue-600 font-mono transition">
                                        {{ str_replace('@', '', $var['key']) }}
                                        <span class="block text-xs text-gray-500 font-sans mt-1">{{ $var['label'] }}</span>
                                    </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 mt-6 border-t pt-4">
                        <a href="{{ route('letter-templates.index') }}" class="px-4 py-2 border rounded-md text-gray-600 hover:bg-gray-50">Cancel</a>
                        <button type="submit" class="bg-gray-900 text-white px-4 py-2 rounded-md shadow hover:bg-gray-800">Save Template</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Include the Quill library -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        var quill = new Quill('#editor-container', {
            theme: 'snow',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline', 'strike'],        
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'align': [] }],
                    ['clean']                                         
                ]
            }
        });

        // Sync Quill HTML content to the hidden input before form submission
        var form = document.getElementById('template-form');
        form.onsubmit = function() {
            var contentInput = document.getElementById('content-input');
            contentInput.value = quill.root.innerHTML;
        };

        // Insert variables to cursor position
        function insertVariable(variableKey) {
            var range = quill.getSelection(true);
            if (range) {
                quill.insertText(range.index, variableKey);
                quill.setSelection(range.index + variableKey.length);
            } else {
                // If editor has no focus, append to end
                var length = quill.getLength();
                quill.insertText(length, variableKey);
            }
        }
    </script>
</x-app-layout>
