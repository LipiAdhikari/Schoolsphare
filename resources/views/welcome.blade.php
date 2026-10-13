<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>SchoolSphere — Smart School Management System</title>

    <!-- Google Fonts: Instrument Sans -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-slate-800 bg-slate-50 flex flex-col justify-between">
    <!-- Header -->
    <header class="h-16 border-b border-slate-200/80 bg-white/90 backdrop-blur-md sticky top-0 z-30 px-4 sm:px-8 flex items-center justify-between">
        <a href="{{ url('/') }}" class="flex items-center gap-3 group">
            <div class="h-9 w-9 rounded-lg bg-indigo-600 flex items-center justify-center text-white shadow-xs group-hover:bg-indigo-500 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342" />
                </svg>
            </div>
            <div>
                <span class="text-base font-bold text-slate-900 tracking-tight">SchoolSphere</span>
                <span class="hidden sm:inline-block text-[11px] text-slate-400 font-medium ml-1.5 uppercase tracking-wide">Smart School OS</span>
            </div>
        </a>

        <div class="flex items-center gap-3">
            <span class="hidden md:inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                MySQL Ready
            </span>
            <x-button variant="primary" size="sm" href="{{ url('/dashboard') }}">
                Launch Dashboard &rarr;
            </x-button>
        </div>
    </header>

    <!-- Main Hero -->
    <main class="flex-1 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto space-y-12">
            <!-- Hero Statement -->
            <div class="text-center space-y-4 pt-4">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 border border-indigo-100 text-indigo-700">
                    <span>Phase 2 Completed</span>
                    <span class="text-indigo-300">&bull;</span>
                    <span>Responsive Blade & Tailwind v4 UI</span>
                </div>
                <h1 class="text-3xl sm:text-5xl font-extrabold text-slate-900 tracking-tight">
                    Centralized School Administration & Academics
                </h1>
                <p class="text-sm sm:text-base text-slate-600 max-w-2xl mx-auto leading-relaxed">
                    SchoolSphere replaces disconnected spreadsheets with a unified, relational database and role-based workflows for administrators, teachers, students, and parents.
                </p>
                <div class="pt-2 flex flex-wrap items-center justify-center gap-3">
                    <x-button variant="primary" size="lg" href="{{ url('/dashboard') }}">
                        Open Executive Dashboard
                    </x-button>
                    <x-button variant="secondary" size="lg" href="https://github.com" target="_blank">
                        View Project Architecture
                    </x-button>
                </div>
            </div>

            <!-- Feature Capabilities Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div class="p-5 rounded-xl bg-white border border-slate-200/80 shadow-xs space-y-2">
                    <div class="h-9 w-9 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900">Academic Structure</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Academic years, multi-section classes, subject allocations, and historical student enrollment tracking.
                    </p>
                </div>

                <div class="p-5 rounded-xl bg-white border border-slate-200/80 shadow-xs space-y-2">
                    <div class="h-9 w-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900">Attendance & Rosters</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Daily class attendance recording, duplicate entry prevention, and aggregated percentage analytics.
                    </p>
                </div>

                <div class="p-5 rounded-xl bg-white border border-slate-200/80 shadow-xs space-y-2">
                    <div class="h-9 w-9 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900">Fees & Examination Marks</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Invoice generation, partial payments balance tracking, and configurable exam grading scales.
                    </p>
                </div>
            </div>

            <!-- Architecture Banner -->
            <div class="p-6 rounded-2xl bg-slate-900 text-white flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h4 class="text-sm font-bold tracking-tight">Built with Professional Full-Stack Standards</h4>
                    <p class="text-xs text-slate-400 mt-1">Laravel 12 &bull; PHP 8.2 &bull; MySQL (XAMPP) &bull; Tailwind CSS v4 &bull; Blade &bull; Vite 7</p>
                </div>
                <x-button variant="outline" size="sm" href="{{ url('/dashboard') }}" class="text-white border-slate-700 hover:bg-slate-800">
                    Preview Dashboard
                </x-button>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="h-14 border-t border-slate-200/80 bg-white px-4 sm:px-8 flex items-center justify-between text-xs text-slate-500">
        <span>&copy; {{ date('Y') }} SchoolSphere. Educational Software System.</span>
        <span class="font-medium text-slate-600">Local Development Environment</span>
    </footer>
</body>
</html>
