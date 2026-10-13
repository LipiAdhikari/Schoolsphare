<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'SchoolSphere' }}</title>

    <!-- Google Fonts: Instrument Sans -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-slate-800 bg-slate-50 flex flex-col justify-between">
    <header class="h-16 border-b border-slate-200/80 bg-white px-4 sm:px-6 flex items-center justify-between">
        <a href="{{ url('/') }}" class="flex items-center gap-2.5">
            <div class="h-9 w-9 rounded-lg bg-indigo-600 flex items-center justify-center text-white shadow-xs">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342" />
                </svg>
            </div>
            <span class="text-base font-bold text-slate-900 tracking-tight">SchoolSphere</span>
        </a>

        <div>
            <a href="{{ url('/dashboard') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">
                Go to Dashboard &rarr;
            </a>
        </div>
    </header>

    <main class="flex-1 flex items-center justify-center p-4 sm:p-6">
        <div class="w-full max-w-md">
            <x-flash />
            {{ $slot }}
        </div>
    </main>

    <footer class="h-12 border-t border-slate-200/80 bg-white px-4 sm:px-6 flex items-center justify-center text-xs text-slate-500">
        &copy; {{ date('Y') }} SchoolSphere — Smart School Management System
    </footer>
</body>
</html>
