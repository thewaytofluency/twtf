<x-layouts.student title="Videos">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Videos</h1>

    @foreach (\App\Enums\CourseLevel::cases() as $level)
        @php $levelVideos = $videosByLevel->get($level->value, collect()); @endphp
        @if ($levelVideos->isNotEmpty())
            <section class="mb-10">
                <h2 class="text-lg font-semibold text-gray-700 mb-4">{{ $level->label() }}</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($levelVideos as $video)
                        <x-content-card
                            :title="$video->title"
                            :description="$video->description"
                            :accessible="$video->isAccessibleBy(auth()->user())"
                        >
                            <a href="{{ route('videos.show', $video) }}" class="mt-auto inline-flex items-center gap-1 text-sm font-medium text-blue-600 hover:text-blue-700">
                                Watch Video
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </x-content-card>
                    @endforeach
                </div>
            </section>
        @endif
    @endforeach

    @if ($videosByLevel->isEmpty())
        <p class="text-gray-500">No videos available yet — check back soon.</p>
    @endif
</x-layouts.student>
