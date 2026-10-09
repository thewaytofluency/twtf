@php
    $totalDone = $docs->filter(fn ($d) => $progress->get($d->id)?->completed_at)->count();
@endphp

<x-layouts.student title="Documents">
    <div class="flex items-end justify-between flex-wrap gap-2 mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Documents</h1>
        @if ($docs->isNotEmpty())
            <p class="text-sm text-gray-500">{{ $totalDone }} of {{ $docs->count() }} studied</p>
        @endif
    </div>

    @if ($docs->isEmpty())
        <p class="text-gray-500">No documents available yet - check back soon.</p>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($docs as $doc)
                @php
                    $accessible = $doc->isAccessibleBy(auth()->user());
                    $done = (bool) $progress->get($doc->id)?->completed_at;
                @endphp
                <x-content-card
                    :title="$doc->title"
                    :description="$doc->description"
                    :course-level="$doc->course_level"
                    :accessible="$accessible"
                    :completed="$done"
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
    @endif
</x-layouts.student>
