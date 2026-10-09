<x-layouts.student title="Study Guide">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Study Guide</h1>

    @foreach (\App\Enums\CourseLevel::cases() as $level)
        @php $levelDocs = $docsByLevel->get($level->value, collect()); @endphp
        @if ($levelDocs->isNotEmpty())
            <section class="mb-10">
                <h2 class="text-lg font-semibold text-gray-700 mb-4">{{ $level->label() }}</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($levelDocs as $doc)
                        <x-content-card
                            :title="$doc->title"
                            :description="$doc->description"
                            :accessible="$doc->isAccessibleBy(auth()->user())"
                            :completed="(bool) $progress->get($doc->id)?->completed_at"
                        >
                            <div class="mt-auto flex items-center justify-between gap-3">
                                <a href="{{ route('documents.show', $doc) }}" class="inline-flex items-center gap-1 text-sm font-medium text-blue-600 hover:text-blue-700">
                                    {{ $doc->isPreviewable() ? 'Read' : 'Open' }}
                                    <x-lucide-chevron-right class="w-4 h-4" />
                                </a>
                                <a href="{{ route('documents.download', $doc) }}" class="inline-flex items-center gap-1 text-sm font-medium text-green-600 hover:text-green-700">
                                    <x-lucide-download class="w-4 h-4" />
                                    Download
                                </a>
                            </div>
                        </x-content-card>
                    @endforeach
                </div>
            </section>
        @endif
    @endforeach

    @if ($docsByLevel->isEmpty())
        <p class="text-gray-500">No structured study materials available yet - check back soon.</p>
    @endif
</x-layouts.student>
