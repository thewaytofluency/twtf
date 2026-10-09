@php
    $embed = $video->embedUrl
        ? $video->embedUrl.'?enablejsapi=1&rel=0&origin='.urlencode(request()->getSchemeAndHttpHost())
        : null;
    $user = auth()->user();
    $nextOpen = $next?->isAccessibleBy($user);
    $level = $video->course_level->label();

    $playerConfig = [
        'type' => 'video',
        'id' => $video->id,
        'url' => route('progress.update'),
        'csrf' => csrf_token(),
        'completed' => $completed,
        'doneCount' => $completedInPlaylist,
        'hasEmbed' => (bool) $embed,
        'nextUrl' => $nextOpen ? route('videos.show', $next) : null,
        'nextTitle' => $next?->title,
    ];
@endphp

<x-layouts.student :title="$video->title">
    <div x-data="lessonPlayer({{ Illuminate\Support\Js::from($playerConfig) }})" class="max-w-6xl">
        <nav class="flex items-center gap-1 text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
            <a href="{{ route('videos.index') }}" class="hover:text-blue-600">Videos</a>
            <x-lucide-chevron-right class="w-4 h-4" />
            <span>{{ $level }}</span>
        </nav>

        @if (session('status'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">{{ session('status') }}</div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_21rem] gap-6 items-start">
            <div class="space-y-4 min-w-0">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    @if ($embed)
                        <div class="relative aspect-video bg-black">
                            <iframe
                                x-ref="frame"
                                src="{{ $embed }}"
                                class="w-full h-full"
                                title="{{ $video->title }}"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen
                            ></iframe>

                            {{-- Shown when the video ends: counts down to the next lesson. --}}
                            <div x-show="upNext" x-cloak class="absolute inset-0 bg-gray-900/90 flex flex-col items-center justify-center text-center text-white p-6">
                                <template x-if="nextUrl">
                                    <div>
                                        <p class="text-sm text-gray-300">Up next in <span x-text="secondsLeft"></span>s</p>
                                        <p class="mt-1 text-lg font-semibold" x-text="nextTitle"></p>
                                        <div class="mt-5 flex items-center justify-center gap-3">
                                            <a :href="nextUrl" class="bg-blue-600 hover:bg-blue-700 px-5 py-2 rounded-full text-sm font-semibold">Play now</a>
                                            <button type="button" @click="cancelUpNext()" class="px-5 py-2 rounded-full text-sm border border-gray-500 hover:bg-white/10">Stay here</button>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="!nextUrl">
                                    <div>
                                        <p class="text-lg font-semibold">Lesson complete 🎉</p>
                                        <p class="mt-1 text-sm text-gray-300">
                                            @if ($next)
                                                The next lesson is part of a higher plan.
                                            @else
                                                You've reached the end of the video path.
                                            @endif
                                        </p>
                                        <div class="mt-5 flex items-center justify-center gap-3">
                                            @if ($next)
                                                <a href="{{ route('subscription.plans') }}" class="bg-blue-600 hover:bg-blue-700 px-5 py-2 rounded-full text-sm font-semibold">See plans</a>
                                            @endif
                                            <button type="button" @click="cancelUpNext()" class="px-5 py-2 rounded-full text-sm border border-gray-500 hover:bg-white/10">Close</button>
                                        </div>
                                    </div>
                                </template>
                            </div>
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
                            <span class="inline-block px-2 py-0.5 rounded-full bg-blue-50 text-blue-600 text-xs font-medium">{{ $level }}</span>
                            <span class="text-xs text-gray-400">Lesson {{ $playlist->search(fn ($v) => $v->is($video)) + 1 }} of {{ $playlist->count() }}</span>
                        </div>
                        <h1 class="text-xl font-bold text-gray-900 mb-2">{{ $video->title }}</h1>
                        @if ($video->description)
                            <p class="text-gray-600 mb-4">{{ $video->description }}</p>
                        @endif

                        <div class="pt-4 border-t border-gray-100 flex items-center justify-between flex-wrap gap-4">
                            <div class="flex items-center gap-3 flex-wrap">
                                <button type="button" @click="toggle()" :disabled="busy"
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold border transition"
                                    :class="completed ? 'bg-green-50 border-green-200 text-green-700 hover:bg-green-100' : 'bg-white border-gray-300 text-gray-700 hover:border-green-400 hover:text-green-700'">
                                    <x-lucide-circle-check class="w-4 h-4" />
                                    <span x-text="completed ? 'Completed' : 'Mark as complete'"></span>
                                </button>

                                @if ($nextOpen)
                                    <form method="POST" action="{{ route('progress.update') }}" x-show="!completed">
                                        @csrf
                                        <input type="hidden" name="type" value="video">
                                        <input type="hidden" name="id" value="{{ $video->id }}">
                                        <input type="hidden" name="completed" value="1">
                                        <input type="hidden" name="continue" value="1">
                                        <button type="submit" class="inline-flex items-center gap-1 px-4 py-2 rounded-full text-sm font-semibold bg-blue-600 text-white hover:bg-blue-700 transition">
                                            Complete &amp; continue <x-lucide-chevron-right class="w-4 h-4" />
                                        </button>
                                    </form>
                                @endif
                            </div>

                            <div class="flex items-center gap-4">
                                <label class="inline-flex items-center gap-2 text-sm text-gray-500 cursor-pointer select-none" title="Automatically play the next lesson when this one ends">
                                    <input type="checkbox" x-model="autoplay" @change="saveAutoplay()" class="rounded border-gray-300 text-blue-600 focus:ring-blue-600">
                                    Autoplay next
                                </label>
                                <x-like-button :model="$video" />
                                <x-share-buttons :title="$video->title" />
                            </div>
                        </div>
                    </div>
                </div>

                <x-lesson-nav :previous="$previous" :next="$next" :current="$video" route="videos.show" />
            </div>

            <x-lesson-playlist
                :items="$playlist" :current="$video" :progress="$progress"
                route="videos.show" :heading="$level.' course'" noun="videos"
            />
        </div>
    </div>
</x-layouts.student>
