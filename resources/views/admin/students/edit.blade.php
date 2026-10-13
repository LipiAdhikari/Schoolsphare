@extends('layouts.app')

@section('title', "Edit {$student->full_name} — SchoolSphere")

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Edit Student Profile</h1>
            <p class="mt-1 text-sm text-slate-500">
                Update personal details, contact information, and enrollment status for {{ $student->full_name }}.
            </p>
        </div>
        <div>
            <a href="{{ route('admin.students.show', $student) }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
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

    <form method="POST" action="{{ route('admin.students.update', $student) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <x-card class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="first_name" class="block text-xs font-medium text-slate-700 mb-1">First Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $student->first_name) }}" required
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="last_name" class="block text-xs font-medium text-slate-700 mb-1">Last Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $student->last_name) }}" required
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="admission_number" class="block text-xs font-medium text-slate-700 mb-1">Admission Number <span class="text-rose-500">*</span></label>
                    <input type="text" name="admission_number" id="admission_number" value="{{ old('admission_number', $student->admission_number) }}" required
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 font-mono">
                </div>
                <div>
                    <label for="gender" class="block text-xs font-medium text-slate-700 mb-1">Gender <span class="text-rose-500">*</span></label>
                    <select name="gender" id="gender" required
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="male" {{ old('gender', $student->gender) === 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender', $student->gender) === 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ old('gender', $student->gender) === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div>
                    <label for="date_of_birth" class="block text-xs font-medium text-slate-700 mb-1">Date of Birth <span class="text-rose-500">*</span></label>
                    <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', $student->date_of_birth?->format('Y-m-d')) }}" required
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="admission_date" class="block text-xs font-medium text-slate-700 mb-1">Admission Date <span class="text-rose-500">*</span></label>
                    <input type="date" name="admission_date" id="admission_date" value="{{ old('admission_date', $student->admission_date?->format('Y-m-d')) }}" required
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="blood_group" class="block text-xs font-medium text-slate-700 mb-1">Blood Group</label>
                    <select name="blood_group" id="blood_group"
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="">Unknown / Optional</option>
                        @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                            <option value="{{ $bg }}" {{ old('blood_group', $student->blood_group) === $bg ? 'selected' : '' }}>{{ $bg }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="status" class="block text-xs font-medium text-slate-700 mb-1">Status <span class="text-rose-500">*</span></label>
                    <select name="status" id="status" required
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="active" {{ old('status', $student->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $student->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="graduated" {{ old('status', $student->status) === 'graduated' ? 'selected' : '' }}>Graduated</option>
                        <option value="transferred" {{ old('status', $student->status) === 'transferred' ? 'selected' : '' }}>Transferred</option>
                        <option value="suspended" {{ old('status', $student->status) === 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>
                <div>
                    <label for="phone" class="block text-xs font-medium text-slate-700 mb-1">Personal Phone</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $student->phone) }}"
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="emergency_contact_phone" class="block text-xs font-medium text-slate-700 mb-1">Emergency Phone</label>
                    <input type="text" name="emergency_contact_phone" id="emergency_contact_phone" value="{{ old('emergency_contact_phone', $student->emergency_contact_phone) }}"
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="sm:col-span-2">
                    <label for="address" class="block text-xs font-medium text-slate-700 mb-1">Address</label>
                    <textarea name="address" id="address" rows="2"
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">{{ old('address', $student->address) }}</textarea>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.students.show', $student) }}" class="px-4 py-2 text-sm font-medium text-slate-700 hover:text-slate-900 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
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
