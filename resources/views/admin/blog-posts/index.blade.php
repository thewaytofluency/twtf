<x-layouts.admin title="Blog Posts">
    @php
        $tabs = ['' => 'All', 'published' => 'Published', 'scheduled' => 'Scheduled', 'draft' => 'Drafts'];
        $countKeys = ['' => 'all', 'published' => 'published', 'scheduled' => 'scheduled', 'draft' => 'draft'];
    @endphp

    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <nav class="flex items-center gap-1 text-sm" aria-label="Filter posts">
            @foreach ($tabs as $key => $label)
                @continue($key !== '' && $counts[$countKeys[$key]] === 0 && $filter !== $key)
                <a
                    href="{{ route('admin.blog-posts.index', $key === '' ? [] : ['status' => $key]) }}"
                    class="px-3 py-1.5 rounded-full {{ ($filter ?? '') === $key ? 'bg-blue-600 text-white font-semibold' : 'text-gray-600 hover:bg-gray-200' }}"
                >
                    {{ $label }} <span class="{{ ($filter ?? '') === $key ? 'text-blue-100' : 'text-gray-400' }}">{{ $counts[$countKeys[$key]] }}</span>
                </a>
            @endforeach
        </nav>

        <a href="{{ route('admin.blog-posts.create') }}" class="inline-flex items-center gap-2 bg-blue-600 text-white font-bold px-6 py-2 rounded-full hover:bg-blue-700 transition">
            <x-lucide-plus class="w-4 h-4" />
            New Post
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Comments</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Likes</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($blogPosts as $blogPost)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if ($blogPost->cover_url)
                                    <img src="{{ $blogPost->cover_url }}" alt="" class="w-14 h-10 object-cover rounded flex-shrink-0">
                                @endif
                                <a href="{{ route('admin.blog-posts.edit', $blogPost) }}" class="text-gray-900 font-medium hover:text-blue-600">{{ $blogPost->title }}</a>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600">{{ $blogPost->author->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if ($blogPost->status === 'draft')
                                <span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 text-xs font-semibold">Draft</span>
                                <div class="text-xs text-gray-400 mt-1">Edited {{ $blogPost->updated_at->diffForHumans() }}</div>
                            @elseif ($blogPost->isScheduled())
                                <span class="px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 text-xs font-semibold">Scheduled</span>
                                <div class="text-xs text-gray-400 mt-1">{{ $blogPost->published_at->format('M j, Y H:i') }}</div>
                            @else
                                <span class="px-2 py-0.5 rounded-full bg-green-50 text-green-700 text-xs font-semibold">Published</span>
                                <div class="text-xs text-gray-400 mt-1">{{ $blogPost->published_at->format('M j, Y') }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-600">{{ $blogPost->comment_count }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $blogPost->like_count }}</td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <a href="{{ route('blog.show', $blogPost) }}" target="_blank" class="inline-flex items-center gap-1 text-gray-500 hover:text-gray-800 mr-4">
                                <x-lucide-external-link class="w-4 h-4" /> {{ $blogPost->isPublished() ? 'View' : 'Preview' }}
                            </a>
                            <a href="{{ route('admin.blog-posts.edit', $blogPost) }}" class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-700 font-medium mr-4">
                                <x-lucide-pencil class="w-4 h-4" /> Edit
                            </a>
                            <form method="POST" action="{{ route('admin.blog-posts.destroy', $blogPost) }}" class="inline" onsubmit="return confirm('Delete this post?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center gap-1 text-red-600 hover:text-red-700 font-medium">
                                    <x-lucide-trash-2 class="w-4 h-4" /> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">No posts here yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $blogPosts->links() }}
    </div>
</x-layouts.admin>
