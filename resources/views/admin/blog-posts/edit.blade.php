<x-layouts.admin title="Edit Blog Post">
    <form method="POST" action="{{ route('admin.blog-posts.update', $blogPost) }}">
        @csrf
        @method('PUT')
        @include('admin.blog-posts._form')
    </form>
</x-layouts.admin>
