{{--
    Ported from src/pages/privatePages/Home.tsx. The mobile drawer's open/close state uses
    Alpine.js (x-data) instead of React's useState. The source hardcodes username = "John Doe";
    that is replaced with the real authenticated user's name. All stats (24 videos, 12
    documents, 68% progress, 7 day streak) are static placeholders in the source — no data
    model backs them — and are kept as-is.
--}}
<x-layouts.public>
    @php
        $username = Auth::user()->name;
        $userInitials = collect(explode(' ', $username))->map(fn ($n) => $n[0] ?? '')->implode('');
    @endphp

    <div x-data="{ drawerOpen: false }">
        <!-- Desktop Header -->
        <header class="hidden md:flex w-full bg-white px-6 py-4 shadow-sm border-b border-gray-200 justify-between items-center">
            <div class="flex items-center space-x-2">
                <img src="/logo.jpg" alt="Logo" class="w-10 h-10 rounded-full">
                <strong>
                    <a href="{{ url('/') }}" class="text-gray-900 text-lg font-semibold">The Way to Fluency</a>
                </strong>
            </div>
            <div class="flex items-center space-x-4">
                <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-sm">
                    {{ $userInitials }}
                </div>
            </div>
        </header>

        <div class="h-[calc(100vh-75px)] flex bg-gray-50">
            <!-- Desktop Sidebar -->
            <aside class="w-56 bg-white shadow-lg hidden md:flex flex-col border-r border-gray-200">
                <nav class="flex-1 py-4">
                    <a href="{{ url('/home') }}" class="flex items-center space-x-3 px-6 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 hover:border-r-2 hover:border-blue-600 transition-all duration-200">
                        <span class="flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                        </span>
                        <span class="font-medium">Home</span>
                    </a>
                    <a href="{{ url('/videos') }}" class="flex items-center space-x-3 px-6 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 hover:border-r-2 hover:border-blue-600 transition-all duration-200">
                        <span class="flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </span>
                        <span class="font-medium">Videos</span>
                    </a>
                    <a href="{{ url('/documents') }}" class="flex items-center space-x-3 px-6 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 hover:border-r-2 hover:border-blue-600 transition-all duration-200">
                        <span class="flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </span>
                        <span class="font-medium">Documents</span>
                    </a>
                    <a href="{{ url('/blog') }}" class="flex items-center space-x-3 px-6 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 hover:border-r-2 hover:border-blue-600 transition-all duration-200">
                        <span class="flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                            </svg>
                        </span>
                        <span class="font-medium">Blog</span>
                    </a>
                    <a href="{{ url('/studyguide') }}" class="flex items-center space-x-3 px-6 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 hover:border-r-2 hover:border-blue-600 transition-all duration-200">
                        <span class="flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </span>
                        <span class="font-medium">Study Guide</span>
                    </a>
                </nav>

                <!-- User Profile Section -->
                <div class="border-t border-gray-200 p-4">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-sm">
                            {{ $userInitials }}
                        </div>
                        <div>
                            <div class="font-medium text-gray-900">{{ $username }}</div>
                            <div class="text-sm text-gray-500">Student</div>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <a href="{{ route('profile.edit') }}" class="flex items-center space-x-3 px-3 py-2 text-gray-600 hover:bg-gray-50 hover:text-gray-900 rounded-md transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span class="text-sm">Profile</span>
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center space-x-3 px-3 py-2 text-gray-600 hover:bg-red-50 hover:text-red-600 rounded-md transition-colors w-full text-left">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                <span class="text-sm">Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            <!-- Main Content -->
            <div class="flex-1 flex flex-col">
                <!-- Mobile Navbar -->
                <nav class="w-full bg-white p-4 shadow-md flex justify-between items-center md:hidden">
                    <div class="flex items-center space-x-2">
                        <img src="/logo.jpg" alt="Logo" class="w-8 h-8 rounded-full">
                        <strong>
                            <a href="{{ url('/') }}" class="text-gray-900 text-lg font-semibold">TWTF</a>
                        </strong>
                    </div>
                    <button class="p-2 text-gray-600 hover:text-gray-900 focus:outline-none" @click="drawerOpen = !drawerOpen" aria-label="Toggle menu">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </nav>

                <!-- Mobile Drawer -->
                <template x-if="drawerOpen">
                    <div>
                        <!-- Backdrop -->
                        <div class="fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden" @click="drawerOpen = false"></div>

                        <!-- Drawer -->
                        <div class="fixed top-0 right-0 h-full w-80 bg-white shadow-2xl z-50 md:hidden transform transition-transform duration-300 ease-in-out">
                            <div class="flex flex-col h-full">
                                <div class="flex items-center justify-between p-4 border-b border-gray-200">
                                    <button class="p-2 text-gray-400 hover:text-gray-600 focus:outline-none" @click="drawerOpen = false" aria-label="Close menu">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>

                                <div class="p-4 border-b border-gray-200">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold">
                                            {{ $userInitials }}
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-900">{{ $username }}</div>
                                            <div class="text-sm text-gray-500">Student</div>
                                        </div>
                                    </div>
                                </div>

                                <nav class="flex-1 py-4">
                                    <a href="{{ url('/home') }}" class="flex items-center space-x-3 px-6 py-4 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors border-b border-gray-100" @click="drawerOpen = false">
                                        <span class="font-medium">Home</span>
                                    </a>
                                    <a href="{{ url('/videos') }}" class="flex items-center space-x-3 px-6 py-4 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors border-b border-gray-100" @click="drawerOpen = false">
                                        <span class="font-medium">Videos</span>
                                    </a>
                                    <a href="{{ url('/documents') }}" class="flex items-center space-x-3 px-6 py-4 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors border-b border-gray-100" @click="drawerOpen = false">
                                        <span class="font-medium">Documents</span>
                                    </a>
                                    <a href="{{ url('/blog') }}" class="flex items-center space-x-3 px-6 py-4 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors border-b border-gray-100" @click="drawerOpen = false">
                                        <span class="font-medium">Blog</span>
                                    </a>
                                    <a href="{{ url('/studyguide') }}" class="flex items-center space-x-3 px-6 py-4 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors border-b border-gray-100" @click="drawerOpen = false">
                                        <span class="font-medium">Study Guide</span>
                                    </a>
                                </nav>

                                <div class="border-t border-gray-200 p-4 space-y-2">
                                    <a href="{{ route('profile.edit') }}" class="flex items-center space-x-3 px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900 rounded-lg transition-colors" @click="drawerOpen = false">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <span>Profile Settings</span>
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex items-center space-x-3 px-4 py-3 text-red-600 hover:bg-red-50 rounded-lg transition-colors w-full text-left">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                            </svg>
                                            <span>Sign Out</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Content Section -->
                <main class="flex-1 p-4 md:p-8 bg-gray-50 h-[100vh] overflow-y-auto">
                    <!-- Mobile Welcome Message -->
                    <div class="md:hidden mb-6">
                        <h1 class="text-xl font-bold text-gray-800 mb-1">Welcome, {{ $username }}!</h1>
                        <p class="text-gray-600 text-sm">Continue your learning journey</p>
                    </div>

                    <!-- Quick Stats -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Total Videos</p>
                                    <p class="text-2xl font-bold text-gray-900">24</p>
                                </div>
                                <div class="p-2 bg-blue-100 rounded-lg">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Documents</p>
                                    <p class="text-2xl font-bold text-gray-900">12</p>
                                </div>
                                <div class="p-2 bg-green-100 rounded-lg">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Progress</p>
                                    <p class="text-2xl font-bold text-gray-900">68%</p>
                                </div>
                                <div class="p-2 bg-yellow-100 rounded-lg">
                                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Streak</p>
                                    <p class="text-2xl font-bold text-gray-900">7 days</p>
                                </div>
                                <div class="p-2 bg-purple-100 rounded-lg">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Main Content Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                            <div class="flex items-center space-x-3 mb-4">
                                <div class="p-2 bg-blue-100 rounded-lg">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <h2 class="text-lg font-semibold text-gray-800">Video Library</h2>
                            </div>
                            <p class="text-gray-600 mb-4">
                                Access our comprehensive video library to enhance your learning experience with interactive content.
                            </p>
                            <a href="{{ url('/videos') }}" class="inline-flex items-center space-x-2 text-blue-600 hover:text-blue-700 font-medium transition-colors">
                                <span>Explore Videos</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>

                        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                            <div class="flex items-center space-x-3 mb-4">
                                <div class="p-2 bg-green-100 rounded-lg">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <h2 class="text-lg font-semibold text-gray-800">Study Materials</h2>
                            </div>
                            <p class="text-gray-600 mb-4">
                                Download and access important documents, worksheets, and study materials for your courses.
                            </p>
                            <a href="{{ url('/documents') }}" class="inline-flex items-center space-x-2 text-green-600 hover:text-green-700 font-medium transition-colors">
                                <span>View Documents</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>

                        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                            <div class="flex items-center space-x-3 mb-4">
                                <div class="p-2 bg-purple-100 rounded-lg">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                    </svg>
                                </div>
                                <h2 class="text-lg font-semibold text-gray-800">Latest Posts</h2>
                            </div>
                            <p class="text-gray-600 mb-4">
                                Stay updated with our latest blog posts, tips, and announcements from the learning community.
                            </p>
                            <a href="{{ url('/blog') }}" class="inline-flex items-center space-x-2 text-purple-600 hover:text-purple-700 font-medium transition-colors">
                                <span>Read Blog</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>

                        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                            <div class="flex items-center space-x-3 mb-4">
                                <div class="p-2 bg-yellow-100 rounded-lg">
                                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                                <h2 class="text-lg font-semibold text-gray-800">Study Guide</h2>
                            </div>
                            <p class="text-gray-600 mb-4">
                                Follow our structured study guide to stay on track and achieve your learning goals effectively.
                            </p>
                            <a href="{{ url('/studyguide') }}" class="inline-flex items-center space-x-2 text-yellow-600 hover:text-yellow-700 font-medium transition-colors">
                                <span>Start Guide</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>
</x-layouts.public>
