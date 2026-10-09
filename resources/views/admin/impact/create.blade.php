<x-layouts.admin title="New Impact Item">
    <form method="POST" action="{{ route('admin.impact.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.impact._form')
    </form>
</x-layouts.admin>
