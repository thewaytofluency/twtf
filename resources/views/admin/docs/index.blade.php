<x-layouts.admin title="Documents">
    <div class="mb-6 flex justify-end">
        <a href="{{ route('admin.docs.create') }}" class="bg-blue-600 text-white font-bold px-6 py-2 rounded-full hover:bg-blue-700 transition">
            New Document
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Level</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Access</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($docs as $doc)
                    <tr>
                        <td class="px-6 py-4 text-gray-900">{{ $doc->title }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $doc->original_filename }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $doc->course_level?->label() ?? '-' }}</td>
                        <td class="px-6 py-4 text-gray-600">Level {{ $doc->required_access_level }}</td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <a href="{{ route('admin.docs.edit', $doc) }}" class="text-blue-600 hover:text-blue-700 font-medium mr-4">Edit</a>
                            <form method="POST" action="{{ route('admin.docs.destroy', $doc) }}" class="inline" onsubmit="return confirm('Delete this document?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-700 font-medium">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">No documents yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $docs->links() }}
    </div>
</x-layouts.admin>
