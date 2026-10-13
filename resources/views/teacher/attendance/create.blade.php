@extends('layouts.app')

@section('title', 'Take Class Attendance — SchoolSphere')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Take Class Attendance</h1>
            <p class="mt-1 text-sm text-slate-500">
                Mark attendance for students enrolled in your assigned sections.
            </p>
        </div>
        <div>
            <a href="{{ route('teacher.attendance.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                &larr; Attendance List
            </a>
        </div>
    </div>

    <!-- Step 1: Assigned Section Selector -->
    <x-card class="p-5">
        <form method="GET" action="{{ route('teacher.attendance.create') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
            <div>
                <label for="class_section_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                    Your Assigned Class Section <span class="text-rose-500">*</span>
                </label>
                <select name="class_section_id" id="class_section_id" required
                    class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Choose Assigned Section...</option>
                    @foreach($sections as $sec)
                        <option value="{{ $sec->id }}" {{ ($selectedSection?->id ?? '') == $sec->id ? 'selected' : '' }}>
                            {{ $sec->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="date" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                    Date <span class="text-rose-500">*</span>
                </label>
                <input type="date" name="date" id="date" value="{{ $date }}" max="{{ date('Y-m-d') }}" required
                    class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <button type="submit" class="w-full px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-xs transition-colors">
                    Load Student Roster &rarr;
                </button>
            </div>
        </form>
    </x-card>

    <!-- Step 2: Roster Sheet -->
    @if($selectedSection)
        @if($existingSession)
            <div class="p-4 bg-amber-50 border border-amber-200 rounded-lg text-amber-800 text-sm flex items-center justify-between">
                <span>
                    <strong>Notice:</strong> Attendance for this section on <strong>{{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</strong> was already recorded. Submitting this form will update the existing session.
                </span>
                <span class="text-xs bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full font-semibold">Editing Existing</span>
            </div>
        @endif

        @if($students->count() > 0)
            <form method="POST" action="{{ route('teacher.attendance.store') }}" class="space-y-6">
                @csrf
                <input type="hidden" name="class_section_id" value="{{ $selectedSection->id }}">
                <input type="hidden" name="attendance_date" value="{{ $date }}">

                <x-card class="overflow-hidden p-0">
                    <div class="p-4 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <h2 class="text-base font-bold text-slate-900">{{ $selectedSection->full_name }}</h2>
                            <p class="text-xs text-slate-500">{{ $students->count() }} enrolled students</p>
                        </div>

                        <div class="flex items-center gap-2">
                            <button type="button" onclick="setAllStatuses('present')" class="px-3 py-1.5 text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-lg border border-emerald-200 transition-colors">
                                &check; Mark All Present
                            </button>
                            <button type="button" onclick="setAllStatuses('absent')" class="px-3 py-1.5 text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-lg border border-rose-200 transition-colors">
                                &cross; Mark All Absent
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead>
                                <tr class="border-b border-slate-200 bg-white text-slate-600 font-medium text-xs uppercase tracking-wider">
                                    <th class="py-3 px-4 w-12 text-center">#</th>
                                    <th class="py-3 px-4">Student</th>
                                    <th class="py-3 px-4 text-center">Status</th>
                                    <th class="py-3 px-4">Remarks</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                @foreach($students as $index => $student)
                                    @php
                                        $existingRecord = $existingSession?->attendances->firstWhere('student_id', $student->id);
                                        $currentStatus = old("attendances.{$index}.status", $existingRecord?->status ?? 'present');
                                        $currentRemark = old("attendances.{$index}.remarks", $existingRecord?->remarks ?? '');
                                    @endphp
                                    <tr class="hover:bg-slate-50/70 transition-colors">
                                        <td class="py-3 px-4 text-center text-xs font-mono text-slate-400">
                                            {{ $index + 1 }}
                                            <input type="hidden" name="attendances[{{ $index }}][student_id]" value="{{ $student->id }}">
                                        </td>
                                        <td class="py-3 px-4">
                                            <div class="font-semibold text-slate-900">{{ $student->full_name }}</div>
                                            <div class="text-xs text-slate-400 font-mono">{{ $student->admission_number }}</div>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <div class="inline-flex rounded-lg p-0.5 bg-slate-100 border border-slate-200 text-xs">
                                                @foreach(['present' => 'P', 'absent' => 'A', 'late' => 'L', 'excused' => 'E'] as $stKey => $stLabel)
                                                    <label class="cursor-pointer">
                                                        <input type="radio" name="attendances[{{ $index }}][status]" value="{{ $stKey }}"
                                                            class="sr-only peer status-radio-{{ $stKey }}" {{ $currentStatus === $stKey ? 'checked' : '' }}>
                                                        <span class="inline-block px-3 py-1 rounded-md font-bold transition-all
                                                            peer-checked:shadow-xs
                                                            {{ $stKey === 'present' ? 'peer-checked:bg-emerald-600 peer-checked:text-white text-slate-600' : '' }}
                                                            {{ $stKey === 'absent' ? 'peer-checked:bg-rose-600 peer-checked:text-white text-slate-600' : '' }}
                                                            {{ $stKey === 'late' ? 'peer-checked:bg-amber-500 peer-checked:text-white text-slate-600' : '' }}
                                                            {{ $stKey === 'excused' ? 'peer-checked:bg-blue-600 peer-checked:text-white text-slate-600' : '' }}">
                                                            {{ $stLabel }}
                                                        </span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="py-3 px-4">
                                            <input type="text" name="attendances[{{ $index }}][remarks]" value="{{ $currentRemark }}"
                                                placeholder="Remarks..."
                                                class="w-full px-2.5 py-1 text-xs border border-slate-200 rounded-md focus:ring-1 focus:ring-indigo-500">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="p-4 bg-slate-50 border-t border-slate-200 space-y-4">
                        <div>
                            <label for="remarks" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                                Session Remarks (Optional)
                            </label>
                            <textarea name="remarks" id="remarks" rows="2" placeholder="Class notes or notices..."
                                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">{{ old('remarks', $existingSession?->remarks ?? '') }}</textarea>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-2">
                            <a href="{{ route('teacher.attendance.index') }}" class="px-4 py-2 text-sm font-medium text-slate-700 hover:text-slate-900 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                                Cancel
                            </a>
                            <button type="submit" class="px-6 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-xs transition-colors">
                                Submit Attendance
                            </button>
                        </div>
                    </div>
                </x-card>
            </form>

            <script>
                function setAllStatuses(status) {
                    document.querySelectorAll('.status-radio-' + status).forEach(radio => {
                        radio.checked = true;
                    });
                }
            </script>
        @else
            <x-card class="p-8 text-center">
                <x-empty-state
                    title="No Students Found"
                    description="No students are enrolled in this section for the current session."
                />
            </x-card>
        @endif
    @endif
</div>
@endsection
