<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Admin' }} - {{ config('app.name', 'The Way to Fluency') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        {{--
            New admin-only shell (Phase 2) - reuses the visual language of the student
            dashboard (resources/views/home.blade.php: same sidebar classes, same
            desktop/mobile split) without touching that file, plus active-route
            highlighting the student sidebar currently lacks.
        --}}
        <div x-data="{ drawerOpen: false }">
            <!-- Desktop Header -->
            <header class="hidden md:flex w-full bg-white px-6 py-4 shadow-sm border-b border-gray-200 justify-between items-center">
                <div class="flex items-center space-x-2">
                    <img src="/logo.jpg" alt="Logo" class="w-10 h-10 rounded-full">
                    <strong>
                        <a href="{{ route('admin.dashboard') }}" class="text-gray-900 text-lg font-semibold">Admin Panel</a>
                    </strong>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ url('/home') }}" class="text-sm text-gray-600 hover:text-blue-600 transition">Back to site</a>
                    <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-sm">
                        {{ collect(explode(' ', Auth::user()->name))->map(fn ($n) => $n[0] ?? '')->implode('') }}
                    </div>
                </div>
            </header>

            <div class="h-[100dvh] md:h-[calc(100vh-75px)] flex bg-gray-50">
                <!-- Desktop Sidebar -->
                <aside class="w-56 bg-white shadow-lg hidden md:flex flex-col border-r border-gray-200">
                    <nav class="flex-1 py-4">
                        @php
                            $adminNavItems = [
                                ['route' => 'admin.dashboard', 'pattern' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'layout-dashboard'],
                                ['route' => 'admin.videos.index', 'pattern' => 'admin.videos.*', 'label' => 'Videos', 'icon' => 'clapperboard'],
                                ['route' => 'admin.docs.index', 'pattern' => 'admin.docs.*', 'label' => 'Documents', 'icon' => 'file-text'],
                                ['route' => 'admin.blog-posts.index', 'pattern' => 'admin.blog-posts.*', 'label' => 'Blog Posts', 'icon' => 'newspaper'],
                                ['route' => 'admin.social-media-links.index', 'pattern' => 'admin.social-media-links.*', 'label' => 'Social Links', 'icon' => 'link'],
                                ['route' => 'admin.users.index', 'pattern' => 'admin.users.*', 'label' => 'Students', 'icon' => 'graduation-cap'],
                                ['route' => 'admin.courses.index', 'pattern' => 'admin.courses.*', 'label' => 'Courses', 'icon' => 'book-open'],
                                ['route' => 'admin.impact.index', 'pattern' => 'admin.impact.*', 'label' => 'Our Impact', 'icon' => 'heart-handshake'],
                                ['route' => 'admin.plans.index', 'pattern' => 'admin.plans.*', 'label' => 'Plans', 'icon' => 'layers'],
                                ['route' => 'admin.subscriptions.index', 'pattern' => 'admin.subscriptions.*', 'label' => 'Subscriptions', 'icon' => 'credit-card'],
                            ];
                        @endphp
                        @foreach ($adminNavItems as $item)
                            <a
                                href="{{ route($item['route']) }}"
                                class="flex items-center space-x-3 px-6 py-3 transition-all duration-200 {{ request()->routeIs($item['pattern']) ? 'bg-blue-50 text-blue-600 border-r-2 border-blue-600 font-medium' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600 hover:border-r-2 hover:border-blue-600' }}"
                            >
                                <x-dynamic-component :component="'lucide-'.$item['icon']" class="w-5 h-5 flex-shrink-0" />
                                <span>{{ $item['label'] }}</span>
                            </a>
                        @endforeach
                    </nav>

                    <div class="border-t border-gray-200 p-4">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center space-x-3 px-3 py-2 text-gray-600 hover:bg-red-50 hover:text-red-600 rounded-md transition-colors w-full text-left">
                                <x-lucide-log-out class="w-4 h-4" />
                                <span class="text-sm">Logout</span>
                            </button>
                        </form>
                    </div>
                </aside>

                <!-- Main Content -->
                <div class="flex-1 min-w-0 min-h-0 flex flex-col overflow-hidden">
                    <!-- Mobile Navbar -->
                    <nav class="w-full bg-white p-4 shadow-md flex justify-between items-center md:hidden">
                        <strong class="text-gray-900 text-lg font-semibold">Admin Panel</strong>
                        <button class="p-2 text-gray-600 hover:text-gray-900 focus:outline-none" @click="drawerOpen = !drawerOpen" aria-label="Toggle menu">
                            <x-lucide-menu class="w-6 h-6" />
                        </button>
                    </nav>

                    <!-- Mobile Drawer -->
                    <template x-if="drawerOpen">
                        <div>
                            <div class="fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden" @click="drawerOpen = false"></div>
                            <div class="fixed top-0 right-0 h-full w-80 bg-white shadow-2xl z-50 md:hidden transform transition-transform duration-300 ease-in-out">
                                <div class="flex flex-col h-full">
                                    <div class="flex items-center justify-between p-4 border-b border-gray-200">
                                        <strong class="text-gray-900">Admin Panel</strong>
                                        <button class="p-2 text-gray-400 hover:text-gray-600 focus:outline-none" @click="drawerOpen = false" aria-label="Close menu">
                                            <x-lucide-x class="w-6 h-6" />
                                        </button>
                                    </div>
                                    <nav class="flex-1 py-4">
                                        @foreach ($adminNavItems as $item)
                                            <a href="{{ route($item['route']) }}" class="flex items-center space-x-3 px-6 py-4 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors border-b border-gray-100" @click="drawerOpen = false">
                                                <x-dynamic-component :component="'lucide-'.$item['icon']" class="w-5 h-5 flex-shrink-0" />
                                                <span class="font-medium">{{ $item['label'] }}</span>
                                            </a>
                                        @endforeach
                                    </nav>
                                    <div class="border-t border-gray-200 p-4">
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="flex items-center space-x-3 px-4 py-3 text-red-600 hover:bg-red-50 rounded-lg transition-colors w-full text-left">
                                                <span>Logout</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Content -->
                    <main class="flex-1 min-h-0 p-4 md:p-8 bg-gray-50 overflow-y-auto">
                        @if (session('status'))
                            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                                {{ session('status') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                                <ul class="list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <h1 class="text-2xl font-bold text-gray-800 mb-6">{{ $title ?? 'Admin' }}</h1>

                        {{ $slot }}
                    </main>
                </div>
            </div>
        </div>
    </body>
</html>
