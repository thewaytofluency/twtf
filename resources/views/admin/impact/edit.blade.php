<x-layouts.admin title="Edit Impact Item">
    <form method="POST" action="{{ route('admin.impact.update', $item) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.impact._form')
    </form>
</x-layouts.admin>
