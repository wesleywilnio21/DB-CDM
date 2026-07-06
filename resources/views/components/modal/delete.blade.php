@props([
    'name',
    'title' => 'Delete Item',
    'message' => 'Are you sure you want to delete this item? This action cannot be undone.',
    'confirmText' => 'Delete',
    'cancelText' => 'Cancel',
    'action' => ''
])

<x-modal :name="$name" focusable>
    <form method="POST" action="{{ $action }}" class="p-6">
        @csrf
        @method('DELETE')

        <h2 class="text-lg font-medium text-gray-900">
            {{ $title }}
        </h2>

        <p class="mt-4 text-sm text-gray-600">
            {{ $message }}
        </p>

        <div class="mt-6 flex justify-end">
            <x-secondary-button x-on:click="$dispatch('close')">
                {{ $cancelText }}
            </x-secondary-button>

            <x-danger-button class="ms-3">
                {{ $confirmText }}
            </x-danger-button>
        </div>
    </form>
</x-modal>
