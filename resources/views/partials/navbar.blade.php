{{--
    Public-facing top navigation, ported from src/components/Navbar.tsx.
    Mobile drawer open/close state uses Alpine.js (x-data) instead of React's useState.

    Fixed to the viewport and pointing at in-page anchors (#courses, #impact, #pricing)
    since the landing page is now one fullpage-scroll-snap document (see welcome.blade.php)
    rather than separate /courses, /impact, /pricing routes.
--}}
<div x-data="{ open: false }" class="w-full fixed top-0 inset-x-0 z-50 bg-white shadow-sm">
    <nav class="w-full max-w-[76rem] bg-white p-4 pb-2 flex justify-between items-center mx-auto">
        <div class="flex items-center space-x-2">
            <img src="/logo.jpg" alt="Logo" class="w-10 h-10 rounded-full">
            <strong>
                <a href="#home" class="text-gray-900 text-lg font-semibold">The Way to Fluency</a>
            </strong>
        </div>

        <div class="hidden md:flex md:items-center">
            <a href="#courses" class="text-gray-600 mx-4 hover:text-blue-600 transition">Courses</a>
            <a href="#impact" class="text-gray-600 mx-4 hover:text-blue-600 transition">Our Impact</a>
            <a href="#pricing" class="text-gray-600 mx-4 hover:text-blue-600 transition">Plans and Pricing</a>

            @guest
                <a href="{{ route('login') }}" class="text-gray-600 mx-4 hover:text-blue-600 transition">Login</a>
                <a href="{{ route('register') }}" class="bg-blue-600 mx-4 text-white font-bold px-5 py-2 rounded-full hover:bg-blue-700 transition">Enroll Now</a>
            @else
                <a href="{{ url('/home') }}" class="bg-blue-600 mx-4 text-white font-bold px-5 py-2 rounded-full hover:bg-blue-700 transition">Keep Learning</a>
            @endguest
        </div>

        <button
            class="md:hidden text-gray-600 focus:outline-none"
            aria-label="Toggle menu"
            @click="open = !open"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </nav>

    <!-- Mobile Drawer Menu -->
    <div
        class="fixed top-0 right-0 h-full w-64 bg-white shadow-lg transform transition-transform duration-300"
        :class="open ? 'translate-x-0 z-10' : 'translate-x-full'"
    >
        <button
            class="absolute top-4 right-4 text-gray-600 focus:outline-none"
            aria-label="Close menu"
            @click="open = false"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        <div class="flex flex-col items-start space-y-4 p-6">
            <a href="#courses" @click="open = false" class="text-gray-600 hover:font-bold hover:text-blue-600 transition">Courses</a>
            <a href="#impact" @click="open = false" class="text-gray-600 hover:font-bold hover:text-blue-600 transition">Our Impact</a>
            <a href="#pricing" @click="open = false" class="text-gray-600 hover:font-bold hover:text-blue-600 transition">Plans and Pricing</a>

            @guest
                <a href="{{ route('login') }}" class="text-gray-600 hover:text-blue-600 transition">Login</a>
                <a href="{{ route('register') }}" class="bg-blue-600 text-white font-bold px-5 py-2 rounded-full hover:bg-blue-700 transition">Enroll Now</a>
            @else
                <a href="{{ url('/home') }}" class="bg-blue-600 text-white font-bold px-5 py-2 rounded-full hover:bg-blue-700 transition">Keep Learning</a>
            @endguest
        </div>
    </div>
</div>
