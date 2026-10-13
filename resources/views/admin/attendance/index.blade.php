@extends('layouts.app')

@section('title', 'Attendance Management — SchoolSphere')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Attendance Registry</h1>
                @if($currentYear)
                    <x-badge color="indigo">{{ $currentYear->name }}</x-badge>
                @endif
            </div>
            <p class="mt-1 text-sm text-slate-500">
                Track daily and subject-wise student attendance, review class averages, and detect absences.
            </p>
        </div>
        <div>
            <a href="{{ route('admin.attendance.create') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-xs transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Record Attendance</span>
            </a>
        </div>
    </div>

    <!-- Today's Attendance Overview Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-stat-card
            title="Today's Sessions"
            value="{{ $stats['sessions_count'] }}"
            icon="document-check"
            change="Recorded today"
            trend="neutral"
        />
        <x-stat-card
            title="Present Today"
            value="{{ $stats['present_count'] }}"
            icon="users"
            change="Students in class"
            trend="up"
        />
        <x-stat-card
            title="Absences Today"
            value="{{ $stats['absent_count'] }}"
            icon="clock"
            change="Unexcused / excused"
            trend="{{ $stats['absent_count'] > 0 ? 'down' : 'neutral' }}"
        />
        <x-stat-card
            title="Attendance Rate"
            value="{{ $stats['rate'] !== null ? $stats['rate'].'%' : '—' }}"
            icon="chart"
            change="Overall daily rate"
            trend="up"
        />
    </div>

    <!-- Filter Bar -->
    <x-card class="p-4">
        <form method="GET" action="{{ route('admin.attendance.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
            <div>
                <label for="date" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">Date</label>
                <input type="date" name="date" id="date" value="{{ request('date') }}"
                    class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label for="class_section_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">Class Section</label>
                <select name="class_section_id" id="class_section_id"
                    class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Class Sections</option>
                    @foreach($classes as $c)
                        <optgroup label="{{ $c->name }}">
                            @foreach($c->sections as $sec)
                                <option value="{{ $sec->id }}" {{ request('class_section_id') == $sec->id ? 'selected' : '' }}>
                                    {{ $c->name }} — {{ $sec->name }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 px-4 py-2 text-sm font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                    Filter
                </button>
                @if(request()->hasAny(['date', 'class_section_id']))
                    <a href="{{ route('admin.attendance.index') }}" class="px-3 py-2 text-sm font-medium text-slate-500 hover:text-slate-700">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </x-card>

    <!-- Sessions Table -->
    <x-card class="overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-slate-600 font-medium text-xs uppercase tracking-wider">
                        <th class="py-3 px-4">Date & Session</th>
                        <th class="py-3 px-4">Class Section</th>
                        <th class="py-3 px-4">Breakdown</th>
                        <th class="py-3 px-4">Attendance Rate</th>
                        <th class="py-3 px-4">Recorded By</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($sessions as $session)
                        @php
                            $present = $session->attendances->where('status', 'present')->count();
                            $absent = $session->attendances->where('status', 'absent')->count();
                            $late = $session->attendances->where('status', 'late')->count();
                            $excused = $session->attendances->where('status', 'excused')->count();
                            $total = $session->attendances->count();
                            $rate = $total > 0 ? round((($present + $late) / $total) * 100, 1) : 0;
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="font-semibold text-slate-900">
                                    {{ $session->attendance_date->format('M d, Y') }}
                                </div>
                                <div class="text-xs text-slate-500 capitalize">
                                    {{ $session->type }} {{ $session->subject ? '— ' . $session->subject->name : '' }}
                                </div>
                            </td>
                            <td class="py-3.5 px-4 font-medium text-slate-900">
                                {{ $session->classSection->full_name }}
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-2 text-xs">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded font-medium bg-emerald-50 text-emerald-700">
                                        {{ $present }} P
                                    </span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded font-medium bg-rose-50 text-rose-700">
                                        {{ $absent }} A
                                    </span>
                                    @if($late > 0)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded font-medium bg-amber-50 text-amber-700">
                                            {{ $late }} L
                                        </span>
                                    @endif
                                    @if($excused > 0)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded font-medium bg-blue-50 text-blue-700">
                                            {{ $excused }} E
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-16 bg-slate-200 rounded-full h-2 overflow-hidden">
                                        <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $rate }}%"></div>
                                    </div>
                                    <span class="text-xs font-semibold text-slate-900">{{ $rate }}%</span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-xs text-slate-600">
                                {{ $session->recorder->name ?? 'System' }}
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.attendance.show', $session) }}" class="px-2.5 py-1 text-xs font-medium text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 rounded transition-colors">
                                        View Sheet
                                    </a>
                                    <a href="{{ route('admin.attendance.create', ['class_section_id' => $session->class_section_id, 'date' => $session->attendance_date->format('Y-m-d')]) }}" class="px-2.5 py-1 text-xs font-medium text-slate-600 hover:text-slate-800 hover:bg-slate-100 rounded transition-colors">
                                        Update
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center">
                                <x-empty-state
                                    title="No Attendance Sessions Found"
                                    description="No attendance records found for the selected criteria. Click 'Record Attendance' to take attendance for a section."
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
