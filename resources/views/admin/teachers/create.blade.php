@extends('layouts.app')

@section('title', 'Register Teacher — SchoolSphere')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Register New Teacher</h1>
            <p class="mt-1 text-sm text-slate-500">
                Add a new faculty member profile and configure their system portal access.
            </p>
        </div>
        <div>
            <a href="{{ route('admin.teachers.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                &larr; Back to Directory
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

    <form method="POST" action="{{ route('admin.teachers.store') }}" class="space-y-6">
        @csrf

        <!-- 1. Faculty Details -->
        <x-card class="p-6">
            <h2 class="text-base font-semibold text-slate-900 mb-4 pb-2 border-b border-slate-100">
                Faculty Information
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="first_name" class="block text-xs font-medium text-slate-700 mb-1">First Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="last_name" class="block text-xs font-medium text-slate-700 mb-1">Last Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="gender" class="block text-xs font-medium text-slate-700 mb-1">Gender <span class="text-rose-500">*</span></label>
                    <select name="gender" id="gender" required
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div>
                    <label for="employee_number" class="block text-xs font-medium text-slate-700 mb-1">Employee ID Number</label>
                    <input type="text" name="employee_number" id="employee_number" value="{{ old('employee_number') }}" placeholder="Auto-generated if blank"
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 font-mono">
                    <span class="text-[10px] text-slate-400">e.g. TCH-2026-0002</span>
                </div>
                <div>
                    <label for="joining_date" class="block text-xs font-medium text-slate-700 mb-1">Joining Date <span class="text-rose-500">*</span></label>
                    <input type="date" name="joining_date" id="joining_date" value="{{ old('joining_date', date('Y-m-d')) }}" required
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="status" class="block text-xs font-medium text-slate-700 mb-1">Status <span class="text-rose-500">*</span></label>
                    <select name="status" id="status" required
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="specialization" class="block text-xs font-medium text-slate-700 mb-1">Subject Specialization</label>
                    <input type="text" name="specialization" id="specialization" value="{{ old('specialization') }}" placeholder="e.g. Pure Mathematics, Physics"
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="qualification" class="block text-xs font-medium text-slate-700 mb-1">Academic Qualification</label>
                    <input type="text" name="qualification" id="qualification" value="{{ old('qualification') }}" placeholder="e.g. M.Sc. Mathematics, B.Ed."
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="phone" class="block text-xs font-medium text-slate-700 mb-1">Contact Phone <span class="text-rose-500">*</span></label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required placeholder="+1 (555) 000-0000"
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="email" class="block text-xs font-medium text-slate-700 mb-1">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="teacher@schoolsphere.test"
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
        </x-card>

        <!-- 2. Portal User Account -->
        <x-card class="p-6">
            <h2 class="text-base font-semibold text-slate-900 mb-4 pb-2 border-b border-slate-100">
                Teacher Portal Login Account
            </h2>
            <div class="space-y-4">
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="create_user" id="create_user" value="1" {{ old('create_user', '1') == '1' ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="create_user" class="text-sm font-medium text-slate-900">
                        Create Teacher Login Account (uses the email above)
                    </label>
                </div>
                <div>
                    <label for="password" class="block text-xs font-medium text-slate-700 mb-1">Password</label>
                    <input type="password" name="password" id="password" placeholder="Default: password123"
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 max-w-sm">
                    <span class="text-[11px] text-slate-400">Leave blank to use default temporary password 'password123'.</span>
                </div>
            </div>
        </x-card>

        <!-- Submit Controls -->
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.teachers.index') }}" class="px-4 py-2 text-sm font-medium text-slate-700 hover:text-slate-900 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-xs transition-colors">
                Register Teacher
            </button>
        </div>
    </form>
</div>
@endsection
