<x-layouts.admin title="Students">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Plan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($users as $user)
                    <tr>
                        <td class="px-6 py-4 text-gray-900">{{ $user->name }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $user->email }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $user->currentSubscription?->plan?->name ?? 'Free' }}</td>
                        <td class="px-6 py-4">
                            @if ($user->isActive())
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">Active</span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <form
                                method="POST"
                                action="{{ route('admin.users.toggle-status', $user) }}"
                                onsubmit="return confirm('{{ $user->isActive() ? 'Deactivate' : 'Activate' }} {{ $user->name }}?')"
                            >
                                @csrf
                                @method('PATCH')
                                @if ($user->isActive())
                                    <button type="submit" class="text-red-600 hover:text-red-700 font-medium">Deactivate</button>
                                @else
                                    <button type="submit" class="text-green-600 hover:text-green-700 font-medium">Activate</button>
                                @endif
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">No students yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
</x-layouts.admin>
