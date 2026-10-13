@extends('layouts.app')

@section('title', "Edit {$teacher->full_name} — SchoolSphere")

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Edit Faculty Profile</h1>
            <p class="mt-1 text-sm text-slate-500">
                Update professional details, specialization, and status for {{ $teacher->full_name }}.
            </p>
        </div>
        <div>
            <a href="{{ route('admin.teachers.show', $teacher) }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                &larr; View Profile
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="p-4 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 text-sm">
            <div class="font-semibold mb-1">Please correct the following errors:</div>
            <ul class="list-disc list-inside space-y-0.5 text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.teachers.update', $teacher) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <x-card class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="first_name" class="block text-xs font-medium text-slate-700 mb-1">First Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $teacher->first_name) }}" required
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="last_name" class="block text-xs font-medium text-slate-700 mb-1">Last Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $teacher->last_name) }}" required
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="employee_number" class="block text-xs font-medium text-slate-700 mb-1">Employee ID <span class="text-rose-500">*</span></label>
                    <input type="text" name="employee_number" id="employee_number" value="{{ old('employee_number', $teacher->employee_number) }}" required
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 font-mono">
                </div>
                <div>
                    <label for="gender" class="block text-xs font-medium text-slate-700 mb-1">Gender <span class="text-rose-500">*</span></label>
                    <select name="gender" id="gender" required
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="male" {{ old('gender', $teacher->gender) === 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender', $teacher->gender) === 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ old('gender', $teacher->gender) === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div>
                    <label for="joining_date" class="block text-xs font-medium text-slate-700 mb-1">Joining Date <span class="text-rose-500">*</span></label>
                    <input type="date" name="joining_date" id="joining_date" value="{{ old('joining_date', $teacher->joining_date?->format('Y-m-d')) }}" required
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="status" class="block text-xs font-medium text-slate-700 mb-1">Status <span class="text-rose-500">*</span></label>
                    <select name="status" id="status" required
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="active" {{ old('status', $teacher->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $teacher->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div>
                    <label for="specialization" class="block text-xs font-medium text-slate-700 mb-1">Specialization</label>
                    <input type="text" name="specialization" id="specialization" value="{{ old('specialization', $teacher->specialization) }}"
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="qualification" class="block text-xs font-medium text-slate-700 mb-1">Qualification</label>
                    <input type="text" name="qualification" id="qualification" value="{{ old('qualification', $teacher->qualification) }}"
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="phone" class="block text-xs font-medium text-slate-700 mb-1">Phone <span class="text-rose-500">*</span></label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $teacher->phone) }}" required
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="email" class="block text-xs font-medium text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $teacher->email) }}"
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.teachers.show', $teacher) }}" class="px-4 py-2 text-sm font-medium text-slate-700 hover:text-slate-900 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-5 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-xs transition-colors">
                    Save Changes
                </button>
            </div>
        </x-card>
    </form>
</div>
@endsection
