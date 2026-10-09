<x-layouts.admin title="Our Impact">
    <div class="mb-6 flex items-center justify-between gap-3">
        <p class="text-sm text-gray-500">These cards appear in the "Our Impact" section of the landing page, in this order.</p>
        <a href="{{ route('admin.impact.create') }}" class="inline-flex items-center gap-2 bg-blue-600 text-white font-bold px-6 py-2 rounded-full hover:bg-blue-700 transition whitespace-nowrap">
            <x-lucide-plus class="w-4 h-4" />
            New Impact Item
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 w-24 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($items as $item)
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">
                            {{-- Captured here: inside the inner @foreach, $loop is the inner loop. --}}
                            @php($rowFirst = $loop->first)
                            @php($rowLast = $loop->last)
                            <div class="flex items-center gap-1">
                                @foreach (['up' => 'arrow-up', 'down' => 'arrow-down'] as $direction => $icon)
                                    <form method="POST" action="{{ route('admin.impact.move', $item) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="direction" value="{{ $direction }}">
                                        <button type="submit" title="Move {{ $direction }}" aria-label="Move {{ $direction }}" class="p-1 rounded text-gray-500 hover:bg-gray-100 hover:text-gray-800 disabled:opacity-30" @disabled(($direction === 'up' && $rowFirst) || ($direction === 'down' && $rowLast))>
                                            <x-dynamic-component :component="'lucide-'.$icon" class="w-4 h-4" />
                                        </button>
                                    </form>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                @if ($item->badge_image_url)
                                    <img src="{{ $item->badge_image_url }}" alt="" class="w-12 h-12 rounded-full object-cover flex-shrink-0">
                                @else
                                    <span class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center text-xl flex-shrink-0">{{ $item->emoji }}</span>
                                @endif
                                <div class="min-w-0">
                                    <a href="{{ route('admin.impact.edit', $item) }}" class="font-medium text-gray-900 hover:text-blue-600">
                                        @if ($item->stat)<span class="text-blue-600 mr-1">{{ $item->stat }}</span>@endif{{ $item->title }}
                                    </a>
                                    <p class="text-sm text-gray-500 truncate max-w-md">{{ $item->description }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            @if ($item->is_active)
                                <span class="px-2 py-0.5 rounded-full bg-green-50 text-green-700 text-xs font-semibold">Visible</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-500 text-xs font-semibold">Hidden</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <a href="{{ route('admin.impact.edit', $item) }}" class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-700 font-medium mr-4">
                                <x-lucide-pencil class="w-4 h-4" /> Edit
                            </a>
                            <form method="POST" action="{{ route('admin.impact.destroy', $item) }}" class="inline" onsubmit="return confirm('Delete this item?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center gap-1 text-red-600 hover:text-red-700 font-medium">
                                    <x-lucide-trash-2 class="w-4 h-4" /> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">No impact items yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.admin>
