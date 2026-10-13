@extends('layouts.app')

@section('title', "{$student->full_name} — Student Profile — SchoolSphere")

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-indigo-600 text-white font-bold text-xl flex items-center justify-center shadow-sm">
                {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
            </div>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $student->full_name }}</h1>
                    @if($student->status === 'active')
                        <x-badge color="emerald">Active</x-badge>
                    @elseif($student->status === 'inactive')
                        <x-badge color="rose">Inactive</x-badge>
                    @else
                        <x-badge color="slate">{{ ucfirst($student->status) }}</x-badge>
                    @endif
                </div>
                <div class="mt-1 flex items-center gap-3 text-sm text-slate-500 font-mono">
                    <span>Admission: {{ $student->admission_number }}</span>
                    <span>&bull;</span>
                    <span>Admitted: {{ $student->admission_date ? $student->admission_date->format('M d, Y') : '—' }}</span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.students.index') }}" class="px-3 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                &larr; Directory
            </a>
            <a href="{{ route('admin.students.edit', $student) }}" class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-xs transition-colors">
                Edit Profile
            </a>
            <form method="POST" action="{{ route('admin.students.toggle-status', $student) }}" class="inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="px-3 py-2 text-sm font-medium {{ $student->status === 'active' ? 'text-rose-600 border border-rose-200 hover:bg-rose-50' : 'text-emerald-600 border border-emerald-200 hover:bg-emerald-50' }} rounded-lg transition-colors">
                    {{ $student->status === 'active' ? 'Deactivate' : 'Reactivate' }}
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Personal & Contact Information -->
        <div class="space-y-6">
            <x-card class="p-5">
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-600 mb-4 pb-2 border-b border-slate-100">
                    Personal Information
                </h2>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Gender</dt>
                        <dd class="font-medium text-slate-900 capitalize">{{ $student->gender }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Date of Birth</dt>
                        <dd class="font-medium text-slate-900">
                            {{ $student->date_of_birth ? $student->date_of_birth->format('M d, Y') : '—' }}
                            @if($student->date_of_birth)
                                <span class="text-xs text-slate-400 font-normal">({{ $student->date_of_birth->age }} yrs)</span>
                            @endif
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Blood Group</dt>
                        <dd class="font-medium text-slate-900">{{ $student->blood_group ?? 'Not specified' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Personal Phone</dt>
                        <dd class="font-medium text-slate-900">{{ $student->phone ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Emergency Phone</dt>
                        <dd class="font-medium text-rose-600">{{ $student->emergency_contact_phone ?? '—' }}</dd>
                    </div>
                    <div class="pt-2 border-t border-slate-100">
                        <dt class="text-slate-500 text-xs mb-1">Residential Address</dt>
                        <dd class="text-slate-800 text-xs leading-relaxed bg-slate-50 p-2.5 rounded-lg border border-slate-100">
                            {{ $student->address ?? 'No address recorded' }}
                        </dd>
                    </div>
                </dl>
            </x-card>

            <!-- Portal Login Account -->
            <x-card class="p-5">
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-600 mb-4 pb-2 border-b border-slate-100">
                    Portal Account Access
                </h2>
                @if($student->user)
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Login Email</dt>
                            <dd class="font-mono text-xs text-slate-900 font-medium">{{ $student->user->email }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Account Status</dt>
                            <dd>
                                <x-badge color="{{ $student->user->status === 'active' ? 'emerald' : 'rose' }}">
                                    {{ ucfirst($student->user->status) }}
                                </x-badge>
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Email Verified</dt>
                            <dd class="text-xs text-slate-700">
                                {{ $student->user->email_verified_at ? $student->user->email_verified_at->format('M d, Y') : 'Unverified' }}
                            </dd>
                        </div>
                    </dl>
                @else
                    <div class="text-center py-4 text-xs text-slate-500">
                        <p>No active login credentials linked.</p>
                        <span class="text-[11px] text-slate-400">Student accesses portal through offline records or guardian account.</span>
                    </div>
                @endif
            </x-card>
        </div>

        <!-- Right: Academic History & Guardians -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Current Academic Placement -->
            <x-card class="p-5">
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-600">
                        Current Academic Placement
                    </h2>
                    @if($currentYear)
                        <span class="text-xs font-medium text-indigo-700 bg-indigo-50 px-2.5 py-1 rounded-full">
                            Session {{ $currentYear->name }}
                        </span>
                    @endif
                </div>

                @php
                    $currentEnrollment = $student->enrollments->where('academic_year_id', $currentYear?->id)->first();
                @endphp

                @if($currentEnrollment && $currentEnrollment->classSection)
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 bg-slate-50 p-4 rounded-xl border border-slate-200">
                        <div>
                            <div class="text-xs text-slate-500 uppercase tracking-wider">Class Grade</div>
                            <div class="text-base font-bold text-slate-900 mt-0.5">
                                {{ $currentEnrollment->classSection->schoolClass->name ?? 'Grade' }}
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-slate-500 uppercase tracking-wider">Assigned Section</div>
                            <div class="text-base font-bold text-indigo-600 mt-0.5">
                                {{ $currentEnrollment->classSection->name }}
                            </div>
                            <div class="text-xs text-slate-400">Room: {{ $currentEnrollment->classSection->room_number ?? 'TBD' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-slate-500 uppercase tracking-wider">Roll Number</div>
                            <div class="text-base font-mono font-bold text-slate-900 mt-0.5">
                                {{ $currentEnrollment->roll_number ?? 'Not assigned' }}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="p-4 bg-amber-50 rounded-lg border border-amber-200 text-amber-800 text-xs flex items-center justify-between">
                        <span>This student is not yet enrolled in the current active academic session ({{ $currentYear?->name }}).</span>
                        <a href="{{ route('admin.enrollments.index') }}" class="font-semibold underline ml-2">Enroll Now &rarr;</a>
                    </div>
                @endif
            </x-card>

            <!-- Linked Guardians & Parents -->
            <x-card class="p-5">
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-600">
                        Linked Parents & Guardians
                    </h2>
                    <a href="{{ route('admin.guardians.create') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                        + Add / Link Guardian
                    </a>
                </div>

                @if($student->guardians->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($student->guardians as $g)
                            <div class="p-4 rounded-xl border border-slate-200 bg-white hover:border-indigo-300 transition-colors">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-700">
                                        {{ ucfirst($g->pivot->relation_type ?? $g->relationship) }}
                                    </span>
                                    @if($g->pivot->is_primary)
                                        <x-badge color="emerald">Primary Contact</x-badge>
                                    @endif
                                </div>
                                <a href="{{ route('admin.guardians.show', $g) }}" class="font-bold text-slate-900 hover:text-indigo-600 transition-colors block">
                                    {{ $g->full_name }}
                                </a>
                                <div class="mt-2 space-y-1 text-xs text-slate-600">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-slate-400">Phone:</span>
                                        <span class="font-medium text-slate-900">{{ $g->phone }}</span>
                                    </div>
                                    @if($g->email)
                                        <div class="flex items-center gap-1.5 font-mono text-[11px]">
                                            <span class="text-slate-400 font-sans">Email:</span>
                                            <span>{{ $g->email }}</span>
                                        </div>
                                    @endif
                                    @if($g->occupation)
                                        <div class="text-[11px] text-slate-500">
                                            Occ: {{ $g->occupation }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-6 text-center text-xs text-slate-400 border border-dashed border-slate-200 rounded-xl">
                        No guardians linked to this student yet.
                    </div>
                @endif
            </x-card>

            <!-- Academic Enrollment History -->
            <x-card class="p-5">
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-600 mb-4 pb-2 border-b border-slate-100">
                    Historical Enrollment Records
                </h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50 text-slate-500 uppercase font-semibold text-[10px]">
                                <th class="py-2.5 px-3">Session</th>
                                <th class="py-2.5 px-3">Class & Section</th>
                                <th class="py-2.5 px-3">Roll No</th>
                                <th class="py-2.5 px-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($student->enrollments as $enr)
                                <tr>
                                    <td class="py-2.5 px-3 font-semibold text-slate-900">
                                        {{ $enr->academicYear->name ?? '—' }}
                                    </td>
                                    <td class="py-2.5 px-3">
                                        {{ $enr->classSection->full_name ?? '—' }}
                                    </td>
                                    <td class="py-2.5 px-3 font-mono">
                                        {{ $enr->roll_number ?? '—' }}
                                    </td>
                                    <td class="py-2.5 px-3">
                                        <x-badge color="indigo">{{ ucfirst($enr->status) }}</x-badge>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-4 text-center text-slate-400 italic">
                                        No historical enrollment records found.
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
