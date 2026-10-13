<header class="h-16 bg-white border-b border-slate-200/80 sticky top-0 z-30 px-4 sm:px-6 flex items-center justify-between">
    <!-- Left: Mobile Sidebar Trigger & Breadcrumb Context -->
    <div class="flex items-center gap-3">
        <button id="open-sidebar-button" type="button" class="lg:hidden p-2 text-slate-500 hover:text-slate-800 rounded-lg hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500" aria-label="Open Sidebar">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </button>

        <div class="flex items-center gap-2">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider hidden sm:inline">SchoolSphere</span>
            <span class="text-slate-300 hidden sm:inline">/</span>
            <span class="text-xs font-semibold text-slate-700">Administration Portal</span>
        </div>
    </div>

    <!-- Right: Context Badges & User Actions -->
    <div class="flex items-center gap-3 sm:gap-4">
        <!-- Academic Session Indicator -->
        <div class="hidden md:flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-50 border border-indigo-100 text-xs text-indigo-700 font-medium">
            <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
            </svg>
            <span>Session: <strong>2026–2027</strong></span>
        </div>

        <!-- Quick Notifications Icon -->
        <button type="button" class="relative p-2 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer" title="Notifications">
            <span class="sr-only">View notifications</span>
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
            </svg>
            <span class="absolute top-1.5 right-1.5 h-2 w-2 rounded-full bg-indigo-600 ring-2 ring-white"></span>
        </button>

        <div class="h-6 w-px bg-slate-200"></div>

        <!-- User Profile Dropdown Trigger -->
        <div class="relative flex items-center gap-3">
            <div class="h-8 w-8 rounded-full bg-slate-800 text-white flex items-center justify-center font-bold text-xs ring-2 ring-indigo-500/20">
                AD
            </div>
            <div class="hidden sm:block text-left">
                <span class="block text-xs font-semibold text-slate-900 leading-tight">Administrator</span>
                <span class="block text-[11px] text-slate-500 leading-tight">Super Admin</span>
            </div>
            <a href="{{ url('/') }}" class="text-xs font-medium text-slate-500 hover:text-rose-600 hover:underline transition-colors ml-1" title="Exit to Landing">
                Logout
            </a>
        </div>
    </div>
</header>
