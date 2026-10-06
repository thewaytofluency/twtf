@php
    $inputClass = 'w-full px-4 py-2 border border-gray-300 rounded-md text-gray-900 focus:outline-none focus:ring-blue-600 focus:border-blue-600';
@endphp

<div class="space-y-4 max-w-2xl">
    <div>
        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
        <input id="title" name="title" type="text" required class="{{ $inputClass }}" value="{{ old('title', $blogPost->title) }}">
        @if ($blogPost->exists)
            <p class="mt-1 text-sm text-gray-500">URL: /blog/{{ $blogPost->slug }} (changing the title regenerates this)</p>
        @endif
    </div>

    <div>
        <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Content</label>
        <textarea id="content" name="content" rows="12" required class="{{ $inputClass }}">{{ old('content', $blogPost->content) }}</textarea>
        <p class="mt-1 text-sm text-gray-500">Plain text — line breaks are preserved when displayed.</p>
    </div>
</div>

<div class="mt-6 flex items-center gap-3">
    <button type="submit" class="bg-blue-600 text-white font-bold px-6 py-2 rounded-full hover:bg-blue-700 transition">
        {{ $blogPost->exists ? 'Save Changes' : 'Publish Post' }}
    </button>
    <a href="{{ route('admin.blog-posts.index') }}" class="text-gray-600 hover:text-gray-900">Cancel</a>
</div>
