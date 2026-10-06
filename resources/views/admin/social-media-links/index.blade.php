<x-layouts.admin title="Social Links">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Platform</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">URL</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Visible</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($socialMediaLinks as $link)
                    <tr>
                        <td class="px-6 py-4 text-gray-900">{{ $link->platform->label() }}</td>
                        <td class="px-6 py-4 text-gray-600 truncate max-w-xs">{{ $link->url }}</td>
                        <td class="px-6 py-4">
                            @if ($link->is_visible)
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">Visible</span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600">Hidden</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <a href="{{ route('admin.social-media-links.edit', $link) }}" class="text-blue-600 hover:text-blue-700 font-medium">Edit</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-layouts.admin>
