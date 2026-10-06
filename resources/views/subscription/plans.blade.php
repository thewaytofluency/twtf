<x-layouts.student title="Choose a Plan">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Choose a Plan</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-4xl">
        @foreach ($plans as $plan)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col">
                <h2 class="text-lg font-bold text-gray-800">{{ $plan->name }}</h2>
                @if ($plan->description)
                    <p class="text-gray-600 text-sm mt-2 flex-1">{{ $plan->description }}</p>
                @endif
                <p class="text-2xl font-extrabold text-blue-600 mt-4">
                    {{ number_format($plan->fee, 2) }} MZN<span class="text-sm font-medium text-gray-500">/month</span>
                </p>

                @if ($plan->id === $currentPlanId)
                    <span class="mt-6 inline-flex items-center justify-center gap-1 px-6 py-2 rounded-full bg-gray-100 text-gray-500 font-semibold text-sm">
                        Current Plan
                    </span>
                @else
                    <a href="{{ route('subscription.request', $plan) }}" class="mt-6 inline-flex items-center justify-center gap-1 bg-blue-600 text-white font-bold px-6 py-2 rounded-full hover:bg-blue-700 transition">
                        Select This Plan
                    </a>
                @endif
            </div>
        @endforeach
    </div>
</x-layouts.student>
