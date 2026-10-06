<x-layouts.admin title="Edit Video">
    <form method="POST" action="{{ route('admin.videos.update', $video) }}">
        @csrf
        @method('PUT')
        @include('admin.videos._form')
    </form>
</x-layouts.admin>
