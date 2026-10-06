<x-layouts.student :title="$blogPost->title">
    <a href="{{ route('blog.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-600 hover:text-blue-600 mb-4">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Back to Blog
    </a>

    <article class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:p-10 max-w-3xl">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $blogPost->title }}</h1>
        <div class="text-sm text-gray-500 mb-6">
            {{ $blogPost->author->name }} · {{ $blogPost->created_at->format('M j, Y') }} · {{ $blogPost->readTimeMinutes }} min read
        </div>

        <div class="prose prose-gray max-w-none text-gray-700 leading-relaxed">
            {!! nl2br(e($blogPost->content)) !!}
        </div>

        <div class="mt-8 pt-6 border-t border-gray-100 flex items-center justify-between flex-wrap gap-4">
            <x-like-button :model="$blogPost" />
            <x-share-buttons :title="$blogPost->title" />
        </div>
    </article>

    <div class="max-w-3xl mt-8">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">
            {{ $blogPost->comment_count }} {{ $blogPost->comment_count === 1 ? 'Comment' : 'Comments' }}
        </h2>

        @if (session('status'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('blog.comments.store', $blogPost) }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
            @csrf
            <textarea
                name="content"
                rows="3"
                required
                placeholder="Add a comment..."
                class="w-full px-4 py-2 border border-gray-300 rounded-md text-gray-900 focus:outline-none focus:ring-blue-600 focus:border-blue-600"
            >{{ old('content') }}</textarea>
            @error('content')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            <button type="submit" class="mt-3 bg-blue-600 text-white font-semibold px-5 py-2 rounded-full hover:bg-blue-700 transition text-sm">
                Post Comment
            </button>
        </form>

        <div class="space-y-4">
            @forelse ($blogPost->comments as $comment)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-xs flex-shrink-0">
                            {{ collect(explode(' ', $comment->user->name))->map(fn ($n) => $n[0] ?? '')->implode('') }}
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ $comment->user->name }}</div>
                            <div class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                    <p class="text-gray-700 text-sm mb-3">{!! nl2br(e($comment->content)) !!}</p>
                    <x-like-button :model="$comment" />
                </div>
            @empty
                <p class="text-gray-500 text-sm">Be the first to comment.</p>
            @endforelse
        </div>
    </div>
</x-layouts.student>
