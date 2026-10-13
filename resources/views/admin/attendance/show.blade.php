@extends('layouts.app')

@section('title', "Attendance Sheet — {$attendanceSession->classSection->full_name} ({$attendanceSession->attendance_date->format('M d, Y')}) — SchoolSphere")

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Attendance Sheet</h1>
                <x-badge color="indigo">{{ $attendanceSession->attendance_date->format('l, M d, Y') }}</x-badge>
            </div>
            <p class="mt-1 text-sm text-slate-500">
                {{ $attendanceSession->classSection->full_name }} &bull; Recorded by {{ $attendanceSession->recorder->name ?? 'System' }}
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.attendance.index') }}" class="px-3 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                &larr; Registry
            </a>
            <a href="{{ route('admin.attendance.create', ['class_section_id' => $attendanceSession->class_section_id, 'date' => $attendanceSession->attendance_date->format('Y-m-d')]) }}" class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-xs transition-colors">
                Edit Sheet
            </a>
        </div>
    </div>

    @php
        $present = $attendanceSession->attendances->where('status', 'present')->count();
        $absent = $attendanceSession->attendances->where('status', 'absent')->count();
        $late = $attendanceSession->attendances->where('status', 'late')->count();
        $excused = $attendanceSession->attendances->where('status', 'excused')->count();
        $total = $attendanceSession->attendances->count();
        $rate = $total > 0 ? round((($present + $late) / $total) * 100, 1) : 0;
    @endphp

    <!-- Summary Metrics -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
        <div class="p-4 bg-white border border-slate-200 rounded-xl">
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Enrolled</div>
            <div class="text-2xl font-bold text-slate-900 mt-1">{{ $total }}</div>
        </div>
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl">
            <div class="text-xs font-semibold text-emerald-700 uppercase tracking-wider">Present</div>
            <div class="text-2xl font-bold text-emerald-800 mt-1">{{ $present }}</div>
        </div>
        <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl">
            <div class="text-xs font-semibold text-rose-700 uppercase tracking-wider">Absent</div>
            <div class="text-2xl font-bold text-rose-800 mt-1">{{ $absent }}</div>
        </div>
        <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl">
            <div class="text-xs font-semibold text-amber-700 uppercase tracking-wider">Late</div>
            <div class="text-2xl font-bold text-amber-800 mt-1">{{ $late }}</div>
        </div>
        <div class="p-4 bg-indigo-50 border border-indigo-200 rounded-xl col-span-2 sm:col-span-1">
            <div class="text-xs font-semibold text-indigo-700 uppercase tracking-wider">Attendance Rate</div>
            <div class="text-2xl font-bold text-indigo-900 mt-1">{{ $rate }}%</div>
        </div>
    </div>

    @if($attendanceSession->remarks)
        <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700">
            <span class="font-bold text-slate-900">Session Remarks:</span> {{ $attendanceSession->remarks }}
        </div>
    @endif

    <!-- Student Attendance List -->
    <x-card class="overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-slate-600 font-medium text-xs uppercase tracking-wider">
                        <th class="py-3 px-4 w-12 text-center">#</th>
                        <th class="py-3 px-4">Student</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4">Remarks</th>
                        <th class="py-3 px-4 text-right">Profile</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @foreach($attendanceSession->attendances as $index => $att)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3 px-4 text-center font-mono text-xs text-slate-400">
                                {{ $index + 1 }}
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-semibold text-slate-900">{{ $att->student->full_name }}</div>
                                <div class="text-xs text-slate-400 font-mono">{{ $att->student->admission_number }}</div>
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($att->status === 'present')
                                    <x-badge color="emerald">Present</x-badge>
                                @elseif($att->status === 'absent')
                                    <x-badge color="rose">Absent</x-badge>
                                @elseif($att->status === 'late')
                                    <x-badge color="amber">Late</x-badge>
                                @else
                                    <x-badge color="blue">Excused</x-badge>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-xs text-slate-500">
                                {{ $att->remarks ?? '—' }}
                            </td>
                            <td class="py-3 px-4 text-right">
                                <a href="{{ route('admin.students.attendance', $att->student) }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-800">
                                    History &rarr;
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>
</div>
@endsection
