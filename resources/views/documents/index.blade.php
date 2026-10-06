<x-layouts.student title="Documents">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Documents</h1>

    @if ($docs->isEmpty())
        <p class="text-gray-500">No documents available yet — check back soon.</p>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($docs as $doc)
                @php $accessible = $doc->isAccessibleBy(auth()->user()); @endphp
                <x-content-card
                    :title="$doc->title"
                    :description="$doc->description"
                    :course-level="$doc->course_level"
                    :accessible="$accessible"
                >
                    <a href="{{ route('documents.download', $doc) }}" class="mt-auto inline-flex items-center gap-1 text-sm font-medium text-green-600 hover:text-green-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Download
                    </a>
                </x-content-card>
            @endforeach
        </div>
    @endif
</x-layouts.student>
