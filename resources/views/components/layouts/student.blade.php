<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Home' }} — {{ config('app.name', 'The Way to Fluency') }}</title>

        <link
            rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
            integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
            crossorigin="anonymous"
            referrerpolicy="no-referrer"
        />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        {{--
            Extracted from home.blade.php (Phase 3) so the 6 new student pages this phase adds
            can share the same sidebar shell instead of duplicating ~150 lines each. Kept as a
            near-literal copy of the original markup (only addition: active-route highlighting,
            which the original sidebar never had) to minimize the chance of a visual regression
            during extraction.
        --}}
        @php
            $username = Auth::user()->name;
            $userInitials = collect(explode(' ', $username))->map(fn ($n) => $n[0] ?? '')->implode('');

            $navActiveClass = 'flex items-center space-x-3 px-6 py-3 bg-blue-50 text-blue-600 border-r-2 border-blue-600 font-medium transition-all duration-200';
            $navInactiveClass = 'flex items-center space-x-3 px-6 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 hover:border-r-2 hover:border-blue-600 transition-all duration-200';
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
                        <a href="{{ url('/home') }}" class="{{ request()->routeIs('home') ? $navActiveClass : $navInactiveClass }}">
                            <span class="flex-shrink-0">
                                <x-lucide-house class="w-5 h-5" />
                            </span>
                            <span class="font-medium">Home</span>
                        </a>
                        <a href="{{ url('/videos') }}" class="{{ request()->routeIs('videos.*') ? $navActiveClass : $navInactiveClass }}">
                            <span class="flex-shrink-0">
                                <x-lucide-video class="w-5 h-5" />
                            </span>
                            <span class="font-medium">Videos</span>
                        </a>
                        <a href="{{ url('/documents') }}" class="{{ request()->routeIs('documents.index') ? $navActiveClass : $navInactiveClass }}">
                            <span class="flex-shrink-0">
                                <x-lucide-file-text class="w-5 h-5" />
                            </span>
                            <span class="font-medium">Documents</span>
                        </a>
                        <a href="{{ url('/blog') }}" class="{{ request()->routeIs('blog.*') ? $navActiveClass : $navInactiveClass }}">
                            <span class="flex-shrink-0">
                                <x-lucide-newspaper class="w-5 h-5" />
                            </span>
                            <span class="font-medium">Blog</span>
                        </a>
                        <a href="{{ url('/studyguide') }}" class="{{ request()->routeIs('documents.study-guide') ? $navActiveClass : $navInactiveClass }}">
                            <span class="flex-shrink-0">
                                <x-lucide-book-open class="w-5 h-5" />
                            </span>
                            <span class="font-medium">Study Guide</span>
                        </a>
                        <a href="{{ route('subscription.index') }}" class="{{ request()->routeIs('subscription.*') ? $navActiveClass : $navInactiveClass }}">
                            <span class="flex-shrink-0">
                                <x-lucide-credit-card class="w-5 h-5" />
                            </span>
                            <span class="font-medium">Subscription</span>
                        </a>
                        @if (Auth::user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="{{ $navInactiveClass }}">
                                <span class="flex-shrink-0">
                                    <x-lucide-shield-check class="w-5 h-5" />
                                </span>
                                <span class="font-medium">Admin Panel</span>
                            </a>
                        @endif
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
                                <x-lucide-user class="w-4 h-4" />
                                <span class="text-sm">Profile</span>
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex items-center space-x-3 px-3 py-2 text-gray-600 hover:bg-red-50 hover:text-red-600 rounded-md transition-colors w-full text-left">
                                    <x-lucide-log-out class="w-4 h-4" />
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
                            <x-lucide-menu class="w-6 h-6" />
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
                                            <x-lucide-x class="w-6 h-6" />
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
                                        <a href="{{ route('subscription.index') }}" class="flex items-center space-x-3 px-6 py-4 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors border-b border-gray-100" @click="drawerOpen = false">
                                            <span class="font-medium">Subscription</span>
                                        </a>
                                        @if (Auth::user()->isAdmin())
                                            <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-6 py-4 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors border-b border-gray-100" @click="drawerOpen = false">
                                                <span class="font-medium">Admin Panel</span>
                                            </a>
                                        @endif
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
                        {{ $slot }}
                    </main>
                </div>
            </div>
        </div>

        {{-- Sibling of the x-data wrapper above (not nested inside <main>'s overflow-y-auto),
             so `fixed` positioning anchors to the viewport regardless of scroll position. --}}
        <x-whatsapp-widget :links="$socialLinks" />
    </body>
</html>
