@php
    $user = auth()->user();
    $nextOpen = $next?->isAccessibleBy($user);
    $level = $doc->course_level?->label() ?? 'General';
    $ext = $doc->extension();

    $config = [
        'type' => 'doc',
        'id' => $doc->id,
        'url' => route('progress.update'),
        'csrf' => csrf_token(),
        'completed' => $completed,
        'doneCount' => $completedInPlaylist,
    ];
@endphp

<x-layouts.student :title="$doc->title">
    <div x-data="lessonProgress({{ Illuminate\Support\Js::from($config) }})" class="max-w-6xl">
        <nav class="flex items-center gap-1 text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
            <a href="{{ route('documents.index') }}" class="hover:text-blue-600">Documents</a>
            <x-lucide-chevron-right class="w-4 h-4" />
            <span>{{ $level }}</span>
        </nav>

        @if (session('status'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">{{ session('status') }}</div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_21rem] gap-6 items-start">
            <div class="space-y-4 min-w-0">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="inline-block px-2 py-0.5 rounded-full bg-blue-50 text-blue-600 text-xs font-medium">{{ $level }}</span>
                            <span class="text-xs text-gray-400 uppercase">{{ $ext }} &middot; {{ $doc->humanSize() }}</span>
                        </div>
                        <h1 class="text-xl font-bold text-gray-900">{{ $doc->title }}</h1>
                        @if ($doc->description)
                            <p class="text-gray-600 mt-2">{{ $doc->description }}</p>
                        @endif

                        <div class="mt-4 flex items-center gap-3 flex-wrap">
                            <a href="{{ route('documents.download', $doc) }}" @click="if (!completed) { completed = true; doneCount++ }"
                               class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold bg-green-600 text-white hover:bg-green-700 transition">
                                <x-lucide-download class="w-4 h-4" /> Download
                            </a>

                            <button type="button" @click="toggle()" :disabled="busy"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold border transition"
                                :class="completed ? 'bg-green-50 border-green-200 text-green-700 hover:bg-green-100' : 'bg-white border-gray-300 text-gray-700 hover:border-green-400 hover:text-green-700'">
                                <x-lucide-circle-check class="w-4 h-4" />
                                <span x-text="completed ? 'Completed' : 'Mark as complete'"></span>
                            </button>

                            @if ($nextOpen)
                                <form method="POST" action="{{ route('progress.update') }}" x-show="!completed">
                                    @csrf
                                    <input type="hidden" name="type" value="doc">
                                    <input type="hidden" name="id" value="{{ $doc->id }}">
                                    <input type="hidden" name="completed" value="1">
                                    <input type="hidden" name="continue" value="1">
                                    <button type="submit" class="inline-flex items-center gap-1 px-4 py-2 rounded-full text-sm font-semibold bg-blue-600 text-white hover:bg-blue-700 transition">
                                        Complete &amp; continue <x-lucide-chevron-right class="w-4 h-4" />
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    {{-- In-page reader: PDFs and images open right here, no download needed. --}}
                    @if ($doc->isPreviewable())
                        @if ($ext === 'pdf')
                            <iframe src="{{ route('documents.preview', $doc) }}#view=FitH" title="{{ $doc->title }}" class="w-full h-[75vh] bg-gray-100"></iframe>
                        @else
                            <img src="{{ route('documents.preview', $doc) }}" alt="{{ $doc->title }}" class="w-full">
                        @endif
                    @else
                        <div class="p-10 text-center text-gray-500 bg-gray-50">
                            <x-lucide-file-text class="w-10 h-10 mx-auto text-gray-300 mb-3" />
                            <p>This file type can't be previewed in the browser.</p>
                            <p class="text-sm">Download it to open it on your device.</p>
                        </div>
                    @endif
                </div>

                <x-lesson-nav :previous="$previous" :next="$next" :current="$doc" route="documents.show" />
            </div>

            <x-lesson-playlist
                :items="$playlist" :current="$doc" :progress="$progress"
                route="documents.show" :heading="$level.' documents'" noun="documents"
            />
        </div>
    </div>
</x-layouts.student>
