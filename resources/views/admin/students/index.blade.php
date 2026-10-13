@extends('layouts.app')

@section('title', 'Students Directory — SchoolSphere')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Student Directory</h1>
                <x-badge color="indigo">{{ $students->total() }} Total</x-badge>
            </div>
            <p class="mt-1 text-sm text-slate-500">
                Manage student admissions, personal records, academic placements, and guardian associations.
            </p>
        </div>
        <div>
            <a href="{{ route('admin.students.create') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-xs transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Admit New Student</span>
            </a>
        </div>
    </div>

    <!-- Filters & Search -->
    <x-card class="p-4">
        <form method="GET" action="{{ route('admin.students.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 items-end">
            <div>
                <label for="search" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Name, admission no, or phone..."
                    class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label for="class_section_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">Class Section</label>
                <select name="class_section_id" id="class_section_id"
                    class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Classes & Sections</option>
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

            <div>
                <label for="status" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">Status</label>
                <select name="status" id="status"
                    class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="graduated" {{ request('status') === 'graduated' ? 'selected' : '' }}>Graduated</option>
                    <option value="transferred" {{ request('status') === 'transferred' ? 'selected' : '' }}>Transferred</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 px-4 py-2 text-sm font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'class_section_id', 'status']))
                    <a href="{{ route('admin.students.index') }}" class="px-3 py-2 text-sm font-medium text-slate-500 hover:text-slate-700">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </x-card>

    <!-- Students Table -->
    <x-card class="overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-slate-600 font-medium text-xs uppercase tracking-wider">
                        <th class="py-3 px-4">Student</th>
                        <th class="py-3 px-4">Class & Section</th>
                        <th class="py-3 px-4">Gender & DOB</th>
                        <th class="py-3 px-4">Guardian / Contact</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($students as $student)
                        @php
                            $activeEnrollment = $student->enrollments->first();
                            $primaryGuardian = $student->guardians->first();
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center text-xs shrink-0">
                                        {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.students.show', $student) }}" class="font-semibold text-slate-900 hover:text-indigo-600 transition-colors">
                                            {{ $student->full_name }}
                                        </a>
                                        <div class="text-xs text-slate-500 font-mono">
                                            {{ $student->admission_number }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($activeEnrollment && $activeEnrollment->classSection)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700">
                                        {{ $activeEnrollment->classSection->full_name }}
                                    </span>
                                    @if($activeEnrollment->roll_number)
                                        <div class="text-[11px] text-slate-500 mt-0.5 font-mono">Roll: {{ $activeEnrollment->roll_number }}</div>
                                    @endif
                                @else
                                    <span class="text-xs text-slate-400 italic">Not Enrolled</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-xs text-slate-600">
                                <span class="capitalize">{{ $student->gender }}</span>
                                <div class="text-slate-400 text-[11px]">{{ $student->date_of_birth ? $student->date_of_birth->format('M d, Y') : '—' }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-xs">
                                @if($primaryGuardian)
                                    <div class="font-medium text-slate-900">{{ $primaryGuardian->full_name }}</div>
                                    <div class="text-slate-500 text-[11px]">{{ $primaryGuardian->phone }}</div>
                                @elseif($student->phone)
                                    <div class="text-slate-700">{{ $student->phone }}</div>
                                @else
                                    <span class="text-slate-400 italic">—</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                @if($student->status === 'active')
                                    <x-badge color="emerald">Active</x-badge>
                                @elseif($student->status === 'inactive')
                                    <x-badge color="rose">Inactive</x-badge>
                                @elseif($student->status === 'graduated')
                                    <x-badge color="indigo">Graduated</x-badge>
                                @elseif($student->status === 'transferred')
                                    <x-badge color="amber">Transferred</x-badge>
                                @else
                                    <x-badge color="slate">{{ ucfirst($student->status) }}</x-badge>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.students.show', $student) }}" class="px-2.5 py-1 text-xs font-medium text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 rounded transition-colors">
                                        View
                                    </a>
                                    <a href="{{ route('admin.students.edit', $student) }}" class="px-2.5 py-1 text-xs font-medium text-slate-600 hover:text-slate-800 hover:bg-slate-100 rounded transition-colors">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.students.toggle-status', $student) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-2.5 py-1 text-xs font-medium {{ $student->status === 'active' ? 'text-rose-600 hover:bg-rose-50' : 'text-emerald-600 hover:bg-emerald-50' }} rounded transition-colors"
                                            title="{{ $student->status === 'active' ? 'Deactivate student' : 'Reactivate student' }}">
                                            {{ $student->status === 'active' ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center">
                                <x-empty-state
                                    title="No Students Found"
                                    description="No student records match your filter criteria. Click 'Admit New Student' to register student profiles."
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($students->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $students->links() }}
            </div>
        @endif
    </x-card>
</div>
@endsection
