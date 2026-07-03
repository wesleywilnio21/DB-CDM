<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Generate New Letter') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                
                @if(session('success'))
                    <div class="mb-4 bg-green-100 text-green-700 p-4 rounded">{{ session('success') }}</div>
                @endif
                
                <form action="{{ route('letter-documents.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Select Template</label>
                        <select name="letter_template_id" id="template-select" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                            <option value="">-- Choose Template --</option>
                            @foreach(\App\Models\LetterTemplate::all() as $template)
                                <option value="{{ $template->id }}">{{ $template->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Select Contact (Umat) [Optional]</label>
                        <select name="contact_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="">-- No specific contact / General --</option>
                            @foreach(\App\Models\Contact::orderBy('name')->get() as $contact)
                                <option value="{{ $contact->id }}">{{ $contact->name }} ({{ $contact->phone }})</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">If selected, the placeholder like @{{nama_umat}} will be replaced automatically upon printing.</p>
                    </div>

                    <!-- Dynamic Variables Form Container -->
                    <div id="dynamic-variables-container" class="mb-4 p-4 bg-gray-50 rounded-md border hidden">
                        <h3 class="text-md font-semibold text-gray-800 mb-3 border-b pb-2">Fill the template variables</h3>
                        <div id="variables-inputs" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Input will be rendered here via JS -->
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 mt-6">
                        <a href="{{ route('letter-documents.index') }}" class="px-4 py-2 border rounded-md text-gray-600 hover:bg-gray-50">Cancel</a>
                        <button type="submit" class="bg-gray-900 text-white px-4 py-2 rounded-md shadow hover:bg-gray-800">Generate Letter Number</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JS for fetching template variables -->
    <script>
        document.getElementById('template-select').addEventListener('change', function() {
            var templateId = this.value;
            var container = document.getElementById('dynamic-variables-container');
            var inputsArea = document.getElementById('variables-inputs');
            
            inputsArea.innerHTML = '';
            
            if(!templateId) {
                container.classList.add('hidden');
                return;
            }

            fetch('/letter-templates/' + templateId + '/variables')
                .then(response => response.json())
                .then(variables => {
                    if(variables.length > 0) {
                        container.classList.remove('hidden');
                        variables.forEach(variable => {
                            var wrapper = document.createElement('div');
                            
                            var labelText = variable.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
                            var label = document.createElement('label');
                            label.className = 'block text-sm font-medium text-gray-700';
                            label.innerText = labelText;
                            
                            var input = document.createElement('input');
                            input.type = 'text';
                            input.name = 'variables[' + variable + ']';
                            input.className = 'mt-1 block w-full rounded-md border-gray-300 shadow-sm';
                            input.required = true;

                            wrapper.appendChild(label);
                            wrapper.appendChild(input);
                            inputsArea.appendChild(wrapper);
                        });
                    } else {
                        container.classList.add('hidden');
                    }
                });
        });
    </script>
</x-app-layout>