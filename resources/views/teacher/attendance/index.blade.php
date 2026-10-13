@extends('layouts.app')

@section('title', 'My Classes Attendance — SchoolSphere')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Class Attendance</h1>
                @if($currentYear)
                    <x-badge color="indigo">{{ $currentYear->name }}</x-badge>
                @endif
            </div>
            <p class="mt-1 text-sm text-slate-500">
                Record and manage attendance for your assigned class sections.
            </p>
        </div>
        <div>
            <a href="{{ route('teacher.attendance.create') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-xs transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Take Attendance</span>
            </a>
        </div>
    </div>

    <!-- Sessions Table -->
    <x-card class="overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-slate-600 font-medium text-xs uppercase tracking-wider">
                        <th class="py-3 px-4">Date</th>
                        <th class="py-3 px-4">Class Section</th>
                        <th class="py-3 px-4">Attendance Counts</th>
                        <th class="py-3 px-4">Rate</th>
                        <th class="py-3 px-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($sessions as $session)
                        @php
                            $present = $session->attendances->where('status', 'present')->count();
                            $absent = $session->attendances->where('status', 'absent')->count();
                            $late = $session->attendances->where('status', 'late')->count();
                            $total = $session->attendances->count();
                            $rate = $total > 0 ? round((($present + $late) / $total) * 100, 1) : 0;
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3.5 px-4 font-semibold text-slate-900">
                                {{ $session->attendance_date->format('M d, Y') }}
                            </td>
                            <td class="py-3.5 px-4 font-medium">
                                {{ $session->classSection->full_name }}
                            </td>
                            <td class="py-3.5 px-4 text-xs">
                                <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 font-semibold">{{ $present }} Present</span>
                                <span class="px-2 py-0.5 rounded bg-rose-50 text-rose-700 font-semibold ml-1">{{ $absent }} Absent</span>
                                @if($late > 0)
                                    <span class="px-2 py-0.5 rounded bg-amber-50 text-amber-700 font-semibold ml-1">{{ $late }} Late</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-xs text-slate-900">
                                {{ $rate }}%
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <a href="{{ route('teacher.attendance.create', ['class_section_id' => $session->class_section_id, 'date' => $session->attendance_date->format('Y-m-d')]) }}" class="px-2.5 py-1 text-xs font-medium text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 rounded transition-colors">
                                    Update Sheet
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center">
                                <x-empty-state
                                    title="No Sessions Recorded Yet"
                                    description="You haven't recorded attendance for your assigned classes yet. Click 'Take Attendance' to begin."
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($sessions->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $sessions->links() }}
            </div>
        @endif
    </x-card>
</div>
@endsection
