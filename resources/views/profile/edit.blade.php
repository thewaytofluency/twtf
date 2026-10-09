@php
    $planName = $subscription?->plan?->name ?? 'Free';
    $daysLeft = $subscription?->ends_at ? max(0, (int) now()->diffInDays($subscription->ends_at, false)) : null;

    $statCards = [
        ['icon' => 'clapperboard', 'color' => 'blue', 'label' => 'Videos completed', 'value' => $stats['videosDone'].'/'.$stats['videosTotal'], 'href' => route('videos.index')],
        ['icon' => 'file-text', 'color' => 'green', 'label' => 'Documents studied', 'value' => $stats['docsDone'].'/'.$stats['docsTotal'], 'href' => route('documents.index')],
        ['icon' => 'newspaper', 'color' => 'purple', 'label' => 'Posts read', 'value' => $stats['postsRead'], 'href' => route('blog.index')],
        ['icon' => 'message-square', 'color' => 'amber', 'label' => 'Comments', 'value' => $stats['comments'], 'href' => route('blog.index')],
        ['icon' => 'heart', 'color' => 'rose', 'label' => 'Likes given', 'value' => $stats['likes'], 'href' => null],
    ];
    $tints = [
        'blue' => 'bg-blue-100 text-blue-600', 'green' => 'bg-green-100 text-green-600', 'purple' => 'bg-purple-100 text-purple-600',
        'amber' => 'bg-amber-100 text-amber-600', 'rose' => 'bg-rose-100 text-rose-600',
    ];

    $activityLink = fn ($item) => match ($item->getMorphClass()) {
        'video' => route('videos.show', $item),
        'doc' => route('documents.show', $item),
        'blog_post' => route('blog.show', $item),
        default => '#',
    };
    $activityIcon = ['video' => 'clapperboard', 'doc' => 'file-text', 'blog_post' => 'newspaper'];
    $activityLabel = ['video' => 'Video', 'doc' => 'Document', 'blog_post' => 'Blog post'];
@endphp

<x-layouts.student title="My Profile">
    <div class="max-w-5xl space-y-6">
        {{-- Identity + plan --}}
        <section class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 flex flex-col sm:flex-row sm:items-center gap-5">
            <div class="flex items-center gap-4 flex-1 min-w-0">
                @if ($user->photo_url)
                    <img src="{{ $user->photo_url }}" alt="" class="w-20 h-20 rounded-full object-cover flex-shrink-0 ring-2 ring-blue-100">
                @else
                    <span class="w-20 h-20 rounded-full bg-blue-600 text-white flex items-center justify-center text-2xl font-bold flex-shrink-0">{{ $user->initials }}</span>
                @endif
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-gray-900 truncate">{{ $user->name }}</h1>
                    <p class="text-sm text-gray-500 truncate">{{ $user->email }}</p>
                    <p class="text-xs text-gray-400 mt-1">Member since {{ $user->created_at->format('F Y') }}</p>
                </div>
            </div>

            <div class="sm:text-right">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-semibold {{ $subscription ? 'bg-yellow-50 text-yellow-700' : 'bg-gray-100 text-gray-600' }}">
                    <x-lucide-zap class="w-4 h-4" /> {{ $planName }} plan
                </span>
                @if ($subscription)
                    <p class="text-xs text-gray-500 mt-1.5">
                        Active until {{ $subscription->ends_at->format('M j, Y') }}
                        @if ($daysLeft !== null)
                            <span class="{{ $daysLeft <= 7 ? 'text-amber-600 font-medium' : '' }}">({{ $daysLeft }} {{ $daysLeft === 1 ? 'day' : 'days' }} left)</span>
                        @endif
                    </p>
                @elseif ($pendingSubscription)
                    <p class="text-xs text-amber-600 mt-1.5">Your subscription request is awaiting review</p>
                @endif
                <a href="{{ route('subscription.index') }}" class="mt-2 inline-flex items-center gap-1 text-sm font-medium text-blue-600 hover:text-blue-700">
                    {{ $subscription || $pendingSubscription ? 'Manage subscription' : 'Upgrade your plan' }}
                    <x-lucide-chevron-right class="w-4 h-4" />
                </a>
            </div>
        </section>

        @if (session('status') === 'profile-updated' || session('status') === 'password-updated')
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3500)" x-transition class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
                {{ session('status') === 'password-updated' ? 'Password updated.' : 'Profile saved.' }}
            </div>
        @endif

        {{-- Stats --}}
        <section class="grid grid-cols-2 md:grid-cols-5 gap-4">
            @foreach ($statCards as $card)
                <a @if ($card['href']) href="{{ $card['href'] }}" @endif class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm {{ $card['href'] ? 'hover:shadow-md transition-shadow' : '' }}">
                    <span class="inline-flex p-2 rounded-lg {{ $tints[$card['color']] }}">
                        <x-dynamic-component :component="'lucide-'.$card['icon']" class="w-5 h-5" />
                    </span>
                    <p class="mt-3 text-2xl font-bold text-gray-900">{{ $card['value'] }}</p>
                    <p class="text-xs text-gray-500">{{ $card['label'] }}</p>
                </a>
            @endforeach
        </section>

        {{-- Account settings --}}
        <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                @include('profile.partials.update-profile-information-form')
            </div>

            <div class="space-y-6">
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    @include('profile.partials.update-password-form')
                </div>

                <div class="bg-white rounded-xl border border-red-100 shadow-sm p-6">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </section>
    </div>
</x-layouts.student>
