@extends('layouts.app')

@section('title', "{$teacher->full_name} — Faculty Profile — SchoolSphere")

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-emerald-600 text-white font-bold text-xl flex items-center justify-center shadow-sm">
                {{ strtoupper(substr($teacher->first_name, 0, 1) . substr($teacher->last_name, 0, 1)) }}
            </div>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $teacher->full_name }}</h1>
                    <x-badge color="{{ $teacher->status === 'active' ? 'emerald' : 'rose' }}">
                        {{ ucfirst($teacher->status) }}
                    </x-badge>
                </div>
                <div class="mt-1 flex items-center gap-3 text-sm text-slate-500 font-mono">
                    <span>Employee ID: {{ $teacher->employee_number }}</span>
                    <span>&bull;</span>
                    <span>Joined: {{ $teacher->joining_date ? $teacher->joining_date->format('M d, Y') : '—' }}</span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.teachers.index') }}" class="px-3 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                &larr; Faculty Directory
            </a>
            <a href="{{ route('admin.teachers.edit', $teacher) }}" class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-xs transition-colors">
                Edit Profile
            </a>
            <form method="POST" action="{{ route('admin.teachers.toggle-status', $teacher) }}" class="inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="px-3 py-2 text-sm font-medium {{ $teacher->status === 'active' ? 'text-rose-600 border border-rose-200 hover:bg-rose-50' : 'text-emerald-600 border border-emerald-200 hover:bg-emerald-50' }} rounded-lg transition-colors">
                    {{ $teacher->status === 'active' ? 'Deactivate' : 'Reactivate' }}
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Details & Account -->
        <div class="space-y-6">
            <x-card class="p-5">
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-600 mb-4 pb-2 border-b border-slate-100">
                    Faculty Details
                </h2>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Gender</dt>
                        <dd class="font-medium text-slate-900 capitalize">{{ $teacher->gender }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Specialization</dt>
                        <dd class="font-medium text-indigo-700">{{ $teacher->specialization ?? 'General' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Qualification</dt>
                        <dd class="font-medium text-slate-900">{{ $teacher->qualification ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Contact Phone</dt>
                        <dd class="font-medium text-slate-900">{{ $teacher->phone }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Email Address</dt>
                        <dd class="font-mono text-xs text-slate-900">{{ $teacher->email ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Joining Date</dt>
                        <dd class="text-slate-900 font-medium">
                            {{ $teacher->joining_date ? $teacher->joining_date->format('M d, Y') : '—' }}
                        </dd>
                    </div>
                </dl>
            </x-card>

            <!-- Portal Login Account -->
            <x-card class="p-5">
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-600 mb-4 pb-2 border-b border-slate-100">
                    Portal Login Account
                </h2>
                @if($teacher->user)
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Login Email</dt>
                            <dd class="font-mono text-xs text-slate-900 font-medium">{{ $teacher->user->email }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Role</dt>
                            <dd><x-badge color="indigo">Teacher</x-badge></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Account Status</dt>
                            <dd>
                                <x-badge color="{{ $teacher->user->status === 'active' ? 'emerald' : 'rose' }}">
                                    {{ ucfirst($teacher->user->status) }}
                                </x-badge>
                            </dd>
                        </div>
                    </dl>
                @else
                    <div class="text-center py-4 text-xs text-slate-500">
                        <p>No user login account attached.</p>
                    </div>
                @endif
            </x-card>
        </div>

        <!-- Right: Teaching Allocations -->
        <div class="lg:col-span-2 space-y-6">
            <x-card class="p-5">
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-600">
                        Teaching Allocations
                    </h2>
                    <a href="{{ route('admin.teaching-assignments.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                        Manage Allocations &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50 text-slate-500 uppercase font-semibold text-[10px]">
                                <th class="py-2.5 px-3">Session</th>
                                <th class="py-2.5 px-3">Class & Section</th>
                                <th class="py-2.5 px-3">Subject</th>
                                <th class="py-2.5 px-3">Type</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($teacher->teachingAssignments as $assignment)
                                <tr>
                                    <td class="py-2.5 px-3 font-semibold text-slate-900">
                                        {{ $assignment->academicYear->name ?? '—' }}
                                    </td>
                                    <td class="py-2.5 px-3 font-medium">
                                        {{ $assignment->classSection->full_name ?? '—' }}
                                    </td>
                                    <td class="py-2.5 px-3">
                                        <span class="font-semibold text-indigo-700">{{ $assignment->subject->name ?? '—' }}</span>
                                        <span class="text-[10px] text-slate-400 font-mono">({{ $assignment->subject->code ?? '' }})</span>
                                    </td>
                                    <td class="py-2.5 px-3 capitalize">
                                        {{ $assignment->subject->type ?? 'theory' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-6 text-center text-slate-400 italic">
                                        No teaching allocations assigned to this teacher yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
