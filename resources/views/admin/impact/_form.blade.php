@php
    $inputClass = 'w-full px-4 py-2 border border-gray-300 rounded-md text-gray-900 focus:outline-none focus:ring-blue-600 focus:border-blue-600';
@endphp

<div class="space-y-4 max-w-xl">
    <div>
        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
        <input id="title" name="title" type="text" required maxlength="120" class="{{ $inputClass }}" value="{{ old('title', $item->title) }}">
    </div>

    <div>
        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
        <textarea id="description" name="description" rows="3" required maxlength="400" class="{{ $inputClass }}">{{ old('description', $item->description) }}</textarea>
    </div>

    {{-- Badge: choose between an emoji and an uploaded image. --}}
    <div x-data="{ badge: @js(old('badge_type', $item->badge_type ?: 'emoji')) }" class="space-y-3">
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
            <input id="emoji" name="emoji" type="text" maxlength="16" placeholder="🌍" class="{{ $inputClass }} max-w-[10rem]" value="{{ old('emoji', $item->emoji) }}">
        </div>

        <div x-show="badge === 'image'" x-cloak>
            @include('admin.landing._image-field', ['currentUrl' => $item->image_url])
        </div>
    </div>

    <div>
        <label for="stat" class="block text-sm font-medium text-gray-700 mb-1">Headline number <span class="text-gray-400 font-normal">(optional)</span></label>
        <input id="stat" name="stat" type="text" maxlength="40" placeholder="e.g. 2,500+" class="{{ $inputClass }} max-w-[14rem]" value="{{ old('stat', $item->stat) }}">
        <p class="mt-1 text-sm text-gray-500">Shown large above the title.</p>
    </div>

    <div class="grid grid-cols-2 gap-4 items-end">
        <div>
            <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">Display order</label>
            <input id="sort_order" name="sort_order" type="number" min="0" class="{{ $inputClass }}" value="{{ old('sort_order', $item->sort_order) }}">
            <p class="mt-1 text-sm text-gray-500">Lower numbers come first.</p>
        </div>
        <label class="flex items-center gap-2 text-gray-700 pb-6">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-600" @checked(old('is_active', $item->is_active))>
            Show on landing page
        </label>
    </div>
</div>

<div class="mt-6 flex items-center gap-3">
    <button type="submit" class="bg-blue-600 text-white font-bold px-6 py-2 rounded-full hover:bg-blue-700 transition">
        {{ $item->exists ? 'Save Changes' : 'Create Impact Item' }}
    </button>
    <a href="{{ route('admin.impact.index') }}" class="text-gray-600 hover:text-gray-900">Cancel</a>
</div>
