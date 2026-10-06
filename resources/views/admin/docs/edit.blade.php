<x-layouts.admin title="Edit Document">
    <form method="POST" action="{{ route('admin.docs.update', $doc) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.docs._form')
    </form>
</x-layouts.admin>
