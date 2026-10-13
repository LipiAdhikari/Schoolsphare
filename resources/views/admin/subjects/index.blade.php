<x-app-layout>
    <x-slot:header>
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Academic Subjects</h1>
            <p class="text-xs text-slate-500 mt-0.5">Manage curriculum subjects, subject codes, and class-level curriculum mappings.</p>
        </div>
    </x-slot:header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Subjects Table (2 cols) -->
        <div class="lg:col-span-2 space-y-6">
            <x-card title="Registered Subjects" description="Curriculum subjects with assigned class grade levels." :padding="false">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 font-semibold uppercase tracking-wider">
                            <tr>
                                <th class="px-5 py-3">Subject Name</th>
                                <th class="px-4 py-3">Code</th>
                                <th class="px-4 py-3 text-center">Type</th>
                                <th class="px-5 py-3">Taught In Classes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse ($subjects as $subject)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="px-5 py-3.5 font-bold text-slate-900">
                                        {{ $subject->name }}
                                    </td>
                                    <td class="px-4 py-3.5 font-mono text-[11px] text-slate-600">
                                        {{ $subject->code }}
                                    </td>
                                    <td class="px-4 py-3.5 text-center">
                                        @if ($subject->type === 'theory')
                                            <x-badge variant="indigo" size="sm">Theory</x-badge>
                                        @elseif ($subject->type === 'practical')
                                            <x-badge variant="emerald" size="sm">Practical</x-badge>
                                        @else
                                            <x-badge variant="sky" size="sm">Composite</x-badge>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <div class="flex flex-wrap gap-1.5">
                                            @forelse ($subject->classes as $cls)
                                                <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 text-[11px] font-medium border border-slate-200">
                                                    {{ $cls->name }}
                                                </span>
                                            @empty
                                                <span class="text-slate-400 text-[11px] italic">Not attached to any class</span>
                                            @endforelse
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-6">
                                        <x-empty-state title="No subjects registered" description="Add your first curriculum subject using the form." />
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($subjects->hasPages())
                    <x-slot:footer>
                        {{ $subjects->links() }}
                    </x-slot:footer>
                @endif
            </x-card>
        </div>

        <!-- Sidebar Forms (1 col) -->
        <div class="space-y-6">
            <!-- Add Subject Form -->
            <x-card title="Add Subject" description="Register a new academic subject.">
                <form method="POST" action="{{ route('admin.subjects.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">Subject Name *</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="e.g. Mathematics" required class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                        @error('name')<p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="code" class="block text-xs font-semibold text-slate-700 mb-1">Subject Code *</label>
                        <input type="text" name="code" id="code" value="{{ old('code') }}" placeholder="e.g. MATH-10" required class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono" />
                        @error('code')<p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="type" class="block text-xs font-semibold text-slate-700 mb-1">Evaluation Type *</label>
                        <select name="type" id="type" required class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="theory" {{ old('type') === 'theory' ? 'selected' : '' }}>Theory</option>
                            <option value="practical" {{ old('type') === 'practical' ? 'selected' : '' }}>Practical / Lab</option>
                            <option value="composite" {{ old('type') === 'composite' ? 'selected' : '' }}>Composite (Theory + Lab)</option>
                        </select>
                        @error('type')<p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="pt-1">
                        <x-button type="submit" variant="primary" size="sm" class="w-full">
                            Save Subject
                        </x-button>
                    </div>
                </form>
            </x-card>

            <!-- Map Subject to Class Form -->
            <x-card title="Attach to Class" description="Link subject into class curriculum.">
                <form method="POST" action="{{ route('admin.subjects.assign-class') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="subject_id" class="block text-xs font-semibold text-slate-700 mb-1">Select Subject *</label>
                        <select name="subject_id" id="subject_id" required class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">-- Choose Subject --</option>
                            @foreach ($subjects as $subj)
                                <option value="{{ $subj->id }}">{{ $subj->name }} ({{ $subj->code }})</option>
                            @endforeach
                        </select>
                        @error('subject_id')<p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="class_id" class="block text-xs font-semibold text-slate-700 mb-1">Select Class Grade *</label>
                        <select name="class_id" id="class_id" required class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">-- Choose Class --</option>
                            @foreach ($classes as $cls)
                                <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                            @endforeach
                        </select>
                        @error('class_id')<p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="pt-1">
                        <x-button type="submit" variant="secondary" size="sm" class="w-full">
                            Attach to Curriculum
                        </x-button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
