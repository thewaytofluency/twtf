<x-layouts.student title="My Subscription">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">My Subscription</h1>

    @if (session('status'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
            {{ session('status') }}
        </div>
    @endif

    @if (session('info'))
        <div class="mb-6 bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-lg">
            {{ session('info') }}
        </div>
    @endif

    @if ($pendingSubscription)
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 mb-8">
            <p class="font-semibold text-yellow-800">Pending Review</p>
            <p class="text-yellow-700 text-sm mt-1">
                Your request for the <strong>{{ $pendingSubscription->plan->name }}</strong> plan is awaiting review.
                We'll update this page once it's approved.
            </p>
        </div>
    @elseif ($currentSubscription)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
            <p class="text-sm font-medium text-gray-600">Current Plan</p>
            <p class="text-2xl font-bold text-gray-900">{{ $currentSubscription->plan->name }}</p>
            <p class="text-sm text-gray-500 mt-1">Renews / expires {{ $currentSubscription->ends_at->format('M j, Y') }}</p>
            <a href="{{ route('subscription.plans') }}" class="inline-block mt-4 text-sm font-medium text-blue-600 hover:text-blue-700">
                Change plan
            </a>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
            <p class="text-sm font-medium text-gray-600">Current Plan</p>
            <p class="text-2xl font-bold text-gray-900">Free</p>
            <a href="{{ route('subscription.plans') }}" class="inline-block mt-4 bg-blue-600 text-white font-bold px-6 py-2 rounded-full hover:bg-blue-700 transition">
                Choose a Plan
            </a>
        </div>
    @endif

    @if ($history->isNotEmpty())
        <h2 class="text-lg font-semibold text-gray-700 mb-4">History</h2>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($history as $subscription)
                        <tr>
                            <td class="px-6 py-4 text-gray-900">{{ $subscription->plan->name }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ number_format($subscription->amount, 2) }} MZN</td>
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
                                @if ($subscription->status->value === 'rejected' && $subscription->admin_notes)
                                    <p class="text-xs text-gray-500 mt-1">{{ $subscription->admin_notes }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ $subscription->created_at->format('M j, Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-layouts.student>
