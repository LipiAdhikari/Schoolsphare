<x-app-layout>
    <x-slot:header>
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Student Enrollments</h1>
            <p class="text-xs text-slate-500 mt-0.5">Manage academic year rosters, class assignments, and historical enrollment records.</p>
        </div>
    </x-slot:header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Enrollments Table (2 cols) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Filter Bar -->
            <x-card :padding="false">
                <form method="GET" action="{{ route('admin.enrollments.index') }}" class="p-4 flex flex-wrap items-center gap-3 bg-slate-50 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <label for="filter_year" class="text-xs font-semibold text-slate-600">Session:</label>
                        <select name="academic_year_id" id="filter_year" onchange="this.form.submit()" class="text-xs rounded-lg border border-slate-300 px-3 py-1.5 bg-white text-slate-800 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                            @foreach ($academicYears as $year)
                                <option value="{{ $year->id }}" {{ (request('academic_year_id', $currentYear?->id) == $year->id) ? 'selected' : '' }}>
                                    {{ $year->name }} {{ $year->is_current ? '(Active)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <label for="filter_section" class="text-xs font-semibold text-slate-600">Section:</label>
                        <select name="class_section_id" id="filter_section" onchange="this.form.submit()" class="text-xs rounded-lg border border-slate-300 px-3 py-1.5 bg-white text-slate-800 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                            <option value="">-- All Sections --</option>
                            @foreach ($sections as $sec)
                                <option value="{{ $sec->id }}" {{ request('class_section_id') == $sec->id ? 'selected' : '' }}>
                                    {{ $sec->fullName }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center gap-2 ml-auto">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search student name..." class="text-xs rounded-lg border border-slate-300 px-3 py-1.5 bg-white text-slate-800 focus:outline-none focus:ring-1 focus:ring-indigo-500 w-44" />
                        <x-button type="submit" variant="secondary" size="xs">Search</x-button>
                    </div>
                </form>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 font-semibold uppercase tracking-wider">
                            <tr>
                                <th class="px-5 py-3">Student</th>
                                <th class="px-4 py-3">Class & Section</th>
                                <th class="px-4 py-3 text-center">Roll No</th>
                                <th class="px-4 py-3 text-center">Session</th>
                                <th class="px-5 py-3 text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse ($enrollments as $enrollment)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="px-5 py-3.5">
                                        <div class="font-bold text-slate-900">{{ $enrollment->student->name }}</div>
                                        <span class="text-[11px] text-slate-400">{{ $enrollment->student->email }}</span>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <span class="font-medium text-slate-800">
                                            {{ $enrollment->classSection->fullName }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3.5 text-center font-mono text-[11px] text-slate-600">
                                        {{ $enrollment->roll_number ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3.5 text-center">
                                        <x-badge variant="slate" size="sm">{{ $enrollment->academicYear->name }}</x-badge>
                                    </td>
                                    <td class="px-5 py-3.5 text-right">
                                        @if ($enrollment->status === 'enrolled')
                                            <x-badge variant="emerald" size="sm">Enrolled</x-badge>
                                        @elseif ($enrollment->status === 'promoted')
                                            <x-badge variant="indigo" size="sm">Promoted</x-badge>
                                        @elseif ($enrollment->status === 'transferred')
                                            <x-badge variant="amber" size="sm">Transferred</x-badge>
                                        @else
                                            <x-badge variant="slate" size="sm">{{ ucfirst($enrollment->status) }}</x-badge>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-6">
                                        <x-empty-state title="No student enrollments found" description="Enroll students into class sections using the form." />
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($enrollments->hasPages())
                    <x-slot:footer>
                        {{ $enrollments->links() }}
                    </x-slot:footer>
                @endif
            </x-card>
        </div>

        <!-- Enroll Form (1 col) -->
        <div>
            <x-card title="Enroll Student" description="Assign student to section for this term.">
                <form method="POST" action="{{ route('admin.enrollments.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="enroll_academic_year" class="block text-xs font-semibold text-slate-700 mb-1">Academic Session *</label>
                        <select name="academic_year_id" id="enroll_academic_year" required class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            @foreach ($academicYears as $year)
                                <option value="{{ $year->id }}" {{ (old('academic_year_id', $currentYear?->id) == $year->id) ? 'selected' : '' }}>
                                    {{ $year->name }} {{ $year->is_current ? '(Current Active)' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('academic_year_id')<p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="student_id" class="block text-xs font-semibold text-slate-700 mb-1">Student Account *</label>
                        <select name="student_id" id="student_id" required class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">-- Choose Student --</option>
                            @foreach ($students as $student)
                                <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                    {{ $student->name }} ({{ $student->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('student_id')<p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="enroll_section_id" class="block text-xs font-semibold text-slate-700 mb-1">Class Section *</label>
                        <select name="class_section_id" id="enroll_section_id" required class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">-- Choose Section --</option>
                            @foreach ($sections as $sec)
                                <option value="{{ $sec->id }}" {{ old('class_section_id') == $sec->id ? 'selected' : '' }}>
                                    {{ $sec->fullName }}
                                </option>
                            @endforeach
                        </select>
                        @error('class_section_id')<p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="roll_number" class="block text-xs font-semibold text-slate-700 mb-1">Roll Number</label>
                            <input type="text" name="roll_number" id="roll_number" value="{{ old('roll_number') }}" placeholder="e.g. 101" class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono" />
                            @error('roll_number')<p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="enroll_status" class="block text-xs font-semibold text-slate-700 mb-1">Status *</label>
                            <select name="status" id="enroll_status" required class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="enrolled" selected>Enrolled</option>
                                <option value="promoted">Promoted</option>
                                <option value="repeated">Repeated</option>
                                <option value="transferred">Transferred</option>
                                <option value="withdrawn">Withdrawn</option>
                            </select>
                            @error('status')<p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="pt-1">
                        <x-button type="submit" variant="primary" size="sm" class="w-full">
                            Enroll Student
                        </x-button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
