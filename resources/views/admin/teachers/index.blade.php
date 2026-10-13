@extends('layouts.app')

@section('title', 'Teachers & Staff Directory — SchoolSphere')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Teachers & Staff</h1>
                <x-badge color="indigo">{{ $teachers->total() }} Total</x-badge>
            </div>
            <p class="mt-1 text-sm text-slate-500">
                Manage faculty members, qualification records, portal accounts, and teaching allocations.
            </p>
        </div>
        <div>
            <a href="{{ route('admin.teachers.create') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-xs transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Register Teacher</span>
            </a>
        </div>
    </div>

    <!-- Filters & Search -->
    <x-card class="p-4">
        <form method="GET" action="{{ route('admin.teachers.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
            <div class="sm:col-span-2">
                <label for="search" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Name, employee no, email, or specialization..."
                    class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label for="status" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">Status</label>
                <div class="flex items-center gap-2">
                    <select name="status" id="status"
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('admin.teachers.index') }}" class="px-2 py-2 text-sm font-medium text-slate-500 hover:text-slate-700">
                            Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </x-card>

    <!-- Table -->
    <x-card class="overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-slate-600 font-medium text-xs uppercase tracking-wider">
                        <th class="py-3 px-4">Faculty Member</th>
                        <th class="py-3 px-4">Specialization & Qualification</th>
                        <th class="py-3 px-4">Contact Info</th>
                        <th class="py-3 px-4">Current Assignments</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($teachers as $teacher)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-xs shrink-0">
                                        {{ strtoupper(substr($teacher->first_name, 0, 1) . substr($teacher->last_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.teachers.show', $teacher) }}" class="font-semibold text-slate-900 hover:text-indigo-600 transition-colors">
                                            {{ $teacher->full_name }}
                                        </a>
                                        <div class="text-xs text-slate-500 font-mono">
                                            {{ $teacher->employee_number }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-xs">
                                <div class="font-medium text-slate-900">{{ $teacher->specialization ?? 'General' }}</div>
                                <div class="text-slate-500 text-[11px]">{{ $teacher->qualification ?? '—' }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-xs">
                                <div class="text-slate-900">{{ $teacher->phone }}</div>
                                @if($teacher->email)
                                    <div class="text-slate-500 text-[11px] font-mono">{{ $teacher->email }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-xs">
                                @if($teacher->teachingAssignments->count() > 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-indigo-50 text-indigo-700">
                                        {{ $teacher->teachingAssignments->count() }} Subjects / Sections
                                    </span>
                                @else
                                    <span class="text-slate-400 italic">None Allocated</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <x-badge color="{{ $teacher->status === 'active' ? 'emerald' : 'rose' }}">
                                    {{ ucfirst($teacher->status) }}
                                </x-badge>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.teachers.show', $teacher) }}" class="px-2.5 py-1 text-xs font-medium text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 rounded transition-colors">
                                        View
                                    </a>
                                    <a href="{{ route('admin.teachers.edit', $teacher) }}" class="px-2.5 py-1 text-xs font-medium text-slate-600 hover:text-slate-800 hover:bg-slate-100 rounded transition-colors">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.teachers.toggle-status', $teacher) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-2.5 py-1 text-xs font-medium {{ $teacher->status === 'active' ? 'text-rose-600 hover:bg-rose-50' : 'text-emerald-600 hover:bg-emerald-50' }} rounded transition-colors">
                                            {{ $teacher->status === 'active' ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center">
                                <x-empty-state
                                    title="No Teachers Found"
                                    description="No faculty members match your filter criteria. Click 'Register Teacher' to add teachers."
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($teachers->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $teachers->links() }}
            </div>
        @endif
    </x-card>
</div>
@endsection
