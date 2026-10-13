@extends('layouts.app')

@section('title', 'Register Guardian — SchoolSphere')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Register Parent / Guardian</h1>
            <p class="mt-1 text-sm text-slate-500">
                Add parent contact details, configure portal access, and associate with student wards.
            </p>
        </div>
        <div>
            <a href="{{ route('admin.guardians.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
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

    <form method="POST" action="{{ route('admin.guardians.store') }}" class="space-y-6">
        @csrf

        <!-- 1. Guardian Details -->
        <x-card class="p-6">
            <h2 class="text-base font-semibold text-slate-900 mb-4 pb-2 border-b border-slate-100">
                Guardian Information
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
                    <label for="relationship" class="block text-xs font-medium text-slate-700 mb-1">Default Relationship <span class="text-rose-500">*</span></label>
                    <select name="relationship" id="relationship" required
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="Father" {{ old('relationship') === 'Father' ? 'selected' : '' }}>Father</option>
                        <option value="Mother" {{ old('relationship') === 'Mother' ? 'selected' : '' }}>Mother</option>
                        <option value="Legal Guardian" {{ old('relationship') === 'Legal Guardian' ? 'selected' : '' }}>Legal Guardian</option>
                        <option value="Relative" {{ old('relationship') === 'Relative' ? 'selected' : '' }}>Relative</option>
                        <option value="Other" {{ old('relationship') === 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div>
                    <label for="phone" class="block text-xs font-medium text-slate-700 mb-1">Contact Phone <span class="text-rose-500">*</span></label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required placeholder="+1 (555) 000-0000"
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="email" class="block text-xs font-medium text-slate-700 mb-1">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="guardian@example.com"
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="occupation" class="block text-xs font-medium text-slate-700 mb-1">Occupation / Profession</label>
                    <input type="text" name="occupation" id="occupation" value="{{ old('occupation') }}" placeholder="e.g. Civil Engineer, Accountant"
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="sm:col-span-2">
                    <label for="address" class="block text-xs font-medium text-slate-700 mb-1">Address</label>
                    <textarea name="address" id="address" rows="2" placeholder="Street address, city"
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">{{ old('address') }}</textarea>
                </div>
            </div>
        </x-card>

        <!-- 2. Link Student (Optional) -->
        <x-card class="p-6">
            <h2 class="text-base font-semibold text-slate-900 mb-4 pb-2 border-b border-slate-100">
                Associate with Student Ward (Optional)
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="student_id" class="block text-xs font-medium text-slate-700 mb-1">Select Student</label>
                    <select name="student_id" id="student_id"
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="">None / Associate Later</option>
                        @foreach($students as $s)
                            <option value="{{ $s->id }}" {{ old('student_id') == $s->id ? 'selected' : '' }}>
                                {{ $s->full_name }} ({{ $s->admission_number }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="relation_type" class="block text-xs font-medium text-slate-700 mb-1">Specific Relation</label>
                    <input type="text" name="relation_type" id="relation_type" value="{{ old('relation_type', 'father') }}" placeholder="e.g. father, mother"
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="flex items-center pt-6">
                    <label class="flex items-center gap-2 cursor-pointer text-sm">
                        <input type="checkbox" name="is_primary" value="1" {{ old('is_primary', '1') == '1' ? 'checked' : '' }}
                            class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="font-medium text-slate-800">Primary Contact</span>
                    </label>
                </div>
            </div>
        </x-card>

        <!-- 3. Portal Account -->
        <x-card class="p-6">
            <h2 class="text-base font-semibold text-slate-900 mb-4 pb-2 border-b border-slate-100">
                Parent Portal Account
            </h2>
            <div class="space-y-4">
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="create_user" id="create_user" value="1" {{ old('create_user') ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="create_user" class="text-sm font-medium text-slate-900">
                        Create Parent Portal Login (requires email provided above)
                    </label>
                </div>
                <div>
                    <label for="password" class="block text-xs font-medium text-slate-700 mb-1">Password</label>
                    <input type="password" name="password" id="password" placeholder="Default: password123"
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 max-w-sm">
                </div>
            </div>
        </x-card>

        <!-- Submit Controls -->
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.guardians.index') }}" class="px-4 py-2 text-sm font-medium text-slate-700 hover:text-slate-900 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-xs transition-colors">
                Register Guardian
            </button>
        </div>
    </form>
</div>
@endsection
