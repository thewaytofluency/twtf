<x-layouts.admin title="New Document">
    <form method="POST" action="{{ route('admin.docs.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.docs._form')
    </form>
</x-layouts.admin>
