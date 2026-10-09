@props(['items', 'current', 'progress', 'route', 'heading', 'noun' => 'lessons'])

{{--
    The "course content" sidebar next to a video/document: every lesson in the same level, with
    the current one highlighted, finished ones ticked and locked ones marked. Must sit inside a
    `lessonProgress` / `lessonPlayer` Alpine scope (it reads `completed` and `doneCount` so the
    current lesson's tick and the counter update the moment it's marked complete).
--}}
@php $user = auth()->user(); @endphp

<aside class="bg-white rounded-xl border border-gray-200 overflow-hidden lg:sticky lg:top-4">
    <div class="px-4 py-3 border-b border-gray-100">
        <h2 class="text-sm font-semibold text-gray-800">{{ $heading }}</h2>
        <p class="text-xs text-gray-500 mt-0.5">
            <span x-text="doneCount"></span> of {{ $items->count() }} {{ $noun }} complete
        </p>
        <div class="mt-2 h-1.5 rounded-full bg-gray-100 overflow-hidden">
            <div class="h-full bg-green-500 transition-all duration-500" :style="'width:' + Math.round(doneCount / {{ max($items->count(), 1) }} * 100) + '%'"></div>
        </div>
    </div>

    <ol class="max-h-[28rem] overflow-y-auto divide-y divide-gray-50" x-init="$nextTick(() => $el.querySelector('[aria-current]')?.scrollIntoView({ block: 'nearest' }))">
        @foreach ($items as $item)
            @php
                $isCurrent = $item->is($current);
                $open = $item->isAccessibleBy($user);
                $done = (bool) $progress->get($item->id)?->completed_at;
            @endphp
            <li @if ($isCurrent) aria-current="true" @endif>
                <a href="{{ $open ? route($route, $item) : route('subscription.plans') }}"
                   class="flex items-start gap-3 px-4 py-3 text-sm transition {{ $isCurrent ? 'bg-blue-50' : 'hover:bg-gray-50' }} {{ $open ? '' : 'opacity-60' }}">
                    <span class="mt-0.5 flex-shrink-0 w-5 h-5">
                        @if ($isCurrent)
                            <span x-show="completed" class="text-green-600"><x-lucide-circle-check class="w-5 h-5" /></span>
                            <span x-show="!completed" class="text-blue-600"><x-lucide-circle-play class="w-5 h-5" /></span>
                        @elseif ($done)
                            <x-lucide-circle-check class="w-5 h-5 text-green-600" />
                        @elseif (! $open)
                            <x-lucide-lock class="w-4 h-4 text-gray-400 mt-0.5 ml-0.5" />
                        @else
                            <x-lucide-circle class="w-5 h-5 text-gray-300" />
                        @endif
                    </span>
                    <span class="min-w-0">
                        <span class="block {{ $isCurrent ? 'font-semibold text-blue-700' : 'text-gray-700' }}">{{ $item->title }}</span>
                        @unless ($open)
                            <span class="block text-xs text-gray-400">Requires an upgrade</span>
                        @endunless
                    </span>
                </a>
            </li>
        @endforeach
    </ol>
</aside>
