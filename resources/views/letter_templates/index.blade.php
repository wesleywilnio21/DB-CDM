<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Letter Templates') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4 flex justify-end">
                <a href="{{ route('letter-templates.create') }}" class="bg-gray-900 text-white px-4 py-2 rounded shadow hover:bg-gray-800">
                    + Create Template
                </a>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b">
                                <th class="p-3">#</th>
                                <th class="p-3">Name</th>
                                <th class="p-3">Number Format</th>
                                <th class="p-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($templates as $template)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="p-3">{{ $loop->iteration }}</td>
                                    <td class="p-3 font-semibold">{{ $template->name }}</td>
                                    <td class="p-3 font-mono text-sm">{{ $template->number_format }}</td>
                                    <td class="p-3 text-right flex justify-end space-x-2">
                                        <a href="{{ route('letter-templates.edit', $template->id) }}" class="text-blue-600 hover:underline">Edit</a>
                                        <form action="{{ route('letter-templates.destroy', $template->id) }}" method="POST" onsubmit="return confirm('Delete this template?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-4 text-center text-gray-500">No templates found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4">
                        {{ $templates->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
