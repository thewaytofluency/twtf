<x-layouts.student :title="$video->title">
    <a href="{{ route('videos.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-600 hover:text-blue-600 mb-4">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Back to Videos
    </a>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden max-w-3xl">
        @if ($video->embedUrl)
            <div class="aspect-video">
                <iframe
                    src="{{ $video->embedUrl }}"
                    class="w-full h-full"
                    title="{{ $video->title }}"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                ></iframe>
            </div>
        @else
            <div class="bg-gray-100 p-8 text-center text-gray-500">
                Video unavailable.
                <a href="{{ $video->youtube_url }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 underline">
                    Watch on YouTube
                </a>
            </div>
        @endif

        <div class="p-6">
            <div class="flex items-center gap-2 mb-2">
                <span class="inline-block px-2 py-0.5 rounded-full bg-blue-50 text-blue-600 text-xs font-medium">
                    {{ $video->course_level->label() }}
                </span>
            </div>
            <h1 class="text-xl font-bold text-gray-900 mb-2">{{ $video->title }}</h1>
            @if ($video->description)
                <p class="text-gray-600 mb-4">{{ $video->description }}</p>
            @endif

            <div class="pt-4 border-t border-gray-100 flex items-center justify-between flex-wrap gap-4">
                <x-like-button :model="$video" />
                <x-share-buttons :title="$video->title" />
            </div>
        </div>
    </div>
</x-layouts.student>
