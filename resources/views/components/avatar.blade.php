@props(['user', 'class' => 'w-8 h-8 text-sm'])

{{-- Round profile photo, or the user's initials on blue when they haven't uploaded one. --}}
@if ($user->photo_url)
    <img src="{{ $user->photo_url }}" alt="" class="{{ $class }} rounded-full object-cover flex-shrink-0">
@else
    <div class="{{ $class }} bg-blue-600 text-white rounded-full flex items-center justify-center font-bold flex-shrink-0">{{ $user->initials }}</div>
@endif
