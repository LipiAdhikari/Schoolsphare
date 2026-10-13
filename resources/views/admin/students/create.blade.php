@extends('layouts.app')

@section('title', 'Admit New Student — SchoolSphere')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Student Admission</h1>
            <p class="mt-1 text-sm text-slate-500">
                Register a new student profile, create portal access, assign academic section, and link guardians.
            </p>
        </div>
        <div>
            <a href="{{ route('admin.students.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
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

    <form method="POST" action="{{ route('admin.students.store') }}" class="space-y-6">
        @csrf

        <!-- 1. Personal Details -->
        <x-card class="p-6">
            <h2 class="text-base font-semibold text-slate-900 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold flex items-center justify-center">1</span>
                Personal Information
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
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
                    <label for="date_of_birth" class="block text-xs font-medium text-slate-700 mb-1">Date of Birth <span class="text-rose-500">*</span></label>
                    <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth') }}" required
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="admission_date" class="block text-xs font-medium text-slate-700 mb-1">Admission Date <span class="text-rose-500">*</span></label>
                    <input type="date" name="admission_date" id="admission_date" value="{{ old('admission_date', date('Y-m-d')) }}" required
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="admission_number" class="block text-xs font-medium text-slate-700 mb-1">Admission Number</label>
                    <input type="text" name="admission_number" id="admission_number" value="{{ old('admission_number') }}" placeholder="Auto-generated if blank"
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <span class="text-[10px] text-slate-400">e.g. STU-2026-0005</span>
                </div>
                <div>
                    <label for="blood_group" class="block text-xs font-medium text-slate-700 mb-1">Blood Group</label>
                    <select name="blood_group" id="blood_group"
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="">Unknown / Optional</option>
                        @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                            <option value="{{ $bg }}" {{ old('blood_group') === $bg ? 'selected' : '' }}>{{ $bg }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="status" class="block text-xs font-medium text-slate-700 mb-1">Initial Status <span class="text-rose-500">*</span></label>
                    <select name="status" id="status" required
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="phone" class="block text-xs font-medium text-slate-700 mb-1">Personal Phone</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" placeholder="+1 (555) 000-0000"
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="emergency_contact_phone" class="block text-xs font-medium text-slate-700 mb-1">Emergency Contact Phone</label>
                    <input type="text" name="emergency_contact_phone" id="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}" placeholder="+1 (555) 999-9999"
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="sm:col-span-2">
                    <label for="address" class="block text-xs font-medium text-slate-700 mb-1">Residential Address</label>
                    <textarea name="address" id="address" rows="2" placeholder="Street address, city, postal code"
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">{{ old('address') }}</textarea>
                </div>
            </div>
        </x-card>

        <!-- 2. Academic Placement (Current Session) -->
        <x-card class="p-6">
            <h2 class="text-base font-semibold text-slate-900 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold flex items-center justify-center">2</span>
                Academic Placement
                @if($currentYear)
                    <span class="text-xs font-normal text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full ml-auto">
                        Current Session: {{ $currentYear->name }}
                    </span>
                @endif
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="class_section_id" class="block text-xs font-medium text-slate-700 mb-1">Assign Class & Section</label>
                    <select name="class_section_id" id="class_section_id"
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="">Enroll Later / None</option>
                        @foreach($classes as $c)
                            <optgroup label="{{ $c->name }}">
                                @foreach($c->sections as $sec)
                                    <option value="{{ $sec->id }}" {{ old('class_section_id') == $sec->id ? 'selected' : '' }}>
                                        {{ $c->name }} — {{ $sec->name }} (Cap: {{ $sec->capacity }})
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="roll_number" class="block text-xs font-medium text-slate-700 mb-1">Roll / Identifier Number</label>
                    <input type="text" name="roll_number" id="roll_number" value="{{ old('roll_number') }}" placeholder="e.g. 10A-14"
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
        </x-card>

        <!-- 3. Portal Login Credentials -->
        <x-card class="p-6">
            <h2 class="text-base font-semibold text-slate-900 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold flex items-center justify-center">3</span>
                Student Portal Credentials
            </h2>
            <div class="space-y-4">
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="create_user" id="create_user" value="1" {{ old('create_user', '1') == '1' ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="create_user" class="text-sm font-medium text-slate-900">
                        Enable Portal Login (Create Student User Account)
                    </label>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="email" class="block text-xs font-medium text-slate-700 mb-1">Student Login Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="student@schoolsphere.test"
                            class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="password" class="block text-xs font-medium text-slate-700 mb-1">Temporary Password</label>
                        <input type="password" name="password" id="password" placeholder="Default: password123"
                            class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
            </div>
        </x-card>

        <!-- 4. Guardian Details -->
        <x-card class="p-6">
            <h2 class="text-base font-semibold text-slate-900 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold flex items-center justify-center">4</span>
                Primary Guardian / Parent
            </h2>
            
            <div class="space-y-4">
                <div class="flex gap-4 text-sm">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="guardian_mode" value="new" {{ old('guardian_mode', 'new') === 'new' ? 'checked' : '' }}
                            onclick="document.getElementById('new_guardian_box').classList.remove('hidden'); document.getElementById('existing_guardian_box').classList.add('hidden');"
                            class="text-indigo-600 focus:ring-indigo-500">
                        <span>Register New Guardian</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="guardian_mode" value="existing" {{ old('guardian_mode') === 'existing' ? 'checked' : '' }}
                            onclick="document.getElementById('new_guardian_box').classList.add('hidden'); document.getElementById('existing_guardian_box').classList.remove('hidden');"
                            class="text-indigo-600 focus:ring-indigo-500">
                        <span>Select Existing Guardian</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="guardian_mode" value="none" {{ old('guardian_mode') === 'none' ? 'checked' : '' }}
                            onclick="document.getElementById('new_guardian_box').classList.add('hidden'); document.getElementById('existing_guardian_box').classList.add('hidden');"
                            class="text-indigo-600 focus:ring-indigo-500">
                        <span>Link Later</span>
                    </label>
                </div>

                <!-- Existing Guardian Dropdown -->
                <div id="existing_guardian_box" class="{{ old('guardian_mode') === 'existing' ? '' : 'hidden' }}">
                    <label for="guardian_id" class="block text-xs font-medium text-slate-700 mb-1">Select Guardian</label>
                    <select name="guardian_id" id="guardian_id"
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="">Choose registered guardian...</option>
                        @foreach($guardians as $g)
                            <option value="{{ $g->id }}" {{ old('guardian_id') == $g->id ? 'selected' : '' }}>
                                {{ $g->full_name }} ({{ $g->relationship }}) — {{ $g->phone }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- New Guardian Form Fields -->
                <div id="new_guardian_box" class="{{ old('guardian_mode', 'new') === 'new' ? '' : 'hidden' }} space-y-4 pt-2">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label for="guardian_first_name" class="block text-xs font-medium text-slate-700 mb-1">Guardian First Name</label>
                            <input type="text" name="guardian_first_name" id="guardian_first_name" value="{{ old('guardian_first_name') }}"
                                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="guardian_last_name" class="block text-xs font-medium text-slate-700 mb-1">Guardian Last Name</label>
                            <input type="text" name="guardian_last_name" id="guardian_last_name" value="{{ old('guardian_last_name') }}"
                                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="guardian_relationship" class="block text-xs font-medium text-slate-700 mb-1">Relationship</label>
                            <select name="guardian_relationship" id="guardian_relationship"
                                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                                <option value="Father">Father</option>
                                <option value="Mother">Mother</option>
                                <option value="Legal Guardian">Legal Guardian</option>
                                <option value="Relative">Relative</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="guardian_phone" class="block text-xs font-medium text-slate-700 mb-1">Guardian Phone</label>
                            <input type="text" name="guardian_phone" id="guardian_phone" value="{{ old('guardian_phone') }}"
                                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="guardian_email" class="block text-xs font-medium text-slate-700 mb-1">Guardian Email (Optional)</label>
                            <input type="email" name="guardian_email" id="guardian_email" value="{{ old('guardian_email') }}"
                                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>
            </div>
        </x-card>

        <!-- Submit Controls -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('admin.students.index') }}" class="px-4 py-2 text-sm font-medium text-slate-700 hover:text-slate-900 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-xs transition-colors">
                Complete Admission
            </button>
        </div>
    </form>
</div>
@endsection
