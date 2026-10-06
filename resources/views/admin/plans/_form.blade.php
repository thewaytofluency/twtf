@php
    $inputClass = 'w-full px-4 py-2 border border-gray-300 rounded-md text-gray-900 focus:outline-none focus:ring-blue-600 focus:border-blue-600';
    $featuresText = old('features', implode("\n", $plan->features ?? []));
@endphp

<div class="space-y-4 max-w-xl">
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
        <input id="name" name="name" type="text" required class="{{ $inputClass }}" value="{{ old('name', $plan->name) }}">
    </div>

    <div>
        <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Code</label>
        <input id="code" name="code" type="text" required placeholder="e.g. standard" class="{{ $inputClass }}" value="{{ old('code', $plan->code) }}">
        <p class="mt-1 text-sm text-gray-500">Unique identifier — letters, numbers, dashes and underscores.</p>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="fee" class="block text-sm font-medium text-gray-700 mb-1">Monthly Fee (MZN)</label>
            <input id="fee" name="fee" type="number" step="0.01" min="0" required class="{{ $inputClass }}" value="{{ old('fee', $plan->fee) }}">
            <p class="mt-1 text-sm text-gray-500">0 for a free plan.</p>
        </div>
        <div>
            <label for="access_level" class="block text-sm font-medium text-gray-700 mb-1">Access Level</label>
            <input id="access_level" name="access_level" type="number" min="0" max="255" required class="{{ $inputClass }}" value="{{ old('access_level', $plan->access_level) }}">
            <p class="mt-1 text-sm text-gray-500">Content needing this level or lower unlocks.</p>
        </div>
    </div>

    <div>
        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Short Description</label>
        <input id="description" name="description" type="text" maxlength="500" class="{{ $inputClass }}" value="{{ old('description', $plan->description) }}">
    </div>

    <div>
        <label for="features" class="block text-sm font-medium text-gray-700 mb-1">Features</label>
        <textarea id="features" name="features" rows="5" class="{{ $inputClass }}">{{ $featuresText }}</textarea>
        <p class="mt-1 text-sm text-gray-500">One per line — shown as a checklist on the pricing cards.</p>
    </div>

    <div class="space-y-2">
        <label class="flex items-center gap-2 text-gray-700">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-600" @checked(old('is_active', $plan->is_active))>
            Active (shown on the site and available to subscribe to)
        </label>
        <label class="flex items-center gap-2 text-gray-700">
            <input type="hidden" name="is_popular" value="0">
            <input type="checkbox" name="is_popular" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-600" @checked(old('is_popular', $plan->is_popular))>
            Highlight as "Most Popular" (only one plan can have this)
        </label>
    </div>
</div>

<div class="mt-6 flex items-center gap-3">
    <button type="submit" class="bg-blue-600 text-white font-bold px-6 py-2 rounded-full hover:bg-blue-700 transition">
        {{ $plan->exists ? 'Save Changes' : 'Create Plan' }}
    </button>
    <a href="{{ route('admin.plans.index') }}" class="text-gray-600 hover:text-gray-900">Cancel</a>
</div>
