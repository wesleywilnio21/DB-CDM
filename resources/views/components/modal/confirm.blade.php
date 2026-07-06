@props([
    'name',
    'title' => 'Confirm Action',
    'message' => 'Are you sure you want to proceed?',
    'confirmText' => 'Confirm',
    'cancelText' => 'Cancel',
    'action' => '',
    'method' => 'POST',
    'formId' => null
])

<x-modal :name="$name" focusable>
    <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900">
            {{ $title }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ $message }}
        </p>

        <div class="mt-6 flex justify-end">
            <x-secondary-button x-on:click="$dispatch('close')">
                {{ $cancelText }}
            </x-secondary-button>

            @if($formId)
                <x-primary-button class="ms-3" x-on:click="document.getElementById('{{ $formId }}').submit()">
                    {{ $confirmText }}
                </x-primary-button>
            @else
                <form method="POST" action="{{ $action }}" class="inline ms-3">
                    @csrf
                    @if(strtoupper($method) !== 'POST')
                        @method($method)
                    @endif
                    <x-primary-button>
                        {{ $confirmText }}
                    </x-primary-button>
                </form>
            @endif
        </div>
    </div>
</x-modal>
