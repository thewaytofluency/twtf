<x-layouts.admin title="New Video">
    <form method="POST" action="{{ route('admin.videos.store') }}">
        @csrf
        @include('admin.videos._form')
    </form>
</x-layouts.admin>
