<x-layouts.admin title="New Course">
    <form method="POST" action="{{ route('admin.courses.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.courses._form')
    </form>
</x-layouts.admin>
