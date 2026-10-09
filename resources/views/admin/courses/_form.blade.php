@php
    $inputClass = 'w-full px-4 py-2 border border-gray-300 rounded-md text-gray-900 focus:outline-none focus:ring-blue-600 focus:border-blue-600';
    $swatches = ['green' => 'bg-green-500', 'blue' => 'bg-blue-500', 'purple' => 'bg-purple-500', 'amber' => 'bg-amber-500', 'rose' => 'bg-rose-500', 'teal' => 'bg-teal-500'];
@endphp

<div class="space-y-4 max-w-xl">
    <div>
        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
        <input id="title" name="title" type="text" required maxlength="120" class="{{ $inputClass }}" value="{{ old('title', $course->title) }}">
    </div>

    <div>
        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
        <textarea id="description" name="description" rows="3" required maxlength="400" class="{{ $inputClass }}">{{ old('description', $course->description) }}</textarea>
    </div>

    {{-- Badge: choose between an emoji and an uploaded image. --}}
    <div x-data="{ badge: @js(old('badge_type', $course->badge_type ?: 'emoji')) }" class="space-y-3">
        <span class="block text-sm font-medium text-gray-700">Badge</span>
        <div class="inline-flex rounded-lg border border-gray-300 overflow-hidden text-sm" role="radiogroup" aria-label="Badge type">
            @foreach (['emoji' => 'Emoji', 'image' => 'Image'] as $value => $label)
                <label class="cursor-pointer">
                    <input type="radio" name="badge_type" value="{{ $value }}" x-model="badge" class="sr-only peer">
                    <span class="block px-4 py-1.5 text-gray-600 peer-checked:bg-blue-600 peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-blue-600">{{ $label }}</span>
                </label>
            @endforeach
        </div>

        <div x-show="badge === 'emoji'">
            <label for="emoji" class="block text-sm font-medium text-gray-700 mb-1">Emoji</label>
            <input id="emoji" name="emoji" type="text" maxlength="16" placeholder="🌱" class="{{ $inputClass }} max-w-[10rem]" value="{{ old('emoji', $course->emoji) }}">
        </div>

        <div x-show="badge === 'image'" x-cloak>
            @include('admin.landing._image-field', ['currentUrl' => $course->image_url])
        </div>
    </div>

    <div>
        <span class="block text-sm font-medium text-gray-700 mb-1">Accent colour</span>
        <div class="flex items-center gap-2 py-1">
            @foreach ($swatches as $key => $bg)
                <label class="cursor-pointer" title="{{ ucfirst($key) }}">
                    <input type="radio" name="accent" value="{{ $key }}" class="peer sr-only" @checked(old('accent', $course->accent) === $key)>
                    <span class="block w-7 h-7 rounded-full {{ $bg }} ring-2 ring-offset-2 ring-transparent peer-checked:ring-gray-800 peer-focus-visible:ring-blue-600"></span>
                </label>
            @endforeach
        </div>
        <p class="mt-1 text-sm text-gray-500">Colours the emoji badge.</p>
    </div>

    <div>
        <label for="cta_url" class="block text-sm font-medium text-gray-700 mb-1">"Learn More" link <span class="text-gray-400 font-normal">(optional)</span></label>
        <input id="cta_url" name="cta_url" type="text" placeholder="https://… or /register" class="{{ $inputClass }}" value="{{ old('cta_url', $course->cta_url) }}">
        <p class="mt-1 text-sm text-gray-500">Leave empty to send visitors to the sign-up page.</p>
    </div>

    <div class="grid grid-cols-2 gap-4 items-end">
        <div>
            <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">Display order</label>
            <input id="sort_order" name="sort_order" type="number" min="0" class="{{ $inputClass }}" value="{{ old('sort_order', $course->sort_order) }}">
            <p class="mt-1 text-sm text-gray-500">Lower numbers come first.</p>
        </div>
        <label class="flex items-center gap-2 text-gray-700 pb-6">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-600" @checked(old('is_active', $course->is_active))>
            Show on landing page
        </label>
    </div>
</div>

<div class="mt-6 flex items-center gap-3">
    <button type="submit" class="bg-blue-600 text-white font-bold px-6 py-2 rounded-full hover:bg-blue-700 transition">
        {{ $course->exists ? 'Save Changes' : 'Create Course' }}
    </button>
    <a href="{{ route('admin.courses.index') }}" class="text-gray-600 hover:text-gray-900">Cancel</a>
</div>
