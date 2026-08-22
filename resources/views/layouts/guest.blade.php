<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex items-center justify-evenly bg-white bg-hero">
            <div class="max-w-md w-full border bg-white p-8 m-4 rounded-xl shadow-lg">
                {{ $slot }}
            </div>
            <div class="hidden md:flex justify-center relative -top-16 -left-8">
                <img src="/logo.jpg" alt="Logo" class="max-w-xs rounded-full z-[1] bg-white shadow-lg" />
            </div>
        </div>
    </body>
</html>
