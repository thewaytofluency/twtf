{{--
    Ported from src/pages/privatePages/Home.tsx. Shell extracted to
    components/layouts/student.blade.php (Phase 3), shared with the new /videos, /documents,
    /blog, /studyguide pages. The source's 4 stat tiles (24 videos / 12 documents / 68% progress
    / 7 day streak) were all hardcoded fakes with no backing data model - no watch-history or
    login-streak table exists anywhere in the schema, and building one just to fill a vanity
    metric wasn't requested. Replaced with 4 real values from HomeController instead: content
    counts (scoped to what this student's plan actually grants) and their subscription state.
--}}
<x-layouts.student title="Home">
    @php
        $username = Auth::user()->name;
    @endphp

    <!-- Mobile Welcome Message -->
    <div class="md:hidden mb-6">
        <h1 class="text-xl font-bold text-gray-800 mb-1">Welcome, {{ $username }}!</h1>
        <p class="text-gray-600 text-sm">Continue your learning journey</p>
    </div>

    <!-- Continue learning -->
    @if ($resume || $nextDoc)
        <section class="mb-8 grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                @if ($resume)
                    <a href="{{ route('videos.show', $resume) }}" class="group flex flex-col p-5 rounded-xl text-white bg-gradient-to-br from-blue-600 to-blue-500 shadow-sm hover:shadow-md transition">
                        <span class="text-xs uppercase tracking-wide text-blue-100">{{ $resumeStarted ? 'Continue watching' : 'Start learning' }}</span>
                        <span class="mt-1 font-semibold leading-snug">{{ $resume->title }}</span>
                        <span class="mt-auto pt-4 inline-flex items-center gap-1 text-sm font-medium">
                            <x-lucide-play class="w-4 h-4" /> {{ $resume->course_level->label() }}
                            <x-lucide-chevron-right class="w-4 h-4 transition-transform group-hover:translate-x-1" />
                        </span>
                    </a>
                @endif
                @if ($nextDoc)
                    <a href="{{ route('documents.show', $nextDoc) }}" class="group flex flex-col p-5 rounded-xl bg-white border border-gray-200 shadow-sm hover:shadow-md hover:border-green-300 transition">
                        <span class="text-xs uppercase tracking-wide text-green-600">Next to study</span>
                        <span class="mt-1 font-semibold leading-snug text-gray-800">{{ $nextDoc->title }}</span>
                        <span class="mt-auto pt-4 inline-flex items-center gap-1 text-sm font-medium text-green-600">
                            <x-lucide-file-text class="w-4 h-4" /> {{ $nextDoc->course_level?->label() ?? 'General' }}
                            <x-lucide-chevron-right class="w-4 h-4 transition-transform group-hover:translate-x-1" />
                        </span>
                    </a>
                @endif
            </div>

            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                <h2 class="text-sm font-semibold text-gray-800 mb-3">Your progress</h2>
                <div class="space-y-3">
                    @foreach ($levelProgress as $row)
                        <div>
                            <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                                <span>{{ $row['level']->label() }}</span>
                                <span>{{ $row['done'] }}/{{ $row['total'] }}</span>
                            </div>
                            <div class="h-1.5 rounded-full bg-gray-100 overflow-hidden">
                                <div class="h-full bg-green-500" style="width: {{ $row['percent'] }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <a href="{{ route('profile.edit') }}" class="mt-4 inline-flex items-center gap-1 text-xs font-medium text-blue-600 hover:text-blue-700">
                    See all your stats <x-lucide-chevron-right class="w-3 h-3" />
                </a>
            </div>
        </section>
    @endif

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Videos</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $videoCount }}</p>
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
                    <p class="text-2xl font-bold text-gray-900">{{ $docCount }}</p>
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
                    <p class="text-sm font-medium text-gray-600">Current Plan</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $planName }}</p>
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
                    <p class="text-sm font-medium text-gray-600">Plan Expires</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $planExpires ?? '-' }}</p>
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
</x-layouts.student>
