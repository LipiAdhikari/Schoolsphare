@extends('layouts.app')

@section('title', "Attendance History — {$student->full_name} — SchoolSphere")

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $student->full_name }}</h1>
                <x-badge color="indigo">Attendance Transcript</x-badge>
            </div>
            <p class="mt-1 text-sm text-slate-500 font-mono">
                Admission No: {{ $student->admission_number }}
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.students.show', $student) }}" class="px-3 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                &larr; Student Profile
            </a>
            <a href="{{ route('admin.attendance.index') }}" class="px-3 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                Registry
            </a>
        </div>
    </div>

    <!-- Summary Metrics -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
        <div class="p-4 bg-white border border-slate-200 rounded-xl">
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Sessions Recorded</div>
            <div class="text-2xl font-bold text-slate-900 mt-1">{{ $stats['total'] }}</div>
        </div>
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl">
            <div class="text-xs font-semibold text-emerald-700 uppercase tracking-wider">Present</div>
            <div class="text-2xl font-bold text-emerald-800 mt-1">{{ $stats['present'] }}</div>
        </div>
        <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl">
            <div class="text-xs font-semibold text-rose-700 uppercase tracking-wider">Absent</div>
            <div class="text-2xl font-bold text-rose-800 mt-1">{{ $stats['absent'] }}</div>
        </div>
        <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl">
            <div class="text-xs font-semibold text-amber-700 uppercase tracking-wider">Late</div>
            <div class="text-2xl font-bold text-amber-800 mt-1">{{ $stats['late'] }}</div>
        </div>
        <div class="p-4 bg-indigo-50 border border-indigo-200 rounded-xl col-span-2 sm:col-span-1">
            <div class="text-xs font-semibold text-indigo-700 uppercase tracking-wider">Attendance Rate</div>
            <div class="text-2xl font-bold text-indigo-900 mt-1">{{ $stats['rate'] }}%</div>
        </div>
    </div>

    <!-- Historical Attendance Table -->
    <x-card class="overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-slate-600 font-medium text-xs uppercase tracking-wider">
                        <th class="py-3 px-4">Date</th>
                        <th class="py-3 px-4">Class Section</th>
                        <th class="py-3 px-4">Type / Subject</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4">Remarks</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($attendances as $att)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3 px-4 font-semibold text-slate-900">
                                {{ $att->session->attendance_date->format('M d, Y') }}
                            </td>
                            <td class="py-3 px-4">
                                {{ $att->session->classSection->full_name }}
                            </td>
                            <td class="py-3 px-4 text-xs text-slate-600 capitalize">
                                {{ $att->session->type }} {{ $att->session->subject ? '— ' . $att->session->subject->name : '' }}
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
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-400 italic">
                                No attendance records recorded for this student yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($attendances->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $attendances->links() }}
            </div>
        @endif
    </x-card>
</div>
@endsection
