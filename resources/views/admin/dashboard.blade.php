<x-layouts.admin title="Dashboard">
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
        <a href="{{ route('admin.subscriptions.index', ['status' => 'pending']) }}" class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <p class="text-sm font-medium text-gray-600">Pending Subscriptions</p>
            <p class="text-2xl font-bold text-gray-900">{{ $pendingSubscriptions }}</p>
        </a>
        <a href="{{ route('admin.users.index') }}" class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <p class="text-sm font-medium text-gray-600">Students</p>
            <p class="text-2xl font-bold text-gray-900">{{ $totalStudents }}</p>
        </a>
        <a href="{{ route('admin.videos.index') }}" class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <p class="text-sm font-medium text-gray-600">Videos</p>
            <p class="text-2xl font-bold text-gray-900">{{ $totalVideos }}</p>
        </a>
        <a href="{{ route('admin.docs.index') }}" class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <p class="text-sm font-medium text-gray-600">Documents</p>
            <p class="text-2xl font-bold text-gray-900">{{ $totalDocs }}</p>
        </a>
        <a href="{{ route('admin.blog-posts.index') }}" class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <p class="text-sm font-medium text-gray-600">Blog Posts</p>
            <p class="text-2xl font-bold text-gray-900">{{ $totalBlogPosts }}</p>
        </a>
    </div>

    @if ($pendingSubscriptions > 0)
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg">
            You have {{ $pendingSubscriptions }} subscription{{ $pendingSubscriptions === 1 ? '' : 's' }} awaiting review.
            <a href="{{ route('admin.subscriptions.index', ['status' => 'pending']) }}" class="underline font-medium">Review now</a>.
        </div>
    @endif
</x-layouts.admin>
