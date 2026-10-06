<x-layouts.admin title="New Blog Post">
    <form method="POST" action="{{ route('admin.blog-posts.store') }}">
        @csrf
        @include('admin.blog-posts._form')
    </form>
</x-layouts.admin>
