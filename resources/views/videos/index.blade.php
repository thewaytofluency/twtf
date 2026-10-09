@php
    $user = auth()->user();
    $accessibleVideos = $videosByLevel->flatten();
    $totalDone = $accessibleVideos->filter(fn ($v) => $progress->get($v->id)?->completed_at)->count();
@endphp

<x-layouts.student title="Videos">
    <div class="flex items-end justify-between flex-wrap gap-2 mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Videos</h1>
        @if ($accessibleVideos->isNotEmpty())
            <p class="text-sm text-gray-500">{{ $totalDone }} of {{ $accessibleVideos->count() }} completed</p>
        @endif
    </div>

    @if ($resume)
        @php $started = $resumeStarted; @endphp
        <a href="{{ route('videos.show', $resume) }}" class="group mb-8 flex items-center gap-4 p-4 bg-gradient-to-r from-blue-600 to-blue-500 text-white rounded-xl shadow-sm hover:shadow-md transition">
            <span class="w-11 h-11 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0"><x-lucide-play class="w-5 h-5" /></span>
            <span class="min-w-0 flex-1">
                <span class="block text-xs uppercase tracking-wide text-blue-100">{{ $started ? 'Continue watching' : 'Start learning' }}</span>
                <span class="block font-semibold truncate">{{ $resume->title }}</span>
            </span>
            <x-lucide-chevron-right class="w-5 h-5 flex-shrink-0 transition-transform group-hover:translate-x-1" />
        </a>
    @endif

    @foreach (\App\Enums\CourseLevel::cases() as $level)
        @php
            $levelVideos = $videosByLevel->get($level->value, collect());
            $levelDone = $levelVideos->filter(fn ($v) => $progress->get($v->id)?->completed_at)->count();
        @endphp
        @if ($levelVideos->isNotEmpty())
            <section class="mb-10">
                <div class="flex items-center justify-between gap-4 mb-4">
                    <h2 class="text-lg font-semibold text-gray-700">{{ $level->label() }}</h2>
                    <div class="flex items-center gap-3 text-xs text-gray-500">
                        <span>{{ $levelDone }}/{{ $levelVideos->count() }}</span>
                        <div class="w-28 h-1.5 rounded-full bg-gray-200 overflow-hidden">
                            <div class="h-full bg-green-500" style="width: {{ round($levelDone / $levelVideos->count() * 100) }}%"></div>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($levelVideos as $video)
                        @php $done = (bool) $progress->get($video->id)?->completed_at; @endphp
                        <x-content-card
                            :title="$video->title"
                            :description="$video->description"
                            :accessible="$video->isAccessibleBy($user)"
                            :completed="$done"
                        >
                            <a href="{{ route('videos.show', $video) }}" class="mt-auto inline-flex items-center gap-1 text-sm font-medium text-blue-600 hover:text-blue-700">
                                {{ $done ? 'Watch again' : ($progress->get($video->id)?->viewed_at ? 'Continue' : 'Watch Video') }}
                                <x-lucide-chevron-right class="w-4 h-4" />
                            </a>
                        </x-content-card>
                    @endforeach
                </div>
            </section>
        @endif
    @endforeach

    @if ($videosByLevel->isEmpty())
        <p class="text-gray-500">No videos available yet - check back soon.</p>
    @endif
</x-layouts.student>
