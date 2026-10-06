<x-layouts.admin title="Record Subscription">
    @php
        $inputClass = 'w-full px-4 py-2 border border-gray-300 rounded-md text-gray-900 focus:outline-none focus:ring-blue-600 focus:border-blue-600';
    @endphp

    <p class="mb-6 text-gray-600 max-w-xl">
        Use this for payments coordinated outside the site (e.g. over WhatsApp). It
        records and immediately activates a subscription — no approval step needed
        since you're the one confirming payment was received.
    </p>

    <form method="POST" action="{{ route('admin.subscriptions.store') }}" class="max-w-xl">
        @csrf

        <div class="space-y-4">
            <div>
                <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Student</label>
                <select id="user_id" name="user_id" required class="{{ $inputClass }}">
                    <option value="">Select a student</option>
                    @foreach ($students as $student)
                        <option value="{{ $student->id }}" @selected(old('user_id') == $student->id)>
                            {{ $student->name }} ({{ $student->email }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="plan_id" class="block text-sm font-medium text-gray-700 mb-1">Plan</label>
                <select id="plan_id" name="plan_id" required class="{{ $inputClass }}">
                    <option value="">Select a plan</option>
                    @foreach ($plans as $plan)
                        <option value="{{ $plan->id }}" @selected(old('plan_id') == $plan->id)>
                            {{ $plan->name }} — {{ number_format($plan->fee, 2) }} MZN/month
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="payment_channel" class="block text-sm font-medium text-gray-700 mb-1">Payment Channel (optional)</label>
                <input id="payment_channel" name="payment_channel" type="text" placeholder="e.g. M-Pesa, e-Mola, bank transfer" class="{{ $inputClass }}" value="{{ old('payment_channel') }}">
            </div>

            <div>
                <label for="admin_notes" class="block text-sm font-medium text-gray-700 mb-1">Notes (optional)</label>
                <textarea id="admin_notes" name="admin_notes" rows="2" class="{{ $inputClass }}">{{ old('admin_notes') }}</textarea>
            </div>
        </div>

        <div class="mt-6 flex items-center gap-3">
            <button type="submit" class="bg-blue-600 text-white font-bold px-6 py-2 rounded-full hover:bg-blue-700 transition">
                Record &amp; Activate
            </button>
            <a href="{{ route('admin.subscriptions.index') }}" class="text-gray-600 hover:text-gray-900">Cancel</a>
        </div>
    </form>
</x-layouts.admin>
