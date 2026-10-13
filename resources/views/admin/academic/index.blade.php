<x-app-layout>
    <x-slot:header>
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Academic Years & Sessions</h1>
            <p class="text-xs text-slate-500 mt-0.5">Manage school calendar cycles, active academic sessions, and enrollment terms.</p>
        </div>
    </x-slot:header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Academic Sessions Table (2 cols) -->
        <div class="lg:col-span-2">
            <x-card title="Configured Academic Sessions" description="School academic terms and active enrollment years." :padding="false">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 font-semibold uppercase tracking-wider">
                            <tr>
                                <th class="px-5 py-3">Session Name</th>
                                <th class="px-4 py-3">Dates</th>
                                <th class="px-4 py-3 text-center">Status</th>
                                <th class="px-4 py-3 text-center">Allocations</th>
                                <th class="px-5 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse ($academicYears as $year)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-slate-900">{{ $year->name }}</span>
                                            @if ($year->is_current)
                                                <x-badge variant="emerald" size="sm">Current Active</x-badge>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3.5 text-slate-600 font-mono text-[11px]">
                                        {{ $year->start_date->format('M Y') }} &rarr; {{ $year->end_date->format('M Y') }}
                                    </td>
                                    <td class="px-4 py-3.5 text-center">
                                        @if ($year->status === 'active')
                                            <x-badge variant="emerald" size="sm">Active</x-badge>
                                        @elseif ($year->status === 'upcoming')
                                            <x-badge variant="sky" size="sm">Upcoming</x-badge>
                                        @else
                                            <x-badge variant="slate" size="sm">Closed</x-badge>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 text-center text-slate-500 text-[11px]">
                                        {{ $year->teaching_assignments_count }} Staff &bull; {{ $year->enrollments_count }} Students
                                    </td>
                                    <td class="px-5 py-3.5 text-right">
                                        @if (!$year->is_current)
                                            <form method="POST" action="{{ route('admin.academic-years.set-current', $year) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 cursor-pointer">
                                                    Set as Active
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-[11px] text-slate-400 font-medium">Default</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-5 text-center text-slate-500">
                                        <x-empty-state title="No academic years" description="Create your first academic year below to begin organizing classes." />
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($academicYears->hasPages())
                    <x-slot:footer>
                        {{ $academicYears->links() }}
                    </x-slot:footer>
                @endif
            </x-card>
        </div>

        <!-- Create Academic Year Form (1 col) -->
        <div>
            <x-card title="Add Academic Session" description="Register a new academic year.">
                <form method="POST" action="{{ route('admin.academic-years.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">Session Name *</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="e.g. 2026–2027" required class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                        @error('name')<p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="start_date" class="block text-xs font-semibold text-slate-700 mb-1">Start Date *</label>
                            <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}" required class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                            @error('start_date')<p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="end_date" class="block text-xs font-semibold text-slate-700 mb-1">End Date *</label>
                            <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}" required class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                            @error('end_date')<p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div>
                        <label for="status" class="block text-xs font-semibold text-slate-700 mb-1">Status *</label>
                        <select name="status" id="status" required class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="upcoming" {{ old('status') === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                            <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="closed" {{ old('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                        @error('status')<p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <input type="checkbox" name="is_current" id="is_current" value="1" {{ old('is_current') ? 'checked' : '' }} class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" />
                        <label for="is_current" class="text-xs text-slate-700 select-none">Set as current active school session</label>
                    </div>

                    <div class="pt-2">
                        <x-button type="submit" variant="primary" size="sm" class="w-full">
                            Save Academic Session
                        </x-button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
