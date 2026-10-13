<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'SchoolSphere — Smart School Management System' }}</title>

    <!-- Google Fonts: Instrument Sans -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-slate-800 bg-slate-50 flex overflow-hidden">

    <!-- Mobile Sidebar Backdrop Overlay -->
    <div id="sidebar-backdrop" class="fixed inset-0 bg-slate-900/60 z-30 hidden lg:hidden transition-opacity duration-300 opacity-0 pointer-events-none" aria-hidden="true"></div>

    <!-- Application Sidebar -->
    @include('layouts.sidebar')

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 h-full overflow-hidden">
        <!-- Top Navigation Bar -->
        @include('layouts.navigation')

        <!-- Page Sub-Header (Optional) -->
        @if (isset($header))
            <div class="bg-white border-b border-slate-200/80 px-4 sm:px-6 py-4">
                <div class="max-w-7xl mx-auto flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    {{ $header }}
                </div>
            </div>
        @endif

        <!-- Scrollable Main Viewport -->
        <main class="flex-1 overflow-y-auto px-4 sm:px-6 py-6 scrollbar-thin">
            <div class="max-w-7xl mx-auto space-y-6">
                <!-- Flash Notification Banner -->
                <x-flash />

                <!-- Content Slot -->
                {{ $slot }}
            </div>
        </main>

        <!-- Footer -->
        <footer class="h-10 bg-white border-t border-slate-200/80 px-4 sm:px-6 flex items-center justify-between text-[11px] text-slate-500">
            <span>&copy; {{ date('Y') }} SchoolSphere. All rights reserved.</span>
            <div class="flex items-center gap-3">
                <a href="{{ url('/') }}" class="hover:text-indigo-600 transition-colors">Welcome Portal</a>
                <span>&bull;</span>
                <span class="text-slate-400">Laravel 12 / PHP 8.2 / Tailwind v4</span>
            </div>
        </footer>
    </div>

    <!-- Vanilla JavaScript for Sidebar Mobile Interaction -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sidebar = document.getElementById('app-sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            const openBtn = document.getElementById('open-sidebar-button');
            const closeBtn = document.getElementById('close-sidebar-button');

            function openSidebar() {
                if (!sidebar || !backdrop) return;
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden', 'pointer-events-none', 'opacity-0');
                backdrop.classList.add('opacity-100');
                document.body.classList.add('overflow-hidden');
            }

            function closeSidebar() {
                if (!sidebar || !backdrop) return;
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('opacity-0', 'pointer-events-none');
                backdrop.classList.remove('opacity-100');
                setTimeout(() => {
                    if (backdrop.classList.contains('opacity-0')) {
                        backdrop.classList.add('hidden');
                    }
                }, 300);
                document.body.classList.remove('overflow-hidden');
            }

            if (openBtn) openBtn.addEventListener('click', openSidebar);
            if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
            if (backdrop) backdrop.addEventListener('click', closeSidebar);

            // Handle ESC key to dismiss drawer
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && sidebar && !sidebar.classList.contains('-translate-x-full')) {
                    closeSidebar();
                }
            });
        });
    </script>
</body>
</html>
