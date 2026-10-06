<x-layouts.student title="Blog">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Blog</h1>

    @if ($blogPosts->isEmpty())
        <p class="text-gray-500">No posts yet — check back soon.</p>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($blogPosts as $blogPost)
                <a href="{{ route('blog.show', $blogPost) }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow flex flex-col">
                    <h2 class="text-lg font-semibold text-gray-800 mb-2">{{ $blogPost->title }}</h2>
                    <p class="text-gray-600 text-sm mb-4 flex-1">
                        {{ $blogPost->excerpt }}
                    </p>
                    <div class="text-xs text-gray-500 flex items-center justify-between">
                        <span>{{ $blogPost->author->name }}</span>
                        <span>{{ $blogPost->readTimeMinutes }} min read</span>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $blogPosts->links() }}
        </div>
    @endif
</x-layouts.student>
