<x-layouts.admin title="Edit Course">
    <form method="POST" action="{{ route('admin.courses.update', $course) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.courses._form')
    </form>
</x-layouts.admin>
