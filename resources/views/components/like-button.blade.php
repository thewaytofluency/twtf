@props(['model'])

@php
    $liked = $model->isLikedBy(auth()->user());
@endphp

<form method="POST" action="{{ route('likes.toggle') }}" class="inline-flex">
    @csrf
    <input type="hidden" name="type" value="{{ $model->getMorphClass() }}">
    <input type="hidden" name="id" value="{{ $model->id }}">
    <button
        type="submit"
        class="inline-flex items-center gap-1 text-sm transition {{ $liked ? 'text-red-600' : 'text-gray-500 hover:text-red-600' }}"
    >
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="{{ $liked ? 'currentColor' : 'none' }}" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
        </svg>
        {{ $model->like_count }}
    </button>
</form>
