@props(['previous' => null, 'next' => null, 'current', 'route'])

{{--
    Previous / next lesson cards under a video or document, so the student can keep going without
    returning to the list. A locked next lesson still shows (with an upgrade link) rather than
    silently disappearing, and crossing into the next level is called out.
--}}
@php
    $user = auth()->user();
    $levelOf = fn ($lesson) => $lesson->course_level?->label() ?? 'General';
@endphp

<nav class="grid grid-cols-1 sm:grid-cols-2 gap-3" aria-label="Lesson navigation">
    @if ($previous)
        <a href="{{ $previous->isAccessibleBy($user) ? route($route, $previous) : route('subscription.plans') }}"
           class="group flex items-center gap-3 p-4 bg-white rounded-xl border border-gray-200 hover:border-blue-300 hover:shadow-sm transition">
            <x-lucide-chevron-left class="w-5 h-5 text-gray-400 group-hover:text-blue-600 flex-shrink-0" />
            <span class="min-w-0">
                <span class="block text-xs uppercase tracking-wide text-gray-400">Previous</span>
                <span class="block text-sm font-medium text-gray-800 truncate">{{ $previous->title }}</span>
            </span>
        </a>
    @else
        <span class="hidden sm:block"></span>
    @endif

    @if ($next)
        @php $nextOpen = $next->isAccessibleBy($user); @endphp
        <a href="{{ $nextOpen ? route($route, $next) : route('subscription.plans') }}"
           data-next-lesson
           class="group flex items-center justify-between gap-3 p-4 rounded-xl border transition text-right
                  {{ $nextOpen ? 'bg-blue-600 border-blue-600 hover:bg-blue-700 text-white' : 'bg-white border-gray-200 hover:border-blue-300' }}">
            <span class="min-w-0 flex-1">
                <span class="block text-xs uppercase tracking-wide {{ $nextOpen ? 'text-blue-100' : 'text-gray-400' }}">
                    @if ($levelOf($next) !== $levelOf($current))
                        Next level &middot; {{ $levelOf($next) }}
                    @else
                        Up next
                    @endif
                </span>
                <span class="block text-sm font-medium truncate {{ $nextOpen ? 'text-white' : 'text-gray-800' }}">{{ $next->title }}</span>
                @unless ($nextOpen)
                    <span class="block text-xs text-blue-600 mt-0.5">Upgrade to unlock</span>
                @endunless
            </span>
            @if ($nextOpen)
                <x-lucide-chevron-right class="w-5 h-5 flex-shrink-0" />
            @else
                <x-lucide-lock class="w-5 h-5 text-gray-400 flex-shrink-0" />
            @endif
        </a>
    @else
        <div class="flex items-center justify-center gap-2 p-4 rounded-xl border border-dashed border-gray-300 text-sm text-gray-500">
            <x-lucide-party-popper class="w-4 h-4" /> You've reached the end of the path
        </div>
    @endif
</nav>
