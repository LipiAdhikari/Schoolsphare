@extends('layouts.app')

@section('title', "{$guardian->full_name} — Guardian Profile — SchoolSphere")

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-purple-600 text-white font-bold text-xl flex items-center justify-center shadow-sm">
                {{ strtoupper(substr($guardian->first_name, 0, 1) . substr($guardian->last_name, 0, 1)) }}
            </div>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $guardian->full_name }}</h1>
                    <x-badge color="purple">{{ $guardian->relationship }}</x-badge>
                </div>
                <div class="mt-1 flex items-center gap-3 text-sm text-slate-500 font-mono">
                    <span>Phone: {{ $guardian->phone }}</span>
                    @if($guardian->email)
                        <span>&bull;</span>
                        <span>{{ $guardian->email }}</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.guardians.index') }}" class="px-3 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                &larr; Directory
            </a>
            <a href="{{ route('admin.guardians.edit', $guardian) }}" class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-xs transition-colors">
                Edit Profile
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Guardian Info & Portal Account -->
        <div class="space-y-6">
            <x-card class="p-5">
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-600 mb-4 pb-2 border-b border-slate-100">
                    Contact & Professional Details
                </h2>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Relationship</dt>
                        <dd class="font-medium text-slate-900">{{ $guardian->relationship }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Contact Phone</dt>
                        <dd class="font-medium text-slate-900">{{ $guardian->phone }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Email Address</dt>
                        <dd class="font-mono text-xs text-slate-900">{{ $guardian->email ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Occupation</dt>
                        <dd class="text-slate-900 font-medium">{{ $guardian->occupation ?? '—' }}</dd>
                    </div>
                    <div class="pt-2 border-t border-slate-100">
                        <dt class="text-slate-500 text-xs mb-1">Residential Address</dt>
                        <dd class="text-slate-800 text-xs leading-relaxed bg-slate-50 p-2.5 rounded-lg border border-slate-100">
                            {{ $guardian->address ?? 'No address recorded' }}
                        </dd>
                    </div>
                </dl>
            </x-card>

            <!-- Portal Login Account -->
            <x-card class="p-5">
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-600 mb-4 pb-2 border-b border-slate-100">
                    Parent Portal Access
                </h2>
                @if($guardian->user)
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Login Email</dt>
                            <dd class="font-mono text-xs text-slate-900 font-medium">{{ $guardian->user->email }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Role</dt>
                            <dd><x-badge color="indigo">Parent</x-badge></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Account Status</dt>
                            <dd>
                                <x-badge color="{{ $guardian->user->status === 'active' ? 'emerald' : 'rose' }}">
                                    {{ ucfirst($guardian->user->status) }}
                                </x-badge>
                            </dd>
                        </div>
                    </dl>
                @else
                    <div class="text-center py-4 text-xs text-slate-500">
                        <p>No parent portal account linked.</p>
                        <span class="text-[11px] text-slate-400">Offline guardian record for notifications and emergency contact.</span>
                    </div>
                @endif
            </x-card>
        </div>

        <!-- Right: Associated Students & Link Tool -->
        <div class="lg:col-span-2 space-y-6">
            <x-card class="p-5">
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-600 mb-4 pb-2 border-b border-slate-100">
                    Associated Students / Wards ({{ $guardian->students->count() }})
                </h2>

                <div class="space-y-3">
                    @forelse($guardian->students as $student)
                        <div class="p-4 rounded-xl border border-slate-200 bg-white flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center text-xs shrink-0">
                                    {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.students.show', $student) }}" class="font-bold text-slate-900 hover:text-indigo-600 transition-colors">
                                            {{ $student->full_name }}
                                        </a>
                                        <span class="text-xs px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-medium">
                                            {{ ucfirst($student->pivot->relation_type) }}
                                        </span>
                                        @if($student->pivot->is_primary)
                                            <x-badge color="emerald">Primary Contact</x-badge>
                                        @endif
                                    </div>
                                    <div class="text-xs text-slate-500 mt-0.5 font-mono">
                                        Admission: {{ $student->admission_number }}
                                    </div>
                                </div>
                            </div>

                            <div>
                                <form method="POST" action="{{ route('admin.guardians.unlink-student', [$guardian, $student]) }}" class="inline"
                                    onsubmit="return confirm('Remove student association with this guardian?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1 text-xs font-medium text-rose-600 hover:text-rose-800 hover:bg-rose-50 rounded transition-colors">
                                        Unlink
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-xs text-slate-400 border border-dashed border-slate-200 rounded-xl">
                            No students associated with this guardian yet. Use the form below to link a student.
                        </div>
                    @endforelse
                </div>
            </x-card>

            <!-- Link Another Student -->
            @if($availableStudents->count() > 0)
                <x-card class="p-5">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-600 mb-3 pb-2 border-b border-slate-100">
                        Link Another Student
                    </h2>
                    <form method="POST" action="{{ route('admin.guardians.link-student', $guardian) }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
                        @csrf
                        <div class="sm:col-span-2">
                            <label for="student_id" class="block text-xs font-medium text-slate-700 mb-1">Student</label>
                            <select name="student_id" id="student_id" required
                                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                                <option value="">Choose student...</option>
                                @foreach($availableStudents as $s)
                                    <option value="{{ $s->id }}">{{ $s->full_name }} ({{ $s->admission_number }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="relation_type" class="block text-xs font-medium text-slate-700 mb-1">Relationship</label>
                            <input type="text" name="relation_type" id="relation_type" value="father" required
                                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div class="flex items-center gap-2">
                            <label class="flex items-center gap-1.5 text-xs text-slate-700 cursor-pointer">
                                <input type="checkbox" name="is_primary" value="1" class="rounded border-slate-300 text-indigo-600">
                                <span>Primary</span>
                            </label>
                            <button type="submit" class="flex-1 px-4 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-xs transition-colors">
                                Link
                            </button>
                        </div>
                    </form>
                </x-card>
            @endif
        </div>
    </div>
</div>
@endsection
