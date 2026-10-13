<x-app-layout>
    <x-slot:header>
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Teaching Assignments</h1>
            <p class="text-xs text-slate-500 mt-0.5">Allocate teachers to specific subjects and class sections per academic session.</p>
        </div>
    </x-slot:header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Allocations Table (2 cols) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Filter Bar -->
            <x-card :padding="false">
                <form method="GET" action="{{ route('admin.teaching-assignments.index') }}" class="p-4 flex flex-wrap items-center gap-3 bg-slate-50 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <label for="academic_year_id" class="text-xs font-semibold text-slate-600">Session:</label>
                        <select name="academic_year_id" id="academic_year_id" onchange="this.form.submit()" class="text-xs rounded-lg border border-slate-300 px-3 py-1.5 bg-white text-slate-800 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                            @foreach ($academicYears as $year)
                                <option value="{{ $year->id }}" {{ (request('academic_year_id', $currentYear?->id) == $year->id) ? 'selected' : '' }}>
                                    {{ $year->name }} {{ $year->is_current ? '(Active)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 font-semibold uppercase tracking-wider">
                            <tr>
                                <th class="px-5 py-3">Teacher</th>
                                <th class="px-4 py-3">Class & Section</th>
                                <th class="px-4 py-3">Subject</th>
                                <th class="px-4 py-3 text-center">Session</th>
                                <th class="px-5 py-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse ($assignments as $assignment)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="px-5 py-3.5">
                                        <div class="font-bold text-slate-900">{{ $assignment->teacher->name }}</div>
                                        <span class="text-[11px] text-slate-400">{{ $assignment->teacher->email }}</span>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <span class="font-medium text-slate-800">
                                            {{ $assignment->classSection->fullName }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <span class="inline-flex items-center gap-1.5 font-semibold text-indigo-700">
                                            {{ $assignment->subject->name }}
                                            <span class="text-[10px] text-slate-400 font-mono font-normal">({{ $assignment->subject->code }})</span>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3.5 text-center">
                                        <x-badge variant="slate" size="sm">{{ $assignment->academicYear->name }}</x-badge>
                                    </td>
                                    <td class="px-5 py-3.5 text-right">
                                        <form method="POST" action="{{ route('admin.teaching-assignments.destroy', $assignment) }}" onsubmit="return confirm('Remove this teaching assignment?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-800 cursor-pointer">
                                                Remove
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-6">
                                        <x-empty-state title="No assignments found" description="Allocate teachers to classes and subjects using the form." />
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($assignments->hasPages())
                    <x-slot:footer>
                        {{ $assignments->links() }}
                    </x-slot:footer>
                @endif
            </x-card>
        </div>

        <!-- Allocate Form (1 col) -->
        <div>
            <x-card title="Allocate Teacher" description="Assign a teacher to a subject & section.">
                <form method="POST" action="{{ route('admin.teaching-assignments.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="form_academic_year" class="block text-xs font-semibold text-slate-700 mb-1">Academic Session *</label>
                        <select name="academic_year_id" id="form_academic_year" required class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            @foreach ($academicYears as $year)
                                <option value="{{ $year->id }}" {{ (old('academic_year_id', $currentYear?->id) == $year->id) ? 'selected' : '' }}>
                                    {{ $year->name }} {{ $year->is_current ? '(Current Active)' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('academic_year_id')<p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="teacher_id" class="block text-xs font-semibold text-slate-700 mb-1">Teacher *</label>
                        <select name="teacher_id" id="teacher_id" required class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">-- Choose Teacher --</option>
                            @foreach ($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->name }} ({{ $teacher->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('teacher_id')<p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="class_section_id" class="block text-xs font-semibold text-slate-700 mb-1">Class Section *</label>
                        <select name="class_section_id" id="class_section_id" required class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">-- Choose Section --</option>
                            @foreach ($sections as $sec)
                                <option value="{{ $sec->id }}" {{ old('class_section_id') == $sec->id ? 'selected' : '' }}>
                                    {{ $sec->fullName }}
                                </option>
                            @endforeach
                        </select>
                        @error('class_section_id')<p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="subject_id" class="block text-xs font-semibold text-slate-700 mb-1">Subject *</label>
                        <select name="subject_id" id="subject_id" required class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">-- Choose Subject --</option>
                            @foreach ($subjects as $subj)
                                <option value="{{ $subj->id }}" {{ old('subject_id') == $subj->id ? 'selected' : '' }}>
                                    {{ $subj->name }} ({{ $subj->code }})
                                </option>
                            @endforeach
                        </select>
                        @error('subject_id')<p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="pt-1">
                        <x-button type="submit" variant="primary" size="sm" class="w-full">
                            Save Allocation
                        </x-button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
