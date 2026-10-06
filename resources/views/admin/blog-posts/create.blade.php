<x-layouts.admin title="New Blog Post">
    <form id="post-form" method="POST" action="{{ route('admin.blog-posts.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.blog-posts._form')
    </form>
</x-layouts.admin>
