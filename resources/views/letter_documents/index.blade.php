<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Generated Letters Database') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4 flex justify-end">
                <a href="{{ route('letter-documents.create') }}" class="bg-gray-900 text-white px-4 py-2 rounded shadow hover:bg-gray-800">
                    + Generate New Letter
                </a>
            </div>
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b">
                                <th class="p-3">Reference No.</th>
                                <th class="p-3">Template</th>
                                <th class="p-3">Contact Person</th>
                                <th class="p-3">Generated At</th>
                                <th class="p-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($documents as $doc)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="p-3 font-mono font-semibold">{{ $doc->letter_number }}</td>
                                    <td class="p-3">{{ $doc->letterTemplate->name ?? '-' }}</td>
                                    <td class="p-3">{{ $doc->contact->name ?? 'No Contact' }}</td>
                                    <td class="p-3 text-sm text-gray-500">{{ $doc->created_at->format('d M Y H:i') }}</td>
                                    <td class="p-3 text-right flex justify-end space-x-2">
                                        <a href="{{ route('letter-documents.show', $doc->id) }}" class="text-blue-600 hover:underline">View/Print</a>
                                        <form action="{{ route('letter-documents.destroy', $doc->id) }}" method="POST" onsubmit="return confirm('Delete this document trace?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-4 text-center text-gray-500">No generated letters yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4">
                        {{ $documents->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
