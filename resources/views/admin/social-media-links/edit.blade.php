<x-layouts.admin title="Edit {{ $socialMediaLink->platform->label() }} Link">
    @php
        $inputClass = 'w-full px-4 py-2 border border-gray-300 rounded-md text-gray-900 focus:outline-none focus:ring-blue-600 focus:border-blue-600';
    @endphp

    <form method="POST" action="{{ route('admin.social-media-links.update', $socialMediaLink) }}" class="max-w-xl">
        @csrf
        @method('PUT')

        <div class="space-y-4">
            <div>
                <label for="url" class="block text-sm font-medium text-gray-700 mb-1">{{ $socialMediaLink->platform->label() }} URL</label>
                <input id="url" name="url" type="url" required class="{{ $inputClass }}" value="{{ old('url', $socialMediaLink->url) }}">
            </div>

            <label class="inline-flex items-center">
                <input type="hidden" name="is_visible" value="0">
                <input type="checkbox" name="is_visible" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-600" @checked(old('is_visible', $socialMediaLink->is_visible))>
                <span class="ml-2 text-sm text-gray-700">Show on the site</span>
            </label>
        </div>

        <div class="mt-6 flex items-center gap-3">
            <button type="submit" class="bg-blue-600 text-white font-bold px-6 py-2 rounded-full hover:bg-blue-700 transition">
                Save Changes
            </button>
            <a href="{{ route('admin.social-media-links.index') }}" class="text-gray-600 hover:text-gray-900">Cancel</a>
        </div>
    </form>
</x-layouts.admin>
