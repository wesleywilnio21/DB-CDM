<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Print Letter: ') }} {{ $letterDocument->letter_number }}
        </h2>
    </x-slot>

    <div class="py-12 print:py-0 print:m-0">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 print:max-w-none print:px-0">
            <div class="mb-4 flex justify-end gap-2">
                <a href="{{ route('letter-documents.print', $letterDocument) }}" target="_blank" class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 print:hidden">
                    Print / Save PDF
                </a>
                <a href="{{ route('letter-documents.envelope', $letterDocument) }}" target="_blank" class="bg-gray-600 text-white px-4 py-2 rounded shadow hover:bg-gray-700 print:hidden">
                    Print Envelope
                </a>
            </div>
            
            <div class="bg-white p-12 shadow-sm sm:rounded-lg print:shadow-none print:p-0">
                <!-- Kop Surat Default -->
                <x-letterhead />

                <!-- Preview / Print Area -->
                <div class="prose max-w-none text-black">
                    @php
                        // Automatic placeholder replacements when viewing/printing
                        $content = $letterDocument->letterTemplate->content ?? '';
                        $contact = $letterDocument->contact;
                        
                        // Default placeholders logic
                        if ($contact) {
                            $content = str_replace('{{nama_umat}}', $contact->name, $content);
                            $content = str_replace('{{alamat}}', $contact->address ?? '', $content);
                            $content = str_replace('{{telepon}}', $contact->phone ?? '', $content);
                        }
                        
                        $content = str_replace('{{tanggal_surat}}', $letterDocument->created_at->translatedFormat('d F Y'), $content);
                        $content = str_replace('{{nomor_surat}}', $letterDocument->letter_number, $content);

                        // Dynamic variables replacements
                        $variables = $letterDocument->variables ?? [];
                        if (is_array($variables)) {
                            foreach ($variables as $key => $value) {
                                $content = str_replace('{{' . $key . '}}', $value, $content);
                            }
                        }
                    @endphp
                    
                    {!! $content !!}
                </div>

                <!-- Bagian Tanda Tangan & Cap -->
                @if($letterDocument->letterTemplate->signatory_name)
                <div class="mt-16 print:mt-12 flex justify-start">
                    <div class="text-left" style="width: 300px;">
                        <p class="m-0 mb-1">Hormat Kami,</p>
                        <p class="m-0 mb-4">Mettacittena</p>
                        
                        <!-- Cap dan Tanda Tangan Wrapper -->
                        <div class="relative w-full h-32 my-4">
                            @if($letterDocument->letterTemplate->stamp_image)
                                <img src="{{ Storage::url($letterDocument->letterTemplate->stamp_image) }}" alt="Cap Surat" class="absolute left-8 top-0 h-32 w-32 object-contain opacity-80 mix-blend-multiply" style="z-index: 1;">
                            @endif

                            @if($letterDocument->letterTemplate->signature_image)
                                <img src="{{ Storage::url($letterDocument->letterTemplate->signature_image) }}" alt="Tanda Tangan" class="absolute left-0 top-4 h-24 object-contain" style="z-index: 2;">
                            @endif
                        </div>

                        <p class="m-0 font-bold underline">{{ $letterDocument->letterTemplate->signatory_name }}</p>
                        <p class="m-0 text-sm">{{ $letterDocument->letterTemplate->signatory_position }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>