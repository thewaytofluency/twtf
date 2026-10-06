<x-layouts.student title="Request {{ $plan->name }}">
    @php
        $inputClass = 'w-full px-4 py-2 border border-gray-300 rounded-md text-gray-900 focus:outline-none focus:ring-blue-600 focus:border-blue-600';
    @endphp

    <a href="{{ route('subscription.plans') }}" class="inline-flex items-center gap-1 text-sm text-gray-600 hover:text-blue-600 mb-4">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Back to Plans
    </a>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:p-8 max-w-xl">
        <h1 class="text-xl font-bold text-gray-900">{{ $plan->name }}</h1>
        <p class="text-2xl font-extrabold text-blue-600 mt-1 mb-6">
            {{ number_format($plan->fee, 2) }} MZN<span class="text-sm font-medium text-gray-500">/month</span>
        </p>

        <p class="text-sm text-gray-600 mb-6">
            Pay via WhatsApp-coordinated mobile money or bank transfer, then upload your proof of payment below.
            We'll review it and activate your plan.
        </p>

        <form method="POST" action="{{ route('subscription.store', $plan) }}" enctype="multipart/form-data">
            @csrf

            <div class="space-y-4">
                <div>
                    <label for="proof" class="block text-sm font-medium text-gray-700 mb-1">Proof of Payment</label>
                    <input id="proof" name="proof" type="file" required class="{{ $inputClass }}">
                    <p class="mt-1 text-sm text-gray-500">A screenshot or receipt (JPG, PNG, or PDF, up to 5MB).</p>
                    @error('proof')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="payment_channel" class="block text-sm font-medium text-gray-700 mb-1">Payment Channel (optional)</label>
                    <input id="payment_channel" name="payment_channel" type="text" placeholder="e.g. M-Pesa, e-Mola, bank transfer" class="{{ $inputClass }}" value="{{ old('payment_channel') }}">
                </div>
            </div>

            <button type="submit" class="mt-6 w-full bg-blue-600 text-white font-bold py-2 rounded-full hover:bg-blue-700 transition">
                Submit for Review
            </button>
        </form>
    </div>
</x-layouts.student>
