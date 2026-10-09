<x-layouts.admin title="Subscriptions">
    <div class="mb-6 flex justify-between items-center">
        <div class="flex flex-wrap gap-2">
            @foreach (['pending' => 'Pending', 'active' => 'Active', 'rejected' => 'Rejected', 'cancelled' => 'Cancelled', 'all' => 'All'] as $value => $label)
                <a
                    href="{{ route('admin.subscriptions.index', ['status' => $value]) }}"
                    class="px-4 py-2 rounded-full text-sm font-medium transition {{ $status === $value ? 'bg-blue-600 text-white' : 'bg-white text-gray-600 border border-gray-300 hover:bg-gray-50' }}"
                >
                    {{ $label }}
                </a>
            @endforeach
        </div>
        <a href="{{ route('admin.subscriptions.create') }}" class="bg-blue-600 text-white font-bold px-6 py-2 rounded-full hover:bg-blue-700 transition">
            Record Subscription
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ends</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($subscriptions as $subscription)
                    <tr>
                        <td class="px-6 py-4 text-gray-900">{{ $subscription->user->name }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $subscription->plan->name }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ number_format($subscription->amount, 2) }} MZN</td>
                        <td class="px-6 py-4 text-gray-600">{{ $subscription->method->label() }}</td>
                        <td class="px-6 py-4">
                            @php
                                $badgeClass = match ($subscription->status->value) {
                                    'active' => 'bg-green-100 text-green-700',
                                    'pending' => 'bg-yellow-100 text-yellow-700',
                                    'rejected', 'cancelled' => 'bg-red-100 text-red-700',
                                    default => 'bg-gray-100 text-gray-600',
                                };
                            @endphp
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $badgeClass }}">
                                {{ $subscription->status->label() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-600">{{ $subscription->ends_at?->format('Y-m-d') ?? '-' }}</td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            @if ($subscription->proof_path)
                                <a href="{{ route('admin.subscriptions.proof', $subscription) }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:text-blue-700 font-medium mr-3">
                                    View Proof
                                </a>
                            @endif
                            @if ($subscription->status->value === 'pending')
                                <form method="POST" action="{{ route('admin.subscriptions.approve', $subscription) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-700 font-medium mr-3">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('admin.subscriptions.reject', $subscription) }}" class="inline" onsubmit="return confirm('Reject this subscription request?')">
                                    @csrf
                                    <button type="submit" class="text-red-600 hover:text-red-700 font-medium">Reject</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">No subscriptions found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $subscriptions->links() }}
    </div>
</x-layouts.admin>
