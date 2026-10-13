<x-app-layout>
    <x-slot:header>
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Executive Dashboard</h1>
            <p class="text-xs text-slate-500 mt-0.5">Welcome back, Administrator. Here is today's school overview and operations summary.</p>
        </div>
        <div class="flex items-center gap-2.5">
            <x-button variant="secondary" size="sm" href="{{ url('/dashboard#attendance') }}">
                <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Record Attendance
            </x-button>
            <x-button variant="primary" size="sm" href="{{ url('/dashboard#students') }}">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                New Admission
            </x-button>
        </div>
    </x-slot:header>

    <!-- Stat Metrics Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Total Students -->
        <x-stat-card
            title="Total Students"
            value="480"
            change="+12"
            changeType="positive"
            subtext="Enrolled across 14 sections"
            :isPlaceholder="true"
        >
            <x-slot:icon>
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                </svg>
            </x-slot:icon>
        </x-stat-card>

        <!-- Teaching Staff -->
        <x-stat-card
            title="Teachers & Staff"
            value="32"
            change="100%"
            changeType="neutral"
            subtext="Allocated to all subjects"
            :isPlaceholder="true"
        >
            <x-slot:icon>
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.636 50.636 0 00-2.658-.813A59.906 59.906 0 0112 3.493a59.903 59.903 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0112 13.489a50.702 50.702 0 017.74-3.342" />
                </svg>
            </x-slot:icon>
        </x-stat-card>

        <!-- Class Sections -->
        <x-stat-card
            title="Class Sections"
            value="14"
            change="Active"
            changeType="positive"
            subtext="Grades 1 through 10"
            :isPlaceholder="true"
        >
            <x-slot:icon>
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                </svg>
            </x-slot:icon>
        </x-stat-card>

        <!-- Today's Attendance -->
        <x-stat-card
            title="Attendance Rate"
            value="94.6%"
            change="+1.2%"
            changeType="positive"
            subtext="454 present today"
            :isPlaceholder="true"
        >
            <x-slot:icon>
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </x-slot:icon>
        </x-stat-card>

        <!-- Pending Fees -->
        <x-stat-card
            title="Fee Collection"
            value="$14,850"
            change="Pending"
            changeType="neutral"
            subtext="18 outstanding invoices"
            :isPlaceholder="true"
        >
            <x-slot:icon>
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                </svg>
            </x-slot:icon>
        </x-stat-card>
    </div>

    <!-- Main Content Area: Split 2-Column Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left 2 Columns -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Today's Attendance Overview Table Card -->
            <x-card
                title="Today's Section Attendance"
                description="Live session reporting across representative classes."
                :padding="false"
            >
                <x-slot:actions>
                    <x-badge variant="amber" size="sm">Sample Preview</x-badge>
                    <x-button variant="outline" size="xs" href="{{ url('/dashboard#attendance') }}">
                        View All
                    </x-button>
                </x-slot:actions>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 font-semibold uppercase tracking-wider">
                            <tr>
                                <th class="px-5 py-3">Class & Section</th>
                                <th class="px-4 py-3">Class Teacher</th>
                                <th class="px-4 py-3 text-center">Present / Total</th>
                                <th class="px-4 py-3 text-center">Rate</th>
                                <th class="px-5 py-3 text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-5 py-3.5 font-semibold text-slate-900">Grade 10 — Section A</td>
                                <td class="px-4 py-3.5">Sarah Jenkins</td>
                                <td class="px-4 py-3.5 text-center font-mono">38 / 40</td>
                                <td class="px-4 py-3.5 text-center font-semibold text-emerald-700">95.0%</td>
                                <td class="px-5 py-3.5 text-right">
                                    <x-badge variant="emerald" size="sm">Submitted</x-badge>
                                </td>
                            </tr>
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-5 py-3.5 font-semibold text-slate-900">Grade 10 — Section B</td>
                                <td class="px-4 py-3.5">Robert Chen</td>
                                <td class="px-4 py-3.5 text-center font-mono">36 / 38</td>
                                <td class="px-4 py-3.5 text-center font-semibold text-emerald-700">94.7%</td>
                                <td class="px-5 py-3.5 text-right">
                                    <x-badge variant="emerald" size="sm">Submitted</x-badge>
                                </td>
                            </tr>
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-5 py-3.5 font-semibold text-slate-900">Grade 9 — Section A</td>
                                <td class="px-4 py-3.5">Emily Davis</td>
                                <td class="px-4 py-3.5 text-center font-mono">34 / 36</td>
                                <td class="px-4 py-3.5 text-center font-semibold text-emerald-700">94.4%</td>
                                <td class="px-5 py-3.5 text-right">
                                    <x-badge variant="emerald" size="sm">Submitted</x-badge>
                                </td>
                            </tr>
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-5 py-3.5 font-semibold text-slate-900">Grade 8 — Section A</td>
                                <td class="px-4 py-3.5">Michael Morales</td>
                                <td class="px-4 py-3.5 text-center font-mono text-slate-400">—</td>
                                <td class="px-4 py-3.5 text-center text-slate-400">—</td>
                                <td class="px-5 py-3.5 text-right">
                                    <x-badge variant="amber" size="sm">Pending</x-badge>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <x-slot:footer>
                    <div class="flex items-center justify-between">
                        <span>Showing 4 of 14 active class sections</span>
                        <span class="text-slate-500 font-medium">Auto-refreshes daily</span>
                    </div>
                </x-slot:footer>
            </x-card>

            <!-- Upcoming Examinations Card -->
            <x-card
                title="Upcoming Examinations & Assessments"
                description="Scheduled academic tests for Academic Session 2026–2027."
            >
                <x-slot:actions>
                    <x-badge variant="sky" size="sm">Mid-Term Term 1</x-badge>
                </x-slot:actions>

                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3.5 rounded-lg border border-slate-100 bg-slate-50/50 hover:bg-white hover:border-slate-200 transition-all">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-lg bg-indigo-50 border border-indigo-100 flex flex-col items-center justify-center text-indigo-700">
                                <span class="text-[9px] font-bold uppercase leading-none">OCT</span>
                                <span class="text-sm font-extrabold leading-tight">20</span>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900">Mathematics — Final Paper 1</h4>
                                <p class="text-[11px] text-slate-500">Grade 10 &bull; Max Marks: 100.00 &bull; 09:00 AM - 12:00 PM</p>
                            </div>
                        </div>
                        <x-badge variant="indigo" size="sm">Draft Schedule</x-badge>
                    </div>

                    <div class="flex items-center justify-between p-3.5 rounded-lg border border-slate-100 bg-slate-50/50 hover:bg-white hover:border-slate-200 transition-all">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-lg bg-indigo-50 border border-indigo-100 flex flex-col items-center justify-center text-indigo-700">
                                <span class="text-[9px] font-bold uppercase leading-none">OCT</span>
                                <span class="text-sm font-extrabold leading-tight">22</span>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900">Science & Laboratory Practical</h4>
                                <p class="text-[11px] text-slate-500">Grade 10 &bull; Max Marks: 75.00 &bull; 10:00 AM - 12:30 PM</p>
                            </div>
                        </div>
                        <x-badge variant="indigo" size="sm">Draft Schedule</x-badge>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Right 1 Column -->
        <div class="space-y-6">
            <!-- Quick Actions Card -->
            <x-card title="Quick Operations" description="Frequently used administrative workflows.">
                <div class="grid grid-cols-2 gap-2.5">
                    <a href="{{ url('/dashboard#students') }}" class="p-3 rounded-lg border border-slate-200 hover:border-indigo-300 hover:bg-indigo-50/40 text-left transition-all group">
                        <div class="h-8 w-8 rounded-md bg-indigo-50 text-indigo-600 flex items-center justify-center mb-2 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.765z" />
                            </svg>
                        </div>
                        <span class="block text-xs font-semibold text-slate-900 leading-tight">Admit Student</span>
                        <span class="block text-[10px] text-slate-500 mt-0.5">Enroll new record</span>
                    </a>

                    <a href="{{ url('/dashboard#attendance') }}" class="p-3 rounded-lg border border-slate-200 hover:border-indigo-300 hover:bg-indigo-50/40 text-left transition-all group">
                        <div class="h-8 w-8 rounded-md bg-emerald-50 text-emerald-600 flex items-center justify-center mb-2 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span class="block text-xs font-semibold text-slate-900 leading-tight">Attendance</span>
                        <span class="block text-[10px] text-slate-500 mt-0.5">Bulk roster entry</span>
                    </a>

                    <a href="{{ url('/dashboard#fees') }}" class="p-3 rounded-lg border border-slate-200 hover:border-indigo-300 hover:bg-indigo-50/40 text-left transition-all group">
                        <div class="h-8 w-8 rounded-md bg-amber-50 text-amber-600 flex items-center justify-center mb-2 group-hover:bg-amber-600 group-hover:text-white transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span class="block text-xs font-semibold text-slate-900 leading-tight">Create Invoice</span>
                        <span class="block text-[10px] text-slate-500 mt-0.5">Fee billing item</span>
                    </a>

                    <a href="{{ url('/dashboard#announcements') }}" class="p-3 rounded-lg border border-slate-200 hover:border-indigo-300 hover:bg-indigo-50/40 text-left transition-all group">
                        <div class="h-8 w-8 rounded-md bg-purple-50 text-purple-600 flex items-center justify-center mb-2 group-hover:bg-purple-600 group-hover:text-white transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.114-1.528-.45a19.78 19.78 0 01-1.115-3.07m2.778-1.154c.22-.018.441-.039.662-.061m0 0a41.42 41.42 0 005.418-.832M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <span class="block text-xs font-semibold text-slate-900 leading-tight">Publish Notice</span>
                        <span class="block text-[10px] text-slate-500 mt-0.5">Broadcast bulletin</span>
                    </a>
                </div>
            </x-card>

            <!-- Announcements Bulletin -->
            <x-card title="School Notices" description="Published school bulletins.">
                <x-slot:actions>
                    <x-badge variant="emerald" size="sm">Active</x-badge>
                </x-slot:actions>

                <div class="space-y-3.5">
                    <div class="p-3 rounded-lg bg-slate-50 border border-slate-100 space-y-1">
                        <div class="flex items-center justify-between">
                            <x-badge variant="indigo" size="sm">All School</x-badge>
                            <span class="text-[10px] text-slate-400">12 Oct 2026</span>
                        </div>
                        <h5 class="text-xs font-semibold text-slate-900">Term 1 Parent-Teacher Conferences</h5>
                        <p class="text-[11px] text-slate-600 line-clamp-2">Scheduled for next Friday. Timetable slots will be made available through the Guardian portal.</p>
                    </div>

                    <div class="p-3 rounded-lg bg-slate-50 border border-slate-100 space-y-1">
                        <div class="flex items-center justify-between">
                            <x-badge variant="sky" size="sm">Teachers</x-badge>
                            <span class="text-[10px] text-slate-400">08 Oct 2026</span>
                        </div>
                        <h5 class="text-xs font-semibold text-slate-900">Mid-Term Assessment Marks Deadline</h5>
                        <p class="text-[11px] text-slate-600 line-clamp-2">All teachers must finalize and review submitted practical marks before October 25th.</p>
                    </div>
                </div>
            </x-card>

            <!-- Environment Diagnostics Card -->
            <div class="p-4 rounded-xl bg-slate-900 text-slate-300 text-xs space-y-2 border border-slate-800">
                <div class="flex items-center justify-between font-semibold text-white">
                    <span class="flex items-center gap-1.5">
                        <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                        Environment Status
                    </span>
                    <span class="text-[11px] font-mono text-slate-400">Healthy</span>
                </div>
                <div class="space-y-1 text-[11px] text-slate-400 pt-1 border-t border-slate-800">
                    <div class="flex justify-between"><span>Framework:</span><span class="text-slate-200 font-medium">Laravel 12.69</span></div>
                    <div class="flex justify-between"><span>PHP Runtime:</span><span class="text-slate-200 font-medium">PHP 8.2.12</span></div>
                    <div class="flex justify-between"><span>Database:</span><span class="text-slate-200 font-medium">MySQL (schoolsphere)</span></div>
                    <div class="flex justify-between"><span>Assets:</span><span class="text-slate-200 font-medium">Tailwind CSS v4 &bull; Vite 7</span></div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
