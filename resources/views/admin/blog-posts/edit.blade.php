<x-layouts.admin title="Edit Blog Post">
    <form id="post-form" method="POST" action="{{ route('admin.blog-posts.update', $blogPost) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.blog-posts._form')
    </form>

    {{-- Outside the main form (nested forms aren't valid HTML); the button in the sidebar points here via form="". --}}
    <form id="delete-post-form" method="POST" action="{{ route('admin.blog-posts.destroy', $blogPost) }}" onsubmit="return confirm('Delete this post permanently?')">
        @csrf
        @method('DELETE')
    </form>
</x-layouts.admin>
