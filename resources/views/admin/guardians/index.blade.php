@extends('layouts.app')

@section('title', 'Guardians & Parents Directory — SchoolSphere')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Guardians & Parents</h1>
                <x-badge color="indigo">{{ $guardians->total() }} Total</x-badge>
            </div>
            <p class="mt-1 text-sm text-slate-500">
                Manage parent contacts, emergency guardians, portal access, and student family linkages.
            </p>
        </div>
        <div>
            <a href="{{ route('admin.guardians.create') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-xs transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Register Guardian</span>
            </a>
        </div>
    </div>

    <!-- Filters & Search -->
    <x-card class="p-4">
        <form method="GET" action="{{ route('admin.guardians.index') }}" class="flex items-center gap-3">
            <div class="flex-1">
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Search guardian by name, phone, or email..."
                    class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <button type="submit" class="px-4 py-2 text-sm font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                Search
            </button>
            @if(request()->filled('search'))
                <a href="{{ route('admin.guardians.index') }}" class="px-3 py-2 text-sm font-medium text-slate-500 hover:text-slate-700">
                    Reset
                </a>
            @endif
        </form>
    </x-card>

    <!-- Table -->
    <x-card class="overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-slate-600 font-medium text-xs uppercase tracking-wider">
                        <th class="py-3 px-4">Guardian / Parent</th>
                        <th class="py-3 px-4">Contact Information</th>
                        <th class="py-3 px-4">Occupation & Address</th>
                        <th class="py-3 px-4">Linked Students / Wards</th>
                        <th class="py-3 px-4">Portal Account</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($guardians as $guardian)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-purple-100 text-purple-700 font-bold flex items-center justify-center text-xs shrink-0">
                                        {{ strtoupper(substr($guardian->first_name, 0, 1) . substr($guardian->last_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.guardians.show', $guardian) }}" class="font-semibold text-slate-900 hover:text-indigo-600 transition-colors">
                                            {{ $guardian->full_name }}
                                        </a>
                                        <div class="text-xs text-slate-500">
                                            {{ $guardian->relationship }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-xs">
                                <div class="font-medium text-slate-900">{{ $guardian->phone }}</div>
                                @if($guardian->email)
                                    <div class="text-slate-500 text-[11px] font-mono">{{ $guardian->email }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-xs">
                                <div class="text-slate-900">{{ $guardian->occupation ?? '—' }}</div>
                                @if($guardian->address)
                                    <div class="text-slate-400 text-[11px] truncate max-w-xs">{{ $guardian->address }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-xs">
                                @if($guardian->students->count() > 0)
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($guardian->students as $student)
                                            <a href="{{ route('admin.students.show', $student) }}" class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs bg-indigo-50 text-indigo-700 hover:bg-indigo-100">
                                                <span>{{ $student->full_name }}</span>
                                                <span class="text-[10px] text-indigo-400">({{ $student->pivot->relation_type }})</span>
                                            </a>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-slate-400 italic">No linked students</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                @if($guardian->user)
                                    <x-badge color="{{ $guardian->user->status === 'active' ? 'emerald' : 'rose' }}">
                                        {{ ucfirst($guardian->user->status) }}
                                    </x-badge>
                                @else
                                    <span class="text-xs text-slate-400 italic">Offline Only</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.guardians.show', $guardian) }}" class="px-2.5 py-1 text-xs font-medium text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 rounded transition-colors">
                                        View
                                    </a>
                                    <a href="{{ route('admin.guardians.edit', $guardian) }}" class="px-2.5 py-1 text-xs font-medium text-slate-600 hover:text-slate-800 hover:bg-slate-100 rounded transition-colors">
                                        Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center">
                                <x-empty-state
                                    title="No Guardians Registered"
                                    description="No parents or guardians found. Click 'Register Guardian' to create a contact profile."
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($guardians->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $guardians->links() }}
            </div>
        @endif
    </x-card>
</div>
@endsection
