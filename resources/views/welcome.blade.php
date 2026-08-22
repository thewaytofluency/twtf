<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="snap-y snap-mandatory scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="">

        <title>{{ config('app.name', 'The Way to Fluency') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        {{--
            Originally ported 1:1 from src/pages/LandingPage.tsx as its own route, this page now
            also absorbs the former /courses, /impact, /pricing pages as fullpage scroll-snap
            sections (#home, #courses, #impact, #pricing) — see the `snap-y snap-mandatory
            scroll-smooth` on <html> above, which drives the snapping, and
            partials/navbar.blade.php for the fixed nav + #anchor links that drive click-to-scroll.
            Each section uses `scroll-mt-24` so it settles below the fixed nav instead of under it.
        --}}
        @include('partials.navbar')

        <section id="home" class="min-h-screen snap-start scroll-mt-24 pt-24 flex flex-col items-center justify-between bg-white bg-hero relative">
            <!-- Hero -->
            <div class="backImg flex flex-col-reverse md:flex-row items-center max-w-[77rem] py-8 px-7">
                <!-- Text Content -->
                <div class="flex-1 text-center md:text-left">
                    <p class="mt-4 text-gray-700 text-lg">Speak from Day 1</p>
                    <h1 class="mt-4 text-[2.7rem] font-extrabold text-gray-900 leading-tight">
                        Join <span class="text-blue-600"> The Way to Fluency</span> today and see where English can take you!
                    </h1>
                    <p class="mt-4 md:text-gray-700 text-lg text-white">
                        Master English with interactive lessons, expert tips, and engaging content.
                    </p>
                    <div class="md:mt-8 mt-10 flex justify-center md:justify-start space-x-4">
                        <button class="bg-blue-600 text-white px-6 py-2 rounded-full shadow-md text-md hover:bg-blue-700 transition ring-1 ring-white">
                            Get Started
                        </button>
                        <button class="bg-white text-gray-800 px-6 py-2 rounded-full border border-gray-300 text-md hover:bg-gray-200 transition ring-1 ring-white">
                            Learn More
                        </button>
                    </div>
                </div>

                <!-- Logo Image -->
                <div class="flex-1 flex justify-center relative">
                    <img src="/logo.jpg" alt="Logo" class="max-w-xs rounded-full z-[1] bg-white shadow-lg">
                </div>
            </div>

            <!-- Partners -->
            <div class="w-full max-w-[73rem] text-center pt-8">
                <div class="relative flex items-center md:justify-start justify-center md:mb-0 mb-8">
                    <svg width="105" height="105" viewBox="0 0 282 281">
                        <path
                            d="M75.9469 280.078C91.5033 280.078 105.96 275.398 117.97 267.376C130.585 258.951 151.308 258.951 163.924 267.376C175.934 275.398 190.39 280.078 205.947 280.078C247.615 280.078 281.394 246.499 281.394 205.078C281.394 189.768 276.779 175.529 268.856 163.661C260.282 150.817 260.282 129.339 268.856 116.496C276.779 104.628 281.394 90.3887 281.394 75.0781C281.394 33.6568 247.615 0.078125 205.947 0.078125C190.39 0.078125 175.934 4.75843 163.924 12.7798C151.308 21.2056 130.585 21.2056 117.97 12.7798C105.96 4.75843 91.5033 0.078125 75.9469 0.078125C34.2787 0.078125 0.5 33.6568 0.5 75.0781C0.5 90.3887 5.11505 104.628 13.0377 116.496C21.6119 129.339 21.6119 150.817 13.0377 163.661C5.11505 175.529 0.5 189.768 0.5 205.078C0.5 246.499 34.2787 280.078 75.9469 280.078Z"
                            fill-rule="evenodd"
                            clip-rule="evenodd"
                            fill="#38b6ff"
                        ></path>
                    </svg>
                    <p class="absolute text-white text-md px-5 text-center font-semibold">
                        Inspiring <br> Global <br> Learners!
                    </p>
                </div>

                <div class="flex justify-evenly space-x-6 mt-4 mb-4">
                    <a href="https://www.youtube.com" target="_blank" rel="noopener noreferrer">
                        <img src="/images/youtube.png" alt="YouTube" class="h-7 hover:scale-110 transition-transform">
                    </a>
                    <a href="https://www.facebook.com" target="_blank" rel="noopener noreferrer">
                        <img src="/images/facebook.png" alt="Facebook" class="h-7 hover:scale-110 transition-transform">
                    </a>
                    <a href="https://www.whatsapp.com" target="_blank" rel="noopener noreferrer">
                        <img src="/images/whatsapp.png" alt="WhatsApp" class="h-7 hover:scale-110 transition-transform">
                    </a>
                    <a href="https://www.instagram.com" target="_blank" rel="noopener noreferrer">
                        <img src="/images/instagram.png" alt="Instagram" class="h-7 hover:scale-110 transition-transform">
                    </a>
                </div>
                {{-- Footer intentionally omitted: source LandingPage.tsx imports Footer but its
                     usage is commented out, so it never renders there either. See
                     resources/views/partials/footer.blade.php. --}}
            </div>
        </section>

        {{--
            Ported from src/pages/CoursesPage.tsx, redesigned beyond the source's plain
            bordered cards: level badges (emoji-in-gradient-circle, matching the hand-drawn/
            playful logo art rather than literal icon fidelity) replace the original's odd
            choice of repeating /logo.jpg as the card image for all three courses.
        --}}
        <section id="courses" class="min-h-screen snap-start scroll-mt-24 flex flex-col items-center justify-center relative bg-gray-50">
            <div class="max-w-[77rem] w-full py-12 px-6">
                <div class="text-center">
                    <span class="inline-block px-4 py-1 rounded-full bg-blue-100 text-blue-600 text-sm font-semibold tracking-wide uppercase">Our Courses</span>
                    <h1 class="mt-4 text-[2.7rem] font-extrabold text-gray-900 leading-tight">
                        Explore Our Courses
                    </h1>
                    <p class="mt-4 text-gray-600 text-lg">
                        Choose the course that fits your learning goals.
                    </p>
                </div>

                @php
                    $courses = [
                        [
                            'title' => 'Beginner English',
                            'description' => 'Start your journey with basic English lessons.',
                            'emoji' => '🌱',
                            'accent' => 'from-green-400 to-green-600',
                        ],
                        [
                            'title' => 'Intermediate English',
                            'description' => 'Enhance your skills with intermediate-level content.',
                            'emoji' => '📈',
                            'accent' => 'from-blue-400 to-blue-600',
                        ],
                        [
                            'title' => 'Advanced English',
                            'description' => 'Master English with advanced lessons and tips.',
                            'emoji' => '🏆',
                            'accent' => 'from-purple-400 to-purple-600',
                        ],
                    ];
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-12">
                    @foreach ($courses as $course)
                        <div class="group bg-white rounded-2xl shadow-md hover:shadow-xl p-8 flex flex-col items-center text-center transition-all duration-300 hover:-translate-y-1">
                            <div class="w-16 h-16 rounded-full bg-gradient-to-br {{ $course['accent'] }} flex items-center justify-center text-3xl mb-5 shadow-md transition-transform duration-300 group-hover:scale-110">
                                {{ $course['emoji'] }}
                            </div>
                            <h2 class="text-xl font-bold text-gray-800">{{ $course['title'] }}</h2>
                            <p class="text-gray-600 mt-2">{{ $course['description'] }}</p>
                            <button class="mt-6 bg-blue-600 text-white px-6 py-2 rounded-full shadow-md text-md hover:bg-blue-700 transition-colors duration-300">
                                Learn More
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{--
            Ported from src/pages/ImpactPage.tsx. The source's /impact1.jpg, /impact2.jpg,
            /impact3.jpg never existed anywhere in the project — they rendered as broken image
            icons. This redesign replaces them with themed emoji badges instead of leaving them
            broken (this page is now an intentional restyle, not a strict 1:1 port).
        --}}
        <section id="impact" class="min-h-screen snap-start scroll-mt-24 flex flex-col items-center justify-center relative bg-white">
            <div class="max-w-[77rem] w-full py-12 px-6">
                <div class="text-center">
                    <span class="inline-block px-4 py-1 rounded-full bg-blue-100 text-blue-600 text-sm font-semibold tracking-wide uppercase">Our Impact</span>
                    <h1 class="mt-4 text-[2.7rem] font-extrabold text-gray-900 leading-tight">
                        Our Impact
                    </h1>
                    <p class="mt-4 text-gray-600 text-lg">
                        Discover how we are making a difference in the world.
                    </p>
                </div>

                @php
                    $impacts = [
                        [
                            'title' => 'Empowering Communities',
                            'description' => 'Helping communities grow through education and resources.',
                            'emoji' => '🤝',
                        ],
                        [
                            'title' => 'Transforming Lives',
                            'description' => 'Providing opportunities to individuals for a better future.',
                            'emoji' => '🚀',
                        ],
                        [
                            'title' => 'Global Reach',
                            'description' => 'Making a difference across the globe with our initiatives.',
                            'emoji' => '🌍',
                        ],
                    ];
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-12">
                    @foreach ($impacts as $impact)
                        <div class="group bg-gray-50 border border-gray-100 rounded-2xl shadow-sm hover:shadow-lg p-8 flex flex-col items-center text-center transition-all duration-300 hover:-translate-y-1">
                            <div class="w-16 h-16 rounded-full bg-blue-50 flex items-center justify-center text-3xl mb-5 transition-transform duration-300 group-hover:scale-110">
                                {{ $impact['emoji'] }}
                            </div>
                            <h2 class="text-xl font-bold text-gray-800">{{ $impact['title'] }}</h2>
                            <p class="text-gray-600 mt-2">{{ $impact['description'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{--
            Ported from src/pages/PlansPricesPage.tsx, redesigned with a highlighted "Most
            Popular" tier and equal-height cards (the source's plain cards had uneven heights
            since Basic has 1 feature vs. 3 for Standard/Premium, leaving Basic's button
            floating in a lot of empty space).
        --}}
        <section id="pricing" class="min-h-screen snap-start scroll-mt-24 flex flex-col items-center justify-center relative bg-hero">
            <div class="max-w-[77rem] w-full py-12 px-6">
                <div class="text-center">
                    <span class="inline-block px-4 py-1 rounded-full bg-white text-blue-600 text-sm font-semibold tracking-wide uppercase shadow-sm">Pricing</span>
                    <h1 class="mt-4 text-[2.7rem] font-extrabold text-gray-900 leading-tight">
                        Choose Your Plan
                    </h1>
                    <p class="mt-4 text-gray-700 text-lg">
                        Find the perfect plan tailored to your learning needs.
                    </p>
                </div>

                @php
                    $plans = [
                        [
                            'title' => 'Basic',
                            'description' => 'Perfect for beginners.',
                            'price' => '1500MZN/month',
                            'features' => ['Access to basic lessons'],
                            'buttonText' => 'Choose Basic',
                            'popular' => false,
                        ],
                        [
                            'title' => 'Standard',
                            'description' => 'Ideal for intermediate learners.',
                            'price' => '2000MZN/month',
                            'features' => ['Access to all lessons', 'Expert tips and tricks', 'Priority support'],
                            'buttonText' => 'Choose Standard',
                            'popular' => true,
                        ],
                        [
                            'title' => 'Premium',
                            'description' => 'For advanced learners.',
                            'price' => '3000MZN/month',
                            'features' => ['Access to all lessons', '1-on-1 coaching', 'Exclusive content'],
                            'buttonText' => 'Choose Premium',
                            'popular' => false,
                        ],
                    ];
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-12">
                    @foreach ($plans as $plan)
                        <div class="relative bg-white rounded-2xl p-8 h-full flex flex-col text-center transition-all duration-300 {{ $plan['popular'] ? 'shadow-2xl ring-2 ring-blue-600 md:-translate-y-4' : 'shadow-md hover:shadow-xl' }}">
                            @if ($plan['popular'])
                                <span class="absolute -top-4 left-1/2 -translate-x-1/2 bg-blue-600 text-white text-xs font-bold uppercase tracking-wide px-4 py-1 rounded-full shadow-md">
                                    Most Popular
                                </span>
                            @endif

                            <h2 class="text-xl font-bold text-gray-800">{{ $plan['title'] }}</h2>
                            <p class="text-gray-600 mt-2">{{ $plan['description'] }}</p>
                            <p class="text-2xl font-extrabold text-blue-600 mt-4">{{ $plan['price'] }}</p>

                            <ul class="mt-6 space-y-3 flex-1 text-left">
                                @foreach ($plan['features'] as $feature)
                                    <li class="flex items-center gap-2 text-gray-600">
                                        <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        {{ $feature }}
                                    </li>
                                @endforeach
                            </ul>

                            <button class="mt-8 w-full text-white px-6 py-3 rounded-full shadow-md font-semibold transition-colors duration-300 {{ $plan['popular'] ? 'bg-blue-600 hover:bg-blue-700' : 'bg-gray-900 hover:bg-gray-800' }}">
                                {{ $plan['buttonText'] }}
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- `snap-end` (not `snap-start`) so this stays a valid, exactly-reachable snap point
             without needing `min-h-screen` — the footer's bottom edge aligns with the viewport
             bottom right at the document's natural end, instead of needing to fill a full
             screen just to remain reachable under `snap-mandatory` (see courses/impact/pricing
             sections above for why a non-snap trailing element doesn't work here). --}}
        <div class="snap-end bg-blue-600 py-10">
            @include('partials.footer')
        </div>
    </body>
</html>
