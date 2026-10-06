@props([
    'title',
    'description' => null,
    'courseLevel' => null,
    'accessible' => true,
])

{{--
    Shared "locked/unlocked" catalog card — used by the videos, documents, and study guide
    index pages (the freemium-upsell pattern: show everything, dim what the student's current
    plan doesn't cover, with an Upgrade link instead of a real link to the gated show/download
    route). The actual access check that matters happens server-side in the controller
    (VideoController@show, DocController@download) — this only controls what's rendered.
--}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col {{ $accessible ? 'hover:shadow-md transition-shadow' : 'opacity-60' }}">
    <div class="flex items-start justify-between gap-2 mb-2">
        <h3 class="text-lg font-semibold text-gray-800">{{ $title }}</h3>
        @if (! $accessible)
            <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
        @endif
    </div>

    @if ($courseLevel)
        <span class="inline-block self-start px-2 py-0.5 mb-2 rounded-full bg-blue-50 text-blue-600 text-xs font-medium">
            {{ $courseLevel->label() }}
        </span>
    @endif

    @if ($description)
        <p class="text-gray-600 text-sm mb-4 flex-1">{{ $description }}</p>
    @endif

    @if ($accessible)
        {{ $slot }}
    @else
        <a href="{{ route('subscription.plans') }}" class="mt-auto inline-flex items-center justify-center gap-1 text-sm font-medium text-blue-600 hover:text-blue-700">
            Upgrade to unlock
        </a>
    @endif
</div>
