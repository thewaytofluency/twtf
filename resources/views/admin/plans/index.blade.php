<x-layouts.admin title="Plans">
    <div class="mb-6 flex justify-end">
        <a href="{{ route('admin.plans.create') }}" class="inline-flex items-center gap-2 bg-blue-600 text-white font-bold px-6 py-2 rounded-full hover:bg-blue-700 transition">
            <x-lucide-plus class="w-4 h-4" />
            New Plan
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fee</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Access</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subscribers</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($plans as $plan)
                    <tr>
                        <td class="px-6 py-4 text-gray-900">
                            {{ $plan->name }}
                            @if ($plan->is_popular)
                                <span class="ml-2 inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-blue-50 text-blue-600 text-xs font-semibold">
                                    <x-lucide-star class="w-3 h-3" /> Popular
                                </span>
                            @endif
                            <div class="text-xs text-gray-400">{{ $plan->code }}</div>
                        </td>
                        <td class="px-6 py-4 text-gray-600">{{ $plan->formattedFee() }}</td>
                        <td class="px-6 py-4 text-gray-600">Level {{ $plan->access_level }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $plan->subscriptions_count }}</td>
                        <td class="px-6 py-4">
                            @if ($plan->is_active)
                                <span class="px-2 py-0.5 rounded-full bg-green-50 text-green-700 text-xs font-semibold">Active</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-500 text-xs font-semibold">Hidden</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <a href="{{ route('admin.plans.edit', $plan) }}" class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-700 font-medium mr-4">
                                <x-lucide-pencil class="w-4 h-4" /> Edit
                            </a>
                            <form method="POST" action="{{ route('admin.plans.destroy', $plan) }}" class="inline" onsubmit="return confirm('Delete this plan?')">
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
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">No plans yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.admin>
